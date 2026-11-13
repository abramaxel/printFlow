{{-- Panel putih mengambang: rounded-3xl + shadow, TANPA garis, tidak mepet tepi --}}
{{-- Overlay gelap khusus mobile --}}
<div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition.opacity
     class="fixed inset-0 z-30 bg-brand-950/40 backdrop-blur-sm lg:hidden" style="display: none;"></div>

{{-- Off-canvas di mobile, mengambang tetap di desktop --}}
<aside x-bind:class="{ 'translate-x-0': sidebarOpen, '-translate-x-[110%]': !sidebarOpen }"
       class="fixed left-4 top-4 bottom-4 z-40 w-64 bg-white rounded-3xl shadow-xl shadow-gray-300/50 flex flex-col overflow-hidden transform transition-transform duration-200 ease-out lg:translate-x-0">

    {{-- Logo (tanpa garis pemisah) --}}
    <div class="flex items-center gap-3 px-5 pt-6 pb-4 shrink-0">
        <div class="w-9 h-9 bg-brand-100 rounded-xl flex items-center justify-center shrink-0">
            <span class="text-base">🖨️</span>
        </div>
        <span class="text-base font-extrabold text-gray-900 tracking-tight truncate">{{ config('app.brand.name', 'FC Murni Jaya') }}</span>
    </div>

    @php
        $userRole = auth()->user()->role ?? '';
        $isWarehouse = in_array($userRole, ['owner', 'super_admin', 'produksi', 'production']);
        $nav = fn($route) => request()->routeIs($route)
            ? 'bg-brand-50 text-brand-700 font-bold'
            : 'text-gray-500 hover:bg-gray-100 hover:text-gray-800 font-medium';
    @endphp

    <nav @click="sidebarOpen = false" class="flex-1 px-3 pb-4 space-y-1 overflow-y-auto soft-scrollbar">

        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-brand-50 text-brand-700 font-bold' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-800 font-medium' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Dashboard
        </a>

        {{-- TRANSAKSI --}}
        @if(in_array($userRole, ['owner', 'super_admin', 'admin', 'cs']))
            @php $qcCount = \App\Models\Order::where('status', 'qc')->count(); @endphp
            <div class="pt-5 pb-1.5 px-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Transaksi</div>

            <a href="{{ route('admin.orders') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('admin.orders') && !request('status') ? 'bg-brand-50 text-brand-700 font-bold' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-800 font-medium' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Daftar Pesanan
            </a>

            <a href="{{ route('admin.orders.create') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ $nav('admin.orders.create') }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Buat Pesanan Baru
            </a>

            <a href="{{ route('admin.orders') }}?status=qc" class="flex items-center justify-between px-3 py-2.5 rounded-xl transition-colors {{ request('status') === 'qc' ? 'bg-orange-50 text-orange-600 font-bold' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-800 font-medium' }}">
                <span class="flex items-center gap-3">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Tindak Lanjut
                </span>
                @if($qcCount > 0)
                    <span class="bg-orange-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $qcCount }}</span>
                @endif
            </a>
        @endif

        {{-- PRODUKSI --}}
        @if($isWarehouse)
            <div class="pt-5 pb-1.5 px-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Produksi</div>

            <a href="{{ route('admin.production') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ $nav('admin.production') }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                Antrian Produksi
            </a>

            <a href="{{ route('admin.service-boms') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ $nav('admin.service-boms') }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                BOM Layanan
            </a>
        @endif

        {{-- MASTER DATA --}}
        @if(in_array($userRole, ['owner', 'super_admin']))
            <div class="pt-5 pb-1.5 px-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Master Data</div>

            <a href="{{ route('admin.categories') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ $nav('admin.categories') }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                Kelola Kategori
            </a>
            <a href="{{ route('admin.services') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ $nav('admin.services') }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Kelola Layanan
            </a>
            <a href="{{ route('admin.materials') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ $nav('admin.materials') }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                Kelola Bahan
            </a>
            <a href="{{ route('admin.sizes') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ $nav('admin.sizes') }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                Kelola Ukuran
            </a>
            <a href="{{ route('admin.settings') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ $nav('admin.settings') }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Pengaturan Toko
            </a>
        @endif

        {{-- PURCHASING --}}
        @if($isWarehouse)
            @php $prPending = \App\Models\PurchaseRequest::where('status', 'pending')->count(); @endphp
            <div class="pt-5 pb-1.5 px-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Purchasing</div>

            <a href="{{ route('admin.purchase-requests') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl transition-colors {{ $nav('admin.purchase-requests') }}">
                <span class="flex items-center gap-3">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Purchase Req.
                </span>
                @if($prPending > 0)
                    <span class="bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $prPending }}</span>
                @endif
            </a>
            <a href="{{ route('admin.goods-receipt') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ $nav('admin.goods-receipt') }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Penerimaan Barang
            </a>
            <a href="{{ route('admin.suppliers') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ $nav('admin.suppliers') }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Daftar Supplier
            </a>
        @endif

        {{-- INVENTORY --}}
        @if($isWarehouse)
            <div class="pt-5 pb-1.5 px-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Inventory</div>

            <a href="{{ route('admin.inventory') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ $nav('admin.inventory') }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Dashboard Stok
            </a>
            <a href="{{ route('admin.stock-opname') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ $nav('admin.stock-opname') }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                Stock Opname
            </a>
            <a href="{{ route('admin.material-audit-trail') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ $nav('admin.material-audit-trail') }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Audit Trail
            </a>
        @endif

        {{-- OWNER & CRM --}}
        @if(in_array($userRole, ['owner', 'super_admin']))
            <div class="pt-5 pb-1.5 px-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Owner & CRM</div>

            <a href="{{ route('admin.production-monitor') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ $nav('admin.production-monitor') }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                Monitor Produksi
            </a>
            <a href="{{ route('admin.customers') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ $nav('admin.customers') }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                CRM Pelanggan
            </a>
        @endif
    </nav>

    {{-- Logout (tanpa garis pemisah, hanya spacing) --}}
    <div class="px-3 pb-5 pt-2 shrink-0">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-500 hover:bg-red-50 hover:text-red-600 transition-colors">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Keluar Sistem
            </button>
        </form>
    </div>
</aside>