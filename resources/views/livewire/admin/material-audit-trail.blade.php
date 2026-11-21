@php
    $A = [
        'up'     => 'M5 10l7-7m0 0l7 7m-7-7v18',
        'down'   => 'M19 14l-7 7m0 0l-7-7m7 7V3',
        'equal'  => 'M5 9h14M5 15h14',
        'clock'  => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        'box'    => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
        'user'   => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
        'search' => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z',
        'undo'   => 'M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6',
        'excel'  => 'M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2',
        'file'   => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'eye'    => 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z',
        'x'      => 'M6 18L18 6M6 6l12 12',
        'copy'   => 'M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z',
        'shield' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
        'alert'  => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
        'link'   => 'M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1',
        'left'   => 'M15 19l-7-7 7-7',
        'right'  => 'M9 5l7 7-7 7',
        'chart'  => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
        'lock'   => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z',
        'check'  => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
    ];
    $aic = fn($k, $c = 'w-5 h-5') => '<svg class="' . $c . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="' . ($A[$k] ?? $A['box']) . '"/></svg>';
    $empty = fn($t) => '<p class="px-6 py-10 text-center text-sm font-bold text-gray-400">' . $t . '</p>';
    $lastDay = null;
@endphp

<div class="w-full space-y-4">
    <style>
        .at-grid { background-image: linear-gradient(rgba(200,236,63,.08) 1px, transparent 1px), linear-gradient(90deg, rgba(200,236,63,.08) 1px, transparent 1px); background-size: 28px 28px; }
        @keyframes atRise { from { opacity:0; transform: translateY(12px);} to { opacity:1; transform:none;} }
        .at-rise { animation: atRise .45s cubic-bezier(.2,.7,.2,1) both; }
        @keyframes atWide { from { transform: scaleX(0);} to { transform: scaleX(1);} }
        .at-wide { transform-origin: left; animation: atWide .55s cubic-bezier(.2,.7,.2,1) both; }
        @keyframes atLive { 0%,100% { box-shadow: 0 0 0 0 rgba(200,236,63,.45);} 50% { box-shadow: 0 0 0 10px rgba(200,236,63,0);} }
        .at-live { animation: atLive 2.4s ease-out infinite; }
        .at-scroll::-webkit-scrollbar { width: 9px; height: 9px; }
        .at-scroll::-webkit-scrollbar-track { background: #e2edf0; border-radius: 9px; }
        .at-scroll::-webkit-scrollbar-thumb { background: #1d6247; border-radius: 9px; border: 2px solid #e2edf0; }
        .at-scroll::-webkit-scrollbar-thumb:hover { background: #0a3f33; }
        .at-select { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' stroke='%230a3f33' stroke-width='2.5' viewBox='0 0 24 24'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right .7rem center; background-size: .9rem; }
        @media (prefers-reduced-motion: reduce) { .at-rise,.at-wide,.at-live { animation: none !important; } }
    </style>

    {{-- HERO --}}
    <div class="forest-hero relative overflow-hidden rounded-3xl text-white shadow-lg shadow-brand-950/25 at-rise">
        <div class="forest-dots absolute inset-0 opacity-40"></div>
        <div class="at-grid absolute inset-0 opacity-45"></div>
        <div class="absolute -top-24 -left-16 w-72 h-72 rounded-full bg-emerald-500/12 blur-3xl"></div>

        <div class="relative z-10 px-6 sm:px-8 pt-6 pb-5 flex flex-col xl:flex-row xl:items-start justify-between gap-5">
            <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/8 border border-white/15 text-[10px] font-bold uppercase tracking-[0.2em] text-signal-300">{!! $aic('lock', 'w-3 h-3') !!} Jejak Permanen</span>
                    <span class="text-[11px] font-bold text-emerald-100/55">{{ $period }}</span>
                </div>
                <h1 class="mt-3 text-2xl sm:text-3xl font-extrabold tracking-tight">Audit Trail Stok</h1>
                <p class="mt-2 text-base text-emerald-100/75 max-w-2xl">Setiap perubahan stok tercatat: siapa, kapan, kenapa, berapa. Data movement tidak diubah di halaman ini — hanya dibaca, disaring, dan diekspor.</p>

                <div class="mt-4 flex flex-wrap items-end gap-x-8 gap-y-3">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-emerald-100/55">Total Movement</p>
                        <p class="text-3xl font-extrabold text-signal-300 tabular-nums leading-none mt-1">{{ number_format($stats['total'] ?? 0, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-emerald-100/55">Net Qty</p>
                        <p class="text-xl font-extrabold tabular-nums leading-none mt-1 {{ ($stats['net_qty'] ?? 0) < 0 ? 'text-red-300' : 'text-white' }}">{{ ($stats['net_qty'] ?? 0) > 0 ? '+' : '' }}{{ number_format($stats['net_qty'] ?? 0, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-emerald-100/55">Net Nilai</p>
                        <p class="text-xl font-extrabold tabular-nums leading-none mt-1 {{ ($stats['net_value'] ?? 0) < 0 ? 'text-red-300' : 'text-white' }}">Rp {{ number_format($stats['net_value'] ?? 0, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <div class="shrink-0 flex flex-col gap-2.5">
                <div class="flex items-center gap-1 p-1 rounded-2xl bg-white/8 border border-white/15">
                    @foreach(['7' => '7h', '30' => '30h', '90' => '90h', 'all' => 'Semua'] as $k => $l)
                        <button type="button" wire:click="setPreset('{{ $k }}')" class="px-3.5 py-1.5 rounded-xl text-sm font-extrabold transition-colors {{ $preset === (string) $k ? 'bg-signal-400 text-brand-950' : 'text-emerald-100/75 hover:bg-white/10' }}">{{ $l }}</button>
                    @endforeach
                </div>
                <button type="button" wire:click="exportExcel" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-signal-400 text-brand-950 text-sm font-extrabold hover:bg-signal-500 shadow-md shadow-signal-400/20 transition-all disabled:opacity-60" wire:loading.attr="disabled" wire:target="exportExcel">
                    {!! $aic('excel', 'w-4 h-4') !!}
                    <span wire:loading.remove wire:target="exportExcel">Export Excel</span>
                    <span wire:loading wire:target="exportExcel">Menyiapkan...</span>
                </button>
                <button type="button" wire:click="exportCsv" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 border border-white/15 text-sm font-bold hover:bg-white/20 transition-colors disabled:opacity-60" wire:loading.attr="disabled" wire:target="exportCsv">
                    {!! $aic('file', 'w-4 h-4') !!} Export CSV
                </button>
            </div>
        </div>

        {{-- Pita tipe movement --}}
        <div class="relative z-10 border-t border-white/10 bg-brand-950/30 px-6 sm:px-8 py-3">
            <div class="flex flex-wrap items-stretch divide-x divide-white/10">
                <button type="button" wire:click="setTypeFilter('all')" class="flex-1 min-w-[8rem] text-left pr-4 py-1.5">
                    <span class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-[0.14em] {{ $typeFilter === 'all' ? 'text-signal-300' : 'text-emerald-100/50' }}">
                        <span class="w-1.5 h-1.5 rounded-full bg-white/40"></span> Semua Tipe
                    </span>
                    <span class="mt-0.5 block text-2xl font-extrabold tabular-nums">{{ $stats['total'] ?? 0 }}</span>
                </button>
                <button type="button" wire:click="setTypeFilter('in')" class="flex-1 min-w-[8rem] text-left px-4 py-1.5">
                    <span class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-[0.14em] {{ $typeFilter === 'in' ? 'text-signal-300' : 'text-emerald-100/50' }}">
                        <span class="w-1.5 h-1.5 rounded-full bg-signal-400"></span> Masuk
                    </span>
                    <span class="mt-0.5 block text-2xl font-extrabold tabular-nums">{{ $stats['c_in'] ?? 0 }}</span>
                    <span class="block text-[10px] text-emerald-100/45 tabular-nums">Rp {{ number_format($stats['v_in'] ?? 0, 0, ',', '.') }}</span>
                </button>
                <button type="button" wire:click="setTypeFilter('out')" class="flex-1 min-w-[8rem] text-left px-4 py-1.5">
                    <span class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-[0.14em] {{ $typeFilter === 'out' ? 'text-red-300' : 'text-emerald-100/50' }}">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Keluar
                    </span>
                    <span class="mt-0.5 block text-2xl font-extrabold tabular-nums">{{ $stats['c_out'] ?? 0 }}</span>
                    <span class="block text-[10px] text-emerald-100/45 tabular-nums">Rp {{ number_format($stats['v_out'] ?? 0, 0, ',', '.') }}</span>
                </button>
                <button type="button" wire:click="setTypeFilter('adjustment')" class="flex-1 min-w-[8rem] text-left pl-4 py-1.5">
                    <span class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-[0.14em] {{ $typeFilter === 'adjustment' ? 'text-sky-300' : 'text-emerald-100/50' }}">
                        <span class="w-1.5 h-1.5 rounded-full bg-sky-400"></span> Adjustment
                    </span>
                    <span class="mt-0.5 block text-2xl font-extrabold tabular-nums">{{ $stats['c_adj'] ?? 0 }}</span>
                    <span class="block text-[10px] text-emerald-100/45 tabular-nums">Rp {{ number_format($stats['v_adj'] ?? 0, 0, ',', '.') }}</span>
                </button>
            </div>
        </div>
    </div>

    {{-- FILTER --}}
    <div class="sticky top-24 z-20 rounded-[1.75rem] bg-white/85 backdrop-blur-xl border border-ice-200 shadow-xl shadow-brand-950/5 p-4 at-rise">
        <div class="flex flex-col lg:flex-row gap-3 lg:items-center">
            <div class="relative flex-1 min-w-0">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-brand-400">{!! $aic('search', 'w-4 h-4') !!}</span>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari catatan, bahan, referensi, sub-tipe, atau batch..."
                       class="w-full pl-11 pr-4 py-3 text-base border border-ice-300 rounded-2xl bg-ice-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25 focus:border-brand-500 transition-all">
            </div>
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <select wire:model.live="materialFilter" class="at-select pl-3.5 pr-9 py-2.5 text-xs font-bold text-brand-800 border border-ice-300 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25 cursor-pointer max-w-[13rem]">
                    <option value="all">Semua bahan</option>
                    @foreach($materials as $m)
                        <option value="{{ $m['id'] }}">{{ $m['name'] }}</option>
                    @endforeach
                </select>
                <select wire:model.live="subTypeFilter" class="at-select pl-3.5 pr-9 py-2.5 text-xs font-bold text-brand-800 border border-ice-300 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25 cursor-pointer max-w-[13rem]">
                    <option value="all">Semua sub-tipe</option>
                    @foreach($subTypes as $st)
                        <option value="{{ $st }}">{{ $st }}</option>
                    @endforeach
                </select>
                <select wire:model.live="userFilter" class="at-select pl-3.5 pr-9 py-2.5 text-xs font-bold text-brand-800 border border-ice-300 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25 cursor-pointer max-w-[11rem]">
                    <option value="all">Semua pelaku</option>
                    @foreach($actors as $a)
                        <option value="{{ $a['id'] }}">{{ $a['name'] }}</option>
                    @endforeach
                </select>
                <select wire:model.live="sort" class="at-select pl-3.5 pr-9 py-2.5 text-xs font-bold text-brand-800 border border-ice-300 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25 cursor-pointer">
                    <option value="newest">Terbaru</option>
                    <option value="oldest">Terlama</option>
                </select>
            </div>
        </div>
        <div class="mt-3 flex flex-wrap items-center gap-2">
            <label class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Dari</label>
            <input type="date" wire:model.live="dateFrom" class="px-3 py-2 text-xs font-bold border border-ice-300 rounded-xl bg-white text-brand-800 focus:outline-none focus:ring-2 focus:ring-brand-500/25">
            <label class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Sampai</label>
            <input type="date" wire:model.live="dateTo" class="px-3 py-2 text-xs font-bold border border-ice-300 rounded-xl bg-white text-brand-800 focus:outline-none focus:ring-2 focus:ring-brand-500/25">
            @if($hasFilter)
                <button type="button" wire:click="clearFilters" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-red-50 text-red-600 text-xs font-extrabold hover:bg-red-100 transition-colors">{!! $aic('undo', 'w-3.5 h-3.5') !!} Reset semua saringan</button>
            @endif
            <span class="ml-auto text-xs text-gray-500 tabular-nums"><b class="text-brand-950">{{ $paginator?->total() ?? 0 }}</b> movement cocok</span>
        </div>
    </div>

    {{-- JEJAK + RAIL --}}
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-4 items-start">
        <div class="xl:col-span-8 rounded-3xl bg-white border border-ice-200 shadow-sm overflow-hidden at-rise min-w-0">
            <div class="px-6 py-3.5 bg-ice-50 border-b border-ice-200 flex flex-wrap items-center justify-between gap-2">
                <div class="flex items-center gap-3">
                    <span class="w-9 h-9 rounded-xl bg-brand-600 text-signal-300 flex items-center justify-center">{!! $aic('clock', 'w-5 h-5') !!}</span>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-gray-400">Jejak Kronologis</p>
                        <h2 class="text-base font-extrabold text-brand-950 leading-tight">Riwayat Movement</h2>
                    </div>
                </div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-ice-200 text-brand-800 text-[11px] font-bold">{!! $aic('shield', 'w-3.5 h-3.5') !!} Read-only</span>
            </div>

            <div wire:loading.class="opacity-40 pointer-events-none" class="transition-opacity duration-200">
                @if(count($rows))
                    <div class="divide-y divide-ice-100">
                        @foreach($rows as $r)
                            @php
                                $tm = $types[$r['type']] ?? [ucfirst($r['type']), 'bg-ice-200 text-brand-800', 'bg-brand-500', 'box'];
                                $showDay = $r['day'] !== $lastDay;
                                $lastDay = $r['day'];
                            @endphp
                            {!! $showDay ? '<div class="px-6 py-2 bg-brand-50/70 border-y border-ice-200 flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-brand-600"></span><span class="text-[11px] font-extrabold uppercase tracking-wider text-brand-800">' . e($r['day_label']) . '</span></div>' : '' !!}
                            <div class="px-6 py-3.5 flex flex-col lg:flex-row lg:items-center gap-3 hover:bg-ice-50/60 transition-colors border-l-4 {{ $r['type'] === 'out' ? 'border-l-red-500' : ($r['type'] === 'in' ? 'border-l-signal-400' : 'border-l-sky-400') }}">
                                <div class="shrink-0 flex items-center gap-3 lg:w-24">
                                    <span class="w-9 h-9 rounded-xl flex items-center justify-center {{ $tm[1] }}">{!! $aic($tm[3], 'w-4 h-4') !!}</span>
                                    <span class="text-sm font-extrabold text-brand-900 tabular-nums">{{ $r['time'] }}</span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="text-sm font-extrabold text-brand-950">{{ $r['material'] }}</span>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase {{ $tm[1] }}">{{ $tm[0] }}</span>
                                        <span class="px-2 py-0.5 rounded bg-ice-100 text-brand-700 text-[10px] font-bold">{{ $r['sub_type'] }}</span>
                                    </div>
                                    <p class="mt-0.5 text-[11px] text-gray-500 flex flex-wrap items-center gap-x-3 gap-y-0.5">
                                        <span class="inline-flex items-center gap-1">{!! $aic('user', 'w-3 h-3') !!} {{ $r['user'] }}</span>
                                        <span class="inline-flex items-center gap-1">{!! $aic('link', 'w-3 h-3') !!} {{ $r['ref_label'] }}</span>
                                        @if($r['batch'])<span class="font-mono">{{ $r['batch'] }}</span>@endif
                                    </p>
                                    @if($r['notes'])<p class="mt-1 text-xs text-gray-600 line-clamp-2">{{ $r['notes'] }}</p>@endif
                                </div>
                                <div class="shrink-0 flex items-center gap-4">
                                    <div class="text-right">
                                        <p class="text-base font-extrabold tabular-nums leading-none {{ $r['signed'] < 0 ? 'text-red-600' : ($r['type'] === 'adjustment' ? 'text-sky-600' : 'text-signal-600') }}">{{ $r['signed'] > 0 ? '+' : '' }}{{ number_format($r['signed'], 2) }}</p>
                                        <p class="text-[10px] text-gray-400 mt-0.5">{{ $r['unit'] }}</p>
                                        <p class="text-[11px] font-bold text-brand-950 tabular-nums mt-0.5">Rp {{ number_format($r['value'], 0, ',', '.') }}</p>
                                    </div>
                                    <button type="button" wire:click="openDetail({{ $r['id'] }})" title="Lihat rincian" class="w-9 h-9 flex items-center justify-center rounded-xl bg-ice-100 text-brand-700 hover:bg-ice-200 transition-colors">{!! $aic('eye', 'w-4 h-4') !!}</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
                {!! count($rows) ? '' : $empty('Tidak ada movement yang cocok dengan saringan ini.') !!}
            </div>

            @if($paginator && $paginator->hasPages())
                @php $cur = $paginator->currentPage(); $last = $paginator->lastPage(); $from = max(1, $cur - 2); $to = min($last, $cur + 2); @endphp
                <div class="px-6 py-3.5 border-t border-ice-200 bg-ice-50 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <p class="text-xs text-gray-500 tabular-nums">Halaman <b class="text-brand-950">{{ $cur }}</b> dari <b class="text-brand-950">{{ $last }}</b></p>
                    <div class="flex items-center gap-1.5">
                        <button type="button" wire:click="previousPage" @disabled($paginator->onFirstPage()) class="w-9 h-9 flex items-center justify-center rounded-xl bg-white border border-ice-300 text-brand-700 hover:bg-ice-100 transition-colors disabled:opacity-40">{!! $aic('left', 'w-4 h-4') !!}</button>
                        @for($p = $from; $p <= $to; $p++)
                            <button type="button" wire:click="gotoPage({{ $p }})" class="w-9 h-9 rounded-xl text-xs font-extrabold tabular-nums transition-colors {{ $p === $cur ? 'bg-brand-600 text-signal-300 shadow-sm' : 'bg-white border border-ice-300 text-brand-700 hover:bg-ice-100' }}">{{ $p }}</button>
                        @endfor
                        <button type="button" wire:click="nextPage" @disabled(!$paginator->hasMorePages()) class="w-9 h-9 flex items-center justify-center rounded-xl bg-white border border-ice-300 text-brand-700 hover:bg-ice-100 transition-colors disabled:opacity-40">{!! $aic('right', 'w-4 h-4') !!}</button>
                    </div>
                </div>
            @endif
        </div>

        {{-- RAIL --}}
        <div class="xl:col-span-4 space-y-4">
            <div class="forest-hero relative overflow-hidden rounded-3xl p-6 text-white shadow-lg shadow-brand-950/20 at-rise">
                <div class="forest-dots absolute inset-0 opacity-40"></div>
                <div class="relative">
                    <p class="text-xs font-bold uppercase tracking-[0.22em] text-signal-300">Ringkasan Periode</p>
                    <div class="mt-4 space-y-3">
                        @foreach([['in', 'Masuk', 'signal'], ['out', 'Keluar', 'red'], ['adjustment', 'Adjustment', 'sky']] as $t)
                            @php
                                $c = $stats['c_' . ($t[0] === 'adjustment' ? 'adj' : $t[0])] ?? 0;
                                $q = $stats['q_' . ($t[0] === 'adjustment' ? 'adj' : $t[0])] ?? 0;
                                $v = $stats['v_' . ($t[0] === 'adjustment' ? 'adj' : $t[0])] ?? 0;
                                $dot = $t[2] === 'signal' ? 'bg-signal-400' : ($t[2] === 'red' ? 'bg-red-500' : 'bg-sky-400');
                            @endphp
                            <div class="rounded-xl bg-white/8 border border-white/15 px-4 py-3">
                                <div class="flex items-center justify-between gap-3">
                                    <span class="inline-flex items-center gap-2 text-sm font-bold"><span class="w-2.5 h-2.5 rounded-sm {{ $dot }}"></span> {{ $t[1] }}</span>
                                    <span class="text-lg font-extrabold tabular-nums">{{ $c }}</span>
                                </div>
                                <div class="mt-1 flex items-center justify-between text-[11px] text-emerald-100/60 tabular-nums">
                                    <span>qty {{ number_format($q, 2) }}</span>
                                    <span>Rp {{ number_format($v, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <p class="mt-4 text-[11px] text-emerald-100/55">Angka mengikuti saringan aktif. Export memakai saringan yang sama.</p>
                </div>
            </div>

            <div class="rounded-3xl bg-white border border-ice-200 shadow-sm overflow-hidden at-rise">
                <div class="px-5 py-3 bg-ice-50 border-b border-ice-200 flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-brand-600 text-signal-300 flex items-center justify-center">{!! $aic('chart', 'w-4 h-4') !!}</span>
                    <p class="text-sm font-extrabold text-brand-950">Bahan Paling Bergerak</p>
                </div>
                @if(count($topMaterials))
                    <div class="p-5 space-y-3">
                        @foreach($topMaterials as $i => $t)
                            <div>
                                <div class="flex items-center justify-between gap-3 mb-1">
                                    <span class="text-xs font-bold text-brand-950 truncate">{{ $t['name'] }}</span>
                                    <span class="shrink-0 text-xs font-extrabold text-brand-700 tabular-nums">{{ $t['count'] }}×</span>
                                </div>
                                <div class="h-2 rounded-full bg-ice-200 overflow-hidden">
                                    <div class="at-wide h-full rounded-full bg-gradient-to-r from-brand-600 to-signal-400" style="width: {{ $t['bar'] }}%; animation-delay: {{ $i * 60 }}ms"></div>
                                </div>
                                <p class="mt-0.5 text-[10px] text-gray-400 tabular-nums">qty {{ number_format($t['qty'], 2) }} · Rp {{ number_format($t['value'], 0, ',', '.') }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
                {!! count($topMaterials) ? '' : $empty('Belum ada movement pada saringan ini.') !!}
            </div>

            <div class="rounded-3xl border-2 {{ ($integrity['total'] ?? 0) > 0 ? 'border-amber-300 bg-amber-50/60' : 'border-signal-300 bg-signal-100/40' }} shadow-sm overflow-hidden at-rise">
                <div class="px-5 py-3 border-b {{ ($integrity['total'] ?? 0) > 0 ? 'border-amber-200' : 'border-signal-300' }} flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg flex items-center justify-center {{ ($integrity['total'] ?? 0) > 0 ? 'bg-amber-500 text-white' : 'bg-signal-400 text-brand-950' }}">{!! $aic(($integrity['total'] ?? 0) > 0 ? 'alert' : 'check', 'w-4 h-4') !!}</span>
                    <p class="text-sm font-extrabold {{ ($integrity['total'] ?? 0) > 0 ? 'text-amber-900' : 'text-brand-950' }}">Kelengkapan Jejak</p>
                </div>
                <div class="p-5 space-y-2 text-xs">
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-gray-600">Tanpa pelaku tercatat</span>
                        <span class="font-extrabold tabular-nums {{ ($integrity['no_user'] ?? 0) > 0 ? 'text-amber-700' : 'text-signal-700' }}">{{ $integrity['no_user'] ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-gray-600">Tanpa dokumen referensi</span>
                        <span class="font-extrabold tabular-nums {{ ($integrity['no_ref'] ?? 0) > 0 ? 'text-amber-700' : 'text-signal-700' }}">{{ $integrity['no_ref'] ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-gray-600">Bernilai Rp 0 padahal ada qty</span>
                        <span class="font-extrabold tabular-nums {{ ($integrity['no_value'] ?? 0) > 0 ? 'text-amber-700' : 'text-signal-700' }}">{{ $integrity['no_value'] ?? 0 }}</span>
                    </div>
                    <p class="pt-2 text-[11px] {{ ($integrity['total'] ?? 0) > 0 ? 'text-amber-800' : 'text-signal-700' }}">
                        {{ ($integrity['total'] ?? 0) > 0 ? 'Periksa baris tersebut agar jejak audit layak dipertanggungjawabkan.' : 'Semua movement punya pelaku, referensi, dan nilai. Jejak audit lengkap.' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- DETAIL --}}
    @if($detail)
        <div class="fixed inset-0 z-[75] flex items-end sm:items-center justify-center p-0 sm:p-4 bg-brand-950/60 backdrop-blur-sm" wire:click.self="closeDetail">
            <div class="w-full sm:max-w-2xl max-h-[90vh] flex flex-col overflow-hidden rounded-t-3xl sm:rounded-3xl bg-ice-50 shadow-2xl">
                @php $tm = $types[$detail['type']] ?? [ucfirst($detail['type']), 'bg-ice-200 text-brand-800', 'bg-brand-500', 'box']; @endphp
                <div class="forest-hero relative overflow-hidden px-6 py-5 flex items-start justify-between gap-4 shrink-0">
                    <div class="forest-dots absolute inset-0 opacity-40"></div>
                    <div class="relative z-10 min-w-0">
                        <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-signal-300">Rincian Movement #{{ $detail['id'] }}</p>
                        <h3 class="mt-1 text-xl font-extrabold text-white tracking-tight">{{ $detail['material'] }}</h3>
                        <div class="mt-2 flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold {{ $tm[1] }}">{!! $aic($tm[3], 'w-3.5 h-3.5') !!} {{ $tm[0] }}</span>
                            <span class="px-2.5 py-1 rounded-full bg-white/10 border border-white/15 text-[11px] font-bold text-white">{{ $detail['sub_type'] }}</span>
                        </div>
                    </div>
                    <button type="button" wire:click="closeDetail" class="relative z-10 shrink-0 w-9 h-9 flex items-center justify-center rounded-xl bg-white/10 text-white hover:bg-white/20 transition-colors">{!! $aic('x', 'w-5 h-5') !!}</button>
                </div>

                <div class="at-scroll flex-1 overflow-y-auto p-6 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="rounded-2xl bg-white border border-ice-200 p-4 text-center">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Perubahan Qty</p>
                            <p class="mt-1 text-2xl font-extrabold tabular-nums {{ $detail['signed'] < 0 ? 'text-red-600' : ($detail['type'] === 'adjustment' ? 'text-sky-600' : 'text-signal-600') }}">{{ $detail['signed'] > 0 ? '+' : '' }}{{ number_format($detail['signed'], 2) }}</p>
                            <p class="text-[11px] text-gray-400">{{ $detail['unit'] }}</p>
                        </div>
                        <div class="rounded-2xl bg-white border border-ice-200 p-4 text-center">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Harga Satuan</p>
                            <p class="mt-1 text-xl font-extrabold text-brand-950 tabular-nums">Rp {{ number_format($detail['unit_cost'], 0, ',', '.') }}</p>
                        </div>
                        <div class="rounded-2xl bg-white border border-ice-200 p-4 text-center">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Nilai Total</p>
                            <p class="mt-1 text-xl font-extrabold text-brand-950 tabular-nums">Rp {{ number_format($detail['value'], 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-white border border-ice-200 p-5 space-y-2.5 text-sm">
                        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-gray-400">Jejak</p>
                        <div class="flex justify-between gap-3"><span class="text-gray-500">Waktu movement</span><span class="font-bold tabular-nums">{{ $detail['stamp'] }}</span></div>
                        <div class="flex justify-between gap-3"><span class="text-gray-500">Dicatat sistem</span><span class="font-bold tabular-nums">{{ $detail['raw']['created_at'] }}</span></div>
                        <div class="flex justify-between gap-3"><span class="text-gray-500">Pelaku</span><span class="font-bold">{{ $detail['user'] }}</span></div>
                        <div class="flex justify-between gap-3"><span class="text-gray-500">Referensi</span><span class="font-bold">{{ $detail['ref_label'] }}</span></div>
                        @if($detail['batch'])<div class="flex justify-between gap-3"><span class="text-gray-500">Batch</span><span class="font-bold font-mono">{{ $detail['batch'] }}</span></div>@endif
                        <div class="flex justify-between gap-3"><span class="text-gray-500">ID bahan</span><span class="font-bold tabular-nums">#{{ $detail['raw']['material_id'] }}</span></div>
                    </div>

                    @if($detail['notes'])
                        <div class="rounded-2xl bg-white border border-ice-200 p-5">
                            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-gray-400">Catatan Lengkap</p>
                            <p class="mt-2 text-sm text-gray-700 whitespace-pre-line leading-relaxed">{{ $detail['notes'] }}</p>
                        </div>
                    @endif
                </div>

                <div class="shrink-0 px-6 py-4 border-t border-ice-200 bg-white flex flex-wrap items-center justify-end gap-2.5">
                    <button type="button" onclick="copyText('Movement #{{ $detail['id'] }} | {{ $detail['stamp'] }} | {{ $detail['material'] }} | {{ $tm[0] }} {{ number_format($detail['signed'], 2) }} {{ $detail['unit'] }} | {{ $detail['ref_label'] }} | {{ $detail['user'] }}', this)"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-ice-100 text-brand-800 text-sm font-bold hover:bg-ice-200 transition-colors">{!! $aic('copy', 'w-4 h-4') !!} Salin jejak</button>
                    @if($detail['link'] === 'order')
                        <a href="{{ route('admin.orders.show', $detail['ref_id']) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-brand-600 text-white text-sm font-bold hover:bg-brand-700 transition-colors">{!! $aic('link', 'w-4 h-4') !!} Buka pesanan</a>
                    @endif
                    @if($detail['link'] === 'receipt')
                        <a href="{{ route('admin.purchase-receipts.pdf', $detail['ref_id']) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-brand-600 text-white text-sm font-bold hover:bg-brand-700 transition-colors">{!! $aic('file', 'w-4 h-4') !!} Buka BPB</a>
                    @endif
                    <button type="button" wire:click="closeDetail" class="px-5 py-2.5 rounded-xl bg-ice-200 text-brand-800 text-sm font-bold hover:bg-ice-300 transition-colors">Tutup</button>
                </div>
            </div>
        </div>
    @endif
</div>