<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pembayaran Invoice {{ $invoice->order->invoice_number ?? '#'.$invoice->id }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen py-8 px-4">
    <div class="max-w-2xl mx-auto">

        {{-- ============ HEADER ============ --}}
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6 text-center border-t-4 border-blue-600">
            <h1 class="text-2xl font-bold text-gray-800">{{ setting('shop_name', config('app.name', 'PrintOrder')) }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ setting('shop_address', '') }}</p>
            <div class="mt-4 pt-4 border-t border-gray-200">
                <p class="text-xs text-gray-500 uppercase tracking-wide">Invoice</p>
                <p class="text-lg font-mono font-bold text-blue-600">{{ $invoice->order->invoice_number ?? '#'.$invoice->id }}</p>
                <p class="text-sm text-gray-600 mt-1">Untuk: <strong>{{ $invoice->order->user->name ?? '-' }}</strong></p>
            </div>
        </div>

        {{-- ============ RINGKASAN TAGIHAN ============ --}}
        <div class="bg-white rounded-xl shadow p-6 mb-6">
            <h2 class="text-sm font-semibold text-gray-500 uppercase mb-3">Ringkasan Tagihan</h2>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Subtotal Item</span>
                    <span class="font-medium">Rp {{ number_format($invoice->subtotal ?? 0, 0, ',', '.') }}</span>
                </div>
                @if(($invoice->shipping_cost ?? 0) > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Ongkos Kirim</span>
                        <span class="font-medium">Rp {{ number_format($invoice->shipping_cost, 0, ',', '.') }}</span>
                    </div>
                @endif
                <div class="flex justify-between pt-3 border-t border-gray-200 text-lg font-bold text-gray-900">
                    <span>Total Tagihan</span>
                    <span class="text-blue-600">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</span>
                </div>
                @if(($invoice->total_paid ?? 0) > 0)
                    <div class="flex justify-between text-green-600">
                        <span>Sudah Dibayar</span>
                        <span class="font-medium">- Rp {{ number_format($invoice->total_paid, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between pt-2 border-t border-dashed border-gray-300 text-base font-bold text-orange-600">
                        <span>Sisa Tagihan</span>
                        <span>Rp {{ number_format($invoice->total_amount - $invoice->total_paid, 0, ',', '.') }}</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- ============ INFO REKENING (DARI SHOPSETTING) ============ --}}
        @if(setting('bank_account'))
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-5 mb-6">
                <h3 class="text-sm font-bold text-blue-800 mb-3 flex items-center">
                    💳 Transfer Pembayaran Ke:
                </h3>
                <div class="bg-white rounded-lg border border-blue-200 p-4 space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Bank</span>
                        <span class="font-bold">{{ setting('bank_name', '-') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">No. Rekening</span>
                        <span class="font-bold font-mono text-lg text-blue-700">{{ setting('bank_account', '-') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Atas Nama</span>
                        <span class="font-bold">{{ setting('bank_holder', '-') }}</span>
                    </div>
                </div>
                <p class="text-xs text-blue-600 mt-3 italic">
                    ⚠️ Transfer sesuai nominal sisa tagihan agar verifikasi lebih cepat.
                </p>
            </div>
        @endif

        {{-- ============ FLASH MESSAGES ============ --}}
        @if (session('success'))
            <div class="p-4 mb-6 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200">
                ✅ {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 mb-6 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ============ FORM UPLOAD BUKTI ============ --}}
        <div class="bg-white rounded-xl shadow p-6 mb-6">
            <h2 class="text-sm font-semibold text-gray-500 uppercase mb-4">Upload Bukti Pembayaran</h2>
            <form action="{{ route('payment.upload', $invoice->payment_token) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nominal Transfer (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" name="amount" required min="1000"
                           value="{{ old('amount', $invoice->total_amount - ($invoice->total_paid ?? 0)) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 font-mono">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Metode Pembayaran <span class="text-red-500">*</span></label>
                    <select name="payment_method" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Pilih Metode --</option>
                        <option value="Transfer Bank">Transfer Bank</option>
                        <option value="E-Wallet">E-Wallet (GoPay/OVO/Dana)</option>
                        <option value="QRIS">QRIS</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Foto/Scan Bukti Transfer <span class="text-red-500">*</span></label>
                    <input type="file" name="proof" required accept="image/*,.pdf"
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, atau PDF (maks. 5MB)</p>
                </div>

                <button type="submit"
                        class="w-full py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                    📤 Upload Bukti Pembayaran
                </button>
            </form>
        </div>

        {{-- ============ FOOTER ============ --}}
        <div class="text-center text-xs text-gray-500 pb-8">
            <p>{{ setting('invoice_footer', 'Terima kasih atas kepercayaan Anda!') }}</p>
            <p class="mt-2">Butuh bantuan? Hubungi {{ setting('shop_phone', '-') }}</p>
        </div>

    </div>
</body>
</html>