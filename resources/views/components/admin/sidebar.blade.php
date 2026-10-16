<aside class="fixed inset-y-0 left-0 z-20 flex flex-col w-64 bg-white border-r border-gray-200 transition-transform transform -translate-x-full md:translate-x-0"
       x-data="{ open: false }" 
       :class="open ? 'translate-x-0' : '-translate-x-full'"
       @click.away="open = false">
    
    <!-- Logo Area -->
    <div class="flex items-center justify-center h-16 border-b border-gray-200">
        <h1 class="text-xl font-bold text-gray-800">🖨️ Print<span class="text-blue-600">Order</span></h1>
    </div>

    <!-- Menu Navigasi -->
    <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
        
        <!-- Dashboard -->
        @php $isActive = request()->routeIs('admin.dashboard'); @endphp
        <a href="{{ route('admin.dashboard') }}" 
           class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 group
           {{ $isActive ? 'bg-blue-50 text-blue-700 border-l-4 border-blue-600' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <svg class="w-5 h-5 mr-3 {{ $isActive ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' }}" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"></path>
                <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"></path>
            </svg>
            Dashboard
        </a>

        <!-- Manajemen Pesanan -->
        <div class="pt-4">
            <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Transaksi</p>
            
            @php $isActiveOrders = request()->routeIs('admin.orders'); @endphp
            <a href="{{ route('admin.orders') }}" 
               class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 group
               {{ $isActiveOrders ? 'bg-blue-50 text-blue-700 border-l-4 border-blue-600' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <svg class="w-5 h-5 mr-3 {{ $isActiveOrders ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' }}" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"></path>
                </svg>
                Daftar Pesanan
            </a>
            
            @php $isActiveCreate = request()->routeIs('admin.orders.create'); @endphp
            <a href="{{ route('admin.orders.create') }}" 
               class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 group
               {{ $isActiveCreate ? 'bg-blue-50 text-blue-700 border-l-4 border-blue-600' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <svg class="w-5 h-5 mr-3 {{ $isActiveCreate ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Buat Pesanan Baru
            </a>
        </div>

        <!-- Master Data -->
        <div class="pt-4">
            <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Master Data</p>
            
            @php $isActiveServices = request()->routeIs('admin.services'); @endphp
            <a href="{{ route('admin.services') }}" 
               class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 group
               {{ $isActiveServices ? 'bg-blue-50 text-blue-700 border-l-4 border-blue-600' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <svg class="w-5 h-5 mr-3 {{ $isActiveServices ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' }}" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                </svg>
                Kelola Layanan
            </a>

            @php $isActiveMaterials = request()->routeIs('admin.materials'); @endphp
            <a href="{{ route('admin.materials') }}" 
               class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 group
               {{ $isActiveMaterials ? 'bg-blue-50 text-blue-700 border-l-4 border-blue-600' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <svg class="w-5 h-5 mr-3 {{ $isActiveMaterials ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' }}" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                </svg>
                Kelola Bahan
            </a>

            @php $isActiveSizes = request()->routeIs('admin.sizes'); @endphp
            <a href="{{ route('admin.sizes') }}" 
               class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 group
               {{ $isActiveSizes ? 'bg-blue-50 text-blue-700 border-l-4 border-blue-600' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <svg class="w-5 h-5 mr-3 {{ $isActiveSizes ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' }}" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm0 6a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1v-2zm0 6a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1v-2z" clip-rule="evenodd"></path>
                </svg>
                Kelola Ukuran
            </a>
        </div>

        <!-- Menu Khusus Super Admin (Owner / CRM) -->
        @if(auth()->check() && auth()->user()->isSuperAdmin())
            <div class="pt-4">
                <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Owner & CRM</p>
                
                @php $isActiveReports = request()->routeIs('admin.reports'); @endphp
                <a href="#" 
                   class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 group
                   {{ $isActiveReports ? 'bg-blue-50 text-blue-700 border-l-4 border-blue-600' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="w-5 h-5 mr-3 {{ $isActiveReports ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' }}" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"></path>
                        <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"></path>
                    </svg>
                    Laporan & Statistik
                </a>
                
                @php $isActiveCustomers = request()->routeIs('admin.customers'); @endphp
                <a href="#" 
                   class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 group
                   {{ $isActiveCustomers ? 'bg-blue-50 text-blue-700 border-l-4 border-blue-600' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="w-5 h-5 mr-3 {{ $isActiveCustomers ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' }}" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                    </svg>
                    Manajemen Pelanggan
                </a>
            </div>
        @endif

    </nav>
</aside>

<!-- Overlay untuk Mobile saat Sidebar terbuka -->
<div x-show="open" @click="open = false" class="fixed inset-0 z-10 bg-black bg-opacity-50 md:hidden"></div>