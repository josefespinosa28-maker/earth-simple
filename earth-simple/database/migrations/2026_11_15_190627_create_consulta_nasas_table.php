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
        Schema::create('consultas_nasa', function (Blueprint $table) {
            $table->id();

            // Relación con predios: al eliminar un predio, se eliminan sus consultas.
            $table->foreignId('predio_id')
                ->constrained('predios')
                ->cascadeOnDelete();

            // Estado de la petición.
            $table->string('estado_respuesta', 20)->default('exitoso');

            // Periodo que cubren los datos recibidos.
            $table->date('fecha_inicio_datos')->nullable();
            $table->date('fecha_fin_datos')->nullable();

            // Métricas climáticas devueltas por el motor de Python (NASA POWER).
            $table->decimal('temp_promedio', 5, 2)->nullable();
            $table->decimal('temp_max', 5, 2)->nullable();
            $table->decimal('temp_min', 5, 2)->nullable();
            $table->decimal('precipitacion_mm', 8, 2)->nullable();
            $table->decimal('humedad_suelo', 5, 3)->nullable();

            $table->timestamps();

            // Índice para consultar el historial por predio y fecha.
            $table->index(
                ['predio_id', 'created_at'],
                'idx_consultas_nasa_predio_servicio'
            );
        });

        // Validar los estados permitidos.
        DB::statement("
            ALTER TABLE consultas_nasa
            ADD CONSTRAINT chk_estado_respuesta
            CHECK (estado_respuesta IN ('exitoso', 'parcial', 'error'))
        ");

        // Validar que las fechas no estén invertidas.
        DB::statement("
            ALTER TABLE consultas_nasa
            ADD CONSTRAINT chk_fechas_consultas_nasa
            CHECK (
                fecha_inicio_datos IS NULL
                OR fecha_fin_datos IS NULL
                OR fecha_inicio_datos <= fecha_fin_datos
            )
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultas_nasa');
    }
};
