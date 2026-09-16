<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Absen Digital' }} · SMK BINA UTAMA KENDAL</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:rounded-school-control focus:bg-school-navy focus:px-4 focus:py-3 focus:text-sm focus:font-semibold focus:text-white">
        Lewati ke konten utama
    </a>

    <div class="min-h-screen xl:flex">
        @if(!isset($hideNav))
            @include('components.navigation.sidebar', ['active' => $active ?? Route::currentRouteName()])
        @endif

        <div class="min-w-0 flex-1">
            @if(!isset($hideNav))
                @include('components.navigation.topbar', ['active' => $active ?? Route::currentRouteName()])
            @endif

            <main id="main-content" class="min-w-0 {{ !isset($hideNav) ? 'px-4 py-5 sm:px-6 xl:px-8 xl:py-7' : '' }}">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
