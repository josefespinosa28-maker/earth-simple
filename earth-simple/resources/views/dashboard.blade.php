<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 space-y-12 sm:space-y-16">

        <!-- Sección 1: Hero Principal (sin reproductor de video ni botón de login) -->
        <section aria-labelledby="hero-heading" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            <!-- Columna de Texto y Acción Principal -->
            <div class="lg:col-span-6 space-y-6">
                <h1 id="hero-heading" class="text-[32px] leading-[40px] sm:text-[44px] sm:leading-[52px] font-extrabold text-[#1A1C1A] tracking-[-0.02em]">
                    Cuida tu tierra y ahorra con la ayuda de la NASA
                </h1>

                <p class="text-[18px] leading-[28px] sm:text-[20px] sm:leading-[30px] font-medium text-[#41493E]">
                    Monitoreo satelital directo a tu parcela en lenguaje claro y accesible. Detecta humedad profunda y decide el mejor momento de siembra sin herramientas costosas.
                </p>

                <!-- Botón Primario: Evaluar zona (altura mín. 56px, #1B5E20) -->
                <div class="pt-1">
                    <a
                        href="#datos-analizados"
                        class="inline-flex items-center justify-center gap-3 w-full sm:w-auto min-h-[56px] px-7 py-3.5 rounded-lg bg-[#1B5E20] hover:bg-[#00450D] active:bg-[#0D3311] text-[#FFFFFF] text-[18px] leading-[24px] font-bold tracking-[0.01em] border-2 border-[#00450D] border-b-[4px] focus:outline-none focus:ring-[3px] focus:ring-[#1B5E20] focus:ring-offset-2 transition-colors"
                    >
                        <svg class="w-6 h-6 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                            <circle cx="12" cy="10" r="3" />
                        </svg>
                        <span>Evaluar zona</span>
                        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M5 12h14" />
                            <path d="m12 5 7 7-7 7" />
                        </svg>
                    </a>
                </div>

                <!-- Beneficios clave -->
                <ul class="space-y-2.5 pt-1">
                    <li class="flex items-center gap-3 text-[18px] leading-[26px] font-bold text-[#1B5E20]">
                        <span class="w-7 h-7 rounded-full bg-[#E8F5E9] border-[1.5px] border-[#2E7D32] inline-flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-[#1B5E20]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <polyline points="20 6 9 17 4 12" />
                            </svg>
                        </span>
                        <span>100% gratuita</span>
                    </li>
                    <li class="flex items-center gap-3 text-[18px] leading-[26px] font-bold text-[#1B5E20]">
                        <span class="w-7 h-7 rounded-full bg-[#E8F5E9] border-[1.5px] border-[#2E7D32] inline-flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-[#1B5E20]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <polyline points="20 6 9 17 4 12" />
                            </svg>
                        </span>
                        <span>Sin registros complicados</span>
                    </li>
                </ul>

                <!-- Banner de Telemetría Satelital NASA (#E3F2FD + barra izquierda 6px #1565C0) -->
                <div class="bg-[#E3F2FD] border-2 border-[#1565C0] border-l-[6px] rounded-xl p-4 sm:p-5 flex items-start sm:items-center gap-3.5">
                    <svg class="w-6 h-6 text-[#1565C0] shrink-0 mt-0.5 sm:mt-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M13 7 9 3 5 7l4 4" />
                        <path d="m17 11 4 4-4 4-4-4" />
                        <path d="m8 12 4 4 6-6-4-4Z" />
                        <path d="m16 8 3-3" />
                        <path d="M9 21a6 6 0 0 0-6-6" />
                    </svg>
                    <p class="text-[16px] leading-[24px] sm:text-[18px] sm:leading-[26px] font-bold text-[#0D47A1]">
                        Datos NASA POWER y MODIS activos para parcelas de México
                    </p>
                </div>
            </div>

            <!-- Columna de Paisaje Agrícola (reemplaza el reproductor de video) -->
            <div class="lg:col-span-6">
                <figure class="bg-[#FFFFFF] border-2 border-[#E0DED7] rounded-2xl overflow-hidden">
                    <div class="relative h-64 sm:h-80 lg:h-[400px] bg-[#E3E2E0]">
                        <img
                            src="{{ asset('images/dashboard-landscape.jpg') }}"
                            alt="Parcela agrícola en el valle con volcán nevado al fondo"
                            class="w-full h-full object-cover object-center"
                        />
                    </div>
                    <figcaption class="p-5 sm:p-6 bg-[#FFFFFF] border-t-2 border-[#E0DED7] flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <p class="text-[18px] leading-[24px] font-bold text-[#1A1C1A]">
                                Caso Real Don José
                            </p>
                            <p class="text-[16px] leading-[22px] font-semibold text-[#41493E]">
                                Valle de Cholula, Puebla
                            </p>
                        </div>
                        <span class="inline-flex items-center min-h-[36px] px-3 py-1 rounded-lg bg-[#E3F2FD] text-[#0D47A1] border-[1.5px] border-[#1565C0] text-[16px] leading-[22px] font-semibold">
                            Monitoreo en campo
                        </span>
                    </figcaption>
                </figure>
            </div>
        </section>

        <!-- Sección 2: ¿Qué datos analizamos para ti? -->
        <section id="datos-analizados" aria-labelledby="datos-heading" class="space-y-8 scroll-mt-24">
            <div class="text-center">
                <h2 id="datos-heading" class="text-[26px] leading-[34px] sm:text-[32px] sm:leading-[40px] font-bold text-[#1A1C1A]">
                    ¿Qué datos analizamos para ti?
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Tarjeta 1: Humedad del suelo -->
                <article class="bg-[#FFFFFF] border-2 border-[#E0DED7] rounded-2xl p-6 flex flex-col justify-between gap-5">
                    <div class="space-y-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3">
                                <div class="w-12 h-12 rounded-xl bg-[#E3F2FD] border-2 border-[#1565C0] flex items-center justify-center text-[#0D47A1] shrink-0">
                                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-[20px] leading-[26px] font-bold text-[#1A1C1A]">
                                        Humedad del suelo
                                    </h3>
                                    <p class="text-[16px] leading-[22px] font-semibold text-[#41493E]">
                                        NASA POWER • Superficie
                                    </p>
                                </div>
                            </div>
                            <span class="inline-flex items-center min-h-[36px] px-3 py-1 rounded-lg bg-[#E3F2FD] text-[#0D47A1] border-[1.5px] border-[#1565C0] text-[15px] font-bold shrink-0">
                                POWER
                            </span>
                        </div>

                        <p class="text-[18px] leading-[28px] font-medium text-[#41493E]">
                            Consulta el agua en tus primeros 5 cm de tierra gratis.
                        </p>
                    </div>

                    <div class="bg-[#F4F3F1] border-2 border-[#E0DED7] rounded-xl p-4">
                        <span class="block text-[16px] leading-[22px] font-semibold text-[#41493E]">
                            Lectura en raíz superficial:
                        </span>
                        <div class="mt-1 flex flex-wrap items-baseline gap-2 tabular-nums">
                            <span class="text-[26px] leading-[34px] font-extrabold text-[#1A1C1A]">34%</span>
                            <span class="text-[16px] leading-[22px] font-bold text-[#2E7D32]">(Nivel óptimo)</span>
                        </div>
                    </div>
                </article>

                <!-- Tarjeta 2: Rotaciones sugeridas -->
                <article class="bg-[#FFFFFF] border-2 border-[#E0DED7] rounded-2xl p-6 flex flex-col justify-between gap-5">
                    <div class="space-y-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3">
                                <div class="w-12 h-12 rounded-xl bg-[#E8F5E9] border-2 border-[#2E7D32] flex items-center justify-center text-[#1B5E20] shrink-0">
                                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z" />
                                        <path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-[20px] leading-[26px] font-bold text-[#1A1C1A]">
                                        Rotaciones sugeridas
                                    </h3>
                                    <p class="text-[16px] leading-[22px] font-semibold text-[#41493E]">
                                        MODIS • Agronomía Regenerativa
                                    </p>
                                </div>
                            </div>
                            <span class="inline-flex items-center min-h-[36px] px-3 py-1 rounded-lg bg-[#E8F5E9] text-[#1B5E20] border-[1.5px] border-[#2E7D32] text-[15px] font-bold shrink-0">
                                MODIS
                            </span>
                        </div>

                        <p class="text-[18px] leading-[28px] font-medium text-[#41493E]">
                            Compara alternativas de cultivo según tu historial agrícola y descanso de la tierra.
                        </p>
                    </div>

                    <div class="bg-[#F4F3F1] border-2 border-[#E0DED7] rounded-xl p-4">
                        <span class="block text-[16px] leading-[22px] font-semibold text-[#41493E]">
                            Enfoque de evaluación:
                        </span>
                        <div class="mt-1 flex flex-wrap items-baseline gap-2">
                            <span class="text-[22px] leading-[30px] font-extrabold text-[#1A1C1A]">Ciclo balanceado</span>
                            <span class="text-[16px] leading-[22px] font-bold text-[#1B5E20]">(Cuidado de suelo)</span>
                        </div>
                    </div>
                </article>

                <!-- Tarjeta 3: Estado de la tierra -->
                <article class="bg-[#FFFFFF] border-2 border-[#E0DED7] rounded-2xl p-6 flex flex-col justify-between gap-5">
                    <div class="space-y-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3">
                                <div class="w-12 h-12 rounded-xl bg-[#FFF3E0] border-2 border-[#E65100] flex items-center justify-center text-[#E65100] shrink-0">
                                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="m8 3 4 8 5-5 5 15H2L8 3z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-[20px] leading-[26px] font-bold text-[#1A1C1A]">
                                        Estado de la tierra
                                    </h3>
                                    <p class="text-[16px] leading-[22px] font-semibold text-[#41493E]">
                                        NASA POWER • Insolación
                                    </p>
                                </div>
                            </div>
                            <span class="inline-flex items-center min-h-[36px] px-3 py-1 rounded-lg bg-[#FFF3E0] text-[#E65100] border-[1.5px] border-[#E65100] text-[15px] font-bold shrink-0">
                                POWER
                            </span>
                        </div>

                        <p class="text-[18px] leading-[28px] font-medium text-[#41493E]">
                            Pendientes, riesgo de erosión y clima histórico.
                        </p>
                    </div>

                    <div class="bg-[#F4F3F1] border-2 border-[#E0DED7] rounded-xl p-4">
                        <span class="block text-[16px] leading-[22px] font-semibold text-[#41493E]">
                            Radiación e insolación:
                        </span>
                        <div class="mt-1 flex flex-wrap items-baseline gap-2 tabular-nums">
                            <span class="text-[26px] leading-[34px] font-extrabold text-[#1A1C1A]">5.8 kWh/m²</span>
                            <span class="text-[16px] leading-[22px] font-bold text-[#2E7D32]">(Desarrollo alto)</span>
                        </div>
                    </div>
                </article>
            </div>
        </section>

        <!-- Sección 3: Casos de estudio en campo -->
        <section aria-labelledby="casos-heading" class="space-y-6">
            <div>
                <p class="text-[16px] leading-[22px] font-bold uppercase tracking-wider text-[#5D4037]">
                    Casos de estudio en campo
                </p>
                <h2 id="casos-heading" class="mt-1 text-[26px] leading-[34px] sm:text-[32px] sm:leading-[40px] font-bold text-[#1A1C1A]">
                    Monitoreo satelital en parcelas de Puebla
                </h2>
            </div>

            <div class="bg-[#FFFFFF] border-2 border-[#E0DED7] rounded-2xl p-6 sm:p-8 grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
                <div class="lg:col-span-7 space-y-3">
                    <div class="inline-flex items-center min-h-[36px] px-3 py-1 rounded-lg bg-[#E8F5E9] text-[#1B5E20] border-[1.5px] border-[#2E7D32] text-[16px] font-bold">
                        Valle de Cholula, Puebla
                    </div>
                    <h3 class="text-[22px] leading-[30px] font-bold text-[#1A1C1A]">
                        Seguimiento de humedad y radiación para ciclos de temporal
                    </h3>
                    <p class="text-[18px] leading-[28px] font-medium text-[#41493E]">
                        Combinando las observaciones satelitales de NASA POWER y MODIS con el historial de cultivo de la parcela, los productores pueden identificar ventanas seguras de siembra y rotaciones que cuidan la fertilidad del terreno.
                    </p>
                </div>

                <div class="lg:col-span-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-4">
                    <div class="bg-[#F4F3F1] border-2 border-[#E0DED7] rounded-xl p-4">
                        <span class="block text-[16px] leading-[22px] font-semibold text-[#5D4037]">
                            Humedad superficial (NASA POWER)
                        </span>
                        <span class="mt-1 block text-[26px] leading-[34px] font-extrabold text-[#1A1C1A] tabular-nums">
                            34% — Nivel óptimo
                        </span>
                    </div>
                    <div class="bg-[#F4F3F1] border-2 border-[#E0DED7] rounded-xl p-4">
                        <span class="block text-[16px] leading-[22px] font-semibold text-[#5D4037]">
                            Insolación promedio POWER
                        </span>
                        <span class="mt-1 block text-[26px] leading-[34px] font-extrabold text-[#1A1C1A] tabular-nums">
                            5.8 kWh/m²
                        </span>
                    </div>
                </div>
            </div>
        </section>

    </div>
</x-app-layout>
