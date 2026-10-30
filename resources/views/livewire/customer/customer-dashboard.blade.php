<div>
    @if (session()->has('success'))
        <div class="p-4 mb-6 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Dashboard Saya</h1>
        <p class="text-gray-500 mt-1">Kelola pesanan dan pembayaran Anda di {{ config('app.brand.name', 'PrintOrder') }}.</p>
    </div>

    {{-- Statistik Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <p class="text-sm font-medium text-gray-500">Total Pesanan</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $totalOrders }}</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <p class="text-sm font-medium text-gray-500">Pesanan Aktif</p>
            <p class="text-2xl font-bold text-orange-600 mt-1">{{ $activeOrders }}</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <p class="text-sm font-medium text-gray-500">Selesai</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ $completedOrders }}</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <p class="text-sm font-medium text-gray-500">Total Belanja</p>
            <p class="text-2xl font-bold text-purple-600 mt-1">Rp {{ number_format($totalSpent, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Pembayaran Pending (Perlu Upload Bukti) --}}
    @if($pendingPayments->count() > 0)
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 mb-8">
        <h2 class="text-lg font-bold text-yellow-800 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            Menunggu Upload Bukti Pembayaran
        </h2>
        <div class="space-y-3">
            @foreach($pendingPayments as $payment)
                <div class="bg-white p-4 rounded-lg border border-yellow-200 flex justify-between items-center">
                    <div>
                        <p class="font-semibold text-gray-900">{{ $payment->invoice->order->invoice_number }}</p>
                        <p class="text-sm text-gray-600">Nominal: <span class="font-bold text-yellow-700">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span></p>
                        <p class="text-xs text-gray-500">Invoice: {{ $payment->invoice->order->invoice_number }}</p>
                    </div>
                    <button wire:click="openUploadModal({{ $payment->id }}, {{ $payment->amount }})" class="px-4 py-2 bg-yellow-500 text-white text-sm font-semibold rounded-lg hover:bg-yellow-600 transition">
                        📤 Upload Bukti
                    </button>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Riwayat Pesanan --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-bold text-gray-800">Riwayat Pesanan</h2>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse($orders as $order)
                <div class="px-6 py-4">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <p class="font-bold text-gray-900">{{ $order->invoice_number }}</p>
                            <p class="text-sm text-gray-500">{{ $order->created_at->format('d M Y, H:i') }}</p>
                        </div>
                        <span class="px-3 py-1 text-xs font-semibold rounded-full
                            @if($order->status === 'completed') bg-green-100 text-green-700
                            @elseif($order->status === 'cancelled' || $order->status === 'rejected') bg-red-100 text-red-700
                            @elseif($order->status === 'processing' || $order->status === 'qc') bg-blue-100 text-blue-700
                            @elseif($order->status === 'unpaid') bg-yellow-100 text-yellow-700
                            @else bg-gray-100 text-gray-700
                            @endif">
                            {{ strtoupper(str_replace('_', ' ', $order->status)) }}
                        </span>
                    </div>

                    {{-- Order Track Visual --}}
                    <div class="mb-3">
                        <div class="flex items-center justify-between text-xs">
                            @php
                                $steps = ['waiting_invoice', 'unpaid', 'paid', 'processing', 'qc', 'shipping', 'completed'];
                                $currentStep = array_search($order->status, $steps);
                                if ($currentStep === false) $currentStep = 0;
                            @endphp
                            @foreach(['Dibuat', 'Belum Bayar', 'SPK Terbit', 'Diproses', 'QC', 'Dikirim', 'Selesai'] as $i => $stepName)
                                <div class="flex flex-col items-center flex-1">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold
                                        @if($i <= $currentStep) bg-blue-600 text-white @else bg-gray-200 text-gray-500 @endif">
                                        @if($i < $currentStep) ✓ @else {{ $i + 1 }} @endif
                                    </div>
                                    <span class="text-[10px] mt-1 text-center {{ $i <= $currentStep ? 'text-blue-600 font-semibold' : 'text-gray-400' }}">{{ $stepName }}</span>
                                </div>
                                @if(!$loop->last)
                                    <div class="flex-1 h-0.5 {{ $i < $currentStep ? 'bg-blue-600' : 'bg-gray-200' }} mx-1"></div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    {{-- Detail Item --}}
                    <div class="bg-gray-50 p-3 rounded-lg text-sm">
                        @foreach($order->items as $item)
                            <div class="flex justify-between items-center mb-1 last:mb-0">
                                <span class="text-gray-700">{{ $item->service->name }} - {{ $item->material->name }} ({{ $item->quantity }}x)</span>
                                <span class="font-semibold text-gray-900">Rp {{ number_format($item->final_subtotal, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                        <div class="border-t border-gray-200 mt-2 pt-2 flex justify-between items-center font-bold">
                            <span>Total:</span>
                            <span class="text-blue-600">Rp {{ number_format($order->items->sum('final_subtotal') + ($order->invoice?->shipping_cost ?? 0), 0, ',', '.') }}</span>
                        </div>
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="mt-3 flex gap-2">
                        @if($order->invoice)
                            <a href="{{ route('admin.invoices.download', $order->invoice->id) }}" target="_blank" class="px-3 py-1.5 bg-gray-100 text-gray-700 text-xs font-semibold rounded-md hover:bg-gray-200 transition">
                                📄 Download Invoice
                            </a>
                        @endif
                        @if($order->status === 'completed')
                            <span class="px-3 py-1.5 bg-green-100 text-green-700 text-xs font-semibold rounded-md">
                                ✅ Pesanan Selesai
                            </span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="px-6 py-12 text-center text-gray-400">
                    <svg class="w-16 h-16 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                    <p class="font-medium">Belum ada pesanan</p>
                    <p class="text-sm mt-1">Pesanan Anda akan muncul di sini setelah dibuat oleh admin.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Modal Upload Bukti Pembayaran --}}
    @if($uploadPaymentId)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" wire:click.self="$set('uploadPaymentId', null)">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6" wire:click.stop>
            <h3 class="text-lg font-bold text-gray-800 mb-4">Upload Bukti Pembayaran</h3>
            
            <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-sm text-blue-800">Nominal: <span class="font-bold">Rp {{ number_format($uploadAmount, 0, ',', '.') }}</span></p>
                <p class="text-xs text-blue-600 mt-1">Silakan transfer ke rekening yang tertera di invoice, lalu upload bukti transfer di sini.</p>
            </div>

            <form wire:submit="submitUpload">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">File Bukti Transfer *</label>
                    <input type="file" wire:model="uploadProof" accept=".jpg,.jpeg,.png,.pdf" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('uploadProof') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    
                    @if($uploadProof)
                        <p class="mt-2 text-xs text-gray-500">File: {{ $uploadProof->getClientOriginalName() }}</p>
                    @endif
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan (Opsional)</label>
                    <textarea wire:model="uploadNotes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Contoh: Transfer dari BCA, tanggal 20 Okt 2026"></textarea>
                </div>

                <div class="flex gap-2">
                    <button type="button" wire:click="$set('uploadPaymentId', null)" class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-200 transition">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition" wire:loading.attr="disabled">
                        <span wire:loading.remove>📤 Upload Bukti</span>
                        <span wire:loading>Mengupload...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>