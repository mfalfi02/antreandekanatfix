<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layanan Dekanat - Display Antrean</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" xintegrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            background-color: #f0f4f8;
        }
    </style>
</head>
<body class="p-8">
    <div class="max-w-7xl mx-auto">
        <header class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">LAYANAN DEKANAT</h1>
                <p class="text-md text-gray-600">Universitas Widya Dharma Pontianak</p>
            </div>
            <div class="text-right text-sm text-gray-500">
                <p>Jumat, 19 September 2025</p>
                <p>01.20.49</p>
            </div>
        </header>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow p-6 text-center">
                <p class="text-3xl font-bold text-blue-600">5</p>
                <p class="text-sm text-gray-500">Antrean Saat Ini</p>
            </div>
            <div class="bg-white rounded-xl shadow p-6 text-center">
                <p class="text-3xl font-bold text-green-600">4</p>
                <p class="text-sm text-gray-500">Petugas Tersedia</p>
            </div>
            <div class="bg-white rounded-xl shadow p-6 text-center">
                <p class="text-3xl font-bold text-orange-600">2</p>
                <p class="text-sm text-gray-500">Sedang Melayani</p>
            </div>
            <div class="bg-white rounded-xl shadow p-6 text-center">
                <p class="text-3xl font-bold text-gray-600">3</p>
                <p class="text-sm text-gray-500">Menunggu</p>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Status Dosen & Petugas Dekanat -->
            <div class="bg-white p-6 rounded-xl shadow">
                <h2 class="text-xl font-semibold text-gray-900 flex items-center mb-4">
                    <i class="fa-solid fa-users text-gray-500 mr-2"></i> Status Dosen & Petugas Dekanat
                </h2>
                <ul class="space-y-4">
                    <li class="flex justify-between items-start border border-gray-200 p-4 rounded-lg">
                        <div class="flex items-start space-x-3">
                            <i class="fa-solid fa-user-circle text-2xl text-gray-400"></i>
                            <div>
                                <p class="font-medium text-gray-900">Prof. Dr. Sutrisno, M.Si.</p>
                                <p class="text-sm text-gray-500">Dekan</p>
                                <p class="text-xs text-gray-400">Ruang Dekan - 0561-123456</p>
                            </div>
                        </div>
                        <span class="text-xs text-green-600 bg-green-100 px-2 py-1 rounded-full font-semibold">Tersedia</span>
                    </li>
                    <li class="flex justify-between items-start border border-gray-200 p-4 rounded-lg">
                        <div class="flex items-start space-x-3">
                            <i class="fa-solid fa-user-circle text-2xl text-gray-400"></i>
                            <div>
                                <p class="font-medium text-gray-900">Dr. Sri Wahyuni, M.Pd.</p>
                                <p class="text-sm text-gray-500">Wakil Dekan I</p>
                                <p class="text-xs text-gray-400">Ruang WD I - Ext. 101</p>
                                <span class="text-xs text-blue-600 bg-blue-100 px-2 py-1 rounded-full mt-1 inline-block">Konsultasi Akademik</span>
                            </div>
                        </div>
                        <span class="text-xs text-yellow-600 bg-yellow-100 px-2 py-1 rounded-full font-semibold">Sibuk</span>
                    </li>
                    <li class="flex justify-between items-start border border-gray-200 p-4 rounded-lg">
                        <div class="flex items-start space-x-3">
                            <i class="fa-solid fa-user-circle text-2xl text-gray-400"></i>
                            <div>
                                <p class="font-medium text-gray-900">Dr. Ahmad Rahman, M.T.</p>
                                <p class="text-sm text-gray-500">Wakil Dekan II</p>
                                <p class="text-xs text-gray-400">Ruang WD II - Ext. 102</p>
                            </div>
                        </div>
                        <span class="text-xs text-green-600 bg-green-100 px-2 py-1 rounded-full font-semibold">Tersedia</span>
                    </li>
                </ul>
            </div>
            
            <!-- Status Antrean Hari Ini -->
            <div class="bg-white p-6 rounded-xl shadow">
                <h2 class="text-xl font-semibold text-gray-900 flex items-center mb-4">
                    <i class="fa-solid fa-list-check text-gray-500 mr-2"></i> Status Antrean Hari Ini
                </h2>
                <ul class="space-y-4">
                    <li class="flex justify-between items-center border border-gray-200 p-4 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900">1. Ahmad Rizki</p>
                            <p class="text-sm text-gray-500">Legalisir Dokumen</p>
                        </div>
                        <span class="text-xs text-green-600 bg-green-100 px-2 py-1 rounded-full font-semibold">Selesai</span>
                    </li>
                    <li class="flex justify-between items-center border border-gray-200 p-4 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900">2. Sari Indah</p>
                            <p class="text-sm text-gray-500">Surat Aktif Kuliah</p>
                        </div>
                        <span class="text-xs text-green-600 bg-green-100 px-2 py-1 rounded-full font-semibold">Selesai</span>
                    </li>
                    <li class="flex justify-between items-center border border-gray-200 p-4 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900">3. Budi Santoso</p>
                            <p class="text-sm text-gray-500">Surat Cuti Akademik</p>
                        </div>
                        <span class="text-xs text-green-600 bg-green-100 px-2 py-1 rounded-full font-semibold">Selesai</span>
                    </li>
                    <li class="flex justify-between items-center border border-gray-200 p-4 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900">4. Lisa Permata</p>
                            <p class="text-sm text-gray-500">Legalisir Dokumen</p>
                        </div>
                        <span class="text-xs text-green-600 bg-green-100 px-2 py-1 rounded-full font-semibold">Selesai</span>
                    </li>
                    <li class="flex justify-between items-center border border-blue-600 p-4 rounded-lg bg-blue-50">
                        <div>
                            <p class="font-medium text-gray-900">5. Doni Pratama</p>
                            <p class="text-sm text-gray-500">Transkrip Nilai</p>
                        </div>
                        <span class="text-xs text-blue-600 bg-blue-100 px-2 py-1 rounded-full font-semibold">Sedang Dilayani</span>
                    </li>
                    <li class="flex justify-between items-center border border-gray-200 p-4 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900">6. Rina Sari</p>
                            <p class="text-sm text-gray-500">Surat Aktif Kuliah</p>
                        </div>
                        <span class="text-xs text-gray-600 bg-gray-200 px-2 py-1 rounded-full font-semibold">Menunggu</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>
