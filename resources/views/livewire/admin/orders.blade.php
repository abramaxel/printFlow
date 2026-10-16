<div>
    <!-- Header Halaman -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Daftar Pesanan</h2>
        <a href="{{ route('admin.orders.create') }}" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 focus:outline-none">
            + Buat Pesanan Baru
        </a>
    </div>

    <!-- Filter & Search Bar -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-4 gap-4 bg-white p-4 rounded-lg shadow-sm border border-gray-200">
        <div class="w-full md:w-1/3">
            <input type="text" wire:model.live="search" placeholder="Cari No. Invoice atau Nama Pelanggan..." class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
        </div>
        <div class="w-full md:w-1/4">
            <select wire:model.live="statusFilter" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                <option value="">Semua Status</option>
                <option value="pending">Pending</option>
                <option value="waiting_invoice">Menunggu Invoice</option>
                <option value="unpaid">Belum Dibayar</option>
                <option value="paid">Sudah Dibayar</option>
                <option value="processing">Diproses</option>
                <option value="qc">Quality Control</option>
                <option value="shipping">Pengiriman</option>
                <option value="completed">Selesai</option>
                <option value="cancelled">Dibatalkan</option>
            </select>
        </div>
    </div>

    <!-- Tabel Data Pesanan -->
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3">No. Invoice</th>
                    <th scope="col" class="px-6 py-3">Tanggal</th>
                    <th scope="col" class="px-6 py-3">Pelanggan</th>
                    <th scope="col" class="px-6 py-3">Total Tagihan</th>
                    <th scope="col" class="px-6 py-3">Status</th>
                    <th scope="col" class="px-6 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900">
                            {{ $order->invoice_number }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $order->created_at->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $order->user->name ?? 'User Dihapus' }}
                        </td>
                        <td class="px-6 py-4 font-semibold text-gray-900">
                            @if($order->invoice)
                                Rp {{ number_format($order->invoice->total_amount, 0, ',', '.') }}
                            @else
                                <span class="text-gray-400 italic">Belum ada tagihan</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="{{ $this->getStatusColor($order->status) }} text-xs font-medium px-2.5 py-0.5 rounded">
                                {{ $this->formatStatus($order->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="font-medium text-blue-600 hover:underline">Lihat Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                            Tidak ada pesanan yang ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>