<div class="space-y-6">
    
    @if (session()->has('finance_success'))
        <div class="p-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200">
            {{ session('finance_success') }}
        </div>
    @endif

    <!-- ========================================== -->
    <!-- CARD 1: INVOICE MANAGEMENT -->
    <!-- ========================================== -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            Manajemen Invoice
        </h3>

        @if(!$order->invoice)
            <!-- Form Buat Invoice -->
            <form wire:submit="generateInvoice" class="space-y-4">
                
                <!-- Pilih Metode & Skema -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-1 text-xs font-bold text-gray-700 uppercase">Metode Pembayaran</label>
                        <div class="flex space-x-2">
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" wire:model="payment_method" value="transfer" class="hidden peer">
                                <div class="text-center py-2 border-2 border-gray-200 rounded-lg peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:text-blue-700 text-sm font-medium transition">
                                    Transfer
                                </div>
                            </label>
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" wire:model="payment_method" value="cash" class="hidden peer">
                                <div class="text-center py-2 border-2 border-gray-200 rounded-lg peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:text-green-700 text-sm font-medium transition">
                                    Tunai
                                </div>
                            </label>
                        </div>
                    </div>
                    <div>
                        <label class="block mb-1 text-xs font-bold text-gray-700 uppercase">Skema Pembayaran</label>
                        <div class="flex space-x-2">
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" wire:model="payment_scheme" value="full" class="hidden peer">
                                <div class="text-center py-2 border-2 border-gray-200 rounded-lg peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:text-blue-700 text-sm font-medium transition">
                                    Langsung Lunas
                                </div>
                            </label>
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" wire:model="payment_scheme" value="installment" class="hidden peer">
                                <div class="text-center py-2 border-2 border-gray-200 rounded-lg peer-checked:border-orange-500 peer-checked:bg-orange-50 peer-checked:text-orange-700 text-sm font-medium transition">
                                    Cicilan / DP
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Ongkir & Pajak -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Ongkos Kirim (Rp)</label>
                        <input type="number" wire:model="shipping_cost" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Pajak/PPN (Rp)</label>
                        <input type="number" wire:model="tax" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    </div>
                </div>

                <!-- Input Dinamis: TUNAI -->
                @if($payment_method === 'cash')
                    <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                        <label class="block mb-1 text-sm font-bold text-green-800">Nominal Tunai yang Diterima (Rp)</label>
                        <input type="number" wire:model="cash_amount" class="bg-white border border-green-300 text-gray-900 text-lg font-bold rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5 mb-3">
                        
                        <label class="block mb-1 text-sm font-bold text-green-800">Upload Foto Kuitansi</label>
                        <input type="file" wire:model="cash_receipt" accept=".jpg,.jpeg,.png,.pdf" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-green-100 file:text-green-700 hover:file:bg-green-200">
                        @error('cash_receipt') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                @endif

                <!-- Input Dinamis: CICILAN -->
                @if($payment_scheme === 'installment')
                    <div class="p-4 bg-orange-50 border border-orange-200 rounded-lg space-y-3">
                        <div>
                            <label class="block mb-1 text-sm font-bold text-orange-800">DP (% dari Total) <span class="text-red-500">*</span></label>
                            <input type="number" wire:model="dp_percentage" min="0" max="100" placeholder="Contoh: 50 untuk DP 50%" class="bg-white border border-orange-300 text-gray-900 text-lg font-bold rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5">
                            <p class="text-xs text-orange-600 mt-1">*Wajib diisi untuk skema cicilan</p>
                        </div>
                        
                        <div>
                            <label class="block mb-1 text-sm font-bold text-orange-800">Jumlah Cicilan (setelah DP) <span class="text-red-500">*</span></label>
                            <input type="number" wire:model="installment_count" min="1" max="12" placeholder="Contoh: 2 untuk 2x cicilan" class="bg-white border border-orange-300 text-gray-900 text-lg font-bold rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5">
                            <p class="text-xs text-orange-600 mt-1">*Wajib diisi (1-12 kali)</p>
                        </div>

                        <!-- Preview Perhitungan -->
                        @php
                            $estTotal = $order->items->sum('final_subtotal') + $shipping_cost + $tax;
                            $dpAmount = round($estTotal * ($dp_percentage / 100), 2);
                            $remaining = $estTotal - $dpAmount;
                            $perInstallment = $installment_count > 0 ? round($remaining / $installment_count, 2) : 0;
                        @endphp
                        <div class="bg-white p-3 rounded border border-orange-200 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Total Tagihan:</span>
                                <span class="font-bold">Rp {{ number_format($estTotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">DP ({{ $dp_percentage }}%):</span>
                                <span class="font-bold text-orange-700">Rp {{ number_format($dpAmount, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Sisa:</span>
                                <span class="font-bold">Rp {{ number_format($remaining, 0, ',', '.') }}</span>
                            </div>
                            @if($installment_count > 0)
                            <div class="flex justify-between text-sm border-t pt-2">
                                <span class="text-gray-600">Per Cicilan ({{ $installment_count }}x):</span>
                                <span class="font-bold text-blue-700">Rp {{ number_format($perInstallment, 0, ',', '.') }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                @endif

                <x-ui.button type="submit" variant="primary" class="w-full justify-center">
                    Generate Invoice
                </x-ui.button>
            </form>
        @else
            <!-- Tampilan Invoice Sudah Jadi -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex justify-between mb-2">
                    <span class="text-sm text-gray-600">Status Invoice:</span>
                    <span class="text-sm font-bold {{ $order->invoice->status == 'paid' ? 'text-green-600' : 'text-yellow-600' }}">
                        {{ strtoupper($order->invoice->status) }}
                    </span>
                </div>

                <!-- Info Metode & Skema -->
                <div class="flex gap-2 mt-2 mb-3">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                        {{ $order->invoice->payment_method == 'cash' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                        {{ $order->invoice->payment_method == 'cash' ? '💵 Tunai' : '🏦 Transfer' }}
                    </span>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                        {{ $order->invoice->payment_scheme == 'installment' ? 'bg-orange-100 text-orange-700' : 'bg-gray-100 text-gray-700' }}">
                        {{ $order->invoice->payment_scheme == 'installment' ? ' Cicilan' : '✅ Langsung Lunas' }}
                    </span>
                </div>

                <div class="flex justify-between text-lg font-bold text-gray-900 mt-3 pt-3 border-t border-blue-200">
                    <span>Total Tagihan:</span>
                    <span>Rp {{ number_format($order->invoice->total_amount, 0, ',', '.') }}</span>
                </div>
                <p class="text-xs text-gray-500 mt-2">Jatuh Tempo: {{ $order->invoice->due_date?->format('d M Y') }}</p>

                <!-- ========================================== -->
                <!-- DETAIL SKEMA CICILAN (Jika Ada) -->
                <!-- ========================================== -->
                @if($order->invoice->payment_scheme === 'installment' && $order->invoice->payment_schedule)
                    <div class="mt-4 p-3 bg-orange-50 border border-orange-200 rounded-lg">
                        <h4 class="text-xs font-bold text-orange-800 mb-2 flex items-center">
                            📋 Skema Pembayaran
                        </h4>
                        
                        @if($order->invoice->installment_notes)
                            <p class="text-xs text-gray-700 mb-3 italic">{{ $order->invoice->installment_notes }}</p>
                        @endif

                        <div class="space-y-2">
                            @foreach($order->invoice->payment_schedule as $index => $schedule)
                                @php
                                    $isPaid = $schedule['status'] === 'verified';
                                    $isPending = $schedule['status'] === 'pending';
                                @endphp
                                <div class="flex justify-between items-center bg-white p-2 rounded border {{ $isPaid ? 'border-green-300' : ($isPending ? 'border-yellow-300' : 'border-gray-200') }}">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-bold {{ $isPaid ? 'text-green-700' : 'text-gray-700' }}">
                                            {{ $schedule['type'] }}
                                        </span>
                                        <span class="text-[10px] text-gray-500">
                                            {{ $schedule['description'] }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-bold">
                                            Rp {{ number_format($schedule['amount'], 0, ',', '.') }}
                                        </span>
                                        <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full 
                                            {{ $isPaid ? 'bg-green-100 text-green-700' : ($isPending ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-600') }}">
                                            {{ $isPaid ? '✓ Lunas' : ($isPending ? '⏳ Menunggu' : 'Belum Dibayar') }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- ========================================== -->
                <!-- PROGRESS PEMBAYARAN + RIWAYAT DETAIL -->
                <!-- ========================================== -->
                <div class="mt-4 pt-4 border-t border-blue-200">
                    <div class="flex justify-between text-xs font-medium text-gray-600 mb-1">
                        <span>Progress Pembayaran</span>
                        <span>Rp {{ number_format($order->invoice->total_paid ?? 0, 0, ',', '.') }} / Rp {{ number_format($order->invoice->total_amount, 0, ',', '.') }}</span>
                    </div>
                    
                    @php
                        $totalPaid = $order->invoice->total_paid ?? 0;
                        $totalAmount = $order->invoice->total_amount;
                        $remaining = $totalAmount - $totalPaid;
                        $percentage = $totalAmount > 0 ? min(100, ($totalPaid / $totalAmount) * 100) : 0;
                    @endphp

                    <div class="w-full bg-gray-200 rounded-full h-2.5 mb-2">
                        <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                    </div>

                    <div class="flex justify-between items-center mb-3">
                        @if($percentage < 100)
                            <p class="text-xs text-orange-600 font-semibold">
                                 Sisa Tagihan: Rp {{ number_format($remaining, 0, ',', '.') }}
                            </p>
                            <span class="text-xs bg-orange-100 text-orange-700 px-2 py-1 rounded-full font-bold">
                                {{ number_format($percentage, 1) }}% Lunas
                            </span>
                        @else
                            <p class="text-xs text-green-600 font-semibold">
                                ✅ LUNAS
                            </p>
                            <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full font-bold">
                                100% Lunas
                            </span>
                        @endif
                    </div>

                    <!-- Riwayat Pembayaran Verified -->
                    @if($totalPaid > 0)
                        <div class="bg-white rounded-lg border border-blue-100 p-3 mb-3">
                            <h4 class="text-xs font-bold text-gray-700 mb-2 flex items-center">
                                <svg class="w-3 h-3 mr-1 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Riwayat Pembayaran Masuk
                            </h4>
                            <div class="space-y-2">
                                @foreach($order->invoice->payments()->where('status', 'verified')->orderBy('verified_at', 'desc')->get() as $payment)
                                    <div class="flex justify-between items-center text-xs border-b border-gray-100 pb-2 last:border-0 last:pb-0">
                                        <div>
                                            <p class="font-medium text-gray-900">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                                            <p class="text-[10px] text-gray-500">{{ $payment->payment_method }} • {{ $payment->verified_at?->format('d M Y, H:i') }}</p>
                                        </div>
                                        <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-[10px] font-semibold">
                                            ✓ Verified
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Pembayaran Pending (Perlu Verifikasi) -->
                    @if($order->invoice->payments()->where('status', 'pending')->count() > 0)
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-3">
                            <h4 class="text-xs font-bold text-yellow-800 mb-2 flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Menunggu Verifikasi
                            </h4>
                            @foreach($order->invoice->payments()->where('status', 'pending')->get() as $payment)
                                <div class="bg-white border border-yellow-200 rounded p-2 mb-2">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <p class="text-sm font-bold text-gray-900">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                                            <p class="text-[10px] text-gray-500">{{ $payment->payment_method }} • {{ $payment->created_at->format('d M Y, H:i') }}</p>
                                        </div>
                                        <span class="bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full text-[10px] font-semibold">
                                            Pending
                                        </span>
                                    </div>

                                    @if($payment->proof_file)
                                        <div class="mb-2 space-y-1">
                                            @foreach((array) $payment->proof_file as $file)
                                                @php $ext = pathinfo($file, PATHINFO_EXTENSION); @endphp
                                                @if(in_array(strtolower($ext), ['jpg', 'jpeg', 'png']))
                                                    <img src="{{ Storage::url($file) }}" class="w-full h-32 object-cover rounded border cursor-pointer" onclick="window.open(this.src, '_blank')">
                                                @else
                                                    <a href="{{ Storage::url($file) }}" target="_blank" class="block text-xs text-blue-600 hover:underline"> {{ basename($file) }}</a>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif

                                    <textarea wire:model="admin_notes" placeholder="Catatan (wajib jika tolak)..." class="w-full text-xs border border-gray-300 rounded p-1.5 mb-2" rows="2"></textarea>
                                    
                                    <div class="flex space-x-1">
                                        <button wire:click="verifyPayment({{ $payment->id }})" class="flex-1 bg-green-600 text-white text-xs py-1.5 rounded hover:bg-green-700">
                                            ✓ Terima
                                        </button>
                                        <button wire:click="rejectPayment({{ $payment->id }})" class="flex-1 bg-red-600 text-white text-xs py-1.5 rounded hover:bg-red-700">
                                            ✕ Tolak
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Tombol Print & Email -->
                <div class="mt-4 space-y-2">
                    <div class="flex space-x-2">
                        <button onclick="window.open('{{ route('admin.invoices.download', $order->invoice->id) }}')" 
                                class="flex-1 flex justify-center items-center px-3 py-2 text-xs font-medium text-white bg-gray-700 rounded-lg hover:bg-gray-800 transition">
                            ️ Print / PDF
                        </button>
                        <button wire:click="sendInvoiceEmail" 
                                class="flex-1 flex justify-center items-center px-3 py-2 text-xs font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition">
                            ✉️ Kirim Email
                        </button>
                    </div>

                    @if($order->invoice->payment_token)
                        @php $magicLink = route('payment.show', $order->invoice->payment_token); @endphp
                        <div class="flex space-x-2">
                            <input type="text" value="{{ $magicLink }}" readonly id="magic-link-input" class="flex-1 px-3 py-2 text-xs bg-white border border-gray-300 rounded-md text-gray-700">
                            <button onclick="navigator.clipboard.writeText('{{ $magicLink }}'); alert('Link tersalin!')" 
                                    class="px-3 py-2 text-xs font-medium text-white bg-green-600 rounded-md hover:bg-green-700">
                                📋 Copy
                            </button>
                            <a href="{{ $magicLink }}" target="_blank" class="px-3 py-2 text-xs font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                                 Buka
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>