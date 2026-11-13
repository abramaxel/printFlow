<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- HERO BAND: sapaan + statistik menyatu (pola bento, bukan 4 card terpisah) --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-800 via-brand-600 to-brand-500 text-white p-6 sm:p-8 mb-8 shadow-lg shadow-brand-600/30">
        <div class="absolute -top-16 -right-16 w-56 h-56 bg-white/10 rounded-full"></div>
        <div class="absolute -bottom-20 -right-2 w-64 h-64 bg-signal-400/10 rounded-full"></div>
        <div class="relative z-10 flex flex-col xl:flex-row xl:items-center gap-6">
            <div class="flex-1 min-w-0">
                <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight">Halo, {{ auth()->user()->name }} 👋</h1>
                <p class="text-lg text-brand-100 mt-2">Pantau pesanan, pembayaran, dan progres produksi Anda secara real-time.</p>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 xl:grid-cols-3 gap-3 xl:w-auto">
                <div class="bg-white/10 rounded-2xl px-4 py-3"><p class="text-sm text-brand-100">Total</p><p class="text-3xl font-extrabold tabular-nums">{{ $stats['total'] ?? 0 }}</p></div>
                <div class="bg-white/10 rounded-2xl px-4 py-3"><p class="text-sm text-brand-100">Aktif</p><p class="text-3xl font-extrabold tabular-nums">{{ $stats['active'] ?? 0 }}</p></div>
                <div class="bg-white/10 rounded-2xl px-4 py-3"><p class="text-sm text-brand-100">Selesai</p><p class="text-3xl font-extrabold tabular-nums">{{ $stats['completed'] ?? 0 }}</p></div>
                <div class="col-span-2 sm:col-span-4 xl:col-span-3 bg-signal-400 text-brand-950 rounded-2xl px-4 py-3"><p class="text-sm font-bold">Total Dibayar</p><p class="text-2xl font-extrabold tabular-nums tracking-tight">Rp {{ number_format($totalPaid ?? 0, 0, ',', '.') }}</p></div>
            </div>
        </div>
    </div>

    {{-- GRID 2 KOLOM: kiri pelacakan, kanan rail sticky --}}
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">

        {{-- KOLOM KIRI: daftar pesanan --}}
        <div class="xl:col-span-8 space-y-5 min-w-0">

            {{-- Filter + cari --}}
            <div class="flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
                <div class="flex flex-wrap gap-2.5">
                    @foreach(['all' => 'Semua', 'active' => 'Aktif', 'unpaid' => 'Belum Bayar', 'completed' => 'Selesai'] as $key => $label)
                        <button wire:click="setFilter('{{ $key }}')" class="px-5 py-2.5 text-base font-bold rounded-full border transition {{ $filter === $key ? 'bg-brand-600 text-white border-brand-600 shadow-sm' : 'bg-white text-gray-600 border-ice-300 hover:bg-ice-50' }}">{{ $label }}</button>
                    @endforeach
                </div>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="🔍 Cari no. invoice..." class="w-full sm:w-64 px-5 py-2.5 text-base border border-ice-300 rounded-xl focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 bg-white">
            </div>

            {{-- Kartu pelacakan --}}
            <div wire:loading.class="opacity-50 pointer-events-none" class="space-y-5 transition">
                @forelse($orders as $order)
                    @php
                        $meta = $statusMeta[$order->status] ?? ['label' => ucfirst(str_replace('_', ' ', $order->status)), 'badge' => 'bg-ice-200 text-brand-800', 'step' => 0];
                        $inv = $order->invoice;
                        $invTotal = (float) ($inv->total_amount ?? 0);
                        $invPaid = (float) ($inv->total_paid ?? 0);
                        $isPaid = $inv && (($inv->status === 'paid') || ($invTotal > 0 && $invPaid >= $invTotal));
                        $isUnpaid = in_array($order->status, ['waiting_invoice', 'unpaid']) && !$isPaid;
                        $payUrl = ($inv && $inv->payment_token) ? route('payment.show', $inv->payment_token) : null;
                        $pdfUrl = $inv ? route('customer.invoices.pdf', $inv->id) : null;
                        $pdfDlUrl = $inv ? route('customer.invoices.download', $inv->id) : null;
                        $step = (int) $meta['step'];
                        $pct = $order->status === 'completed' ? 100 : max(0, min(100, round($step / 5 * 100)));
                        $eta = $order->deadline_date ? \Carbon\Carbon::parse($order->deadline_date)->format('d M Y') : null;
                        $waMsg = urlencode('Halo Admin '.$contact['name'].', saya '.auth()->user()->name.'. Mohon terbitkan invoice untuk pesanan '.$order->invoice_number.' agar saya bisa melakukan pembayaran. Terima kasih.');
                        $stages = ['Invoice', 'Bayar', 'Produksi', 'QC', 'Kirim', 'Selesai'];
                    @endphp
                    <div class="bg-white rounded-2xl border border-ice-200 shadow-sm p-5 sm:p-6 hover:shadow-md transition-shadow">

                        <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-4">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2.5">
                                    <span class="text-lg sm:text-xl font-extrabold text-brand-950 tracking-tight">{{ $order->invoice_number }}</span>
                                    <button onclick="copyText('{{ $order->invoice_number }}', this)" title="Salin nomor pesanan" class="w-8 h-8 flex items-center justify-center rounded-lg bg-ice-100 text-brand-700 hover:bg-ice-200 text-base font-bold transition-colors">⧉</button>
                                    <span class="px-3 py-1 text-sm font-bold rounded-full {{ $meta['badge'] }}">{{ $meta['label'] }}</span>
                                    @if($isPaid)<span class="px-3 py-1 text-sm font-extrabold rounded-full bg-signal-400 text-brand-950 tracking-wider">LUNAS</span>@endif
                                </div>
                                <p class="text-base text-gray-500 mt-2">{{ $order->created_at->format('d M Y, H:i') }} • {{ $order->items->count() }} item • {{ $order->items->first()?->service->name ?? '-' }}</p>
                            </div>
                            <div class="text-left lg:text-right shrink-0">
                                <p class="text-2xl font-extrabold text-brand-950 tabular-nums tracking-tight">Rp {{ number_format($invTotal, 0, ',', '.') }}</p>
                                <p class="text-base {{ $isPaid ? 'text-signal-600 font-bold' : 'text-gray-500' }}">{{ $isPaid ? 'Lunas' : ($inv ? 'Belum Lunas' : 'Menunggu invoice') }}</p>
                            </div>
                        </div>

                        {{-- Timeline pelacakan --}}
                        <div class="mt-5 rounded-xl bg-ice-50 border border-ice-200 p-4 sm:p-5">
                            <div class="flex flex-col md:flex-row md:items-center gap-5">
                                <div class="flex-1 min-w-0">
                                    <div class="grid grid-cols-6">
                                        @foreach($stages as $i => $label)
                                            @php
                                                $done = ($i < $step) || ($i === $step && $order->status === 'completed');
                                                $current = ($i === $step && $order->status !== 'completed' && $step >= 0);
                                            @endphp
                                            <div class="relative flex flex-col items-center">
                                                @if(!$loop->first)<span class="absolute left-0 right-1/2 top-2.5 h-1 rounded {{ $step >= $i ? 'bg-brand-500' : 'bg-ice-300' }}"></span>@endif
                                                @if(!$loop->last)<span class="absolute left-1/2 right-0 top-2.5 h-1 rounded {{ $step > $i ? 'bg-brand-500' : 'bg-ice-300' }}"></span>@endif
                                                <span class="relative z-10 w-5 h-5 rounded-full flex items-center justify-center {{ $done ? 'bg-brand-600 text-white' : ($current ? 'bg-signal-400 ring-4 ring-signal-200 animate-pulse' : 'bg-ice-300') }}">
                                                    @if($done)<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>@endif
                                                </span>
                                                <span class="mt-2.5 text-sm font-bold {{ $done ? 'text-brand-700' : ($current ? 'text-brand-950' : 'text-gray-400') }} text-center leading-tight">{{ $label }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="md:w-56 shrink-0 bg-white rounded-xl border border-ice-200 p-4">
                                    <div class="flex items-center justify-between text-sm font-bold text-gray-500"><span>Progres</span><span class="text-brand-700 text-base tabular-nums">{{ $pct }}%</span></div>
                                    <div class="mt-2 h-2.5 bg-ice-200 rounded-full overflow-hidden"><div class="h-full bg-brand-500 rounded-full" style="width: {{ $pct }}%"></div></div>
                                    <p class="mt-2.5 text-sm text-gray-500">Estimasi selesai: <b class="text-brand-950">{{ $eta ?? 'menunggu jadwal' }}</b></p>
                                </div>
                            </div>
                        </div>

                        {{-- Aksi --}}
                        <div class="mt-5 flex flex-wrap justify-end gap-2.5">
                            <button wire:click="openDetail({{ $order->id }})" class="px-4 py-2.5 text-base font-bold rounded-xl bg-ice-100 text-brand-800 hover:bg-ice-200 transition-colors">Detail</button>
                            @if($isPaid && $pdfUrl)
                                <a href="{{ $pdfUrl }}" target="_blank" class="px-5 py-2.5 text-base font-bold rounded-xl bg-brand-600 text-white hover:bg-brand-700 transition-colors">🧾 Lihat Invoice</a>
                                <a href="{{ $pdfDlUrl }}" class="px-4 py-2.5 text-base font-bold rounded-xl bg-signal-400 text-brand-950 hover:bg-signal-500 transition-colors">⬇ Unduh</a>
                            @elseif($payUrl)
                                <a href="{{ $payUrl }}" target="_blank" class="px-5 py-2.5 text-base font-bold rounded-xl bg-brand-600 text-white hover:bg-brand-700 transition-colors">{{ $isUnpaid ? '💳 Bayar Sekarang' : '🧾 Lihat Invoice' }}</a>
                                <button onclick="copyText('{{ $payUrl }}', this)" class="px-4 py-2.5 text-base font-bold rounded-xl bg-ice-100 text-brand-800 hover:bg-ice-200 transition-colors">🔗 Salin Link</button>
                            @elseif($isUnpaid && $contact['whatsapp'])
                                <a href="https://wa.me/{{ $contact['whatsapp'] }}?text={{ $waMsg }}" target="_blank" class="px-5 py-2.5 text-base font-bold rounded-xl bg-signal-500 text-brand-950 hover:bg-signal-600 transition-colors">💬 Minta Invoice</a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-2xl border border-ice-200 p-16 text-center text-gray-400">
                        <p class="text-xl font-bold">📭 Tidak ada pesanan</p>
                        <p class="text-base mt-2">Coba ubah filter / kata kunci, atau buat pesanan baru.</p>
                    </div>
                @endforelse
            </div>

            @if($orders->hasPages())<div class="pt-2">{{ $orders->links() }}</div>@endif
        </div>

        {{-- KOLOM KANAN: rail sticky --}}
        <div class="xl:col-span-4 space-y-5 xl:sticky xl:top-24 min-w-0">

            @if(($stats['unpaid'] ?? 0) > 0)
                <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5">
                    <p class="text-base font-bold text-amber-800">🔔 {{ $stats['unpaid'] }} pesanan menunggu tindakan</p>
                    <p class="text-sm text-amber-700 mt-1">Invoice belum terbit atau pembayaran belum selesai.</p>
                    <button wire:click="setFilter('unpaid')" class="mt-3 w-full px-4 py-2.5 text-base font-bold rounded-xl bg-amber-500 text-white hover:bg-amber-600 transition-colors">Lihat Sekarang →</button>
                </div>
            @endif

            <div class="bg-white rounded-2xl border border-ice-200 shadow-sm p-6">
                <h3 class="text-xl font-bold text-brand-950 mb-4">🏪 Kontak {{ $contact['name'] }}</h3>
                @if($contact['whatsapp'])
                    <a href="https://wa.me/{{ $contact['whatsapp'] }}?text={{ urlencode('Halo Admin '.$contact['name'].', saya '.auth()->user()->name.'. Saya butuh bantuan terkait pesanan saya.') }}" target="_blank"
                       class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-signal-500 hover:bg-signal-600 text-brand-950 text-base font-extrabold rounded-xl transition-colors">
                        💬 Chat WhatsApp Admin
                    </a>
                @else
                    <p class="text-sm text-amber-700 bg-amber-50 border border-amber-200 rounded-lg p-3">Nomor admin belum diatur. Set <code>BRAND_WHATSAPP</code> di .env.</p>
                @endif
                <div class="mt-4 space-y-1.5 text-base text-gray-500">
                    @if($contact['address'])<p>📍 {{ $contact['address'] }}</p>@endif
                    <p>🕒 {{ $contact['hours'] }}</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-ice-200 shadow-sm p-6">
                <h3 class="text-xl font-bold text-brand-950 mb-4">💳 Cara Pembayaran</h3>
                <ol class="list-decimal list-inside space-y-2 text-base text-gray-600">
                    <li>Tunggu admin menerbitkan <b>invoice</b>.</li>
                    <li>Klik <b>💳 Bayar Sekarang</b> (magic link).</li>
                    <li>Transfer & <b>upload bukti</b> di halaman tersebut.</li>
                    <li>Admin verifikasi → pesanan masuk <b>produksi</b>.</li>
                    <li>Invoice <b>lunas</b> = PDF ber-stempel LUNAS.</li>
                </ol>
                <p class="text-sm text-gray-400 mt-4">Invoice belum terbit? Gunakan tombol <b>💬 Minta Invoice</b>.</p>
            </div>
        </div>
    </div>

    {{-- MODAL DETAIL --}}
    @if($detailOrder)
        @php
            $dmeta = $statusMeta[$detailOrder->status] ?? ['label' => $detailOrder->status, 'badge' => 'bg-ice-200 text-brand-800', 'step' => 0];
            $dInv = $detailOrder->invoice;
            $dTotal = (float) ($dInv->total_amount ?? 0);
            $dPaid = (float) ($dInv->total_paid ?? 0);
            $dIsPaid = $dInv && (($dInv->status === 'paid') || ($dTotal > 0 && $dPaid >= $dTotal));
            $dPayUrl = ($dInv && $dInv->payment_token) ? route('payment.show', $dInv->payment_token) : null;
            $dPdfUrl = $dInv ? route('customer.invoices.pdf', $dInv->id) : null;
            $dPdfDl = $dInv ? route('customer.invoices.download', $dInv->id) : null;
        @endphp
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-brand-950/50 backdrop-blur-sm" wire:click.self="closeDetail">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl max-h-[85vh] overflow-y-auto">
                <div class="sticky top-0 bg-white px-6 py-5 border-b border-ice-200 flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-extrabold text-brand-950">{{ $detailOrder->invoice_number }}</h3>
                        <span class="inline-block mt-1.5 px-3 py-1 text-sm font-bold rounded-full {{ $dmeta['badge'] }}">{{ $dmeta['label'] }}</span>
                        @if($dIsPaid)<span class="inline-block mt-1.5 ml-1.5 px-3 py-1 text-sm font-extrabold rounded-full bg-signal-400 text-brand-950">LUNAS</span>@endif
                    </div>
                    <button wire:click="closeDetail" class="text-gray-400 hover:text-gray-600 text-2xl">✕</button>
                </div>
                <div class="p-6 space-y-6">
                    <div class="overflow-x-auto rounded-xl border border-ice-200">
                        <table class="w-full text-base">
                            <thead class="bg-ice-50 text-sm text-gray-500"><tr><th class="px-4 py-2.5 text-left">Layanan</th><th class="px-4 py-2.5 text-left">Bahan</th><th class="px-4 py-2.5 text-center">Luas / Qty</th><th class="px-4 py-2.5 text-right">Subtotal</th></tr></thead>
                            <tbody class="divide-y divide-ice-100">
                                @foreach($detailOrder->items as $item)
                                    <tr><td class="px-4 py-3">{{ $item->service->name ?? '-' }}</td><td class="px-4 py-3 text-gray-600">{{ $item->material->name ?? '-' }}</td><td class="px-4 py-3 text-center text-gray-600">{{ number_format($item->area_m2 ?? 0, 2) }} m² / {{ $item->quantity ?? 1 }}</td><td class="px-4 py-3 text-right font-semibold">Rp {{ number_format($item->final_subtotal ?? 0, 0, ',', '.') }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="rounded-xl bg-ice-50 border border-ice-200 p-5 space-y-2 text-base">
                        <div class="flex justify-between"><span class="text-gray-500">Total Tagihan</span><span class="font-bold">Rp {{ number_format($dTotal, 0, ',', '.') }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Sudah Dibayar</span><span class="font-bold text-signal-600">Rp {{ number_format($dPaid, 0, ',', '.') }}</span></div>
                        <div class="flex justify-between border-t border-ice-200 pt-2"><span class="text-gray-500">Sisa Tagihan</span><span class="font-extrabold {{ ($dTotal - $dPaid) > 0 ? 'text-red-600' : 'text-signal-600' }}">Rp {{ number_format(max(0, $dTotal - $dPaid), 0, ',', '.') }}</span></div>
                    </div>
                    @if($dIsPaid && $dPdfUrl)
                        <div class="flex gap-2.5">
                            <a href="{{ $dPdfUrl }}" target="_blank" class="flex-1 text-center px-4 py-3.5 bg-brand-600 text-white text-base font-bold rounded-xl hover:bg-brand-700 transition-colors">🧾 Lihat Invoice (PDF Lunas)</a>
                            <a href="{{ $dPdfDl }}" class="px-4 py-3.5 bg-signal-400 text-brand-950 text-base font-bold rounded-xl hover:bg-signal-500 transition-colors">⬇</a>
                        </div>
                    @elseif($dPayUrl)
                        <div class="flex gap-2.5">
                            <a href="{{ $dPayUrl }}" target="_blank" class="flex-1 text-center px-4 py-3.5 bg-brand-600 text-white text-base font-bold rounded-xl hover:bg-brand-700 transition-colors">💳 Buka Halaman Pembayaran</a>
                            <button onclick="copyText('{{ $dPayUrl }}', this)" class="px-4 py-3.5 bg-ice-100 text-brand-800 text-base font-bold rounded-xl hover:bg-ice-200 transition-colors">🔗</button>
                        </div>
                    @elseif($contact['whatsapp'])
                        <a href="https://wa.me/{{ $contact['whatsapp'] }}?text={{ urlencode('Halo Admin, mohon terbitkan invoice untuk pesanan '.$detailOrder->invoice_number.'.') }}" target="_blank" class="block w-full text-center px-4 py-3.5 bg-signal-500 text-brand-950 text-base font-bold rounded-xl hover:bg-signal-600 transition-colors">💬 Minta Invoice ke Admin</a>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>