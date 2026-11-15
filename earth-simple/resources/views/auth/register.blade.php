<x-guest-layout>
    <main class="flex-1 flex flex-col items-center justify-center px-4 py-8 sm:px-6">
        <div class="w-full max-w-lg">

            <!-- Marca minimalista -->
            <div class="flex items-center justify-center gap-3 mb-6">
                <div class="w-11 h-11 rounded-lg bg-[#1B5E20] border-2 border-[#00450D] flex items-center justify-center text-[#FFFFFF] shrink-0">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M7 20h10" />
                        <path d="M10 20c5.5-2.5.8-6.4 3-10" />
                        <path d="M9.5 9.4c1.1.8 1.8 2.2 2.3 3.7-2 .4-3.5.4-4.8-.3-1.2-.6-2.3-1.9-3-4.2 2.8-.5 4.4 0 5.5.8z" />
                        <path d="M14.1 6a7 7 0 0 0-1.1 4c1.9-.1 3.3-.6 4.3-1.4 1-1 1.6-2.3 1.7-4.6-2.7.1-4 1-4.9 2z" />
                    </svg>
                </div>
                <span class="text-[24px] leading-[30px] font-extrabold tracking-tight text-[#1A1C1A]">
                    Earth-simple
                </span>
            </div>

            <!-- Tarjeta de Registro -->
            <section aria-labelledby="register-heading" class="bg-[#FFFFFF] border-2 border-[#E0DED7] rounded-2xl p-6 sm:p-8">
                <div class="mb-6">
                    <h1 id="register-heading" class="text-[26px] leading-[34px] sm:text-[32px] sm:leading-[40px] font-bold text-[#1A1C1A] tracking-[-0.01em]">
                        Crear cuenta
                    </h1>
                    <p class="mt-1.5 text-[18px] leading-[28px] font-medium text-[#41493E]">
                        Completa tus datos para registrarte.
                    </p>
                </div>

                <!-- Errores de Validación (Estado Riesgo) -->
                @if ($errors->any())
                    <div role="alert" class="mb-6 p-4 rounded-xl bg-[#FFEBEE] border-2 border-[#C62828] text-[#C62828]">
                        <p class="text-[18px] leading-[24px] font-bold">
                            Revisa los datos ingresados
                        </p>
                        <ul class="mt-2 list-disc list-inside text-[18px] leading-[26px] font-medium space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf

                    <!-- Nombre -->
                    <div>
                        <label for="name" class="block text-[18px] leading-[24px] font-bold tracking-[0.01em] text-[#1A1C1A] mb-2">
                            Nombre completo
                        </label>
                        <input
                            id="name"
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            required
                            autofocus
                            autocomplete="name"
                            placeholder="Tu nombre"
                            class="agro-input"
                        />
                    </div>

                    <!-- Correo electrónico -->
                    <div>
                        <label for="email" class="block text-[18px] leading-[24px] font-bold tracking-[0.01em] text-[#1A1C1A] mb-2">
                            Correo electrónico
                        </label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autocomplete="username"
                            placeholder="ejemplo@correo.com"
                            class="agro-input"
                        />
                    </div>

                    <!-- Contraseña -->
                    <div>
                        <label for="password" class="block text-[18px] leading-[24px] font-bold tracking-[0.01em] text-[#1A1C1A] mb-2">
                            Contraseña
                        </label>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="new-password"
                            placeholder="Mínimo 8 caracteres"
                            class="agro-input"
                        />
                    </div>

                    <!-- Confirmar contraseña -->
                    <div>
                        <label for="password_confirmation" class="block text-[18px] leading-[24px] font-bold tracking-[0.01em] text-[#1A1C1A] mb-2">
                            Confirmar contraseña
                        </label>
                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            required
                            autocomplete="new-password"
                            placeholder="Repite tu contraseña"
                            class="agro-input"
                        />
                    </div>

                    <!-- Términos y Política de Privacidad (Jetstream) -->
                    @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                        <div>
                            <label for="terms" class="flex items-start gap-3.5 min-h-[56px] px-3 py-2 -mx-3 rounded-lg cursor-pointer hover:bg-[#F4F3F1] transition-colors">
                                <input
                                    id="terms"
                                    type="checkbox"
                                    name="terms"
                                    required
                                    class="agro-checkbox shrink-0 mt-0.5"
                                />
                                <span class="text-[16px] leading-[24px] font-medium text-[#1A1C1A]">
                                    {!! __('Acepto los :terms_of_service y la :privacy_policy', [
                                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline font-bold text-[#1B5E20] hover:text-[#00450D] focus:outline-none focus:ring-[3px] focus:ring-[#1B5E20] rounded">'.__('Términos de servicio').'</a>',
                                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline font-bold text-[#1B5E20] hover:text-[#00450D] focus:outline-none focus:ring-[3px] focus:ring-[#1B5E20] rounded">'.__('Política de privacidad').'</a>',
                                    ]) !!}
                                </span>
                            </label>
                        </div>
                    @endif

                    <!-- Botones -->
                    <div class="pt-1 space-y-3">
                        <button
                            type="submit"
                            class="w-full min-h-[56px] px-6 py-3 rounded-lg bg-[#1B5E20] hover:bg-[#00450D] active:bg-[#0D3311] text-[#FFFFFF] text-[18px] leading-[24px] font-bold tracking-[0.01em] border-2 border-[#00450D] border-b-[4px] inline-flex items-center justify-center gap-2.5 focus:outline-none focus:ring-[3px] focus:ring-[#1B5E20] focus:ring-offset-2 transition-colors"
                        >
                            <span>Registrarme</span>
                        </button>

                        <a
                            href="{{ route('login') }}"
                            class="w-full min-h-[56px] px-4 py-2.5 rounded-lg bg-[#FFFFFF] hover:bg-[#F4F3F1] active:bg-[#E9E8E5] border-2 border-[#5D4037] text-[#5D4037] text-[16px] leading-[22px] font-bold inline-flex items-center justify-center text-center focus:outline-none focus:ring-[3px] focus:ring-[#1B5E20] transition-colors"
                        >
                            ¿Ya tienes cuenta? Inicia sesión
                        </a>
                    </div>
                </form>
            </section>

        </div>
    </main>
</x-guest-layout>
