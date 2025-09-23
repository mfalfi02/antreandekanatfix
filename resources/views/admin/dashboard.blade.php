<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Antrean Dekanat - Dashboard Admin</title>
    @vite('resources/css/app.css')
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

</form>

</div>
    <style>
        body {
            background-color: #f0f4f8; /* Tailwind equivalent: bg-gray-100 or a custom shade */
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" xintegrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body class="p-8">

    @php
    $allUsers = collect();
    if(isset($mahasiswas)) $allUsers = $allUsers->merge($mahasiswas);
    if(isset($dosens)) $allUsers = $allUsers->merge($dosens);
    if(isset($admins)) $allUsers = $allUsers->merge($admins);
@endphp

{{-- @foreach ($allUsers as $user)
<div class="flex justify-between items-center p-4 border border-gray-200 rounded-lg mb-2">
    <div class="flex-1">
        <p class="font-semibold text-gray-900">{{ $user->name }}</p>
        <p class="text-sm text-gray-500">
            @if(isset($user->email))
                {{ $user->email }}
            @elseif(isset($user->nim))
                NIM: {{ $user->nim }}
            @elseif(isset($user->kode_dosen))
                Kode Dosen: {{ $user->kode_dosen }}
            @endif
        </p>

        @php
            $role = $user->role ?? (isset($user->nim) ? 'Mahasiswa' : (isset($user->kode_dosen) ? 'Dosen' : 'Admin'));
            $status = $user->status ?? 'Tidak Aktif';
            $badgeClass = $status == 'Aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
        @endphp

        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full {{ $badgeClass }} mt-1 inline-block">
            {{ $role }} - {{ $status }}
        </span>
    </div>

    <div class="flex items-center space-x-2">
        <button data-id="{{ $user->id ?? '' }}" class="edit-btn text-gray-500 hover:text-blue-600 transition-colors">
            <i class="fa-solid fa-edit"></i>
        </button>
        <button data-id="{{ $user->id ?? '' }}" class="delete-btn text-gray-500 hover:text-red-600 transition-colors">
            <i class="fa-solid fa-trash-alt"></i>
        </button>
    </div>
</div>
@endforeach --}}



    <div class="min-h-screen bg-white p-8">
        <div class="max-w-7xl mx-auto space-y-8">
            <!-- Header -->
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-900">Dashboard Admin</h1>
                <p class="text-gray-600">Kelola sistem antrean dekanat</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white p-6 rounded-xl shadow-md flex items-center gap-4 transition-transform transform hover:scale-105 hover:shadow-lg">
                    <i class="fa-solid fa-users text-4xl text-blue-600"></i>
                    <div>
                        <p class="text-3xl font-bold text-gray-900">{{ $totalUsers }}</p>
                        <p class="text-sm text-gray-500">Total Pengguna</p>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-md flex items-center gap-4 transition-transform transform hover:scale-105 hover:shadow-lg">
                    <i class="fa-solid fa-signal text-4xl text-green-600"></i>
                    <div>
                        <p class="text-3xl font-bold text-gray-900">{{ $activeQueues }}</p>
                        <p class="text-sm text-gray-500">Antrean Aktif</p>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-md flex items-center gap-4 transition-transform transform hover:scale-105 hover:shadow-lg">
                    <i class="fa-solid fa-calendar-check text-4xl text-orange-600"></i>
                    <div>
                        <p class="text-3xl font-bold text-gray-900">{{ $completedToday }}</p>
                        <p class="text-sm text-gray-500">Selesai Hari Ini</p>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-md flex items-center gap-4 transition-transform transform hover:scale-105 hover:shadow-lg">
                    <i class="fa-solid fa-layer-group text-4xl text-purple-600"></i>
                    <div>
                        <p class="text-3xl font-bold text-gray-900">{{ $serviceCategories }}</p>
                        <p class="text-sm text-gray-500">Kategori Layanan</p>
                    </div>
                </div>
            </div>

                    <!-- User Management Section -->
            <div class="bg-white p-6 rounded-xl shadow-md">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-900">Manajemen Pengguna</h2>
                   <button onclick="window.location='{{ route('users.create') }}'" 
        class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors flex items-center gap-2">
    <i class="fa-solid fa-plus"></i> Tambah
</button>

                </div>

                <!-- Tabs -->
                <div class="mb-4 border-b border-gray-200">
                    <nav class="-mb-px flex space-x-4">
                        <button class="tab-btn py-2 px-4 text-sm font-medium text-blue-600 border-b-2 border-blue-600 focus:outline-none" data-tab="admins">Admin</button>
                        <button class="tab-btn py-2 px-4 text-sm font-medium text-gray-600 hover:text-blue-600 focus:outline-none" data-tab="dosens">Dosen</button>
                        <button class="tab-btn py-2 px-4 text-sm font-medium text-gray-600 hover:text-blue-600 focus:outline-none" data-tab="mahasiswas">Mahasiswa</button>
                    </nav>
                </div>

                <!-- Tab Contents -->
                <div>
                    <!-- Admins -->
                    <div class="tab-content" id="admins">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach ($admins as $admin)
                            <div class="p-4 border border-gray-200 rounded-lg flex flex-col justify-between">
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $admin->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $admin->email }}</p>
                                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-green-100 text-green-800 mt-2 inline-block">Admin - Aktif</span>
                                </div>
                                <div class="flex justify-end mt-2 space-x-2">
                                    <!-- Edit -->
                                   <a href="{{ route('users.edit', ['role' => 'Admin', 'id' => $admin->id]) }}" 
                                        class="p-2 rounded hover:bg-blue-100 transition-colors">
                                            <i class="fa-solid fa-pen text-blue-600"></i>
                                        </a>


                                    <!-- Delete -->
                                  <form action="{{ route('users.destroy', ['role' => 'Admin', 'id' => $admin->id]) }}" method="POST" onsubmit="return confirm('Yakin hapus Admin ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-gray-500 hover:text-red-600">
                                                <i class="fa-solid fa-trash-alt"></i>
                                            </button>
                                        </form>
                                </div>

                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Dosens -->
                    <div class="tab-content hidden" id="dosens">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach ($dosens as $dosen)
                            <div class="p-4 border border-gray-200 rounded-lg flex flex-col justify-between">
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $dosen->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $dosen->email }}</p>
                                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-green-100 text-green-800 mt-2 inline-block">Dosen - {{ $dosen->status ?? 'Aktif' }}</span>
                                </div>
                                <div class="flex justify-end mt-2 space-x-2">
                                        <!-- Edit -->
                                        <a href="{{ route('users.edit', ['role' => 'Dosen', 'id' => $dosen->kode_dosen]) }}" 
                                                class="p-2 rounded hover:bg-blue-100 transition-colors">
                                                    <i class="fa-solid fa-pen text-blue-600"></i>
                                                </a>


                                        <!-- Delete -->
                                        <form action="{{ route('users.destroy', ['role' => 'Dosen', 'id' => $dosen->kode_dosen]) }}" method="POST" onsubmit="return confirm('Yakin hapus Dosen ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-gray-500 hover:text-red-600">
                                                    <i class="fa-solid fa-trash-alt"></i>
                                                </button>
                                            </form>
                                    </div>

                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Mahasiswas -->
                    <div class="tab-content hidden" id="mahasiswas">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach ($mahasiswas as $mhs)
                            <div class="p-4 border border-gray-200 rounded-lg flex flex-col justify-between">
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $mhs->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $mhs->nim ?? 'NIM tidak ada' }}</p>
                                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full {{ $mhs->status == 'Aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} mt-2 inline-block">Mahasiswa - {{ $mhs->status ?? 'Tidak Aktif' }}</span>
                                </div>
                                <div class="flex justify-end mt-2 space-x-2">
                                    <!-- Edit -->
                                    <a href="{{ route('users.edit', ['role' => 'Mahasiswa', 'id' => $mhs->nim]) }}" 
                                        class="p-2 rounded hover:bg-blue-100 transition-colors">
                                            <i class="fa-solid fa-pen text-blue-600"></i>
                                        </a>

                                    <!-- Delete -->
                                    <form action="{{ route('users.destroy', ['role' => 'Mahasiswa', 'id' => $mhs->nim]) }}" method="POST" onsubmit="return confirm('Yakin hapus Mahasiswa ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-gray-500 hover:text-red-600">
                                                <i class="fa-solid fa-trash-alt"></i>
                                            </button>
                                        </form>
                                </div>          

                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <script>
                // Tab switching
                const tabs = document.querySelectorAll('.tab-btn');
                const contents = document.querySelectorAll('.tab-content');

               tabs.forEach(tab => {
                    tab.addEventListener('click', () => {
                        // Reset semua tab
                        tabs.forEach(t => {
                            t.classList.remove('border-blue-600', 'text-blue-600', 'border-b-2');
                            t.classList.add('text-gray-600');
                        });

                        // Tambahkan style ke tab aktif
                        tab.classList.add('border-b-2', 'border-blue-600', 'text-blue-600');
                        tab.classList.remove('text-gray-600');

                        // Switch content
                        const target = tab.dataset.tab;
                        contents.forEach(c => c.classList.add('hidden'));
                        document.getElementById(target).classList.remove('hidden');
                    });
                });

            </script>


                <!-- Service Categories -->
                <div class="bg-white p-6 rounded-xl shadow-md">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold text-gray-900">Kategori Layanan</h2>
                       <button onclick="window.location='{{ route('auth.admin.services.create') }}'" 
        class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors flex items-center gap-2">
    <i class="fa-solid fa-plus"></i> Tambah
</button>

                    </div>
                   <div class="space-y-4">
    @foreach ($services as $service)
    <div class="flex justify-between items-center p-4 border border-gray-200 rounded-lg">
        <div class="flex-1">
            <p class="font-semibold text-gray-900">{{ $service->name }}</p>
            <p class="text-sm text-gray-500">{{ $service->description }}</p>
            <p class="text-xs text-gray-500">Est: {{ $service->est_time }}</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('services.edit', $service->id) }}" 
               class="text-gray-500 hover:text-blue-600 transition-colors">
               <i class="fa-solid fa-edit"></i>
            </a>
            <form action="{{ route('services.destroy', $service->id) }}" method="POST" class="inline-block">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-gray-500 hover:text-red-600 transition-colors" 
                        onclick="return confirm('Yakin ingin menghapus service ini?')">
                    <i class="fa-solid fa-trash-alt"></i>
                </button>
            </form>
        </div>
    </div>
    @endforeach
</div>

                </div>
            </div>

            <!-- Reports & Analytics -->
            <div class="bg-white p-6 rounded-xl shadow-md">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Laporan & Analitik</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                   <!-- Laporan Harian -->
                    <a href="{{ route('reports.daily') }}" class="bg-gray-100 p-6 rounded-xl shadow-sm hover:bg-gray-200 transition-colors text-center font-medium text-gray-700">
                        <i class="fa-solid fa-file-alt text-3xl mb-2"></i>
                        <p>Laporan Harian</p>
                    </a>

                    <!-- Statistik Layanan -->
                    <a href="{{ route('reports.services') }}" class="bg-gray-100 p-6 rounded-xl shadow-sm hover:bg-gray-200 transition-colors text-center font-medium text-gray-700">
                        <i class="fa-solid fa-chart-bar text-3xl mb-2"></i>
                        <p>Statistik Layanan</p>
                    </a>

                    <!-- Rekap Laporan -->
                    <a href="{{ route('reports.rekap') }}" class="bg-gray-100 p-6 rounded-xl shadow-sm hover:bg-gray-200 transition-colors text-center font-medium text-gray-700">
                        <i class="fa-solid fa-file-invoice text-3xl mb-2"></i>
                        <p>Rekap Laporan</p>
                    </a>


                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Manage User and Service
            document.getElementById('add-user-btn').addEventListener('click', () => {
                alert('Fungsionalitas "Tambah Pengguna" akan diimplementasikan di sini.');
            });

            document.getElementById('add-service-btn').addEventListener('click', () => {
                alert('Fungsionalitas "Tambah Kategori Layanan" akan diimplementasikan di sini.');
            });

            document.querySelectorAll('.edit-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const id = e.currentTarget.dataset.id;
                    alert(`Mengedit item dengan ID: ${id}. Fungsionalitas akan diimplementasikan di sini.`);
                });
            });

            document.querySelectorAll('.delete-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const id = e.currentTarget.dataset.id;
                    alert(`Menghapus item dengan ID: ${id}. Fungsionalitas akan diimplementasikan di sini.`);
                });
            });

            // Reports & Analytics
            document.getElementById('daily-report-btn').addEventListener('click', () => {
                alert('Membuka Laporan Harian. Fungsionalitas akan diimplementasikan di sini.');
            });
            document.getElementById('service-stats-btn').addEventListener('click', () => {
                alert('Membuka Statistik Layanan. Fungsionalitas akan diimplementasikan di sini.');
            });
            document.getElementById('rekap-report-btn').addEventListener('click', () => {
                alert('Membuka Rekap Laporan. Fungsionalitas akan diimplementasikan di sini.');
            });

        });
    </script>
</body>
</html>
