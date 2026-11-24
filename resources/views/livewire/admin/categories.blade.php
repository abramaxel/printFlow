@php
    $K = [
        'tag'    => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z',
        'layers' => 'M12 2l9 5-9 5-9-5 9-5z M3 12l9 5 9-5 M3 17l9 5 9-5',
        'printer'=> 'M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z',
        'box'    => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
        'ruler'  => 'M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4',
        'plus'   => 'M12 4v16m8-8H4',
        'edit'   => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
        'trash'  => 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16',
        'eye'    => 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z',
        'search' => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z',
        'undo'   => 'M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6',
        'check'  => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        'alert'  => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
        'x'      => 'M6 18L18 6M6 6l12 12',
        'copy'   => 'M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z',
        'pause'  => 'M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z',
        'play'   => 'M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'money'  => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'arrow'  => 'M14 5l7 7m0 0l-7 7m7-7H3',
        'move'   => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4',
        'lock'   => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z',
        'bolt'   => 'M13 10V3L4 14h7v7l9-11h-7z',
        'note'   => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'shield' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
    ];
    $kic = fn($k, $c = 'w-5 h-5') => '<svg class="' . $c . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="' . ($K[$k] ?? $K['tag']) . '"/></svg>';
    $empty = fn($t) => '<p class="px-6 py-9 text-center text-sm font-bold text-gray-400">' . $t . '</p>';
@endphp

<div class="w-full space-y-4">
    <style>
        .ct-grid { background-image: linear-gradient(rgba(200,236,63,.08) 1px, transparent 1px), linear-gradient(90deg, rgba(200,236,63,.08) 1px, transparent 1px); background-size: 28px 28px; }
        @keyframes ctRise { from { opacity:0; transform: translateY(12px);} to { opacity:1; transform:none;} }
        .ct-rise { animation: ctRise .45s cubic-bezier(.2,.7,.2,1) both; }
        @keyframes ctWide { from { transform: scaleX(0);} to { transform: scaleX(1);} }
        .ct-wide { transform-origin: left; animation: ctWide .55s cubic-bezier(.2,.7,.2,1) both; }
        @keyframes ctLive { 0%,100% { box-shadow: 0 0 0 0 rgba(200,236,63,.45);} 50% { box-shadow: 0 0 0 10px rgba(200,236,63,0);} }
        .ct-live { animation: ctLive 2.4s ease-out infinite; }
        .ct-scroll::-webkit-scrollbar { width: 9px; height: 9px; }
        .ct-scroll::-webkit-scrollbar-track { background: #e2edf0; border-radius: 9px; }
        .ct-scroll::-webkit-scrollbar-thumb { background: #1d6247; border-radius: 9px; border: 2px solid #e2edf0; }
        .ct-scroll::-webkit-scrollbar-thumb:hover { background: #0a3f33; }
        .ct-select { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' stroke='%230a3f33' stroke-width='2.5' viewBox='0 0 24 24'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right .7rem center; background-size: .9rem; }
        .ct-field { transition: box-shadow .2s ease, border-color .2s ease, background-color .2s ease; }
        @media (prefers-reduced-motion: reduce) { .ct-rise,.ct-wide,.ct-live { animation: none !important; } }
    </style>

    {{-- TOAST --}}
    @if(session('cat_success') || session('cat_error'))
        @php $ok = (bool) session('cat_success'); @endphp
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5500)" x-show="show" x-cloak
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-3"
             class="fixed top-24 right-4 sm:right-6 z-[80] max-w-sm" role="status" aria-live="polite">
            <div class="relative overflow-hidden rounded-2xl px-5 py-4 shadow-2xl flex items-start gap-3 {{ $ok ? 'forest-hero shadow-brand-950/40' : 'bg-red-600 shadow-red-900/40' }}">
                @if($ok)<div class="forest-dots absolute inset-0 opacity-40"></div>@endif
                <span class="relative z-10 shrink-0 w-9 h-9 rounded-xl flex items-center justify-center {{ $ok ? 'bg-signal-400 text-brand-950' : 'bg-white/20 text-white' }}">{!! $kic($ok ? 'check' : 'alert', 'w-5 h-5') !!}</span>
                <p class="relative z-10 flex-1 text-base font-bold text-white">{{ session('cat_success') ?? session('cat_error') }}</p>
                <button type="button" @click="show = false" class="relative z-10 shrink-0 text-white/70 hover:text-white transition-colors">{!! $kic('x', 'w-4 h-4') !!}</button>
            </div>
        </div>
    @endif

    {{-- HERO --}}
    <div class="forest-hero relative overflow-hidden rounded-3xl text-white shadow-lg shadow-brand-950/25 ct-rise">
        <div class="forest-dots absolute inset-0 opacity-40"></div>
        <div class="ct-grid absolute inset-0 opacity-45"></div>
        <div class="absolute -top-24 -left-16 w-72 h-72 rounded-full bg-emerald-500/12 blur-3xl"></div>
        <div class="absolute -bottom-24 right-0 w-64 h-64 rounded-full bg-signal-400/10 blur-3xl"></div>

        <div class="relative z-10 px-6 sm:px-8 pt-6 pb-5 flex flex-col xl:flex-row xl:items-start justify-between gap-5">
            <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/8 border border-white/15 text-[10px] font-bold uppercase tracking-[0.2em] text-signal-300">{!! $kic('layers', 'w-3 h-3') !!} Master Data</span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-signal-400 text-brand-950 text-[11px] font-extrabold uppercase tracking-wider">{!! $kic('bolt', 'w-3.5 h-3.5') !!} Induk Semua Modul</span>
                </div>
                <h1 class="mt-3 text-2xl sm:text-3xl font-extrabold tracking-tight">Manajemen Divisi Produksi</h1>
                <p class="mt-2 text-base text-emerald-100/75 max-w-2xl">Divisi adalah pengelompokan tertinggi. Layanan, bahan baku, dan ukuran semuanya terikat ke divisi — perubahan di sini memengaruhi seluruh alur pesanan dan MRP.</p>

                <div class="mt-4 flex flex-wrap items-end gap-x-8 gap-y-3">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-emerald-100/55">Nilai Bahan per Divisi</p>
                        <p class="text-3xl font-extrabold text-signal-300 tabular-nums leading-none mt-1">Rp {{ number_format($stats['stock_value'], 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-emerald-100/55">Total Layanan</p>
                        <p class="text-xl font-extrabold tabular-nums leading-none mt-1">{{ $stats['services'] }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-emerald-100/55">Total Bahan</p>
                        <p class="text-xl font-extrabold tabular-nums leading-none mt-1">{{ $stats['materials'] }}</p>
                    </div>
                </div>
            </div>

            <div class="shrink-0 flex flex-col gap-2.5">
                <a href="{{ route('admin.services') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-signal-400 text-brand-950 text-sm font-extrabold hover:bg-signal-500 shadow-md shadow-signal-400/20 transition-all">{!! $kic('printer', 'w-4 h-4') !!} Kelola Layanan</a>
                <a href="{{ route('admin.materials') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 border border-white/15 text-sm font-bold hover:bg-white/20 transition-colors">{!! $kic('box', 'w-4 h-4') !!} Kelola Bahan</a>
            </div>
        </div>

        <div class="relative z-10 border-t border-white/10 bg-brand-950/30 px-6 sm:px-8 py-3">
            <div class="flex flex-wrap items-stretch divide-x divide-white/10">
                @foreach([
                    ['Total Divisi', $stats['categories'], 'layers', 'all'],
                    ['Aktif', $stats['active'], 'check', 'active'],
                    ['Nonaktif', $stats['categories'] - $stats['active'], 'pause', 'inactive'],
                    ['Belum Terpakai', $stats['empty'], 'alert', 'empty'],
                    ['Ukuran Terikat', $stats['sizes'], 'ruler', null],
                ] as $i => $c)
                    <button type="button" @if($c[3]) wire:click="$set('statusFilter','{{ $c[3] }}')" @endif
                            class="flex-1 min-w-[8rem] text-left py-1.5 {{ $i === 0 ? 'pr-4' : ($i === 4 ? 'pl-4' : 'px-4') }} {{ $c[3] ? 'hover:opacity-80 transition-opacity' : 'cursor-default' }}">
                        <span class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-[0.14em] {{ $statusFilter === $c[3] ? 'text-signal-300' : 'text-emerald-100/50' }}">{!! $kic($c[2], 'w-3.5 h-3.5') !!} {{ $c[0] }}</span>
                        <span class="mt-0.5 block text-2xl font-extrabold tabular-nums">{{ $c[1] }}</span>
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- FORM TAMBAH --}}
    <section class="relative overflow-hidden rounded-3xl bg-white border-2 border-signal-400 shadow-sm ct-rise">
        <div class="absolute left-0 top-6 bottom-6 w-1.5 rounded-r-full bg-gradient-to-b from-signal-400 to-brand-600"></div>
        <div class="p-6 pl-7 sm:pl-8">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-full bg-brand-600 text-signal-300 flex items-center justify-center text-sm font-black shrink-0">+</span>
                <div>
                    <h2 class="text-lg font-extrabold text-brand-950 tracking-tight">Tambah Divisi Baru</h2>
                    <p class="text-sm text-gray-500">Slug dibuat otomatis dari nama dan dijamin unik.</p>
                </div>
            </div>

            <form wire:submit="create" class="mt-5 grid grid-cols-1 lg:grid-cols-12 gap-4 items-start">
                <div class="lg:col-span-4">
                    <label for="c-name" class="block text-sm font-extrabold text-brand-950 mb-1.5">Nama Divisi <span class="text-red-500">*</span></label>
                    <input id="c-name" wire:model="name" type="text" maxlength="120" placeholder="Contoh: Large Format, Digital Printing"
                           class="ct-field w-full px-4 py-3 text-base font-bold border border-ice-300 rounded-xl bg-ice-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25 focus:border-brand-500 @error('name') border-red-400 bg-red-50 @enderror">
                    @error('name') <p class="mt-1.5 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="{{ $hasDesc ? 'lg:col-span-5' : 'lg:col-span-6' }}">
                    <label for="c-desc" class="block text-sm font-extrabold text-brand-950 mb-1.5">Deskripsi <span class="text-gray-400 font-semibold">(opsional)</span></label>
                    <input id="c-desc" wire:model="description" type="text" maxlength="500" placeholder="Keterangan singkat cakupan divisi ini"
                           class="ct-field w-full px-4 py-3 text-base border border-ice-300 rounded-xl bg-ice-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25 focus:border-brand-500">
                </div>
                <div class="lg:col-span-3 flex flex-col gap-2">
                    @if($hasActive)
                        <label class="inline-flex items-center gap-2.5 px-3 py-2.5 rounded-xl border cursor-pointer transition-colors {{ $is_active ? 'border-signal-400 bg-signal-100/60' : 'border-ice-300 bg-ice-50' }}">
                            <input type="checkbox" wire:model="is_active" class="w-4 h-4 rounded border-ice-400 text-signal-500 focus:ring-signal-400 cursor-pointer">
                            <span class="text-xs font-extrabold {{ $is_active ? 'text-signal-700' : 'text-gray-500' }}">{{ $is_active ? 'Langsung aktif' : 'Simpan nonaktif' }}</span>
                        </label>
                    @endif
                    <button type="submit" class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-brand-600 text-signal-300 text-base font-extrabold hover:bg-brand-700 shadow-md shadow-brand-900/25 transition-all disabled:opacity-60"
                            wire:loading.attr="disabled" wire:target="create">
                        <span wire:loading.remove wire:target="create" class="inline-flex items-center gap-2">{!! $kic('plus', 'w-4 h-4') !!} Tambah</span>
                        <span wire:loading wire:target="create">Menyimpan...</span>
                    </button>
                </div>
            </form>
        </div>
    </section>

    {{-- FILTER --}}
    <div class="sticky top-24 z-20 rounded-[1.75rem] bg-white/85 backdrop-blur-xl border border-ice-200 shadow-xl shadow-brand-950/5 p-4 ct-rise">
        <div class="flex flex-col lg:flex-row gap-3 lg:items-center">
            <div class="relative flex-1 min-w-0">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-brand-400">{!! $kic('search', 'w-4 h-4') !!}</span>
                <input type="text" wire:model.live.debounce.250ms="search" placeholder="Cari nama divisi, slug, atau deskripsi..."
                       class="w-full pl-11 pr-4 py-3 text-base border border-ice-300 rounded-2xl bg-ice-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25 focus:border-brand-500 transition-all">
            </div>
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                @foreach(['all' => 'Semua', 'active' => 'Aktif', 'inactive' => 'Nonaktif', 'empty' => 'Belum Terpakai'] as $k => $l)
                    <button type="button" wire:click="$set('statusFilter','{{ $k }}')"
                            class="px-3.5 py-2 rounded-full text-xs font-extrabold transition-all {{ $statusFilter === $k ? 'bg-brand-600 text-signal-300 shadow-sm' : 'bg-white border border-ice-300 text-brand-800 hover:bg-ice-100' }}">{{ $l }}</button>
                @endforeach
                <select wire:model.live="sort" class="ct-select pl-3.5 pr-9 py-2 text-xs font-bold text-brand-800 border border-ice-300 rounded-xl bg-white cursor-pointer">
                    <option value="name">Urut: Nama</option>
                    <option value="richest">Urut: Isi terbanyak</option>
                    <option value="value">Urut: Nilai bahan</option>
                    <option value="newest">Urut: Terbaru</option>
                </select>
                @if($hasFilter)
                    <button type="button" wire:click="clearFilters" title="Reset saringan" class="w-9 h-9 flex items-center justify-center rounded-xl bg-red-50 text-red-600 hover:bg-red-100 transition-colors">{!! $kic('undo', 'w-4 h-4') !!}</button>
                @endif
                <span class="text-xs text-gray-500 tabular-nums"><b class="text-brand-950">{{ count($rows) }}</b> divisi</span>
            </div>
        </div>
    </div>

    {{-- DAFTAR --}}
    <div class="rounded-3xl bg-white border border-ice-200 shadow-sm overflow-hidden ct-rise">
        <div class="px-6 py-3.5 bg-ice-50 border-b border-ice-200 flex flex-wrap items-center justify-between gap-2">
            <div class="flex items-center gap-3">
                <span class="w-9 h-9 rounded-xl bg-brand-600 text-signal-300 flex items-center justify-center">{!! $kic('tag', 'w-5 h-5') !!}</span>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-gray-400">Daftar Divisi</p>
                    <h2 class="text-base font-extrabold text-brand-950 leading-tight">Struktur &amp; Keterpakaian</h2>
                </div>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-ice-200 text-brand-800 text-[11px] font-bold">{!! $kic('shield', 'w-3.5 h-3.5') !!} Hapus aman: data dipindah, tidak hilang</span>
        </div>

        <div wire:loading.class="opacity-40 pointer-events-none" class="transition-opacity duration-200">
            @if(count($rows))
                <div class="divide-y divide-ice-100">
                    @foreach($rows as $r)
                        <div class="px-6 py-4 hover:bg-ice-50/60 transition-colors border-l-4 {{ $r['active'] ? ($r['total'] === 0 ? 'border-l-amber-400' : 'border-l-signal-400') : 'border-l-ice-300' }}">
                            <div class="flex flex-col xl:flex-row xl:items-center gap-4">
                                <div class="min-w-0 xl:w-72 shrink-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <p class="text-base font-extrabold text-brand-950 leading-tight">{{ $r['name'] }}</p>
                                        <span class="px-2 py-0.5 rounded text-[9px] font-extrabold uppercase {{ $r['active'] ? 'bg-signal-100 text-signal-700' : 'bg-ice-300 text-gray-600' }}">{{ $r['active'] ? 'Aktif' : 'Nonaktif' }}</span>
                                        @if($r['total'] === 0)<span class="px-2 py-0.5 rounded bg-amber-100 text-amber-800 text-[9px] font-extrabold uppercase">Kosong</span>@endif
                                    </div>
                                    <div class="mt-1 flex items-center gap-1.5">
                                        <code class="text-[11px] font-mono text-gray-500">{{ $r['slug'] }}</code>
                                        <button type="button" onclick="copyText('{{ $r['slug'] }}', this)" title="Salin slug" class="w-6 h-6 flex items-center justify-center rounded text-brand-400 hover:bg-brand-600 hover:text-signal-300 transition-colors">{!! $kic('copy', 'w-3 h-3') !!}</button>
                                    </div>
                                    @if($r['description'])<p class="mt-1 text-[11px] text-gray-500 line-clamp-2">{{ $r['description'] }}</p>@endif
                                </div>

                                <div class="flex-1 grid grid-cols-2 sm:grid-cols-4 gap-3">
                                    <div class="rounded-xl bg-ice-50 border border-ice-200 px-3 py-2">
                                        <p class="text-[9px] font-bold uppercase tracking-wider text-gray-400 inline-flex items-center gap-1">{!! $kic('printer', 'w-3 h-3') !!} Layanan</p>
                                        <p class="text-lg font-extrabold text-brand-950 tabular-nums leading-tight">{{ $r['services'] }}</p>
                                    </div>
                                    <div class="rounded-xl bg-ice-50 border border-ice-200 px-3 py-2">
                                        <p class="text-[9px] font-bold uppercase tracking-wider text-gray-400 inline-flex items-center gap-1">{!! $kic('box', 'w-3 h-3') !!} Bahan</p>
                                        <p class="text-lg font-extrabold text-brand-950 tabular-nums leading-tight">{{ $r['materials'] }}</p>
                                    </div>
                                    <div class="rounded-xl bg-ice-50 border border-ice-200 px-3 py-2">
                                        <p class="text-[9px] font-bold uppercase tracking-wider text-gray-400 inline-flex items-center gap-1">{!! $kic('ruler', 'w-3 h-3') !!} Ukuran</p>
                                        <p class="text-lg font-extrabold text-brand-950 tabular-nums leading-tight">{{ $hasSizeCat ? $r['sizes'] : '—' }}</p>
                                    </div>
                                    <div class="rounded-xl bg-brand-50 border border-brand-200 px-3 py-2">
                                        <p class="text-[9px] font-bold uppercase tracking-wider text-brand-600">Nilai Bahan</p>
                                        <p class="text-sm font-extrabold text-brand-950 tabular-nums leading-tight">Rp {{ number_format($r['stock_value'], 0, ',', '.') }}</p>
                                    </div>
                                </div>

                                <div class="shrink-0 flex items-center gap-1.5">
                                    <button type="button" wire:click="openDetail({{ $r['id'] }})" title="Rincian isi divisi" class="w-9 h-9 flex items-center justify-center rounded-xl bg-ice-100 text-brand-700 hover:bg-ice-200 transition-colors">{!! $kic('eye', 'w-4 h-4') !!}</button>
                                    <button type="button" wire:click="openEdit({{ $r['id'] }})" title="Edit divisi" class="w-9 h-9 flex items-center justify-center rounded-xl bg-brand-600 text-white hover:bg-brand-700 transition-colors">{!! $kic('edit', 'w-4 h-4') !!}</button>
                                    @if($hasActive)
                                        <button type="button" wire:click="toggleActive({{ $r['id'] }})" title="{{ $r['active'] ? 'Nonaktifkan' : 'Aktifkan' }}"
                                                class="w-9 h-9 flex items-center justify-center rounded-xl {{ $r['active'] ? 'bg-amber-50 text-amber-700 hover:bg-amber-100' : 'bg-signal-100 text-signal-700 hover:bg-signal-200' }} transition-colors">
                                            {!! $kic($r['active'] ? 'pause' : 'play', 'w-4 h-4') !!}
                                        </button>
                                    @endif
                                    <button type="button" wire:click="openDelete({{ $r['id'] }})" title="Hapus divisi" class="w-9 h-9 flex items-center justify-center rounded-xl bg-red-50 text-red-600 hover:bg-red-100 transition-colors">{!! $kic('trash', 'w-4 h-4') !!}</button>
                                </div>
                            </div>

                            <div class="mt-3 flex items-center gap-3">
                                <div class="flex-1 h-1.5 rounded-full bg-ice-200 overflow-hidden">
                                    <div class="ct-wide h-full rounded-full bg-gradient-to-r from-brand-600 to-signal-400" style="width: {{ $r['bar'] }}%"></div>
                                </div>
                                <span class="shrink-0 text-[10px] font-bold text-gray-400 tabular-nums">{{ $r['total'] }} entitas · rata-rata jasa Rp {{ number_format($r['avg_price'], 0, ',', '.') }}/m²</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
            {!! count($rows) ? '' : $empty('Tidak ada divisi yang cocok dengan saringan ini.') !!}
        </div>
    </div>

    {{-- MODAL EDIT --}}
    @if($editId)
        <div class="fixed inset-0 z-[75] flex items-end sm:items-center justify-center p-0 sm:p-4 bg-brand-950/60 backdrop-blur-sm" wire:click.self="closeEdit">
            <div class="w-full sm:max-w-lg overflow-hidden rounded-t-3xl sm:rounded-3xl bg-ice-50 shadow-2xl">
                <div class="forest-hero relative overflow-hidden px-6 py-5 flex items-start justify-between gap-4">
                    <div class="forest-dots absolute inset-0 opacity-40"></div>
                    <div class="relative z-10">
                        <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-signal-300">Ubah Divisi</p>
                        <h3 class="mt-1 text-xl font-extrabold text-white tracking-tight">Edit Divisi Produksi</h3>
                        <p class="mt-1 text-sm text-emerald-100/75">Slug menyesuaikan nama secara otomatis.</p>
                    </div>
                    <button type="button" wire:click="closeEdit" class="relative z-10 shrink-0 w-9 h-9 flex items-center justify-center rounded-xl bg-white/10 text-white hover:bg-white/20 transition-colors">{!! $kic('x', 'w-5 h-5') !!}</button>
                </div>
                <form wire:submit="saveEdit" class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-extrabold text-brand-950 mb-1.5">Nama Divisi <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="eName" maxlength="120"
                               class="ct-field w-full px-4 py-3 text-base font-bold border border-ice-300 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25 @error('eName') border-red-400 bg-red-50 @enderror">
                        @error('eName') <p class="mt-1.5 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
                    </div>
                    @if($hasDesc)
                        <div>
                            <label class="block text-sm font-extrabold text-brand-950 mb-1.5">Deskripsi</label>
                            <textarea wire:model="eDescription" rows="3" maxlength="500"
                                      class="ct-scroll w-full px-4 py-3 text-base border border-ice-300 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25 resize-y"></textarea>
                        </div>
                    @endif
                    @if($hasActive)
                        <label class="inline-flex items-center gap-3 px-4 py-3 rounded-xl border-2 cursor-pointer transition-colors {{ $eActive ? 'border-signal-400 bg-signal-100/50' : 'border-ice-300 bg-white' }}">
                            <input type="checkbox" wire:model="eActive" class="w-5 h-5 rounded border-ice-400 text-signal-500 focus:ring-signal-400 cursor-pointer">
                            <span class="text-sm font-extrabold {{ $eActive ? 'text-signal-700' : 'text-gray-500' }}">{{ $eActive ? 'Divisi aktif — bisa dipilih saat membuat pesanan' : 'Nonaktif — disembunyikan dari pilihan baru' }}</span>
                        </label>
                    @endif
                    <div class="flex flex-wrap gap-2.5 pt-1">
                        <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-2xl bg-brand-600 text-signal-300 text-base font-extrabold hover:bg-brand-700 transition-colors disabled:opacity-60"
                                wire:loading.attr="disabled" wire:target="saveEdit">
                            <span wire:loading.remove wire:target="saveEdit">Simpan Perubahan</span>
                            <span wire:loading wire:target="saveEdit">Menyimpan...</span>
                        </button>
                        <button type="button" wire:click="closeEdit" class="px-5 py-3.5 rounded-2xl bg-ice-200 text-brand-800 text-base font-bold hover:bg-ice-300 transition-colors">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- MODAL HAPUS --}}
    @if($deleteId && $deleteTarget)
        <div class="fixed inset-0 z-[76] flex items-end sm:items-center justify-center p-0 sm:p-4 bg-brand-950/60 backdrop-blur-sm" wire:click.self="closeDelete">
            <div class="w-full sm:max-w-lg overflow-hidden rounded-t-3xl sm:rounded-3xl bg-ice-50 shadow-2xl">
                <div class="relative overflow-hidden px-6 py-5 flex items-start justify-between gap-4" style="background: linear-gradient(135deg,#991b1b 0%,#7f1d1d 55%,#450a0a 100%)">
                    <div class="absolute top-0 left-0 right-0 h-1.5" style="background-image: repeating-linear-gradient(45deg,#dc2626 0 9px,#fee2e2 9px 18px)"></div>
                    <div class="relative z-10 pt-1">
                        <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-red-200">Hapus Divisi</p>
                        <h3 class="mt-1 text-xl font-extrabold text-white tracking-tight">{{ $deleteTarget['name'] }}</h3>
                        <p class="mt-1 text-sm text-red-50/85 tabular-nums">{{ $deleteTarget['services'] }} layanan · {{ $deleteTarget['materials'] }} bahan · {{ $deleteTarget['sizes'] }} ukuran</p>
                    </div>
                    <button type="button" wire:click="closeDelete" class="relative z-10 shrink-0 w-9 h-9 flex items-center justify-center rounded-xl bg-white/15 text-white hover:bg-white/25 transition-colors">{!! $kic('x', 'w-5 h-5') !!}</button>
                </div>

                <div class="p-6 space-y-4">
                    @if($deleteTarget['total'] > 0)
                        <div class="rounded-xl bg-amber-50 border border-amber-200 px-4 py-3 flex items-start gap-2.5">
                            <span class="shrink-0 text-amber-700 mt-0.5">{!! $kic('move', 'w-4 h-4') !!}</span>
                            <p class="text-sm text-amber-800">Divisi ini masih dipakai <b>{{ $deleteTarget['total'] }} data</b>. Pilih divisi tujuan agar seluruh layanan, bahan, dan ukuran dipindahkan lebih dulu — tidak ada data yang hilang.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-extrabold text-brand-950 mb-1.5">Pindahkan semua isi ke <span class="text-red-500">*</span></label>
                            <select wire:model="moveTo" class="ct-select w-full pl-4 pr-10 py-3 text-base border border-ice-300 rounded-xl bg-white cursor-pointer @error('moveTo') border-red-400 bg-red-50 @enderror">
                                <option value="">— pilih divisi tujuan —</option>
                                @foreach($moveOptions as $m)
                                    <option value="{{ $m['id'] }}">{{ $m['name'] }} ({{ $m['total'] }} data)</option>
                                @endforeach
                            </select>
                            @error('moveTo') <p class="mt-1.5 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
                        </div>
                    @else
                        <div class="rounded-xl bg-signal-100 border border-signal-300 px-4 py-3 flex items-start gap-2.5">
                            <span class="shrink-0 text-signal-700 mt-0.5">{!! $kic('check', 'w-4 h-4') !!}</span>
                            <p class="text-sm text-brand-900">Divisi ini kosong — tidak ada layanan, bahan, atau ukuran yang terhubung. Aman dihapus permanen.</p>
                        </div>
                    @endif

                    <div class="flex flex-wrap gap-2.5">
                        <button type="button" wire:click="confirmDelete"
                                class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-2xl bg-red-600 text-white text-base font-extrabold hover:bg-red-700 shadow-md shadow-red-500/25 transition-all disabled:opacity-60"
                                wire:loading.attr="disabled" wire:target="confirmDelete">
                            <span wire:loading.remove wire:target="confirmDelete" class="inline-flex items-center gap-2">{!! $kic('trash', 'w-4 h-4') !!} {{ $deleteTarget['total'] > 0 ? 'Pindahkan & Hapus' : 'Hapus Divisi' }}</span>
                            <span wire:loading wire:target="confirmDelete">Memproses...</span>
                        </button>
                        <button type="button" wire:click="closeDelete" class="px-5 py-3.5 rounded-2xl bg-ice-200 text-brand-800 text-base font-bold hover:bg-ice-300 transition-colors">Batal</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL DETAIL --}}
    @if($detail)
        <div class="fixed inset-0 z-[74] flex items-end sm:items-center justify-center p-0 sm:p-4 bg-brand-950/60 backdrop-blur-sm" wire:click.self="closeDetail">
            <div class="w-full sm:max-w-2xl max-h-[90vh] flex flex-col overflow-hidden rounded-t-3xl sm:rounded-3xl bg-ice-50 shadow-2xl">
                <div class="forest-hero relative overflow-hidden px-6 py-5 flex items-start justify-between gap-4 shrink-0">
                    <div class="forest-dots absolute inset-0 opacity-40"></div>
                    <div class="relative z-10 min-w-0">
                        <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-signal-300">Rincian Divisi</p>
                        <h3 class="mt-1 text-xl font-extrabold text-white tracking-tight">{{ $detail['name'] }}</h3>
                        <p class="mt-1 text-sm text-emerald-100/75 font-mono">{{ $detail['slug'] }} · dibuat {{ $detail['created'] ?? '—' }}</p>
                    </div>
                    <button type="button" wire:click="closeDetail" class="relative z-10 shrink-0 w-9 h-9 flex items-center justify-center rounded-xl bg-white/10 text-white hover:bg-white/20 transition-colors">{!! $kic('x', 'w-5 h-5') !!}</button>
                </div>

                <div class="ct-scroll flex-1 overflow-y-auto p-6 space-y-4">
                    @if($detail['description'])
                        <div class="rounded-2xl bg-white border border-ice-200 p-4">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Deskripsi</p>
                            <p class="mt-1 text-sm text-gray-700 whitespace-pre-line">{{ $detail['description'] }}</p>
                        </div>
                    @endif

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        @foreach([['Layanan', $detail['services']], ['Bahan', $detail['materials']], ['Ukuran', $hasSizeCat ? $detail['sizes'] : '—'], ['Stok Bersih', number_format($detail['stock_net'], 2)]] as $k)
                            <div class="rounded-2xl bg-white border border-ice-200 p-4">
                                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">{{ $k[0] }}</p>
                                <p class="mt-1 text-xl font-extrabold text-brand-950 tabular-nums leading-tight">{{ $k[1] }}</p>
                            </div>
                        @endforeach
                    </div>

                    <div class="rounded-2xl bg-white border border-ice-200 overflow-hidden">
                        <p class="px-5 py-3 bg-ice-50 border-b border-ice-200 text-[10px] font-bold uppercase tracking-[0.2em] text-gray-400">Layanan dalam Divisi Ini</p>
                        @if(count($detail['service_list']))
                            <div class="divide-y divide-ice-100">
                                @foreach($detail['service_list'] as $s)
                                    <div class="px-5 py-2.5 flex items-center justify-between gap-3">
                                        <span class="text-sm font-bold text-brand-950 truncate">{{ $s['name'] }}</span>
                                        <span class="shrink-0 text-sm font-extrabold text-brand-700 tabular-nums">Rp {{ number_format($s['price'], 0, ',', '.') }}/m²</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        {!! count($detail['service_list']) ? '' : $empty('Belum ada layanan pada divisi ini.') !!}
                    </div>

                    <div class="rounded-2xl bg-white border border-ice-200 overflow-hidden">
                        <p class="px-5 py-3 bg-ice-50 border-b border-ice-200 text-[10px] font-bold uppercase tracking-[0.2em] text-gray-400">Bahan dalam Divisi Ini</p>
                        @if(count($detail['material_list']))
                            <div class="divide-y divide-ice-100">
                                @foreach($detail['material_list'] as $m)
                                    <div class="px-5 py-2.5 flex items-center justify-between gap-3">
                                        <span class="text-sm font-bold text-brand-950 truncate">{{ $m['name'] }}</span>
                                        <span class="shrink-0 text-right">
                                            <span class="block text-sm font-extrabold tabular-nums {{ $m['net'] <= $m['min'] ? 'text-red-600' : 'text-brand-950' }}">{{ number_format($m['net'], 2) }} {{ $m['unit'] }}</span>
                                            <span class="block text-[10px] text-gray-400 tabular-nums">min {{ number_format($m['min'], 2) }}</span>
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        {!! count($detail['material_list']) ? '' : $empty('Belum ada bahan pada divisi ini.') !!}
                    </div>
                </div>

                <div class="shrink-0 px-6 py-4 border-t border-ice-200 bg-white flex flex-wrap items-center justify-end gap-2.5">
                    <button type="button" wire:click="closeDetail" class="px-5 py-2.5 rounded-xl bg-ice-100 text-brand-800 text-sm font-bold hover:bg-ice-200 transition-colors">Tutup</button>
                    <button type="button" wire:click="openEdit({{ $detail['id'] }})" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-600 text-white text-sm font-bold hover:bg-brand-700 transition-colors">{!! $kic('edit', 'w-4 h-4') !!} Edit Divisi</button>
                </div>
            </div>
        </div>
    @endif
</div>