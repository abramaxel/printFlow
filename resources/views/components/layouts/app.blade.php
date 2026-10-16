<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ordering System</title>
    
    <!-- Load Tailwind CSS & JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Load Livewire Styles -->
    @livewireStyles
</head>
<body class="bg-gray-100 min-h-screen">
    
    <!-- Konten dari Login/Register akan muncul di sini -->
    {{ $slot }}
    
    <!-- Load Livewire Scripts -->
    @livewireScripts
</body>
</html>