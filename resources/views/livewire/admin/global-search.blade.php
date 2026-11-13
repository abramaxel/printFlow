<div class="relative" x-data>
    <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </div>
        <input
            wire:model.live.debounce.300ms="q"
            type="text"
            class="block w-full pl-10 pr-3 py-2 border-0 rounded-xl bg-ice-100 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:bg-white sm:text-sm transition-all"
            placeholder="Cari invoice, pelanggan, material, supplier..."
        >
        <div wire:loading wire:target="q" class="absolute inset-y-0 right-0 pr-3 flex items-center">
            <svg class="w-4 h-4 animate-spin text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
        </div>
    </div>

    @if($open)
        <div @click.away="$wire.set('q', '')" class="absolute left-0 right-0 mt-2 bg-white rounded-xl shadow-lg border border-ice-200 max-h-96 overflow-y-auto z-50">

            {{-- PESANAN --}}
            @if($results['orders']->count())
                <div class="px-4 pt-3 pb-1 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Pesanan / Invoice</div>
                @foreach($results['orders'] as $o)
                    <a href="{{ route('admin.orders.show', $o->id) }}" class="flex items-center justify-between px-4 py-2.5 hover:bg-ice-50 transition-colors">
                        <span class="text-sm font-bold text-brand-950">{{ $o->invoice_number }}</span>
                        <span class="text-xs text-gray-500 truncate ml-3">{{ $o->user->name ?? '-' }}</span>
                    </a>
                @endforeach
            @endif

            {{-- PELANGGAN --}}
            @if($results['customers']->count())
                <div class="px-4 pt-3 pb-1 text-[10px] font-bold text-gray-400 uppercase tracking-wider border-t border-ice-100">Pelanggan</div>
                @foreach($results['customers'] as $c)
                    <a href="{{ route('admin.customers') }}" class="flex items-center justify-between px-4 py-2.5 hover:bg-ice-50 transition-colors">
                        <span class="text-sm font-medium text-brand-950 truncate">{{ $c->name }}</span>
                        <span class="text-xs text-gray-500 truncate ml-3">{{ $c->email }}</span>
                    </a>
                @endforeach
            @endif

            {{-- MATERIAL --}}
            @if($results['materials']->count())
                <div class="px-4 pt-3 pb-1 text-[10px] font-bold text-gray-400 uppercase tracking-wider border-t border-ice-100">Material</div>
                @foreach($results['materials'] as $m)
                    <a href="{{ route('admin.materials') }}" class="flex items-center justify-between px-4 py-2.5 hover:bg-ice-50 transition-colors">
                        <span class="text-sm font-medium text-brand-950 truncate">{{ $m->name }}</span>
                        <span class="text-xs text-gray-500 shrink-0 ml-3">Stok {{ number_format((float)$m->stock, 1) }} {{ $m->unit }}</span>
                    </a>
                @endforeach
            @endif

            {{-- SUPPLIER --}}
            @if($results['suppliers']->count())
                <div class="px-4 pt-3 pb-1 text-[10px] font-bold text-gray-400 uppercase tracking-wider border-t border-ice-100">Supplier</div>
                @foreach($results['suppliers'] as $s)
                    <a href="{{ route('admin.suppliers') }}" class="flex items-center justify-between px-4 py-2.5 hover:bg-ice-50 transition-colors">
                        <span class="text-sm font-medium text-brand-950 truncate">{{ $s->name }}</span>
                        <span class="text-xs text-gray-500 truncate ml-3">{{ $s->phone ?? '' }}</span>
                    </a>
                @endforeach
            @endif

            {{-- KOSONG --}}
            @if($results['orders']->isEmpty() && $results['customers']->isEmpty() && $results['materials']->isEmpty() && $results['suppliers']->isEmpty())
                <div class="px-4 py-6 text-center text-sm text-gray-400">Tidak ada hasil untuk &ldquo;{{ $term }}&rdquo;</div>
            @endif
        </div>
    @endif
</div>