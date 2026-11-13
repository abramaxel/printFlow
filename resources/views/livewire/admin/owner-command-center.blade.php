@php
    // Pustaka ikon garis (satu bahasa dengan sidebar)
    $P = [
        'approve' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
        'clock'   => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        'box'     => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
        'alert'   => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
        'invoice' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'bom'     => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10',
        'rop'     => 'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4',
        'bolt'    => 'M13 10V3L4 14h7v7l9-11h-7z',
        'trend'   => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
        'cart'    => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z',
        'shield'  => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
        'users'   => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
        'download'=> 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4',
        'clip'    => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01',
        'monitor' => 'M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z',
        'money'   => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'check'   => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        'plus'    => 'M12 6v6m0 0v6m0-6h6m-6 0H6',
    ];
    $ico = fn($k, $c = 'w-5 h-5') => '<svg class="' . $c . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="' . ($P[$k] ?? $P['alert']) . '"/></svg>';
@endphp

<div wire:poll.60s class="w-full space-y-8 pb-12">

    {{-- HEADER --}}
    <div class="flex flex-col xl:flex-row xl:items-end xl:justify-between gap-6 pb-6 border-b border-ice-300">
        <div>
            <h1 class="text-3xl font-extrabold text-brand-950 tracking-tight">Command Center</h1>
            <p class="text-gray-500 mt-2 text-lg">Ringkasan performa bisnis <span class="font-semibold text-brand-600">{{ config('app.brand.name', 'FC Murni Jaya') }}</span> &mdash; {{ now()->format('F Y') }}.</p>
        </div>
        <div class="flex flex-col items-start xl:items-end gap-2">
            <div class="flex flex-wrap items-center gap-3 bg-white p-1.5 rounded-xl shadow-sm">
                <div class="flex rounded-lg bg-ice-100 p-1">
                    @foreach(['7d' => '7 Hari', '30d' => '30 Hari', 'month' => 'Bulan Ini'] as $k => $lbl)
                        <button wire:click="setPeriod('{{ $k }}')" class="px-4 py-2 text-sm font-bold rounded-md transition-all {{ $period === $k ? 'bg-white text-brand-700 shadow-sm' : 'text-gray-500 hover:text-brand-700' }}">{{ $lbl }}</button>
                    @endforeach
                </div>
                {{-- REFRESH: ikon panah melingkar (bukan slider) + spin saat memuat --}}
                <button wire:click="$refresh" title="Muat ulang data sekarang" aria-label="Muat ulang data"
                        class="flex items-center justify-center w-10 h-10 text-brand-600 bg-ice-100 hover:bg-ice-200 rounded-lg transition-colors">
                    <span wire:loading.remove wire:target="$refresh" class="flex">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </span>
                    <span wire:loading wire:target="$refresh" class="flex">
                        <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </span>
                </button>
            </div>
            <p x-data="{ at: {{ now()->timestamp }}, tick: 0 }"
               x-init="setInterval(() => tick = Math.floor(Date.now()/1000) - at, 1000)"
               class="text-[10px] font-medium text-gray-400">
                Diperbarui otomatis tiap 60 detik &bull;
                <span x-text="tick < 5 ? 'baru saja' : (tick < 60 ? tick + ' detik lalu' : Math.floor(tick/60) + ' menit lalu')"></span>
            </p>
        </div>
    </div>

    {{-- 1. HERO ROW --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">
        {{-- HERO PETROL + SIGNAL LIME --}}
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-brand-800 via-brand-600 to-brand-500 p-6 text-white shadow-lg shadow-brand-600/30">
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/10 rounded-full"></div>
            <div class="absolute -bottom-16 -right-4 w-48 h-48 bg-signal-400/10 rounded-full"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-bold uppercase tracking-wider text-brand-100">Pendapatan {{ $period === 'month' ? 'Bulan Ini' : ($period === '30d' ? '30 Hari' : '7 Hari') }}</span>
                    <span class="w-8 h-8 rounded-full bg-white/15 text-signal-300 flex items-center justify-center">{!! $ico('money', 'w-4 h-4') !!}</span>
                </div>
                <p class="text-3xl font-extrabold tabular-nums tracking-tight">Rp {{ number_format($revenue, 0, ',', '.') }}</p>
                <p class="text-xs text-brand-100 mt-2">Uang masuk terverifikasi</p>
                <div class="flex items-center gap-2 mt-5">
                    <span class="px-2.5 py-1 rounded-full bg-signal-400 text-brand-950 text-[10px] font-extrabold">{{ $receiptCount }} BPB MASUK</span>
                    <span class="px-2.5 py-1 rounded-full bg-white/15 text-[10px] font-bold">{{ $newOrders }} pesanan baru</span>
                </div>
            </div>
        </div>

        {{-- BIAYA & MARGIN --}}
        <div class="bg-white rounded-2xl shadow-sm p-6 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Biaya & Margin</span>
                <span class="text-[10px] font-bold text-brand-700 bg-ice-100 px-2 py-1 rounded-md">{{ $period === 'month' ? 'BULAN INI' : strtoupper($period) }}</span>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <div class="flex items-center gap-1.5 mb-1"><span class="w-1.5 h-1.5 rounded-full bg-brand-300"></span><span class="text-[10px] font-bold text-gray-400 uppercase">Pembelian</span></div>
                    <p class="text-lg font-extrabold text-brand-950 tabular-nums tracking-tight">Rp {{ number_format($purchases, 0, ',', '.') }}</p>
                </div>
                <div>
                    <div class="flex items-center gap-1.5 mb-1"><span class="w-1.5 h-1.5 rounded-full bg-signal-500"></span><span class="text-[10px] font-bold text-gray-400 uppercase">Margin</span></div>
                    <p class="text-lg font-extrabold {{ $margin >= 0 ? 'text-brand-600' : 'text-red-600' }} tabular-nums tracking-tight">Rp {{ number_format($margin, 0, ',', '.') }}</p>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-ice-200 flex items-center justify-between text-xs">
                <span class="text-gray-400 font-medium">Rasio margin</span>
                <span class="font-bold {{ $margin >= 0 ? 'text-brand-600' : 'text-red-600' }}">{{ $revenue > 0 ? round(($margin / $revenue) * 100, 1) : 0 }}%</span>
            </div>
        </div>

        {{-- KESEHATAN PIUTANG --}}
        <a href="{{ route('admin.orders') }}?status=unpaid" class="block bg-white rounded-2xl shadow-sm p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Kesehatan Piutang</span>
                <span class="w-8 h-8 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center">{!! $ico('alert', 'w-4 h-4') !!}</span>
            </div>
            <p class="text-2xl font-extrabold text-brand-950 tabular-nums tracking-tight">Rp {{ number_format($receivable, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-500 mt-1">Total tagihan belum lunas</p>
            @php $safeRatio = $receivable > 0 ? max(0, min(100, round((($receivable - $overdueValue) / $receivable) * 100))) : 100; @endphp
            <div class="mt-4 h-2 rounded-full bg-ice-200 overflow-hidden flex">
                <div class="h-full {{ $safeRatio >= 70 ? 'bg-brand-500' : ($safeRatio >= 40 ? 'bg-amber-500' : 'bg-red-500') }}" style="width: {{ $safeRatio }}%"></div>
                <div class="h-full bg-red-400/70" style="width: {{ 100 - $safeRatio }}%"></div>
            </div>
            <div class="flex items-center justify-between mt-2 text-[10px] font-bold">
                <span class="text-gray-400">{{ $safeRatio }}% aman</span>
                <span class="{{ $overdueCount > 0 ? 'text-red-500' : 'text-gray-400' }}">{{ $overdueCount }} jatuh tempo</span>
            </div>
        </a>
    </div>

    {{-- ROW B --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white rounded-2xl shadow-sm p-5 flex items-center gap-4">
            <span class="w-11 h-11 rounded-xl bg-ice-100 text-brand-600 flex items-center justify-center shrink-0">{!! $ico('plus') !!}</span>
            <div><p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Pesanan Baru</p><p class="text-xl font-extrabold text-brand-950 tabular-nums">{{ $newOrders }}</p></div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-5 flex items-center gap-4">
            <span class="w-11 h-11 rounded-xl bg-signal-100 text-signal-600 flex items-center justify-center shrink-0">{!! $ico('check') !!}</span>
            <div><p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Selesai</p><p class="text-xl font-extrabold text-brand-950 tabular-nums">{{ $doneOrders }}</p></div>
        </div>
        <a href="{{ route('admin.purchase-requests') }}" class="bg-white rounded-2xl shadow-sm p-5 flex items-center gap-4 hover:shadow-md transition-shadow">
            <span class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">{!! $ico('invoice') !!}</span>
            <div><p class="text-xs font-bold text-gray-400 uppercase tracking-wider">PR Menunggu</p><p class="text-xl font-extrabold text-brand-950 tabular-nums">{{ $prPending }} <span class="text-xs font-bold text-amber-600">({{ $prCritical }} kritis)</span></p></div>
        </a>
    </div>

    {{-- 2. DECISION QUEUE (ikon garis, grid kompak) --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-ice-200 flex items-center justify-between bg-ice-50">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-red-50 text-red-600 rounded-xl">{!! $ico('alert') !!}</div>
                <div>
                    <h2 class="text-lg font-bold text-brand-950">Butuh Keputusan Anda</h2>
                    <p class="text-xs text-gray-500 mt-0.5">{{ count($decisions) }} item menunggu tindakan &bull; diurutkan dari prioritas tertinggi</p>
                </div>
            </div>
            <span class="hidden sm:inline-flex items-center gap-1.5 text-[10px] font-bold text-brand-700 bg-ice-100 px-3 py-1.5 rounded-full uppercase tracking-wider"><span class="w-1.5 h-1.5 rounded-full bg-signal-500"></span> Prioritas</span>
        </div>
        <div class="p-6">
            @if(count($decisions) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    @foreach($decisions as $d)
                        @php
                            $a = $d['sev'] === 0
                                ? ['edge' => 'border-l-red-500',    'chip' => 'bg-red-50 text-red-600',     'badge' => 'bg-red-100 text-red-700',   'btn' => 'bg-red-600 hover:bg-red-700 text-white shadow-sm shadow-red-200']
                                : ($d['sev'] === 1
                                    ? ['edge' => 'border-l-amber-400', 'chip' => 'bg-amber-50 text-amber-600', 'badge' => 'bg-amber-100 text-amber-700', 'btn' => 'bg-brand-800 hover:bg-brand-900 text-white shadow-sm shadow-brand-200']
                                    : ['edge' => 'border-l-brand-300', 'chip' => 'bg-ice-100 text-brand-600',  'badge' => 'bg-ice-200 text-brand-700',   'btn' => 'bg-white hover:bg-ice-50 text-brand-700 border border-ice-300']);
                        @endphp
                        <div class="flex flex-col rounded-2xl border border-ice-200 border-l-4 {{ $a['edge'] }} bg-ice-50 p-5 hover:bg-white hover:shadow-md hover:-translate-y-0.5 transition-all">
                            <div class="flex items-start justify-between mb-3">
                                <div class="w-10 h-10 rounded-xl {{ $a['chip'] }} flex items-center justify-center">{!! $ico($d['icon']) !!}</div>
                                <span class="px-2 py-0.5 text-[10px] font-extrabold rounded-md {{ $a['badge'] }}">{{ $d['count'] }}</span>
                            </div>
                            <p class="text-sm font-bold text-brand-950 leading-snug">{{ $d['title'] }}</p>
                            <p class="text-xs text-gray-500 mt-1 mb-4">{{ $d['detail'] }}</p>
                            <a href="{{ $d['route'] }}" class="mt-auto flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-bold transition-all {{ $a['btn'] }}">{{ $d['cta'] }} &rarr;</a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-10 text-center">
                    <div class="w-16 h-16 bg-signal-100 text-signal-600 rounded-full flex items-center justify-center mb-4">{!! $ico('check', 'w-8 h-8') !!}</div>
                    <p class="text-brand-950 font-bold text-lg">Semua Terkendali!</p>
                    <p class="text-gray-500 text-sm mt-1">Tidak ada keputusan mendesak saat ini.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- 3. PIPELINE & TREN --}}
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        <div class="xl:col-span-7 bg-white rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-bold text-brand-950 flex items-center gap-2"><span class="text-signal-500">{!! $ico('bolt') !!}</span> Pipeline Produksi</h2>
                <a href="{{ route('admin.production') }}" class="text-xs font-bold text-brand-600 hover:text-brand-700">Lihat Antrian &rarr;</a>
            </div>
            <div class="grid grid-cols-3 md:grid-cols-6 gap-3">
                @foreach([
                    ['k' => 'waiting_invoice', 'l' => 'Menunggu',  'c' => 'text-brand-700',  'b' => 'bg-ice-100 border-ice-300'],
                    ['k' => 'unpaid',          'l' => 'Belum Bayar','c' => 'text-amber-600',  'b' => 'bg-amber-50 border-amber-200'],
                    ['k' => 'paid',            'l' => 'SPK Terbit', 'c' => 'text-brand-600',  'b' => 'bg-brand-50 border-brand-200'],
                    ['k' => 'processing',      'l' => 'Dicetak',    'c' => 'text-brand-700',  'b' => 'bg-brand-100 border-brand-200'],
                    ['k' => 'qc',              'l' => 'QC',         'c' => 'text-brand-800',  'b' => 'bg-brand-200 border-brand-300'],
                    ['k' => 'shipping',        'l' => 'Siap Kirim', 'c' => 'text-signal-600', 'b' => 'bg-signal-100 border-signal-300'],
                ] as $s)
                    <a href="{{ route('admin.orders') }}?status={{ $s['k'] }}" class="min-w-0 rounded-xl border {{ $s['b'] }} p-4 text-center transition-all hover:-translate-y-1 hover:shadow-sm">
                        <p class="text-2xl font-extrabold {{ $s['c'] }} tabular-nums mb-1">{{ $pipeline[$s['k']] }}</p>
                        <p class="text-[10px] font-bold {{ $s['c'] }} uppercase tracking-wide opacity-80">{{ $s['l'] }}</p>
                    </a>
                @endforeach
            </div>
        </div>

        <div class="xl:col-span-5 bg-white rounded-2xl shadow-sm p-6 flex flex-col">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-bold text-brand-950 flex items-center gap-2"><span class="text-brand-500">{!! $ico('trend') !!}</span> Tren 6 Bulan</h2>
                <div class="flex rounded-lg bg-ice-100 p-1">
                    <button wire:click="setTrendMetric('revenue')" class="px-3 py-1.5 text-[10px] font-bold rounded-md transition-all {{ $trendMetric === 'revenue' ? 'bg-white text-brand-700 shadow-sm' : 'text-gray-500' }}">PENDAPATAN</button>
                    <button wire:click="setTrendMetric('purchases')" class="px-3 py-1.5 text-[10px] font-bold rounded-md transition-all {{ $trendMetric === 'purchases' ? 'bg-white text-signal-600 shadow-sm' : 'text-gray-500' }}">PEMBELIAN</button>
                </div>
            </div>
            @php
                $maxVal = max(1, collect($trend)->max($trendMetric));
                $fmtShort = fn($v) => $v >= 1_000_000_000 ? round($v / 1_000_000_000, 1) . 'M'
                            : ($v >= 1_000_000 ? round($v / 1_000_000, 1) . 'jt'
                            : ($v >= 1_000 ? round($v / 1_000, 0) . 'rb' : (string) round($v)));
            @endphp
            {{-- Tinggi bar PIXEL PASTI (bukan %) → tidak bisa collapse/terpotong --}}
            <div class="flex items-end gap-1.5 sm:gap-3 h-44">
                @foreach($trend as $t)
                    @php $px = (int) max(6, round(($t[$trendMetric] / $maxVal) * 140)); @endphp
                    <div class="flex-1 min-w-0 flex flex-col items-center justify-end gap-1.5 h-full">
                        <span class="text-[9px] sm:text-[10px] font-bold text-brand-700 tabular-nums truncate w-full text-center">{{ $fmtShort($t[$trendMetric]) }}</span>
                        <div class="w-full rounded-t-md {{ $trendMetric === 'revenue' ? 'bg-brand-500' : 'bg-signal-400' }}" style="height: {{ $px }}px"></div>
                        <span class="text-[9px] sm:text-[10px] font-bold text-gray-400 truncate w-full text-center">{{ $t['label'] }}</span>
                    </div>
                @endforeach
            </div>
            <div class="mt-6 pt-4 border-t border-ice-200 flex justify-between items-center">
                <span class="text-xs font-bold text-gray-400 uppercase">Total 6 Bulan</span>
                <span class="text-sm font-extrabold text-brand-950 tabular-nums">Rp {{ number_format(collect($trend)->sum($trendMetric), 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    {{-- 4. STOK & PURCHASING --}}
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        <div class="xl:col-span-6 bg-white rounded-2xl shadow-sm overflow-hidden flex flex-col">
            <div class="px-6 py-5 border-b border-ice-200 flex items-center justify-between bg-ice-50">
                <h2 class="text-lg font-bold text-brand-950 flex items-center gap-2"><span class="text-brand-500">{!! $ico('box') !!}</span> Stok & Batch</h2>
                <a href="{{ route('admin.inventory') }}" class="text-xs font-bold text-brand-600 hover:text-brand-700">Inventory &rarr;</a>
            </div>
            <div class="p-6 space-y-3 flex-1">
                @forelse($stockAlerts as $m)
                    @php $n = (float)$m->stock - (float)$m->stock_reserved; @endphp
                    <div class="flex items-center justify-between rounded-xl border {{ $n <= 0 ? 'border-red-200 bg-red-50/50' : 'border-amber-200 bg-amber-50/50' }} p-4">
                        <div class="min-w-0 pr-4">
                            <p class="text-sm font-bold text-brand-950 truncate">{{ $m->name }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">Bersih: <span class="font-bold {{ $n <= 0 ? 'text-red-600' : 'text-amber-600' }}">{{ number_format($n, 1) }}</span> / Min: {{ number_format((float)$m->minimum_stock, 1) }} {{ $m->unit }}</p>
                        </div>
                        <span class="shrink-0 px-3 py-1 text-[10px] font-extrabold rounded-md {{ $n <= 0 ? 'bg-red-600 text-white' : 'bg-amber-500 text-white' }} uppercase tracking-wider">{{ $n <= 0 ? 'Habis' : 'Kritis' }}</span>
                    </div>
                @empty
                    <div class="h-full flex flex-col items-center justify-center py-8 text-center">
                        <div class="w-12 h-12 bg-signal-100 text-signal-600 rounded-full flex items-center justify-center mb-3">{!! $ico('check', 'w-6 h-6') !!}</div>
                        <p class="text-sm font-bold text-brand-950">Semua Bahan Aman</p>
                    </div>
                @endforelse
                @if($expCount > 0)
                    <div class="rounded-xl border border-amber-200 bg-amber-50/50 p-4 mt-4">
                        <p class="text-xs font-bold text-amber-700 mb-2">{{ $expCount }} Batch Kadaluarsa &le;30 Hari</p>
                        @foreach($expBatches as $b)
                            <p class="text-xs text-gray-600 flex justify-between"><span class="font-medium truncate pr-2">{{ $b['name'] }}</span><span class="shrink-0">{{ number_format($b['qty'], 1) }} {{ $b['unit'] }} &bull; {{ \Carbon\Carbon::parse($b['expiry'])->format('d M') }}</span></p>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="xl:col-span-6 bg-white rounded-2xl shadow-sm overflow-hidden flex flex-col">
            <div class="px-6 py-5 border-b border-ice-200 flex items-center justify-between bg-ice-50">
                <h2 class="text-lg font-bold text-brand-950 flex items-center gap-2"><span class="text-brand-500">{!! $ico('cart') !!}</span> Purchasing</h2>
                <a href="{{ route('admin.purchase-requests') }}" class="text-xs font-bold text-brand-600 hover:text-brand-700">Approve PR &rarr;</a>
            </div>
            <div class="p-6 space-y-5 flex-1">
                <div class="flex items-center justify-between rounded-xl bg-amber-50 border border-amber-100 p-4">
                    <div><p class="text-sm font-bold text-brand-950">{{ $prPending }} PR Pending</p><p class="text-xs text-gray-500 mt-0.5">{{ $prCritical }} Kritikal &bull; {{ $prAwaiting }} Menunggu Barang</p></div>
                    <p class="text-sm font-extrabold text-amber-600 tabular-nums">Rp {{ number_format($prValue, 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-3">PR Prioritas Tertinggi</p>
                    @forelse($recentPrs as $pr)
                        <div class="flex items-center justify-between py-2 border-b border-ice-100 last:border-0">
                            <div class="min-w-0 pr-3"><p class="text-sm font-bold text-brand-950 truncate">{{ $pr->material->name ?? '-' }}</p><p class="text-xs text-gray-500">{{ number_format((float)$pr->requested_quantity, 1) }} {{ $pr->unit }}</p></div>
                            <span class="shrink-0 px-2 py-0.5 text-[9px] font-extrabold rounded {{ $pr->urgency === 'critical' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700' }} uppercase">{{ $pr->urgency }}</span>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 italic">Tidak ada PR pending.</p>
                    @endforelse
                </div>
                <div class="pt-4 border-t border-ice-200">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-3">BPB Terbaru</p>
                    @forelse($recentReceipts as $r)
                        <div class="flex items-center justify-between py-1">
                            <div class="min-w-0 pr-3"><p class="text-sm font-medium text-brand-950 truncate">{{ $r->supplier->name ?? '-' }}</p><p class="text-[10px] text-gray-400 font-mono">{{ $r->bpb_number }}</p></div>
                            <a href="{{ route('admin.purchase-receipts.pdf', $r->id) }}" target="_blank" class="shrink-0 text-xs font-bold text-brand-600 hover:underline tabular-nums">Rp {{ number_format((float)$r->total_cost, 0, ',', '.') }}</a>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 italic">Belum ada penerimaan barang.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- 5. KEPATUHAN & CRM --}}
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        <div class="xl:col-span-6 bg-white rounded-2xl shadow-sm p-6">
            <h2 class="text-lg font-bold text-brand-950 flex items-center gap-2 mb-6"><span class="text-brand-500">{!! $ico('shield') !!}</span> Kepatuhan & Integritas</h2>
            <div class="grid grid-cols-2 gap-4">
                <a href="{{ route('admin.materials') }}" class="rounded-xl border border-ice-200 p-4 hover:border-red-300 hover:bg-red-50/30 transition-all"><p class="text-xs font-bold text-gray-400 uppercase mb-2">Reservasi > Stok</p><p class="text-2xl font-extrabold {{ $overReserved->count() > 0 ? 'text-red-600' : 'text-signal-600' }} tabular-nums">{{ $overReserved->count() }}</p></a>
                <a href="{{ route('admin.materials') }}" class="rounded-xl border border-ice-200 p-4 hover:border-amber-300 hover:bg-amber-50/30 transition-all"><p class="text-xs font-bold text-gray-400 uppercase mb-2">ROP Belum Diset</p><p class="text-2xl font-extrabold {{ $ropUnset > 0 ? 'text-amber-600' : 'text-signal-600' }} tabular-nums">{{ $ropUnset }}</p></a>
                <a href="{{ route('admin.service-boms') }}" class="rounded-xl border border-ice-200 p-4 hover:border-amber-300 hover:bg-amber-50/30 transition-all"><p class="text-xs font-bold text-gray-400 uppercase mb-2">Layanan Tanpa BOM</p><p class="text-2xl font-extrabold {{ $noBom > 0 ? 'text-amber-600' : 'text-signal-600' }} tabular-nums">{{ $noBom }}</p></a>
                <a href="{{ route('admin.stock-opname') }}" class="rounded-xl border border-ice-200 p-4 hover:border-brand-300 hover:bg-ice-50 transition-all"><p class="text-xs font-bold text-gray-400 uppercase mb-2">Adjustment / Opname</p><p class="text-2xl font-extrabold text-brand-950 tabular-nums">{{ $adjCount }}</p><p class="text-[10px] text-gray-400 mt-1 font-medium">{{ $lastOpname ? \Carbon\Carbon::parse($lastOpname)->format('d M Y') : 'Belum pernah' }}</p></a>
            </div>
        </div>

        <div class="xl:col-span-6 bg-white rounded-2xl shadow-sm overflow-hidden flex flex-col">
            <div class="px-6 py-5 border-b border-ice-200 flex items-center justify-between bg-ice-50">
                <h2 class="text-lg font-bold text-brand-950 flex items-center gap-2"><span class="text-brand-500">{!! $ico('users') !!}</span> Top Pelanggan</h2>
                <a href="{{ route('admin.customers') }}" class="text-xs font-bold text-brand-600 hover:text-brand-700">CRM &rarr;</a>
            </div>
            <div class="p-6 space-y-4 flex-1">
                @forelse($topCustomers as $c)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-full bg-ice-200 text-brand-700 font-bold flex items-center justify-center shrink-0">{{ strtoupper(substr($c->name, 0, 1)) }}</div>
                            <div class="min-w-0"><p class="text-sm font-bold text-brand-950 truncate">{{ $c->name }}</p><p class="text-xs text-gray-500 truncate">{{ $c->email }}</p></div>
                        </div>
                        <div class="shrink-0 text-right pl-3">
                            <p class="text-sm font-extrabold text-brand-600 tabular-nums">Rp {{ number_format((float)($c->paid_period ?? 0), 0, ',', '.') }}</p>
                            <span class="inline-block mt-0.5 px-2 py-0.5 text-[9px] font-bold rounded bg-ice-100 text-brand-700 uppercase">{{ $c->tier ?? 'regular' }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 italic text-center py-4">Belum ada pembayaran periode ini.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- 6. AKSI CEPAT (ikon garis seragam) --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
        @foreach([
            ['r' => 'admin.purchase-requests',  'i' => 'approve',  'l' => 'Approve PR'],
            ['r' => 'admin.goods-receipt',      'i' => 'download', 'l' => 'Terima Barang'],
            ['r' => 'admin.stock-opname',       'i' => 'clip',     'l' => 'Stock Opname'],
            ['r' => 'admin.material-audit-trail','i' => 'clock',   'l' => 'Audit Trail'],
            ['r' => 'admin.production-monitor', 'i' => 'monitor',  'l' => 'Monitor Prod.'],
            ['r' => 'admin.customers',          'i' => 'users',    'l' => 'CRM'],
        ] as $a)
            <a href="{{ route($a['r']) }}" class="flex flex-col items-center justify-center p-4 bg-white rounded-2xl shadow-sm hover:shadow-md hover:-translate-y-1 transition-all group">
                <div class="w-12 h-12 rounded-xl bg-ice-100 text-brand-600 flex items-center justify-center mb-3 group-hover:bg-brand-600 group-hover:text-signal-300 transition-colors">{!! $ico($a['i'], 'w-6 h-6') !!}</div>
                <p class="text-xs font-bold text-brand-950 text-center">{{ $a['l'] }}</p>
            </a>
        @endforeach
    </div>

</div>