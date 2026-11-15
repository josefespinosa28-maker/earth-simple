from datetime import date
from typing import Optional
from pydantic import BaseModel, Field, model_validator


class CropHistoryItem(BaseModel):
    crop_name: str = Field(..., example="Maíz", description="Nombre del cultivo sembrado")
    botanical_family: Optional[str] = Field(None, example="Poaceae", description="Familia botánica si se conoce")
    sowing_date: date = Field(..., example="2025-05-15")
    harvest_date: Optional[date] = Field(None, example="2025-10-20")
    yield_tons_per_ha: Optional[float] = Field(None, ge=0, example=4.5)


class AnalysisRequest(BaseModel):
    parcel_id: int = Field(..., example=12, description="ID de la parcela en la base de datos de Laravel")
    latitude: float = Field(..., ge=-90.0, le=90.0, example=19.0414)
    longitude: float = Field(..., ge=-180.0, le=180.0, example=-98.2063)
    area_hectares: float = Field(..., gt=0, example=5.5)
    soil_type: Optional[str] = Field(None, example="Franco arcilloso", description="Null si el agricultor no lo conoce")
    has_irrigation: bool = Field(..., example=False)
    start_date: date = Field(..., example="2025-01-01", description="Inicio del periodo histórico a analizar")
    end_date: date = Field(..., example="2025-12-31", description="Fin del periodo histórico a analizar")
    crop_history: list[CropHistoryItem] = Field(default_factory=list, description="Historial de ciclos previos")

    @model_validator(mode="after")
    def validate_date_range(self) -> "AnalysisRequest":
        if self.end_date < self.start_date:
            raise ValueError("end_date no puede ser anterior a start_date")
        return self