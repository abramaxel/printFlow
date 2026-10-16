<div>
    <!-- Header Halaman -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Manajemen Bahan / Media Cetak</h2>
        <x-ui.button variant="primary" wire:click="openCreateModal">
            + Tambah Bahan
        </x-ui.button>
    </div>

    <!-- Flash Message -->
    @if (session()->has('success'))
        <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <!-- Search Bar -->
    <div class="mb-4">
        <input type="text" wire:model.live="search" placeholder="Cari bahan..." class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
    </div>

    <!-- Tabel Data -->
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3">Nama Bahan</th>
                    <th scope="col" class="px-6 py-3">Kategori</th>
                    <th scope="col" class="px-6 py-3">Harga (/m2)</th>
                    <th scope="col" class="px-6 py-3">Stok</th>
                    <th scope="col" class="px-6 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($materials as $material)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $material->name }}</td>
                        <td class="px-6 py-4">{{ $material->category->name ?? '-' }}</td>
                        <td class="px-6 py-4">Rp {{ number_format($material->price_per_m2, 0, ',', '.') }}</td>
                        <td class="px-6 py-4">
                            <span class="{{ $material->stock > 0 ? 'text-green-600' : 'text-red-600' }} font-semibold">
                                {{ $material->stock }} m2
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <x-ui.button variant="secondary" size="sm" wire:click="edit({{ $material->id }})">Edit</x-ui.button>
                            <x-ui.button variant="danger" size="sm" wire:click="delete({{ $material->id }})" wire:confirm="Yakin ingin menghapus bahan ini?">Hapus</x-ui.button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada data bahan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal Form -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" wire:keydown.escape="$set('showModal', false)">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6 max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">
                        {{ $isEditing ? 'Edit Bahan' : 'Tambah Bahan Baru' }}
                    </h3>
                    <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form wire:submit="save">
                    <!-- Dropdown Kategori -->
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-medium text-gray-900">Kategori</label>
                        <select wire:model="category_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <x-ui.input label="Nama Bahan" name="name" wireModel="name" placeholder="Contoh: Flexi China" :error="$errors->first('name')" />
                    <x-ui.input label="Slug (URL)" name="slug" wireModel="slug" placeholder="flexi-china" :error="$errors->first('slug')" />
                    
                    <div class="grid grid-cols-2 gap-4">
                        <x-ui.input label="Harga per m2 (Rp)" name="price_per_m2" type="number" wireModel="price_per_m2" placeholder="15000" :error="$errors->first('price_per_m2')" />
                        <x-ui.input label="Stok (m2)" name="stock" type="number" wireModel="stock" placeholder="100" :error="$errors->first('stock')" />
                    </div>

                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-medium text-gray-900">Deskripsi</label>
                        <textarea wire:model="description" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" rows="3"></textarea>
                    </div>

                    <div class="flex items-center mb-4">
                        <input type="checkbox" wire:model="is_active" id="is_active" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                        <label for="is_active" class="ml-2 text-sm font-medium text-gray-900">Aktifkan Bahan</label>
                    </div>

                    <div class="flex justify-end space-x-2">
                        <x-ui.button variant="secondary" type="button" wire:click="$set('showModal', false)">Batal</x-ui.button>
                        <x-ui.button variant="primary" type="submit">Simpan</x-ui.button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>