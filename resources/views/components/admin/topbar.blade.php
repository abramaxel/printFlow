@php
    $T = [
        'menu'   => 'M4 6h16M4 12h16M4 18h16',
        'user'   => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
        'key'    => 'M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z',
        'home'   => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
        'file'   => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'plus'   => 'M12 4v16m8-8H4',
        'printer'=> 'M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z',
        'cart'   => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z',
        'truck'  => 'M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0 2 2 0 00-4 0zm10 0a2 2 0 104 0 2 2 0 00-4 0z',
        'chart'  => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
        'shield' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
        'users'  => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
        'store'  => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
        'card'   => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',
        'copy'   => 'M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z',
        'logout' => 'M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1',
        'chev'   => 'M19 9l-7 7-7-7',
        'bolt'   => 'M13 10V3L4 14h7v7l9-11h-7z',
    ];
    $tic = fn($k, $c = 'w-4 h-4') => '<svg class="'.$c.'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="'.($T[$k] ?? $T['file']).'"/></svg>';

    $me = auth()->user();
    $role = (string) ($me->role ?? '');
    $isOwner = in_array($role, ['owner', 'super_admin'], true);
    $isWarehouse = in_array($role, ['owner', 'super_admin', 'produksi', 'production'], true);
    $isFront = in_array($role, ['owner', 'super_admin', 'admin', 'cs'], true);
    $has = fn($n) => \Illuminate\Support\Facades\Route::has($n);

    $roleLabel = match ($role) {
        'owner' => 'Owner',
        'super_admin' => 'Super Admin',
        'admin' => 'Admin',
        'cs' => 'Customer Service',
        'produksi', 'production' => 'Produksi / Gudang',
        default => ucfirst(str_replace('_', ' ', $role)),
    };

    $work = [];
    if ($has('admin.dashboard')) $work[] = ['admin.dashboard', 'Dashboard Saya', 'home'];
    if ($isFront && $has('admin.orders')) $work[] = ['admin.orders', 'Daftar Pesanan', 'file'];
    if ($isFront && $has('admin.orders.create')) $work[] = ['admin.orders.create', 'Buat Pesanan Baru', 'plus'];
    if ($isWarehouse && $has('admin.production')) $work[] = ['admin.production', 'Antrian Produksi', 'printer'];
    if ($isWarehouse && $has('admin.production.requests')) $work[] = ['admin.production.requests', 'Ajukan Purchase Request', 'cart'];
    if ($isOwner && $has('admin.purchase-requests')) $work[] = ['admin.purchase-requests', 'Persetujuan Pembelian', 'shield'];
    if ($isWarehouse && $has('admin.goods-receipt')) $work[] = ['admin.goods-receipt', 'Penerimaan Barang', 'truck'];
    if ($isOwner && $has('admin.inventory')) $work[] = ['admin.inventory', 'Dashboard Inventory', 'chart'];
    if ($isOwner && $has('admin.customers')) $work[] = ['admin.customers', 'CRM Pelanggan', 'users'];
    $work = array_slice($work, 0, 6);

    $auditRoute = null;
    foreach (['admin.material-audit-trail', 'admin.audit-trail', 'admin.audit'] as $r) {
        if ($has($r)) { $auditRoute = $r; break; }
    }

    $brandName = (string) config('app.brand.name', config('app.name', 'FC Murni Jaya'));
@endphp

<style>
    .tb-scroll { scrollbar-width: thin; scrollbar-color: rgba(29,98,71,.42) rgba(10,63,51,.05); }
    .tb-scroll::-webkit-scrollbar { width: 8px }
    .tb-scroll::-webkit-scrollbar-track { background: rgba(10,63,51,.05); border-radius: 8px; margin: 6px 0 }
    .tb-scroll::-webkit-scrollbar-thumb { background: rgba(29,98,71,.42); border-radius: 8px; border: 2px solid transparent; background-clip: padding-box }
    .tb-scroll::-webkit-scrollbar-thumb:hover { background: rgba(10,63,51,.68); background-clip: padding-box }
    .tb-glass { background: rgba(255,255,255,.90); backdrop-filter: blur(18px) saturate(140%); -webkit-backdrop-filter: blur(18px) saturate(140%); }
    .tb-hair { background: linear-gradient(90deg, transparent, rgba(200,236,63,.75) 22%, rgba(29,98,71,.35) 55%, transparent); }
</style>

<div class="sticky top-0 z-30 pointer-events-none px-3 sm:px-5 lg:px-7 pt-3 pb-2">
    <div class="mx-auto w-full max-w-[1600px] pointer-events-auto">
        <div class="relative flex items-center gap-2 sm:gap-3 h-16 px-2.5 sm:px-4 rounded-[1.35rem] tb-glass border border-ice-200 shadow-lg shadow-brand-950/10">
            <span class="tb-hair absolute inset-x-8 top-0 h-px pointer-events-none"></span>

            <button type="button" @click="sidebarOpen = true"
                    class="lg:hidden shrink-0 w-10 h-10 flex items-center justify-center rounded-xl text-brand-700 hover:bg-brand-50 active:scale-95 transition-all"
                    title="Buka menu" aria-label="Buka menu">
                {!! $tic('menu', 'w-5 h-5') !!}
            </button>

            <div class="lg:hidden flex items-center gap-2 min-w-0 mr-auto">
                <span class="shrink-0 w-8 h-8 rounded-lg bg-gradient-to-br from-brand-600 to-brand-800 text-signal-300 flex items-center justify-center">
                    {!! $tic('printer', 'w-4 h-4') !!}
                </span>
                <p class="text-[13px] font-extrabold text-brand-950 truncate">{{ $brandName }}</p>
            </div>

            <div class="hidden md:block flex-1 min-w-0 max-w-xl">
                <livewire:admin.global-search />
            </div>

            <div class="flex items-center gap-1.5 sm:gap-2.5 shrink-0 ml-auto">
                <livewire:admin.notification-bell />

                <span class="hidden sm:block w-px h-8 bg-ice-300"></span>

                <div class="relative" x-data="{ open: false }" @keydown.escape.window="open = false">
                    <button type="button" @click="open = !open" :aria-expanded="open"
                            class="flex items-center gap-2.5 pl-1 pr-1.5 sm:pr-2 py-1 rounded-xl hover:bg-brand-50/70 active:scale-[.98] transition-all focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500/30">
                        <div class="text-right hidden sm:block">
                            <p class="text-[13px] font-extrabold text-brand-950 leading-tight">{{ $me->name }}</p>
                            <p class="text-[9.5px] font-bold text-brand-700/50 uppercase tracking-[.14em]">{{ $roleLabel }}</p>
                        </div>
                        <x-avatar :user="$me" size="md" shape="rounded-xl" class="shadow-sm ring-1 ring-brand-200" />
                        <span class="hidden sm:block text-brand-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''">{!! $tic('chev') !!}</span>
                    </button>

                    <div x-show="open" x-cloak @click.away="open = false"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="tb-scroll absolute right-0 mt-3 w-72 max-h-[76vh] overflow-y-auto bg-white rounded-2xl shadow-2xl shadow-brand-950/20 border border-ice-200 z-50">

                        <div class="forest-hero relative overflow-hidden px-4 py-3.5 rounded-t-2xl">
                            <div class="forest-dots absolute inset-0 opacity-40"></div>
                            <div class="relative flex items-center gap-3">
                                <x-avatar :user="$me" size="md" shape="rounded-xl" />
                                <div class="min-w-0">
                                    <p class="text-sm font-extrabold text-white truncate">{{ $me->name }}</p>
                                    <p class="text-[11px] text-emerald-100/70 truncate">{{ $me->email }}</p>
                                    <span class="mt-1 inline-flex px-2 py-0.5 rounded bg-signal-400 text-brand-950 text-[9px] font-extrabold uppercase tracking-wider">{{ $roleLabel }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="p-1.5">
                            <p class="px-3 pt-2 pb-1 text-[9px] font-bold uppercase tracking-[.18em] text-brand-800/40">Akun Saya</p>
                            @if($has('admin.account'))
                                <a href="{{ route('admin.account') }}" @click="open = false"
                                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold text-brand-900 hover:bg-brand-50 transition-colors">
                                    <span class="text-brand-500">{!! $tic('user') !!}</span>
                                    Profil &amp; Akun
                                </a>
                                <a href="{{ route('admin.account') }}#keamanan" @click="open = false"
                                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold text-brand-900 hover:bg-brand-50 transition-colors">
                                    <span class="text-amber-500">{!! $tic('key') !!}</span>
                                    Ganti Kata Sandi
                                </a>
                            @endif
                            <button type="button" onclick="copyText('{{ $me->email }}', this)"
                                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold text-brand-900 hover:bg-brand-50 transition-colors text-left">
                                <span class="text-brand-500">{!! $tic('copy') !!}</span>
                                Salin Email Login
                            </button>
                        </div>

                        @if(count($work))
                            <div class="p-1.5 border-t border-ice-100">
                                <p class="px-3 pt-2 pb-1 text-[9px] font-bold uppercase tracking-[.18em] text-brand-800/40">Ruang Kerja</p>
                                @foreach($work as $w)
                                    <a href="{{ route($w[0]) }}" @click="open = false"
                                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-gray-700 hover:bg-brand-50 hover:text-brand-700 transition-colors">
                                        <span class="text-brand-400">{!! $tic($w[2]) !!}</span>
                                        {{ $w[1] }}
                                    </a>
                                @endforeach
                            </div>
                        @endif

                        @if($isOwner || $auditRoute)
                            <div class="p-1.5 border-t border-ice-100">
                                <p class="px-3 pt-2 pb-1 text-[9px] font-bold uppercase tracking-[.18em] text-brand-800/40">Kontrol &amp; Jejak</p>
                                @if($auditRoute)
                                    <a href="{{ route($auditRoute) }}" @click="open = false"
                                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-gray-700 hover:bg-brand-50 hover:text-brand-700 transition-colors">
                                        <span class="text-brand-400">{!! $tic('shield') !!}</span>
                                        Audit Trail Stok
                                    </a>
                                @endif
                                @if($isOwner && $has('admin.settings'))
                                    <a href="{{ route('admin.settings') }}" @click="open = false"
                                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-gray-700 hover:bg-brand-50 hover:text-brand-700 transition-colors">
                                        <span class="text-brand-400">{!! $tic('store') !!}</span>
                                        Pengaturan Toko
                                    </a>
                                @endif
                                @if($isOwner && $has('admin.payment-channels'))
                                    <a href="{{ route('admin.payment-channels') }}" @click="open = false"
                                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-gray-700 hover:bg-brand-50 hover:text-brand-700 transition-colors">
                                        <span class="text-brand-400">{!! $tic('card') !!}</span>
                                        Kanal Pembayaran
                                    </a>
                                @endif
                            </div>
                        @endif

                        <div class="p-1.5 border-t border-ice-100">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-extrabold text-red-600 hover:bg-red-50 transition-colors">
                                    {!! $tic('logout') !!}
                                    Keluar dari Akun
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>