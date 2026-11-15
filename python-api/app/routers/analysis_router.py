import asyncio
import logging
import math
from collections.abc import Awaitable
from statistics import fmean

import httpx
from fastapi import APIRouter, Depends, HTTPException

from app.clients.elevacion_srtm import ClienteElevacionSrtm
from app.clients.modis_ornl import ClienteModisOrnl
from app.clients.nasa_power import NasaPowerClient
from app.schemas.request_schemas import AnalysisRequest
from app.schemas.response_schemas import (
    AnalysisResponse,
    ClimateMetrics,
    CropRecommendation,
    IndicadorCalculado,
    MetricasVegetacion,
    MonthlySeriesItem,
)
from app.services.data_cleaner import ClimateDataCleaner
from app.services.reglas_cultivos import MotorReglasCultivos

logger = logging.getLogger(__name__)
router = APIRouter(prefix="/analysis", tags=["Agricultural Analysis"])


async def _consultar_fuente(
    nombre: str,
    solicitud: Awaitable[dict],
) -> tuple[dict | None, str | None]:
    try:
        return await solicitud, None
    except (HTTPException, httpx.HTTPError, ValueError, KeyError) as error:
        logger.warning("Falló la fuente ambiental %s: %s", nombre, error)
        return None, f"No se pudieron obtener datos de {nombre}; se continuará con los datos disponibles."


@router.post("/climate-preview", response_model=AnalysisResponse)
async def preview_parcel_climate(
    payload: AnalysisRequest,
    nasa_client: NasaPowerClient = Depends(NasaPowerClient),
    cliente_modis: ClienteModisOrnl = Depends(ClienteModisOrnl),
    cliente_elevacion: ClienteElevacionSrtm = Depends(ClienteElevacionSrtm),
):
    environmental_data = payload.environmental_data
    supplied_climate = (
        environmental_data.climate_metrics.model_dump(exclude_none=True)
        if environmental_data and environmental_data.climate_metrics
        else {}
    )
    climate_fields = {
        "avg_temp_c",
        "max_temp_c",
        "min_temp_c",
        "total_precipitation_mm",
        "avg_root_soil_moisture",
    }
    climate_complete = climate_fields.issubset(supplied_climate)
    supplied_vegetation = (
        environmental_data.vegetation_metrics.model_dump(exclude_none=True)
        if environmental_data and environmental_data.vegetation_metrics
        else None
    )
    if supplied_vegetation is not None:
        for indice in ("ndvi", "evi"):
            clave_promedio = f"avg_{indice}"
            if supplied_vegetation.get(clave_promedio) is None:
                valores = [
                    observacion[indice]
                    for observacion in supplied_vegetation.get("observations", [])
                    if isinstance(observacion.get(indice), (int, float))
                    and math.isfinite(observacion[indice])
                ]
                if valores:
                    supplied_vegetation[clave_promedio] = fmean(valores)
    vegetation_available = bool(
        supplied_vegetation
        and (
            supplied_vegetation.get("avg_ndvi") is not None
            or supplied_vegetation.get("avg_evi") is not None
        )
    )
    supplied_elevation = (
        environmental_data.elevation_m
        if environmental_data is not None
        else None
    )

    requests: dict[str, Awaitable[dict]] = {}
    if not climate_complete:
        requests["NASA POWER"] = nasa_client.fetch_daily_climate(
            latitude=payload.latitude,
            longitude=payload.longitude,
            start_date=payload.start_date,
            end_date=payload.end_date,
        )
    if not vegetation_available:
        requests["MODIS ORNL"] = cliente_modis.obtener_serie_vegetacion(
            latitud=payload.latitude,
            longitud=payload.longitude,
            fecha_inicio=payload.start_date,
            fecha_fin=payload.end_date,
        )
    if supplied_elevation is None:
        requests["Open-Meteo Elevation"] = cliente_elevacion.obtener_elevacion(
            latitud=payload.latitude,
            longitud=payload.longitude,
        )

    nombres = list(requests)
    resultados = await asyncio.gather(
        *(
            _consultar_fuente(nombre, solicitud)
            for nombre, solicitud in requests.items()
        )
    )
    respuestas = dict(zip(nombres, resultados, strict=True))
    advertencias = [
        advertencia
        for _, advertencia in respuestas.values()
        if advertencia is not None
    ]

    metricas_climaticas = ClimateDataCleaner._metricas_vacias()
    series_mensuales: list[dict] = []
    respuesta_nasa = respuestas.get("NASA POWER", (None, None))[0]
    if respuesta_nasa is not None:
        try:
            procesado = ClimateDataCleaner.process_power_data(respuesta_nasa)
            metricas_climaticas.update(procesado["climate_metrics"])
            series_mensuales = procesado["monthly_series"]
        except (KeyError, TypeError, ValueError) as error:
            logger.warning("Respuesta de NASA POWER no procesable: %s", error)
            advertencias.append(
                "La respuesta de NASA POWER no tenía datos procesables."
            )
    metricas_climaticas.update(supplied_climate)

    metricas_vegetacion = supplied_vegetation
    respuesta_modis = respuestas.get("MODIS ORNL", (None, None))[0]
    if respuesta_modis is not None:
        try:
            metricas_vegetacion = ClimateDataCleaner.procesar_datos_vegetacion(
                respuesta_modis
            )
        except (KeyError, TypeError, ValueError) as error:
            logger.warning("Respuesta de MODIS ORNL no procesable: %s", error)
            advertencias.append("La respuesta de MODIS ORNL no tenía datos procesables.")

    elevacion_m = supplied_elevation
    respuesta_elevacion = respuestas.get("Open-Meteo Elevation", (None, None))[0]
    if respuesta_elevacion is not None:
        try:
            elevacion_m = ClimateDataCleaner.procesar_dato_elevacion(
                respuesta_elevacion
            )
        except (KeyError, TypeError, ValueError) as error:
            logger.warning("Respuesta de elevación no procesable: %s", error)
            advertencias.append("La respuesta de elevación no tenía datos procesables.")

    recomendaciones = MotorReglasCultivos.generar_recomendaciones(
        metricas_climaticas=metricas_climaticas,
        metricas_vegetacion=metricas_vegetacion,
        elevacion_m=elevacion_m,
        historial_cultivos=[
            cultivo.model_dump() for cultivo in payload.crop_history
        ],
        fecha_inicio=payload.start_date,
        fecha_fin=payload.end_date,
        tiene_riego=payload.has_irrigation,
        tipo_suelo=payload.soil_type,
        tipo_riego=payload.irrigation_type,
        fecha_siembra_deseada=payload.desired_sowing_date,
        temporada_siembra=payload.planting_season,
        objetivo_produccion=payload.production_goal,
    )

    metricas_climaticas_ausentes = [
        nombre
        for nombre, valor in metricas_climaticas.items()
        if valor is None
    ]
    if len(metricas_climaticas_ausentes) == len(metricas_climaticas):
        advertencias.append("No hay métricas climáticas utilizables para el periodo solicitado.")
    elif metricas_climaticas_ausentes:
        advertencias.append(
            "Las métricas climáticas están incompletas; faltan: "
            + ", ".join(metricas_climaticas_ausentes)
            + "."
        )
    if metricas_vegetacion is None:
        advertencias.append("No hay observaciones NDVI/EVI utilizables en el periodo solicitado.")
    if elevacion_m is None:
        advertencias.append("No hay un dato de elevación utilizable.")
    if not recomendaciones["sufficient_information"]:
        advertencias.append(
            "Información insuficiente: no se generaron recomendaciones; "
            "se requiere precipitación o humedad del suelo utilizable, NDVI/EVI "
            "válido, o una señal de rotación con dos ciclos anuales identificados."
        )

    return AnalysisResponse(
        parcel_id=payload.parcel_id,
        coordinates_analyzed={
            "latitude": round(payload.latitude, 4),
            "longitude": round(payload.longitude, 4),
        },
        inferred_soil_type=payload.soil_type,
        climate_metrics=ClimateMetrics(**metricas_climaticas),
        monthly_series=[
            MonthlySeriesItem(**item) for item in series_mensuales
        ],
        recommendations=[
            CropRecommendation(**item)
            for item in recomendaciones["recommendations"]
        ],
        vegetation_metrics=(
            MetricasVegetacion(**metricas_vegetacion)
            if metricas_vegetacion is not None
            else None
        ),
        elevation_m=elevacion_m,
        sufficient_information=recomendaciones["sufficient_information"],
        confidence_score=recomendaciones["confidence_score"],
        confidence_level=recomendaciones["confidence_level"],
        calculated_indicators={
            nombre: IndicadorCalculado(**indicador)
            for nombre, indicador in recomendaciones["calculated_indicators"].items()
        },
        data_warnings=advertencias,
    )
