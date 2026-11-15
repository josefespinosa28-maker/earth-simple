import unittest
from datetime import date

from fastapi import HTTPException
from pydantic import ValidationError

from app.routers.analysis_router import preview_parcel_climate
from app.schemas.request_schemas import AnalysisRequest
from app.services.data_cleaner import ClimateDataCleaner
from app.services.reglas_cultivos import MotorReglasCultivos


class AnalysisRequestTests(unittest.TestCase):
    def test_requires_valid_coordinates_and_date_range(self):
        with self.assertRaises(ValidationError):
            AnalysisRequest(
                latitude=100,
                longitude=-98,
                start_date="2025-12-31",
                end_date="2025-01-01",
            )

    def test_allows_unknown_optional_parcel_data(self):
        request = AnalysisRequest(
            latitude=19.16,
            longitude=-98.4069,
            start_date="2025-01-01",
            end_date="2025-12-31",
            has_irrigation=None,
            crop_history=[{"crop_name": "Maíz"}],
        )
        self.assertIsNone(request.area_hectares)
        self.assertIsNone(request.crop_history[0].sowing_date)


class RecommendationEngineTests(unittest.TestCase):
    def test_ranks_up_to_three_options_and_does_not_claim_nitrogen_depletion(self):
        result = MotorReglasCultivos.generar_recomendaciones(
            metricas_climaticas={
                "total_precipitation_mm": 300,
                "avg_root_soil_moisture": 0.2,
            },
            metricas_vegetacion=None,
            elevacion_m=1900,
            historial_cultivos=[
                {
                    "crop_name": "Maíz",
                    "botanical_family": "Poaceae",
                    "crop_cycle_type": "annual",
                    "sowing_date": date(2024, 5, 1),
                },
                {
                    "crop_name": "Maíz",
                    "botanical_family": "Poaceae",
                    "crop_cycle_type": "annual",
                    "sowing_date": date(2025, 5, 1),
                },
            ],
            fecha_inicio=date(2025, 1, 1),
            fecha_fin=date(2025, 12, 31),
            tiene_riego=False,
        )
        recommendations = result["recommendations"]
        self.assertEqual(len(recommendations), 3)
        self.assertEqual(
            [item["score"] for item in recommendations],
            sorted((item["score"] for item in recommendations), reverse=True),
        )
        self.assertEqual(recommendations[0]["recommended_crop"], "Haba")
        self.assertTrue(
            all(
                "no demuestra agotamiento de nitrógeno" in rule
                for rule in recommendations[0]["triggered_rules"]
                if rule.startswith("ROTACION_FAMILIA")
            )
        )

    def test_unclassified_or_perennial_cycles_do_not_trigger_annual_rotation(self):
        history = [
            {
                "crop_name": f"cultivo-{year}",
                "botanical_family": "Poaceae",
                "crop_cycle_type": cycle_type,
                "sowing_date": date(year, 5, 1),
            }
            for year, cycle_type in ((2024, "annual"), (2025, "perennial"))
        ]
        result = MotorReglasCultivos.generar_recomendaciones(
            metricas_climaticas={},
            metricas_vegetacion=None,
            elevacion_m=None,
            historial_cultivos=history,
            fecha_inicio=date(2025, 1, 1),
            fecha_fin=date(2025, 12, 31),
            tiene_riego=None,
        )
        self.assertFalse(
            any(rule.startswith("ROTACION_FAMILIA") for rule in result["decision_rules"])
        )

    def test_no_environmental_or_history_evidence_returns_no_recommendations(self):
        result = MotorReglasCultivos.generar_recomendaciones(
            metricas_climaticas={},
            metricas_vegetacion=None,
            elevacion_m=None,
            historial_cultivos=[],
            fecha_inicio=date(2025, 1, 1),
            fecha_fin=date(2025, 12, 31),
            tiene_riego=None,
        )
        self.assertFalse(result["sufficient_information"])
        self.assertEqual(result["recommendations"], [])

    def test_crop_name_without_history_metadata_is_not_enough_evidence(self):
        result = MotorReglasCultivos.generar_recomendaciones(
            metricas_climaticas={},
            metricas_vegetacion=None,
            elevacion_m=None,
            historial_cultivos=[{"crop_name": "Maíz"}],
            fecha_inicio=date(2025, 1, 1),
            fecha_fin=date(2025, 12, 31),
            tiene_riego=None,
        )
        self.assertFalse(result["sufficient_information"])
        self.assertEqual(result["recommendations"], [])


class ClimateDataCleanerTests(unittest.TestCase):
    def test_missing_nasa_parameters_are_null_not_zero(self):
        result = ClimateDataCleaner.process_power_data(
            {"PRECTOTCORR": {"20250101": -999.0}}
        )
        self.assertIsNone(result["climate_metrics"]["avg_temp_c"])
        self.assertIsNone(result["climate_metrics"]["total_precipitation_mm"])

    def test_elevation_scalar_and_list_formats_are_supported(self):
        self.assertEqual(ClimateDataCleaner.procesar_dato_elevacion({"elevation": 42.5}), 42.5)
        self.assertEqual(ClimateDataCleaner.procesar_dato_elevacion({"elevation": [42.5]}), 42.5)
        self.assertIsNone(ClimateDataCleaner.procesar_dato_elevacion({"elevation": []}))


class PartialSourceFailureTests(unittest.IsolatedAsyncioTestCase):
    async def test_external_failures_return_partial_response_and_warnings(self):
        class FailingClient:
            async def fetch_daily_climate(self, **kwargs):
                raise HTTPException(
                    status_code=504,
                    detail=f"timeout; unexpected arguments: {kwargs}",
                )

        class FailingModis:
            async def obtener_serie_vegetacion(self, **kwargs):
                raise HTTPException(
                    status_code=502,
                    detail=f"unavailable; unexpected arguments: {kwargs}",
                )

        class FailingElevation:
            async def obtener_elevacion(self, **kwargs):
                raise HTTPException(
                    status_code=502,
                    detail=f"unavailable; unexpected arguments: {kwargs}",
                )

        payload = AnalysisRequest(
            latitude=19.16,
            longitude=-98.4069,
            start_date="2025-01-01",
            end_date="2025-12-31",
        )
        response = await preview_parcel_climate(
            payload,
            nasa_client=FailingClient(),
            cliente_modis=FailingModis(),
            cliente_elevacion=FailingElevation(),
        )
        self.assertFalse(response.sufficient_information)
        self.assertEqual(response.recommendations, [])
        self.assertGreaterEqual(len(response.data_warnings), 3)
        self.assertIsNone(response.climate_metrics.avg_temp_c)

    async def test_complete_supplied_environment_skips_external_queries(self):
        class UnusedClient:
            async def fetch_daily_climate(self, **kwargs):
                raise AssertionError(
                    f"NASA POWER should not be queried: {kwargs}"
                )

        class UnusedModis:
            async def obtener_serie_vegetacion(self, **kwargs):
                raise AssertionError(f"MODIS should not be queried: {kwargs}")

        class UnusedElevation:
            async def obtener_elevacion(self, **kwargs):
                raise AssertionError(f"Elevation should not be queried: {kwargs}")

        payload = AnalysisRequest(
            latitude=19.16,
            longitude=-98.4069,
            start_date="2025-01-01",
            end_date="2025-12-31",
            has_irrigation=False,
            environmental_data={
                "climate_metrics": {
                    "avg_temp_c": 15,
                    "max_temp_c": 25,
                    "min_temp_c": 5,
                    "total_precipitation_mm": 300,
                    "avg_root_soil_moisture": 0.2,
                },
                "vegetation_metrics": {
                    "observations": [
                        {"date": "2025-05-01", "ndvi": 0.2, "evi": 0.1}
                    ]
                },
                "elevation_m": 1900,
            },
        )
        response = await preview_parcel_climate(
            payload,
            nasa_client=UnusedClient(),
            cliente_modis=UnusedModis(),
            cliente_elevacion=UnusedElevation(),
        )
        self.assertEqual(response.recommendations[0].recommended_crop, "Haba")
        self.assertEqual(len(response.recommendations), 3)
        self.assertEqual(response.climate_metrics.total_precipitation_mm, 300)
        self.assertEqual(response.vegetation_metrics.avg_ndvi, 0.2)


if __name__ == "__main__":
    unittest.main()
