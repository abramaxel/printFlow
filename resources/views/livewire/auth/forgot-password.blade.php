@php
    $I = [
        'mail'=>'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
        'chat'=>'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z',
        'lock'=>'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z',
        'key'=>'M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z',
        'shield'=>'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
        'arrow'=>'M14 5l7 7m0 0l-7 7m7-7H3',
        'back'=>'M10 19l-7-7m0 0l7-7m-7 7h18',
        'check'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        'alert'=>'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
        'clock'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        'undo'=>'M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6',
        'printer'=>'M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z',
        'eye'=>'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z',
        'eyeoff'=>'M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21',
        'send'=>'M12 19l9 2-9-18-9 18 9-2zm0 0v-8',
    ];
    $ic = fn($k,$c='w-5 h-5') => '<svg class="'.$c.'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="'.($I[$k] ?? $I['lock']).'"/></svg>';
    $steps = ['identify'=>'Tujuan','verify'=>'Kode OTP','reset'=>'Sandi Baru','done'=>'Selesai'];
    $order = array_keys($steps);
    $idx = array_search($step, $order, true);
@endphp

<div data-ax-root><div class="min-h-screen w-full lg:grid lg:grid-cols-[.85fr_1fr]"
     x-data="{
        digits: Array({{ $digits }}).fill(''),
        left: 0, timer: null, show:false,
        start(s){ this.left = s; clearInterval(this.timer); this.timer = setInterval(()=>{ if(this.left>0) this.left--; else clearInterval(this.timer); }, 1000); },
        focusFirst(){ this.$nextTick(()=>{ const el=this.$refs.d0; if(el) el.focus(); }); },
        clearAll(){ this.digits = Array({{ $digits }}).fill(''); this.focusFirst(); },
        push(){ const v=this.digits.join(''); $wire.set('code', v); if(v.length==={{ $digits }}) $wire.verify(); },
        onInput(i,e){
            const raw=(e.target.value||'').replace(/\D/g,'');
            if(raw.length>1){ this.fill(raw,i); return; }
            this.digits[i]=raw;
            e.target.value=raw;
            if(raw && i<{{ $digits-1 }}) this.$refs['d'+(i+1)].focus();
            this.push();
        },
        onKey(i,e){
            if(e.key==='Backspace' && !this.digits[i] && i>0){ this.$refs['d'+(i-1)].focus(); this.digits[i-1]=''; this.$refs['d'+(i-1)].value=''; e.preventDefault(); this.push(); }
            if(e.key==='ArrowLeft' && i>0){ this.$refs['d'+(i-1)].focus(); e.preventDefault(); }
            if(e.key==='ArrowRight' && i<{{ $digits-1 }}){ this.$refs['d'+(i+1)].focus(); e.preventDefault(); }
        },
        fill(text,from=0){
            const clean=(text||'').replace(/\D/g,'').slice(0,{{ $digits }});
            for(let k=0;k<clean.length && from+k<{{ $digits }};k++){ this.digits[from+k]=clean[k]; const el=this.$refs['d'+(from+k)]; if(el) el.value=clean[k]; }
            const next=Math.min(from+clean.length, {{ $digits-1 }});
            this.$refs['d'+next]?.focus();
            this.push();
        },
        onPaste(e){ e.preventDefault(); this.fill((e.clipboardData||window.clipboardData).getData('text'),0); },
        get strength(){ const p=this.pw||''; let s=0; if(p.length>=8)s++; if(/[a-z]/.test(p)&&/[A-Z]/.test(p))s++; if(/\d/.test(p))s++; if(/[^A-Za-z0-9]/.test(p))s++; return s; },
        pw:'', pw2:'',
        get match(){ return this.pw2.length>0 && this.pw===this.pw2; }
     }"
     @otp-sent.window="start($event.detail.seconds ?? {{ (int) config('otp.resend_cooldown',60) }}); focusFirst()"
     @otp-focus.window="focusFirst()"
     @otp-clear.window="clearAll()"
     @reset-focus.window="$nextTick(()=>document.getElementById('rs-pw')?.focus())"
     @@auth-shake.window="$el.querySelector('#fp-card')?.classList.add('ax-shake'); setTimeout(()=>$el.querySelector('#fp-card')?.classList.remove('ax-shake'),450)">

    {{-- PANEL SAMPING --}}
    <section class="ax-mesh ax-grain relative overflow-hidden min-h-[168px] lg:min-h-screen flex flex-col justify-between px-6 sm:px-10 lg:px-12 py-7 lg:py-12 text-white">
        <div class="ax-halftone ax-pan absolute inset-0 opacity-[.2] pointer-events-none"></div>
        <div class="ax-drift absolute -top-24 -right-20 w-[22rem] h-[22rem] rounded-full blur-3xl pointer-events-none" style="background:rgba(34,211,238,.2)"></div>
        <div class="ax-drift absolute -bottom-28 -left-16 w-[20rem] h-[20rem] rounded-full blur-3xl pointer-events-none" style="background:rgba(200,236,63,.18); animation-delay:-8s"></div>

        <div class="relative ax-rise flex items-center gap-3">
            <span class="shrink-0 w-11 h-11 rounded-2xl flex items-center justify-center shadow-lg" style="background:var(--lime); color:var(--press-900)">{!! $ic('printer','w-6 h-6') !!}</span>
            <div class="min-w-0">
                <p class="text-base font-extrabold tracking-tight leading-none truncate">{{ $brand }}</p>
                <p class="text-[10px] font-bold uppercase tracking-[.24em] mt-1" style="color:var(--lime)">Pemulihan Akun</p>
            </div>
        </div>

        <div class="relative hidden lg:block">
            <h1 class="ax-rise text-[2.3rem] xl:text-[2.8rem] font-extrabold leading-[1.06] tracking-tight max-w-[13ch]" style="animation-delay:.1s">
                Kembali masuk<br><span style="color:var(--lime)">dengan aman.</span>
            </h1>
            <p class="ax-rise mt-4 text-[14.5px] leading-relaxed text-white/70 max-w-[36ch]" style="animation-delay:.18s">
                Kode sekali pakai dikirim ke kontak terdaftar. Tidak ada tautan yang bisa diteruskan ke orang lain.
            </p>

            <div class="ax-rise mt-8 space-y-2.5" style="animation-delay:.26s">
                @foreach([
                    ['shield','Kode di-hash di server','Tidak pernah disimpan sebagai teks'],
                    ['clock','Berlaku ' . $ttlMinutes . ' menit','Otomatis kedaluwarsa setelah itu'],
                    ['lock','Maksimal 5 percobaan','Kode hangus bila salah berulang'],
                ] as $f)
                    <div class="flex items-start gap-3 rounded-2xl border border-white/12 bg-white/[.06] px-4 py-3">
                        <span class="shrink-0 w-8 h-8 rounded-lg flex items-center justify-center" style="background:rgba(200,236,63,.16); color:var(--lime)">{!! $ic($f[0],'w-4 h-4') !!}</span>
                        <div class="min-w-0">
                            <p class="text-[13px] font-extrabold leading-tight">{{ $f[1] }}</p>
                            <p class="text-[11px] text-white/55 mt-0.5">{{ $f[2] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <a href="{{ route('login') }}" class="relative ax-rise inline-flex items-center gap-2 self-start px-4 py-2.5 rounded-xl border border-white/15 bg-white/[.07] text-[12.5px] font-bold hover:bg-white/15 transition-colors" style="animation-delay:.3s">
            {!! $ic('back','w-4 h-4') !!} Kembali ke halaman masuk
        </a>
    </section>

    {{-- PANEL FORM --}}
    <section class="relative bg-ice-100 ax-grid-light flex items-center justify-center px-5 sm:px-10 py-9 lg:py-12">
        <div id="fp-card" class="w-full max-w-[27rem]">

            {{-- stepper --}}
            <div class="ax-rise flex items-center gap-1.5 mb-6">
                @foreach($order as $i => $key)
                    <div class="flex items-center gap-1.5 {{ $i < count($order)-1 ? 'flex-1' : '' }}">
                        <span class="shrink-0 inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider transition-colors"
                              style="{{ $i <= $idx ? 'background:var(--press-700); color:var(--lime)' : 'background:#fff; color:var(--ink-mute); border:1px solid var(--ice-300)' }}">
                            @if($i < $idx){!! $ic('check','w-3 h-3') !!}@else<span class="mono">{{ $i+1 }}</span>@endif
                            <span class="hidden sm:inline">{{ $steps[$key] }}</span>
                        </span>
                        @if($i < count($order)-1)
                            <span class="flex-1 h-0.5 rounded-full" style="background:{{ $i < $idx ? 'var(--press-500)' : 'var(--ice-300)' }}"></span>
                        @endif
                    </div>
                @endforeach
            </div>

            @if($failure !== '')
                <div class="ax-pop mb-4 rounded-2xl border-l-4 px-4 py-3 flex items-start gap-2.5" style="border-color:#ef4444; background:#fef2f2">
                    <span class="shrink-0 mt-0.5" style="color:#dc2626">{!! $ic('alert','w-4 h-4') !!}</span>
                    <p class="text-[13px] font-semibold leading-snug" style="color:#991b1b">{{ $failure }}</p>
                </div>
            @endif
            @if($notice !== '')
                <div class="ax-pop mb-4 rounded-2xl border-l-4 px-4 py-3 flex items-start gap-2.5" style="border-color:#f59e0b; background:#fffbeb">
                    <span class="shrink-0 mt-0.5" style="color:#b45309">{!! $ic('alert','w-4 h-4') !!}</span>
                    <p class="text-[13px] font-semibold leading-snug" style="color:#92400e">{{ $notice }}</p>
                </div>
            @endif

            {{-- STEP 1 --}}
            @if($step === 'identify')
                <div class="ax-rise">
                    <h2 class="text-[1.75rem] font-extrabold tracking-tight leading-tight">Lupa kata sandi?</h2>
                    <p class="mt-2 text-[14px]" style="color:var(--ink-soft)">Masukkan email atau nomor WhatsApp terdaftar. Kami kirim kode {{ $digits }} digit.</p>

                    <form wire:submit="sendCode" class="mt-6 space-y-4">
                        <div>
                            <label for="fp-id" class="block text-[12.5px] font-extrabold mb-1.5">Email atau nomor WhatsApp</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none" style="color:var(--ink-mute)">{!! $ic('mail','w-[18px] h-[18px]') !!}</span>
                                <input id="fp-id" type="text" wire:model="identifier" required autofocus
                                       placeholder="nama@perusahaan.co.id atau 0812xxxx"
                                       class="ax-field w-full pl-12 pr-4 py-3.5 text-[16px] rounded-2xl bg-white border border-ice-300">
                            </div>
                        </div>

                        <div>
                            <span class="block text-[12.5px] font-extrabold mb-2">Kirim kode melalui</span>
                            <div class="grid grid-cols-2 gap-2.5">
                                <button type="button" wire:click="setChannel('email')"
                                        class="ax-btn flex flex-col items-center gap-1.5 px-3 py-3.5 rounded-2xl border-2 bg-white text-center"
                                        style="{{ $channel === 'email' ? 'border-color:var(--press-600); background:rgba(200,236,63,.14)' : 'border-color:var(--ice-300)' }}">
                                    <span style="color:{{ $channel === 'email' ? 'var(--press-700)' : 'var(--ink-mute)' }}">{!! $ic('mail','w-5 h-5') !!}</span>
                                    <span class="text-[12px] font-extrabold" style="color:{{ $channel === 'email' ? 'var(--press-800)' : 'var(--ink-soft)' }}">Email</span>
                                </button>
                                <button type="button" @if($waReady) wire:click="setChannel('whatsapp')" @endif
                                        class="ax-btn relative flex flex-col items-center gap-1.5 px-3 py-3.5 rounded-2xl border-2 bg-white text-center {{ $waReady ? '' : 'opacity-55 cursor-not-allowed' }}"
                                        style="{{ $channel === 'whatsapp' ? 'border-color:var(--press-600); background:rgba(200,236,63,.14)' : 'border-color:var(--ice-300)' }}">
                                    <span style="color:{{ $channel === 'whatsapp' ? 'var(--press-700)' : 'var(--ink-mute)' }}">{!! $ic('chat','w-5 h-5') !!}</span>
                                    <span class="text-[12px] font-extrabold" style="color:{{ $channel === 'whatsapp' ? 'var(--press-800)' : 'var(--ink-soft)' }}">WhatsApp</span>
                                    @unless($waReady)
                                        <span class="absolute -top-2 right-2 px-1.5 py-0.5 rounded text-[8.5px] font-extrabold uppercase" style="background:var(--ice-300); color:var(--ink-soft)">Nonaktif</span>
                                    @endunless
                                </button>
                            </div>
                            @unless($waReady)
                                <p class="mt-2 text-[11.5px] leading-snug" style="color:var(--ink-mute)">
                                    Kanal WhatsApp aktif setelah gateway diisi pada <code class="mono">.env</code> (<code class="mono">OTP_WHATSAPP_ENABLED</code> &amp; <code class="mono">FONNTE_TOKEN</code>). Sementara kode dikirim via email.
                                </p>
                            @endunless
                        </div>

                        <button type="submit" wire:loading.attr="disabled" wire:target="sendCode"
                                class="ax-btn w-full inline-flex items-center justify-center gap-2 px-6 py-4 rounded-2xl text-[15px] font-extrabold shadow-lg disabled:opacity-70"
                                style="background:var(--press-700); color:var(--lime); box-shadow:0 12px 26px -12px rgba(10,63,51,.7)">
                            <span wire:loading.remove wire:target="sendCode" class="inline-flex items-center gap-2">{!! $ic('send','w-[18px] h-[18px]') !!} Kirim Kode</span>
                            <span wire:loading wire:target="sendCode" class="inline-flex items-center gap-2">
                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-80" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.4 0 0 5.4 0 12h4z"/></svg>
                                Mengirim…
                            </span>
                        </button>
                    </form>
                </div>
            @endif

            {{-- STEP 2 --}}
            @if($step === 'verify')
                <div class="ax-rise">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white border border-ice-300 text-[10px] font-extrabold uppercase tracking-[.16em]" style="color:var(--press-600)">
                        {!! $ic($channel === 'whatsapp' ? 'chat' : 'mail','w-3.5 h-3.5') !!} Terkirim via {{ $channel === 'whatsapp' ? 'WhatsApp' : 'Email' }}
                    </span>
                    <h2 class="mt-3 text-[1.75rem] font-extrabold tracking-tight leading-tight">Masukkan kode</h2>
                    <p class="mt-2 text-[14px]" style="color:var(--ink-soft)">
                        Dikirim ke <b class="mono" style="color:var(--press-700)">{{ $masked }}</b>. Berlaku {{ $ttlMinutes }} menit.
                    </p>

                    <div class="mt-6" @paste="onPaste($event)">
                        <div class="flex gap-2 sm:gap-2.5">
                            @for($i = 0; $i < $digits; $i++)
                                <input x-ref="d{{ $i }}" type="text" inputmode="numeric" autocomplete="{{ $i === 0 ? 'one-time-code' : 'off' }}"
                                       maxlength="{{ $i === 0 ? 6 : 1 }}" aria-label="Digit {{ $i+1 }}"
                                       @input="onInput({{ $i }},$event)" @keydown="onKey({{ $i }},$event)"
                                       @focus="$event.target.select()"
                                       class="ax-otp ax-field mono flex-1 min-w-0 h-16 sm:h-[4.5rem] text-center text-[26px] sm:text-[30px] font-bold rounded-2xl bg-white border-2 border-ice-300"
                                       style="color:var(--press-800)">
                            @endfor
                        </div>
                        <div class="mt-3 flex items-center justify-between gap-3 text-[12px]">
                            <span style="color:var(--ink-mute)">Sisa percobaan: <b class="mono" style="color:var(--press-700)">{{ $attemptsLeft }}</b></span>
                            <button type="button" @click="clearAll()" class="font-bold hover:underline" style="color:var(--press-600)">Bersihkan</button>
                        </div>
                    </div>

                    <button type="button" wire:click="verify" wire:loading.attr="disabled" wire:target="verify"
                            class="ax-btn mt-5 w-full inline-flex items-center justify-center gap-2 px-6 py-4 rounded-2xl text-[15px] font-extrabold shadow-lg disabled:opacity-70"
                            style="background:var(--press-700); color:var(--lime); box-shadow:0 12px 26px -12px rgba(10,63,51,.7)">
                        <span wire:loading.remove wire:target="verify" class="inline-flex items-center gap-2">{!! $ic('shield','w-[18px] h-[18px]') !!} Verifikasi Kode</span>
                        <span wire:loading wire:target="verify">Memeriksa…</span>
                    </button>

                    <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                        <button type="button" wire:click="resend" x-show="left === 0"
                                class="inline-flex items-center gap-1.5 text-[13px] font-extrabold hover:underline" style="color:var(--press-600)">
                            {!! $ic('undo','w-4 h-4') !!} Kirim ulang kode
                        </button>
                        <span x-show="left > 0" class="inline-flex items-center gap-1.5 text-[13px] font-semibold" style="color:var(--ink-mute)">
                            {!! $ic('clock','w-4 h-4') !!} Kirim ulang dalam <b class="mono" x-text="left + 's'"></b>
                        </span>
                        <button type="button" wire:click="backToIdentify" class="text-[13px] font-bold hover:underline" style="color:var(--ink-soft)">Ganti tujuan</button>
                    </div>
                </div>
            @endif

            {{-- STEP 3 --}}
            @if($step === 'reset')
                <div class="ax-rise">
                    <h2 class="text-[1.75rem] font-extrabold tracking-tight leading-tight">Buat sandi baru</h2>
                    <p class="mt-2 text-[14px]" style="color:var(--ink-soft)">Minimal 8 karakter, mengandung huruf dan angka.</p>

                    <form wire:submit="savePassword" class="mt-6 space-y-4">
                        <div>
                            <label for="rs-pw" class="block text-[12.5px] font-extrabold mb-1.5">Kata sandi baru</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none" style="color:var(--ink-mute)">{!! $ic('lock','w-[18px] h-[18px]') !!}</span>
                                <input id="rs-pw" :type="show ? 'text' : 'password'" wire:model="password" x-model="pw"
                                       autocomplete="new-password" required
                                       class="ax-field w-full pl-12 pr-12 py-3.5 text-[16px] rounded-2xl bg-white border @error('password') border-red-400 @else border-ice-300 @enderror">
                                <button type="button" @click="show=!show" class="absolute inset-y-0 right-0 pr-4 flex items-center" style="color:var(--ink-mute)">
                                    <span x-show="!show">{!! $ic('eye','w-[18px] h-[18px]') !!}</span>
                                    <span x-show="show" x-cloak style="color:var(--press-600)">{!! $ic('eyeoff','w-[18px] h-[18px]') !!}</span>
                                </button>
                            </div>
                            @error('password') <p class="mt-1.5 text-[12px] font-semibold text-red-600">{{ $message }}</p> @enderror
                            <div x-show="pw.length>0" x-cloak class="mt-2">
                                <div class="flex gap-1">
                                    <template x-for="i in 4" :key="i">
                                        <span class="flex-1 h-1.5 rounded-full transition-colors"
                                              :style="`background:${ i<=strength ? (strength<=1?'#ef4444':strength===2?'#f59e0b':strength===3?'#1d6247':'#c8ec3f') : 'var(--ice-300)' }`"></span>
                                    </template>
                                </div>
                                <p class="mt-1 text-[11.5px] font-bold" :style="`color:${strength<=1?'#dc2626':strength===2?'#b45309':'var(--press-600)'}`"
                                   x-text="['Terlalu lemah','Lemah','Cukup','Kuat','Sangat kuat'][strength]"></p>
                            </div>
                        </div>

                        <div>
                            <label for="rs-pw2" class="block text-[12.5px] font-extrabold mb-1.5">Ulangi kata sandi</label>
                            <input id="rs-pw2" :type="show ? 'text' : 'password'" wire:model="password_confirmation" x-model="pw2"
                                   autocomplete="new-password" required
                                   class="ax-field w-full px-4 py-3.5 text-[16px] rounded-2xl bg-white border border-ice-300">
                            <p x-show="pw2.length>0" x-cloak class="mt-1.5 text-[12px] font-bold"
                               :style="`color:${match?'var(--press-600)':'#dc2626'}`"
                               x-text="match ? 'Sandi cocok' : 'Belum cocok'"></p>
                        </div>

                        <button type="submit" wire:loading.attr="disabled" wire:target="savePassword"
                                class="ax-btn w-full inline-flex items-center justify-center gap-2 px-6 py-4 rounded-2xl text-[15px] font-extrabold shadow-lg disabled:opacity-70"
                                style="background:var(--press-700); color:var(--lime); box-shadow:0 12px 26px -12px rgba(10,63,51,.7)">
                            <span wire:loading.remove wire:target="savePassword" class="inline-flex items-center gap-2">{!! $ic('key','w-[18px] h-[18px]') !!} Simpan Sandi Baru</span>
                            <span wire:loading wire:target="savePassword">Menyimpan…</span>
                        </button>
                    </form>
                </div>
            @endif

            {{-- STEP 4 --}}
            @if($step === 'done')
                <div class="ax-rise text-center">
                    <span class="inline-flex w-20 h-20 rounded-3xl items-center justify-center mx-auto shadow-lg" style="background:var(--lime); color:var(--press-900)">{!! $ic('check','w-10 h-10') !!}</span>
                    <h2 class="mt-5 text-[1.85rem] font-extrabold tracking-tight">Sandi diperbarui</h2>
                    <p class="mt-2 text-[14px] max-w-[34ch] mx-auto" style="color:var(--ink-soft)">
                        Semua kode pemulihan sudah dihanguskan. Silakan masuk memakai kata sandi baru Anda.
                    </p>
                    <button type="button" wire:click="finish"
                            class="ax-btn mt-7 w-full inline-flex items-center justify-center gap-2 px-6 py-4 rounded-2xl text-[15px] font-extrabold shadow-lg"
                            style="background:var(--press-700); color:var(--lime); box-shadow:0 12px 26px -12px rgba(10,63,51,.7)">
                        Lanjut ke halaman masuk {!! $ic('arrow','w-[18px] h-[18px]') !!}
                    </button>
                </div>
            @endif

            <div class="ax-rise mt-7 rounded-2xl bg-white border border-ice-300 px-4 py-3.5 flex items-start gap-3" style="animation-delay:.2s">
                <span class="shrink-0 mt-0.5" style="color:var(--press-500)">{!! $ic('shield','w-[18px] h-[18px]') !!}</span>
                <p class="text-[12px] leading-snug" style="color:var(--ink-soft)">
                    Tim {{ $brand }} tidak pernah meminta kode OTP Anda.
                    @if($support !== '')
                        Mencurigakan? <a href="https://wa.me/{{ preg_replace('/\D+/','',$support) }}" target="_blank" rel="noopener" class="font-extrabold hover:underline" style="color:var(--press-600)">Lapor admin</a>.
                    @endif
                </p>
            </div>
        </div>
    </section>
</div>
</div>
