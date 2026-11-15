<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsultaNasa extends Model
{
    use HasFactory;

    protected $table = 'consultas_nasa';

    protected $fillable = [
        'predio_id',
        'estado_respuesta',
        'fecha_inicio_datos',
        'fecha_fin_datos',
        'temp_promedio',
        'temp_max',
        'temp_min',
        'precipitacion_mm',
        'humedad_suelo',
    ];

    protected $casts = [
        'fecha_inicio_datos' => 'date',
        'fecha_fin_datos' => 'date',
        'temp_promedio' => 'decimal:2',
        'temp_max' => 'decimal:2',
        'temp_min' => 'decimal:2',
        'precipitacion_mm' => 'decimal:2',
        'humedad_suelo' => 'decimal:3',
    ];

    public function predio(): BelongsTo
    {
        return $this->belongsTo(Predio::class, 'predio_id');
    }
}
