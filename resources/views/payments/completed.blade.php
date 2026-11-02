<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Pesanan Selesai</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white max-w-md w-full p-8 rounded-xl shadow-lg text-center">
        <div class="text-6xl mb-4">✅</div>
        <h1 class="text-2xl font-bold text-green-600 mb-2">Pesanan Selesai</h1>
        <p class="text-gray-600 mb-4">
            Pesanan <strong>{{ $invoice->order->invoice_number ?? '#' . $invoice->id }}</strong>
            telah selesai. Invoice ini tidak lagi menerima pembayaran.
        </p>
        <p class="text-sm text-gray-500">Terima kasih telah berbisnis dengan kami! 🙏</p>
    </div>
</body>
</html>