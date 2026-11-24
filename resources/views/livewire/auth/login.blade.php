@php
    $svg = fn($d, $c = 'w-5 h-5') => '<svg class="'.$c.'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="'.$d.'"/></svg>';
    $P = [
        'mail' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
        'lock' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z',
        'eye' => 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z',
        'eyeoff' => 'M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21',
        'arrow' => 'M14 5l7 7m0 0l-7 7m7-7H3',
        'alert' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
        'printer' => 'M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z',
        'box' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
        'shield' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
        'link' => 'M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1',
        'users' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
        'check' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        'building' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
    ];

    $iPrinter = $svg($P['printer'], 'w-6 h-6');
    $iMail = $svg($P['mail'], 'w-[18px] h-[18px]');
    $iLock = $svg($P['lock'], 'w-[18px] h-[18px]');
    $iEye = $svg($P['eye'], 'w-[18px] h-[18px]');
    $iEyeOff = $svg($P['eyeoff'], 'w-[18px] h-[18px]');
    $iArrow = $svg($P['arrow'], 'w-[18px] h-[18px]');
    $iAlert = $svg($P['alert'], 'w-4 h-4');
    $iAlertSm = $svg($P['alert'], 'w-3.5 h-3.5');
    $iShield = $svg($P['shield'], 'w-[18px] h-[18px]');
    $iCheck = $svg($P['check'], 'w-4 h-4');
    $iBuilding = $svg($P['building'], 'w-[18px] h-[18px]');
    $iBadge = $svg($P['building'], 'w-3.5 h-3.5');

    $regUrl = null;
    foreach (['register', 'customer.register', 'auth.register', 'signup', 'daftar', 'customer.signup'] as $n) {
        if (\Illuminate\Support\Facades\Route::has($n)) { $regUrl = route($n); break; }
    }
    if (!$regUrl) {
        foreach (\Illuminate\Support\Facades\Route::getRoutes()->getRoutes() as $r) {
            if (!in_array('GET', $r->methods(), true)) continue;
            $u = $r->uri();
            if (str_contains($u, '{')) continue;
            if (preg_match('/(^|\/)(register|daftar|signup)(\/|$)/i', $u)) { $regUrl = url($u); break; }
        }
    }

    $resUrl = null;
    foreach (['password.request', 'password.email', 'forgot-password', 'lupa-password'] as $n) {
        if (\Illuminate\Support\Facades\Route::has($n)) { $resUrl = route($n); break; }
    }

    $waAdmin = $support !== '' ? 'https://wa.me/'.preg_replace('/\D+/', '', $support) : null;

    $featHtml = '';
    foreach ([
        ['box', 'MRP & ROP otomatis', 'Bahan dipesan sebelum habis'],
        ['link', 'Invoice tanpa login', 'Klien bayar lewat tautan'],
        ['shield', 'Approval berjenjang', 'Produksi ajuan, owner setuju'],
        ['users', 'Akun perusahaan', 'Tim procurement satu pintu'],
    ] as $f) {
        $featHtml .= '<div class="rounded-2xl border border-white/12 bg-white/[.06] backdrop-blur-sm px-4 py-3.5">'
            . '<span class="inline-flex w-8 h-8 rounded-lg items-center justify-center mb-2" style="background:rgba(200,236,63,.16);color:var(--lime)">'.$svg($P[$f[0]], 'w-4 h-4').'</span>'
            . '<p class="text-[13px] font-extrabold leading-tight">'.e($f[1]).'</p>'
            . '<p class="text-[11px] text-white/55 mt-0.5 leading-snug">'.e($f[2]).'</p></div>';
    }

    $tags = ['MRP bahan baku','Invoice magic link','Approval berjenjang','Audit trail permanen','Batch FIFO','Multi-role akses','Cicilan / DP','Laporan delay'];
    $marquee = '';
    foreach (array_merge($tags, $tags) as $t) {
        $marquee .= '<span class="shrink-0 px-3 py-1.5 rounded-full border border-white/12 bg-white/[.05] text-[10.5px] font-bold text-white/60 whitespace-nowrap">'.e($t).'</span>';
    }

    $errEmail = $errors->first('email');
    $errPass = $errors->first('password');
    $clsEmail = $errEmail ? 'border-red-400' : 'border-ice-300';
    $clsPass = $errPass ? 'border-red-400' : 'border-ice-300';
    $errEmailHtml = $errEmail ? '<p class="mt-1.5 text-[12px] font-semibold text-red-600">'.e($errEmail).'</p>' : '';
    $errPassHtml = $errPass ? '<p class="mt-1.5 text-[12px] font-semibold text-red-600">'.e($errPass).'</p>' : '';

    $failureBox = $failure !== ''
        ? '<div class="ax-pop mt-5 rounded-2xl border-l-4 px-4 py-3 flex items-start gap-2.5" style="border-color:#ef4444;background:#fef2f2">'
            . '<span class="shrink-0 mt-0.5" style="color:#dc2626">'.$iAlert.'</span>'
            . '<p class="text-[13px] font-semibold leading-snug" style="color:#991b1b">'.e($failure).'</p></div>'
        : '';

    $okMsg = session('auth_success');
    $successBox = $okMsg
        ? '<div class="ax-pop mt-5 rounded-2xl border-l-4 px-4 py-3 flex items-start gap-2.5" style="border-color:#1d6247;background:rgba(200,236,63,.18)">'
            . '<span class="shrink-0 mt-0.5" style="color:var(--press-600)">'.$iCheck.'</span>'
            . '<p class="text-[13px] font-semibold leading-snug" style="color:var(--press-800)">'.e($okMsg).'</p></div>'
        : '';

    $resetLink = $resUrl
        ? '<a href="'.$resUrl.'" class="text-[12px] font-extrabold hover:underline underline-offset-2" style="color:var(--press-600)">Lupa sandi?</a>'
        : '';

    $registerBlock = $regUrl
        ? '<div class="ax-rise mt-6 flex items-center gap-3" style="animation-delay:.24s">'
            . '<span class="flex-1 h-px" style="background:var(--ice-300)"></span>'
            . '<span class="text-[11px] font-bold uppercase tracking-[.18em]" style="color:var(--ink-mute)">Belum punya akun</span>'
            . '<span class="flex-1 h-px" style="background:var(--ice-300)"></span></div>'
            . '<a href="'.$regUrl.'" class="ax-rise ax-btn mt-4 w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-2xl text-[14px] font-extrabold bg-white border-2 hover:bg-ice-50" style="animation-delay:.28s;border-color:var(--press-600);color:var(--press-700)">'
            . $iBuilding.' Daftarkan perusahaan</a>'
        : ($waAdmin
            ? '<div class="ax-rise mt-6 rounded-2xl border-2 border-dashed px-4 py-3.5 flex items-start gap-3" style="animation-delay:.24s;border-color:var(--ice-300)">'
                . '<span class="shrink-0 mt-0.5" style="color:var(--press-500)">'.$iBuilding.'</span>'
                . '<p class="text-[12.5px] leading-snug" style="color:var(--ink-soft)">Belum punya akun perusahaan? '
                . '<a href="'.$waAdmin.'" target="_blank" rel="noopener" class="font-extrabold hover:underline" style="color:var(--press-600)">Ajukan lewat admin</a> '
                . 'agar akun tim procurement Anda disiapkan.</p></div>'
            : '');

    $supportLink = $waAdmin
        ? ' Butuh bantuan? <a href="'.$waAdmin.'" target="_blank" rel="noopener" class="font-extrabold hover:underline" style="color:var(--press-600)">Hubungi admin</a>.'
        : '';
@endphp

<div class="min-h-screen w-full lg:grid lg:grid-cols-[1.06fr_1fr]"
     x-data="{ show:false, caps:false, spot:{x:50,y:40},
        move(e){ const r=e.currentTarget.getBoundingClientRect(); this.spot.x=((e.clientX-r.left)/r.width)*100; this.spot.y=((e.clientY-r.top)/r.height)*100; },
        checkCaps(e){ this.caps = e.getModifierState && e.getModifierState('CapsLock'); } }"
     @@auth-shake.window="const c=document.getElementById('auth-card'); c?.classList.add('ax-shake'); setTimeout(()=>c?.classList.remove('ax-shake'),450)">

    <section @mousemove="move($event)" class="ax-mesh ax-grain relative overflow-hidden min-h-[236px] lg:min-h-screen flex flex-col justify-between px-6 sm:px-10 lg:px-14 py-7 lg:py-12 text-white">
        <div class="ax-halftone ax-pan absolute inset-0 opacity-[.22] pointer-events-none"></div>
        <div class="ax-drift absolute -top-28 -left-24 w-[26rem] h-[26rem] rounded-full blur-3xl pointer-events-none" style="background:rgba(34,211,238,.20)"></div>
        <div class="ax-drift absolute -bottom-32 -right-20 w-[24rem] h-[24rem] rounded-full blur-3xl pointer-events-none" style="background:rgba(240,70,140,.18);animation-delay:-9s"></div>
        <div class="absolute inset-0 pointer-events-none hidden lg:block" :style="`background:radial-gradient(30rem 30rem at ${spot.x}% ${spot.y}%, rgba(200,236,63,.14), transparent 70%)`"></div>

        <svg class="ax-spin absolute top-6 right-6 w-14 h-14 opacity-40 hidden sm:block" viewBox="0 0 60 60" fill="none" aria-hidden="true">
            <circle cx="30" cy="30" r="17" stroke="#c8ec3f" stroke-width="1.2"/>
            <circle cx="30" cy="30" r="7" stroke="#22d3ee" stroke-width="1.2"/>
            <path d="M30 4v18M30 38v18M4 30h18M38 30h18" stroke="#f0468c" stroke-width="1.2"/>
        </svg>

        <div class="relative ax-rise flex items-center gap-3">
            <span class="shrink-0 w-11 h-11 rounded-2xl flex items-center justify-center shadow-lg" style="background:var(--lime);color:var(--press-900)">{!! $iPrinter !!}</span>
            <div class="min-w-0">
                <p class="text-base font-extrabold tracking-tight leading-none truncate">{{ $brand }}</p>
                <p class="text-[10px] font-bold uppercase tracking-[.24em] mt-1" style="color:var(--lime)">Print Operations Platform</p>
            </div>
        </div>

        <div class="relative hidden lg:block mt-10">
            <p class="ax-rise text-[11px] font-bold uppercase tracking-[.28em] text-white/55" style="animation-delay:.06s">Untuk perusahaan &amp; procurement</p>
            <h1 class="ax-rise mt-4 text-[2.7rem] xl:text-[3.35rem] font-extrabold leading-[1.04] tracking-tight max-w-[15ch]" style="animation-delay:.12s">Produksi cetak<br><span style="color:var(--lime)">terkendali penuh.</span></h1>
            <p class="ax-rise mt-5 text-[15px] leading-relaxed text-white/70 max-w-[42ch]" style="animation-delay:.2s">Dari purchase request bahan sampai invoice perusahaan — satu alur, satu jejak audit, tanpa spreadsheet terpisah.</p>

            <div class="ax-rise mt-8 grid grid-cols-2 gap-2.5 max-w-[30rem]" style="animation-delay:.28s">{!! $featHtml !!}</div>

            <div class="ax-rise relative mt-9 h-[168px] max-w-[27rem]" style="animation-delay:.36s" aria-hidden="true">
                <div class="ax-sheet absolute inset-x-6 top-4 h-[132px] rounded-2xl bg-white/10 border border-white/15" style="--rot:-5deg"></div>
                <div class="ax-sheet absolute inset-x-3 top-2 h-[140px] rounded-2xl bg-white/16 border border-white/20" style="--rot:-2deg;animation-delay:-2.4s"></div>
                <div class="ax-sheet absolute inset-x-0 top-0 h-[150px] rounded-2xl bg-white shadow-2xl overflow-hidden" style="--rot:1.4deg;animation-delay:-4.8s">
                    <div class="ax-sweep absolute inset-0 bg-gradient-to-r from-transparent via-white/60 to-transparent pointer-events-none"></div>
                    <div class="relative px-5 py-4" style="color:var(--ink)">
                        <div class="flex items-start justify-between gap-3 pb-2.5 border-b" style="border-color:var(--ice-300)">
                            <div>
                                <p class="text-[9px] font-bold uppercase tracking-[.2em]" style="color:var(--ink-mute)">Invoice Perusahaan</p>
                                <p class="mono text-[13px] font-bold mt-0.5">INV-20261123-7NTA</p>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase" style="background:rgba(200,236,63,.35);color:var(--press-800)">DP 50%</span>
                        </div>
                        <div class="mt-2.5 space-y-1.5 text-[11px]">
                            <div class="flex justify-between"><span style="color:var(--ink-soft)">Backdrop 3x2 m - 12 pcs</span><span class="mono font-bold">Rp 4.320.000</span></div>
                            <div class="flex justify-between"><span style="color:var(--ink-soft)">Kartu nama laminasi - 500</span><span class="mono font-bold">Rp 850.000</span></div>
                        </div>
                        <div class="mt-2.5 pt-2 border-t flex items-center justify-between" style="border-color:var(--ice-300)">
                            <span class="text-[10px] font-bold uppercase tracking-wider" style="color:var(--ink-mute)">Tagihan tahap 1</span>
                            <span class="mono text-[15px] font-extrabold" style="color:var(--press-700)">Rp 2.585.000</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="relative mt-6 lg:mt-8 overflow-hidden" aria-hidden="true">
            <div class="ax-marquee flex gap-2.5 w-max">{!! $marquee !!}</div>
        </div>
    </section>

    <section class="relative bg-ice-100 ax-grid-light flex items-center justify-center px-5 sm:px-10 py-9 lg:py-12">
        <div id="auth-card" class="w-full max-w-[26.5rem]">
            <div class="ax-rise" style="animation-delay:.08s">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white border border-ice-300 text-[10px] font-extrabold uppercase tracking-[.18em]" style="color:var(--press-600)">{!! $iBadge !!} Portal Klien &amp; Staf</span>
                <h2 class="mt-4 text-[1.9rem] sm:text-[2.15rem] font-extrabold tracking-tight leading-tight">Masuk ke akun</h2>
                <p class="mt-2 text-[14px]" style="color:var(--ink-soft)">Kelola pesanan, tagihan, dan persetujuan produksi Anda.</p>
            </div>

            {!! $failureBox !!}
            {!! $successBox !!}

            <form wire:submit="authenticate" class="ax-rise mt-6 space-y-4" style="animation-delay:.16s">
                <div>
                    <label for="lg-email" class="block text-[12.5px] font-extrabold mb-1.5">Email terdaftar</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none" style="color:var(--ink-mute)">{!! $iMail !!}</span>
                        <input id="lg-email" type="email" wire:model="email" autocomplete="username" inputmode="email" required placeholder="nama@perusahaan.co.id"
                               class="ax-field w-full pl-12 pr-4 py-3.5 text-[16px] rounded-2xl bg-white border {{ $clsEmail }}">
                    </div>
                    {!! $errEmailHtml !!}
                </div>

                <div>
                    <div class="flex items-center justify-between gap-3 mb-1.5">
                        <label for="lg-pass" class="block text-[12.5px] font-extrabold">Kata sandi</label>
                        {!! $resetLink !!}
                    </div>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none" style="color:var(--ink-mute)">{!! $iLock !!}</span>
                        <input id="lg-pass" :type="show ? 'text' : 'password'" wire:model="password" required autocomplete="current-password" placeholder="........"
                               @keydown="checkCaps($event)" @keyup="checkCaps($event)" @blur="caps=false"
                               class="ax-field w-full pl-12 pr-12 py-3.5 text-[16px] rounded-2xl bg-white border {{ $clsPass }}">
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-4 flex items-center" style="color:var(--ink-mute)">
                            <span x-show="!show">{!! $iEye !!}</span>
                            <span x-show="show" x-cloak style="color:var(--press-600)">{!! $iEyeOff !!}</span>
                        </button>
                    </div>
                    {!! $errPassHtml !!}
                    <p x-show="caps" x-cloak class="mt-1.5 text-[12px] font-bold flex items-center gap-1.5" style="color:#b45309">{!! $iAlertSm !!} Caps Lock aktif</p>
                </div>

                <label class="flex items-center gap-3 cursor-pointer select-none pt-0.5">
                    <span class="relative inline-flex shrink-0">
                        <input type="checkbox" wire:model="remember" class="peer sr-only">
                        <span class="w-11 h-6 rounded-full transition-colors peer-checked:bg-[color:var(--press-600)]" style="background:var(--ice-300)"></span>
                        <span class="absolute top-0.5 left-0.5 w-5 h-5 rounded-full bg-white shadow transition-transform peer-checked:translate-x-5"></span>
                    </span>
                    <span class="text-[13px] font-semibold" style="color:var(--ink-soft)">Tetap masuk di perangkat ini</span>
                </label>

                <button type="submit" wire:loading.attr="disabled" wire:target="authenticate"
                        class="ax-btn w-full inline-flex items-center justify-center gap-2 px-6 py-4 rounded-2xl text-[15px] font-extrabold shadow-lg disabled:opacity-70"
                        style="background:var(--press-700);color:var(--lime);box-shadow:0 12px 26px -12px rgba(10,63,51,.7)">
                    <span wire:loading.remove wire:target="authenticate" class="inline-flex items-center gap-2">Masuk {!! $iArrow !!}</span>
                    <span wire:loading wire:target="authenticate" class="inline-flex items-center gap-2">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-80" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.4 0 0 5.4 0 12h4z"/></svg>
                        Memverifikasi...
                    </span>
                </button>
            </form>

            {!! $registerBlock !!}

            <div class="ax-rise mt-7 rounded-2xl bg-white border border-ice-300 px-4 py-3.5 flex items-start gap-3" style="animation-delay:.32s">
                <span class="shrink-0 mt-0.5" style="color:var(--press-500)">{!! $iShield !!}</span>
                <p class="text-[12px] leading-snug" style="color:var(--ink-soft)">Setiap aktivitas tercatat pada jejak audit. Kami tidak pernah meminta kata sandi atau kode OTP Anda.{!! $supportLink !!}</p>
            </div>

            <p class="mt-6 text-center text-[11px]" style="color:var(--ink-mute)">{{ date('Y') }} {{ $brand }} - Print Operations Platform</p>
        </div>
    </section>
</div>