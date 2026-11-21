@php
    $R = [
        'cart'  => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z',
        'box'   => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
        'alert' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
        'check' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        'clock' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        'x'     => 'M6 18L18 6M6 6l12 12',
        'send'  => 'M12 19l9 2-9-18-9 18 9-2zm0 0v-8',
        'search'=> 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z',
        'bolt'  => 'M13 10V3L4 14h7v7l9-11h-7z',
        'back'  => 'M10 19l-7-7m0 0l7-7m-7 7h18',
        'down'  => 'M19 14l-7 7m0 0l-7-7m7 7V3',
        'arrow' => 'M14 5l7 7m0 0l-7 7m7-7H3',
        'truck' => 'M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0 2 2 0 00-4 0zm10 0a2 2 0 104 0 2 2 0 00-4 0z',
        'note'  => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'fire'  => 'M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.24 17 7.2c1 2.16 1.5 4.3 1.5 5.8 0 1.5-.5 3-1.5 4.2z M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z',
        'undo'  => 'M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6',
        'left'  => 'M15 19l-7-7 7-7',
        'right' => 'M9 5l7 7-7 7',
        'list'  => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
    ];
    $ric = fn($k, $c = 'w-5 h-5') => '<svg class="' . $c . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="' . ($R[$k] ?? $R['box']) . '"/></svg>';

    $st = [
        'pending'   => ['Menunggu Owner', 'bg-amber-100 text-amber-800', 'clock'],
        'approved'  => ['Disetujui', 'bg-brand-200 text-brand-900', 'check'],
        'ordered'   => ['Dipesan ke Supplier', 'bg-brand-100 text-brand-700', 'cart'],
        'received'  => ['Barang Diterima', 'bg-signal-400 text-brand-950', 'truck'],
        'cancelled' => ['Dibatalkan', 'bg-ice-300 text-gray-600', 'x'],
    ];
    $urgLabel = ['critical' => 'Kritis', 'warning' => 'Warning', 'normal' => 'Normal'];
    $allSubmitted = count($alerts) > 0 && $stats['actionable'] === 0;
@endphp

<div class="w-full space-y-6"
     x-data="{
        scrollToId(id) {
            const el = document.getElementById(id);
            if (!el) return;
            const y = el.getBoundingClientRect().top + window.scrollY - 108;
            window.scrollTo({ top: Math.max(0, y), behavior: 'smooth' });
            el.classList.remove('pr-focus');
            void el.offsetWidth;
            el.classList.add('pr-focus');
            setTimeout(() => el.classList.remove('pr-focus'), 1900);
        },
        goForm() { this.scrollToId('form-pengajuan'); },
        goStatus() { this.scrollToId('status-pengajuan'); }
     }"
     @pr-scroll-to-form.window="$nextTick(() => setTimeout(goForm, 70))"
     @pr-scroll-to-status.window="$nextTick(() => setTimeout(goStatus, 70))">

    <style>
        .danger-hero {
            background:
                radial-gradient(ellipse at top left, rgba(248,113,113,.45), transparent 58%),
                radial-gradient(ellipse at bottom right, rgba(251,191,36,.14), transparent 55%),
                linear-gradient(135deg, #991b1b 0%, #7f1d1d 48%, #450a0a 100%);
        }
        .waiting-hero {
            background:
                radial-gradient(ellipse at top left, rgba(251,191,36,.30), transparent 58%),
                radial-gradient(ellipse at bottom right, rgba(220,38,38,.22), transparent 55%),
                linear-gradient(135deg, #78350f 0%, #7f1d1d 55%, #431407 100%);
        }
        .danger-dots { background-image: radial-gradient(rgba(254,202,202,.20) 1px, transparent 1px); background-size: 20px 20px; }
        .hazard-bar { background-image: repeating-linear-gradient(45deg, #dc2626 0 9px, #fee2e2 9px 18px); }
        .hazard-bar-dark { background-image: repeating-linear-gradient(45deg, #450a0a 0 9px, #fca5a5 9px 18px); }
        .hazard-bar-amber { background-image: repeating-linear-gradient(45deg, #b45309 0 9px, #fde68a 9px 18px); }

        @keyframes prRise { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
        .pr-rise { animation: prRise .5s cubic-bezier(.2,.7,.2,1) both; }
        @keyframes prSiren { 0%,100% { box-shadow: 0 0 0 0 rgba(254,202,202,.55); } 50% { box-shadow: 0 0 0 13px rgba(254,202,202,0); } }
        .pr-siren { animation: prSiren 1.9s ease-out infinite; }
        @keyframes prSirenRed { 0%,100% { box-shadow: 0 0 0 0 rgba(220,38,38,.45); } 50% { box-shadow: 0 0 0 12px rgba(220,38,38,0); } }
        .pr-siren-red { animation: prSirenRed 2s ease-out infinite; }
        @keyframes prAurora { 0%,100% { transform: translate3d(0,0,0) scale(1); } 50% { transform: translate3d(26px,-16px,0) scale(1.09); } }
        .pr-aurora { animation: prAurora 15s ease-in-out infinite; }
        @keyframes prNudge { 0%,100% { transform: translateY(0); } 50% { transform: translateY(4px); } }
        .pr-nudge { animation: prNudge 1.5s ease-in-out infinite; }
        @keyframes prFocus { 0% { box-shadow: 0 0 0 0 rgba(200,236,63,0); } 25% { box-shadow: 0 0 0 7px rgba(200,236,63,.5); } 100% { box-shadow: 0 0 0 0 rgba(200,236,63,0); } }
        .pr-focus { animation: prFocus 1.9s ease-out 1; }
        @keyframes prStripe { 0% { background-position: 0 0; } 100% { background-position: 36px 0; } }
        .pr-stripe-move { animation: prStripe 1.4s linear infinite; }
        @keyframes prTick { 0%,100% { opacity: 1; } 50% { opacity: .35; } }
        .pr-tick { animation: prTick 1.8s ease-in-out infinite; }

        .pr-scroll::-webkit-scrollbar { width: 8px; height: 8px; }
        .pr-scroll::-webkit-scrollbar-track { background: #e2edf0; border-radius: 8px; }
        .pr-scroll::-webkit-scrollbar-thumb { background: #1d6247; border-radius: 8px; }
        .pr-select { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' stroke='%230a3f33' stroke-width='2.5' viewBox='0 0 24 24'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right .85rem center; background-size: 1rem; }
        #form-pengajuan, #alert-stok, #status-pengajuan, #pr-disetujui { scroll-margin-top: 108px; }
        @media (prefers-reduced-motion: reduce) {
            .pr-rise, .pr-siren, .pr-siren-red, .pr-aurora, .pr-nudge, .pr-focus, .pr-stripe-move, .pr-tick { animation: none !important; }
        }
    </style>

    {{-- TOAST --}}
    @if(session('pr_success') || session('pr_warning'))
        @php $ok = (bool) session('pr_success'); @endphp
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-cloak
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-3"
             class="fixed top-24 right-4 sm:right-6 z-[70] max-w-sm" role="status" aria-live="polite">
            <div class="relative overflow-hidden rounded-2xl px-5 py-4 shadow-2xl flex items-start gap-3 {{ $ok ? 'forest-hero shadow-brand-950/40' : 'waiting-hero shadow-amber-950/40' }}">
                <div class="{{ $ok ? 'forest-dots' : 'danger-dots' }} absolute inset-0 opacity-45"></div>
                <span class="relative z-10 shrink-0 w-9 h-9 rounded-xl flex items-center justify-center {{ $ok ? 'bg-signal-400 text-brand-950' : 'bg-amber-300 text-amber-950' }}">
                    {!! $ric($ok ? 'check' : 'clock', 'w-5 h-5') !!}
                </span>
                <p class="relative z-10 flex-1 text-base font-bold text-white">{{ session('pr_success') ?? session('pr_warning') }}</p>
                <button type="button" @click="show = false" class="relative z-10 shrink-0 text-white/70 hover:text-white transition-colors">{!! $ric('x', 'w-4 h-4') !!}</button>
            </div>
        </div>
    @endif

    {{-- HERO --}}
    <div class="forest-hero relative overflow-hidden rounded-3xl text-white px-6 py-6 sm:px-8 sm:py-7 shadow-lg shadow-brand-950/25 pr-rise">
        <div class="forest-dots absolute inset-0 opacity-45"></div>
        <div class="pr-aurora absolute -top-24 -left-16 w-72 h-72 rounded-full bg-emerald-500/15 blur-3xl"></div>
        <div class="absolute -bottom-24 right-0 w-64 h-64 rounded-full bg-signal-400/10 blur-3xl"></div>

        <div class="relative z-10 flex flex-col xl:flex-row xl:items-center justify-between gap-5">
            <div class="min-w-0">
                <a href="{{ route('admin.production') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/5 border border-white/10 text-sm font-bold text-emerald-100/80 hover:bg-white/10 hover:text-signal-300 transition-colors">
                    {!! $ric('back', 'w-4 h-4') !!} Antrian Produksi
                </a>
                <p class="mt-4 text-xs font-bold uppercase tracking-[0.24em] text-signal-300">Pengajuan Pembelian</p>
                <h1 class="mt-2 text-3xl font-extrabold tracking-tight">Ajukan Purchase Request</h1>
                <p class="mt-2 text-base text-emerald-100/75 max-w-2xl">Produksi mengajukan kebutuhan bahan. Persetujuan tetap di tangan owner — Anda membuat, menerima, dan memantau.</p>
            </div>
            <div class="rounded-2xl bg-white/5 border border-white/10 px-5 py-4 shrink-0">
                <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-emerald-100/55">Nilai Menunggu Approval</p>
                <p class="mt-1 text-2xl font-extrabold text-signal-300 tabular-nums">Rp {{ number_format($stats['pending_value'], 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="relative z-10 mt-7 grid grid-cols-2 sm:grid-cols-4 gap-3">
            <button type="button" @click="scrollToId('alert-stok')"
                    class="text-left rounded-2xl px-4 py-3 border transition-all hover:-translate-y-0.5 {{ $stats['actionable'] > 0 ? 'bg-red-600/85 border-red-300/50 pr-siren-red' : ($stats['alerts'] > 0 ? 'bg-amber-600/70 border-amber-300/40' : 'bg-white/5 border-white/10') }}">
                <span class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-[0.16em] {{ $stats['alerts'] > 0 ? 'text-red-50' : 'text-emerald-100/60' }}">
                    {!! $ric('alert', 'w-3.5 h-3.5') !!} Bahan Kritis
                </span>
                <span class="mt-1 block text-2xl font-extrabold tabular-nums">{{ $stats['alerts'] }}</span>
                <span class="mt-0.5 block text-[11px] font-bold {{ $stats['alerts'] > 0 ? 'text-red-100' : 'text-emerald-100/50' }}">
                    @if($stats['actionable'] > 0) {{ $stats['actionable'] }} perlu diajukan
                    @elseif($stats['alerts'] > 0) semua sudah diajukan
                    @else stok aman @endif
                </span>
            </button>

            <button type="button" @click="goStatus()" class="text-left rounded-2xl px-4 py-3 border bg-white/5 border-white/10 hover:bg-white/10 transition-all hover:-translate-y-0.5">
                <span class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-[0.16em] text-emerald-100/60">{!! $ric('clock', 'w-3.5 h-3.5') !!} Menunggu Owner</span>
                <span class="mt-1 block text-2xl font-extrabold tabular-nums">{{ $stats['pending'] }}</span>
            </button>

            <button type="button" @click="scrollToId('pr-disetujui')"
                    class="text-left rounded-2xl px-4 py-3 border transition-all hover:-translate-y-0.5 {{ $stats['incoming'] > 0 ? 'bg-signal-400 border-signal-400 text-brand-950' : 'bg-white/5 border-white/10' }}">
                <span class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-[0.16em] {{ $stats['incoming'] > 0 ? 'text-brand-900/70' : 'text-emerald-100/60' }}">{!! $ric('truck', 'w-3.5 h-3.5') !!} Siap Diterima</span>
                <span class="mt-1 block text-2xl font-extrabold tabular-nums">{{ $stats['incoming'] }}</span>
            </button>

            <button type="button" @click="goStatus()" class="text-left rounded-2xl px-4 py-3 border bg-white/5 border-white/10 hover:bg-white/10 transition-all hover:-translate-y-0.5">
                <span class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-[0.16em] text-emerald-100/60">{!! $ric('check', 'w-3.5 h-3.5') !!} Barang Diterima</span>
                <span class="mt-1 block text-2xl font-extrabold tabular-nums">{{ $stats['received'] }}</span>
            </button>
        </div>
    </div>

    {{-- BAHAN DIPAKAI PESANAN --}}
    @if(!empty($orderContext))
        @php $ctxAllDone = $orderContext['low_count'] > 0 && $orderContext['actionable'] === 0; @endphp
        <div class="relative overflow-hidden rounded-[2rem] {{ $ctxAllDone ? 'waiting-hero' : 'danger-hero' }} text-white shadow-2xl shadow-red-950/35 pr-rise">
            <div class="absolute top-0 left-0 right-0 h-2 {{ $ctxAllDone ? 'hazard-bar-amber' : 'hazard-bar pr-stripe-move' }}"></div>
            <div class="danger-dots absolute inset-0 opacity-60"></div>
            <div class="pr-aurora absolute -top-24 -right-20 w-72 h-72 rounded-full {{ $ctxAllDone ? 'bg-amber-400/20' : 'bg-red-400/25' }} blur-3xl"></div>

            <div class="relative z-10 p-6 sm:p-8 pt-8">
                <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-5">
                    <div class="flex items-start gap-4 min-w-0">
                        <span class="shrink-0 w-14 h-14 rounded-2xl bg-white flex items-center justify-center {{ $ctxAllDone ? 'text-amber-700' : 'text-red-700 pr-siren' }}">
                            {!! $ric($ctxAllDone ? 'clock' : 'fire', 'w-7 h-7') !!}
                        </span>
                        <div class="min-w-0">
                            <p class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 border border-white/25 text-[11px] font-extrabold uppercase tracking-[0.2em] text-red-50">
                                <span class="w-1.5 h-1.5 rounded-full bg-white {{ $ctxAllDone ? 'pr-tick' : '' }}"></span>
                                @if($orderContext['low_count'] === 0) Bahan pesanan ini
                                @elseif($ctxAllDone) {{ $orderContext['low_count'] }} bahan kurang — semua sudah diajukan
                                @else {{ $orderContext['actionable'] }} dari {{ $orderContext['low_count'] }} bahan belum diajukan @endif
                            </p>
                            <h2 class="mt-3 text-2xl sm:text-3xl font-extrabold tracking-tight">{{ $orderContext['invoice'] }}</h2>
                            <p class="mt-1.5 text-base text-red-50/85">
                                {{ $ctxAllDone ? 'Pengajuan sudah dikirim. Produksi menunggu persetujuan owner dan kedatangan barang.' : 'Produksi pesanan ini tertahan sampai bahan diajukan dan disetujui owner.' }}
                                @if(!empty($orderContext['deadline'])) <b class="text-white">Target {{ $orderContext['deadline'] }}.</b> @endif
                            </p>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                        <a href="{{ route('admin.orders.show', $orderContext['id']) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 border border-white/20 text-sm font-bold text-white hover:bg-white/20 transition-colors">
                            Lihat pesanan {!! $ric('arrow', 'w-4 h-4') !!}
                        </a>
                        <button type="button" @click="{{ $ctxAllDone ? 'goStatus()' : 'goForm()' }}"
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white {{ $ctxAllDone ? 'text-amber-700 hover:bg-amber-50' : 'text-red-700 hover:bg-red-50' }} text-sm font-extrabold transition-colors">
                            {!! $ric($ctxAllDone ? 'list' : 'down', 'w-4 h-4') !!} {{ $ctxAllDone ? 'Pantau status PR' : 'Langsung ke form' }}
                        </button>
                    </div>
                </div>

                <div class="mt-7 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    @foreach($orderContext['rows'] as $r)
                        <div class="relative overflow-hidden rounded-2xl bg-white text-brand-950 border-2 {{ $r['low'] ? 'border-red-300' : 'border-white/40' }} p-5 shadow-xl shadow-red-950/20 hover:-translate-y-1 transition-transform">
                            <div class="absolute left-0 top-0 bottom-0 w-2 {{ $r['pending'] ? 'bg-amber-500' : ($r['low'] ? 'bg-red-600' : 'bg-brand-500') }}"></div>
                            @if($r['low'])<div class="absolute top-0 right-0 left-2 h-1 {{ $r['pending'] ? 'hazard-bar-amber' : 'hazard-bar' }}"></div>@endif

                            <div class="pl-2">
                                <div class="flex items-start justify-between gap-2">
                                    <p class="text-lg font-extrabold leading-snug">{{ $r['name'] }}</p>
                                    @if($r['pending'])
                                        <span class="shrink-0 px-2.5 py-1 rounded-full bg-amber-400 text-amber-950 text-[10px] font-extrabold uppercase tracking-wider">Diajukan</span>
                                    @elseif($r['low'])
                                        <span class="shrink-0 px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider {{ $r['net'] <= 0 ? 'bg-red-600 text-white' : 'bg-red-100 text-red-700' }}">{{ $r['net'] <= 0 ? 'Habis' : 'Kurang' }}</span>
                                    @else
                                        <span class="shrink-0 px-2.5 py-1 rounded-full bg-signal-100 text-signal-700 text-[10px] font-extrabold uppercase tracking-wider">Aman</span>
                                    @endif
                                </div>

                                <div class="mt-4 grid grid-cols-2 gap-3">
                                    <div class="rounded-xl bg-ice-50 border border-ice-200 px-3 py-2.5">
                                        <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Dipakai</p>
                                        <p class="mt-0.5 text-lg font-extrabold text-brand-800 tabular-nums leading-none">{{ number_format($r['need'], 2) }}</p>
                                        <p class="text-[11px] text-gray-400 mt-0.5">{{ $r['unit'] }}</p>
                                    </div>
                                    <div class="rounded-xl px-3 py-2.5 border {{ $r['low'] ? 'bg-red-50 border-red-200' : 'bg-ice-50 border-ice-200' }}">
                                        <p class="text-[10px] font-bold uppercase tracking-wider {{ $r['low'] ? 'text-red-500' : 'text-gray-400' }}">Sisa Gudang</p>
                                        <p class="mt-0.5 text-lg font-extrabold tabular-nums leading-none {{ $r['low'] ? 'text-red-600' : 'text-brand-800' }}">{{ number_format($r['net'], 2) }}</p>
                                        <p class="text-[11px] mt-0.5 {{ $r['low'] ? 'text-red-400' : 'text-gray-400' }}">{{ $r['unit'] }}</p>
                                    </div>
                                </div>

                                @if($r['gap'] > 0)
                                    <div class="mt-3 flex items-center gap-2 rounded-xl {{ $r['pending'] ? 'bg-amber-500 text-amber-950' : 'bg-red-600 text-white' }} px-3 py-2">
                                        {!! $ric('alert', 'w-4 h-4') !!}
                                        <p class="text-sm font-extrabold tabular-nums">Kekurangan {{ number_format($r['gap'], 2) }} {{ $r['unit'] }}</p>
                                    </div>
                                @endif

                                <div class="mt-3 flex items-center justify-between text-xs text-gray-500">
                                    <span>Minimum <b class="text-brand-800 tabular-nums">{{ number_format($r['min'], 2) }}</b></span>
                                    <span>Saran <b class="text-brand-700 tabular-nums">{{ number_format($r['suggest'], 2) }} {{ $r['unit'] }}</b></span>
                                </div>

                                @if($r['pending'])
                                    <div class="mt-4 rounded-xl bg-amber-50 border border-amber-300 px-3.5 py-3">
                                        <p class="flex items-center gap-2 text-sm font-extrabold text-amber-800">
                                            <span class="pr-tick">{!! $ric('clock', 'w-4 h-4') !!}</span> {{ $r['pr_number'] }} menunggu owner
                                        </p>
                                        <div class="mt-2.5 flex gap-2">
                                            <button type="button" @click="goStatus()" class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg bg-white border border-amber-300 text-xs font-bold text-amber-800 hover:bg-amber-100 transition-colors">{!! $ric('list', 'w-3.5 h-3.5') !!} Pantau</button>
                                            @if($r['pr_by_me'] && $r['pr_id'])
                                                <button type="button" wire:click="cancelRequest({{ $r['pr_id'] }})" wire:confirm="Batalkan PR ini agar bisa diajukan ulang?"
                                                        class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg bg-red-50 border border-red-200 text-xs font-bold text-red-600 hover:bg-red-100 transition-colors">{!! $ric('undo', 'w-3.5 h-3.5') !!} Batalkan</button>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <button type="button" wire:click="selectMaterial({{ $r['id'] }})"
                                            class="mt-4 w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl text-base font-extrabold shadow-md transition-all hover:-translate-y-0.5 {{ $r['low'] ? 'bg-red-600 text-white hover:bg-red-700 shadow-red-500/30' : 'bg-brand-600 text-signal-300 hover:bg-brand-700 shadow-brand-900/25' }}">
                                        {!! $ric('cart', 'w-4 h-4') !!} Ajukan bahan ini
                                        <span class="pr-nudge">{!! $ric('down', 'w-4 h-4') !!}</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <p class="mt-6 flex items-center justify-center gap-2 text-sm font-bold text-red-50/80">
                    <span class="pr-nudge">{!! $ric('down', 'w-4 h-4') !!}</span>
                    {{ $ctxAllDone ? 'Semua bahan pesanan ini sudah diajukan — pantau keputusan owner di bawah' : 'Klik Ajukan bahan ini — halaman otomatis turun & form terisi' }}
                </p>
            </div>
        </div>
    @endif

    {{-- ALERT STOK --}}
    <div id="alert-stok" class="relative overflow-hidden rounded-[2rem] shadow-xl shadow-red-950/15 pr-rise">
        <div class="relative overflow-hidden {{ count($alerts) ? ($allSubmitted ? 'waiting-hero' : 'danger-hero') : 'forest-hero' }} px-6 py-5">
            <div class="{{ count($alerts) ? 'danger-dots' : 'forest-dots' }} absolute inset-0 opacity-55"></div>
            @if(count($alerts))<div class="absolute top-0 left-0 right-0 h-1.5 {{ $allSubmitted ? 'hazard-bar-amber' : 'hazard-bar pr-stripe-move' }}"></div>@endif

            <div class="relative z-10 flex flex-wrap items-center justify-between gap-4 pt-1">
                <div class="flex items-center gap-4 min-w-0">
                    <span class="shrink-0 w-12 h-12 rounded-2xl flex items-center justify-center {{ count($alerts) ? ($allSubmitted ? 'bg-amber-300 text-amber-950' : 'bg-white text-red-700 pr-siren') : 'bg-signal-400 text-brand-950' }}">
                        {!! $ric(count($alerts) ? ($allSubmitted ? 'clock' : 'alert') : 'check', 'w-6 h-6') !!}
                    </span>
                    <div class="min-w-0">
                        <p class="text-[11px] font-bold uppercase tracking-[0.22em] {{ count($alerts) ? ($allSubmitted ? 'text-amber-200' : 'text-red-100') : 'text-signal-300' }}">
                            @if(!count($alerts)) Stok Produksi Sehat
                            @elseif($allSubmitted) Menunggu Keputusan Owner
                            @else Zona Merah — Stok Produksi @endif
                        </p>
                        <h2 class="mt-1 text-xl font-extrabold text-white tracking-tight">Alert Stok Produksi</h2>
                        <p class="text-sm {{ count($alerts) ? 'text-red-50/85' : 'text-emerald-100/75' }}">
                            @if(!count($alerts)) Semua bahan berada di atas batas minimum.
                            @else
                                {{ $stats['alerts'] }} bahan di bawah ROP
                                @if($stats['empty'] > 0) • {{ $stats['empty'] }} habis total @endif
                                • <b class="text-white">{{ $stats['actionable'] }} perlu diajukan</b>
                                • {{ $stats['pending_alerts'] }} menunggu owner
                            @endif
                        </p>
                    </div>
                </div>

                @if($stats['actionable'] > 0)
                    <button type="button" wire:click="requestAllCritical" wire:confirm="Ajukan PR untuk {{ $stats['actionable'] }} bahan kritis yang belum pending?"
                            class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-signal-400 text-brand-950 text-base font-extrabold hover:bg-signal-500 shadow-lg shadow-signal-400/25 hover:-translate-y-0.5 transition-all disabled:opacity-60"
                            wire:loading.attr="disabled" wire:target="requestAllCritical">
                        <span wire:loading.remove wire:target="requestAllCritical" class="inline-flex items-center gap-2">
                            {!! $ric('bolt', 'w-4 h-4') !!} Ajukan Semua
                            <span class="px-2 py-0.5 rounded-full bg-brand-950 text-signal-300 text-xs font-black tabular-nums">{{ $stats['actionable'] }}</span>
                        </span>
                        <span wire:loading wire:target="requestAllCritical">Memproses...</span>
                    </button>
                @elseif(count($alerts))
                    <div class="flex flex-wrap items-center gap-2.5">
                        <span class="inline-flex items-center gap-2 px-4 py-3 rounded-xl bg-white/12 border border-amber-200/40 text-sm font-extrabold text-amber-100">
                            <span class="pr-tick">{!! $ric('check', 'w-4 h-4') !!}</span> Semua sudah diajukan
                        </span>
                        <button type="button" @click="goStatus()" class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-white text-amber-800 text-base font-extrabold hover:bg-amber-50 shadow-lg shadow-amber-950/20 hover:-translate-y-0.5 transition-all">
                            {!! $ric('list', 'w-4 h-4') !!} Pantau Status PR
                        </button>
                    </div>
                @endif
            </div>
        </div>

        @if(count($alerts))
            <div class="{{ $allSubmitted ? 'bg-amber-50/70 border-amber-200' : 'bg-red-50/70 border-red-200' }} border-x-2 border-b-2 rounded-b-[2rem] p-5 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach($alerts as $a)
                    @php
                        $crit = $a['net'] <= 0;
                        $wait = $a['pending'];
                        $dark = !$wait && $crit;
                        $card = $wait
                            ? 'bg-gradient-to-br from-amber-50 via-white to-red-50 border-amber-400 text-brand-950 shadow-amber-300/40'
                            : ($crit
                                ? 'bg-gradient-to-br from-red-700 via-red-600 to-red-800 border-red-900 text-white shadow-red-900/35'
                                : 'bg-gradient-to-br from-red-50 via-white to-red-100 border-red-400 text-brand-950 shadow-red-300/40');
                        $label = $dark ? 'text-red-100/75' : ($wait ? 'text-amber-600' : 'text-red-500');
                        $value = $dark ? 'text-white' : ($wait ? 'text-amber-700' : 'text-red-700');
                        $inner = $dark ? 'bg-white/12 border-white/25' : ($wait ? 'bg-white border-amber-200' : 'bg-white border-red-200');
                        $sub   = $dark ? 'text-red-100/70' : 'text-gray-500';
                    @endphp
                    <div class="relative overflow-hidden rounded-2xl border-2 {{ $card }} p-5 pt-6 flex flex-col shadow-lg hover:-translate-y-1 transition-transform">
                        <div class="absolute top-0 left-0 right-0 h-1.5 {{ $wait ? 'hazard-bar-amber' : ($dark ? 'hazard-bar-dark pr-stripe-move' : 'hazard-bar') }}"></div>

                        <div class="flex items-start justify-between gap-2">
                            <p class="text-lg font-extrabold leading-snug">{{ $a['name'] }}</p>
                            <span class="shrink-0 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider {{ $wait ? 'bg-amber-400 text-amber-950' : ($dark ? 'bg-white text-red-700 pr-siren' : 'bg-red-600 text-white') }}">
                                {!! $ric($wait ? 'clock' : ($crit ? 'fire' : 'alert'), 'w-3 h-3') !!}
                                {{ $wait ? 'Diajukan' : ($crit ? 'Habis' : 'Kritis') }}
                            </span>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-3">
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-wider {{ $label }}">Sisa Bersih</p>
                                <p class="mt-0.5 text-2xl font-extrabold tabular-nums leading-none {{ $value }}">{{ number_format($a['net'], 2) }}</p>
                                <p class="text-[11px] mt-1 {{ $sub }}">{{ $a['unit'] }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-wider {{ $label }}">Minimum</p>
                                <p class="mt-0.5 text-2xl font-extrabold tabular-nums leading-none {{ $dark ? 'text-red-100' : 'text-brand-900' }}">{{ number_format($a['min'], 2) }}</p>
                                <p class="text-[11px] mt-1 {{ $sub }}">{{ $a['unit'] }}</p>
                            </div>
                        </div>

                        <div class="mt-3">
                            <div class="h-2 rounded-full overflow-hidden {{ $dark ? 'bg-white/20' : ($wait ? 'bg-amber-200' : 'bg-red-200') }}">
                                <div class="h-full rounded-full {{ $dark ? 'bg-white' : ($wait ? 'bg-amber-500' : 'bg-red-600') }}" style="width: {{ max(3, $a['ratio']) }}%"></div>
                            </div>
                            <p class="mt-1.5 text-[11px] font-bold {{ $sub }}">{{ $a['ratio'] }}% dari batas minimum</p>
                        </div>

                        <div class="mt-4 rounded-xl border {{ $inner }} px-3.5 py-3">
                            <p class="text-[10px] font-bold uppercase tracking-wider {{ $label }}">{{ $wait ? 'Jumlah Diajukan' : 'Saran Pengajuan' }}</p>
                            <p class="mt-0.5 text-xl font-extrabold tabular-nums {{ $dark ? 'text-signal-300' : ($wait ? 'text-amber-700' : 'text-brand-700') }}">{{ number_format($a['suggest'], 2) }} {{ $a['unit'] }}</p>
                            <p class="text-[11px] mt-0.5 tabular-nums {{ $sub }}">≈ Rp {{ number_format($a['suggest'] * $a['price'], 0, ',', '.') }} • lead {{ $a['lead'] }} hari</p>
                        </div>

                        <div class="mt-4">
                            @if($wait)
                                <div class="rounded-xl bg-amber-100 border border-amber-300 px-3.5 py-3">
                                    <p class="flex items-center gap-2 text-sm font-extrabold text-amber-900">
                                        <span class="pr-tick">{!! $ric('clock', 'w-4 h-4') !!}</span> {{ $a['pr_number'] }} • menunggu owner
                                    </p>
                                    @if($a['pr_since'])<p class="mt-1 text-[11px] text-amber-700">Diajukan {{ $a['pr_since'] }}{{ $a['pr_by_me'] ? ' oleh Anda' : '' }}</p>@endif
                                    <div class="mt-2.5 flex gap-2">
                                        <button type="button" @click="goStatus()" class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg bg-white border border-amber-300 text-xs font-bold text-amber-800 hover:bg-amber-50 transition-colors">{!! $ric('list', 'w-3.5 h-3.5') !!} Pantau</button>
                                        @if($a['pr_by_me'] && $a['pr_id'])
                                            <button type="button" wire:click="cancelRequest({{ $a['pr_id'] }})" wire:confirm="Batalkan PR ini agar bisa diajukan ulang?"
                                                    class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg bg-red-50 border border-red-200 text-xs font-bold text-red-600 hover:bg-red-100 transition-colors">{!! $ric('undo', 'w-3.5 h-3.5') !!} Batalkan</button>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <button type="button" wire:click="quickRequest({{ $a['id'] }})"
                                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl text-base font-extrabold shadow-md transition-all hover:-translate-y-0.5 disabled:opacity-60 {{ $dark ? 'bg-signal-400 text-brand-950 hover:bg-signal-500 shadow-signal-400/25' : 'bg-red-600 text-white hover:bg-red-700 shadow-red-500/30' }}"
                                        wire:loading.attr="disabled" wire:target="quickRequest({{ $a['id'] }})">
                                    {!! $ric('send', 'w-4 h-4') !!} Ajukan PR Sekarang
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white border-x border-b border-ice-200 rounded-b-[2rem] p-12 text-center">
                <span class="inline-flex w-16 h-16 rounded-full bg-signal-100 text-signal-600 items-center justify-center mx-auto">{!! $ric('check', 'w-7 h-7') !!}</span>
                <p class="mt-4 text-lg font-extrabold text-brand-950">Semua bahan di atas batas minimum</p>
                <p class="mt-1 text-base text-gray-500">Belum ada kebutuhan pembelian mendesak.</p>
            </div>
        @endif
    </div>

    {{-- PR DISETUJUI — SIAP DITERIMA --}}
    <div id="pr-disetujui" class="relative overflow-hidden rounded-[2rem] shadow-xl shadow-brand-950/15 pr-rise">
        <div class="forest-hero relative overflow-hidden px-6 py-5">
            <div class="forest-dots absolute inset-0 opacity-50"></div>
            <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-signal-400 via-brand-400 to-signal-400"></div>
            <div class="relative z-10 flex flex-wrap items-center justify-between gap-4 pt-1">
                <div class="flex items-center gap-4 min-w-0">
                    <span class="shrink-0 w-12 h-12 rounded-2xl flex items-center justify-center {{ $stats['incoming'] > 0 ? 'bg-signal-400 text-brand-950' : 'bg-white/10 text-emerald-100/60' }}">
                        {!! $ric('truck', 'w-6 h-6') !!}
                    </span>
                    <div class="min-w-0">
                        <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-signal-300">Disetujui Owner — Menunggu Barang</p>
                        <h2 class="mt-1 text-xl font-extrabold text-white tracking-tight">PR Siap Diterima</h2>
                        <p class="text-sm text-emerald-100/75">
                            {{ $stats['incoming'] > 0 ? $stats['incoming'] . ' PR disetujui • ≈ Rp ' . number_format($stats['incoming_value'], 0, ',', '.') : 'Belum ada PR yang disetujui owner.' }}
                        </p>
                    </div>
                </div>
                @if($stats['incoming'] > 0)
                    <a href="{{ route('admin.goods-receipt') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-signal-400 text-brand-950 text-base font-extrabold hover:bg-signal-500 shadow-lg shadow-signal-400/25 hover:-translate-y-0.5 transition-all">
                        {!! $ric('truck', 'w-4 h-4') !!} Buka Penerimaan Barang
                    </a>
                @endif
            </div>
        </div>

        @if($stats['incoming'] > 0)
            <div class="bg-ice-50 border-x-2 border-b-2 border-brand-200 rounded-b-[2rem] p-5 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach($incoming as $a)
                    <div class="relative overflow-hidden rounded-2xl bg-white border-2 {{ $a['overdue'] ? 'border-red-300' : 'border-brand-200' }} p-5 shadow-lg shadow-brand-950/5 hover:-translate-y-1 transition-transform">
                        <div class="absolute left-0 top-0 bottom-0 w-2 {{ $a['overdue'] ? 'bg-red-500' : 'bg-signal-400' }}"></div>
                        <div class="pl-2">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="text-base font-extrabold text-brand-950">{{ $a['number'] }}</p>
                                    <p class="text-xs text-gray-500">{{ $a['approved'] ?? '-' }} • {{ $a['approver'] }}</p>
                                </div>
                                <span class="shrink-0 px-2.5 py-1 rounded-full bg-brand-200 text-brand-900 text-[10px] font-extrabold uppercase">Disetujui</span>
                            </div>

                            <p class="mt-3 text-lg font-extrabold text-brand-950 leading-snug">{{ $a['material'] }}</p>

                            <div class="mt-3 grid grid-cols-3 gap-2 text-center">
                                <div class="rounded-xl bg-brand-50 border border-brand-200 px-2 py-2">
                                    <p class="text-[9px] font-bold uppercase text-gray-400">Dipesan</p>
                                    <p class="mt-0.5 text-base font-extrabold text-brand-700 tabular-nums leading-none">{{ number_format($a['qty'], 2) }}</p>
                                    <p class="text-[10px] text-gray-400 mt-0.5">{{ $a['unit'] }}</p>
                                </div>
                                <div class="rounded-xl px-2 py-2 border {{ $a['net'] <= 0 ? 'bg-red-50 border-red-200' : 'bg-ice-50 border-ice-200' }}">
                                    <p class="text-[9px] font-bold uppercase text-gray-400">Sisa Kini</p>
                                    <p class="mt-0.5 text-base font-extrabold tabular-nums leading-none {{ $a['net'] <= 0 ? 'text-red-600' : 'text-brand-900' }}">{{ number_format($a['net'], 2) }}</p>
                                    <p class="text-[10px] text-gray-400 mt-0.5">min {{ number_format($a['min'], 2) }}</p>
                                </div>
                                <div class="rounded-xl bg-signal-100 border border-signal-300 px-2 py-2">
                                    <p class="text-[9px] font-bold uppercase text-signal-700">Setelah</p>
                                    <p class="mt-0.5 text-base font-extrabold text-brand-950 tabular-nums leading-none">{{ number_format($a['after'], 2) }}</p>
                                    <p class="text-[10px] text-signal-700 mt-0.5">{{ $a['unit'] }}</p>
                                </div>
                            </div>

                            <div class="mt-3 flex flex-wrap items-center justify-between gap-2 text-xs">
                                <span class="text-gray-500">Estimasi <b class="text-brand-950 tabular-nums">Rp {{ number_format($a['estimate'], 0, ',', '.') }}</b></span>
                                @if($a['eta'])<span class="text-gray-500">Perkiraan tiba <b class="text-brand-800">{{ $a['eta'] }}</b></span>@endif
                            </div>
                            @if($a['needed'])
                                <p class="mt-1.5 inline-flex items-center gap-1.5 text-xs font-bold {{ $a['overdue'] ? 'text-red-600' : 'text-gray-500' }}">
                                    {!! $ric('clock', 'w-3.5 h-3.5') !!} Dibutuhkan {{ $a['needed'] }}{{ $a['overdue'] ? ' — sudah terlewat' : '' }}
                                </p>
                            @endif

                            <a href="{{ route('admin.goods-receipt', ['pr_id' => $a['id']]) }}"
                               class="mt-4 w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-brand-600 text-signal-300 text-base font-extrabold hover:bg-brand-700 shadow-md shadow-brand-900/25 transition-all hover:-translate-y-0.5">
                                {!! $ric('truck', 'w-4 h-4') !!} Catat Penerimaan
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white border-x border-b border-ice-200 rounded-b-[2rem] p-10 text-center">
                <span class="inline-flex w-16 h-16 rounded-full bg-ice-100 text-brand-500 items-center justify-center mx-auto">{!! $ric('clock', 'w-7 h-7') !!}</span>
                <p class="mt-4 text-lg font-extrabold text-brand-950">Belum ada PR disetujui</p>
                <p class="mt-1 text-base text-gray-500">Setelah owner menyetujui pengajuan, PR akan muncul di sini untuk diterima.</p>
            </div>
        @endif
    </div>

    {{-- FORM + ESTIMASI --}}
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">
        <div id="form-pengajuan" class="xl:col-span-8 relative overflow-hidden rounded-[2rem] bg-white border-2 {{ $materialId ? 'border-signal-400' : 'border-ice-200' }} shadow-sm pr-rise transition-colors">
            <div class="absolute left-0 top-8 bottom-8 w-1.5 rounded-r-full bg-gradient-to-b from-signal-400 to-brand-600"></div>
            @if($materialId)<div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-signal-400 via-brand-500 to-signal-400"></div>@endif

            <div class="p-6 sm:p-7 pl-7 sm:pl-8">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-[0.22em] {{ $materialId ? 'text-signal-600' : 'text-gray-400' }}">{{ $materialId ? 'Siap dikirim ke owner' : 'Pengajuan manual' }}</p>
                        <h2 class="mt-1 text-xl font-extrabold text-brand-950 tracking-tight">Form Pengajuan</h2>
                        <p class="text-sm text-gray-500 mt-1">Terisi otomatis dari kartu bahan. Periksa jumlah, lalu kirim.</p>
                    </div>
                    @if($materialId)
                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-signal-100 text-signal-700 text-xs font-extrabold">{!! $ric('check', 'w-3.5 h-3.5') !!} Bahan terpilih</span>
                    @endif
                </div>

                <form wire:submit="createRequest" class="mt-6 space-y-5">
                    <div>
                        <label class="block text-base font-extrabold text-brand-950 mb-2">Bahan Baku <span class="text-red-500">*</span></label>

                        @if($materialId && !empty($picked))
                            <div class="rounded-2xl border-2 border-brand-300 bg-brand-50 p-4 flex flex-col sm:flex-row sm:items-center gap-4">
                                <span class="shrink-0 w-12 h-12 rounded-xl bg-brand-600 text-signal-300 flex items-center justify-center">{!! $ric('box', 'w-6 h-6') !!}</span>
                                <div class="min-w-0 flex-1">
                                    <p class="text-lg font-extrabold text-brand-950 truncate">{{ $picked['name'] }}</p>
                                    <p class="text-sm text-gray-600 tabular-nums">Sisa {{ number_format($picked['net'], 2) }} {{ $picked['unit'] }} • Min {{ number_format($picked['min'], 2) }} • Rp {{ number_format($picked['price'], 0, ',', '.') }}/{{ $picked['unit'] }}</p>
                                </div>
                                <button type="button" wire:click="clearMaterial" class="shrink-0 px-4 py-2 rounded-xl bg-white border border-ice-300 text-sm font-bold text-brand-800 hover:bg-ice-100 transition-colors">Ganti bahan</button>
                            </div>
                        @else
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-brand-400">{!! $ric('search', 'w-5 h-5') !!}</span>
                                <input type="text" wire:model.live.debounce.300ms="materialQuery" placeholder="Ketik nama bahan, mis. Flexi / Art Paper"
                                       class="w-full pl-12 pr-4 py-3.5 text-base border-2 border-ice-300 rounded-2xl bg-ice-50 focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-500/20 focus:border-brand-500 transition-all">
                            </div>
                            @if(count($materialMatches))
                                <div class="mt-2 pr-scroll max-h-64 overflow-y-auto rounded-2xl border border-ice-200 divide-y divide-ice-100">
                                    @foreach($materialMatches as $m)
                                        <button type="button" wire:click="selectMaterial({{ $m['id'] }})" class="w-full text-left flex items-center justify-between gap-3 px-4 py-3 hover:bg-ice-50 transition-colors">
                                            <span class="min-w-0">
                                                <span class="block text-base font-bold text-brand-950 truncate">{{ $m['name'] }}</span>
                                                <span class="block text-sm text-gray-500 tabular-nums">Sisa {{ number_format($m['net'], 2) }} {{ $m['unit'] }} • Min {{ number_format($m['min'], 2) }}</span>
                                            </span>
                                            <span class="shrink-0 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold {{ $m['net'] <= 0 ? 'bg-red-600 text-white' : ($m['net'] <= $m['min'] ? 'bg-red-100 text-red-700' : 'bg-signal-100 text-signal-700') }}">
                                                {{ $m['net'] <= 0 ? 'HABIS' : ($m['net'] <= $m['min'] ? 'KRITIS' : 'AMAN') }}
                                            </span>
                                        </button>
                                    @endforeach
                                </div>
                            @else
                                <p class="mt-2 text-sm text-gray-400">
                                    {{ $allSubmitted ? 'Semua bahan kritis sudah diajukan. Form ini untuk kebutuhan lain di luar alert.' : 'Atau klik Ajukan bahan ini pada kartu di atas untuk mengisi otomatis.' }}
                                </p>
                            @endif
                        @endif
                        @error('materialId') <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-extrabold text-brand-950 mb-1.5">Jumlah Diminta <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="number" step="0.0001" min="0" wire:model.live.debounce.300ms="quantity"
                                       class="w-full px-4 py-3 text-base font-bold tabular-nums border border-ice-300 rounded-xl bg-ice-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25 focus:border-brand-500 @error('quantity') border-red-400 bg-red-50 @enderror">
                                @if($materialId)<span class="absolute inset-y-0 right-0 pr-3 flex items-center text-xs font-bold text-gray-400">{{ $picked['unit'] ?? '' }}</span>@endif
                            </div>
                            @if($materialId)<button type="button" wire:click="useSuggested" class="mt-1.5 text-xs font-bold text-brand-700 hover:text-brand-600 transition-colors">Pakai saran sistem</button>@endif
                            @error('quantity') <p class="mt-1.5 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-extrabold text-brand-950 mb-1.5">Tingkat Urgensi</label>
                            <select wire:model="urgency" class="pr-select w-full pl-4 pr-10 py-3 text-base border border-ice-300 rounded-xl bg-ice-50 focus:outline-none focus:ring-2 focus:ring-brand-500/25 cursor-pointer">
                                <option value="critical">Kritis — hentikan produksi</option>
                                <option value="warning">Warning — segera</option>
                                <option value="normal">Normal — rutin</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-extrabold text-brand-950 mb-1.5">Dibutuhkan Tanggal</label>
                            <input type="date" wire:model="neededBy" class="w-full px-4 py-3 text-base border border-ice-300 rounded-xl bg-ice-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25 focus:border-brand-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-extrabold text-brand-950 mb-1.5">Alasan Pengajuan</label>
                        <input type="text" wire:model="reason" maxlength="255" placeholder="Contoh: ROP trigger / stok habis setelah cetak backdrop"
                               class="w-full px-4 py-3 text-base border border-ice-300 rounded-xl bg-ice-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25 focus:border-brand-500">
                    </div>

                    <div>
                        <label class="block text-sm font-extrabold text-brand-950 mb-1.5">Catatan untuk Owner</label>
                        <textarea wire:model="notes" rows="3" maxlength="500" placeholder="Contoh: butuh untuk 3 pesanan backdrop minggu ini, supplier biasa lead time 3 hari."
                                  class="pr-scroll w-full px-4 py-3 text-base border border-ice-300 rounded-xl bg-ice-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25 focus:border-brand-500 resize-y"></textarea>
                        @error('notes') <p class="mt-1.5 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" @disabled(!$materialId)
                            class="w-full inline-flex items-center justify-center gap-2 px-6 py-4 rounded-2xl bg-brand-600 text-signal-300 text-base font-extrabold hover:bg-brand-700 shadow-md shadow-brand-900/25 hover:-translate-y-0.5 transition-all disabled:opacity-40 disabled:cursor-not-allowed"
                            wire:loading.attr="disabled" wire:target="createRequest">
                        <span wire:loading.remove wire:target="createRequest" class="inline-flex items-center gap-2">{!! $ric('send', 'w-5 h-5') !!} Kirim Pengajuan ke Owner</span>
                        <span wire:loading wire:target="createRequest">Mengirim...</span>
                    </button>
                </form>
            </div>
        </div>

        <div class="xl:col-span-4 xl:sticky xl:top-24 forest-hero relative overflow-hidden rounded-[2rem] p-6 text-white shadow-lg shadow-brand-950/20 pr-rise">
            <div class="forest-dots absolute inset-0 opacity-45"></div>
            <div class="absolute -bottom-16 -right-12 w-48 h-48 rounded-full bg-signal-400/10 blur-3xl"></div>
            <div class="relative">
                <p class="text-xs font-bold uppercase tracking-[0.22em] text-signal-300">Estimasi Pengajuan</p>
                @if($materialId && !empty($picked))
                    <p class="mt-3 text-lg font-extrabold leading-snug">{{ $picked['name'] }}</p>
                    <div class="mt-4 space-y-2.5 text-sm">
                        <div class="flex justify-between gap-3"><span class="text-emerald-100/60">Jumlah</span><span class="font-bold tabular-nums">{{ number_format((float) $quantity, 2) }} {{ $picked['unit'] }}</span></div>
                        <div class="flex justify-between gap-3"><span class="text-emerald-100/60">Harga satuan</span><span class="font-bold tabular-nums">Rp {{ number_format($picked['price'], 0, ',', '.') }}</span></div>
                        <div class="flex justify-between gap-3"><span class="text-emerald-100/60">Urgensi</span><span class="font-bold {{ $urgency === 'critical' ? 'text-red-300' : ($urgency === 'warning' ? 'text-amber-300' : 'text-white') }}">{{ $urgLabel[$urgency] ?? $urgency }}</span></div>
                        <div class="flex justify-between gap-3"><span class="text-emerald-100/60">Lead time</span><span class="font-bold tabular-nums">{{ $picked['lead'] }} hari</span></div>
                        <div class="flex justify-between gap-3"><span class="text-emerald-100/60">Sisa gudang</span><span class="font-bold tabular-nums {{ $picked['net'] <= 0 ? 'text-red-300' : 'text-white' }}">{{ number_format($picked['net'], 2) }} {{ $picked['unit'] }}</span></div>
                    </div>
                    <div class="mt-5 pt-5 border-t border-white/10">
                        <p class="text-xs font-bold uppercase tracking-[0.22em] text-emerald-100/60">Total Estimasi</p>
                        <p class="mt-1 text-3xl font-extrabold text-signal-300 tabular-nums tracking-tight">Rp {{ number_format($estimate, 0, ',', '.') }}</p>
                    </div>
                    <p class="mt-4 text-xs text-emerald-100/60">Estimasi memakai harga master bahan. Harga final ditentukan saat penerimaan barang (BPB).</p>
                @else
                    <div class="mt-6 py-8 text-center">
                        <span class="inline-flex w-14 h-14 rounded-full bg-white/5 border border-white/10 text-emerald-100/50 items-center justify-center">{!! $ric('cart', 'w-6 h-6') !!}</span>
                        <p class="mt-3 text-sm text-emerald-100/60">Pilih bahan dulu untuk melihat estimasi biaya.</p>
                        <button type="button" @click="scrollToId('alert-stok')" class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/10 border border-white/15 text-sm font-bold hover:bg-white/20 transition-colors">
                            {!! $ric('alert', 'w-4 h-4') !!} Lihat bahan kritis
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- STATUS PENGAJUAN --}}
    <div id="status-pengajuan" class="rounded-[2rem] bg-white border border-ice-200 shadow-sm overflow-hidden pr-rise">
        <div class="px-6 py-4 border-b border-ice-200 bg-ice-50 flex flex-col lg:flex-row lg:items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-extrabold text-brand-950">Status Pengajuan</h2>
                <p class="text-sm text-gray-500">Pantau keputusan owner atas PR yang diajukan.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                @foreach(['all' => 'Semua', 'pending' => 'Menunggu', 'approved' => 'Disetujui', 'received' => 'Diterima', 'cancelled' => 'Batal'] as $k => $l)
                    <button type="button" wire:click="setStatusFilter('{{ $k }}')"
                            class="px-4 py-2 rounded-full text-sm font-extrabold transition-all {{ $statusFilter === $k ? 'bg-brand-600 text-signal-300 shadow-sm' : 'bg-ice-100 text-brand-800 hover:bg-ice-200' }}">{{ $l }}</button>
                @endforeach
            </div>
        </div>

        <div wire:loading.class="opacity-40 pointer-events-none" class="divide-y divide-ice-100 transition-opacity duration-200">
            @forelse($prs as $pr)
                @php
                    $s = $st[$pr->status] ?? ['Unknown', 'bg-ice-200 text-brand-800', 'note'];
                    $uCls = $pr->urgency === 'critical' ? 'bg-red-600 text-white' : ($pr->urgency === 'warning' ? 'bg-amber-400 text-brand-950' : 'bg-ice-200 text-brand-800');
                @endphp
                <div class="p-5 flex flex-col lg:flex-row lg:items-center gap-4 hover:bg-ice-50/60 transition-colors {{ $pr->urgency === 'critical' && $pr->status === 'pending' ? 'border-l-4 border-l-red-500' : 'border-l-4 border-l-transparent' }}">
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-base font-extrabold text-brand-950">{{ $pr->pr_number }}</span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold rounded-full {{ $s[1] }}">{!! $ric($s[2], 'w-3.5 h-3.5') !!} {{ $s[0] }}</span>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase {{ $uCls }}">{{ $urgLabel[$pr->urgency] ?? $pr->urgency }}</span>
                            @if((int) $pr->requested_by === (int) $myId)
                                <span class="px-2.5 py-0.5 rounded-full bg-ice-200 text-brand-700 text-[10px] font-extrabold uppercase">Pengajuan Anda</span>
                            @endif
                        </div>
                        <p class="mt-1 text-sm text-gray-600">
                            <b class="text-brand-900">{{ $pr->material->name ?? 'Bahan dihapus' }}</b>
                            • {{ number_format((float) $pr->requested_quantity, 2) }} {{ $pr->unit }}
                            • {{ $pr->created_at?->format('d M Y, H:i') }}
                        </p>
                        @if($pr->reason)<p class="mt-0.5 text-xs text-gray-400">Alasan: {{ $pr->reason }}</p>@endif
                        @if($pr->approver)<p class="mt-0.5 text-xs text-gray-400">Diproses oleh {{ $pr->approver->name }}{{ $pr->approved_at ? ' • ' . $pr->approved_at->format('d M Y, H:i') : '' }}</p>@endif
                    </div>
                    <div class="flex items-center gap-4 shrink-0">
                        <div class="text-left lg:text-right">
                            <p class="text-base font-extrabold text-brand-950 tabular-nums">Rp {{ number_format((float) $pr->estimated_cost, 0, ',', '.') }}</p>
                            @if($pr->needed_by_date)<p class="text-xs text-gray-400 tabular-nums">Butuh {{ $pr->needed_by_date->format('d M Y') }}</p>@endif
                        </div>
                        @if($pr->status === 'pending' && (int) $pr->requested_by === (int) $myId)
                            <button type="button" wire:click="cancelRequest({{ $pr->id }})" wire:confirm="Batalkan pengajuan PR ini?"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-red-50 text-red-600 text-sm font-bold hover:bg-red-100 transition-colors">{!! $ric('x', 'w-3.5 h-3.5') !!} Batalkan</button>
                        @elseif(in_array($pr->status, ['approved', 'ordered']))
                            <a href="{{ route('admin.goods-receipt', ['pr_id' => $pr->id]) }}"
                               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-brand-600 text-white text-sm font-bold hover:bg-brand-700 transition-colors">{!! $ric('truck', 'w-3.5 h-3.5') !!} Terima</a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <span class="inline-flex w-16 h-16 rounded-full bg-ice-100 text-brand-500 items-center justify-center mx-auto">{!! $ric('note', 'w-7 h-7') !!}</span>
                    <p class="mt-4 text-lg font-extrabold text-brand-950">Belum ada pengajuan</p>
                    <p class="mt-1 text-base text-gray-500">Gunakan panel alert atau form di atas untuk membuat PR.</p>
                </div>
            @endforelse
        </div>

        @if($prs->hasPages())
            <div class="px-6 py-4 border-t border-ice-200 bg-ice-50 flex items-center justify-between gap-3">
                <p class="text-sm text-gray-500">Halaman <b class="text-brand-950 tabular-nums">{{ $prs->currentPage() }}</b> dari <b class="text-brand-950 tabular-nums">{{ $prs->lastPage() }}</b></p>
                <div class="flex items-center gap-1.5">
                    <button type="button" wire:click="previousPage" @disabled($prs->onFirstPage()) class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-ice-300 text-brand-700 hover:bg-ice-100 transition-colors disabled:opacity-40">{!! $ric('left', 'w-4 h-4') !!}</button>
                    <button type="button" wire:click="nextPage" @disabled(!$prs->hasMorePages()) class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-ice-300 text-brand-700 hover:bg-ice-100 transition-colors disabled:opacity-40">{!! $ric('right', 'w-4 h-4') !!}</button>
                </div>
            </div>
        @endif
    </div>
</div>