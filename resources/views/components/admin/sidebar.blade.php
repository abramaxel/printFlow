@php
    use Illuminate\Support\Facades\Route;
    use Illuminate\Support\Facades\Cache;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Schema;

    $S = [
        'home'    => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
        'file'    => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'plus'    => 'M12 4v16m8-8H4',
        'alert'   => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
        'printer' => 'M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z',
        'flask'   => 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z',
        'send'    => 'M12 19l9 2-9-18-9 18 9-2zm0 0v-8',
        'tag'     => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z',
        'box'     => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
        'ruler'   => 'M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4',
        'store'   => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
        'cart'    => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z',
        'truck'   => 'M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0 2 2 0 00-4 0zm10 0a2 2 0 104 0 2 2 0 00-4 0z',
        'building'=> 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
        'chart'   => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
        'clipboard'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
        'clock'   => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        'monitor' => 'M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2',
        'users'   => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
        'user'    => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
        'logout'  => 'M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1',
        'x'       => 'M6 18L18 6M6 6l12 12',
        'bolt'    => 'M13 10V3L4 14h7v7l9-11h-7z',
        'shield'  => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
    ];
    $sic = fn($k, $c = 'w-[18px] h-[18px]') => '<svg class="' . $c . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="' . ($S[$k] ?? $S['file']) . '"/></svg>';

    $me = auth()->user();
    $role = (string) ($me->role ?? '');
    $isOwner = in_array($role, ['owner', 'super_admin'], true);
    $isMaster = in_array($role, ['owner', 'super_admin', 'admin'], true);
    $isWarehouse = in_array($role, ['owner', 'super_admin', 'produksi', 'production'], true);
    $isFront = in_array($role, ['owner', 'super_admin', 'admin', 'cs'], true);

    $roleLabel = match ($role) {
        'owner' => 'Owner',
        'super_admin' => 'Super Admin',
        'admin' => 'Admin',
        'cs' => 'Customer Service',
        'produksi', 'production' => 'Produksi / Gudang',
        default => ucfirst(str_replace('_', ' ', $role)),
    };

    $pick = function (array $names) {
        foreach ($names as $n) if (Route::has($n)) return $n;
        return null;
    };
    $on = fn($n) => $n ? request()->routeIs($n, $n . '.*') : false;

    $rBom      = $pick(['admin.boms', 'admin.service-boms', 'admin.bom', 'admin.service.boms']);
    $rOpname   = $pick(['admin.stock-opname', 'admin.opname', 'admin.stock.opname', 'admin.stock_opname']);
    $rAudit    = $pick(['admin.material-audit-trail', 'admin.audit-trail', 'admin.audit']);
    $rMonitor  = $pick(['admin.production-monitor', 'admin.monitor', 'admin.production.monitor']);
    $rFollow   = $pick(['admin.follow-up', 'admin.followup', 'admin.tindak-lanjut', 'admin.orders.follow-up']);

    $counts = ['follow' => 0, 'pr' => 0, 'queue' => 0, 'opname' => 0];
    try {
        $counts = Cache::remember('sb.counts.' . ($me->id ?? 0), 45, function () {
            $c = ['follow' => 0, 'pr' => 0, 'queue' => 0, 'opname' => 0];
            try { $c['follow'] = \App\Models\Order::whereIn('status', ['waiting_invoice', 'unpaid'])->count(); } catch (\Throwable $e) {}
            try { $c['pr'] = \App\Models\PurchaseRequest::where('status', 'pending')->count(); } catch (\Throwable $e) {}
            try { $c['queue'] = \App\Models\Order::where('status', 'paid')->count(); } catch (\Throwable $e) {}
            try {
                if (Schema::hasTable('stock_opnames')) {
                    $c['opname'] = DB::table('stock_opnames')->where('status', 'draft')->count();
                }
            } catch (\Throwable $e) {}
            return $c;
        });
    } catch (\Throwable $e) {}

    $groups = [];

    $g = [];
    if ($isFront && Route::has('admin.orders')) $g[] = ['n' => 'admin.orders', 'label' => 'Daftar Pesanan', 'icon' => 'file', 'badge' => 0, 'tone' => 'info', 'active' => request()->routeIs('admin.orders', 'admin.orders.show', 'admin.orders.index')];
    if ($isFront && Route::has('admin.orders.create')) $g[] = ['n' => 'admin.orders.create', 'label' => 'Buat Pesanan Baru', 'icon' => 'plus', 'badge' => 0, 'tone' => 'info', 'active' => $on('admin.orders.create')];
    if ($isFront && $rFollow) $g[] = ['n' => $rFollow, 'label' => 'Tindak Lanjut', 'icon' => 'alert', 'badge' => $counts['follow'], 'tone' => 'danger', 'active' => $on($rFollow)];
    if ($isFront && !$rFollow && Route::has('admin.orders')) $g[] = ['n' => 'admin.orders', 'q' => ['status' => 'unpaid'], 'label' => 'Tindak Lanjut', 'icon' => 'alert', 'badge' => $counts['follow'], 'tone' => 'danger', 'active' => false];
    if ($g) $groups[] = ['title' => 'Transaksi', 'items' => $g];

    $g = [];
    if ($isWarehouse && Route::has('admin.production')) $g[] = ['n' => 'admin.production', 'label' => 'Antrian Produksi', 'icon' => 'printer', 'badge' => $counts['queue'], 'tone' => 'info', 'active' => $on('admin.production')];
    if ($isWarehouse && $rBom) $g[] = ['n' => $rBom, 'label' => 'BOM Layanan', 'icon' => 'flask', 'badge' => 0, 'tone' => 'info', 'active' => $on($rBom)];
    if ($isWarehouse && Route::has('admin.production.requests')) $g[] = ['n' => 'admin.production.requests', 'label' => 'Ajukan PR', 'icon' => 'send', 'badge' => $counts['pr'], 'tone' => 'warning', 'active' => $on('admin.production.requests')];
    if ($g) $groups[] = ['title' => 'Produksi', 'items' => $g];

    $g = [];
    if ($isMaster && Route::has('admin.categories')) $g[] = ['n' => 'admin.categories', 'label' => 'Kelola Kategori', 'icon' => 'tag', 'badge' => 0, 'tone' => 'info', 'active' => $on('admin.categories')];
    if ($isMaster && Route::has('admin.services')) $g[] = ['n' => 'admin.services', 'label' => 'Kelola Layanan', 'icon' => 'file', 'badge' => 0, 'tone' => 'info', 'active' => $on('admin.services')];
    if ($isMaster && Route::has('admin.materials')) $g[] = ['n' => 'admin.materials', 'label' => 'Kelola Bahan', 'icon' => 'box', 'badge' => 0, 'tone' => 'info', 'active' => $on('admin.materials')];
    if ($isMaster && Route::has('admin.sizes')) $g[] = ['n' => 'admin.sizes', 'label' => 'Kelola Ukuran', 'icon' => 'ruler', 'badge' => 0, 'tone' => 'info', 'active' => $on('admin.sizes')];
    if ($isOwner && Route::has('admin.settings')) $g[] = ['n' => 'admin.settings', 'label' => 'Pengaturan Toko', 'icon' => 'store', 'badge' => 0, 'tone' => 'info', 'active' => $on('admin.settings')];
    if ($g) $groups[] = ['title' => 'Master Data', 'items' => $g];

    $g = [];
    if ($isOwner && Route::has('admin.purchase-requests')) $g[] = ['n' => 'admin.purchase-requests', 'label' => 'Purchase Req.', 'icon' => 'cart', 'badge' => $counts['pr'], 'tone' => 'danger', 'active' => $on('admin.purchase-requests')];
    if ($isWarehouse && Route::has('admin.goods-receipt')) $g[] = ['n' => 'admin.goods-receipt', 'label' => 'Penerimaan Barang', 'icon' => 'truck', 'badge' => 0, 'tone' => 'info', 'active' => $on('admin.goods-receipt')];
    if ($isWarehouse && Route::has('admin.suppliers')) $g[] = ['n' => 'admin.suppliers', 'label' => 'Daftar Supplier', 'icon' => 'building', 'badge' => 0, 'tone' => 'info', 'active' => $on('admin.suppliers')];
    if ($g) $groups[] = ['title' => 'Purchasing', 'items' => $g];

    $g = [];
    if ($isOwner && Route::has('admin.inventory')) $g[] = ['n' => 'admin.inventory', 'label' => 'Dashboard Stok', 'icon' => 'chart', 'badge' => 0, 'tone' => 'info', 'active' => $on('admin.inventory')];
    if ($isWarehouse && $rOpname) $g[] = ['n' => $rOpname, 'label' => 'Stock Opname', 'icon' => 'clipboard', 'badge' => $counts['opname'], 'tone' => 'warning', 'active' => $on($rOpname)];
    if ($isWarehouse && $rAudit) $g[] = ['n' => $rAudit, 'label' => 'Audit Trail', 'icon' => 'clock', 'badge' => 0, 'tone' => 'info', 'active' => $on($rAudit)];
    if ($g) $groups[] = ['title' => 'Inventory', 'items' => $g];

    $g = [];
    if ($isOwner && $rMonitor) $g[] = ['n' => $rMonitor, 'label' => 'Monitor Produksi', 'icon' => 'monitor', 'badge' => 0, 'tone' => 'info', 'active' => $on($rMonitor)];
    if ($isOwner && Route::has('admin.customers')) $g[] = ['n' => 'admin.customers', 'label' => 'CRM Pelanggan', 'icon' => 'users', 'badge' => 0, 'tone' => 'info', 'active' => $on('admin.customers')];
    if ($g) $groups[] = ['title' => 'Owner & CRM', 'items' => $g];

    $badgeTone = [
        'danger'  => 'bg-red-500 text-white ring-red-300/60',
        'warning' => 'bg-amber-400 text-brand-950 ring-amber-200/70',
        'info'    => 'bg-brand-600 text-signal-300 ring-brand-300/50',
    ];

    $brandName = (string) config('app.brand.name', config('app.name', 'FC Murni Jaya'));
@endphp

<div>
    <style>
        .sb-dots { background-image: radial-gradient(rgba(10,63,51,.11) 1px, transparent 1px); background-size: 18px 18px; }
        .sb-grid { background-image: linear-gradient(rgba(10,63,51,.055) 1px, transparent 1px), linear-gradient(90deg, rgba(10,63,51,.055) 1px, transparent 1px); background-size: 34px 34px; }
        .sb-diag { background-image: repeating-linear-gradient(135deg, rgba(29,98,71,.10) 0 2px, transparent 2px 13px); }
        .sb-rule { background-image: linear-gradient(90deg, rgba(10,63,51,.22), rgba(200,236,63,.35), transparent); }
        @keyframes sbDrift { 0%,100% { transform: translate3d(0,0,0) scale(1); } 50% { transform: translate3d(14px,-18px,0) scale(1.12); } }
        .sb-drift { animation: sbDrift 22s ease-in-out infinite; }
        @keyframes sbIn { from { opacity: 0; transform: translateX(-8px); } to { opacity: 1; transform: none; } }
        .sb-in { animation: sbIn .38s cubic-bezier(.2,.7,.2,1) both; }
        @keyframes sbPing { 0%,100% { box-shadow: 0 0 0 0 rgba(220,38,38,.35); } 50% { box-shadow: 0 0 0 6px rgba(220,38,38,0); } }
        .sb-ping { animation: sbPing 2.4s ease-out infinite; }
        .sb-scroll { scrollbar-width: thin; scrollbar-color: #9dc0b3 transparent; }
        .sb-scroll::-webkit-scrollbar { width: 8px; }
        .sb-scroll::-webkit-scrollbar-track { background: rgba(10,63,51,.045); border-radius: 8px; margin: 6px 0; }
        .sb-scroll::-webkit-scrollbar-thumb { background: rgba(29,98,71,.32); border-radius: 8px; border: 2px solid transparent; background-clip: padding-box; }
        .sb-scroll::-webkit-scrollbar-thumb:hover { background: rgba(10,63,51,.55); background-clip: padding-box; }
        @media (prefers-reduced-motion: reduce) { .sb-drift,.sb-in,.sb-ping { animation: none !important; } }
    </style>

    {{-- Scrim mobile --}}
    <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-40 bg-brand-950/45 backdrop-blur-[2px] lg:hidden"></div>

    <aside class="fixed inset-y-0 left-0 z-50 w-72 p-3 transform transition-transform duration-300 ease-out lg:translate-x-0"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

        <div class="relative h-full flex flex-col overflow-hidden rounded-3xl bg-white/92 backdrop-blur-xl border border-ice-200 shadow-xl shadow-brand-950/10">

            {{-- Pola tema, opacity samar --}}
            <div class="pointer-events-none absolute inset-0 bg-gradient-to-b from-brand-50/70 via-white/0 to-signal-100/25"></div>
            <div class="sb-dots pointer-events-none absolute inset-0 opacity-40"></div>
            <div class="sb-grid pointer-events-none absolute inset-0 opacity-45"></div>
            <div class="sb-drift pointer-events-none absolute -top-20 -left-16 w-56 h-56 rounded-full bg-emerald-300/12 blur-3xl"></div>
            <div class="sb-drift pointer-events-none absolute bottom-10 -right-20 w-52 h-52 rounded-full bg-signal-300/14 blur-3xl" style="animation-delay:-8s"></div>
            <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-brand-400/40 to-transparent"></div>

            {{-- Brand --}}
            <div class="relative shrink-0 px-4 pt-4 pb-3.5">
                <div class="sb-diag pointer-events-none absolute inset-0 opacity-25"></div>
                <div class="relative flex items-center gap-3">
                    <span class="relative shrink-0 w-10 h-10 rounded-xl bg-gradient-to-br from-brand-600 to-brand-800 text-signal-300 flex items-center justify-center shadow-md shadow-brand-900/25 ring-1 ring-signal-400/30">
                        {!! $sic('printer', 'w-5 h-5') !!}
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="text-[15px] font-extrabold text-brand-950 leading-tight truncate">{{ $brandName }}</p>
                        <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-brand-700/45">Print Management</p>
                    </div>
                    <button @click="sidebarOpen = false" class="lg:hidden shrink-0 w-8 h-8 flex items-center justify-center rounded-lg text-brand-700 hover:bg-brand-50 transition-colors" title="Tutup menu">
                        {!! $sic('x', 'w-4 h-4') !!}
                    </button>
                </div>
                <div class="sb-rule mt-3.5 h-px w-full"></div>
            </div>

            {{-- Navigasi --}}
            <nav class="sb-scroll relative flex-1 overflow-y-auto px-3 pb-3">
                @if(Route::has('admin.dashboard'))
                    @php $dashOn = request()->routeIs('admin.dashboard'); @endphp
                    <a href="{{ route('admin.dashboard') }}" @click="sidebarOpen = false"
                       class="sb-in group relative flex items-center gap-3 px-3 py-2.5 rounded-xl mb-1 transition-all {{ $dashOn ? 'bg-gradient-to-r from-brand-700 to-brand-600 text-white shadow-md shadow-brand-900/25' : 'text-gray-600 hover:bg-brand-50/80 hover:text-brand-800' }}">
                        @if($dashOn)<span class="absolute left-0 top-2 bottom-2 w-1 rounded-r-full bg-signal-400"></span>@endif
                        <span class="{{ $dashOn ? 'text-signal-300' : 'text-brand-400 group-hover:text-brand-600' }} transition-colors">{!! $sic('home') !!}</span>
                        <span class="text-[13.5px] font-bold">Dashboard</span>
                    </a>
                @endif

                @foreach($groups as $gi => $group)
                    <div class="mt-4 first:mt-2">
                        <div class="flex items-center gap-2 px-3 mb-1.5">
                            <p class="text-[9.5px] font-extrabold uppercase tracking-[0.2em] text-brand-800/40">{{ $group['title'] }}</p>
                            <span class="sb-rule flex-1 h-px opacity-60"></span>
                        </div>

                        @foreach($group['items'] as $ii => $item)
                            @php
                                $href = route($item['n'], $item['q'] ?? []);
                                $act = $item['active'];
                            @endphp
                            <a href="{{ $href }}" @click="sidebarOpen = false"
                               style="animation-delay: {{ min(($gi * 3 + $ii) * 22, 260) }}ms"
                               class="sb-in group relative flex items-center gap-3 px-3 py-2.5 rounded-xl mb-0.5 transition-all {{ $act ? 'bg-gradient-to-r from-brand-700 to-brand-600 text-white shadow-md shadow-brand-900/25' : 'text-gray-600 hover:bg-brand-50/80 hover:text-brand-800' }}">
                                @if($act)<span class="absolute left-0 top-2 bottom-2 w-1 rounded-r-full bg-signal-400"></span>@endif
                                <span class="{{ $act ? 'text-signal-300' : 'text-brand-400 group-hover:text-brand-600' }} transition-colors">{!! $sic($item['icon']) !!}</span>
                                <span class="flex-1 min-w-0 text-[13.5px] font-{{ $act ? 'extrabold' : 'semibold' }} truncate">{{ $item['label'] }}</span>
                                @if($item['badge'] > 0)
                                    <span class="shrink-0 min-w-[20px] h-5 px-1.5 rounded-full text-[10px] font-black tabular-nums flex items-center justify-center ring-1 {{ $act ? 'bg-signal-400 text-brand-950 ring-signal-200/60' : $badgeTone[$item['tone']] }} {{ !$act && $item['tone'] === 'danger' ? 'sb-ping' : '' }}">
                                        {{ $item['badge'] > 99 ? '99+' : $item['badge'] }}
                                    </span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                @endforeach

                <div class="mt-4 mb-1 px-3">
                    <div class="sb-rule h-px w-full opacity-50"></div>
                </div>
            </nav>

            {{-- Akun & keluar --}}
            <div class="relative shrink-0 border-t border-ice-200/80 bg-white/70 backdrop-blur-sm px-3 py-3">
                <div class="sb-diag pointer-events-none absolute inset-0 opacity-20"></div>

                @if(Route::has('admin.account'))
                    @php $accOn = request()->routeIs('admin.account'); @endphp
                    <a href="{{ route('admin.account') }}" @click="sidebarOpen = false"
                       class="relative flex items-center gap-3 px-2.5 py-2 rounded-xl mb-1.5 transition-all {{ $accOn ? 'bg-brand-50 ring-1 ring-brand-300' : 'hover:bg-brand-50/80' }}">
                        <x-avatar :user="$me" size="sm" shape="rounded-xl" />
                        <div class="min-w-0 flex-1">
                            <p class="text-[13px] font-extrabold text-brand-950 leading-tight truncate">{{ $me->name }}</p>
                            <p class="text-[10px] font-bold uppercase tracking-wider text-brand-700/50 truncate">{{ $roleLabel }}</p>
                        </div>
                        <span class="shrink-0 text-brand-400">{!! $sic('user', 'w-4 h-4') !!}</span>
                    </a>
                @endif

                <form method="POST" action="{{ route('logout') }}" class="relative">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13.5px] font-extrabold text-red-600 hover:bg-red-50 transition-colors">
                        {!! $sic('logout') !!}
                        Keluar Sistem
                    </button>
                </form>
            </div>
        </div>
    </aside>
</div>