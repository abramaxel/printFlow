@php
    $idx = array_search($tier, $ladder);
    $idx = $idx === false ? -1 : $idx;
    $pct = $idx < 0 ? 0 : round($idx / (count($ladder) - 1) * 100);
    $icons = [
        'regular' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
        'silver'  => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.783-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z',
        'gold'    => 'M3 7l4 4 5-6 5 6 4-4v10H3V7z',
        'vip'     => 'M12 2l7 7-7 13L5 9l7-7z',
    ];
@endphp

<div class="relative min-h-screen bg-ice-100 overflow-hidden">
    <style>
        @keyframes pfAurora { 0%,100% { transform: translate3d(0,0,0) scale(1); } 50% { transform: translate3d(34px,-22px,0) scale(1.08); } }
        .pf-aurora { animation: pfAurora 14s ease-in-out infinite; }
        .pf-aurora-slow { animation: pfAurora 22s ease-in-out infinite reverse; }
        @keyframes pfShine { 0% { transform: translateX(-130%) skewX(-12deg); } 55%,100% { transform: translateX(130%) skewX(-12deg); } }
        .pf-shine { animation: pfShine 6s ease-in-out infinite; }
        @keyframes pfRise { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .pf-rise { animation: pfRise .7s cubic-bezier(.2,.7,.2,1) both; }
        .pf-rise-delay { animation: pfRise .7s .15s cubic-bezier(.2,.7,.2,1) both; }
        @keyframes pfPulse { 0%,100% { box-shadow: 0 0 0 0 rgba(200,236,63,.45); } 50% { box-shadow: 0 0 0 14px rgba(200,236,63,0); } }
        .pf-pulse { animation: pfPulse 2.4s ease-out infinite; }
    </style>

    {{-- ============ HERO IMERSIF: HIJAU DARK + PATTERN LIME ============ --}}
    <section class="relative overflow-hidden bg-dark-950 text-white">
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_left,_var(--tw-gradient-stops))] from-emerald-800/70 via-dark-900 to-dark-950"></div>
        <div class="absolute inset-0 opacity-[0.10]" style="background-image: radial-gradient(#c8ec3f 1px, transparent 1px); background-size: 22px 22px;"></div>
        <div class="pf-aurora absolute -top-32 -left-24 w-[34rem] h-[34rem] rounded-full bg-emerald-500/20 blur-3xl"></div>
        <div class="pf-aurora-slow absolute -bottom-40 right-0 w-[30rem] h-[30rem] rounded-full bg-signal-400/10 blur-3xl"></div>
        <div class="absolute inset-y-0 right-1/4 w-40 rotate-12 bg-gradient-to-b from-signal-400/0 via-signal-400/10 to-signal-400/0 blur-2xl"></div>
        <div class="absolute inset-0 opacity-[0.05] mix-blend-overlay" style="background-image: url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='160' height='160'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='2'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.6'/%3E%3C/svg%3E&quot;);"></div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-10 pb-28 lg:pt-16 lg:pb-36">
            <div class="flex flex-col lg:flex-row lg:items-center gap-12">

                {{-- Identitas --}}
                <div class="flex-1 min-w-0 pf-rise">
                    <a href="{{ route('customer.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/5 border border-white/10 text-sm font-bold text-emerald-100/80 hover:bg-white/10 hover:text-signal-300 transition-colors">← Dashboard</a>
                    <p class="mt-8 inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/5 border border-signal-400/30 text-signal-300 text-xs font-bold uppercase tracking-[0.25em]">Kartu Member Digital</p>
                    <h1 class="mt-4 text-4xl sm:text-5xl font-extrabold tracking-tight leading-tight">{{ $user->name }}</h1>
                    <div class="mt-4 flex flex-wrap items-center gap-3">
                        <span class="text-base text-emerald-100/80 break-all">{{ $user->email }}</span>
                        <button onclick="copyText('{{ $user->email }}', this)" class="px-3 py-1.5 rounded-lg bg-white/5 border border-white/10 text-sm font-bold text-signal-300 hover:bg-signal-400 hover:text-dark-950 transition-colors">⧉ Salin</button>
                    </div>
                    <div class="mt-7 flex flex-wrap items-center gap-3">
                        <span class="px-5 py-2 rounded-full bg-signal-400 text-dark-950 text-sm font-extrabold uppercase tracking-widest shadow-lg shadow-signal-400/30">{{ $current['label'] }}</span>
                        <span class="text-sm text-emerald-100/60">{{ $current['tag'] }} • tier aktif dari database</span>
                    </div>
                </div>

                {{-- Kartu member 3D --}}
                <div class="lg:w-[26rem] shrink-0 pf-rise-delay"
                     x-data="{ rx: 0, ry: 0 }"
                     @mousemove="const r = $el.getBoundingClientRect(); ry = ((event.clientX - r.left) / r.width - .5) * 12; rx = -((event.clientY - r.top) / r.height - .5) * 12;"
                     @mouseleave="rx = 0; ry = 0;">
                    <div class="relative" :style="`transform: perspective(1100px) rotateX(${rx}deg) rotateY(${ry}deg)`" style="transition: transform .15s ease-out;">
                        <div class="relative overflow-hidden rounded-[2rem] border border-signal-400/25 bg-gradient-to-br from-emerald-900 via-dark-900 to-dark-950 p-6 sm:p-7 shadow-2xl shadow-emerald-950/60">
                            <div class="absolute -right-10 -top-10 w-44 h-44 opacity-20" style="background: repeating-linear-gradient(45deg, #c8ec3f 0 6px, transparent 6px 14px);"></div>
                            <div class="absolute -left-16 -bottom-16 w-48 h-48 rounded-full bg-signal-400/10 blur-2xl"></div>
                            <div class="pf-shine absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent pointer-events-none"></div>

                            <div class="relative flex items-center justify-between">
                                <span class="text-xs font-bold uppercase tracking-[0.25em] text-emerald-100/70">{{ config('app.brand.name', 'FC Murni Jaya') }}</span>
                                <span class="w-11 h-8 rounded-md bg-gradient-to-br from-signal-300 to-signal-500 shadow-inner"></span>
                            </div>

                            <div class="relative mt-9 flex items-center gap-4">
                                <div class="relative shrink-0">
                                    <span class="absolute inset-0 rounded-full ring-2 ring-signal-400/40 animate-ping"></span>
                                    <x-avatar :user="$user" size="lg" shape="rounded-full" class="relative shadow-lg shadow-signal-400/30" />
                                </div>
                                <div class="min-w-0">
                                    <p class="text-lg font-extrabold truncate">{{ $user->name }}</p>
                                    <p class="text-sm text-emerald-100/70 truncate">{{ $user->email }}</p>
                                </div>
                            </div>

                            <div class="relative mt-9 flex items-end justify-between">
                                <div>
                                    <p class="text-[10px] uppercase tracking-[0.3em] text-emerald-100/50">Tier Membership</p>
                                    <p class="text-3xl font-extrabold text-signal-300 tracking-tight">{{ $current['label'] }}</p>
                                </div>
                                <div class="flex gap-1.5 pb-1">
                                    @foreach($ladder as $i => $key)
                                        <span class="w-2.5 h-2.5 rounded-full {{ $i <= $idx ? 'bg-signal-400' : 'bg-white/15' }}"></span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Transisi gelombang ke area terang --}}
        <svg class="absolute bottom-0 left-0 w-full h-16 text-ice-100" viewBox="0 0 1440 90" fill="currentColor" preserveAspectRatio="none"><path d="M0,60 C240,90 480,20 720,45 C960,70 1200,30 1440,55 L1440,90 L0,90 Z"/></svg>
    </section>

    {{-- ============ TANGGA TIER + KEUNTUNGAN ============ --}}
    <section class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 pb-16">
        <div class="relative rounded-[2rem] bg-white/75 backdrop-blur-xl border border-ice-200 shadow-xl shadow-brand-900/5 p-6 sm:p-10 overflow-hidden">
            <div class="absolute -top-24 -right-24 w-72 h-72 rounded-full bg-signal-400/10 blur-3xl"></div>

            <div class="relative">
                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                    <div>
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-brand-950 tracking-tight">Perjalanan Tier Anda</h2>
                        <p class="text-base text-gray-500 mt-1">Semakin sering bertransaksi, semakin tinggi tier & keuntungannya.</p>
                    </div>
                    <a href="{{ route('customer.settings') }}" class="self-start sm:self-auto inline-flex items-center gap-2 px-5 py-3 rounded-full bg-gradient-to-r from-signal-400 to-signal-500 text-dark-950 text-base font-extrabold shadow-md shadow-signal-400/30 hover:shadow-lg hover:shadow-signal-400/40 hover:-translate-y-0.5 transition-all">Kelola di Pengaturan →</a>
                </div>

                @if($tier === 'inactive')
                    <div class="mt-6 rounded-2xl border-2 border-red-300 bg-red-50 px-5 py-4 text-base font-bold text-red-800">⚠️ Akun Anda ditandai non-aktif. Hubungi admin untuk aktivasi kembali.</div>
                @endif

                {{-- Tangga tier menyala --}}
                <div class="mt-12 relative">
                    <div class="absolute left-6 right-6 top-6 h-1.5 rounded-full bg-ice-200"></div>
                    <div class="absolute left-6 top-6 h-1.5 rounded-full bg-gradient-to-r from-emerald-500 to-signal-400 shadow-[0_0_12px_rgba(200,236,63,.6)]" style="width: calc({{ $pct }}% - {{ $pct > 0 ? 3 : 0 }}rem);"></div>
                    <div class="relative grid grid-cols-4 gap-2">
                        @foreach($ladder as $i => $key)
                            @php $t = $tiers[$key]; $active = $key === $tier; $passed = $i <= $idx; @endphp
                            <div class="flex flex-col items-center text-center">
                                <span class="w-12 h-12 rounded-full flex items-center justify-center border-2 transition-all {{ $active ? 'pf-pulse bg-signal-400 border-signal-400 text-dark-950' : ($passed ? 'bg-emerald-600 border-emerald-600 text-white' : 'bg-white border-ice-300 text-gray-400') }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icons[$key] }}"/></svg>
                                </span>
                                <p class="mt-3 text-sm font-extrabold {{ $active ? 'text-brand-950' : 'text-gray-500' }}">{{ $t['label'] }}</p>
                                @if($active)
                                    <span class="mt-1 px-2.5 py-0.5 rounded-full bg-signal-100 text-signal-700 text-[10px] font-extrabold uppercase tracking-wider">Saat ini</span>
                                @else
                                    <span class="mt-1 text-[10px] font-bold text-gray-400 uppercase tracking-wider">{{ $t['tag'] }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Keuntungan tier aktif --}}
                <div class="mt-12 grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2 relative rounded-3xl bg-gradient-to-br from-emerald-900 to-dark-950 text-white p-6 sm:p-8 overflow-hidden">
                        <div class="absolute inset-0 opacity-[0.08]" style="background-image: radial-gradient(#c8ec3f 1px, transparent 1px); background-size: 18px 18px;"></div>
                        <div class="absolute -top-16 -right-16 w-48 h-48 rounded-full bg-signal-400/15 blur-3xl"></div>
                        <div class="relative">
                            <h3 class="text-xl font-extrabold text-signal-300">Keuntungan Tier {{ $current['label'] }}</h3>
                            <ul class="mt-5 space-y-3.5">
                                @foreach($current['benefits'] as $b)
                                    <li class="flex gap-3 items-start">
                                        <span class="mt-0.5 shrink-0 w-6 h-6 rounded-full bg-signal-400/20 text-signal-300 flex items-center justify-center text-xs font-extrabold">✓</span>
                                        <span class="text-base text-emerald-50/90">{{ $b }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <div class="rounded-3xl bg-white border border-ice-200 p-6 sm:p-8 flex flex-col justify-between shadow-sm">
                        <div>
                            <p class="text-sm font-bold uppercase tracking-wider text-gray-400">Status Membership</p>
                            <p class="mt-3 text-4xl font-extrabold text-brand-950 tracking-tight">{{ $current['label'] }}</p>
                            <p class="mt-1 text-base text-gray-500">{{ $current['tag'] }}</p>
                        </div>
                        <a href="{{ route('customer.history') }}" class="mt-8 inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-brand-600 text-white text-base font-bold hover:bg-brand-700 transition-colors shadow-sm">Lihat Riwayat Pesanan →</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>