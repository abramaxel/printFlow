<div class="max-w-7xl mx-auto px-4 py-8">
    {{-- Header + Statistik --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">🏭 Antrian Produksi</h2>
        <p class="text-sm text-gray-500 mb-4">Kelola konfirmasi bahan & alur cetak. Diurutkan: antre-konfirmasi ➔ deadline terdekat.</p>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
            <div class="bg-white p-3 rounded-lg shadow-sm border-l-4 border-gray-400"><p class="text-[10px] uppercase font-bold text-gray-500">Total</p><p class="text-xl font-bold text-gray-800">{{ $total }}</p></div>
            <div class="bg-white p-3 rounded-lg shadow-sm border-l-4 border-yellow-500"><p class="text-[10px] uppercase font-bold text-gray-500">Antre Konfirmasi</p><p class="text-xl font-bold text-yellow-600">{{ $awaiting }}</p></div>
            <div class="bg-white p-3 rounded-lg shadow-sm border-l-4 border-blue-500"><p class="text-[10px] uppercase font-bold text-gray-500">On Process</p><p class="text-xl font-bold text-blue-600">{{ $onProcess }}</p></div>
            <div class="bg-white p-3 rounded-lg shadow-sm border-l-4 border-green-500"><p class="text-[10px] uppercase font-bold text-gray-500">Siap Cetak</p><p class="text-xl font-bold text-green-600">{{ $ready }}</p></div>
            <div class="bg-white p-3 rounded-lg shadow-sm border-l-4 border-red-500"><p class="text-[10px] uppercase font-bold text-gray-500">Tertahan Stok</p><p class="text-xl font-bold text-red-600">{{ $shortage }}</p></div>
        </div>
    </div>

    {{-- Flash --}}
    @if (session()->has('success'))
        <div class="p-3 mb-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200">{{ session('success') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="p-3 mb-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200">{{ session('error') }}</div>
    @endif

    {{-- Search + Filter --}}
    <div class="flex flex-col md:flex-row gap-3 mb-6 bg-white p-3 rounded-lg shadow-sm border border-gray-200">
        <div class="flex-1 relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"><svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg></span>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari No. Invoice / Nama Pelanggan..." class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5">
        </div>
        <select wire:model.live="statusFilter" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full md:w-56 p-2.5">
            <option value="">Semua Tahap</option>
            <option value="awaiting">🟡 Antre Konfirmasi (paid)</option>
            <option value="onprocess">🔵 On Process (processing)</option>
            <option value="ready">✅ Siap Cetak (stok aman)</option>
            <option value="shortage">⚠️ Tertahan (stok kurang)</option>
        </select>
    </div>

    {{-- List --}}
    @forelse($orders as $order)
        @php $isReady = $this->orderReady($order); @endphp
        <div class="bg-white rounded-xl shadow-sm border {{ $isReady ? 'border-gray-200' : 'border-red-200' }} mb-5 overflow-hidden">
            {{-- Header --}}
            <div class="bg-gray-50 px-5 py-3 border-b border-gray-200 flex flex-col md:flex-row justify-between md:items-center gap-2">
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h3 class="text-base font-bold text-gray-900">{{ $order->invoice_number }}</h3>
                        @if($order->status === 'paid')
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-yellow-100 text-yellow-800">SPK TERBIT – ANTRE KONFIRMASI</span>
                        @else
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-blue-100 text-blue-800">ON PROCESS</span>
                        @endif
                        @if(!$isReady)<span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-red-100 text-red-700 animate-pulse">STOK KURANG</span>@endif
                    </div>
                    <p class="text-xs text-gray-600 mt-0.5">{{ $order->user->name }} • {{ $order->created_at->format('d M Y, H:i') }}
                        @if($order->deadline_date) • 📅 Deadline <strong>{{ $order->deadline_date->format('d M Y') }}</strong>@endif
                    </p>
                </div>
                @if($order->notes)<div class="text-[11px] bg-yellow-50 text-yellow-800 px-2 py-1 rounded border border-yellow-200 max-w-xs">📝 {{ $order->notes }}</div>@endif
            </div>

            {{-- Tabel item --}}
            <div class="p-5">
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left text-gray-600">
                        <thead class="text-[10px] text-gray-700 uppercase bg-gray-100">
                            <tr>
                                <th class="px-3 py-2">Item / Spesifikasi &amp; SLA</th>
                                <th class="px-3 py-2 text-center">Butuh (m²)</th>
                                <th class="px-3 py-2 text-center">Stok Gudang</th>
                                <th class="px-3 py-2 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                @php
                                    $stock    = (float) ($item->material->stock ?? 0);
                                    $reserved = (float) ($item->material->stock_reserved ?? 0);
                                    $need     = (float) $item->area_m2;
                                    $avail    = max(0, $stock - $reserved);
                                    $ok       = $avail >= $need;
                                    $dName = $item->duration_name ?? (method_exists($item, 'duration') ? optional($item->duration)->name : null) ?? '—';
                                    $dMult = (float) ($item->duration_multiplier ?? 1);
                                @endphp
                                <tr class="border-b border-gray-100 align-top">
                                    <td class="px-3 py-3">
                                        <p class="font-semibold text-gray-900">{{ $item->service->name ?? '-' }}</p>
                                        <p class="text-[10px] text-gray-500">{{ $item->material->name ?? '-' }} • {{ $item->size->name ?? '-' }} (Qty {{ $item->quantity }})</p>
                                        <span class="inline-flex items-center px-2 py-0.5 mt-1 rounded-full text-[10px] font-bold
                                            {{ $dMult >= 2 ? 'bg-red-100 text-red-700' : ($dMult > 1 ? 'bg-orange-100 text-orange-700' : 'bg-green-100 text-green-700') }}">
                                            ⏱ {{ $dName }} (×{{ rtrim(rtrim(number_format($dMult,2,'.',''),'0'),'.') }})
                                        </span>
                                        @if($item->notes)<p class="text-[10px] italic text-gray-400 mt-1">"{{ $item->notes }}"</p>@endif
                                        @if(!empty($item->design_file))
                                            <div class="flex flex-wrap gap-1 mt-1.5">
                                                @foreach((array) $item->design_file as $f)
                                                    <a href="{{ Storage::url($f) }}" target="_blank" class="inline-flex items-center px-2 py-0.5 text-[10px] font-medium text-blue-700 bg-blue-50 border border-blue-200 rounded hover:bg-blue-100">📎 {{ Str::limit(basename($f), 18) }}</a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3 text-center font-bold text-blue-700">{{ number_format($need, 2) }}</td>
                                    <td class="px-3 py-3 text-center">
                                        <div class="text-[10px]">Total {{ number_format($stock, 2) }}</div>
                                        <div class="text-[10px] text-orange-600">Reserve {{ number_format($reserved, 2) }}</div>
                                        <div class="font-bold mt-0.5 {{ $ok ? 'text-green-600' : 'text-red-600' }}">Sisa {{ number_format($avail, 2) }}</div>
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        @if($ok)
                                            <span class="inline-flex items-center text-[10px] bg-green-100 text-green-800 px-2 py-1 rounded-full font-semibold">✓ Aman</span>
                                        @else
                                            <span class="inline-flex items-center text-[10px] bg-red-100 text-red-800 px-2 py-1 rounded-full font-semibold animate-pulse">⚠ Kurang</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Aksi (HANYA tombol, tanpa form incident) --}}
                <div class="mt-4 pt-3 border-t border-gray-100 flex flex-col sm:flex-row justify-end gap-2">
                    {{-- Tombol Lapor Kendala (TUNGGAL) --}}
                    @if(in_array($order->status, ['paid', 'processing']))
                        <button wire:click="openIncident({{ $order->id }})"
                            class="flex items-center justify-center px-5 py-2.5 text-sm font-semibold rounded-lg bg-orange-100 text-orange-700 hover:bg-orange-200 transition">
                            ⚠ Lapor Kendala
                        </button>
                    @endif

                    {{-- Tombol Konfirmasi / QC --}}
                    @if($order->status === 'paid')
                        <button
                            wire:click="confirmMaterialTakeout({{ $order->id }})"
                            @if(!$isReady) disabled @endif
                            wire:confirm="Yakin bahan fisik sudah diambil? Stok permanen berkurang & status jadi ON PROCESS."
                            wire:loading.attr="disabled" wire:target="confirmMaterialTakeout({{ $order->id }})"
                            class="flex items-center justify-center px-5 py-2.5 text-sm font-semibold rounded-lg transition shadow-sm
                                {{ $isReady ? 'bg-green-600 text-white hover:bg-green-700' : 'bg-gray-200 text-gray-400 cursor-not-allowed' }}">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span wire:loading.remove wire:target="confirmMaterialTakeout({{ $order->id }})">{{ $isReady ? 'Konfirmasi Ambil Bahan & Cetak' : 'Tertahan – Stok Kurang' }}</span>
                            <span wire:loading wire:target="confirmMaterialTakeout({{ $order->id }})">Memproses…</span>
                        </button>
                    @elseif($order->status === 'processing')
                        <button
                            wire:click="moveToQC({{ $order->id }})"
                            wire:confirm="Cetak selesai? Pindahkan ke Quality Control (QC)?"
                            wire:loading.attr="disabled" wire:target="moveToQC({{ $order->id }})"
                            class="flex items-center justify-center px-5 py-2.5 text-sm font-semibold rounded-lg bg-purple-600 text-white hover:bg-purple-700 transition shadow-sm">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span wire:loading.remove wire:target="moveToQC({{ $order->id }})">Selesai Cetak → QC</span>
                            <span wire:loading wire:target="moveToQC({{ $order->id }})">Memproses…</span>
                        </button>
                    @endif
                </div>

                {{-- FORM INCIDENT: label jelas + satuan HARI + preview proyeksi + aksi di bawah --}}
                @if($incidentFor === $order->id)
                    <div class="mt-4 p-4 bg-orange-50 border border-orange-200 rounded-lg space-y-3">
                        <div>
                            <h4 class="text-sm font-bold text-orange-800">⚠ Lapor Kendala Produksi — {{ $order->invoice_number }}</h4>
                            <p class="text-[11px] text-orange-600 mt-0.5">Laporan masuk ke Monitor Produksi Owner beserta estimasi keterlambatan.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            {{-- Jenis kendala --}}
                            <div>
                                <label class="block mb-1 text-xs font-semibold text-orange-800">Jenis Kendala</label>
                                <select wire:model="incidentType" class="w-full bg-white border border-orange-300 text-sm rounded-lg p-2.5">
                                    @foreach(\App\Models\ProductionIncident::TYPES as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Estimasi dampak: SATUAN HARI, ditulis eksplisit --}}
                            <div>
                                <label class="block mb-1 text-xs font-semibold text-orange-800">Estimasi Tambahan Keterlambatan</label>
                                <div class="relative">
                                    <input type="number" wire:model.live="incidentImpact" min="0" max="30"
                                        class="w-full bg-white border border-orange-300 text-sm rounded-lg p-2.5 pr-16 font-bold">
                                    <span class="absolute right-3 top-2.5 text-xs font-bold text-orange-600 uppercase">Hari</span>
                                </div>
                                <p class="text-[10px] text-orange-600 mt-1">
                                    Isi <strong>2</strong> = kendala menambah <strong>2 hari</strong> dari deadline. Isi <strong>0</strong> = tidak mempengaruhi deadline.
                                </p>
                            </div>
                        </div>

                        {{-- Preview proyeksi: membuat arti angka LANGSUNG terlihat --}}
                        @if($order->deadline_date && (int) $incidentImpact > 0)
                            <div class="text-[11px] text-orange-700 bg-orange-100 border border-orange-200 rounded-lg p-2.5">
                                📅 Proyeksi deadline bila kendala ini nyata:
                                <strong>{{ $order->deadline_date->copy()->addDays((int) $incidentImpact)->format('d M Y') }}</strong>
                                <span class="text-orange-500">(semula {{ $order->deadline_date->format('d M Y') }})</span>
                                <span class="block mt-0.5 text-orange-500">Proyeksi hanya informasi — revisi deadline tetap hak Owner di Monitor Produksi.</span>
                            </div>
                        @endif

                        {{-- Deskripsi --}}
                        <div>
                            <label class="block mb-1 text-xs font-semibold text-orange-800">Penjelasan Kendala <span class="text-red-500">*</span></label>
                            <textarea wire:model="incidentDesc" rows="2" class="w-full bg-white border border-orange-300 text-sm rounded-lg p-2.5" placeholder="Contoh: Mesin cetak utama rusak, sparepart harus dipesan dulu..."></textarea>
                            @error('incidentDesc') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Baris aksi: Batal & Kirim berdampingan di kanan bawah (pola form standar) --}}
                        <div class="flex justify-end gap-2 pt-1 border-t border-orange-200">
                            <button wire:click="cancelIncident"
                                class="px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 transition">
                                Batal
                            </button>
                            <button wire:click="submitIncident({{ $order->id }})"
                                wire:loading.attr="disabled" wire:target="submitIncident({{ $order->id }})"
                                class="px-4 py-2 bg-orange-600 text-white text-sm font-semibold rounded-lg hover:bg-orange-700 transition">
                                <span wire:loading.remove wire:target="submitIncident({{ $order->id }})">📨 Kirim Laporan ke Owner</span>
                                <span wire:loading wire:target="submitIncident({{ $order->id }})">Mengirim…</span>
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @empty
        <div class="text-center py-16 bg-white rounded-xl border border-dashed border-gray-300">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            <p class="text-gray-500 font-medium">Antrian kosong 🎉</p>
            <p class="text-gray-400 text-sm">Tidak ada pesanan pada tahap konfirmasi/produksi.</p>
        </div>
    @endforelse
</div>