<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">🧾 Kelola BOM Layanan</h1>
            <p class="text-sm text-gray-500 mt-1">Definisikan resep bahan & scrap allowance untuk setiap layanan</p>
        </div>
        <button wire:click="openForm" {{ !$selectedServiceId ? 'disabled' : '' }}
                class="px-5 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition flex items-center gap-2 shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah / Edit BOM
        </button>
    </div>

    @if (session()->has('success'))
        <div class="mb-4 p-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200">{{ session('success') }}</div>
    @endif

    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Layanan</label>
        <select wire:model.live="selectedServiceId" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 bg-white">
            <option value="">-- Pilih Layanan untuk melihat BOM --</option>
            @foreach($services as $service)
                <option value="{{ $service->id }}">{{ $service->name }}</option>
            @endforeach
        </select>
    </div>

    @if($selectedServiceId)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-4 border-b border-gray-200 bg-gray-50">
                <h3 class="font-bold text-gray-800">Daftar Versi BOM</h3>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Versi</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Komposisi Bahan</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Catatan</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Status</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($boms as $bom)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 font-mono font-bold text-blue-600">v{{ $bom->version }}</td>
                            <td class="px-4 py-3">
                                <ul class="list-disc list-inside text-xs text-gray-600 space-y-1">
                                    @foreach($bom->items as $item)
                                        <li>
                                            <span class="font-medium text-gray-800">{{ $item->material->name }}</span>: 
                                            {{ $item->quantity_per_unit }} {{ $item->unit }} 
                                            @if($item->scrap_percentage > 0)
                                                <span class="text-orange-600">(Scrap: {{ $item->scrap_percentage }}%)</span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="px-4 py-3 text-gray-600 text-xs max-w-xs truncate">{{ $bom->notes ?: '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($bom->is_active)
                                    <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-green-100 text-green-800">AKTIF (MRP)</span>
                                @else
                                    <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-gray-100 text-gray-600">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button wire:click="toggleActive({{ $bom->id }})" 
                                            class="px-3 py-1.5 text-xs font-semibold rounded-lg {{ $bom->is_active ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700' }} hover:opacity-80 transition">
                                        {{ $bom->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                    <button wire:click="openForm({{ $bom->id }})" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition">✏️ Edit</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-gray-400">
                                <p class="text-lg">📭 Belum ada BOM untuk layanan ini</p>
                                <p class="text-sm mt-1">Klik "Tambah / Edit BOM" untuk membuat resep bahan</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

    {{-- MODAL FORM BOM --}}
    @if($showForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto relative animate-fade-in-up">
                <div class="flex justify-between items-center p-6 border-b border-gray-100 sticky top-0 bg-white z-10">
                    <h3 class="text-lg font-bold text-gray-900">{{ $editingBomId ? '✏️ Edit BOM' : '➕ Tambah BOM Baru' }}</h3>
                    <button wire:click="$set('showForm', false)" class="text-gray-400 hover:text-red-500 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit="save" class="p-6 space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Layanan *</label>
                            <select wire:model="selectedServiceId" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100" disabled>
                                <option value="{{ $selectedServiceId }}">{{ $services->firstWhere('id', $selectedServiceId)->name ?? '-' }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Versi BOM *</label>
                            <input type="number" wire:model="bomVersion" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            @error('bomVersion') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <label class="block text-sm font-bold text-gray-700">Komposisi Bahan *</label>
                            <button type="button" wire:click="addItemRow" class="text-xs font-semibold text-blue-600 hover:text-blue-800">+ Tambah Bahan</button>
                        </div>
                        
                        @foreach($bomItems as $i => $item)
                            <div class="grid grid-cols-12 gap-3 mb-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="col-span-4">
                                    <label class="block text-xs text-gray-500 mb-1">Material</label>
                                    <select wire:model="bomItems.{{ $i }}.material_id" class="w-full px-2 py-2 border border-gray-300 rounded-lg text-sm bg-white">
                                        <option value="">-- Pilih --</option>
                                        @foreach($materials as $mat)
                                            <option value="{{ $mat->id }}">{{ $mat->name }}</option>
                                        @endforeach
                                    </select>
                                    @error("bomItems.{$i}.material_id") <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-xs text-gray-500 mb-1">Qty / Unit</label>
                                    <input type="number" step="0.01" wire:model="bomItems.{{ $i }}.quantity_per_unit" class="w-full px-2 py-2 border border-gray-300 rounded-lg text-sm">
                                    @error("bomItems.{$i}.quantity_per_unit") <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-xs text-gray-500 mb-1">Satuan</label>
                                    <select wire:model="bomItems.{{ $i }}.unit" class="w-full px-2 py-2 border border-gray-300 rounded-lg text-sm bg-white">
                                        <option value="m2">m²</option>
                                        <option value="liter">Liter</option>
                                        <option value="pcs">Pcs</option>
                                        <option value="kg">Kg</option>
                                    </select>
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-xs text-gray-500 mb-1">Scrap (%)</label>
                                    <input type="number" step="0.1" wire:model="bomItems.{{ $i }}.scrap_percentage" class="w-full px-2 py-2 border border-gray-300 rounded-lg text-sm">
                                    @error("bomItems.{$i}.scrap_percentage") <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-span-2 flex items-end justify-center pb-1">
                                    <button type="button" wire:click="removeItemRow({{ $i }})" class="px-2 py-2 text-red-500 hover:bg-red-50 rounded-lg transition">🗑️</button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Internal</label>
                        <textarea wire:model="bomNotes" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Misal: Versi ini menggunakan tinta jenis baru"></textarea>
                    </div>

                    <div class="flex gap-3 pt-4 border-t border-gray-100">
                        <button type="submit" class="flex-1 px-4 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition shadow-sm">💾 Simpan BOM</button>
                        <button type="button" wire:click="$set('showForm', false)" class="px-4 py-2.5 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>