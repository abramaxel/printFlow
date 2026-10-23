<div class="max-w-7xl mx-auto px-4 py-8">
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Input Pesanan Baru</h2>
            <p class="text-sm text-gray-500 mt-1">Pilih spesifikasi, tentukan durasi pengerjaan (SLA), dan metode pengiriman.</p>
        </div>
        <a href="{{ route('admin.orders') }}" class="text-sm text-gray-600 hover:text-blue-600 flex items-center transition">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Daftar
        </a>
    </div>

    @if (session()->has('success'))
        <div class="p-4 mb-6 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit="submitOrder">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- ================= KOLOM KIRI (2/3) ================= --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- 1. Data Pelanggan --}}
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 border-b border-gray-100 pb-3 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        1. Data Pelanggan
                    </h3>
                    <div>
                        <label class="block mb-1.5 text-sm font-semibold text-gray-700">Pilih Pelanggan</label>
                        <select wire:model.live="user_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            <option value="">-- Pilih Pelanggan --</option>
                            @foreach($customers as $c)<option value="{{ $c['value'] }}">{{ $c['label'] }}</option>@endforeach
                        </select>
                        @error('user_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- 2. Detail Item --}}
                @foreach($items as $index => $item)
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 transition-all hover:shadow-md">
                    <div class="flex justify-between items-center mb-5 border-b border-gray-100 pb-3">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                            <span class="bg-blue-100 text-blue-700 text-xs font-bold px-2.5 py-1 rounded-md mr-3">ITEM #{{ $index + 1 }}</span>
                            Detail Spesifikasi
                        </h3>
                        @if(count($items) > 1)
                            <button type="button" wire:click="removeItem({{ $index }})" class="text-red-500 hover:text-red-700 hover:bg-red-50 p-2 rounded-lg transition text-sm flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                Hapus Item
                            </button>
                        @endif
                    </div>

                    {{-- Baris A: Layanan & Bahan --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                        <x-ui.select label="Jenis Layanan" name="items.{{ $index }}.service_id" :options="$services" wireModel="items.{{ $index }}.service_id" :error="$errors->first('items.' . $index . '.service_id')" />
                        <x-ui.select label="Jenis Bahan" name="items.{{ $index }}.material_id" :options="$materials" wireModel="items.{{ $index }}.material_id" :error="$errors->first('items.' . $index . '.material_id')" />
                    </div>

                    {{-- Baris B: Ukuran, Durasi (SLA), Qty --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-3">
                        <x-ui.select label="Ukuran" name="items.{{ $index }}.size_id" :options="$sizes" wireModel="items.{{ $index }}.size_id" :error="$errors->first('items.' . $index . '.size_id')" />
                        <x-ui.select label="Durasi Pengerjaan" name="items.{{ $index }}.duration_id" :options="$durations" wireModel="items.{{ $index }}.duration_id" :error="$errors->first('items.' . $index . '.duration_id')" />
                        <div>
                            <label class="block mb-1.5 text-sm font-semibold text-gray-700">Jumlah (Qty)</label>
                            <div class="relative">
                                <input type="number" wire:model.live="items.{{ $index }}.quantity" min="1" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 pl-10 transition">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                </div>
                            </div>
                            @error('items.' . $index . '.quantity') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Badge info durasi (real-time) --}}
                    @php $dur = $this->getDurationInfo($index); @endphp
                    @if($dur)
                        <div class="mb-5 flex flex-wrap items-center gap-2 text-xs">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full font-bold
                                {{ $dur['multiplier'] >= 2 ? 'bg-red-100 text-red-700' : ($dur['multiplier'] > 1 ? 'bg-orange-100 text-orange-700' : 'bg-green-100 text-green-700') }}">
                                ⏱ {{ $dur['name'] }} · {{ $dur['days'] }} hari · ×{{ rtrim(rtrim(number_format($dur['multiplier'], 2, '.', ''), '0'), '.') }}
                            </span>
                            @if($dur['description'])
                                <span class="text-gray-500">{{ $dur['description'] }}</span>
                            @endif
                        </div>
                    @endif

                    {{-- Subtotal item (sudah termasuk biaya durasi) --}}
                    <div class="bg-blue-50 border border-blue-100 p-4 rounded-lg mb-5 flex justify-between items-center">
                        <span class="text-sm font-semibold text-blue-800 flex items-center">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            Subtotal Item Ini (termasuk durasi):
                        </span>
                        <span class="text-xl font-bold text-blue-900">Rp {{ number_format($this->getItemSubtotal($index), 0, ',', '.') }}</span>
                    </div>

                    {{-- File & Catatan --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block mb-2 text-sm font-semibold text-gray-700">File Desain</label>
                            <div class="flex items-center justify-center w-full">
                                <label class="flex flex-col items-center justify-center w-full h-28 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-blue-50 hover:border-blue-400 transition-all">
                                    <div class="flex flex-col items-center justify-center pt-2 pb-2">
                                        <svg class="w-8 h-8 mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                        <p class="text-xs text-gray-500"><span class="font-semibold text-blue-600">Klik untuk upload</span> atau drag & drop</p>
                                        <p class="text-[10px] text-gray-400 mt-1">JPG, PNG, PDF, ZIP (Maks. 20MB/file)</p>
                                    </div>
                                    <input type="file" wire:model="items.{{ $index }}.design_files" multiple class="hidden" accept=".jpg,.jpeg,.png,.pdf,.zip" />
                                </label>
                            </div>
                            @error('items.' . $index . '.design_files.*')
                                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                            @if(isset($item['design_files']) && is_iterable($item['design_files']) && count($item['design_files']) > 0)
                                <div class="mt-3 space-y-2">
                                    @foreach($item['design_files'] as $fileIndex => $file)
                                        <div class="flex items-center justify-between p-2.5 bg-blue-50 border border-blue-200 rounded-lg text-xs">
                                            <div class="flex items-center overflow-hidden">
                                                <svg class="w-4 h-4 text-blue-600 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                <span class="text-gray-700 truncate max-w-[140px]" title="{{ $file->getClientOriginalName() }}">{{ $file->getClientOriginalName() }}</span>
                                            </div>
                                            <div class="flex items-center flex-shrink-0 ml-2 gap-1.5">
                                                <span class="text-[10px] text-gray-500 bg-white px-2 py-0.5 rounded border border-blue-100">{{ number_format($file->getSize() / 1024, 1) }} KB</span>
                                                <button
                                                    type="button"
                                                    wire:click="removeFile({{ $index }}, {{ $fileIndex }})"
                                                    wire:loading.attr="disabled"
                                                    wire:target="removeFile({{ $index }}, {{ $fileIndex }})"
                                                    title="Hapus file ini (salah upload?)"
                                                    class="text-red-400 hover:text-red-600 hover:bg-red-100 p-1 rounded transition"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-semibold text-gray-700">Catatan Khusus Item</label>
                            <textarea wire:model="items.{{ $index }}.notes" rows="5" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3 resize-none" placeholder="Contoh:&#10;- Warna merah lebih cerah&#10;- Potong die-cut&#10;- Jangan dilipat"></textarea>
                        </div>
                    </div>
                </div>
                @endforeach

                {{-- Tombol tambah item --}}
                <button type="button" wire:click="addItem" class="w-full py-3 border-2 border-dashed border-blue-300 text-blue-600 rounded-xl hover:bg-blue-50 hover:border-blue-400 font-semibold text-sm flex justify-center items-center transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    + Tambah Item Lain ke Pesanan Ini
                </button>

                {{-- 3. Metode Pengiriman --}}
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 border-b border-gray-100 pb-3 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 001 1H15m-2-4a1 1 0 011-1h1a1 1 0 011 1v4a1 1 0 01-1 1h-1a1 1 0 01-1-1v-4zM7 20a2 2 0 100-4 2 2 0 000 4zm10 0a2 2 0 100-4 2 2 0 000 4z"></path></svg>
                        3. Metode Pengiriman
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
                        @php $shipOptions = ['pickup' => ['🏪','Pickup (Ambil di Toko)','Gratis'], 'regular' => ['🚚','Kurir Reguler','JNE / J&T / SiCepat'], 'instant' => ['⚡','Kurir Instant','GoSend / Grab']]; @endphp
                        @foreach($shipOptions as $val => $meta)
                            <label class="flex flex-col p-3 border rounded-lg cursor-pointer transition
                                {{ $shipping_method === $val ? 'border-blue-500 bg-blue-50 ring-1 ring-blue-200' : 'border-gray-300 hover:bg-gray-50' }}">
                                <input type="radio" wire:model.live="shipping_method" value="{{ $val }}" class="sr-only">
                                <span class="font-semibold text-sm text-gray-800">{{ $meta[0] }} {{ $meta[1] }}</span>
                                <span class="text-[11px] text-gray-500 mt-0.5">{{ $meta[2] }}</span>
                            </label>
                        @endforeach
                    </div>

                    @if($shipping_method !== 'pickup')
                        <div class="mb-2">
                            <label class="block mb-1 text-sm font-semibold text-gray-700">Alamat Pengiriman Lengkap</label>
                            <textarea wire:model="shipping_address" rows="3" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3 resize-none" placeholder="Nama penerima, No. HP, jalan, kelurahan, kecamatan, kota, provinsi, kode pos..."></textarea>
                            @error('shipping_address') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    @endif

                    <p class="text-xs text-gray-500 flex items-start bg-gray-50 border border-gray-200 rounded-lg p-3">
                        <svg class="w-4 h-4 mr-1.5 mt-0.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Ongkos kirim & nama kurir <strong>tidak diinput di sini</strong> — akan diisi admin saat menerbitkan Invoice, dan urusan resi/tracking diserahkan ke pihak ekspedisi.
                    </p>
                </div>

                {{-- 4. Catatan Global --}}
                <div class="bg-gray-50 p-6 rounded-xl border border-gray-200">
                    <h3 class="text-sm font-bold text-gray-700 mb-3 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        4. Catatan Keseluruhan Pesanan (Opsional)
                    </h3>
                    <textarea wire:model="global_notes" rows="3" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3 resize-none" placeholder="Contoh: Tolong dikemas kotak kardus tebal, diambil kurir Senin pagi."></textarea>
                </div>
            </div>

            {{-- ================= KOLOM KANAN (1/3): KALKULATOR ================= --}}
            <div class="lg:col-span-1">
                <div class="bg-white border-2 border-blue-100 rounded-xl p-6 sticky top-6 shadow-lg">
                    <h3 class="text-lg font-bold text-blue-900 mb-5 flex items-center pb-3 border-b border-blue-100">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        Ringkasan Estimasi Harga Pesanan
                    </h3>

                    @if($estimated_price > 0)
                    <div class="space-y-5">
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-600">Total Harga Master:</span>
                                <span class="text-lg font-bold text-gray-500 line-through">Rp {{ number_format($estimated_price, 0, ',', '.') }}</span>
                            </div>
                            @if($total_duration_fee > 0)
                                <div class="flex justify-between items-center mt-2 pt-2 border-t border-gray-200">
                                    <span class="text-xs text-orange-700 font-medium">↳ termasuk biaya percepatan (durasi):</span>
                                    <span class="text-xs font-bold text-orange-700">+ Rp {{ number_format($total_duration_fee, 0, ',', '.') }}</span>
                                </div>
                            @endif
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Diskon (%)</label>
                            <div class="relative">
                                <input type="number" wire:model.live.debounce.300ms="discount_percentage" min="0" max="100" placeholder="0" class="w-full pl-3 pr-8 py-2.5 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-medium transition">
                                <span class="absolute right-3 top-2.5 text-gray-500 font-bold">%</span>
                            </div>
                            <p class="text-xs text-gray-400 mt-1.5 text-center">atau input harga deal langsung di bawah</p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Harga Deal / Final (Rp)</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2.5 text-gray-500 font-bold">Rp</span>
                                <input type="number" wire:model.live.debounce.300ms="final_price_input" placeholder="{{ number_format($estimated_price, 0, ',', '.') }}" class="w-full pl-10 pr-3 py-2.5 border-2 {{ $errors->has('final_price_input') ? 'border-red-400 focus:border-red-500 focus:ring-red-100' : 'border-blue-300 focus:border-blue-500 focus:ring-blue-100' }} rounded-lg text-sm font-bold text-gray-900 transition">
                            </div>
                            @error('final_price_input') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <button type="button" wire:click="useMasterPrice" class="w-full py-2.5 px-4 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg border border-gray-300 transition-all duration-200 flex items-center justify-center gap-2 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            Reset ke Harga Master
                        </button>

                        @if($discount > 0)
                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-lg p-4 shadow-sm">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-bold text-green-800">Diskon Diberikan:</span>
                                <span class="text-lg font-bold text-green-700">- Rp {{ number_format($discount, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-green-700 font-medium">Persentase:</span>
                                <span class="bg-green-200 text-green-800 text-xs font-bold px-2.5 py-1 rounded-full border border-green-300">{{ number_format($discount_percentage, 1) }}% OFF</span>
                            </div>
                        </div>
                        @endif

                        <div class="border-t-2 border-dashed border-blue-200 pt-4 mt-2">
                            <div class="flex justify-between items-center">
                                <span class="text-base font-bold text-gray-700">Total Tagihan:</span>
                                <span class="text-2xl font-extrabold text-blue-700">Rp {{ number_format($final_price_input, 0, ',', '.') }}</span>
                            </div>
                            @if($discount > 0)
                                <p class="text-xs text-green-600 mt-2 text-center font-medium bg-green-100 py-1 rounded">✓ Hemat Rp {{ number_format($discount, 0, ',', '.') }} dari harga master</p>
                            @endif
                        </div>
                    </div>
                    @else
                    <div class="text-center py-10 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <p class="text-sm text-gray-500 font-medium">Lengkapi detail item<br>untuk menampilkan rincian biaya</p>
                    </div>
                    @endif

                    <x-ui.button type="submit" variant="primary" size="lg" class="w-full justify-center mt-6 py-3.5 text-base font-bold shadow-md hover:shadow-lg transition-all" wire:loading.attr="disabled" wire:target="submitOrder">
                        <span wire:loading.remove wire:target="submitOrder">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Simpan Pesanan
                        </span>
                        <span wire:loading wire:target="submitOrder" class="flex items-center justify-center">
                            <svg class="animate-spin h-5 w-5 mr-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Menyimpan...
                        </span>
                    </x-ui.button>
                </div>
            </div>
        </div>
    </form>
</div>