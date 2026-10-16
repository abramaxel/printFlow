<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8">
        <h2 class="text-2xl font-bold text-center mb-6 text-gray-800">Login</h2>

        <form wire:submit="login">
            <!-- Email -->
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                    Email
                </label>
                <input 
                    wire:model="email" 
                    type="email" 
                    id="email" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="john@example.com"
                >
                @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                    Password
                </label>
                <input 
                    wire:model="password" 
                    type="password" 
                    id="password" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="••••••••"
                >
                @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Remember Me -->
            <div class="mb-6 flex items-center">
                <input 
                    wire:model="remember" 
                    type="checkbox" 
                    id="remember" 
                    class="mr-2"
                >
                <label for="remember" class="text-gray-700 text-sm">
                    Ingat saya
                </label>
            </div>

            <!-- Submit Button -->
            <button 
                type="submit" 
                class="w-full bg-blue-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-blue-600 transition"
            >
                Login
            </button>
        </form>

        <p class="text-center text-gray-600 text-sm mt-4">
            Belum punya akun? 
            <a href="{{ route('register') }}" class="text-blue-500 hover:underline">Register di sini</a>
        </p>
    </div>
</div>