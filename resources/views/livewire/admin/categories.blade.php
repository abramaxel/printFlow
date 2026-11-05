<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">📂 Manajemen Kategori</h2>
    </div>

    @if (session()->has('success'))
        <div class="p-4 mb-6 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200">{{ session('success') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="p-4 mb-6 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200">{{ session('error') }}</div>
    @endif

    {{-- FORM INPUT / EDIT --}}
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ $categoryId ? '✏️ Edit Kategori' : '➕ Tambah Kategori Baru' }}</h3>
        <form wire:submit="{{ $categoryId ? 'update' : 'store' }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori <span class="text-red-500">*</span></label>
                <input type="text" wire:model="name" placeholder="Contoh: Large Format, Digital Printing..." 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <input type="text" wire:model="description" placeholder="Keterangan singkat kategori..." 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                    {{ $categoryId ? '💾 Simpan Perubahan' : '➕ Tambah' }}
                </button>
                @if($categoryId)
                    <button type="button" wire:click="cancelEdit" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition">
                        Batal
                    </button>
                @endif
            </div>
        </form>
    </div>

    {{-- TABEL KATEGORI --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="font-semibold text-gray-800">Daftar Kategori</h3>
            <input type="text" wire:model.live="search" placeholder="Cari kategori..." 
                class="px-3 py-1.5 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500 w-64">
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3">Nama</th>
                        <th class="px-6 py-3">Slug</th>
                        <th class="px-6 py-3">Jumlah Layanan</th>
                        <th class="px-6 py-3">Jumlah Bahan</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($categories as $cat)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $cat->name }}</td>
                            <td class="px-6 py-4 text-gray-500 font-mono text-xs">{{ $cat->slug }}</td>
                            <td class="px-6 py-4"><span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs">{{ $cat->services_count }}</span></td>
                            <td class="px-6 py-4"><span class="px-2 py-1 bg-orange-100 text-orange-700 rounded-full text-xs">{{ $cat->materials_count }}</span></td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <button wire:click="edit({{ $cat->id }})" class="text-blue-600 hover:text-blue-800 text-xs font-medium">✏️ Edit</button>
                                <button wire:click="delete({{ $cat->id }})" 
                                    wire:confirm="Yakin hapus kategori '{{ $cat->name }}'? Data tidak bisa dikembalikan!" 
                                    class="text-red-600 hover:text-red-800 text-xs font-medium">🗑️ Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">Belum ada kategori.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-200">
            {{ $categories->links() }}
        </div>
    </div>
</div>