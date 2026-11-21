@php
    $I = [
        'invoice' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'alert'   => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
        'card'    => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',
        'printer' => 'M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z',
        'shield'  => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
        'truck'   => 'M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0 2 2 0 00-4 0zm10 0a2 2 0 104 0 2 2 0 00-4 0z',
        'check'   => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        'x'       => 'M6 18L18 6M6 6l12 12',
        'box'     => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
        'search'  => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z',
        'plus'    => 'M12 4v16m8-8H4',
        'copy'    => 'M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z',
        'arrow'   => 'M14 5l7 7m0 0l-7 7m7-7H3',
        'left'    => 'M15 19l-7-7 7-7',
        'right'   => 'M9 5l7 7-7 7',
        'undo'    => 'M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6',
        'up'      => 'M5 15l7-7 7 7',
        'down'    => 'M19 9l-7 7-7-7',
        'sort'    => 'M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4',
        'money'   => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    ];
    $iic = fn($k, $c = 'w-5 h-5') => '<svg class="' . $c . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="' . ($I[$k] ?? $I['box']) . '"/></svg>';

    $stepMap = ['waiting_invoice' => 0, 'unpaid' => 0, 'paid' => 1, 'processing' => 2, 'qc' => 3, 'shipping' => 4, 'completed' => 5, 'cancelled' => -1, 'rejected' => -1];
    $stages = ['Invoice', 'Bayar', 'Produksi', 'QC', 'Kirim', 'Selesai'];

    $pills = $groups;
    if ($statusFilter !== '' && !isset($pills[$statusFilter])) {
        $pills[$statusFilter] = ['label' => $this->formatStatus($statusFilter), 'statuses' => [$statusFilter]];
    }
    if ($restricted) { unset($pills['completed'], $pills['cancelled']); }

    $cur = $orders->currentPage();
    $last = $orders->lastPage();
    $from = max(1, $cur - 2);
    $to = min($last, $cur + 2);
    $hasFilter = trim((string) $search) !== '' || $statusFilter !== '';
@endphp

<div class="w-full space-y-6">
    <style>
        @keyframes olRise { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
        .ol-rise { animation: olRise .5s cubic-bezier(.2,.7,.2,1) both; }
        @keyframes olLive { 0%,100% { box-shadow: 0 0 0 0 rgba(200,236,63,.5); } 50% { box-shadow: 0 0 0 9px rgba(200,236,63,0); } }
        .ol-live { animation: olLive 2.4s ease-out infinite; }
        .ol-scroll::-webkit-scrollbar { height: 8px; width: 8px; }
        .ol-scroll::-webkit-scrollbar-track { background: #e2edf0; border-radius: 8px; }
        .ol-scroll::-webkit-scrollbar-thumb { background: #1d6247; border-radius: 8px; }
        .ol-select { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' stroke='%230a3f33' stroke-width='2.5' viewBox='0 0 24 24'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right .85rem center; background-size: 1rem; }
        @media (prefers-reduced-motion: reduce) { .ol-rise, .ol-live { animation: none !important; } }
    </style>

    {{-- ===== HERO ===== --}}
    <div class="forest-hero relative overflow-hidden rounded-3xl text-white px-6 py-6 sm:px-8 sm:py-7 shadow-lg shadow-brand-950/25 ol-rise">
        <div class="forest-dots absolute inset-0 opacity-45"></div>
        <div class="absolute -top-24 -left-16 w-72 h-72 rounded-full bg-emerald-500/15 blur-3xl"></div>
        <div class="absolute -bottom-24 right-0 w-64 h-64 rounded-full bg-signal-400/10 blur-3xl"></div>

        <div class="relative z-10 flex flex-col xl:flex-row xl:items-center justify-between gap-6">
            <div class="min-w-0">
                <p class="text-xs font-bold uppercase tracking-[0.24em] text-signal-300">Manajemen Transaksi</p>
                <h1 class="mt-2 text-3xl font-extrabold tracking-tight">Daftar Pesanan</h1>
                <p class="mt-2 text-base text-emerald-100/75 max-w-xl">Cari berdasarkan nomor invoice, nama, email, atau nomor telepon pelanggan.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3 shrink-0">
                <div class="rounded-2xl bg-white/5 border border-white/10 px-4 py-3">
                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-emerald-100/55">Uang Diterima</p>
                    <p class="mt-0.5 text-xl font-extrabold text-signal-300 tabular-nums">Rp {{ number_format($revenue, 0, ',', '.') }}</p>
                </div>
                <a href="{{ route('admin.orders.create') }}"
                   class="inline-flex items-center gap-2 px-5 py-3.5 rounded-2xl bg-signal-400 text-brand-950 text-base font-extrabold hover:bg-signal-500 hover:-translate-y-0.5 shadow-md shadow-signal-400/25 transition-all">
                    {!! $iic('plus', 'w-5 h-5') !!} Buat Pesanan Baru
                </a>
            </div>
        </div>

        {{-- Stat chips --}}
        <div class="relative z-10 mt-7 grid grid-cols-2 sm:grid-cols-4 gap-3">
            @foreach([
                ['', 'Total Pesanan', 'box'],
                ['need_action', 'Perlu Tindakan', 'alert'],
                ['production', 'Dalam Produksi', 'printer'],
                ['shipping', 'Siap / Dikirim', 'truck'],
            ] as $chip)
                <button type="button" wire:click="setFilter('{{ $chip[0] }}')"
                        class="text-left rounded-2xl px-4 py-3 border transition-all hover:-translate-y-0.5 {{ $statusFilter === $chip[0] ? 'bg-signal-400 border-signal-400 text-brand-950' : 'bg-white/5 border-white/10 text-white hover:bg-white/10' }}">
                    <span class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-[0.18em] {{ $statusFilter === $chip[0] ? 'text-brand-900/70' : 'text-emerald-100/55' }}">
                        {!! $iic($chip[2], 'w-3.5 h-3.5') !!} {{ $chip[1] }}
                    </span>
                    <span class="mt-1 block text-2xl font-extrabold tabular-nums">{{ $counts[$chip[0]] ?? 0 }}</span>
                </button>
            @endforeach
        </div>
    </div>

    {{-- ===== CONTROL BAR ===== --}}
    <div class="sticky top-24 z-20 rounded-[1.75rem] bg-white/85 backdrop-blur-xl border border-ice-200 shadow-xl shadow-brand-950/5 p-4 ol-rise">
        <div class="flex flex-col lg:flex-row gap-3 lg:items-center">
            <div class="relative flex-1 min-w-0">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-brand-400">{!! $iic('search', 'w-4 h-4') !!}</span>
                <input type="text" wire:model.live.debounce.300ms="search"
                       placeholder="Cari invoice, nama, email, atau nomor telepon..."
                       class="w-full pl-11 pr-10 py-3 text-base border border-ice-300 rounded-2xl bg-ice-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25 focus:border-brand-500 transition-all">
                <span wire:loading wire:target="search" class="absolute inset-y-0 right-0 pr-4 flex items-center">
                    <svg class="w-4 h-4 animate-spin text-brand-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.4 0 0 5.4 0 12h4z"/></svg>
                </span>
            </div>
            <div class="flex items-center gap-2.5 shrink-0">
                <select wire:model.live="sortBy" class="ol-select pl-4 pr-10 py-3 text-sm font-bold text-brand-800 border border-ice-300 rounded-2xl bg-ice-50 focus:outline-none focus:ring-2 focus:ring-brand-500/25 cursor-pointer">
                    <option value="created_at">Urut: Tanggal</option>
                    <option value="invoice_number">Urut: No. Invoice</option>
                    <option value="status">Urut: Status</option>
                </select>
                <button type="button" wire:click="$set('sortDirection','{{ $sortDirection === 'asc' ? 'desc' : 'asc' }}')"
                        title="{{ $sortDirection === 'asc' ? 'Naik (A→Z / lama→baru)' : 'Turun (Z→A / baru→lama)' }}"
                        class="w-11 h-11 flex items-center justify-center rounded-2xl bg-ice-100 text-brand-700 hover:bg-ice-200 transition-colors">
                    {!! $iic($sortDirection === 'asc' ? 'up' : 'down', 'w-4 h-4') !!}
                </button>
                @if($hasFilter)
                    <button type="button" wire:click="clearFilters"
                            class="inline-flex items-center gap-2 px-4 py-3 rounded-2xl bg-red-50 text-red-600 text-sm font-bold hover:bg-red-100 transition-colors">
                        {!! $iic('undo', 'w-4 h-4') !!} Reset
                    </button>
                @endif
            </div>
        </div>

        <div class="mt-3 flex flex-wrap gap-2">
            @foreach($pills as $key => $g)
                @php $n = $counts[$key] ?? null; @endphp
                <button type="button" wire:click="setFilter('{{ $key }}')"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-extrabold transition-all {{ $statusFilter === (string) $key ? 'bg-brand-600 text-signal-300 shadow-sm shadow-brand-900/25' : 'bg-ice-100 text-brand-800 hover:bg-ice-200' }}">
                    {{ $g['label'] }}
                    @if(!is_null($n))
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-black tabular-nums {{ $statusFilter === (string) $key ? 'bg-signal-400 text-brand-950' : 'bg-white text-brand-700' }}">{{ $n }}</span>
                    @endif
                </button>
            @endforeach
        </div>
    </div>

    {{-- ===== LIST ===== --}}
    <div class="rounded-[2rem] bg-white border border-ice-200 shadow-sm overflow-hidden ol-rise">
        <div class="px-6 py-4 border-b border-ice-200 bg-ice-50 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <p class="text-sm text-gray-500">
                Menampilkan <b class="text-brand-950 tabular-nums">{{ $orders->firstItem() ?? 0 }}–{{ $orders->lastItem() ?? 0 }}</b>
                dari <b class="text-brand-950 tabular-nums">{{ $orders->total() }}</b> pesanan
            </p>
            @if($restricted)
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-ice-200 text-brand-800 text-xs font-bold">
                    {!! $iic('shield', 'w-3.5 h-3.5') !!} Pesanan selesai &amp; batal disembunyikan untuk role Anda
                </span>
            @endif
        </div>

        <div wire:loading.class="opacity-40 pointer-events-none" class="transition-opacity duration-200">
            @if($orders->count() > 0)
                {{-- Tabel desktop --}}
                <div class="hidden lg:block ol-scroll overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-[11px] uppercase tracking-wider text-gray-400 border-b border-ice-200">
                                @foreach([['invoice_number', 'No. Invoice'], [null, 'Pelanggan'], ['created_at', 'Tanggal'], [null, 'Tagihan'], ['status', 'Status'], [null, 'Aksi']] as $col)
                                    <th class="px-6 py-3 font-bold {{ $loop->last ? 'text-right' : '' }}">
                                        @if($col[0])
                                            <button type="button" wire:click="setSortBy('{{ $col[0] }}')" class="inline-flex items-center gap-1.5 hover:text-brand-700 transition-colors">
                                                {{ $col[1] }}
                                                @if($sortBy === $col[0])
                                                    {!! $iic($sortDirection === 'asc' ? 'up' : 'down', 'w-3 h-3') !!}
                                                @else
                                                    {!! $iic('sort', 'w-3 h-3 opacity-40') !!}
                                                @endif
                                            </button>
                                        @else
                                            {{ $col[1] }}
                                        @endif
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-ice-100">
                            @foreach($orders as $order)
                                @php
                                    $st = (string) $order->status;
                                    $step = $stepMap[$st] ?? 0;
                                    $inv = $order->invoice;
                                    $total = (float) ($inv->total_amount ?? 0);
                                    $paid = (float) ($inv->total_paid ?? 0);
                                    $lunas = $inv && (($inv->status === 'paid') || ($total > 0 && $paid >= $total));
                                    $urgent = in_array($st, ['waiting_invoice', 'unpaid'], true);
                                @endphp
                                <tr class="hover:bg-ice-50/70 transition-colors {{ $urgent ? 'border-l-4 border-l-red-400' : 'border-l-4 border-l-transparent' }}">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <span class="text-base font-extrabold text-brand-950 tracking-tight">{{ $order->invoice_number }}</span>
                                            <button type="button" onclick="copyText('{{ $order->invoice_number }}', this)" title="Salin nomor invoice"
                                                    class="w-7 h-7 flex items-center justify-center rounded-lg text-brand-500 hover:bg-brand-600 hover:text-signal-300 transition-colors">
                                                {!! $iic('copy', 'w-3.5 h-3.5') !!}
                                            </button>
                                        </div>
                                        <p class="mt-0.5 text-xs text-gray-500 truncate max-w-[16rem]">{{ $order->items->first()?->service->name ?? '-' }} • {{ $order->items->count() }} item</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <x-avatar :user="$order->user" size="sm" shape="rounded-xl" />
                                            <div class="min-w-0">
                                                <p class="text-sm font-bold text-brand-950 truncate max-w-[12rem]">{{ $order->user->name ?? 'User dihapus' }}</p>
                                                <p class="text-xs text-gray-500 tabular-nums truncate max-w-[12rem]">{{ $order->user->phone ?? $order->user->email ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm font-semibold text-brand-900 tabular-nums">{{ $order->created_at->format('d M Y') }}</p>
                                        <p class="text-xs text-gray-400 tabular-nums">{{ $order->created_at->format('H:i') }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($inv)
                                            <p class="text-base font-extrabold text-brand-950 tabular-nums">Rp {{ number_format($total, 0, ',', '.') }}</p>
                                            <p class="text-xs {{ $lunas ? 'text-signal-600 font-extrabold' : 'text-gray-500' }}">
                                                {{ $lunas ? 'Lunas' : 'Sisa Rp ' . number_format(max(0, $total - $paid), 0, ',', '.') }}
                                            </p>
                                        @else
                                            <span class="text-sm text-gray-400 italic">Belum ada tagihan</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold rounded-full {{ $this->getStatusColor($st) }}">
                                            {!! $iic($this->getStatusIcon($st), 'w-3.5 h-3.5') !!} {{ $this->formatStatus($st) }}
                                        </span>
                                        @if($step >= 0)
                                            <div class="mt-2 flex gap-1 max-w-[9rem]">
                                                @foreach($stages as $i => $lbl)
                                                    <span class="flex-1 h-1.5 rounded-full {{ $i < $step || ($i === $step && $st === 'completed') ? 'bg-brand-600' : ($i === $step ? 'bg-signal-400' : 'bg-ice-200') }}" title="{{ $lbl }}"></span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('admin.orders.show', $order->id) }}"
                                           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-brand-600 text-white text-sm font-bold hover:bg-brand-700 transition-colors">
                                            Detail {!! $iic('arrow', 'w-3.5 h-3.5') !!}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Kartu mobile --}}
                <div class="lg:hidden divide-y divide-ice-100">
                    @foreach($orders as $order)
                        @php
                            $st = (string) $order->status;
                            $step = $stepMap[$st] ?? 0;
                            $inv = $order->invoice;
                            $total = (float) ($inv->total_amount ?? 0);
                            $paid = (float) ($inv->total_paid ?? 0);
                            $lunas = $inv && (($inv->status === 'paid') || ($total > 0 && $paid >= $total));
                            $urgent = in_array($st, ['waiting_invoice', 'unpaid'], true);
                        @endphp
                        <div class="p-5 {{ $urgent ? 'border-l-4 border-l-red-400' : 'border-l-4 border-l-transparent' }}">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-base font-extrabold text-brand-950 tracking-tight">{{ $order->invoice_number }}</p>
                                    <p class="mt-0.5 text-xs text-gray-500">{{ $order->created_at->format('d M Y, H:i') }} • {{ $order->items->count() }} item</p>
                                </div>
                                <span class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold rounded-full {{ $this->getStatusColor($st) }}">
                                    {!! $iic($this->getStatusIcon($st), 'w-3.5 h-3.5') !!} {{ $this->formatStatus($st) }}
                                </span>
                            </div>

                            <div class="mt-3 flex items-center gap-3">
                                <x-avatar :user="$order->user" size="sm" shape="rounded-xl" />
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-brand-950 truncate">{{ $order->user->name ?? 'User dihapus' }}</p>
                                    <p class="text-xs text-gray-500 tabular-nums truncate">{{ $order->user->phone ?? '-' }}</p>
                                </div>
                            </div>

                            @if($step >= 0)
                                <div class="mt-3 flex gap-1">
                                    @foreach($stages as $i => $lbl)
                                        <span class="flex-1 h-1.5 rounded-full {{ $i < $step || ($i === $step && $st === 'completed') ? 'bg-brand-600' : ($i === $step ? 'bg-signal-400' : 'bg-ice-200') }}"></span>
                                    @endforeach
                                </div>
                            @endif

                            <div class="mt-4 flex items-end justify-between gap-3">
                                <div>
                                    @if($inv)
                                        <p class="text-lg font-extrabold text-brand-950 tabular-nums">Rp {{ number_format($total, 0, ',', '.') }}</p>
                                        <p class="text-xs {{ $lunas ? 'text-signal-600 font-extrabold' : 'text-gray-500' }}">{{ $lunas ? 'Lunas' : 'Sisa Rp ' . number_format(max(0, $total - $paid), 0, ',', '.') }}</p>
                                    @else
                                        <span class="text-sm text-gray-400 italic">Belum ada tagihan</span>
                                    @endif
                                </div>
                                <a href="{{ route('admin.orders.show', $order->id) }}"
                                   class="shrink-0 inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-brand-600 text-white text-sm font-bold hover:bg-brand-700 transition-colors">
                                    Detail {!! $iic('arrow', 'w-3.5 h-3.5') !!}
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-14 text-center">
                    <span class="inline-flex w-20 h-20 rounded-full bg-ice-100 text-brand-500 items-center justify-center mx-auto">{!! $iic('box', 'w-9 h-9') !!}</span>
                    <p class="mt-5 text-xl font-extrabold text-brand-950">Tidak ada pesanan yang cocok</p>
                    <p class="mt-2 text-base text-gray-500">Coba ubah kata kunci atau saringan status.</p>
                    <div class="mt-6 flex flex-wrap items-center justify-center gap-2.5">
                        @if($hasFilter)
                            <button type="button" wire:click="clearFilters" class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-ice-100 text-brand-800 text-base font-bold hover:bg-ice-200 transition-colors">
                                {!! $iic('undo', 'w-4 h-4') !!} Reset Saringan
                            </button>
                        @endif
                        <a href="{{ route('admin.orders.create') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-brand-600 text-white text-base font-bold hover:bg-brand-700 transition-colors">
                            {!! $iic('plus', 'w-4 h-4') !!} Buat Pesanan Baru
                        </a>
                    </div>
                </div>
            @endif
        </div>

        {{-- Pagination --}}
        @if($orders->hasPages())
            <div class="px-6 py-4 border-t border-ice-200 bg-ice-50 flex flex-col sm:flex-row items-center justify-between gap-3">
                <p class="text-sm text-gray-500">Halaman <b class="text-brand-950 tabular-nums">{{ $cur }}</b> dari <b class="text-brand-950 tabular-nums">{{ $last }}</b></p>
                <div class="flex items-center gap-1.5">
                    <button type="button" wire:click="previousPage" @disabled($orders->onFirstPage())
                            class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-ice-300 text-brand-700 hover:bg-ice-100 transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
                        {!! $iic('left', 'w-4 h-4') !!}
                    </button>
                    @if($from > 1)
                        <button type="button" wire:click="gotoPage(1)" class="w-10 h-10 rounded-xl bg-white border border-ice-300 text-sm font-bold text-brand-700 hover:bg-ice-100 transition-colors">1</button>
                        @if($from > 2)<span class="px-1 text-gray-400">…</span>@endif
                    @endif
                    @for($p = $from; $p <= $to; $p++)
                        <button type="button" wire:click="gotoPage({{ $p }})"
                                class="w-10 h-10 rounded-xl text-sm font-extrabold tabular-nums transition-colors {{ $p === $cur ? 'bg-brand-600 text-signal-300 shadow-sm' : 'bg-white border border-ice-300 text-brand-700 hover:bg-ice-100' }}">{{ $p }}</button>
                    @endfor
                    @if($to < $last)
                        @if($to < $last - 1)<span class="px-1 text-gray-400">…</span>@endif
                        <button type="button" wire:click="gotoPage({{ $last }})" class="w-10 h-10 rounded-xl bg-white border border-ice-300 text-sm font-bold text-brand-700 hover:bg-ice-100 transition-colors">{{ $last }}</button>
                    @endif
                    <button type="button" wire:click="nextPage" @disabled(!$orders->hasMorePages())
                            class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-ice-300 text-brand-700 hover:bg-ice-100 transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
                        {!! $iic('right', 'w-4 h-4') !!}
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>