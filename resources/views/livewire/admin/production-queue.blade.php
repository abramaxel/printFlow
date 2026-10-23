<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Header & Summary Cards -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-800">🏭 Antrian Produksi</h2>
        <p class="text-sm text-gray-500 mb-4">Kelola pengambilan bahan dan antrean cetak</p>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-blue-500">
                <p class="text-xs text-gray-500 uppercase font-bold">Total Antrian</p>
                <p class="text-2xl font-bold text-gray-800">{{ $totalOrders }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-green-500">
                <p class="text-xs text-gray-500 uppercase font-bold">Siap Cetak (Stok Aman)</p>
                <p class="text-2xl font-bold text-green-600">{{ $readyOrders }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-red-500">
                <p class="text-xs text-gray-500 uppercase font-bold">Tertahan (Stok Kurang)</p>
                <p class="text-2xl font-bold text-red-600">{{ $shortageOrders }}</p>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="p-4 mb-6 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <!-- Search & Filter Bar -->
    <div class="flex flex-col md:flex-row gap-4 mb-6 bg-white p-4 rounded-lg shadow-sm border border-gray-200">
        <div class="flex-1 relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </span>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari No. Invoice atau Nama Pelanggan..." class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5">
        </div>
        <select wire:model.live="stockFilter" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full md:w-48 p-2.5">
            <option value="">Semua Status Stok</option>
            <option value="ready">✅ Stok Aman</option>
            <option value="shortage">⚠️ Stok Kurang</option>
        </select>
    </div>

    <!-- List Orders -->
    @forelse($orders as $order)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 overflow-hidden transition-all hover:shadow-md">
            <!-- Header Card Pesanan -->
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex flex-col md:flex-row justify-between md:items-center gap-2">
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-lg font-bold text-gray-900">{{ $order->invoice_number }}</h3>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-600">Pelanggan: <span class="font-medium">{{ $order->user->name }}</span> • {{ $order->created_at->format('d M Y, H:i') }}</p>
                </div>
                @if($order->notes)
                    <div class="text-xs bg-yellow-100 text-yellow-800 px-3 py-1.5 rounded-lg border border-yellow-200 max-w-sm">
                        <strong>Catatan Admin:</strong> {{ $order->notes }}
                    </div>
                @endif
            </div>

            <!-- Tabel Kebutuhan Bahan -->
            <div class="p-6">
                <h4 class="text-sm font-bold text-gray-700 uppercase mb-3 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    Rincian Material & Desain
                </h4>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-600">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                            <tr>
                                <th class="px-4 py-3">Item & Desain</th>
                                <th class="px-4 py-3 text-center">Butuh</th>
                                <th class="px-4 py-3 text-center">Stok Gudang</th>
                                <th class="px-4 py-3 text-center font-bold">Status Stok</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                @php
                                    $required = $item->area_m2;
                                    $totalStock = $item->material->stock ?? 0;
                                    $reserved = $item->material->stock_reserved ?? 0;
                                    $available = max(0, $totalStock - $reserved);
                                    $isEnough = $available >= $required;
                                @endphp
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="px-4 py-4">
                                        <div class="font-semibold text-gray-900">{{ $item->material->name ?? 'Material Deleted' }}</div>
                                        <div class="text-xs text-gray-500 mb-2">{{ $item->service->name ?? '-' }} • {{ $item->size->name ?? '-' }} (Qty: {{ $item->quantity }})</div>
                                        
                                        <!-- File Desain -->
                                        @if($item->design_file)
                                            <div class="flex flex-wrap gap-2 mt-1">
                                                @foreach((array)$item->design_file as $file)
                                                    <a href="{{ Storage::url($file) }}" target="_blank" class="inline-flex items-center text-xs text-blue-600 hover:underline bg-blue-50 px-2 py-1 rounded border border-blue-200">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                        Lihat Desain
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif

                                        <!-- Catatan Item -->
                                        @if($item->notes)
                                            <div class="mt-2 p-2 bg-yellow-50 border-l-4 border-yellow-400 text-xs text-yellow-800 italic">
                                                💬 {{ $item->notes }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-center font-bold text-blue-700">
                                        {{ number_format($required, 2) }} m²
                                    </td>
                                    <td class="px-4 py-4 text-center text-gray-600">
                                        <div class="text-xs">Total: {{ number_format($totalStock, 2) }}</div>
                                        <div class="text-xs text-orange-600">Reserve: {{ number_format($reserved, 2) }}</div>
                                        <div class="font-bold mt-1 {{ $isEnough ? 'text-green-600' : 'text-red-600' }}">
                                            Sisa: {{ number_format($available, 2) }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        @if($isEnough)
                                            <span class="inline-flex items-center text-xs bg-green-100 text-green-800 px-2.5 py-1 rounded-full font-semibold">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                                Aman
                                            </span>
                                        @else
                                            <span class="inline-flex items-center text-xs bg-red-100 text-red-800 px-2.5 py-1 rounded-full font-semibold animate-pulse">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                                Kurang!
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Tombol Aksi Produksi -->
                <div class="mt-6 flex flex-col sm:flex-row justify-end gap-3 border-t border-gray-100 pt-4">
                    @php
                        // Cek apakah semua item di order ini stoknya aman
                        $allItemsReady = $order->items->every(function($item) {
                            $available = max(0, ($item->material->stock ?? 0) - ($item->material->stock_reserved ?? 0));
                            return $available >= $item->area_m2;
                        });
                    @endphp

                    @if($order->status === 'processing')
                        <button 
                            wire:click="confirmMaterialTakeout({{ $order->id }})"
                            @if(!$allItemsReady) disabled title="Stok bahan tidak mencukupi!" @endif
                            wire:confirm="Apakah Anda yakin bahan fisik sudah diambil dari gudang? Stok akan permanen berkurang dan pesanan masuk ke tahap Cetak."
                            class="flex items-center justify-center px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition shadow-sm disabled:bg-gray-300 disabled:cursor-not-allowed"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span wire:loading.remove wire:target="confirmMaterialTakeout({{ $order->id }})">
                                {{ $allItemsReady ? 'Konfirmasi Ambil Bahan & Cetak' : 'Stok Kurang (Tahan)' }}
                            </span>
                            <span wire:loading wire:target="confirmMaterialTakeout({{ $order->id }})">Memproses...</span>
                        </button>
                    @elseif($order->status === 'printing')
                         <button 
                            wire:click="moveToQC({{ $order->id }})"
                            wire:confirm="Apakah cetak sudah selesai? Pindahkan ke Quality Control (QC)?"
                            class="flex items-center justify-center px-6 py-3 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 transition shadow-sm"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span wire:loading.remove wire:target="moveToQC({{ $order->id }})">Selesai Cetak, Lanjut ke QC</span>
                            <span wire:loading wire:target="moveToQC({{ $order->id }})">Memproses...</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="text-center py-16 bg-white rounded-xl border border-dashed border-gray-300">
            <svg class="w-20 h-20 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            <p class="text-gray-500 font-medium text-lg">Antrian Kosong 🎉</p>
            <p class="text-gray-400 text-sm mt-1">Tidak ada pesanan yang sedang dalam tahap produksi saat ini.</p>
        </div>
    @endforelse
</div>