<div class="max-w-4xl mx-auto">
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">⚙️ Pengaturan Toko</h1>
        <p class="text-sm text-gray-500 mt-1">
            Kelola identitas toko dan informasi rekening pembayaran. 
            Perubahan berlaku otomatis di Invoice PDF dan halaman pembayaran customer.
        </p>
    </div>

    {{-- Flash Success --}}
    @if (session()->has('success'))
        <div class="mb-4 p-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    {{-- Form --}}
    <form wire:submit="save" class="space-y-6">

        {{-- Section: Identitas Toko --}}
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                🏪 Identitas Toko
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach(['shop_name', 'shop_phone', 'shop_email'] as $key)
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">
                            {{ $fields[$key]['label'] }}
                        </label>
                        <input type="{{ $fields[$key]['type'] }}" 
                               wire:model="settings.{{ $key }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                @endforeach
            </div>
            <div class="mt-4">
                <label class="block mb-1 text-sm font-medium text-gray-700">
                    {{ $fields['shop_address']['label'] }}
                </label>
                <textarea wire:model="settings.shop_address" rows="2"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
            </div>
        </div>

        {{-- Section: Informasi Rekening --}}
        <div class="bg-white p-6 rounded-lg shadow-sm border border-orange-200">
            <h2 class="text-lg font-semibold text-orange-800 mb-4 flex items-center">
                💳 Informasi Rekening Pembayaran
                <span class="ml-2 text-xs bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full">Tampil di Magic Link</span>
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach(['bank_name', 'bank_account', 'bank_holder'] as $key)
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">
                            {{ $fields[$key]['label'] }}
                        </label>
                        <input type="text" 
                               wire:model="settings.{{ $key }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500 font-mono">
                    </div>
                @endforeach
            </div>
            <p class="text-xs text-orange-600 mt-3 italic">
                ⚠️ Informasi ini akan ditampilkan di halaman pembayaran customer (/bayar/{token}) dan di Invoice PDF.
            </p>
        </div>

        {{-- Section: Footer Invoice --}}
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                📄 Footer Invoice
            </h2>
            <textarea wire:model="settings.invoice_footer" rows="2"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500"
                      placeholder="Contoh: Terima kasih atas kepercayaan Anda!"></textarea>
        </div>

        {{-- Tombol Simpan --}}
        <div class="flex justify-end">
            <button type="submit" 
                    class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition"
                    wire:loading.attr="disabled" wire:target="save">
                <span wire:loading.remove wire:target="save">💾 Simpan Pengaturan</span>
                <span wire:loading wire:target="save">Menyimpan...</span>
            </button>
        </div>
    </form>
</div>