@php
    $G = [
        'truck' => 'M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0 2 2 0 00-4 0zm10 0a2 2 0 104 0 2 2 0 00-4 0z',
        'box'   => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
        'check' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        'clock' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        'alert' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
        'x'     => 'M6 18L18 6M6 6l12 12',
        'plus'  => 'M12 4v16m8-8H4',
        'save'  => 'M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4',
        'clip'  => 'M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13',
        'note'  => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'print' => 'M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z',
        'search'=> 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z',
        'down'  => 'M19 14l-7 7m0 0l-7-7m7 7V3',
        'arrow' => 'M14 5l7 7m0 0l-7 7m7-7H3',
        'money' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'cal'   => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
        'user'  => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
        'cart'  => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z',
        'fire'  => 'M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.24 17 7.2c1 2.16 1.5 4.3 1.5 5.8 0 1.5-.5 3-1.5 4.2z',
        'list'  => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
    ];
    $gic = fn($k, $c = 'w-5 h-5') => '<svg class="' . $c . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="' . ($G[$k] ?? $G['box']) . '"/></svg>';
    $UG = ['critical' => ['Kritis', 'bg-red-600 text-white'], 'warning' => ['Warning', 'bg-amber-400 text-brand-950'], 'normal' => ['Normal', 'bg-ice-200 text-brand-800']];
@endphp

<div class="w-full space-y-6"
     x-data="{
        goForm() {
            const el = document.getElementById('form-penerimaan');
            if (!el) return;
            window.scrollTo({ top: Math.max(0, el.getBoundingClientRect().top + window.scrollY - 108), behavior: 'smooth' });
            el.classList.remove('gr-focus'); void el.offsetWidth; el.classList.add('gr-focus');
            setTimeout(() => el.classList.remove('gr-focus'), 1900);
        }
     }"
     @gr-scroll-to-form.window="$nextTick(() => setTimeout(goForm, 70))">

    <style>
        .gr-incoming {
            background:
                radial-gradient(ellipse at top left, rgba(200,236,63,.22), transparent 58%),
                radial-gradient(ellipse at bottom right, rgba(16,185,129,.18), transparent 55%),
                linear-gradient(135deg, #0a3f33 0%, #062e25 55%, #03201a 100%);
        }
        @keyframes grRise { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
        .gr-rise { animation: grRise .5s cubic-bezier(.2,.7,.2,1) both; }
        @keyframes grLive { 0%,100% { box-shadow: 0 0 0 0 rgba(200,236,63,.5); } 50% { box-shadow: 0 0 0 12px rgba(200,236,63,0); } }
        .gr-live { animation: grLive 2.2s ease-out infinite; }
        @keyframes grNudge { 0%,100% { transform: translateY(0); } 50% { transform: translateY(4px); } }
        .gr-nudge { animation: grNudge 1.5s ease-in-out infinite; }
        @keyframes grFocus { 0% { box-shadow: 0 0 0 0 rgba(200,236,63,0); } 25% { box-shadow: 0 0 0 7px rgba(200,236,63,.5); } 100% { box-shadow: 0 0 0 0 rgba(200,236,63,0); } }
        .gr-focus { animation: grFocus 1.9s ease-out 1; }
        .gr-scroll::-webkit-scrollbar { width: 8px; height: 8px; }
        .gr-scroll::-webkit-scrollbar-track { background: #e2edf0; border-radius: 8px; }
        .gr-scroll::-webkit-scrollbar-thumb { background: #1d6247; border-radius: 8px; }
        .gr-select { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' stroke='%230a3f33' stroke-width='2.5' viewBox='0 0 24 24'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right .85rem center; background-size: 1rem; }
        #form-penerimaan, #siap-diterima, #riwayat-bpb { scroll-margin-top: 108px; }
        @media (prefers-reduced-motion: reduce) { .gr-rise, .gr-live, .gr-nudge, .gr-focus { animation: none !important; } }
    </style>

    @if(session('gr_success') || session('gr_error'))
        @php $ok = (bool) session('gr_success'); @endphp
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 6000)" x-show="show" x-cloak
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-3"
             class="fixed top-24 right-4 sm:right-6 z-[80] max-w-sm" role="status" aria-live="polite">
            <div class="relative overflow-hidden rounded-2xl px-5 py-4 shadow-2xl flex items-start gap-3 {{ $ok ? 'forest-hero shadow-brand-950/40' : 'bg-red-600 shadow-red-900/40' }}">
                @if($ok)<div class="forest-dots absolute inset-0 opacity-40"></div>@endif
                <span class="relative z-10 shrink-0 w-9 h-9 rounded-xl flex items-center justify-center {{ $ok ? 'bg-signal-400 text-brand-950' : 'bg-white/20 text-white' }}">{!! $gic($ok ? 'check' : 'alert', 'w-5 h-5') !!}</span>
                <p class="relative z-10 flex-1 text-base font-bold text-white">{{ session('gr_success') ?? session('gr_error') }}</p>
                <button type="button" @click="show = false" class="relative z-10 shrink-0 text-white/70 hover:text-white transition-colors">{!! $gic('x', 'w-4 h-4') !!}</button>
            </div>
        </div>
    @endif

    {{-- HERO --}}
    <div class="forest-hero relative overflow-hidden rounded-3xl text-white px-6 py-6 sm:px-8 sm:py-7 shadow-lg shadow-brand-950/25 gr-rise">
        <div class="forest-dots absolute inset-0 opacity-45"></div>
        <div class="absolute -top-24 -left-16 w-72 h-72 rounded-full bg-emerald-500/15 blur-3xl"></div>
        <div class="absolute -bottom-24 right-0 w-64 h-64 rounded-full bg-signal-400/10 blur-3xl"></div>

        <div class="relative z-10 flex flex-col xl:flex-row xl:items-center justify-between gap-6">
            <div class="min-w-0">
                <p class="text-xs font-bold uppercase tracking-[0.24em] text-signal-300">Gudang &amp; Pembelian</p>
                <h1 class="mt-2 text-3xl font-extrabold tracking-tight">Penerimaan Barang</h1>
                <p class="mt-2 text-base text-emerald-100/75 max-w-2xl">Catat barang masuk dari supplier. Stok, batch FIFO, audit trail, dan status PR diperbarui otomatis.</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 shrink-0">
                <div class="rounded-2xl bg-signal-400 text-brand-950 px-5 py-4 shadow-lg shadow-signal-400/20">
                    <p class="text-[10px] font-bold uppercase tracking-[0.2em]">Siap Diterima</p>
                    <p class="mt-1 text-2xl font-extrabold tabular-nums">{{ $stats['approved'] }} PR</p>
                    <p class="text-[11px] font-bold mt-0.5 tabular-nums">≈ Rp {{ number_format($stats['approved_value'], 0, ',', '.') }}</p>
                </div>
                <div class="flex flex-col gap-2">
                    <a href="{{ route('admin.purchase-requests') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 border border-white/15 text-sm font-bold hover:bg-white/20 transition-colors">{!! $gic('list', 'w-4 h-4') !!} Daftar PR</a>
                    <a href="{{ route('admin.suppliers') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 border border-white/15 text-sm font-bold hover:bg-white/20 transition-colors">{!! $gic('user', 'w-4 h-4') !!} Supplier</a>
                </div>
            </div>
        </div>

        <div class="relative z-10 mt-7 grid grid-cols-2 sm:grid-cols-4 gap-3">
            <button type="button" onclick="document.getElementById('siap-diterima')?.scrollIntoView({behavior:'smooth',block:'start'})"
                    class="text-left rounded-2xl px-4 py-3 border transition-all hover:-translate-y-0.5 {{ $stats['approved'] > 0 ? 'bg-signal-400 border-signal-400 text-brand-950 gr-live' : 'bg-white/5 border-white/10' }}">
                <span class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-[0.16em] {{ $stats['approved'] > 0 ? 'text-brand-900/70' : 'text-emerald-100/60' }}">{!! $gic('truck', 'w-3.5 h-3.5') !!} PR Disetujui</span>
                <span class="mt-1 block text-2xl font-extrabold tabular-nums">{{ $stats['approved'] }}</span>
            </button>
            <div class="rounded-2xl px-4 py-3 border bg-white/5 border-white/10">
                <span class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-[0.16em] text-emerald-100/60">{!! $gic('cal', 'w-3.5 h-3.5') !!} BPB Bulan Ini</span>
                <span class="mt-1 block text-2xl font-extrabold tabular-nums">{{ $stats['month_count'] }}</span>
            </div>
            <div class="rounded-2xl px-4 py-3 border bg-white/5 border-white/10">
                <span class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-[0.16em] text-emerald-100/60">{!! $gic('money', 'w-3.5 h-3.5') !!} Nilai Bulan Ini</span>
                <span class="mt-1 block text-lg font-extrabold tabular-nums text-signal-300">Rp {{ number_format($stats['month_value'], 0, ',', '.') }}</span>
            </div>
            <div class="rounded-2xl px-4 py-3 border {{ $stats['expiring'] > 0 ? 'bg-amber-600/70 border-amber-300/40' : 'bg-white/5 border-white/10' }}">
                <span class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-[0.16em] {{ $stats['expiring'] > 0 ? 'text-amber-50' : 'text-emerald-100/60' }}">{!! $gic('fire', 'w-3.5 h-3.5') !!} Batch ≤30 Hari</span>
                <span class="mt-1 block text-2xl font-extrabold tabular-nums">{{ $stats['expiring'] }}</span>
            </div>
        </div>
    </div>

    {{-- SIAP DITERIMA --}}
    <div id="siap-diterima" class="relative overflow-hidden rounded-[2rem] shadow-xl shadow-brand-950/15 gr-rise">
        <div class="gr-incoming relative overflow-hidden px-6 py-5">
            <div class="forest-dots absolute inset-0 opacity-50"></div>
            <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-signal-400 via-brand-400 to-signal-400"></div>
            <div class="relative z-10 flex flex-wrap items-center justify-between gap-4 pt-1">
                <div class="flex items-center gap-4 min-w-0">
                    <span class="shrink-0 w-12 h-12 rounded-2xl flex items-center justify-center {{ count($approved) ? 'bg-signal-400 text-brand-950 gr-live' : 'bg-white/10 text-emerald-100/60' }}">{!! $gic('truck', 'w-6 h-6') !!}</span>
                    <div class="min-w-0">
                        <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-signal-300">Disetujui Owner — Menunggu Kedatangan</p>
                        <h2 class="mt-1 text-xl font-extrabold text-white tracking-tight">PR Siap Diterima</h2>
                        <p class="text-sm text-emerald-100/75">{{ count($approved) ? count($approved) . ' PR bisa langsung dicatat penerimaannya.' : 'Belum ada PR yang disetujui untuk diterima.' }}</p>
                    </div>
                </div>
                @if(count($approved))
                    <button type="button" @click="goForm()" class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-white text-brand-900 text-base font-extrabold hover:bg-ice-100 shadow-lg shadow-brand-950/20 transition-all">{!! $gic('down', 'w-4 h-4') !!} Ke Form</button>
                @endif
            </div>
        </div>

        @if(count($approved))
            <div class="bg-ice-50 border-x-2 border-b-2 border-brand-200 rounded-b-[2rem] p-5 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach($approved as $a)
                    <div class="relative overflow-hidden rounded-2xl bg-white border-2 {{ $a['overdue'] ? 'border-red-300' : 'border-brand-200' }} p-5 shadow-lg shadow-brand-950/5 hover:-translate-y-1 transition-transform">
                        <div class="absolute left-0 top-0 bottom-0 w-2 {{ $a['overdue'] ? 'bg-red-500' : 'bg-signal-400' }}"></div>
                        <div class="pl-2">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="text-base font-extrabold text-brand-950">{{ $a['number'] }}</p>
                                    <p class="text-xs text-gray-500">{{ $a['approved'] ?? '-' }} • {{ $a['approver'] }}</p>
                                </div>
                                <span class="shrink-0 px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase {{ $UG[$a['urgency']][1] ?? $UG['normal'][1] }}">{{ $UG[$a['urgency']][0] ?? $a['urgency'] }}</span>
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
                                @if($a['needed'])
                                    <span class="inline-flex items-center gap-1.5 font-bold {{ $a['overdue'] ? 'text-red-600' : 'text-gray-500' }}">{!! $gic('clock', 'w-3.5 h-3.5') !!} {{ $a['needed'] }}</span>
                                @endif
                            </div>

                            <button type="button" wire:click="fillFromPr({{ $a['id'] }})"
                                    class="mt-4 w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-brand-600 text-signal-300 text-base font-extrabold hover:bg-brand-700 shadow-md shadow-brand-900/25 transition-all hover:-translate-y-0.5">
                                {!! $gic('save', 'w-4 h-4') !!} Isi Form dari PR Ini
                                <span class="gr-nudge">{!! $gic('down', 'w-4 h-4') !!}</span>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white border-x border-b border-ice-200 rounded-b-[2rem] p-10 text-center">
                <span class="inline-flex w-16 h-16 rounded-full bg-ice-100 text-brand-500 items-center justify-center mx-auto">{!! $gic('check', 'w-7 h-7') !!}</span>
                <p class="mt-4 text-lg font-extrabold text-brand-950">Tidak ada PR menunggu penerimaan</p>
                <p class="mt-1 text-base text-gray-500">Anda tetap bisa mencatat pembelian langsung tanpa PR di form bawah.</p>
            </div>
        @endif
    </div>

    {{-- FORM + RINGKASAN --}}
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">
        <div id="form-penerimaan" class="xl:col-span-8 relative overflow-hidden rounded-[2rem] bg-white border-2 {{ $purchase_request_id ? 'border-signal-400' : 'border-ice-200' }} shadow-sm gr-rise transition-colors">
            <div class="absolute left-0 top-8 bottom-8 w-1.5 rounded-r-full bg-gradient-to-b from-signal-400 to-brand-600"></div>
            <div class="p-6 sm:p-7 pl-7 sm:pl-8">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-[0.22em] {{ $purchase_request_id ? 'text-signal-600' : 'text-gray-400' }}">{{ $purchase_request_id ? 'Terhubung ke PR' : 'Pembelian langsung' }}</p>
                        <h2 class="mt-1 text-xl font-extrabold text-brand-950 tracking-tight">Form Penerimaan</h2>
                        <p class="text-sm text-gray-500 mt-1">Harga &amp; kadaluarsa terisi otomatis dari master bahan.</p>
                    </div>
                    @if($purchase_request_id)
                        <button type="button" wire:click="$set('purchase_request_id','')" class="px-4 py-2 rounded-xl bg-ice-100 text-brand-800 text-sm font-bold hover:bg-ice-200 transition-colors">Lepas PR</button>
                    @endif
                </div>

                <form wire:submit="save" class="mt-6 space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-extrabold text-brand-950 mb-1.5">Supplier <span class="text-red-500">*</span></label>
                            <select wire:model="supplier_id" class="gr-select w-full pl-4 pr-10 py-3 text-base border border-ice-300 rounded-xl bg-ice-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25 cursor-pointer @error('supplier_id') border-red-400 bg-red-50 @enderror">
                                <option value="">— Pilih Supplier —</option>
                                @foreach($suppliers as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                            @error('supplier_id') <p class="mt-1.5 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-extrabold text-brand-950 mb-1.5">Tanggal Terima <span class="text-red-500">*</span></label>
                            <input type="date" wire:model="receipt_date" class="w-full px-4 py-3 text-base border border-ice-300 rounded-xl bg-ice-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25">
                            @error('receipt_date') <p class="mt-1.5 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-extrabold text-brand-950 mb-1.5">No. Surat Jalan Supplier</label>
                            <input type="text" wire:model="supplier_doc_number" placeholder="SJ-ABC-789" class="w-full px-4 py-3 text-base border border-ice-300 rounded-xl bg-ice-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25">
                        </div>
                        <div>
                            <label class="block text-sm font-extrabold text-brand-950 mb-1.5">Tautkan ke PR</label>
                            <select wire:model.live="purchase_request_id" class="gr-select w-full pl-4 pr-10 py-3 text-base border border-ice-300 rounded-xl bg-ice-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25 cursor-pointer">
                                <option value="">— Pembelian langsung (tanpa PR) —</option>
                                @foreach($approved as $a)
                                    <option value="{{ $a['id'] }}">{{ $a['number'] }} — {{ $a['material'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="border-t-2 border-dashed border-ice-200 pt-5">
                        <div class="flex items-center justify-between gap-3 mb-3">
                            <div>
                                <h3 class="text-base font-extrabold text-brand-950">Barang Diterima <span class="text-red-500">*</span></h3>
                                <p class="text-xs text-gray-500">{{ count($items) }} baris • subtotal dihitung otomatis</p>
                            </div>
                            <button type="button" wire:click="addItem" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-ice-100 text-brand-800 text-sm font-bold hover:bg-ice-200 transition-colors">{!! $gic('plus', 'w-4 h-4') !!} Tambah Baris</button>
                        </div>

                        <div class="space-y-3">
                            @foreach($items as $i => $item)
                                @php $c = $catalog[$item['material_id']] ?? null; @endphp
                                <div class="rounded-2xl border border-ice-200 bg-ice-50/60 p-4">
                                    <div class="flex items-center justify-between gap-3 mb-3">
                                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-600 text-signal-300 text-xs font-extrabold uppercase tracking-wider">Baris {{ $i + 1 }}</span>
                                        <div class="flex items-center gap-2">
                                            @if($c)
                                                <span class="text-xs text-gray-500 tabular-nums">Sisa {{ number_format($c['net'], 2) }} → <b class="text-signal-600">{{ number_format($c['net'] + (float) $item['quantity'], 2) }} {{ $c['unit'] }}</b></span>
                                            @endif
                                            <button type="button" wire:click="removeItem({{ $i }})" class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors">{!! $gic('x', 'w-4 h-4') !!}</button>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                                        <div class="sm:col-span-5">
                                            <label class="block text-xs font-bold text-gray-500 mb-1">Bahan</label>
                                            <select wire:model.live="items.{{ $i }}.material_id" class="gr-select w-full pl-3 pr-9 py-2.5 text-sm border border-ice-300 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25 cursor-pointer">
                                                <option value="">— Pilih —</option>
                                                @foreach($catalog as $id => $m)
                                                    <option value="{{ $id }}">{{ $m['name'] }}</option>
                                                @endforeach
                                            </select>
                                            @error('items.'.$i.'.material_id') <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p> @enderror
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label class="block text-xs font-bold text-gray-500 mb-1">Qty{{ $c ? ' ('.$c['unit'].')' : '' }}</label>
                                            <input type="number" step="0.0001" min="0" wire:model.live.debounce.300ms="items.{{ $i }}.quantity" class="w-full px-3 py-2.5 text-sm font-bold tabular-nums border border-ice-300 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25">
                                            @error('items.'.$i.'.quantity') <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p> @enderror
                                        </div>
                                        <div class="sm:col-span-3">
                                            <label class="block text-xs font-bold text-gray-500 mb-1">Harga / Unit</label>
                                            <input type="number" step="0.01" min="0" wire:model.live.debounce.300ms="items.{{ $i }}.unit_cost" class="w-full px-3 py-2.5 text-sm font-bold tabular-nums border border-ice-300 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25">
                                            @error('items.'.$i.'.unit_cost') <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p> @enderror
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label class="block text-xs font-bold text-gray-500 mb-1">Kadaluarsa</label>
                                            <input type="date" wire:model="items.{{ $i }}.expiry_date" class="w-full px-3 py-2.5 text-sm border border-ice-300 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25">
                                        </div>
                                    </div>

                                    <div class="mt-3 flex flex-wrap items-center justify-between gap-2">
                                        <span class="text-xs text-gray-500">
                                            @if($c && $c['batch'])
                                                <b class="text-brand-700">Batch FIFO aktif</b>{{ $c['shelf'] > 0 ? ' • umur simpan '.$c['shelf'].' hari' : '' }}
                                            @elseif($c)
                                                Bahan tanpa pelacakan batch
                                            @else
                                                Pilih bahan untuk melihat konteks stok
                                            @endif
                                        </span>
                                        <span class="text-base font-extrabold text-brand-950 tabular-nums">Rp {{ number_format((float) $item['quantity'] * (float) $item['unit_cost'], 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 border-t-2 border-dashed border-ice-200 pt-5">
                        @foreach([['delivery_note_file', 'Scan Surat Jalan'], ['invoice_file', 'Scan Faktur Supplier']] as $f)
                            <div>
                                <label class="block text-sm font-extrabold text-brand-950 mb-1.5">{{ $f[1] }} <span class="text-gray-400 font-semibold">(opsional)</span></label>
                                <div class="relative rounded-xl border-2 border-dashed border-ice-300 bg-ice-50 p-4 text-center hover:border-brand-500 transition-colors cursor-pointer">
                                    <input type="file" wire:model="{{ $f[0] }}" accept=".jpg,.jpeg,.png,.pdf" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                    <span class="inline-flex text-brand-500">{!! $gic('clip', 'w-5 h-5') !!}</span>
                                    <p class="mt-1 text-sm text-gray-500"><b class="text-brand-700">Klik untuk upload</b></p>
                                    <p class="text-xs text-gray-400">JPG, PNG, PDF • maks 5MB</p>
                                </div>
                                <div wire:loading wire:target="{{ $f[0] }}" class="mt-1.5 text-sm font-bold text-brand-700">Mengunggah...</div>
                                @error($f[0]) <p class="mt-1.5 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
                            </div>
                        @endforeach
                    </div>

                    <div>
                        <label class="block text-sm font-extrabold text-brand-950 mb-1.5">Catatan Penerimaan</label>
                        <textarea wire:model="notes" rows="3" maxlength="500" placeholder="Kondisi barang, selisih qty, kemasan rusak, dll."
                                  class="gr-scroll w-full px-4 py-3 text-base border border-ice-300 rounded-xl bg-ice-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25 resize-y"></textarea>
                        @error('notes') <p class="mt-1.5 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-6 py-4 rounded-2xl bg-brand-600 text-signal-300 text-base font-extrabold hover:bg-brand-700 shadow-md shadow-brand-900/25 hover:-translate-y-0.5 transition-all disabled:opacity-60"
                            wire:loading.attr="disabled" wire:target="save">
                        <span wire:loading.remove wire:target="save" class="inline-flex items-center gap-2">{!! $gic('save', 'w-5 h-5') !!} Simpan Penerimaan &amp; Update Stok</span>
                        <span wire:loading wire:target="save" class="inline-flex items-center gap-2">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.4 0 0 5.4 0 12h4z"/></svg> Menyimpan...
                        </span>
                    </button>
                </form>
            </div>
        </div>

        {{-- RINGKASAN --}}
        <div class="xl:col-span-4 xl:sticky xl:top-24 forest-hero relative overflow-hidden rounded-[2rem] p-6 text-white shadow-lg shadow-brand-950/20 gr-rise">
            <div class="forest-dots absolute inset-0 opacity-45"></div>
            <div class="absolute -bottom-16 -right-12 w-48 h-48 rounded-full bg-signal-400/10 blur-3xl"></div>
            <div class="relative">
                <p class="text-xs font-bold uppercase tracking-[0.22em] text-signal-300">Ringkasan BPB</p>
                <div class="mt-4 space-y-2.5 text-sm">
                    <div class="flex justify-between gap-3"><span class="text-emerald-100/60">Baris barang</span><span class="font-bold tabular-nums">{{ count($items) }}</span></div>
                    <div class="flex justify-between gap-3"><span class="text-emerald-100/60">Tanggal</span><span class="font-bold tabular-nums">{{ $receipt_date ? \Carbon\Carbon::parse($receipt_date)->format('d M Y') : '—' }}</span></div>
                    <div class="flex justify-between gap-3"><span class="text-emerald-100/60">Tautan PR</span><span class="font-bold">{{ $purchase_request_id ? 'Ada' : 'Pembelian langsung' }}</span></div>
                </div>

                <div class="mt-5 pt-5 border-t border-white/10 space-y-2.5">
                    @foreach($lines as $l)
                        <div class="flex items-start justify-between gap-3 text-sm">
                            <span class="min-w-0 truncate {{ $l['name'] ? 'text-white' : 'text-emerald-100/40 italic' }}">{{ $l['name'] ?? 'baris '.($l['index']+1).' belum dipilih' }}</span>
                            <span class="shrink-0 font-bold tabular-nums">Rp {{ number_format($l['sub'], 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="mt-5 pt-5 border-t border-white/10">
                    <p class="text-xs font-bold uppercase tracking-[0.22em] text-emerald-100/60">Total Nilai BPB</p>
                    <p class="mt-1 text-3xl font-extrabold text-signal-300 tabular-nums tracking-tight">Rp {{ number_format($grand, 0, ',', '.') }}</p>
                </div>

                @if(collect($lines)->whereNotNull('name')->count())
                    <div class="mt-5 pt-5 border-t border-white/10">
                        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-emerald-100/60 mb-2.5">Proyeksi Stok</p>
                        <div class="space-y-2">
                            @foreach($lines as $l)
                                @if($l['name'])
                                    <div class="text-xs">
                                        <div class="flex justify-between gap-2"><span class="truncate text-emerald-100/75">{{ $l['name'] }}</span><span class="shrink-0 font-bold tabular-nums {{ $l['after'] <= $l['min'] ? 'text-amber-300' : 'text-signal-300' }}">{{ number_format($l['net'], 2) }} → {{ number_format($l['after'], 2) }}</span></div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                <p class="mt-5 text-xs text-emerald-100/60">Menyimpan akan membuat BPB, menambah stok, membentuk batch FIFO, mencatat audit trail, dan menutup PR menjadi <b class="text-signal-300">received</b>.</p>
            </div>
        </div>
    </div>

    {{-- RIWAYAT --}}
    <div id="riwayat-bpb" class="rounded-[2rem] bg-white border border-ice-200 shadow-sm overflow-hidden gr-rise">
        <div class="px-6 py-4 border-b border-ice-200 bg-ice-50 flex flex-col lg:flex-row lg:items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-extrabold text-brand-950">Riwayat Penerimaan</h2>
                <p class="text-sm text-gray-500">12 BPB terakhir • klik PDF untuk cetak bukti.</p>
            </div>
            <div class="relative lg:w-72">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-brand-400">{!! $gic('search', 'w-4 h-4') !!}</span>
                <input type="text" wire:model.live.debounce.300ms="historySearch" placeholder="Cari BPB, surat jalan, supplier..."
                       class="w-full pl-11 pr-4 py-2.5 text-base border border-ice-300 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25 focus:border-brand-500">
            </div>
        </div>

        <div wire:loading.class="opacity-40 pointer-events-none" class="divide-y divide-ice-100 transition-opacity duration-200">
            @forelse($history as $h)
                <div class="p-5 flex flex-col lg:flex-row lg:items-center gap-4 hover:bg-ice-50/60 transition-colors">
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-base font-extrabold text-brand-950">{{ $h['bpb'] }}</span>
                            <span class="px-2.5 py-0.5 rounded-full bg-signal-400 text-brand-950 text-[10px] font-extrabold uppercase">Selesai</span>
                            @if($h['doc'])<span class="text-xs text-gray-400">SJ: {{ $h['doc'] }}</span>@endif
                        </div>
                        <p class="mt-1 text-sm text-gray-600"><b class="text-brand-900">{{ $h['supplier'] }}</b> • {{ $h['date'] }} • diterima {{ $h['receiver'] }}</p>
                        <div class="mt-1.5 flex flex-wrap gap-1.5">
                            @foreach($h['items'] as $it)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-ice-100 text-brand-800 text-xs font-semibold">
                                    {!! $gic('box', 'w-3 h-3') !!} {{ $it['name'] }} • {{ number_format($it['qty'], 2) }} {{ $it['unit'] }}@if($it['batch']) • {{ $it['batch'] }}@endif
                                </span>
                            @endforeach
                        </div>
                    </div>
                    <div class="flex items-center gap-4 shrink-0">
                        <p class="text-lg font-extrabold text-brand-950 tabular-nums">Rp {{ number_format($h['total'], 0, ',', '.') }}</p>
                        <a href="{{ route('admin.purchase-receipts.pdf', $h['id']) }}" target="_blank"
                           class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-brand-600 text-white text-sm font-bold hover:bg-brand-700 transition-colors">
                            {!! $gic('print', 'w-4 h-4') !!} PDF
                        </a>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <span class="inline-flex w-16 h-16 rounded-full bg-ice-100 text-brand-500 items-center justify-center mx-auto">{!! $gic('note', 'w-7 h-7') !!}</span>
                    <p class="mt-4 text-lg font-extrabold text-brand-950">Belum ada penerimaan tercatat</p>
                    <p class="mt-1 text-base text-gray-500">Gunakan form di atas untuk mencatat barang masuk.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>