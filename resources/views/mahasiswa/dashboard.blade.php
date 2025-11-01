<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Antrean Dekanat FTI</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            background-color: #f0f4f8;
        }
    </style>
</head>

<body class="p-8">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- HEADER --}}
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    Dashboard {{ ucfirst($data['user']->role) }}
                </h1>
                <p class="text-gray-600">
                    Selamat datang, {{ $data['user']->name }}
                </p>
            </div>
            <div class="flex items-center gap-4">
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                        class="px-4 py-2 rounded-lg bg-red-600 text-white font-medium hover:bg-red-700 transition flex items-center gap-2">
                        <i class="fa fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </div>

        {{-- STATUS PEJABAT --}}
        <div class="bg-white p-6 rounded-xl shadow-lg mt-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Status Dosen Dekanat</h2>
            <div class="space-y-3 max-h-96 overflow-y-auto">
                @foreach ($data['pejabat'] as $pejabat)
                    <div class="p-3 border rounded-lg flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                <i class="fa fa-user"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ $pejabat->name }}</p>
                                <p class="text-sm text-gray-500">{{ ucfirst($pejabat->jabatan) }}</p>
                                <p class="text-xs text-gray-400">
                                    <i class="fa fa-map-marker-alt"></i> {{ $pejabat->ruangan ?? '-' }}
                                </p>
                            </div>
                        </div>
                        <div>
                            @if (!empty($pejabat->is_active_queue) && $pejabat->is_active_queue)
                                <span class="px-3 py-1 rounded-full bg-green-100 text-blue-800 text-xs font-semibold">Buka</span>
                            @else
                                <span class="px-3 py-1 rounded-full bg-red-200 text-gray-600 text-xs font-semibold">Tutup</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- BAGIAN PENGONTROL UNTUK PEJABAT --}}
        @if ($data['user']->role === 'pejabat')
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <h2 class="text-xl font-semibold text-gray-900 flex items-center gap-2 mb-4">
                    <i class="fa-solid fa-play-circle text-gray-600"></i> Kontrol Antrean
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Jenis Layanan</label>
                        <select id="service-select"
                            class="mt-2 block w-full pl-3 pr-10 py-2 border-gray-300 rounded-md focus:ring-blue-500">
                            <option value="">Pilih jenis layanan</option>
                            <option value="all">Semua layanan</option>
                            @foreach ($data['services'] as $service)
                                <option value="{{ $service->id }}">{{ $service->nama_layanan }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center space-x-2">
                        <button id="open-queue-btn-1"
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Buka</button>
                        <button id="close-queue-btn-1"
                            class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 hidden">Tutup</button>
                    </div>
                </div>
            </div>

            {{-- Statistik --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white p-6 rounded-xl shadow-lg flex items-center gap-4">
                    <i class="fa-solid fa-users text-3xl text-gray-600"></i>
                    <div>
                        <p class="text-2xl font-bold">{{ $data['activeQueues'] }}</p>
                        <p class="text-sm text-gray-500">Menunggu</p>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-lg flex items-center gap-4">
                    <i class="fa-solid fa-check-circle text-3xl text-green-600"></i>
                    <div>
                        <p class="text-2xl font-bold">{{ $data['completedQueues'] }}</p>
                        <p class="text-sm text-gray-500">Selesai</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- FORM ANTREAN UNTUK MAHASISWA DAN DOSEN --}}
        @if (in_array($data['user']->role, ['mahasiswa', 'dosen']))
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Ambil Antrean --}}
                <div class="bg-white p-6 rounded-xl shadow-md">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Ambil Nomor Antrean</h2>
                    <div class="space-y-4">
                        <label class="block text-sm font-medium text-gray-700">Pilih Dosen Tujuan</label>
                        <select id="dean-select"
                            class="mt-2 block w-full pl-3 pr-10 py-2 border-gray-300 rounded-md focus:ring-blue-500">
                            <option value="">Pilih dosen</option>
                            @foreach ($data['pejabat'] as $pejabat)
                                <option value="{{ $pejabat->kode }}">{{ $pejabat->name }}</option>
                            @endforeach
                        </select>

                        <label class="block text-sm font-medium text-gray-700">Jenis Layanan</label>
                        <select id="service-select"
                            class="mt-2 block w-full pl-3 pr-10 py-2 border-gray-300 rounded-md focus:ring-blue-500">
                            <option value="">Pilih layanan</option>
                            @foreach ($data['services'] as $service)
                                <option value="{{ $service->id }}">{{ $service->nama_layanan }}</option>
                            @endforeach
                        </select>

                        <button id="join-queue-btn"
                            class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-700 transition">
                            Ambil Nomor
                        </button>
                    </div>
                </div>

                {{-- Status Antrean --}}
                <div class="bg-white p-6 rounded-xl shadow-md">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Antrean Anda</h2>
                    <div id="my-queue-list" class="space-y-3 max-h-96 overflow-y-auto">
                        @forelse($data['myQueues'] as $queue)
                            <div class="p-3 border rounded-lg flex justify-between items-center">
                                <div>
                                    <p class="font-semibold text-gray-900">#{{ $queue->nomor_antrian }} -
                                        {{ $queue->service->nama_layanan ?? '-' }}</p>
                                    <p class="text-sm text-gray-500">{{ ucfirst($queue->status) }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500">Belum ada antrean.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        @endif
    </div>
</body>
</html>
