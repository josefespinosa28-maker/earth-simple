<?php

namespace App\Livewire;

use App\Services\PredioService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Throwable;

class EvaluadorParcela extends Component
{
    public int $paso = 1;

    public string $ubicacionTexto = PredioService::UBICACION_PRUEBA;
    public float $latitud = PredioService::LATITUD_PRUEBA;
    public float $longitud = PredioService::LONGITUD_PRUEBA;

    public $anchoMetros = 50;
    public $largoMetros = 80;
    public string $cultivoAnterior = 'Maíz';
    public ?string $cultivoActual = null;

    public ?array $respuestaNasa = null;
    public ?string $mensajeError = null;

    public bool $guardadoExitoso = false;
    public ?int $predioGuardadoId = null;
    public ?int $consultaNasaGuardadaId = null;

    protected function rules(): array
    {
        return [
            'anchoMetros' => ['required', 'numeric', 'min:1', 'max:50000'],
            'largoMetros' => ['required', 'numeric', 'min:1', 'max:50000'],
            'cultivoAnterior' => ['required', 'string', 'max:100'],
            'cultivoActual' => ['nullable', 'string', 'max:100'],
        ];
    }

    protected function messages(): array
    {
        return [
            'anchoMetros.required' => 'Ingresa el ancho de tu parcela en metros.',
            'anchoMetros.numeric' => 'El ancho debe ser un número válido.',
            'anchoMetros.min' => 'El ancho debe ser de al menos 1 metro.',
            'largoMetros.required' => 'Ingresa el largo de tu parcela en metros.',
            'largoMetros.numeric' => 'El largo debe ser un número válido.',
            'largoMetros.min' => 'El largo debe ser de al menos 1 metro.',
            'cultivoAnterior.required' => 'Selecciona el cultivo anterior de la parcela.',
        ];
    }

    public function getMetrosCuadradosProperty(): float
    {
        $ancho = is_numeric($this->anchoMetros) ? (float) $this->anchoMetros : 0.0;
        $largo = is_numeric($this->largoMetros) ? (float) $this->largoMetros : 0.0;

        return max(round($ancho * $largo, 2), 0.0);
    }

    public function getAreaHectareasProperty(): float
    {
        return round($this->metrosCuadrados / 10000, 4);
    }

    /**
     * Paso 1 -> Paso 2: Valida las medidas e invoca el servicio de Python (NASA POWER) mediante PredioService.
     */
    public function analizarPasoUno(PredioService $predioService): void
    {
        $this->mensajeError = null;
        $this->guardadoExitoso = false;
        $this->validate();

        try {
            $this->respuestaNasa = $predioService->consultarClimaParcela(
                (float) $this->anchoMetros,
                (float) $this->largoMetros,
                $this->cultivoAnterior,
                $this->cultivoActual
            );

            $this->paso = 2;
        } catch (Throwable $error) {
            $this->mensajeError = $error->getMessage();
        }
    }

    /**
     * Regresa al Paso 1 para ajustar dimensiones o cultivo anterior.
     */
    public function volverPasoUno(): void
    {
        $this->mensajeError = null;
        $this->paso = 1;
    }

    /**
     * Ejecuta la transacción en PredioService para guardar el Predio y la ConsultaNasa en la base de datos.
     */
    public function guardarEvaluacion(PredioService $predioService): void
    {
        $this->mensajeError = null;

        if (empty($this->respuestaNasa)) {
            $this->mensajeError = 'Primero debes ejecutar el diagnóstico satelital en el Paso 1.';
            $this->paso = 1;
            return;
        }

        $userId = Auth::id();
        if (!$userId) {
            $this->mensajeError = 'Debes iniciar sesión para guardar el predio.';
            return;
        }

        try {
            $resultado = $predioService->guardarPredioYConsulta(
                (int) $userId,
                [
                    'ubicacion_texto' => $this->ubicacionTexto,
                    'ancho_metros' => (float) $this->anchoMetros,
                    'largo_metros' => (float) $this->largoMetros,
                    'cultivo_anterior' => $this->cultivoAnterior,
                    'cultivo_actual' => $this->cultivoActual,
                ],
                $this->respuestaNasa
            );

            $this->predioGuardadoId = $resultado['predio']->id;
            $this->consultaNasaGuardadaId = $resultado['consulta_nasa']->id;
            $this->guardadoExitoso = true;
            $this->paso = 3;
        } catch (Throwable $error) {
            $this->mensajeError = 'No se pudo guardar la evaluación en la base de datos: ' . $error->getMessage();
        }
    }

    /**
     * Reinicia el flujo para registrar otra parcela.
     */
    public function nuevaEvaluacion(): void
    {
        $this->reset([
            'respuestaNasa',
            'mensajeError',
            'guardadoExitoso',
            'predioGuardadoId',
            'consultaNasaGuardadaId',
        ]);
        $this->anchoMetros = 50;
        $this->largoMetros = 80;
        $this->cultivoAnterior = 'Maíz';
        $this->paso = 1;
    }

    public function render()
    {
        return view('livewire.evaluador-parcela', [
            'catalogoCultivos' => PredioService::catalogoCultivosAnteriores(),
        ]);
    }
}
