<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Antrean Dekanat - Dashboard Admin</title>

    @vite('resources/css/app.css')

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        body {
            background-color: #f0f4f8;
        }
    </style>
</head>

<body class="p-8">

    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Dashboard Admin</h1>
            <p class="text-gray-600">Kelola sistem antrean dekanat</p>
        </div>

        <form action="{{ route('logout') }}" method="POST" class="inline">
            @csrf
            <button type="submit"
                class="px-4 py-2 rounded-lg bg-red-600 text-white font-medium hover:bg-red-700 transition duration-200 flex items-center gap-2">
                <i class="fa fa-sign-out-alt"></i> Logout
            </button>
        </form>
    </div>

    {{-- Dashboard Content --}}
    <div class="min-h-screen bg-white p-8">
        <div class="max-w-7xl mx-auto space-y-8">

            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div
                    class="bg-white p-6 rounded-xl shadow-md flex items-center gap-4 hover:scale-105 hover:shadow-lg transition-transform">
                    <i class="fa-solid fa-users text-4xl text-blue-600"></i>
                    <div>
                        <p class="text-3xl font-bold text-gray-900">{{ $data['totalUsers'] }}</p>
                        <p class="text-sm text-gray-500">Total Pengguna</p>
                    </div>
                </div>

                <div
                    class="bg-white p-6 rounded-xl shadow-md flex items-center gap-4 hover:scale-105 hover:shadow-lg transition-transform">
                    <i class="fa-solid fa-signal text-4xl text-green-600"></i>
                    <div>
                        <p class="text-3xl font-bold text-gray-900">{{ $data['activeQueues'] }}</p>
                        <p class="text-sm text-gray-500">Antrean Aktif</p>
                    </div>
                </div>

                <div
                    class="bg-white p-6 rounded-xl shadow-md flex items-center gap-4 hover:scale-105 hover:shadow-lg transition-transform">
                    <i class="fa-solid fa-calendar-check text-4xl text-orange-600"></i>
                    <div>
                        <p class="text-3xl font-bold text-gray-900">{{ $data['completedQueues'] }}</p>
                        <p class="text-sm text-gray-500">Selesai Hari Ini</p>
                    </div>
                </div>

                <div
                    class="bg-white p-6 rounded-xl shadow-md flex items-center gap-4 hover:scale-105 hover:shadow-lg transition-transform">
                    <i class="fa-solid fa-layer-group text-4xl text-purple-600"></i>
                    <div>
                        <p class="text-3xl font-bold text-gray-900">{{ $data['totalServices'] }}</p>
                        <p class="text-sm text-gray-500">Kategori Layanan</p>
                    </div>
                </div>
            </div>

            {{-- Users Section --}}
            <div class="flex flex-col sm:flex-row justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-900 mb-2 sm:mb-0">Pengguna</h2>
                <div class="flex items-center gap-2">
                    <input type="text" id="searchUser" placeholder="Cari nama atau kode..."
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition w-60">
                    <button onclick="window.location='{{ route('users.create') }}'"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors flex items-center gap-2">
                        <i class="fa-solid fa-plus"></i> Tambah Pengguna
                    </button>
                </div>
            </div>

            {{-- Tabs Navigation --}}
            <div class="mb-4 border-b border-gray-200">
                <nav class="-mb-px flex space-x-4">
                    <button class="tab-btn py-2 px-4 text-sm font-medium text-blue-600 border-b-2 border-blue-600"
                        data-tab="admin">Admin</button>
                    <button class="tab-btn py-2 px-4 text-sm font-medium text-gray-600 hover:text-blue-600"
                        data-tab="dosen">Dosen</button>
                    <button class="tab-btn py-2 px-4 text-sm font-medium text-gray-600 hover:text-blue-600"
                        data-tab="mahasiswa">Mahasiswa</button>
                    <button class="tab-btn py-2 px-4 text-sm font-medium text-gray-600 hover:text-blue-600"
                        data-tab="pejabat">Pejabat</button>
                </nav>
            </div>

            {{-- Tab Content --}}
            <div>
                @foreach (['admin', 'dosen', 'mahasiswa', 'pejabat'] as $tabId)
                    <div class="tab-content {{ $loop->first ? '' : 'hidden' }}" id="{{ $tabId }}">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 user-item">
                            @foreach ($data['users'] as $user)
                                @if ($user->role === $tabId)
                                    <div
                                        class="p-4 border border-gray-200 rounded-lg flex flex-col justify-between user-card">
                                        <div>
                                            <p class="font-semibold text-gray-900 user-name">{{ $user->name }}</p>
                                            <p class="text-sm text-gray-500">{{ $user->kode }}</p>
                                            <span
                                                class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-green-100 text-green-800 mt-2 inline-block">
                                                {{ ucfirst($tabId) }} - Aktif
                                            </span>
                                        </div>
                                        <div class="flex justify-end mt-2 space-x-2">
                                            <a href="{{ route('users.edit', $user->kode) }}"
                                                class="p-2 rounded hover:bg-blue-100 transition-colors">
                                                <i class="fa-solid fa-pen text-blue-600"></i>
                                            </a>
                                            <form action="{{ route('users.destroy', $user->kode) }}" method="POST"
                                                onsubmit="return confirm('Yakin hapus {{ ucfirst($tabId) }} ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-gray-500 hover:text-red-600">
                                                    <i class="fa-solid fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Service Categories --}}
            <div class="flex flex-col sm:flex-row justify-between items-center mt-8 mb-4">
                <h2 class="text-xl font-semibold text-gray-900 mb-2 sm:mb-0">Kategori Layanan</h2>
                <div class="flex items-center gap-2">
                    <input type="text" id="searchService" placeholder="Cari layanan..."
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition w-60">
                    <button onclick="window.location='{{ route('services.create') }}'"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors flex items-center gap-2">
                        <i class="fa-solid fa-plus"></i> Tambah Service
                    </button>
                </div>
            </div>

            <div class="space-y-4" id="serviceList">
                @foreach ($data['service'] as $service)
                    <div class="flex justify-between items-center p-4 border border-gray-200 rounded-lg service-card">
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900 service-name">{{ $service->nama_layanan }}</p>
                            <p class="text-sm text-gray-500">{{ $service->deskripsi }}</p>
                            <p class="text-xs text-gray-500">
                                ⏱️ Est: {{ $service->est }} menit
                            </p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('services.edit', $service->id) }}"
                                class="text-gray-500 hover:text-blue-600 transition-colors">
                                <i class="fa-solid fa-edit"></i>
                            </a>
                            <form action="{{ route('services.destroy', $service->id) }}" method="POST"
                                onsubmit="return confirm('Yakin ingin menghapus service ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-gray-500 hover:text-red-600 transition-colors">
                                    <i class="fa-solid fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>


            {{-- Reports & Analytics --}}
            <div class="bg-white p-6 rounded-xl shadow-md mt-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Laporan & Analitik</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="{{ route('reports.daily') }}"
                        class="bg-gray-100 p-6 rounded-xl shadow-sm hover:bg-gray-200 transition-colors text-center font-medium text-gray-700">
                        <i class="fa-solid fa-file-alt text-3xl mb-2"></i>
                        <p>Laporan Harian</p>
                    </a>

                    <a href="{{ route('reports.services') }}"
                        class="bg-gray-100 p-6 rounded-xl shadow-sm hover:bg-gray-200 transition-colors text-center font-medium text-gray-700">
                        <i class="fa-solid fa-chart-bar text-3xl mb-2"></i>
                        <p>Statistik Layanan</p>
                    </a>

                    <a href="{{ route('reports.rekap') }}"
                        class="bg-gray-100 p-6 rounded-xl shadow-sm hover:bg-gray-200 transition-colors text-center font-medium text-gray-700">
                        <i class="fa-solid fa-file-invoice text-3xl mb-2"></i>
                        <p>Rekap Laporan</p>
                    </a>
                </div>
            </div>

        </div>
    </div>

    {{-- Script: Tabs --}}
    <script>
        // === Tab Navigation ===
        const tabs = document.querySelectorAll('.tab-btn');
        const contents = document.querySelectorAll('.tab-content');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                tabs.forEach(t => t.classList.remove('border-b-2', 'border-blue-600', 'text-blue-600'));
                contents.forEach(c => c.classList.add('hidden'));

                tab.classList.add('border-b-2', 'border-blue-600', 'text-blue-600');
                document.getElementById(tab.dataset.tab).classList.remove('hidden');
            });
        });

        // === Search Users ===
        document.getElementById('searchUser').addEventListener('input', function() {
            const query = this.value.toLowerCase();
            document.querySelectorAll('.user-card').forEach(card => {
                const name = card.querySelector('.user-name').textContent.toLowerCase();
                const kode = card.querySelector('p.text-sm').textContent.toLowerCase();
                card.style.display = (name.includes(query) || kode.includes(query)) ? '' : 'none';
            });
        });

        // === Search Services ===
        document.getElementById('searchService').addEventListener('input', function() {
            const query = this.value.toLowerCase();
            document.querySelectorAll('.service-card').forEach(card => {
                const name = card.querySelector('.service-name').textContent.toLowerCase();
                const desc = card.querySelector('p.text-sm').textContent.toLowerCase();
                card.style.display = (name.includes(query) || desc.includes(query)) ? '' : 'none';
            });
        });
    </script>


</body>

</html>
