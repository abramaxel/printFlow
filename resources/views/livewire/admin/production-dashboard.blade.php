@php
    $D = [
        'printer' => 'M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z',
        'bolt'    => 'M13 10V3L4 14h7v7l9-11h-7z',
        'shield'  => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
        'truck'   => 'M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0 2 2 0 00-4 0zm10 0a2 2 0 104 0 2 2 0 00-4 0z',
        'check'   => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        'alert'   => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
        'clock'   => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        'box'     => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
        'send'    => 'M12 19l9 2-9-18-9 18 9-2zm0 0v-8',
        'flask'   => 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z',
        'clipboard'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
        'chart'   => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
        'search'  => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z',
        'undo'    => 'M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6',
        'arrow'   => 'M14 5l7 7m0 0l-7 7m7-7H3',
        'flag'    => 'M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9',
        'copy'    => 'M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z',
    ];
    $dic = fn($k, $c = 'w-5 h-5') => '<svg class="' . $c . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="' . ($D[$k] ?? $D['box']) . '"/></svg>';
    $empty = fn($t) => '<p class="px-6 py-10 text-center text-sm font-bold text-gray-400">' . $t . '</p>';

    $TONE = [
        'signal' => ['bg-signal-100 text-signal-700', 'bg-signal-400'],
        'amber'  => ['bg-amber-100 text-amber-800', 'bg-amber-400'],
        'violet' => ['bg-violet-100 text-violet-700', 'bg-violet-400'],
        'sky'    => ['bg-sky-100 text-sky-700', 'bg-sky-400'],
    ];
@endphp

<div class="w-full space-y-4">
    <style>
        .pd-grid { background-image: linear-gradient(rgba(200,236,63,.08) 1px, transparent 1px), linear-gradient(90deg, rgba(200,236,63,.08) 1px, transparent 1px); background-size: 30px 30px; }
        .pd-dots { background-image: radial-gradient(rgba(10,63,51,.10) 1px, transparent 1px); background-size: 18px 18px; }
        .pd-flow { background-image: repeating-linear-gradient(45deg, rgba(255,255,255,.28) 0 6px, transparent 6px 14px); }
        @keyframes pdRise { from { opacity:0; transform: translateY(10px);} to { opacity:1; transform:none;} }
        .pd-rise { animation: pdRise .4s cubic-bezier(.2,.7,.2,1) both; }
        @keyframes pdWide { from { transform: scaleX(0);} to { transform: scaleX(1);} }
        .pd-wide { transform-origin: left; animation: pdWide .6s cubic-bezier(.2,.7,.2,1) both; }
        @keyframes pdMove { 0% { background-position: 0 0; } 100% { background-position: 28px 0; } }
        .pd-move { animation: pdMove 1.3s linear infinite; }
        @keyframes pdPulse { 0%,100% { box-shadow: 0 0 0 0 rgba(220,38,38,.4);} 50% { box-shadow: 0 0 0 9px rgba(220,38,38,0);} }
        .pd-pulse { animation: pdPulse 2.2s ease-out infinite; }
        .pd-scroll::-webkit-scrollbar { width: 10px; height: 10px; }
        .pd-scroll::-webkit-scrollbar-track { background: #e2edf0; border-radius: 10px; }
        .pd-scroll::-webkit-scrollbar-thumb { background: #1d6247; border-radius: 10px; border: 2px solid #e2edf0; }
        .pd-scroll::-webkit-scrollbar-thumb:hover { background: #0a3f33; }
        .pd-field { transition: box-shadow .18s ease, border-color .18s ease, background-color .18s ease; }
        @media (prefers-reduced-motion: reduce) { .pd-rise,.pd-wide,.pd-move,.pd-pulse { animation: none !important; } }
    </style>

    <div class="forest-hero relative overflow-hidden rounded-3xl text-white shadow-lg shadow-brand-950/25 pd-rise">
        <div class="forest-dots absolute inset-0 opacity-40"></div>
        <div class="pd-grid absolute inset-0 opacity-40"></div>
        <div class="absolute -top-24 -left-16 w-72 h-72 rounded-full bg-emerald-500/12 blur-3xl"></div>
        <div class="absolute -bottom-24 right-0 w-64 h-64 rounded-full bg-signal-400/10 blur-3xl"></div>

        <div class="relative z-10 px-6 sm:px-8 pt-6 pb-5 flex flex-col xl:flex-row xl:items-start justify-between gap-5">
            <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/8 border border-white/15 text-[10px] font-bold uppercase tracking-[0.2em] text-signal-300">{!! $dic('printer', 'w-3 h-3') !!} Lantai Produksi</span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold uppercase tracking-wider {{ $stats['critical'] > 0 ? 'bg-red-600 pd-pulse' : 'bg-signal-400 text-brand-950' }}">
                        {!! $dic($stats['critical'] > 0 ? 'alert' : 'check', 'w-3.5 h-3.5') !!}
                        {{ $stats['critical'] > 0 ? $stats['critical'] . ' bahan kritis' : 'Bahan aman' }}
                    </span>
                </div>
                <h1 class="mt-3 text-2xl sm:text-3xl font-extrabold tracking-tight">Dashboard Produksi</h1>
                <p class="mt-2 text-base text-emerald-100/75 max-w-2xl">Pantau antrean lantai produksi, QC, dan kesiapan bahan. Klik angka tahap untuk menyaring antrean di bawah.</p>

                <div class="mt-4 flex flex-wrap items-end gap-x-8 gap-y-3">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-emerald-100/55">Pesanan dalam Pipeline</p>
                        <p class="text-3xl font-extrabold text-signal-300 tabular-nums leading-none mt-1">{{ $stats['pipeline'] }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-emerald-100/55">Selesai Hari Ini</p>
                        <p class="text-xl font-extrabold tabular-nums leading-none mt-1">{{ $stats['done_today'] }}</p>
                        <p class="text-[11px] text-emerald-100/55 mt-1">{{ $today }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-emerald-100/55">Deadline Mendesak</p>
                        <p class="text-xl font-extrabold tabular-nums leading-none mt-1 {{ ($stats['late'] + $stats['soon']) > 0 ? 'text-amber-300' : 'text-white' }}">{{ $stats['late'] + $stats['soon'] }}</p>
                        <p class="text-[11px] text-emerald-100/55 mt-1 tabular-nums">{{ $stats['late'] }} lewat · {{ $stats['soon'] }} ≤2 hari</p>
                    </div>
                </div>
            </div>

            <div class="shrink-0 flex flex-col gap-2.5">
                <a href="{{ route('admin.production') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-signal-400 text-brand-950 text-sm font-extrabold hover:bg-signal-500 shadow-md shadow-signal-400/20 transition-all">{!! $dic('printer', 'w-4 h-4') !!} Buka Antrian Produksi</a>
                <a href="{{ route('admin.production.requests') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 border border-white/15 text-sm font-bold hover:bg-white/20 transition-colors">{!! $dic('send', 'w-4 h-4') !!} Ajukan Pembelian Bahan</a>
                <button type="button" wire:click="$refresh" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 border border-white/15 text-sm font-bold hover:bg-white/20 transition-colors disabled:opacity-60" wire:loading.attr="disabled">
                    {!! $dic('undo', 'w-4 h-4') !!}
                    <span wire:loading.remove>Segarkan Data</span>
                    <span wire:loading>Memuat...</span>
                </button>
            </div>
        </div>

        <div class="relative z-10 border-t border-white/10 bg-brand-950/30 px-6 sm:px-8 py-3">
            <div class="flex flex-wrap items-stretch divide-x divide-white/10">
                @foreach($segments as $i => $sg)
                    <button type="button" wire:click="setStage('{{ $sg['key'] }}')"
                            class="flex-1 min-w-[8rem] text-left py-1.5 hover:opacity-80 transition-opacity {{ $i === 0 ? 'pr-4' : 'px-4' }}">
                        <span class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-[0.14em] {{ $stage === $sg['key'] ? 'text-signal-300' : 'text-emerald-100/50' }}">
                            <span class="w-2 h-2 rounded-sm" style="background: {{ $sg['hue'] }}"></span> {{ $sg['label'] }}
                        </span>
                        <span class="mt-0.5 block text-2xl font-extrabold tabular-nums">{{ $sg['count'] }}</span>
                    </button>
                @endforeach
                <div class="flex-1 min-w-[8rem] text-left pl-4 py-1.5">
                    <span class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-[0.14em] text-emerald-100/50">{!! $dic('check', 'w-3.5 h-3.5') !!} Selesai Hari Ini</span>
                    <span class="mt-0.5 block text-2xl font-extrabold tabular-nums">{{ $stats['done_today'] }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="relative overflow-hidden rounded-3xl bg-white border border-ice-200 shadow-sm pd-rise">
        <div class="pd-dots pointer-events-none absolute inset-0 opacity-30"></div>
        <div class="relative px-6 py-4">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-gray-400">Alur Antrean Aktif</p>
                    <h2 class="text-base font-extrabold text-brand-950 leading-tight">Sebaran tahap pengerjaan</h2>
                </div>
                <span class="text-xs text-gray-500 tabular-nums"><b class="text-brand-950">{{ $stats['pipeline'] }}</b> pesanan dalam pipeline</span>
            </div>

            @if($stats['pipeline'] > 0)
                <div class="flex h-4 rounded-full overflow-hidden bg-ice-200">
                    @foreach($segments as $i => $sg)
                        @if($sg['count'] > 0)
                            <div class="pd-wide relative h-full {{ $sg['key'] === 'processing' ? 'pd-flow pd-move' : '' }}"
                                 style="width: {{ $sg['share'] }}%; background: {{ $sg['hue'] }}; animation-delay: {{ $i * 80 }}ms"
                                 title="{{ $sg['label'] }}: {{ $sg['count'] }} pesanan ({{ $sg['share'] }}%)"></div>
                        @endif
                    @endforeach
                </div>
                <div class="mt-3 flex flex-wrap gap-x-5 gap-y-2">
                    @foreach($segments as $sg)
                        <button type="button" wire:click="setStage('{{ $sg['key'] }}')"
                                class="inline-flex items-center gap-2 text-xs font-bold transition-opacity hover:opacity-70 {{ $stage !== 'all' && $stage !== $sg['key'] ? 'opacity-40' : '' }}">
                            <span class="w-2.5 h-2.5 rounded-sm" style="background: {{ $sg['hue'] }}"></span>
                            <span class="text-brand-900">{{ $sg['label'] }}</span>
                            <span class="text-gray-400 tabular-nums">{{ $sg['count'] }} · {{ $sg['share'] }}%</span>
                        </button>
                    @endforeach
                </div>
            @else
                <div class="rounded-2xl bg-signal-100 border border-signal-300 px-5 py-4 flex items-center gap-3">
                    <span class="shrink-0 w-10 h-10 rounded-xl bg-signal-400 text-brand-950 flex items-center justify-center">{!! $dic('check', 'w-5 h-5') !!}</span>
                    <p class="text-sm font-bold text-brand-900">Tidak ada pesanan aktif di lantai produksi. Semua SPK sudah tuntas atau belum masuk tahap cetak.</p>
                </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-4 items-start">
        <div class="xl:col-span-8 rounded-3xl bg-white border border-ice-200 shadow-sm overflow-hidden pd-rise min-w-0">
            <div class="px-6 py-3.5 bg-ice-50 border-b border-ice-200">
                <div class="flex flex-col lg:flex-row gap-3 lg:items-center">
                    <div class="flex items-center gap-3 min-w-0 lg:flex-1">
                        <span class="shrink-0 w-9 h-9 rounded-xl bg-brand-600 text-signal-300 flex items-center justify-center">{!! $dic('clipboard', 'w-5 h-5') !!}</span>
                        <div class="min-w-0">
                            <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-gray-400">Antrean Kerja</p>
                            <h2 class="text-base font-extrabold text-brand-950 leading-tight truncate">{{ $stage === 'all' ? 'Semua Tahap' : ($stagesMeta[$stage][0] ?? 'Tahap') }}</h2>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-brand-400">{!! $dic('search', 'w-3.5 h-3.5') !!}</span>
                            <input type="text" wire:model.live.debounce.250ms="search" placeholder="Cari invoice / pelanggan..."
                                   class="pd-field w-full sm:w-56 pl-9 pr-3 py-2 text-sm border border-ice-300 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25 focus:border-brand-500">
                        </div>
                        @if($hasFilter)
                            <button type="button" wire:click="clearFilters" title="Reset saringan" class="w-9 h-9 flex items-center justify-center rounded-xl bg-red-50 text-red-600 hover:bg-red-100 transition-colors">{!! $dic('undo', 'w-4 h-4') !!}</button>
                        @endif
                        <a href="{{ route('admin.production') }}" class="hidden sm:inline-flex items-center gap-1 text-xs font-extrabold text-brand-700 hover:text-brand-600 transition-colors whitespace-nowrap">Lihat semua {!! $dic('arrow', 'w-3.5 h-3.5') !!}</a>
                    </div>
                </div>
            </div>

            <div wire:loading.class="opacity-40 pointer-events-none" class="transition-opacity duration-200">
                @if(count($queue))
                    <div class="pd-scroll overflow-x-auto">
                        <table class="w-full text-left min-w-[46rem]">
                            <thead>
                                <tr class="text-[10px] uppercase tracking-wider text-gray-400 border-b border-ice-200">
                                    <th class="px-6 py-2.5 font-bold">Invoice</th>
                                    <th class="px-3 py-2.5 font-bold">Pelanggan</th>
                                    <th class="px-3 py-2.5 font-bold">Layanan</th>
                                    <th class="px-3 py-2.5 font-bold">Tahap</th>
                                    <th class="px-3 py-2.5 font-bold text-right">Deadline</th>
                                    <th class="px-6 py-2.5 font-bold text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-ice-100">
                                @foreach($queue as $q)
                                    <tr class="hover:bg-ice-50/70 transition-colors border-l-4 {{ $q['late'] ? 'border-l-red-500' : ($q['soon'] ? 'border-l-amber-400' : 'border-l-transparent') }}">
                                        <td class="px-6 py-3">
                                            <div class="flex items-center gap-1.5">
                                                <p class="text-sm font-extrabold text-brand-950 leading-tight">{{ $q['invoice'] }}</p>
                                                <button type="button" onclick="copyText('{{ $q['invoice'] }}', this)" title="Salin invoice" class="w-6 h-6 flex items-center justify-center rounded text-brand-400 hover:bg-brand-600 hover:text-signal-300 transition-colors">{!! $dic('copy', 'w-3 h-3') !!}</button>
                                            </div>
                                            <p class="text-[11px] text-gray-400 tabular-nums mt-0.5">{{ $q['created'] }} · {{ $q['items'] }} item</p>
                                        </td>
                                        <td class="px-3 py-3">
                                            <p class="text-sm font-bold text-brand-900 truncate max-w-[11rem]">{{ $q['customer'] }}</p>
                                            @if($q['priority'] === 'high')
                                                <span class="mt-0.5 inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-violet-100 text-violet-700 text-[9px] font-extrabold uppercase">{!! $dic('flag', 'w-2.5 h-2.5') !!} Prioritas</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-3"><p class="text-[12px] text-gray-600 truncate max-w-[12rem]">{{ $q['service'] }}</p></td>
                                        <td class="px-3 py-3">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase {{ $TONE[$q['tone']][0] }}">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $TONE[$q['tone']][1] }}"></span> {{ $q['stage'] }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-3 text-right">
                                            <p class="text-[12px] font-bold tabular-nums {{ $q['late'] ? 'text-red-600' : ($q['soon'] ? 'text-amber-600' : 'text-brand-900') }}">{{ $q['deadline'] ?? '—' }}</p>
                                            @if($q['days'] !== null)
                                                <p class="text-[10px] font-bold tabular-nums {{ $q['late'] ? 'text-red-600' : ($q['soon'] ? 'text-amber-600' : 'text-gray-400') }}">
                                                    {{ $q['late'] ? 'lewat ' . abs($q['days']) . ' hari' : ($q['days'] === 0 ? 'hari ini' : $q['days'] . ' hari lagi') }}
                                                </p>
                                            @endif
                                        </td>
                                        <td class="px-6 py-3 text-right">
                                            <a href="{{ route('admin.orders.show', $q['id']) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-brand-600 text-white text-[11px] font-extrabold hover:bg-brand-700 transition-colors">Detail {!! $dic('arrow', 'w-3 h-3') !!}</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-3 bg-ice-50 border-t border-ice-200 flex flex-wrap items-center justify-between gap-2">
                        <p class="text-[11px] text-gray-500 tabular-nums">Menampilkan {{ count($queue) }} dari <b class="text-brand-950">{{ $queueTotal }}</b> pesanan cocok</p>
                        <a href="{{ route('admin.production') }}" class="inline-flex items-center gap-1 text-[11px] font-extrabold text-brand-700 hover:text-brand-600 transition-colors">Kerjakan di antrian produksi {!! $dic('arrow', 'w-3 h-3') !!}</a>
                    </div>
                @endif
                {!! count($queue) ? '' : $empty('Tidak ada pesanan pada saringan ini.') !!}
            </div>
        </div>

        <div class="xl:col-span-4 space-y-4">
            <div class="rounded-3xl bg-white border-2 {{ $stats['critical'] > 0 ? 'border-red-300' : 'border-signal-300' }} shadow-sm overflow-hidden pd-rise">
                <div class="px-5 py-3.5 border-b {{ $stats['critical'] > 0 ? 'border-red-200 bg-red-50' : 'border-signal-300 bg-signal-100/50' }} flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="shrink-0 w-8 h-8 rounded-lg flex items-center justify-center {{ $stats['critical'] > 0 ? 'bg-red-600 text-white' : 'bg-signal-400 text-brand-950' }}">{!! $dic($stats['critical'] > 0 ? 'alert' : 'check', 'w-4 h-4') !!}</span>
                        <div class="min-w-0">
                            <p class="text-[10px] font-bold uppercase tracking-[0.2em] {{ $stats['critical'] > 0 ? 'text-red-500' : 'text-signal-700' }}">Kesiapan Bahan</p>
                            <p class="text-sm font-extrabold text-brand-950 leading-tight">Bahan Kritis</p>
                        </div>
                    </div>
                    <span class="shrink-0 px-2.5 py-1 rounded-full text-xs font-black tabular-nums {{ $stats['critical'] > 0 ? 'bg-red-600 text-white' : 'bg-signal-400 text-brand-950' }}">{{ $stats['critical'] }}</span>
                </div>

                @if(count($critical))
                    <div class="divide-y divide-ice-100">
                        @foreach($critical as $c)
                            <div class="px-5 py-3 {{ $c['empty'] ? 'bg-red-50/40' : '' }}">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="text-sm font-extrabold text-brand-950 leading-tight truncate">{{ $c['name'] }}</p>
                                        <p class="text-[11px] text-gray-500 tabular-nums mt-0.5">
                                            sisa <b class="{{ $c['empty'] ? 'text-red-600' : 'text-amber-600' }}">{{ number_format($c['net'], 2) }}</b>
                                            / min {{ number_format($c['min'], 2) }} {{ $c['unit'] }}
                                        </p>
                                    </div>
                                    <span class="shrink-0 px-2 py-0.5 rounded text-[9px] font-extrabold uppercase {{ $c['empty'] ? 'bg-red-600 text-white' : 'bg-amber-100 text-amber-800' }}">{{ $c['empty'] ? 'Habis' : 'Kritis' }}</span>
                                </div>
                                <div class="mt-2 h-1.5 rounded-full bg-ice-200 overflow-hidden">
                                    <div class="h-full rounded-full {{ $c['empty'] ? 'bg-red-600' : 'bg-amber-500' }}" style="width: {{ max(3, $c['coverage']) }}%"></div>
                                </div>
                                <div class="mt-2 flex items-center justify-between gap-2">
                                    <span class="text-[10px] text-gray-400 tabular-nums">lead {{ $c['lead'] }} hari</span>
                                    @if($c['pr_state'] === 'pending')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-amber-100 text-amber-800 text-[9px] font-extrabold uppercase">{!! $dic('clock', 'w-2.5 h-2.5') !!} {{ $c['pr_number'] }} menunggu</span>
                                    @elseif($c['pr_state'])
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-sky-100 text-sky-700 text-[9px] font-extrabold uppercase">{!! $dic('truck', 'w-2.5 h-2.5') !!} {{ $c['pr_number'] }} disetujui</span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-red-600 text-white text-[9px] font-extrabold uppercase">{!! $dic('alert', 'w-2.5 h-2.5') !!} belum diajukan</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="p-4 border-t border-ice-200 bg-ice-50">
                        @if($stats['unrequested'] > 0)
                            <a href="{{ route('admin.production.requests') }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-red-600 text-white text-sm font-extrabold hover:bg-red-700 shadow-md shadow-red-500/25 transition-all">
                                {!! $dic('send', 'w-4 h-4') !!} Ajukan {{ $stats['unrequested'] }} Bahan Sekarang
                            </a>
                        @else
                            <a href="{{ route('admin.production.requests') }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-brand-600 text-signal-300 text-sm font-extrabold hover:bg-brand-700 transition-colors">
                                {!! $dic('clipboard', 'w-4 h-4') !!} Pantau Status Pengajuan
                            </a>
                        @endif
                        @if($stats['critical'] > count($critical))
                            <p class="mt-2 text-center text-[11px] text-gray-500 tabular-nums">+{{ $stats['critical'] - count($critical) }} bahan kritis lainnya</p>
                        @endif
                    </div>
                @else
                    <div class="p-8 text-center">
                        <span class="inline-flex w-12 h-12 rounded-full bg-signal-100 text-signal-600 items-center justify-center mx-auto">{!! $dic('check', 'w-6 h-6') !!}</span>
                        <p class="mt-3 text-sm font-extrabold text-brand-950">Semua bahan di atas minimum</p>
                        <p class="mt-1 text-xs text-gray-500">Produksi dapat berjalan tanpa menunggu pengadaan.</p>
                    </div>
                @endif
            </div>

            <div class="rounded-3xl bg-white border-2 {{ count($attention) ? 'border-amber-300' : 'border-ice-200' }} shadow-sm overflow-hidden pd-rise">
                <div class="px-5 py-3.5 border-b {{ count($attention) ? 'border-amber-200 bg-amber-50' : 'border-ice-200 bg-ice-50' }} flex items-center gap-2.5">
                    <span class="shrink-0 w-8 h-8 rounded-lg flex items-center justify-center {{ count($attention) ? 'bg-amber-500 text-white' : 'bg-ice-200 text-brand-700' }}">{!! $dic('clock', 'w-4 h-4') !!}</span>
                    <div class="min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-[0.2em] {{ count($attention) ? 'text-amber-600' : 'text-gray-400' }}">Prioritas Waktu</p>
                        <p class="text-sm font-extrabold text-brand-950 leading-tight">Deadline Mendesak</p>
                    </div>
                </div>
                @if(count($attention))
                    <div class="divide-y divide-ice-100">
                        @foreach($attention as $a)
                            <a href="{{ route('admin.orders.show', $a['id']) }}" class="block px-5 py-3 hover:bg-ice-50/70 transition-colors border-l-4 {{ $a['late'] ? 'border-l-red-500' : 'border-l-amber-400' }}">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="text-[13px] font-extrabold text-brand-950 leading-tight truncate">{{ $a['invoice'] }}</p>
                                        <p class="text-[11px] text-gray-500 truncate">{{ $a['customer'] }} · {{ $a['stage'] }}</p>
                                    </div>
                                    <span class="shrink-0 text-right">
                                        <span class="block text-[11px] font-extrabold tabular-nums {{ $a['late'] ? 'text-red-600' : 'text-amber-600' }}">{{ $a['late'] ? 'lewat ' . abs($a['days']) . 'h' : ($a['days'] === 0 ? 'hari ini' : $a['days'] . ' hari') }}</span>
                                        <span class="block text-[10px] text-gray-400 tabular-nums">{{ $a['deadline'] }}</span>
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
                {!! count($attention) ? '' : '<p class="px-5 py-8 text-center text-sm font-bold text-gray-400">Tidak ada deadline mendesak.</p>' !!}
            </div>
        </div>
    </div>

    @if(count($links))
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3">
            @foreach($links as $i => $l)
                <a href="{{ route($l[0]) }}" style="animation-delay: {{ min($i * 50, 250) }}ms"
                   class="pd-rise group relative overflow-hidden rounded-2xl bg-white border border-ice-200 p-4 shadow-sm hover:shadow-lg hover:shadow-brand-950/5 hover:-translate-y-0.5 transition-all">
                    <div class="pd-dots pointer-events-none absolute inset-0 opacity-25"></div>
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-signal-400 to-brand-600"></div>
                    <div class="relative flex items-start gap-3 pl-2">
                        <span class="shrink-0 w-10 h-10 rounded-xl bg-brand-600 text-signal-300 flex items-center justify-center group-hover:bg-brand-700 transition-colors">{!! $dic($l[3], 'w-5 h-5') !!}</span>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-extrabold text-brand-950 leading-tight">{{ $l[1] }}</p>
                            <p class="text-[11px] text-gray-500 mt-0.5">{{ $l[2] }}</p>
                        </div>
                        <span class="shrink-0 text-brand-300 group-hover:text-brand-600 group-hover:translate-x-0.5 transition-all">{!! $dic('arrow', 'w-4 h-4') !!}</span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>