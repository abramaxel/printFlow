@props([
    'label' => '',
    'name' => '',
    'options' => [],
    'wireModel' => null,
    'error' => null
])

<div class="mb-4">
    @if($label)
        <label for="{{ $name }}" class="block mb-2 text-sm font-medium text-gray-900">{{ $label }}</label>
    @endif
    
    <select 
        id="{{ $name }}" 
        name="{{ $name }}"
        @if($wireModel) wire:model.live="{{ $wireModel }}" @endif
        {{ $attributes->merge(['class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5']) }}
    >
        <option value="">-- Pilih {{ $label }} --</option>
        @foreach($options as $option)
            <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
        @endforeach
    </select>
    
    @if($error)
        <p class="mt-1 text-sm text-red-600">{{ $error }}</p>
    @endif
</div>