<?php

namespace App\Services;

use App\Models\ConsultaNasa;
use App\Models\Predio;
use App\Models\RotacionRecomendada;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class PredioService
{
    /**
     * Parámetros base de prueba tomados de routes/web.php (zona Huejotzingo / San Francisco Tepeyecac, Puebla).
     */
    public const LATITUD_PRUEBA = 19.1600;
    public const LONGITUD_PRUEBA = -98.4069;
    public const UBICACION_PRUEBA = 'San Francisco Tepeyecac, Puebla';
    public const FECHA_INICIO_PRUEBA = '2025-01-01';
    public const FECHA_FIN_PRUEBA = '2025-12-31';

    protected MotorAgroNasaService $motorAgroNasaService;

    public function __construct(MotorAgroNasaService $motorAgroNasaService)
    {
        $this->motorAgroNasaService = $motorAgroNasaService;
    }

    /**
     * Catálogo de cultivos anteriores con su familia botánica y tipo de ciclo para el motor de reglas en Python.
     */
    public static function catalogoCultivosAnteriores(): array
    {
        return [
            'Maíz' => [
                'etiqueta' => 'Maíz (Agota nitrógeno)',
                'familia_botanica' => 'Poaceae',
                'tipo_ciclo' => 'annual',
            ],
            'Frijol' => [
                'etiqueta' => 'Frijol (Fija nitrógeno)',
                'familia_botanica' => 'Fabaceae',
                'tipo_ciclo' => 'annual',
            ],
            'Trigo' => [
                'etiqueta' => 'Trigo (Cereal de grano)',
                'familia_botanica' => 'Poaceae',
                'tipo_ciclo' => 'annual',
            ],
            'Cebada' => [
                'etiqueta' => 'Cebada (Ciclo corto)',
                'familia_botanica' => 'Poaceae',
                'tipo_ciclo' => 'annual',
            ],
            'Haba' => [
                'etiqueta' => 'Haba (Leguminosa de altura)',
                'familia_botanica' => 'Fabaceae',
                'tipo_ciclo' => 'annual',
            ],
            'Avena' => [
                'etiqueta' => 'Avena (Forraje y grano)',
                'familia_botanica' => 'Poaceae',
                'tipo_ciclo' => 'annual',
            ],
        ];
    }

    /**
     * Calcula los metros cuadrados y las hectáreas a partir del ancho y largo en metros.
     */
    public function calcularSuperficie(float $anchoMetros, float $largoMetros): array
    {
        if ($anchoMetros <= 0 || $largoMetros <= 0) {
            throw new InvalidArgumentException('El ancho y el largo de la parcela deben ser mayores a 0 metros.');
        }

        $metrosCuadrados = round($anchoMetros * $largoMetros, 2);
        $areaHectareas = round($metrosCuadrados / 10000, 4);

        // Garantizar mínimo 0.0001 ha para cumplir chk_predios_area_positiva
        if ($areaHectareas <= 0) {
            $areaHectareas = 0.0001;
        }

        return [
            'metros_cuadrados' => $metrosCuadrados,
            'area_hectareas' => $areaHectareas,
        ];
    }

    /**
     * Consulta el microservicio de Python (NASA POWER + MODIS + SRTM y MotorReglasCultivos)
     * combinando los datos del agricultor con los parámetros de prueba de web.php.
     */
    public function consultarClimaParcela(
        float $anchoMetros,
        float $largoMetros,
        string $cultivoAnterior,
        ?string $cultivoActual = null
    ): array {
        $superficie = $this->calcularSuperficie($anchoMetros, $largoMetros);
        $catalogo = self::catalogoCultivosAnteriores();
        $familiaBotanica = $catalogo[$cultivoAnterior]['familia_botanica'] ?? 'Poaceae';
        $tipoCiclo = $catalogo[$cultivoAnterior]['tipo_ciclo'] ?? 'annual';

        // Enviamos dos ciclos anuales recientes del cultivo anterior para activar las reglas de rotación de familias en Python
        $parcelaPrueba = [
            'id' => 1,
            'latitud' => self::LATITUD_PRUEBA,
            'longitud' => self::LONGITUD_PRUEBA,
            'superficie_hectareas' => $superficie['area_hectareas'],
            'tipo_suelo' => null,
            'tiene_riego' => false,
            'historial_cultivos' => [
                [
                    'crop_name' => $cultivoAnterior,
                    'botanical_family' => $familiaBotanica,
                    'sowing_date' => '2024-05-10',
                    'harvest_date' => '2024-10-15',
                    'yield_tons_per_ha' => 3.6,
                    'crop_cycle_type' => $tipoCiclo,
                ],
                [
                    'crop_name' => $cultivoAnterior,
                    'botanical_family' => $familiaBotanica,
                    'sowing_date' => '2025-05-10',
                    'harvest_date' => '2025-10-15',
                    'yield_tons_per_ha' => 3.8,
                    'crop_cycle_type' => $tipoCiclo,
                ],
            ],
        ];

        $respuesta = $this->motorAgroNasaService->analizarParcela(
            $parcelaPrueba,
            self::FECHA_INICIO_PRUEBA,
            self::FECHA_FIN_PRUEBA
        );

        $tieneAdvertencias = !empty($respuesta['data_warnings']);
        $respuesta['estado_respuesta'] = $tieneAdvertencias ? 'parcial' : 'exitoso';
        $respuesta['fecha_inicio_datos'] = self::FECHA_INICIO_PRUEBA;
        $respuesta['fecha_fin_datos'] = self::FECHA_FIN_PRUEBA;
        $respuesta['superficie'] = $superficie;
        $respuesta['cultivo_anterior'] = $cultivoAnterior;
        $respuesta['cultivo_actual'] = $cultivoActual;

        return $respuesta;
    }

    /**
     * Guarda hasta el Paso 3 en una sola transacción de base de datos:
     * 1) Los datos iniciales del predio en `predios`
     * 2) Las métricas climáticas en `consultas_nasa`
     * 3) La recomendación de rotación seleccionada en `rotaciones_recomendadas`
     */
    public function guardarPredioYConsulta(
        int $userId,
        array $datosPredio,
        array $respuestaNasa,
        ?array $recomendacionElegida = null
    ): array {
        $anchoMetros = (float) ($datosPredio['ancho_metros'] ?? 50);
        $largoMetros = (float) ($datosPredio['largo_metros'] ?? 80);
        $superficie = $this->calcularSuperficie($anchoMetros, $largoMetros);

        $latitud = (float) ($respuestaNasa['coordinates_analyzed']['latitude'] ?? self::LATITUD_PRUEBA);
        $longitud = (float) ($respuestaNasa['coordinates_analyzed']['longitude'] ?? self::LONGITUD_PRUEBA);

        // Construir geometrías PostGIS válidas (SRID 4326) a partir del centroide y las dimensiones en metros
        $geometrias = $this->construirGeometriasWkt($latitud, $longitud, $anchoMetros, $largoMetros);

        // Determinar la recomendación elegida (por defecto la #1 de mayor puntaje de Python)
        $recomendacionElegida = $recomendacionElegida
            ?? ($respuestaNasa['recommendations'][0] ?? null);

        $cultivoRecomendadoNombre = $recomendacionElegida['recommended_crop'] ?? null;
        $cultivoActual = $datosPredio['cultivo_actual'] ?: $cultivoRecomendadoNombre;

        $metricas = $respuestaNasa['climate_metrics'] ?? [];

        return DB::transaction(function () use (
            $userId,
            $datosPredio,
            $superficie,
            $cultivoActual,
            $geometrias,
            $respuestaNasa,
            $metricas,
            $recomendacionElegida
        ) {
            // 1. Insertar en `predios`
            $predio = Predio::create([
                'user_id' => $userId,
                'ubicacion_texto' => $datosPredio['ubicacion_texto'] ?? self::UBICACION_PRUEBA,
                'area_hectareas' => $superficie['area_hectareas'],
                'cultivo_actual' => $cultivoActual,
                'cultivo_anterior' => $datosPredio['cultivo_anterior'] ?? 'Maíz',
                'poligono' => DB::raw("extensions.ST_GeomFromText('{$geometrias['poligono_wkt']}', 4326)"),
                'centroide' => DB::raw("extensions.ST_GeomFromText('{$geometrias['centroide_wkt']}', 4326)"),
            ]);

            // 2. Insertar en `consultas_nasa`
            $estadoRespuesta = $respuestaNasa['estado_respuesta'] ?? 'exitoso';
            if (!in_array($estadoRespuesta, ['exitoso', 'parcial', 'error'], true)) {
                $estadoRespuesta = 'exitoso';
            }

            $consultaNasa = ConsultaNasa::create([
                'predio_id' => $predio->id,
                'estado_respuesta' => $estadoRespuesta,
                'fecha_inicio_datos' => $respuestaNasa['fecha_inicio_datos'] ?? self::FECHA_INICIO_PRUEBA,
                'fecha_fin_datos' => $respuestaNasa['fecha_fin_datos'] ?? self::FECHA_FIN_PRUEBA,
                'temp_promedio' => isset($metricas['avg_temp_c']) ? (float) $metricas['avg_temp_c'] : null,
                'temp_max' => isset($metricas['max_temp_c']) ? (float) $metricas['max_temp_c'] : null,
                'temp_min' => isset($metricas['min_temp_c']) ? (float) $metricas['min_temp_c'] : null,
                'precipitacion_mm' => isset($metricas['total_precipitation_mm']) ? (float) $metricas['total_precipitation_mm'] : null,
                'humedad_suelo' => isset($metricas['avg_root_soil_moisture']) ? (float) $metricas['avg_root_soil_moisture'] : null,
            ]);

            // 3. Insertar en `rotaciones_recomendadas` la recomendación de rotación de cultivo
            $rotacionGuardada = null;
            if (!empty($recomendacionElegida['recommended_crop'])) {
                $puntaje = isset($recomendacionElegida['score']) && is_numeric($recomendacionElegida['score'])
                    ? max(0.0, min(100.0, (float) $recomendacionElegida['score']))
                    : null;

                $rotacionGuardada = RotacionRecomendada::create([
                    'predio_id' => $predio->id,
                    'cultivo_recomendado' => (string) $recomendacionElegida['recommended_crop'],
                    'familia_botanica' => isset($recomendacionElegida['botanical_family'])
                        ? (string) $recomendacionElegida['botanical_family']
                        : null,
                    'puntaje_compatibilidad' => $puntaje,
                ]);
            }

            return [
                'predio' => $predio,
                'consulta_nasa' => $consultaNasa,
                'rotacion_recomendada' => $rotacionGuardada,
            ];
        });
    }

    /**
     * Genera las cadenas WKT (Well-Known Text) para el punto centroide y el polígono rectangular
     * en coordenadas geográficas WGS84 (SRID 4326) respetando el orden (longitud latitud).
     */
    protected function construirGeometriasWkt(
        float $latitud,
        float $longitud,
        float $anchoMetros,
        float $largoMetros
    ): array {
        $metrosPorGradoLat = 111320.0;
        $metrosPorGradoLng = max(111320.0 * cos(deg2rad($latitud)), 10000.0);

        $semiAnchoLng = max(($anchoMetros / 2.0) / $metrosPorGradoLng, 0.00005);
        $semiLargoLat = max(($largoMetros / 2.0) / $metrosPorGradoLat, 0.00005);

        $minLng = round($longitud - $semiAnchoLng, 6);
        $maxLng = round($longitud + $semiAnchoLng, 6);
        $minLat = round($latitud - $semiLargoLat, 6);
        $maxLat = round($latitud + $semiLargoLat, 6);

        $centroLng = round($longitud, 6);
        $centroLat = round($latitud, 6);

        $poligonoWkt = sprintf(
            'POLYGON((%F %F, %F %F, %F %F, %F %F, %F %F))',
            $minLng,
            $minLat,
            $maxLng,
            $minLat,
            $maxLng,
            $maxLat,
            $minLng,
            $maxLat,
            $minLng,
            $minLat
        );

        $centroideWkt = sprintf('POINT(%F %F)', $centroLng, $centroLat);

        return [
            'poligono_wkt' => $poligonoWkt,
            'centroide_wkt' => $centroideWkt,
        ];
    }
}
