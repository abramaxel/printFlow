<div wire:poll.60s class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Dashboard Produksi</h1>
            <p class="text-sm text-gray-500 mt-1">Pantau antrian lantai produksi, QC, dan kesiapan bahan — bukan metrik keuangan.</p>
        </div>
        <div class="flex gap-2">
            <button wire:click="$refresh" class="px-4 py-2 text-sm font-semibold rounded-lg bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 transition">
                🔄 Refresh
            </button>
            <a href="{{ route('admin.production') }}" class="px-4 py-2 text-sm font-semibold rounded-lg bg-green-600 text-white hover:bg-green-700 transition">
                🏭 Buka Antrian Produksi
            </a>
        </div>
    </div>

    {{-- KPI produksi --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-lg">📥</span>
                <div>
                    <p class="text-xs text-gray-500">Siap Produksi</p>
                    <p class="text-xl font-bold text-gray-900">{{ $stages['ready'] }}</p>
                </div>
            </div>
            <p class="text-[11px] text-gray-400 mt-2">Sudah dibayar, menunggu mulai cetak.</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center text-lg">🖨️</span>
                <div>
                    <p class="text-xs text-gray-500">Sedang Diproses</p>
                    <p class="text-xl font-bold text-gray-900">{{ $stages['processing'] }}</p>
                </div>
            </div>
            <p class="text-[11px] text-gray-400 mt-2">SPK aktif di mesin / pengerjaan.</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center text-lg">🔬</span>
                <div>
                    <p class="text-xs text-gray-500">Quality Control</p>
                    <p class="text-xl font-bold text-gray-900">{{ $stages['qc'] }}</p>
                </div>
            </div>
            <p class="text-[11px] text-gray-400 mt-2">Menunggu pemeriksaan hasil cetak.</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-lg">🚚</span>
                <div>
                    <p class="text-xs text-gray-500">Siap Kirim / Pickup</p>
                    <p class="text-xl font-bold text-gray-900">{{ $stages['shipping'] }}</p>
                </div>
            </div>
            <p class="text-[11px] text-gray-400 mt-2">Lolos QC, menunggu diserahkan.</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-lg">✅</span>
                <div>
                    <p class="text-xs text-gray-500">Selesai Hari Ini</p>
                    <p class="text-xl font-bold text-gray-900">{{ $stages['done_today'] }}</p>
                </div>
            </div>
            <p class="text-[11px] text-gray-400 mt-2">Output produksi tanggal {{ now()->format('d M Y') }}.</p>
        </div>
    </div>

    {{-- Pipeline bar --}}
    @php
        $pipelineTotal = max(1, $stages['ready'] + $stages['processing'] + $stages['qc'] + $stages['shipping']);
        $seg = fn($n) => round($n / $pipelineTotal * 100, 1);
    @endphp
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-sm font-semibold text-gray-800">Alur Antrian Aktif</h2>
            <span class="text-xs text-gray-400">{{ $pipelineTotal }} pesanan dalam pipeline</span>
        </div>
        <div class="flex h-3 rounded-full overflow-hidden bg-gray-100">
            <div class="bg-blue-500" style="width: {{ $seg($stages['ready']) }}%"></div>
            <div class="bg-amber-500" style="width: {{ $seg($stages['processing']) }}%"></div>
            <div class="bg-purple-500" style="width: {{ $seg($stages['qc']) }}%"></div>
            <div class="bg-indigo-500" style="width: {{ $seg($stages['shipping']) }}%"></div>
        </div>
        <div class="flex flex-wrap gap-4 mt-3 text-xs text-gray-600">
            <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span> Siap ({{ $stages['ready'] }})</span>
            <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Proses ({{ $stages['processing'] }})</span>
            <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-purple-500"></span> QC ({{ $stages['qc'] }})</span>
            <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span> Kirim ({{ $stages['shipping'] }})</span>
        </div>
    </div>

    {{-- Aktivitas + bahan kritis --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-800">Antrian Produksi Terbaru</h2>
                <a href="{{ route('admin.production') }}" class="text-xs font-semibold text-green-700 hover:underline">Lihat semua →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-xs text-gray-500">
                        <tr>
                            <th class="px-4 py-2 text-left">Invoice</th>
                            <th class="px-4 py-2 text-left">Pelanggan</th>
                            <th class="px-4 py-2 text-left">Layanan</th>
                            <th class="px-4 py-2 text-center">Status</th>
                            <th class="px-4 py-2 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($activity as $order)
                            @php
                                $badge = match($order->status) {
                                    'paid' => 'bg-blue-100 text-blue-700',
                                    'processing' => 'bg-amber-100 text-amber-700',
                                    'qc' => 'bg-purple-100 text-purple-700',
                                    'shipping' => 'bg-indigo-100 text-indigo-700',
                                    default => 'bg-gray-100 text-gray-600',
                                };
                                $label = match($order->status) {
                                    'paid' => 'Siap Produksi',
                                    'processing' => 'Diproses',
                                    'qc' => 'QC',
                                    'shipping' => 'Kirim',
                                    default => $order->status,
                                };
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-semibold text-gray-900">{{ $order->invoice_number }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $order->user->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $order->items->first()?->service->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-center"><span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $badge }}">{{ $label }}</span></td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="text-xs font-semibold text-green-700 hover:underline">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-10 text-center text-gray-400">🎉 Tidak ada antrian aktif. Semua pesanan selesai.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-800">⚠️ Bahan Kritis</h2>
                <span class="px-2 py-0.5 text-xs font-bold rounded-full {{ $criticalCount > 0 ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">{{ $criticalCount }}</span>
            </div>
            <div class="p-4 space-y-3">
                @forelse($criticalMaterials as $m)
                    @php $net = (float)$m->stock - (float)$m->stock_reserved; @endphp
                    <div class="rounded-lg border border-red-100 bg-red-50 p-3">
                        <p class="text-sm font-semibold text-gray-900">{{ $m->name }}</p>
                        <p class="text-xs text-gray-600 mt-0.5">Sisa bersih: <b class="text-red-600">{{ number_format($net, 2) }} {{ $m->unit }}</b> / min {{ number_format((float)$m->minimum_stock, 2) }}</p>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 text-center py-6">✅ Semua bahan di atas stok minimum.</p>
                @endforelse
                @if($criticalCount > 0)
                    <a href="{{ route('admin.purchase-requests') }}" class="block text-center px-3 py-2 text-xs font-semibold rounded-lg bg-red-600 text-white hover:bg-red-700 transition">Buat / Lihat Purchase Request →</a>
                @endif
            </div>
        </div>
    </div>

    {{-- Aksi cepat --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ route('admin.production') }}" class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 hover:border-green-300 hover:bg-green-50 transition">
            <p class="text-lg">🏭</p><p class="text-sm font-semibold text-gray-800 mt-1">Antrian Produksi</p>
            <p class="text-xs text-gray-400 mt-0.5">Ambil & kerjakan SPK</p>
        </a>
        <a href="{{ route('admin.service-boms') }}" class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 hover:border-green-300 hover:bg-green-50 transition">
            <p class="text-lg">🧩</p><p class="text-sm font-semibold text-gray-800 mt-1">BOM Layanan</p>
            <p class="text-xs text-gray-400 mt-0.5">Resep bahan per layanan</p>
        </a>
        <a href="{{ route('admin.inventory') }}" class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 hover:border-green-300 hover:bg-green-50 transition">
            <p class="text-lg">📊</p><p class="text-sm font-semibold text-gray-800 mt-1">Dashboard Inventory</p>
            <p class="text-xs text-gray-400 mt-0.5">Stok, batch, kadaluarsa</p>
        </a>
        <a href="{{ route('admin.stock-opname') }}" class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 hover:border-green-300 hover:bg-green-50 transition">
            <p class="text-lg">📋</p><p class="text-sm font-semibold text-gray-800 mt-1">Stock Opname</p>
            <p class="text-xs text-gray-400 mt-0.5">Cocokkan stok fisik & sistem</p>
        </a>
    </div>

    <p class="text-xs text-gray-400">
        ℹ️ Metrik keuangan (pendapatan/omzet) sengaja tidak ditampilkan di role Produksi — itu tersedia di Dashboard Owner.
        Dashboard ini fokus pada kesiapan kerja, antrian, dan bahan.
    </p>
</div>