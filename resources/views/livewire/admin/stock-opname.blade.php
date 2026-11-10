<div class="max-w-7xl mx-auto px-4 py-8">
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">📋 Stock Opname</h1>
            <p class="text-sm text-gray-500 mt-1">Rekonsiliasi stok fisik vs sistem — setiap koreksi tercatat di audit trail</p>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="mb-4 p-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200">{{ session('success') }}</div>
    @endif
    @if (session()->has('info'))
        <div class="mb-4 p-4 text-sm text-blue-800 rounded-lg bg-blue-50 border border-blue-200">{{ session('info') }}</div>
    @endif

    {{-- Search --}}
    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 mb-6">
        <input type="text" wire:model.live.debounce.300ms="search"
               placeholder="🔍 Cari bahan untuk opname..."
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
    </div>

    {{-- Tabel Bahan --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-8">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Bahan</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Stok Sistem</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Reserved</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Available</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($materials as $m)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900">{{ $m->name }}</div>
                                <div class="text-xs text-gray-500">{{ $m->category->name ?? '-' }} • {{ $m->unit ?? 'm2' }}</div>
                            </td>
                            <td class="px-4 py-3 text-center font-medium">{{ number_format($m->system_stock, 2) }}</td>
                            <td class="px-4 py-3 text-center text-orange-600">{{ number_format($m->stock_reserved ?? 0, 2) }}</td>
                            <td class="px-4 py-3 text-center font-bold {{ $m->available <= 0 ? 'text-red-600' : 'text-gray-900' }}">
                                {{ number_format($m->available, 2) }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button wire:click="openOpname({{ $m->id }})"
                                        class="px-4 py-2 text-xs font-semibold rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition shadow-sm">
                                    📋 Hitung Fisik
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-12 text-center text-gray-400">📭 Tidak ada bahan ditemukan</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Riwayat Opname --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-900">🗂 Riwayat Opname (10 Terakhir)</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Tanggal</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Bahan</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Selisih</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Catatan</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Oleh</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($opnameHistory as $mv)
                        @php
                            $isSurplus = str_contains((string)($mv->notes ?? ''), '+');
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $mv->movement_date->format('d M Y H:i') }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $materialNames[$mv->material_id] ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 text-xs font-bold rounded-full {{ $isSurplus ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $isSurplus ? '▲ +' : '▼ -' }}{{ number_format($mv->quantity, 2) }} {{ $mv->unit }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-600 max-w-xs truncate" title="{{ $mv->notes }}">{{ $mv->notes }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $userNames[$mv->user_id] ?? 'Sistem' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-12 text-center text-gray-400">Belum ada riwayat opname.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL OPNAME (blur consistent) --}}
    @if($showForm)
        @php
            $mat  = \App\Models\Material::find($selectedMaterialId);
            $sysQty = $mat ? (float)$mat->stock : 0;
            $diff = (float)$physical_qty - $sysQty;
        @endphp
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-md relative animate-fade-in-up">
                <div class="flex justify-between items-center p-6 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900">📋 Hitung Stok Fisik</h3>
                    <button wire:click="cancelOpname" class="text-gray-400 hover:text-red-500 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit="saveOpname" class="p-6 space-y-4">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="font-bold text-gray-900">{{ $mat->name ?? '-' }}</p>
                        <p class="text-sm text-gray-600">Satuan: {{ $mat->unit ?? 'm2' }} • Kategori: {{ $mat->category->name ?? '-' }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4 text-center">
                        <div class="bg-blue-50 p-3 rounded-lg">
                            <p class="text-xs text-blue-600 font-medium">Stok Sistem</p>
                            <p class="text-xl font-bold text-blue-800">{{ number_format($sysQty, 2) }}</p>
                        </div>
                        <div class="bg-indigo-50 p-3 rounded-lg">
                            <p class="text-xs text-indigo-600 font-medium">Hitung Fisik</p>
                            <input type="number" step="0.01" wire:model="physical_qty"
                                   class="w-full text-center text-xl font-bold text-indigo-800 bg-transparent border-none focus:ring-0 p-0">
                            @error('physical_qty') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Indikator selisih live --}}
                    <div class="p-3 rounded-lg {{ abs($diff) < 0.01 ? 'bg-green-50 border border-green-200' : ($diff > 0 ? 'bg-emerald-50 border border-emerald-200' : 'bg-red-50 border border-red-200') }}">
                        <p class="text-sm font-bold {{ abs($diff) < 0.01 ? 'text-green-700' : ($diff > 0 ? 'text-emerald-700' : 'text-red-700') }}">
                            @if(abs($diff) < 0.01)
                                ✅ Tidak ada selisih — stok sesuai
                            @elseif($diff > 0)
                                ▲ Surplus: +{{ number_format($diff, 2) }} {{ $mat->unit ?? 'm2' }}
                            @else
                                ▼ Defisit: -{{ number_format(abs($diff), 2) }} {{ $mat->unit ?? 'm2' }}
                            @endif
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan (penyebab selisih)</label>
                        <textarea wire:model="notes" rows="2"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="Misal: 2 lembar sobek saat lifting, 0.5 m² lembab, dll"></textarea>
                        @error('notes') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex gap-3 pt-4 border-t border-gray-100">
                        <button type="submit" class="flex-1 px-4 py-2.5 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition shadow-sm">💾 Simpan Adjustment</button>
                        <button type="button" wire:click="cancelOpname" class="px-4 py-2.5 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>