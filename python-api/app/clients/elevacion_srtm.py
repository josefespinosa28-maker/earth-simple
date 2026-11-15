import math

import httpx
from fastapi import HTTPException

from app.core.config import settings


class ClienteElevacionSrtm:
    """Consulta la elevación de una coordenada mediante Open-Meteo."""

    async def obtener_elevacion(self, latitud: float, longitud: float) -> dict:
        parametros = {"latitude": latitud, "longitude": longitud}

        async with httpx.AsyncClient(timeout=settings.HTTP_TIMEOUT) as cliente:
            try:
                respuesta = await cliente.get(
                    settings.URL_ELEVACION_OPEN_METEO,
                    params=parametros,
                )
                respuesta.raise_for_status()
                datos = respuesta.json()
                valor_elevacion = datos.get("elevation")
                if isinstance(valor_elevacion, list):
                    elevacion = valor_elevacion[0] if valor_elevacion else None
                else:
                    elevacion = valor_elevacion

                if isinstance(elevacion, str):
                    try:
                        elevacion = float(elevacion)
                    except ValueError as error:
                        raise HTTPException(
                            status_code=502,
                            detail="La respuesta de elevación no contiene el dato esperado.",
                        ) from error

                if not isinstance(elevacion, (int, float)) or not math.isfinite(float(elevacion)):
                    raise HTTPException(
                        status_code=502,
                        detail="La respuesta de elevación no contiene el dato esperado.",
                    )
                return {**datos, "elevation": float(elevacion)}
            except httpx.TimeoutException as error:
                raise HTTPException(
                    status_code=504,
                    detail="Tiempo de espera agotado al conectar con Open-Meteo Elevation.",
                ) from error
            except httpx.HTTPStatusError as error:
                raise HTTPException(
                    status_code=502,
                    detail=f"Open-Meteo Elevation devolvió un error HTTP {error.response.status_code}.",
                ) from error
            except httpx.RequestError as error:
                raise HTTPException(
                    status_code=502,
                    detail="No fue posible establecer conexión con Open-Meteo Elevation.",
                ) from error
