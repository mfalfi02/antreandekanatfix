<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | Antrean Dekanat</title>
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
    @include('partials.pwa-head')
</head>

<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100">
    <div class="min-h-screen flex flex-col">
        {{-- Navbar --}}
        <header class="bg-white dark:bg-gray-800 shadow p-4 flex justify-between items-center">
            <h1 class="font-bold text-xl">Antrean Dekanat</h1>
            <nav>
                <ul class="flex gap-4 items-center">
                    {{-- Tombol Kembali ke Dashboard --}}
                    <li>
                        <a href="{{ route('adm') }}"
                            class="px-4 py-2 rounded-lg bg-blue-600 text-white font-medium hover:bg-blue-700 transition duration-200 flex items-center gap-2">
                            <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
                        </a>
                    </li>
                </ul>
            </nav>
        </header>

        {{-- Main Content --}}
        <main class="flex-1 p-6">
            @yield('content')
        </main>

        {{-- Footer --}}
        <footer class="bg-gray-100 dark:bg-gray-700 p-4 text-center text-sm">
            &copy; {{ date('Y') }} Antrean Dekanat - Universitas Widya Dharma
        </footer>
    </div>

    {{-- Notifikasi --}}
    @if (session('success'))
        <div id="notification"
            class="fixed top-4 right-4 bg-green-500 text-white px-4 py-3 rounded shadow-md flex items-center gap-2 transition-transform transform">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="ml-2 text-white hover:text-gray-200">
                &times;
            </button>
        </div>

        <script>
            // Auto-hide setelah 3 detik
            setTimeout(() => {
                const notif = document.getElementById('notification');
                if (notif) notif.remove();
            }, 3000);
        </script>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @include('partials.pwa-scripts')

</body>

</html>
