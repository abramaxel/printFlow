@php
    $L = [
        'store'=>'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z',
        'card'=>'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',
        'qr'=>'M4 4h6v6H4V4zm10 0h6v6h-6V4zM4 14h6v6H4v-6zm10 0h2v2h-2v-2zm4 0h2v2h-2v-2zm-4 4h2v2h-2v-2zm4 0h2v2h-2v-2z',
        'wallet'=>'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
        'cash'=>'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
        'check'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        'alert'=>'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
        'clock'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        'x'=>'M6 18L18 6M6 6l12 12',
        'copy'=>'M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z',
        'upload'=>'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12',
        'chat'=>'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z',
        'lock'=>'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z',
        'send'=>'M12 19l9 2-9-18-9 18 9-2zm0 0v-8',
        'eye'=>'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z',
        'chev'=>'M19 9l-7 7-7-7',
        'file'=>'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'pin'=>'M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z',
    ];
    $lic = fn($k,$c='w-4 h-4') => '<svg class="'.$c.'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="'.($L[$k] ?? $L['file']).'"/></svg>';
    $chIcon = match($channel['method']) { 'qris'=>'qr','ewallet'=>'wallet','cash'=>'cash',default=>'card' };
    $PST = ['pending'=>['Menunggu','bg-amber-100 text-amber-800'],'verified'=>['Terverifikasi','bg-signal-100 text-signal-700'],'rejected'=>['Ditolak','bg-red-100 text-red-700']];
@endphp
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Bayar {{ $invoiceNumber }} — {{ $shop['shop_name'] }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .pl-dots{background-image:radial-gradient(rgba(10,63,51,.08) 1px,transparent 1px);background-size:20px 20px}
        .pl-grid{background-image:linear-gradient(rgba(200,236,63,.07) 1px,transparent 1px),linear-gradient(90deg,rgba(200,236,63,.07) 1px,transparent 1px);background-size:30px 30px}
        @keyframes plRise{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:none}}
        .pl-rise{animation:plRise .35s cubic-bezier(.2,.7,.2,1) both}
        @keyframes plLive{0%,100%{box-shadow:0 0 0 0 rgba(200,236,63,.45)}50%{box-shadow:0 0 0 9px rgba(200,236,63,0)}}
        .pl-live{animation:plLive 2.2s ease-out infinite}
        details.pl-fold>summary{list-style:none;cursor:pointer}
        details.pl-fold>summary::-webkit-details-marker{display:none}
        details.pl-fold[open] .pl-chev{transform:rotate(180deg)}
        @media (prefers-reduced-motion:reduce){.pl-rise,.pl-live{animation:none!important}}
    </style>
</head>
<body class="min-h-full bg-ice-100 antialiased">
<div class="min-h-screen relative">
    <div class="pl-dots pointer-events-none fixed inset-0 opacity-40"></div>

    @if(session('pl_success') || session('pl_error'))
        @php $ok = (bool) session('pl_success'); @endphp
        <div id="pl-toast" class="fixed top-3 left-1/2 -translate-x-1/2 z-[80] w-[calc(100%-1.5rem)] max-w-sm transition-all duration-300">
            <div class="rounded-2xl px-4 py-3 shadow-2xl flex items-start gap-2.5 {{ $ok ? 'forest-hero' : 'bg-red-600' }}">
                <span class="shrink-0 text-white">{!! $lic($ok?'check':'alert','w-5 h-5') !!}</span>
                <p class="flex-1 text-[13px] font-bold text-white leading-snug">{{ session('pl_success') ?? session('pl_error') }}</p>
                <button type="button" onclick="document.getElementById('pl-toast').remove()" class="shrink-0 text-white/70 hover:text-white">{!! $lic('x') !!}</button>
            </div>
        </div>
    @endif

    <div class="relative mx-auto w-full max-w-lg px-3 py-5 space-y-3">

        {{-- HEADER + NOMINAL --}}
        <div class="forest-hero relative overflow-hidden rounded-2xl text-white shadow-lg shadow-brand-950/25 pl-rise">
            <div class="forest-dots absolute inset-0 opacity-40"></div>
            <div class="pl-grid absolute inset-0 opacity-40"></div>
            <div class="relative px-5 py-4">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="shrink-0 w-8 h-8 rounded-lg bg-signal-400 text-brand-950 flex items-center justify-center">{!! $lic('store') !!}</span>
                        <p class="text-sm font-extrabold truncate">{{ $shop['shop_name'] }}</p>
                    </div>
                    @if($cancelled)
                        <span class="shrink-0 px-2 py-0.5 rounded-full bg-white/15 text-[10px] font-extrabold uppercase">Batal</span>
                    @elseif($settled)
                        <span class="shrink-0 px-2 py-0.5 rounded-full bg-signal-400 text-brand-950 text-[10px] font-extrabold uppercase">Lunas</span>
                    @elseif($anyPending)
                        <span class="shrink-0 px-2 py-0.5 rounded-full bg-amber-400 text-brand-950 text-[10px] font-extrabold uppercase">Diverifikasi</span>
                    @elseif($overdue > 0)
                        <span class="shrink-0 px-2 py-0.5 rounded-full bg-red-600 text-white text-[10px] font-extrabold uppercase">Lewat {{ $overdue }}h</span>
                    @else
                        <span class="shrink-0 px-2 py-0.5 rounded-full bg-amber-400 text-brand-950 text-[10px] font-extrabold uppercase">Belum Bayar</span>
                    @endif
                </div>

                <div class="mt-3 flex items-center gap-1.5">
                    <p class="text-[13px] font-extrabold font-mono text-signal-300">{{ $invoiceNumber }}</p>
                    <button type="button" onclick="plCopy('{{ $invoiceNumber }}',this)" class="w-6 h-6 flex items-center justify-center rounded text-emerald-100/70 hover:text-signal-300">{!! $lic('copy','w-3.5 h-3.5') !!}</button>
                </div>
                <p class="text-[11px] text-emerald-100/65 truncate">{{ $customer }} · {{ $channel['label'] }}</p>

                @if(!$cancelled)
                    <div class="mt-3 pt-3 border-t border-white/10">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-100/55">
                            {{ $settled ? 'Total Lunas' : ($isCash ? 'Bayar di Toko' : 'Transfer Sekarang' . ($stage['installment'] ? ' · '.$stage['short'] : '')) }}
                        </p>
                        <div class="mt-1 flex items-center justify-between gap-3">
                            <p class="text-2xl font-extrabold tabular-nums text-signal-300 leading-none">
                                Rp {{ number_format($settled ? $numbers['total'] : ($isCash ? $numbers['outstanding'] : $stage['due']), 0, ',', '.') }}
                            </p>
                            @if(!$settled && !$isCash)
                                <button type="button" onclick="plCopy('{{ (int) $stage['due'] }}',this)" class="shrink-0 inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-signal-400 text-brand-950 text-[11px] font-extrabold hover:bg-signal-500 transition-colors">{!! $lic('copy','w-3.5 h-3.5') !!} Salin</button>
                            @endif
                        </div>
                        @if($stage['installment'] && !$settled)
                            <p class="mt-1.5 text-[11px] text-emerald-100/70">
                                Tahap {{ $stage['index'] }}/2 · sisa invoice Rp {{ number_format($numbers['outstanding'],0,',','.') }}
                            </p>
                        @endif
                        @if($due && !$settled)
                            <p class="mt-1 text-[11px] {{ $overdue > 0 ? 'text-red-300 font-bold' : 'text-emerald-100/60' }}">
                                Tempo {{ $due }}{{ $overdue > 0 ? ' · lewat '.$overdue.' hari' : ($leftDays !== null ? ' · '.($leftDays === 0 ? 'hari ini' : $leftDays.' hari lagi') : '') }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- STATUS UTAMA --}}
        @if($cancelled)
            <div class="rounded-2xl bg-white border border-ice-300 p-5 text-center pl-rise">
                <p class="text-base font-extrabold text-brand-950">Pesanan Dibatalkan</p>
                <p class="mt-1 text-[12px] text-gray-500">Bila sudah transfer, hubungi admin untuk pengembalian dana.</p>
            </div>
        @elseif($settled)
            <div class="rounded-2xl bg-white border-2 border-signal-400 p-5 text-center pl-rise">
                <span class="inline-flex w-12 h-12 rounded-full bg-signal-400 text-brand-950 items-center justify-center mx-auto pl-live">{!! $lic('check','w-6 h-6') !!}</span>
                <p class="mt-2 text-base font-extrabold text-brand-950">Tagihan Lunas</p>
                <p class="mt-1 text-[12px] text-gray-600">Rp {{ number_format($numbers['paid'],0,',','.') }} terverifikasi. Pesanan diproses sesuai jadwal.</p>
            </div>
        @elseif($isCash)
            <div class="rounded-2xl bg-white border border-brand-300 p-4 pl-rise">
                <div class="flex items-start gap-3">
                    <span class="shrink-0 w-9 h-9 rounded-xl bg-brand-600 text-signal-300 flex items-center justify-center">{!! $lic('cash','w-5 h-5') !!}</span>
                    <div class="min-w-0">
                        <p class="text-sm font-extrabold text-brand-950">Bayar Tunai di Toko</p>
                        <p class="text-[12px] text-gray-600 mt-0.5">Tidak perlu unggah bukti. Datang ke kasir sesuai jam operasional.</p>
                        @if($shop['shop_hours'] !== '')<p class="text-[11px] text-gray-400 mt-1">{{ $shop['shop_hours'] }}</p>@endif
                    </div>
                </div>
            </div>
        @elseif($anyPending)
            <div class="rounded-2xl bg-amber-50 border-2 border-amber-300 p-4 pl-rise">
                <div class="flex items-start gap-3">
                    <span class="shrink-0 w-9 h-9 rounded-xl bg-amber-500 text-white flex items-center justify-center pl-live">{!! $lic('clock','w-5 h-5') !!}</span>
                    <div class="min-w-0">
                        <p class="text-sm font-extrabold text-amber-900">Bukti Diterima — Menunggu Verifikasi</p>
                        <p class="text-[12px] text-amber-800 mt-0.5">
                            Rp {{ number_format($numbers['pending'],0,',','.') }} sedang diperiksa admin (maks. 1×24 jam kerja).
                            <b>Formulir ditutup</b> — Anda tidak perlu mengirim ulang.
                        </p>
                        @if($lastPending)
                            <p class="text-[11px] text-amber-700 mt-1 tabular-nums">Dikirim {{ $lastPending['created'] }}</p>
                        @endif
                        <p class="text-[11px] text-amber-700 mt-1.5">Setelah diverifikasi, tagihan tahap berikutnya muncul otomatis di halaman ini.</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- TUJUAN BAYAR (ringkas) --}}
        @if($formReady)
            <div class="rounded-2xl bg-white border border-ice-200 shadow-sm p-4 pl-rise">
                <div class="flex items-center justify-between gap-2 mb-3">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Tujuan Pembayaran</p>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-brand-50 text-brand-700 text-[10px] font-extrabold">{!! $lic($chIcon,'w-3 h-3') !!} {{ $channel['label'] }}</span>
                </div>

                @if($channel['method'] === 'qris' && $channel['qris_url'])
                    <div class="flex items-center gap-4">
                        <img src="{{ $channel['qris_url'] }}" alt="QRIS" class="shrink-0 w-28 h-28 rounded-xl border border-ice-200 object-contain bg-white p-1">
                        <div class="min-w-0">
                            <p class="text-sm font-extrabold text-brand-950">{{ $shop['qris_merchant'] ?: $shop['shop_name'] }}</p>
                            <p class="text-[11px] text-gray-500 mt-1">Scan, ketik nominal, screenshot bukti.</p>
                            <a href="{{ $channel['qris_url'] }}" download class="mt-2 inline-flex items-center gap-1 text-[11px] font-bold text-brand-700 hover:text-brand-600">{!! $lic('file','w-3 h-3') !!} Simpan QR</a>
                        </div>
                    </div>
                @elseif($channel['method'] === 'ewallet')
                    <p class="text-[11px] font-bold text-gray-400">{{ $shop['ewallet_provider'] ?: 'E-Wallet' }}</p>
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-xl font-extrabold tabular-nums text-brand-950 break-all">{{ $shop['ewallet_number'] }}</p>
                        <button type="button" onclick="plCopy('{{ $shop['ewallet_number'] }}',this)" class="shrink-0 w-9 h-9 flex items-center justify-center rounded-lg bg-brand-600 text-white hover:bg-brand-700">{!! $lic('copy') !!}</button>
                    </div>
                    @if($shop['ewallet_holder'] !== '')<p class="text-[12px] text-gray-500 mt-1">a.n. {{ $shop['ewallet_holder'] }}</p>@endif
                @else
                    <p class="text-[11px] font-bold text-gray-400">{{ $shop['bank_name'] ?: 'Bank' }}</p>
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-xl font-extrabold tabular-nums text-brand-950 break-all">{{ $shop['bank_account'] }}</p>
                        <button type="button" onclick="plCopy('{{ $shop['bank_account'] }}',this)" class="shrink-0 w-9 h-9 flex items-center justify-center rounded-lg bg-brand-600 text-white hover:bg-brand-700">{!! $lic('copy') !!}</button>
                    </div>
                    @if($shop['bank_holder'] !== '')<p class="text-[12px] text-gray-500 mt-1">a.n. {{ $shop['bank_holder'] }}</p>@endif
                @endif

                <p class="mt-3 text-[11px] text-gray-500 leading-snug">{{ $channel['hint'] }}</p>
                @if($channel['fallback'])
                    <p class="mt-1.5 text-[11px] font-bold text-amber-600">Kanal pilihan admin belum siap — gunakan transfer bank ini.</p>
                @endif
            </div>

            {{-- FORM UNGGAH --}}
            <form method="POST" action="{{ route('payment.upload', $token) }}" enctype="multipart/form-data" id="pl-form"
                  class="rounded-2xl bg-white border-2 border-signal-400 shadow-sm p-4 pl-rise">
                @csrf
                <p class="text-sm font-extrabold text-brand-950">Unggah Bukti · {{ $stage['short'] }}</p>
                <p class="text-[11px] text-gray-500 mt-0.5">Nominal Rp {{ number_format($stage['due'],0,',','.') }} sudah tercatat otomatis.</p>

                <label for="pl-proof" id="pl-drop" class="mt-3 block rounded-xl border-2 border-dashed border-ice-300 bg-ice-50 p-4 text-center cursor-pointer hover:border-brand-500 transition-colors">
                    <input id="pl-proof" name="proof" type="file" class="sr-only" accept=".jpg,.jpeg,.png,.pdf">
                    <img id="pl-thumb" src="" alt="Bukti" class="hidden mx-auto max-h-36 rounded-lg object-contain shadow">
                    <span id="pl-icon" class="inline-flex w-11 h-11 rounded-xl bg-white border border-ice-300 text-brand-500 items-center justify-center mx-auto">{!! $lic('upload','w-6 h-6') !!}</span>
                    <p id="pl-fname" class="mt-2 text-[13px] text-gray-600"><b class="text-brand-700">Pilih file bukti</b></p>
                    <p id="pl-hint" class="text-[11px] text-gray-400">JPG, PNG, PDF · maks 5MB</p>
                </label>
                @error('proof') <p class="mt-1.5 text-[12px] font-semibold text-red-600">{{ $message }}</p> @enderror

                <input name="note" type="text" maxlength="255" value="{{ old('note') }}" placeholder="Catatan opsional (mis. nama pengirim)"
                       class="mt-3 w-full px-3 py-2.5 text-[13px] border border-ice-300 rounded-xl bg-ice-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25">

                <button type="submit" id="pl-submit" class="mt-3 w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-xl bg-brand-600 text-signal-300 text-[15px] font-extrabold hover:bg-brand-700 shadow-md shadow-brand-900/25 transition-all disabled:opacity-60">
                    {!! $lic('send','w-5 h-5') !!} <span id="pl-submit-label">Kirim Bukti</span>
                </button>
            </form>
        @elseif(!$cancelled && !$settled && !$isCash && !$anyPending && !$channel['ready'])
            <div class="rounded-2xl bg-amber-50 border border-amber-300 p-4 pl-rise">
                <p class="text-sm font-extrabold text-amber-900">Kanal pembayaran belum disiapkan</p>
                <p class="text-[12px] text-amber-800 mt-0.5">Hubungi admin untuk instruksi pembayaran yang sah.</p>
            </div>
        @endif

        {{-- RINCIAN (dilipat) --}}
        <details class="pl-fold rounded-2xl bg-white border border-ice-200 shadow-sm overflow-hidden">
            <summary class="px-4 py-3 flex items-center justify-between gap-3 hover:bg-ice-50 transition-colors">
                <span class="text-[13px] font-extrabold text-brand-950">Rincian Invoice</span>
                <span class="pl-chev text-brand-400 transition-transform">{!! $lic('chev') !!}</span>
            </summary>
            <div class="px-4 pb-4 pt-1 space-y-2 text-[13px]">
                <div class="flex justify-between gap-3 py-1.5 border-b border-ice-100"><span class="text-gray-600">Subtotal Item</span><span class="font-bold tabular-nums">Rp {{ number_format($numbers['subtotal'],0,',','.') }}</span></div>
                <div class="flex justify-between gap-3 py-1.5 border-b border-ice-100"><span class="text-gray-600">Ongkos Kirim</span><span class="font-bold tabular-nums">Rp {{ number_format($numbers['shipping'],0,',','.') }}</span></div>
                @if($numbers['discount'] > 0)
                    <div class="flex justify-between gap-3 py-1.5 border-b border-ice-100"><span class="text-gray-600">Diskon</span><span class="font-bold text-red-600 tabular-nums">− Rp {{ number_format($numbers['discount'],0,',','.') }}</span></div>
                @endif
                <div class="flex justify-between gap-3 py-1.5 border-b border-ice-100"><span class="font-extrabold text-brand-950">Total Invoice</span><span class="font-extrabold tabular-nums">Rp {{ number_format($numbers['total'],0,',','.') }}</span></div>
                @if($numbers['paid'] > 0)
                    <div class="flex justify-between gap-3 py-1.5 border-b border-ice-100"><span class="text-gray-600">Sudah Dibayar</span><span class="font-bold text-signal-600 tabular-nums">− Rp {{ number_format($numbers['paid'],0,',','.') }}</span></div>
                @endif
                @if($stage['installment'])
                    <div class="flex justify-between gap-3 py-1.5 border-b border-ice-100"><span class="text-gray-600">DP {{ rtrim(rtrim(number_format($stage['dp_percent'],1,'.',''),'0'),'.') }}%</span><span class="font-bold tabular-nums">Rp {{ number_format($stage['dp_amount'],0,',','.') }} {{ $stage['dp_done'] ? '(lunas)' : '' }}</span></div>
                    <div class="flex justify-between gap-3 py-1.5 border-b border-ice-100"><span class="text-gray-600">Pelunasan</span><span class="font-bold tabular-nums">Rp {{ number_format($stage['rest_amount'],0,',','.') }}</span></div>
                @endif
                <div class="flex justify-between gap-3 pt-2"><span class="font-extrabold text-brand-950">Sisa</span><span class="text-lg font-extrabold tabular-nums {{ $numbers['outstanding'] > 0 ? 'text-red-600' : 'text-signal-600' }}">Rp {{ number_format($numbers['outstanding'],0,',','.') }}</span></div>
                <p class="text-[11px] text-gray-400 pt-1">Harga sudah termasuk PPN.</p>
            </div>
        </details>

        {{-- RIWAYAT (dilipat) --}}
        @if(count($payments))
            <details class="pl-fold rounded-2xl bg-white border border-ice-200 shadow-sm overflow-hidden">
                <summary class="px-4 py-3 flex items-center justify-between gap-3 hover:bg-ice-50 transition-colors">
                    <span class="text-[13px] font-extrabold text-brand-950">Bukti Terkirim ({{ count($payments) }})</span>
                    <span class="pl-chev text-brand-400 transition-transform">{!! $lic('chev') !!}</span>
                </summary>
                <div class="divide-y divide-ice-100">
                    @foreach($payments as $p)
                        @php $ps = $PST[$p['status']] ?? ['Diproses','bg-ice-200 text-brand-800']; @endphp
                        <div class="px-4 py-2.5 flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <span class="text-[13px] font-extrabold tabular-nums">Rp {{ number_format($p['amount'],0,',','.') }}</span>
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold uppercase {{ $ps[1] }}">{{ $ps[0] }}</span>
                                </div>
                                <p class="text-[11px] text-gray-400 tabular-nums">{{ $p['created'] }}</p>
                            </div>
                            @if($p['proof'])
                                <a href="{{ $p['proof'] }}" target="_blank" rel="noopener" class="shrink-0 w-8 h-8 flex items-center justify-center rounded-lg bg-ice-100 text-brand-700 hover:bg-ice-200">{!! $lic('eye') !!}</a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </details>
        @endif

        {{-- BANTUAN --}}
        <div class="flex items-center justify-between gap-3 px-1">
            <p class="text-[11px] text-gray-500 leading-snug">Nominal atau kanal tidak sesuai? Hubungi admin.</p>
            @if($wa)
                <a href="https://wa.me/{{ $wa }}?text={{ urlencode('Halo, konfirmasi pembayaran '.$invoiceNumber) }}" target="_blank" rel="noopener"
                   class="shrink-0 inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-signal-400 text-brand-950 text-[11px] font-extrabold hover:bg-signal-500 transition-colors">{!! $lic('chat','w-3.5 h-3.5') !!} Admin</a>
            @endif
        </div>
        <p class="text-center text-[11px] text-gray-400 pb-3">{{ $shop['invoice_footer'] }}</p>
    </div>
</div>

<script>
    function plCopy(t, el) {
        const done = () => { if(!el) return; const o=el.innerHTML;
            el.innerHTML='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>';
            setTimeout(()=>{el.innerHTML=o;},1300); };
        if (navigator.clipboard && window.isSecureContext) { navigator.clipboard.writeText(String(t)).then(done).catch(()=>{}); }
        else { const a=document.createElement('textarea'); a.value=String(t); a.style.position='fixed'; a.style.opacity='0';
            document.body.appendChild(a); a.select(); try{document.execCommand('copy');done();}catch(e){} document.body.removeChild(a); }
    }
    (function(){
        const f=document.getElementById('pl-proof'), th=document.getElementById('pl-thumb'),
              ic=document.getElementById('pl-icon'), fn=document.getElementById('pl-fname'),
              hi=document.getElementById('pl-hint'), dr=document.getElementById('pl-drop');
        if(f){ f.addEventListener('change',function(){
            const x=this.files&&this.files[0]; if(!x) return;
            fn.innerHTML='<b class="text-brand-950"></b>'; fn.querySelector('b').textContent=x.name;
            hi.textContent=(x.size/1048576).toFixed(2)+' MB · klik untuk ganti';
            dr.classList.remove('border-ice-300','bg-ice-50'); dr.classList.add('border-signal-400','bg-signal-100/40');
            if(x.type.startsWith('image/')){ th.src=URL.createObjectURL(x); th.classList.remove('hidden'); ic.classList.add('hidden'); }
            else { th.classList.add('hidden'); ic.classList.remove('hidden'); }
        }); }
        const fm=document.getElementById('pl-form'), bt=document.getElementById('pl-submit'), lb=document.getElementById('pl-submit-label');
        if(fm&&bt&&lb){ fm.addEventListener('submit',function(){ bt.disabled=true; lb.textContent='Mengirim...'; }); }
        const ts=document.getElementById('pl-toast');
        if(ts){ setTimeout(()=>{ ts.style.opacity='0'; ts.style.transform='translate(-50%,-10px)'; setTimeout(()=>ts.remove(),320); },6000); }
    })();
</script>
</body>
</html>