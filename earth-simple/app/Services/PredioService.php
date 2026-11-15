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
     * Catálogo de cultivos anteriores con su familia botánica para el contrato de FastAPI.
     */
    public static function catalogoCultivosAnteriores(): array
    {
        return [
            'Maíz' => [
                'etiqueta' => 'Maíz (Agota nitrógeno)',
                'familia_botanica' => 'Poaceae',
            ],
            'Frijol' => [
                'etiqueta' => 'Frijol (Fija nitrógeno)',
                'familia_botanica' => 'Fabaceae',
            ],
            'Trigo' => [
                'etiqueta' => 'Trigo (Cereal de grano)',
                'familia_botanica' => 'Poaceae',
            ],
            'Cebada' => [
                'etiqueta' => 'Cebada (Ciclo corto)',
                'familia_botanica' => 'Poaceae',
            ],
            'Haba' => [
                'etiqueta' => 'Haba (Leguminosa de altura)',
                'familia_botanica' => 'Fabaceae',
            ],
            'Calabaza' => [
                'etiqueta' => 'Calabaza (Cobertura de suelo)',
                'familia_botanica' => 'Cucurbitaceae',
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
     * Consulta el microservicio de Python (NASA POWER) combinando los datos del agricultor
     * con los parámetros de prueba de web.php.
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
                    'sowing_date' => '2025-05-10',
                    'harvest_date' => '2025-10-15',
                    'yield_tons_per_ha' => 3.8,
                ],
            ],
        ];

        $respuesta = $this->motorAgroNasaService->analizarParcela(
            $parcelaPrueba,
            self::FECHA_INICIO_PRUEBA,
            self::FECHA_FIN_PRUEBA
        );

        $respuesta['estado_respuesta'] = 'exitoso';
        $respuesta['fecha_inicio_datos'] = self::FECHA_INICIO_PRUEBA;
        $respuesta['fecha_fin_datos'] = self::FECHA_FIN_PRUEBA;
        $respuesta['superficie'] = $superficie;
        $respuesta['cultivo_anterior'] = $cultivoAnterior;
        $respuesta['cultivo_actual'] = $cultivoActual;

        return $respuesta;
    }

    /**
     * Guarda en una sola transacción de base de datos el registro en `predios`
     * y el resultado climático en `consultas_nasa` (más `rotaciones_recomendadas` si existen).
     */
    public function guardarPredioYConsulta(
        int $userId,
        array $datosPredio,
        array $respuestaNasa
    ): array {
        $anchoMetros = (float) ($datosPredio['ancho_metros'] ?? 50);
        $largoMetros = (float) ($datosPredio['largo_metros'] ?? 80);
        $superficie = $this->calcularSuperficie($anchoMetros, $largoMetros);

        $latitud = (float) ($respuestaNasa['coordinates_analyzed']['latitude'] ?? self::LATITUD_PRUEBA);
        $longitud = (float) ($respuestaNasa['coordinates_analyzed']['longitude'] ?? self::LONGITUD_PRUEBA);

        // Construir geometrías PostGIS válidas (SRID 4326) a partir del centroide y las dimensiones en metros
        $geometrias = $this->construirGeometriasWkt($latitud, $longitud, $anchoMetros, $largoMetros);

        $recomendacionPrincipal = $respuestaNasa['recommendations'][0]['recommended_crop'] ?? null;
        $cultivoActual = $datosPredio['cultivo_actual'] ?: $recomendacionPrincipal;

        $metricas = $respuestaNasa['climate_metrics'] ?? [];

        return DB::transaction(function () use (
            $userId,
            $datosPredio,
            $superficie,
            $cultivoActual,
            $geometrias,
            $respuestaNasa,
            $metricas
        ) {
            $predio = Predio::create([
                'user_id' => $userId,
                'ubicacion_texto' => $datosPredio['ubicacion_texto'] ?? self::UBICACION_PRUEBA,
                'area_hectareas' => $superficie['area_hectareas'],
                'cultivo_actual' => $cultivoActual,
                'cultivo_anterior' => $datosPredio['cultivo_anterior'] ?? 'Maíz',
                'poligono' => DB::raw("extensions.ST_GeomFromText('{$geometrias['poligono_wkt']}', 4326)"),
                'centroide' => DB::raw("extensions.ST_GeomFromText('{$geometrias['centroide_wkt']}', 4326)"),
            ]);

            $consultaNasa = ConsultaNasa::create([
                'predio_id' => $predio->id,
                'estado_respuesta' => $respuestaNasa['estado_respuesta'] ?? 'exitoso',
                'fecha_inicio_datos' => $respuestaNasa['fecha_inicio_datos'] ?? self::FECHA_INICIO_PRUEBA,
                'fecha_fin_datos' => $respuestaNasa['fecha_fin_datos'] ?? self::FECHA_FIN_PRUEBA,
                'temp_promedio' => isset($metricas['avg_temp_c']) ? (float) $metricas['avg_temp_c'] : null,
                'temp_max' => isset($metricas['max_temp_c']) ? (float) $metricas['max_temp_c'] : null,
                'temp_min' => isset($metricas['min_temp_c']) ? (float) $metricas['min_temp_c'] : null,
                'precipitacion_mm' => isset($metricas['total_precipitation_mm']) ? (float) $metricas['total_precipitation_mm'] : null,
                'humedad_suelo' => isset($metricas['avg_root_soil_moisture']) ? (float) $metricas['avg_root_soil_moisture'] : null,
            ]);

            $rotacionesGuardadas = [];
            if (!empty($respuestaNasa['recommendations']) && is_array($respuestaNasa['recommendations'])) {
                foreach ($respuestaNasa['recommendations'] as $rec) {
                    if (!empty($rec['recommended_crop'])) {
                        $rotacionesGuardadas[] = RotacionRecomendada::create([
                            'predio_id' => $predio->id,
                            'cultivo_recomendado' => (string) $rec['recommended_crop'],
                            'familia_botanica' => isset($rec['botanical_family']) ? (string) $rec['botanical_family'] : null,
                            'puntaje_compatibilidad' => isset($rec['score']) && is_numeric($rec['score'])
                                ? (float) $rec['score']
                                : null,
                        ]);
                    }
                }
            }

            return [
                'predio' => $predio,
                'consulta_nasa' => $consultaNasa,
                'rotaciones' => $rotacionesGuardadas,
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
        // Conversión aproximada de metros a grados en la latitud de la parcela
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

        // En WKT PostGIS el anillo exterior del polígono debe cerrar en el primer vértice
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
