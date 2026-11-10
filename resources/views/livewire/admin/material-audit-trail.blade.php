<div class="max-w-7xl mx-auto px-4 py-8">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">🔍 Audit Trail Stok</h1>
            <p class="text-sm text-gray-500 mt-1">Jejak digital setiap perubahan stok — siapa, kapan, kenapa, berapa</p>
        </div>
        <div class="flex gap-2">
            <button wire:click="exportExcel" 
                    class="px-4 py-2 text-sm font-semibold rounded-lg bg-green-600 text-white hover:bg-green-700 transition flex items-center gap-2 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export Excel
            </button>
            <button wire:click="resetFilters" class="px-4 py-2 text-sm font-semibold rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Reset Filter
            </button>
        </div>
    </div>

    {{-- Statistik Cards - Grid yang Lebih Jelas --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-6 rounded-lg shadow-sm border-2 border-green-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-green-600 uppercase mb-1">📥 Movement Masuk</p>
                    <p class="text-3xl font-bold text-green-700">{{ $stats['in'] }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border-2 border-red-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-red-600 uppercase mb-1">📤 Movement Keluar</p>
                    <p class="text-3xl font-bold text-red-700">{{ $stats['out'] }}</p>
                </div>
                <div class="bg-red-100 p-3 rounded-full">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border-2 border-purple-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-purple-600 uppercase mb-1">⚖️ Adjustment</p>
                    <p class="text-3xl font-bold text-purple-700">{{ $stats['adj'] }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 1a9.005 9.005 0 000 18h18a9.005 9.005 0 000-18l-3 1m0 0l3 1"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Section --}}
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 mb-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-4">🔎 Filter Data</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Material</label>
                <select wire:model.live="materialId" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Material</option>
                    @foreach($materials as $m)
                        <option value="{{ $m->id }}">{{ $m->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Tipe Movement</label>
                <select wire:model.live="movementType" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Tipe</option>
                    <option value="in">📥 Masuk</option>
                    <option value="out"> Keluar</option>
                    <option value="reserve">🔒 Reservasi</option>
                    <option value="release">🔓 Release</option>
                    <option value="adjustment">⚖️ Adjustment</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Dari Tanggal</label>
                <input type="date" wire:model.live="dateFrom" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Sampai Tanggal</label>
                <input type="date" wire:model.live="dateTo" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Cari Catatan</label>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Ketik..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>
    </div>

    {{-- Tabel Movement --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b-2 border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase">Waktu</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase">Material</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 text-xs uppercase">Tipe</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 text-xs uppercase">Sub-Tipe</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 text-xs uppercase">Qty</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 text-xs uppercase">Nilai</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase">Referensi</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase">Catatan</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase">Oleh</th>
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
                                'in'=>'📥','out'=>'','reserve'=>'🔒','release'=>'','adjustment'=>'⚖️', default=>'❓',
                            };
                        @endphp
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-gray-600 whitespace-nowrap text-xs">{{ $mv->movement_date->format('d M Y H:i') }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900 text-xs">{{ $materialNames[$mv->material_id] ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 text-xs font-bold rounded-full {{ $typeColor }}">
                                    {{ $typeIcon }} {{ strtoupper($mv->movement_type) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center text-xs text-gray-600">{{ $mv->sub_type }}</td>
                            <td class="px-4 py-3 text-center font-medium text-xs">{{ number_format($mv->quantity, 2) }} {{ $mv->unit }}</td>
                            <td class="px-4 py-3 text-center text-xs text-gray-600">Rp {{ number_format($mv->total_cost ?? 0, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-xs text-gray-600">
                                @if($mv->reference_type)
                                    <span class="font-mono">{{ $mv->reference_type }} #{{ $mv->reference_id }}</span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-600 max-w-xs truncate" title="{{ $mv->notes }}">
                                {{ $mv->notes ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-600">{{ $userNames[$mv->user_id] ?? 'Sistem' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-12 text-center text-gray-400">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-lg font-medium">📭 Tidak ada movement ditemukan</p>
                                    <p class="text-sm mt-1">Coba ubah filter atau reset untuk melihat semua data</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($movements->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
                {{ $movements->links() }}
            </div>
        @endif
    </div>
</div>