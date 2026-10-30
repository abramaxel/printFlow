<aside class="fixed top-0 left-0 z-40 w-64 h-screen transition-transform bg-white border-r border-gray-200">
    <div class="flex items-center h-16 px-6 border-b border-gray-200">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center">
            @if(config('app.brand.logo') && file_exists(public_path(config('app.brand.logo'))))
                <img src="{{ asset(config('app.brand.logo')) }}" alt="Logo" class="h-8 w-auto mr-2">
            @else
                <span class="text-2xl mr-2">🖨️</span>
            @endif
            <span class="text-lg font-bold text-gray-800 truncate">{{ config('app.brand.name', 'PrintOrder') }}</span>
        </a>
    </div>

    <nav class="p-4 space-y-1 overflow-y-auto h-[calc(100vh-4rem)]">
        {{-- DASHBOARD (Semua Role) --}}
        <a href="{{ route('admin.dashboard') }}" 
           class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100 transition {{ request()->routeIs('admin.dashboard') ? 'bg-green-50 text-green-700 font-semibold' : '' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            Dashboard
        </a>

        @php $userRole = auth()->user()->role ?? ''; @endphp

        {{-- TRANSAKSI (Owner, Admin, CS) --}}
        @if(in_array($userRole, ['owner', 'super_admin', 'admin', 'cs']))
        <div class="pt-4 mt-4 border-t border-gray-200">
            <div class="px-4 text-xs font-semibold text-gray-500 uppercase mb-2">Transaksi</div>

            <a href="{{ route('admin.orders') }}" 
               class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100 transition {{ request()->routeIs('admin.orders') ? 'bg-green-50 text-green-700 font-semibold' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-3a1 1 0 011-1zm-7 0h3a1 1 0 011 1v3a1 1 0 01-1 1H3a1 1 0 01-1-1v-3a1 1 0 011-1zm0-9h10a1 1 0 011 1v3a1 1 0 01-1 1H3a1 1 0 01-1-1V3a1 1 0 011-1z"></path></svg>
                Daftar Pesanan
            </a>

            <a href="{{ route('admin.orders.create') }}" 
               class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100 transition {{ request()->routeIs('admin.orders.create') ? 'bg-green-50 text-green-700 font-semibold' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Buat Pesanan Baru
            </a>

            {{-- Menu Khusus CS: Perlu Tindak Lanjut (Order QC) --}}
            @if(in_array($userRole, ['cs', 'admin', 'super_admin', 'owner']))
                @php $qcCount = \App\Models\Order::where('status', 'qc')->count(); @endphp
                <a href="{{ route('admin.orders') }}?status=qc" 
                   class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-orange-50 transition {{ request('status') === 'qc' ? 'bg-orange-50 text-orange-700 font-semibold' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Perlu Tindak Lanjut
                    @if($qcCount > 0)
                        <span class="ml-auto bg-orange-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $qcCount }}</span>
                    @endif
                </a>
            @endif
        </div>
        @endif

        {{-- PRODUKSI (Owner, Admin Produksi, Super Admin) --}}
        @if(in_array($userRole, ['owner', 'super_admin', 'produksi']))
        <div class="pt-4 mt-4 border-t border-gray-200">
            <div class="px-4 text-xs font-semibold text-gray-500 uppercase mb-2">Produksi</div>

            <a href="{{ route('admin.production') }}" 
               class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100 transition {{ request()->routeIs('admin.production') ? 'bg-green-50 text-green-700 font-semibold' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                Antrian Produksi
            </a>
        </div>
        @endif

        {{-- MASTER DATA (Owner, Super Admin, Admin) --}}
        @if(in_array($userRole, ['owner', 'super_admin', 'admin']))
        <div class="pt-4 mt-4 border-t border-gray-200">
            <div class="px-4 text-xs font-semibold text-gray-500 uppercase mb-2">Master Data</div>

            <a href="{{ route('admin.services') }}" 
               class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100 transition {{ request()->routeIs('admin.services') ? 'bg-green-50 text-green-700 font-semibold' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Kelola Layanan
            </a>

            <a href="{{ route('admin.materials') }}" 
               class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100 transition {{ request()->routeIs('admin.materials') ? 'bg-green-50 text-green-700 font-semibold' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                Kelola Bahan
            </a>

            <a href="{{ route('admin.sizes') }}" 
               class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100 transition {{ request()->routeIs('admin.sizes') ? 'bg-green-50 text-green-700 font-semibold' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                Kelola Ukuran
            </a>
        </div>
        @endif

        {{-- OWNER & CRM (Hanya Owner & Super Admin) --}}
        @if(in_array($userRole, ['owner', 'super_admin']))
        <div class="pt-4 mt-4 border-t border-gray-200">
            <div class="px-4 text-xs font-semibold text-gray-500 uppercase mb-2">Owner & CRM</div>

            <a href="{{ route('admin.production-monitor') }}" 
               class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100 transition {{ request()->routeIs('admin.production-monitor') ? 'bg-green-50 text-green-700 font-semibold' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                Monitor Produksi
            </a>
        </div>
        @endif

        {{-- LOGOUT --}}
        <div class="pt-4 mt-4 border-t border-gray-200">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center px-4 py-2 text-red-600 rounded-lg hover:bg-red-50 transition">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Logout
                </button>
            </form>
        </div>
    </nav>
</aside>