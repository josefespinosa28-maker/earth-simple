from datetime import date
from typing import Any


class MotorReglasCultivos:
    """Genera un ranking heurístico a partir de las reglas disponibles del MVP."""

    UMBRAL_PRECIPITACION_ANUAL_MM = 500.0
    UMBRAL_HUMEDAD_SUELO = 0.25
    UMBRAL_NDVI = 0.30
    UMBRAL_EVI = 0.20
    UMBRAL_ELEVACION_M = 1800.0
    CICLOS_POACEAE_PARA_ROTACION = 2
    CULTIVOS = (
        ("Maíz", "Poaceae"),
        ("Frijol", "Fabaceae"),
        ("Haba", "Fabaceae"),
        ("Avena", "Poaceae"),
    )

    @classmethod
    def generar_recomendaciones(
        cls,
        metricas_climaticas: dict[str, Any],
        metricas_vegetacion: dict[str, Any] | None,
        elevacion_m: float | None,
        historial_cultivos: list[dict[str, Any]],
        fecha_inicio: date,
        fecha_fin: date,
        tiene_riego: bool | None,
        tipo_suelo: str | None = None,
        tipo_riego: str | None = None,
        fecha_siembra_deseada: date | None = None,
        temporada_siembra: str | None = None,
        objetivo_produccion: str | None = None,
    ) -> dict[str, Any]:
        reglas: list[str] = []
        dias_periodo = (fecha_fin - fecha_inicio).days + 1
        precipitacion = cls._numero(
            metricas_climaticas.get("total_precipitation_mm")
        )
        precipitacion_anualizada = (
            precipitacion * 365 / dias_periodo
            if precipitacion is not None and dias_periodo > 0
            else None
        )
        humedad_suelo = cls._numero(
            metricas_climaticas.get("avg_root_soil_moisture")
        )

        deficit_precipitacion = (
            precipitacion_anualizada is not None
            and precipitacion_anualizada < cls.UMBRAL_PRECIPITACION_ANUAL_MM
        )
        deficit_humedad_suelo = (
            humedad_suelo is not None
            and humedad_suelo < cls.UMBRAL_HUMEDAD_SUELO
        )
        ndvi = cls._numero(
            metricas_vegetacion.get("avg_ndvi")
            if metricas_vegetacion is not None
            else None
        )
        evi = cls._numero(
            metricas_vegetacion.get("avg_evi")
            if metricas_vegetacion is not None
            else None
        )
        vegetacion_baja = (
            (ndvi is not None and ndvi < cls.UMBRAL_NDVI)
            or (evi is not None and evi < cls.UMBRAL_EVI)
        )
        rotacion_poaceae = cls._rotacion_poaceae_reciente(historial_cultivos)

        if deficit_precipitacion:
            reglas.append(
                "DEFICIT_PRECIPITACION: precipitación anualizada "
                f"{precipitacion_anualizada:.2f} mm < "
                f"{cls.UMBRAL_PRECIPITACION_ANUAL_MM:.2f} mm/año."
            )
        if deficit_humedad_suelo:
            reglas.append(
                "DEFICIT_HUMEDAD_SUELO: humedad media "
                f"{humedad_suelo:.3f} < {cls.UMBRAL_HUMEDAD_SUELO:.2f}."
            )
        if ndvi is not None and ndvi < cls.UMBRAL_NDVI:
            reglas.append(
                f"INDICE_VEGETACION_BAJO: NDVI medio {ndvi:.4f} < "
                f"{cls.UMBRAL_NDVI:.2f}; no es una medición de fertilidad."
            )
        if evi is not None and evi < cls.UMBRAL_EVI:
            reglas.append(
                f"INDICE_VEGETACION_BAJO: EVI medio {evi:.4f} < "
                f"{cls.UMBRAL_EVI:.2f}; no es una medición de fertilidad."
            )
        if rotacion_poaceae:
            reglas.append(
                "ROTACION_FAMILIA: los dos ciclos anuales más recientes "
                "clasificados son Poaceae; esto señala repetición de familia, "
                "no demuestra agotamiento de nitrógeno."
            )

        deficit_hidrico = deficit_precipitacion or deficit_humedad_suelo
        recomienda_fabaceae = rotacion_poaceae or (
            (deficit_hidrico or vegetacion_baja) and tiene_riego is False
        )
        familia_objetivo = "Fabaceae" if recomienda_fabaceae else "Poaceae"
        cultivo_por_altitud = cls._cultivo_por_altitud(
            familia_objetivo,
            elevacion_m,
        )
        if deficit_hidrico and tiene_riego is False:
            reglas.append(
                "ROTACION_DEFICIT_HIDRICO: regla heurística activada por "
                "precipitación/humedad y ausencia de riego reportada."
            )
        elif vegetacion_baja and tiene_riego is False and not rotacion_poaceae:
            reglas.append(
                "ROTACION_VEGETACION: índice de vegetación bajo sin riego "
                "reportado activa una preferencia heurística; no diagnostica "
                "déficit hídrico ni nutricional."
            )

        if elevacion_m is not None:
            reglas.append(
                f"CULTIVO_POR_ALTITUD: regla existente de {cls.UMBRAL_ELEVACION_M:.0f} m "
                f"aplicada a {elevacion_m:.1f} m."
            )

        metricas_disponibles = any(
            cls._numero(valor) is not None
            for valor in metricas_climaticas.values()
        )
        vegetacion_disponible = ndvi is not None or evi is not None
        historial_disponible = cls._historial_informativo(historial_cultivos)
        hay_evidencia = (
            precipitacion is not None
            or humedad_suelo is not None
            or vegetacion_disponible
            or rotacion_poaceae
        )
        confianza = cls._puntaje_confianza(
            metricas_disponibles=metricas_disponibles,
            vegetacion_disponible=vegetacion_disponible,
            elevacion_disponible=elevacion_m is not None,
            historial_disponible=historial_disponible,
            riego_conocido=tiene_riego is not None,
        )
        nivel_confianza = cls._nivel_confianza(confianza, hay_evidencia)
        indicadores = cls._indicadores(
            metricas_climaticas=metricas_climaticas,
            precipitacion_anualizada=precipitacion_anualizada,
            deficit_precipitacion=deficit_precipitacion,
            deficit_humedad_suelo=deficit_humedad_suelo,
            vegetacion_disponible=vegetacion_disponible,
            vegetacion_baja=vegetacion_baja,
            elevacion_m=elevacion_m,
            rotacion_poaceae=rotacion_poaceae,
            historial_disponible=historial_disponible,
            tiene_riego=tiene_riego,
        )

        if not hay_evidencia:
            return {
                "recommendations": [],
                "sufficient_information": False,
                "confidence_score": confianza,
                "confidence_level": nivel_confianza,
                "calculated_indicators": indicadores,
                "decision_rules": reglas,
            }

        recomendaciones = []
        for orden, (cultivo, familia) in enumerate(cls.CULTIVOS):
            puntaje = 50
            if familia == familia_objetivo:
                puntaje += 25
            else:
                puntaje -= 15
            if cultivo == cultivo_por_altitud:
                puntaje += 20

            factores = cls._factores_cultivo(
                cultivo=cultivo,
                familia=familia,
                familia_objetivo=familia_objetivo,
                cultivo_por_altitud=cultivo_por_altitud,
                indicadores=indicadores,
                tipo_suelo=tipo_suelo,
                tipo_riego=tipo_riego,
                fecha_siembra_deseada=fecha_siembra_deseada,
                temporada_siembra=temporada_siembra,
                objetivo_produccion=objetivo_produccion,
            )
            if cultivo == cultivo_por_altitud:
                razon = (
                    f"{cultivo} encabeza el ranking porque coincide con la "
                    "familia que priorizan las reglas existentes y, cuando hay "
                    "elevación disponible, con su umbral de altitud."
                )
            else:
                razon = (
                    f"{cultivo} es una alternativa del catálogo actual. Su "
                    "puntuación refleja concordancia con reglas heurísticas "
                    "compartidas, no tolerancias específicas validadas para el cultivo."
                )
            recomendaciones.append(
                {
                    "recommended_crop": cultivo,
                    "botanical_family": familia,
                    "score": float(max(0, min(100, puntaje))),
                    "confidence_score": float(confianza),
                    "confidence_level": nivel_confianza,
                    "reasoning": razon,
                    "triggered_rules": list(reglas),
                    "factors_evaluated": factores,
                    "_order": orden,
                }
            )

        recomendaciones.sort(key=lambda item: (-item["score"], item["_order"]))
        for recomendacion in recomendaciones:
            recomendacion.pop("_order")

        return {
            "recommendations": recomendaciones[:3],
            "sufficient_information": True,
            "confidence_score": confianza,
            "confidence_level": nivel_confianza,
            "calculated_indicators": indicadores,
            "decision_rules": reglas,
        }

    @staticmethod
    def _numero(valor) -> float | None:
        if isinstance(valor, (int, float)) and not isinstance(valor, bool):
            numero = float(valor)
            if numero == numero and abs(numero) != float("inf"):
                return numero
        return None

    @classmethod
    def _rotacion_poaceae_reciente(
        cls,
        historial: list[dict[str, Any]],
    ) -> bool:
        ciclos = [
            cultivo
            for cultivo in historial
            if cls._campo_historial(cultivo, "sowing_date") is not None
        ]
        ciclos.sort(
            key=lambda cultivo: cls._campo_historial(cultivo, "sowing_date"),
            reverse=True,
        )
        recientes = ciclos[: cls.CICLOS_POACEAE_PARA_ROTACION]
        return (
            len(recientes) == cls.CICLOS_POACEAE_PARA_ROTACION
            and all(
                cls._campo_historial(cultivo, "crop_cycle_type") == "annual"
                for cultivo in recientes
            )
            and all(
                (
                    cls._campo_historial(cultivo, "botanical_family") or ""
                ).strip().casefold()
                == "poaceae"
                for cultivo in recientes
            )
        )

    @staticmethod
    def _campo_historial(cultivo: dict[str, Any], nombre: str) -> Any:
        return (
            cultivo.get(nombre)
            if isinstance(cultivo, dict)
            else getattr(cultivo, nombre, None)
        )

    @classmethod
    def _historial_informativo(
        cls,
        historial: list[dict[str, Any]],
    ) -> bool:
        campos_relevantes = (
            "botanical_family",
            "crop_cycle_type",
            "sowing_date",
            "harvest_date",
            "yield_tons_per_ha",
        )
        return any(
            any(
                (valor := cls._campo_historial(cultivo, campo)) is not None
                and (not isinstance(valor, str) or bool(valor.strip()))
                for campo in campos_relevantes
            )
            for cultivo in historial
        )

    @classmethod
    def _cultivo_por_altitud(
        cls,
        familia: str,
        elevacion_m: float | None,
    ) -> str | None:
        if elevacion_m is None:
            return None
        altitud_alta = elevacion_m >= cls.UMBRAL_ELEVACION_M
        if familia == "Fabaceae":
            return "Haba" if altitud_alta else "Frijol"
        return "Avena" if altitud_alta else "Maíz"

    @staticmethod
    def _puntaje_confianza(
        *,
        metricas_disponibles: bool,
        vegetacion_disponible: bool,
        elevacion_disponible: bool,
        historial_disponible: bool,
        riego_conocido: bool,
    ) -> int:
        return (
            40 * metricas_disponibles
            + 15 * vegetacion_disponible
            + 15 * elevacion_disponible
            + 20 * historial_disponible
            + 10 * riego_conocido
        )

    @staticmethod
    def _nivel_confianza(puntaje: int, hay_evidencia: bool) -> str:
        if not hay_evidencia:
            return "Insuficiente"
        return "Media" if puntaje >= 65 else "Baja"

    @classmethod
    def _indicadores(
        cls,
        *,
        metricas_climaticas: dict[str, Any],
        precipitacion_anualizada: float | None,
        deficit_precipitacion: bool,
        deficit_humedad_suelo: bool,
        vegetacion_disponible: bool,
        vegetacion_baja: bool,
        elevacion_m: float | None,
        rotacion_poaceae: bool,
        historial_disponible: bool,
        tiene_riego: bool | None,
    ) -> dict[str, dict[str, Any]]:
        agua_conocida = (
            precipitacion_anualizada is not None
            or cls._numero(metricas_climaticas.get("avg_root_soil_moisture"))
            is not None
        )
        riesgo_agua = deficit_precipitacion or deficit_humedad_suelo
        if not agua_conocida:
            estado_agua = "desconocido"
            explicacion_agua = "No hay precipitación ni humedad de suelo utilizables."
        elif tiene_riego is True and riesgo_agua:
            estado_agua = "riesgo_con_riego_reportado"
            explicacion_agua = (
                "Se activó un umbral de precipitación o humedad aunque se reporte "
                "riego; no se verificó su suficiencia."
            )
        elif tiene_riego is True:
            estado_agua = "riego_reportado"
            explicacion_agua = (
                "El usuario reporta riego; su suficiencia y disponibilidad no se verifican."
            )
        elif riesgo_agua:
            estado_agua = "riesgo_estimado"
            explicacion_agua = (
                "Al menos un umbral heurístico de precipitación o humedad fue activado."
            )
        else:
            estado_agua = "sin_umbral_de_deficit"
            explicacion_agua = (
                "Los datos disponibles no activaron los umbrales existentes; "
                "esto no garantiza disponibilidad hídrica."
            )

        return {
            "disponibilidad_hidrica": {
                "status": estado_agua,
                "value": precipitacion_anualizada,
                "evidence_type": "estimated" if agua_conocida else "unavailable",
                "explanation": explicacion_agua,
            },
            "compatibilidad_altitudinal": {
                "status": (
                    "umbral_existente_aplicado"
                    if elevacion_m is not None
                    else "desconocida"
                ),
                "value": elevacion_m,
                "evidence_type": "estimated" if elevacion_m is not None else "unavailable",
                "explanation": (
                    f"Elevación puntual comparada con el umbral heurístico "
                    f"de {cls.UMBRAL_ELEVACION_M:.0f} m; no representa tolerancias "
                    "por cultivo."
                    if elevacion_m is not None
                    else "No se obtuvo elevación."
                ),
            },
            "compatibilidad_climatica": {
                "status": (
                    "datos_generales_disponibles"
                    if any(cls._numero(valor) is not None for valor in metricas_climaticas.values())
                    else "desconocida"
                ),
                "value": None,
                "evidence_type": (
                    "estimated"
                    if any(
                        cls._numero(valor) is not None
                        for valor in metricas_climaticas.values()
                    )
                    else "unavailable"
                ),
                "explanation": (
                    "Hay métricas climáticas del periodo, pero no existe un "
                    "catálogo de requerimientos climáticos por cultivo."
                    if any(
                        cls._numero(valor) is not None
                        for valor in metricas_climaticas.values()
                    )
                    else "No hay métricas climáticas utilizables para el periodo."
                ),
            },
            "compatibilidad_regional": {
                "status": "no_disponible",
                "value": None,
                "evidence_type": "unavailable",
                "explanation": "No hay servicio ni regionalización municipal implementados.",
            },
            "rotacion_familias": {
                "status": (
                    "repeticion_poaceae"
                    if rotacion_poaceae
                    else "sin_restriccion_detectada"
                    if historial_disponible
                    else "desconocida"
                ),
                "value": "Poaceae" if rotacion_poaceae else None,
                "evidence_type": "heuristic" if historial_disponible else "unavailable",
                "explanation": (
                    "Sólo ciclos recientes explícitamente clasificados como anuales "
                    "pueden activar esta señal; no infiere agotamiento nutricional."
                ),
            },
            "balance_nutricional": {
                "status": "no_determinado",
                "value": None,
                "evidence_type": "unavailable",
                "explanation": (
                    "El historial, el rendimiento y NDVI/EVI no sustituyen análisis "
                    "de suelo ni permiten estimar fertilidad."
                ),
            },
            "respuesta_vegetacion": {
                "status": (
                    "indice_bajo"
                    if vegetacion_baja
                    else "observada"
                    if vegetacion_disponible
                    else "desconocida"
                ),
                "value": None,
                "evidence_type": "measured" if vegetacion_disponible else "unavailable",
                "explanation": (
                    "NDVI/EVI describen respuesta espectral de vegetación; no son "
                    "análisis de agua ni fertilidad del suelo."
                ),
            },
        }

    @staticmethod
    def _factores_cultivo(
        *,
        cultivo: str,
        familia: str,
        familia_objetivo: str,
        cultivo_por_altitud: str | None,
        indicadores: dict[str, dict[str, Any]],
        tipo_suelo: str | None,
        tipo_riego: str | None,
        fecha_siembra_deseada: date | None,
        temporada_siembra: str | None,
        objetivo_produccion: str | None,
    ) -> list[dict[str, Any]]:
        coincide_familia = familia == familia_objetivo
        coincide_altitud = cultivo_por_altitud is not None and cultivo == cultivo_por_altitud
        return [
            {
                "factor": "reglas_existentes",
                "status": "priorizado" if coincide_familia else "alternativa",
                "value": familia,
                "explanation": (
                    "Coincide con la familia priorizada por reglas existentes."
                    if coincide_familia
                    else "No coincide con la familia priorizada; se conserva como alternativa."
                ),
            },
            {
                "factor": "altitud",
                "status": (
                    "coincide_con_umbral_heuristico"
                    if coincide_altitud
                    else "sin_compatibilidad_especifica"
                ),
                "value": cultivo_por_altitud,
                "explanation": (
                    "Seleccionado por el umbral de altitud del motor anterior; "
                    "no es una tolerancia validada del cultivo."
                    if coincide_altitud
                    else "No hay un rango altitudinal específico disponible para este cultivo."
                ),
            },
            {
                "factor": "agua",
                "status": indicadores["disponibilidad_hidrica"]["status"],
                "value": indicadores["disponibilidad_hidrica"]["value"],
                "explanation": indicadores["disponibilidad_hidrica"]["explanation"],
            },
            {
                "factor": "clima",
                "status": "sin_requerimientos_por_cultivo",
                "value": None,
                "explanation": indicadores["compatibilidad_climatica"]["explanation"],
            },
            {
                "factor": "region",
                "status": "no_disponible",
                "value": None,
                "explanation": indicadores["compatibilidad_regional"]["explanation"],
            },
            {
                "factor": "rotacion",
                "status": indicadores["rotacion_familias"]["status"],
                "value": indicadores["rotacion_familias"]["value"],
                "explanation": indicadores["rotacion_familias"]["explanation"],
            },
            {
                "factor": "nutricion",
                "status": "no_determinada",
                "value": None,
                "explanation": indicadores["balance_nutricional"]["explanation"],
            },
            {
                "factor": "temporada",
                "status": (
                    "reportada_no_evaluada"
                    if fecha_siembra_deseada is not None or temporada_siembra
                    else "no_disponible"
                ),
                "value": (
                    fecha_siembra_deseada.isoformat()
                    if fecha_siembra_deseada is not None
                    else temporada_siembra
                ),
                "explanation": "No existe calendario de siembra regional por cultivo.",
            },
            {
                "factor": "tipo_suelo",
                "status": "reportado_no_evaluado" if tipo_suelo else "desconocido",
                "value": tipo_suelo,
                "explanation": "No hay análisis ni requisitos de suelo por cultivo integrados.",
            },
            {
                "factor": "tipo_riego",
                "status": "reportado_no_evaluado" if tipo_riego else "desconocido",
                "value": tipo_riego,
                "explanation": "El tipo de riego no se usa para inferir caudal ni disponibilidad.",
            },
            {
                "factor": "objetivo_produccion",
                "status": (
                    "reportado_no_evaluado"
                    if objetivo_produccion
                    else "no_disponible"
                ),
                "value": objetivo_produccion,
                "explanation": "No hay datos de mercado, costos o requerimientos de alimentación animal.",
            },
        ]
