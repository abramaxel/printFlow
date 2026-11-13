<div class="space-y-6">
    {{-- Header panel --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-gray-900">🧭 Panel Operasional Owner</h2>
            <p class="text-sm text-gray-500 mt-1">Purchasing, gudang, kepatuhan & CRM — terhubung ke modul yang sudah aktif.</p>
        </div>
        <button wire:click="$refresh" class="px-4 py-2 text-sm font-semibold rounded-lg bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 transition">🔄 Refresh</button>
    </div>

    {{-- KPI operasional --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ route('admin.purchase-requests') }}" class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 hover:border-green-300 transition">
            <p class="text-xs text-gray-500">PR Menunggu Approval</p>
            <p class="text-2xl font-bold text-orange-600 mt-1">{{ $prPending }}</p>
            <p class="text-[11px] text-gray-400 mt-1">Est. Rp {{ number_format($prPendingValue, 0, ',', '.') }} • {{ $prAwaitingReceipt }} menunggu barang</p>
        </a>
        <a href="{{ route('admin.goods-receipt') }}" class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 hover:border-green-300 transition">
            <p class="text-xs text-gray-500">Pembelian Bulan Ini</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">Rp {{ number_format($purchaseMonth, 0, ',', '.') }}</p>
            <p class="text-[11px] text-gray-400 mt-1">{{ $receiptCount }} BPB tercatat</p>
        </a>
        <a href="{{ route('admin.orders') }}?status=unpaid" class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 hover:border-red-300 transition">
            <p class="text-xs text-gray-500">Piutang Jatuh Tempo</p>
            <p class="text-2xl font-bold text-red-600 mt-1">Rp {{ number_format($overdueValue, 0, ',', '.') }}</p>
            <p class="text-[11px] text-gray-400 mt-1">{{ $overdueCount }} invoice lewat due date</p>
        </a>
        <a href="{{ route('admin.inventory') }}" class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 hover:border-purple-300 transition">
            <p class="text-xs text-gray-500">Batch Kadaluarsa ≤30 Hari</p>
            <p class="text-2xl font-bold text-purple-600 mt-1">{{ $expCount }}</p>
            <p class="text-[11px] text-gray-400 mt-1">{{ number_format($expQty, 2) }} unit berisiko</p>
        </a>
    </div>

    {{-- Purchasing + Integritas --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Purchasing pipeline --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-800">🛒 Purchasing Pipeline</h3>
                <a href="{{ route('admin.purchase-requests') }}" class="text-xs font-semibold text-green-700 hover:underline">Approve PR →</a>
            </div>
            <div class="p-4 space-y-4">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase mb-2">PR pending (prioritas kritis dulu)</p>
                    @forelse($recentPrs as $pr)
                        <div class="flex items-center justify-between py-1.5 border-b border-gray-50 last:border-0">
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $pr->material->name ?? '-' }}</p>
                                <p class="text-xs text-gray-500">{{ number_format((float)$pr->requested_quantity, 2) }} {{ $pr->unit }} • Rp {{ number_format((float)$pr->estimated_cost, 0, ',', '.') }}</p>
                            </div>
                            <span class="ml-2 px-2 py-0.5 text-xs font-bold rounded-full {{ $pr->urgency === 'critical' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700' }}">{{ strtoupper($pr->urgency) }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 py-2">✅ Tidak ada PR pending.</p>
                    @endforelse
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Penerimaan terbaru (BPB)</p>
                    @forelse($recentReceipts as $r)
                        <div class="flex items-center justify-between py-1.5 border-b border-gray-50 last:border-0">
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $r->supplier->name ?? '-' }}</p>
                                <p class="text-xs text-gray-500">{{ $r->bpb_number }} • {{ $r->receipt_date?->format('d M Y') }}</p>
                            </div>
                            <div class="ml-2 text-right">
                                <p class="text-sm font-semibold text-gray-900">Rp {{ number_format((float)$r->total_cost, 0, ',', '.') }}</p>
                                <a href="{{ route('admin.purchase-receipts.pdf', $r->id) }}" target="_blank" class="text-xs text-green-700 hover:underline">PDF</a>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 py-2">Belum ada penerimaan barang.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Integritas & kepatuhan --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-800">🛡️ Integritas Data & Kepatuhan</h3>
            </div>
            <div class="p-4 space-y-3">
                @if($overReserved->count() > 0)
                    <div class="rounded-lg border border-red-200 bg-red-50 p-3">
                        <p class="text-sm font-semibold text-red-700">⚠️ Reservasi melebihi stok (stok bersih negatif)</p>
                        @foreach($overReserved as $m)
                            <p class="text-xs text-gray-700 mt-1">{{ $m->name }}: bersih <b class="text-red-600">{{ number_format((float)$m->stock - (float)$m->stock_reserved, 2) }} {{ $m->unit }}</b></p>
                        @endforeach
                        <a href="{{ route('admin.materials') }}" class="inline-block mt-2 text-xs font-semibold text-red-700 underline">Perbaiki stok / reservasi →</a>
                    </div>
                @endif
                @if($expBatches->count() > 0)
                    <div class="rounded-lg border border-purple-200 bg-purple-50 p-3">
                        <p class="text-sm font-semibold text-purple-700">⏳ Batch mendekati kadaluarsa (FIFO)</p>
                        @foreach($expBatches as $b)
                            <p class="text-xs text-gray-700 mt-1">{{ $b['name'] }}: {{ number_format($b['qty'], 2) }} {{ $b['unit'] }} • exp {{ \Carbon\Carbon::parse($b['expiry'])->format('d M Y') }}</p>
                        @endforeach
                        <a href="{{ route('admin.inventory') }}" class="inline-block mt-2 text-xs font-semibold text-purple-700 underline">Lihat batch →</a>
                    </div>
                @endif
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('admin.materials') }}" class="rounded-lg border border-gray-200 p-3 hover:bg-gray-50 transition">
                        <p class="text-xs text-gray-500">ROP belum diset</p>
                        <p class="text-lg font-bold {{ $ropUnset > 0 ? 'text-amber-600' : 'text-green-600' }}">{{ $ropUnset }}</p>
                        <p class="text-[11px] text-gray-400">bahan minimum = 0</p>
                    </a>
                    <a href="{{ route('admin.service-boms') }}" class="rounded-lg border border-gray-200 p-3 hover:bg-gray-50 transition">
                        <p class="text-xs text-gray-500">Layanan tanpa BOM</p>
                        <p class="text-lg font-bold {{ $noBom > 0 ? 'text-amber-600' : 'text-green-600' }}">{{ $noBom }}</p>
                        <p class="text-[11px] text-gray-400">MRP belum bisa hitung</p>
                    </a>
                    <a href="{{ route('admin.stock-opname') }}" class="rounded-lg border border-gray-200 p-3 hover:bg-gray-50 transition">
                        <p class="text-xs text-gray-500">Adjustment bulan ini</p>
                        <p class="text-lg font-bold text-gray-900">{{ $adjMonth }}</p>
                        <p class="text-[11px] text-gray-400">indikasi selisih stok</p>
                    </a>
                    <a href="{{ route('admin.material-audit-trail') }}" class="rounded-lg border border-gray-200 p-3 hover:bg-gray-50 transition">
                        <p class="text-xs text-gray-500">Opname terakhir</p>
                        <p class="text-sm font-bold text-gray-900 mt-1">{{ $lastOpname ? \Carbon\Carbon::parse($lastOpname)->format('d M Y') : 'Belum pernah' }}</p>
                        <p class="text-[11px] text-gray-400">audit trail tersedia</p>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- CRM + aksi cepat --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-800">🏆 Top Pelanggan Bulan Ini</h3>
                <a href="{{ route('admin.customers') }}" class="text-xs font-semibold text-green-700 hover:underline">CRM →</a>
            </div>
            <div class="p-4 space-y-2">
                @forelse($topCustomers as $c)
                    <div class="flex items-center justify-between py-1.5 border-b border-gray-50 last:border-0">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $c->name }}</p>
                            <p class="text-xs text-gray-500">{{ $c->email }}</p>
                        </div>
                        <div class="ml-2 text-right">
                            <p class="text-sm font-semibold text-green-700">Rp {{ number_format((float)($c->paid_month ?? 0), 0, ',', '.') }}</p>
                            <span class="px-2 py-0.5 text-xs font-bold rounded-full bg-gray-100 text-gray-600">{{ ucfirst($c->tier ?? 'regular') }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 py-2">Belum ada pembayaran terverifikasi bulan ini.</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-800 mb-3">⚡ Aksi Cepat Owner</h3>
            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('admin.purchase-requests') }}" class="rounded-lg border border-gray-200 p-3 hover:border-green-300 hover:bg-green-50 transition"><p class="text-lg">📝</p><p class="text-sm font-semibold text-gray-800 mt-1">Approve PR</p></a>
                <a href="{{ route('admin.goods-receipt') }}" class="rounded-lg border border-gray-200 p-3 hover:border-green-300 hover:bg-green-50 transition"><p class="text-lg">📥</p><p class="text-sm font-semibold text-gray-800 mt-1">Terima Barang</p></a>
                <a href="{{ route('admin.stock-opname') }}" class="rounded-lg border border-gray-200 p-3 hover:border-green-300 hover:bg-green-50 transition"><p class="text-lg">📋</p><p class="text-sm font-semibold text-gray-800 mt-1">Stock Opname</p></a>
                <a href="{{ route('admin.material-audit-trail') }}" class="rounded-lg border border-gray-200 p-3 hover:border-green-300 hover:bg-green-50 transition"><p class="text-lg">🕵️</p><p class="text-sm font-semibold text-gray-800 mt-1">Audit Trail</p></a>
                <a href="{{ route('admin.production-monitor') }}" class="rounded-lg border border-gray-200 p-3 hover:border-green-300 hover:bg-green-50 transition"><p class="text-lg">📊</p><p class="text-sm font-semibold text-gray-800 mt-1">Monitor Produksi</p></a>
                <a href="{{ route('admin.customers') }}" class="rounded-lg border border-gray-200 p-3 hover:border-green-300 hover:bg-green-50 transition"><p class="text-lg">👥</p><p class="text-sm font-semibold text-gray-800 mt-1">CRM Pelanggan</p></a>
            </div>
        </div>
    </div>
</div>