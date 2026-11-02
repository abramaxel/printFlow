<div class="relative" x-data="{ open: @entangle('isOpen') }" @click.away="open = false">
    {{-- Tombol Lonceng --}}
    <button @click="open = !open; if(open) $wire.loadNotifications()" 
        class="relative p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
        </svg>
        
        {{-- Badge Unread --}}
        @if($unreadCount > 0)
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold px-1.5 py-0.5 rounded-full min-w-[18px] text-center">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    {{-- Dropdown Notifikasi --}}
    <div x-show="open" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-2xl border border-gray-200 z-50 overflow-hidden"
        style="display: none;">
        
        {{-- Header --}}
        <div class="px-4 py-3 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <h3 class="text-sm font-bold text-gray-800">🔔 Notifikasi</h3>
            @if($unreadCount > 0)
                <button wire:click="markAllAsRead" class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                    Tandai semua dibaca
                </button>
            @endif
        </div>

        {{-- List Notifikasi --}}
        <div class="max-h-96 overflow-y-auto">
            @forelse($notifications as $notification)
                @php $data = $notification->data; @endphp
                <a href="{{ $data['url'] ?? '#' }}" 
                    wire:click="markAsRead('{{ $notification->id }}')"
                    class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-50 transition {{ $notification->read_at ? 'opacity-60' : 'bg-blue-50/30' }}">
                    <div class="flex items-start gap-3">
                        <span class="text-xl flex-shrink-0">{{ $data['icon'] ?? '📌' }}</span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ $data['title'] ?? 'Notifikasi' }}</p>
                            <p class="text-xs text-gray-600 mt-0.5 line-clamp-2">{{ $data['message'] ?? '' }}</p>
                            <p class="text-[10px] text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                        @if(!$notification->read_at)
                            <span class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0 mt-1.5"></span>
                        @endif
                    </div>
                </a>
            @empty
                <div class="px-4 py-8 text-center text-gray-400">
                    <p class="text-3xl mb-2">🎉</p>
                    <p class="text-sm">Tidak ada notifikasi baru</p>
                </div>
            @endforelse
        </div>

        {{-- Footer --}}
        @if($notifications->count() > 0)
            <div class="px-4 py-2 border-t border-gray-100 bg-gray-50 text-center">
                <span class="text-xs text-gray-500">{{ $notifications->count() }} notifikasi terakhir</span>
            </div>
        @endif
    </div>
</div>