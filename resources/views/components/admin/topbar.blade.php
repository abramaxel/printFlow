<div class="sticky top-0 z-20 px-4 sm:px-6 lg:px-8 pt-4 pb-3 bg-ice-100/85 backdrop-blur-md">
    <div class="mx-auto w-full max-w-[1600px]">
        <div class="flex items-center justify-between gap-3 h-16 px-4 sm:px-5 bg-white rounded-2xl shadow-lg shadow-gray-300/50">

            {{-- Hamburger mobile --}}
            <button @click="sidebarOpen = true" class="lg:hidden p-2 text-brand-700 hover:bg-ice-100 rounded-xl transition-colors" title="Buka menu">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>

            {{-- GLOBAL SEARCH (Livewire — benar-benar mencari) --}}
            <div class="flex-1 min-w-0 max-w-md hidden md:block">
                <livewire:admin.global-search />
            </div>

            {{-- Kanan --}}
            <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                {{-- LONCENG NOTIFIKASI: embed komponen Livewire yang sudah Anda buat --}}
                <livewire:admin.notification-bell />

                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-3 focus:outline-none group">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-bold text-gray-900 leading-tight group-hover:text-brand-600 transition-colors">{{ auth()->user()->name }}</p>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">{{ auth()->user()->role }}</p>
                        </div>
                        <div class="h-9 w-9 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center font-bold text-sm shadow-sm">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    </button>

                    <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-3 w-48 bg-white rounded-xl shadow-lg py-1 z-50">
                        <a href="{{ route('admin.settings') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-ice-50 hover:text-brand-600 transition-colors">Pengaturan</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">Keluar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>