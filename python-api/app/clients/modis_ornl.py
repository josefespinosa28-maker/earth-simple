import asyncio
from datetime import date, timedelta

import httpx
from fastapi import HTTPException

from app.core.config import settings


class ClienteModisOrnl:
    """Consulta observaciones históricas NDVI y EVI de MODIS divididas en bloques seguros."""

    BANDAS = ("250m_16_days_NDVI", "250m_16_days_EVI")
    DIAS_POR_BLOQUE = 120

    @staticmethod
    def convertir_fecha_juliana(fecha: date) -> str:
        return f"A{fecha.year}{fecha.timetuple().tm_yday:03d}"

    def _dividir_rango_fechas(self, fecha_inicio: date, fecha_fin: date) -> list[tuple[date, date]]:
        """Divide un periodo largo en ventanas seguras evitando tramos finales menores a 16 días."""
        bloques = []
        actual = fecha_inicio
        while actual <= fecha_fin:
            fin_bloque = min(actual + timedelta(days=self.DIAS_POR_BLOQUE - 1), fecha_fin)
            # Si el remanente siguiente sería menor a 16 días (1 ciclo MODIS), absorberlo aquí si no excede 140 días
            if 0 < (fecha_fin - fin_bloque).days < 16 and (fecha_fin - actual).days <= 140:
                fin_bloque = fecha_fin
            bloques.append((actual, fin_bloque))
            actual = fin_bloque + timedelta(days=1)
        return bloques

    async def _consultar_banda(
        self,
        cliente: httpx.AsyncClient,
        latitud: float,
        longitud: float,
        banda: str,
        inicio: date,
        fin: date,
    ) -> dict:
        parametros = {
            "latitude": latitud,
            "longitude": longitud,
            "band": banda,
            "startDate": self.convertir_fecha_juliana(inicio),
            "endDate": self.convertir_fecha_juliana(fin),
            "kmAboveBelow": 0,
            "kmLeftRight": 0,
        }
        respuesta = await cliente.get(settings.URL_MODIS_ORNL, params=parametros)

        # Si ORNL indica que no hay captura satelital en esa ventana corta, devolver vacío sin abortar
        if respuesta.status_code == 400 and "No data available" in respuesta.text:
            return {"subset": []}

        respuesta.raise_for_status()
        datos = respuesta.json()

        if "subset" not in datos:
            raise HTTPException(
                status_code=502,
                detail=f"La respuesta de MODIS ORNL para {banda} no contiene la serie esperada.",
            )
        return datos

    async def obtener_serie_vegetacion(
        self,
        latitud: float,
        longitud: float,
        fecha_inicio: date,
        fecha_fin: date,
    ) -> dict:
        bloques = self._dividir_rango_fechas(fecha_inicio, fecha_fin)

        async with httpx.AsyncClient(timeout=settings.HTTP_TIMEOUT) as cliente:
            try:
                tareas = [
                    self._consultar_banda(cliente, latitud, longitud, banda, inicio, fin)
                    for banda in self.BANDAS
                    for inicio, fin in bloques
                ]
                resultados = await asyncio.gather(*tareas)

                subset_combinado = []
                header_base = {}
                for resultado in resultados:
                    if not header_base and "xllcorner" in resultado:
                        header_base = dict(resultado)
                    subset_combinado.extend(resultado.get("subset", []))

                header_base["subset"] = subset_combinado
                return header_base

            except httpx.TimeoutException as error:
                raise HTTPException(
                    status_code=504,
                    detail="Tiempo de espera agotado al conectar con MODIS ORNL.",
                ) from error
            except httpx.HTTPStatusError as error:
                raise HTTPException(
                    status_code=502,
                    detail=f"MODIS ORNL devolvió un error HTTP {error.response.status_code}: {error.response.text}",
                ) from error
            except httpx.RequestError as error:
                raise HTTPException(
                    status_code=502,
                    detail="No fue posible establecer conexión con MODIS ORNL.",
                ) from error