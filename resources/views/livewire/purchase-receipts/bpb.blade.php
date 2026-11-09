<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $receipt->bpb_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #111; margin: 24px; }
        .header { border-bottom: 3px solid #111; padding-bottom: 10px; margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center; }
        h1 { font-size: 17px; margin: 0 0 4px 0; }
        .muted { color: #6b7280; font-size: 10px; }
        table.info { width: 100%; margin-bottom: 14px; }
        table.info td { padding: 2px 0; vertical-align: top; }
        table.items { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        table.items th, table.items td { border: 1px solid #999; padding: 6px 8px; font-size: 11px; }
        table.items th { background: #f3f4f6; text-align: left; }
        .right { text-align: right; }
        .total-row td { font-weight: bold; background: #f9fafb; }
        .sign { margin-top: 48px; display: flex; justify-content: space-between; }
        .sign div { width: 45%; text-align: center; }
        .sign .space { height: 70px; }
        .footer { margin-top: 36px; text-align: center; font-size: 9px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <h1>BUKTI PENERIMAAN BARANG (BPB)</h1>
            <p class="muted">{{ config('app.brand.name', 'PrintOrder') }} — Dokumen internal & tanda terima untuk supplier</p>
        </div>
        <div class="right">
            <strong>No: {{ $receipt->bpb_number }}</strong><br>
            <span class="muted">Tanggal: {{ $receipt->receipt_date->format('d/m/Y') }}</span>
        </div>
    </div>

    <table class="info">
        <tr>
            <td style="width: 50%;">
                <strong>Diterima dari:</strong><br>
                {{ $receipt->supplier->name }}<br>
                @if($receipt->supplier->contact_person) CP: {{ $receipt->supplier->contact_person }} @if($receipt->supplier->phone) ({{ $receipt->supplier->phone }}) @endif<br>@endif
                @if($receipt->supplier->address)<span class="muted">{{ $receipt->supplier->address }}</span>@endif
            </td>
            <td>
                <strong>Referensi:</strong><br>
                No. Surat Jalan Supplier: {{ $receipt->supplier_doc_number ?? '-' }}<br>
                Purchase Request: {{ $receipt->purchaseRequest->pr_number ?? '-' }}<br>
                Diterima oleh: {{ $receipt->receiver->name ?? '-' }}
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 35%;">Nama Bahan</th>
                <th style="width: 12%;">Qty</th>
                <th style="width: 8%;">Satuan</th>
                <th style="width: 14%;">Harga Satuan</th>
                <th style="width: 14%;">Total</th>
                <th style="width: 12%;">Batch / Exp</th>
            </tr>
        </thead>
        <tbody>
            @foreach($receipt->items as $i => $it)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $it->material->name ?? '-' }}</td>
                    <td class="right">{{ number_format($it->quantity, 2) }}</td>
                    <td>{{ $it->unit }}</td>
                    <td class="right">{{ number_format($it->unit_cost, 2) }}</td>
                    <td class="right">{{ number_format($it->total_cost, 2) }}</td>
                    <td class="muted">{{ $it->batch_number ?? '-' }}<br>{{ $it->expiry_date ? $it->expiry_date->format('d/m/Y') : '-' }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="5" class="right">TOTAL NILAI PENERIMAAN</td>
                <td class="right">{{ number_format($receipt->total_cost, 2) }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>

    @if($receipt->notes)
        <p><strong>Catatan:</strong> {{ $receipt->notes }}</p>
    @endif

    <p class="muted">Barang telah diperiksa secara fisik (jumlah & kondisi) sesuai surat jalan supplier. Dokumen ini menjadi dasar pencatatan persediaan dan verifikasi tagihan supplier.</p>

    <div class="sign">
        <div>
            <strong>Yang Menyerahkan,</strong>
            <div class="space"></div>
            <p>( ______________________ )</p>
            <p class="muted">Pihak Supplier</p>
        </div>
        <div>
            <strong>Yang Menerima,</strong>
            <div class="space"></div>
            <p>( {{ $receipt->receiver->name ?? '______________________' }} )</p>
            <p class="muted">Staf Gudang {{ config('app.brand.name', 'PrintOrder') }}</p>
        </div>
    </div>

    <p class="footer">Dokumen dicetak otomatis oleh sistem pada {{ now()->format('d M Y, H:i') }} — {{ $receipt->bpb_number }}</p>
</body>
</html>