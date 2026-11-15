<x-guest-layout>
    <main class="flex-1 flex items-center justify-center px-4 py-8 sm:px-6 lg:px-8">
        <div class="w-full max-w-5xl grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">

            <!-- Columna de Imagen y Marca Minimalista -->
            <div class="lg:col-span-5 flex flex-col bg-[#FFFFFF] border-2 border-[#E0DED7] rounded-2xl overflow-hidden">
                <div class="relative h-52 sm:h-64 lg:flex-1 lg:min-h-[320px] bg-[#E3E2E0]">
                    <img
                        src="{{ asset('images/login-crop.jpg') }}"
                        alt="Cultivo de maíz bajo cielo despejado"
                        class="w-full h-full object-cover object-center"
                    />
                </div>

                <div class="p-5 sm:p-6 bg-[#FFFFFF] border-t-2 border-[#E0DED7]">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-lg bg-[#1B5E20] border-2 border-[#00450D] flex items-center justify-center text-[#FFFFFF] shrink-0">
                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M7 20h10" />
                                <path d="M10 20c5.5-2.5.8-6.4 3-10" />
                                <path d="M9.5 9.4c1.1.8 1.8 2.2 2.3 3.7-2 .4-3.5.4-4.8-.3-1.2-.6-2.3-1.9-3-4.2 2.8-.5 4.4 0 5.5.8z" />
                                <path d="M14.1 6a7 7 0 0 0-1.1 4c1.9-.1 3.3-.6 4.3-1.4 1-1 1.6-2.3 1.7-4.6-2.7.1-4 1-4.9 2z" />
                            </svg>
                        </div>
                        <span class="text-[22px] leading-[28px] font-extrabold tracking-tight text-[#1A1C1A]">
                            Earth-simple
                        </span>
                    </div>
                    <p class="mt-2.5 text-[18px] leading-[26px] font-medium text-[#41493E]">
                        Rotación agrícola con datos de campo y clima.
                    </p>
                </div>
            </div>

            <!-- Columna de Formulario de Acceso -->
            <section aria-labelledby="login-heading" class="lg:col-span-7 flex">
                <div class="w-full bg-[#FFFFFF] border-2 border-[#E0DED7] rounded-2xl p-6 sm:p-8 flex flex-col justify-center">
                    <div class="mb-6">
                        <h1 id="login-heading" class="text-[26px] leading-[34px] sm:text-[32px] sm:leading-[40px] font-bold text-[#1A1C1A] tracking-[-0.01em]">
                            Iniciar sesión
                        </h1>
                        <p class="mt-1.5 text-[18px] leading-[28px] font-medium text-[#41493E]">
                            Ingresa a tu cuenta para continuar.
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

                    <!-- Estado de Sesión (Estado Favorable) -->
                    @session('status')
                        <div role="status" class="mb-6 p-4 rounded-xl bg-[#E8F5E9] border-2 border-[#2E7D32] text-[#1B5E20]">
                            <p class="text-[18px] leading-[26px] font-bold">
                                {{ $value }}
                            </p>
                        </div>
                    @endsession

                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf

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
                                autofocus
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
                            <div class="relative flex items-center">
                                <input
                                    id="password"
                                    type="password"
                                    name="password"
                                    required
                                    autocomplete="current-password"
                                    placeholder="Ingresa tu contraseña"
                                    class="agro-input pr-28"
                                />
                                <button
                                    type="button"
                                    id="toggle-password-btn"
                                    onclick="toggleFieldPassword()"
                                    aria-label="Mostrar u ocultar contraseña"
                                    aria-pressed="false"
                                    class="absolute right-1.5 h-11 min-w-[88px] px-3 rounded-md bg-[#F4F3F1] hover:bg-[#E9E8E5] active:bg-[#E3E2E0] border-2 border-[#5D4037] text-[#1A1C1A] text-[16px] leading-[22px] font-bold inline-flex items-center justify-center gap-1.5 focus:outline-none focus:ring-[3px] focus:ring-[#1B5E20] transition-colors"
                                >
                                    <svg id="eye-open-icon" class="w-5 h-5 text-[#5D4037] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                    <svg id="eye-closed-icon" class="w-5 h-5 text-[#5D4037] shrink-0 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24" />
                                        <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68" />
                                        <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61" />
                                        <line x1="2" y1="2" x2="22" y2="22" />
                                    </svg>
                                    <span id="toggle-password-text">Ver</span>
                                </button>
                            </div>
                        </div>

                        <!-- Recordar sesión -->
                        <div>
                            <label for="remember_me" class="inline-flex items-center gap-3.5 min-h-[56px] px-3 py-2 -mx-3 rounded-lg cursor-pointer select-none hover:bg-[#F4F3F1] transition-colors">
                                <input
                                    id="remember_me"
                                    type="checkbox"
                                    name="remember"
                                    class="agro-checkbox shrink-0"
                                />
                                <span class="text-[18px] leading-[26px] font-semibold text-[#1A1C1A]">
                                    Mantener sesión iniciada
                                </span>
                            </label>
                        </div>

                        <!-- Botón Primario y Acciones Secundarias -->
                        <div class="pt-1 space-y-3">
                            <button
                                type="submit"
                                class="w-full min-h-[56px] px-6 py-3 rounded-lg bg-[#1B5E20] hover:bg-[#00450D] active:bg-[#0D3311] text-[#FFFFFF] text-[18px] leading-[24px] font-bold tracking-[0.01em] border-2 border-[#00450D] border-b-[4px] inline-flex items-center justify-center gap-2.5 focus:outline-none focus:ring-[3px] focus:ring-[#1B5E20] focus:ring-offset-2 transition-colors"
                            >
                                <span>Entrar</span>
                                <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M5 12h14" />
                                    <path d="m12 5 7 7-7 7" />
                                </svg>
                            </button>

                            @if (Route::has('password.request') || Route::has('register'))
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                                    @if (Route::has('password.request'))
                                        <a
                                            href="{{ route('password.request') }}"
                                            class="w-full min-h-[56px] px-4 py-2.5 rounded-lg bg-[#FFFFFF] hover:bg-[#F4F3F1] active:bg-[#E9E8E5] border-2 border-[#5D4037] text-[#5D4037] text-[16px] leading-[22px] font-bold inline-flex items-center justify-center text-center focus:outline-none focus:ring-[3px] focus:ring-[#1B5E20] transition-colors {{ !Route::has('register') ? 'sm:col-span-2' : '' }}"
                                        >
                                            ¿Olvidaste tu contraseña?
                                        </a>
                                    @endif

                                    @if (Route::has('register'))
                                        <a
                                            href="{{ route('register') }}"
                                            class="w-full min-h-[56px] px-4 py-2.5 rounded-lg bg-[#FFFFFF] hover:bg-[#F4F3F1] active:bg-[#E9E8E5] border-2 border-[#5D4037] text-[#5D4037] text-[16px] leading-[22px] font-bold inline-flex items-center justify-center text-center focus:outline-none focus:ring-[3px] focus:ring-[#1B5E20] transition-colors {{ !Route::has('password.request') ? 'sm:col-span-2' : '' }}"
                                        >
                                            Crear cuenta
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </form>
                </div>
            </section>

        </div>
    </main>

    <script>
        function toggleFieldPassword() {
            const passwordInput = document.getElementById('password');
            const toggleBtn = document.getElementById('toggle-password-btn');
            const toggleText = document.getElementById('toggle-password-text');
            const eyeOpen = document.getElementById('eye-open-icon');
            const eyeClosed = document.getElementById('eye-closed-icon');

            if (!passwordInput) return;

            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            toggleBtn.setAttribute('aria-pressed', isPassword ? 'true' : 'false');
            toggleText.textContent = isPassword ? 'Ocultar' : 'Ver';
            eyeOpen.classList.toggle('hidden', isPassword);
            eyeClosed.classList.toggle('hidden', !isPassword);
        }
    </script>
</x-guest-layout>
