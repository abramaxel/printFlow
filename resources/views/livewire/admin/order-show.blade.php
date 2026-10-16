<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- ========================================== -->
    <!-- HEADER: Invoice Info -->
    <!-- ========================================== -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Detail Pesanan</h2>
            <p class="text-sm text-gray-500 mt-1">
                Invoice: <span class="font-mono font-bold text-blue-600">{{ $order->invoice_number }}</span>
                <span class="ml-3 px-2 py-1 text-xs font-semibold rounded-full 
                    {{ $order->status == 'completed' ? 'bg-green-100 text-green-800' : 
                       ($order->status == 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
                    {{ strtoupper(str_replace('_', ' ', $order->status)) }}
                </span>
            </p>
        </div>
        <a href="{{ route('admin.orders') }}" class="text-sm text-gray-600 hover:text-blue-600 flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Daftar
        </a>
    </div>

    @if (session()->has('success'))
        <div class="p-4 mb-6 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200">{{ session('success') }}</div>
    @endif

    <!-- ========================================== -->
    <!-- SECTION 1: Info Pelanggan + Ringkasan Invoice (2 Kolom) -->
    <!-- ========================================== -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        
        <!-- Info Pelanggan -->
        <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                Informasi Pelanggan
            </h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-500 text-xs">Nama Pelanggan</p>
                    <p class="font-medium text-gray-900">{{ $order->user->name }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs">Email</p>
                    <p class="font-medium text-gray-900">{{ $order->user->email }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs">Tanggal Order</p>
                    <p class="font-medium text-gray-900">{{ $order->created_at->format('d M Y, H:i') }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs">Catatan Order</p>
                    <p class="font-medium text-gray-900">{{ $order->notes ?: '-' }}</p>
                </div>
            </div>

            <!-- Form Input No WA -->
            <div class="mt-4 pt-4 border-t border-gray-100">
                <label class="block text-xs font-medium text-gray-700 mb-1">No. WhatsApp (Untuk Kirim Link)</label>
                <div class="flex space-x-2">
                    <input type="text" wire:model="customer_phone" placeholder="0812xxxxxxx" class="flex-1 px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                    <x-ui.button variant="primary" size="sm" wire:click="updateCustomerPhone">Simpan</x-ui.button>
                </div>
                @error('customer_phone') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Ringkasan Invoice -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Ringkasan Tagihan
            </h3>

            @if($order->invoice)
                <!-- TAMPILAN JIKA INVOICE SUDAH ADA -->
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Subtotal Item:</span>
                        <span class="font-medium">Rp {{ number_format($order->invoice->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Ongkir:</span>
                        <span class="font-medium">Rp {{ number_format($order->invoice->shipping_cost, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Pajak:</span>
                        <span class="font-medium">Rp {{ number_format($order->invoice->tax, 0, ',', '.') }}</span>
                    </div>
                    <div class="pt-3 border-t border-gray-200 flex justify-between text-lg font-bold text-gray-900">
                        <span>Total:</span>
                        <span class="text-blue-600">Rp {{ number_format($order->invoice->total_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center pt-2">
                        <span class="text-xs text-gray-500">Status:</span>
                        <span class="px-2 py-1 text-xs font-bold rounded-full 
                            {{ $order->invoice->status == 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ strtoupper($order->invoice->status) }}
                        </span>
                    </div>
                </div>
            @else
                <!-- TAMPILAN JIKA INVOICE BELUM ADA (ESTIMASI) -->
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Subtotal Item:</span>
                        <span class="font-medium">Rp {{ number_format($order->items->sum('final_subtotal'), 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Ongkir:</span>
                        <span class="font-medium text-gray-400 italic">Belum di-set</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Pajak:</span>
                        <span class="font-medium text-gray-400 italic">Belum di-set</span>
                    </div>
                    <div class="pt-3 border-t border-gray-200 flex justify-between text-lg font-bold text-gray-900">
                        <span>Estimasi Total:</span>
                        <span class="text-blue-600">Rp {{ number_format($order->items->sum('final_subtotal'), 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center pt-2">
                        <span class="text-xs text-gray-500">Status:</span>
                        <span class="px-2 py-1 text-xs font-bold rounded-full bg-gray-100 text-gray-600">
                            BELUM ADA INVOICE
                        </span>
                    </div>
                    <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="text-xs text-blue-800">
                            💡 Silakan scroll ke bawah untuk membuat invoice dengan mengatur ongkir dan pajak.
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- ========================================== -->
    <!-- SECTION 2: Item Pesanan (Full Width) -->
    <!-- ========================================== -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
            Detail Item Pesanan
        </h3>
        
        @foreach($order->items as $item)
            <div class="border border-gray-200 rounded-lg p-5 bg-gray-50">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h4 class="font-bold text-gray-900 text-base">{{ $item->service->name }}</h4>
                        <p class="text-sm text-gray-600 mt-1">{{ $item->material->name }} | {{ $item->size->name }}</p>
                    </div>
                    <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-3 py-1 rounded-full">Qty: {{ $item->quantity }}</span>
                </div>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm mb-4">
                    <div class="bg-white p-3 rounded border border-gray-200">
                        <p class="text-gray-500 text-xs mb-1">Luas Total</p>
                        <p class="font-semibold text-gray-900">{{ number_format($item->area_m2, 2) }} m²</p>
                    </div>
                    <div class="bg-white p-3 rounded border border-gray-200">
                        <p class="text-gray-500 text-xs mb-1">Harga Master</p>
                        <p class="font-semibold text-gray-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-white p-3 rounded border border-gray-200">
                        <p class="text-gray-500 text-xs mb-1">Diskon</p>
                        <p class="font-semibold text-red-600">- Rp {{ number_format($item->discount, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-white p-3 rounded border border-green-200 bg-green-50">
                        <p class="text-gray-500 text-xs mb-1">Harga Final (Deal)</p>
                        <p class="font-bold text-green-700">Rp {{ number_format($item->final_subtotal, 0, ',', '.') }}</p>
                    </div>
                </div>

                 @if(!empty($item->design_file))
                            <div class="mt-3 pt-3 border-t border-gray-200">
                                <p class="text-xs text-gray-500 mb-2">File Desain:</p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach((array) $item->design_file as $file)
                                        <a href="{{ Storage::url($file) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-100 rounded-lg hover:bg-blue-200 transition">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                            {{ basename($file) }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
            </div>
        @endforeach
    </div>

    <!-- ========================================== -->
    <!-- SECTION 3: Manajemen Invoice & Pembayaran (Full Width) -->
    <!-- ========================================== -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            Invoice & Pembayaran
        </h3>
        @livewire('admin.order-finance', ['order' => $order])
    </div>

    <!-- ========================================== -->
    <!-- SECTION 4: Update Status Produksi + Timeline (2 Kolom) -->
    <!-- ========================================== -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Update Status (1/3) -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                Update Status Produksi
            </h3>
            
            <form wire:submit="updateStatus">
                <div class="mb-4">
                    <label class="block mb-2 text-sm font-medium text-gray-900">Status Baru</label>
                    <select wire:model="new_status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        @foreach($available_statuses as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block mb-2 text-sm font-medium text-gray-900">Catatan (Opsional)</label>
                    <textarea wire:model="status_notes" rows="3" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Contoh: Bahan sudah ready, mulai cetak..."></textarea>
                </div>

                <x-ui.button type="submit" variant="primary" class="w-full justify-center">
                    Simpan Perubahan
                </x-ui.button>
            </form>
        </div>

        <!-- Timeline Riwayat (2/3) -->
        <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Riwayat Perubahan (Order Track)
            </h3>
            
            <ol class="relative border-l-2 border-gray-200 ml-3">                  
                @forelse($order->histories as $history)
                    <li class="mb-6 ml-6">
                        <div class="absolute w-4 h-4 bg-blue-600 rounded-full -left-2.5 border-4 border-white shadow"></div>
                        <time class="mb-1 text-xs font-normal text-gray-400">{{ $history->created_at->format('d M Y, H:i') }} ({{ $history->created_at->diffForHumans() }})</time>
                        <h3 class="text-sm font-semibold text-gray-900">
                            {{ str_replace('_', ' ', ucfirst($history->old_status)) }} 
                            <span class="text-gray-400 mx-2">➔</span> 
                            <span class="text-blue-600">{{ str_replace('_', ' ', ucfirst($history->new_status)) }}</span>
                        </h3>
                        @if($history->notes)
                            <p class="mt-1 text-xs text-gray-600 bg-gray-50 p-2 rounded italic">"{{ $history->notes }}"</p>
                        @endif
                        <p class="text-xs text-gray-400 mt-1">Oleh: {{ $history->user->name }}</p>
                    </li>
                @empty
                    <li class="text-sm text-gray-500 italic py-4 text-center">Belum ada riwayat perubahan status.</li>
                @endforelse
            </ol>
        </div>

    </div>
</div>