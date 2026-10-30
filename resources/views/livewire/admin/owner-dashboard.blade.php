<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Command Center</h1>
        <p class="text-gray-500 mt-1">Ringkasan performa bisnis {{ config('app.brand.name', 'PrintOrder') }} — {{ now()->format('F Y') }}.</p>
    </div>

    {{-- ============ KPI CARDS ============ --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <p class="text-sm font-medium text-gray-500">Pendapatan Bulan Ini</p>
            <p class="text-2xl font-bold text-green-600 mt-1">Rp {{ number_format($monthlyRevenue ?? 0, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-2">Uang masuk terverifikasi</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <p class="text-sm font-medium text-gray-500">Total Piutang</p>
            <p class="text-2xl font-bold text-orange-600 mt-1">Rp {{ number_format($monthlyOutstanding ?? 0, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-2">Tagihan belum lunas</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <p class="text-sm font-medium text-gray-500">Pesanan Baru</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $newOrders ?? 0 }}</p>
            <p class="text-xs text-gray-400 mt-2">Order masuk bulan ini</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <p class="text-sm font-medium text-gray-500">Pesanan Selesai</p>
            <p class="text-2xl font-bold text-purple-600 mt-1">{{ $completedOrders ?? 0 }}</p>
            <p class="text-xs text-gray-400 mt-2">Sukses ditutup bulan ini</p>
        </div>
    </div>

    {{-- ============ RADAR PIPELINE ============ --}}
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-8">
        <h2 class="text-lg font-bold text-gray-800 mb-4">⚡ Radar Pipeline Produksi (Real-time)</h2>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            @php
                $statuses = [
                    'waiting_invoice' => ['Menunggu Invoice', 'text-gray-600', 'bg-gray-100 text-gray-600'],
                    'unpaid'          => ['Belum Bayar',      'text-yellow-600', 'bg-yellow-100 text-yellow-700'],
                    'paid'            => ['SPK Terbit',       'text-blue-600', 'bg-blue-100 text-blue-700'],
                    'processing'      => ['Sedang Dicetak',   'text-indigo-600', 'bg-indigo-100 text-indigo-700'],
                    'qc'              => ['Quality Control',  'text-purple-600', 'bg-purple-100 text-purple-700'],
                    'shipping'        => ['Siap Kirim',       'text-green-600', 'bg-green-100 text-green-700'],
                ];
            @endphp
            @foreach($statuses as $key => $meta)
                <div class="p-4 rounded-lg border border-gray-200 text-center">
                    <p class="text-3xl font-bold {{ $meta[1] }}">{{ $pipeline[$key] ?? 0 }}</p>
                    <span class="text-[10px] font-semibold mt-1 {{ $meta[2] }} px-2 py-1 rounded-full inline-block">{{ $meta[0] }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ============ GRAFIK + ALERT STOK (2 KOLOM) ============ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- GRAFIK TREN PENDAPATAN --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold text-gray-800">📊 Tren Pendapatan (6 Bulan)</h2>
                <span class="text-xs text-gray-500">Berdasarkan pembayaran terverifikasi</span>
            </div>
            <div wire:ignore class="relative" style="height: 280px;">
                <canvas id="revenueChart"></canvas>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100 grid grid-cols-2 gap-4 text-xs">
                <div>
                    <p class="text-gray-500">Total 6 Bulan</p>
                    <p class="text-lg font-bold text-gray-800">Rp {{ number_format(array_sum($chartData['values'] ?? []), 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Total Transaksi</p>
                    <p class="text-lg font-bold text-gray-800">{{ array_sum($chartData['counts'] ?? []) }}x</p>
                </div>
            </div>
        </div>

        {{-- ALERT STOK MENIPIS --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold text-gray-800">⚠️ Alert Stok Menipis</h2>
                <a href="{{ route('admin.materials') }}" class="text-xs text-blue-600 hover:underline">Kelola Bahan →</a>
            </div>

            @if(empty($lowStockAlerts) || count($lowStockAlerts) === 0)
                <div class="flex flex-col items-center justify-center py-12 text-center">
                    <svg class="w-12 h-12 text-green-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <p class="text-sm font-medium text-gray-700">Semua stok aman</p>
                    <p class="text-xs text-gray-400 mt-1">Tidak ada bahan di bawah threshold 20%</p>
                </div>
            @else
                <div class="space-y-2 max-h-[320px] overflow-y-auto pr-1">
                    @foreach($lowStockAlerts as $alert)
                        @php
                            $badge = match($alert['severity']) {
                                'critical' => 'bg-red-100 text-red-700 border-red-200',
                                'warning'  => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                                default    => 'bg-blue-100 text-blue-700 border-blue-200',
                            };
                            $label = match($alert['severity']) {
                                'critical' => 'KRITIS',
                                'warning'  => 'PERINGATAN',
                                default    => 'RENDAH',
                            };
                        @endphp
                        <div class="p-3 rounded-lg border border-gray-200 hover:border-gray-300 transition">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $alert['name'] }}</p>
                                    <p class="text-[10px] text-gray-500 mt-0.5">
                                        Tersedia: <span class="font-bold">{{ number_format($alert['available'], 2) }}</span> m²
                                        dari {{ number_format($alert['stock'], 2) }} m²
                                    </p>
                                </div>
                                <span class="text-[9px] font-bold px-2 py-0.5 rounded-full border {{ $badge }} ml-2 flex-shrink-0">
                                    {{ $label }} {{ $alert['percentage'] }}%
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                <div class="h-1.5 rounded-full transition-all {{ $alert['severity'] === 'critical' ? 'bg-red-500' : ($alert['severity'] === 'warning' ? 'bg-yellow-500' : 'bg-blue-500') }}" style="width: {{ min(100, $alert['percentage']) }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- ============ SCRIPT CHART.JS (HANYA SATU BLOK, DI DALAM ROOT DIV) ============ --}}
    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const chartData = @json($chartData ?? ['labels' => [], 'values' => [], 'counts' => []]);
        const ctx = document.getElementById('revenueChart');
        
        if (!ctx) {
            console.warn('Canvas revenueChart tidak ditemukan di DOM.');
            return;
        }

        const existingChart = Chart.getChart(ctx);
        if (existingChart) {
            existingChart.destroy();
        }

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: chartData.values,
                    backgroundColor: chartData.values.map((_, i) => 
                        i === chartData.values.length - 1 ? '#10b981' : '#9ca3af'
                    ),
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1f2937',
                        padding: 10,
                        cornerRadius: 6,
                        callbacks: {
                            label: function(context) {
                                const val = context.parsed.y;
                                const count = chartData.counts[context.dataIndex] || 0;
                                return [
                                    'Rp ' + val.toLocaleString('id-ID'),
                                    count + ' transaksi'
                                ];
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f3f4f6' },
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                            },
                            font: { size: 10 }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10 } }
                    }
                }
            }
        });
    });
    </script>
    @endpush
</div>