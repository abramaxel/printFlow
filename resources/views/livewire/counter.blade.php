<div>
    <div class="max-w-md mx-auto mt-10 p-6 bg-white rounded-lg shadow-lg">
        <h2 class="text-2xl font-bold mb-4 text-center text-gray-800">Counter Test</h2>
        
        <!-- TAMPILKAN VARIABEL $count DARI PHP -->
        <div class="text-center mb-6">
            <span class="text-5xl font-bold text-blue-600">{{ $count }}</span>
        </div>
        
        <!-- TOMBOL YANG MEMANGGIL FUNGSI DARI PHP -->
        <div class="flex gap-3 justify-center">
            <!-- wire:click="decrement" = Ketika diklik, panggil fungsi decrement() di PHP -->
            <button 
                wire:click="decrement" 
                class="px-6 py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 transition font-semibold"
            >
                - Kurang
            </button>
            
            <!-- wire:click="increment" = Ketika diklik, panggil fungsi increment() di PHP -->
            <button 
                wire:click="increment" 
                class="px-6 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition font-semibold"
            >
                + Tambah
            </button>
        </div>
    </div>
</div>