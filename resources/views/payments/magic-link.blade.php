<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - {{ $invoice->order->invoice_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta http-equiv="refresh" content="30"> <!-- Auto refresh setiap 30 detik -->
</head>
<body class="bg-gray-100 min-h-screen py-8 px-4">

    <div class="max-w-md mx-auto">
        <!-- Header -->
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">🖨️ PrintOrder</h1>
            <p class="text-sm text-gray-500 mt-1">Invoice: {{ $invoice->order->invoice_number }}</p>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 text-sm text-center">
                {{ session('success') }}
            </div>
        @endif

        <!-- Card 1: Total Tagihan -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-4 border-t-4 border-blue-600">
            <p class="text-sm text-gray-500 mb-1">Total Tagihan Anda</p>
            <h2 class="text-3xl font-bold text-gray-900">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</h2>
            <div class="mt-4 pt-4 border-t border-gray-100 flex justify-between items-center">
                <span class="text-xs text-gray-500">Status</span>
                <span class="px-2 py-1 text-xs font-bold rounded-full {{ $invoice->status == 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                    {{ strtoupper($invoice->status) }}
                </span>
            </div>
            
            @if($invoice->status != 'paid')
            <a href="{{ route('admin.invoices.download', $invoice->id) }}" target="_blank" class="mt-4 block w-full text-center text-xs text-blue-600 hover:underline">
                📄 Download Invoice PDF
            </a>
            @endif
        </div>

        @if($invoice->status != 'paid')
            <!-- Cek apakah ada pembayaran pending -->
            @php
                $hasPendingPayment = $invoice->payments()->where('status', 'pending')->exists();
                $latestPendingPayment = $invoice->payments()->where('status', 'pending')->latest()->first();
            @endphp

            @if($hasPendingPayment)
                <!-- Card: Menunggu Verifikasi -->
                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded mb-4">
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-yellow-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <p class="text-sm font-bold text-yellow-800"> Menunggu Verifikasi Admin</p>
                            <p class="text-xs text-yellow-700 mt-1">
                                Bukti transfer sudah diupload. Mohon tunggu hingga admin memverifikasi pembayaran Anda.
                            </p>
                            <p class="text-xs text-yellow-600 mt-2">
                                Halaman ini akan refresh otomatis setiap 30 detik.
                            </p>
                            
                            @if($latestPendingPayment)
                                <div class="mt-3 pt-3 border-t border-yellow-300">
                                    <p class="text-xs text-yellow-700">
                                        <strong>Nominal:</strong> Rp {{ number_format($latestPendingPayment->amount, 0, ',', '.') }}<br>
                                        <strong>Upload:</strong> {{ $latestPendingPayment->created_at->format('d M Y, H:i') }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <!-- Card 2: Info Transfer -->
                <div class="bg-white rounded-xl shadow-sm p-6 mb-4">
                    <h3 class="text-sm font-semibold text-gray-800 mb-3">Transfer ke Rekening:</h3>
                    <div class="bg-blue-50 rounded-lg p-4 flex justify-between items-center">
                        <div>
                            <p class="text-xs text-gray-500">{{ $bankName }}</p>
                            <p class="text-lg font-bold text-gray-900 tracking-wider">{{ $bankAccount }}</p>
                            <p class="text-xs text-gray-600">a.n {{ $bankHolder }}</p>
                        </div>
                        <button onclick="navigator.clipboard.writeText('{{ $bankAccount }}'); alert('No. Rek disalin!')" class="text-blue-600 hover:text-blue-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                        </button>
                    </div>
                </div>

                <!-- Card 3: Form Upload -->
                <div class="bg-white rounded-xl shadow-sm p-6 mb-4">
                    <h3 class="text-sm font-semibold text-gray-800 mb-4">Upload Bukti Transfer</h3>
                    
                    <!-- Tampilkan Info Pembayaran yang Harus Dilakukan -->
                    @if($invoice->payment_schedule && count($invoice->payment_schedule) > 0)
                        @php
                            $pendingPayment = null;
                            foreach($invoice->payment_schedule as $schedule) {
                                if ($schedule['status'] === 'pending') {
                                    $pendingPayment = $schedule;
                                    break;
                                }
                            }
                        @endphp

                        @if($pendingPayment)
                            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded mb-4">
                                <p class="text-sm text-gray-700 mb-1">Silakan Transfer:</p>
                                <p class="text-2xl font-bold text-blue-900">Rp {{ number_format($pendingPayment['amount'], 0, ',', '.') }}</p>
                                <p class="text-xs text-gray-600 mt-1 font-semibold">{{ $pendingPayment['description'] }}</p>
                            </div>
                        @else
                            <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded mb-4">
                                <p class="text-sm text-green-800 font-semibold">✅ Semua pembayaran sudah lunas!</p>
                                <p class="text-xs text-green-700 mt-1">Terima kasih, pesanan Anda sedang diproses.</p>
                            </div>
                        @endif
                    @else
                        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded mb-4">
                            <p class="text-sm text-gray-700 mb-1">Silakan Transfer:</p>
                            <p class="text-2xl font-bold text-blue-900">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</p>
                        </div>
                    @endif

                    <form action="{{ route('payment.upload', $invoice->payment_token) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <input type="hidden" name="bank_name_hidden" value="{{ $bankName }}">

                        <!-- Input File Bukti Transfer -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Upload Bukti Transfer</label>
                            <input type="file" name="proof_files[]" multiple required accept=".jpg,.jpeg,.png,.pdf,.zip"
                                   class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            @error('proof_files.*') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 rounded-lg hover:bg-blue-700 transition shadow-lg shadow-blue-500/30">
                            Kirim Bukti Pembayaran
                        </button>
                    </form>
                </div>
            @endif
        @else
            <!-- Card Jika Sudah Lunas -->
            <div class="bg-green-50 border border-green-200 rounded-xl p-6 text-center">
                <svg class="w-12 h-12 text-green-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <h3 class="text-lg font-bold text-green-800">Pembayaran Berhasil!</h3>
                <p class="text-sm text-green-700 mt-1">Terima kasih, pesanan Anda sedang diproses.</p>
            </div>
        @endif

        <p class="text-center text-xs text-gray-400 mt-6">© 2026 PrintOrder. All rights reserved.</p>
    </div>

</body>
</html>