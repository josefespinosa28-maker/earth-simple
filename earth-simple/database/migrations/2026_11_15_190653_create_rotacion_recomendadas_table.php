<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */

    public function up(): void
    {
        Schema::create('rotaciones_recomendadas', function (Blueprint $table) {
            $table->id();

            // Relación con el terreno evaluado.
            $table->foreignId('predio_id')
                ->constrained('predios')
                ->cascadeOnDelete();

            // Cultivo principal recomendado.
            $table->string('cultivo_recomendado', 100);
            $table->string('familia_botanica', 100)->nullable();

            $table->decimal('puntaje_compatibilidad', 5, 2)->nullable();
            $table->timestamps();

        });

        // El puntaje debe estar entre 0 y 100.
        DB::statement("
        ALTER TABLE rotaciones_recomendadas
        ADD CONSTRAINT chk_rotaciones_puntaje
        CHECK (
            puntaje_compatibilidad IS NULL
            OR puntaje_compatibilidad BETWEEN 0 AND 100
        )
    ");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rotaciones_recomendadas');
    }
};