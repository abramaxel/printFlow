<div class="max-w-7xl mx-auto px-4 py-8">
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">🔍 Audit Trail Stok</h1>
            <p class="text-sm text-gray-500 mt-1">Jejak digital setiap perubahan stok — siapa, kapan, kenapa, berapa</p>
        </div>
        <button wire:click="resetFilters" class="px-4 py-2 text-sm font-semibold rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition">🔄 Reset Filter</button>
    </div>

    {{-- Statistik --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-green-50 p-4 rounded-lg border border-green-200 text-center">
            <p class="text-2xl font-bold text-green-700">{{ $stats['in'] }}</p>
            <p class="text-xs text-green-600">📥 Movement Masuk</p>
        </div>
        <div class="bg-red-50 p-4 rounded-lg border border-red-200 text-center">
            <p class="text-2xl font-bold text-red-700">{{ $stats['out'] }}</p>
            <p class="text-xs text-red-600">📤 Movement Keluar</p>
        </div>
        <div class="bg-purple-50 p-4 rounded-lg border border-purple-200 text-center">
            <p class="text-2xl font-bold text-purple-700">{{ $stats['adj'] }}</p>
            <p class="text-xs text-purple-600">⚖️ Adjustment (Opname)</p>
        </div>
    </div>

    {{-- Filter --}}
    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Material</label>
                <select wire:model.live="materialId" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white">
                    <option value="">Semua Material</option>
                    @foreach($materials as $m)<option value="{{ $m->id }}">{{ $m->name }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Tipe Movement</label>
                <select wire:model.live="movementType" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white">
                    <option value="">Semua Tipe</option>
                    <option value="in">📥 Masuk</option>
                    <option value="out">📤 Keluar</option>
                    <option value="reserve">🔒 Reservasi</option>
                    <option value="release">🔓 Release</option>
                    <option value="adjustment">⚖️ Adjustment</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Dari Tanggal</label>
                <input type="date" wire:model.live="dateFrom" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Sampai Tanggal</label>
                <input type="date" wire:model.live="dateTo" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Cari Catatan/Sub-type</label>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="🔍 ..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
            </div>
        </div>
    </div>

    {{-- Tabel Movement --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Waktu</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Material</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Tipe</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Sub-Tipe</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Qty</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Nilai</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Referensi</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Catatan</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Oleh</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($movements as $mv)
                        @php
                            $typeColor = match($mv->movement_type) {
                                'in'        => 'bg-green-100 text-green-800',
                                'out'       => 'bg-red-100 text-red-800',
                                'reserve'   => 'bg-orange-100 text-orange-800',
                                'release'   => 'bg-blue-100 text-blue-800',
                                'adjustment'=> 'bg-purple-100 text-purple-800',
                                default     => 'bg-gray-100 text-gray-800',
                            };
                            $typeIcon = match($mv->movement_type) {
                                'in'=>'📥','out'=>'📤','reserve'=>'🔒','release'=>'🔓','adjustment'=>'⚖️', default=>'❓',
                            };
                        @endphp
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $mv->movement_date->format('d M Y H:i') }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $materialNames[$mv->material_id] ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 text-xs font-bold rounded-full {{ $typeColor }}">{{ $typeIcon }} {{ strtoupper($mv->movement_type) }}</span>
                            </td>
                            <td class="px-4 py-3 text-center text-xs text-gray-600">{{ $mv->sub_type }}</td>
                            <td class="px-4 py-3 text-center font-medium">{{ number_format($mv->quantity, 2) }} {{ $mv->unit }}</td>
                            <td class="px-4 py-3 text-center text-xs text-gray-600">Rp {{ number_format($mv->total_cost ?? 0, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-xs text-gray-600">
                                @if($mv->reference_type){{ $mv->reference_type }} #{{ $mv->reference_id }}@else—@endif
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-600 max-w-xs truncate" title="{{ $mv->notes }}">{{ $mv->notes ?? '—' }}</td>
                            <td class="px-4 py-3 text-xs text-gray-600">{{ $userNames[$mv->user_id] ?? 'Sistem' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="px-4 py-12 text-center text-gray-400">📭 Tidak ada movement ditemukan. Coba ubah filter.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($movements->hasPages())
            <div class="px-4 py-3 border-t border-gray-200">{{ $movements->links() }}</div>
        @endif
    </div>
</div>