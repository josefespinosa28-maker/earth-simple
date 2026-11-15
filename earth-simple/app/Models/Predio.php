<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Predio extends Model
{
    use HasFactory;

    protected $table = 'predios';

    protected $fillable = [
        'user_id',
        'ubicacion_texto',
        'poligono',
        'centroide',
        'area_hectareas',
        'cultivo_actual',
        'cultivo_anterior',
    ];

    protected $casts = [
        'area_hectareas' => 'decimal:4',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function consultasNasa(): HasMany
    {
        return $this->hasMany(ConsultaNasa::class, 'predio_id');
    }

    public function rotacionesRecomendadas(): HasMany
    {
        return $this->hasMany(RotacionRecomendada::class, 'predio_id');
    }
}
