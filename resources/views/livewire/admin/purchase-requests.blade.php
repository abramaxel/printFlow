@php
    $Q = [
        'check'  => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        'x'      => 'M6 18L18 6M6 6l12 12',
        'clock'  => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        'alert'  => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
        'cart'   => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z',
        'truck'  => 'M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0 2 2 0 00-4 0zm10 0a2 2 0 104 0 2 2 0 00-4 0z',
        'box'    => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
        'search' => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z',
        'copy'   => 'M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z',
        'eye'    => 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z',
        'money'  => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'user'   => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
        'cal'    => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
        'arrow'  => 'M14 5l7 7m0 0l-7 7m7-7H3',
        'left'   => 'M15 19l-7-7 7-7',
        'right'  => 'M9 5l7 7-7 7',
        'bolt'   => 'M13 10V3L4 14h7v7l9-11h-7z',
        'shield' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
        'note'   => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'fire'   => 'M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.24 17 7.2c1 2.16 1.5 4.3 1.5 5.8 0 1.5-.5 3-1.5 4.2z M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z',
        'undo'   => 'M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6',
        'send'   => 'M12 19l9 2-9-18-9 18 9-2zm0 0v-8',
        'list'   => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
        'up'     => 'M5 15l7-7 7 7',
        'down'   => 'M19 9l-7 7-7-7',
        'trend'  => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6',
    ];
    $qic = fn($k, $c = 'w-5 h-5') => '<svg class="' . $c . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="' . ($Q[$k] ?? $Q['box']) . '"/></svg>';

    $ST = [
        'pending'   => ['Menunggu Keputusan', 'bg-amber-100 text-amber-800', 'clock'],
        'approved'  => ['Disetujui', 'bg-brand-200 text-brand-900', 'check'],
        'ordered'   => ['Dipesan ke Supplier', 'bg-brand-100 text-brand-700', 'cart'],
        'received'  => ['Barang Diterima', 'bg-signal-400 text-brand-950', 'truck'],
        'cancelled' => ['Ditolak / Batal', 'bg-ice-300 text-gray-600', 'x'],
    ];
    $UG = [
        'critical' => ['Kritis', 'bg-red-600 text-white', 'fire'],
        'warning'  => ['Warning', 'bg-amber-400 text-brand-950', 'alert'],
        'normal'   => ['Normal', 'bg-ice-200 text-brand-800', 'clock'],
    ];
    $chips = [
        ['pending', 'Perlu Keputusan', $stats['pending'], 'clock'],
        ['critical', 'Kritis', $stats['critical'], 'fire'],
        ['approved', 'Disetujui / Dipesan', $stats['approved'], 'check'],
        ['received', 'Diterima', $stats['received'], 'truck'],
        ['cancelled', 'Ditolak', $stats['cancelled'], 'x'],
    ];
@endphp

<div class="w-full space-y-6">
    <style>
        .danger-hero {
            background:
                radial-gradient(ellipse at top left, rgba(248,113,113,.45), transparent 58%),
                radial-gradient(ellipse at bottom right, rgba(251,191,36,.14), transparent 55%),
                linear-gradient(135deg, #991b1b 0%, #7f1d1d 48%, #450a0a 100%);
        }
        .waiting-hero {
            background:
                radial-gradient(ellipse at top left, rgba(251,191,36,.32), transparent 58%),
                radial-gradient(ellipse at bottom right, rgba(200,236,63,.12), transparent 55%),
                linear-gradient(135deg, #78350f 0%, #4d2c0a 55%, #291505 100%);
        }
        .danger-dots { background-image: radial-gradient(rgba(254,202,202,.20) 1px, transparent 1px); background-size: 20px 20px; }
        .hazard-bar { background-image: repeating-linear-gradient(45deg, #dc2626 0 9px, #fee2e2 9px 18px); }
        .hazard-bar-amber { background-image: repeating-linear-gradient(45deg, #b45309 0 9px, #fde68a 9px 18px); }

        @keyframes pqRise { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
        .pq-rise { animation: pqRise .5s cubic-bezier(.2,.7,.2,1) both; }
        @keyframes pqSiren { 0%,100% { box-shadow: 0 0 0 0 rgba(254,202,202,.55); } 50% { box-shadow: 0 0 0 13px rgba(254,202,202,0); } }
        .pq-siren { animation: pqSiren 1.9s ease-out infinite; }
        @keyframes pqSirenRed { 0%,100% { box-shadow: 0 0 0 0 rgba(220,38,38,.45); } 50% { box-shadow: 0 0 0 12px rgba(220,38,38,0); } }
        .pq-siren-red { animation: pqSirenRed 2s ease-out infinite; }
        @keyframes pqAurora { 0%,100% { transform: translate3d(0,0,0) scale(1); } 50% { transform: translate3d(26px,-16px,0) scale(1.09); } }
        .pq-aurora { animation: pqAurora 16s ease-in-out infinite; }
        @keyframes pqStripe { 0% { background-position: 0 0; } 100% { background-position: 36px 0; } }
        .pq-stripe { animation: pqStripe 1.4s linear infinite; }
        @keyframes pqTick { 0%,100% { opacity: 1; } 50% { opacity: .35; } }
        .pq-tick { animation: pqTick 1.8s ease-in-out infinite; }
        @keyframes pqBarIn { from { opacity: 0; transform: translateY(24px); } to { opacity: 1; transform: translateY(0); } }
        .pq-bar-in { animation: pqBarIn .32s cubic-bezier(.2,.7,.2,1) both; }

        .pq-scroll::-webkit-scrollbar { width: 8px; height: 8px; }
        .pq-scroll::-webkit-scrollbar-track { background: #e2edf0; border-radius: 8px; }
        .pq-scroll::-webkit-scrollbar-thumb { background: #1d6247; border-radius: 8px; }
        .pq-scroll::-webkit-scrollbar-thumb:hover { background: #0a3f33; }
        .pq-select { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' stroke='%230a3f33' stroke-width='2.5' viewBox='0 0 24 24'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right .85rem center; background-size: 1rem; }
        #antrian-keputusan, #buku-besar { scroll-margin-top: 108px; }
        @media (prefers-reduced-motion: reduce) {
            .pq-rise, .pq-siren, .pq-siren-red, .pq-aurora, .pq-stripe, .pq-tick, .pq-bar-in { animation: none !important; }
        }
    </style>

    {{-- ===== TOAST ===== --}}
    @if(session('pr_success') || session('pr_error'))
        @php $ok = (bool) session('pr_success'); @endphp
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5200)" x-show="show" x-cloak
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-3"
             class="fixed top-24 right-4 sm:right-6 z-[80] max-w-sm" role="status" aria-live="polite">
            <div class="relative overflow-hidden rounded-2xl px-5 py-4 shadow-2xl flex items-start gap-3 {{ $ok ? 'forest-hero shadow-brand-950/40' : 'danger-hero shadow-red-950/40' }}">
                <div class="{{ $ok ? 'forest-dots' : 'danger-dots' }} absolute inset-0 opacity-45"></div>
                <span class="relative z-10 shrink-0 w-9 h-9 rounded-xl flex items-center justify-center {{ $ok ? 'bg-signal-400 text-brand-950' : 'bg-white text-red-700' }}">
                    {!! $qic($ok ? 'check' : 'alert', 'w-5 h-5') !!}
                </span>
                <p class="relative z-10 flex-1 text-base font-bold text-white">{{ session('pr_success') ?? session('pr_error') }}</p>
                <button type="button" @click="show = false" class="relative z-10 shrink-0 text-white/70 hover:text-white transition-colors">{!! $qic('x', 'w-4 h-4') !!}</button>
            </div>
        </div>
    @endif

    {{-- ===== HERO ===== --}}
    <div class="forest-hero relative overflow-hidden rounded-3xl text-white px-6 py-6 sm:px-8 sm:py-7 shadow-lg shadow-brand-950/25 pq-rise">
        <div class="forest-dots absolute inset-0 opacity-45"></div>
        <div class="pq-aurora absolute -top-24 -left-16 w-72 h-72 rounded-full bg-emerald-500/15 blur-3xl"></div>
        <div class="absolute -bottom-24 right-0 w-64 h-64 rounded-full bg-signal-400/10 blur-3xl"></div>
        <div class="absolute inset-y-0 left-1/2 w-28 rotate-12 bg-gradient-to-b from-signal-400/0 via-signal-400/10 to-signal-400/0 blur-2xl"></div>

        <div class="relative z-10 flex flex-col xl:flex-row xl:items-center justify-between gap-6">
            <div class="min-w-0">
                <p class="text-xs font-bold uppercase tracking-[0.24em] text-signal-300">Pusat Persetujuan Owner</p>
                <h1 class="mt-2 text-3xl font-extrabold tracking-tight">Purchase Request</h1>
                <p class="mt-2 text-base text-emerald-100/75 max-w-2xl">
                    Tinjau pengajuan bahan dari produksi &amp; MRP, lihat dampak ke stok, lalu setujui atau tolak dengan alasan.
                    @if(!$canApprove)
                        <b class="text-amber-300">Role Anda hanya dapat melihat — persetujuan dipegang owner.</b>
                    @endif
                </p>
            </div>
            <div class="flex flex-col sm:flex-row items-stretch gap-3 shrink-0">
                <div class="rounded-2xl bg-signal-400 text-brand-950 px-5 py-4 shadow-lg shadow-signal-400/20">
                    <p class="text-[10px] font-bold uppercase tracking-[0.2em]">Nilai Perlu Keputusan</p>
                    <p class="mt-1 text-2xl font-extrabold tabular-nums">Rp {{ number_format($stats['value'], 0, ',', '.') }}</p>
                    <p class="text-[11px] font-bold mt-0.5">{{ $stats['pending'] }} PR • {{ $stats['critical'] }} kritis</p>
                </div>
                <div class="flex flex-col gap-2">
                    <a href="{{ route('admin.goods-receipt') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 border border-white/15 text-sm font-bold hover:bg-white/20 transition-colors">
                        {!! $qic('truck', 'w-4 h-4') !!} Penerimaan Barang
                    </a>
                    <a href="{{ route('admin.production.requests') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 border border-white/15 text-sm font-bold hover:bg-white/20 transition-colors">
                        {!! $qic('send', 'w-4 h-4') !!} Halaman Pengajuan
                    </a>
                </div>
            </div>
        </div>

        <div class="relative z-10 mt-7 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
            @foreach($chips as $c)
                @php
                    $isCrit = $c[0] === 'critical';
                    $active = $isCrit ? ($statusFilter === 'pending' && $urgencyFilter === 'critical') : ($statusFilter === $c[0] && $urgencyFilter === 'all');
                @endphp
                <button type="button"
                        wire:click="{{ $isCrit ? "setStatusFilter('pending')" : "setStatusFilter('{$c[0]}')" }}"
                        @if($isCrit) wire:click="setUrgencyFilter('critical')" @else wire:click="setUrgencyFilter('all')" @endif
                        class="text-left rounded-2xl px-4 py-3 border transition-all hover:-translate-y-0.5 {{ $active ? 'bg-signal-400 border-signal-400 text-brand-950' : ($isCrit && $c[2] > 0 ? 'bg-red-600/85 border-red-300/50 pq-siren-red' : 'bg-white/5 border-white/10 hover:bg-white/10') }}">
                    <span class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-[0.16em] {{ $active ? 'text-brand-900/70' : ($isCrit && $c[2] > 0 ? 'text-red-50' : 'text-emerald-100/60') }}">
                        {!! $qic($c[3], 'w-3.5 h-3.5') !!} {{ $c[1] }}
                    </span>
                    <span class="mt-1 block text-2xl font-extrabold tabular-nums">{{ $c[2] }}</span>
                </button>
            @endforeach
        </div>

        @if($stats['pending'] > 0)
            <div class="relative z-10 mt-4 flex flex-wrap items-center gap-2.5">
                @if($stats['overdue'] > 0)
                    <span class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-red-600 text-white text-sm font-extrabold">
                        {!! $qic('alert', 'w-4 h-4') !!} {{ $stats['overdue'] }} PR melewati tanggal dibutuhkan
                    </span>
                @endif
                @if($stats['oldest'] > 0)
                    <span class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white/10 border border-white/15 text-sm font-bold text-emerald-100/85">
                        {!! $qic('clock', 'w-4 h-4') !!} Pengajuan tertua {{ $stats['oldest'] }} hari
                    </span>
                @endif
                <button type="button" onclick="document.getElementById('antrian-keputusan')?.scrollIntoView({behavior:'smooth',block:'start'})"
                        class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white/10 border border-white/15 text-sm font-bold hover:bg-white/20 transition-colors">
                    {!! $qic('down', 'w-4 h-4') !!} Tinjau sekarang
                </button>
            </div>
        @endif
    </div>

    {{-- ===== ANTRIAN KEPUTUSAN ===== --}}
    <div id="antrian-keputusan" class="relative overflow-hidden rounded-[2rem] shadow-xl shadow-brand-950/10 pq-rise">
        @php $hasCritical = $stats['critical'] > 0; @endphp
        <div class="relative overflow-hidden {{ count($pending) ? ($hasCritical ? 'danger-hero' : 'waiting-hero') : 'forest-hero' }} px-6 py-5">
            <div class="{{ count($pending) ? 'danger-dots' : 'forest-dots' }} absolute inset-0 opacity-55"></div>
            @if(count($pending))
                <div class="absolute top-0 left-0 right-0 h-1.5 {{ $hasCritical ? 'hazard-bar pq-stripe' : 'hazard-bar-amber' }}"></div>
            @endif

            <div class="relative z-10 flex flex-wrap items-center justify-between gap-4 pt-1">
                <div class="flex items-center gap-4 min-w-0">
                    <span class="shrink-0 w-12 h-12 rounded-2xl flex items-center justify-center {{ count($pending) ? ($hasCritical ? 'bg-white text-red-700 pq-siren' : 'bg-amber-300 text-amber-950') : 'bg-signal-400 text-brand-950' }}">
                        {!! $qic(count($pending) ? ($hasCritical ? 'fire' : 'clock') : 'check', 'w-6 h-6') !!}
                    </span>
                    <div class="min-w-0">
                        <p class="text-[11px] font-bold uppercase tracking-[0.22em] {{ count($pending) ? ($hasCritical ? 'text-red-100' : 'text-amber-200') : 'text-signal-300' }}">
                            {{ count($pending) ? ($hasCritical ? 'Prioritas Tinggi — Perlu Keputusan' : 'Menunggu Keputusan Anda') : 'Tidak Ada Pengajuan Tertunda' }}
                        </p>
                        <h2 class="mt-1 text-xl font-extrabold text-white tracking-tight">Antrian Persetujuan</h2>
                        <p class="text-sm {{ count($pending) ? 'text-red-50/85' : 'text-emerald-100/75' }}">
                            {{ count($pending) ? count($pending) . ' PR menunggu • nilai Rp ' . number_format($stats['value'], 0, ',', '.') : 'Semua pengajuan sudah diproses.' }}
                        </p>
                    </div>
                </div>

                @if(count($pending) && $canApprove)
                    <div class="flex flex-wrap items-center gap-2.5">
                        <button type="button" wire:click="selectAllPending"
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/12 border border-white/20 text-sm font-bold text-white hover:bg-white/20 transition-colors">
                            {!! $qic('list', 'w-4 h-4') !!} Pilih semua
                        </button>
                        <button type="button" onclick="document.getElementById('buku-besar')?.scrollIntoView({behavior:'smooth',block:'start'})"
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white text-brand-900 text-sm font-extrabold hover:bg-ice-100 transition-colors">
                            {!! $qic('note', 'w-4 h-4') !!} Buku besar PR
                        </button>
                    </div>
                @endif
            </div>
        </div>

        @if(count($pending))
            <div class="{{ $hasCritical ? 'bg-red-50/70 border-red-200' : 'bg-amber-50/60 border-amber-200' }} border-x-2 border-b-2 rounded-b-[2rem] p-5 grid grid-cols-1 lg:grid-cols-2 gap-4">
                @foreach($pending as $p)
                    @php
                        $crit = $p['urgency'] === 'critical';
                        $sel = in_array($p['id'], $selected, true);
                        $card = $crit
                            ? 'bg-gradient-to-br from-red-700 via-red-600 to-red-800 border-red-900 text-white shadow-red-900/30'
                            : 'bg-white border-amber-300 text-brand-950 shadow-amber-200/50';
                        $dark = $crit;
                        $lbl = $dark ? 'text-red-100/75' : 'text-gray-400';
                        $sub = $dark ? 'text-red-100/70' : 'text-gray-500';
                        $inner = $dark ? 'bg-white/12 border-white/25' : 'bg-ice-50 border-ice-200';
                    @endphp
                    <div class="relative overflow-hidden rounded-2xl border-2 {{ $card }} {{ $sel ? 'ring-4 ring-signal-400' : '' }} p-5 pt-6 shadow-lg transition-all hover:-translate-y-1">
                        <div class="absolute top-0 left-0 right-0 h-1.5 {{ $crit ? 'hazard-bar pq-stripe' : 'hazard-bar-amber' }}"></div>

                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3 min-w-0">
                                @if($canApprove)
                                    <label class="shrink-0 mt-0.5 cursor-pointer">
                                        <input type="checkbox" wire:click="toggleSelect({{ $p['id'] }})" @checked($sel)
                                               class="w-5 h-5 rounded border-2 {{ $dark ? 'border-white/50 bg-white/10' : 'border-ice-400' }} text-signal-500 focus:ring-signal-400 cursor-pointer">
                                    </label>
                                @endif
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="text-lg font-extrabold tracking-tight">{{ $p['number'] }}</span>
                                        <button type="button" onclick="copyText('{{ $p['number'] }}', this)" title="Salin nomor PR"
                                                class="w-7 h-7 flex items-center justify-center rounded-lg {{ $dark ? 'text-white/70 hover:bg-white/20' : 'text-brand-500 hover:bg-brand-600 hover:text-signal-300' }} transition-colors">
                                            {!! $qic('copy', 'w-3.5 h-3.5') !!}
                                        </button>
                                    </div>
                                    <p class="mt-0.5 text-sm {{ $sub }}">{{ $p['created'] }} • {{ $p['age_days'] }} hari lalu</p>
                                </div>
                            </div>
                            <span class="shrink-0 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider {{ $dark ? 'bg-white text-red-700' : $UG[$p['urgency']][1] }}">
                                {!! $qic($UG[$p['urgency']][2] ?? 'clock', 'w-3 h-3') !!} {{ $UG[$p['urgency']][0] ?? $p['urgency'] }}
                            </span>
                        </div>

                        <div class="mt-4 rounded-xl border {{ $inner }} px-4 py-3">
                            <p class="text-base font-extrabold {{ $dark ? 'text-white' : 'text-brand-950' }}">{{ $p['material'] }}</p>
                            <div class="mt-2.5 grid grid-cols-3 gap-2 text-center">
                                <div>
                                    <p class="text-[9px] font-bold uppercase tracking-wider {{ $lbl }}">Diminta</p>
                                    <p class="mt-0.5 text-base font-extrabold tabular-nums {{ $dark ? 'text-signal-300' : 'text-brand-700' }}">{{ number_format($p['qty'], 2) }}</p>
                                    <p class="text-[10px] {{ $sub }}">{{ $p['unit'] }}</p>
                                </div>
                                <div>
                                    <p class="text-[9px] font-bold uppercase tracking-wider {{ $lbl }}">Sisa Kini</p>
                                    <p class="mt-0.5 text-base font-extrabold tabular-nums {{ $p['net'] <= 0 ? ($dark ? 'text-white' : 'text-red-600') : ($dark ? 'text-red-100' : 'text-brand-900') }}">{{ number_format($p['net'], 2) }}</p>
                                    <p class="text-[10px] {{ $sub }}">min {{ number_format($p['min'], 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-[9px] font-bold uppercase tracking-wider {{ $lbl }}">Setelah Terima</p>
                                    <p class="mt-0.5 text-base font-extrabold tabular-nums {{ $dark ? 'text-signal-300' : 'text-signal-600' }}">{{ number_format($p['after'], 2) }}</p>
                                    <p class="text-[10px] {{ $sub }}">{{ $p['after_coverage'] }}% ROP</p>
                                </div>
                            </div>
                            <div class="mt-3 flex gap-1.5">
                                <div class="flex-1 h-2 rounded-full overflow-hidden {{ $dark ? 'bg-white/20' : 'bg-ice-200' }}">
                                    <div class="h-full rounded-full {{ $dark ? 'bg-white' : 'bg-red-500' }}" style="width: {{ max(3, $p['coverage']) }}%"></div>
                                </div>
                                <div class="flex-1 h-2 rounded-full overflow-hidden {{ $dark ? 'bg-white/20' : 'bg-ice-200' }}">
                                    <div class="h-full rounded-full {{ $dark ? 'bg-signal-400' : 'bg-signal-500' }}" style="width: {{ max(3, $p['after_coverage']) }}%"></div>
                                </div>
                            </div>
                            <p class="mt-1.5 text-[10px] font-bold {{ $sub }}">kiri: stok sekarang • kanan: proyeksi setelah barang datang</p>
                        </div>

                        <div class="mt-3 grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-wider {{ $lbl }}">Estimasi Biaya</p>
                                <p class="mt-0.5 text-lg font-extrabold tabular-nums {{ $dark ? 'text-white' : 'text-brand-950' }}">Rp {{ number_format($p['estimate'], 0, ',', '.') }}</p>
                                <p class="text-[10px] {{ $sub }}">
                                    @if($p['estimate_auto'])
                                        estimasi sistem • Rp {{ number_format($p['unit_price'], 0, ',', '.') }}/{{ $p['unit'] }}
                                    @else
                                        dari pengaju
                                    @endif
                                </p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-wider {{ $lbl }}">Dibutuhkan</p>
                                <p class="mt-0.5 text-lg font-extrabold tabular-nums {{ $p['risk'] ? ($dark ? 'text-amber-300' : 'text-red-600') : ($dark ? 'text-white' : 'text-brand-950') }}">{{ $p['needed'] ?? '—' }}</p>
                                <p class="text-[10px] {{ $sub }}">lead {{ $p['lead'] }} hari{{ $p['risk'] ? ' • terlalu mepet' : '' }}</p>
                            </div>
                        </div>

                        <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-1.5 text-xs {{ $sub }}">
                            <span class="inline-flex items-center gap-1.5">{!! $qic('user', 'w-3.5 h-3.5') !!} {{ $p['requester'] }}</span>
                            @if($p['reason'])<span class="inline-flex items-center gap-1.5">{!! $qic('note', 'w-3.5 h-3.5') !!} {{ $p['reason'] }}</span>@endif
                            @if($p['risk'])
                                <span class="inline-flex items-center gap-1.5 font-extrabold {{ $dark ? 'text-amber-300' : 'text-red-600' }}">{!! $qic('alert', 'w-3.5 h-3.5') !!} Lead time melewati target</span>
                            @endif
                        </div>

                        <div class="mt-4 flex flex-wrap gap-2">
                            <button type="button" wire:click="openDetail({{ $p['id'] }})"
                                    class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-sm font-bold transition-colors {{ $dark ? 'bg-white/12 border border-white/25 text-white hover:bg-white/20' : 'bg-ice-100 text-brand-800 hover:bg-ice-200' }}">
                                {!! $qic('eye', 'w-4 h-4') !!} Detail
                            </button>
                            @if($canApprove)
                                <button type="button" wire:click="openReject({{ $p['id'] }})"
                                        class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-sm font-extrabold transition-colors {{ $dark ? 'bg-white text-red-700 hover:bg-red-50' : 'bg-red-50 border border-red-300 text-red-600 hover:bg-red-100' }}">
                                    {!! $qic('x', 'w-4 h-4') !!} Tolak
                                </button>
                                <button type="button" wire:click="approve({{ $p['id'] }})"
                                        wire:confirm="Setujui {{ $p['number'] }} — {{ number_format($p['qty'], 2) }} {{ $p['unit'] }} {{ $p['material'] }} (Rp {{ number_format($p['estimate'], 0, ',', '.') }})?"
                                        class="flex-1 min-w-[10rem] inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-signal-400 text-brand-950 text-sm font-extrabold hover:bg-signal-500 shadow-md shadow-signal-400/25 transition-all disabled:opacity-60"
                                        wire:loading.attr="disabled" wire:target="approve({{ $p['id'] }})">
                                    <span wire:loading.remove wire:target="approve({{ $p['id'] }})" class="inline-flex items-center gap-2">{!! $qic('check', 'w-4 h-4') !!} Setujui</span>
                                    <span wire:loading wire:target="approve({{ $p['id'] }})">Memproses...</span>
                                </button>
                            @else
                                <span class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold {{ $dark ? 'bg-white/10 text-white/70' : 'bg-ice-100 text-gray-500' }}">
                                    {!! $qic('shield', 'w-4 h-4') !!} Menunggu owner
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white border-x border-b border-ice-200 rounded-b-[2rem] p-12 text-center">
                <span class="inline-flex w-16 h-16 rounded-full bg-signal-100 text-signal-600 items-center justify-center mx-auto">{!! $qic('check', 'w-7 h-7') !!}</span>
                <p class="mt-4 text-lg font-extrabold text-brand-950">Tidak ada pengajuan tertunda</p>
                <p class="mt-1 text-base text-gray-500">Semua purchase request sudah diproses. Riwayat lengkap ada di buku besar bawah.</p>
            </div>
        @endif
    </div>

    {{-- ===== BUKU BESAR PR ===== --}}
    <div id="buku-besar" class="space-y-4">
        <div class="sticky top-24 z-20 rounded-[1.75rem] bg-white/85 backdrop-blur-xl border border-ice-200 shadow-xl shadow-brand-950/5 p-4 pq-rise">
            <div class="flex flex-col lg:flex-row gap-3 lg:items-center">
                <div class="relative flex-1 min-w-0">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-brand-400">{!! $qic('search', 'w-4 h-4') !!}</span>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari no. PR, nama bahan, atau pengaju..."
                           class="w-full pl-11 pr-10 py-3 text-base border border-ice-300 rounded-2xl bg-ice-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25 focus:border-brand-500 transition-all">
                    <span wire:loading wire:target="search" class="absolute inset-y-0 right-0 pr-4 flex items-center">
                        <svg class="w-4 h-4 animate-spin text-brand-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.4 0 0 5.4 0 12h4z"/></svg>
                    </span>
                </div>
                <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                    <select wire:model.live="urgencyFilter" class="pq-select pl-4 pr-10 py-3 text-sm font-bold text-brand-800 border border-ice-300 rounded-2xl bg-ice-50 focus:outline-none focus:ring-2 focus:ring-brand-500/25 cursor-pointer">
                        <option value="all">Semua urgensi</option>
                        <option value="critical">Kritis</option>
                        <option value="warning">Warning</option>
                        <option value="normal">Normal</option>
                    </select>
                    <select wire:model.live="sort" class="pq-select pl-4 pr-10 py-3 text-sm font-bold text-brand-800 border border-ice-300 rounded-2xl bg-ice-50 focus:outline-none focus:ring-2 focus:ring-brand-500/25 cursor-pointer">
                        <option value="priority">Urut: Prioritas</option>
                        <option value="needed">Urut: Dibutuhkan</option>
                        <option value="value">Urut: Nilai terbesar</option>
                        <option value="newest">Urut: Terbaru</option>
                        <option value="oldest">Urut: Terlama</option>
                    </select>
                    @if($hasFilter)
                        <button type="button" wire:click="clearFilters" class="inline-flex items-center gap-2 px-4 py-3 rounded-2xl bg-red-50 text-red-600 text-sm font-bold hover:bg-red-100 transition-colors">
                            {!! $qic('undo', 'w-4 h-4') !!} Reset
                        </button>
                    @endif
                </div>
            </div>
            <div class="mt-3 flex flex-wrap gap-2">
                @foreach(['all' => 'Semua', 'pending' => 'Menunggu', 'approved' => 'Disetujui', 'received' => 'Diterima', 'cancelled' => 'Ditolak'] as $k => $l)
                    <button type="button" wire:click="setStatusFilter('{{ $k }}')"
                            class="px-4 py-2 rounded-full text-sm font-extrabold transition-all {{ $statusFilter === $k ? 'bg-brand-600 text-signal-300 shadow-sm shadow-brand-900/25' : 'bg-ice-100 text-brand-800 hover:bg-ice-200' }}">{{ $l }}</button>
                @endforeach
            </div>
        </div>

        <div class="rounded-[2rem] bg-white border border-ice-200 shadow-sm overflow-hidden pq-rise">
            <div class="px-6 py-4 border-b border-ice-200 bg-ice-50 flex flex-wrap items-center justify-between gap-2">
                <p class="text-sm text-gray-500">
                    Menampilkan <b class="text-brand-950 tabular-nums">{{ $paginator->firstItem() ?? 0 }}–{{ $paginator->lastItem() ?? 0 }}</b>
                    dari <b class="text-brand-950 tabular-nums">{{ $paginator->total() }}</b> PR
                </p>
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-ice-200 text-brand-800 text-xs font-bold">
                    {!! $qic('shield', 'w-3.5 h-3.5') !!} {{ $canApprove ? 'Anda berwenang menyetujui' : 'Mode baca saja' }}
                </span>
            </div>

            <div wire:loading.class="opacity-40 pointer-events-none" class="transition-opacity duration-200">
                @if(count($ledger))
                    {{-- Tabel desktop --}}
                    <div class="hidden xl:block pq-scroll overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="text-[11px] uppercase tracking-wider text-gray-400 border-b border-ice-200">
                                    <th class="px-6 py-3 font-bold">PR &amp; Pengaju</th>
                                    <th class="px-6 py-3 font-bold">Bahan &amp; Stok</th>
                                    <th class="px-6 py-3 font-bold text-right">Diminta</th>
                                    <th class="px-6 py-3 font-bold text-right">Estimasi</th>
                                    <th class="px-6 py-3 font-bold">Urgensi</th>
                                    <th class="px-6 py-3 font-bold">Status</th>
                                    <th class="px-6 py-3 font-bold text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-ice-100">
                                @foreach($ledger as $p)
                                    <tr class="hover:bg-ice-50/70 transition-colors {{ $p['status'] === 'pending' ? ($p['urgency'] === 'critical' ? 'border-l-4 border-l-red-500' : 'border-l-4 border-l-amber-400') : 'border-l-4 border-l-transparent' }}">
                                        <td class="px-6 py-4">
                                            <p class="text-base font-extrabold text-brand-950">{{ $p['number'] }}</p>
                                            <p class="text-xs text-gray-500">{{ $p['created'] }}</p>
                                            <p class="text-xs text-gray-400 inline-flex items-center gap-1">{!! $qic('user', 'w-3 h-3') !!} {{ $p['requester'] }}</p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="text-sm font-bold text-brand-950">{{ $p['material'] }}</p>
                                            <p class="text-xs tabular-nums {{ $p['net'] <= 0 ? 'text-red-600 font-extrabold' : 'text-gray-500' }}">Sisa {{ number_format($p['net'], 2) }} / min {{ number_format($p['min'], 2) }} {{ $p['unit'] }}</p>
                                            @if($p['needed'])<p class="text-xs text-gray-400 tabular-nums">Butuh {{ $p['needed'] }}</p>@endif
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <p class="text-base font-extrabold text-brand-700 tabular-nums">{{ number_format($p['qty'], 2) }}</p>
                                            <p class="text-xs text-gray-400">{{ $p['unit'] }}</p>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <p class="text-base font-extrabold text-brand-950 tabular-nums">Rp {{ number_format($p['estimate'], 0, ',', '.') }}</p>
                                            @if($p['estimate_auto'])<p class="text-[10px] font-bold text-amber-600">estimasi sistem</p>@endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase {{ $UG[$p['urgency']][1] ?? $UG['normal'][1] }}">
                                                {!! $qic($UG[$p['urgency']][2] ?? 'clock', 'w-3 h-3') !!} {{ $UG[$p['urgency']][0] ?? $p['urgency'] }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            @php $s = $ST[$p['status']] ?? ['—', 'bg-ice-200 text-brand-800', 'note']; @endphp
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold {{ $s[1] }}">{!! $qic($s[2], 'w-3.5 h-3.5') !!} {{ $s[0] }}</span>
                                            @if($p['approver'])<p class="mt-1 text-[10px] text-gray-400">oleh {{ $p['approver'] }}</p>@endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center justify-end gap-2">
                                                <button type="button" wire:click="openDetail({{ $p['id'] }})" title="Detail"
                                                        class="w-9 h-9 flex items-center justify-center rounded-xl bg-ice-100 text-brand-700 hover:bg-ice-200 transition-colors">{!! $qic('eye', 'w-4 h-4') !!}</button>
                                                @if($p['status'] === 'pending' && $canApprove)
                                                    <button type="button" wire:click="openReject({{ $p['id'] }})" title="Tolak"
                                                            class="w-9 h-9 flex items-center justify-center rounded-xl bg-red-50 text-red-600 hover:bg-red-100 transition-colors">{!! $qic('x', 'w-4 h-4') !!}</button>
                                                    <button type="button" wire:click="approve({{ $p['id'] }})"
                                                            wire:confirm="Setujui {{ $p['number'] }}?"
                                                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-signal-400 text-brand-950 text-sm font-extrabold hover:bg-signal-500 transition-colors">
                                                        {!! $qic('check', 'w-3.5 h-3.5') !!} Setujui
                                                    </button>
                                                @elseif(in_array($p['status'], ['approved', 'ordered']))
                                                    <a href="{{ route('admin.goods-receipt', ['pr' => $p['id']]) }}"
                                                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-brand-600 text-white text-sm font-bold hover:bg-brand-700 transition-colors">
                                                        {!! $qic('truck', 'w-3.5 h-3.5') !!} Terima
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Kartu mobile/tablet --}}
                    <div class="xl:hidden divide-y divide-ice-100">
                        @foreach($ledger as $p)
                            @php $s = $ST[$p['status']] ?? ['—', 'bg-ice-200 text-brand-800', 'note']; @endphp
                            <div class="p-5 {{ $p['status'] === 'pending' ? ($p['urgency'] === 'critical' ? 'border-l-4 border-l-red-500' : 'border-l-4 border-l-amber-400') : 'border-l-4 border-l-transparent' }}">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="text-base font-extrabold text-brand-950">{{ $p['number'] }}</p>
                                        <p class="text-xs text-gray-500">{{ $p['created'] }} • {{ $p['requester'] }}</p>
                                    </div>
                                    <span class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold {{ $s[1] }}">{!! $qic($s[2], 'w-3.5 h-3.5') !!} {{ $s[0] }}</span>
                                </div>
                                <p class="mt-2.5 text-sm font-bold text-brand-950">{{ $p['material'] }}</p>
                                <p class="text-xs tabular-nums {{ $p['net'] <= 0 ? 'text-red-600 font-extrabold' : 'text-gray-500' }}">Sisa {{ number_format($p['net'], 2) }} / min {{ number_format($p['min'], 2) }} {{ $p['unit'] }}</p>
                                <div class="mt-3 flex items-end justify-between gap-3">
                                    <div>
                                        <p class="text-lg font-extrabold text-brand-950 tabular-nums">Rp {{ number_format($p['estimate'], 0, ',', '.') }}</p>
                                        <p class="text-xs text-gray-500 tabular-nums">{{ number_format($p['qty'], 2) }} {{ $p['unit'] }}@if($p['needed']) • butuh {{ $p['needed'] }}@endif</p>
                                    </div>
                                    <span class="shrink-0 px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase {{ $UG[$p['urgency']][1] ?? $UG['normal'][1] }}">{{ $UG[$p['urgency']][0] ?? $p['urgency'] }}</span>
                                </div>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <button type="button" wire:click="openDetail({{ $p['id'] }})" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-ice-100 text-brand-800 text-sm font-bold hover:bg-ice-200 transition-colors">{!! $qic('eye', 'w-4 h-4') !!} Detail</button>
                                    @if($p['status'] === 'pending' && $canApprove)
                                        <button type="button" wire:click="openReject({{ $p['id'] }})" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-red-50 text-red-600 text-sm font-bold hover:bg-red-100 transition-colors">{!! $qic('x', 'w-4 h-4') !!} Tolak</button>
                                        <button type="button" wire:click="approve({{ $p['id'] }})" wire:confirm="Setujui {{ $p['number'] }}?"
                                                class="flex-1 inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl bg-signal-400 text-brand-950 text-sm font-extrabold hover:bg-signal-500 transition-colors">{!! $qic('check', 'w-4 h-4') !!} Setujui</button>
                                    @elseif(in_array($p['status'], ['approved', 'ordered']))
                                        <a href="{{ route('admin.goods-receipt', ['pr' => $p['id']]) }}" class="flex-1 inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl bg-brand-600 text-white text-sm font-bold hover:bg-brand-700 transition-colors">{!! $qic('truck', 'w-4 h-4') !!} Terima Barang</a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-14 text-center">
                        <span class="inline-flex w-20 h-20 rounded-full bg-ice-100 text-brand-500 items-center justify-center mx-auto">{!! $qic('note', 'w-9 h-9') !!}</span>
                        <p class="mt-5 text-xl font-extrabold text-brand-950">Tidak ada PR yang cocok</p>
                        <p class="mt-2 text-base text-gray-500">Ubah kata kunci, status, atau urgensi.</p>
                        @if($hasFilter)
                            <button type="button" wire:click="clearFilters" class="mt-6 inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-brand-600 text-white text-base font-bold hover:bg-brand-700 transition-colors">
                                {!! $qic('undo', 'w-4 h-4') !!} Reset Saringan
                            </button>
                        @endif
                    </div>
                @endif
            </div>

            @if($paginator->hasPages())
                @php
                    $cur = $paginator->currentPage(); $last = $paginator->lastPage();
                    $from = max(1, $cur - 2); $to = min($last, $cur + 2);
                @endphp
                <div class="px-6 py-4 border-t border-ice-200 bg-ice-50 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <p class="text-sm text-gray-500">Halaman <b class="text-brand-950 tabular-nums">{{ $cur }}</b> dari <b class="text-brand-950 tabular-nums">{{ $last }}</b></p>
                    <div class="flex items-center gap-1.5">
                        <button type="button" wire:click="previousPage" @disabled($paginator->onFirstPage()) class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-ice-300 text-brand-700 hover:bg-ice-100 transition-colors disabled:opacity-40">{!! $qic('left', 'w-4 h-4') !!}</button>
                        @if($from > 1)
                            <button type="button" wire:click="gotoPage(1)" class="w-10 h-10 rounded-xl bg-white border border-ice-300 text-sm font-bold text-brand-700 hover:bg-ice-100 transition-colors">1</button>
                            @if($from > 2)<span class="px-1 text-gray-400">…</span>@endif
                        @endif
                        @for($p = $from; $p <= $to; $p++)
                            <button type="button" wire:click="gotoPage({{ $p }})" class="w-10 h-10 rounded-xl text-sm font-extrabold tabular-nums transition-colors {{ $p === $cur ? 'bg-brand-600 text-signal-300 shadow-sm' : 'bg-white border border-ice-300 text-brand-700 hover:bg-ice-100' }}">{{ $p }}</button>
                        @endfor
                        @if($to < $last)
                            @if($to < $last - 1)<span class="px-1 text-gray-400">…</span>@endif
                            <button type="button" wire:click="gotoPage({{ $last }})" class="w-10 h-10 rounded-xl bg-white border border-ice-300 text-sm font-bold text-brand-700 hover:bg-ice-100 transition-colors">{{ $last }}</button>
                        @endif
                        <button type="button" wire:click="nextPage" @disabled(!$paginator->hasMorePages()) class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-ice-300 text-brand-700 hover:bg-ice-100 transition-colors disabled:opacity-40">{!! $qic('right', 'w-4 h-4') !!}</button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- ===== BULK BAR ===== --}}
    @if(count($selected) && $canApprove)
        <div class="fixed bottom-4 left-0 right-0 z-40 px-4 pq-bar-in">
            <div class="max-w-3xl mx-auto forest-hero relative overflow-hidden rounded-[1.5rem] shadow-2xl shadow-brand-950/40 px-5 py-4 flex flex-col sm:flex-row sm:items-center gap-4">
                <div class="forest-dots absolute inset-0 opacity-40"></div>
                <div class="relative flex items-center gap-3 flex-1 min-w-0">
                    <span class="shrink-0 w-10 h-10 rounded-xl bg-signal-400 text-brand-950 flex items-center justify-center font-extrabold tabular-nums">{{ count($selected) }}</span>
                    <div class="min-w-0">
                        <p class="text-base font-extrabold text-white">PR dipilih untuk disetujui</p>
                        <p class="text-sm text-emerald-100/65 tabular-nums">Total estimasi Rp {{ number_format($selectedValue, 0, ',', '.') }}</p>
                    </div>
                </div>
                <div class="relative flex items-center gap-2.5 shrink-0">
                    <button type="button" wire:click="clearSelection" class="px-4 py-2.5 rounded-xl bg-white/10 border border-white/15 text-white text-sm font-bold hover:bg-white/20 transition-colors">Bersihkan</button>
                    <button type="button" wire:click="bulkApprove" wire:confirm="Setujui {{ count($selected) }} PR sekaligus senilai Rp {{ number_format($selectedValue, 0, ',', '.') }}?"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-signal-400 text-brand-950 text-sm font-extrabold hover:bg-signal-500 transition-colors disabled:opacity-60"
                            wire:loading.attr="disabled" wire:target="bulkApprove">
                        <span wire:loading.remove wire:target="bulkApprove" class="inline-flex items-center gap-2">{!! $qic('bolt', 'w-4 h-4') !!} Setujui Sekaligus</span>
                        <span wire:loading wire:target="bulkApprove">Memproses...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ===== MODAL DETAIL ===== --}}
    @if($detail)
        @php $s = $ST[$detail['status']] ?? ['—', 'bg-ice-200 text-brand-800', 'note']; @endphp
        <div class="fixed inset-0 z-[70] flex items-end sm:items-center justify-center p-0 sm:p-4 bg-brand-950/60 backdrop-blur-sm" wire:click.self="closeDetail">
            <div class="w-full sm:max-w-2xl max-h-[90vh] flex flex-col overflow-hidden rounded-t-3xl sm:rounded-3xl bg-ice-50 shadow-2xl">
                <div class="forest-hero relative overflow-hidden px-6 py-5 flex items-start justify-between gap-4 shrink-0">
                    <div class="forest-dots absolute inset-0 opacity-40"></div>
                    <div class="relative z-10 min-w-0">
                        <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-signal-300">Detail Purchase Request</p>
                        <h3 class="mt-1 text-xl font-extrabold text-white tracking-tight">{{ $detail['number'] }}</h3>
                        <div class="mt-2 flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold {{ $s[1] }}">{!! $qic($s[2], 'w-3.5 h-3.5') !!} {{ $s[0] }}</span>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase {{ $UG[$detail['urgency']][1] ?? $UG['normal'][1] }}">{{ $UG[$detail['urgency']][0] ?? $detail['urgency'] }}</span>
                        </div>
                    </div>
                    <button type="button" wire:click="closeDetail" class="relative z-10 shrink-0 w-9 h-9 flex items-center justify-center rounded-xl bg-white/10 text-white hover:bg-white/20 transition-colors">{!! $qic('x', 'w-5 h-5') !!}</button>
                </div>

                <div class="pq-scroll flex-1 overflow-y-auto p-6 space-y-5">
                    <div class="rounded-2xl bg-white border border-ice-200 p-5">
                        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-gray-400">Bahan &amp; Dampak Stok</p>
                        <p class="mt-1.5 text-lg font-extrabold text-brand-950">{{ $detail['material'] }}</p>
                        <div class="mt-4 grid grid-cols-3 gap-3 text-center">
                            <div class="rounded-xl bg-ice-50 border border-ice-200 px-3 py-3">
                                <p class="text-[10px] font-bold uppercase text-gray-400">Diminta</p>
                                <p class="mt-1 text-lg font-extrabold text-brand-700 tabular-nums">{{ number_format($detail['qty'], 2) }}</p>
                                <p class="text-[10px] text-gray-400">{{ $detail['unit'] }}</p>
                            </div>
                            <div class="rounded-xl px-3 py-3 border {{ $detail['net'] <= 0 ? 'bg-red-50 border-red-200' : 'bg-ice-50 border-ice-200' }}">
                                <p class="text-[10px] font-bold uppercase text-gray-400">Sisa Kini</p>
                                <p class="mt-1 text-lg font-extrabold tabular-nums {{ $detail['net'] <= 0 ? 'text-red-600' : 'text-brand-900' }}">{{ number_format($detail['net'], 2) }}</p>
                                <p class="text-[10px] text-gray-400">min {{ number_format($detail['min'], 2) }}</p>
                            </div>
                            <div class="rounded-xl bg-signal-100 border border-signal-300 px-3 py-3">
                                <p class="text-[10px] font-bold uppercase text-signal-700">Setelah Terima</p>
                                <p class="mt-1 text-lg font-extrabold text-brand-950 tabular-nums">{{ number_format($detail['after'], 2) }}</p>
                                <p class="text-[10px] text-signal-700">{{ $detail['after_coverage'] }}% ROP</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="rounded-2xl bg-white border border-ice-200 p-5 space-y-2.5 text-sm">
                            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-gray-400">Komersial</p>
                            <div class="flex justify-between gap-3"><span class="text-gray-500">Harga satuan</span><span class="font-bold tabular-nums">Rp {{ number_format($detail['unit_price'], 0, ',', '.') }}</span></div>
                            <div class="flex justify-between gap-3"><span class="text-gray-500">Estimasi total</span><span class="font-extrabold text-brand-950 tabular-nums">Rp {{ number_format($detail['estimate'], 0, ',', '.') }}</span></div>
                            <div class="flex justify-between gap-3"><span class="text-gray-500">Sumber estimasi</span><span class="font-bold">{{ $detail['estimate_auto'] ? 'Hitungan sistem' : 'Dari pengaju' }}</span></div>
                            <div class="flex justify-between gap-3"><span class="text-gray-500">Lead time</span><span class="font-bold tabular-nums">{{ $detail['lead'] }} hari</span></div>
                        </div>
                        <div class="rounded-2xl bg-white border border-ice-200 p-5 space-y-2.5 text-sm">
                            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-gray-400">Jejak Pengajuan</p>
                            <div class="flex justify-between gap-3"><span class="text-gray-500">Pengaju</span><span class="font-bold">{{ $detail['requester'] }}</span></div>
                            <div class="flex justify-between gap-3"><span class="text-gray-500">Diajukan</span><span class="font-bold tabular-nums">{{ $detail['created'] }}</span></div>
                            <div class="flex justify-between gap-3"><span class="text-gray-500">Dibutuhkan</span><span class="font-bold tabular-nums {{ $detail['risk'] ? 'text-red-600' : '' }}">{{ $detail['needed'] ?? '—' }}</span></div>
                            <div class="flex justify-between gap-3"><span class="text-gray-500">Diproses</span><span class="font-bold">{{ $detail['approver'] ?? '—' }}</span></div>
                            @if($detail['approved'])<div class="flex justify-between gap-3"><span class="text-gray-500">Waktu proses</span><span class="font-bold tabular-nums">{{ $detail['approved'] }}</span></div>@endif
                        </div>
                    </div>

                    @if($detail['reason'] || $detail['notes'])
                        <div class="rounded-2xl bg-white border border-ice-200 p-5">
                            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-gray-400">Alasan &amp; Catatan</p>
                            @if($detail['reason'])<p class="mt-2 text-base font-bold text-brand-950">{{ $detail['reason'] }}</p>@endif
                            @if($detail['notes'])<p class="mt-2 text-sm text-gray-600 whitespace-pre-line leading-relaxed">{{ $detail['notes'] }}</p>@endif
                        </div>
                    @endif

                    @if($detail['receipt'])
                        <div class="rounded-2xl bg-signal-100 border border-signal-300 p-5">
                            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-signal-700">Bukti Penerimaan</p>
                            <div class="mt-2 flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <p class="text-base font-extrabold text-brand-950">{{ $detail['receipt']['bpb'] }}</p>
                                    <p class="text-sm text-gray-600 tabular-nums">{{ $detail['receipt']['date'] ?? '-' }}</p>
                                </div>
                                <p class="text-lg font-extrabold text-brand-950 tabular-nums">Rp {{ number_format($detail['receipt']['cost'], 0, ',', '.') }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="shrink-0 px-6 py-4 border-t border-ice-200 bg-white flex flex-wrap items-center justify-end gap-2.5">
                    <button type="button" wire:click="closeDetail" class="px-5 py-2.5 rounded-xl bg-ice-100 text-brand-800 text-base font-bold hover:bg-ice-200 transition-colors">Tutup</button>
                    @if($detail['status'] === 'pending' && $canApprove)
                        <button type="button" wire:click="openReject({{ $detail['id'] }})" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-red-50 border border-red-300 text-red-600 text-base font-bold hover:bg-red-100 transition-colors">{!! $qic('x', 'w-4 h-4') !!} Tolak</button>
                        <button type="button" wire:click="approve({{ $detail['id'] }})" wire:confirm="Setujui {{ $detail['number'] }}?"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-signal-400 text-brand-950 text-base font-extrabold hover:bg-signal-500 transition-colors">{!! $qic('check', 'w-4 h-4') !!} Setujui</button>
                    @elseif(in_array($detail['status'], ['approved', 'ordered']))
                        <a href="{{ route('admin.goods-receipt', ['pr' => $detail['id']]) }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-600 text-white text-base font-bold hover:bg-brand-700 transition-colors">{!! $qic('truck', 'w-4 h-4') !!} Terima Barang</a>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- ===== MODAL TOLAK ===== --}}
    @if($rejectId && $rejectTarget)
        <div class="fixed inset-0 z-[75] flex items-end sm:items-center justify-center p-0 sm:p-4 bg-brand-950/60 backdrop-blur-sm" wire:click.self="closeReject">
            <div class="w-full sm:max-w-lg overflow-hidden rounded-t-3xl sm:rounded-3xl bg-ice-50 shadow-2xl">
                <div class="danger-hero relative overflow-hidden px-6 py-5 flex items-start justify-between gap-4">
                    <div class="danger-dots absolute inset-0 opacity-45"></div>
                    <div class="absolute top-0 left-0 right-0 h-1.5 hazard-bar"></div>
                    <div class="relative z-10 min-w-0 pt-1">
                        <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-red-100">Tolak Pengajuan</p>
                        <h3 class="mt-1 text-xl font-extrabold text-white tracking-tight">{{ $rejectTarget['number'] }}</h3>
                        <p class="mt-1 text-sm text-red-50/85">{{ $rejectTarget['material'] }} • {{ number_format($rejectTarget['qty'], 2) }} {{ $rejectTarget['unit'] }}</p>
                    </div>
                    <button type="button" wire:click="closeReject" class="relative z-10 shrink-0 w-9 h-9 flex items-center justify-center rounded-xl bg-white/15 text-white hover:bg-white/25 transition-colors">{!! $qic('x', 'w-5 h-5') !!}</button>
                </div>

                <form wire:submit="submitReject" class="p-6 space-y-5">
                    <div class="rounded-xl bg-amber-50 border border-amber-200 px-4 py-3 flex items-start gap-2.5">
                        <span class="shrink-0 text-amber-700 mt-0.5">{!! $qic('alert', 'w-4 h-4') !!}</span>
                        <p class="text-sm text-amber-800">Alasan penolakan akan tercatat dan terlihat oleh tim produksi sebagai dasar tindak lanjut.</p>
                    </div>
                    <div>
                        <label class="block text-base font-extrabold text-brand-950 mb-2">Alasan Penolakan <span class="text-red-500">*</span></label>
                        <textarea wire:model="rejectReason" rows="4" maxlength="500"
                                  placeholder="Contoh: anggaran bulan ini habis, tunda ke awal bulan depan. Atau: jumlah terlalu besar, ajukan ulang 100 m² dulu."
                                  class="pq-scroll w-full px-4 py-3.5 text-base leading-relaxed border border-ice-300 rounded-2xl bg-white focus:outline-none focus:ring-2 focus:ring-red-500/25 focus:border-red-400 resize-y @error('rejectReason') border-red-400 bg-red-50 @enderror"></textarea>
                        @error('rejectReason') <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex flex-wrap gap-2.5">
                        <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-2xl bg-red-600 text-white text-base font-extrabold hover:bg-red-700 shadow-md shadow-red-500/25 transition-all disabled:opacity-60"
                                wire:loading.attr="disabled" wire:target="submitReject">
                            <span wire:loading.remove wire:target="submitReject" class="inline-flex items-center gap-2">{!! $qic('x', 'w-4 h-4') !!} Tolak Pengajuan</span>
                            <span wire:loading wire:target="submitReject">Memproses...</span>
                        </button>
                        <button type="button" wire:click="closeReject" class="px-5 py-3.5 rounded-2xl bg-ice-200 text-brand-800 text-base font-bold hover:bg-ice-300 transition-colors">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>