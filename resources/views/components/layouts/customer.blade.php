<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard' }} - {{ config('app.brand.name', 'PrintOrder') }}</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-50">

    <div class="min-h-screen">
        <!-- Topbar Customer -->
        <nav class="bg-white border-b border-gray-200 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="{{ route('customer.dashboard') }}" class="flex items-center">
                            @if(config('app.brand.logo') && file_exists(public_path(config('app.brand.logo'))))
                                <img src="{{ asset(config('app.brand.logo')) }}" alt="Logo" class="h-8 w-auto mr-2">
                            @else
                                <span class="text-2xl mr-2">🖨️</span>
                            @endif
                            <span class="text-lg font-bold text-gray-800">{{ config('app.brand.name', 'PrintOrder') }}</span>
                        </a>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-600">Halo, {{ auth()->user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-red-500 text-white text-sm font-semibold rounded-lg hover:bg-red-600 transition">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Konten Utama -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            {{ $slot }}
        </main>
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>