@php
    $I = [
        'chart'  => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
        'box'    => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
        'money'  => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'alert'  => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
        'fire'   => 'M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.24 17 7.2c1 2.16 1.5 4.3 1.5 5.8 0 1.5-.5 3-1.5 4.2z',
        'check'  => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        'clock'  => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        'bolt'   => 'M13 10V3L4 14h7v7l9-11h-7z',
        'cart'   => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z',
        'truck'  => 'M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0 2 2 0 00-4 0zm10 0a2 2 0 104 0 2 2 0 00-4 0z',
        'search' => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z',
        'undo'   => 'M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6',
        'arrow'  => 'M14 5l7 7m0 0l-7 7m7-7H3',
        'down'   => 'M19 14l-7 7m0 0l-7-7m7 7V3',
        'up'     => 'M5 10l7-7m0 0l7 7m-7-7v18',
        'lock'   => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z',
        'note'   => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'shield' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
        'user'   => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
        'chev'   => 'M19 9l-7 7-7-7',
    ];
    $iic = fn($k, $c = 'w-5 h-5') => '<svg class="' . $c . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="' . ($I[$k] ?? $I['box']) . '"/></svg>';
    $empty = fn($t) => '<p class="px-6 py-7 text-center text-sm font-bold text-gray-400">' . $t . '</p>';

    $ST = [
        'habis'  => ['Habis', 'bg-red-600 text-white', 'text-red-600', 'bg-red-600'],
        'kritis' => ['Kritis', 'bg-red-100 text-red-700', 'text-red-600', 'bg-red-500'],
        'rendah' => ['Rendah', 'bg-amber-100 text-amber-800', 'text-amber-600', 'bg-amber-500'],
        'over'   => ['Berlebih', 'bg-sky-100 text-sky-700', 'text-sky-600', 'bg-sky-500'],
        'aman'   => ['Aman', 'bg-signal-100 text-signal-700', 'text-signal-600', 'bg-brand-500'],
    ];
    $UG = ['critical' => ['Kritis', 'bg-red-600 text-white'], 'warning' => ['Warning', 'bg-amber-400 text-brand-950'], 'normal' => ['Normal', 'bg-ice-200 text-brand-800']];
    $VM = [
        'kritis'    => ['Gudang Kritis', 'fire', 'bg-red-600 text-white'],
        'perhatian' => ['Perlu Perhatian', 'alert', 'bg-amber-500 text-white'],
        'sehat'     => ['Gudang Sehat', 'check', 'bg-signal-400 text-brand-950'],
    ][$verdict];

    $urgentCount = $stats['critical'] + $stats['pending_pr'];
    $watchCount = count($expiring) + $stats['idle_total'];
    $dataCount = $stats['no_rop'] + $stats['no_price'];
    $dataText = trim(($stats['no_rop'] > 0 ? $stats['no_rop'] . ' bahan tanpa batas minimum' : '') . ($stats['no_rop'] > 0 && $stats['no_price'] > 0 ? ' · ' : '') . ($stats['no_price'] > 0 ? $stats['no_price'] . ' bahan tanpa harga' : ''));
@endphp

<div class="w-full space-y-4">
    <style>
        .danger-hero { background: radial-gradient(ellipse at top left, rgba(248,113,113,.42), transparent 58%), linear-gradient(135deg,#991b1b 0%,#7f1d1d 50%,#450a0a 100%); }
        .danger-dots { background-image: radial-gradient(rgba(254,202,202,.20) 1px, transparent 1px); background-size: 20px 20px; }
        .iv-grid { background-image: linear-gradient(rgba(200,236,63,.08) 1px, transparent 1px), linear-gradient(90deg, rgba(200,236,63,.08) 1px, transparent 1px); background-size: 30px 30px; }
        .hazard-bar { background-image: repeating-linear-gradient(45deg,#dc2626 0 9px,#fee2e2 9px 18px); }
        @keyframes ivRise { from { opacity:0; transform: translateY(12px);} to { opacity:1; transform:none;} }
        .iv-rise { animation: ivRise .45s cubic-bezier(.2,.7,.2,1) both; }
        @keyframes ivSiren { 0%,100% { box-shadow: 0 0 0 0 rgba(220,38,38,.45);} 50% { box-shadow: 0 0 0 10px rgba(220,38,38,0);} }
        .iv-siren { animation: ivSiren 2s ease-out infinite; }
        @keyframes ivGrow { from { transform: scaleY(0);} to { transform: scaleY(1);} }
        .iv-bar { transform-origin: bottom; animation: ivGrow .5s cubic-bezier(.2,.7,.2,1) both; }
        @keyframes ivWide { from { transform: scaleX(0);} to { transform: scaleX(1);} }
        .iv-wide { transform-origin: left; animation: ivWide .55s cubic-bezier(.2,.7,.2,1) both; }
        .iv-scroll::-webkit-scrollbar { width: 9px; height: 9px; }
        .iv-scroll::-webkit-scrollbar-track { background: #e2edf0; border-radius: 9px; }
        .iv-scroll::-webkit-scrollbar-thumb { background: #1d6247; border-radius: 9px; border: 2px solid #e2edf0; }
        .iv-scroll::-webkit-scrollbar-thumb:hover { background: #0a3f33; }
        .iv-select { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' stroke='%230a3f33' stroke-width='2.5' viewBox='0 0 24 24'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right .8rem center; background-size: .95rem; }
        details.iv-fold > summary { list-style: none; cursor: pointer; }
        details.iv-fold > summary::-webkit-details-marker { display: none; }
        details.iv-fold[open] .iv-chev { transform: rotate(180deg); }
        #p1, #p2, #p3, #katalog { scroll-margin-top: 104px; }
        @media (prefers-reduced-motion: reduce) { .iv-rise,.iv-siren,.iv-bar,.iv-wide { animation: none !important; } }
    </style>

    {{-- ═══ LAPIS 0 · VERDICT + ANGKA KUNCI ═══ --}}
    <div class="forest-hero relative overflow-hidden rounded-3xl text-white shadow-lg shadow-brand-950/25 iv-rise">
        <div class="forest-dots absolute inset-0 opacity-40"></div>
        <div class="iv-grid absolute inset-0 opacity-45"></div>
        <div class="absolute -top-24 -left-16 w-72 h-72 rounded-full bg-emerald-500/12 blur-3xl"></div>

        <div class="relative z-10 px-6 sm:px-8 pt-6 pb-5">
            <div class="flex flex-col xl:flex-row xl:items-start justify-between gap-5">
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/8 border border-white/15 text-[10px] font-bold uppercase tracking-[0.2em] text-signal-300">{!! $iic('lock', 'w-3 h-3') !!} Owner</span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold uppercase tracking-wider {{ $VM[2] }} {{ $verdict === 'kritis' ? 'iv-siren' : '' }}">{!! $iic($VM[1], 'w-3.5 h-3.5') !!} {{ $VM[0] }}</span>
                        <span class="text-[11px] font-bold text-emerald-100/55 tabular-nums">{{ $range }} hari terakhir</span>
                    </div>
                    <h1 class="mt-3 text-2xl sm:text-3xl font-extrabold tracking-tight">Dashboard Inventory</h1>
                    <div class="mt-4 flex flex-wrap items-end gap-x-8 gap-y-3">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-emerald-100/55">Nilai Persediaan</p>
                            <p class="text-3xl sm:text-4xl font-extrabold text-signal-300 tabular-nums tracking-tight leading-none mt-1">Rp {{ number_format($stats['value'], 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-emerald-100/55">Siap Pakai</p>
                            <p class="text-xl font-extrabold tabular-nums leading-none mt-1">Rp {{ number_format($stats['net_value'], 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-emerald-100/55">Teralokasi</p>
                            <p class="text-xl font-extrabold tabular-nums leading-none mt-1">Rp {{ number_format($stats['reserved_value'], 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-emerald-100/55">SKU Aktif</p>
                            <p class="text-xl font-extrabold tabular-nums leading-none mt-1">{{ $stats['sku'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="shrink-0 flex flex-col gap-2.5">
                    <div class="flex items-center gap-1 p-1 rounded-2xl bg-white/8 border border-white/15">
                        @foreach([30, 60, 90] as $d)
                            <button type="button" wire:click="setRange({{ $d }})" class="px-3.5 py-1.5 rounded-xl text-sm font-extrabold tabular-nums transition-colors {{ $range === $d ? 'bg-signal-400 text-brand-950' : 'text-emerald-100/75 hover:bg-white/10' }}">{{ $d }}h</button>
                        @endforeach
                        <button type="button" wire:click="$refresh" title="Muat ulang" class="w-8 h-8 flex items-center justify-center rounded-xl text-emerald-100/75 hover:bg-white/10 transition-colors">{!! $iic('undo', 'w-4 h-4') !!}</button>
                    </div>
                    <a href="{{ route('admin.purchase-requests') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-signal-400 text-brand-950 text-sm font-extrabold hover:bg-signal-500 shadow-md shadow-signal-400/20 transition-all">
                        {!! $iic('cart', 'w-4 h-4') !!} Keputusan Pembelian
                        <span class="px-1.5 py-0.5 rounded-full bg-brand-950 text-signal-300 text-[10px] font-black tabular-nums">{{ $stats['pending_pr'] }}</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="relative z-10 border-t border-white/10 bg-brand-950/30 px-6 sm:px-8 py-3">
            <div class="flex flex-wrap items-stretch divide-x divide-white/10">
                <button type="button" onclick="document.getElementById('p1')?.scrollIntoView({behavior:'smooth',block:'start'})" class="group flex-1 min-w-[9rem] text-left pr-4 py-1.5">
                    <span class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-[0.14em] {{ $urgentCount > 0 ? 'text-red-300' : 'text-emerald-100/50' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $urgentCount > 0 ? 'bg-red-500' : 'bg-emerald-400/40' }}"></span> Prioritas 1 · Bertindak
                    </span>
                    <span class="mt-0.5 flex items-baseline gap-1.5">
                        <span class="text-2xl font-extrabold tabular-nums {{ $urgentCount > 0 ? 'text-white' : 'text-emerald-100/60' }}">{{ $urgentCount }}</span>
                        <span class="text-[11px] font-bold text-emerald-100/50 group-hover:text-signal-300 transition-colors">item →</span>
                    </span>
                </button>
                <button type="button" onclick="document.getElementById('p2')?.scrollIntoView({behavior:'smooth',block:'start'})" class="group flex-1 min-w-[9rem] text-left px-4 py-1.5">
                    <span class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-[0.14em] {{ $watchCount > 0 ? 'text-amber-300' : 'text-emerald-100/50' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $watchCount > 0 ? 'bg-amber-400' : 'bg-emerald-400/40' }}"></span> Prioritas 2 · Pantau
                    </span>
                    <span class="mt-0.5 flex items-baseline gap-1.5">
                        <span class="text-2xl font-extrabold tabular-nums {{ $watchCount > 0 ? 'text-white' : 'text-emerald-100/60' }}">{{ $watchCount }}</span>
                        <span class="text-[11px] font-bold text-emerald-100/50 group-hover:text-signal-300 transition-colors">item →</span>
                    </span>
                </button>
                <button type="button" onclick="document.getElementById('p3')?.scrollIntoView({behavior:'smooth',block:'start'})" class="group flex-1 min-w-[9rem] text-left px-4 py-1.5">
                    <span class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-[0.14em] {{ $dataCount > 0 ? 'text-sky-300' : 'text-emerald-100/50' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $dataCount > 0 ? 'bg-sky-400' : 'bg-emerald-400/40' }}"></span> Prioritas 3 · Data
                    </span>
                    <span class="mt-0.5 flex items-baseline gap-1.5">
                        <span class="text-2xl font-extrabold tabular-nums {{ $dataCount > 0 ? 'text-white' : 'text-emerald-100/60' }}">{{ $dataCount }}</span>
                        <span class="text-[11px] font-bold text-emerald-100/50 group-hover:text-signal-300 transition-colors">item →</span>
                    </span>
                </button>
                <button type="button" onclick="document.getElementById('katalog')?.scrollIntoView({behavior:'smooth',block:'start'})" class="group flex-1 min-w-[9rem] text-left pl-4 py-1.5">
                    <span class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-[0.14em] text-emerald-100/50">
                        <span class="w-1.5 h-1.5 rounded-full bg-signal-400/60"></span> Referensi · Katalog
                    </span>
                    <span class="mt-0.5 flex items-baseline gap-1.5">
                        <span class="text-2xl font-extrabold tabular-nums text-emerald-100/80">{{ $catalog_total }}</span>
                        <span class="text-[11px] font-bold text-emerald-100/50 group-hover:text-signal-300 transition-colors">bahan →</span>
                    </span>
                </button>
            </div>
        </div>
    </div>

    {{-- ═══ PRIORITAS 1 ═══ --}}
    <section id="p1" class="rounded-3xl overflow-hidden border-2 {{ $urgentCount > 0 ? 'border-red-300' : 'border-signal-300' }} bg-white shadow-sm iv-rise">
        <div class="relative overflow-hidden {{ $urgentCount > 0 ? 'danger-hero' : 'forest-hero' }} px-6 py-4">
            <div class="{{ $urgentCount > 0 ? 'danger-dots' : 'forest-dots' }} absolute inset-0 opacity-50"></div>
            <div class="absolute top-0 left-0 right-0 h-1 {{ $urgentCount > 0 ? 'hazard-bar' : '' }}"></div>
            <div class="relative z-10 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <span class="shrink-0 w-10 h-10 rounded-xl flex items-center justify-center {{ $urgentCount > 0 ? 'bg-white text-red-700 iv-siren' : 'bg-signal-400 text-brand-950' }}">{!! $iic($urgentCount > 0 ? 'fire' : 'check', 'w-5 h-5') !!}</span>
                    <div class="min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-[0.22em] {{ $urgentCount > 0 ? 'text-red-200' : 'text-signal-300' }}">Prioritas 1</p>
                        <h2 class="text-lg font-extrabold text-white tracking-tight leading-tight">Bertindak Sekarang</h2>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2 text-xs font-bold">
                    <span class="px-3 py-1.5 rounded-lg bg-white/15 border border-white/20 text-white tabular-nums">{{ $stats['critical'] }} kritis · Rp {{ number_format($stats['restock_cost'], 0, ',', '.') }}</span>
                    <span class="px-3 py-1.5 rounded-lg bg-white/15 border border-white/20 text-white tabular-nums">{{ $stats['pending_pr'] }} PR · Rp {{ number_format($stats['pending_value'], 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        @if($urgentCount > 0)
            <div class="grid grid-cols-1 xl:grid-cols-12 divide-y xl:divide-y-0 xl:divide-x divide-ice-200">
                <div class="xl:col-span-7">
                    <div class="px-6 py-3 bg-ice-50 border-b border-ice-200 flex items-center justify-between gap-3">
                        <p class="text-sm font-extrabold text-brand-950">Bahan Perlu Dibeli</p>
                        <a href="{{ route('admin.production.requests') }}" class="text-xs font-extrabold text-brand-700 hover:text-brand-600 transition-colors">Halaman pengajuan →</a>
                    </div>
                    @if(count($restock))
                        <div class="iv-scroll overflow-x-auto">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="text-[10px] uppercase tracking-wider text-gray-400 border-b border-ice-200">
                                        <th class="px-6 py-2.5 font-bold">Bahan</th>
                                        <th class="px-3 py-2.5 font-bold text-right">Sisa / Min</th>
                                        <th class="px-3 py-2.5 font-bold text-right">Beli</th>
                                        <th class="px-3 py-2.5 font-bold text-right">Biaya</th>
                                        <th class="px-6 py-2.5 font-bold text-right">PR</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-ice-100">
                                    @foreach($restock as $r)
                                        <tr class="hover:bg-red-50/40 transition-colors border-l-4 {{ $r['net'] <= 0 ? 'border-l-red-600' : 'border-l-red-300' }}">
                                            <td class="px-6 py-3">
                                                <p class="text-sm font-extrabold text-brand-950 leading-tight">{{ $r['name'] }}</p>
                                                <p class="text-[11px] text-gray-500">{{ $r['category'] }} · lead {{ $r['lead'] }}h</p>
                                                <div class="mt-1 w-24 h-1.5 rounded-full bg-ice-200 overflow-hidden">
                                                    <div class="h-full rounded-full {{ $ST[$r['status']][3] }}" style="width: {{ max(3, $r['coverage']) }}%"></div>
                                                </div>
                                            </td>
                                            <td class="px-3 py-3 text-right">
                                                <p class="text-base font-extrabold tabular-nums leading-none {{ $r['net'] <= 0 ? 'text-red-600' : 'text-brand-900' }}">{{ number_format($r['net'], 2) }}</p>
                                                <p class="text-[10px] text-gray-400 tabular-nums mt-0.5">min {{ number_format($r['min'], 2) }}</p>
                                            </td>
                                            <td class="px-3 py-3 text-right">
                                                <p class="text-base font-extrabold text-brand-700 tabular-nums leading-none">{{ number_format($r['suggest'], 2) }}</p>
                                                <p class="text-[10px] text-gray-400 mt-0.5">{{ $r['unit'] }}</p>
                                            </td>
                                            <td class="px-3 py-3 text-right text-sm font-extrabold text-brand-950 tabular-nums">Rp {{ number_format($r['suggest_cost'], 0, ',', '.') }}</td>
                                            <td class="px-6 py-3 text-right">
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-extrabold {{ $r['pr_number'] ? 'bg-amber-100 text-amber-800' : 'bg-red-600 text-white' }}">
                                                    {!! $iic($r['pr_number'] ? 'clock' : 'alert', 'w-3 h-3') !!} {{ $r['pr_number'] ?? 'Belum' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                    {!! count($restock) ? '' : $empty('Semua stok di atas batas minimum.') !!}
                </div>

                <div class="xl:col-span-5">
                    <div class="px-6 py-3 bg-ice-50 border-b border-ice-200 flex items-center justify-between gap-3">
                        <p class="text-sm font-extrabold text-brand-950">PR Menunggu Anda</p>
                        <a href="{{ route('admin.purchase-requests') }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-brand-600 text-white text-[11px] font-extrabold hover:bg-brand-700 transition-colors">Tinjau {!! $iic('arrow', 'w-3 h-3') !!}</a>
                    </div>
                    @if(count($pending))
                        <div class="divide-y divide-ice-100">
                            @foreach($pending as $p)
                                <div class="px-6 py-3 flex items-center justify-between gap-3 border-l-4 {{ $p['urgency'] === 'critical' ? 'border-l-red-500' : 'border-l-transparent' }}">
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-1.5">
                                            <span class="text-sm font-extrabold text-brand-950">{{ $p['number'] }}</span>
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold uppercase {{ $UG[$p['urgency']][1] ?? $UG['normal'][1] }}">{{ $UG[$p['urgency']][0] ?? $p['urgency'] }}</span>
                                        </div>
                                        <p class="text-[11px] text-gray-500 truncate">{{ $p['material'] }} · {{ number_format($p['qty'], 2) }} {{ $p['unit'] }}</p>
                                        <p class="text-[10px] text-gray-400">{{ $p['age'] }}h menunggu{{ $p['needed'] ? ' · butuh ' . $p['needed'] : '' }}</p>
                                    </div>
                                    <p class="shrink-0 text-sm font-extrabold text-brand-950 tabular-nums">Rp {{ number_format($p['estimate'], 0, ',', '.') }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    {!! count($pending) ? '' : $empty('Tidak ada PR yang menunggu keputusan.') !!}

                    <div class="px-6 py-3.5 border-t border-ice-200 bg-brand-50/50 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Pembelian bulan ini</p>
                            <p class="text-lg font-extrabold text-brand-950 tabular-nums leading-tight">Rp {{ number_format($stats['month_value'], 0, ',', '.') }}</p>
                            <p class="text-[11px] text-gray-500 tabular-nums">{{ $stats['month_count'] }} BPB · {{ $stats['incoming_pr'] }} siap diterima</p>
                        </div>
                        <a href="{{ route('admin.goods-receipt') }}" class="shrink-0 inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-brand-600 text-white text-xs font-extrabold hover:bg-brand-700 transition-colors">{!! $iic('truck', 'w-3.5 h-3.5') !!} Terima</a>
                    </div>
                </div>
            </div>
        @endif

        @if($urgentCount === 0)
            <div class="px-6 py-8 text-center">
                <span class="inline-flex w-14 h-14 rounded-full bg-signal-100 text-signal-600 items-center justify-center mx-auto">{!! $iic('shield', 'w-7 h-7') !!}</span>
                <p class="mt-3 text-lg font-extrabold text-brand-950">Gudang aman — tidak ada tindakan mendesak</p>
                <p class="mt-1 text-sm text-gray-500">Semua bahan di atas batas minimum dan tidak ada PR yang menunggu keputusan.</p>
            </div>
        @endif
    </section>

    {{-- ═══ PRIORITAS 2 ═══ --}}
    <section id="p2" class="rounded-3xl overflow-hidden border border-ice-200 bg-white shadow-sm iv-rise">
        <div class="px-6 py-3.5 bg-ice-50 border-b border-ice-200 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="w-9 h-9 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center">{!! $iic('clock', 'w-5 h-5') !!}</span>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-amber-600">Prioritas 2</p>
                    <h2 class="text-base font-extrabold text-brand-950 leading-tight">Pantau &amp; Efisiensi Modal</h2>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2 text-xs font-bold">
                <span class="px-3 py-1.5 rounded-lg bg-amber-50 border border-amber-200 text-amber-800 tabular-nums">Mengendap Rp {{ number_format($stats['idle_value'], 0, ',', '.') }}</span>
                <span class="px-3 py-1.5 rounded-lg bg-ice-100 border border-ice-300 text-brand-800 tabular-nums">Kadaluarsa Rp {{ number_format($stats['expire_value'], 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 divide-y lg:divide-y-0 lg:divide-x divide-ice-200">
            <div>
                <div class="px-6 py-2.5 border-b border-ice-100 flex items-center justify-between gap-2">
                    <p class="text-sm font-extrabold text-brand-950">Modal Mengendap</p>
                    <span class="text-[11px] font-bold text-gray-400">tanpa pakai {{ $range }}h</span>
                </div>
                @if(count($idle))
                    <div class="divide-y divide-ice-100">
                        @foreach($idle as $r)
                            <div class="px-6 py-2.5 flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-brand-950 truncate">{{ $r['name'] }}</p>
                                    <p class="text-[11px] text-gray-500 tabular-nums">{{ number_format($r['stock'], 2) }} {{ $r['unit'] }} · {{ $r['idle_days'] === null ? 'belum pernah dipakai' : $r['idle_days'] . 'h lalu' }}</p>
                                </div>
                                <p class="shrink-0 text-sm font-extrabold text-amber-700 tabular-nums">Rp {{ number_format($r['value'], 0, ',', '.') }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
                {!! count($idle) ? '' : $empty('Perputaran sehat — tidak ada bahan mengendap.') !!}
            </div>

            <div>
                <div class="px-6 py-2.5 border-b border-ice-100 flex items-center justify-between gap-2">
                    <p class="text-sm font-extrabold text-brand-950">Batch Mendekati Kadaluarsa</p>
                    <span class="text-[11px] font-bold text-gray-400">≤ 30 hari</span>
                </div>
                @if(count($expiring))
                    <div class="divide-y divide-ice-100">
                        @foreach($expiring as $b)
                            <div class="px-6 py-2.5 flex items-center justify-between gap-3 border-l-4 {{ $b['days'] <= 7 ? 'border-l-red-500' : 'border-l-amber-400' }}">
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-brand-950 truncate">{{ $b['batch'] }}</p>
                                    <p class="text-[11px] text-gray-500 tabular-nums">{{ number_format($b['qty'], 2) }} · {{ $b['supplier'] ?: 'supplier —' }}</p>
                                </div>
                                <div class="shrink-0 text-right">
                                    <p class="text-sm font-extrabold text-amber-700 tabular-nums">Rp {{ number_format($b['value'], 0, ',', '.') }}</p>
                                    <p class="text-[10px] font-bold {{ $b['days'] < 0 ? 'text-red-600' : 'text-gray-400' }}">{{ $b['days'] < 0 ? 'lewat ' . abs($b['days']) . 'h' : $b['days'] . 'h lagi' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
                {!! count($expiring) ? '' : $empty('Tidak ada risiko kadaluarsa dalam 30 hari.') !!}
            </div>
        </div>
    </section>

    {{-- ═══ PRIORITAS 3 ═══ --}}
    @if($dataCount > 0)
        <section id="p3" class="rounded-2xl border border-sky-200 bg-sky-50/60 px-5 py-4 iv-rise">
            <div class="flex flex-col lg:flex-row lg:items-center gap-4">
                <div class="flex items-center gap-3 min-w-0 flex-1">
                    <span class="shrink-0 w-9 h-9 rounded-xl bg-sky-600 text-white flex items-center justify-center">{!! $iic('note', 'w-5 h-5') !!}</span>
                    <div class="min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-sky-700">Prioritas 3 · Kualitas Data</p>
                        <p class="text-sm font-extrabold text-brand-950 leading-snug">{{ $dataText }}</p>
                        <p class="text-[11px] text-gray-600">MRP belum bisa memperingatkan restock &amp; valuasi belum lengkap untuk bahan tersebut.</p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2 shrink-0">
                    <button type="button" wire:click="setStatusFilter('norop')" onclick="setTimeout(()=>document.getElementById('katalog')?.scrollIntoView({behavior:'smooth',block:'start'}),120)" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white border border-sky-300 text-sky-800 text-xs font-extrabold hover:bg-sky-100 transition-colors">{!! $iic('search', 'w-3.5 h-3.5') !!} Lihat tanpa ROP</button>
                    <a href="{{ route('admin.materials') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-brand-600 text-white text-xs font-extrabold hover:bg-brand-700 transition-colors">{!! $iic('box', 'w-3.5 h-3.5') !!} Lengkapi Data</a>
                </div>
            </div>
        </section>
    @endif

    {{-- ═══ ANALITIK (dilipat) ═══ --}}
    <details class="iv-fold rounded-3xl border border-ice-200 bg-white shadow-sm overflow-hidden iv-rise">
        <summary class="px-6 py-4 bg-ice-50 hover:bg-ice-100 transition-colors flex items-center justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
                <span class="shrink-0 w-9 h-9 rounded-xl bg-brand-600 text-signal-300 flex items-center justify-center">{!! $iic('chart', 'w-5 h-5') !!}</span>
                <div class="min-w-0">
                    <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-gray-400">Analitik Pendukung</p>
                    <p class="text-base font-extrabold text-brand-950 leading-tight">Arus stok, komposisi nilai &amp; pemakaian</p>
                </div>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <span class="hidden sm:inline text-xs font-bold text-gray-500 tabular-nums">masuk {{ number_format($stats['in_qty'], 0, ',', '.') }} · keluar {{ number_format($stats['out_qty'], 0, ',', '.') }}</span>
                <span class="iv-chev w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-ice-300 text-brand-700 transition-transform">{!! $iic('chev', 'w-4 h-4') !!}</span>
            </div>
        </summary>

        <div class="border-t border-ice-200 divide-y divide-ice-200">
            <div class="grid grid-cols-1 xl:grid-cols-12 divide-y xl:divide-y-0 xl:divide-x divide-ice-200">
                <div class="xl:col-span-7 p-6">
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                        <p class="text-sm font-extrabold text-brand-950">Arus Stok {{ $range }} Hari</p>
                        <div class="flex items-center gap-4 text-[11px] font-bold">
                            <span class="inline-flex items-center gap-1.5 text-brand-800"><span class="w-2.5 h-2.5 rounded-sm bg-signal-400"></span> Masuk</span>
                            <span class="inline-flex items-center gap-1.5 text-brand-800"><span class="w-2.5 h-2.5 rounded-sm bg-red-500"></span> Keluar</span>
                        </div>
                    </div>
                    @if($peak > 0)
                        <div class="flex items-end gap-[3px] h-36 border-b-2 border-ice-200">
                            @foreach($series as $i => $d)
                                <div class="flex-1 min-w-0 h-full flex items-end justify-center gap-[2px]" title="{{ $d['day'] }} — masuk {{ number_format($d['in'], 2) }}, keluar {{ number_format($d['out'], 2) }}">
                                    <div class="iv-bar w-1/2 rounded-t bg-signal-400 hover:bg-signal-500 transition-colors" style="height: {{ $d['in_pct'] }}%; animation-delay: {{ min($i * 6, 320) }}ms"></div>
                                    <div class="iv-bar w-1/2 rounded-t bg-red-500 hover:bg-red-600 transition-colors" style="height: {{ $d['out_pct'] }}%; animation-delay: {{ min($i * 6 + 30, 350) }}ms"></div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-1.5 flex justify-between text-[10px] font-bold text-gray-400 tabular-nums">
                            <span>{{ $series[0]['day'] ?? '' }}</span>
                            <span>{{ $series[(int) floor(count($series) / 2)]['day'] ?? '' }}</span>
                            <span>{{ $series[count($series) - 1]['day'] ?? '' }}</span>
                        </div>
                        <div class="mt-4 flex flex-wrap gap-x-6 gap-y-2 text-sm">
                            <span class="text-gray-500">Nilai masuk <b class="text-brand-950 tabular-nums">Rp {{ number_format($stats['in_val'], 0, ',', '.') }}</b></span>
                            <span class="text-gray-500">Nilai keluar <b class="text-brand-950 tabular-nums">Rp {{ number_format($stats['out_val'], 0, ',', '.') }}</b></span>
                        </div>
                    @endif
                    {!! $peak > 0 ? '' : $empty('Belum ada pergerakan stok tercatat pada rentang ini.') !!}
                </div>

                <div class="xl:col-span-5 p-6">
                    <p class="text-sm font-extrabold text-brand-950 mb-4">Komposisi Nilai per Divisi</p>
                    @if(count($composition))
                        <div class="space-y-3">
                            @foreach($composition as $i => $c)
                                <div>
                                    <div class="flex items-center justify-between gap-3 mb-1">
                                        <span class="inline-flex items-center gap-2 min-w-0">
                                            <span class="w-2.5 h-2.5 rounded-sm shrink-0" style="background: {{ $c['hue'] }}"></span>
                                            <span class="text-xs font-bold text-brand-950 truncate">{{ $c['name'] }}</span>
                                        </span>
                                        <span class="shrink-0 text-xs font-extrabold text-brand-950 tabular-nums">Rp {{ number_format($c['value'], 0, ',', '.') }}</span>
                                    </div>
                                    <div class="h-2 rounded-full bg-ice-200 overflow-hidden">
                                        <div class="iv-wide h-full rounded-full" style="width: {{ $c['bar'] }}%; background: {{ $c['hue'] }}; animation-delay: {{ $i * 60 }}ms"></div>
                                    </div>
                                    <p class="mt-0.5 text-[10px] font-bold text-gray-400 tabular-nums">{{ $c['share'] }}%</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    {!! count($composition) ? '' : $empty('Valuasi belum terbentuk — lengkapi harga master bahan.') !!}
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-12 divide-y xl:divide-y-0 xl:divide-x divide-ice-200">
                <div class="xl:col-span-7 p-6">
                    <div class="mb-4">
                        <p class="text-sm font-extrabold text-brand-950">{{ $mover_source === 'movement' ? 'Bahan Paling Banyak Dipakai' : 'Permintaan Produksi Aktif' }}</p>
                        <p class="text-[11px] text-gray-500">{{ $mover_source === 'movement' ? 'Pemakaian nyata ' . $range . ' hari' : 'Belum ada pemakaian — ditampilkan kebutuhan pesanan berjalan' }}</p>
                    </div>
                    @if(count($movers))
                        <div class="space-y-3">
                            @foreach($movers as $i => $mv)
                                <div>
                                    <div class="flex items-center justify-between gap-3 mb-1">
                                        <span class="inline-flex items-center gap-2 min-w-0">
                                            <span class="shrink-0 w-5 h-5 rounded bg-brand-600 text-signal-300 flex items-center justify-center text-[10px] font-black tabular-nums">{{ $i + 1 }}</span>
                                            <span class="text-xs font-bold text-brand-950 truncate">{{ $mv['name'] }}</span>
                                        </span>
                                        <span class="shrink-0 text-xs font-extrabold text-brand-700 tabular-nums">{{ number_format($mv['qty'], 2) }} {{ $mv['unit'] }}</span>
                                    </div>
                                    <div class="h-2 rounded-full bg-ice-200 overflow-hidden">
                                        <div class="iv-wide h-full rounded-full bg-gradient-to-r from-brand-600 to-signal-400" style="width: {{ max(4, $mv['share']) }}%; animation-delay: {{ $i * 60 }}ms"></div>
                                    </div>
                                    <div class="mt-0.5 flex items-center justify-between text-[10px] font-bold">
                                        <span class="text-gray-400 tabular-nums">Rp {{ number_format($mv['value'], 0, ',', '.') }}</span>
                                        <span class="{{ $ST[$mv['status']][2] }} tabular-nums">sisa {{ number_format($mv['net'], 2) }} · {{ $ST[$mv['status']][0] }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    {!! count($movers) ? '' : $empty('Belum ada pemakaian maupun permintaan aktif.') !!}
                </div>

                <div class="xl:col-span-5 p-6">
                    <p class="text-sm font-extrabold text-brand-950 mb-4">Supplier Teratas Bulan Ini</p>
                    @if(count($suppliers))
                        <div class="divide-y divide-ice-100">
                            @foreach($suppliers as $s)
                                <div class="py-2.5 flex items-center justify-between gap-3">
                                    <span class="min-w-0 inline-flex items-center gap-2 text-xs font-bold text-brand-950 truncate">{!! $iic('user', 'w-3.5 h-3.5') !!} {{ $s['name'] }}</span>
                                    <span class="shrink-0 text-right">
                                        <span class="block text-xs font-extrabold text-brand-950 tabular-nums">Rp {{ number_format($s['value'], 0, ',', '.') }}</span>
                                        <span class="block text-[10px] text-gray-400 tabular-nums">{{ $s['count'] }} BPB</span>
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    {!! count($suppliers) ? '' : $empty('Belum ada pembelian bulan ini.') !!}
                </div>
            </div>
        </div>
    </details>

    {{-- ═══ REFERENSI · KATALOG ═══ --}}
    <section id="katalog" class="rounded-3xl border border-ice-200 bg-white shadow-sm overflow-hidden iv-rise">
        <div class="px-6 py-4 bg-ice-50 border-b border-ice-200">
            <div class="flex flex-col lg:flex-row gap-3 lg:items-center">
                <div class="min-w-0 lg:w-52 shrink-0">
                    <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-gray-400">Referensi</p>
                    <p class="text-base font-extrabold text-brand-950 leading-tight">Katalog Persediaan</p>
                </div>
                <div class="relative flex-1 min-w-0">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-brand-400">{!! $iic('search', 'w-4 h-4') !!}</span>
                    <input type="text" wire:model.live.debounce.250ms="search" placeholder="Cari bahan atau divisi..." class="w-full pl-10 pr-4 py-2.5 text-sm border border-ice-300 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25 focus:border-brand-500 transition-all">
                </div>
                <div class="flex flex-wrap items-center gap-2 shrink-0">
                    <select wire:model.live="categoryFilter" class="iv-select pl-3.5 pr-9 py-2.5 text-xs font-bold text-brand-800 border border-ice-300 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25 cursor-pointer">
                        <option value="all">Semua divisi</option>
                        @foreach($categories as $c)
                            <option value="{{ $c['id'] }}">{{ $c['name'] }}</option>
                        @endforeach
                    </select>
                    <select wire:model.live="sort" class="iv-select pl-3.5 pr-9 py-2.5 text-xs font-bold text-brand-800 border border-ice-300 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25 cursor-pointer">
                        <option value="urgency">Urgensi</option>
                        <option value="value">Nilai terbesar</option>
                        <option value="stock">Sisa tersedikit</option>
                        <option value="name">Nama</option>
                    </select>
                    @if($has_filter)
                        <button type="button" wire:click="clearFilters" title="Reset saringan" class="w-9 h-9 flex items-center justify-center rounded-xl bg-red-50 text-red-600 hover:bg-red-100 transition-colors">{!! $iic('undo', 'w-4 h-4') !!}</button>
                    @endif
                </div>
            </div>
            <div class="mt-3 flex flex-wrap items-center gap-2">
                @foreach(['all' => 'Semua', 'critical' => 'Kritis / Habis', 'idle' => 'Mengendap', 'norop' => 'Tanpa ROP', 'over' => 'Berlebih'] as $k => $l)
                    <button type="button" wire:click="setStatusFilter('{{ $k }}')" class="px-3.5 py-1.5 rounded-full text-xs font-extrabold transition-all {{ $statusFilter === $k ? 'bg-brand-600 text-signal-300 shadow-sm' : 'bg-white border border-ice-300 text-brand-800 hover:bg-ice-100' }}">{{ $l }}</button>
                @endforeach
                <span class="ml-auto text-xs text-gray-500 tabular-nums"><b class="text-brand-950">{{ $catalog_total }}</b> bahan cocok</span>
            </div>
        </div>

        <div wire:loading.class="opacity-40 pointer-events-none" class="transition-opacity duration-200">
            @if(count($catalog))
                <div class="iv-scroll overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-[10px] uppercase tracking-wider text-gray-400 border-b border-ice-200">
                                <th class="px-6 py-2.5 font-bold">Bahan</th>
                                <th class="px-3 py-2.5 font-bold text-right">Stok</th>
                                <th class="px-3 py-2.5 font-bold text-right">Alokasi</th>
                                <th class="px-3 py-2.5 font-bold text-right">Sisa</th>
                                <th class="px-3 py-2.5 font-bold">Cakupan ROP</th>
                                <th class="px-3 py-2.5 font-bold text-right">Nilai</th>
                                <th class="px-6 py-2.5 font-bold text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-ice-100">
                            @foreach($catalog as $r)
                                <tr class="hover:bg-ice-50/70 transition-colors border-l-4 {{ in_array($r['status'], ['habis','kritis']) ? 'border-l-red-500' : 'border-l-transparent' }}">
                                    <td class="px-6 py-2.5">
                                        <p class="text-sm font-bold text-brand-950 leading-tight">{{ $r['name'] }}</p>
                                        <p class="text-[11px] text-gray-500">{{ $r['category'] }} · lead {{ $r['lead'] }}h{{ $r['batch'] ? ' · FIFO' : '' }}</p>
                                        <div class="mt-1 flex flex-wrap gap-1">
                                            <span class="px-1.5 py-0.5 rounded bg-ice-300 text-gray-600 text-[9px] font-extrabold uppercase {{ $r['active'] ? 'hidden' : '' }}">nonaktif</span>
                                            <span class="px-1.5 py-0.5 rounded bg-amber-100 text-amber-800 text-[9px] font-extrabold uppercase {{ $r['no_rop'] ? '' : 'hidden' }}">tanpa ROP</span>
                                            <span class="px-1.5 py-0.5 rounded bg-sky-100 text-sky-700 text-[9px] font-extrabold uppercase {{ $r['no_price'] ? '' : 'hidden' }}">tanpa harga</span>
                                        </div>
                                    </td>
                                    <td class="px-3 py-2.5 text-right text-xs font-bold text-brand-900 tabular-nums">{{ number_format($r['stock'], 2) }}</td>
                                    <td class="px-3 py-2.5 text-right text-xs font-bold text-amber-600 tabular-nums">{{ number_format($r['reserved'], 2) }}</td>
                                    <td class="px-3 py-2.5 text-right">
                                        <p class="text-sm font-extrabold tabular-nums leading-none {{ $r['net'] <= 0 ? 'text-red-600' : 'text-brand-950' }}">{{ number_format($r['net'], 2) }}</p>
                                        <p class="text-[10px] text-gray-400 mt-0.5">{{ $r['unit'] }}</p>
                                    </td>
                                    <td class="px-3 py-2.5">
                                        <div class="w-20 h-1.5 rounded-full bg-ice-200 overflow-hidden {{ $r['no_rop'] ? 'hidden' : '' }}">
                                            <div class="h-full rounded-full {{ $ST[$r['status']][3] }}" style="width: {{ max(3, $r['coverage']) }}%"></div>
                                        </div>
                                        <p class="mt-0.5 text-[10px] font-bold {{ $r['no_rop'] ? 'text-amber-600' : 'text-gray-400' }} tabular-nums">{{ $r['no_rop'] ? 'belum diatur' : $r['coverage'] . '% · min ' . number_format($r['min'], 2) }}</p>
                                    </td>
                                    <td class="px-3 py-2.5 text-right">
                                        <p class="text-xs font-extrabold text-brand-950 tabular-nums">Rp {{ number_format($r['value'], 0, ',', '.') }}</p>
                                        <p class="text-[10px] text-gray-400 tabular-nums">Rp {{ number_format($r['price'], 0, ',', '.') }}/{{ $r['unit'] }}</p>
                                    </td>
                                    <td class="px-6 py-2.5 text-right">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-extrabold {{ $ST[$r['status']][1] }}">{{ $ST[$r['status']][0] }}</span>
                                        <p class="mt-0.5 text-[10px] font-bold text-brand-600 tabular-nums {{ $r['demand'] > 0 ? '' : 'hidden' }}">butuh {{ number_format($r['demand'], 2) }}</p>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            @if($catalog_total > 12)
                <div class="px-6 py-3 border-t border-ice-200 bg-ice-50 flex justify-center">
                    <button type="button" wire:click="toggleAll" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-ice-300 text-brand-800 text-xs font-extrabold hover:bg-ice-100 transition-colors">
                        {!! $iic($showAll ? 'up' : 'down', 'w-3.5 h-3.5') !!} {{ $showAll ? 'Ringkas ke 12 teratas' : 'Tampilkan semua ' . $catalog_total . ' bahan' }}
                    </button>
                </div>
            @endif

            {!! count($catalog) ? '' : $empty('Tidak ada bahan yang cocok dengan saringan ini.') !!}
        </div>
    </section>
</div>