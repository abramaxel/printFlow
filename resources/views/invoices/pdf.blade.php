<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->order->invoice_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10.5px; color: #1f2937; line-height: 1.45; margin: 0; }
        table { width: 100%; border-collapse: collapse; }
        .muted { color: #6b7280; } .small { font-size: 9px; } .bold { font-weight: bold; }
        .right { text-align: right; } .center { text-align: center; }
        .brand { font-size: 20px; font-weight: bold; color: #2563eb; }
        .doc-title { font-size: 16px; font-weight: bold; letter-spacing: 1px; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 3px; font-size: 9px; font-weight: bold; }
        .badge-unpaid { background: #fef3c7; color: #b45309; }
        .badge-paid { background: #d1fae5; color: #047857; }
        .badge-partial { background: #dbeafe; color: #1d4ed8; }
        .section-title { font-size: 8.5px; font-weight: bold; color: #6b7280; text-transform: uppercase; letter-spacing: .5px; margin: 0 0 3px; }
        .box-table td { border: 1px solid #e5e7eb; padding: 6px 8px; vertical-align: top; }
        .stat-label { font-size: 8px; color: #6b7280; text-transform: uppercase; letter-spacing: .5px; }
        .stat-value { font-size: 11px; font-weight: bold; color: #111827; margin-top: 2px; }
        .items-table th { background: #f3f4f6; border-bottom: 2px solid #d1d5db; font-size: 8.5px; text-transform: uppercase; color: #374151; padding: 6px 8px; text-align: left; }
        .items-table td { border-bottom: 1px solid #e5e7eb; padding: 7px 8px; vertical-align: top; }
        .bullet { margin: 1px 0 0; font-size: 9px; color: #6b7280; }
        .paybox { border: 1px solid #dbeafe; background: #eff6ff; padding: 8px 10px; }
        .totals-table td { padding: 2.5px 0; font-size: 10px; }
        .grand td { border-top: 2px solid #1f2937; font-size: 12px; font-weight: bold; color: #1d4ed8; padding-top: 5px; }
        .note { font-size: 8.5px; color: #6b7280; background: #f9fafb; border: 1px solid #e5e7eb; padding: 6px 8px; }
    </style>
</head>
<body>
@php
    $order = $invoice->order;
    $bd = \App\Models\Invoice::costBreakdown($order);
    $shipping = (float) $invoice->shipping_cost;
    $grand = (float) $invoice->total_amount;
    $paid = (float) ($invoice->total_paid ?? 0);
    $totalQty = $order->items->sum('quantity');
    $totalArea = $order->items->sum('area_m2');
    $shipLabel = ['pickup' => 'Pickup di Toko (Tanpa Ongkir)', 'regular' => 'Kurir Reguler', 'instant' => 'Kurir Instant'][$order->shipping_method ?? 'pickup'] ?? 'Pickup di Toko';
    $bankName = \App\Models\ShopSetting::where('key', 'bank_name')->first()?->value ?? 'BCA';
    $bankAccount = \App\Models\ShopSetting::where('key', 'bank_account')->first()?->value ?? '-';
    $bankHolder = \App\Models\ShopSetting::where('key', 'bank_holder')->first()?->value ?? '-';
    $statusBadge = $invoice->status === 'paid' ? 'badge-paid' : ($paid > 0 ? 'badge-partial' : 'badge-unpaid');
    $statusText = $invoice->status === 'paid' ? 'LUNAS' : ($paid > 0 ? 'DIBAYAR SEBAGIAN' : 'BELUM DIBAYAR');
@endphp

{{-- ===== HEADER ===== --}}
<table style="margin-bottom: 14px;">
    <tr>
        <td style="width: 60%; vertical-align: top;">
            <div class="brand">PrintOrder</div>
            <div class="small muted">Jl. Percetakan Digital No. 123, Jakarta &bull; info@printorder.test</div>
        </td>
        <td style="width: 40%; vertical-align: top;" class="right">
            <div class="doc-title">INVOICE</div>
            <div class="small muted">NO: <span class="bold" style="color:#111827;">{{ $order->invoice_number }}</span></div>
            <div style="margin-top: 3px;"><span class="badge {{ $statusBadge }}">{{ $statusText }}</span></div>
        </td>
    </tr>
</table>

{{-- ===== DITAGIHKAN + STATUS ===== --}}
<table style="margin-bottom: 12px;">
    <tr>
        <td style="width: 50%; vertical-align: top; padding-right: 12px;">
            <p class="section-title">Ditagihkan Kepada</p>
            <div class="bold">{{ $order->user->name }}</div>
            <div class="small muted">Email: {{ $order->user->email }}</div>
            <div class="small muted">No. HP: {{ $order->user->phone ?? '-' }}</div>
            @if(($order->shipping_method ?? 'pickup') !== 'pickup' && $order->shipping_address)
                <div class="small muted" style="margin-top: 4px;">Alamat: {{ $order->shipping_address }}</div>
            @endif
        </td>
        <td style="width: 50%; vertical-align: top;">
            <p class="section-title">Status & Pengiriman</p>
            <div class="small">Metode: <span class="bold">{{ $shipLabel }}</span></div>
            <div class="small">Estimasi Selesai: <span class="bold">{{ $order->deadline_date ? $order->deadline_date->format('d M Y') : '-' }}</span></div>
            <div class="small">Pembayaran: <span class="bold">{{ $invoice->payment_method === 'cash' ? 'Tunai di Toko' : 'Transfer Bank' }}{{ $invoice->payment_scheme === 'installment' ? ' (Cicilan)' : '' }}</span></div>
        </td>
    </tr>
</table>

{{-- ===== KOTAK STATISTIK ===== --}}
<table class="box-table" style="margin-bottom: 14px;">
    <tr>
        <td style="width: 25%;"><div class="stat-label">Tanggal Invoice</div><div class="stat-value">{{ $invoice->created_at->format('d M Y') }}</div></td>
        <td style="width: 25%;"><div class="stat-label">Jatuh Tempo</div><div class="stat-value">{{ $invoice->due_date ? $invoice->due_date->format('d M Y') : '-' }}</div></td>
        <td style="width: 25%;"><div class="stat-label">Total Kuantitas</div><div class="stat-value">{{ number_format($totalQty, 0, ',', '.') }} Pcs</div></td>
        <td style="width: 25%;"><div class="stat-label">Total Luas Area</div><div class="stat-value">{{ number_format($totalArea, 2) }} m&sup2;</div></td>
    </tr>
</table>

{{-- ===== TABEL ITEM ===== --}}
<table class="items-table">
    <thead>
        <tr>
            <th style="width: 40%;">Deskripsi Pekerjaan</th>
            <th style="width: 16%;" class="center">Kuantitas / Luas</th>
            <th style="width: 16%;" class="right">Harga Satuan</th>
            <th style="width: 12%;" class="right">Biaya SLA</th>
            <th style="width: 16%;" class="right">Jumlah</th>
        </tr>
    </thead>
    <tbody>
        @foreach($bd['lines'] as $line)
            <tr>
                <td>
                    <div class="bold">{{ $line['service'] }}</div>
                    <p class="bullet">&bull; Bahan: {{ $line['material'] }}</p>
                    <p class="bullet">&bull; Ukuran: {{ $line['size'] }}</p>
                    <p class="bullet">&bull; Layanan SLA: {{ $line['duration'] }} (&times;{{ rtrim(rtrim(number_format($line['multiplier'], 2, '.', ''), '0'), '.') }})</p>
                    @if($line['notes'])<p class="bullet">&bull; Catatan: {{ $line['notes'] }}</p>@endif
                </td>
                <td class="center">{{ number_format($line['qty'], 0, ',', '.') }} Pcs<br><span class="small muted">({{ number_format($line['area'], 2) }} m&sup2;)</span></td>
                <td class="right">Rp {{ number_format($line['unit_combined'], 0, ',', '.') }}/m&sup2;<br><span class="small muted">Dasar: Rp {{ number_format($line['base'], 0, ',', '.') }}</span></td>
                <td class="right">{{ $line['fee'] > 0 ? 'Rp ' . number_format($line['fee'], 0, ',', '.') : '-' }}</td>
                <td class="right bold">Rp {{ number_format($line['final'], 0, ',', '.') }}@if($line['discount'] > 0)<br><span class="small" style="color:#dc2626;">diskon -{{ number_format($line['discount'], 0, ',', '.') }}</span>@endif</td>
            </tr>
        @endforeach
    </tbody>
</table>

{{-- ===== PETUNJUK BAYAR + TOTAL ===== --}}
<table style="margin-top: 12px;">
    <tr>
        <td style="width: 52%; vertical-align: top; padding-right: 12px;">
            @if($invoice->payment_method === 'transfer')
                <div class="paybox">
                    <p class="section-title" style="color: #1d4ed8;">Petunjuk Pembayaran Transfer Bank</p>
                    <div class="bold">BANK {{ strtoupper($bankName) }} &mdash; {{ $bankAccount }}</div>
                    <div class="small muted">a.n. {{ $bankHolder }}</div>
                    <p class="small muted" style="margin: 5px 0 0;">Transfer sesuai nominal Total Tagihan. Unggah bukti transfer melalui tautan yang dikirimkan ke email/WhatsApp Anda.</p>
                </div>
            @else
                <div class="paybox" style="border-color: #d1fae5; background: #ecfdf5;">
                    <p class="section-title" style="color: #047857;">Pembayaran Tunai</p>
                    <p class="small muted" style="margin: 0;">Pembayaran dilakukan secara tunai di toko. Kuitansi resmi diberikan setelah pembayaran diterima.</p>
                </div>
            @endif

            @if($invoice->payment_scheme === 'installment' && $invoice->payment_schedule)
                <table class="box-table" style="margin-top: 8px;">
                    <tr><td colspan="3" style="background: #f9fafb;"><span class="section-title">Jadwal Pembayaran</span></td></tr>
                    @foreach($invoice->payment_schedule as $row)
                        <tr>
                            <td>{{ $row['description'] }}</td>
                            <td class="right">Rp {{ number_format($row['amount'], 0, ',', '.') }}</td>
                            <td class="center" style="width: 22%;">{{ $row['status'] === 'verified' ? 'LUNAS' : 'MENUNGGU' }}</td>
                        </tr>
                    @endforeach
                </table>
            @endif
        </td>
        <td style="width: 48%; vertical-align: top;">
            <table class="totals-table">
                <tr><td>Total Harga Dasar</td><td class="right">Rp {{ number_format($bd['total_base'], 0, ',', '.') }}</td></tr>
                <tr><td>Biaya Percepatan (SLA)</td><td class="right">Rp {{ number_format($bd['total_fee'], 0, ',', '.') }}</td></tr>
                <tr><td>Total Harga Master</td><td class="right">Rp {{ number_format($bd['total_master'], 0, ',', '.') }}</td></tr>
                @if($bd['total_discount'] > 0)
                    <tr><td>Diskon</td><td class="right" style="color: #dc2626;">-Rp {{ number_format($bd['total_discount'], 0, ',', '.') }}</td></tr>
                @endif
                <tr><td>Subtotal</td><td class="right">Rp {{ number_format($bd['total_final'], 0, ',', '.') }}</td></tr>
                <tr><td>Ongkos Kirim</td><td class="right">Rp {{ number_format($shipping, 0, ',', '.') }}</td></tr>
                <tr class="grand"><td>TOTAL TAGIHAN</td><td class="right">Rp {{ number_format($grand, 0, ',', '.') }}</td></tr>
                @if($paid > 0)
                    <tr><td class="muted">Terbayar</td><td class="right muted">Rp {{ number_format($paid, 0, ',', '.') }}</td></tr>
                    <tr><td class="bold">Sisa Tagihan</td><td class="right bold">Rp {{ number_format($grand - $paid, 0, ',', '.') }}</td></tr>
                @endif
            </table>
        </td>
    </tr>
</table>

{{-- ===== CATATAN PPN ===== --}}
<p class="note" style="margin-top: 12px;">
    <span class="bold">Catatan Perpajakan (PPN):</span> Seluruh harga pada invoice ini sudah termasuk Pajak Pertambahan Nilai (PPN) sesuai ketentuan peraturan perundang-undangan (UU PPN s.t.d.d. UU No. 7/2021 tentang HPP jo. PP No. 44/2022). Tidak ada pungutan pajak tambahan di luar Total Tagihan.
</p>

{{-- ===== FOOTER ===== --}}
<table style="margin-top: 18px; border-top: 1px solid #e5e7eb;">
    <tr>
        <td class="small muted" style="padding-top: 8px;">Terima kasih atas kepercayaan Anda menggunakan jasa cetak PrintOrder.</td>
        <td class="small muted right" style="padding-top: 8px; width: 40%;">PRINTORDER OFFICIAL</td>
    </tr>
</table>
</body>
</html>