<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class Counter extends Component
{
    // VARIABEL: Ini adalah "data" yang bisa berubah-ubah
    // Awalnya 0, nanti bisa ditambah atau dikurang
    public $count = 0;
    
    // FUNGSI: Ini adalah "aksi" yang bisa dipanggil dari HTML
    // Ketika dipanggil, $count akan ditambah 1
    public function increment()
    {
        $this->count++;
    }
    
    // FUNGSI: Ketika dipanggil, $count akan dikurang 1
    public function decrement()
    {
        $this->count--;
    }
    
    // FUNGSI: Ini WAJIB ada, untuk menentukan file HTML mana yang ditampilkan
    public function render()
    {
        return view('livewire.counter');
    }
}