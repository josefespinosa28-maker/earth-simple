from datetime import date
from typing import Literal

from pydantic import BaseModel, ConfigDict, Field, model_validator


class CropHistoryItem(BaseModel):
    model_config = ConfigDict(str_strip_whitespace=True)

    crop_name: str = Field(..., min_length=1, example="Maíz")
    botanical_family: str | None = Field(None, example="Poaceae")
    sowing_date: date | None = Field(None, example="2025-05-15")
    harvest_date: date | None = Field(None, example="2025-10-20")
    yield_tons_per_ha: float | None = Field(
        None,
        ge=0,
        allow_inf_nan=False,
        example=4.5,
    )
    crop_cycle_type: Literal["annual", "perennial"] | None = Field(
        None,
        description="Indicar sólo si se conoce; los ciclos no clasificados no activan reglas de rotación.",
    )

    @model_validator(mode="after")
    def validate_crop_dates(self) -> "CropHistoryItem":
        if (
            self.sowing_date is not None
            and self.harvest_date is not None
            and self.harvest_date < self.sowing_date
        ):
            raise ValueError("harvest_date no puede ser anterior a sowing_date")
        return self


class ClimateMetricsInput(BaseModel):
    avg_temp_c: float | None = Field(None, allow_inf_nan=False)
    max_temp_c: float | None = Field(None, allow_inf_nan=False)
    min_temp_c: float | None = Field(None, allow_inf_nan=False)
    total_precipitation_mm: float | None = Field(
        None,
        ge=0,
        allow_inf_nan=False,
    )
    avg_root_soil_moisture: float | None = Field(
        None,
        ge=0,
        le=1,
        allow_inf_nan=False,
    )


class VegetationObservationInput(BaseModel):
    date: str
    ndvi: float | None = Field(None, allow_inf_nan=False)
    evi: float | None = Field(None, allow_inf_nan=False)


class VegetationMetricsInput(BaseModel):
    avg_ndvi: float | None = Field(None, allow_inf_nan=False)
    avg_evi: float | None = Field(None, allow_inf_nan=False)
    observations: list[VegetationObservationInput] = Field(default_factory=list)


class EnvironmentalDataInput(BaseModel):
    """Optional normalized measurements already available to Laravel."""

    climate_metrics: ClimateMetricsInput | None = None
    vegetation_metrics: VegetationMetricsInput | None = None
    elevation_m: float | None = Field(None, allow_inf_nan=False)


class AnalysisRequest(BaseModel):
    parcel_id: int | None = Field(None, ge=1, example=12)
    latitude: float = Field(
        ...,
        ge=-90.0,
        le=90.0,
        allow_inf_nan=False,
        example=19.0414,
    )
    longitude: float = Field(
        ...,
        ge=-180.0,
        le=180.0,
        allow_inf_nan=False,
        example=-98.2063,
    )
    area_hectares: float | None = Field(
        None,
        gt=0,
        allow_inf_nan=False,
        example=5.5,
    )
    soil_type: str | None = Field(
        None,
        max_length=100,
        example="Franco arcilloso",
        description="Null si el agricultor no lo conoce.",
    )
    has_irrigation: bool | None = Field(
        None,
        description="Usar null cuando la disponibilidad de riego sea desconocida.",
    )
    irrigation_type: str | None = Field(None, max_length=100)
    start_date: date = Field(..., example="2025-01-01", description="Inicio del periodo ambiental a analizar")
    end_date: date = Field(..., example="2025-12-31", description="Fin del periodo ambiental a analizar")
    crop_history: list[CropHistoryItem] = Field(
        default_factory=list,
        description="Ciclos previos; los campos desconocidos pueden omitirse.",
    )
    desired_sowing_date: date | None = Field(None, description="Fecha de siembra deseada")
    planting_season: str | None = Field(None, max_length=50)
    production_goal: Literal[
        "autoconsumo",
        "comercializacion",
        "alimentacion_animal",
    ] | None = None
    environmental_data: EnvironmentalDataInput | None = None

    @model_validator(mode="after")
    def validate_request(self) -> "AnalysisRequest":
        if self.end_date < self.start_date:
            raise ValueError("end_date no puede ser anterior a start_date")
        if self.has_irrigation is False and self.irrigation_type:
            raise ValueError("irrigation_type requiere has_irrigation=true")
        return self
