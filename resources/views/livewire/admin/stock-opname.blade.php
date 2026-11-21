@php
    $O = [
        'clipboard' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
        'box'    => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
        'check'  => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        'alert'  => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
        'x'      => 'M6 18L18 6M6 6l12 12',
        'plus'   => 'M12 4v16m8-8H4',
        'up'     => 'M5 10l7-7m0 0l7 7m-7-7v18',
        'down'   => 'M19 14l-7 7m0 0l-7-7m7 7V3',
        'save'   => 'M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4',
        'search' => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z',
        'trash'  => 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16',
        'eye'    => 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z',
        'undo'   => 'M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6',
        'equal'  => 'M5 9h14M5 15h14',
        'bolt'   => 'M13 10V3L4 14h7v7l9-11h-7z',
        'note'   => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'shield' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
        'arrow'  => 'M14 5l7 7m0 0l-7 7m7-7H3',
        'tag'    => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z',
        'lock'   => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z',
    ];
    $oic = fn($k, $c = 'w-5 h-5') => '<svg class="' . $c . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="' . ($O[$k] ?? $O['box']) . '"/></svg>';
    $empty = fn($t) => '<p class="px-6 py-8 text-center text-sm font-bold text-gray-400">' . $t . '</p>';

    $SS = [
        'draft'     => ['Draft', 'bg-amber-100 text-amber-800', 'note'],
        'posted'    => ['Diposting', 'bg-signal-400 text-brand-950', 'check'],
        'cancelled' => ['Dibatalkan', 'bg-ice-300 text-gray-600', 'x'],
    ];
    $scopeLabel = ['all' => 'Semua bahan', 'critical' => 'Bahan kritis', 'category' => 'Per divisi', 'manual' => 'Pilih manual'];
    $stateMeta = [
        'uncounted' => ['Belum dihitung', 'bg-ice-200 text-gray-600'],
        'match'     => ['Cocok', 'bg-signal-100 text-signal-700'],
        'surplus'   => ['Lebih', 'bg-sky-100 text-sky-700'],
        'shrink'    => ['Kurang', 'bg-red-100 text-red-700'],
    ];
    $progress = $totals['items'] > 0 ? (int) round($totals['counted'] / $totals['items'] * 100) : 0;
@endphp

<div class="w-full space-y-4">
    <style>
        .op-hero { background: radial-gradient(ellipse at top left, rgba(200,236,63,.20), transparent 58%), radial-gradient(ellipse at bottom right, rgba(16,185,129,.16), transparent 55%), linear-gradient(135deg,#0a3f33 0%,#062e25 55%,#03201a 100%); }
        .op-warn { background: radial-gradient(ellipse at top left, rgba(251,191,36,.30), transparent 58%), linear-gradient(135deg,#78350f 0%,#4d2c0a 55%,#291505 100%); }
        .op-grid { background-image: linear-gradient(rgba(200,236,63,.08) 1px, transparent 1px), linear-gradient(90deg, rgba(200,236,63,.08) 1px, transparent 1px); background-size: 28px 28px; }
        @keyframes opRise { from { opacity:0; transform: translateY(12px);} to { opacity:1; transform:none;} }
        .op-rise { animation: opRise .45s cubic-bezier(.2,.7,.2,1) both; }
        @keyframes opLive { 0%,100% { box-shadow: 0 0 0 0 rgba(200,236,63,.5);} 50% { box-shadow: 0 0 0 11px rgba(200,236,63,0);} }
        .op-live { animation: opLive 2.2s ease-out infinite; }
        .op-scroll::-webkit-scrollbar { width: 9px; height: 9px; }
        .op-scroll::-webkit-scrollbar-track { background: #e2edf0; border-radius: 9px; }
        .op-scroll::-webkit-scrollbar-thumb { background: #1d6247; border-radius: 9px; border: 2px solid #e2edf0; }
        .op-scroll::-webkit-scrollbar-thumb:hover { background: #0a3f33; }
        .op-select { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' stroke='%230a3f33' stroke-width='2.5' viewBox='0 0 24 24'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right .7rem center; background-size: .9rem; }
        .op-num::-webkit-outer-spin-button, .op-num::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
        .op-num { -moz-appearance: textfield; }
        #lembar, #riwayat { scroll-margin-top: 104px; }
        @media (prefers-reduced-motion: reduce) { .op-rise,.op-live { animation: none !important; } }
    </style>

    {{-- TOAST --}}
    @if(session('op_success') || session('op_warning') || session('op_error'))
        @php
            $msg = session('op_success') ?? session('op_warning') ?? session('op_error');
            $tone = session('op_success') ? 'ok' : (session('op_error') ? 'err' : 'warn');
        @endphp
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 6000)" x-show="show" x-cloak
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-3"
             class="fixed top-24 right-4 sm:right-6 z-[80] max-w-sm" role="status" aria-live="polite">
            <div class="relative overflow-hidden rounded-2xl px-5 py-4 shadow-2xl flex items-start gap-3 {{ $tone === 'ok' ? 'forest-hero shadow-brand-950/40' : ($tone === 'err' ? 'bg-red-600 shadow-red-900/40' : 'op-warn shadow-amber-950/40') }}">
                @if($tone === 'ok')<div class="forest-dots absolute inset-0 opacity-40"></div>@endif
                <span class="relative z-10 shrink-0 w-9 h-9 rounded-xl flex items-center justify-center {{ $tone === 'ok' ? 'bg-signal-400 text-brand-950' : 'bg-white text-red-700' }}">{!! $oic($tone === 'ok' ? 'check' : 'alert', 'w-5 h-5') !!}</span>
                <p class="relative z-10 flex-1 text-base font-bold text-white">{{ $msg }}</p>
                <button type="button" @click="show = false" class="relative z-10 shrink-0 text-white/70 hover:text-white transition-colors">{!! $oic('x', 'w-4 h-4') !!}</button>
            </div>
        </div>
    @endif

    {{-- HERO --}}
    <div class="forest-hero relative overflow-hidden rounded-3xl text-white shadow-lg shadow-brand-950/25 op-rise">
        <div class="forest-dots absolute inset-0 opacity-40"></div>
        <div class="op-grid absolute inset-0 opacity-45"></div>
        <div class="absolute -top-24 -left-16 w-72 h-72 rounded-full bg-emerald-500/12 blur-3xl"></div>

        <div class="relative z-10 px-6 sm:px-8 pt-6 pb-5 flex flex-col xl:flex-row xl:items-start justify-between gap-5">
            <div class="min-w-0">
                <p class="text-xs font-bold uppercase tracking-[0.24em] text-signal-300">Rekonsiliasi Gudang</p>
                <h1 class="mt-2 text-2xl sm:text-3xl font-extrabold tracking-tight">Stock Opname</h1>
                <p class="mt-2 text-base text-emerald-100/75 max-w-2xl">Hitung fisik, bandingkan dengan sistem, wajibkan alasan setiap selisih, lalu posting. Stok diperbarui dan koreksi tercatat di audit trail.</p>
            </div>
            <div class="shrink-0 flex flex-wrap gap-3">
                <div class="rounded-2xl bg-white/5 border border-white/10 px-4 py-3">
                    <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-emerald-100/55">Bahan Aktif</p>
                    <p class="mt-0.5 text-2xl font-extrabold tabular-nums">{{ $stats['materials'] }}</p>
                </div>
                <div class="rounded-2xl px-4 py-3 border {{ $stats['draft'] > 0 ? 'bg-amber-600/70 border-amber-300/40' : 'bg-white/5 border-white/10' }}">
                    <p class="text-[10px] font-bold uppercase tracking-[0.18em] {{ $stats['draft'] > 0 ? 'text-amber-50' : 'text-emerald-100/55' }}">Draft Terbuka</p>
                    <p class="mt-0.5 text-2xl font-extrabold tabular-nums">{{ $stats['draft'] }}</p>
                </div>
                <div class="rounded-2xl bg-white/5 border border-white/10 px-4 py-3">
                    <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-emerald-100/55">Posting Bulan Ini</p>
                    <p class="mt-0.5 text-2xl font-extrabold tabular-nums">{{ $stats['month_posted'] }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- MULAI OPNAME --}}
    @if(!$active)
        <section class="op-hero relative overflow-hidden rounded-3xl text-white p-6 sm:p-8 shadow-lg shadow-brand-950/20 op-rise">
            <div class="forest-dots absolute inset-0 opacity-40"></div>
            <div class="relative z-10">
                <div class="flex items-center gap-3">
                    <span class="w-12 h-12 rounded-2xl bg-signal-400 text-brand-950 flex items-center justify-center op-live">{!! $oic('clipboard', 'w-6 h-6') !!}</span>
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-signal-300">Langkah 1</p>
                        <h2 class="text-xl font-extrabold tracking-tight">Buat Lembar Opname</h2>
                    </div>
                </div>
                <p class="mt-3 text-sm text-emerald-100/70 max-w-2xl">Pilih cakupan penghitungan. Lembar tersimpan sebagai draft sehingga bisa dilanjutkan kapan saja sebelum diposting.</p>

                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                    <button type="button" wire:click="startSession('all')" class="text-left rounded-2xl bg-white/8 border border-white/15 hover:bg-white/15 hover:-translate-y-0.5 p-5 transition-all">
                        <span class="inline-flex w-10 h-10 rounded-xl bg-signal-400 text-brand-950 items-center justify-center">{!! $oic('box', 'w-5 h-5') !!}</span>
                        <p class="mt-3 text-base font-extrabold">Semua Bahan</p>
                        <p class="mt-1 text-xs text-emerald-100/65">Opname menyeluruh {{ $stats['materials'] }} bahan aktif.</p>
                    </button>
                    <button type="button" wire:click="startSession('critical')" class="text-left rounded-2xl bg-white/8 border border-white/15 hover:bg-white/15 hover:-translate-y-0.5 p-5 transition-all">
                        <span class="inline-flex w-10 h-10 rounded-xl bg-red-500 text-white items-center justify-center">{!! $oic('alert', 'w-5 h-5') !!}</span>
                        <p class="mt-3 text-base font-extrabold">Bahan Kritis</p>
                        <p class="mt-1 text-xs text-emerald-100/65">Hanya yang di bawah batas minimum.</p>
                    </button>
                    <div class="rounded-2xl bg-white/8 border border-white/15 p-5">
                        <span class="inline-flex w-10 h-10 rounded-xl bg-white/15 text-signal-300 items-center justify-center">{!! $oic('tag', 'w-5 h-5') !!}</span>
                        <p class="mt-3 text-base font-extrabold">Per Divisi</p>
                        <select wire:model="startCategory" class="op-select mt-2 w-full pl-3 pr-9 py-2 text-sm font-bold rounded-xl bg-white/10 border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-signal-400/50 cursor-pointer">
                            <option value="" class="text-brand-950">— pilih divisi —</option>
                            @foreach($categories as $c)
                                <option value="{{ $c['id'] }}" class="text-brand-950">{{ $c['name'] }}</option>
                            @endforeach
                        </select>
                        <button type="button" wire:click="startSession('category')" class="mt-2 w-full px-3 py-2 rounded-xl bg-signal-400 text-brand-950 text-xs font-extrabold hover:bg-signal-500 transition-colors">Mulai divisi ini</button>
                    </div>
                    <button type="button" wire:click="startSession('manual')" class="text-left rounded-2xl bg-white/8 border border-white/15 hover:bg-white/15 hover:-translate-y-0.5 p-5 transition-all">
                        <span class="inline-flex w-10 h-10 rounded-xl bg-white/15 text-signal-300 items-center justify-center">{!! $oic('plus', 'w-5 h-5') !!}</span>
                        <p class="mt-3 text-base font-extrabold">Pilih Manual</p>
                        <p class="mt-1 text-xs text-emerald-100/65">Lembar kosong, tambahkan bahan satu per satu.</p>
                    </button>
                </div>
            </div>
        </section>
    @endif

    {{-- LEMBAR KERJA --}}
    @if($active)
        <section id="lembar" class="grid grid-cols-1 xl:grid-cols-12 gap-4 items-start">
            <div class="xl:col-span-8 space-y-4 min-w-0">
                <div class="rounded-3xl bg-white border-2 border-amber-300 shadow-sm overflow-hidden op-rise">
                    <div class="op-warn relative overflow-hidden px-6 py-4 text-white">
                        <div class="absolute top-0 left-0 right-0 h-1" style="background-image: repeating-linear-gradient(45deg,#b45309 0 9px,#fde68a 9px 18px)"></div>
                        <div class="relative z-10 flex flex-wrap items-center justify-between gap-3 pt-1">
                            <div class="min-w-0">
                                <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-amber-200">Langkah 2 · Lembar Kerja</p>
                                <h2 class="mt-1 text-xl font-extrabold tracking-tight">{{ $active['number'] }}</h2>
                                <p class="text-xs text-amber-50/80">{{ $scopeLabel[$active['scope']] ?? $active['scope'] }} · dibuat {{ $active['created'] }} oleh {{ $active['counter'] }}</p>
                            </div>
                            <div class="shrink-0 text-right">
                                <p class="text-[10px] font-bold uppercase tracking-wider text-amber-200">Progres Hitung</p>
                                <p class="text-2xl font-extrabold tabular-nums">{{ $totals['counted'] }}/{{ $totals['items'] }}</p>
                                <div class="mt-1 w-32 h-2 rounded-full bg-white/20 overflow-hidden">
                                    <div class="h-full rounded-full bg-signal-400 transition-all duration-500" style="width: {{ max(3, $progress) }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="px-5 py-3 bg-ice-50 border-b border-ice-200 flex flex-col lg:flex-row gap-3 lg:items-center">
                        <div class="relative flex-1 min-w-0">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-brand-400">{!! $oic('search', 'w-4 h-4') !!}</span>
                            <input type="text" wire:model.live.debounce.250ms="search" placeholder="Cari bahan di lembar ini..." class="w-full pl-10 pr-4 py-2.5 text-sm border border-ice-300 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25 focus:border-brand-500">
                        </div>
                        <div class="flex flex-wrap items-center gap-2 shrink-0">
                            @foreach(['all' => 'Semua', 'uncounted' => 'Belum dihitung', 'variance' => 'Ada selisih', 'match' => 'Cocok'] as $k => $l)
                                <button type="button" wire:click="setRowFilter('{{ $k }}')" class="px-3.5 py-1.5 rounded-full text-xs font-extrabold transition-all {{ $rowFilter === $k ? 'bg-brand-600 text-signal-300 shadow-sm' : 'bg-white border border-ice-300 text-brand-800 hover:bg-ice-100' }}">{{ $l }}</button>
                            @endforeach
                            <button type="button" wire:click="fillAllMatch" wire:confirm="Isi semua baris yang masih kosong dengan angka stok sistem?" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-signal-100 border border-signal-300 text-signal-700 text-xs font-extrabold hover:bg-signal-200 transition-colors">{!! $oic('equal', 'w-3.5 h-3.5') !!} Samakan sisa</button>
                        </div>
                    </div>

                    @if(count($rows))
                        <div class="op-scroll overflow-x-auto">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="text-[10px] uppercase tracking-wider text-gray-400 border-b border-ice-200">
                                        <th class="px-5 py-2.5 font-bold">Bahan</th>
                                        <th class="px-3 py-2.5 font-bold text-right">Sistem</th>
                                        <th class="px-3 py-2.5 font-bold text-center">Hitung Fisik</th>
                                        <th class="px-3 py-2.5 font-bold text-right">Selisih</th>
                                        <th class="px-3 py-2.5 font-bold">Alasan &amp; Catatan</th>
                                        <th class="px-4 py-2.5 font-bold text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-ice-100">
                                    @foreach($rows as $r)
                                        <tr class="align-top hover:bg-ice-50/60 transition-colors border-l-4 {{ $r['state'] === 'shrink' ? 'border-l-red-500' : ($r['state'] === 'surplus' ? 'border-l-sky-400' : ($r['state'] === 'match' ? 'border-l-signal-400' : 'border-l-transparent')) }}"
                                            x-data="{ phys: @js($r['physical_qty']), sys: {{ $r['system_qty'] }}, get v() { return this.phys === '' || this.phys === null ? null : Number(this.phys) - this.sys; } }">
                                            <td class="px-5 py-3">
                                                <p class="text-sm font-extrabold text-brand-950 leading-tight">{{ $r['name'] }}</p>
                                                <p class="text-[11px] text-gray-500">{{ $r['category'] }} · {{ $r['unit'] }}</p>
                                                <span class="mt-1 inline-flex px-2 py-0.5 rounded text-[9px] font-extrabold uppercase {{ $stateMeta[$r['state']][1] }}">{{ $stateMeta[$r['state']][0] }}</span>
                                            </td>
                                            <td class="px-3 py-3 text-right">
                                                <p class="text-base font-extrabold text-brand-900 tabular-nums leading-none">{{ number_format($r['system_qty'], 2) }}</p>
                                                <p class="text-[10px] text-amber-600 tabular-nums mt-0.5">alokasi {{ number_format($r['reserved_qty'], 2) }}</p>
                                            </td>
                                            <td class="px-3 py-3">
                                                <input type="number" step="0.0001" min="0" inputmode="decimal"
                                                       wire:model.blur="items.{{ $r['index'] }}.physical_qty"
                                                       @input="phys = $event.target.value"
                                                       placeholder="0"
                                                       class="op-num w-28 px-3 py-2 text-base font-extrabold tabular-nums text-center border-2 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25 transition-colors {{ $r['counted'] ? 'border-brand-400' : 'border-amber-300' }}">
                                                <p class="mt-1 text-[10px] text-gray-400 text-center">Tab untuk hitung</p>
                                            </td>
                                            <td class="px-3 py-3 text-right">
                                                <p class="text-base font-extrabold tabular-nums leading-none"
                                                   :class="v === null ? 'text-gray-300' : (v > 0 ? 'text-sky-600' : (v < 0 ? 'text-red-600' : 'text-signal-600'))"
                                                   x-text="v === null ? '—' : (v > 0 ? '+' : '') + v.toFixed(2)">{{ $r['counted'] ? ($r['variance_qty'] > 0 ? '+' : '') . number_format($r['variance_qty'], 2) : '—' }}</p>
                                                <p class="text-[10px] font-bold tabular-nums mt-0.5 {{ $r['variance_value'] < 0 ? 'text-red-500' : ($r['variance_value'] > 0 ? 'text-sky-600' : 'text-gray-400') }}">Rp {{ number_format($r['variance_value'], 0, ',', '.') }}</p>
                                            </td>
                                            <td class="px-3 py-3">
                                                <select wire:model.live="items.{{ $r['index'] }}.reason"
                                                        class="op-select w-full min-w-[11rem] pl-3 pr-8 py-2 text-xs font-bold border rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25 cursor-pointer {{ $r['counted'] && $r['variance_qty'] != 0 && !$r['reason'] ? 'border-red-400 bg-red-50 text-red-700' : 'border-ice-300 text-brand-800' }}">
                                                    <option value="">{{ $r['counted'] && $r['variance_qty'] != 0 ? '— wajib pilih alasan —' : 'tidak ada selisih' }}</option>
                                                    @foreach($reasons as $k => $l)
                                                        <option value="{{ $k }}">{{ $l }}</option>
                                                    @endforeach
                                                </select>
                                                <input type="text" maxlength="255" wire:model.blur="items.{{ $r['index'] }}.notes" placeholder="Catatan baris (wajib bila sebab lain)"
                                                       class="mt-1.5 w-full min-w-[11rem] px-3 py-1.5 text-xs border border-ice-300 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25">
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" wire:click="fillMatch({{ $r['index'] }})" title="Samakan dengan sistem" class="w-8 h-8 flex items-center justify-center rounded-lg bg-signal-100 text-signal-700 hover:bg-signal-200 transition-colors">{!! $oic('equal', 'w-4 h-4') !!}</button>
                                                    <button type="button" wire:click="removeItem({{ $r['index'] }})" title="Hapus baris" class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors">{!! $oic('trash', 'w-4 h-4') !!}</button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                    {!! count($rows) ? '' : $empty('Tidak ada baris yang cocok dengan saringan ini.') !!}

                    <div class="px-5 py-4 border-t border-ice-200 bg-ice-50">
                        <label class="block text-xs font-extrabold text-brand-950 mb-1.5">Tambah bahan ke lembar</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-brand-400">{!! $oic('plus', 'w-4 h-4') !!}</span>
                            <input type="text" wire:model.live.debounce.250ms="addQuery" placeholder="Ketik nama bahan yang belum ada di lembar..." class="w-full pl-10 pr-4 py-2.5 text-sm border border-ice-300 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25 focus:border-brand-500">
                        </div>
                        @if(count($addMatches))
                            <div class="mt-2 op-scroll max-h-52 overflow-y-auto rounded-xl border border-ice-200 bg-white divide-y divide-ice-100">
                                @foreach($addMatches as $m)
                                    <button type="button" wire:click="addMaterial({{ $m['id'] }})" class="w-full text-left flex items-center justify-between gap-3 px-4 py-2.5 hover:bg-ice-50 transition-colors">
                                        <span class="min-w-0">
                                            <span class="block text-sm font-bold text-brand-950 truncate">{{ $m['name'] }}</span>
                                            <span class="block text-xs text-gray-500 tabular-nums">stok sistem {{ number_format($m['stock'], 2) }} {{ $m['unit'] }}</span>
                                        </span>
                                        <span class="shrink-0 inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-brand-600 text-signal-300 text-xs font-extrabold">{!! $oic('plus', 'w-3 h-3') !!} Tambah</span>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- RAIL POSTING --}}
            <div class="xl:col-span-4 xl:sticky xl:top-24 space-y-4">
                <div class="forest-hero relative overflow-hidden rounded-3xl p-6 text-white shadow-lg shadow-brand-950/20 op-rise">
                    <div class="forest-dots absolute inset-0 opacity-40"></div>
                    <div class="relative">
                        <p class="text-xs font-bold uppercase tracking-[0.22em] text-signal-300">Langkah 3 · Review</p>
                        <div class="mt-4 grid grid-cols-2 gap-3">
                            <div class="rounded-xl bg-white/8 border border-white/15 px-3 py-2.5">
                                <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-100/55">Cocok</p>
                                <p class="text-xl font-extrabold tabular-nums text-signal-300">{{ $totals['match'] }}</p>
                            </div>
                            <div class="rounded-xl bg-white/8 border border-white/15 px-3 py-2.5">
                                <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-100/55">Belum Dihitung</p>
                                <p class="text-xl font-extrabold tabular-nums {{ $totals['items'] - $totals['counted'] > 0 ? 'text-amber-300' : 'text-white' }}">{{ $totals['items'] - $totals['counted'] }}</p>
                            </div>
                            <div class="rounded-xl bg-sky-500/20 border border-sky-300/30 px-3 py-2.5">
                                <p class="text-[10px] font-bold uppercase tracking-wider text-sky-200">Lebih</p>
                                <p class="text-xl font-extrabold tabular-nums">{{ $totals['surplus'] }}</p>
                                <p class="text-[10px] text-sky-200 tabular-nums">Rp {{ number_format($totals['surplus_value'], 0, ',', '.') }}</p>
                            </div>
                            <div class="rounded-xl bg-red-500/25 border border-red-300/30 px-3 py-2.5">
                                <p class="text-[10px] font-bold uppercase tracking-wider text-red-100">Kurang</p>
                                <p class="text-xl font-extrabold tabular-nums">{{ $totals['shrink'] }}</p>
                                <p class="text-[10px] text-red-100 tabular-nums">Rp {{ number_format($totals['shrink_value'], 0, ',', '.') }}</p>
                            </div>
                        </div>

                        <div class="mt-5 pt-5 border-t border-white/10">
                            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-emerald-100/55">Dampak Nilai Persediaan</p>
                            <p class="mt-1 text-3xl font-extrabold tabular-nums tracking-tight {{ $totals['net_value'] < 0 ? 'text-red-300' : ($totals['net_value'] > 0 ? 'text-sky-300' : 'text-signal-300') }}">
                                {{ $totals['net_value'] > 0 ? '+' : '' }}Rp {{ number_format($totals['net_value'], 0, ',', '.') }}
                            </p>
                        </div>

                        <div class="mt-4 rounded-xl px-3.5 py-2.5 text-xs font-bold {{ $totals['ready'] ? 'bg-signal-400 text-brand-950' : 'bg-amber-500/25 border border-amber-300/30 text-amber-100' }}">
                            {!! $oic($totals['ready'] ? 'check' : 'alert', 'w-3.5 h-3.5 inline mr-1') !!}
                            {{ $totals['ready'] ? 'Siap diposting — semua baris lengkap.' : ($totals['missing_reason'] > 0 ? $totals['missing_reason'] . ' selisih belum punya alasan.' : 'Lengkapi hitungan fisik semua baris.') }}
                        </div>

                        <div class="mt-5">
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-emerald-100/55 mb-1.5">Catatan lembar</label>
                            <textarea wire:model="sessionNotes" rows="3" maxlength="500" placeholder="Contoh: opname akhir bulan, dihitung dua orang."
                                      class="op-scroll w-full px-3 py-2.5 text-sm rounded-xl bg-white/10 border border-white/20 text-white placeholder-emerald-100/40 focus:outline-none focus:ring-2 focus:ring-signal-400/50 resize-y"></textarea>
                            <button type="button" wire:click="saveNotes" class="mt-2 w-full px-3 py-2 rounded-xl bg-white/10 border border-white/15 text-xs font-bold hover:bg-white/20 transition-colors">Simpan catatan</button>
                        </div>

                        <button type="button" wire:click="post" @disabled(!$totals['ready'])
                                wire:confirm="Posting opname ini? Stok sistem akan diubah sesuai hitungan fisik dan koreksi tercatat permanen di audit trail."
                                class="mt-5 w-full inline-flex items-center justify-center gap-2 px-5 py-4 rounded-2xl bg-signal-400 text-brand-950 text-base font-extrabold hover:bg-signal-500 shadow-md shadow-signal-400/25 transition-all disabled:opacity-40 disabled:cursor-not-allowed"
                                wire:loading.attr="disabled" wire:target="post">
                            <span wire:loading.remove wire:target="post" class="inline-flex items-center gap-2">{!! $oic('lock', 'w-5 h-5') !!} Posting Penyesuaian</span>
                            <span wire:loading wire:target="post">Memposting...</span>
                        </button>

                        <div class="mt-2.5 flex gap-2">
                            <button type="button" wire:click="closeWorkspace" class="flex-1 px-3 py-2.5 rounded-xl bg-white/10 border border-white/15 text-xs font-bold hover:bg-white/20 transition-colors">Tutup lembar</button>
                            <button type="button" wire:click="cancelSession" wire:confirm="Batalkan lembar opname ini? Stok tidak akan diubah." class="flex-1 px-3 py-2.5 rounded-xl bg-red-500/20 border border-red-300/30 text-xs font-bold text-red-100 hover:bg-red-500/30 transition-colors">Batalkan</button>
                        </div>

                        <p class="mt-4 text-[11px] text-emerald-100/55">Draft tersimpan otomatis setiap kali Anda mengisi angka atau alasan. Aman ditinggal dan dilanjutkan nanti.</p>
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- RIWAYAT --}}
    <section id="riwayat" class="rounded-3xl bg-white border border-ice-200 shadow-sm overflow-hidden op-rise">
        <div class="px-6 py-4 bg-ice-50 border-b border-ice-200 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="w-9 h-9 rounded-xl bg-brand-600 text-signal-300 flex items-center justify-center">{!! $oic('shield', 'w-5 h-5') !!}</span>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-gray-400">Audit</p>
                    <h2 class="text-base font-extrabold text-brand-950 leading-tight">Riwayat Lembar Opname</h2>
                </div>
            </div>
            <span class="text-xs text-gray-500">12 lembar terakhir</span>
        </div>

        @if(count($history))
            <div class="divide-y divide-ice-100">
                @foreach($history as $h)
                    <div class="px-6 py-3.5 flex flex-col lg:flex-row lg:items-center gap-3 hover:bg-ice-50/60 transition-colors border-l-4 {{ $h['status'] === 'draft' ? 'border-l-amber-400' : ($h['status'] === 'posted' ? 'border-l-signal-400' : 'border-l-transparent') }}">
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-base font-extrabold text-brand-950">{{ $h['number'] }}</span>
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase {{ $SS[$h['status']][1] }}">{!! $oic($SS[$h['status']][2], 'w-3 h-3') !!} {{ $SS[$h['status']][0] }}</span>
                                <span class="px-2 py-0.5 rounded bg-ice-100 text-brand-700 text-[10px] font-bold">{{ $scopeLabel[$h['scope']] ?? $h['scope'] }}</span>
                            </div>
                            <p class="mt-0.5 text-xs text-gray-500 tabular-nums">{{ $h['items'] }} bahan · {{ $h['variance'] }} selisih · dibuat {{ $h['created'] }}</p>
                            @if($h['posted'])<p class="text-[11px] text-gray-400">Diposting {{ $h['posted'] }} oleh {{ $h['poster'] }}</p>@endif
                        </div>
                        <div class="flex items-center gap-4 shrink-0">
                            <div class="text-right">
                                <p class="text-xs font-bold text-sky-600 tabular-nums">+Rp {{ number_format($h['surplus'], 0, ',', '.') }}</p>
                                <p class="text-xs font-bold text-red-600 tabular-nums">−Rp {{ number_format($h['shrink'], 0, ',', '.') }}</p>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <button type="button" wire:click="openDetail({{ $h['id'] }})" title="Lihat rincian" class="w-9 h-9 flex items-center justify-center rounded-xl bg-ice-100 text-brand-700 hover:bg-ice-200 transition-colors">{!! $oic('eye', 'w-4 h-4') !!}</button>
                                @if($h['status'] === 'draft')
                                    <button type="button" wire:click="openSession({{ $h['id'] }})" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-amber-500 text-white text-xs font-extrabold hover:bg-amber-600 transition-colors">{!! $oic('arrow', 'w-3.5 h-3.5') !!} Lanjutkan</button>
                                @endif
                                @if($h['status'] === 'cancelled')
                                    <button type="button" wire:click="reopenSession({{ $h['id'] }})" title="Jadikan draft lagi" class="w-9 h-9 flex items-center justify-center rounded-xl bg-ice-100 text-brand-700 hover:bg-ice-200 transition-colors">{!! $oic('undo', 'w-4 h-4') !!}</button>
                                @endif
                                @if(in_array($h['status'], ['draft', 'cancelled']))
                                    <button type="button" wire:click="deleteSession({{ $h['id'] }})" wire:confirm="Hapus permanen lembar ini?" title="Hapus" class="w-9 h-9 flex items-center justify-center rounded-xl bg-red-50 text-red-600 hover:bg-red-100 transition-colors">{!! $oic('trash', 'w-4 h-4') !!}</button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
        {!! count($history) ? '' : $empty('Belum ada lembar opname. Mulai dari panel di atas.') !!}
    </section>

    {{-- DETAIL --}}
    @if($detail)
        <div class="fixed inset-0 z-[75] flex items-end sm:items-center justify-center p-0 sm:p-4 bg-brand-950/60 backdrop-blur-sm" wire:click.self="closeDetail">
            <div class="w-full sm:max-w-3xl max-h-[90vh] flex flex-col overflow-hidden rounded-t-3xl sm:rounded-3xl bg-ice-50 shadow-2xl">
                <div class="forest-hero relative overflow-hidden px-6 py-5 flex items-start justify-between gap-4 shrink-0">
                    <div class="forest-dots absolute inset-0 opacity-40"></div>
                    <div class="relative z-10 min-w-0">
                        <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-signal-300">Rincian Opname</p>
                        <h3 class="mt-1 text-xl font-extrabold text-white tracking-tight">{{ $detail['number'] }}</h3>
                        <p class="mt-1 text-sm text-emerald-100/70">{{ $SS[$detail['status']][0] }} · dibuat {{ $detail['created'] }}{{ $detail['posted'] ? ' · diposting ' . $detail['posted'] : '' }}</p>
                    </div>
                    <button type="button" wire:click="closeDetail" class="relative z-10 shrink-0 w-9 h-9 flex items-center justify-center rounded-xl bg-white/10 text-white hover:bg-white/20 transition-colors">{!! $oic('x', 'w-5 h-5') !!}</button>
                </div>

                <div class="op-scroll flex-1 overflow-y-auto p-6">
                    @if($detail['notes'])
                        <div class="rounded-2xl bg-white border border-ice-200 p-4 mb-4">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Catatan</p>
                            <p class="mt-1 text-sm text-gray-700 whitespace-pre-line">{{ $detail['notes'] }}</p>
                        </div>
                    @endif
                    <div class="rounded-2xl bg-white border border-ice-200 overflow-hidden">
                        <div class="op-scroll overflow-x-auto">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="text-[10px] uppercase tracking-wider text-gray-400 border-b border-ice-200 bg-ice-50">
                                        <th class="px-4 py-2.5 font-bold">Bahan</th>
                                        <th class="px-3 py-2.5 font-bold text-right">Sistem</th>
                                        <th class="px-3 py-2.5 font-bold text-right">Fisik</th>
                                        <th class="px-3 py-2.5 font-bold text-right">Selisih</th>
                                        <th class="px-4 py-2.5 font-bold">Alasan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-ice-100">
                                    @foreach($detail['rows'] as $d)
                                        <tr>
                                            <td class="px-4 py-2.5">
                                                <p class="text-sm font-bold text-brand-950">{{ $d['name'] }}</p>
                                                @if($d['notes'])<p class="text-[11px] text-gray-500">{{ $d['notes'] }}</p>@endif
                                            </td>
                                            <td class="px-3 py-2.5 text-right text-sm font-bold text-brand-900 tabular-nums">{{ number_format($d['system'], 2) }}</td>
                                            <td class="px-3 py-2.5 text-right text-sm font-bold text-brand-950 tabular-nums">{{ $d['physical'] === null ? '—' : number_format($d['physical'], 2) }}</td>
                                            <td class="px-3 py-2.5 text-right">
                                                <p class="text-sm font-extrabold tabular-nums {{ $d['variance'] > 0 ? 'text-sky-600' : ($d['variance'] < 0 ? 'text-red-600' : 'text-signal-600') }}">{{ $d['variance'] > 0 ? '+' : '' }}{{ number_format($d['variance'], 2) }}</p>
                                                <p class="text-[10px] text-gray-400 tabular-nums">Rp {{ number_format($d['value'], 0, ',', '.') }}</p>
                                            </td>
                                            <td class="px-4 py-2.5 text-xs text-gray-600">{{ $d['reason'] ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="shrink-0 px-6 py-4 border-t border-ice-200 bg-white flex justify-end">
                    <button type="button" wire:click="closeDetail" class="px-5 py-2.5 rounded-xl bg-brand-600 text-white text-base font-bold hover:bg-brand-700 transition-colors">Tutup</button>
                </div>
            </div>
        </div>
    @endif
</div>