<div class="max-w-7xl mx-auto px-4 py-8">
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">📐 Manajemen Ukuran</h1>
            <p class="text-sm text-gray-500 mt-1">Daftar ukuran standar dan penyesuaian harga</p>
        </div>
        <button wire:click="openForm" 
                class="px-5 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition flex items-center gap-2 shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Ukuran
        </button>
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="mb-4 p-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    {{-- Search Filter --}}
    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 mb-6">
        <input type="text" wire:model.live.debounce.300ms="search" 
               placeholder="🔍 Cari nama ukuran..."
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
    </div>

    {{-- Sizes Table --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Nama Ukuran</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Slug</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Dimensi (cm)</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">Luas (m²)</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Status</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($sizes as $size)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $size->name }}</td>
                            <td class="px-4 py-3 text-gray-600 font-mono text-xs">{{ $size->slug }}</td>
                            <td class="px-4 py-3 text-center text-gray-600">
                                @if($size->width_cm && $size->height_cm)
                                    {{ $size->width_cm }} x {{ $size->height_cm }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right font-medium text-gray-900">{{ number_format($size->area_m2, 2) }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full {{ $size->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $size->is_active ? 'AKTIF' : 'NONAKTIF' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-2">
                                    <button wire:click="openForm({{ $size->id }})"
                                            class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition">
                                        ✏️ Edit
                                    </button>
                                    <button wire:click="delete({{ $size->id }})"
                                            wire:confirm="Yakin hapus ukuran {{ $size->name }}?"
                                            class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-red-100 text-red-700 hover:bg-red-200 transition">
                                        🗑️
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-gray-400">
                                <p class="text-lg">📭 Belum ada ukuran terdaftar</p>
                                <p class="text-sm mt-1">Klik "Tambah Ukuran" untuk memulai</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL POPUP CARD (Blur Background Consistent) --}}
    @if($showForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg relative animate-fade-in-up">
                
                {{-- Modal Header --}}
                <div class="flex justify-between items-center p-6 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900">
                        {{ $editingId ? '✏️ Edit Ukuran' : '➕ Tambah Ukuran Baru' }}
                    </h3>
                    <button wire:click="$set('showForm', false)" class="text-gray-400 hover:text-red-500 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Modal Body Form --}}
                <form wire:submit="save" class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Ukuran *</label>
                        <input type="text" wire:model="name" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Contoh: A3, 50x70cm">
                        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Slug (URL)</label>
                        <input type="text" wire:model="slug" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600"
                               placeholder="auto-generated" readonly>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Lebar (cm)</label>
                            <input type="number" step="0.1" wire:model="width_cm" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tinggi (cm)</label>
                            <input type="number" step="0.1" wire:model="height_cm" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Luas (m²) *</label>
                            <input type="number" step="0.01" wire:model="area_m2" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            @error('area_m2') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Penyesuaian Harga (Rp)</label>
                            <input type="number" wire:model="price_adjustment" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="0">
                        </div>
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <input type="checkbox" wire:model="is_active" id="sizeActive" 
                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="sizeActive" class="text-sm text-gray-700">Aktifkan Ukuran</label>
                    </div>

                    {{-- Modal Footer Actions --}}
                    <div class="flex gap-3 pt-4 border-t border-gray-100">
                        <button type="submit" 
                                class="flex-1 px-4 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition shadow-sm">
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