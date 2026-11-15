import httpx
from datetime import date
from fastapi import HTTPException
from app.core.config import settings


class NasaPowerClient:
    """Cliente dedicado a consultar datos agroclimáticos diarios de NASA POWER."""

    # Variables clave para agricultura:
    # T2M: Temp media (°C) | T2M_MAX: Temp máx | T2M_MIN: Temp mín
    # PRECTOTCORR: Precipitación corregida (mm/día)
    # GWETROOT: Humedad del suelo en zona de raíces (escala 0-1)
    DEFAULT_PARAMETERS = "T2M,T2M_MAX,T2M_MIN,PRECTOTCORR,GWETROOT"

    async def fetch_daily_climate(
        self,
        latitude: float,
        longitude: float,
        start_date: date,
        end_date: date,
    ) -> dict:
        params = {
            "parameters": self.DEFAULT_PARAMETERS,
            "community": "AG",
            "longitude": round(longitude, 2),
            "latitude": round(latitude, 2),
            "start": start_date.strftime("%Y%m%d"),
            "end": end_date.strftime("%Y%m%d"),
            "format": "JSON",
        }

        async with httpx.AsyncClient(timeout=settings.HTTP_TIMEOUT) as client:
            try:
                response = await client.get(settings.NASA_POWER_URL, params=params)
                response.raise_for_status()
                data = response.json()

                # Validar que la NASA devolvió el bloque esperado
                if "properties" not in data or "parameter" not in data["properties"]:
                    raise HTTPException(
                        status_code=502,
                        detail="La respuesta de NASA POWER no contiene la estructura esperada.",
                    )

                return data["properties"]["parameter"]

            except httpx.TimeoutException:
                raise HTTPException(
                    status_code=504,
                    detail="Tiempo de espera agotado al conectar con NASA POWER API.",
                )
            except httpx.HTTPStatusError as exc:
                raise HTTPException(
                    status_code=502,
                    detail=f"NASA POWER API devolvió un error HTTP {exc.response.status_code}.",
                )