<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin Dashboard' }} - Ordering System</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-50">

    <div class="flex h-screen overflow-hidden">
        
        <!-- Sidebar Navigasi -->
        <x-admin.sidebar />

        <!-- Wrapper untuk Konten Utama -->
        <div class="flex-1 flex flex-col overflow-hidden md:ml-64">
            
            <!-- Topbar / Navbar Atas -->
            <x-admin.topbar />

            <!-- Area Konten Halaman (Dashboard, CRUD, dll) -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6">
                {{ $slot }}
            </main>
            
        </div>
    </div>

    @livewireScripts
</body>
</html>