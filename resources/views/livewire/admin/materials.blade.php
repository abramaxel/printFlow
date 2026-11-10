<div class="max-w-7xl mx-auto px-4 py-8">
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">📦 Manajemen Bahan Baku</h1>
            <p class="text-sm text-gray-500 mt-1">Pantau stok, ROP, dan buat Purchase Request</p>
        </div>
        <button wire:click="openForm" 
                class="px-5 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Bahan
        </button>
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="mb-4 p-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200">
            {{ session('success') }}
        </div>
    @endif
    @if (session()->has('warning'))
        <div class="mb-4 p-4 text-sm text-yellow-800 rounded-lg bg-yellow-50 border border-yellow-200">
            {{ session('warning') }}
        </div>
    @endif

    {{-- MRP Summary Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
            <p class="text-xs font-medium text-gray-500">Total Nilai Stok</p>
            <p class="text-xl font-bold text-gray-900 mt-1">Rp {{ number_format($totalValue ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="bg-red-50 p-4 rounded-lg border border-red-200">
            <p class="text-xs font-medium text-red-600">🔴 Kritis / Habis</p>
            <p class="text-xl font-bold text-red-700 mt-1">{{ $criticalCount ?? 0 }}</p>
        </div>
        <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
            <p class="text-xs font-medium text-yellow-600">🟡 Perlu Reorder</p>
            <p class="text-xl font-bold text-yellow-700 mt-1">{{ $warningCount ?? 0 }}</p>
        </div>
        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
            <p class="text-xs font-medium text-green-600">🟢 Aman</p>
            <p class="text-xl font-bold text-green-700 mt-1">{{ $safeCount ?? 0 }}</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <input type="text" wire:model.live.debounce.300ms="search" 
                       placeholder="🔍 Cari nama bahan..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <select wire:model.live="statusFilter" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Status</option>
                    <option value="out_of_stock">🔴 Habis Stok</option>
                    <option value="critical">🔴 Kritis</option>
                    <option value="warning">🟡 Perlu Reorder</option>
                    <option value="safe">🟢 Aman</option>
                </select>
            </div>
            <div>
                <select wire:model.live="categoryFilter" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Kategori</option>
                    @foreach($categories ?? [] as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Materials Table --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Bahan</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Kategori</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Stok Fisik</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Reserved</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Net Available</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Avg/Hari</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Status</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($materials ?? [] as $material)
                        @php
                            // SAFE FALLBACK: jika mrp null, gunakan default array
                            $mrp = $material->mrp ?? ['status'=>'safe','net_available'=>0,'avg_daily_usage'=>0,'needs_reorder'=>false,'suggested_qty'=>0,'urgency'=>'normal'];
                            
                            $statusColor = match($mrp['status'] ?? 'safe') {
                                'out_of_stock' => 'bg-red-100 text-red-800',
                                'critical' => 'bg-red-100 text-red-800',
                                'warning' => 'bg-yellow-100 text-yellow-800',
                                default => 'bg-green-100 text-green-800',
                            };
                            $statusLabel = match($mrp['status'] ?? 'safe') {
                                'out_of_stock' => '🔴 HABIS',
                                'critical' => '🔴 KRITIS',
                                'warning' => '🟡 REORDER',
                                default => '🟢 AMAN',
                            };
                        @endphp
                        <tr class="hover:bg-gray-50 transition {{ ($mrp['urgency'] ?? 'normal') === 'critical' ? 'bg-red-50/50' : '' }}">
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900">{{ $material->name }}</div>
                                <div class="text-xs text-gray-500">{{ $material->unit }} • Rp {{ number_format($material->price ?? 0, 0, ',', '.') }}</div>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $material->category->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-center font-medium">{{ number_format($material->stock ?? 0, 1) }}</td>
                            <td class="px-4 py-3 text-center text-orange-600">{{ number_format($material->stock_reserved ?? 0, 1) }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="font-bold {{ ($mrp['net_available'] ?? 0) <= 0 ? 'text-red-600' : 'text-gray-900' }}">
                                    {{ number_format($mrp['net_available'] ?? 0, 1) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center text-gray-500">{{ number_format($mrp['avg_daily_usage'] ?? 0, 2) }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full {{ $statusColor }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-2">
                                    @if($mrp['needs_reorder'] ?? false)
                                        <button wire:click="createPurchaseRequest({{ $material->id }})"
                                                wire:confirm="Buat Purchase Request untuk {{ $material->name }}?"
                                                class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition"
                                                title="Saran: {{ number_format($mrp['suggested_qty'] ?? 0, 0) }} {{ $material->unit }}">
                                            🛒 Buat PR
                                        </button>
                                    @endif
                                    <button wire:click="openForm({{ $material->id }})"
                                            class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition">
                                        ✏️ Edit
                                    </button>
                                    <button wire:click="delete({{ $material->id }})"
                                            wire:confirm="Yakin hapus {{ $material->name }}?"
                                            class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-red-100 text-red-700 hover:bg-red-200 transition">
                                        🗑️
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center text-gray-400">
                                <p class="text-lg">📭 Tidak ada bahan baku ditemukan</p>
                                <p class="text-sm mt-1">Klik "Tambah Bahan" untuk memulai</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Form Modal --}}
    @if($showForm)
         <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto relative animate-fade-in-up">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center sticky top-0 bg-white z-10">
                    <h3 class="text-lg font-bold text-gray-900">{{ $editingId ? '✏️ Edit Bahan' : '➕ Tambah Bahan Baru' }}</h3>
                    <button wire:click="$set('showForm', false)" class="text-gray-400 hover:text-red-500 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit="save" class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Bahan *</label>
                            <input type="text" wire:model="name" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Contoh: Flexy Korea 440gr">
                            @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kategori *</label>
                            <select wire:model="category_id" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Pilih Kategori</option>
                                @foreach($categories ?? [] as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Satuan *</label>
                            <select wire:model="unit" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                <option value="m2">m² (Meter Persegi)</option>
                                <option value="liter">Liter</option>
                                <option value="pcs">Pcs</option>
                                <option value="rim">Rim</option>
                                <option value="kg">Kg</option>
                                <option value="roll">Roll</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Harga per Satuan (Rp) *</label>
                            <input type="number" wire:model="price" step="0.01"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            @error('price') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Stok Saat Ini *</label>
                            <input type="number" wire:model="stock" step="0.01"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Threshold Warning *</label>
                            <input type="number" wire:model="threshold" step="0.01"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Minimum Stock (ROP) *</label>
                            <input type="number" wire:model="minimum_stock" step="0.01"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Maximum Stock</label>
                            <input type="number" wire:model="maximum_stock" step="0.01"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Lead Time (Hari) *</label>
                            <input type="number" wire:model="lead_time_days"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Shelf Life (Hari)</label>
                            <input type="number" wire:model="shelf_life_days"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Untuk tinta/bahan kimia">
                        </div>

                        <div class="col-span-2 flex items-center gap-3">
                            <input type="checkbox" wire:model="is_batch_tracked" id="batchTrack"
                                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <label for="batchTrack" class="text-sm text-gray-700">
                                📦 Track per Batch (untuk FIFO & kadaluarsa)
                            </label>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-4 border-t border-gray-200">
                        <button type="submit" 
                                class="flex-1 px-4 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                            💾 Simpan
                        </button>
                        <button type="button" wire:click="$set('showForm', false)"
                                class="px-4 py-2.5 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>