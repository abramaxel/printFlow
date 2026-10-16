<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->order->invoice_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; line-height: 1.5; }
        .header { display: flex; justify-content: space-between; margin-bottom: 30px; border-bottom: 2px solid #2563eb; padding-bottom: 10px; }
        .company-name { font-size: 24px; font-weight: bold; color: #2563eb; }
        .invoice-title { font-size: 20px; font-weight: bold; text-align: right; color: #1f2937; }
        .info-section { margin-bottom: 30px; }
        .info-box h3 { font-size: 11px; color: #6b7280; text-transform: uppercase; margin-bottom: 5px; }
        .info-box p { margin: 2px 0; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background-color: #f3f4f6; color: #374151; text-align: left; padding: 10px; font-size: 11px; text-transform: uppercase; border-bottom: 2px solid #e5e7eb; }
        td { padding: 12px 10px; border-bottom: 1px solid #e5e7eb; font-size: 12px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-section { margin-top: 20px; }
        .total-row { display: flex; justify-content: space-between; padding: 8px 0; font-size: 12px; }
        .grand-total { font-size: 16px; font-weight: bold; color: #2563eb; border-top: 2px solid #e5e7eb; padding-top: 10px; margin-top: 5px; }
        .footer { margin-top: 50px; text-align: center; font-size: 10px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 10px; }
        .status-badge { display: inline-block; padding: 4px 12px; border-radius: 4px; font-size: 11px; font-weight: bold; }
        .status-unpaid { background-color: #fef3c7; color: #d97706; }
        .status-paid { background-color: #d1fae5; color: #059669; }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header">
        <div>
            <div class="company-name">🖨️ PrintOrder</div>
            <p style="margin: 5px 0 0; font-size: 11px; color: #6b7280;">Jl. Percetakan Digital No. 123<br>Jakarta, Indonesia<br>info@printorder.test</p>
        </div>
        <div style="text-align: right;">
            <div class="invoice-title">INVOICE</div>
            <p style="margin: 8px 0 0; font-size: 12px;">
                <strong>No:</strong> {{ $invoice->order->invoice_number }}<br>
                <strong>Tanggal:</strong> {{ $invoice->created_at->format('d M Y') }}<br>
                <strong>Jatuh Tempo:</strong> {{ $invoice->due_date->format('d M Y') }}
            </p>
        </div>
    </div>

    <!-- Info Customer -->
    <div class="info-section" style="display: flex; justify-content: space-between; margin-bottom: 30px;">
        <div style="width: 60%;">
            <h3 style="font-size: 11px; color: #6b7280; text-transform: uppercase; margin-bottom: 8px;">Ditagihkan Kepada:</h3>
            <p style="margin: 0; font-weight: bold; font-size: 13px;">{{ $invoice->order->user->name }}</p>
            <p style="margin: 3px 0 0; font-size: 12px;">{{ $invoice->order->user->email }}</p>
            <p style="margin: 3px 0 0; font-size: 12px;">{{ $invoice->order->user->phone ?? '-' }}</p>
        </div>
        <div style="width: 35%; text-align: right;">
            <h3 style="font-size: 11px; color: #6b7280; text-transform: uppercase; margin-bottom: 8px;">Status Pembayaran:</h3>
            <span class="status-badge {{ $invoice->status == 'paid' ? 'status-paid' : 'status-unpaid' }}">
                {{ strtoupper($invoice->status) }}
            </span>
        </div>
    </div>

    <!-- Tabel Item -->
    <table>
        <thead>
            <tr>
                <th style="width: 40%;">Deskripsi</th>
                <th class="text-center">Qty</th>
                <th class="text-center">Luas (m²)</th>
                <th class="text-right">Harga Satuan</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->order->items as $item)
                <tr>
                    <td>
                        <strong style="font-size: 12px;">{{ $item->service->name }}</strong><br>
                        <span style="font-size: 10px; color: #6b7280;">{{ $item->material->name }} | {{ $item->size->name }}</span>
                    </td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-center">{{ number_format($item->area_m2, 2) }}</td>
                    <td class="text-right">
                        @php
                            $unitPrice = $item->final_subtotal / $item->quantity;
                        @endphp
                        Rp {{ number_format($unitPrice, 0, ',', '.') }}
                    </td>
                    <td class="text-right" style="font-weight: bold;">
                        Rp {{ number_format($item->final_subtotal, 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Total -->
    <div class="total-section">
        <div style="width: 300px; margin-left: auto;">
            <div class="total-row">
                <span>Subtotal Item:</span>
                <span style="font-weight: 600;">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="total-row">
                <span>Ongkos Kirim:</span>
                <span>Rp {{ number_format($invoice->shipping_cost, 0, ',', '.') }}</span>
            </div>
            <div class="total-row">
                <span>Pajak/PPN:</span>
                <span>Rp {{ number_format($invoice->tax, 0, ',', '.') }}</span>
            </div>
            <div class="grand-total total-row">
                <span>TOTAL TAGIHAN:</span>
                <span>Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p style="margin: 0 0 5px;">Terima kasih atas kepercayaan Anda menggunakan jasa PrintOrder.</p>
        <p style="margin: 0; font-size: 10px;">
            Silakan transfer ke rekening BCA: 123-456-7890 a.n PrintOrder dan upload bukti di aplikasi.
        </p>
    </div>

</body>
</html>