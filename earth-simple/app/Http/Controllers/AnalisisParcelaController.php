<?php

namespace App\Http\Controllers;

use App\Services\MotorAgroNasaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;
use RuntimeException;

class AnalisisParcelaController extends Controller
{
    public function __invoke(Request $solicitud, MotorAgroNasaService $servicioAgro): JsonResponse
    {
        $datosParcela = $solicitud->input(
            'parcela',
            $solicitud->except(['fecha_inicio', 'fecha_fin', 'start_date', 'end_date'])
        );
        $fechaInicio = $solicitud->input('start_date', $solicitud->input('fecha_inicio'));
        $fechaFin = $solicitud->input('end_date', $solicitud->input('fecha_fin'));

        $validacion = Validator::make(
            [
                'parcela' => $datosParcela,
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin,
            ],
            [
                'parcela' => ['required', 'array'],
                'fecha_inicio' => ['required', 'date_format:Y-m-d'],
                'fecha_fin' => ['required', 'date_format:Y-m-d', 'after_or_equal:fecha_inicio'],
            ]
        );
        if ($validacion->fails()) {
            return response()->json(['message' => 'Los datos enviados no son válidos.', 'errors' => $validacion->errors()], 422);
        }

        try {
            return response()->json(
                $servicioAgro->analizarParcela($datosParcela, $fechaInicio, $fechaFin)
            );
        } catch (InvalidArgumentException $error) {
            return response()->json(['message' => $error->getMessage()], 422);
        } catch (RuntimeException $error) {
            return response()->json(['message' => $error->getMessage()], 502);
        }
    }
}
