<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard' }} - {{ config('app.brand.name', 'FC Murni Jaya') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Build Vite (bukan CDN) agar palet brand/ice/signal aktif --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>

    @livewireStyles
</head>
<body class="bg-ice-100 text-gray-900 antialiased min-h-screen flex flex-col">

    {{-- TOPBAR MENGAMBANG (satu bahasa dengan topbar admin) --}}
    <header class="sticky top-0 z-30 px-4 sm:px-6 lg:px-8 pt-4 pb-2 bg-ice-100/85 backdrop-blur-md">
        <div class="max-w-7xl mx-auto">
            <div class="flex items-center justify-between gap-3 h-16 px-4 sm:px-5 bg-white rounded-2xl shadow-lg shadow-gray-300/50">

                {{-- Kiri: brand --}}
                <a href="{{ route('customer.dashboard') }}" class="flex items-center gap-2.5 min-w-0">
                    <span class="w-9 h-9 bg-brand-600 rounded-xl flex items-center justify-center text-lg shadow-sm shrink-0">🖨️</span>
                    <span class="text-lg font-extrabold text-brand-950 tracking-tight truncate">{{ config('app.brand.name', 'FC Murni Jaya') }}</span>
                    <span class="hidden md:inline-flex px-2.5 py-1 bg-ice-100 text-brand-700 text-xs font-bold rounded-full uppercase tracking-wider">Portal Pelanggan</span>
                </a>

                {{-- Kanan: profil saja (tanpa tombol bantuan) --}}
                <div class="flex items-center gap-3 shrink-0">
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-3 focus:outline-none group">
                            <div class="text-right hidden sm:block">
                                <p class="text-base font-bold text-gray-900 leading-tight group-hover:text-brand-600 transition-colors">{{ auth()->user()->name }}</p>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Pelanggan</p>
                            </div>
                            <div class="h-10 w-10 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center font-extrabold text-base shadow-sm">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                        </button>

                        <div x-show="open" @click.away="open = false" x-transition
                             class="absolute right-0 mt-3 w-52 bg-white rounded-xl shadow-lg border border-ice-200 py-1.5 z-50">
                            <div class="px-4 py-2 border-b border-ice-100 mb-1">
                                <p class="text-base font-bold text-brand-950 truncate">{{ auth()->user()->name }}</p>
                                <p class="text-sm text-gray-500 truncate">{{ auth()->user()->email }}</p>
                            </div>
                            <a href="{{ route('customer.dashboard') }}" class="block px-4 py-2.5 text-base text-gray-700 hover:bg-ice-50 hover:text-brand-600 transition-colors">Dashboard Saya</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2.5 text-base font-bold text-red-600 hover:bg-red-50 transition-colors">Keluar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="flex-1 pt-2">
        {{ $slot }}
    </main>

    {{-- Helper salin dengan fallback untuk HTTP (non-secure context) --}}
    <script>
        window.copyText = function (text, btn) {
            var done = function () {
                if (!btn) return;
                var old = btn.innerHTML;
                btn.innerHTML = '✅ Tersalin';
                setTimeout(function () { btn.innerHTML = old; }, 1800);
            };
            var fallback = function () {
                var ta = document.createElement('textarea');
                ta.value = text;
                ta.style.position = 'fixed';
                ta.style.opacity = '0';
                document.body.appendChild(ta);
                ta.select();
                try { document.execCommand('copy'); done(); } catch (e) {}
                document.body.removeChild(ta);
            };
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(done).catch(fallback);
            } else {
                fallback();
            }
        };
    </script>

    @livewireScripts
</body>
</html>