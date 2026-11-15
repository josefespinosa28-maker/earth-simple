<?php

use App\Http\Controllers\AnalisisParcelaController;
use App\Services\MotorAgroNasaService;
use Illuminate\Support\Facades\Route;

Route::post('/analisis-parcela', AnalisisParcelaController::class);

Route::get('/probar-nasa', function (MotorAgroNasaService $servicioAgro) {
    $parcelaPrueba = [
        'id'                   => 1,
        'latitud'              => 19.1600, // Ejemplo: Huejotzingo, Puebla
        'longitud'             => -98.4069,
        'superficie_hectareas' => 3.5,
        'tipo_suelo'           => null,
        'tiene_riego'          => false,
        'historial_cultivos'   => [
            [
                'crop_name'         => 'Maíz',
                'botanical_family'  => 'Poaceae',
                'sowing_date'       => '2025-05-10',
                'harvest_date'      => '2025-10-15',
                'yield_tons_per_ha' => 3.8,
            ],
        ],
    ];

    $resultado = $servicioAgro->analizarParcela($parcelaPrueba, '2025-01-01', '2025-12-31');

    return response()->json($resultado);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/evaluar-zona', function () {
        return view('evaluar-zona');
    })->name('evaluar-zona');
});
