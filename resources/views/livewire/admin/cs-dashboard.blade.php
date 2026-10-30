<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="mb-8 flex justify-between items-end">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Dashboard Customer Service</h1>
            <p class="text-gray-500 mt-1">Halo, {{ auth()->user()->name }}. Berikut adalah tugas dan pesanan yang memerlukan perhatian Anda.</p>
        </div>
        <a href="{{ route('admin.orders.create') }}" class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition flex items-center shadow-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Buat Pesanan Baru
        </a>
    </div>

    {{-- ============ KPI CARDS (TUGAS CS) ============ --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        {{-- Menunggu Invoice --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Menunggu Invoice</p>
                <p class="text-3xl font-bold text-orange-600 mt-1">{{ $waitingInvoice->count() }}</p>
                <p class="text-xs text-gray-400 mt-2">Perlu dibuatkan tagihan</p>
            </div>
            <div class="p-3 bg-orange-50 rounded-lg text-orange-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
        </div>

        {{-- Belum Bayar (Follow Up) --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Belum Dibayar</p>
                <p class="text-3xl font-bold text-red-600 mt-1">{{ $unpaidOrders->count() }}</p>
                <p class="text-xs text-gray-400 mt-2">Perlu follow-up ke pelanggan</p>
            </div>
            <div class="p-3 bg-red-50 rounded-lg text-red-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        {{-- Sedang Diproses --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Sedang Diproses</p>
                <p class="text-3xl font-bold text-blue-600 mt-1">{{ $inProduction }}</p>
                <p class="text-xs text-gray-400 mt-2">SPK Terbit, Cetak, atau QC</p>
            </div>
            <div class="p-3 bg-blue-50 rounded-lg text-blue-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
            </div>
        </div>
    </div>

    {{-- ============ TABEL TUGAS: MENUNGGU INVOICE & BELUM BAYAR ============ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        {{-- Kolom 1: Menunggu Invoice --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-orange-50">
                <h3 class="font-bold text-gray-800 flex items-center">
                    <span class="w-2 h-2 bg-orange-500 rounded-full mr-2"></span>
                    Perlu Dibuatkan Invoice
                </h3>
                <a href="{{ route('admin.orders') }}?status=waiting_invoice" class="text-xs text-orange-600 hover:underline">Lihat Semua</a>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($waitingInvoice as $order)
                    <div class="px-6 py-4 hover:bg-gray-50 transition flex justify-between items-center">
                        <div>
                            <p class="text-sm font-bold text-gray-900">{{ $order->invoice_number }}</p>
                            <p class="text-xs text-gray-500">{{ $order->user->name }} • {{ $order->created_at->format('d M, H:i') }}</p>
                        </div>
                        <a href="{{ route('admin.orders.show', $order->id) }}" class="px-3 py-1.5 bg-orange-100 text-orange-700 text-xs font-semibold rounded-md hover:bg-orange-200 transition">
                            Buat Invoice
                        </a>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-gray-400 text-sm">
                        🎉 Tidak ada pesanan yang menunggu invoice.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Kolom 2: Belum Bayar (Follow Up) --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-red-50">
                <h3 class="font-bold text-gray-800 flex items-center">
                    <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                    Belum Dibayar (Perlu Follow-up)
                </h3>
                <a href="{{ route('admin.orders') }}?status=unpaid" class="text-xs text-red-600 hover:underline">Lihat Semua</a>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($unpaidOrders as $order)
                    <div class="px-6 py-4 hover:bg-gray-50 transition flex justify-between items-center">
                        <div>
                            <p class="text-sm font-bold text-gray-900">{{ $order->invoice_number }}</p>
                            <p class="text-xs text-gray-500">{{ $order->user->name }} • Jatuh Tempo: {{ $order->invoice?->due_date?->format('d M') ?? '-' }}</p>
                            <p class="text-xs font-semibold text-red-600 mt-1">
                                Rp {{ number_format($order->invoice?->total_amount ?? 0, 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="flex flex-col gap-2">
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="px-3 py-1.5 bg-blue-100 text-blue-700 text-xs font-semibold rounded-md hover:bg-blue-200 transition text-center">
                                Detail
                            </a>
                            {{-- Tombol WhatsApp (Opsional, ganti nomor dan teks sesuai kebutuhan) --}}
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->user->phone ?? '') }}?text=Halo%20{{ urlencode($order->user->name) }},%20mengingatkan%20untuk%20pesanan%20{{ $order->invoice_number }}%20segera%20dilunasi.%20Terima%20kasih." target="_blank" class="px-3 py-1.5 bg-green-100 text-green-700 text-xs font-semibold rounded-md hover:bg-green-200 transition text-center flex items-center justify-center">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                WA
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-gray-400 text-sm">
                        🎉 Semua pelanggan sudah membayar tepat waktu.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>