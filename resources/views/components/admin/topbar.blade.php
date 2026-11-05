<header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-6 shadow-sm z-30 sticky top-0">
    {{-- Kiri: Judul Halaman / Breadcrumb --}}
    <div class="flex items-center gap-4">
        {{-- Tombol Toggle Sidebar Mobile --}}
        <button @click="$dispatch('toggle-sidebar')" class="md:hidden p-2 text-gray-500 hover:text-gray-700 rounded-lg hover:bg-gray-100 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        </button>
        
        {{-- Judul Halaman Dinamis (Opsional) --}}
        <h1 class="text-xl font-bold text-gray-800 hidden sm:block">{{ $title ?? 'Dashboard' }}</h1>
    </div>

    {{-- Kanan: Search Bar (Desktop Only) + Notifikasi & Profil --}}
    <div class="flex items-center gap-4">
        
        {{-- 🔍 SEARCH BAR (Hanya tampil di layar besar) --}}
        <div class="hidden md:block relative">
            <input type="text" placeholder="Cari pesanan, invoice..." 
                class="pl-10 pr-4 py-2 w-64 bg-gray-50 border border-gray-200 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
            <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </div>

        {{-- 🔔 NOTIFICATION BELL (Livewire Component) --}}
        @livewire('admin.notification-bell')

        {{-- 👤 USER PROFILE DROPDOWN --}}
        <div x-data="{ open: false }" @click.away="open = false" class="relative">
            <button @click="open = !open" class="flex items-center gap-3 pl-3 pr-2 py-1.5 rounded-full hover:bg-gray-100 transition border border-transparent hover:border-gray-200">
                {{-- Avatar Inisial --}}
                <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-bold shadow-sm">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                
                {{-- Nama & Role (Hidden di mobile) --}}
                <div class="hidden lg:flex flex-col items-start text-right">
                    <span class="text-sm font-semibold text-gray-700 leading-tight">{{ auth()->user()->name }}</span>
                    <span class="text-[10px] text-gray-500 uppercase tracking-wide">{{ str_replace('_', ' ', auth()->user()->role) }}</span>
                </div>
                
                {{-- Chevron Icon --}}
                <svg class="w-4 h-4 text-gray-400 transform transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>

            {{-- Dropdown Menu --}}
            <div x-show="open" 
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="transform opacity-100 scale-100"
                x-transition:leave-end="transform opacity-0 scale-95"
                class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-gray-100 py-1 z-50 origin-top-right"
                style="display: none;">
                
                {{-- Info User Singkat --}}
                <div class="px-4 py-3 border-b border-gray-100 mb-1">
                    <p class="text-xs text-gray-500">Login sebagai</p>
                    <p class="text-sm font-medium text-gray-900 truncate">{{ auth()->user()->email }}</p>
                </div>

                {{-- Menu Items --}}
                <a href="{{ route('customer.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2 transition">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    Lihat Profil
                </a>
                
                {{-- Pengaturan Toko (Hanya Owner/Super Admin) --}}
                @if(in_array(auth()->user()->role, ['owner', 'super_admin']))
                    <a href="{{ route('admin.settings') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2 transition">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Pengaturan Toko
                    </a>
                @endif

                <div class="border-t border-gray-100 my-1"></div>

                {{-- Logout --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>