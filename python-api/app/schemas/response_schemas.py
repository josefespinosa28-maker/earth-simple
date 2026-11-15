from typing import Literal

from pydantic import BaseModel, Field


class ClimateMetrics(BaseModel):
    avg_temp_c: float | None = None
    max_temp_c: float | None = None
    min_temp_c: float | None = None
    total_precipitation_mm: float | None = None
    avg_root_soil_moisture: float | None = Field(
        None,
        description="Estimación NASA POWER de humedad en zona de raíces (0 a 1).",
    )


class MonthlySeriesItem(BaseModel):
    month: str
    avg_temp_c: float | None = None
    precipitation_mm: float | None = None
    soil_moisture: float | None = None


class ObservacionVegetacion(BaseModel):
    date: str
    ndvi: float | None = None
    evi: float | None = None


class MetricasVegetacion(BaseModel):
    avg_ndvi: float | None = None
    avg_evi: float | None = None
    observations: list[ObservacionVegetacion] = Field(default_factory=list)


class IndicadorCalculado(BaseModel):
    status: str
    value: float | str | bool | None = None
    evidence_type: Literal["measured", "estimated", "heuristic", "unavailable"]
    explanation: str


class FactorEvaluado(BaseModel):
    factor: str
    status: str
    value: float | str | bool | None = None
    explanation: str


class CropRecommendation(BaseModel):
    recommended_crop: str = Field(..., example="Frijol")
    botanical_family: str = Field(..., example="Fabaceae")
    score: float = Field(..., ge=0, le=100, description="Puntuación heurística, no validada científicamente.")
    confidence_score: float = Field(..., ge=0, le=100, description="Cobertura de evidencia disponible; no es probabilidad.")
    confidence_level: str
    reasoning: str
    triggered_rules: list[str] = Field(default_factory=list)
    factors_evaluated: list[FactorEvaluado] = Field(default_factory=list)


class AnalysisResponse(BaseModel):
    parcel_id: int | None = None
    coordinates_analyzed: dict[str, float]
    inferred_soil_type: str | None = None
    climate_metrics: ClimateMetrics
    monthly_series: list[MonthlySeriesItem] = Field(default_factory=list)
    recommendations: list[CropRecommendation] = Field(default_factory=list, max_length=3)
    vegetation_metrics: MetricasVegetacion | None = None
    elevation_m: float | None = None
    sufficient_information: bool
    confidence_score: float = Field(..., ge=0, le=100)
    confidence_level: str
    calculated_indicators: dict[str, IndicadorCalculado]
    data_warnings: list[str] = Field(default_factory=list)
    methodological_limitations: str = Field(
        default=(
            "NASA POWER aporta estimaciones agroclimáticas de escala regional; "
            "MODIS MOD13Q1 aporta observaciones de vegetación de 250 m cada 16 días; "
            "la elevación de Open-Meteo es puntual. NDVI/EVI no miden fertilidad. "
            "No hay en el proyecto un catálogo de tolerancias por cultivo ni datos "
            "de municipio, suelo analizado o calendario regional. La puntuación y "
            "la confianza son heurísticas de cobertura y reglas, no validaciones "
            "agronómicas ni probabilidades. La puntuación usa base 50, suma 25 "
            "por coincidir con la familia priorizada (o resta 15 si no coincide) "
            "y suma 20 por coincidir con la selección del umbral altitudinal "
            "existente. La confianza suma cobertura de evidencia: clima 40, "
            "vegetación 15, elevación 15, historial 20 y riego conocido 10. "
            "No es una probabilidad; el nivel máximo está limitado a Media "
            "por la falta de requisitos agronómicos por cultivo."
        )
    )
