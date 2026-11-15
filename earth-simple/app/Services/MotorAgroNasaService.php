<?php

namespace App\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use RuntimeException;

class MotorAgroNasaService
{
    protected string $urlBase;
    protected int $tiempoEspera;

    public function __construct()
    {
        $this->urlBase = rtrim(config('services.agro_engine.url'), '/');
        $this->tiempoEspera = (int) config('services.agro_engine.timeout', 25);
    }

    /** Envía los datos de un arreglo o modelo de parcela al microservicio FastAPI. */
    public function analizarParcela(array|Model $datosParcela, string $fechaInicio, string $fechaFin): array
    {
        if ($datosParcela instanceof Model) {
            $datosParcela = $datosParcela->toArray();
        }

        $latitud = $datosParcela['latitud'] ?? $datosParcela['latitude'] ?? null;
        $longitud = $datosParcela['longitud'] ?? $datosParcela['longitude'] ?? null;
        if (!is_numeric($latitud) || !is_numeric($longitud)) {
            throw new InvalidArgumentException('La parcela debe incluir latitud y longitud numéricas.');
        }

        // Armar el paquete respetando el contrato que espera FastAPI.
        $datosEnvio = [
            'parcel_id'      => $datosParcela['id'] ?? null,
            'latitude'       => (float) $latitud,
            'longitude'      => (float) $longitud,
            'area_hectares'  => isset($datosParcela['superficie_hectareas'])
                ? (float) $datosParcela['superficie_hectareas']
                : null,
            'soil_type'      => $datosParcela['tipo_suelo'] ?? null,
            'has_irrigation' => $datosParcela['tiene_riego'] ?? $datosParcela['has_irrigation'] ?? null,
            'irrigation_type' => $datosParcela['tipo_riego'] ?? $datosParcela['irrigation_type'] ?? null,
            'start_date'     => $fechaInicio,
            'end_date'       => $fechaFin,
            'crop_history'   => $datosParcela['historial_cultivos'] ?? $datosParcela['crop_history'] ?? [],
            'desired_sowing_date' => $datosParcela['fecha_siembra_deseada'] ?? $datosParcela['desired_sowing_date'] ?? null,
            'planting_season' => $datosParcela['temporada_siembra'] ?? $datosParcela['planting_season'] ?? null,
            'production_goal' => $datosParcela['objetivo_produccion'] ?? $datosParcela['production_goal'] ?? null,
            'environmental_data' => $datosParcela['environmental_data'] ?? null,
        ];

        try {
            $respuesta = Http::acceptJson()
                ->timeout($this->tiempoEspera)
                ->post("{$this->urlBase}/analysis/climate-preview", $datosEnvio);

            $respuesta->throw();

            $datosRespuesta = $respuesta->json();
            if (!is_array($datosRespuesta)) {
                throw new RuntimeException('El motor agroclimático devolvió una respuesta JSON no válida.');
            }
            return $datosRespuesta;

        } catch (RequestException $error) {
            if ($error->response->status() === 422) {
                $detalle = $error->response->json('detail')
                    ?? $error->response->json('message')
                    ?? $error->getMessage();
                if (is_array($detalle)) {
                    $detalle = json_encode($detalle, JSON_UNESCAPED_UNICODE);
                }
                throw new InvalidArgumentException(
                    'El motor agroclimático rechazó los datos enviados: ' . $detalle,
                    422,
                    $error
                );
            }
            throw new RuntimeException(
                'Error al comunicarse con el motor agroclimático de Python: ' . $error->getMessage(),
                $error->getCode(),
                $error
            );
        } catch (ConnectionException $error) {
            throw new RuntimeException(
                'Error al comunicarse con el motor agroclimático de Python: ' . $error->getMessage(),
                $error->getCode(),
                $error
            );
        }
    }
}