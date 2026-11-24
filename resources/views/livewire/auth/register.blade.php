@php
    $svg = fn($d, $cl = 'w-5 h-5') => '<svg class="'.$cl.'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="'.$d.'"/></svg>';
    $P = [
        'mail' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
        'lock' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z',
        'user' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
        'phone' => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z',
        'building' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
        'printer' => 'M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z',
        'shield' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
        'check' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        'alert' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
        'arrow' => 'M14 5l7 7m0 0l-7 7m7-7H3',
        'back' => 'M10 19l-7-7m0 0l7-7m-7 7h18',
        'clock' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        'undo' => 'M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6',
        'send' => 'M12 19l9 2-9-18-9 18 9-2zm0 0v-8',
        'box' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
        'link' => 'M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1',
        'users' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
        'pin' => 'M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z',
        'eye' => 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z',
        'eyeoff' => 'M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21',
    ];
    $iPrinter = $svg($P['printer'], 'w-6 h-6');
    $iMail = $svg($P['mail'], 'w-[18px] h-[18px]');
    $iLock = $svg($P['lock'], 'w-[18px] h-[18px]');
    $iUser = $svg($P['user'], 'w-[18px] h-[18px]');
    $iPhone = $svg($P['phone'], 'w-[18px] h-[18px]');
    $iBuild = $svg($P['building'], 'w-[18px] h-[18px]');
    $iPin = $svg($P['pin'], 'w-[18px] h-[18px]');
    $iEye = $svg($P['eye'], 'w-[18px] h-[18px]');
    $iEyeOff = $svg($P['eyeoff'], 'w-[18px] h-[18px]');
    $iArrow = $svg($P['arrow'], 'w-[18px] h-[18px]');
    $iBack = $svg($P['back'], 'w-4 h-4');
    $iAlert = $svg($P['alert'], 'w-4 h-4');
    $iShield = $svg($P['shield'], 'w-[18px] h-[18px]');
    $iCheck = $svg($P['check'], 'w-4 h-4');
    $iCheckBig = $svg($P['check'], 'w-10 h-10');
    $iClock = $svg($P['clock'], 'w-4 h-4');
    $iUndo = $svg($P['undo'], 'w-4 h-4');
    $iSend = $svg($P['send'], 'w-[18px] h-[18px]');

    $featHtml = '';
    foreach ([
        ['box', 'Stok & MRP terpantau', 'Bahan dipesan sebelum habis'],
        ['link', 'Invoice tanpa login', 'Tim finance bayar lewat tautan'],
        ['shield', 'Approval berjenjang', 'Ajuan produksi, owner setuju'],
        ['users', 'Satu akun perusahaan', 'Riwayat order terpusat'],
    ] as $f) {
        $featHtml .= '<div class="flex items-start gap-3 rounded-2xl border border-white/12 bg-white/[.06] px-4 py-3">'
            . '<span class="shrink-0 w-8 h-8 rounded-lg flex items-center justify-center" style="background:rgba(200,236,63,.16);color:var(--lime)">'.$svg($P[$f[0]], 'w-4 h-4').'</span>'
            . '<div class="min-w-0"><p class="text-[13px] font-extrabold leading-tight">'.e($f[1]).'</p>'
            . '<p class="text-[11px] text-white/55 mt-0.5">'.e($f[2]).'</p></div></div>';
    }

    $order = ['form' => 'Data', 'verify' => 'Verifikasi', 'done' => 'Selesai'];
    $keys = array_keys($order);
    $idx = array_search($step, $keys, true);
    if ($idx === false) $idx = 0;
    $stepper = '';
    foreach ($keys as $i => $k) {
        $on = $i <= $idx;
        $style = $on ? 'background:var(--press-700);color:var(--lime)' : 'background:#fff;color:var(--ink-mute);border:1px solid var(--ice-300)';
        $inner = $i < $idx ? $iCheck : '<span class="mono">'.($i + 1).'</span>';
        $stepper .= '<div class="flex items-center gap-1.5 '.($i < count($keys) - 1 ? 'flex-1' : '').'">'
            . '<span class="shrink-0 inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider" style="'.$style.'">'
            . $inner . '<span class="hidden sm:inline">'.e($order[$k]).'</span></span>';
        if ($i < count($keys) - 1) {
            $stepper .= '<span class="flex-1 h-0.5 rounded-full" style="background:'.($i < $idx ? 'var(--press-500)' : 'var(--ice-300)').'"></span>';
        }
        $stepper .= '</div>';
    }

    $err = fn($k) => $errors->first($k) ? '<p class="mt-1.5 text-[12px] font-semibold text-red-600">'.e($errors->first($k)).'</p>' : '';
    $b = fn($k) => $errors->has($k) ? 'border-red-400' : 'border-ice-300';

    $failureBox = $failure !== ''
        ? '<div class="ax-pop mb-4 rounded-2xl border-l-4 px-4 py-3 flex items-start gap-2.5" style="border-color:#ef4444;background:#fef2f2">'
            . '<span class="shrink-0 mt-0.5" style="color:#dc2626">'.$iAlert.'</span>'
            . '<p class="text-[13px] font-semibold leading-snug" style="color:#991b1b">'.e($failure).'</p></div>'
        : '';
    $noticeBox = $notice !== ''
        ? '<div class="ax-pop mb-4 rounded-2xl border-l-4 px-4 py-3 flex items-start gap-2.5" style="border-color:#1d6247;background:rgba(200,236,63,.18)">'
            . '<span class="shrink-0 mt-0.5" style="color:var(--press-600)">'.$iCheck.'</span>'
            . '<p class="text-[13px] font-semibold leading-snug" style="color:var(--press-800)">'.e($notice).'</p></div>'
        : '';

    $bizStyle = $accountType === 'business' ? 'border-color:var(--press-600);background:rgba(200,236,63,.14)' : 'border-color:var(--ice-300)';
    $perStyle = $accountType === 'personal' ? 'border-color:var(--press-600);background:rgba(200,236,63,.14)' : 'border-color:var(--ice-300)';
    $waAdmin = $support !== '' ? 'https://wa.me/'.preg_replace('/\D+/', '', $support) : null;
    $supportLink = $waAdmin ? ' <a href="'.$waAdmin.'" target="_blank" rel="noopener" class="font-extrabold hover:underline" style="color:var(--press-600)">Hubungi admin</a>.' : '';
    $otpBoxes = '';
    for ($i = 0; $i < $digits; $i++) {
        $otpBoxes .= '<input x-ref="d'.$i.'" type="text" inputmode="numeric" maxlength="'.($i === 0 ? $digits : 1).'"'
            . ' autocomplete="'.($i === 0 ? 'one-time-code' : 'off').'" aria-label="Digit '.($i + 1).'"'
            . ' @input="onInput('.$i.',$event)" @keydown="onKey('.$i.',$event)" @focus="$event.target.select()"'
            . ' class="ax-otp ax-field mono flex-1 min-w-0 h-16 text-center text-[26px] font-bold rounded-2xl bg-white border-2 border-ice-300" style="color:var(--press-800)">';
    }
@endphp

<div class="min-h-screen w-full lg:grid lg:grid-cols-[.85fr_1fr]"
     data-step="{{ $step }}" data-type="{{ $accountType }}"
     x-data="{
        digits: Array({{ $digits }}).fill(''), left: 0, timer: null, show: false, pw: '', pw2: '',
        start(s){ this.left=s; clearInterval(this.timer); this.timer=setInterval(()=>{ if(this.left>0) this.left--; else clearInterval(this.timer); },1000); },
        focusFirst(){ this.$nextTick(()=>{ this.$refs.d0?.focus(); }); },
        clearAll(){ this.digits=Array({{ $digits }}).fill(''); for(let i=0;i<{{ $digits }};i++){ const el=this.$refs['d'+i]; if(el) el.value=''; } this.focusFirst(); },
        push(){ const v=this.digits.join(''); $wire.set('code', v); if(v.length==={{ $digits }}) $wire.verify(); },
        onInput(i,e){ const raw=(e.target.value||'').replace(/\D/g,''); if(raw.length>1){ this.fill(raw,i); return; } this.digits[i]=raw; e.target.value=raw; if(raw && i<{{ $digits - 1 }}) this.$refs['d'+(i+1)]?.focus(); this.push(); },
        onKey(i,e){ if(e.key==='Backspace' && !this.digits[i] && i>0){ const p=this.$refs['d'+(i-1)]; p?.focus(); this.digits[i-1]=''; if(p) p.value=''; e.preventDefault(); this.push(); } if(e.key==='ArrowLeft' && i>0){ this.$refs['d'+(i-1)]?.focus(); e.preventDefault(); } if(e.key==='ArrowRight' && i<{{ $digits - 1 }}){ this.$refs['d'+(i+1)]?.focus(); e.preventDefault(); } },
        fill(t,from=0){ const c=(t||'').replace(/\D/g,'').slice(0,{{ $digits }}); for(let k=0;k<c.length && from+k<{{ $digits }};k++){ this.digits[from+k]=c[k]; const el=this.$refs['d'+(from+k)]; if(el) el.value=c[k]; } this.$refs['d'+Math.min(from+c.length,{{ $digits - 1 }})]?.focus(); this.push(); },
        onPaste(e){ e.preventDefault(); this.fill((e.clipboardData||window.clipboardData).getData('text'),0); },
        get strength(){ const p=this.pw||''; let s=0; if(p.length>=8)s++; if(/[a-z]/.test(p)&&/[A-Z]/.test(p))s++; if(/\d/.test(p))s++; if(/[^A-Za-z0-9]/.test(p))s++; return s; },
        get match(){ return this.pw2.length>0 && this.pw===this.pw2; }
     }"
     @otp-sent.window="start($event.detail.seconds ?? 60); focusFirst()"
     @otp-focus.window="focusFirst()"
     @otp-clear.window="clearAll()"
     @reg-shake.window="const c=document.getElementById('reg-card'); c?.classList.add('ax-shake'); setTimeout(()=>c?.classList.remove('ax-shake'),450)">

    <style>
        .stp { display: none }
        [data-step="form"] .stp-form { display: block }
        [data-step="verify"] .stp-verify { display: block }
        [data-step="done"] .stp-done { display: block }
        .fld-company { display: none }
        [data-type="business"] .fld-company { display: block }
    </style>

    <section class="ax-mesh ax-grain relative overflow-hidden min-h-[180px] lg:min-h-screen flex flex-col justify-between px-6 sm:px-10 lg:px-12 py-7 lg:py-12 text-white">
        <div class="ax-halftone ax-pan absolute inset-0 opacity-[.2] pointer-events-none"></div>
        <div class="ax-drift absolute -top-24 -right-20 w-[22rem] h-[22rem] rounded-full blur-3xl pointer-events-none" style="background:rgba(34,211,238,.2)"></div>
        <div class="ax-drift absolute -bottom-28 -left-16 w-[20rem] h-[20rem] rounded-full blur-3xl pointer-events-none" style="background:rgba(200,236,63,.18);animation-delay:-8s"></div>

        <div class="relative ax-rise flex items-center gap-3">
            <span class="shrink-0 w-11 h-11 rounded-2xl flex items-center justify-center shadow-lg" style="background:var(--lime);color:var(--press-900)">{!! $iPrinter !!}</span>
            <div class="min-w-0">
                <p class="text-base font-extrabold tracking-tight leading-none truncate">{{ $brand }}</p>
                <p class="text-[10px] font-bold uppercase tracking-[.24em] mt-1" style="color:var(--lime)">Pendaftaran Klien</p>
            </div>
        </div>

        <div class="relative hidden lg:block">
            <h1 class="ax-rise text-[2.3rem] xl:text-[2.8rem] font-extrabold leading-[1.06] tracking-tight max-w-[14ch]" style="animation-delay:.1s">Satu akun untuk<br><span style="color:var(--lime)">seluruh cetakan.</span></h1>
            <p class="ax-rise mt-4 text-[14.5px] leading-relaxed text-white/70 max-w-[36ch]" style="animation-delay:.18s">Ajukan order, pantau produksi, dan unduh invoice perusahaan dari satu portal.</p>
            <div class="ax-rise mt-8 space-y-2.5" style="animation-delay:.26s">{!! $featHtml !!}</div>
        </div>

        <a href="{{ $loginUrl }}" class="relative ax-rise inline-flex items-center gap-2 self-start px-4 py-2.5 rounded-xl border border-white/15 bg-white/[.07] text-[12.5px] font-bold hover:bg-white/15 transition-colors" style="animation-delay:.3s">{!! $iBack !!} Sudah punya akun? Masuk</a>
    </section>

    <section class="relative bg-ice-100 ax-grid-light flex items-center justify-center px-5 sm:px-10 py-9 lg:py-12">
        <div id="reg-card" class="w-full max-w-[28rem]">
            <div class="ax-rise flex items-center gap-1.5 mb-6">{!! $stepper !!}</div>

            {!! $failureBox !!}
            {!! $noticeBox !!}

            <div class="stp stp-form ax-rise">
                <h2 class="text-[1.75rem] font-extrabold tracking-tight leading-tight">Daftarkan akun</h2>
                <p class="mt-2 text-[14px]" style="color:var(--ink-soft)">Gratis. Verifikasi email {{ $ttlMinutes }} menit, langsung bisa order.</p>

                <form wire:submit="submit" class="mt-6 space-y-4">
                    <div>
                        <span class="block text-[12.5px] font-extrabold mb-2">Jenis akun</span>
                        <div class="grid grid-cols-2 gap-2.5">
                            <button type="button" wire:click="setType('business')" class="ax-btn flex flex-col items-center gap-1.5 px-3 py-3.5 rounded-2xl border-2 bg-white" style="{{ $bizStyle }}">
                                <span style="color:var(--press-700)">{!! $iBuild !!}</span>
                                <span class="text-[12px] font-extrabold" style="color:var(--press-800)">Perusahaan</span>
                            </button>
                            <button type="button" wire:click="setType('personal')" class="ax-btn flex flex-col items-center gap-1.5 px-3 py-3.5 rounded-2xl border-2 bg-white" style="{{ $perStyle }}">
                                <span style="color:var(--press-700)">{!! $iUser !!}</span>
                                <span class="text-[12px] font-extrabold" style="color:var(--press-800)">Perorangan</span>
                            </button>
                        </div>
                    </div>

                    <div class="fld-company">
                        <label for="rg-company" class="block text-[12.5px] font-extrabold mb-1.5">Nama perusahaan <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none" style="color:var(--ink-mute)">{!! $iBuild !!}</span>
                            <input id="rg-company" type="text" wire:model="companyName" maxlength="150" placeholder="PT Maju Bersama"
                                   class="ax-field w-full pl-12 pr-4 py-3.5 text-[16px] rounded-2xl bg-white border {{ $b('companyName') }}">
                        </div>
                        {!! $err('companyName') !!}
                    </div>

                    <div>
                        <label for="rg-name" class="block text-[12.5px] font-extrabold mb-1.5">Nama penanggung jawab <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none" style="color:var(--ink-mute)">{!! $iUser !!}</span>
                            <input id="rg-name" type="text" wire:model="name" maxlength="120" placeholder="Nama lengkap"
                                   class="ax-field w-full pl-12 pr-4 py-3.5 text-[16px] rounded-2xl bg-white border {{ $b('name') }}">
                        </div>
                        {!! $err('name') !!}
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="rg-email" class="block text-[12.5px] font-extrabold mb-1.5">Email <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none" style="color:var(--ink-mute)">{!! $iMail !!}</span>
                                <input id="rg-email" type="email" wire:model="email" inputmode="email" autocomplete="username" placeholder="nama@perusahaan.co.id"
                                       class="ax-field w-full pl-12 pr-4 py-3.5 text-[16px] rounded-2xl bg-white border {{ $b('email') }}">
                            </div>
                            {!! $err('email') !!}
                        </div>
                        <div>
                            <label for="rg-phone" class="block text-[12.5px] font-extrabold mb-1.5">WhatsApp <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none" style="color:var(--ink-mute)">{!! $iPhone !!}</span>
                                <input id="rg-phone" type="tel" wire:model="phone" inputmode="tel" maxlength="30" placeholder="0812xxxxxxx"
                                       class="ax-field w-full pl-12 pr-4 py-3.5 text-[16px] rounded-2xl bg-white border {{ $b('phone') }}">
                            </div>
                            {!! $err('phone') !!}
                        </div>
                    </div>

                    <div>
                        <label for="rg-addr" class="block text-[12.5px] font-extrabold mb-1.5">Alamat pengiriman <span style="color:var(--ink-mute)">(opsional)</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none" style="color:var(--ink-mute)">{!! $iPin !!}</span>
                            <input id="rg-addr" type="text" wire:model="address" maxlength="500" placeholder="Jalan, nomor, kota"
                                   class="ax-field w-full pl-12 pr-4 py-3.5 text-[16px] rounded-2xl bg-white border border-ice-300">
                        </div>
                    </div>

                    <div>
                        <label for="rg-pw" class="block text-[12.5px] font-extrabold mb-1.5">Kata sandi <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none" style="color:var(--ink-mute)">{!! $iLock !!}</span>
                            <input id="rg-pw" :type="show ? 'text' : 'password'" wire:model="password" x-model="pw" autocomplete="new-password" placeholder="Min. 8 karakter"
                                   class="ax-field w-full pl-12 pr-12 py-3.5 text-[16px] rounded-2xl bg-white border {{ $b('password') }}">
                            <button type="button" @click="show=!show" class="absolute inset-y-0 right-0 pr-4 flex items-center" style="color:var(--ink-mute)">
                                <span x-show="!show">{!! $iEye !!}</span>
                                <span x-show="show" x-cloak style="color:var(--press-600)">{!! $iEyeOff !!}</span>
                            </button>
                        </div>
                        {!! $err('password') !!}
                        <div x-show="pw.length>0" x-cloak class="mt-2">
                            <div class="flex gap-1">
                                <template x-for="i in 4" :key="i">
                                    <span class="flex-1 h-1.5 rounded-full transition-colors" :style="`background:${ i<=strength ? (strength<=1?'#ef4444':strength===2?'#f59e0b':strength===3?'#1d6247':'#c8ec3f') : 'var(--ice-300)' }`"></span>
                                </template>
                            </div>
                            <p class="mt-1 text-[11.5px] font-bold" :style="`color:${strength<=1?'#dc2626':strength===2?'#b45309':'var(--press-600)'}`" x-text="['Terlalu lemah','Lemah','Cukup','Kuat','Sangat kuat'][strength]"></p>
                        </div>
                    </div>

                    <div>
                        <label for="rg-pw2" class="block text-[12.5px] font-extrabold mb-1.5">Ulangi kata sandi <span class="text-red-500">*</span></label>
                        <input id="rg-pw2" :type="show ? 'text' : 'password'" wire:model="password_confirmation" x-model="pw2" autocomplete="new-password"
                               class="ax-field w-full px-4 py-3.5 text-[16px] rounded-2xl bg-white border border-ice-300">
                        <p x-show="pw2.length>0" x-cloak class="mt-1.5 text-[12px] font-bold" :style="`color:${match?'var(--press-600)':'#dc2626'}`" x-text="match ? 'Sandi cocok' : 'Belum cocok'"></p>
                    </div>

                    <label class="flex items-start gap-3 cursor-pointer select-none">
                        <input type="checkbox" wire:model="agree" class="mt-0.5 w-5 h-5 rounded border-ice-300 shrink-0" style="accent-color:var(--press-600)">
                        <span class="text-[12.5px] leading-snug" style="color:var(--ink-soft)">Saya menyetujui ketentuan layanan dan kebijakan privasi {{ $brand }}, serta memastikan data perusahaan yang diisi benar.</span>
                    </label>
                    {!! $err('agree') !!}

                    <button type="submit" wire:loading.attr="disabled" wire:target="submit"
                            class="ax-btn w-full inline-flex items-center justify-center gap-2 px-6 py-4 rounded-2xl text-[15px] font-extrabold shadow-lg disabled:opacity-70"
                            style="background:var(--press-700);color:var(--lime);box-shadow:0 12px 26px -12px rgba(10,63,51,.7)">
                        <span wire:loading.remove wire:target="submit" class="inline-flex items-center gap-2">{!! $iSend !!} Kirim Kode Verifikasi</span>
                        <span wire:loading wire:target="submit">Mengirim...</span>
                    </button>
                </form>
            </div>

            <div class="stp stp-verify ax-rise">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white border border-ice-300 text-[10px] font-extrabold uppercase tracking-[.16em]" style="color:var(--press-600)">{!! $iMail !!} Terkirim via Email</span>
                <h2 class="mt-3 text-[1.75rem] font-extrabold tracking-tight leading-tight">Verifikasi email</h2>
                <p class="mt-2 text-[14px]" style="color:var(--ink-soft)">Kode dikirim ke <b class="mono" style="color:var(--press-700)">{{ $masked }}</b>. Berlaku {{ $ttlMinutes }} menit.</p>

                <div class="mt-6" @paste="onPaste($event)">
                    <div class="flex gap-2">{!! $otpBoxes !!}</div>
                    <div class="mt-3 flex items-center justify-between gap-3 text-[12px]">
                        <span style="color:var(--ink-mute)">Sisa percobaan: <b class="mono" style="color:var(--press-700)">{{ $attemptsLeft }}</b></span>
                        <button type="button" @click="clearAll()" class="font-bold hover:underline" style="color:var(--press-600)">Bersihkan</button>
                    </div>
                </div>

                <button type="button" wire:click="verify" wire:loading.attr="disabled" wire:target="verify"
                        class="ax-btn mt-5 w-full inline-flex items-center justify-center gap-2 px-6 py-4 rounded-2xl text-[15px] font-extrabold shadow-lg disabled:opacity-70"
                        style="background:var(--press-700);color:var(--lime);box-shadow:0 12px 26px -12px rgba(10,63,51,.7)">
                    <span wire:loading.remove wire:target="verify" class="inline-flex items-center gap-2">{!! $iShield !!} Verifikasi &amp; Buat Akun</span>
                    <span wire:loading wire:target="verify">Memproses...</span>
                </button>

                <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                    <button type="button" wire:click="resend" x-show="left === 0" class="inline-flex items-center gap-1.5 text-[13px] font-extrabold hover:underline" style="color:var(--press-600)">{!! $iUndo !!} Kirim ulang kode</button>
                    <span x-show="left > 0" class="inline-flex items-center gap-1.5 text-[13px] font-semibold" style="color:var(--ink-mute)">{!! $iClock !!} Kirim ulang dalam <b class="mono" x-text="left + 's'"></b></span>
                    <button type="button" wire:click="backToForm" class="text-[13px] font-bold hover:underline" style="color:var(--ink-soft)">Ubah data</button>
                </div>
            </div>

            <div class="stp stp-done ax-rise text-center">
                <span class="inline-flex w-20 h-20 rounded-3xl items-center justify-center mx-auto shadow-lg" style="background:var(--lime);color:var(--press-900)">{!! $iCheckBig !!}</span>
                <h2 class="mt-5 text-[1.85rem] font-extrabold tracking-tight">Akun aktif</h2>
                <p class="mt-2 text-[14px] max-w-[34ch] mx-auto" style="color:var(--ink-soft)">Email terverifikasi dan Anda sudah masuk. Silakan lengkapi pesanan pertama Anda.</p>
                <button type="button" wire:click="finish" class="ax-btn mt-7 w-full inline-flex items-center justify-center gap-2 px-6 py-4 rounded-2xl text-[15px] font-extrabold shadow-lg" style="background:var(--press-700);color:var(--lime);box-shadow:0 12px 26px -12px rgba(10,63,51,.7)">Masuk ke portal {!! $iArrow !!}</button>
            </div>

            <div class="ax-rise mt-7 rounded-2xl bg-white border border-ice-300 px-4 py-3.5 flex items-start gap-3" style="animation-delay:.2s">
                <span class="shrink-0 mt-0.5" style="color:var(--press-500)">{!! $iShield !!}</span>
                <p class="text-[12px] leading-snug" style="color:var(--ink-soft)">Data perusahaan hanya dipakai untuk penagihan dan pengiriman. Kami tidak pernah meminta kode OTP Anda.{!! $supportLink !!}</p>
            </div>
        </div>
    </section>
</div>