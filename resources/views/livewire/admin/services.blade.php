<div>
    <!-- Header Halaman -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Manajemen Layanan</h2>
        <x-ui.button variant="primary" wire:click="openCreateModal">
            + Tambah Layanan
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
        <input type="text" wire:model.live="search" placeholder="Cari layanan..." class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
    </div>

    <!-- Tabel Data (Flowbite Style) -->
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3">Nama Layanan</th>
                    <th scope="col" class="px-6 py-3">Harga (/m2)</th>
                    <th scope="col" class="px-6 py-3">Status</th>
                    <th scope="col" class="px-6 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($services as $service)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $service->name }}</td>
                        <td class="px-6 py-4">Rp {{ number_format($service->price_per_m2, 0, ',', '.') }}</td>
                        <td class="px-6 py-4">
                            @if($service->is_active)
                                <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">Aktif</span>
                            @else
                                <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <x-ui.button variant="secondary" size="sm" wire:click="edit({{ $service->id }})">
                                Edit
                            </x-ui.button>
                            <x-ui.button variant="danger" size="sm" wire:click="delete({{ $service->id }})" wire:confirm="Yakin ingin menghapus layanan ini?">
                                Hapus
                            </x-ui.button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">Tidak ada data layanan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal Form (Create / Edit) -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" wire:keydown.escape="$set('showModal', false)">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">
                        {{ $isEditing ? 'Edit Layanan' : 'Tambah Layanan Baru' }}
                    </h3>
                    <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form wire:submit="save">
                    <!-- Panggil Komponen Input -->
                    <x-ui.input label="Nama Layanan" name="name" wireModel="name" placeholder="Contoh: Cetak Banner" :error="$errors->first('name')" />
                    
                    <x-ui.input label="Slug (URL)" name="slug" wireModel="slug" placeholder="contoh: cetak-banner" :error="$errors->first('slug')" />
                    
                    <x-ui.input label="Harga per m2 (Rp)" name="price_per_m2" type="number" wireModel="price_per_m2" placeholder="25000" :error="$errors->first('price_per_m2')" />
                    
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-medium text-gray-900">Deskripsi</label>
                        <textarea wire:model="description" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" rows="3"></textarea>
                    </div>

                    <div class="flex items-center mb-4">
                        <input type="checkbox" wire:model="is_active" id="is_active" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                        <label for="is_active" class="ml-2 text-sm font-medium text-gray-900">Aktifkan Layanan</label>
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