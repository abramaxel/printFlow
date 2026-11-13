<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard' }} - {{ config('app.brand.name', 'FC Murni Jaya') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .soft-scrollbar::-webkit-scrollbar { width: 5px; }
        .soft-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .soft-scrollbar::-webkit-scrollbar-thumb { background: #cfdfe4; border-radius: 10px; }
        .soft-scrollbar::-webkit-scrollbar-thumb:hover { background: #b3cbd3; }
    </style>

    @livewireStyles
</head>
<body class="bg-ice-100 text-gray-900 antialiased min-h-screen">

{{-- State sidebar mobile + wrapper --}}
<div x-data="{ sidebarOpen: false }" class="min-h-screen">

    @include('components.admin.sidebar')

    {{-- Konten: tanpa margin di mobile, ml-72 mulai layar besar --}}
    <div class="flex flex-col min-h-screen lg:ml-72">

        @include('components.admin.topbar')

        <main class="flex-1 px-4 sm:px-6 lg:px-8 pb-8 pt-2">
            {{-- Batas lebar agar tidak "melar" di monitor besar --}}
            <div class="mx-auto w-full max-w-[1600px]">
                {{ $slot }}
            </div>
        </main>
    </div>

</div>

@livewireScripts
</body>
</html>