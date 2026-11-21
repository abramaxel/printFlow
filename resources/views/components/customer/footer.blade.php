 @php
    $brandName = config('app.brand.name', 'FC Murni Jaya');
    $waRaw = preg_replace('/[^0-9]/', '', (string) (config('app.brand.whatsapp') ?: config('app.brand.phone') ?: ''));
    if ($waRaw !== '' && str_starts_with($waRaw, '0')) $waRaw = '62' . substr($waRaw, 1);
    $address   = (string) config('app.brand.address', '');
    $hours     = (string) config('app.brand.hours', 'Senin–Sabtu, 08.00–17.00');
    $brandMail = config('app.brand.email');
    $openHour  = (int) config('app.brand.open_hour', 8);
    $closeHour = (int) config('app.brand.close_hour', 17);
    $openDays  = config('app.brand.open_days', [1, 2, 3, 4, 5, 6]);

    $F = [
        'home'    => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
        'archive' => 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4',
        'user'    => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
        'cog'     => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
        'chat'    => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z',
        'pin'     => 'M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z',
        'clock'   => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        'mail'    => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
        'up'      => 'M5 10l7-7m0 0l7 7m-7-7v18',
        'shield'  => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
        'doc'     => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'printer' => 'M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z',
        'copy'    => 'M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z',
        'x'       => 'M6 18L18 6M6 6l12 12',
        'bolt'    => 'M13 10V3L4 14h7v7l9-11h-7z',
    ];
    $fic = fn($k, $c = 'w-4 h-4') => '<svg class="' . $c . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="' . ($F[$k] ?? $F['doc']) . '"/></svg>';

    $footerNav = [
        ['r' => 'customer.dashboard', 'i' => 'home',    'l' => 'Dashboard'],
        ['r' => 'customer.history',   'i' => 'archive', 'l' => 'Riwayat Pesanan'],
        ['r' => 'customer.profile',   'i' => 'user',    'l' => 'Profil Saya'],
        ['r' => 'customer.settings',  'i' => 'cog',     'l' => 'Pengaturan Akun'],
    ];
    $capabilities = ['Spanduk & Baliho', 'Backdrop & Wall Mural', 'Brosur & Flyer', 'Stiker & Cutting', 'Kartu Nama', 'Undangan'];
@endphp

<footer id="portal-footer" class="relative mt-14 text-white"
        x-data="{
            modal: null,
            jam: '--:--:--',
            buka: false,
            catatan: '',
            tick() {
                try {
                    const parts = new Intl.DateTimeFormat('en-GB', { timeZone: 'Asia/Jakarta', weekday: 'short', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false }).formatToParts(new Date());
                    const get = t => (parts.find(p => p.type === t) || {}).value || '';
                    const map = { Mon: 1, Tue: 2, Wed: 3, Thu: 4, Fri: 5, Sat: 6, Sun: 7 };
                    const d = map[get('weekday')] || 0;
                    const h = parseInt(get('hour'), 10) || 0;
                    const m = parseInt(get('minute'), 10) || 0;
                    const s = parseInt(get('second'), 10) || 0;
                    const p = n => String(n).padStart(2, '0');
                    this.jam = p(h) + ':' + p(m) + ':' + p(s);
                    const days = @json($openDays);
                    const oh = {{ $openHour }}, ch = {{ $closeHour }};
                    this.buka = days.includes(d) && h >= oh && h < ch;
                    this.catatan = this.buka ? ('Melayani sampai ' + p(ch) + '.00 WIB') : ('Layanan dibuka ' + p(oh) + '.00 WIB');
                } catch (e) {
                    this.jam = new Date().toLocaleTimeString('id-ID');
                    this.catatan = 'Jam operasional mengikuti waktu toko';
                }
            }
        }"
        x-init="tick(); setInterval(() => tick(), 1000)"
        x-effect="document.body.style.overflow = modal ? 'hidden' : ''"
        @keydown.escape.window="modal = null">

    <style>
        #portal-footer .ft-reveal { transition: opacity .65s cubic-bezier(.2,.7,.2,1), transform .65s cubic-bezier(.2,.7,.2,1); }
        #portal-footer[data-reveal-ready] .ft-reveal { opacity: 0; transform: translateY(20px); }
        #portal-footer[data-reveal-ready] .ft-reveal.is-in { opacity: 1; transform: none; }
        @keyframes ftAurora { 0%,100% { transform: translate3d(0,0,0) scale(1); } 50% { transform: translate3d(28px,-16px,0) scale(1.08); } }
        #portal-footer .ft-aurora { animation: ftAurora 18s ease-in-out infinite; }
        #portal-footer .ft-aurora-slow { animation: ftAurora 26s ease-in-out infinite reverse; }
        @keyframes ftShine { 0% { transform: translateX(-140%) skewX(-14deg); } 60%,100% { transform: translateX(140%) skewX(-14deg); } }
        #portal-footer .ft-shine { animation: ftShine 8s ease-in-out infinite; }
        @keyframes ftLive { 0%,100% { box-shadow: 0 0 0 0 rgba(200,236,63,.5); } 50% { box-shadow: 0 0 0 8px rgba(200,236,63,0); } }
        #portal-footer .ft-live { animation: ftLive 2.4s ease-out infinite; }
        #portal-footer .ft-link { transition: color .2s ease, transform .2s ease; }
        #portal-footer .ft-link:hover { transform: translateX(4px); }
        .ft-scroll::-webkit-scrollbar { width: 8px; }
        .ft-scroll::-webkit-scrollbar-track { background: #e2edf0; border-radius: 8px; }
        .ft-scroll::-webkit-scrollbar-thumb { background: #1d6247; border-radius: 8px; }
        .ft-scroll::-webkit-scrollbar-thumb:hover { background: #0a3f33; }
        @media (prefers-reduced-motion: reduce) {
            #portal-footer .ft-aurora, #portal-footer .ft-aurora-slow, #portal-footer .ft-shine, #portal-footer .ft-live { animation: none !important; }
        }
    </style>

    {{-- Transisi gelombang dari workspace terang ke footer gelap --}}
    <svg class="absolute -top-px left-0 w-full h-14 sm:h-20 text-ice-100 pointer-events-none" viewBox="0 0 1440 90" fill="currentColor" preserveAspectRatio="none" aria-hidden="true">
        <path d="M0,0 H1440 V26 C1180,74 980,6 720,32 C460,58 240,16 0,40 Z"/>
    </svg>

    <div class="forest-hero relative overflow-hidden">
        <div class="forest-dots absolute inset-0 opacity-50"></div>
        <div class="ft-aurora absolute -top-24 -left-16 w-[26rem] h-[26rem] rounded-full bg-emerald-500/12 blur-3xl"></div>
        <div class="ft-aurora-slow absolute -bottom-28 right-0 w-[24rem] h-[24rem] rounded-full bg-signal-400/10 blur-3xl"></div>
        <div class="absolute inset-y-0 left-2/3 w-28 rotate-12 bg-gradient-to-b from-signal-400/0 via-signal-400/8 to-signal-400/0 blur-2xl"></div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 sm:pt-28 pb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-10 lg:gap-8">

                {{-- Brand + status operasional --}}
                <div class="lg:col-span-4 ft-reveal">
                    <div class="flex items-center gap-3">
                        <span class="relative overflow-hidden w-12 h-12 rounded-2xl bg-gradient-to-br from-signal-300 to-signal-500 text-brand-950 flex items-center justify-center shadow-lg shadow-signal-400/25">
                            <span class="ft-shine absolute inset-0 bg-gradient-to-r from-transparent via-white/50 to-transparent"></span>
                            <span class="relative">{!! $fic('printer', 'w-6 h-6') !!}</span>
                        </span>
                        <div class="min-w-0">
                            <p class="text-xl font-extrabold tracking-tight truncate">{{ $brandName }}</p>
                            <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-signal-300">Portal Pelanggan</p>
                        </div>
                    </div>

                    <p class="mt-5 text-base leading-relaxed text-emerald-100/70 max-w-sm">
                        Sistem informasi percetakan terintegrasi — pesan, bayar, dan lacak produksi cetakan Anda dari satu tempat, kapan saja.
                    </p>

                    {{-- Jam live + status buka/tutup --}}
                    <div class="mt-6 inline-flex flex-col gap-2 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-sm px-4 py-3.5">
                        <div class="flex items-center gap-2.5">
                            <span class="w-2.5 h-2.5 rounded-full shrink-0" :class="buka ? 'bg-signal-400 ft-live' : 'bg-red-400'"></span>
                            <span class="text-sm font-extrabold" :class="buka ? 'text-signal-300' : 'text-red-300'" x-text="buka ? 'Sedang Melayani' : 'Di Luar Jam Layanan'"></span>
                        </div>
                        <div class="flex items-center gap-2 text-emerald-100/70">
                            {!! $fic('clock', 'w-4 h-4') !!}
                            <span class="text-sm font-bold tabular-nums" x-text="jam + ' WIB'"></span>
                        </div>
                        <p class="text-xs text-emerald-100/50" x-text="catatan"></p>
                    </div>
                </div>

                {{-- Navigasi --}}
                <div class="lg:col-span-2 ft-reveal">
                    <h3 class="text-xs font-extrabold uppercase tracking-[0.22em] text-signal-300">Navigasi</h3>
                    <ul class="mt-5 space-y-3">
                        @foreach($footerNav as $n)
                            <li>
                                <a href="{{ route($n['r']) }}" class="ft-link inline-flex items-center gap-2.5 text-base font-semibold text-emerald-100/75 hover:text-signal-300">
                                    <span class="text-brand-300">{!! $fic($n['i'], 'w-4 h-4') !!}</span>{{ $n['l'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Layanan --}}
                <div class="lg:col-span-3 ft-reveal">
                    <h3 class="text-xs font-extrabold uppercase tracking-[0.22em] text-signal-300">Layanan Cetak</h3>
                    <div class="mt-5 flex flex-wrap gap-2">
                        @foreach($capabilities as $c)
                            <span class="px-3 py-1.5 rounded-full bg-white/5 border border-white/10 text-sm font-semibold text-emerald-100/75">{{ $c }}</span>
                        @endforeach
                    </div>
                    <div class="mt-5 flex items-start gap-2.5 rounded-2xl bg-signal-400/10 border border-signal-400/25 px-4 py-3">
                        <span class="text-signal-300 shrink-0 mt-0.5">{!! $fic('bolt', 'w-4 h-4') !!}</span>
                        <p class="text-sm text-emerald-100/80">Butuh cepat? Pilih durasi <b class="text-signal-300">Kilat</b> saat memesan agar masuk prioritas produksi.</p>
                    </div>
                </div>

                {{-- Kontak & bantuan --}}
                <div class="lg:col-span-3 ft-reveal">
                    <h3 class="text-xs font-extrabold uppercase tracking-[0.22em] text-signal-300">Hubungi Kami</h3>

                    @if($waRaw)
                        <a href="https://wa.me/{{ $waRaw }}?text={{ urlencode('Halo Admin ' . $brandName . ', saya ' . auth()->user()->name . '. Saya butuh bantuan terkait pesanan saya.') }}"
                           target="_blank" rel="noopener"
                           class="mt-5 flex items-center justify-center gap-2 w-full px-4 py-3 rounded-xl bg-signal-400 text-brand-950 text-base font-extrabold hover:bg-signal-500 shadow-md shadow-signal-400/20 hover:-translate-y-0.5 transition-all">
                            {!! $fic('chat', 'w-4 h-4') !!} Chat WhatsApp
                        </a>
                        <button onclick="copyText('+{{ $waRaw }}', this)"
                                class="mt-2.5 w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 text-sm font-bold text-emerald-100/80 hover:bg-white/10 hover:text-signal-300 transition-colors">
                            {!! $fic('copy', 'w-3.5 h-3.5') !!} +{{ $waRaw }}
                        </button>
                    @endif

                    <div class="mt-4 space-y-3 text-sm text-emerald-100/70">
                        @if($address)
                            <p class="flex items-start gap-2.5"><span class="text-brand-300 shrink-0 mt-0.5">{!! $fic('pin', 'w-4 h-4') !!}</span><span>{{ $address }}</span></p>
                        @endif
                        <p class="flex items-start gap-2.5"><span class="text-brand-300 shrink-0 mt-0.5">{!! $fic('clock', 'w-4 h-4') !!}</span><span>{{ $hours }}</span></p>
                        @if($brandMail)
                            <p class="flex items-start gap-2.5"><span class="text-brand-300 shrink-0 mt-0.5">{!! $fic('mail', 'w-4 h-4') !!}</span><span class="break-all">{{ $brandMail }}</span></p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Bottom bar --}}
            <div class="mt-12 pt-6 border-t border-white/10 flex flex-col lg:flex-row items-center justify-between gap-5 ft-reveal">
                <div class="flex flex-col sm:flex-row items-center gap-3 sm:gap-5 text-center sm:text-left">
                    <p class="text-sm text-emerald-100/60">&copy; {{ now()->format('Y') }} <b class="text-emerald-100/85">{{ $brandName }}</b>. Hak cipta dilindungi.</p>
                    <span class="hidden sm:block w-1 h-1 rounded-full bg-white/20"></span>
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/5 border border-white/10 text-xs font-bold text-emerald-100/70">
                        <span class="w-1.5 h-1.5 rounded-full bg-signal-400 ft-live"></span> Portal Online
                    </span>
                </div>

                <div class="flex flex-wrap items-center justify-center gap-2">
                    <button @click="modal = 'terms'" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold text-emerald-100/75 hover:bg-white/10 hover:text-signal-300 transition-colors">
                        {!! $fic('doc', 'w-4 h-4') !!} Syarat &amp; Ketentuan
                    </button>
                    <button @click="modal = 'privacy'" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold text-emerald-100/75 hover:bg-white/10 hover:text-signal-300 transition-colors">
                        {!! $fic('shield', 'w-4 h-4') !!} Kebijakan Privasi
                    </button>
                    <button onclick="window.scrollTo({ top: 0, behavior: 'smooth' })"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-signal-400 text-brand-950 text-sm font-extrabold hover:bg-signal-500 hover:-translate-y-0.5 transition-all">
                        {!! $fic('up', 'w-4 h-4') !!} Ke Atas
                    </button>
                </div>
            </div>
        </div>

        <div class="relative z-10 h-1 bg-gradient-to-r from-brand-700 via-signal-400 to-brand-700"></div>
    </div>

    {{-- ===== MODAL: SYARAT & PRIVASI ===== --}}
    <div x-show="modal" x-cloak x-transition.opacity class="fixed inset-0 z-[60] flex items-end sm:items-center justify-center p-0 sm:p-4 bg-brand-950/70 backdrop-blur-sm" @click.self="modal = null">
        <div x-show="modal" x-transition:enter="transition ease-out duration-250" x-transition:enter-start="opacity-0 translate-y-6 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             class="w-full sm:max-w-2xl max-h-[88vh] flex flex-col overflow-hidden rounded-t-3xl sm:rounded-3xl bg-ice-50 shadow-2xl">

            <div class="forest-hero relative overflow-hidden px-6 py-5 flex items-start justify-between gap-4 shrink-0">
                <div class="forest-dots absolute inset-0 opacity-40"></div>
                <div class="relative z-10 min-w-0">
                    <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-signal-300">{{ $brandName }}</p>
                    <h3 class="mt-1 text-xl font-extrabold text-white tracking-tight" x-text="modal === 'terms' ? 'Syarat & Ketentuan Layanan' : 'Kebijakan Privasi'"></h3>
                </div>
                <button @click="modal = null" class="relative z-10 shrink-0 w-9 h-9 flex items-center justify-center rounded-xl bg-white/10 text-white hover:bg-white/20 transition-colors" aria-label="Tutup">{!! $fic('x', 'w-5 h-5') !!}</button>
            </div>

            <div class="ft-scroll flex-1 overflow-y-auto px-6 py-6 text-gray-700">
                {{-- Syarat --}}
                <div x-show="modal === 'terms'" class="space-y-5">
                    <p class="text-base leading-relaxed">Dengan menggunakan portal ini, Anda menyetujui ketentuan berikut yang mengatur pemesanan, pembayaran, dan produksi cetakan di {{ $brandName }}.</p>
                    @foreach([
                        ['Pemesanan & Konfirmasi', 'Pesanan tercatat setelah Anda atau admin membuat order. Pesanan baru berstatus "Menunggu Invoice" dan baru dianggap sah untuk diproses setelah admin menerbitkan invoice resmi berisi rincian layanan, bahan, ukuran, dan nominal.'],
                        ['Harga & Pembayaran', 'Harga dihitung berdasarkan luas (m²), jumlah, jenis bahan, dan durasi pengerjaan yang dipilih. Seluruh harga sudah termasuk pajak. Pembayaran dilakukan melalui transfer ke rekening resmi yang tertera pada halaman pembayaran (magic link), lalu bukti transfer diunggah pada halaman tersebut.'],
                        ['Verifikasi & Mulai Produksi', 'Tim admin memverifikasi bukti transfer terlebih dahulu. Setelah pembayaran atau uang muka dinyatakan valid, pesanan masuk antrian produksi. Selama verifikasi belum selesai, pesanan belum dijadwalkan pada mesin cetak.'],
                        ['Waktu Pengerjaan', 'Estimasi selesai tertera pada setiap pesanan. Pilihan durasi kilat dikenakan biaya tambahan dan menempatkan pesanan pada prioritas lebih tinggi. Estimasi dapat bergeser bila terjadi keterlambatan bahan baku atau revisi desain dari pihak pelanggan.'],
                        ['Desain & Hak Cipta', 'Pelanggan menjamin bahwa file desain yang diunggah adalah milik sendiri atau telah memperoleh izin penggunaan. {{ $brandName }} tidak bertanggung jawab atas tuntutan pihak ketiga terkait materi yang dicetak atas permintaan pelanggan.'],
                        ['Revisi & Pembatalan', 'Revisi desain hanya dapat dilakukan sebelum pesanan masuk tahap cetak. Pembatalan setelah produksi berjalan tidak dapat dikembalikan penuh karena bahan telah terpakai; sisa dana diperhitungkan oleh admin sesuai progres pengerjaan.'],
                        ['Serah Terima & Komplain', 'Periksa jumlah dan kondisi cetakan saat serah terima. Komplain kerusakan atau ketidaksesuaian disampaikan maksimal 2×24 jam setelah pesanan diterima, disertai foto, melalui kanal bantuan resmi.'],
                        ['Keadaan Kahar', 'Gangguan listrik, kerusakan mesin, bencana, atau gangguan pasokan bahan dapat memengaruhi jadwal. Bila terjadi, admin akan menghubungi Anda dan menawarkan jadwal ulang atau pengembalian dana sesuai kesepakatan.'],
                    ] as $i => $s)
                        <div class="rounded-2xl border border-ice-200 bg-white p-5">
                            <div class="flex items-center gap-3">
                                <span class="shrink-0 w-7 h-7 rounded-lg bg-brand-600 text-signal-300 flex items-center justify-center text-sm font-extrabold">{{ $i + 1 }}</span>
                                <h4 class="text-base font-extrabold text-brand-950">{{ $s[0] }}</h4>
                            </div>
                            <p class="mt-2.5 text-base leading-relaxed">{{ $s[1] }}</p>
                        </div>
                    @endforeach
                </div>

                {{-- Privasi --}}
                <div x-show="modal === 'privacy'" class="space-y-5">
                    <p class="text-base leading-relaxed">{{ $brandName }} menghormati privasi Anda. Kebijakan ini menjelaskan data apa yang kami simpan, untuk apa digunakan, dan bagaimana kami melindunginya.</p>
                    @foreach([
                        ['Data yang Kami Kumpulkan', 'Nama, alamat email, nomor telepon/WhatsApp, alamat pengiriman, file desain yang Anda unggah, bukti transfer, serta riwayat pesanan dan pembayaran.'],
                        ['Tujuan Penggunaan', 'Data digunakan semata-mata untuk memproses pesanan, menerbitkan invoice, memverifikasi pembayaran, menjalankan produksi, mengatur pengiriman, dan mengabari status pesanan Anda.'],
                        ['Penyimpanan & Keamanan', 'Data disimpan pada basis data dengan kata sandi yang dienkripsi. Akses ke area administrasi dibatasi berdasarkan peran (owner, admin, customer service, produksi), sehingga staf hanya melihat data yang relevan dengan tugasnya.'],
                        ['Berbagi dengan Pihak Ketiga', 'Kami tidak menjual data Anda. Informasi alamat dan kontak hanya dibagikan kepada pihak pengiriman sebatas yang diperlukan untuk mengantar pesanan.'],
                        ['Cookie & Sesi', 'Portal menggunakan cookie sesi untuk menjaga Anda tetap masuk dan melindungi formulir dari pemalsuan permintaan. Kami tidak memasang pelacak iklan pihak ketiga.'],
                        ['Hak Anda', 'Anda dapat melihat dan memperbarui nama, email, telepon, serta alamat melalui halaman Pengaturan Akun, dan mengganti kata sandi kapan saja. Untuk permintaan penghapusan akun, hubungi admin melalui kanal bantuan resmi.'],
                        ['Masa Penyimpanan', 'Riwayat transaksi dan invoice disimpan selama diperlukan untuk keperluan pembukuan, audit stok, serta layanan purna jual dan komplain.'],
                        ['Perubahan Kebijakan', 'Bila kebijakan ini diperbarui, versi terbaru akan selalu tersedia pada bagian ini di footer portal.'],
                    ] as $i => $s)
                        <div class="rounded-2xl border border-ice-200 bg-white p-5">
                            <div class="flex items-center gap-3">
                                <span class="shrink-0 w-7 h-7 rounded-lg bg-brand-600 text-signal-300 flex items-center justify-center text-sm font-extrabold">{{ $i + 1 }}</span>
                                <h4 class="text-base font-extrabold text-brand-950">{{ $s[0] }}</h4>
                            </div>
                            <p class="mt-2.5 text-base leading-relaxed">{{ $s[1] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="shrink-0 px-6 py-4 border-t border-ice-200 bg-white flex items-center justify-between gap-3">
                <p class="text-sm text-gray-500">Terakhir diperbarui {{ now()->format('d M Y') }}</p>
                <button @click="modal = null" class="px-5 py-2.5 rounded-xl bg-brand-600 text-white text-base font-bold hover:bg-brand-700 transition-colors">Saya Mengerti</button>
            </div>
        </div>
    </div>

    <script>
        (function () {
            var f = document.getElementById('portal-footer');
            if (!f) return;
            var items = f.querySelectorAll('.ft-reveal');
            var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            if (!('IntersectionObserver' in window) || reduce) {
                items.forEach(function (el) { el.classList.add('is-in'); });
                return;
            }
            f.setAttribute('data-reveal-ready', '');
            var io = new IntersectionObserver(function (entries) {
                entries.forEach(function (e) {
                    if (e.isIntersecting) { e.target.classList.add('is-in'); io.unobserve(e.target); }
                });
            }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });
            items.forEach(function (el) { io.observe(el); });
        })();
    </script>
</footer>