<div class="space-y-6">
    @if (session()->has('finance_success'))
        <div class="p-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200">{{ session('finance_success') }}</div>
    @endif

    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            Manajemen Penagihan & Pembayaran
        </h3>

        @php
            $itemsFinal = $order->items->sum('final_subtotal');
            $liveShipping = $this->isPickup() ? 0 : (float) $shipping_cost;
            $liveGrand = round($itemsFinal + $liveShipping, 2);
        @endphp

        @if(!$order->invoice)
            <form wire:submit="generateInvoice" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-1 text-xs font-bold text-gray-700 uppercase">Metode Pembayaran</label>
                        <div class="flex space-x-2">
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" wire:model.live="payment_method" value="transfer" class="hidden peer">
                                <div class="text-center py-2 border-2 border-gray-200 rounded-lg peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:text-blue-700 text-sm font-medium transition">🏦 Transfer</div>
                            </label>
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" wire:model.live="payment_method" value="cash" class="hidden peer">
                                <div class="text-center py-2 border-2 border-gray-200 rounded-lg peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:text-green-700 text-sm font-medium transition">💵 Tunai</div>
                            </label>
                        </div>
                    </div>
                    <div>
                        <label class="block mb-1 text-xs font-bold text-gray-700 uppercase">Skema Pembayaran</label>
                        <div class="flex space-x-2">
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" wire:model.live="payment_scheme" value="full" class="hidden peer">
                                <div class="text-center py-2 border-2 border-gray-200 rounded-lg peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:text-blue-700 text-sm font-medium transition">Langsung Lunas</div>
                            </label>
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" wire:model.live="payment_scheme" value="installment" class="hidden peer">
                                <div class="text-center py-2 border-2 border-gray-200 rounded-lg peer-checked:border-orange-500 peer-checked:bg-orange-50 peer-checked:text-orange-700 text-sm font-medium transition">Cicilan / DP</div>
                            </label>
                        </div>
                    </div>
                </div>

                @if($this->isPickup())
                    <div class="p-3 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-600">🏪 <strong>Pickup di toko</strong> — tanpa ongkos kirim (Rp 0).</div>
                @else
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Ongkos Kirim (Rp) — {{ ucfirst($order->shipping_method) }}</label>
                        <input type="number" wire:model.live="shipping_cost" min="0" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        @error('shipping_cost') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                @endif

                @if($payment_method === 'cash')
                    <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                        <label class="block mb-1 text-sm font-bold text-green-800">Nominal Tunai Diterima (Rp)</label>
                        <input type="number" wire:model="cash_amount" class="bg-white border border-green-300 text-gray-900 text-lg font-bold rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5 mb-3">
                        @error('cash_amount') <p class="mb-2 text-xs text-red-600">{{ $message }}</p> @enderror
                        <label class="block mb-1 text-sm font-bold text-green-800">Upload Foto Kuitansi</label>
                        <input type="file" wire:model="cash_receipt" accept=".jpg,.jpeg,.png,.pdf" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-green-100 file:text-green-700 hover:file:bg-green-200">
                        @error('cash_receipt') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                @endif

                @if($payment_scheme === 'installment')
                    <div class="p-4 bg-orange-50 border border-orange-200 rounded-lg space-y-3">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block mb-1 text-sm font-bold text-orange-800">DP (% dari Total) <span class="text-red-500">*</span></label>
                                <input type="number" wire:model.live="dp_percentage" min="1" max="100" placeholder="cth: 30" class="bg-white border border-orange-300 text-gray-900 text-lg font-bold rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5">
                            </div>
                            <div>
                                <label class="block mb-1 text-sm font-bold text-orange-800">Jumlah Cicilan <span class="text-red-500">*</span></label>
                                <input type="number" wire:model.live="installment_count" min="1" max="12" placeholder="cth: 2" class="bg-white border border-orange-300 text-gray-900 text-lg font-bold rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5">
                            </div>
                        </div>
                        @error('dp_percentage') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                        @error('installment_count') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                        @php
                            $dpAmt = round($liveGrand * ($dp_percentage / 100), 2);
                            $sisa = round($liveGrand - $dpAmt, 2);
                            $per = $installment_count > 0 ? round($sisa / $installment_count, 2) : 0;
                        @endphp
                        <div class="bg-white p-3 rounded border border-orange-200 space-y-1 text-sm">
                            <div class="flex justify-between"><span class="text-gray-600">Grand Total:</span><span class="font-bold">Rp {{ number_format($liveGrand, 0, ',', '.') }}</span></div>
                            <div class="flex justify-between"><span class="text-gray-600">DP ({{ $dp_percentage }}%):</span><span class="font-bold text-orange-700">Rp {{ number_format($dpAmt, 0, ',', '.') }}</span></div>
                            <div class="flex justify-between"><span class="text-gray-600">Sisa:</span><span class="font-bold">Rp {{ number_format($sisa, 0, ',', '.') }}</span></div>
                            @if($installment_count > 0)
                                <div class="flex justify-between border-t pt-1"><span class="text-gray-600">Per Cicilan ({{ $installment_count }}x):</span><span class="font-bold text-blue-700">Rp {{ number_format($per, 0, ',', '.') }}</span></div>
                            @endif
                        </div>
                    </div>
                @endif

                <x-ui.button type="submit" variant="primary" class="w-full justify-center" wire:loading.attr="disabled" wire:target="generateInvoice">
                    <span wire:loading.remove wire:target="generateInvoice">Generate Invoice (Rp {{ number_format($liveGrand, 0, ',', '.') }})</span>
                    <span wire:loading wire:target="generateInvoice">Membuat Invoice...</span>
                </x-ui.button>
            </form>
        @else
            @if($this->isLocked())
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                    🔒 <strong>Invoice terkunci.</strong> Sudah ada pembayaran masuk (Rp {{ number_format($order->invoice->total_paid, 0, ',', '.') }}). Untuk mengubah invoice, tolak/batalkan pembayaran terlebih dahulu.
                </div>
            @endif

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex justify-between mb-2">
                    <span class="text-sm text-gray-600">Status Invoice:</span>
                    <span class="text-sm font-bold {{ $order->invoice->status == 'paid' ? 'text-green-600' : 'text-yellow-600' }}">{{ strtoupper($order->invoice->status) }}</span>
                </div>
                <div class="flex gap-2 my-2">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $order->invoice->payment_method == 'cash' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">{{ $order->invoice->payment_method == 'cash' ? '💵 Tunai' : '🏦 Transfer' }}</span>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $order->invoice->payment_scheme == 'installment' ? 'bg-orange-100 text-orange-700' : 'bg-gray-100 text-gray-700' }}">{{ $order->invoice->payment_scheme == 'installment' ? '📅 Cicilan' : '✅ Langsung Lunas' }}</span>
                </div>
                <div class="flex justify-between text-lg font-bold text-gray-900 mt-2 pt-3 border-t border-blue-200">
                    <span>Total Tagihan:</span><span>Rp {{ number_format($order->invoice->total_amount, 0, ',', '.') }}</span>
                </div>
                <p class="text-xs text-gray-500 mt-1">Jatuh tempo: {{ $order->invoice->due_date?->format('d M Y') }} • Harga sudah termasuk PPN.</p>

                @if($order->invoice->payment_scheme === 'installment' && $order->invoice->payment_schedule)
                    <div class="mt-4 p-3 bg-orange-50 border border-orange-200 rounded-lg">
                        <h4 class="text-xs font-bold text-orange-800 mb-2">📋 Skema Pembayaran</h4>
                        @if($order->invoice->installment_notes)<p class="text-xs text-gray-700 mb-3 italic">{{ $order->invoice->installment_notes }}</p>@endif
                        <div class="space-y-2">
                            @foreach($order->invoice->payment_schedule as $schedule)
                                @php $isPaid = $schedule['status'] === 'verified'; @endphp
                                <div class="flex justify-between items-center bg-white p-2 rounded border {{ $isPaid ? 'border-green-300' : 'border-yellow-300' }}">
                                    <div>
                                        <span class="text-xs font-bold {{ $isPaid ? 'text-green-700' : 'text-gray-700' }}">{{ $schedule['type'] }}</span>
                                        <span class="text-[10px] text-gray-500 ml-1">{{ $schedule['description'] }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-bold">Rp {{ number_format($schedule['amount'], 0, ',', '.') }}</span>
                                        <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full {{ $isPaid ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">{{ $isPaid ? '✓ Lunas' : '⏳ Menunggu' }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @php
                    $totalPaid = (float) ($order->invoice->total_paid ?? 0);
                    $totalAmount = (float) $order->invoice->total_amount;
                    $remaining = $totalAmount - $totalPaid;
                    $percentage = $totalAmount > 0 ? min(100, ($totalPaid / $totalAmount) * 100) : 0;
                @endphp
                <div class="mt-4 pt-4 border-t border-blue-200">
                    <div class="flex justify-between text-xs font-medium text-gray-600 mb-1">
                        <span>Progress Pembayaran</span>
                        <span>Rp {{ number_format($totalPaid, 0, ',', '.') }} / Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5 mb-2">
                        <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                    </div>
                    <div class="flex justify-between items-center">
                        @if($percentage < 100)
                            <p class="text-xs text-orange-600 font-semibold">⏳ Sisa Tagihan: Rp {{ number_format($remaining, 0, ',', '.') }}</p>
                            <span class="text-xs bg-orange-100 text-orange-700 px-2 py-1 rounded-full font-bold">{{ number_format($percentage, 1) }}% Lunas</span>
                        @else
                            <p class="text-xs text-green-600 font-semibold">✅ LUNAS</p>
                            <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full font-bold">100% Lunas</span>
                        @endif
                    </div>
                </div>

                @if($order->invoice->payments()->where('status', 'pending')->count() > 0)
                    <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                        <h4 class="text-xs font-bold text-yellow-800 mb-2">⏳ Menunggu Verifikasi</h4>
                        @foreach($order->invoice->payments()->where('status', 'pending')->get() as $payment)
                            <div class="bg-white border border-yellow-200 rounded p-2 mb-2">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <p class="text-sm font-bold text-gray-900">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                                        <p class="text-[10px] text-gray-500">{{ $payment->payment_method }} • {{ $payment->created_at->format('d M Y, H:i') }}</p>
                                    </div>
                                    <span class="bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full text-[10px] font-semibold">Pending</span>
                                </div>
                                @if($payment->proof_file)
                                    <div class="mb-2 space-y-1">
                                        @foreach((array) $payment->proof_file as $file)
                                            @php $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION)); @endphp
                                            @if(in_array($ext, ['jpg', 'jpeg', 'png']))
                                                <img src="{{ Storage::url($file) }}" class="w-full h-32 object-cover rounded border cursor-pointer" onclick="window.open(this.src, '_blank')">
                                            @else
                                                <a href="{{ Storage::url($file) }}" target="_blank" class="block text-xs text-blue-600 hover:underline">📎 {{ basename($file) }}</a>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                                <textarea wire:model="admin_notes" placeholder="Catatan (wajib jika tolak)..." class="w-full text-xs border border-gray-300 rounded p-1.5 mb-2" rows="2"></textarea>
                                <div class="flex space-x-1">
                                    <button wire:click="verifyPayment({{ $payment->id }})" class="flex-1 bg-green-600 text-white text-xs py-1.5 rounded hover:bg-green-700">✓ Terima</button>
                                    <button wire:click="rejectPayment({{ $payment->id }})" class="flex-1 bg-red-600 text-white text-xs py-1.5 rounded hover:bg-red-700">✕ Tolak</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if($totalPaid > 0)
                    <div class="mt-3 bg-white rounded-lg border border-blue-100 p-3">
                        <h4 class="text-xs font-bold text-gray-700 mb-2">✅ Riwayat Pembayaran Masuk</h4>
                        <div class="space-y-2">
                            @foreach($order->invoice->payments()->where('status', 'verified')->orderBy('verified_at', 'desc')->get() as $payment)
                                <div class="flex justify-between items-center text-xs border-b border-gray-100 pb-2 last:border-0 last:pb-0">
                                    <div>
                                        <p class="font-medium text-gray-900">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                                        <p class="text-[10px] text-gray-500">{{ $payment->payment_method }} • {{ $payment->verified_at?->format('d M Y, H:i') }}</p>
                                    </div>
                                    <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-[10px] font-semibold">✓ Verified</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="mt-4 space-y-2">
                    <div class="flex space-x-2">
                        <button onclick="window.open('{{ route('admin.invoices.download', $order->invoice->id) }}')" class="flex-1 flex justify-center items-center px-3 py-2 text-xs font-medium text-white bg-gray-700 rounded-lg hover:bg-gray-800 transition">🖨️ Print / PDF</button>
                        <button wire:click="sendInvoiceEmail" class="flex-1 flex justify-center items-center px-3 py-2 text-xs font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition">✉️ Kirim Email</button>
                    </div>
                    @if($order->invoice->payment_token)
                        @php $magicLink = route('payment.show', $order->invoice->payment_token); @endphp
                        <div class="flex space-x-2">
                            <input type="text" value="{{ $magicLink }}" readonly id="magic-link-input" class="flex-1 px-3 py-2 text-xs bg-white border border-gray-300 rounded-md text-gray-700">
                            <button onclick="copyMagicLink()" class="px-3 py-2 text-xs font-medium text-white bg-green-600 rounded-md hover:bg-green-700">📋 Copy</button>
                            <a href="{{ $magicLink }}" target="_blank" class="px-3 py-2 text-xs font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">Buka</a>
                        </div>
                        <script>
                            function copyMagicLink() {
                                var t = document.getElementById('magic-link-input');
                                t.select(); t.setSelectionRange(0, 99999);
                                document.execCommand('copy');
                                alert('✅ Link tersalin!');
                            }
                        </script>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>