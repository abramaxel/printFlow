<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">👥 CRM Pelanggan</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola data pelanggan, loyalitas, dan nilai transaksi</p>
    </div>

    @if (session()->has('success'))
        <div class="mb-4 p-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200">{{ session('success') }}</div>
    @endif

    {{-- Statistik Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
            <p class="text-sm text-gray-500">Total Pelanggan</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] ?? 0 }}</p>
        </div>
        <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
            <p class="text-sm text-purple-600">VIP Members</p>
            <p class="text-2xl font-bold text-purple-700">{{ $stats['vip'] ?? 0 }}</p>
        </div>
        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
            <p class="text-sm text-gray-600">Non-Aktif</p>
            <p class="text-2xl font-bold text-gray-700">{{ $stats['inactive'] ?? 0 }}</p>
        </div>
        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
            <p class="text-sm text-green-600">💰 Omzet Terbayar</p>
            <p class="text-xl font-bold text-green-700">Rp {{ number_format($stats['omzet'] ?? 0, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Search & Filter --}}
    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input type="text" wire:model.live.debounce.300ms="search"
                   placeholder="🔍 Cari nama atau email..."
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            <select wire:model.live="filterTier" class="px-4 py-2 border border-gray-300 rounded-lg bg-white">
                <option value="">Semua Tier</option>
                <option value="regular">Regular</option>
                <option value="silver">Silver</option>
                <option value="gold">Gold</option>
                <option value="vip">VIP</option>
                <option value="inactive">Non-Aktif</option>
            </select>
        </div>
    </div>

    {{-- Tabel Pelanggan --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Nama</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Email</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Total Order</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Total Belanja</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Tier</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($customers as $customer)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $customer->name }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $customer->email }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-blue-100 text-blue-800">
                                    {{ (int) ($customer->orders_count ?? 0) }} order
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center font-semibold text-green-700">
                                Rp {{ number_format((float) ($customer->total_spent ?? 0), 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full
                                    @if($customer->tier === 'vip') bg-purple-100 text-purple-800
                                    @elseif($customer->tier === 'gold') bg-yellow-100 text-yellow-800
                                    @elseif($customer->tier === 'silver') bg-gray-200 text-gray-700
                                    @else bg-gray-100 text-gray-600 @endif">
                                    {{ ucfirst($customer->tier ?? 'regular') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button wire:click="openNoteModal({{ $customer->id }})"
                                        class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-blue-100 text-blue-700 hover:bg-blue-200 transition">
                                    ✏️ Edit
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-gray-400">Tidak ada data pelanggan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($customers->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">{{ $customers->links() }}</div>
        @endif
    </div>

    {{-- Modal Edit Note & Tier --}}
    @if($showNoteModal)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-lg p-6">
                <h3 class="text-lg font-bold mb-4">📝 Edit Catatan & Tier Pelanggan</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Tier Pelanggan</label>
                        <select wire:model="newTier" class="w-full px-3 py-2 border rounded-lg bg-white">
                            <option value="regular">Regular</option>
                            <option value="silver">Silver</option>
                            <option value="gold">Gold</option>
                            <option value="vip">VIP</option>
                            <option value="inactive">Non-Aktif</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Catatan Internal</label>
                        <textarea wire:model="noteContent" rows="4"
                                  class="w-full px-3 py-2 border rounded-lg"
                                  placeholder="Catatan tentang pelanggan ini..."></textarea>
                    </div>
                </div>
                <div class="flex gap-3 mt-6">
                    <button wire:click="saveNoteAndTier"
                            class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">💾 Simpan</button>
                    <button wire:click="closeNoteModal"
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Batal</button>
                </div>
            </div>
        </div>
    @endif
</div>