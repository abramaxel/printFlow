@php
    $P = [
        'printer' => 'M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z',
        'box'     => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
        'shield'  => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
        'truck'   => 'M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0 2 2 0 00-4 0zm10 0a2 2 0 104 0 2 2 0 00-4 0z',
        'alert'   => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
        'check'   => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        'clock'   => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        'bolt'    => 'M13 10V3L4 14h7v7l9-11h-7z',
        'search'  => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z',
        'copy'    => 'M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z',
        'arrow'   => 'M14 5l7 7m0 0l-7 7m7-7H3',
        'undo'    => 'M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6',
        'x'       => 'M6 18L18 6M6 6l12 12',
        'cart'    => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z',
        'flag'    => 'M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9',
        'note'    => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'phone'   => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z',
        'eye'     => 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z',
    ];
    $pic = fn($k, $c = 'w-5 h-5') => '<svg class="' . $c . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="' . ($P[$k] ?? $P['box']) . '"/></svg>';

    $stageMeta = [
        'awaiting'   => ['Antre Konfirmasi', 'bg-amber-100 text-amber-800', 'clock'],
        'processing' => ['Sedang Dicetak', 'bg-brand-200 text-brand-900', 'printer'],
        'qc'         => ['Quality Control', 'bg-brand-300 text-brand-950', 'shield'],
    ];
    $chips = [
        ['all', 'Total Antrian', 'box', $stats['total']],
        ['awaiting', 'Antre Konfirmasi', 'clock', $stats['awaiting']],
        ['processing', 'Sedang Dicetak', 'printer', $stats['processing']],
        ['qc', 'Quality Control', 'shield', $stats['qc']],
        ['blocked', 'Tertahan Stok', 'alert', $stats['blocked']],
    ];
@endphp

<div class="w-full space-y-6">
    <style>
        @keyframes pqRise { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
        .pq-rise { animation: pqRise .5s cubic-bezier(.2,.7,.2,1) both; }
        @keyframes pqLive { 0%,100% { box-shadow: 0 0 0 0 rgba(220,38,38,.4); } 50% { box-shadow: 0 0 0 10px rgba(220,38,38,0); } }
        .pq-live { animation: pqLive 2.2s ease-out infinite; }
        @keyframes pqFlow { 0% { background-position: 0 0; } 100% { background-position: 28px 0; } }
        .pq-flow { background-image: repeating-linear-gradient(45deg, rgba(200,236,63,.35) 0 6px, transparent 6px 14px); animation: pqFlow 1.1s linear infinite; }
        .pq-scroll::-webkit-scrollbar { height: 8px; width: 8px; }
        .pq-scroll::-webkit-scrollbar-track { background: #e2edf0; border-radius: 8px; }
        .pq-scroll::-webkit-scrollbar-thumb { background: #1d6247; border-radius: 8px; }
        .pq-scroll::-webkit-scrollbar-thumb:hover { background: #0a3f33; }
        .pq-select { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' stroke='%230a3f33' stroke-width='2.5' viewBox='0 0 24 24'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right .85rem center; background-size: 1rem; }
        @media (prefers-reduced-motion: reduce) { .pq-rise, .pq-live, .pq-flow { animation: none !important; } }
    </style>

    {{-- ===== TOAST ===== --}}
    @if(session('queue_success') || session('queue_error'))
        @php $ok = (bool) session('queue_success'); @endphp
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-cloak
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-3"
             class="fixed top-24 right-4 sm:right-6 z-[70] max-w-sm" role="status" aria-live="polite">
            <div class="relative overflow-hidden rounded-2xl px-5 py-4 shadow-2xl flex items-start gap-3 {{ $ok ? 'forest-hero shadow-brand-950/40' : 'bg-red-600 shadow-red-900/40' }}">
                @if($ok)<div class="forest-dots absolute inset-0 opacity-40"></div>@endif
                <span class="relative z-10 shrink-0 w-9 h-9 rounded-xl flex items-center justify-center {{ $ok ? 'bg-signal-400 text-brand-950' : 'bg-white/20 text-white' }}">
                    {!! $pic($ok ? 'check' : 'alert', 'w-5 h-5') !!}
                </span>
                <p class="relative z-10 flex-1 text-base font-bold text-white">{{ session('queue_success') ?? session('queue_error') }}</p>
                <button type="button" @click="show = false" class="relative z-10 shrink-0 text-white/60 hover:text-white transition-colors">{!! $pic('x', 'w-4 h-4') !!}</button>
            </div>
        </div>
    @endif

    {{-- ===== HERO ===== --}}
    <div class="forest-hero relative overflow-hidden rounded-3xl text-white px-6 py-6 sm:px-8 sm:py-7 shadow-lg shadow-brand-950/25 pq-rise">
        <div class="forest-dots absolute inset-0 opacity-45"></div>
        <div class="absolute -top-24 -left-16 w-72 h-72 rounded-full bg-emerald-500/15 blur-3xl"></div>
        <div class="absolute -bottom-24 right-0 w-64 h-64 rounded-full bg-signal-400/10 blur-3xl"></div>
        <div class="absolute inset-y-0 left-1/2 w-28 rotate-12 bg-gradient-to-b from-signal-400/0 via-signal-400/10 to-signal-400/0 blur-2xl"></div>

        <div class="relative z-10 flex flex-col xl:flex-row xl:items-center justify-between gap-5">
            <div class="min-w-0">
                <p class="text-xs font-bold uppercase tracking-[0.24em] text-signal-300">Lantai Produksi</p>
                <h1 class="mt-2 text-3xl font-extrabold tracking-tight">Antrian Produksi</h1>
                <p class="mt-2 text-base text-emerald-100/75 max-w-2xl">Konfirmasi ambil bahan, jalankan cetak, lalu lulus QC. Diurutkan: antre konfirmasi &rarr; deadline terdekat.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                <a href="{{ route('admin.production.requests') }}" class="inline-flex items-center gap-2 px-4 py-3 rounded-2xl bg-white/10 border border-white/15 text-sm font-bold hover:bg-white/20 transition-colors">
                    {!! $pic('cart', 'w-4 h-4') !!} Ajukan Purchase Request
                </a>
                <a href="{{ route('admin.inventory') }}" class="inline-flex items-center gap-2 px-4 py-3 rounded-2xl bg-signal-400 text-brand-950 text-sm font-extrabold hover:bg-signal-500 hover:-translate-y-0.5 shadow-md shadow-signal-400/25 transition-all">
                    {!! $pic('box', 'w-4 h-4') !!} Cek Stok Gudang
                </a>
            </div>
        </div>

        <div class="relative z-10 mt-7 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
            @foreach($chips as $c)
                <button type="button" wire:click="setStage('{{ $c[0] }}')"
                        class="text-left rounded-2xl px-4 py-3 border transition-all hover:-translate-y-0.5 {{ $stage === $c[0] ? 'bg-signal-400 border-signal-400 text-brand-950' : ($c[0] === 'blocked' && $c[3] > 0 ? 'bg-red-500/20 border-red-400/40 text-white hover:bg-red-500/30' : 'bg-white/5 border-white/10 text-white hover:bg-white/10') }}">
                    <span class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-[0.16em] {{ $stage === $c[0] ? 'text-brand-900/70' : 'text-emerald-100/60' }}">
                        {!! $pic($c[2], 'w-3.5 h-3.5') !!} {{ $c[1] }}
                    </span>
                    <span class="mt-1 block text-2xl font-extrabold tabular-nums">{{ $c[3] }}</span>
                </button>
            @endforeach
        </div>
    </div>
     @if(($stats['incoming'] ?? 0) > 0)
        <div class="forest-hero relative overflow-hidden rounded-[2rem] px-6 py-5 text-white shadow-lg shadow-brand-950/20 pq-rise">
            <div class="forest-dots absolute inset-0 opacity-45"></div>
            <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-signal-400 via-brand-400 to-signal-400"></div>
            <div class="relative z-10 flex flex-wrap items-center justify-between gap-4 pt-1">
                <div class="flex items-center gap-4 min-w-0">
                    <span class="shrink-0 w-12 h-12 rounded-2xl bg-signal-400 text-brand-950 flex items-center justify-center">{!! $pic('truck', 'w-6 h-6') !!}</span>
                    <div class="min-w-0">
                        <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-signal-300">Bahan Sedang Dalam Pengadaan</p>
                        <h2 class="mt-1 text-lg font-extrabold tracking-tight">{{ $stats['incoming'] }} item menunggu kedatangan barang</h2>
                        <p class="text-sm text-emerald-100/75">PR sudah disetujui owner. Catat penerimaan agar pesanan bisa lanjut cetak.</p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2.5 shrink-0">
                    <a href="{{ route('admin.production.requests') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 border border-white/15 text-sm font-bold hover:bg-white/20 transition-colors">{!! $pic('list', 'w-4 h-4') !!} Lihat PR Disetujui</a>
                    <a href="{{ route('admin.goods-receipt') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-signal-400 text-brand-950 text-sm font-extrabold hover:bg-signal-500 transition-colors">{!! $pic('truck', 'w-4 h-4') !!} Penerimaan Barang</a>
                </div>
            </div>
        </div>
    @endif


    {{-- ===== CONTROL BAR ===== --}}
    <div class="sticky top-24 z-20 rounded-[1.75rem] bg-white/85 backdrop-blur-xl border border-ice-200 shadow-xl shadow-brand-950/5 p-4 pq-rise">
        <div class="flex flex-col lg:flex-row gap-3 lg:items-center">
            <div class="relative flex-1 min-w-0">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-brand-400">{!! $pic('search', 'w-4 h-4') !!}</span>
                <input type="text" wire:model.live.debounce.300ms="search"
                       placeholder="Cari invoice, pelanggan, telepon, layanan, atau bahan..."
                       class="w-full pl-11 pr-10 py-3 text-base border border-ice-300 rounded-2xl bg-ice-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25 focus:border-brand-500 transition-all">
                <span wire:loading wire:target="search" class="absolute inset-y-0 right-0 pr-4 flex items-center">
                    <svg class="w-4 h-4 animate-spin text-brand-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.4 0 0 5.4 0 12h4z"/></svg>
                </span>
            </div>
            <div class="flex items-center gap-2.5 shrink-0">
                <select wire:model.live="sort" class="pq-select pl-4 pr-10 py-3 text-sm font-bold text-brand-800 border border-ice-300 rounded-2xl bg-ice-50 focus:outline-none focus:ring-2 focus:ring-brand-500/25 cursor-pointer">
                    <option value="priority">Urut: Prioritas kerja</option>
                    <option value="deadline">Urut: Deadline terdekat</option>
                    <option value="newest">Urut: Terbaru masuk</option>
                </select>
                @if($hasFilter)
                    <button type="button" wire:click="clearFilters" class="inline-flex items-center gap-2 px-4 py-3 rounded-2xl bg-red-50 text-red-600 text-sm font-bold hover:bg-red-100 transition-colors">
                        {!! $pic('undo', 'w-4 h-4') !!} Reset
                    </button>
                @endif
            </div>
        </div>

        <div class="mt-3 flex flex-wrap gap-2">
            @foreach([['all','Semua Tahap'],['awaiting','Antre Konfirmasi'],['processing','Sedang Dicetak'],['qc','Quality Control'],['blocked','Tertahan Stok']] as $p)
                <button type="button" wire:click="setStage('{{ $p[0] }}')"
                        class="px-4 py-2 rounded-full text-sm font-extrabold transition-all {{ $stage === $p[0] ? 'bg-brand-600 text-signal-300 shadow-sm shadow-brand-900/25' : 'bg-ice-100 text-brand-800 hover:bg-ice-200' }}">
                    {{ $p[1] }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- ===== QUEUE ===== --}}
    <div wire:loading.class="opacity-40 pointer-events-none" class="space-y-5 transition-opacity duration-200">
        @forelse($queue as $q)
            @php
                $sm = $stageMeta[$q['stage']] ?? ['Antrian', 'bg-ice-200 text-brand-800', 'box'];
                $dl = $q['days_left'];
                $dlTone = is_null($dl) ? 'bg-ice-100 text-gray-500'
                    : ($dl < 0 ? 'bg-red-100 text-red-700' : ($dl <= 2 ? 'bg-amber-100 text-amber-800' : 'bg-ice-100 text-brand-800'));
                $dlText = is_null($dl) ? 'Tanpa deadline' : ($dl < 0 ? 'Terlambat ' . abs($dl) . ' hari' : ($dl === 0 ? 'Jatuh tempo hari ini' : $dl . ' hari lagi'));
                $isBlocked = $q['blocked'] && $q['stage'] === 'awaiting';
            @endphp

            <article class="relative overflow-hidden rounded-[2rem] bg-white border shadow-sm pq-rise {{ $isBlocked ? 'border-red-200' : 'border-ice-200' }}">
                <div class="absolute left-0 top-0 bottom-0 w-1.5 {{ $isBlocked ? 'bg-gradient-to-b from-red-500 to-red-300' : ($q['stage'] === 'processing' ? 'bg-gradient-to-b from-brand-500 to-brand-700 pq-flow' : 'bg-gradient-to-b from-signal-400 to-brand-500') }}"></div>

                {{-- Header --}}
                <div class="pl-6 sm:pl-8 pr-5 sm:pr-7 pt-5 pb-4 border-b border-ice-200 {{ $isBlocked ? 'bg-red-50/50' : 'bg-ice-50/60' }}">
                    <div class="flex flex-col xl:flex-row xl:items-start justify-between gap-4">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="text-xl font-extrabold text-brand-950 tracking-tight">{{ $q['invoice'] }}</h2>
                                <button type="button" onclick="copyText('{{ $q['invoice'] }}', this)" title="Salin nomor invoice"
                                        class="w-7 h-7 flex items-center justify-center rounded-lg text-brand-500 hover:bg-brand-600 hover:text-signal-300 transition-colors">
                                    {!! $pic('copy', 'w-3.5 h-3.5') !!}
                                </button>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold rounded-full {{ $sm[1] }}">
                                    {!! $pic($sm[2], 'w-3.5 h-3.5') !!} {{ $sm[0] }}
                                </span>
                                @if($isBlocked)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-extrabold rounded-full bg-red-600 text-white pq-live">
                                        {!! $pic('alert', 'w-3.5 h-3.5') !!} Stok Kurang
                                    </span>
                                @endif
                                @if($q['priority'] === 'high')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-extrabold rounded-full bg-amber-400 text-brand-950">
                                        {!! $pic('flag', 'w-3.5 h-3.5') !!} Prioritas Tinggi
                                    </span>
                                @endif
                                @if($q['incidents'] > 0)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold rounded-full bg-amber-100 text-amber-800">
                                        {!! $pic('note', 'w-3.5 h-3.5') !!} {{ $q['incidents'] }} kendala
                                    </span>
                                @endif
                            </div>

                            <div class="mt-3 flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-gray-500">
                                <span class="inline-flex items-center gap-2 min-w-0">
                                    <x-avatar :user="$q['user']" size="xs" shape="rounded-lg" />
                                    <b class="text-brand-900 truncate max-w-[12rem]">{{ $q['customer'] }}</b>
                                </span>
                                @if($q['phone'])
                                    <span class="inline-flex items-center gap-1.5 tabular-nums">{!! $pic('phone', 'w-3.5 h-3.5') !!} {{ $q['phone'] }}</span>
                                @endif
                                <span class="inline-flex items-center gap-1.5">{!! $pic('clock', 'w-3.5 h-3.5') !!} {{ $q['created'] }}</span>
                                <span class="inline-flex items-center gap-1.5">{!! $pic('truck', 'w-3.5 h-3.5') !!} {{ ['pickup' => 'Ambil di Toko', 'regular' => 'Kirim Reguler', 'instant' => 'Kirim Instant'][$q['shipping']] ?? $q['shipping'] }}</span>
                            </div>
                        </div>

                        <div class="shrink-0 flex flex-col items-start xl:items-end gap-2">
                            <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-sm font-extrabold {{ $dlTone }}">
                                {!! $pic('clock', 'w-4 h-4') !!} {{ $dlText }}
                            </span>
                            @if($q['deadline'])
                                <span class="text-xs text-gray-400 tabular-nums">Target: {{ $q['deadline'] }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Item: tabel desktop --}}
                <div class="hidden lg:block pl-6 sm:pl-8 pr-5 sm:pr-7 py-4">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-[10px] uppercase tracking-wider text-gray-400">
                                <th class="py-2 font-bold">Item / Spesifikasi &amp; SLA</th>
                                <th class="py-2 font-bold text-right w-28">Butuh</th>
                                <th class="py-2 font-bold text-right w-44">Stok Gudang</th>
                                <th class="py-2 font-bold text-right w-32">Ketersediaan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-ice-100">
                            @foreach($q['items'] as $it)
                                <tr>
                                    <td class="py-3 pr-4">
                                        <p class="text-base font-extrabold text-brand-950">{{ $it['service'] }}</p>
                                        <p class="mt-0.5 text-sm text-gray-500">{{ $it['material'] }} • {{ $it['size'] }} • Qty {{ $it['quantity'] }}</p>
                                        <div class="mt-1.5 flex flex-wrap items-center gap-2">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold {{ $it['sla_mult'] > 1 ? 'bg-red-100 text-red-700' : 'bg-signal-100 text-signal-700' }}">
                                                {!! $pic($it['sla_mult'] > 1 ? 'bolt' : 'clock', 'w-3 h-3') !!}
                                                {{ $it['sla_name'] }} · {{ $it['sla_days'] }} hari · ×{{ rtrim(rtrim(number_format($it['sla_mult'], 2, '.', ''), '0'), '.') }}
                                            </span>
                                            @if($it['notes'])
                                                <span class="inline-flex items-center gap-1.5 text-[11px] text-gray-500 max-w-[22rem] truncate" title="{{ $it['notes'] }}">
                                                    {!! $pic('note', 'w-3 h-3') !!} {{ $it['notes'] }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-3 text-right">
                                        <p class="text-base font-extrabold text-brand-700 tabular-nums">{{ number_format($it['need'], 2) }}</p>
                                        <p class="text-[11px] text-gray-400">{{ $it['unit'] }}</p>
                                    </td>
                                    <td class="py-3 text-right">
                                        <p class="text-xs text-gray-500 tabular-nums">Total {{ number_format($it['stock'], 2) }}</p>
                                        <p class="text-xs text-amber-600 tabular-nums">Reserve {{ number_format($it['reserved'], 2) }}</p>
                                        <p class="text-sm font-extrabold tabular-nums {{ $it['net'] <= 0 ? 'text-red-600' : 'text-brand-800' }}">Sisa {{ number_format($it['net'], 2) }}</p>
                                    </td>
                                    <td class="py-3 text-right">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-extrabold rounded-full {{ $it['short'] ? 'bg-red-100 text-red-700' : 'bg-signal-100 text-signal-700' }}">
                                            {!! $pic($it['short'] ? 'alert' : 'check', 'w-3.5 h-3.5') !!} {{ $it['short'] ? 'Kurang' : 'Aman' }}
                                        </span>
                                        <div class="mt-2 ml-auto w-24 h-1.5 rounded-full bg-ice-200 overflow-hidden">
                                            <div class="h-full rounded-full {{ $it['short'] ? 'bg-red-500' : 'bg-brand-600' }}" style="width: {{ max(4, $it['ratio']) }}%"></div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Item: kartu mobile --}}
                <div class="lg:hidden pl-6 pr-5 py-4 space-y-3">
                    @foreach($q['items'] as $it)
                        <div class="rounded-2xl border border-ice-200 bg-ice-50/60 p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-base font-extrabold text-brand-950">{{ $it['service'] }}</p>
                                    <p class="mt-0.5 text-sm text-gray-500">{{ $it['material'] }} • {{ $it['size'] }} • Qty {{ $it['quantity'] }}</p>
                                </div>
                                <span class="shrink-0 inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-extrabold rounded-full {{ $it['short'] ? 'bg-red-100 text-red-700' : 'bg-signal-100 text-signal-700' }}">
                                    {!! $pic($it['short'] ? 'alert' : 'check', 'w-3 h-3') !!} {{ $it['short'] ? 'Kurang' : 'Aman' }}
                                </span>
                            </div>
                            <div class="mt-3 grid grid-cols-2 gap-3 text-sm">
                                <div>
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Butuh</p>
                                    <p class="font-extrabold text-brand-700 tabular-nums">{{ number_format($it['need'], 2) }} {{ $it['unit'] }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Sisa Gudang</p>
                                    <p class="font-extrabold tabular-nums {{ $it['net'] <= 0 ? 'text-red-600' : 'text-brand-800' }}">{{ number_format($it['net'], 2) }} {{ $it['unit'] }}</p>
                                </div>
                            </div>
                            <div class="mt-3 h-1.5 rounded-full bg-ice-200 overflow-hidden">
                                <div class="h-full rounded-full {{ $it['short'] ? 'bg-red-500' : 'bg-brand-600' }}" style="width: {{ max(4, $it['ratio']) }}%"></div>
                            </div>
                            <span class="mt-3 inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold {{ $it['sla_mult'] > 1 ? 'bg-red-100 text-red-700' : 'bg-signal-100 text-signal-700' }}">
                                {!! $pic($it['sla_mult'] > 1 ? 'bolt' : 'clock', 'w-3 h-3') !!} {{ $it['sla_name'] }} · {{ $it['sla_days'] }} hari
                            </span>
                        </div>
                    @endforeach
                </div>

                {{-- Aksi --}}
                <div class="pl-6 sm:pl-8 pr-5 sm:pr-7 py-4 border-t border-ice-200 bg-ice-50/60 flex flex-wrap items-center justify-end gap-2.5">
                    <a href="{{ route('admin.orders.show', $q['id']) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-ice-300 text-brand-800 text-sm font-bold hover:bg-ice-100 transition-colors">
                        {!! $pic('eye', 'w-4 h-4') !!} Detail Pesanan
                    </a>

                    <button type="button" wire:click="openIncident({{ $q['id'] }})"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-50 border border-amber-300 text-amber-800 text-sm font-extrabold hover:bg-amber-100 transition-colors">
                        {!! $pic('alert', 'w-4 h-4') !!} Lapor Kendala
                    </button>

                    @if($q['stage'] === 'awaiting')
                         @if($isBlocked)
                            @if(($q['incoming_count'] ?? 0) > 0)
                                <a href="{{ route('admin.goods-receipt', ['pr_id' => $q['incoming'][0]['pr_id']]) }}"
                                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-signal-400 text-brand-950 text-sm font-extrabold hover:bg-signal-500 shadow-md shadow-signal-400/25 transition-all">
                                    {!! $pic('truck', 'w-4 h-4') !!} Terima Barang — {{ $q['incoming'][0]['number'] }}
                                </a>
                                <span class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-100 border border-amber-300 text-amber-800 text-sm font-extrabold">
                                    {!! $pic('clock', 'w-4 h-4') !!} Menunggu kedatangan bahan
                                </span>
                            @else
                                <a href="{{ route('admin.production.requests', ['order' => $q['id']]) }}"
                                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-red-600 text-white text-sm font-extrabold hover:bg-red-700 shadow-md shadow-red-500/25 transition-all">
                                    {!! $pic('cart', 'w-4 h-4') !!} Ajukan PR Bahan Ini
                                </a>
                                <button type="button" disabled
                                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-ice-200 text-gray-400 text-sm font-extrabold cursor-not-allowed">
                                    {!! $pic('check', 'w-4 h-4') !!} Tertahan – Stok Kurang
                                </button>
                            @endif
                            <button type="button" wire:click="confirmPrint({{ $q['id'] }})"
                                    wire:confirm="Ambil bahan dari gudang dan mulai cetak pesanan ini?"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-600 text-signal-300 text-sm font-extrabold hover:bg-brand-700 shadow-md shadow-brand-900/25 transition-all disabled:opacity-60"
                                    wire:loading.attr="disabled" wire:target="confirmPrint({{ $q['id'] }})">
                                <span wire:loading.remove wire:target="confirmPrint({{ $q['id'] }})" class="inline-flex items-center gap-2">{!! $pic('printer', 'w-4 h-4') !!} Konfirmasi Ambil Bahan &amp; Cetak</span>
                                <span wire:loading wire:target="confirmPrint({{ $q['id'] }})">Memproses...</span>
                            </button>
                        @endif
                    @elseif($q['stage'] === 'processing')
                        <button type="button" wire:click="sendToQc({{ $q['id'] }})"
                                wire:confirm="Cetak selesai? Kirim pesanan ini ke Quality Control."
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-600 text-signal-300 text-sm font-extrabold hover:bg-brand-700 shadow-md shadow-brand-900/25 transition-all disabled:opacity-60"
                                wire:loading.attr="disabled" wire:target="sendToQc({{ $q['id'] }})">
                            <span wire:loading.remove wire:target="sendToQc({{ $q['id'] }})" class="inline-flex items-center gap-2">{!! $pic('shield', 'w-4 h-4') !!} Cetak Selesai → QC</span>
                            <span wire:loading wire:target="sendToQc({{ $q['id'] }})">Memproses...</span>
                        </button>
                    @else
                        <button type="button" wire:click="passQc({{ $q['id'] }})"
                                wire:confirm="Hasil cetak sudah sesuai? Luluskan QC dan tandai siap kirim."
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-signal-400 text-brand-950 text-sm font-extrabold hover:bg-signal-500 shadow-md shadow-signal-400/25 transition-all disabled:opacity-60"
                                wire:loading.attr="disabled" wire:target="passQc({{ $q['id'] }})">
                            <span wire:loading.remove wire:target="passQc({{ $q['id'] }})" class="inline-flex items-center gap-2">{!! $pic('truck', 'w-4 h-4') !!} Lulus QC → Siap Kirim</span>
                            <span wire:loading wire:target="passQc({{ $q['id'] }})">Memproses...</span>
                        </button>
                    @endif
                </div>
            </article>
        @empty
            <div class="forest-hero relative overflow-hidden rounded-[2rem] p-14 text-center text-white pq-rise">
                <div class="forest-dots absolute inset-0 opacity-45"></div>
                <div class="absolute -top-20 -right-16 w-64 h-64 rounded-full bg-signal-400/10 blur-3xl"></div>
                <div class="relative z-10">
                    <span class="inline-flex w-20 h-20 rounded-full bg-white/5 border border-signal-400/30 text-signal-300 items-center justify-center mx-auto">{!! $pic('check', 'w-9 h-9') !!}</span>
                    <p class="mt-6 text-2xl font-extrabold">{{ $hasFilter ? 'Tidak ada antrian yang cocok' : 'Antrian produksi bersih' }}</p>
                    <p class="mt-2 text-base text-emerald-100/70">{{ $hasFilter ? 'Coba ubah kata kunci atau saringan tahap.' : 'Semua pesanan sudah diproses. Tidak ada pekerjaan tertunda.' }}</p>
                    @if($hasFilter)
                        <button type="button" wire:click="clearFilters" class="mt-7 inline-flex items-center gap-2 px-6 py-3 rounded-full bg-signal-400 text-brand-950 text-base font-extrabold hover:bg-signal-500 transition-colors">
                            {!! $pic('undo', 'w-4 h-4') !!} Reset Saringan
                        </button>
                    @endif
                </div>
            </div>
        @endforelse
    </div>

    {{-- ===== MODAL KENDALA ===== --}}
    @if($incidentOrderId)
        <div class="fixed inset-0 z-[60] flex items-end sm:items-center justify-center p-0 sm:p-4 bg-brand-950/60 backdrop-blur-sm" wire:click.self="closeIncident">
            <div class="w-full sm:max-w-lg bg-ice-50 rounded-t-3xl sm:rounded-3xl shadow-2xl overflow-hidden">
                <div class="forest-hero relative overflow-hidden px-6 py-5 flex items-start justify-between gap-4">
                    <div class="forest-dots absolute inset-0 opacity-40"></div>
                    <div class="relative z-10 min-w-0">
                        <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-signal-300">Laporan Produksi</p>
                        <h3 class="mt-1 text-xl font-extrabold text-white tracking-tight">Lapor Kendala</h3>
                    </div>
                    <button type="button" wire:click="closeIncident" class="relative z-10 shrink-0 w-9 h-9 flex items-center justify-center rounded-xl bg-white/10 text-white hover:bg-white/20 transition-colors">{!! $pic('x', 'w-5 h-5') !!}</button>
                </div>

                <form wire:submit="submitIncident" class="p-6 space-y-5">
                    <div>
                        <label class="block text-base font-extrabold text-brand-950 mb-2">Jenis Kendala</label>
                        <select wire:model="incidentReason" class="pq-select w-full pl-4 pr-10 py-3.5 text-base border border-ice-300 rounded-2xl bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25 focus:border-brand-500 cursor-pointer">
                            @foreach($reasons as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-base font-extrabold text-brand-950 mb-2">Detail Kendala</label>
                        <textarea wire:model="incidentNote" rows="4" maxlength="500"
                                  placeholder="Contoh: Flexi China 280gr sisa 100 m², butuh 1.000 m². Sudah ajukan PR ke supplier."
                                  class="pq-scroll w-full px-4 py-3.5 text-base leading-relaxed border border-ice-300 rounded-2xl bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25 focus:border-brand-500 resize-y @error('incidentNote') border-red-400 bg-red-50 @enderror"></textarea>
                        @error('incidentNote') <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
                        <p class="mt-2 text-sm text-gray-400">Pesanan akan ditandai prioritas tinggi dan catatan ini tersimpan di riwayat pesanan.</p>
                    </div>
                    <div class="flex flex-wrap gap-2.5">
                        <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-2xl bg-brand-600 text-white text-base font-extrabold hover:bg-brand-700 transition-colors disabled:opacity-60" wire:loading.attr="disabled" wire:target="submitIncident">
                            <span wire:loading.remove wire:target="submitIncident" class="inline-flex items-center gap-2">{!! $pic('flag', 'w-4 h-4') !!} Kirim Laporan</span>
                            <span wire:loading wire:target="submitIncident">Mengirim...</span>
                        </button>
                        <button type="button" wire:click="closeIncident" class="px-5 py-3.5 rounded-2xl bg-ice-200 text-brand-800 text-base font-bold hover:bg-ice-300 transition-colors">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>