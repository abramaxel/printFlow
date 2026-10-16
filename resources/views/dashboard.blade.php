<x-layouts.app>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-4">Dashboard</h1>
        <p class="text-gray-600">Selamat datang, {{ auth()->user()->name }}!</p>
        <p class="text-gray-600">Email: {{ auth()->user()->email }}</p>
        <p class="text-gray-600">Role: {{ auth()->user()->role }}</p>
        
        <form action="{{ route('logout') }}" method="POST" class="mt-6">
            @csrf
            <button 
                type="submit" 
                class="bg-red-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-red-600 transition"
            >
                Logout
            </button>
        </form>
    </div>
</x-layouts.app>