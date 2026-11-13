<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>{{ $invoice->order->invoice_number ?? 'Invoice' }}</title>
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1f2937; margin: 24px; }
    table { width: 100%; border-collapse: collapse; }
    .muted { color: #6b7280; }
    .small { font-size: 9px; }
    .brand-name { font-size: 20px; font-weight: bold; color: #111827; }
    .doc-title { font-size: 16px; font-weight: bold; letter-spacing: 2px; color: #111827; }
    .meta-box { border: 1px solid #d1d5db; background: #f9fafb; padding: 8px 10px; }
    .meta-box td { padding: 2px 0; vertical-align: top; }
    .items th { background: #111827; color: #ffffff; padding: 6px 8px; font-size: 10px; text-align: left; }
    .items td { border: 1px solid #e5e7eb; padding: 6px 8px; vertical-align: top; }
    .right { text-align: right; }
    .center { text-align: center; }
    .totals td { padding: 4px 8px; }
    .grand td { border-top: 2px solid #111827; font-weight: bold; font-size: 13px; }
    .badge { display: inline-block; padding: 3px 10px; font-size: 10px; font-weight: bold; letter-spacing: 1px; color: #fff; }
    .badge-paid { background: #16a34a; }
    .badge-unpaid { background: #dc2626; }
    .badge-partial { background: #d97706; }
    .stamp { display: inline-block; padding: 8px 26px; border: 4px double #16a34a; color: #16a34a; font-size: 24px; font-weight: bold; letter-spacing: 6px; }
    .stamp-unpaid { border-color: #dc2626; color: #dc2626; }
    .stamp-partial { border-color: #d97706; color: #d97706; }
    .section-title { font-size: 11px; font-weight: bold; color: #111827; margin: 14px 0 6px 0; }
    .footer { margin-top: 26px; border-top: 1px solid #e5e7eb; padding-top: 8px; }
</style>
</head>
<body>
@php
    $order     = $invoice->order;
    $customer  = $order?->user;
    $items     = $order?->items ?? collect();
    $total     = (float) ($invoice->total_amount ?? 0);
    $paid      = (float) ($invoice->total_paid ?? 0);
    $remaining = max(0, $total - $paid);
    $isPaid    = ($invoice->status === 'paid') || ($total > 0 && $paid >= $total);
    $isPartial = !$isPaid && $paid > 0;
    $statusLabel = $isPaid ? 'LUNAS' : ($isPartial ? 'DIBAYAR SEBAGIAN (DP)' : 'BELUM LUNAS');
    $badgeClass  = $isPaid ? 'badge-paid' : ($isPartial ? 'badge-partial' : 'badge-unpaid');
    $stampClass  = $isPaid ? '' : ($isPartial ? 'stamp-partial' : 'stamp-unpaid');
    $payments    = collect($invoice->payments ?? [])->where('status', 'verified');
    $schemeLabel = match($invoice->payment_scheme) {
        'full' => 'Langsung Lunas',
        'installment', 'dp', 'partial' => 'Cicilan / DP',
        default => ucfirst((string) ($invoice->payment_scheme ?? '-')),
    };
    $methodLabel = match($invoice->payment_method) {
        'transfer' => 'Transfer Bank',
        'cash' => 'Tunai',
        default => ucfirst((string) ($invoice->payment_method ?? '-')),
    };
@endphp

{{-- HEADER --}}
<table>
    <tr>
        <td style="width:55%; vertical-align:top;">
            <div class="brand-name">{{ config('app.brand.name', 'FC Murni Jaya') }}</div>
            <div class="muted small">
                @if(config('app.brand.address')){{ config('app.brand.address') }}<br>@endif
                @if(config('app.brand.whatsapp'))WA: {{ config('app.brand.whatsapp') }}<br>@endif
                {{ config('app.brand.hours', 'Senin–Sabtu, 08.00–17.00') }}
            </div>
        </td>
        <td style="width:45%; vertical-align:top;">
            <table class="meta-box">
                <tr><td colspan="2" class="doc-title">INVOICE</td></tr>
                <tr><td class="muted">No. Invoice</td><td class="right"><b>{{ $order->invoice_number ?? ('INV-'.$invoice->id) }}</b></td></tr>
                <tr><td class="muted">Tanggal Terbit</td><td class="right">{{ $invoice->created_at?->format('d M Y') ?? '-' }}</td></tr>
                <tr><td class="muted">Jatuh Tempo</td><td class="right">{{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') : '-' }}</td></tr>
                <tr><td class="muted">Status</td><td class="right"><span class="badge {{ $badgeClass }}">{{ $statusLabel }}</span></td></tr>
            </table>
        </td>
    </tr>
</table>

{{-- BILL TO --}}
<div class="section-title">DITAGIHKAN KEPADA</div>
<table>
    <tr>
        <td style="width:55%; vertical-align:top;">
            <b>{{ $customer->name ?? '-' }}</b><br>
            <span class="muted">{{ $customer->email ?? '-' }}</span><br>
            <span class="muted">{{ $customer->phone ?? '-' }}</span><br>
            <span class="muted">{{ $customer->address ?? '-' }}</span>
        </td>
        <td style="width:45%; vertical-align:top;" class="right">
            <span class="muted">Metode:</span> <b>{{ $methodLabel }}</b><br>
            <span class="muted">Skema:</span> <b>{{ $schemeLabel }}</b><br>
            @if($isPaid && $invoice->paid_at)
                <span class="muted">Lunas pada:</span> <b>{{ \Carbon\Carbon::parse($invoice->paid_at)->format('d M Y, H:i') }}</b>
            @endif
        </td>
    </tr>
</table>

{{-- ITEMS --}}
<div class="section-title">RINCIAN PESANAN</div>
<table class="items">
    <thead>
        <tr>
            <th style="width:4%;">#</th>
            <th style="width:30%;">Layanan / Spesifikasi</th>
            <th style="width:18%;">Bahan</th>
            <th style="width:12%;" class="center">Luas / Qty</th>
            <th style="width:12%;" class="right">Harga Satuan</th>
            <th style="width:10%;" class="right">Diskon</th>
            <th style="width:14%;" class="right">Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @forelse($items as $i => $item)
            <tr>
                <td class="center">{{ $i + 1 }}</td>
                <td>
                    <b>{{ $item->service->name ?? '-' }}</b><br>
                    <span class="muted small">{{ $item->duration_name ?? 'Normal' }} ({{ $item->duration_days ?? 0 }} hari)</span>
                </td>
                <td>{{ $item->material->name ?? '-' }}</td>
                <td class="center">{{ number_format((float)($item->area_m2 ?? 0), 2) }} m² / {{ $item->quantity ?? 1 }}</td>
                <td class="right">Rp {{ number_format((float)($item->unit_price ?? 0), 0, ',', '.') }}</td>
                <td class="right">Rp {{ number_format((float)($item->discount ?? 0), 0, ',', '.') }}</td>
                <td class="right"><b>Rp {{ number_format((float)($item->final_subtotal ?? 0), 0, ',', '.') }}</b></td>
            </tr>
        @empty
            <tr><td colspan="7" class="center muted">Tidak ada item.</td></tr>
        @endforelse
    </tbody>
</table>

{{-- TOTALS + STAMP --}}
<table style="margin-top:12px;">
    <tr>
        <td style="width:50%; vertical-align:middle;">
            <span class="stamp {{ $stampClass }}">{{ $isPaid ? 'LUNAS' : ($isPartial ? 'DP' : 'BELUM LUNAS') }}</span>
        </td>
        <td style="width:50%; vertical-align:top;">
            <table class="totals">
                <tr><td class="muted">Subtotal</td><td class="right">Rp {{ number_format((float)($invoice->subtotal ?? 0), 0, ',', '.') }}</td></tr>
                <tr><td class="muted">Ongkos Kirim</td><td class="right">Rp {{ number_format((float)($invoice->shipping_cost ?? 0), 0, ',', '.') }}</td></tr>
                <tr><td class="muted">Diskon</td><td class="right">- Rp {{ number_format((float)($invoice->discount ?? 0), 0, ',', '.') }}</td></tr>
                <tr class="grand"><td>TOTAL TAGIHAN</td><td class="right">Rp {{ number_format($total, 0, ',', '.') }}</td></tr>
                <tr><td class="muted">Sudah Dibayar</td><td class="right" style="color:#16a34a;"><b>Rp {{ number_format($paid, 0, ',', '.') }}</b></td></tr>
                <tr><td class="muted">Sisa Tagihan</td><td class="right" style="color:{{ $remaining > 0 ? '#dc2626' : '#16a34a' }};"><b>Rp {{ number_format($remaining, 0, ',', '.') }}</b></td></tr>
            </table>
        </td>
    </tr>
</table>

{{-- PAYMENT HISTORY --}}
@if($payments->count() > 0)
    <div class="section-title">RIWAYAT PEMBAYARAN TERVERIFIKASI</div>
    <table class="items">
        <thead>
            <tr><th style="width:25%;">Tanggal</th><th style="width:35%;">Metode</th><th style="width:20%;" class="right">Nominal</th><th style="width:20%;" class="center">Status</th></tr>
        </thead>
        <tbody>
            @foreach($payments as $p)
                <tr>
                    <td>{{ $p->verified_at ? \Carbon\Carbon::parse($p->verified_at)->format('d M Y, H:i') : ($p->created_at?->format('d M Y, H:i') ?? '-') }}</td>
                    <td>{{ $p->payment_method ?? '-' }}</td>
                    <td class="right">Rp {{ number_format((float)($p->amount ?? 0), 0, ',', '.') }}</td>
                    <td class="center"><span class="badge badge-paid">VERIFIED</span></td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

{{-- FOOTER --}}
<div class="footer small muted">
    <b>Terima kasih telah mempercayakan cetak Anda kepada {{ config('app.brand.name', 'FC Murni Jaya') }}.</b><br>
    Dokumen ini diterbitkan secara elektronik dan sah tanpa tanda tangan basah.
    Harga sudah termasuk pajak (PPN). Simpan invoice ini sebagai bukti transaksi.<br>
    Dicetak: {{ now()->format('d M Y, H:i') }} • {{ $order->invoice_number ?? ('INV-'.$invoice->id) }}
</div>
</body>
</html>