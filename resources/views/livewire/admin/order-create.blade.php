@php
    $O = [
        'back'  => 'M10 19l-7-7m0 0l7-7m-7 7h18',
        'user'  => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
        'guest' => 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z',
        'phone' => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z',
        'mail'  => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
        'search'=> 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z',
        'store' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z',
        'truck' => 'M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0 2 2 0 00-4 0zm10 0a2 2 0 104 0 2 2 0 00-4 0z',
        'bolt'  => 'M13 10V3L4 14h7v7l9-11h-7z',
        'box'   => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
        'clock' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        'plus'  => 'M12 4v16m8-8H4',
        'x'     => 'M6 18L18 6M6 6l12 12',
        'check' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        'pin'   => 'M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z',
        'save'  => 'M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4',
        'clip'  => 'M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13',
        'note'  => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'alert' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
        'tag'   => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z',
    ];
    $oic = fn($k, $c = 'w-5 h-5') => '<svg class="' . $c . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="' . ($O[$k] ?? $O['note']) . '"/></svg>';
    $tierBadge = ['regular' => 'bg-ice-200 text-brand-800', 'silver' => 'bg-gray-200 text-gray-700', 'gold' => 'bg-amber-200 text-amber-800', 'vip' => 'bg-signal-400 text-brand-950', 'inactive' => 'bg-red-100 text-red-700'];
    $customerReady = ($customer_mode === 'member' && !empty($user_id)) || ($customer_mode === 'guest' && trim($guest_name) !== '' && trim($guest_phone) !== '');
    $shipReady = $shipping_method === 'pickup' || trim($shipping_address) !== '';
@endphp

<div class="w-full space-y-6">
    <style>
        @keyframes ocRise { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
        .oc-rise { animation: ocRise .5s cubic-bezier(.2,.7,.2,1) both; }
        @keyframes ocLive { 0%,100% { box-shadow: 0 0 0 0 rgba(200,236,63,.5); } 50% { box-shadow: 0 0 0 10px rgba(200,236,63,0); } }
        .oc-live { animation: ocLive 2.4s ease-out infinite; }
        .oc-scroll::-webkit-scrollbar { width: 8px; }
        .oc-scroll::-webkit-scrollbar-track { background: #e2edf0; border-radius: 8px; }
        .oc-scroll::-webkit-scrollbar-thumb { background: #1d6247; border-radius: 8px; }
        .oc-field { transition: box-shadow .2s ease, border-color .2s ease, background-color .2s ease; }
        @media (prefers-reduced-motion: reduce) { .oc-rise, .oc-live { animation: none !important; } }
    </style>

    {{-- HEADER --}}
    <div class="forest-hero relative overflow-hidden rounded-3xl text-white px-6 py-6 sm:px-8 sm:py-7 shadow-lg shadow-brand-950/25">
        <div class="forest-dots absolute inset-0 opacity-45"></div>
        <div class="absolute -top-20 -right-12 w-56 h-56 rounded-full bg-signal-400/10 blur-3xl"></div>
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-5">
            <div class="min-w-0">
                <a href="{{ route('admin.orders') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/5 border border-white/10 text-sm font-bold text-emerald-100/80 hover:bg-white/10 hover:text-signal-300 transition-colors">
                    {!! $oic('back', 'w-4 h-4') !!} Daftar Pesanan
                </a>
                <h1 class="mt-4 text-3xl font-extrabold tracking-tight">Buat Pesanan Baru</h1>
                <p class="mt-2 text-base text-emerald-100/75 max-w-2xl">Cari pelanggan lewat nomor telepon, atau layani guest tanpa registrasi. Alamat cukup diisi di bagian pengiriman.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <span class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-sm font-bold {{ $customerReady ? 'bg-signal-400 text-brand-950' : 'bg-white/10 border border-white/15 text-emerald-100/80' }}">
                    {!! $oic($customerReady ? 'check' : 'user', 'w-4 h-4') !!} Pelanggan
                </span>
                <span class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-sm font-bold {{ $estimated_price > 0 ? 'bg-signal-400 text-brand-950' : 'bg-white/10 border border-white/15 text-emerald-100/80' }}">
                    {!! $oic($estimated_price > 0 ? 'check' : 'box', 'w-4 h-4') !!} Item
                </span>
                <span class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-sm font-bold {{ $shipReady ? 'bg-signal-400 text-brand-950' : 'bg-white/10 border border-white/15 text-emerald-100/80' }}">
                    {!! $oic($shipReady ? 'check' : 'truck', 'w-4 h-4') !!} Kirim
                </span>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-2xl border border-signal-300 bg-signal-100 px-5 py-4 text-base font-bold text-signal-700">{{ session('success') }}</div>
    @endif
    @if(session('info'))
        <div class="rounded-2xl border border-ice-300 bg-white px-5 py-4 text-base font-semibold text-brand-800">{{ session('info') }}</div>
    @endif
    @error('submit')
        <div class="rounded-2xl border-2 border-red-300 bg-red-50 px-5 py-4 text-base font-bold text-red-800">{{ $message }}</div>
    @enderror
    @error('customer_mode')
        <div class="rounded-2xl border-2 border-amber-300 bg-amber-50 px-5 py-4 text-base font-bold text-amber-800">{{ $message }}</div>
    @enderror

    <form wire:submit="submitOrder" x-data="{ gname: @js($guest_name), gphone: @js($guest_phone) }"
          class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">

        {{-- ============ KIRI ============ --}}
        <div class="xl:col-span-8 space-y-6 min-w-0">

            {{-- 01 PELANGGAN --}}
            <section class="relative overflow-hidden rounded-[2rem] bg-white border border-ice-200 shadow-sm oc-rise">
                <div class="absolute left-0 top-8 bottom-8 w-1.5 rounded-r-full bg-gradient-to-b from-signal-400 to-brand-600"></div>
                <div class="p-6 sm:p-7 pl-7 sm:pl-8">
                    <div class="flex items-center gap-3">
                        <span class="w-10 h-10 rounded-full bg-brand-600 text-signal-300 flex items-center justify-center text-sm font-black shrink-0">01</span>
                        <div class="min-w-0">
                            <h2 class="text-xl font-extrabold text-brand-950 tracking-tight">Data Pelanggan</h2>
                            <p class="text-sm text-gray-500">Member dicari dari nomor terdaftar. Guest cukup nama &amp; nomor.</p>
                        </div>
                        @if($customer_mode !== '')
                            <button type="button" wire:click="$set('customer_mode','')" class="ml-auto shrink-0 text-sm font-bold text-brand-700 hover:text-brand-600 transition-colors">Ubah tipe</button>
                        @endif
                    </div>

                    {{-- Pilihan tipe --}}
                    @if($customer_mode === '')
                        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <button type="button" wire:click="setMode('member')"
                                    class="group text-left rounded-2xl border-2 border-ice-200 hover:border-brand-500 hover:bg-ice-50 p-5 transition-all hover:-translate-y-0.5">
                                <span class="inline-flex w-12 h-12 rounded-2xl bg-brand-600 text-signal-300 items-center justify-center">{!! $oic('user', 'w-6 h-6') !!}</span>
                                <p class="mt-4 text-lg font-extrabold text-brand-950">Member Terdaftar</p>
                                <p class="mt-1 text-sm text-gray-500">Cari pakai nomor telepon / WhatsApp yang sudah tersimpan.</p>
                            </button>
                            <button type="button" wire:click="setMode('guest')"
                                    class="group text-left rounded-2xl border-2 border-ice-200 hover:border-signal-500 hover:bg-signal-100/40 p-5 transition-all hover:-translate-y-0.5">
                                <span class="inline-flex w-12 h-12 rounded-2xl bg-signal-400 text-brand-950 items-center justify-center">{!! $oic('guest', 'w-6 h-6') !!}</span>
                                <p class="mt-4 text-lg font-extrabold text-brand-950">Guest Baru</p>
                                <p class="mt-1 text-sm text-gray-500">Belum terdaftar? Tetap bisa order tanpa bikin akun manual.</p>
                            </button>
                        </div>

                    {{-- MEMBER --}}
                    @elseif($customer_mode === 'member')
                        @if(!empty($user_id) && !empty($member))
                            <div class="mt-6 forest-hero relative overflow-hidden rounded-2xl p-5 text-white">
                                <div class="forest-dots absolute inset-0 opacity-40"></div>
                                <div class="relative flex flex-col sm:flex-row sm:items-center gap-4">
                                    <div class="flex items-center gap-4 min-w-0 flex-1">
                                        <span class="shrink-0 w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-400 to-signal-400 text-brand-950 flex items-center justify-center text-xl font-extrabold">{{ strtoupper(substr($member['name'], 0, 1)) }}</span>
                                        <div class="min-w-0">
                                            <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-signal-300">Member terpilih</p>
                                            <p class="text-lg font-extrabold truncate">{{ $member['name'] }}</p>
                                            <p class="text-sm text-emerald-100/75 tabular-nums">{{ $member['phone'] ?: 'Nomor belum diisi' }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0">
                                        <span class="px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider {{ $tierBadge[$member['tier']] ?? $tierBadge['regular'] }}">{{ $member['tier'] }}</span>
                                        <button type="button" wire:click="clearMember" class="px-4 py-2 rounded-xl bg-white/10 border border-white/15 text-sm font-bold hover:bg-white/20 transition-colors">Ganti</button>
                                    </div>
                                </div>
                                @if(!empty($member['address']))
                                    <p class="relative mt-4 pt-3 border-t border-white/10 text-sm text-emerald-100/70 flex items-start gap-2">
                                        <span class="shrink-0 mt-0.5 text-signal-300">{!! $oic('pin', 'w-4 h-4') !!}</span>
                                        <span>{{ $member['address'] }}{{ !empty($member['detail']) ? ' • Patokan: ' . $member['detail'] : '' }}</span>
                                    </p>
                                @endif
                            </div>
                            @error('user_id') <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
                        @else
                            <div class="mt-6">
                                <label for="oc-phone" class="block text-base font-extrabold text-brand-950 mb-2">Cari Nomor Telepon / WhatsApp</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-brand-400">{!! $oic('search', 'w-5 h-5') !!}</span>
                                    <input id="oc-phone" wire:model.live.debounce.300ms="phone_query" type="text" inputmode="tel" autocomplete="off" autofocus
                                           placeholder="08xxxxxxxxxx / 628xxx / 4 digit terakhir"
                                           class="oc-field w-full pl-12 pr-11 py-3.5 text-base tabular-nums border-2 border-ice-300 rounded-2xl bg-ice-50 focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-500/20 focus:border-brand-500">
                                    <span wire:loading wire:target="phone_query" class="absolute inset-y-0 right-0 pr-4 flex items-center">
                                        <svg class="w-4 h-4 animate-spin text-brand-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.4 0 0 5.4 0 12h4z"/></svg>
                                    </span>
                                </div>
                                <p class="mt-2 text-sm text-gray-400">Format bebas: 08…, 628…, +62 812-3456-7890. Bisa juga ketik nama pelanggan.</p>
                                @error('user_id') <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror

                                @if(count($matches) > 0)
                                    <div class="mt-4 oc-scroll max-h-72 overflow-y-auto rounded-2xl border border-ice-200 divide-y divide-ice-100">
                                        @foreach($matches as $m)
                                            <button type="button" wire:click="selectMember({{ $m['id'] }})"
                                                    class="w-full text-left flex items-center gap-3 px-4 py-3 hover:bg-ice-50 transition-colors">
                                                <span class="shrink-0 w-10 h-10 rounded-xl bg-brand-100 text-brand-700 flex items-center justify-center font-extrabold">{{ strtoupper(substr($m['name'], 0, 1)) }}</span>
                                                <span class="min-w-0 flex-1">
                                                    <span class="block text-base font-bold text-brand-950 truncate">{{ $m['name'] }}</span>
                                                    <span class="block text-sm text-gray-500 tabular-nums truncate">{{ $m['phone'] ?: $m['email'] }}</span>
                                                </span>
                                                <span class="shrink-0 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase {{ $tierBadge[$m['tier']] ?? $tierBadge['regular'] }}">{{ $m['tier'] }}</span>
                                            </button>
                                        @endforeach
                                    </div>
                                @elseif($searched)
                                    <div class="mt-4 rounded-2xl border-2 border-amber-300 bg-amber-50 p-5">
                                        <p class="flex items-start gap-2.5 text-base font-extrabold text-amber-800">
                                            <span class="shrink-0 mt-0.5">{!! $oic('alert', 'w-5 h-5') !!}</span>
                                            Nomor "{{ $phone_query }}" belum terdaftar.
                                        </p>
                                        <p class="mt-1.5 text-sm text-amber-700">Lanjutkan sebagai guest — data akan otomatis tersimpan sebagai pelanggan baru.</p>
                                        <button type="button" wire:click="switchToGuest"
                                                class="mt-4 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-signal-400 text-brand-950 text-base font-extrabold hover:bg-signal-500 transition-colors">
                                            {!! $oic('guest', 'w-4 h-4') !!} Lanjut sebagai Guest Baru
                                        </button>
                                    </div>
                                @endif
                            </div>
                        @endif

                    {{-- GUEST --}}
                    @else
                        <div class="mt-6 rounded-2xl border border-signal-300 bg-signal-100/40 p-4 flex items-start gap-3">
                            <span class="shrink-0 w-9 h-9 rounded-xl bg-signal-400 text-brand-950 flex items-center justify-center">{!! $oic('guest', 'w-4 h-4') !!}</span>
                            <p class="text-sm text-brand-900"><b>Mode Guest.</b> Cukup nama &amp; nomor. Email opsional — bila kosong, sistem membuat email internal agar pesanan tetap tercatat rapi.</p>
                        </div>

                        <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="sm:col-span-2">
                                <label for="oc-gname" class="block text-base font-extrabold text-brand-950 mb-2">Nama Pelanggan <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-brand-400">{!! $oic('user', 'w-5 h-5') !!}</span>
                                    <input id="oc-gname" wire:model.live.debounce.250ms="guest_name" @input="gname = $event.target.value" type="text" autocomplete="off"
                                           class="oc-field w-full pl-12 pr-4 py-3.5 text-base border border-ice-300 rounded-2xl bg-ice-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25 focus:border-brand-500 @error('guest_name') border-red-400 bg-red-50 @enderror">
                                </div>
                                @error('guest_name') <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="oc-gphone" class="block text-base font-extrabold text-brand-950 mb-2">No. Telepon / WhatsApp <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-brand-400">{!! $oic('phone', 'w-5 h-5') !!}</span>
                                    <input id="oc-gphone" wire:model.live.debounce.250ms="guest_phone" @input="gphone = $event.target.value" type="tel" inputmode="tel" autocomplete="off" placeholder="08xxxxxxxxxx"
                                           class="oc-field w-full pl-12 pr-4 py-3.5 text-base tabular-nums border border-ice-300 rounded-2xl bg-ice-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25 focus:border-brand-500 @error('guest_phone') border-red-400 bg-red-50 @enderror">
                                </div>
                                @error('guest_phone') <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="oc-gmail" class="block text-base font-extrabold text-brand-950 mb-2">Email <span class="text-gray-400 font-semibold">(opsional)</span></label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-brand-400">{!! $oic('mail', 'w-5 h-5') !!}</span>
                                    <input id="oc-gmail" wire:model.live.debounce.250ms="guest_email" type="email" autocomplete="off" placeholder="nama@contoh.com"
                                           class="oc-field w-full pl-12 pr-4 py-3.5 text-base border border-ice-300 rounded-2xl bg-ice-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25 focus:border-brand-500 @error('guest_email') border-red-400 bg-red-50 @enderror">
                                </div>
                                @error('guest_email') <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    @endif
                </div>
            </section>

            {{-- 02 ITEM --}}
            <section class="relative overflow-hidden rounded-[2rem] bg-white border border-ice-200 shadow-sm oc-rise">
                <div class="absolute left-0 top-8 bottom-8 w-1.5 rounded-r-full bg-gradient-to-b from-brand-400 to-brand-600"></div>
                <div class="p-6 sm:p-7 pl-7 sm:pl-8">
                    <div class="flex items-center gap-3">
                        <span class="w-10 h-10 rounded-full bg-brand-600 text-signal-300 flex items-center justify-center text-sm font-black shrink-0">02</span>
                        <div class="min-w-0">
                            <h2 class="text-xl font-extrabold text-brand-950 tracking-tight">Spesifikasi Cetakan</h2>
                            <p class="text-sm text-gray-500">Pilih divisi dulu, pilihan lain menyesuaikan otomatis.</p>
                        </div>
                        <span class="ml-auto shrink-0 px-3 py-1 rounded-full bg-ice-100 text-brand-700 text-sm font-extrabold tabular-nums">{{ count($items) }} item</span>
                    </div>

                    <div class="mt-6 space-y-5">
                        @foreach($items as $index => $item)
                            @php $catId = $item['category_id'] ?? ''; @endphp
                            <div class="rounded-2xl border border-ice-200 bg-ice-50/60 p-5">
                                <div class="flex items-center justify-between gap-3 mb-4">
                                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-600 text-signal-300 text-xs font-extrabold uppercase tracking-wider">Item {{ $index + 1 }}</span>
                                    @if(count($items) > 1)
                                        <button type="button" wire:click="removeItem({{ $index }})" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-red-50 text-red-600 text-sm font-bold hover:bg-red-100 transition-colors">
                                            {!! $oic('x', 'w-3.5 h-3.5') !!} Hapus
                                        </button>
                                    @endif
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-extrabold text-brand-950 mb-1.5">Divisi Produksi <span class="text-red-500">*</span></label>
                                        <select wire:model.live="items.{{ $index }}.category_id"
                                                class="oc-field w-full px-4 py-3 text-base font-bold border border-ice-300 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25 focus:border-brand-500">
                                            <option value="">— Pilih Divisi —</option>
                                            @foreach($categories as $c)
                                                <option value="{{ $c['id'] }}">{{ $c['name'] }}</option>
                                            @endforeach
                                        </select>
                                        @error('items.' . $index . '.category_id') <p class="mt-1.5 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
                                    </div>

                                    @if(!empty($catId))
                                        <div>
                                            <label class="block text-sm font-extrabold text-brand-950 mb-1.5">Layanan Cetak <span class="text-red-500">*</span></label>
                                            <select wire:model.live="items.{{ $index }}.service_id"
                                                    class="oc-field w-full px-4 py-3 text-base border border-ice-300 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25 focus:border-brand-500">
                                                <option value="">— Pilih Layanan —</option>
                                                @foreach(($servicesByCat[$catId] ?? []) as $s)
                                                    <option value="{{ $s['id'] }}">{{ $s['name'] }}</option>
                                                @endforeach
                                            </select>
                                            @error('items.' . $index . '.service_id') <p class="mt-1.5 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-extrabold text-brand-950 mb-1.5">Bahan Baku <span class="text-red-500">*</span></label>
                                            <select wire:model.live="items.{{ $index }}.material_id"
                                                    class="oc-field w-full px-4 py-3 text-base border border-ice-300 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25 focus:border-brand-500">
                                                <option value="">— Pilih Bahan —</option>
                                                @foreach(($materialsByCat[$catId] ?? []) as $m)
                                                    <option value="{{ $m['id'] }}">{{ $m['name'] }}</option>
                                                @endforeach
                                            </select>
                                            @error('items.' . $index . '.material_id') <p class="mt-1.5 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-extrabold text-brand-950 mb-1.5">Ukuran <span class="text-red-500">*</span></label>
                                            <select wire:model.live="items.{{ $index }}.size_id"
                                                    class="oc-field w-full px-4 py-3 text-base border border-ice-300 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25 focus:border-brand-500">
                                                <option value="">— Pilih Ukuran —</option>
                                                @foreach(($sizesByCat[$catId] ?? []) as $z)
                                                    <option value="{{ $z['id'] }}">{{ $z['name'] }}</option>
                                                @endforeach
                                            </select>
                                            @error('items.' . $index . '.size_id') <p class="mt-1.5 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
                                        </div>
                                    @else
                                        <div class="md:col-span-2 rounded-xl border-2 border-dashed border-ice-300 p-5 text-center text-sm text-gray-400">
                                            Pilih <b class="text-brand-600">Divisi Produksi</b> untuk membuka layanan, bahan, dan ukuran.
                                        </div>
                                    @endif

                                    <div>
                                        <label class="block text-sm font-extrabold text-brand-950 mb-1.5">Durasi (SLA) <span class="text-red-500">*</span></label>
                                        <select wire:model.live="items.{{ $index }}.duration_id"
                                                class="oc-field w-full px-4 py-3 text-base border border-ice-300 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25 focus:border-brand-500">
                                            <option value="">— Pilih Durasi —</option>
                                            @foreach($durations as $d)
                                                <option value="{{ $d['id'] }}">{{ $d['name'] }} (+{{ $d['days'] }} hari) · ×{{ rtrim(rtrim(number_format($d['multiplier'], 2, '.', ''), '0'), '.') }}</option>
                                            @endforeach
                                        </select>
                                        @error('items.' . $index . '.duration_id') <p class="mt-1.5 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-extrabold text-brand-950 mb-1.5">Jumlah (Qty) <span class="text-red-500">*</span></label>
                                        <input type="number" min="1" wire:model.live.debounce.300ms="items.{{ $index }}.quantity"
                                               class="oc-field w-full px-4 py-3 text-base tabular-nums border border-ice-300 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25 focus:border-brand-500">
                                        @error('items.' . $index . '.quantity') <p class="mt-1.5 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                @php $dur = $this->getDurationInfo($index); @endphp
                                @if($dur)
                                    <div class="mt-4 rounded-xl bg-brand-50 border border-brand-200 px-4 py-3 flex items-start gap-3">
                                        <span class="shrink-0 text-brand-600 mt-0.5">{!! $oic('clock', 'w-4 h-4') !!}</span>
                                        <div class="min-w-0">
                                            <p class="text-sm font-extrabold text-brand-800">{{ $dur['name'] }} · {{ $dur['days'] }} hari · ×{{ rtrim(rtrim(number_format($dur['multiplier'], 2, '.', ''), '0'), '.') }}</p>
                                            @if(!empty($dur['description'])) <p class="text-xs text-brand-600 mt-0.5">{{ $dur['description'] }}</p> @endif
                                        </div>
                                    </div>
                                @endif

                                <div class="mt-4 grid grid-cols-1 lg:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-extrabold text-brand-950 mb-1.5">File Desain</label>
                                        <div class="relative rounded-xl border-2 border-dashed border-ice-300 bg-white p-4 text-center hover:border-brand-500 transition-colors cursor-pointer">
                                            <input type="file" wire:model="items.{{ $index }}.design_files" multiple accept=".jpg,.jpeg,.png,.pdf,.zip"
                                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                            <span class="inline-flex text-brand-500">{!! $oic('clip', 'w-5 h-5') !!}</span>
                                            <p class="mt-1 text-sm text-gray-500"><b class="text-brand-700">Klik untuk upload</b> atau drag &amp; drop</p>
                                            <p class="text-xs text-gray-400 mt-0.5">JPG, PNG, PDF, ZIP • maks 20MB/file</p>
                                        </div>
                                        <div wire:loading wire:target="items.{{ $index }}.design_files" class="mt-2 text-sm font-bold text-brand-700">Mengunggah...</div>
                                        @error('items.' . $index . '.design_files.*') <p class="mt-1.5 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror

                                        @if(!empty($item['design_files']) && is_iterable($item['design_files']))
                                            <div class="mt-2 space-y-1.5">
                                                @foreach($item['design_files'] as $fi => $file)
                                                    <div class="flex items-center justify-between gap-2 rounded-xl bg-white border border-ice-200 px-3 py-2">
                                                        <span class="text-sm text-brand-900 truncate">{{ $this->cleanFileName($file) }}</span>
                                                        <button type="button" wire:click="removeFile({{ $index }}, {{ $fi }})" class="shrink-0 text-red-600 hover:text-red-700">{!! $oic('x', 'w-4 h-4') !!}</button>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <label class="block text-sm font-extrabold text-brand-950 mb-1.5">Catatan Produksi</label>
                                        <textarea wire:model="items.{{ $index }}.notes" rows="4" maxlength="300"
                                                  placeholder="Contoh: warna merah lebih cerah, finishing laminasi doff, grommet tiap 50 cm"
                                                  class="oc-field oc-scroll w-full px-4 py-3 text-base border border-ice-300 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25 focus:border-brand-500 resize-y"></textarea>
                                        @error('items.' . $index . '.notes') <p class="mt-1.5 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                <div class="mt-4 rounded-xl bg-white border border-ice-200 px-4 py-3 flex items-center justify-between">
                                    <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Subtotal item</span>
                                    <span class="text-lg font-extrabold text-brand-950 tabular-nums">Rp {{ number_format($this->getItemSubtotal($index), 0, ',', '.') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <button type="button" wire:click="addItem"
                            class="mt-5 w-full inline-flex items-center justify-center gap-2 py-3.5 rounded-2xl border-2 border-dashed border-brand-300 text-brand-700 text-base font-extrabold hover:bg-brand-50 hover:border-brand-500 transition-all">
                        {!! $oic('plus', 'w-5 h-5') !!} Tambah Item Lain
                    </button>
                </div>
            </section>

            {{-- 03 PENGIRIMAN --}}
            <section class="relative overflow-hidden rounded-[2rem] bg-white border border-ice-200 shadow-sm oc-rise">
                <div class="absolute left-0 top-8 bottom-8 w-1.5 rounded-r-full bg-gradient-to-b from-signal-400 to-signal-600"></div>
                <div class="p-6 sm:p-7 pl-7 sm:pl-8">
                    <div class="flex items-center gap-3">
                        <span class="w-10 h-10 rounded-full bg-brand-600 text-signal-300 flex items-center justify-center text-sm font-black shrink-0">03</span>
                        <div class="min-w-0">
                            <h2 class="text-xl font-extrabold text-brand-950 tracking-tight">Pengambilan / Pengiriman</h2>
                            <p class="text-sm text-gray-500">Alamat hanya diminta bila pesanan diantar.</p>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
                        @foreach([
                            ['pickup', 'store', 'Ambil di Toko', 'Tanpa ongkir'],
                            ['regular', 'truck', 'Kirim Reguler', 'JNE / J&T / ekspedisi'],
                            ['instant', 'bolt', 'Kirim Instant', 'GoSend / Grab'],
                        ] as $opt)
                            <button type="button" wire:click="$set('shipping_method','{{ $opt[0] }}')"
                                    class="text-left rounded-2xl border-2 p-4 transition-all hover:-translate-y-0.5 {{ $shipping_method === $opt[0] ? 'border-brand-500 bg-brand-50 shadow-sm' : 'border-ice-200 hover:border-brand-300' }}">
                                <span class="inline-flex w-11 h-11 rounded-xl items-center justify-center {{ $shipping_method === $opt[0] ? 'bg-brand-600 text-signal-300' : 'bg-ice-100 text-brand-600' }}">{!! $oic($opt[1], 'w-5 h-5') !!}</span>
                                <p class="mt-3 text-base font-extrabold text-brand-950">{{ $opt[2] }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $opt[3] }}</p>
                            </button>
                        @endforeach
                    </div>
                    @error('shipping_method') <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror

                    @if($shipping_method === 'pickup')
                        <div class="mt-5 forest-hero relative overflow-hidden rounded-2xl p-5 text-white">
                            <div class="forest-dots absolute inset-0 opacity-40"></div>
                            <div class="relative">
                                <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-signal-300">Titik Pengambilan</p>
                                <p class="mt-1.5 text-lg font-extrabold">{{ $shop_name }}</p>
                                <p class="mt-2 text-sm text-emerald-100/75 flex items-start gap-2">
                                    <span class="shrink-0 mt-0.5 text-signal-300">{!! $oic('pin', 'w-4 h-4') !!}</span>
                                    <span>{{ $shop_address !== '' ? $shop_address : 'Alamat toko belum diatur — lengkapi di Pengaturan Toko.' }}</span>
                                </p>
                                <p class="mt-1.5 text-sm text-emerald-100/75 flex items-start gap-2">
                                    <span class="shrink-0 mt-0.5 text-signal-300">{!! $oic('clock', 'w-4 h-4') !!}</span><span>{{ $shop_hours }}</span>
                                </p>
                            </div>
                        </div>
                    @else
                        <div class="mt-5 space-y-5">
                            @if($customer_mode === 'member' && !empty($member['address']))
                                <div class="rounded-xl bg-brand-50 border border-brand-200 px-4 py-3 text-sm text-brand-800 flex items-start gap-2.5">
                                    <span class="shrink-0 mt-0.5">{!! $oic('check', 'w-4 h-4') !!}</span>
                                    <span>Alamat tersimpan pelanggan sudah dipakai sebagai dasar. Ubah bila pengiriman kali ini berbeda.</span>
                                </div>
                            @endif

                            <div>
                                <label for="oc-addr" class="block text-base font-extrabold text-brand-950 mb-2">Alamat Pengiriman <span class="text-red-500">*</span></label>
                                <textarea id="oc-addr" wire:model="shipping_address" rows="4" maxlength="1000"
                                          placeholder="Jalan, nomor rumah, RT/RW, kelurahan, kecamatan, kota, kode pos"
                                          class="oc-field oc-scroll w-full px-4 py-3.5 text-base leading-relaxed border border-ice-300 rounded-2xl bg-ice-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25 focus:border-brand-500 resize-y @error('shipping_address') border-red-400 bg-red-50 @enderror"></textarea>
                                @error('shipping_address') <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="oc-detail" class="block text-base font-extrabold text-brand-950 mb-2">Patokan Kurir <span class="text-gray-400 font-semibold">(opsional)</span></label>
                                <input id="oc-detail" wire:model="shipping_detail" type="text" maxlength="500"
                                       placeholder="Pagar hitam, masuk gang samping warung, rumah ketiga di kiri"
                                       class="oc-field w-full px-4 py-3.5 text-base border border-signal-300 rounded-2xl bg-signal-100/40 focus:bg-white focus:outline-none focus:ring-2 focus:ring-signal-500/35 focus:border-signal-500">
                                @error('shipping_detail') <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
                            </div>

                            @if($customer_mode === 'member' && !empty($user_id))
                                <label class="flex items-start gap-3 rounded-xl bg-ice-50 border border-ice-200 px-4 py-3 cursor-pointer">
                                    <input type="checkbox" wire:model="save_address_to_member" class="mt-1 w-4 h-4 rounded border-ice-400 text-brand-600 focus:ring-brand-500">
                                    <span class="text-sm text-gray-600">Simpan alamat &amp; patokan ini ke data pelanggan agar pesanan berikutnya terisi otomatis.</span>
                                </label>
                            @endif
                        </div>
                    @endif
                </div>
            </section>

            {{-- 04 CATATAN --}}
            <section class="relative overflow-hidden rounded-[2rem] bg-white border border-ice-200 shadow-sm oc-rise">
                <div class="absolute left-0 top-8 bottom-8 w-1.5 rounded-r-full bg-gradient-to-b from-brand-500 to-brand-800"></div>
                <div class="p-6 sm:p-7 pl-7 sm:pl-8">
                    <div class="flex items-center gap-3">
                        <span class="w-10 h-10 rounded-full bg-brand-600 text-signal-300 flex items-center justify-center text-sm font-black shrink-0">04</span>
                        <div class="min-w-0">
                            <h2 class="text-xl font-extrabold text-brand-950 tracking-tight">Catatan Pesanan</h2>
                            <p class="text-sm text-gray-500">Berlaku untuk seluruh item, tercetak di SPK.</p>
                        </div>
                    </div>
                    <textarea wire:model="global_notes" rows="3" maxlength="500"
                              placeholder="Contoh: customer minta proof dulu sebelum cetak massal, hubungi via WhatsApp."
                              class="oc-field oc-scroll mt-5 w-full px-4 py-3.5 text-base border border-ice-300 rounded-2xl bg-ice-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25 focus:border-brand-500 resize-y"></textarea>
                    @error('global_notes') <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
                </div>
            </section>
        </div>

        {{-- ============ KANAN: RINGKASAN ============ --}}
        <div class="xl:col-span-4 xl:sticky xl:top-24 space-y-5 min-w-0">
            <div class="forest-hero relative overflow-hidden rounded-[2rem] p-6 text-white shadow-lg shadow-brand-950/20">
                <div class="forest-dots absolute inset-0 opacity-45"></div>
                <div class="absolute -bottom-16 -right-12 w-48 h-48 rounded-full bg-signal-400/10 blur-3xl"></div>
                <div class="relative">
                    <p class="text-xs font-bold uppercase tracking-[0.22em] text-signal-300">Ringkasan</p>

                    <div class="mt-4 space-y-2.5 text-sm">
                        <div class="flex items-start justify-between gap-3">
                            <span class="text-emerald-100/60">Pelanggan</span>
                            <span class="font-bold text-right min-w-0 truncate">
                                @if($customer_mode === 'member' && !empty($member))
                                    {{ $member['name'] }}
                                @elseif($customer_mode === 'guest')
                                    <span x-text="gname.trim() || 'Guest (belum diisi)'">Guest</span>
                                @else
                                    <span class="text-emerald-100/50">belum dipilih</span>
                                @endif
                            </span>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-emerald-100/60">Tipe</span>
                            <span class="font-bold">{{ $customer_mode === 'member' ? 'Member' : ($customer_mode === 'guest' ? 'Guest' : '—') }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-emerald-100/60">Pengiriman</span>
                            <span class="font-bold">{{ ['pickup' => 'Ambil di Toko', 'regular' => 'Kirim Reguler', 'instant' => 'Kirim Instant'][$shipping_method] ?? '—' }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-emerald-100/60">Jumlah item</span>
                            <span class="font-bold tabular-nums">{{ count($items) }}</span>
                        </div>
                    </div>

                    @if($estimated_price > 0)
                        <div class="mt-5 pt-5 border-t border-white/10 space-y-2.5 text-sm">
                            <div class="flex justify-between gap-3">
                                <span class="text-emerald-100/60">Harga master</span>
                                <span class="font-bold tabular-nums">Rp {{ number_format($estimated_price, 0, ',', '.') }}</span>
                            </div>
                            @if($total_duration_fee > 0)
                                <div class="flex justify-between gap-3 text-signal-300">
                                    <span>Biaya percepatan</span>
                                    <span class="font-bold tabular-nums">+ Rp {{ number_format($total_duration_fee, 0, ',', '.') }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="mt-5 space-y-3">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-emerald-100/60 mb-1.5">Diskon (%)</label>
                                <input type="number" min="0" max="100" wire:model.live.debounce.300ms="discount_percentage"
                                       class="w-full px-4 py-2.5 text-base tabular-nums rounded-xl bg-white/10 border border-white/15 text-white placeholder-emerald-100/40 focus:outline-none focus:ring-2 focus:ring-signal-400/50">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-emerald-100/60 mb-1.5">Harga Deal (Rp)</label>
                                <input type="number" min="0" wire:model.live.debounce.400ms="final_price_input"
                                       class="w-full px-4 py-2.5 text-base font-extrabold tabular-nums rounded-xl bg-white/10 border border-white/15 text-white focus:outline-none focus:ring-2 focus:ring-signal-400/50">
                                @error('final_price_input') <p class="mt-1.5 text-sm font-semibold text-red-300">{{ $message }}</p> @enderror
                            </div>
                            <button type="button" wire:click="useMasterPrice" class="w-full text-sm font-bold text-signal-300 hover:text-signal-200 transition-colors">Reset ke harga master</button>
                        </div>

                        @if($discount > 0)
                            <div class="mt-4 rounded-xl bg-signal-400 text-brand-950 px-4 py-3">
                                <div class="flex justify-between text-sm font-extrabold">
                                    <span>Diskon</span><span class="tabular-nums">- Rp {{ number_format($discount, 0, ',', '.') }}</span>
                                </div>
                                <p class="text-xs font-bold mt-0.5 text-right">{{ number_format($discount_percentage, 1) }}% OFF</p>
                            </div>
                        @endif

                        <div class="mt-5 pt-5 border-t border-white/10">
                            <p class="text-xs font-bold uppercase tracking-[0.22em] text-emerald-100/60">Total Tagihan</p>
                            <p class="mt-1 text-3xl font-extrabold text-signal-300 tabular-nums tracking-tight">Rp {{ number_format($final_price_input, 0, ',', '.') }}</p>
                        </div>
                    @else
                        <div class="mt-6 py-8 text-center">
                            <span class="inline-flex w-14 h-14 rounded-full bg-white/5 border border-white/10 text-emerald-100/50 items-center justify-center">{!! $oic('box', 'w-6 h-6') !!}</span>
                            <p class="mt-3 text-sm text-emerald-100/60">Lengkapi spesifikasi item<br>untuk menampilkan rincian biaya.</p>
                        </div>
                    @endif

                    <button type="submit" @disabled(!$customerReady || $estimated_price <= 0 || !$shipReady)
                            class="mt-6 w-full inline-flex items-center justify-center gap-2 py-3.5 rounded-2xl bg-signal-400 text-brand-950 text-base font-extrabold hover:bg-signal-500 shadow-md shadow-signal-400/25 transition-all disabled:opacity-40 disabled:cursor-not-allowed"
                            wire:loading.attr="disabled" wire:target="submitOrder">
                        <span wire:loading.remove wire:target="submitOrder" class="inline-flex items-center gap-2">{!! $oic('save', 'w-4 h-4') !!} Simpan Pesanan</span>
                        <span wire:loading wire:target="submitOrder" class="inline-flex items-center gap-2">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.4 0 0 5.4 0 12h4z"/></svg> Menyimpan...
                        </span>
                    </button>

                    @if(!$customerReady || !$shipReady)
                        <p class="mt-3 text-xs text-emerald-100/60 text-center">
                            @if(!$customerReady) Lengkapi data pelanggan dulu. @elseif(!$shipReady) Lengkapi alamat pengiriman. @endif
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </form>
</div>
