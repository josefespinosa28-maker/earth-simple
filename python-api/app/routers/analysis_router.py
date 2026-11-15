from fastapi import APIRouter, Depends
from app.clients.nasa_power import NasaPowerClient
from app.schemas.request_schemas import AnalysisRequest
from app.schemas.response_schemas import (
    AnalysisResponse,
    ClimateMetrics,
    CropRecommendation,
    MonthlySeriesItem,
)
from app.services.data_cleaner import ClimateDataCleaner

router = APIRouter(prefix="/analysis", tags=["Agricultural Analysis"])


@router.post("/climate-preview", response_model=AnalysisResponse)
async def preview_parcel_climate(
    payload: AnalysisRequest,
    nasa_client: NasaPowerClient = Depends(NasaPowerClient),
):
    # 1. Consultar datos reales a NASA POWER
    raw_nasa = await nasa_client.fetch_daily_climate(
        latitude=payload.latitude,
        longitude=payload.longitude,
        start_date=payload.start_date,
        end_date=payload.end_date,
    )

    # 2. Limpiar y resumir con Pandas
    processed = ClimateDataCleaner.process_power_data(raw_nasa)

    # 3. Devolver estructura contractual (aún con recomendación placeholder antes de programar las reglas)
    return AnalysisResponse(
        parcel_id=payload.parcel_id,
        coordinates_analyzed={
            "latitude": round(payload.latitude, 2),
            "longitude": round(payload.longitude, 2),
        },
        inferred_soil_type=payload.soil_type or "Pendiente de consulta a SoilGrids",
        climate_metrics=ClimateMetrics(**processed["climate_metrics"]),
        monthly_series=[MonthlySeriesItem(**item) for item in processed["monthly_series"]],
        recommendations=[
            CropRecommendation(
                recommended_crop="Evaluación pendiente",
                botanical_family="N/A",
                confidence_level="Pendiente",
                reasoning="Conexión con NASA POWER exitosa. Siguiente paso: activar motor de reglas agronómicas.",
                triggered_rules=["CONNECTIVITY_TEST_OK"],
            )
        ],
    )