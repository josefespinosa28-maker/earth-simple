<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class RotacionRecomendada extends Model
{
    use HasFactory;

    protected $table = 'rotaciones_recomendadas';

    protected $fillable = [
        'predio_id',
        'cultivo_recomendado',
        'familia_botanica',
        'puntaje_compatibilidad',
    ];

    protected $casts = [
        'puntaje_compatibilidad' => 'decimal:2',
    ];

    public function predio(): BelongsTo
    {
        return $this->belongsTo(Predio::class, 'predio_id');
    }
}


