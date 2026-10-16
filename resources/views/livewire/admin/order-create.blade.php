<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Input Pesanan Baru (Multi-Item)</h2>
        <a href="{{ route('admin.orders') }}" class="text-sm text-gray-600 hover:text-blue-600">← Kembali</a>
    </div>

    @if (session()->has('success'))
        <div class="p-4 mb-6 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200">{{ session('success') }}</div>
    @endif

    <form wire:submit="submitOrder">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- ========================================== -->
            <!-- KOLOM KIRI: FORM INPUT -->
            <!-- ========================================== -->
            <div class="lg:col-span-2 space-y-6 bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                
                <h3 class="text-lg font-semibold text-gray-800 border-b pb-2">1. Data Pelanggan</h3>
                <x-ui.select label="Pilih Pelanggan" name="user_id" :options="$customers" wireModel="user_id" :error="$errors->first('user_id')" />

                <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 pt-4">2. Detail Item Pesanan</h3>
                
                @foreach($items as $index => $item)
                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 relative group mb-4">
                        @if(count($items) > 1)
                            <button type="button" wire:click="removeItem({{ $index }})" class="absolute top-2 right-2 text-red-500 hover:text-red-700" title="Hapus Item">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        @endif
                        <p class="text-xs font-bold text-gray-500 mb-3 uppercase">Item #{{ $index + 1 }}</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <x-ui.select label="Layanan" name="items.{{ $index }}.service_id" :options="$services" wireModel="items.{{ $index }}.service_id" :error="$errors->first('items.' . $index . '.service_id')" />
                            <x-ui.select label="Bahan" name="items.{{ $index }}.material_id" :options="$materials" wireModel="items.{{ $index }}.material_id" :error="$errors->first('items.' . $index . '.material_id')" />
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <x-ui.select label="Ukuran" name="items.{{ $index }}.size_id" :options="$sizes" wireModel="items.{{ $index }}.size_id" :error="$errors->first('items.' . $index . '.size_id')" />
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Jumlah (Qty)</label>
                                <input type="number" wire:model.live="items.{{ $index }}.quantity" min="1" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="block mb-1 text-sm font-medium text-gray-700">Catatan Khusus Item Ini (Opsional)</label>
                            <textarea wire:model="items.{{ $index }}.notes" rows="2" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Contoh: Warna merah lebih cerah"></textarea>
                        </div>

                        <!-- UPLOAD FILE DESAIN PER ITEM -->
                        <div class="border-t border-gray-300 pt-4 mt-4">
                            <label class="block mb-2 text-sm font-bold text-gray-700">File Desain untuk Item #{{ $index + 1 }}</label>
                            
                            <div class="flex items-center justify-center w-full">
                                <label class="flex flex-col items-center justify-center w-full h-24 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-white hover:bg-gray-50 transition">
                                    <div class="flex flex-col items-center justify-center pt-2 pb-2">
                                        <svg class="w-6 h-6 mb-2 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                        </svg>
                                        <p class="text-xs text-gray-500"><span class="font-semibold">Klik untuk upload</span></p>
                                        <p class="text-[10px] text-gray-400">JPG, PNG, PDF, ZIP (Maks. 20MB)</p>
                                    </div>
                                    <input type="file" wire:model="items.{{ $index }}.design_files" multiple class="hidden" accept=".jpg,.jpeg,.png,.pdf,.zip" />
                                </label>
                            </div>
                            
                            @error('items.' . $index . '.design_files.*') 
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p> 
                            @enderror

                            <!-- Tampilkan File yang Sudah Diupload untuk Item Ini -->
                            @if(isset($item['design_files']) && is_array($item['design_files']) && count($item['design_files']) > 0)
                                <div class="mt-2 space-y-1">
                                    @foreach($item['design_files'] as $fileIndex => $file)
                                        <div class="flex items-center justify-between p-2 bg-blue-50 border border-blue-200 rounded text-xs">
                                            <div class="flex items-center">
                                                <svg class="w-3 h-3 text-blue-600 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                <span class="text-gray-700 truncate max-w-xs">{{ $file->getClientOriginalName() }}</span>
                                            </div>
                                            <span class="text-[10px] text-gray-500">{{ number_format($file->getSize() / 1024, 1) }} KB</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Total Harga Master Item Ini -->
                        <div class="mt-4 pt-3 border-t border-gray-300 flex justify-between items-center bg-white p-3 rounded-md shadow-sm">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                <span class="text-sm font-medium text-gray-700">Total Harga Master Item Ini:</span>
                            </div>
                            <span class="text-lg font-bold text-blue-700">
                                Rp {{ number_format($this->getItemSubtotal($index), 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                @endforeach

                <button type="button" wire:click="addItem" class="w-full py-2 border-2 border-dashed border-blue-300 text-blue-600 rounded-lg hover:bg-blue-50 font-medium text-sm flex justify-center items-center transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Item Lain ke Pesanan Ini
                </button>

                <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 pt-4">3. Catatan Global</h3>
                <div class="mb-4">
                    <label class="block mb-1 text-sm font-medium text-gray-700">Catatan Keseluruhan Pesanan</label>
                    <textarea wire:model="global_notes" rows="2" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Contoh: Tolong dikemas rapi, ambil hari Senin."></textarea>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- KOLOM KANAN: ESTIMASI HARGA -->
            <!-- ========================================== -->
            <div class="lg:col-span-1">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 sticky top-6">
                    <h3 class="text-lg font-bold text-blue-900 mb-4">Kalkulator Harga</h3>

                    @if($estimated_price > 0)
                        <div class="space-y-3 text-sm text-gray-700 mb-6">
                            <div class="flex justify-between">
                                <span>Total Harga Master:</span>
                                <span class="font-medium text-gray-500 line-through">Rp {{ number_format($estimated_price, 0, ',', '.') }}</span>
                            </div>
                            
                            <div class="pt-3 border-t border-blue-200">
                                <label class="block mb-1 text-sm font-bold text-gray-900">Harga Deal / Final</label>
                                
                                <div class="relative">
                                    <span class="absolute left-3 top-2.5 text-gray-500 font-semibold">Rp</span>
                                    <input 
                                        type="text" 
                                        wire:model.live.debounce.500ms="final_price_input" 
                                        placeholder="{{ number_format($estimated_price, 0, ',', '.') }}"
                                        class="bg-white border {{ $errors->has('final_price_input') ? 'border-red-500' : 'border-blue-300' }} text-gray-900 text-lg font-bold rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 pl-10"
                                    >
                                </div>
                                <p class="text-xs text-gray-500 mt-1">
                                    💡 Ketik angka tanpa titik (contoh: 112000000 untuk 112 juta)
                                </p>
                                @error('final_price_input') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror

                                <!-- Tombol Cepat -->
                                <button type="button" wire:click="useMasterPrice" class="mt-2 w-full py-1.5 text-xs font-medium text-blue-700 bg-blue-100 rounded hover:bg-blue-200 transition">
                                    🎯 Gunakan Harga Master (Rp {{ number_format($estimated_price, 0, ',', '.') }})
                                </button>
                            </div>

                            @if($discount > 0)
                                <div class="flex justify-between items-center bg-green-50 border border-green-200 text-green-700 p-3 rounded-lg">
                                    <div class="flex flex-col">
                                        <span class="text-xs text-green-600 uppercase font-semibold">Potongan</span>
                                        <span class="text-sm font-bold">- Rp {{ number_format($discount, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="bg-green-200 text-green-800 text-xs font-bold px-2.5 py-1 rounded-full border border-green-300">
                                        {{ number_format($discount_percentage, 1) }}% OFF
                                    </div>
                                </div>
                            @endif

                            <div class="border-t border-blue-200 pt-3 mt-3">
                                <div class="flex justify-between text-xl font-bold text-blue-900">
                                    <span>Total Tagihan:</span>
                                    <span>Rp {{ number_format($final_price_input, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-gray-500 italic mb-6">Lengkapi minimal 1 item untuk melihat estimasi.</p>
                    @endif

                    <x-ui.button type="submit" variant="primary" size="lg" class="w-full justify-center" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="submitOrder">Simpan Pesanan</span>
                        <span wire:loading wire:target="submitOrder">Menyimpan...</span>
                    </x-ui.button>
                </div>
            </div>
        </div>
    </form>
</div>