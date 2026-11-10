<div class="max-w-7xl mx-auto px-4 py-8">
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Command Center</h1>
        <p class="text-sm text-gray-500 mt-1">
            Ringkasan performa bisnis {{ config('app.brand.name', 'PrintOrder') }} — {{ now()->format('F Y') }}.
        </p>
    </div>

    {{-- ============ KPI CARDS ============ --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
            <p class="text-xs font-medium text-gray-500">Pendapatan Bulan Ini</p>
            <p class="text-xl font-bold text-green-600 mt-1">Rp {{ number_format($monthlyRevenue ?? 0, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">Uang masuk terverifikasi</p>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
            <p class="text-xs font-medium text-gray-500">Total Piutang</p>
            <p class="text-xl font-bold text-orange-500 mt-1">Rp {{ number_format($monthlyOutstanding ?? 0, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">Tagihan belum lunas</p>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
            <p class="text-xs font-medium text-gray-500">Pesanan Baru</p>
            <p class="text-xl font-bold text-gray-900 mt-1">{{ $newOrders ?? 0 }}</p>
            <p class="text-xs text-gray-400 mt-1">Order masuk bulan ini</p>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
            <p class="text-xs font-medium text-gray-500">Pesanan Selesai</p>
            <p class="text-xl font-bold text-purple-600 mt-1">{{ $completedOrders ?? 0 }}</p>
            <p class="text-xs text-gray-400 mt-1">Sukses ditutup bulan ini</p>
        </div>
    </div>

    {{-- ============ RADAR PIPELINE ============ --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 mb-6">
        <h3 class="font-bold text-gray-900 mb-4">⚡ Radar Pipeline Produksi (Real-time)</h3>
        @php
            $statuses = [
                'waiting_invoice' => ['Menunggu Invoice', 'bg-gray-100 text-gray-600'],
                'unpaid'          => ['Belum Bayar',      'bg-yellow-100 text-yellow-700'],
                'paid'            => ['SPK Terbit',       'bg-blue-100 text-blue-700'],
                'processing'      => ['Sedang Dicetak',   'bg-indigo-100 text-indigo-700'],
                'qc'              => ['Quality Control',  'bg-purple-100 text-purple-700'],
                'shipping'        => ['Siap Kirim',       'bg-green-100 text-green-700'],
            ];
        @endphp
        <div class="grid grid-cols-3 md:grid-cols-6 gap-3">
            @foreach($statuses as $key => $meta)
                <div class="text-center p-3 rounded-lg border border-gray-100">
                    <p class="text-2xl font-bold text-gray-900">{{ $pipeline[$key] ?? 0 }}</p>
                    <span class="inline-block mt-1 px-2 py-0.5 text-xs font-semibold rounded-full {{ $meta[1] }}">
                        {{ $meta[0] }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ============ GRAFIK + ALERT STOK (2 KOLOM) ============ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- GRAFIK TREN PENDAPATAN --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <h3 class="font-bold text-gray-900 mb-1">📊 Tren Pendapatan (6 Bulan)</h3>
            <p class="text-xs text-gray-500 mb-4">Berdasarkan pembayaran terverifikasi</p>
            <div style="height: 260px;">
                <canvas id="revenueChart"></canvas>
            </div>
            <div class="grid grid-cols-2 gap-4 mt-4 pt-4 border-t border-gray-100">
                <div>
                    <p class="text-xs text-gray-500">Total 6 Bulan</p>
                    <p class="text-lg font-bold text-gray-900">Rp {{ number_format(array_sum($chartData['values'] ?? []), 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Total Transaksi</p>
                    <p class="text-lg font-bold text-gray-900">{{ array_sum($chartData['counts'] ?? []) }}x</p>
                </div>
            </div>
        </div>

        {{-- ALERT STOK MENIPIS (LOGIKA MRP — SINKRON dgn Materials & PR) --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex flex-col">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="font-bold text-gray-900 flex items-center gap-2"><span>⚠️</span> Alert Stok Menipis</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Basis: net available vs minimum stock (ROP)</p>
                </div>
                @if(!empty($lowStockAlerts))
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">
                        {{ count($lowStockAlerts) }} kritis
                    </span>
                @endif
            </div>

            @if(empty($lowStockAlerts))
                <div class="flex-1 flex flex-col items-center justify-center text-center py-6">
                    <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center mb-3">
                        <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <p class="text-sm font-semibold text-green-700">Semua stok aman</p>
                    <p class="text-xs text-gray-500 mt-1">Tidak ada bahan di bawah minimum stock</p>
                    <a href="{{ route('admin.materials') }}" class="text-xs font-semibold text-gray-500 hover:text-gray-700 mt-3 underline">Kelola Bahan →</a>
                </div>
            @else
                <ul class="flex-1 space-y-3 overflow-y-auto max-h-72 pr-1">
                    @foreach($lowStockAlerts as $alert)
                        @php
                            $st    = $alert['status'] ?? 'safe';
                            $isOut = $st === 'out_of_stock';
                            $net   = (float)($alert['net_available'] ?? 0);
                            $min   = (float)($alert['minimum_stock'] ?? 0);
                            $pipe  = (float)($alert['pipeline'] ?? 0);
                            $pct   = $min > 0 ? max(0, min(100, ($net / $min) * 100)) : 0;
                            $box   = $isOut ? 'bg-red-50 border-red-200' : 'bg-orange-50 border-orange-200';
                            $bar   = $isOut ? 'bg-red-500' : 'bg-orange-500';
                            $txt   = $isOut ? 'text-red-700' : 'text-orange-700';
                            $label = match($st) { 'out_of_stock' => '🔴 HABIS', 'critical' => '🔴 KRITIS', default => '🟡 REORDER' };
                        @endphp
                        <li class="p-3 rounded-lg border {{ $box }}">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-semibold text-gray-900 truncate pr-2">{{ $alert['name'] ?? '-' }}</span>
                                <span class="shrink-0 px-2 py-0.5 rounded-full text-[10px] font-bold {{ $isOut ? 'bg-red-100 text-red-700' : 'bg-orange-100 text-orange-700' }}">{{ $label }}</span>
                            </div>
                            <div class="w-full h-1.5 bg-white/70 rounded-full overflow-hidden mb-2">
                                <div class="h-full {{ $bar }} rounded-full transition-all" style="width: {{ $pct }}%"></div>
                            </div>
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-gray-600">Bersih <b class="{{ $txt }}">{{ number_format($net, 1) }}</b> / Min {{ number_format($min, 1) }} {{ $alert['unit'] ?? 'm²' }}</span>
                                @if($pipe > 0)<span class="text-gray-400" title="Dikunci order berjalan">🔒 {{ number_format($pipe, 1) }}</span>@endif
                            </div>
                        </li>
                    @endforeach
                </ul>
                <a href="{{ route('admin.purchase-requests') }}" class="mt-4 inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-red-600 text-white text-sm font-semibold hover:bg-red-700 transition">📋 Lihat & Tindak Purchase Request →</a>
            @endif
        </div>

    {{-- ============ SCRIPT CHART.JS ============ --}}
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const el = document.getElementById('revenueChart');
            if (!el) return;
            const data = @json($chartData ?? ['labels' => [], 'values' => []]);
            new Chart(el.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: data.labels ?? [],
                    datasets: [{
                        label: 'Pendapatan (Rp)',
                        data: data.values ?? [],
                        backgroundColor: 'rgba(99,102,241,0.6)',
                        borderRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, ticks: { callback: v => 'Rp ' + (v/1e6).toFixed(0) + 'jt' } },
                        x: { grid: { display: false } }
                    }
                }
            });
        });
    </script>
    @endpush
</div>