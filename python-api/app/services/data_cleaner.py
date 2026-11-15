import numpy as np
import pandas as pd


class ClimateDataCleaner:
    """Limpia y resume las series diarias provenientes de NASA POWER."""

    @staticmethod
    def process_power_data(raw_parameters: dict) -> dict:
        df = pd.DataFrame(raw_parameters)
        if df.empty:
            return {
                "climate_metrics": ClimateDataCleaner._metricas_vacias(),
                "monthly_series": [],
            }

        fechas = pd.to_datetime(df.index, format="%Y%m%d", errors="coerce")
        df = df.loc[fechas.notna()].copy()
        df.index = fechas[fechas.notna()]
        df.replace(-999.0, np.nan, inplace=True)

        for columna in ("T2M", "T2M_MAX", "T2M_MIN", "PRECTOTCORR", "GWETROOT"):
            if columna not in df:
                df[columna] = np.nan
            df[columna] = pd.to_numeric(df[columna], errors="coerce")

        climate_metrics = {
            "avg_temp_c": ClimateDataCleaner._valor(df["T2M"].mean(), 2),
            "max_temp_c": ClimateDataCleaner._valor(df["T2M_MAX"].max(), 2),
            "min_temp_c": ClimateDataCleaner._valor(df["T2M_MIN"].min(), 2),
            "total_precipitation_mm": ClimateDataCleaner._valor(
                df["PRECTOTCORR"].sum(min_count=1), 2
            ),
            "avg_root_soil_moisture": ClimateDataCleaner._valor(
                df["GWETROOT"].mean(), 3
            ),
        }

        if df.empty:
            return {"climate_metrics": climate_metrics, "monthly_series": []}

        mensual = (
            df.resample("ME")
            .agg(
                avg_temp_c=("T2M", "mean"),
                precipitation_mm=("PRECTOTCORR", lambda valores: valores.sum(min_count=1)),
                soil_moisture=("GWETROOT", "mean"),
            )
            .round(2)
        )
        monthly_series = [
            {
                "month": fecha.strftime("%Y-%m"),
                "avg_temp_c": ClimateDataCleaner._valor(fila["avg_temp_c"], 2),
                "precipitation_mm": ClimateDataCleaner._valor(
                    fila["precipitation_mm"], 2
                ),
                "soil_moisture": ClimateDataCleaner._valor(fila["soil_moisture"], 2),
            }
            for fecha, fila in mensual.iterrows()
        ]
        return {
            "climate_metrics": climate_metrics,
            "monthly_series": monthly_series,
        }

    @staticmethod
    def _metricas_vacias() -> dict:
        return {
            "avg_temp_c": None,
            "max_temp_c": None,
            "min_temp_c": None,
            "total_precipitation_mm": None,
            "avg_root_soil_moisture": None,
        }

    @staticmethod
    def _valor(valor, decimales: int) -> float | None:
        if pd.isna(valor) or not np.isfinite(valor):
            return None
        return round(float(valor), decimales)

    @staticmethod
    def procesar_datos_vegetacion(datos_crudos: dict) -> dict | None:
        """Limpia las bandas MODIS y escala sus valores válidos."""
        filas: dict[str, dict[str, float]] = {}
        bandas = {
            "250m_16_days_NDVI": "ndvi",
            "250m_16_days_EVI": "evi",
        }

        for observacion in datos_crudos.get("subset", []):
            clave_metrica = bandas.get(observacion.get("band"))
            if clave_metrica is None:
                continue

            valores = pd.to_numeric(
                pd.Series(observacion.get("data", [])),
                errors="coerce",
            )
            valores = valores[valores > -3000]
            if valores.empty:
                continue

            fecha = observacion.get("calendar_date") or observacion.get("date")
            if fecha:
                filas.setdefault(str(fecha), {})[clave_metrica] = round(
                    float(valores.mean()) * 0.0001,
                    4,
                )

        if not filas:
            return None

        serie = pd.DataFrame(
            [{"date": fecha, **valores} for fecha, valores in filas.items()]
        ).sort_values("date")
        observaciones = [
            {
                "date": str(fila["date"]),
                "ndvi": ClimateDataCleaner._valor(fila.get("ndvi"), 4),
                "evi": ClimateDataCleaner._valor(fila.get("evi"), 4),
            }
            for fila in serie.to_dict(orient="records")
        ]
        ndvi = pd.to_numeric(
            serie["ndvi"] if "ndvi" in serie else pd.Series(dtype=float),
            errors="coerce",
        )
        evi = pd.to_numeric(
            serie["evi"] if "evi" in serie else pd.Series(dtype=float),
            errors="coerce",
        )
        return {
            "avg_ndvi": ClimateDataCleaner._valor(ndvi.mean(), 4),
            "avg_evi": ClimateDataCleaner._valor(evi.mean(), 4),
            "observations": observaciones,
        }

    @staticmethod
    def procesar_dato_elevacion(datos_crudos: dict) -> float | None:
        valor_elevacion = datos_crudos.get("elevation")
        if isinstance(valor_elevacion, list):
            valores = pd.Series(valor_elevacion)
        elif valor_elevacion is None:
            valores = pd.Series(dtype=float)
        else:
            valores = pd.Series([valor_elevacion])

        elevaciones = pd.to_numeric(valores, errors="coerce").dropna()
        if elevaciones.empty:
            return None
        return float(elevaciones.iloc[0])
