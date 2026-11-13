<div class="min-h-screen flex bg-gray-50 font-sans">
    
    {{-- SISI KIRI: Branding Area (Dark Premium + Gradasi Hijau) --}}
    <div class="hidden lg:flex lg:w-1/2 bg-dark-900 relative overflow-hidden items-center justify-center p-12">
        {{-- Gradasi radial kehijauan (khas versi sebelumnya) --}}
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_left,_var(--tw-gradient-stops))] from-emerald-800/60 via-dark-900 to-dark-950"></div>
        <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-emerald-500/25 rounded-full blur-3xl"></div>
        <div class="absolute -top-20 -left-20 w-80 h-80 bg-signal-400/10 rounded-full blur-3xl"></div>

        <div class="relative z-10 max-w-md text-white">
            <div class="flex items-center gap-3 mb-8">
                <div class="w-12 h-12 bg-emerald-500 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/30">
                    <span class="text-2xl">🖨️</span>
                </div>
                <span class="text-2xl font-bold tracking-tight">{{ config('app.brand.name', 'FC Murni Jaya') }}</span>
            </div>

            <h1 class="text-4xl font-extrabold leading-tight mb-6">
                Sistem Informasi <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-300 to-signal-300">Percetakan Terintegrasi</span>
            </h1>

            <p class="text-gray-300 text-lg leading-relaxed mb-8">
                Kelola pesanan, pantau produksi, dan kendalikan stok bahan baku secara real-time dalam satu platform ERP modern.
            </p>

            <div class="space-y-4">
                <div class="flex items-center gap-3 text-gray-200">
                    <div class="w-6 h-6 rounded-full bg-emerald-500/20 flex items-center justify-center text-emerald-300 text-xs">✓</div>
                    <span>Manajemen Order & Invoice Otomatis</span>
                </div>
                <div class="flex items-center gap-3 text-gray-200">
                    <div class="w-6 h-6 rounded-full bg-emerald-500/20 flex items-center justify-center text-emerald-300 text-xs">✓</div>
                    <span>Material Requirement Planning (MRP)</span>
                </div>
                <div class="flex items-center gap-3 text-gray-200">
                    <div class="w-6 h-6 rounded-full bg-emerald-500/20 flex items-center justify-center text-emerald-300 text-xs">✓</div>
                    <span>Dashboard Analitik Bisnis Real-time</span>
                </div>
            </div>
        </div>
    </div>

    {{-- SISI KANAN: Form Area (Clean White) --}}
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12">
        <div class="w-full max-w-md">
            
            {{-- Logo Mobile (Muncul hanya di layar kecil) --}}
            <div class="lg:hidden flex items-center justify-center gap-2 mb-8">
                <span class="text-3xl">🖨️</span>
                <span class="text-2xl font-bold text-gray-900">{{ config('app.brand.name', 'FC Murni Jaya') }}</span>
            </div>

            <div class="mb-8">
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Selamat Datang Kembali</h2>
                <p class="text-gray-500">Silakan masuk ke akun Anda untuk melanjutkan.</p>
            </div>

            @if (session('error'))
                <div class="mb-6 p-4 text-sm text-red-700 bg-red-50 border border-red-200 rounded-xl flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ session('error') }}
                </div>
            @endif

            <form wire:submit="login" class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5" for="email">Alamat Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                        </div>
                        <input 
                            wire:model="email" type="email" id="email"
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent transition-all placeholder-gray-400"
                            placeholder="nama@contoh.com" required
                        >
                    </div>
                    @error('email') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-sm font-semibold text-gray-700" for="password">Password</label>
                        <a href="#" class="text-sm font-medium text-brand-600 hover:text-brand-700">Lupa password?</a>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <input 
                            wire:model="password" type="password" id="password"
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent transition-all placeholder-gray-400"
                            placeholder="••••••••" required
                        >
                    </div>
                    @error('password') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center">
                    <input wire:model="remember" type="checkbox" id="remember" class="h-4 w-4 text-brand-600 focus:ring-brand-500 border-gray-300 rounded cursor-pointer">
                    <label for="remember" class="ml-2 block text-sm text-gray-600 cursor-pointer">Ingat saya selama 30 hari</label>
                </div>

                <button 
                    type="submit" 
                    class="w-full flex justify-center items-center gap-2 py-3.5 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-brand-600 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition-all transform hover:-translate-y-0.5"
                >
                    <span wire:loading.remove wire:target="login">Masuk ke Dashboard</span>
                    <svg wire:loading wire:target="login" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </form>

            <div class="mt-8 text-center">
                <p class="text-sm text-gray-600">
                    Belum memiliki akun? 
                    <a href="{{ route('register') }}" class="font-bold text-brand-600 hover:text-brand-700 transition-colors">Daftar sebagai Pelanggan</a>
                </p>
            </div>
            
            <p class="mt-12 text-center text-xs text-gray-400">
                &copy; {{ date('Y') }} {{ config('app.brand.name', 'FC Murni Jaya') }}. Hak Cipta Dilindungi.
            </p>
        </div>
    </div>
</div>