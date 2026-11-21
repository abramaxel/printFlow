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
        'eye'     => 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z',
        'down'    => 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4',
        'chat'    => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z',
        'link'    => 'M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1',
        'archive' => 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4',
        'arrow'   => 'M14 5l7 7m0 0l-7 7m7-7H3',
        'pin'     => 'M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z',
        'clock'   => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        'box'     => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
    ];
    $ic = fn($k, $c = 'w-5 h-5') => '<svg class="' . $c . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="' . ($P[$k] ?? $P['invoice']) . '"/></svg>';
@endphp

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <style>
        @keyframes dbRise { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: translateY(0); } }
        .db-rise { animation: dbRise .5s cubic-bezier(.2,.7,.2,1) both; }
        @keyframes dbPing { 0%,100% { box-shadow: 0 0 0 0 rgba(220,38,38,.35); } 50% { box-shadow: 0 0 0 10px rgba(220,38,38,0); } }
        .db-ping { animation: dbPing 2.4s ease-out infinite; }
        .db-help summary::-webkit-details-marker { display: none; }
        .db-help summary { list-style: none; cursor: pointer; }
        .db-help[open] .db-chev { transform: rotate(180deg); }
    </style>

    {{-- ===== SAPAAN RINGKAS ===== --}}
    <div class="forest-hero relative overflow-hidden rounded-3xl text-white px-5 py-5 sm:px-7 sm:py-6 mb-5 shadow-lg shadow-brand-950/25 db-rise">
        <div class="forest-dots absolute inset-0 opacity-40"></div>
        <div class="absolute -top-16 -right-10 w-48 h-48 rounded-full bg-signal-400/10 blur-3xl"></div>
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="min-w-0">
                <p class="text-xs font-bold uppercase tracking-[0.22em] text-signal-300">Pesanan Berjalan</p>
                <h1 class="mt-1.5 text-2xl sm:text-3xl font-extrabold tracking-tight truncate">Halo, {{ auth()->user()->name }}</h1>
            </div>
            <div class="flex items-center gap-2.5 shrink-0">
                <span class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white/10 border border-white/10 text-sm font-bold">
                    <span class="w-1.5 h-1.5 rounded-full bg-signal-400"></span>{{ $stats['active'] }} aktif
                </span>
                <a href="{{ route('customer.history') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-signal-400 text-brand-950 text-sm font-extrabold hover:bg-signal-500 transition-colors">
                    {!! $ic('archive', 'w-4 h-4') !!} Riwayat
                </a>
            </div>
        </div>
    </div>

    {{-- ===== STRIP TINDAKAN (hanya bila perlu) ===== --}}
    @if($stats['pending'] > 0 && !$urgentOnly)
        <div class="mb-5 flex flex-col sm:flex-row sm:items-center gap-3 rounded-2xl border border-red-200 bg-gradient-to-r from-red-50 to-white px-4 py-3.5 db-rise">
            <span class="db-ping shrink-0 w-9 h-9 rounded-xl bg-red-600 text-white flex items-center justify-center">{!! $ic('alert', 'w-4 h-4') !!}</span>
            <p class="flex-1 text-base text-red-800"><b class="font-extrabold">{{ $stats['pending'] }} pesanan</b> menunggu invoice atau pembayaran.</p>
            <button wire:click="toggleUrgent" class="shrink-0 inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-red-600 text-white text-sm font-extrabold hover:bg-red-700 transition-colors">
                Lihat {!! $ic('arrow', 'w-4 h-4') !!}
            </button>
        </div>
    @endif

    {{-- ===== DAFTAR PESANAN BERJALAN ===== --}}
    <div class="bg-white rounded-3xl border border-ice-200 shadow-sm overflow-hidden db-rise">
        <div class="px-5 sm:px-6 py-4 border-b border-ice-200 flex flex-col sm:flex-row sm:items-center gap-3">
            <div class="flex items-center gap-2.5 flex-1 min-w-0">
                <h2 class="text-lg font-extrabold text-brand-950">Sedang Berjalan</h2>
                <span class="px-2.5 py-0.5 rounded-full bg-ice-100 text-brand-700 text-sm font-extrabold tabular-nums">{{ $orders->total() }}</span>
                @if($urgentOnly)
                    <button wire:click="toggleUrgent" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-red-100 text-red-700 text-xs font-extrabold hover:bg-red-200 transition-colors">
                        Perlu tindakan {!! $ic('x', 'w-3.5 h-3.5') !!}
                    </button>
                @endif
            </div>
            <div class="relative sm:w-64">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-brand-400">{!! $ic('search', 'w-4 h-4') !!}</span>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari no. invoice..."
                       class="w-full pl-10 pr-9 py-2.5 text-base border border-ice-300 rounded-xl bg-ice-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25 focus:border-brand-500 transition-all">
                <span wire:loading wire:target="search" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                    <svg class="w-4 h-4 animate-spin text-brand-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.4 0 0 5.4 0 12h4z"/></svg>
                </span>
            </div>
        </div>

        <ul wire:loading.class="opacity-40 pointer-events-none" class="divide-y divide-ice-100 transition-opacity duration-200">
            @forelse($orders as $o)
                @php
                    $meta = $statusMeta[$o->status] ?? ['label' => ucfirst(str_replace('_', ' ', $o->status)), 'badge' => 'bg-ice-200 text-brand-800', 'chip' => 'bg-ice-100 text-brand-500', 'icon' => 'invoice', 'step' => 0];
                    $inv = $o->invoice;
                    $total = (float) ($inv->total_amount ?? 0);
                    $paidAmt = (float) ($inv->total_paid ?? 0);
                    $isPaid = $inv && (($inv->status === 'paid') || ($total > 0 && $paidAmt >= $total));
                    $urgent = in_array($o->status, ['waiting_invoice', 'unpaid']) && !$isPaid;
                    $payUrl = ($inv && $inv->payment_token) ? route('payment.show', $inv->payment_token) : null;
                    $pdfUrl = $inv ? route('customer.invoices.pdf', $inv->id) : null;
                    $step = (int) $meta['step'];
                    $pct = max(0, min(100, round($step / 5 * 100)));
                @endphp
                <li class="group relative px-5 sm:px-6 py-4 hover:bg-ice-50/70 transition-colors {{ $urgent ? 'border-l-4 border-l-red-400' : 'border-l-4 border-l-transparent' }}">
                    <div class="flex flex-col lg:flex-row lg:items-center gap-4">
                        <div class="flex items-start gap-3.5 flex-1 min-w-0">
                            <span class="mt-0.5 shrink-0 w-11 h-11 rounded-2xl flex items-center justify-center {{ $meta['chip'] }}">{!! $ic($meta['icon'], 'w-5 h-5') !!}</span>
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="text-base sm:text-lg font-extrabold text-brand-950 tracking-tight">{{ $o->invoice_number }}</span>
                                    <button onclick="copyText('{{ $o->invoice_number }}', this)" title="Salin nomor invoice"
                                            class="w-7 h-7 flex items-center justify-center rounded-lg text-brand-500 hover:bg-brand-600 hover:text-signal-300 transition-all opacity-100 sm:opacity-0 sm:group-hover:opacity-100">
                                        {!! $ic('copy', 'w-3.5 h-3.5') !!}
                                    </button>
                                    <span class="px-2.5 py-0.5 text-xs font-bold rounded-full {{ $meta['badge'] }}">{{ $meta['label'] }}</span>
                                    @if($isPaid)<span class="px-2.5 py-0.5 text-xs font-extrabold rounded-full bg-signal-400 text-brand-950 tracking-wide">LUNAS</span>@endif
                                </div>
                                <p class="mt-1 text-sm text-gray-500 truncate">{{ $o->items->first()?->service->name ?? '-' }} • {{ $o->created_at->format('d M Y') }} • {{ $o->items->count() }} item</p>
                                <div class="mt-2.5 flex items-center gap-3">
                                    <div class="flex-1 max-w-[16rem] h-1.5 rounded-full bg-ice-200 overflow-hidden">
                                        <div class="h-full rounded-full transition-all duration-500 {{ $urgent ? 'bg-red-500' : 'bg-brand-600' }}" style="width: {{ max(4, $pct) }}%"></div>
                                    </div>
                                    <span class="text-xs font-extrabold tabular-nums {{ $urgent ? 'text-red-600' : 'text-brand-700' }}">{{ $pct }}%</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between lg:justify-end gap-4 shrink-0">
                            <div class="text-left lg:text-right">
                                <p class="text-lg font-extrabold text-brand-950 tabular-nums tracking-tight">Rp {{ number_format($total, 0, ',', '.') }}</p>
                                <p class="text-xs {{ $isPaid ? 'text-signal-600 font-extrabold' : ($inv ? 'text-gray-500' : 'text-gray-400 italic') }}">
                                    {{ $isPaid ? 'Lunas' : ($inv ? 'Sisa Rp ' . number_format(max(0, $total - $paidAmt), 0, ',', '.') : 'Invoice belum terbit') }}
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                @if($isPaid && $pdfUrl)
                                    <a href="{{ $pdfUrl }}" target="_blank" class="inline-flex items-center gap-1.5 px-4 py-2.5 text-sm font-bold rounded-xl bg-brand-600 text-white hover:bg-brand-700 transition-colors">{!! $ic('invoice', 'w-4 h-4') !!} Invoice</a>
                                @elseif($payUrl)
                                    <a href="{{ $payUrl }}" target="_blank" class="inline-flex items-center gap-1.5 px-4 py-2.5 text-sm font-extrabold rounded-xl bg-brand-600 text-signal-300 hover:bg-brand-700 transition-colors">{!! $ic('card', 'w-4 h-4') !!} Bayar</a>
                                @elseif($contact['whatsapp'])
                                    <a href="https://wa.me/{{ $contact['whatsapp'] }}?text={{ urlencode('Halo Admin, saya ' . auth()->user()->name . '. Mohon terbitkan invoice untuk pesanan ' . $o->invoice_number . '. Terima kasih.') }}" target="_blank"
                                       class="inline-flex items-center gap-1.5 px-4 py-2.5 text-sm font-extrabold rounded-xl bg-signal-400 text-brand-950 hover:bg-signal-500 transition-colors">{!! $ic('chat', 'w-4 h-4') !!} Minta Invoice</a>
                                @endif
                                <button wire:click="openDetail({{ $o->id }})" title="Lihat detail" class="w-10 h-10 flex items-center justify-center rounded-xl bg-ice-100 text-brand-700 hover:bg-ice-200 transition-colors">{!! $ic('eye', 'w-4 h-4') !!}</button>
                            </div>
                        </div>
                    </div>
                </li>
            @empty
                <li class="px-6 py-14 text-center">
                    <span class="inline-flex w-16 h-16 rounded-full bg-signal-100 text-signal-600 items-center justify-center mx-auto">{!! $ic('check', 'w-7 h-7') !!}</span>
                    <p class="mt-4 text-xl font-extrabold text-brand-950">{{ $urgentOnly || $search ? 'Tidak ada yang cocok' : 'Semua pesanan sudah tuntas' }}</p>
                    <p class="mt-1.5 text-base text-gray-500">{{ $urgentOnly || $search ? 'Coba hapus saringan atau kata kunci.' : 'Tidak ada pesanan berjalan saat ini.' }}</p>
                    <div class="mt-6 flex flex-wrap items-center justify-center gap-2.5">
                        @if($urgentOnly || $search)
                            <button wire:click="toggleUrgent" class="px-5 py-2.5 rounded-xl bg-ice-100 text-brand-800 text-base font-bold hover:bg-ice-200 transition-colors">Reset saringan</button>
                        @endif
                        <a href="{{ route('customer.history') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-600 text-white text-base font-bold hover:bg-brand-700 transition-colors">{!! $ic('archive', 'w-4 h-4') !!} Buka Riwayat</a>
                    </div>
                </li>
            @endforelse
        </ul>

        <div class="px-5 sm:px-6 py-3.5 border-t border-ice-200 bg-ice-50/60 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <p class="text-sm text-gray-500">Pesanan selesai &amp; dibatalkan tidak ditampilkan di sini.</p>
            <a href="{{ route('customer.history') }}" class="inline-flex items-center gap-1.5 text-sm font-extrabold text-brand-700 hover:text-brand-600 transition-colors">
                {{ $stats['archived'] }} pesanan di arsip {!! $ic('arrow', 'w-4 h-4') !!}
            </a>
        </div>

        @if($orders->hasPages())
            <div class="px-5 sm:px-6 py-4 border-t border-ice-200">{{ $orders->links() }}</div>
        @endif
    </div>

    {{-- ===== ARSIP + BANTUAN ===== --}}
    <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
        <a href="{{ route('customer.history') }}" class="forest-hero group relative overflow-hidden rounded-3xl p-6 text-white shadow-sm hover:shadow-xl hover:shadow-brand-950/20 hover:-translate-y-0.5 transition-all">
            <div class="forest-dots absolute inset-0 opacity-40"></div>
            <div class="absolute -bottom-14 -right-10 w-40 h-40 rounded-full bg-signal-400/10 blur-2xl"></div>
            <div class="relative z-10">
                <span class="inline-flex w-10 h-10 rounded-xl bg-white/10 border border-signal-400/25 text-signal-300 items-center justify-center">{!! $ic('archive', 'w-5 h-5') !!}</span>
                <p class="mt-4 text-xs font-bold uppercase tracking-[0.2em] text-emerald-100/60">Arsip Pesanan</p>
                <p class="mt-1 text-3xl font-extrabold tabular-nums">{{ $stats['completed'] }} <span class="text-base font-bold text-emerald-100/70">selesai</span></p>
                <p class="mt-2 text-sm text-emerald-100/70">Total dibayar <b class="text-signal-300">Rp {{ number_format($totalPaid, 0, ',', '.') }}</b></p>
                <span class="mt-5 inline-flex items-center gap-2 text-sm font-extrabold text-signal-300 group-hover:gap-3 transition-all">Buka Riwayat Lengkap {!! $ic('arrow', 'w-4 h-4') !!}</span>
            </div>
        </a>

        <div class="bg-white rounded-3xl border border-ice-200 shadow-sm p-6">
            <h3 class="text-lg font-extrabold text-brand-950">Butuh Bantuan?</h3>
            @if($contact['whatsapp'])
                <a href="https://wa.me/{{ $contact['whatsapp'] }}?text={{ urlencode('Halo Admin ' . $contact['name'] . ', saya ' . auth()->user()->name . '. Saya butuh bantuan terkait pesanan saya.') }}" target="_blank"
                   class="mt-3 flex items-center justify-center gap-2 w-full px-4 py-3 bg-signal-400 hover:bg-signal-500 text-brand-950 text-base font-extrabold rounded-xl transition-colors">
                    {!! $ic('chat', 'w-4 h-4') !!} Chat WhatsApp Admin
                </a>
            @else
                <p class="mt-3 text-sm text-amber-700 bg-amber-50 border border-amber-200 rounded-xl p-3">Nomor admin belum diatur.</p>
            @endif
            <div class="mt-3 space-y-1.5 text-sm text-gray-500">
                @if($contact['address'])<p class="flex items-start gap-2">{!! $ic('pin', 'w-4 h-4') !!} <span>{{ $contact['address'] }}</span></p>@endif
                <p class="flex items-start gap-2">{!! $ic('clock', 'w-4 h-4') !!} <span>{{ $contact['hours'] }}</span></p>
            </div>

            <details class="db-help mt-4 rounded-xl border border-ice-200 bg-ice-50 overflow-hidden">
                <summary class="flex items-center justify-between px-4 py-3 text-sm font-extrabold text-brand-800 hover:bg-ice-100 transition-colors">
                    Cara Pembayaran
                    <svg class="db-chev w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                </summary>
                <ol class="px-4 pb-4 pt-1 space-y-1.5 text-sm text-gray-600 list-decimal list-inside">
                    <li>Admin menerbitkan <b>invoice</b>.</li>
                    <li>Klik <b>Bayar</b> → halaman magic link.</li>
                    <li>Transfer &amp; <b>upload bukti</b>.</li>
                    <li>Admin verifikasi → masuk <b>produksi</b>.</li>
                    <li>Invoice lunas tersedia sebagai <b>PDF</b>.</li>
                </ol>
            </details>
        </div>
    </div>

    {{-- ===== MODAL DETAIL ===== --}}
    @if($detailOrder)
        @php
            $dmeta = $statusMeta[$detailOrder->status] ?? ['label' => $detailOrder->status, 'badge' => 'bg-ice-200 text-brand-800', 'chip' => 'bg-ice-100 text-brand-500', 'icon' => 'invoice', 'step' => 0];
            $dInv = $detailOrder->invoice;
            $dTotal = (float) ($dInv->total_amount ?? 0);
            $dPaid = (float) ($dInv->total_paid ?? 0);
            $dIsPaid = $dInv && (($dInv->status === 'paid') || ($dTotal > 0 && $dPaid >= $dTotal));
            $dPayUrl = ($dInv && $dInv->payment_token) ? route('payment.show', $dInv->payment_token) : null;
            $dPdfUrl = $dInv ? route('customer.invoices.pdf', $dInv->id) : null;
            $dPdfDl = $dInv ? route('customer.invoices.download', $dInv->id) : null;
        @endphp
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-brand-950/60 backdrop-blur-sm" wire:click.self="closeDetail">
            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-2xl max-h-[85vh] overflow-y-auto">
                <div class="sticky top-0 bg-white px-6 py-5 border-b border-ice-200 flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <h3 class="text-xl font-extrabold text-brand-950 tracking-tight truncate">{{ $detailOrder->invoice_number }}</h3>
                        <div class="mt-2 flex flex-wrap items-center gap-2">
                            <span class="px-3 py-1 text-sm font-bold rounded-full {{ $dmeta['badge'] }}">{{ $dmeta['label'] }}</span>
                            @if($dIsPaid)<span class="px-3 py-1 text-sm font-extrabold rounded-full bg-signal-400 text-brand-950">LUNAS</span>@endif
                        </div>
                    </div>
                    <button wire:click="closeDetail" class="shrink-0 w-9 h-9 flex items-center justify-center rounded-xl text-gray-400 hover:bg-ice-100 hover:text-gray-600 transition-colors">{!! $ic('x', 'w-5 h-5') !!}</button>
                </div>
                <div class="p-6 space-y-6">
                    <div class="overflow-x-auto rounded-2xl border border-ice-200">
                        <table class="w-full text-base">
                            <thead class="bg-ice-50 text-sm text-gray-500"><tr><th class="px-4 py-2.5 text-left">Layanan</th><th class="px-4 py-2.5 text-left">Bahan</th><th class="px-4 py-2.5 text-center">Luas / Qty</th><th class="px-4 py-2.5 text-right">Subtotal</th></tr></thead>
                            <tbody class="divide-y divide-ice-100">
                                @foreach($detailOrder->items as $item)
                                    <tr><td class="px-4 py-3">{{ $item->service->name ?? '-' }}</td><td class="px-4 py-3 text-gray-600">{{ $item->material->name ?? '-' }}</td><td class="px-4 py-3 text-center text-gray-600">{{ number_format($item->area_m2 ?? 0, 2) }} m² / {{ $item->quantity ?? 1 }}</td><td class="px-4 py-3 text-right font-semibold">Rp {{ number_format($item->final_subtotal ?? 0, 0, ',', '.') }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="rounded-2xl bg-ice-50 border border-ice-200 p-5 space-y-2 text-base">
                        <div class="flex justify-between"><span class="text-gray-500">Total Tagihan</span><span class="font-bold">Rp {{ number_format($dTotal, 0, ',', '.') }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Sudah Dibayar</span><span class="font-bold text-signal-600">Rp {{ number_format($dPaid, 0, ',', '.') }}</span></div>
                        <div class="flex justify-between border-t border-ice-200 pt-2"><span class="text-gray-500">Sisa Tagihan</span><span class="font-extrabold {{ ($dTotal - $dPaid) > 0 ? 'text-red-600' : 'text-signal-600' }}">Rp {{ number_format(max(0, $dTotal - $dPaid), 0, ',', '.') }}</span></div>
                    </div>
                    <div class="flex flex-wrap gap-2.5">
                        @if($dPdfUrl)
                            <a href="{{ $dPdfUrl }}" target="_blank" class="flex-1 min-w-[12rem] inline-flex items-center justify-center gap-2 px-4 py-3.5 bg-brand-600 text-white text-base font-bold rounded-xl hover:bg-brand-700 transition-colors">{!! $ic('invoice', 'w-4 h-4') !!} Lihat Invoice</a>
                            <a href="{{ $dPdfDl }}" class="inline-flex items-center justify-center gap-2 px-4 py-3.5 bg-signal-400 text-brand-950 text-base font-bold rounded-xl hover:bg-signal-500 transition-colors">{!! $ic('down', 'w-4 h-4') !!} Unduh</a>
                        @endif
                        @if($dPayUrl && !$dIsPaid)
                            <a href="{{ $dPayUrl }}" target="_blank" class="flex-1 min-w-[12rem] inline-flex items-center justify-center gap-2 px-4 py-3.5 bg-brand-600 text-signal-300 text-base font-extrabold rounded-xl hover:bg-brand-700 transition-colors">{!! $ic('card', 'w-4 h-4') !!} Bayar Sekarang</a>
                            <button onclick="copyText('{{ $dPayUrl }}', this)" class="inline-flex items-center justify-center px-4 py-3.5 bg-ice-100 text-brand-800 rounded-xl hover:bg-ice-200 transition-colors">{!! $ic('link', 'w-4 h-4') !!}</button>
                        @elseif(!$dInv && $contact['whatsapp'])
                            <a href="https://wa.me/{{ $contact['whatsapp'] }}?text={{ urlencode('Halo Admin, mohon terbitkan invoice untuk pesanan ' . $detailOrder->invoice_number . '.') }}" target="_blank" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-3.5 bg-signal-400 text-brand-950 text-base font-extrabold rounded-xl hover:bg-signal-500 transition-colors">{!! $ic('chat', 'w-4 h-4') !!} Minta Invoice ke Admin</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>