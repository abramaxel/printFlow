<div class="min-h-screen flex bg-gray-50 font-sans">
    
    {{-- SISI KIRI: Branding Area (Dark Premium + Gradasi Hijau) --}}
    <div class="hidden lg:flex lg:w-1/2 bg-dark-900 relative overflow-hidden items-center justify-center p-12">
        {{-- Gradasi radial kehijauan (arah berlawanan agar segar) --}}
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_bottom_right,_var(--tw-gradient-stops))] from-emerald-800/60 via-dark-900 to-dark-950"></div>
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-emerald-500/25 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-20 -right-20 w-80 h-80 bg-signal-400/10 rounded-full blur-3xl"></div>

        <div class="relative z-10 max-w-md text-white">
            <div class="flex items-center gap-3 mb-8">
                <div class="w-12 h-12 bg-emerald-500 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/30">
                    <span class="text-2xl">🖨️</span>
                </div>
                <span class="text-2xl font-bold tracking-tight">{{ config('app.brand.name', 'FC Murni Jaya') }}</span>
            </div>

            <h1 class="text-4xl font-extrabold leading-tight mb-6">
                Bergabunglah dengan <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-300 to-signal-300">Ribuan Pelanggan Kami</span>
            </h1>

            <p class="text-gray-300 text-lg leading-relaxed mb-8">
                Buat akun gratis untuk mulai memesan kebutuhan cetak Anda, lacak progres produksi secara mandiri, dan dapatkan invoice digital instan.
            </p>

            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-6">
                <p class="text-sm font-semibold text-emerald-300 mb-3">KEUNTUNGAN AKUN PELANGGAN:</p>
                <ul class="space-y-3 text-sm text-gray-200">
                    <li class="flex items-start gap-2">
                        <span class="text-signal-300 mt-0.5">➤</span> Pantau status pesanan real-time (Antri, Cetak, QC, Kirim)
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-signal-300 mt-0.5">➤</span> Riwayat transaksi & unduh Invoice PDF kapan saja
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-signal-300 mt-0.5">➤</span> Pembayaran fleksibel via Magic Link aman
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- SISI KANAN: Form Register --}}
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12 overflow-y-auto">
        <div class="w-full max-w-md py-8">
            
            {{-- Logo Mobile --}}
            <div class="lg:hidden flex items-center justify-center gap-2 mb-8">
                <span class="text-3xl">🖨️</span>
                <span class="text-2xl font-bold text-gray-900">{{ config('app.brand.name', 'FC Murni Jaya') }}</span>
            </div>

            <div class="mb-8">
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Buat Akun Baru</h2>
                <p class="text-gray-500">Isi data di bawah ini untuk mendaftar sebagai pelanggan.</p>
            </div>

            <form wire:submit="register" class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Lengkap</label>
                    <input wire:model="name" type="text" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent transition-all" placeholder="Contoh: Budi Santoso" required>
                    @error('name') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Alamat Email</label>
                    <input wire:model="email" type="email" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent transition-all" placeholder="nama@contoh.com" required>
                    @error('email') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Password</label>
                        <input wire:model="password" type="password" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent transition-all" placeholder="Min. 8 karakter" required>
                        @error('password') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Konfirmasi</label>
                        <input wire:model="password_confirmation" type="password" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent transition-all" placeholder="Ulangi password" required>
                    </div>
                </div>

                <button 
                    type="submit" 
                    class="w-full flex justify-center items-center gap-2 py-3.5 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-brand-600 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition-all transform hover:-translate-y-0.5 mt-2"
                >
                    <span wire:loading.remove wire:target="register">Daftar Sekarang</span>
                    <svg wire:loading wire:target="register" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </form>

            <div class="mt-8 text-center">
                <p class="text-sm text-gray-600">
                    Sudah punya akun? 
                    <a href="{{ route('login') }}" class="font-bold text-brand-600 hover:text-brand-700 transition-colors">Masuk di sini</a>
                </p>
            </div>
        </div>
    </div>
</div>