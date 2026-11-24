@php
    $A = [
        'user'   => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
        'mail'   => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
        'phone'  => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z',
        'camera' => 'M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z M15 13a3 3 0 11-6 0 3 3 0 016 0z',
        'lock'   => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z',
        'key'    => 'M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z',
        'save'   => 'M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4',
        'undo'   => 'M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6',
        'check'  => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        'alert'  => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
        'x'      => 'M6 18L18 6M6 6l12 12',
        'trash'  => 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16',
        'upload' => 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12',
        'shield' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
        'clock'  => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        'copy'   => 'M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z',
        'eye'    => 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z',
        'eyeoff' => 'M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21',
        'bolt'   => 'M13 10V3L4 14h7v7l9-11h-7z',
        'store'  => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z',
        'arrow'  => 'M14 5l7 7m0 0l-7 7m7-7H3',
        'badge'  => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z',
    ];
    $aic = fn($k, $c = 'w-5 h-5') => '<svg class="' . $c . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="' . ($A[$k] ?? $A['user']) . '"/></svg>';
@endphp

<div class="w-full space-y-4"
     x-data="{
        f: { name: @js($name), email: @js($email), phone: @js($phone) },
        o: { name: @js($original['name'] ?? ''), email: @js($original['email'] ?? ''), phone: @js($original['phone'] ?? '') },
        showCur: false, showNew: false,
        pw: '', pw2: '',
        get dirty() { return ['name','email','phone'].some(k => (this.f[k] ?? '') !== (this.o[k] ?? '')); },
        get initial() { return (this.f.name.trim() || 'S').charAt(0).toUpperCase(); },
        get filled() { return ['name','email','phone'].filter(k => (this.f[k] ?? '').trim() !== '').length; },
        get completeness() { return Math.round(this.filled / 3 * 100); },
        get strength() {
            let s = 0;
            if (this.pw.length >= 8) s++;
            if (/[a-z]/.test(this.pw) && /[A-Z]/.test(this.pw)) s++;
            if (/\d/.test(this.pw)) s++;
            if (/[^A-Za-z0-9]/.test(this.pw)) s++;
            return s;
        },
        get strengthLabel() { return ['Terlalu lemah','Lemah','Cukup','Kuat','Sangat kuat'][this.strength]; },
        get match() { return this.pw2.length > 0 && this.pw === this.pw2; },
        sync() {
            const g = id => { const el = document.getElementById(id); return el ? el.value : ''; };
            this.f = { name: g('ac-name'), email: g('ac-email'), phone: g('ac-phone') };
        },
        afterReset() { this.$nextTick(() => this.sync()); },
        clearPw() { this.pw = ''; this.pw2 = ''; }
     }"
     @acc-reset.window="afterReset()"
     @acc-password-done.window="clearPw()">

    <style>
        .ac-grid { background-image: linear-gradient(rgba(200,236,63,.08) 1px, transparent 1px), linear-gradient(90deg, rgba(200,236,63,.08) 1px, transparent 1px); background-size: 28px 28px; }
        @keyframes acRise { from { opacity:0; transform: translateY(12px);} to { opacity:1; transform:none;} }
        .ac-rise { animation: acRise .42s cubic-bezier(.2,.7,.2,1) both; }
        @keyframes acLive { 0%,100% { box-shadow: 0 0 0 0 rgba(200,236,63,.45);} 50% { box-shadow: 0 0 0 10px rgba(200,236,63,0);} }
        .ac-live { animation: acLive 2.4s ease-out infinite; }
        @keyframes acBar { from { opacity:0; transform: translateY(20px);} to { opacity:1; transform:none;} }
        .ac-bar { animation: acBar .3s cubic-bezier(.2,.7,.2,1) both; }
        .ac-scroll::-webkit-scrollbar { width: 10px; height: 10px; }
        .ac-scroll::-webkit-scrollbar-track { background: #e2edf0; border-radius: 10px; }
        .ac-scroll::-webkit-scrollbar-thumb { background: #1d6247; border-radius: 10px; border: 2px solid #e2edf0; }
        .ac-scroll::-webkit-scrollbar-thumb:hover { background: #0a3f33; }
        .ac-field { transition: box-shadow .18s ease, border-color .18s ease, background-color .18s ease; }
        #keamanan { scroll-margin-top: 104px; }
        @media (prefers-reduced-motion: reduce) { .ac-rise,.ac-live,.ac-bar { animation: none !important; } }
    </style>

    {{-- TOAST --}}
    @if(session('acc_success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5500)" x-show="show" x-cloak
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-3"
             class="fixed top-24 right-4 sm:right-6 z-[80] max-w-sm" role="status" aria-live="polite">
            <div class="forest-hero relative overflow-hidden rounded-2xl px-5 py-4 shadow-2xl shadow-brand-950/40 flex items-start gap-3">
                <div class="forest-dots absolute inset-0 opacity-40"></div>
                <span class="relative z-10 shrink-0 w-9 h-9 rounded-xl bg-signal-400 text-brand-950 flex items-center justify-center">{!! $aic('check', 'w-5 h-5') !!}</span>
                <p class="relative z-10 flex-1 text-base font-bold text-white">{{ session('acc_success') }}</p>
                <button type="button" @click="show = false" class="relative z-10 shrink-0 text-white/70 hover:text-white transition-colors">{!! $aic('x', 'w-4 h-4') !!}</button>
            </div>
        </div>
    @endif

    {{-- HERO --}}
    <div class="forest-hero relative overflow-hidden rounded-3xl text-white shadow-lg shadow-brand-950/25 ac-rise">
        <div class="forest-dots absolute inset-0 opacity-40"></div>
        <div class="ac-grid absolute inset-0 opacity-45"></div>
        <div class="absolute -top-24 -left-16 w-72 h-72 rounded-full bg-emerald-500/12 blur-3xl"></div>
        <div class="absolute -bottom-24 right-0 w-64 h-64 rounded-full bg-signal-400/10 blur-3xl"></div>

        <div class="relative z-10 px-6 sm:px-8 py-6 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="flex items-start gap-4 min-w-0">
                <div class="relative shrink-0">
                    <span class="absolute inset-0 rounded-2xl ring-2 ring-signal-400/40 ac-live"></span>
                    @if($avatarUrl)
                        <img src="{{ $avatarUrl }}" alt="{{ $name }}" class="relative w-16 h-16 rounded-2xl object-cover shadow-lg shadow-brand-950/30">
                    @else
                        <div class="relative w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-400 to-signal-400 text-brand-950 flex items-center justify-center text-2xl font-extrabold shadow-lg shadow-signal-400/25"
                             x-text="initial">{{ strtoupper(substr($name, 0, 1)) }}</div>
                    @endif
                </div>
                <div class="min-w-0">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/8 border border-white/15 text-[10px] font-bold uppercase tracking-[0.2em] text-signal-300">{!! $aic('badge', 'w-3 h-3') !!} Akun Staf Internal</span>
                    <h1 class="mt-2 text-2xl sm:text-3xl font-extrabold tracking-tight truncate" x-text="f.name.trim() || 'Nama Staf'">{{ $name }}</h1>
                    <p class="mt-1 text-sm text-emerald-100/70 truncate" x-text="f.email.trim() || 'email belum diisi'">{{ $email }}</p>
                    <div class="mt-2.5 flex flex-wrap items-center gap-2">
                        <span class="px-2.5 py-1 rounded-full bg-signal-400 text-brand-950 text-[10px] font-extrabold uppercase tracking-wider">{{ $roleLabel }}</span>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase {{ $statusLabel === 'active' ? 'bg-white/12 border border-white/20 text-white' : 'bg-red-500 text-white' }}">{{ $statusLabel === 'active' ? 'Aktif' : ucfirst($statusLabel) }}</span>
                        <span class="text-[11px] text-emerald-100/50 tabular-nums">Bergabung {{ $joined }}</span>
                    </div>
                </div>
            </div>

            <div class="shrink-0 w-full lg:w-64">
                <div class="flex items-center justify-between gap-3 mb-1.5">
                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-emerald-100/55">Kelengkapan Profil</p>
                    <p class="text-sm font-extrabold text-signal-300 tabular-nums" x-text="completeness + '%'">{{ $phone ? '100%' : '67%' }}</p>
                </div>
                <div class="h-2.5 rounded-full bg-white/12 overflow-hidden">
                    <div class="h-full rounded-full bg-gradient-to-r from-emerald-400 to-signal-400 transition-all duration-500" :style="'width:' + Math.max(4, completeness) + '%'"></div>
                </div>
                <div class="mt-3 flex gap-2">
                    <a href="#keamanan" class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-white/10 border border-white/15 text-xs font-bold hover:bg-white/20 transition-colors">{!! $aic('key', 'w-3.5 h-3.5') !!} Ganti Sandi</a>
                    <button type="button" onclick="copyText('{{ $email }}', this)" class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-white/10 border border-white/15 text-xs font-bold hover:bg-white/20 transition-colors">{!! $aic('copy', 'w-3.5 h-3.5') !!} Email</button>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-4 items-start">
        {{-- FORM --}}
        <div class="xl:col-span-8 space-y-4 min-w-0">
            {{-- 01 PROFIL --}}
            <section class="relative overflow-hidden rounded-3xl bg-white border border-ice-200 shadow-sm ac-rise">
                <div class="absolute left-0 top-6 bottom-6 w-1.5 rounded-r-full bg-gradient-to-b from-signal-400 to-brand-600"></div>
                <div class="p-6 pl-7 sm:pl-8">
                    <div class="flex items-center gap-3">
                        <span class="w-10 h-10 rounded-full bg-brand-600 text-signal-300 flex items-center justify-center text-sm font-black shrink-0">01</span>
                        <div>
                            <h2 class="text-lg font-extrabold text-brand-950 tracking-tight">Data Diri</h2>
                            <p class="text-sm text-gray-500">Dipakai pada topbar, jejak audit, dan catatan pesanan.</p>
                        </div>
                    </div>

                    <form wire:submit="saveProfile" class="mt-5 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="sm:col-span-2">
                                <label for="ac-name" class="block text-sm font-extrabold text-brand-950 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-brand-400">{!! $aic('user', 'w-4 h-4') !!}</span>
                                    <input id="ac-name" wire:model="name" @input="f.name = $event.target.value" type="text" maxlength="120"
                                           class="ac-field w-full pl-10 pr-4 py-3 text-base font-bold border border-ice-300 rounded-xl bg-ice-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25 focus:border-brand-500 @error('name') border-red-400 bg-red-50 @enderror">
                                </div>
                                @error('name') <p class="mt-1.5 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="ac-email" class="block text-sm font-extrabold text-brand-950 mb-1.5">Email Login <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-brand-400">{!! $aic('mail', 'w-4 h-4') !!}</span>
                                    <input id="ac-email" wire:model="email" @input="f.email = $event.target.value" type="email" maxlength="150"
                                           class="ac-field w-full pl-10 pr-4 py-3 text-base border border-ice-300 rounded-xl bg-ice-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25 focus:border-brand-500 @error('email') border-red-400 bg-red-50 @enderror">
                                </div>
                                @error('email') <p class="mt-1.5 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
                                <p class="mt-1 text-[11px] text-gray-400">Email ini dipakai untuk masuk ke sistem.</p>
                            </div>
                            <div>
                                <label for="ac-phone" class="block text-sm font-extrabold text-brand-950 mb-1.5">Telepon / WhatsApp</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-brand-400">{!! $aic('phone', 'w-4 h-4') !!}</span>
                                    <input id="ac-phone" wire:model="phone" @input="f.phone = $event.target.value" type="tel" maxlength="30" placeholder="08xxxxxxxxxx"
                                           class="ac-field w-full pl-10 pr-4 py-3 text-base tabular-nums border border-ice-300 rounded-xl bg-ice-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25 focus:border-brand-500 @error('phone') border-red-400 bg-red-50 @enderror">
                                </div>
                                @error('phone') <p class="mt-1.5 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-2.5 pt-1">
                            <button type="submit" class="inline-flex items-center gap-2 px-6 py-3.5 rounded-2xl bg-brand-600 text-signal-300 text-base font-extrabold hover:bg-brand-700 shadow-md shadow-brand-900/25 transition-all disabled:opacity-60"
                                    wire:loading.attr="disabled" wire:target="saveProfile">
                                <span wire:loading.remove wire:target="saveProfile" class="inline-flex items-center gap-2">{!! $aic('save', 'w-4 h-4') !!} Simpan Data Diri</span>
                                <span wire:loading wire:target="saveProfile">Menyimpan...</span>
                            </button>
                            <button type="button" wire:click="resetProfile" x-show="dirty" x-cloak
                                    class="inline-flex items-center gap-2 px-5 py-3.5 rounded-2xl bg-ice-100 text-brand-800 text-base font-bold hover:bg-ice-200 transition-colors">
                                {!! $aic('undo', 'w-4 h-4') !!} Batalkan
                            </button>
                            <span class="ml-auto text-[11px] text-gray-400">Role dan status akun hanya dapat diubah owner lewat manajemen pengguna.</span>
                        </div>
                    </form>
                </div>
            </section>

            {{-- 02 FOTO --}}
            <section class="relative overflow-hidden rounded-3xl bg-white border border-ice-200 shadow-sm ac-rise">
                <div class="absolute left-0 top-6 bottom-6 w-1.5 rounded-r-full bg-gradient-to-b from-brand-500 to-brand-800"></div>
                <div class="p-6 pl-7 sm:pl-8">
                    <div class="flex items-center gap-3">
                        <span class="w-10 h-10 rounded-full bg-brand-600 text-signal-300 flex items-center justify-center text-sm font-black shrink-0">02</span>
                        <div>
                            <h2 class="text-lg font-extrabold text-brand-950 tracking-tight">Foto Profil</h2>
                            <p class="text-sm text-gray-500">Tampil di topbar dan dropdown akun.</p>
                        </div>
                    </div>

                    <form wire:submit="saveAvatar" class="mt-5 flex flex-col sm:flex-row sm:items-center gap-5">
                        <div class="relative shrink-0 self-start">
                            @if($avatar && str_starts_with((string) $avatar->getMimeType(), 'image/'))
                                <img src="{{ $avatar->temporaryUrl() }}" alt="Pratinjau foto baru" class="w-20 h-20 rounded-2xl object-cover ring-2 ring-signal-400/60 shadow-lg">
                            @elseif($avatarUrl)
                                <img src="{{ $avatarUrl }}" alt="{{ $name }}" class="w-20 h-20 rounded-2xl object-cover ring-2 ring-brand-300 shadow-lg">
                            @else
                                <x-avatar :user="$user" size="xl" shape="rounded-2xl" :ring="true" class="shadow-lg" />
                            @endif
                            <label for="ac-avatar" title="Pilih foto"
                                   class="absolute -bottom-2 -right-2 w-8 h-8 rounded-xl bg-signal-400 text-brand-950 flex items-center justify-center cursor-pointer hover:bg-signal-500 shadow-md transition-colors">
                                {!! $aic('camera', 'w-4 h-4') !!}
                            </label>
                        </div>

                        <div class="flex-1 min-w-0">
                            <input id="ac-avatar" type="file" wire:model="avatar" class="sr-only" accept="image/jpeg,image/png,image/jpg,image/webp">
                            <div class="flex flex-wrap items-center gap-2">
                                <label for="ac-avatar" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-ice-100 border border-ice-300 text-sm font-bold text-brand-800 hover:bg-ice-200 cursor-pointer transition-colors">
                                    {!! $aic('upload', 'w-4 h-4') !!} Pilih File
                                </label>
                                <button type="submit" @disabled(!$avatar)
                                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-brand-600 text-signal-300 text-sm font-extrabold hover:bg-brand-700 transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
                                        wire:loading.attr="disabled" wire:target="avatar">
                                    {!! $aic('save', 'w-4 h-4') !!}
                                    <span wire:loading.remove wire:target="avatar">Unggah</span>
                                    <span wire:loading wire:target="avatar">Mengunggah…</span>
                                </button>
                                @if($currentAvatar)
                                    <button type="button" wire:click="removeAvatar" wire:confirm="Hapus foto profil dan kembali ke inisial nama?"
                                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-red-50 border border-red-200 text-sm font-bold text-red-600 hover:bg-red-100 transition-colors">
                                        {!! $aic('trash', 'w-4 h-4') !!} Hapus
                                    </button>
                                @endif
                            </div>
                            <div wire:loading wire:target="avatar" class="mt-3 h-1.5 rounded-full bg-ice-200 overflow-hidden">
                                <div class="h-full w-1/2 rounded-full bg-brand-500 animate-pulse"></div>
                            </div>
                            @error('avatar') <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
                            <p class="mt-2 text-xs text-gray-400">
                                @if($avatar)<b class="text-brand-700">{{ $avatar->getClientOriginalName() }}</b> siap diunggah · @endif
                                JPG, PNG, WEBP · maksimal 2MB
                            </p>
                        </div>
                    </form>
                </div>
            </section>

            {{-- 03 KEAMANAN --}}
            <section id="keamanan" class="relative overflow-hidden rounded-3xl bg-white border-2 border-amber-300 shadow-sm ac-rise">
                <div class="absolute left-0 top-6 bottom-6 w-1.5 rounded-r-full bg-gradient-to-b from-amber-400 to-amber-600"></div>
                <div class="p-6 pl-7 sm:pl-8">
                    <div class="flex items-center gap-3">
                        <span class="w-10 h-10 rounded-full bg-amber-500 text-white flex items-center justify-center text-sm font-black shrink-0">03</span>
                        <div>
                            <h2 class="text-lg font-extrabold text-brand-950 tracking-tight">Keamanan Akun</h2>
                            <p class="text-sm text-gray-500">Ganti kata sandi secara berkala, terutama bila akun dipakai bersama perangkat.</p>
                        </div>
                    </div>

                    <form wire:submit="updatePassword" class="mt-5 space-y-4">
                        <div>
                            <label class="block text-sm font-extrabold text-brand-950 mb-1.5">Kata Sandi Saat Ini <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-brand-400">{!! $aic('lock', 'w-4 h-4') !!}</span>
                                <input type="password" wire:model="current_password" :type="showCur ? 'text' : 'password'" autocomplete="current-password"
                                       class="ac-field w-full pl-10 pr-11 py-3 text-base border border-ice-300 rounded-xl bg-ice-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/25 focus:border-amber-500 @error('current_password') border-red-400 bg-red-50 @enderror">
                                <button type="button" @click="showCur = !showCur" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-brand-600 transition-colors">
                                    <span x-show="!showCur">{!! $aic('eye', 'w-4 h-4') !!}</span>
                                    <span x-show="showCur" x-cloak>{!! $aic('eyeoff', 'w-4 h-4') !!}</span>
                                </button>
                            </div>
                            @error('current_password') <p class="mt-1.5 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-extrabold text-brand-950 mb-1.5">Kata Sandi Baru <span class="text-red-500">*</span></label>
                                <input type="password" wire:model="password" x-model="pw" :type="showNew ? 'text' : 'password'" autocomplete="new-password"
                                       class="ac-field w-full px-4 py-3 text-base border border-ice-300 rounded-xl bg-ice-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/25 focus:border-amber-500 @error('password') border-red-400 bg-red-50 @enderror">
                                @error('password') <p class="mt-1.5 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
                                <div class="mt-2" x-show="pw.length > 0" x-cloak>
                                    <div class="flex gap-1">
                                        <template x-for="i in 4" :key="i">
                                            <span class="flex-1 h-1.5 rounded-full transition-colors"
                                                  :class="i <= strength ? (strength <= 1 ? 'bg-red-500' : (strength === 2 ? 'bg-amber-500' : (strength === 3 ? 'bg-brand-500' : 'bg-signal-400'))) : 'bg-ice-200'"></span>
                                        </template>
                                    </div>
                                    <p class="mt-1 text-[11px] font-bold" :class="strength <= 1 ? 'text-red-600' : (strength === 2 ? 'text-amber-600' : 'text-brand-700')" x-text="strengthLabel"></p>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-extrabold text-brand-950 mb-1.5">Ulangi Kata Sandi Baru <span class="text-red-500">*</span></label>
                                <input type="password" wire:model="password_confirmation" x-model="pw2" :type="showNew ? 'text' : 'password'" autocomplete="new-password"
                                       class="ac-field w-full px-4 py-3 text-base border border-ice-300 rounded-xl bg-ice-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/25 focus:border-amber-500">
                                <p class="mt-2 text-[11px] font-bold" x-show="pw2.length > 0" x-cloak
                                   :class="match ? 'text-signal-600' : 'text-red-600'"
                                   x-text="match ? 'Kata sandi cocok' : 'Belum cocok'"></p>
                            </div>
                        </div>

                        <div class="rounded-xl bg-amber-50 border border-amber-200 px-4 py-3 flex items-start gap-2.5">
                            <span class="shrink-0 text-amber-700 mt-0.5">{!! $aic('shield', 'w-4 h-4') !!}</span>
                            <p class="text-sm text-amber-800">Syarat minimal: <b>8 karakter</b>, mengandung <b>huruf</b> dan <b>angka</b>, serta berbeda dari kata sandi lama. Kombinasi huruf besar-kecil dan simbol membuat akun lebih aman.</p>
                        </div>

                        <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-2xl bg-amber-500 text-white text-base font-extrabold hover:bg-amber-600 shadow-md shadow-amber-500/25 transition-all disabled:opacity-60"
                                wire:loading.attr="disabled" wire:target="updatePassword">
                            <span wire:loading.remove wire:target="updatePassword" class="inline-flex items-center gap-2">{!! $aic('key', 'w-4 h-4') !!} Ganti Kata Sandi</span>
                            <span wire:loading wire:target="updatePassword">Memproses...</span>
                        </button>
                    </form>
                </div>
            </section>
        </div>

        {{-- RAIL --}}
        <div class="xl:col-span-4 xl:sticky xl:top-24 space-y-4">
            <div class="rounded-3xl bg-white border border-ice-200 shadow-sm overflow-hidden ac-rise">
                <div class="px-5 py-3.5 bg-ice-50 border-b border-ice-200 flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-brand-600 text-signal-300 flex items-center justify-center">{!! $aic('badge', 'w-4 h-4') !!}</span>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-gray-400">Informasi Akun</p>
                        <p class="text-sm font-extrabold text-brand-950 leading-tight">Tidak dapat diubah sendiri</p>
                    </div>
                </div>
                <div class="p-5 space-y-2.5 text-sm">
                    <div class="flex justify-between gap-3"><span class="text-gray-500">ID akun</span><span class="font-bold tabular-nums">#{{ $user->id }}</span></div>
                    <div class="flex justify-between gap-3"><span class="text-gray-500">Role</span><span class="font-bold">{{ $roleLabel }}</span></div>
                    <div class="flex justify-between gap-3"><span class="text-gray-500">Status</span><span class="font-bold {{ $statusLabel === 'active' ? 'text-signal-600' : 'text-red-600' }}">{{ ucfirst($statusLabel) }}</span></div>
                    <div class="flex justify-between gap-3"><span class="text-gray-500">Bergabung</span><span class="font-bold tabular-nums">{{ $joined }}</span></div>
                    <div class="flex justify-between gap-3"><span class="text-gray-500">Terakhir diubah</span><span class="font-bold tabular-nums">{{ $updated }}</span></div>
                    @if($verified !== 'n/a')
                        <div class="flex justify-between gap-3"><span class="text-gray-500">Email terverifikasi</span><span class="font-bold tabular-nums {{ $verified ? 'text-signal-600' : 'text-amber-600' }}">{{ $verified ?: 'belum' }}</span></div>
                    @endif
                </div>
            </div>

            <div class="forest-hero relative overflow-hidden rounded-3xl p-6 text-white shadow-lg shadow-brand-950/20 ac-rise">
                <div class="forest-dots absolute inset-0 opacity-40"></div>
                <div class="relative">
                    <p class="text-xs font-bold uppercase tracking-[0.22em] text-signal-300">Hak Akses Anda</p>
                    <p class="mt-1 text-[11px] text-emerald-100/60">Berdasarkan role <b class="text-signal-300">{{ $roleLabel }}</b>.</p>
                    <div class="mt-4 space-y-2">
                        @foreach($access as $ac)
                            <div class="flex items-start gap-2.5 rounded-xl bg-white/8 border border-white/15 px-3.5 py-2.5">
                                <span class="shrink-0 text-signal-300 mt-0.5">{!! $aic('check', 'w-4 h-4') !!}</span>
                                <p class="text-[12px] leading-snug text-emerald-100/85">{{ $ac }}</p>
                            </div>
                        @endforeach
                    </div>
                    <p class="mt-4 text-[11px] text-emerald-100/50">Perubahan role hanya dapat dilakukan owner atau super admin.</p>
                </div>
            </div>

            <div class="rounded-3xl bg-white border border-ice-200 p-5 ac-rise">
                <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-gray-400">Praktik Aman</p>
                <div class="mt-3 space-y-2.5">
                    @foreach([
                        'Jangan bagikan kata sandi ke rekan kerja — setiap orang punya akun sendiri agar jejak audit sahih.',
                        'Ganti kata sandi bila perangkat dipakai bersama atau setelah shift berakhir.',
                        'Selalu keluar dari akun saat meninggalkan komputer toko.',
                    ] as $tip)
                        <div class="flex items-start gap-2.5">
                            <span class="shrink-0 text-brand-500 mt-0.5">{!! $aic('shield', 'w-3.5 h-3.5') !!}</span>
                            <p class="text-[12px] leading-snug text-gray-600">{{ $tip }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- STICKY BAR --}}
    <div x-show="dirty" x-cloak class="fixed bottom-4 left-0 right-0 z-40 px-4 ac-bar">
        <div class="max-w-3xl mx-auto forest-hero relative overflow-hidden rounded-[1.5rem] shadow-2xl shadow-brand-950/40 px-5 py-4 flex flex-col sm:flex-row sm:items-center gap-4">
            <div class="forest-dots absolute inset-0 opacity-40"></div>
            <div class="relative flex items-center gap-3 flex-1 min-w-0">
                <span class="shrink-0 w-10 h-10 rounded-xl bg-amber-400 text-brand-950 flex items-center justify-center ac-live">{!! $aic('alert', 'w-5 h-5') !!}</span>
                <div class="min-w-0">
                    <p class="text-base font-extrabold text-white">Data diri belum disimpan</p>
                    <p class="text-sm text-emerald-100/65 truncate">Topbar dan jejak audit masih memakai nama lama.</p>
                </div>
            </div>
            <div class="relative flex items-center gap-2.5 shrink-0">
                <button type="button" wire:click="resetProfile" class="px-4 py-2.5 rounded-xl bg-white/10 border border-white/15 text-white text-sm font-bold hover:bg-white/20 transition-colors">Batalkan</button>
                <button type="button" wire:click="saveProfile" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-signal-400 text-brand-950 text-sm font-extrabold hover:bg-signal-500 transition-colors disabled:opacity-60"
                        wire:loading.attr="disabled" wire:target="saveProfile">
                    <span wire:loading.remove wire:target="saveProfile" class="inline-flex items-center gap-2">{!! $aic('save', 'w-4 h-4') !!} Simpan</span>
                    <span wire:loading wire:target="saveProfile">Menyimpan...</span>
                </button>
            </div>
        </div>
    </div>
</div>