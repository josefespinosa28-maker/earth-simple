from typing import Optional
from pydantic import BaseModel, Field


class ClimateMetrics(BaseModel):
    avg_temp_c: float
    max_temp_c: float
    min_temp_c: float
    total_precipitation_mm: float
    avg_root_soil_moisture: float = Field(..., description="Humedad del suelo en zona de raíces (0 a 1)")


class MonthlySeriesItem(BaseModel):
    month: str = Field(..., example="2025-05")
    avg_temp_c: float
    precipitation_mm: float
    soil_moisture: float


class CropRecommendation(BaseModel):
    recommended_crop: str = Field(..., example="Frijol")
    botanical_family: str = Field(..., example="Fabaceae")
    confidence_level: str = Field(..., example="Alta")
    reasoning: str = Field(
        ...,
        example="Tras 2 ciclos consecutivos de Maíz (Poaceae) y precipitación acumulada baja (410 mm), se sugiere rotar a una leguminosa fijadora de nitrógeno y menor demanda hídrica."
    )
    triggered_rules: list[str] = Field(..., description="Lista de reglas y umbrales exactos que activaron esta sugerencia")


class AnalysisResponse(BaseModel):
    parcel_id: int
    coordinates_analyzed: dict[str, float]
    inferred_soil_type: Optional[str] = None
    climate_metrics: ClimateMetrics
    monthly_series: list[MonthlySeriesItem]
    recommendations: list[CropRecommendation]
    methodological_limitations: str = Field(
        default=(
            "Los datos climáticos de NASA POWER tienen una resolución espacial regional (~50 km x 50 km) "
            "y representan condiciones agroclimáticas generales, no microclimas específicos de la parcela."
        )
    )