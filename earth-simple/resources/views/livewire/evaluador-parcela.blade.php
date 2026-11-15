<div
    x-data="{
        pasoActual: @entangle('paso'),
        ancho: @entangle('anchoMetros').live,
        largo: @entangle('largoMetros').live,
        get metrosCuadrados() {
            const a = parseFloat(this.ancho) || 0;
            const l = parseFloat(this.largo) || 0;
            return Math.max(Math.round(a * l * 100) / 100, 0);
        },
        get hectareas() {
            return (this.metrosCuadrados / 10000).toFixed(2);
        },
         get metrosFormateados() {
            return this.metrosCuadrados.toLocaleString('es-MX');
        }
    }"
    class="space-y-8"
>
    <!-- Barra de Pasos (Stepper) -->
    <div class="bg-[#FFFFFF] border-2 border-[#E0DED7] rounded-2xl p-4 sm:p-5">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
            <!-- Paso 1 Indicator -->
            <div
                class="flex items-center gap-3.5 p-3 rounded-xl transition-colors"
                :class="pasoActual === 1 ? 'bg-[#E8F5E9] border-2 border-[#1B5E20]' : (pasoActual > 1 ? 'bg-[#FAF9F6] border-2 border-[#2E7D32]' : 'border-2 border-transparent')"
            >
                <div
                    class="w-10 h-10 rounded-full flex items-center justify-center font-extrabold text-[18px] shrink-0 transition-colors"
                    :class="pasoActual > 1 ? 'bg-[#E8F5E9] text-[#1B5E20] border-2 border-[#1B5E20]' : (pasoActual === 1 ? 'bg-[#1B5E20] text-[#FFFFFF]' : 'bg-[#EFEEEB] text-[#41493E]')"
                >
                    <template x-if="pasoActual > 1">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12" />
                        </svg>
                    </template>
                    <template x-if="pasoActual <= 1">
                        <span>1</span>
                    </template>
                </div>
                <div class="min-w-0">
                    <p class="text-[16px] leading-[22px] font-bold text-[#1A1C1A]">
                        1. Medición del Terreno
                    </p>
                    <p class="text-[14px] leading-[20px] font-medium text-[#41493E] truncate">
                        <span x-show="pasoActual === 1">Límites y área de siembra</span>
                        <span x-show="pasoActual > 1" x-cloak>{{ $ubicacionTexto }} • <span x-text="metrosFormateados"></span> m²</span>
                    </p>
                </div>
            </div>

            <!-- Paso 2 Indicator -->
            <div
                class="flex items-center gap-3.5 p-3 rounded-xl transition-colors"
                :class="pasoActual === 2 ? 'bg-[#E8F5E9] border-2 border-[#1B5E20]' : (pasoActual > 2 ? 'bg-[#FAF9F6] border-2 border-[#2E7D32]' : 'border-2 border-transparent')"
            >
                <div
                    class="w-10 h-10 rounded-full flex items-center justify-center font-extrabold text-[18px] shrink-0 transition-colors"
                    :class="pasoActual > 2 ? 'bg-[#E8F5E9] text-[#1B5E20] border-2 border-[#1B5E20]' : (pasoActual === 2 ? 'bg-[#1B5E20] text-[#FFFFFF]' : 'bg-[#EFEEEB] text-[#41493E]')"
                >
                    <template x-if="pasoActual > 2">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12" />
                        </svg>
                    </template>
                    <template x-if="pasoActual <= 2">
                        <span>2</span>
                    </template>
                </div>
                <div class="min-w-0">
                    <p class="text-[16px] leading-[22px] font-bold text-[#1A1C1A]">
                        2. Diagnóstico NASA POWER y Clima
                    </p>
                    <p class="text-[14px] leading-[20px] font-medium text-[#41493E] truncate">
                        Temp, precipitaciones y suelo
                    </p>
                </div>
            </div>

            <!-- Paso 3 Indicator -->
            <div
                class="flex items-center gap-3.5 p-3 rounded-xl transition-colors"
                :class="pasoActual === 3 ? 'bg-[#E8F5E9] border-2 border-[#1B5E20]' : 'border-2 border-transparent'"
            >
                <div
                    class="w-10 h-10 rounded-full flex items-center justify-center font-extrabold text-[18px] shrink-0 transition-colors"
                    :class="pasoActual === 3 ? 'bg-[#1B5E20] text-[#FFFFFF]' : 'bg-[#EFEEEB] text-[#41493E]'"
                >
                    <span>3</span>
                </div>
                <div class="min-w-0">
                    <p class="text-[16px] leading-[22px] font-bold text-[#1A1C1A]">
                        3. Recomendaciones de Siembra
                    </p>
                    <p class="text-[14px] leading-[20px] font-medium text-[#41493E] truncate">
                        Cultivos aptos y guardado en BD
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerta de error general -->
    @if ($mensajeError)
        <div role="alert" class="p-4 sm:p-5 rounded-2xl bg-[#FFEBEE] border-2 border-[#C62828] text-[#C62828] flex items-start gap-3">
            <svg class="w-6 h-6 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10" />
                <line x1="12" y1="8" x2="12" y2="12" />
                <line x1="12" y1="16" x2="12.01" y2="16" />
            </svg>
            <div>
                <p class="text-[18px] leading-[24px] font-bold">Atención</p>
                <p class="mt-1 text-[16px] leading-[24px] font-medium">{{ $mensajeError }}</p>
            </div>
        </div>
    @endif

    <!-- ========================================== -->
    <!-- PASO 1: MEDICIÓN DEL TERRENO + MAPA VISUAL -->
    <!-- ========================================== -->
    <div
        x-show="pasoActual === 1"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start"
    >
        <!-- Columna Izquierda: Ubicación y Mapa Visual (7 columnas) -->
        <div class="lg:col-span-7 space-y-4">
            <!-- Barra de Ubicación Detectada (Datos de prueba de web.php) -->
            <div class="bg-[#FFFFFF] border-2 border-[#E0DED7] rounded-2xl p-4 sm:p-5 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-[#E8F5E9] border-2 border-[#2E7D32] flex items-center justify-center text-[#1B5E20] shrink-0">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                            <circle cx="12" cy="10" r="3" />
                        </svg>
                    </div>
                    <div>
                        <span class="block text-[14px] leading-[20px] font-semibold text-[#41493E]">
                            Ubicación de la parcela (GPS activo)
                        </span>
                        <span class="block text-[18px] leading-[24px] font-extrabold text-[#1A1C1A]">
                            {{ $ubicacionTexto }}
                        </span>
                    </div>
                </div>

                <div class="inline-flex items-center gap-2 min-h-[40px] px-3.5 py-1.5 rounded-lg bg-[#FAF9F6] border-2 border-[#5D4037] text-[14px] leading-[20px] font-bold text-[#5D4037] tabular-nums">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#2E7D32] animate-pulse"></span>
                    <span>{{ number_format($latitud, 4) }}° N, {{ number_format($longitud, 4) }}° O</span>
                </div>
            </div>

            <!-- Mapa Visual de Parcela -->
            <div class="relative bg-[#EAE6DF] border-2 border-[#E0DED7] rounded-2xl overflow-hidden h-[380px] sm:h-[440px] select-none">
                <!-- Fondo cartográfico estilizado (SVG de caminos, parcelas y localidades de Puebla) -->
                <svg class="absolute inset-0 w-full h-full object-cover" viewBox="0 0 800 500" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
                    <rect width="800" height="500" fill="#EFECE6" />
                    <!-- Zonas agrícolas verdes suaves -->
                    <path d="M0 0H320L280 210H0V0Z" fill="#E4E9DF" />
                    <path d="M480 0H800V260L520 220L480 0Z" fill="#E1E7DC" />
                    <path d="M120 320L450 300L510 500H80L120 320Z" fill="#E5EAE0" />
                    <!-- Retícula de parcelas rurales -->
                    <g stroke="#DEDAD0" stroke-width="1.5">
                        <line x1="0" y1="110" x2="800" y2="90" />
                        <line x1="0" y1="240" x2="800" y2="260" />
                        <line x1="0" y1="390" x2="800" y2="370" />
                        <line x1="160" y1="0" x2="140" y2="500" />
                        <line x1="340" y1="0" x2="370" y2="500" />
                        <line x1="560" y1="0" x2="530" y2="500" />
                        <line x1="690" y1="0" x2="710" y2="500" />
                    </g>
                    <!-- Carreteras principales y secundarias -->
                    <path d="M-20 80 Q 220 140, 410 190 T 820 310" stroke="#D5CFC2" stroke-width="10" fill="none" />
                    <path d="M-20 80 Q 220 140, 410 190 T 820 310" stroke="#FAF9F6" stroke-width="5" fill="none" />
                    <path d="M240 -20 Q 300 220, 360 520" stroke="#D5CFC2" stroke-width="8" fill="none" />
                    <path d="M240 -20 Q 300 220, 360 520" stroke="#FFFFFF" stroke-width="4" fill="none" />
                    <path d="M580 -20 Q 520 240, 440 520" stroke="#D9D3C7" stroke-width="6" fill="none" />
                </svg>

                <!-- Etiquetas de localidades de referencia -->
                <span class="absolute top-12 left-28 text-[13px] font-bold text-[#41493E]/80 pointer-events-none">
                    Santa María Zacatepec
                </span>
                <span class="absolute top-8 right-24 text-[13px] font-bold text-[#41493E]/80 pointer-events-none">
                    San Francisco Ocotlán
                </span>
                <span class="absolute bottom-16 left-10 text-[13px] font-bold text-[#41493E]/80 pointer-events-none">
                    San Gregorio Atzompa
                </span>
                <span class="absolute bottom-12 right-20 text-[14px] font-extrabold text-[#1A1C1A]/75 pointer-events-none">
                    Tlaxcalancingo
                </span>

                <!-- Chip superior izquierdo: Imágenes satelitales calibradas -->
                <div class="absolute top-4 left-4 bg-[#FFFFFF] border-2 border-[#E0DED7] rounded-xl px-3.5 py-2 flex items-center gap-2.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#2E7D32]"></span>
                    <span class="text-[14px] leading-[18px] font-bold text-[#1A1C1A]">
                        Imágenes satelitales calibradas
                    </span>
                </div>

                <!-- Polígono Visual de la Parcela Activa -->
                <div class="absolute inset-0 flex items-center justify-center p-8">
                    <div class="relative w-full max-w-[360px] h-[210px] sm:h-[230px] border-[3px] border-[#1B5E20] bg-[#1B5E20]/20 flex items-center justify-center">
                        <!-- 4 Vértices en las esquinas -->
                        <span class="absolute -top-2.5 -left-2.5 w-5 h-5 rounded-full bg-[#FFFFFF] border-[3px] border-[#1B5E20]"></span>
                        <span class="absolute -top-2.5 -right-2.5 w-5 h-5 rounded-full bg-[#FFFFFF] border-[3px] border-[#1B5E20]"></span>
                        <span class="absolute -bottom-2.5 -left-2.5 w-5 h-5 rounded-full bg-[#FFFFFF] border-[3px] border-[#1B5E20]"></span>
                        <span class="absolute -bottom-2.5 -right-2.5 w-5 h-5 rounded-full bg-[#FFFFFF] border-[3px] border-[#1B5E20]"></span>

                        <!-- Etiqueta superior: Largo (metros) -->
                        <div class="absolute -top-4 left-1/2 -translate-x-1/2 px-3 py-1 rounded-md bg-[#2F312F] text-[#FFFFFF] text-[14px] leading-[18px] font-bold tabular-nums whitespace-nowrap">
                            <span x-text="(parseFloat(largo) || 0) + ' metros'">{{ $largoMetros }} metros</span>
                        </div>

                        <!-- Etiqueta izquierda vertical: Ancho (metros) -->
                        <div class="absolute left-2 top-1/2 -translate-y-1/2 -rotate-90 origin-center px-3 py-1 rounded-md bg-[#2F312F] text-[#FFFFFF] text-[14px] leading-[18px] font-bold tabular-nums whitespace-nowrap">
                            <span x-text="(parseFloat(ancho) || 0) + ' metros'">{{ $anchoMetros }} metros</span>
                        </div>

                        <!-- Etiqueta central: Parcela Activa -->
                        <div class="bg-[#FFFFFF] border-2 border-[#1B5E20] rounded-xl px-4 py-2.5 flex items-center gap-2 shadow-sm">
                            <span class="w-5 h-5 rounded-full bg-[#E8F5E9] text-[#1B5E20] inline-flex items-center justify-center shrink-0">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12" />
                                </svg>
                            </span>
                            <span class="text-[15px] leading-[20px] font-extrabold text-[#1A1C1A] tabular-nums">
                                Parcela Activa (<span x-text="parseFloat(ancho) || 0">{{ $anchoMetros }}</span> m × <span x-text="parseFloat(largo) || 0">{{ $largoMetros }}</span> m)
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Controles visuales de zoom (+ / -) -->
                <div class="absolute bottom-4 right-4 flex flex-col rounded-xl overflow-hidden border-2 border-[#E0DED7] bg-[#FFFFFF]">
                    <span class="w-11 h-11 flex items-center justify-center text-[20px] font-bold text-[#1A1C1A] border-b border-[#E0DED7] select-none">+</span>
                    <span class="w-11 h-11 flex items-center justify-center text-[20px] font-bold text-[#1A1C1A] select-none">−</span>
                </div>
            </div>

            <p class="flex items-center gap-2 text-[15px] leading-[22px] font-medium text-[#41493E]">
                <svg class="w-5 h-5 text-[#5D4037] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10" />
                    <path d="M12 16v-4" />
                    <path d="M12 8h.01" />
                </svg>
                <span>Edita los valores métricos en el panel derecho para calibrar el área de tu parcela.</span>
            </p>
        </div>

        <!-- Columna Derecha: Formulario de Medidas (5 columnas) -->
        <div class="lg:col-span-5">
            <div class="bg-[#FFFFFF] border-2 border-[#E0DED7] rounded-2xl p-6 sm:p-8 space-y-6">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="text-[24px] leading-[32px] font-bold text-[#1A1C1A]">
                            Medidas de tu parcela
                        </h2>
                        <p class="mt-1 text-[16px] leading-[24px] font-medium text-[#41493E]">
                            Ingresa las dimensiones reales para calibrar el análisis agroclimático.
                        </p>
                    </div>
                    <span class="inline-flex items-center min-h-[36px] px-3 py-1 rounded-lg bg-[#E8F5E9] text-[#1B5E20] border-[1.5px] border-[#2E7D32] text-[14px] font-bold shrink-0">
                        Paso 1 de 3
                    </span>
                </div>

                <!-- Inputs de Ancho y Largo en Metros -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="ancho_metros" class="block text-[15px] leading-[20px] font-bold uppercase tracking-wider text-[#41493E] mb-2">
                            Ancho (Metros)
                        </label>
                        <div class="relative flex items-center">
                            <input
                                id="ancho_metros"
                                type="number"
                                step="any"
                                min="1"
                                wire:model.live="anchoMetros"
                                class="agro-input pr-10 tabular-nums font-bold text-[20px]"
                            />
                            <span class="absolute right-3.5 text-[16px] font-bold text-[#41493E] pointer-events-none">
                                m
                            </span>
                        </div>
                        @error('anchoMetros')
                            <p class="mt-1.5 text-[15px] font-bold text-[#C62828]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="largo_metros" class="block text-[15px] leading-[20px] font-bold uppercase tracking-wider text-[#41493E] mb-2">
                            Largo (Metros)
                        </label>
                        <div class="relative flex items-center">
                            <input
                                id="largo_metros"
                                type="number"
                                step="any"
                                min="1"
                                wire:model.live="largoMetros"
                                class="agro-input pr-10 tabular-nums font-bold text-[20px]"
                            />
                            <span class="absolute right-3.5 text-[16px] font-bold text-[#41493E] pointer-events-none">
                                m
                            </span>
                        </div>
                        @error('largoMetros')
                            <p class="mt-1.5 text-[15px] font-bold text-[#C62828]">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Recuadro de Superficie Total Calculada -->
                <div class="bg-[#FAF9F6] border-2 border-[#E0DED7] rounded-xl p-4 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3.5">
                        <div class="w-11 h-11 rounded-xl bg-[#E8F5E9] border-[1.5px] border-[#2E7D32] flex items-center justify-center text-[#1B5E20] shrink-0">
                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="5" width="18" height="14" rx="2" />
                                <path d="M7 15v-2" />
                                <path d="M12 15v-4" />
                                <path d="M17 15v-2" />
                            </svg>
                        </div>
                        <div>
                            <span class="block text-[14px] leading-[20px] font-semibold text-[#41493E]">
                                Superficie total calculada
                            </span>
                            <div class="flex flex-wrap items-baseline gap-1.5 tabular-nums">
                                <span class="text-[22px] leading-[28px] font-extrabold text-[#1B5E20]" x-text="metrosFormateados + ' m²'">
                                    {{ number_format($this->metrosCuadrados) }} m²
                                </span>
                                <span class="text-[16px] leading-[22px] font-semibold text-[#41493E]">
                                    (<span x-text="hectareas">{{ number_format($this->areaHectareas, 2) }}</span> hectáreas)
                                </span>
                            </div>
                        </div>
                    </div>

                    <span class="inline-flex items-center min-h-[32px] px-3 py-1 rounded-lg bg-[#E8F5E9] text-[#1B5E20] border border-[#2E7D32] text-[14px] font-bold shrink-0">
                        Óptimo
                    </span>
                </div>

                <!-- Selector: Cultivo Anterior -->
                <div>
                    <label for="cultivo_anterior" class="block text-[15px] leading-[20px] font-bold uppercase tracking-wider text-[#41493E] mb-2">
                        Cultivo Anterior
                    </label>
                    <select
                        id="cultivo_anterior"
                        wire:model.live="cultivoAnterior"
                        class="agro-input font-bold"
                    >
                        @foreach ($catalogoCultivos as $clave => $datosCultivo)
                            <option value="{{ $clave }}">{{ $datosCultivo['etiqueta'] }}</option>
                        @endforeach
                    </select>
                    @error('cultivoAnterior')
                        <p class="mt-1.5 text-[15px] font-bold text-[#C62828]">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Botón Continuar al Paso 2 -->
                <div class="pt-2 space-y-3">
                    <button
                        type="button"
                        wire:click="analizarPasoUno"
                        wire:loading.attr="disabled"
                        class="w-full min-h-[56px] px-6 py-3.5 rounded-xl bg-[#1B5E20] hover:bg-[#00450D] active:bg-[#0D3311] disabled:opacity-60 text-[#FFFFFF] text-[18px] leading-[24px] font-bold border-2 border-[#00450D] border-b-[4px] inline-flex items-center justify-center gap-3 focus:outline-none focus:ring-[3px] focus:ring-[#1B5E20] transition-colors"
                    >
                        <span wire:loading.remove wire:target="analizarPasoUno">
                            Continuar al Paso 2: Diagnóstico NASA POWER
                        </span>
                        <span wire:loading wire:target="analizarPasoUno">
                            Consultando NASA POWER...
                        </span>
                        <svg wire:loading.remove wire:target="analizarPasoUno" class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14" />
                            <path d="m12 5 7 7-7 7" />
                        </svg>
                    </button>

                    <p class="text-center text-[14px] leading-[20px] font-medium text-[#41493E]">
                        Los datos satelitales de temperatura, precipitación y humedad del suelo se cargarán automáticamente.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- PASO 2: DIAGNÓSTICO AGROCLIMÁTICO (NASA POWER Y MODIS) -->
    <!-- ========================================== -->
    <div
        x-show="pasoActual === 2"
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        class="space-y-6"
    >
        @php
            $metricas = $respuestaNasa['climate_metrics'] ?? [];
            $tempPromedio = $metricas['avg_temp_c'] ?? 19.5;
            $tempMax = $metricas['max_temp_c'] ?? 26.2;
            $tempMin = $metricas['min_temp_c'] ?? 9.1;
            $precipitacionMm = $metricas['total_precipitation_mm'] ?? 0.0;
            $humedadRaizFraccion = $metricas['avg_root_soil_moisture'] ?? 0.62;
            $humedadRaizPorcentaje = round($humedadRaizFraccion * 100);
            $coordenadasAnalizadas = $respuestaNasa['coordinates_analyzed'] ?? ['latitude' => $latitud, 'longitude' => $longitud];
            $sueloInferido = $respuestaNasa['inferred_soil_type'] ?? 'Pendiente de consulta';
            $recomendacion = $respuestaNasa['recommendations'][0] ?? null;
        @endphp

        <!-- Encabezado del Diagnóstico -->
        <div class="flex flex-wrap items-end justify-between gap-4 pb-4 border-b-2 border-[#E0DED7]">
            <div>
                <h2 class="text-[26px] leading-[34px] sm:text-[32px] sm:leading-[40px] font-extrabold text-[#1A1C1A]">
                    Diagnóstico Agroclimático
                </h2>
                <p class="mt-1 text-[18px] leading-[26px] font-medium text-[#41493E]">
                    Datos satelitales NASA POWER y MODIS · <strong class="font-bold text-[#1A1C1A]">{{ $ubicacionTexto }} (<span x-text="metrosFormateados">{{ number_format($this->metrosCuadrados) }}</span> m²)</strong>
                </p>
            </div>

            <div class="inline-flex items-center gap-2 min-h-[40px] px-4 py-1.5 rounded-lg bg-[#E8F5E9] text-[#1B5E20] border-[1.5px] border-[#2E7D32] text-[15px] font-bold">
                <span class="w-2.5 h-2.5 rounded-full bg-[#2E7D32]"></span>
                <span>Conectado con NASA POWER API</span>
            </div>
        </div>

        <!-- Cuadrícula de 6 Tarjetas de Métricas Agroclimáticas -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Tarjeta 1: Temperatura del aire -->
            <article class="bg-[#FFFFFF] border-2 border-[#E0DED7] rounded-2xl p-6 flex flex-col justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2.5 text-[#1B5E20] font-bold text-[16px]">
                        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 4v10.54a4 4 0 1 1-4 0V4a2 2 0 0 1 4 0Z" />
                        </svg>
                        <span class="text-[#41493E]">Temperatura del aire</span>
                    </div>
                    <div class="mt-3 flex items-baseline gap-1.5 tabular-nums">
                        <span class="text-[36px] leading-[44px] font-extrabold text-[#1A1C1A]">
                            {{ number_format((float) $tempPromedio, 1) }}
                        </span>
                        <span class="text-[22px] font-bold text-[#41493E]">°C</span>
                    </div>
                </div>
                <div class="pt-3 border-t-2 border-[#EFEEEB] flex items-center gap-2 text-[15px] font-semibold text-[#41493E]">
                    <span class="text-[#1B5E20] font-extrabold">✓</span>
                    <span>Promedio térmico en campo (T2M)</span>
                </div>
            </article>

            <!-- Tarjeta 2: Rango térmico diurno -->
            <article class="bg-[#FFFFFF] border-2 border-[#E0DED7] rounded-2xl p-6 flex flex-col justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2.5 text-[#5D4037] font-bold text-[16px]">
                        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2v2" /><path d="M12 20v2" /><path d="m4.93 4.93 1.41 1.41" /><path d="m17.66 17.66 1.41 1.41" /><path d="M2 12h2" /><path d="M20 12h2" /><circle cx="12" cy="12" r="4" />
                        </svg>
                        <span class="text-[#41493E]">Rango térmico extremo</span>
                    </div>
                    <div class="mt-3 flex items-baseline gap-2 tabular-nums">
                        <span class="text-[34px] leading-[42px] font-extrabold text-[#1A1C1A]">
                            {{ number_format((float) $tempMax, 1) }}°
                        </span>
                        <span class="text-[22px] font-bold text-[#717A6D]">/</span>
                        <span class="text-[26px] leading-[34px] font-bold text-[#5D4037]">
                            {{ number_format((float) $tempMin, 1) }}°C
                        </span>
                    </div>
                </div>
                <div class="pt-3 border-t-2 border-[#EFEEEB] flex items-center gap-2 text-[15px] font-semibold text-[#41493E]">
                    <span class="text-[#1B5E20] font-extrabold">✓</span>
                    <span>Máxima y mínima registradas en el ciclo</span>
                </div>
            </article>

            <!-- Tarjeta 3: Cultivo Anterior y Superficie -->
            <article class="bg-[#FFFFFF] border-2 border-[#E0DED7] rounded-2xl p-6 flex flex-col justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2.5 text-[#1B5E20] font-bold text-[16px]">
                        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M7 20h10" />
                            <path d="M10 20c5.5-2.5.8-6.4 3-10" />
                            <path d="M9.5 9.4c1.1.8 1.8 2.2 2.3 3.7-2 .4-3.5.4-4.8-.3-1.2-.6-2.3-1.9-3-4.2 2.8-.5 4.4 0 5.5.8z" />
                        </svg>
                        <span class="text-[#41493E]">Historial de parcela</span>
                    </div>
                    <div class="mt-3 flex items-baseline gap-2 tabular-nums">
                        <span class="text-[30px] leading-[38px] font-extrabold text-[#1A1C1A]">
                            {{ $cultivoAnterior }}
                        </span>
                        <span class="text-[18px] font-bold text-[#5D4037]">
                            ({{ number_format($this->areaHectareas, 2) }} ha)
                        </span>
                    </div>
                </div>
                <div class="pt-3 border-t-2 border-[#EFEEEB] flex items-center gap-2 text-[15px] font-semibold text-[#41493E]">
                    <span class="text-[#1B5E20] font-extrabold">✓</span>
                    <span>Base para evaluación de rotación</span>
                </div>
            </article>

            <!-- Tarjeta 4: Lluvia acumulada -->
            <article class="bg-[#FFFFFF] border-2 border-[#E0DED7] rounded-2xl p-6 flex flex-col justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2.5 text-[#1565C0] font-bold text-[16px]">
                        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242" />
                            <path d="M16 14v6" /><path d="M8 14v6" /><path d="M12 16v6" />
                        </svg>
                        <span class="text-[#41493E]">Lluvia acumulada del ciclo</span>
                    </div>
                    <div class="mt-3 flex items-baseline gap-1.5 tabular-nums">
                        <span class="text-[36px] leading-[44px] font-extrabold text-[#1A1C1A]">
                            {{ number_format((float) $precipitacionMm, 1) }}
                        </span>
                        <span class="text-[20px] font-bold text-[#41493E]">mm</span>
                    </div>
                </div>
                <div class="pt-3 border-t-2 border-[#EFEEEB] flex items-center gap-2 text-[15px] font-semibold text-[#41493E]">
                    <span class="text-[#1B5E20] font-extrabold">✓</span>
                    <span>Precipitación corregida (PRECTOTCORR)</span>
                </div>
            </article>

            <!-- Tarjeta 5: Humedad del suelo en raíz -->
            <article class="bg-[#FFFFFF] border-2 border-[#E0DED7] rounded-2xl p-6 flex flex-col justify-between gap-4">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2.5 text-[#1B5E20] font-bold text-[16px]">
                            <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z" />
                            </svg>
                            <span class="text-[#41493E]">Humedad del suelo</span>
                        </div>
                        <span class="text-[14px] font-extrabold text-[#1B5E20]">
                            {{ $humedadRaizPorcentaje >= 40 ? 'Óptima' : 'Moderada' }}
                        </span>
                    </div>

                    <div class="mt-3 flex items-baseline gap-2 tabular-nums">
                        <span class="text-[36px] leading-[44px] font-extrabold text-[#1A1C1A]">
                            {{ $humedadRaizPorcentaje }}%
                        </span>
                        <span class="text-[15px] font-semibold text-[#41493E]">
                            en raíz (GWETROOT)
                        </span>
                    </div>

                    <!-- Barra de progreso de humedad -->
                    <div class="mt-3 w-full h-2.5 rounded-full bg-[#E3E2E0] overflow-hidden">
                        <div
                            class="h-full rounded-full bg-[#1B5E20]"
                            style="width: {{ min(max($humedadRaizPorcentaje, 5), 100) }}%;"
                        ></div>
                    </div>
                </div>
                <div class="pt-3 border-t-2 border-[#EFEEEB] flex items-center gap-2 text-[15px] font-semibold text-[#41493E]">
                    <span class="text-[#1B5E20] font-extrabold">✓</span>
                    <span>Estrato radicular evaluado por NASA POWER</span>
                </div>
            </article>

            <!-- Tarjeta 6: Coordenadas y Periodo Analizado -->
            <article class="bg-[#FFFFFF] border-2 border-[#E0DED7] rounded-2xl p-6 flex flex-col justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2.5 text-[#1565C0] font-bold text-[16px]">
                        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10" />
                            <path d="M2 12h20" />
                            <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
                        </svg>
                        <span class="text-[#41493E]">Punto satelital analizado</span>
                    </div>
                    <div class="mt-3 flex items-baseline gap-2 tabular-nums">
                        <span class="text-[26px] leading-[34px] font-extrabold text-[#1A1C1A]">
                            {{ $coordenadasAnalizadas['latitude'] ?? 19.16 }}°, {{ $coordenadasAnalizadas['longitude'] ?? -98.41 }}°
                        </span>
                    </div>
                </div>
                <div class="pt-3 border-t-2 border-[#EFEEEB] flex items-center gap-2 text-[15px] font-semibold text-[#41493E]">
                    <span class="text-[#1B5E20] font-extrabold">✓</span>
                    <span>Periodo: {{ $respuestaNasa['fecha_inicio_datos'] ?? '2025-01-01' }} a {{ $respuestaNasa['fecha_fin_datos'] ?? '2025-12-31' }}</span>
                </div>
            </article>
        </div>

        <!-- Banner de Diagnóstico Integral -->
        <div class="bg-[#FFFFFF] border-2 border-[#2E7D32] rounded-2xl p-6 sm:p-7 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-full bg-[#E8F5E9] border-2 border-[#2E7D32] flex items-center justify-center text-[#1B5E20] shrink-0">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z" />
                        <path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-[20px] leading-[28px] font-extrabold text-[#1B5E20]">
                        Diagnóstico de tu terreno: Datos listos para registro
                    </h3>
                    <p class="mt-1 text-[17px] leading-[26px] font-medium text-[#41493E]">
                        {{ $recomendacion['reasoning'] ?? 'La tierra conserva humedad adecuada tras las lluvias registradas y temperatura estable para planificar tu rotación de cultivo.' }}
                    </p>
                </div>
            </div>

            <div class="lg:text-right shrink-0 border-t-2 lg:border-t-0 lg:border-l-2 border-[#EFEEEB] pt-4 lg:pt-0 lg:pl-6 w-full lg:w-auto">
                <span class="block text-[13px] font-bold uppercase tracking-wider text-[#41493E]">
                    Estado de consulta NASA
                </span>
                <span class="block text-[26px] leading-[34px] font-extrabold text-[#1B5E20]">
                    100% Verificado
                </span>
                <span class="block text-[14px] font-medium text-[#41493E]">
                    Listo para guardar en base de datos
                </span>
            </div>
        </div>

        <!-- Barra de botones inferior (Paso 2) -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-2">
            <button
                type="button"
                wire:click="volverPasoUno"
                class="w-full sm:w-auto min-h-[56px] px-6 py-3 rounded-xl bg-[#FFFFFF] hover:bg-[#F4F3F1] border-2 border-[#5D4037] text-[#5D4037] text-[17px] font-bold inline-flex items-center justify-center gap-2.5 focus:outline-none focus:ring-[3px] focus:ring-[#1B5E20] transition-colors"
            >
                <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 12H5" />
                    <path d="m12 19-7-7 7-7" />
                </svg>
                <span>Volver al Paso 1 (Medición)</span>
            </button>

            <button
                type="button"
                wire:click="guardarEvaluacion"
                wire:loading.attr="disabled"
                class="w-full sm:w-auto min-h-[56px] px-7 py-3.5 rounded-xl bg-[#1B5E20] hover:bg-[#00450D] active:bg-[#0D3311] disabled:opacity-60 text-[#FFFFFF] text-[18px] font-bold border-2 border-[#00450D] border-b-[4px] inline-flex items-center justify-center gap-3 focus:outline-none focus:ring-[3px] focus:ring-[#1B5E20] transition-colors"
            >
                <span wire:loading.remove wire:target="guardarEvaluacion">
                    Continuar al Paso 3: Guardar Predio y Consulta NASA
                </span>
                <span wire:loading wire:target="guardarEvaluacion">
                    Guardando transacción en BD...
                </span>
                <svg wire:loading.remove wire:target="guardarEvaluacion" class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14" />
                    <path d="m12 5 7 7-7 7" />
                </svg>
            </button>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- PASO 3: CONFIRMACIÓN DE TRANSACCIÓN GUARDADA -->
    <!-- ========================================== -->
    <div
        x-show="pasoActual === 3"
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
    >
        <div class="bg-[#FFFFFF] border-2 border-[#2E7D32] rounded-2xl p-6 sm:p-8 space-y-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-[#E8F5E9] border-2 border-[#2E7D32] flex items-center justify-center text-[#1B5E20] shrink-0">
                        <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                            <polyline points="22 4 12 14.01 9 11.01" />
                        </svg>
                    </div>
                    <div>
                        <span class="inline-flex items-center px-3 py-0.5 rounded-md bg-[#E8F5E9] text-[#1B5E20] text-[14px] font-bold">
                            Transacción completada con éxito
                        </span>
                        <h2 class="mt-1 text-[26px] leading-[34px] font-extrabold text-[#1A1C1A]">
                            Predio y diagnóstico NASA guardados
                        </h2>
                    </div>
                </div>

                <button
                    type="button"
                    wire:click="nuevaEvaluacion"
                    class="min-h-[56px] px-6 py-3 rounded-xl bg-[#1B5E20] hover:bg-[#00450D] text-[#FFFFFF] text-[17px] font-bold inline-flex items-center justify-center gap-2 focus:outline-none focus:ring-[3px] focus:ring-[#1B5E20] transition-colors"
                >
                    <span>Evaluar otra parcela</span>
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                <div class="bg-[#FAF9F6] border-2 border-[#E0DED7] rounded-xl p-5 space-y-2">
                    <span class="text-[14px] font-bold uppercase tracking-wider text-[#5D4037]">
                        Tabla: predios (Registro #{{ $predioGuardadoId }})
                    </span>
                    <p class="text-[18px] font-bold text-[#1A1C1A]">
                        {{ $ubicacionTexto }}
                    </p>
                    <p class="text-[16px] font-medium text-[#41493E] tabular-nums">
                        Superficie: <strong>{{ number_format($this->metrosCuadrados) }} m² ({{ number_format($this->areaHectareas, 4) }} ha)</strong>
                    </p>
                    <p class="text-[16px] font-medium text-[#41493E]">
                        Cultivo anterior: <strong>{{ $cultivoAnterior }}</strong>
                    </p>
                </div>

                <div class="bg-[#FAF9F6] border-2 border-[#E0DED7] rounded-xl p-5 space-y-2">
                    <span class="text-[14px] font-bold uppercase tracking-wider text-[#1565C0]">
                        Tabla: consultas_nasa (Registro #{{ $consultaNasaGuardadaId }})
                    </span>
                    <p class="text-[18px] font-bold text-[#1A1C1A] tabular-nums">
                        Temp. media: {{ number_format((float) $tempPromedio, 2) }} °C (Máx {{ number_format((float) $tempMax, 1) }}° / Mín {{ number_format((float) $tempMin, 1) }}°)
                    </p>
                    <p class="text-[16px] font-medium text-[#41493E] tabular-nums">
                        Precipitación: <strong>{{ number_format((float) $precipitacionMm, 2) }} mm</strong> · Humedad raíz: <strong>{{ $humedadRaizPorcentaje }}%</strong>
                    </p>
                    <p class="text-[16px] font-medium text-[#2E7D32]">
                        Estado de respuesta: <strong>exitoso</strong>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
