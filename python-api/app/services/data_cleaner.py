import numpy as np
import pandas as pd


class ClimateDataCleaner:
    """Limpia y resume las series diarias provenientes de NASA POWER."""

    @staticmethod
    def process_power_data(raw_parameters: dict) -> dict:
        # 1. Convertir diccionario de parámetros a DataFrame (fechas como índice)
        df = pd.DataFrame(raw_parameters)
        df.index = pd.to_datetime(df.index, format="%Y%m%d")

        # 2. Reemplazar los valores faltantes (-999) por NaN para no distorsionar promedios
        df.replace(-999.0, np.nan, inplace=True)

        # 3. Calcular métricas globales del periodo
        climate_metrics = {
            "avg_temp_c": round(float(df["T2M"].mean()), 2),
            "max_temp_c": round(float(df["T2M_MAX"].max()), 2),
            "min_temp_c": round(float(df["T2M_MIN"].min()), 2),
            "total_precipitation_mm": round(float(df["PRECTOTCORR"].sum()), 2),
            "avg_root_soil_moisture": round(float(df["GWETROOT"].mean()), 3),
        }

        # 4. Agrupar por mes ('ME' = Month End) para las gráficas en el frontend
        df_monthly = (
            df.resample("ME")
            .agg(
                {
                    "T2M": "mean",
                    "PRECTOTCORR": "sum",
                    "GWETROOT": "mean",
                }
            )
            .round(2)
            .reset_index()
        )

        df_monthly["month"] = df_monthly["index"].dt.strftime("%Y-%m")
        monthly_series = [
            {
                "month": row["month"],
                "avg_temp_c": float(row["T2M"]) if pd.notna(row["T2M"]) else 0.0,
                "precipitation_mm": float(row["PRECTOTCORR"]) if pd.notna(row["PRECTOTCORR"]) else 0.0,
                "soil_moisture": float(row["GWETROOT"]) if pd.notna(row["GWETROOT"]) else 0.0,
            }
            for _, row in df_monthly.iterrows()
        ]

        return {
            "climate_metrics": climate_metrics,
            "monthly_series": monthly_series,
        }