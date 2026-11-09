<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">📥 Penerimaan Barang (Goods Receipt)</h1>
        <p class="text-sm text-gray-500 mt-1">Catat barang masuk dari supplier — stok, batch FIFO, dan BPB terbentuk otomatis</p>
    </div>

    @if (session()->has('success'))
        <div class="mb-4 p-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        {{-- FORM PENERIMAAN --}}
        <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Form Penerimaan</h2>
            <form wire:submit="save" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Supplier *</label>
                        <select wire:model="supplier_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white">
                            <option value="">-- Pilih Supplier --</option>
                            @foreach($suppliers as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                        @error('supplier_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Terima *</label>
                        <input type="date" wire:model="receipt_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        @error('receipt_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">No. Surat Jalan Supplier</label>
                        <input type="text" wire:model="supplier_doc_number" placeholder="SJ-ABC-789" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Link ke PR (opsional)</label>
                        <select wire:model="purchase_request_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white">
                            <option value="">-- Pembelian langsung (tanpa PR) --</option>
                            @foreach($openPrs as $pr)
                                <option value="{{ $pr->id }}">{{ $pr->pr_number }} — {{ $pr->material->name ?? '' }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- ITEM BARANG --}}
                <div class="border-t border-gray-200 pt-4">
                    <div class="flex justify-between items-center mb-2">
                        <label class="text-sm font-bold text-gray-700">Daftar Barang Diterima *</label>
                        <button type="button" wire:click="addItem" class="text-xs font-semibold text-blue-600 hover:text-blue-800">+ Tambah Baris</button>
                    </div>
                    @foreach($items as $i => $item)
                        <div class="grid grid-cols-12 gap-2 mb-2 items-end">
                            <div class="col-span-4">
                                <label class="block text-xs text-gray-500 mb-1">Material</label>
                                <select wire:model="items.{{ $i }}.material_id" class="w-full px-2 py-2 border border-gray-300 rounded-lg text-sm bg-white">
                                    <option value="">-- Pilih --</option>
                                    @foreach($materials as $m)
                                        <option value="{{ $m->id }}">{{ $m->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs text-gray-500 mb-1">Qty ({{ $materials->firstWhere('id', $item['material_id'])?->unit ?? 'unit' }})</label>
                                <input type="number" step="0.01" wire:model="items.{{ $i }}.quantity" class="w-full px-2 py-2 border border-gray-300 rounded-lg text-sm">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs text-gray-500 mb-1">Harga/Unit</label>
                                <input type="number" step="0.01" wire:model="items.{{ $i }}.unit_cost" class="w-full px-2 py-2 border border-gray-300 rounded-lg text-sm">
                            </div>
                            <div class="col-span-3">
                                <label class="block text-xs text-gray-500 mb-1">Kadaluarsa (opsional)</label>
                                <input type="date" wire:model="items.{{ $i }}.expiry_date" class="w-full px-2 py-2 border border-gray-300 rounded-lg text-sm">
                            </div>
                            <div class="col-span-1 text-center">
                                <button type="button" wire:click="removeItem({{ $i }})" class="px-2 py-2 text-red-500 hover:bg-red-50 rounded-lg">🗑️</button>
                            </div>
                        </div>
                        @error('items.'.$i.'.material_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        @error('items.'.$i.'.quantity') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    @endforeach
                </div>

                {{-- UPLOAD DOKUMEN --}}
                <div class="grid grid-cols-2 gap-4 border-t border-gray-200 pt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">📎 Scan Surat Jalan (opsional)</label>
                        <input type="file" wire:model="delivery_note_file" class="w-full text-sm border border-gray-300 rounded-lg p-2">
                        @error('delivery_note_file') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">📎 Scan Faktur Supplier (opsional)</label>
                        <input type="file" wire:model="invoice_file" class="w-full text-sm border border-gray-300 rounded-lg p-2">
                        @error('invoice_file') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                    <textarea wire:model="notes" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Kondisi barang, selisih, dll"></textarea>
                </div>

                <button type="submit" wire:loading.attr="disabled" class="w-full px-4 py-3 bg-green-600 text-white font-bold rounded-lg hover:bg-green-700 transition">
                    ✅ Simpan Penerimaan & Update Stok
                </button>
            </form>
        </div>

        {{-- PANEL INFO PR TERBUKA --}}
        <div class="bg-white p-6 rounded-lg shadow-sm border border-blue-200">
            <h2 class="text-lg font-bold text-blue-700 mb-4">📋 PR Menunggu Penerimaan</h2>
            @forelse($openPrs as $pr)
                <div class="p-3 mb-2 bg-blue-50 rounded-lg border-l-4 border-blue-500">
                    <p class="font-medium text-gray-900 text-sm">{{ $pr->material->name ?? '-' }}</p>
                    <p class="text-xs text-gray-600">{{ number_format($pr->requested_quantity, 1) }} {{ $pr->unit }} • {{ strtoupper($pr->status) }}</p>
                    <a href="{{ route('admin.goods-receipt', ['pr_id' => $pr->id]) }}" class="text-xs font-semibold text-blue-600 hover:underline">Isi form dari PR ini →</a>
                </div>
            @empty
                <p class="text-sm text-gray-400 text-center py-6">Tidak ada PR terbuka.</p>
            @endforelse
        </div>
    </div>

    {{-- RIWAYAT PENERIMAAN --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-900">🗂 Riwayat Penerimaan (10 Terakhir)</h2>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">No. BPB</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Tanggal</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Supplier</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Item</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Total</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">BPB</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($receipts as $r)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono text-xs">{{ $r->bpb_number }}</td>
                        <td class="px-4 py-3">{{ $r->receipt_date->format('d M Y') }}</td>
                        <td class="px-4 py-3">{{ $r->supplier->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-600">
                            @foreach($r->items as $it)
                                {{ $it->material->name ?? '-' }} ({{ number_format($it->quantity, 1) }} {{ $it->unit }})@if(!$looplast = false){{ '' }}@endif{{ !$loop->last ? ', ' : '' }}
                            @endforeach
                        </td>
                        <td class="px-4 py-3 text-center font-medium">Rp {{ number_format($r->total_cost, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('admin.purchase-receipts.pdf', $r->id) }}" target="_blank" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200">🖨️ PDF</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-12 text-center text-gray-400">Belum ada penerimaan barang tercatat.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>