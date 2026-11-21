@php
    $P = [
        'invoice' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'alert'   => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
        'card'    => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',
        'printer' => 'M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z',
        'shield'  => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
        'truck'   => 'M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0 2 2 0 00-4 0zm10 0a2 2 0 104 0 2 2 0 00-4 0z',
        'check'   => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        'x'       => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
        'copy'    => 'M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z',
        'search'  => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z',
        'cal'     => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
        'box'     => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
        'down'    => 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4',
        'chat'    => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z',
        'link'    => 'M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1',
        'spark'   => 'M13 10V3L4 14h7v7l9-11h-7z',
    ];
    $ic = fn($k, $c = 'w-5 h-5') => '<svg class="' . $c . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="' . ($P[$k] ?? $P['invoice']) . '"/></svg>';
    $bulan = ['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'];
    $lastMonth = null;
    $stages = ['Invoice', 'Bayar', 'Produksi', 'QC', 'Kirim', 'Selesai'];
@endphp

<div class="relative min-h-screen bg-ice-100 overflow-hidden">
    <style>
        @keyframes hsAurora { 0%,100% { transform: translate3d(0,0,0) scale(1); } 50% { transform: translate3d(30px,-18px,0) scale(1.07); } }
        .hs-aurora { animation: hsAurora 16s ease-in-out infinite; }
        .hs-aurora-slow { animation: hsAurora 24s ease-in-out infinite reverse; }
        @keyframes hsRise { from { opacity: 0; transform: translateY(22px); } to { opacity: 1; transform: translateY(0); } }
        .hs-rise { animation: hsRise .6s cubic-bezier(.2,.7,.2,1) both; }
        @keyframes hsNode { 0%,100% { box-shadow: 0 0 0 0 rgba(200,236,63,.5); } 50% { box-shadow: 0 0 0 12px rgba(200,236,63,0); } }
        .hs-node-live { animation: hsNode 2.6s ease-out infinite; }
        @keyframes hsShine { 0% { transform: translateX(-140%) skewX(-14deg); } 60%,100% { transform: translateX(140%) skewX(-14deg); } }
        .hs-shine { animation: hsShine 7s ease-in-out infinite; }
        .hs-select { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' stroke='%230a3f33' stroke-width='2.5' viewBox='0 0 24 24'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right .9rem center; background-size: 1rem; }
    </style>

    {{-- ================= HERO FOREST ================= --}}
    <section class="forest-hero relative overflow-hidden text-white">
        <div class="forest-dots absolute inset-0 opacity-60"></div>
        <div class="hs-aurora absolute -top-28 -left-20 w-[30rem] h-[30rem] rounded-full bg-emerald-500/15 blur-3xl"></div>
        <div class="hs-aurora-slow absolute -bottom-32 right-0 w-[26rem] h-[26rem] rounded-full bg-signal-400/10 blur-3xl"></div>
        <div class="absolute inset-y-0 left-1/3 w-32 rotate-12 bg-gradient-to-b from-signal-400/0 via-signal-400/10 to-signal-400/0 blur-2xl"></div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-10 pb-28 lg:pt-14 lg:pb-32">
            <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8">
                <div class="min-w-0 hs-rise">
                    <a href="{{ route('customer.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/5 border border-white/10 text-sm font-bold text-emerald-100/80 hover:bg-white/10 hover:text-signal-300 transition-colors">← Dashboard</a>
                    <p class="mt-7 inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/5 border border-signal-400/30 text-signal-300 text-xs font-bold uppercase tracking-[0.25em]">Arsip Transaksi</p>
                    <h1 class="mt-4 text-4xl sm:text-5xl font-extrabold tracking-tight leading-tight">Riwayat Pesanan</h1>
                    <p class="mt-3 text-lg text-emerald-100/75 max-w-xl">Setiap pesanan tercatat sebagai tiket perjalanan — dari invoice terbit hingga cetakan tiba di tangan Anda.</p>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-2 gap-3 lg:w-[22rem] shrink-0 hs-rise">
                    <div class="rounded-2xl bg-white/5 border border-white/10 backdrop-blur-sm px-4 py-3">
                        <p class="text-xs uppercase tracking-wider text-emerald-100/60">Total Arsip</p>
                        <p class="text-3xl font-extrabold tabular-nums mt-0.5">{{ $summary['total'] }}</p>
                    </div>
                    <div class="rounded-2xl bg-white/5 border border-white/10 backdrop-blur-sm px-4 py-3">
                        <p class="text-xs uppercase tracking-wider text-emerald-100/60">Berjalan</p>
                        <p class="text-3xl font-extrabold tabular-nums mt-0.5 text-signal-300">{{ $summary['active'] }}</p>
                    </div>
                    <div class="rounded-2xl bg-white/5 border border-white/10 backdrop-blur-sm px-4 py-3">
                        <p class="text-xs uppercase tracking-wider text-emerald-100/60">Selesai</p>
                        <p class="text-3xl font-extrabold tabular-nums mt-0.5">{{ $summary['completed'] }}</p>
                    </div>
                    <div class="rounded-2xl bg-signal-400 text-brand-950 px-4 py-3 shadow-lg shadow-signal-400/20">
                        <p class="text-xs font-bold uppercase tracking-wider">Total Dibayar</p>
                        <p class="text-xl font-extrabold tabular-nums tracking-tight mt-0.5">Rp {{ number_format($summary['paid'], 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <svg class="absolute bottom-0 left-0 w-full h-16 text-ice-100" viewBox="0 0 1440 90" fill="currentColor" preserveAspectRatio="none"><path d="M0,58 C220,92 470,18 720,44 C970,70 1210,28 1440,54 L1440,90 L0,90 Z"/></svg>
    </section>

    {{-- ================= CONTROL BAR ================= --}}
    <div class="sticky top-24 z-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-10">
        <div class="rounded-[1.75rem] bg-white/85 backdrop-blur-xl border border-ice-200 shadow-xl shadow-brand-950/5 p-3 sm:p-4">
            <div class="flex flex-col xl:flex-row gap-3 xl:items-center xl:justify-between">
                <div class="flex flex-wrap gap-2">
                    @foreach(['all' => 'Semua', 'active' => 'Berjalan', 'unpaid' => 'Perlu Tindakan', 'completed' => 'Selesai'] as $key => $label)
                        <button wire:click="setFilter('{{ $key }}')"
                                class="group relative px-5 py-2.5 text-base font-bold rounded-full transition-all {{ $filter === $key ? 'bg-brand-600 text-signal-300 shadow-md shadow-brand-900/25' : 'bg-ice-100 text-brand-800 hover:bg-ice-200' }}">
                            {{ $label }}
                            @if($key === 'unpaid' && $summary['pending'] > 0)
                                <span class="ml-1.5 inline-flex items-center justify-center min-w-5 h-5 px-1.5 rounded-full bg-red-500 text-white text-[11px] font-extrabold">{{ $summary['pending'] }}</span>
                            @endif
                        </button>
                    @endforeach
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <select wire:model.live="sort" class="hs-select pl-4 pr-10 py-2.5 text-base font-bold text-brand-800 border border-ice-300 rounded-full bg-ice-50 focus:outline-none focus:ring-2 focus:ring-brand-500/30 cursor-pointer">
                        <option value="newest">Terbaru dulu</option>
                        <option value="oldest">Terlama dulu</option>
                    </select>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-brand-400">{!! $ic('search', 'w-4 h-4') !!}</span>
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari no. invoice..."
                               class="w-full sm:w-64 pl-11 pr-10 py-2.5 text-base border border-ice-300 rounded-full bg-ice-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition-all">
                        <span wire:loading wire:target="search" class="absolute inset-y-0 right-0 pr-4 flex items-center">
                            <svg class="w-4 h-4 animate-spin text-brand-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.4 0 0 5.4 0 12h4z"/></svg>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= TIMELINE TIKET ================= --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-10 pb-20">
        <div wire:loading.class="opacity-40 pointer-events-none" class="relative transition-opacity duration-200">

            @if($orders->count() > 0)
                <div class="hidden lg:block absolute left-6 top-4 bottom-4 w-0.5 -translate-x-1/2 rounded-full bg-gradient-to-b from-signal-400 via-brand-500 to-brand-200"></div>
            @endif

            <div class="space-y-7">
                @forelse($orders as $o)
                    @php
                        $meta = $statusMeta[$o->status] ?? ['label' => ucfirst(str_replace('_', ' ', $o->status)), 'badge' => 'bg-ice-200 text-brand-800', 'step' => 0, 'icon' => 'invoice', 'node' => 'bg-white border-2 border-ice-300 text-brand-400', 'bar' => 'from-ice-400 to-transparent'];
                        $inv = $o->invoice;
                        $total = (float) ($inv->total_amount ?? 0);
                        $paidAmt = (float) ($inv->total_paid ?? 0);
                        $isPaid = $inv && (($inv->status === 'paid') || ($total > 0 && $paidAmt >= $total));
                        $isUnpaid = in_array($o->status, ['waiting_invoice', 'unpaid']) && !$isPaid;
                        $payUrl = ($inv && $inv->payment_token) ? route('payment.show', $inv->payment_token) : null;
                        $pdfUrl = $inv ? route('customer.invoices.pdf', $inv->id) : null;
                        $pdfDl  = $inv ? route('customer.invoices.download', $inv->id) : null;
                        $step = (int) $meta['step'];
                        $pct = $o->status === 'completed' ? 100 : max(0, min(100, round($step / 5 * 100)));
                        $monthKey = $o->created_at->format('Y-m');
                        $showMonth = $monthKey !== $lastMonth;
                        $lastMonth = $monthKey;
                        $live = !in_array($o->status, ['completed', 'cancelled']);
                    @endphp

                    @if($showMonth)
                        <div class="lg:grid lg:grid-cols-[3rem_1fr] lg:gap-8 items-center pt-2">
                            <div class="hidden lg:flex justify-center">
                                <span class="relative z-10 w-4 h-4 rounded-full bg-signal-400 ring-4 ring-ice-100"></span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-brand-600 text-signal-300 text-sm font-extrabold uppercase tracking-[0.18em] shadow-sm">
                                    {!! $ic('cal', 'w-4 h-4') !!}
                                    {{ $bulan[$o->created_at->format('m')] }} {{ $o->created_at->format('Y') }}
                                </span>
                                <span class="hidden sm:block flex-1 h-px bg-gradient-to-r from-ice-300 to-transparent"></span>
                            </div>
                        </div>
                    @endif

                    <div class="lg:grid lg:grid-cols-[3rem_1fr] lg:gap-8 items-start hs-rise">
                        {{-- Node timeline --}}
                        <div class="hidden lg:flex justify-center pt-8">
                            <span class="relative z-10 w-12 h-12 rounded-full flex items-center justify-center shadow-md {{ $meta['node'] }} {{ $live ? 'hs-node-live' : '' }}">
                                {!! $ic($meta['icon'], 'w-5 h-5') !!}
                            </span>
                        </div>

                        {{-- Tiket --}}
                        <article class="group relative overflow-hidden rounded-[1.75rem] bg-white border border-ice-200 shadow-sm hover:shadow-2xl hover:shadow-brand-950/10 hover:-translate-y-1 transition-all duration-300">
                            <div class="h-1.5 w-full bg-gradient-to-r {{ $meta['bar'] }}"></div>
                            <div class="hs-shine absolute inset-0 bg-gradient-to-r from-transparent via-signal-400/5 to-transparent pointer-events-none opacity-0 group-hover:opacity-100"></div>

                            <div class="relative flex flex-col lg:flex-row">
                                {{-- Badan tiket --}}
                                <div class="flex-1 min-w-0 p-6 sm:p-7">
                                    <div class="flex flex-wrap items-center gap-2.5">
                                        <span class="lg:hidden w-9 h-9 rounded-full flex items-center justify-center {{ $meta['node'] }}">{!! $ic($meta['icon'], 'w-4 h-4') !!}</span>
                                        <h3 class="text-xl sm:text-2xl font-extrabold text-brand-950 tracking-tight">{{ $o->invoice_number }}</h3>
                                        <button onclick="copyText('{{ $o->invoice_number }}', this)" title="Salin nomor invoice"
                                                class="w-9 h-9 flex items-center justify-center rounded-xl bg-ice-100 text-brand-600 hover:bg-brand-600 hover:text-signal-300 transition-colors">
                                            {!! $ic('copy', 'w-4 h-4') !!}
                                        </button>
                                        <span class="px-3 py-1 text-sm font-bold rounded-full {{ $meta['badge'] }}">{{ $meta['label'] }}</span>
                                        @if($isPaid)
                                            <span class="px-3 py-1 text-sm font-extrabold rounded-full bg-signal-400 text-brand-950 tracking-wider shadow-sm shadow-signal-400/40">LUNAS</span>
                                        @endif
                                    </div>

                                    <div class="mt-3 flex flex-wrap items-center gap-x-5 gap-y-2 text-base text-gray-500">
                                        <span class="inline-flex items-center gap-2">{!! $ic('cal', 'w-4 h-4') !!} {{ $o->created_at->format('d M Y, H:i') }}</span>
                                        <span class="inline-flex items-center gap-2">{!! $ic('box', 'w-4 h-4') !!} {{ $o->items->count() }} item</span>
                                        <span class="inline-flex items-center gap-2 font-semibold text-brand-700">{!! $ic('printer', 'w-4 h-4') !!} {{ $o->items->first()?->service->name ?? '-' }}</span>
                                    </div>

                                    {{-- Rail progres --}}
                                    <div class="mt-6">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-xs font-bold uppercase tracking-[0.18em] text-gray-400">Progres Produksi</span>
                                            <span class="text-sm font-extrabold text-brand-700 tabular-nums">{{ $pct }}%</span>
                                        </div>
                                        <div class="flex gap-1.5">
                                            @foreach($stages as $i => $label)
                                                <div class="flex-1 min-w-0">
                                                    <div class="h-2 rounded-full transition-all {{ $i < $step || ($i === $step && $o->status === 'completed') ? 'bg-brand-600' : ($i === $step ? 'bg-signal-400' : 'bg-ice-200') }}"></div>
                                                    <p class="mt-1.5 text-[10px] font-bold text-center truncate {{ $i <= $step ? 'text-brand-700' : 'text-gray-400' }}">{{ $label }}</p>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                {{-- Perforasi + stub --}}
                                <div class="relative shrink-0 lg:w-[19rem] border-t-2 border-dashed border-ice-300 lg:border-t-0 lg:border-l-2 bg-ice-50/70 p-6 sm:p-7 flex flex-col justify-between gap-5">
                                    <span class="lg:hidden absolute -top-2.5 -left-2.5 w-5 h-5 rounded-full bg-ice-100"></span>
                                    <span class="lg:hidden absolute -top-2.5 -right-2.5 w-5 h-5 rounded-full bg-ice-100"></span>
                                    <span class="hidden lg:block absolute -left-2.5 -top-2.5 w-5 h-5 rounded-full bg-ice-100"></span>
                                    <span class="hidden lg:block absolute -left-2.5 -bottom-2.5 w-5 h-5 rounded-full bg-ice-100"></span>

                                    <div>
                                        <p class="text-xs font-bold uppercase tracking-[0.18em] text-gray-400">Nilai Pesanan</p>
                                        <p class="mt-1 text-3xl font-extrabold text-brand-950 tabular-nums tracking-tight leading-none">Rp {{ number_format($total, 0, ',', '.') }}</p>
                                        <p class="mt-2 text-base {{ $isPaid ? 'text-signal-600 font-extrabold' : ($inv ? 'text-gray-500' : 'text-gray-400 italic') }}">
                                            {{ $isPaid ? 'Lunas' : ($inv ? 'Sisa Rp ' . number_format(max(0, $total - $paidAmt), 0, ',', '.') : 'Invoice belum terbit') }}
                                        </p>
                                    </div>

                                    <div class="flex flex-col gap-2">
                                        @if($pdfUrl)
                                            <a href="{{ $pdfUrl }}" target="_blank" class="inline-flex items-center justify-center gap-2 px-4 py-3 text-base font-bold rounded-xl bg-brand-600 text-white hover:bg-brand-700 shadow-sm shadow-brand-900/20 transition-colors">
                                                {!! $ic('invoice', 'w-4 h-4') !!} Lihat Invoice
                                            </a>
                                            <div class="flex gap-2">
                                                <a href="{{ $pdfDl }}" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 text-base font-bold rounded-xl bg-signal-400 text-brand-950 hover:bg-signal-500 transition-colors">
                                                    {!! $ic('down', 'w-4 h-4') !!} Unduh
                                                </a>
                                                @if($payUrl && !$isPaid)
                                                    <button onclick="copyText('{{ $payUrl }}', this)" title="Salin link pembayaran" class="px-4 py-2.5 rounded-xl bg-white border border-ice-300 text-brand-700 hover:bg-ice-100 transition-colors">{!! $ic('link', 'w-4 h-4') !!}</button>
                                                @endif
                                            </div>
                                        @endif

                                        @if($payUrl && !$isPaid)
                                            <a href="{{ $payUrl }}" target="_blank" class="inline-flex items-center justify-center gap-2 px-4 py-3 text-base font-extrabold rounded-xl bg-gradient-to-r from-brand-600 to-brand-500 text-signal-300 hover:from-brand-700 hover:to-brand-600 shadow-md shadow-brand-900/25 transition-all">
                                                {!! $ic('card', 'w-4 h-4') !!} Bayar Sekarang
                                            </a>
                                        @elseif(!$inv && $wa)
                                            <a href="https://wa.me/{{ $wa }}?text={{ urlencode('Halo Admin, saya ' . auth()->user()->name . '. Mohon terbitkan invoice untuk pesanan ' . $o->invoice_number . '. Terima kasih.') }}" target="_blank"
                                               class="inline-flex items-center justify-center gap-2 px-4 py-3 text-base font-extrabold rounded-xl bg-signal-400 text-brand-950 hover:bg-signal-500 shadow-sm shadow-signal-400/40 transition-colors">
                                                {!! $ic('chat', 'w-4 h-4') !!} Minta Invoice
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>
                @empty
                    <div class="relative overflow-hidden rounded-[2rem] forest-hero text-white p-12 sm:p-16 text-center">
                        <div class="forest-dots absolute inset-0 opacity-50"></div>
                        <div class="hs-aurora absolute -top-20 -right-16 w-72 h-72 rounded-full bg-signal-400/10 blur-3xl"></div>
                        <div class="relative z-10">
                            <span class="inline-flex w-20 h-20 rounded-full bg-white/5 border border-signal-400/30 items-center justify-center text-signal-300 mx-auto">{!! $ic('box', 'w-9 h-9') !!}</span>
                            <p class="mt-6 text-2xl font-extrabold">Belum ada tiket di arsip ini</p>
                            <p class="mt-2 text-base text-emerald-100/70">Coba ubah filter atau kata kunci pencarian Anda.</p>
                            <button wire:click="setFilter('all')" class="mt-7 inline-flex items-center gap-2 px-6 py-3 rounded-full bg-signal-400 text-brand-950 text-base font-extrabold hover:bg-signal-500 transition-colors">
                                {!! $ic('spark', 'w-4 h-4') !!} Tampilkan Semua
                            </button>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        @if($orders->hasPages())
            <div class="mt-10 flex justify-center">{{ $orders->links() }}</div>
        @endif
    </section>
</div>