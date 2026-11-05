<div class="max-w-7xl mx-auto px-4 py-8">
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Input Pesanan Baru</h2>
            <p class="text-sm text-gray-500 mt-1">Pilih spesifikasi, tentukan durasi pengerjaan (SLA), dan metode pengiriman.</p>
        </div>
        <a href="{{ route('admin.orders') }}" class="text-sm text-gray-600 hover:text-blue-600 flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Daftar
        </a>
    </div>

    @if (session()->has('success'))
        <div class="p-4 mb-6 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200">{{ session('success') }}</div>
    @endif

    <form wire:submit="submitOrder" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- ================= KOLOM KIRI (2/3) ================= --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- 1. Data Pelanggan --}}
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">1. Data Pelanggan</h3>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Pelanggan <span class="text-red-500">*</span></label>
                    <select wire:model="user_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 bg-white">
                        <option value="">-- Pilih Pelanggan --</option>
                        @foreach($customers as $c)
                            <option value="{{ $c['id'] }}">{{ $c['label'] }}</option>
                        @endforeach
                    </select>
                    @error('user_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- 2. Detail Item --}}
            @foreach($items as $index => $item)
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 relative">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">ITEM #{{ $index + 1 }}</h3>
                    @if(count($items) > 1)
                        <button type="button" wire:click="removeItem({{ $index }})" class="text-xs text-red-600 hover:text-red-800 font-medium">Hapus Item</button>
                    @endif
                </div>

                {{-- ============ BARIS A: DIVISI PRODUKSI (KATEGORI BISNIS) ============ --}}
                <div class="mb-4 p-4 bg-indigo-50/60 border border-indigo-200 rounded-lg">
                    <label class="flex items-center gap-2 text-sm font-bold text-indigo-800 mb-1">
                        🗂️ Divisi Produksi <span class="text-red-500">*</span>
                    </label>
                    <p class="text-[11px] text-indigo-600 mb-2">
                        Kelompok besar bisnis toko (mis. Large Format, Digital Print).
                        Seluruh pilihan di bawah akan menyesuaikan otomatis mengikuti divisi ini.
                    </p>
                    <select wire:model.live="items.{{ $index }}.category_id"
                        class="w-full px-3 py-2 border border-indigo-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 bg-white font-semibold text-indigo-900">
                        <option value="">-- Pilih Divisi Produksi --</option>
                        @foreach(\App\Models\Category::where('is_active', true)->get() as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('items.' . $index . '.category_id')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- ============ BARIS B: PRODUK SPESIFIK (TERKUNCI SAMPAI DIVISI DIPILIH) ============ --}}
                @if(!empty($item['category_id']))
                    <div class="mb-4 p-4 bg-white border border-gray-200 rounded-lg">
                        <div class="flex items-center justify-between mb-1">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-800">
                                🖨️ Produk / Layanan Cetak <span class="text-red-500">*</span>
                            </label>
                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700 font-semibold">
                                Divisi aktif: {{ \App\Models\Category::find($item['category_id'])?->name }}
                            </span>
                        </div>
                        <p class="text-[11px] text-gray-500 mb-3">
                            Produk spesifik yang dipesan customer di dalam divisi tersebut, beserta bahan bakunya.
                        </p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">🖨️ Layanan Cetak *</label>
                               <select wire:model.live="items.{{ $index }}.service_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 bg-white disabled:bg-gray-100"
                                    {{ empty($item['category_id']) ? 'disabled' : '' }}>
                                    <option value="">-- Pilih Layanan --</option>
                                    @foreach(\App\Models\Service::where('is_active', true)
                                        ->when(!empty($item['category_id']), fn($q) => $q->where('category_id', $item['category_id']))
                                        ->get() as $service)
                                        <option value="{{ $service->id }}">{{ $service->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">🧵 Bahan Baku *</label>
                                <select wire:model.live="items.{{ $index }}.material_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 bg-white disabled:bg-gray-100"
                                    {{ empty($item['category_id']) ? 'disabled' : '' }}>
                                    <option value="">-- Pilih Bahan --</option>
                                    @foreach(\App\Models\Material::where('is_active', true)
                                        ->when(!empty($item['category_id']), fn($q) => $q->where('category_id', $item['category_id']))
                                        ->get() as $material)
                                        <option value="{{ $material->id }}">{{ $material->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="mb-4 p-4 border-2 border-dashed border-gray-200 rounded-lg text-center text-xs text-gray-400">
                        🔒 Pilih <strong class="text-indigo-500">Divisi Produksi</strong> terlebih dahulu
                        untuk membuka pilihan Layanan Cetak, Bahan Baku, dan Ukuran.
                    </div>
                @endif

                {{-- ============ BARIS C: UKURAN · DURASI (SLA) · QTY ============ --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    {{-- Kolom 1: Ukuran (terfilter divisi) --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">📐 Ukuran Standar <span class="text-red-500">*</span></label>
                        <select wire:model.live="items.{{ $index }}.size_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 bg-white disabled:bg-gray-100"
                            {{ empty($item['category_id']) ? 'disabled' : '' }}>
                            <option value="">-- Pilih Ukuran --</option>
                            @foreach(\App\Models\Size::where('is_active', true)
                                ->when(!empty($item['category_id']),
                                    fn($q) => $q->where(fn($w) => $w->where('category_id', $item['category_id'])->orWhereNull('category_id')))
                                ->get() as $size)
                                <option value="{{ $size->id }}">{{ $size->name }}</option>
                            @endforeach
                        </select>
                        @error('items.' . $index . '.size_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Kolom 2: DURASI PENGERJAAN (snippet Anda) --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">
                            ⏱️ Durasi Pengerjaan (SLA) <span class="text-red-500">*</span>
                        </label>
                        <select wire:model.live="items.{{ $index }}.duration_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 bg-white">
                            <option value="">-- Pilih Durasi --</option>
                            @foreach(\App\Models\ProcessingDuration::where('is_active', true)->orderBy('days')->get() as $dur)
                                <option value="{{ $dur->id }}">
                                    {{ $dur->name }} (+{{ $dur->days }} hari) · ×{{ rtrim(rtrim(number_format((float) $dur->multiplier, 2, '.', ''), '0'), '.') }}
                                </option>
                            @endforeach
                        </select>
                        @error('items.' . $index . '.duration_id')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Kolom 3: Jumlah / Qty --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">🔢 Jumlah (Qty) <span class="text-red-500">*</span></label>
                        <input type="number" wire:model.live="items.{{ $index }}.quantity" min="1"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        @error('items.' . $index . '.quantity') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Badge info durasi & Subtotal (Tetap Sama) --}}
                @php $dur = $this->getDurationInfo($index); @endphp
                @if($dur)
                    <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg flex items-start gap-3">
                        <span class="text-xl">⏱️</span>
                        <div>
                            <p class="text-sm font-semibold text-blue-800">{{ $dur['name'] }} · {{ $dur['days'] }} hari · ×{{ rtrim(rtrim(number_format($dur['multiplier'], 2, '.', ''), '0'), '.') }}</p>
                            @if($dur['description']) <p class="text-xs text-blue-600 mt-0.5">{{ $dur['description'] }}</p> @endif
                        </div>
                    </div>
                @endif

                <div class="mb-4 p-3 bg-gray-50 border border-gray-200 rounded-lg text-right">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Subtotal Item Ini:</p>
                    <p class="text-lg font-bold text-gray-900">Rp {{ number_format($this->getItemSubtotal($index), 0, ',', '.') }}</p>
                </div>

                {{-- File & Catatan --}}
                <div class="space-y-4">
                    {{-- FILE DESAIN --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">File Desain</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-blue-500 transition cursor-pointer relative">
                            <input type="file" wire:model="items.{{ $index }}.design_files" multiple
                                accept=".jpg,.jpeg,.png,.pdf,.zip"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            <p class="text-sm text-gray-500"><span class="font-semibold text-blue-600">Klik untuk upload</span> atau drag & drop</p>
                            <p class="text-xs text-gray-400 mt-1">
                                JPG, PNG, PDF, ZIP • Maks. 20MB/file • Gunakan file final siap cetak (resolusi ≥ 150 dpi)
                            </p>
                        </div>
                        @error('items.' . $index . '.design_files.*') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror

                        {{-- DAFTAR FILE + TOMBOL HAPUS PER FILE --}}
                        @if(isset($item['design_files']) && is_iterable($item['design_files']) && count($item['design_files']) > 0)
                            <div class="mt-2 space-y-1">
                                @foreach($item['design_files'] as $fileIndex => $file)
                                    <div class="flex items-center justify-between gap-2 p-2 bg-gray-50 rounded border border-gray-200 text-xs">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <span class="text-base flex-shrink-0">📎</span>
                                            <span class="truncate max-w-[220px]" title="{{ $this->cleanFileName($file) }}">
                                                {{ $this->cleanFileName($file) }}
                                            </span>
                                            <span class="text-gray-400 flex-shrink-0">{{ number_format($file->getSize() / 1024, 1) }} KB</span>
                                        </div>
                                        <button type="button"
                                            wire:click="removeFile({{ $index }}, {{ $fileIndex }})"
                                            wire:confirm="Hapus file ini dari daftar upload?"
                                            class="flex-shrink-0 px-2 py-1 text-red-600 hover:bg-red-50 rounded font-semibold"
                                            title="Hapus file">
                                            ✕ Hapus
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- CATATAN KHUSUS ITEM + CONTOH --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Khusus Item</label>
                        <textarea wire:model="items.{{ $index }}.notes" rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 resize-none"
                            placeholder="Tulis instruksi produksi untuk item ini. Contoh:&#10;- Warna merah dibuat lebih cerah dari proof&#10;- Potong die-cut mengikuti pola&#10;- Finishing: laminasi doff, jangan dilipat&#10;- Pasang grommet/lubang mata ayam setiap 50 cm"></textarea>
                        <p class="text-[11px] text-gray-400 mt-1">
                            Opsional • Maks. 300 karakter • Catatan ini tercetak di SPK dan terlihat oleh tim produksi.
                        </p>
                        @error('items.' . $index . '.notes') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
            @endforeach

            <button type="button" wire:click="addItem" class="w-full py-3 border-2 border-dashed border-blue-300 text-blue-600 rounded-lg hover:bg-blue-50 transition font-semibold flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Item Lain
            </button>

            {{-- Metode Pengiriman & Catatan Global (Tetap Sama) --}}
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">3. Metode Pengiriman</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @php $shipOptions = ['pickup' => ['🏪','Pickup','Gratis'],'regular' => ['🚚','Reguler','JNE/J&T'], 'instant' => ['⚡','Instant','GoSend/Grab']]; @endphp
                    @foreach($shipOptions as $val => $meta)
                        <label class="cursor-pointer border-2 rounded-lg p-4 flex items-center gap-3 transition {{ $shipping_method === $val ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                            <input type="radio" wire:model.live="shipping_method" value="{{ $val }}" class="hidden peer">
                            <span class="text-2xl">{{ $meta[0] }}</span>
                            <div><p class="font-semibold text-gray-900">{{ $meta[1] }}</p><p class="text-xs text-gray-500">{{ $meta[2] }}</p></div>
                        </label>
                    @endforeach
                </div>
                @if($shipping_method !== 'pickup')
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Pengiriman</label>
                        <textarea wire:model="shipping_address" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"></textarea>
                        @error('shipping_address') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                @endif
            </div>
        </div>

        {{-- ================= KOLOM KANAN (1/3): KALKULATOR ================= --}}
        <div class="lg:col-span-1">
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 sticky top-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Ringkasan Estimasi Harga Pesanan</h3>
                
                @if($estimated_price > 0)
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between pb-2 border-b border-gray-100">
                            <span class="text-gray-600">Total Harga Master:</span>
                            <span class="font-semibold text-gray-900">Rp {{ number_format($estimated_price, 0, ',', '.') }}</span>
                        </div>
                        
                        @if($total_duration_fee > 0)
                            <div class="flex justify-between text-orange-600">
                                <span>↳ Biaya percepatan (durasi):</span>
                                <span class="font-semibold">+ Rp {{ number_format($total_duration_fee, 0, ',', '.') }}</span>
                            </div>
                        @endif

                        <div class="pt-2">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Diskon (%)</label>
                            <input type="number" wire:model.live="discount_percentage" min="0" max="100" class="w-full px-3 py-1.5 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500 mb-2">
                            
                            <p class="text-xs text-gray-400 text-center my-1">atau input harga deal langsung di bawah</p>
                            
                            <label class="block text-xs font-medium text-gray-500 mb-1">Harga Deal / Final (Rp)</label>
                            <input type="number" wire:model.live="final_price_input" min="0" class="w-full px-3 py-1.5 border border-gray-300 rounded-md text-sm font-bold focus:ring-blue-500 focus:border-blue-500 mb-2">
                            @error('final_price_input') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                            
                            <button type="button" wire:click="resetToMasterPrice" class="w-full text-xs text-blue-600 hover:text-blue-800 py-1">Reset ke Harga Master</button>
                        </div>

                        @if($discount > 0)
                            <div class="p-3 bg-green-50 border border-green-200 rounded-lg">
                                <div class="flex justify-between text-green-800 font-semibold">
                                    <span>Diskon Diberikan:</span>
                                    <span>- Rp {{ number_format($discount, 0, ',', '.') }}</span>
                                </div>
                                <p class="text-xs text-green-600 mt-1 text-right">Persentase: {{ number_format($discount_percentage, 1) }}% OFF</p>
                            </div>
                        @endif

                        <div class="pt-3 border-t border-gray-200">
                            <div class="flex justify-between items-end">
                                <span class="text-gray-600 font-medium">Total Tagihan:</span>
                                <span class="text-2xl font-extrabold text-blue-700">Rp {{ number_format($final_price_input, 0, ',', '.') }}</span>
                            </div>
                            @if($discount > 0)
                                <p class="text-xs text-green-600 mt-1 text-right">✓ Hemat Rp {{ number_format($discount, 0, ',', '.') }} dari harga master</p>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="py-8 text-center text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        <p class="text-sm">Lengkapi detail item<br>untuk menampilkan rincian biaya</p>
                    </div>
                @endif

                <button type="submit" class="w-full mt-6 py-3 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed" {{ $estimated_price <= 0 ? 'disabled' : '' }}>
                    <span wire:loading.remove wire:target="save">💾 Simpan Pesanan</span>
                    <span wire:loading wire:target="save">Menyimpan...</span>
                </button>
            </div>
        </div>

    </form>
</div>