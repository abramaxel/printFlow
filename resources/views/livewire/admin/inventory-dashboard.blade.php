<div class="max-w-7xl mx-auto px-4 py-8">
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">📊 Dashboard Inventory</h1>
        <p class="text-sm text-gray-500 mt-1">Analitik persediaan, pergerakan stok, dan peringatan gudang</p>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-gray-500">Total Nilai Stok</p>
                <span class="text-2xl">💰</span>
            </div>
            <p class="text-xl font-bold text-gray-900">Rp {{ number_format($kpi['total_value'], 0, ',', '.') }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ $kpi['total_materials'] }} jenis material aktif</p>
        </div>

        <div class="bg-red-50 p-4 rounded-lg border border-red-200">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-red-600">Material Kritis</p>
                <span class="text-2xl">🚨</span>
            </div>
            <p class="text-xl font-bold text-red-700">{{ $kpi['critical_materials'] }}</p>
            <p class="text-xs text-red-500 mt-1">Di bawah minimum stock</p>
        </div>

        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-blue-600">Pending PR</p>
                <span class="text-2xl">📋</span>
            </div>
            <p class="text-xl font-bold text-blue-700">{{ $kpi['pending_pr'] }}</p>
            <p class="text-xs text-blue-500 mt-1">Purchase Request menunggu</p>
        </div>

        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-green-600">Fast Moving</p>
                <span class="text-2xl">⚡</span>
            </div>
            <p class="text-xl font-bold text-green-700">{{ $fastMoving->count() }}</p>
            <p class="text-xs text-green-500 mt-1">Top bahan 30 hari</p>
        </div>
    </div>

    {{-- Grafik Tren Pergerakan Stok --}}
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h2 class="text-lg font-bold text-gray-900">📈 Tren Pergerakan Stok (30 Hari)</h2>
                <p class="text-sm text-gray-500">Barang masuk vs barang keluar per hari</p>
            </div>
            <div class="flex gap-4 text-sm">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                    <span class="text-gray-600">Masuk</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 bg-red-500 rounded-full"></span>
                    <span class="text-gray-600">Keluar</span>
                </div>
            </div>
        </div>
        <div style="height: 300px;">
            <canvas id="movementChart"></canvas>
        </div>
    </div>

    {{-- Grid 2 Kolom --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Top 5 Fast Moving Materials --}}
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <h2 class="text-lg font-bold text-gray-900 mb-4">⚡ Top 5 Bahan Terlaris (30 Hari)</h2>
            @if($fastMoving->count() > 0)
                <div class="space-y-3">
                    @foreach($fastMoving as $idx => $item)
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                            <div class="flex-shrink-0 w-8 h-8 bg-blue-100 text-blue-700 rounded-full flex items-center justify-center font-bold text-sm">
                                {{ $idx + 1 }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-900 truncate">{{ $item->material->name ?? '-' }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ number_format($item->total_usage, 1) }} {{ $item->material->unit ?? 'unit' }} 
                                    • Rp {{ number_format($item->total_cost, 0, ',', '.') }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-gray-900">{{ number_format($item->avg_daily, 2) }}</p>
                                <p class="text-xs text-gray-500">per hari</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-400 text-center py-8">Belum ada data pemakaian 30 hari terakhir</p>
            @endif
        </div>

        {{-- Alert: Stok Kritis --}}
        <div class="bg-white p-6 rounded-lg shadow-sm border border-red-200">
            <h2 class="text-lg font-bold text-red-700 mb-4">🚨 Stok Kritis (Butuh Restock)</h2>
            @if($criticalStock->count() > 0)
                <div class="space-y-2">
                    @foreach($criticalStock as $material)
                        @php
                            $available = $material->stock - $material->stock_reserved;
                            $percentage = $material->minimum_stock > 0 ? ($available / $material->minimum_stock) * 100 : 0;
                        @endphp
                        <div class="p-3 bg-red-50 rounded-lg border-l-4 border-red-500">
                            <div class="flex justify-between items-start mb-1">
                                <p class="font-medium text-gray-900">{{ $material->name }}</p>
                                <span class="text-xs font-bold text-red-700 bg-red-100 px-2 py-0.5 rounded">
                                    {{ number_format($percentage, 0) }}%
                                </span>
                            </div>
                            <div class="flex justify-between text-xs text-gray-600">
                                <span>Tersedia: <b class="text-red-700">{{ number_format($available, 1) }} {{ $material->unit }}</b></span>
                                <span>Min: {{ number_format($material->minimum_stock, 1) }} {{ $material->unit }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-green-600 text-center py-8">✅ Semua stok dalam kondisi aman</p>
            @endif
        </div>
    </div>

    {{-- Grid 2 Kolom - Batch Expired & PR --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Batch Kadaluarsa --}}
        <div class="bg-white p-6 rounded-lg shadow-sm border border-orange-200">
            <h2 class="text-lg font-bold text-orange-700 mb-4">⏰ Batch Segera Kadaluarsa</h2>
            @if($expiringBatches->count() > 0)
                <div class="space-y-2">
                    @foreach($expiringBatches as $batch)
                        @php
                            $daysLeft = now()->diffInDays($batch->expiry_date, false);
                            $color = $daysLeft <= 7 ? 'red' : ($daysLeft <= 14 ? 'orange' : 'yellow');
                        @endphp
                        <div class="p-3 bg-{{ $color }}-50 rounded-lg">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $batch->material->name ?? '-' }}</p>
                                    <p class="text-xs text-gray-500">Batch: {{ $batch->batch_number }}</p>
                                </div>
                                <span class="text-xs font-bold text-{{ $color }}-700 bg-{{ $color }}-100 px-2 py-0.5 rounded">
                                    @if($daysLeft < 0)
                                        EXPIRED
                                    @else
                                        {{ $daysLeft }} hari
                                    @endif
                                </span>
                            </div>
                            <p class="text-xs text-gray-600 mt-1">
                                Sisa: {{ number_format($batch->remaining_quantity, 1) }} {{ $batch->material->unit ?? 'unit' }}
                            </p>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-400 text-center py-8">Tidak ada batch yang akan kadaluarsa dalam 30 hari</p>
            @endif
        </div>

        {{-- Pending Purchase Requests --}}
        <div class="bg-white p-6 rounded-lg shadow-sm border border-blue-200">
            <h2 class="text-lg font-bold text-blue-700 mb-4">📋 Purchase Request Menunggu</h2>
            @if($purchaseRequests->count() > 0)
                <div class="space-y-2">
                    @foreach($purchaseRequests as $pr)
                        @php
                            $urgencyColor = match($pr->urgency) {
                                'critical' => 'red',
                                'warning' => 'yellow',
                                default => 'blue',
                            };
                        @endphp
                        <div class="p-3 bg-{{ $urgencyColor }}-50 rounded-lg border-l-4 border-{{ $urgencyColor }}-500">
                            <div class="flex justify-between items-start mb-1">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $pr->material->name ?? '-' }}</p>
                                    <p class="text-xs text-gray-500">{{ $pr->pr_number }}</p>
                                </div>
                                <span class="text-xs font-bold text-{{ $urgencyColor }}-700 bg-{{ $urgencyColor }}-100 px-2 py-0.5 rounded uppercase">
                                    {{ $pr->urgency }}
                                </span>
                            </div>
                            <div class="flex justify-between text-xs text-gray-600 mt-1">
                                <span>Qty: <b>{{ number_format($pr->requested_quantity, 1) }} {{ $pr->unit }}</b></span>
                                <span>Rp {{ number_format($pr->estimated_cost, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-400 text-center py-8">Tidak ada PR yang menunggu persetujuan</p>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('movementChart').getContext('2d');
        const trend = @json($movementTrend);
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: trend.labels,
                datasets: [
                    {
                        label: 'Barang Masuk',
                        data: trend.in,
                        borderColor: 'rgb(34, 197, 94)',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        tension: 0.4,
                        fill: true,
                    },
                    {
                        label: 'Barang Keluar',
                        data: trend.out,
                        borderColor: 'rgb(239, 68, 68)',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        tension: 0.4,
                        fill: true,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            maxTicksLimit: 10,
                        }
                    }
                }
            }
        });
    });
</script>
@endpush