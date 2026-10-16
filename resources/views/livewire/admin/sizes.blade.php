<div>
    <!-- Header Halaman -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Manajemen Ukuran (Sizes)</h2>
        <x-ui.button variant="primary" wire:click="openCreateModal">
            + Tambah Ukuran
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
        <input type="text" wire:model.live="search" placeholder="Cari ukuran..." class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
    </div>

    <!-- Tabel Data -->
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3">Nama Ukuran</th>
                    <th scope="col" class="px-6 py-3">Dimensi (cm)</th>
                    <th scope="col" class="px-6 py-3">Luas Area (m2)</th>
                    <th scope="col" class="px-6 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sizes as $size)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $size->name }}</td>
                        <td class="px-6 py-4">
                            @if($size->width_cm && $size->height_cm)
                                {{ $size->width_cm }} x {{ $size->height_cm }} cm
                            @else
                                <span class="text-gray-400 italic">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 font-semibold text-blue-600">{{ $size->area_m2 }} m2</td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <x-ui.button variant="secondary" size="sm" wire:click="edit({{ $size->id }})">Edit</x-ui.button>
                            <x-ui.button variant="danger" size="sm" wire:click="delete({{ $size->id }})" wire:confirm="Yakin ingin menghapus ukuran ini?">Hapus</x-ui.button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">Tidak ada data ukuran.</td>
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
                        {{ $isEditing ? 'Edit Ukuran' : 'Tambah Ukuran Baru' }}
                    </h3>
                    <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form wire:submit="save">
                    <x-ui.input label="Nama Ukuran" name="name" wireModel="name" placeholder="Contoh: A4 atau Apparel Size M" :error="$errors->first('name')" />
                    <x-ui.input label="Slug (URL)" name="slug" wireModel="slug" placeholder="a4" :error="$errors->first('slug')" />
                    
                    <!-- Grid untuk Dimensi -->
                    <div class="grid grid-cols-2 gap-4">
                        <x-ui.input label="Lebar (cm)" name="width_cm" type="number" step="0.01" wireModel="width_cm" placeholder="21.00" :error="$errors->first('width_cm')" />
                        <x-ui.input label="Tinggi (cm)" name="height_cm" type="number" step="0.01" wireModel="height_cm" placeholder="29.70" :error="$errors->first('height_cm')" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <x-ui.input label="Luas Area (m2) *" name="area_m2" type="number" step="0.0001" wireModel="area_m2" placeholder="0.0624" :error="$errors->first('area_m2')" />
                        <x-ui.input label="Penyesuaian Harga (Rp)" name="price_adjustment" type="number" wireModel="price_adjustment" placeholder="0" :error="$errors->first('price_adjustment')" />
                    </div>

                    <div class="flex items-center mb-4">
                        <input type="checkbox" wire:model="is_active" id="is_active" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                        <label for="is_active" class="ml-2 text-sm font-medium text-gray-900">Aktifkan Ukuran</label>
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