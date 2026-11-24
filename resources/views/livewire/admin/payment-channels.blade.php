@php
    $K = [
        'card'=>'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',
        'qr'=>'M4 4h6v6H4V4zm10 0h6v6h-6V4zM4 14h6v6H4v-6zm10 0h2v2h-2v-2zm4 0h2v2h-2v-2zm-4 4h2v2h-2v-2zm4 0h2v2h-2v-2z',
        'wallet'=>'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
        'cash'=>'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
        'check'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        'alert'=>'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
        'x'=>'M6 18L18 6M6 6l12 12',
        'upload'=>'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12',
        'trash'=>'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16',
        'save'=>'M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4',
        'lock'=>'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z',
        'shield'=>'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
        'bolt'=>'M13 10V3L4 14h7v7l9-11h-7z',
        'store'=>'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z',
    ];
    $kic = fn($k,$c='w-5 h-5') => '<svg class="'.$c.'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="'.($K[$k] ?? $K['card']).'"/></svg>';
@endphp

<div class="w-full space-y-4">
    <style>
        .pc-grid{background-image:linear-gradient(rgba(200,236,63,.08) 1px,transparent 1px),linear-gradient(90deg,rgba(200,236,63,.08) 1px,transparent 1px);background-size:30px 30px}
        .pc-dots{background-image:radial-gradient(rgba(10,63,51,.10) 1px,transparent 1px);background-size:18px 18px}
        @keyframes pcRise{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:none}}
        .pc-rise{animation:pcRise .4s cubic-bezier(.2,.7,.2,1) both}
        @keyframes pcLive{0%,100%{box-shadow:0 0 0 0 rgba(200,236,63,.45)}50%{box-shadow:0 0 0 10px rgba(200,236,63,0)}}
        .pc-live{animation:pcLive 2.4s ease-out infinite}
        .pc-field{transition:box-shadow .18s,border-color .18s,background-color .18s}
        @media (prefers-reduced-motion:reduce){.pc-rise,.pc-live{animation:none!important}}
    </style>

    @if(session('pc_success'))
        <div x-data="{show:true}" x-init="setTimeout(()=>show=false,5500)" x-show="show" x-cloak
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-3"
             class="fixed top-24 right-4 sm:right-6 z-[80] max-w-sm" role="status" aria-live="polite">
            <div class="forest-hero relative overflow-hidden rounded-2xl px-5 py-4 shadow-2xl shadow-brand-950/40 flex items-start gap-3">
                <div class="forest-dots absolute inset-0 opacity-40"></div>
                <span class="relative z-10 shrink-0 w-9 h-9 rounded-xl bg-signal-400 text-brand-950 flex items-center justify-center">{!! $kic('check','w-5 h-5') !!}</span>
                <p class="relative z-10 flex-1 text-base font-bold text-white">{{ session('pc_success') }}</p>
                <button type="button" @click="show=false" class="relative z-10 shrink-0 text-white/70 hover:text-white">{!! $kic('x','w-4 h-4') !!}</button>
            </div>
        </div>
    @endif

    <div class="forest-hero relative overflow-hidden rounded-3xl text-white shadow-lg shadow-brand-950/25 pc-rise">
        <div class="forest-dots absolute inset-0 opacity-40"></div>
        <div class="pc-grid absolute inset-0 opacity-45"></div>
        <div class="absolute -top-24 -left-16 w-72 h-72 rounded-full bg-emerald-500/12 blur-3xl"></div>

        <div class="relative z-10 px-6 sm:px-8 py-6 flex flex-col xl:flex-row xl:items-start justify-between gap-5">
            <div class="min-w-0">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/8 border border-white/15 text-[10px] font-bold uppercase tracking-[0.2em] text-signal-300">{!! $kic('lock','w-3 h-3') !!} Khusus Owner</span>
                <h1 class="mt-3 text-2xl sm:text-3xl font-extrabold tracking-tight">Kanal Pembayaran</h1>
                <p class="mt-2 text-base text-emerald-100/75 max-w-2xl">Aset resmi yang dilihat pelanggan di halaman bayar. Semua kanal berujung unggah bukti — tanpa payment gateway, tanpa EDC.</p>
            </div>
            <div class="shrink-0 grid grid-cols-2 gap-2.5">
                @foreach([['Transfer Bank',$bankReady,'card'],['QRIS Statis',$qrisReady,'qr'],['E-Wallet',$walletReady,'wallet'],['Tunai',true,'cash']] as $s)
                    <div class="rounded-xl px-4 py-3 border {{ $s[1] ? 'bg-signal-400 border-signal-400 text-brand-950' : 'bg-white/8 border-white/15' }}">
                        <span class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider {{ $s[1] ? '' : 'text-emerald-100/60' }}">{!! $kic($s[2],'w-3.5 h-3.5') !!} {{ $s[0] }}</span>
                        <span class="mt-0.5 block text-sm font-extrabold">{{ $s[1] ? 'Siap' : 'Belum diatur' }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @if(!$ready)
        <div class="rounded-3xl p-8 text-white shadow-xl shadow-amber-950/25 pc-rise" style="background:linear-gradient(135deg,#78350f 0%,#4d2c0a 55%,#291505 100%)">
            <div class="flex items-start gap-4">
                <span class="shrink-0 w-14 h-14 rounded-2xl bg-white text-amber-700 flex items-center justify-center">{!! $kic('alert','w-7 h-7') !!}</span>
                <div>
                    <h2 class="text-xl font-extrabold">Tabel pengaturan belum siap</h2>
                    <p class="mt-2 text-base text-amber-50/85">Jalankan migrasi agar kanal pembayaran dapat disimpan.</p>
                    <div class="mt-4 rounded-xl bg-brand-950/40 border border-white/15 px-4 py-3 font-mono text-sm text-signal-300">php artisan migrate</div>
                </div>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-4 items-start">
            <div class="xl:col-span-7 space-y-4 min-w-0">
                {{-- QRIS --}}
                <section class="relative overflow-hidden rounded-3xl bg-white border-2 {{ $qrisReady ? 'border-signal-400' : 'border-ice-200' }} shadow-sm pc-rise">
                    <div class="absolute left-0 top-6 bottom-6 w-1.5 rounded-r-full bg-gradient-to-b from-signal-400 to-brand-600"></div>
                    <div class="p-6 pl-7 sm:pl-8">
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 rounded-full bg-brand-600 text-signal-300 flex items-center justify-center text-sm font-black shrink-0">01</span>
                            <div>
                                <h2 class="text-lg font-extrabold text-brand-950 tracking-tight">QRIS Statis Toko</h2>
                                <p class="text-sm text-gray-500">Gambar QRIS resmi dari bank/acquirer Anda. Pelanggan scan lalu ketik nominal di aplikasinya.</p>
                            </div>
                        </div>

                        <div class="mt-5 flex flex-col sm:flex-row gap-5">
                            <div class="shrink-0 self-start">
                                @if($qris && str_starts_with((string) $qris->getMimeType(),'image/'))
                                    <img src="{{ $qris->temporaryUrl() }}" alt="Pratinjau QRIS" class="w-40 h-40 rounded-2xl object-contain border-2 border-signal-400 bg-white p-2">
                                @elseif($qrisUrl)
                                    <img src="{{ $qrisUrl }}" alt="QRIS toko" class="w-40 h-40 rounded-2xl object-contain border border-ice-300 bg-white p-2">
                                @else
                                    <div class="w-40 h-40 rounded-2xl border-2 border-dashed border-ice-300 bg-ice-50 flex items-center justify-center text-brand-300">{!! $kic('qr','w-12 h-12') !!}</div>
                                @endif
                            </div>

                            <div class="flex-1 min-w-0">
                                <form wire:submit="saveQris">
                                    <input id="pc-qris" type="file" wire:model="qris" class="sr-only" accept="image/jpeg,image/png,image/jpg,image/webp">
                                    <div class="flex flex-wrap gap-2">
                                        <label for="pc-qris" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-ice-100 border border-ice-300 text-sm font-bold text-brand-800 hover:bg-ice-200 cursor-pointer transition-colors">{!! $kic('upload','w-4 h-4') !!} Pilih Gambar</label>
                                        <button type="submit" @disabled(!$qris) class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-brand-600 text-signal-300 text-sm font-extrabold hover:bg-brand-700 transition-colors disabled:opacity-40" wire:loading.attr="disabled" wire:target="qris">
                                            {!! $kic('save','w-4 h-4') !!}
                                            <span wire:loading.remove wire:target="qris">Unggah</span>
                                            <span wire:loading wire:target="qris">Mengunggah…</span>
                                        </button>
                                        @if($qrisPath)
                                            <button type="button" wire:click="removeQris" wire:confirm="Hapus gambar QRIS? Invoice berkanal QRIS akan diarahkan ke transfer bank." class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-red-50 border border-red-200 text-sm font-bold text-red-600 hover:bg-red-100 transition-colors">{!! $kic('trash','w-4 h-4') !!} Hapus</button>
                                        @endif
                                    </div>
                                    @error('qris') <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
                                    <p class="mt-2 text-xs text-gray-400">JPG, PNG, WEBP · maksimal 2MB. Gunakan gambar QRIS asli tanpa crop agar tetap terbaca.</p>
                                </form>

                                <div class="mt-4">
                                    <label class="block text-sm font-extrabold text-brand-950 mb-1.5">Nama Merchant / NMID</label>
                                    <input type="text" wire:model="qris_merchant" maxlength="120" placeholder="FC MURNI JAYA — NMID ID1024xxxxxxx"
                                           class="pc-field w-full px-4 py-3 text-base border border-ice-300 rounded-xl bg-ice-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25">
                                    <p class="mt-1 text-[11px] text-gray-400">Ditampilkan di bawah QR agar pelanggan bisa mencocokkan nama sebelum membayar.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- E-WALLET --}}
                <section class="relative overflow-hidden rounded-3xl bg-white border border-ice-200 shadow-sm pc-rise">
                    <div class="absolute left-0 top-6 bottom-6 w-1.5 rounded-r-full bg-gradient-to-b from-brand-500 to-brand-800"></div>
                    <div class="p-6 pl-7 sm:pl-8">
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 rounded-full bg-brand-600 text-signal-300 flex items-center justify-center text-sm font-black shrink-0">02</span>
                            <div>
                                <h2 class="text-lg font-extrabold text-brand-950 tracking-tight">E-Wallet Toko</h2>
                                <p class="text-sm text-gray-500">Nomor GoPay / OVO / DANA / ShopeePay milik toko untuk transfer saldo.</p>
                            </div>
                        </div>

                        <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-extrabold text-brand-950 mb-1.5">Provider</label>
                                <input type="text" wire:model="ewallet_provider" maxlength="80" placeholder="GoPay / OVO / DANA"
                                       class="pc-field w-full px-4 py-3 text-base border border-ice-300 rounded-xl bg-ice-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25">
                            </div>
                            <div>
                                <label class="block text-sm font-extrabold text-brand-950 mb-1.5">Nomor E-Wallet</label>
                                <input type="tel" wire:model="ewallet_number" maxlength="40" placeholder="081234567890"
                                       class="pc-field w-full px-4 py-3 text-base tabular-nums border border-ice-300 rounded-xl bg-ice-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25 @error('ewallet_number') border-red-400 bg-red-50 @enderror">
                                @error('ewallet_number') <p class="mt-1.5 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-extrabold text-brand-950 mb-1.5">Atas Nama</label>
                                <input type="text" wire:model="ewallet_holder" maxlength="120" placeholder="FC Murni Jaya"
                                       class="pc-field w-full px-4 py-3 text-base border border-ice-300 rounded-xl bg-ice-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25">
                            </div>
                        </div>

                        <button type="button" wire:click="save" class="mt-5 inline-flex items-center gap-2 px-6 py-3.5 rounded-2xl bg-brand-600 text-signal-300 text-base font-extrabold hover:bg-brand-700 shadow-md shadow-brand-900/25 transition-all disabled:opacity-60" wire:loading.attr="disabled" wire:target="save">
                            <span wire:loading.remove wire:target="save" class="inline-flex items-center gap-2">{!! $kic('save','w-4 h-4') !!} Simpan Kanal</span>
                            <span wire:loading wire:target="save">Menyimpan...</span>
                        </button>
                    </div>
                </section>
            </div>

            {{-- RAIL --}}
            <div class="xl:col-span-5 xl:sticky xl:top-24 space-y-4">
                <div class="forest-hero relative overflow-hidden rounded-3xl p-6 text-white shadow-lg shadow-brand-950/20 pc-rise">
                    <div class="forest-dots absolute inset-0 opacity-40"></div>
                    <div class="relative">
                        <p class="text-xs font-bold uppercase tracking-[0.22em] text-signal-300">Cara Kerja di Halaman Bayar</p>
                        <div class="mt-4 space-y-2.5">
                            @foreach([
                                ['CS menetapkan kanal','Saat menerbitkan invoice: transfer, QRIS, e-wallet, atau tunai.'],
                                ['Pelanggan tidak memilih apa pun','Nominal tahap dan kanal sudah terkunci sesuai keputusan CS.'],
                                ['Semua kanal unggah bukti','Transfer, QRIS statis, dan e-wallet sama-sama berujung unggah screenshot.'],
                                ['Tunai tanpa magic link','Halaman bayar hanya menampilkan info; pencatatan dilakukan CS di admin.'],
                            ] as $i => $s)
                                <div class="flex items-start gap-3 rounded-xl bg-white/8 border border-white/15 px-4 py-3">
                                    <span class="shrink-0 w-6 h-6 rounded-lg bg-signal-400 text-brand-950 flex items-center justify-center text-[11px] font-black">{{ $i + 1 }}</span>
                                    <div class="min-w-0">
                                        <p class="text-sm font-extrabold">{{ $s[0] }}</p>
                                        <p class="text-[11px] text-emerald-100/60 leading-snug">{{ $s[1] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl bg-white border-2 border-amber-300 p-5 pc-rise">
                    <div class="flex items-start gap-3">
                        <span class="shrink-0 w-9 h-9 rounded-xl bg-amber-500 text-white flex items-center justify-center">{!! $kic('alert','w-5 h-5') !!}</span>
                        <div class="min-w-0">
                            <p class="text-sm font-extrabold text-amber-900">Soal QRIS dinamis &amp; EDC</p>
                            <p class="mt-1 text-[12px] text-amber-800 leading-snug">QRIS yang nominalnya terisi otomatis hanya bisa dibuat lewat payment gateway berlisensi (Midtrans/Xendit/DOKU) atau EDC acquire bank, plus webhook verifikasi. Selama belum ada integrasi itu, gunakan QRIS statis — pelanggan mengetik nominal di aplikasinya, sistem tetap memverifikasi lewat bukti.</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl bg-white border border-ice-200 p-5 pc-rise">
                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-gray-400">Kanal belum siap?</p>
                    <p class="mt-2 text-[12px] text-gray-600 leading-snug">Bila kanal pilihan CS belum dikonfigurasi di sini, halaman bayar otomatis mengarahkan pelanggan ke transfer bank dengan nominal yang sama, dan menampilkan pemberitahuan jujur. Pastikan rekening di Pengaturan Toko sudah terisi.</p>
                    <a href="{{ route('admin.settings') }}" class="mt-3 inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-ice-100 text-brand-800 text-sm font-bold hover:bg-ice-200 transition-colors">{!! $kic('store','w-4 h-4') !!} Buka Pengaturan Toko</a>
                </div>
            </div>
        </div>
    @endif
</div>