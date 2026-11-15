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
        DB::statement('CREATE EXTENSION IF NOT EXISTS postgis');

        Schema::create('predios', function (Blueprint $table) {
            $table->id();
            // Relación obligatoria con el usuario registrado (si se borra el usuario, se borran sus predios)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->string('ubicacion_texto', 150)->nullable();

            // Superficie calculada en hectáreas (ej. 12.5000 ha)
            $table->decimal('area_hectareas', 10, 4);

            // Contexto agrícola proporcionado por el agricultor
            $table->string('cultivo_actual', 100)->nullable();
            $table->string('cultivo_anterior', 100)->nullable();

            $table->timestamps();
        });


        DB::statement('ALTER TABLE predios ADD COLUMN poligono extensions.GEOMETRY(Polygon, 4326) NOT NULL');

        DB::statement('ALTER TABLE predios ADD COLUMN centroide extensions.GEOMETRY(Point, 4326) NOT NULL');

        // 4. Añadimos restricciones de integridad y validación geométrica
        DB::statement("
            ALTER TABLE predios
            ADD CONSTRAINT chk_predios_area_positiva
            CHECK (area_hectareas > 0)
        ");

        DB::statement("
            ALTER TABLE predios
            ADD CONSTRAINT chk_predios_poligono_valido
            CHECK (extensions.ST_IsValid(poligono))
        ");

        // 5. Creamos los índices espaciales GIST para consultas rápidas en el mapa
        DB::statement('CREATE INDEX idx_predios_poligono_gist ON predios USING GIST (poligono)');

        DB::statement('CREATE INDEX idx_predios_centroide_gist ON predios USING GIST (centroide)');

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('predios');
    }
};
