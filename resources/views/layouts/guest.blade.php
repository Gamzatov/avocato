<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'AvoCato Sushi') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-[#090909] font-sans text-[#f5f5f0] antialiased">
        <div class="flex min-h-screen flex-col items-center justify-center overflow-hidden px-4 py-10">
            <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_50%_0%,rgba(185,220,61,.16),transparent_32%),linear-gradient(180deg,rgba(255,255,255,.04),transparent_34%)]"></div>
            <div class="relative z-10 w-full max-w-[440px]">
                <a class="mx-auto mb-7 flex h-24 w-24 items-center justify-center overflow-hidden rounded-full border border-white/10 bg-white shadow-[0_18px_60px_rgba(0,0,0,.38)]" href="/">
                    <img class="h-full w-full object-contain p-1" src="{{ asset('images/logo.png') }}" alt="AvoCato Sushi">
                </a>

                <div class="rounded-[24px] border border-[#2a2a2a] bg-[#111]/95 p-6 shadow-[0_24px_80px_rgba(0,0,0,.45)] sm:p-8">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
