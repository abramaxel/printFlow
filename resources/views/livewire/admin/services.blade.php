<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">📋 Kelola Layanan</h1>
            <p class="text-sm text-gray-500 mt-1">Daftar layanan cetak dan harga dasar</p>
        </div>
        <button wire:click="openForm" class="px-5 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition flex items-center gap-2 shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Layanan
        </button>
    </div>

    @if (session()->has('success'))
        <div class="mb-4 p-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200">{{ session('success') }}</div>
    @endif

    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 mb-6">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="🔍 Cari nama layanan..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Nama Layanan</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Slug</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">Harga (/m²)</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Status</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($services as $service)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $service->name }}</td>
                            <td class="px-4 py-3 text-gray-600 font-mono text-xs">{{ $service->slug }}</td>
                            <td class="px-4 py-3 text-right font-medium text-gray-900">Rp {{ number_format($service->price_per_m2 ?? 0, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full {{ $service->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $service->is_active ? 'AKTIF' : 'NONAKTIF' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-2">
                                    <button wire:click="openForm({{ $service->id }})" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition">✏️ Edit</button>
                                    <button wire:click="delete({{ $service->id }})" wire:confirm="Yakin hapus {{ $service->name }}?" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-red-100 text-red-700 hover:bg-red-200 transition">🗑️</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-12 text-center text-gray-400">📭 Belum ada layanan terdaftar</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL POPUP CARD (Blur Background Consistent) --}}
    @if($showForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg relative animate-fade-in-up">
                <div class="flex justify-between items-center p-6 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900">{{ $editingId ? '✏️ Edit Layanan' : '➕ Tambah Layanan Baru' }}</h3>
                    <button wire:click="$set('showForm', false)" class="text-gray-400 hover:text-red-500 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form wire:submit="save" class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Layanan *</label>
                        <input type="text" wire:model="name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Contoh: Cetak Banner Outdoor">
                        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Slug (URL)</label>
                        <input type="text" wire:model="slug" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600" readonly>
                        <p class="text-xs text-gray-500 mt-1">Slug otomatis dari nama layanan.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Harga per m² (Rp) *</label>
                        <input type="number" wire:model="price_per_m2" step="1000" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="0">
                        @error('price_per_m2') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                        <textarea wire:model="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Spesifikasi teknis, bahan, dll"></textarea>
                    </div>
                    <div class="flex items-center gap-3 pt-2">
                        <input type="checkbox" wire:model="is_active" id="svcActive" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="svcActive" class="text-sm text-gray-700">Aktifkan Layanan (tampilkan di portal customer)</label>
                    </div>
                    <div class="flex gap-3 pt-4 border-t border-gray-100">
                        <button type="submit" class="flex-1 px-4 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition shadow-sm">💾 Simpan</button>
                        <button type="button" wire:click="$set('showForm', false)" class="px-4 py-2.5 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>