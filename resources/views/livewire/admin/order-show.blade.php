<div class="max-w-7xl mx-auto px-4 py-8">
    {{-- ============ HEADER ============ --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Detail Pesanan</h2>
            <p class="text-sm text-gray-500 mt-1">
                Invoice: <span class="font-mono font-bold text-blue-600">{{ $order->invoice_number }}</span>
                <span class="ml-3 px-2 py-1 text-xs font-semibold rounded-full
                    {{ $order->status == 'completed' ? 'bg-green-100 text-green-800' : ($order->status == 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
                    {{ strtoupper(str_replace('_', ' ', $order->status)) }}
                </span>
            </p>
        </div>
        <a href="{{ route('admin.orders') }}" class="text-sm text-gray-600 hover:text-blue-600 flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Daftar
        </a>
    </div>

    @if (session()->has('success'))
        <div class="p-4 mb-6 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200">{{ session('success') }}</div>
    @endif

    @if (session()->has('whatsapp_success'))
        <div class="p-4 mb-6 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200">{{ session('whatsapp_success') }}</div>
    @endif

    @if (session()->has('whatsapp_error'))
        <div class="p-4 mb-6 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200">{{ session('whatsapp_error') }}</div>
    @endif

    {{-- ============ SECTION 1: INFO PELANGGAN + RINGKASAN TAGIHAN ============ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                Informasi Pelanggan
            </h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div><p class="text-gray-500 text-xs">Nama Pelanggan</p><p class="font-medium text-gray-900">{{ $order->user->name }}</p></div>
                <div><p class="text-gray-500 text-xs">Email</p><p class="font-medium text-gray-900">{{ $order->user->email }}</p></div>
                <div><p class="text-gray-500 text-xs">Tanggal Order</p><p class="font-medium text-gray-900">{{ $order->created_at->format('d M Y, H:i') }}</p></div>
                <div><p class="text-gray-500 text-xs">Catatan Order</p><p class="font-medium text-gray-900">{{ $order->notes ?: '-' }}</p></div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100">
                <label class="block text-xs font-medium text-gray-700 mb-1">No. WhatsApp (Untuk Kirim Link)</label>
                <div class="flex space-x-2">
                    <input type="text" wire:model="customer_phone" placeholder="0812xxxxxxx" class="flex-1 px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                    <x-ui.button variant="primary" size="sm" wire:click="updateCustomerPhone">Simpan</x-ui.button>
                </div>
                @error('customer_phone') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Ringkasan Tagihan
            </h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">Subtotal Item:</span><span class="font-medium">Rp {{ number_format($order->items->sum('final_subtotal'), 0, ',', '.') }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Ongkir ({{ ucfirst($order->shipping_method ?? 'pickup') }}):</span><span class="font-medium">Rp {{ number_format($order->invoice?->shipping_cost ?? 0, 0, ',', '.') }}</span></div>
                <div class="pt-3 border-t border-gray-200 flex justify-between text-lg font-bold text-gray-900">
                    <span>Total:</span>
                    <span class="text-blue-600">Rp {{ number_format($order->invoice?->total_amount ?? $order->items->sum('final_subtotal'), 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center pt-2">
                    <span class="text-xs text-gray-500">Status:</span>
                    <span class="px-2 py-1 text-xs font-bold rounded-full {{ ($order->invoice?->status ?? '') == 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ strtoupper($order->invoice?->status ?? 'BELUM ADA') }}
                    </span>
                </div>
                <p class="text-[10px] text-gray-400">*Harga sudah termasuk pajak (PPN).</p>
            </div>
        </div>
    </div>

    {{-- ============ SECTION 2: DETAIL PESANAN & RINCIAN BIAYA ============ --}}
    @php $bd = \App\Models\Invoice::costBreakdown($order); @endphp
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-1 flex items-center">
            <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
            Detail Pesanan & Rincian Biaya
        </h3>
        <p class="text-xs text-gray-500 mb-4">Harga sudah termasuk pajak (PPN). Rincian per baris mengikuti standar line-item e-invoice.</p>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-gray-600">
                <thead class="text-[10px] text-gray-700 uppercase bg-gray-100">
                    <tr>
                        <th class="px-3 py-2">Item / Spesifikasi</th>
                        <th class="px-3 py-2 text-center">Qty × Luas</th>
                        <th class="px-3 py-2 text-right">Satuan (L+B)/m²</th>
                        <th class="px-3 py-2 text-right">Harga Dasar</th>
                        <th class="px-3 py-2 text-right">Biaya Durasi (SLA)</th>
                        <th class="px-3 py-2 text-right">Master</th>
                        <th class="px-3 py-2 text-right">Diskon</th>
                        <th class="px-3 py-2 text-right">Final</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $i => $item)
                        @php $line = $bd['lines'][$i] ?? null; @endphp
                        <tr class="border-b border-gray-100 align-top">
                            <td class="px-3 py-3">
                                <p class="font-semibold text-gray-900 text-sm">{{ $item->service->name }}</p>
                                <p class="text-[10px] text-gray-500">{{ $item->material->name }} • {{ $item->size->name }}</p>
                                <p class="text-[10px] text-orange-600 mt-0.5">⏱ {{ $line['duration'] ?? 'Normal' }} (×{{ rtrim(rtrim(number_format($line['multiplier'] ?? 1, 2, '.', ''), '0'), '.') }})</p>
                                @if($item->notes)<p class="text-[10px] italic text-gray-400 mt-0.5">"{{ $item->notes }}"</p>@endif
                                @if(!empty($item->design_file))
                                    <div class="flex flex-wrap gap-1 mt-1.5">
                                        @foreach((array) $item->design_file as $file)
                                            <a href="{{ Storage::url($file) }}" target="_blank" class="inline-flex items-center px-2 py-0.5 text-[10px] font-medium text-blue-700 bg-blue-50 border border-blue-200 rounded hover:bg-blue-100 transition">
                                                📎 {{ Str::limit(basename($file), 22) }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-center">{{ $item->quantity }} × {{ number_format($item->area_m2, 2) }} m²</td>
                            <td class="px-3 py-3 text-right">
                                {{ number_format($line['unit_combined'] ?? 0, 0, ',', '.') }}
                                <span class="block text-[9px] text-gray-400">L: {{ number_format($item->unit_price_service, 0, ',', '.') }} + B: {{ number_format($item->unit_price_material, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-3 py-3 text-right">{{ number_format($line['base'] ?? 0, 0, ',', '.') }}</td>
                            <td class="px-3 py-3 text-right text-orange-600">+ {{ number_format($line['fee'] ?? 0, 0, ',', '.') }}</td>
                            <td class="px-3 py-3 text-right font-medium">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            <td class="px-3 py-3 text-right text-red-500">- {{ number_format($item->discount, 0, ',', '.') }}</td>
                            <td class="px-3 py-3 text-right font-bold text-gray-900">{{ number_format($item->final_subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="text-xs font-semibold bg-gray-50">
                    <tr>
                        <td colspan="3" class="px-3 py-2 text-right">Total Harga Dasar:</td>
                        <td class="px-3 py-2 text-right">{{ number_format($bd['total_base'], 0, ',', '.') }}</td>
                        <td class="px-3 py-2 text-right text-orange-600">+ {{ number_format($bd['total_fee'], 0, ',', '.') }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($bd['total_master'], 0, ',', '.') }}</td>
                        <td class="px-3 py-2 text-right text-red-500">- {{ number_format($bd['total_discount'], 0, ',', '.') }}</td>
                        <td class="px-3 py-2 text-right text-blue-700">{{ number_format($bd['total_final'], 0, ',', '.') }}</td>
                    </tr>
                    @php
                        $shipping = $order->invoice ? (float) $order->invoice->shipping_cost : 0;
                        $grand = $order->invoice ? (float) $order->invoice->total_amount : $bd['total_final'];
                    @endphp
                    <tr>
                        <td colspan="7" class="px-3 py-2 text-right">Ongkos Kirim ({{ ucfirst($order->shipping_method ?? 'pickup') }}):</td>
                        <td class="px-3 py-2 text-right">{{ number_format($shipping, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="text-sm">
                        <td colspan="7" class="px-3 py-2 text-right font-bold text-gray-800">GRAND TOTAL:</td>
                        <td class="px-3 py-2 text-right font-extrabold text-blue-700">Rp {{ number_format($grand, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="mt-3 flex flex-wrap gap-4 text-xs text-gray-500">
            <span>📅 Estimasi selesai produksi: <strong>{{ $order->deadline_date ? $order->deadline_date->format('d M Y') : '-' }}</strong></span>
            <span>🚚 Metode kirim: <strong>{{ ucfirst($order->shipping_method ?? 'pickup') }}</strong></span>
        </div>
    </div>

    {{-- ============ SECTION 3: PENAGIHAN (TERPISAH) ============ --}}
    <div class="mb-6">
        @livewire('admin.order-finance', ['order' => $order])
    </div>

    {{-- ============ SECTION PENGIRIMAN & RESI ============ --}}
    @php
        $invoice = $order->invoice;
        $isPaid = $invoice && ($invoice->status === 'paid' || (float) $invoice->total_paid >= (float) $invoice->total_amount);
    @endphp

    @if(in_array($order->status, ['qc', 'shipping', 'completed']))
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 001 1H15m-2-4a1 1 0 011-1h1a1 1 0 011 1v4a1 1 0 01-1 1h-1a1 1 0 01-1-1v-4zM7 20a2 2 0 100-4 2 2 0 000 4zm10 0a2 2 0 100-4 2 2 0 000 4z"></path>
            </svg>
            Pengiriman & Resi
        </h3>

        {{-- Info Pengiriman --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div class="p-4 bg-gray-50 rounded-lg">
                <p class="text-xs text-gray-500 mb-1">Metode Pengiriman</p>
                <p class="font-semibold text-gray-900">{{ ucfirst($order->shipping_method ?? 'pickup') }}</p>
                @if($order->shipping_method !== 'pickup' && $order->shipping_address)
                    <p class="text-sm text-gray-600 mt-2">{{ $order->shipping_address }}</p>
                @endif
            </div>
            <div class="p-4 bg-gray-50 rounded-lg">
                <p class="text-xs text-gray-500 mb-1">Status Pembayaran</p>
                @if($isPaid)
                    <p class="font-semibold text-green-600">✅ LUNAS</p>
                    @if($invoice)
                        <p class="text-xs text-gray-500 mt-1">Rp {{ number_format($invoice->total_paid ?? 0, 0, ',', '.') }} / Rp {{ number_format($invoice->total_amount ?? 0, 0, ',', '.') }}</p>
                    @endif
                @else
                    <p class="font-semibold text-red-600">⚠️ BELUM LUNAS</p>
                    @if($invoice)
                        <p class="text-xs text-gray-500 mt-1">Sisa: Rp {{ number_format(($invoice->total_amount ?? 0) - ($invoice->total_paid ?? 0), 0, ',', '.') }}</p>
                    @endif
                @endif
            </div>
        </div>

        {{-- Input Resi (hanya muncul jika status qc atau shipping) --}}
        @if(in_array($order->status, ['qc', 'shipping']))
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Nomor Resi / Tracking Number <span class="text-red-500">*</span>
            </label>
            <input type="text" wire:model="tracking_number" 
                placeholder="Contoh: JNE123456789ID, SICEPAT123456789"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            <p class="text-xs text-gray-500 mt-1">Masukkan nomor resi dari jasa pengiriman (JNE, J&T, SiCepat, dll)</p>
            @error('tracking_number') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        @endif

        {{-- Tampilkan Resi jika sudah ada --}}
        @if($order->tracking_number)
        <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <p class="text-sm text-blue-800">
                <span class="font-semibold">📦 Nomor Resi:</span> 
                <span class="font-bold text-lg">{{ $order->tracking_number }}</span>
            </p>
            @if($order->shipped_at)
                <p class="text-xs text-blue-600 mt-1">Dikirim pada: {{ \Carbon\Carbon::parse($order->shipped_at)->format('d M Y, H:i') }}</p>
            @endif
        </div>
        @endif

        {{-- Tombol Aksi Cepat --}}
        <div class="flex gap-3">
            @if($order->status === 'qc')
                <button type="button" 
                    wire:click="$set('new_status', 'shipping')" 
                    onclick="setTimeout(() => document.getElementById('status-form').requestSubmit(), 100)"
                    class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition">
                    🚚 Kirim Pesanan & Kirim Resi
                </button>
            @endif
            
            @if($order->status === 'shipping')
                <button type="button" 
                    wire:click="$set('new_status', 'completed')" 
                    onclick="setTimeout(() => document.getElementById('status-form').requestSubmit(), 100)"
                    class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition">
                    ✅ Konfirmasi Diterima
                </button>
            @endif

            @if($order->status === 'completed')
                <div class="px-4 py-2 bg-green-100 text-green-700 text-sm font-semibold rounded-lg">
                    ✅ PESANAN SELESAI — Invoice Dikunci Permanen
                </div>
            @endif
        </div>

        {{-- Override untuk Owner (hanya muncul jika belum lunas) --}}
        @if(!$isPaid && in_array(auth()->user()->role, ['owner', 'super_admin']) && $order->status === 'qc')
            <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <label class="flex items-center">
                    <input type="checkbox" wire:model="allow_unpaid_shipping" class="rounded border-gray-300 text-yellow-600 shadow-sm focus:border-yellow-300 focus:ring focus:ring-yellow-200 focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-yellow-800 font-semibold">
                        ⚠️ Izinkan Kirim Tanpa Lunas (Owner Override)
                    </span>
                </label>
                <p class="text-xs text-yellow-700 mt-1">Hanya untuk kasus mendesak. Akan dicatat di riwayat pesanan.</p>
            </div>
        @endif
    </div>
    @endif

    {{-- ============ SECTION 4: STATUS PRODUKSI + TIMELINE ============ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                Update Status Produksi
            </h3>
            
            {{-- FORM DENGAN ID AGAR BISA DI-TRIGGER OLEH TOMBOL AKSI CEPAT DI ATAS --}}
            <form id="status-form" wire:submit="updateStatus">
                <div class="mb-4">
                    <label class="block mb-2 text-sm font-medium text-gray-900">Status Baru</label>
                    <select id="status-select" wire:model="new_status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        @foreach($available_statuses as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('new_status') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="mb-4">
                    <label class="block mb-2 text-sm font-medium text-gray-900">Catatan (Opsional)</label>
                    <textarea wire:model="status_notes" rows="3" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Contoh: Bahan sudah ready, mulai cetak..."></textarea>
                </div>
                
                {{-- Hidden inputs agar data ikut terkirim saat form disubmit --}}
                <input type="hidden" wire:model="tracking_number">
                <input type="hidden" wire:model="allow_unpaid_shipping">
                
                {{-- SATU TOMBOL SIMPAN YANG RAPI --}}
                <x-ui.button type="submit" variant="primary" class="w-full justify-center">
                    Simpan Perubahan
                </x-ui.button>
            </form>
        </div>

        <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Riwayat Perubahan (Order Track)
            </h3>
            <ol class="relative border-l-2 border-gray-200 ml-3">
                @forelse($order->histories as $history)
                    <li class="mb-6 ml-6">
                        <div class="absolute w-4 h-4 bg-blue-600 rounded-full -left-2.5 border-4 border-white shadow"></div>
                        <time class="mb-1 text-xs font-normal text-gray-400">{{ $history->created_at->format('d M Y, H:i') }} ({{ $history->created_at->diffForHumans() }})</time>
                        <h3 class="text-sm font-semibold text-gray-900">
                            {{ str_replace('_', ' ', ucfirst($history->old_status)) }}
                            <span class="text-gray-400 mx-2">➔</span>
                            <span class="text-blue-600">{{ str_replace('_', ' ', ucfirst($history->new_status)) }}</span>
                        </h3>
                        @if($history->notes)<p class="mt-1 text-xs text-gray-600 bg-gray-50 p-2 rounded italic">"{{ $history->notes }}"</p>@endif
                        <p class="text-xs text-gray-400 mt-1">Oleh: {{ $history->user->name }}</p>
                    </li>
                @empty
                    <li class="text-sm text-gray-500 italic py-4 text-center">Belum ada riwayat perubahan status.</li>
                @endforelse
            </ol>
        </div>
    </div>
</div>