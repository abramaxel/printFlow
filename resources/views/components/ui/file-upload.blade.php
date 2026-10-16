@props([
    'label' => '',
    'name' => '',
    'wireModel' => null,
    'error' => null
])

<div class="mb-4">
    @if($label)
        <label class="block mb-2 text-sm font-medium text-gray-900">{{ $label }}</label>
    @endif
    
    <div class="flex items-center justify-center w-full">
        <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                <svg class="w-8 h-8 mb-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                </svg>
                <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Klik untuk upload</span> atau drag & drop</p>
                <p class="text-xs text-gray-500">SVG, PNG, JPG, PDF (MAX. 10MB)</p>
            </div>
            <input type="file" class="hidden" @if($wireModel) wire:model="{{ $wireModel }}" @endif />
        </label>
    </div>
    
    @if($error)
        <p class="mt-1 text-sm text-red-600">{{ $error }}</p>
    @endif
    
    @if($wireModel && $this->$wireModel)
        <p class="mt-2 text-sm text-green-600 flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            File terpilih: {{ $this->$wireModel->getClientOriginalName() }}
        </p>
    @endif
</div>