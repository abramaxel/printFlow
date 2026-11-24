<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Invoice Dibatalkan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white max-w-md w-full p-8 rounded-xl shadow-lg text-center">
        <div class="text-6xl mb-4">🚫</div>
        <h1 class="text-2xl font-bold text-red-600 mb-2">Invoice Dibatalkan</h1>
        <p class="text-gray-600 mb-4">
            Invoice <strong>{{ $invoice->order->invoice_number ?? '#' . $invoice->id }}</strong>
            telah dibatalkan dan tidak lagi menerima pembayaran.
        </p>
        <div class="p-3 bg-red-50 border border-red-200 rounded-lg text-left text-sm">
            <p class="text-gray-700 font-semibold">Alasan:</p>
            <p class="italic text-gray-600">"{{ $invoice->cancelled_reason ?? '-' }}"</p>
        </div>
        <p class="text-sm text-gray-500 mt-4">Silakan hubungi admin untuk informasi lebih lanjut.</p>
    </div>
</body>
</html>