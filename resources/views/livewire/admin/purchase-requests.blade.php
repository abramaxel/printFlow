<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">📋 Purchase Request</h1>
            <p class="text-sm text-gray-500 mt-1">Permintaan pembelian dari MRP — approve, tolak, atau terima barang</p>
        </div>
        <select wire:model.live="statusFilter" class="px-4 py-2 border border-gray-300 rounded-lg text-sm">
            <option value="">Semua Status</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="received">Received</option>
            <option value="cancelled">Cancelled</option>
        </select>
    </div>

    @if (session()->has('success'))
        <div class="mb-4 p-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200">{{ session('success') }}</div>
    @endif
    @if (session()->has('warning'))
        <div class="mb-4 p-4 text-sm text-yellow-800 rounded-lg bg-yellow-50 border border-yellow-200">{{ session('warning') }}</div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">No. PR</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Material</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Qty Diminta</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Estimasi Biaya</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Urgency</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Status</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($prs as $pr)
                    @php
                        $urgencyColor = match($pr->urgency) {
                            'critical' => 'bg-red-100 text-red-800',
                            'warning' => 'bg-yellow-100 text-yellow-800',
                            default => 'bg-blue-100 text-blue-800',
                        };
                        $statusColor = match($pr->status) {
                            'pending' => 'bg-orange-100 text-orange-800',
                            'approved' => 'bg-blue-100 text-blue-800',
                            'received' => 'bg-green-100 text-green-800',
                            default => 'bg-gray-100 text-gray-600',
                        };
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $pr->pr_number }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900">
                            {{ $pr->material->name ?? '-' }}
                            <div class="text-xs text-gray-500 font-normal">{{ $pr->reason }}</div>
                        </td>
                        <td class="px-4 py-3 text-center">{{ number_format($pr->requested_quantity, 1) }} {{ $pr->unit }}</td>
                        <td class="px-4 py-3 text-center">Rp {{ number_format($pr->estimated_cost, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center"><span class="px-2.5 py-1 text-xs font-bold rounded-full {{ $urgencyColor }}">{{ strtoupper($pr->urgency) }}</span></td>
                        <td class="px-4 py-3 text-center"><span class="px-2.5 py-1 text-xs font-bold rounded-full {{ $statusColor }}">{{ strtoupper($pr->status) }}</span></td>
                        <td class="px-4 py-3">
                            <div class="flex justify-center gap-2">
                                @if($pr->status === 'pending')
                                    <button wire:click="approve({{ $pr->id }})" wire:confirm="Setujui {{ $pr->pr_number }}?" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-green-600 text-white hover:bg-green-700">✅ Approve</button>
                                    <button wire:click="reject({{ $pr->id }})" wire:confirm="Batalkan {{ $pr->pr_number }}?" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-red-100 text-red-700 hover:bg-red-200">❌ Tolak</button>
                                @elseif($pr->status === 'approved')
                                    <a href="{{ route('admin.goods-receipt', ['pr_id' => $pr->id]) }}" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-blue-600 text-white hover:bg-blue-700">📥 Terima Barang</a>
                                @else
                                    <span class="text-xs text-gray-400">—</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-12 text-center text-gray-400">Tidak ada Purchase Request.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>