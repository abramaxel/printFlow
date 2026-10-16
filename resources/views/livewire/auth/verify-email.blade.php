<x-layouts.app>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-lg shadow-md">
            <div class="text-center">
                <h2 class="text-2xl font-bold text-gray-900">Verifikasi Email Anda</h2>
                <p class="mt-2 text-sm text-gray-600">
                    Sebelum melanjutkan, silakan periksa email Anda untuk link verifikasi.
                </p>
            </div>

            @if (session('message'))
                <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50">
                    {{ session('message') }}
                </div>
            @endif

            <form method="POST" action="{{ route('verification.send') }}" class="mt-4 space-y-4">
                @csrf
                <div>
                    <x-ui.button type="submit" variant="primary" class="w-full justify-center">
                        Kirim Ulang Email Verifikasi
                    </x-ui.button>
                </div>
            </form>

            <div class="text-center mt-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-blue-600 hover:underline">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>