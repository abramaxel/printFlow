@php
    $B = [
        'layers' => 'M12 2l9 5-9 5-9-5 9-5z M3 12l9 5 9-5 M3 17l9 5 9-5',
        'box'    => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
        'check'  => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        'alert'  => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
        'x'      => 'M6 18L18 6M6 6l12 12',
        'plus'   => 'M12 4v16m8-8H4',
        'save'   => 'M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4',
        'search' => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z',
        'up'     => 'M5 15l7-7 7 7',
        'down'   => 'M19 9l-7 7-7-7',
        'copy'   => 'M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z',
        'trash'  => 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16',
        'calc'   => 'M9 7h6m-6 4h6m-6 4h3M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z',
        'money'  => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'printer'=> 'M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z',
        'undo'   => 'M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6',
        'tag'    => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z',
        'bolt'   => 'M13 10V3L4 14h7v7l9-11h-7z',
        'note'   => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'arrow'  => 'M14 5l7 7m0 0l-7 7m7-7H3',
        'flask'  => 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z',
    ];
    $bic = fn($k, $c = 'w-5 h-5') => '<svg class="' . $c . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="' . ($B[$k] ?? $B['box']) . '"/></svg>';
@endphp

<div class="w-full space-y-6"
     x-data="{
        goEditor() {
            const el = document.getElementById('editor-resep');
            if (!el) return;
            window.scrollTo({ top: Math.max(0, el.getBoundingClientRect().top + window.scrollY - 108), behavior: 'smooth' });
            el.classList.remove('bm-focus'); void el.offsetWidth; el.classList.add('bm-focus');
            setTimeout(() => el.classList.remove('bm-focus'), 1800);
        }
     }"
     @bom-scroll-to-editor.window="$nextTick(() => setTimeout(goEditor, 70))">

    <style>
        .bm-recipe {
            background:
                radial-gradient(ellipse at top left, rgba(200,236,63,.20), transparent 58%),
                radial-gradient(ellipse at bottom right, rgba(16,185,129,.18), transparent 55%),
                linear-gradient(135deg, #0a3f33 0%, #062e25 55%, #03201a 100%);
        }
        .bm-warn {
            background:
                radial-gradient(ellipse at top left, rgba(251,191,36,.30), transparent 58%),
                radial-gradient(ellipse at bottom right, rgba(220,38,38,.20), transparent 55%),
                linear-gradient(135deg, #78350f 0%, #7f1d1d 55%, #431407 100%);
        }
        .bm-grid-lines { background-image: linear-gradient(rgba(200,236,63,.10) 1px, transparent 1px), linear-gradient(90deg, rgba(200,236,63,.10) 1px, transparent 1px); background-size: 26px 26px; }
        @keyframes bmRise { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
        .bm-rise { animation: bmRise .5s cubic-bezier(.2,.7,.2,1) both; }
        @keyframes bmLive { 0%,100% { box-shadow: 0 0 0 0 rgba(200,236,63,.5); } 50% { box-shadow: 0 0 0 12px rgba(200,236,63,0); } }
        .bm-live { animation: bmLive 2.3s ease-out infinite; }
        @keyframes bmFocus { 0% { box-shadow: 0 0 0 0 rgba(200,236,63,0); } 25% { box-shadow: 0 0 0 7px rgba(200,236,63,.45); } 100% { box-shadow: 0 0 0 0 rgba(200,236,63,0); } }
        .bm-focus { animation: bmFocus 1.8s ease-out 1; }
        @keyframes bmFlow { 0% { background-position: 0 0; } 100% { background-position: 30px 0; } }
        .bm-flow { background-image: repeating-linear-gradient(45deg, rgba(200,236,63,.35) 0 6px, transparent 6px 15px); animation: bmFlow 1.3s linear infinite; }
        .bm-scroll::-webkit-scrollbar { width: 9px; height: 9px; }
        .bm-scroll::-webkit-scrollbar-track { background: #e2edf0; border-radius: 9px; }
        .bm-scroll::-webkit-scrollbar-thumb { background: #1d6247; border-radius: 9px; border: 2px solid #e2edf0; }
        .bm-scroll::-webkit-scrollbar-thumb:hover { background: #0a3f33; }
        .bm-select { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' stroke='%230a3f33' stroke-width='2.5' viewBox='0 0 24 24'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right .85rem center; background-size: 1rem; }
        #editor-resep { scroll-margin-top: 108px; }
        @media (prefers-reduced-motion: reduce) { .bm-rise, .bm-live, .bm-focus, .bm-flow { animation: none !important; } }
    </style>

    {{-- TOAST --}}
    @if(session('bom_success') || session('bom_warning') || session('bom_error'))
        @php
            $msg = session('bom_success') ?? session('bom_warning') ?? session('bom_error');
            $tone = session('bom_success') ? 'ok' : (session('bom_error') ? 'err' : 'warn');
        @endphp
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5200)" x-show="show" x-cloak
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-3"
             class="fixed top-24 right-4 sm:right-6 z-[80] max-w-sm" role="status" aria-live="polite">
            <div class="relative overflow-hidden rounded-2xl px-5 py-4 shadow-2xl flex items-start gap-3 {{ $tone === 'ok' ? 'forest-hero shadow-brand-950/40' : ($tone === 'err' ? 'bg-red-600 shadow-red-900/40' : 'bm-warn shadow-amber-950/40') }}">
                @if($tone === 'ok')<div class="forest-dots absolute inset-0 opacity-40"></div>@endif
                <span class="relative z-10 shrink-0 w-9 h-9 rounded-xl flex items-center justify-center {{ $tone === 'ok' ? 'bg-signal-400 text-brand-950' : 'bg-white text-red-700' }}">
                    {!! $bic($tone === 'ok' ? 'check' : 'alert', 'w-5 h-5') !!}
                </span>
                <p class="relative z-10 flex-1 text-base font-bold text-white">{{ $msg }}</p>
                <button type="button" @click="show = false" class="relative z-10 shrink-0 text-white/70 hover:text-white transition-colors">{!! $bic('x', 'w-4 h-4') !!}</button>
            </div>
        </div>
    @endif

    {{-- HERO --}}
    <div class="forest-hero relative overflow-hidden rounded-3xl text-white px-6 py-6 sm:px-8 sm:py-7 shadow-lg shadow-brand-950/25 bm-rise">
        <div class="forest-dots absolute inset-0 opacity-45"></div>
        <div class="bm-grid-lines absolute inset-0 opacity-40"></div>
        <div class="absolute -top-24 -left-16 w-72 h-72 rounded-full bg-emerald-500/15 blur-3xl"></div>
        <div class="absolute -bottom-24 right-0 w-64 h-64 rounded-full bg-signal-400/10 blur-3xl"></div>

        <div class="relative z-10 flex flex-col xl:flex-row xl:items-center justify-between gap-6">
            <div class="min-w-0">
                <p class="text-xs font-bold uppercase tracking-[0.24em] text-signal-300">Resep Produksi</p>
                <h1 class="mt-2 text-3xl font-extrabold tracking-tight">Kelola BOM Layanan</h1>
                <p class="mt-2 text-base text-emerald-100/75 max-w-2xl">Tentukan bahan, kebutuhan per satuan, dan scrap allowance untuk setiap layanan. MRP memakai resep ini untuk menghitung kebutuhan material.</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 shrink-0">
                <div class="rounded-2xl bg-signal-400 text-brand-950 px-5 py-4 shadow-lg shadow-signal-400/20 min-w-[13rem]">
                    <p class="text-[10px] font-bold uppercase tracking-[0.2em]">Cakupan Resep</p>
                    <p class="mt-1 text-2xl font-extrabold tabular-nums">{{ $stats['coverage'] }}%</p>
                    <div class="mt-2 h-2 rounded-full bg-brand-950/20 overflow-hidden">
                        <div class="h-full rounded-full bg-brand-950" style="width: {{ max(3, $stats['coverage']) }}%"></div>
                    </div>
                    <p class="mt-1.5 text-[11px] font-bold tabular-nums">{{ $stats['with_bom'] }} dari {{ $stats['services'] }} layanan</p>
                </div>
                <div class="flex flex-col gap-2">
                    <a href="{{ route('admin.services') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 border border-white/15 text-sm font-bold hover:bg-white/20 transition-colors">{!! $bic('printer', 'w-4 h-4') !!} Master Layanan</a>
                    <a href="{{ route('admin.materials') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 border border-white/15 text-sm font-bold hover:bg-white/20 transition-colors">{!! $bic('box', 'w-4 h-4') !!} Master Bahan</a>
                </div>
            </div>
        </div>

        <div class="relative z-10 mt-7 grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="rounded-2xl px-4 py-3 border bg-white/5 border-white/10">
                <span class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-[0.16em] text-emerald-100/60">{!! $bic('layers', 'w-3.5 h-3.5') !!} Total Layanan</span>
                <span class="mt-1 block text-2xl font-extrabold tabular-nums">{{ $stats['services'] }}</span>
            </div>
            <button type="button" wire:click="$set('onlyMissing', true)"
                    class="text-left rounded-2xl px-4 py-3 border transition-all hover:-translate-y-0.5 {{ $stats['missing'] > 0 ? 'bg-amber-600/75 border-amber-300/40' : 'bg-white/5 border-white/10' }}">
                <span class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-[0.16em] {{ $stats['missing'] > 0 ? 'text-amber-50' : 'text-emerald-100/60' }}">{!! $bic('alert', 'w-3.5 h-3.5') !!} Belum Ada Resep</span>
                <span class="mt-1 block text-2xl font-extrabold tabular-nums">{{ $stats['missing'] }}</span>
            </button>
            <div class="rounded-2xl px-4 py-3 border bg-white/5 border-white/10">
                <span class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-[0.16em] text-emerald-100/60">{!! $bic('note', 'w-3.5 h-3.5') !!} Baris Resep</span>
                <span class="mt-1 block text-2xl font-extrabold tabular-nums">{{ $stats['items'] }}</span>
            </div>
            <div class="rounded-2xl px-4 py-3 border bg-white/5 border-white/10">
                <span class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-[0.16em] text-emerald-100/60">{!! $bic('box', 'w-3.5 h-3.5') !!} Bahan Dipakai</span>
                <span class="mt-1 block text-2xl font-extrabold tabular-nums">{{ $stats['materials'] }}</span>
            </div>
        </div>
    </div>

    @if(!$ready)
        <div class="relative overflow-hidden rounded-[2rem] bm-warn text-white p-8 shadow-xl shadow-amber-950/25 bm-rise">
            <div class="absolute top-0 left-0 right-0 h-2" style="background-image: repeating-linear-gradient(45deg,#b45309 0 9px,#fde68a 9px 18px)"></div>
            <div class="relative z-10 flex items-start gap-4">
                <span class="shrink-0 w-14 h-14 rounded-2xl bg-white text-amber-700 flex items-center justify-center">{!! $bic('alert', 'w-7 h-7') !!}</span>
                <div>
                    <h2 class="text-xl font-extrabold">Skema BOM belum siap</h2>
                    <p class="mt-2 text-base text-amber-50/85">Tabel <b>service_boms</b> / <b>service_bom_items</b> belum ada atau kolom kebutuhan bahan belum lengkap.</p>
                    <div class="mt-4 rounded-xl bg-brand-950/40 border border-white/15 px-4 py-3 font-mono text-sm text-signal-300">php artisan migrate</div>
                    <p class="mt-3 text-sm text-amber-50/70">Setelah migrasi berjalan, muat ulang halaman ini untuk mulai menyusun resep.</p>
                </div>
            </div>
        </div>
    @else
        {{-- CONTROL BAR --}}
        <div class="sticky top-24 z-20 rounded-[1.75rem] bg-white/85 backdrop-blur-xl border border-ice-200 shadow-xl shadow-brand-950/5 p-4 bm-rise">
            <div class="flex flex-col lg:flex-row gap-3 lg:items-center">
                <div class="relative flex-1 min-w-0">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-brand-400">{!! $bic('search', 'w-4 h-4') !!}</span>
                    <input type="text" wire:model.live.debounce.250ms="search" placeholder="Cari layanan atau divisi produksi..."
                           class="w-full pl-11 pr-4 py-3 text-base border border-ice-300 rounded-2xl bg-ice-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25 focus:border-brand-500 transition-all">
                </div>
                <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                    <select wire:model.live="categoryFilter" class="bm-select pl-4 pr-10 py-3 text-sm font-bold text-brand-800 border border-ice-300 rounded-2xl bg-ice-50 focus:outline-none focus:ring-2 focus:ring-brand-500/25 cursor-pointer">
                        <option value="all">Semua divisi</option>
                        @foreach($categories as $c)
                            <option value="{{ $c['id'] }}">{{ $c['name'] }}</option>
                        @endforeach
                    </select>
                    <select wire:model.live="sort" class="bm-select pl-4 pr-10 py-3 text-sm font-bold text-brand-800 border border-ice-300 rounded-2xl bg-ice-50 focus:outline-none focus:ring-2 focus:ring-brand-500/25 cursor-pointer">
                        <option value="name">Urut: Nama</option>
                        <option value="category">Urut: Divisi</option>
                        <option value="missing">Urut: Belum ada resep</option>
                        <option value="richest">Urut: Bahan terbanyak</option>
                    </select>
                    <button type="button" wire:click="$toggle('onlyMissing')"
                            class="inline-flex items-center gap-2 px-4 py-3 rounded-2xl text-sm font-extrabold transition-all {{ $onlyMissing ? 'bg-amber-500 text-white shadow-sm shadow-amber-500/30' : 'bg-ice-100 text-brand-800 hover:bg-ice-200' }}">
                        {!! $bic('alert', 'w-4 h-4') !!} Tanpa Resep
                    </button>
                    @if($hasFilter)
                        <button type="button" wire:click="clearFilters" class="inline-flex items-center gap-2 px-4 py-3 rounded-2xl bg-red-50 text-red-600 text-sm font-bold hover:bg-red-100 transition-colors">{!! $bic('undo', 'w-4 h-4') !!} Reset</button>
                    @endif
                </div>
            </div>
        </div>

        {{-- WORKSPACE --}}
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">
            {{-- DAFTAR LAYANAN --}}
            <div class="xl:col-span-4 xl:sticky xl:top-44 rounded-[2rem] bg-white border border-ice-200 shadow-sm overflow-hidden bm-rise">
                <div class="px-5 py-4 border-b border-ice-200 bg-ice-50 flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-base font-extrabold text-brand-950">Daftar Layanan</h2>
                        <p class="text-xs text-gray-500">Klik untuk membuka resepnya</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-full bg-brand-600 text-signal-300 text-xs font-extrabold tabular-nums">{{ count($services) }}</span>
                </div>

                <div wire:loading.class="opacity-40" class="bm-scroll max-h-[34rem] overflow-y-auto divide-y divide-ice-100 transition-opacity">
                    @forelse($services as $s)
                        @php $isSel = (int) $serviceId === (int) $s['id']; @endphp
                        <button type="button" wire:click="selectService({{ $s['id'] }})"
                                class="w-full text-left px-5 py-3.5 transition-colors {{ $isSel ? 'bg-brand-50 border-l-4 border-l-brand-600' : 'hover:bg-ice-50 border-l-4 border-l-transparent' }}">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-base font-extrabold truncate {{ $isSel ? 'text-brand-800' : 'text-brand-950' }}">{{ $s['name'] }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ $s['category'] }} • Rp {{ number_format($s['price'], 0, ',', '.') }}/m²</p>
                                </div>
                                @if($s['items'] > 0)
                                    <span class="shrink-0 inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-extrabold {{ $s['bom_active'] ? 'bg-signal-100 text-signal-700' : 'bg-ice-300 text-gray-600' }}">
                                        {!! $bic('layers', 'w-3 h-3') !!} {{ $s['items'] }}
                                    </span>
                                @else
                                    <span class="shrink-0 px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 text-[10px] font-extrabold uppercase">Kosong</span>
                                @endif
                            </div>
                        </button>
                    @empty
                        <div class="p-10 text-center">
                            <span class="inline-flex w-14 h-14 rounded-full bg-ice-100 text-brand-500 items-center justify-center mx-auto">{!! $bic('search', 'w-6 h-6') !!}</span>
                            <p class="mt-3 text-base font-extrabold text-brand-950">Tidak ada layanan cocok</p>
                            <button type="button" wire:click="clearFilters" class="mt-3 px-4 py-2 rounded-xl bg-ice-100 text-brand-800 text-sm font-bold hover:bg-ice-200 transition-colors">Reset saringan</button>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- EDITOR --}}
            <div class="xl:col-span-8 space-y-5 min-w-0">
                @if(!$selected)
                    <div class="bm-recipe relative overflow-hidden rounded-[2rem] p-12 text-center text-white shadow-lg shadow-brand-950/20 bm-rise">
                        <div class="forest-dots absolute inset-0 opacity-45"></div>
                        <div class="bm-grid-lines absolute inset-0 opacity-40"></div>
                        <div class="relative z-10">
                            <span class="inline-flex w-20 h-20 rounded-full bg-white/5 border border-signal-400/30 text-signal-300 items-center justify-center mx-auto">{!! $bic('flask', 'w-9 h-9') !!}</span>
                            <p class="mt-6 text-2xl font-extrabold">Pilih layanan untuk menyusun resep</p>
                            <p class="mt-2 text-base text-emerald-100/70 max-w-lg mx-auto">Resep menentukan bahan apa saja yang dikonsumsi MRP setiap kali layanan ini diproduksi, termasuk toleransi scrap.</p>
                            @if($stats['missing'] > 0)
                                <button type="button" wire:click="$set('onlyMissing', true)"
                                        class="mt-7 inline-flex items-center gap-2 px-6 py-3 rounded-full bg-signal-400 text-brand-950 text-base font-extrabold hover:bg-signal-500 transition-colors">
                                    {!! $bic('alert', 'w-4 h-4') !!} Lihat {{ $stats['missing'] }} layanan tanpa resep
                                </button>
                            @endif
                        </div>
                    </div>
                @else
                    {{-- HEADER EDITOR --}}
                    <div id="editor-resep" class="relative overflow-hidden rounded-[2rem] bg-white border-2 {{ $bomExists ? 'border-brand-200' : 'border-amber-300' }} shadow-sm bm-rise">
                        <div class="absolute left-0 top-0 bottom-0 w-1.5 {{ $bomExists ? 'bg-gradient-to-b from-signal-400 to-brand-600' : 'bg-gradient-to-b from-amber-400 to-amber-600' }}"></div>
                        <div class="p-6 sm:p-7 pl-7 sm:pl-8">
                            <div class="flex flex-wrap items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <p class="text-[11px] font-bold uppercase tracking-[0.22em] {{ $bomExists ? 'text-signal-600' : 'text-amber-600' }}">
                                        {{ $bomExists ? 'Resep tersimpan • versi ' . max(1, $bomVersion) : 'Resep baru — belum disimpan' }}
                                    </p>
                                    <h2 class="mt-1 text-2xl font-extrabold text-brand-950 tracking-tight">{{ $selected['name'] }}</h2>
                                    <div class="mt-2 flex flex-wrap items-center gap-2">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-ice-100 text-brand-800 text-xs font-bold">{!! $bic('tag', 'w-3.5 h-3.5') !!} {{ $selected['category'] }}</span>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-brand-50 text-brand-700 text-xs font-bold tabular-nums">{!! $bic('money', 'w-3.5 h-3.5') !!} Rp {{ number_format($selected['price'], 0, ',', '.') }}/m²</span>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-ice-100 text-brand-800 text-xs font-bold tabular-nums">{!! $bic('layers', 'w-3.5 h-3.5') !!} {{ count($lines) }} bahan</span>
                                    </div>
                                </div>
                                <label class="shrink-0 inline-flex items-center gap-3 px-4 py-3 rounded-2xl border-2 cursor-pointer transition-colors {{ $bomActive ? 'border-signal-400 bg-signal-100/50' : 'border-ice-300 bg-ice-50' }}">
                                    <input type="checkbox" wire:model.live="bomActive" class="w-5 h-5 rounded border-ice-400 text-signal-500 focus:ring-signal-400 cursor-pointer">
                                    <span class="text-sm font-extrabold {{ $bomActive ? 'text-signal-700' : 'text-gray-500' }}">{{ $bomActive ? 'Resep aktif' : 'Nonaktif' }}</span>
                                </label>
                            </div>

                            {{-- SIMULATOR --}}
                            <div class="mt-6 bm-recipe relative overflow-hidden rounded-2xl p-5 text-white">
                                <div class="forest-dots absolute inset-0 opacity-40"></div>
                                <div class="relative">
                                    <div class="flex flex-wrap items-end justify-between gap-4">
                                        <div>
                                            <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-signal-300">Simulasi Kebutuhan</p>
                                            <p class="mt-1 text-sm text-emerald-100/70">Hitung konsumsi bahan untuk ukuran order nyata.</p>
                                        </div>
                                        <div class="flex flex-wrap items-end gap-3">
                                            <div>
                                                <label class="block text-[10px] font-bold uppercase tracking-wider text-emerald-100/60 mb-1">Luas (m²)</label>
                                                <input type="number" step="0.01" min="0" wire:model.live.debounce.250ms="simArea"
                                                       class="w-28 px-3 py-2.5 text-base font-bold tabular-nums rounded-xl bg-white/10 border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-signal-400/50">
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold uppercase tracking-wider text-emerald-100/60 mb-1">Qty</label>
                                                <input type="number" step="1" min="0" wire:model.live.debounce.250ms="simQty"
                                                       class="w-24 px-3 py-2.5 text-base font-bold tabular-nums rounded-xl bg-white/10 border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-signal-400/50">
                                            </div>
                                            <div class="flex gap-1.5">
                                                @foreach([['1','1'],['10','1'],['100','1'],['1','100']] as $p)
                                                    <button type="button" wire:click="$set('simArea','{{ $p[0] }}')" wire:click="$set('simQty','{{ $p[1] }}')"
                                                            class="px-3 py-2 rounded-lg bg-white/10 border border-white/15 text-xs font-bold hover:bg-white/20 transition-colors tabular-nums">{{ $p[0] }}×{{ $p[1] }}</button>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-5 grid grid-cols-2 lg:grid-cols-4 gap-3">
                                        <div class="rounded-xl bg-white/8 border border-white/15 px-4 py-3">
                                            <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-100/55">Nilai Bahan</p>
                                            <p class="mt-1 text-lg font-extrabold text-signal-300 tabular-nums">Rp {{ number_format($sim['cost'], 0, ',', '.') }}</p>
                                        </div>
                                        <div class="rounded-xl bg-white/8 border border-white/15 px-4 py-3">
                                            <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-100/55">Nilai Jasa</p>
                                            <p class="mt-1 text-lg font-extrabold tabular-nums">Rp {{ number_format($sim['revenue'], 0, ',', '.') }}</p>
                                        </div>
                                        <div class="rounded-xl bg-white/8 border border-white/15 px-4 py-3">
                                            <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-100/55">Rasio Bahan / Jasa</p>
                                            <p class="mt-1 text-lg font-extrabold tabular-nums {{ $sim['ratio'] > 100 ? 'text-amber-300' : 'text-white' }}">{{ $sim['ratio'] }}%</p>
                                        </div>
                                        <div class="rounded-xl px-4 py-3 border {{ $sim['short'] > 0 ? 'bg-red-600/85 border-red-300/40' : 'bg-white/8 border-white/15' }}">
                                            <p class="text-[10px] font-bold uppercase tracking-wider {{ $sim['short'] > 0 ? 'text-red-50' : 'text-emerald-100/55' }}">Stok Kurang</p>
                                            <p class="mt-1 text-lg font-extrabold tabular-nums">{{ $sim['short'] }} bahan</p>
                                        </div>
                                    </div>

                                    @if(count($lines))
                                        <div class="mt-4 bm-scroll overflow-x-auto">
                                            <table class="w-full text-left text-sm">
                                                <thead>
                                                    <tr class="text-[10px] uppercase tracking-wider text-emerald-100/55">
                                                        <th class="py-2 pr-3 font-bold">Bahan</th>
                                                        <th class="py-2 px-3 font-bold text-right">Per m²</th>
                                                        <th class="py-2 px-3 font-bold text-right">+Scrap</th>
                                                        <th class="py-2 px-3 font-bold text-right">Kebutuhan</th>
                                                        <th class="py-2 px-3 font-bold text-right">Sisa Gudang</th>
                                                        <th class="py-2 pl-3 font-bold text-right">Nilai</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-white/10">
                                                    @foreach($lines as $l)
                                                        <tr>
                                                            <td class="py-2.5 pr-3">
                                                                <span class="font-bold {{ $l['name'] ? 'text-white' : 'text-emerald-100/40 italic' }}">{{ $l['name'] ?? 'belum dipilih' }}</span>
                                                                @if($l['optional'])<span class="ml-1.5 px-1.5 py-0.5 rounded bg-white/15 text-[9px] font-bold uppercase">opsional</span>@endif
                                                                @if($l['inactive'])<span class="ml-1.5 px-1.5 py-0.5 rounded bg-amber-400 text-brand-950 text-[9px] font-bold uppercase">nonaktif</span>@endif
                                                            </td>
                                                            <td class="py-2.5 px-3 text-right tabular-nums text-emerald-100/80">{{ number_format($l['per_unit'], 4) }}</td>
                                                            <td class="py-2.5 px-3 text-right tabular-nums text-emerald-100/80">{{ number_format($l['scrap'], 1) }}%</td>
                                                            <td class="py-2.5 px-3 text-right font-extrabold tabular-nums text-signal-300">{{ number_format($l['need'], 2) }} {{ $l['unit'] }}</td>
                                                            <td class="py-2.5 px-3 text-right tabular-nums {{ $l['short'] ? 'text-red-300 font-extrabold' : 'text-emerald-100/80' }}">{{ number_format($l['net'], 2) }}</td>
                                                            <td class="py-2.5 pl-3 text-right font-bold tabular-nums">Rp {{ number_format($l['cost'], 0, ',', '.') }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- BARIS BAHAN --}}
                            <div class="mt-6">
                                <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
                                    <div>
                                        <h3 class="text-base font-extrabold text-brand-950">Komposisi Bahan</h3>
                                        <p class="text-xs text-gray-500">Kebutuhan per 1 m² layanan, sebelum scrap.</p>
                                    </div>
                                    <button type="button" wire:click="addItem" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-ice-100 text-brand-800 text-sm font-bold hover:bg-ice-200 transition-colors">{!! $bic('plus', 'w-4 h-4') !!} Baris Kosong</button>
                                </div>

                                @error('items') <p class="mb-3 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
                                @error('service') <p class="mb-3 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror

                                <div class="space-y-3">
                                    @foreach($items as $i => $it)
                                        @php $m = $catalog[$it['material_id']] ?? null; @endphp
                                        <div class="relative overflow-hidden rounded-2xl border border-ice-200 bg-ice-50/60 p-4">
                                            <div class="absolute left-0 top-0 bottom-0 w-1 {{ $m ? 'bg-brand-500' : 'bg-ice-300' }}"></div>
                                            <div class="pl-2">
                                                <div class="flex items-center justify-between gap-3 mb-3">
                                                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-600 text-signal-300 text-xs font-extrabold uppercase tracking-wider">Bahan {{ $i + 1 }}</span>
                                                    <div class="flex items-center gap-1.5">
                                                        <button type="button" wire:click="moveItem({{ $i }}, 'up')" @disabled($loop->first) title="Naikkan"
                                                                class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-ice-300 text-brand-700 hover:bg-ice-100 transition-colors disabled:opacity-30">{!! $bic('up', 'w-4 h-4') !!}</button>
                                                        <button type="button" wire:click="moveItem({{ $i }}, 'down')" @disabled($loop->last) title="Turunkan"
                                                                class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-ice-300 text-brand-700 hover:bg-ice-100 transition-colors disabled:opacity-30">{!! $bic('down', 'w-4 h-4') !!}</button>
                                                        <button type="button" wire:click="removeItem({{ $i }})" title="Hapus baris"
                                                                class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors">{!! $bic('trash', 'w-4 h-4') !!}</button>
                                                    </div>
                                                </div>

                                                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                                                    <div class="sm:col-span-6">
                                                        <label class="block text-xs font-bold text-gray-500 mb-1">Bahan Baku</label>
                                                        <select wire:model.live="items.{{ $i }}.material_id"
                                                                class="bm-select w-full pl-3 pr-9 py-2.5 text-sm font-bold border border-ice-300 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25 cursor-pointer @error('items.'.$i.'.material_id') border-red-400 bg-red-50 @enderror">
                                                            <option value="">— Pilih bahan —</option>
                                                            @foreach($catalog as $id => $cm)
                                                                <option value="{{ $id }}">{{ $cm['name'] }}{{ $cm['active'] ? '' : ' (nonaktif)' }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('items.'.$i.'.material_id') <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p> @enderror
                                                    </div>
                                                    <div class="sm:col-span-3">
                                                        <label class="block text-xs font-bold text-gray-500 mb-1">Kebutuhan / m²{{ $m ? ' ('.$m['unit'].')' : '' }}</label>
                                                        <input type="number" step="0.0001" min="0" wire:model.live.debounce.250ms="items.{{ $i }}.qty"
                                                               class="w-full px-3 py-2.5 text-sm font-bold tabular-nums border border-ice-300 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25 @error('items.'.$i.'.qty') border-red-400 bg-red-50 @enderror">
                                                        @error('items.'.$i.'.qty') <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p> @enderror
                                                    </div>
                                                    <div class="sm:col-span-3">
                                                        <label class="block text-xs font-bold text-gray-500 mb-1">Scrap (%)</label>
                                                        <input type="number" step="0.1" min="0" max="100" wire:model.live.debounce.250ms="items.{{ $i }}.scrap"
                                                               class="w-full px-3 py-2.5 text-sm font-bold tabular-nums border border-ice-300 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25 @error('items.'.$i.'.scrap') border-red-400 bg-red-50 @enderror">
                                                        @error('items.'.$i.'.scrap') <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p> @enderror
                                                    </div>
                                                    <div class="sm:col-span-9">
                                                        <label class="block text-xs font-bold text-gray-500 mb-1">Catatan baris</label>
                                                        <input type="text" maxlength="255" wire:model="items.{{ $i }}.notes" placeholder="Contoh: potong memanjang, sisakan tepi 2 cm"
                                                               class="w-full px-3 py-2.5 text-sm border border-ice-300 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25">
                                                    </div>
                                                    <div class="sm:col-span-3 flex items-end">
                                                        <label class="w-full inline-flex items-center gap-2.5 px-3 py-2.5 rounded-xl border cursor-pointer transition-colors {{ ($it['optional'] ?? false) ? 'border-signal-400 bg-signal-100/60' : 'border-ice-300 bg-white' }}">
                                                            <input type="checkbox" wire:model.live="items.{{ $i }}.optional" class="w-4 h-4 rounded border-ice-400 text-signal-500 focus:ring-signal-400 cursor-pointer">
                                                            <span class="text-xs font-extrabold {{ ($it['optional'] ?? false) ? 'text-signal-700' : 'text-gray-500' }}">Opsional</span>
                                                        </label>
                                                    </div>
                                                </div>

                                                @if($m)
                                                    <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-1.5 text-xs">
                                                        <span class="text-gray-500">Harga <b class="text-brand-900 tabular-nums">Rp {{ number_format($m['price'], 0, ',', '.') }}/{{ $m['unit'] }}</b></span>
                                                        <span class="text-gray-500">Sisa gudang <b class="tabular-nums {{ $m['net'] <= 0 ? 'text-red-600' : 'text-brand-900' }}">{{ number_format($m['net'], 2) }} {{ $m['unit'] }}</b></span>
                                                        @php $ln = collect($lines)->firstWhere('index', $i); @endphp
                                                        @if($ln)
                                                            <span class="text-gray-500">Simulasi <b class="text-brand-700 tabular-nums">{{ number_format($ln['need'], 2) }} {{ $ln['unit'] }}</b></span>
                                                            @if($ln['short'])
                                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-red-100 text-red-700 font-extrabold">{!! $bic('alert', 'w-3 h-3') !!} stok kurang</span>
                                                            @endif
                                                        @endif
                                                        @if(!$m['active'])
                                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 font-extrabold">{!! $bic('alert', 'w-3 h-3') !!} bahan nonaktif</span>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- PICKER CEPAT --}}
                                <div class="mt-4 rounded-2xl border-2 border-dashed border-brand-300 bg-brand-50/40 p-4">
                                    <label class="block text-sm font-extrabold text-brand-950 mb-2">Tambah bahan cepat</label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-brand-400">{!! $bic('search', 'w-4 h-4') !!}</span>
                                        <input type="text" wire:model.live.debounce.250ms="materialQuery" placeholder="Ketik nama bahan yang belum ada di resep..."
                                               class="w-full pl-11 pr-4 py-3 text-base border border-ice-300 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25 focus:border-brand-500">
                                    </div>
                                    @if(count($materialMatches))
                                        <div class="mt-2 bm-scroll max-h-56 overflow-y-auto rounded-xl border border-ice-200 bg-white divide-y divide-ice-100">
                                            @foreach($materialMatches as $mm)
                                                <button type="button" wire:click="attachMaterial({{ $mm['id'] }})" class="w-full text-left flex items-center justify-between gap-3 px-4 py-2.5 hover:bg-ice-50 transition-colors">
                                                    <span class="min-w-0">
                                                        <span class="block text-sm font-bold text-brand-950 truncate">{{ $mm['name'] }}</span>
                                                        <span class="block text-xs text-gray-500 tabular-nums">Rp {{ number_format($mm['price'], 0, ',', '.') }}/{{ $mm['unit'] }} • sisa {{ number_format($mm['net'], 2) }}</span>
                                                    </span>
                                                    <span class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-brand-600 text-signal-300 text-xs font-extrabold">{!! $bic('plus', 'w-3 h-3') !!} Tambah</span>
                                                </button>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                {{-- CATATAN RESEP --}}
                                <div class="mt-5">
                                    <label class="block text-sm font-extrabold text-brand-950 mb-1.5">Catatan Resep</label>
                                    <textarea wire:model="bomNotes" rows="3" maxlength="500" placeholder="Contoh: gunakan tinta original, laminasi setelah cetak kering 30 menit."
                                              class="bm-scroll w-full px-4 py-3 text-base border border-ice-300 rounded-xl bg-ice-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25 resize-y"></textarea>
                                    @error('bomNotes') <p class="mt-1.5 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
                                </div>

                                {{-- AKSI --}}
                                <div class="mt-6 flex flex-wrap items-center gap-2.5">
                                    <button type="button" wire:click="save"
                                            class="flex-1 min-w-[14rem] inline-flex items-center justify-center gap-2 px-6 py-4 rounded-2xl bg-brand-600 text-signal-300 text-base font-extrabold hover:bg-brand-700 shadow-md shadow-brand-900/25 hover:-translate-y-0.5 transition-all disabled:opacity-60"
                                            wire:loading.attr="disabled" wire:target="save">
                                        <span wire:loading.remove wire:target="save" class="inline-flex items-center gap-2">{!! $bic('save', 'w-5 h-5') !!} Simpan Resep</span>
                                        <span wire:loading wire:target="save" class="inline-flex items-center gap-2">
                                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.4 0 0 5.4 0 12h4z"/></svg> Menyimpan...
                                        </span>
                                    </button>
                                    <button type="button" wire:click="openCopy" class="inline-flex items-center gap-2 px-5 py-4 rounded-2xl bg-ice-100 text-brand-800 text-base font-bold hover:bg-ice-200 transition-colors">
                                        {!! $bic('copy', 'w-4 h-4') !!} Salin ke Layanan Lain
                                    </button>
                                    <button type="button" wire:click="selectService({{ $selected['id'] }})" class="inline-flex items-center gap-2 px-5 py-4 rounded-2xl bg-white border border-ice-300 text-brand-800 text-base font-bold hover:bg-ice-50 transition-colors">
                                        {!! $bic('undo', 'w-4 h-4') !!} Muat Ulang
                                    </button>
                                    @if($bomExists)
                                        <button type="button" wire:click="deleteBom" wire:confirm="Hapus seluruh resep layanan ini? MRP tidak akan menghitung kebutuhan bahan untuk layanan ini."
                                                class="inline-flex items-center gap-2 px-5 py-4 rounded-2xl bg-red-50 border border-red-300 text-red-600 text-base font-bold hover:bg-red-100 transition-colors">
                                            {!! $bic('trash', 'w-4 h-4') !!} Hapus Resep
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- MODAL SALIN --}}
    @if($showCopy)
        <div class="fixed inset-0 z-[75] flex items-end sm:items-center justify-center p-0 sm:p-4 bg-brand-950/60 backdrop-blur-sm" wire:click.self="closeCopy">
            <div class="w-full sm:max-w-lg max-h-[88vh] flex flex-col overflow-hidden rounded-t-3xl sm:rounded-3xl bg-ice-50 shadow-2xl">
                <div class="forest-hero relative overflow-hidden px-6 py-5 flex items-start justify-between gap-4 shrink-0">
                    <div class="forest-dots absolute inset-0 opacity-40"></div>
                    <div class="relative z-10 min-w-0">
                        <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-signal-300">Duplikasi Resep</p>
                        <h3 class="mt-1 text-xl font-extrabold text-white tracking-tight">Salin ke Layanan Lain</h3>
                        <p class="mt-1 text-sm text-emerald-100/75">{{ count($lines) }} bahan dari {{ $selected['name'] ?? '-' }}</p>
                    </div>
                    <button type="button" wire:click="closeCopy" class="relative z-10 shrink-0 w-9 h-9 flex items-center justify-center rounded-xl bg-white/10 text-white hover:bg-white/20 transition-colors">{!! $bic('x', 'w-5 h-5') !!}</button>
                </div>

                <div class="bm-scroll flex-1 overflow-y-auto p-6">
                    <div class="rounded-xl bg-amber-50 border border-amber-200 px-4 py-3 flex items-start gap-2.5">
                        <span class="shrink-0 text-amber-700 mt-0.5">{!! $bic('alert', 'w-4 h-4') !!}</span>
                        <p class="text-sm text-amber-800">Resep layanan tujuan akan <b>ditimpa</b> dengan komposisi ini.</p>
                    </div>

                    <div class="relative mt-4">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-brand-400">{!! $bic('search', 'w-4 h-4') !!}</span>
                        <input type="text" wire:model.live.debounce.250ms="copyQuery" placeholder="Cari layanan tujuan..."
                               class="w-full pl-11 pr-4 py-3 text-base border border-ice-300 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25 focus:border-brand-500">
                    </div>
                    @error('copy') <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror

                    <div class="mt-3 bm-scroll max-h-64 overflow-y-auto rounded-xl border border-ice-200 bg-white divide-y divide-ice-100">
                        @forelse($copyTargets as $t)
                            <button type="button" wire:click="$set('copyTargetId', {{ $t['id'] }})"
                                    class="w-full text-left flex items-center justify-between gap-3 px-4 py-3 transition-colors {{ (int) $copyTargetId === (int) $t['id'] ? 'bg-brand-50' : 'hover:bg-ice-50' }}">
                                <span class="min-w-0">
                                    <span class="block text-sm font-bold text-brand-950 truncate">{{ $t['name'] }}</span>
                                    <span class="block text-xs text-gray-500">{{ $t['category'] }} • {{ $t['items'] }} bahan saat ini</span>
                                </span>
                                @if((int) $copyTargetId === (int) $t['id'])
                                    <span class="shrink-0 text-brand-600">{!! $bic('check', 'w-5 h-5') !!}</span>
                                @endif
                            </button>
                        @empty
                            <p class="px-4 py-8 text-center text-sm text-gray-400">Tidak ada layanan tujuan cocok.</p>
                        @endforelse
                    </div>
                </div>

                <div class="shrink-0 px-6 py-4 border-t border-ice-200 bg-white flex flex-wrap gap-2.5">
                    <button type="button" wire:click="applyCopy" @disabled(!$copyTargetId)
                            class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-2xl bg-brand-600 text-signal-300 text-base font-extrabold hover:bg-brand-700 transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
                            wire:loading.attr="disabled" wire:target="applyCopy">
                        <span wire:loading.remove wire:target="applyCopy" class="inline-flex items-center gap-2">{!! $bic('copy', 'w-4 h-4') !!} Salin Resep</span>
                        <span wire:loading wire:target="applyCopy">Menyalin...</span>
                    </button>
                    <button type="button" wire:click="closeCopy" class="px-5 py-3.5 rounded-2xl bg-ice-200 text-brand-800 text-base font-bold hover:bg-ice-300 transition-colors">Batal</button>
                </div>
            </div>
        </div>
    @endif
</div>