@props([
    'user'  => null,
    'size'  => 'md',
    'shape' => 'rounded-full',
    'ring'  => false,
])

@php
    $map = [
        'xs'  => 'w-8 h-8 text-xs',
        'sm'  => 'w-9 h-9 text-sm',
        'md'  => 'w-10 h-10 text-base',
        'lg'  => 'w-14 h-14 text-xl',
        'xl'  => 'w-20 h-20 text-2xl',
        '2xl' => 'w-24 h-24 text-3xl',
    ];
    $sz      = $map[$size] ?? $map['md'];
    $name    = trim((string) ($user->name ?? ''));
    $initial = mb_strtoupper(mb_substr($name !== '' ? $name : '?', 0, 1));
    $path    = (string) ($user->avatar ?? '');
    $url     = null;

    if ($path !== '' && !empty($user->id)) {
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            $url = $path;
        } elseif (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
            $url = route('media.avatar', ['user' => $user->id, 'v' => substr(md5($path), 0, 8)]);
        }
    }

    $ringClass = $ring ? ' ring-2 ring-signal-400/40' : '';
@endphp

@if($url)
    <img src="{{ $url }}" alt="Foto {{ $name }}" loading="lazy"
         {{ $attributes->merge(['class' => $sz . ' ' . $shape . ' object-cover shrink-0' . $ringClass]) }}>
@else
    <div aria-hidden="true"
         {{ $attributes->merge(['class' => $sz . ' ' . $shape . ' bg-gradient-to-br from-emerald-400 to-signal-400 text-brand-950 flex items-center justify-center font-extrabold shrink-0 select-none' . $ringClass]) }}>{{ $initial }}</div>
@endif