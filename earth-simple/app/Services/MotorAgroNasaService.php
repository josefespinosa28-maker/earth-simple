<?php

namespace App\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
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

    /**
     * Envía un arreglo con los datos de la parcela al microservicio en FastAPI.
     */
    public function analizarParcela(array $datosParcela, string $fechaInicio, string $fechaFin): array
    {
        // Armar el paquete respetando el contrato que espera FastAPI
        $datosEnvio = [
            'parcel_id'      => $datosParcela['id'] ?? 1,
            'latitude'       => (float) $datosParcela['latitud'],
            'longitude'      => (float) $datosParcela['longitud'],
            'area_hectares'  => (float) ($datosParcela['superficie_hectareas'] ?? 1.0),
            'soil_type'      => $datosParcela['tipo_suelo'] ?? null,
            'has_irrigation' => (bool) ($datosParcela['tiene_riego'] ?? false),
            'start_date'     => $fechaInicio,
            'end_date'       => $fechaFin,
            'crop_history'   => $datosParcela['historial_cultivos'] ?? [],
        ];

        try {
            $respuesta = Http::acceptJson()
                ->timeout($this->tiempoEspera)
                ->post("{$this->urlBase}/analysis/climate-preview", $datosEnvio);

            $respuesta->throw();

            return $respuesta->json();

        } catch (RequestException $error) {
            throw new RuntimeException(
                'Error al comunicarse con el motor agroclimático de Python: ' . $error->getMessage(),
                $error->getCode(),
                $error
            );
        }
    }
}