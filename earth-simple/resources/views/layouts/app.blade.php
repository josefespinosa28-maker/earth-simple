<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Earth-simple') }}</title>

    <!-- Fonts: Plus Jakarta Sans (Agro Daylight High-Contrast) -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap"
        rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles
</head>

<body class="bg-[#FAF9F6] text-[#1A1C1A] font-sans antialiased selection:bg-[#1B5E20] selection:text-white">
    <x-banner />

    <div class="min-h-screen flex flex-col bg-[#FAF9F6] text-[#1A1C1A]">
        @livewire('navigation-menu')

        <!-- Page Heading (opcional para vistas internas como Perfil) -->
        @if (isset($header))
            <header class="bg-[#FFFFFF] border-b-2 border-[#E0DED7]">
                <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main class="flex-1">
            {{ $slot }}
        </main>
    </div>

    @stack('modals')

    @livewireScripts
</body>

</html>