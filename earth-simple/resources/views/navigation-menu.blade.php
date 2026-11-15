<nav x-data="{ open: false }" class="bg-[#FFFFFF] border-b-2 border-[#E0DED7] sticky top-0 z-40">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center min-h-[76px] py-2 gap-4">
            <div class="flex items-center gap-8">
                <!-- Logo y Marca Earth-simple -->
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-3 min-h-[56px] pr-2 rounded-lg focus:outline-none focus:ring-[3px] focus:ring-[#1B5E20]">
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
                </a>

                <!-- Enlaces de Navegación (Escritorio) -->
                <div class="hidden sm:flex sm:items-center sm:gap-2">
                    <a
                        href="{{ route('dashboard') }}"
                        class="inline-flex items-center min-h-[56px] px-4 rounded-lg text-[18px] leading-[24px] font-bold transition-colors focus:outline-none focus:ring-[3px] focus:ring-[#1B5E20] {{ request()->routeIs('dashboard') ? 'bg-[#E8F5E9] text-[#1B5E20] border-2 border-[#2E7D32]' : 'text-[#41493E] hover:bg-[#F4F3F1] hover:text-[#1A1C1A] border-2 border-transparent' }}"
                    >
                        Inicio
                    </a>
                    <a
                        href="{{ route('evaluar-zona') }}"
                        class="inline-flex items-center min-h-[56px] px-4 rounded-lg text-[18px] leading-[24px] font-bold transition-colors focus:outline-none focus:ring-[3px] focus:ring-[#1B5E20] {{ request()->routeIs('evaluar-zona') ? 'bg-[#E8F5E9] text-[#1B5E20] border-2 border-[#2E7D32]' : 'text-[#41493E] hover:bg-[#F4F3F1] hover:text-[#1A1C1A] border-2 border-transparent' }}"
                    >
                        Evaluar zona
                    </a>
                </div>
            </div>

            <!-- Controles de Usuario Autenticado (Escritorio) -->
            <div class="hidden sm:flex sm:items-center sm:gap-3">
                <!-- Teams Dropdown -->
                @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                    <div class="relative">
                        <x-dropdown align="right" width="60">
                            <x-slot name="trigger">
                                <button type="button" class="inline-flex items-center gap-2 min-h-[56px] px-4 rounded-lg border-2 border-[#E0DED7] hover:border-[#5D4037] bg-[#FFFFFF] text-[16px] leading-[22px] font-bold text-[#1A1C1A] focus:outline-none focus:ring-[3px] focus:ring-[#1B5E20] transition-colors">
                                    <span>{{ Auth::user()->currentTeam->name }}</span>
                                    <svg class="w-4 h-4 text-[#5D4037]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                    </svg>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <div class="w-60">
                                    <div class="block px-4 py-2 text-xs font-bold uppercase text-[#5D4037]">
                                        {{ __('Manage Team') }}
                                    </div>

                                    <x-dropdown-link href="{{ route('teams.show', Auth::user()->currentTeam->id) }}">
                                        {{ __('Team Settings') }}
                                    </x-dropdown-link>

                                    @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                                        <x-dropdown-link href="{{ route('teams.create') }}">
                                            {{ __('Create New Team') }}
                                        </x-dropdown-link>
                                    @endcan

                                    @if (Auth::user()->allTeams()->count() > 1)
                                        <div class="border-t border-[#E0DED7]"></div>

                                        <div class="block px-4 py-2 text-xs font-bold uppercase text-[#5D4037]">
                                            {{ __('Switch Teams') }}
                                        </div>

                                        @foreach (Auth::user()->allTeams() as $team)
                                            <x-switchable-team :team="$team" />
                                        @endforeach
                                    @endif
                                </div>
                            </x-slot>
                        </x-dropdown>
                    </div>
                @endif

                <!-- Settings Dropdown -->
                <div class="relative">
                    <x-dropdown align="right" width="56">
                        <x-slot name="trigger">
                            <button type="button" class="inline-flex items-center gap-2.5 min-h-[56px] px-4 rounded-lg border-2 border-[#5D4037] bg-[#FFFFFF] hover:bg-[#F4F3F1] text-[16px] leading-[22px] font-bold text-[#1A1C1A] focus:outline-none focus:ring-[3px] focus:ring-[#1B5E20] transition-colors">
                                @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                    <img class="w-8 h-8 rounded-full object-cover border border-[#5D4037]" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                                @else
                                    <span class="w-8 h-8 rounded-full bg-[#1B5E20] text-[#FFFFFF] inline-flex items-center justify-center text-[15px] font-extrabold shrink-0">
                                        {{ mb_strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}
                                    </span>
                                @endif

                                <span>{{ Auth::user()->name }}</span>

                                <svg class="w-4 h-4 text-[#5D4037] shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="block px-4 py-2 text-xs font-bold uppercase tracking-wider text-[#5D4037]">
                                Mi cuenta
                            </div>

                            <x-dropdown-link href="{{ route('profile.show') }}" class="text-[16px] font-semibold text-[#1A1C1A] py-3">
                                Perfil
                            </x-dropdown-link>

                            @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                <x-dropdown-link href="{{ route('api-tokens.index') }}" class="text-[16px] font-semibold text-[#1A1C1A] py-3">
                                    Tokens API
                                </x-dropdown-link>
                            @endif

                            <div class="border-t-2 border-[#EFEEEB]"></div>

                            <form method="POST" action="{{ route('logout') }}" x-data>
                                @csrf

                                <x-dropdown-link href="{{ route('logout') }}"
                                         @click.prevent="$root.submit();"
                                         class="text-[16px] font-bold text-[#C62828] py-3">
                                    Cerrar sesión
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

            <!-- Botón Hamburguesa (Móvil, objetivo táctil 56x56px) -->
            <div class="flex items-center sm:hidden">
                <button
                    @click="open = ! open"
                    type="button"
                    aria-label="Abrir menú de navegación"
                    class="inline-flex items-center justify-center w-14 h-14 rounded-lg border-2 border-[#5D4037] bg-[#FFFFFF] text-[#1A1C1A] hover:bg-[#F4F3F1] focus:outline-none focus:ring-[3px] focus:ring-[#1B5E20] transition-colors"
                >
                    <svg class="w-7 h-7" stroke="currentColor" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Menú Responsivo (Móvil) -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t-2 border-[#E0DED7] bg-[#FFFFFF]">
        <div class="px-4 py-3 space-y-2">
            <a
                href="{{ route('dashboard') }}"
                class="flex items-center min-h-[56px] px-4 rounded-lg text-[18px] font-bold {{ request()->routeIs('dashboard') ? 'bg-[#E8F5E9] text-[#1B5E20] border-2 border-[#2E7D32]' : 'text-[#1A1C1A] hover:bg-[#F4F3F1]' }}"
            >
                Inicio
            </a>
            <a
                href="{{ route('evaluar-zona') }}"
                class="flex items-center min-h-[56px] px-4 rounded-lg text-[18px] font-bold {{ request()->routeIs('evaluar-zona') ? 'bg-[#E8F5E9] text-[#1B5E20] border-2 border-[#2E7D32]' : 'text-[#1A1C1A] hover:bg-[#F4F3F1]' }}"
            >
                Evaluar zona
            </a>
        </div>

        <div class="px-4 py-4 border-t-2 border-[#E0DED7]">
            <div class="flex items-center gap-3 mb-3">
                @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                    <img class="w-11 h-11 rounded-full object-cover border-2 border-[#5D4037]" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                @else
                    <span class="w-11 h-11 rounded-full bg-[#1B5E20] text-[#FFFFFF] inline-flex items-center justify-center text-[18px] font-extrabold shrink-0">
                        {{ mb_strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}
                    </span>
                @endif

                <div class="min-w-0">
                    <div class="font-bold text-[18px] leading-[24px] text-[#1A1C1A] truncate">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-[16px] leading-[22px] text-[#41493E] truncate">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="space-y-2">
                <a
                    href="{{ route('profile.show') }}"
                    class="flex items-center min-h-[56px] px-4 rounded-lg border-2 border-[#E0DED7] bg-[#FAF9F6] text-[18px] font-bold text-[#1A1C1A]"
                >
                    Perfil
                </a>

                @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                    <a
                        href="{{ route('api-tokens.index') }}"
                        class="flex items-center min-h-[56px] px-4 rounded-lg border-2 border-[#E0DED7] bg-[#FAF9F6] text-[18px] font-bold text-[#1A1C1A]"
                    >
                        Tokens API
                    </a>
                @endif

                <form method="POST" action="{{ route('logout') }}" x-data>
                    @csrf
                    <button
                        type="submit"
                        class="w-full flex items-center justify-center min-h-[56px] px-4 rounded-lg border-2 border-[#C62828] bg-[#FFEBEE] text-[18px] font-bold text-[#C62828]"
                    >
                        Cerrar sesión
                    </button>
                </form>

                @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                    <div class="pt-3 mt-3 border-t-2 border-[#E0DED7] space-y-2">
                        <div class="text-[14px] font-bold uppercase tracking-wider text-[#5D4037]">
                            {{ __('Manage Team') }}
                        </div>

                        <a href="{{ route('teams.show', Auth::user()->currentTeam->id) }}" class="flex items-center min-h-[56px] px-4 rounded-lg border-2 border-[#E0DED7] text-[18px] font-bold text-[#1A1C1A]">
                            {{ __('Team Settings') }}
                        </a>

                        @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                            <a href="{{ route('teams.create') }}" class="flex items-center min-h-[56px] px-4 rounded-lg border-2 border-[#E0DED7] text-[18px] font-bold text-[#1A1C1A]">
                                {{ __('Create New Team') }}
                            </a>
                        @endcan
                    </div>
                @endif
            </div>
        </div>
    </div>
</nav>
