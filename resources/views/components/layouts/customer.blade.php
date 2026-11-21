<!DOCTYPE html>
<html lang="id">
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
        [x-cloak] { display: none !important; }
    </style>

    @livewireStyles
</head>
<body class="bg-ice-100 text-gray-900 antialiased min-h-screen flex flex-col">

    <header class="sticky top-0 z-30 px-4 sm:px-6 lg:px-8 pt-4 pb-2 bg-ice-100/85 backdrop-blur-md">
        <div class="max-w-7xl mx-auto">
            <div class="flex items-center justify-between gap-3 h-16 px-4 sm:px-5 bg-white rounded-2xl shadow-lg shadow-gray-300/50">

                <a href="{{ route('customer.dashboard') }}" class="flex items-center gap-2.5 min-w-0">
                    <span class="w-9 h-9 bg-brand-600 rounded-xl flex items-center justify-center text-lg shadow-sm shrink-0">🖨️</span>
                    <span class="text-lg font-extrabold text-brand-950 tracking-tight truncate">{{ config('app.brand.name', 'FC Murni Jaya') }}</span>
                    <span class="hidden md:inline-flex px-2.5 py-1 bg-ice-100 text-brand-700 text-xs font-bold rounded-full uppercase tracking-wider">Portal Pelanggan</span>
                </a>

                <div class="flex items-center gap-3 shrink-0">
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-3 focus:outline-none group">
                            <div class="text-right hidden sm:block">
                                <p class="text-base font-bold text-gray-900 leading-tight group-hover:text-brand-600 transition-colors">{{ auth()->user()->name }}</p>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Pelanggan</p>
                            </div>
                            <x-avatar :user="auth()->user()" size="md" shape="rounded-full" class="shadow-sm" />
                        </button>

                        <div x-show="open" x-cloak @click.away="open = false" x-transition
                             class="absolute right-0 mt-3 w-60 bg-white rounded-xl shadow-lg border border-ice-200 py-1.5 z-50">
                            <div class="px-4 py-3 border-b border-ice-100 mb-1 flex items-center gap-3">
                                <x-avatar :user="auth()->user()" size="sm" shape="rounded-xl" />
                                <div class="min-w-0">
                                    <p class="text-base font-bold text-brand-950 truncate">{{ auth()->user()->name }}</p>
                                    <p class="text-sm text-gray-500 truncate">{{ auth()->user()->email }}</p>
                                </div>
                            </div>

                            <a href="{{ route('customer.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 text-base text-gray-700 hover:bg-ice-50 hover:text-brand-600 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                Dashboard Saya
                            </a>
                            <a href="{{ route('customer.profile') }}" class="flex items-center gap-3 px-4 py-2.5 text-base text-gray-700 hover:bg-ice-50 hover:text-brand-600 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Cek Profile
                            </a>
                            <a href="{{ route('customer.history') }}" class="flex items-center gap-3 px-4 py-2.5 text-base text-gray-700 hover:bg-ice-50 hover:text-brand-600 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Riwayat
                            </a>
                            <a href="{{ route('customer.settings') }}" class="flex items-center gap-3 px-4 py-2.5 text-base text-gray-700 hover:bg-ice-50 hover:text-brand-600 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Pengaturan
                            </a>

                            <div class="border-t border-ice-100 my-1"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-base font-bold text-red-600 hover:bg-red-50 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    Keluar
                                </button>
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

    @include('components.customer.footer')

    <script>
        window.copyText = function (text, btn) {
            var done = function () {
                if (!btn) return;
                var old = btn.innerHTML;
                btn.innerHTML = 'Tersalin';
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