<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Antrean Dekanat - Dashboard Admin</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.pwa-head', [
        'appleTouchIcon' => asset('pwa/dashboard-icons/icon-180x180.png'),
        'favicon16' => asset('pwa/dashboard-icons/icon-16x16.png'),
        'favicon32' => asset('pwa/dashboard-icons/icon-32x32.png'),
        'appleTitle' => 'Dashboard Antrean'
    ])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <style>
        :root {
            --bg-1: #f7fbff;
            --bg-2: #eef5ff;
            --panel: #ffffff;
            --line: #dbe6f6;
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --danger: #dc2626;
            --danger-hover: #b91c1c;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background:
                radial-gradient(circle at 12% -8%, rgba(15, 96, 240, .15), transparent 36%),
                radial-gradient(circle at 88% -12%, rgba(56, 189, 248, .14), transparent 34%),
                linear-gradient(180deg, var(--bg-2), var(--bg-1));
            min-height: 100vh;
        }

        .panel {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 1rem;
            box-shadow: 0 14px 30px -20px rgba(15, 23, 42, .35);
        }

        .room-sync-table thead th {
            background: linear-gradient(180deg, #f8fbff, #eef4ff);
            color: #334155;
            font-size: .73rem;
            font-weight: 700;
            letter-spacing: .03em;
            text-transform: uppercase;
            border-bottom: 1px solid #dbe6f6;
        }

        .room-sync-table tbody tr {
            transition: background-color .15s ease;
        }

        .room-sync-table tbody tr:hover {
            background: #f8fbff;
        }

        .soft-input {
            border: 1px solid #cfdcf1;
            border-radius: .75rem;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            transition: border-color .2s ease, box-shadow .2s ease;
        }

        .soft-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, .15);
        }

        .user-card {
            border: 1px solid #dbe6f6;
            border-radius: 1rem;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            box-shadow: 0 10px 24px -22px rgba(15, 23, 42, .45);
            transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
        }

        .user-card:hover {
            transform: translateY(-2px);
            border-color: #bfdbfe;
            box-shadow: 0 16px 26px -22px rgba(37, 99, 235, .35);
        }

        .tab-btn {
            border: 1px solid transparent;
            border-radius: .75rem .75rem 0 0;
            transition: color .15s ease, border-color .15s ease, background-color .15s ease;
        }

        .tab-btn.is-active {
            color: #1d4ed8;
            border-color: #bfdbfe;
            border-bottom-color: #ffffff;
            background: linear-gradient(180deg, #eff6ff 0%, #ffffff 85%);
        }

        .user-action-btn {
            min-width: 2.25rem;
            height: 2.25rem;
            border-radius: .65rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: background-color .15s ease, color .15s ease, border-color .15s ease;
        }

        .user-empty-state {
            border: 1px dashed #bfdbfe;
            border-radius: .9rem;
            background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
        }

        #location-map {
            height: 280px;
            border-radius: 1rem;
            overflow: hidden;
            border: 1px solid #dbe6f6;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .7);
        }

        .user-list-scroll {
            max-height: 34rem;
            overflow-y: auto;
            padding-right: .25rem;
            scrollbar-width: thin;
            scrollbar-color: #c5d4eb #f6f9ff;
        }

        .user-list-scroll::-webkit-scrollbar {
            width: 8px;
        }

        .user-list-scroll::-webkit-scrollbar-thumb {
            background: #c5d4eb;
            border-radius: 999px;
        }

        .user-list-scroll::-webkit-scrollbar-track {
            background: #f6f9ff;
        }

        .service-card {
            border: 1px solid #dbe6f6;
            border-radius: 1rem;
            background: #fff;
            box-shadow: 0 10px 24px -22px rgba(15, 23, 42, .45);
            transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
        }

        .service-card:hover {
            transform: translateY(-2px);
            border-color: #bfdbfe;
            box-shadow: 0 16px 26px -22px rgba(37, 99, 235, .35);
        }

        .btn-brand {
            background: var(--primary);
            color: #fff;
            border-radius: .65rem;
            font-weight: 600;
            transition: background-color .18s ease, transform .12s ease;
        }

        .btn-brand:hover {
            background: var(--primary-hover);
        }

        .btn-danger {
            background: var(--danger);
            color: #fff;
            border-radius: .65rem;
            font-weight: 600;
            transition: background-color .18s ease, transform .12s ease;
        }

        .btn-danger:hover {
            background: var(--danger-hover);
        }

        @media (max-width: 768px) {
            body {
                padding: .9rem;
            }

            .panel {
                border-radius: .85rem;
            }

            .admin-header-actions {
                width: 100%;
                justify-content: space-between;
                gap: .6rem;
                flex-wrap: wrap;
            }

            .admin-toolbar {
                width: 100%;
                align-items: stretch;
                flex-direction: column;
            }

            .admin-toolbar .soft-input {
                width: 100% !important;
            }

            .admin-toolbar .btn-brand {
                width: 100%;
                justify-content: center;
            }

            .admin-room-table-wrap {
                margin-left: -.5rem;
                margin-right: -.5rem;
                border-radius: .75rem;
            }

            .room-sync-table {
                font-size: .75rem;
            }

            .tab-btn {
                flex: 1 1 calc(50% - .5rem);
                text-align: center;
                justify-content: center;
            }

            .user-card {
                padding: .85rem;
            }

            .service-card {
                align-items: flex-start;
                flex-direction: column;
                gap: .75rem;
            }

            .service-card > div:last-child {
                margin-left: 0;
            }
        }
    </style>
</head>

<body class="p-4 md:p-7">
    {{-- Header dashboard admin: pintu masuk ke ringkasan, status ruang, dan aksi utama --}}
    <div class="panel p-5 md:p-6 flex flex-col gap-3 md:flex-row md:justify-between md:items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Dashboard Admin</h1>
            <p class="text-gray-600">Kelola sistem antrean dekanat</p>
        </div>

        <div class="admin-header-actions flex items-center gap-4">
            <div class="text-right">
                <p id="current-date-admin" class="text-xs text-gray-500">-</p>
                <p id="current-time-admin" class="text-sm font-semibold text-gray-800">- WIB</p>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit"
                    class="btn-danger px-4 py-2 flex items-center gap-2">
                    <i class="fa fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
    </div>

    {{-- Area utama dashboard admin: statistik, daftar user, layanan, dan peta lokasi --}}
    <div class="min-h-screen">
        <div class="max-w-7xl mx-auto space-y-8">

            @if (session('error'))
                <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    <i class="fa-solid fa-triangle-exclamation mr-2"></i>
                    {{ session('error') }}
                </div>
            @endif

            {{-- Pengaturan lokasi antrean --}}
            <div class="panel p-6">
                @php
                    $locationSetting = $locationSetting ?? null;
                @endphp
                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4 mb-4">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">Pengaturan Lokasi Antrean</h2>
                        <p class="text-sm text-gray-500">Ambil Titik Kordinat untuk menentukan radius sistem.</p>
                    </div>
                    <div class="text-sm text-gray-600">
                        <p class="font-semibold text-gray-800">Radius aktif</p>
                        <p>{{ $locationSetting?->radius_meters ?? 300 }} meter</p>
                    </div>
                </div>

                <form id="location-setting-form" method="POST" action="{{ route('adm.location.update') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    @csrf
                    <label class="block">
                        <span class="mb-1.5 block text-sm font-medium text-slate-700">Latitude Pusat</span>
                        <input
                            id="location-center-latitude"
                            type="number"
                            name="center_latitude"
                            step="0.0000001"
                            required
                            value="{{ old('center_latitude', $locationSetting?->center_latitude) }}"
                            class="soft-input w-full px-3 py-2 text-sm"
                        >
                    </label>
                    <label class="block">
                        <span class="mb-1.5 block text-sm font-medium text-slate-700">Longitude Pusat</span>
                        <input
                            id="location-center-longitude"
                            type="number"
                            name="center_longitude"
                            step="0.0000001"
                            required
                            value="{{ old('center_longitude', $locationSetting?->center_longitude) }}"
                            class="soft-input w-full px-3 py-2 text-sm"
                        >
                    </label>
                    <label class="block">
                        <span class="mb-1.5 block text-sm font-medium text-slate-700">Radius Meter</span>
                        <input
                            type="number"
                            name="radius_meters"
                            min="1"
                            max="5000"
                            required
                            value="{{ old('radius_meters', $locationSetting?->radius_meters ?? 300) }}"
                            class="soft-input w-full px-3 py-2 text-sm"
                        >
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 sm:gap-3 md:col-span-4">
                        <button type="button" id="use-browser-location-btn"
                            class="btn-brand px-4 py-2 text-sm inline-flex items-center justify-center gap-2 w-full">
                            <i class="fa-solid fa-crosshairs"></i>
                            Gunakan Lokasi Saat Ini
                        </button>
                        <button type="button" id="reset-location-btn"
                            class="px-4 py-2 text-sm inline-flex items-center justify-center gap-2 w-full rounded-xl border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 transition-colors">
                            <i class="fa-solid fa-rotate-left"></i>
                            Reset Lokasi Awal
                        </button>
                        <button type="submit" class="btn-brand px-4 py-2 text-sm inline-flex items-center justify-center gap-2 w-full">
                            <i class="fa-solid fa-location-dot"></i>
                            Simpan Lokasi
                        </button>
                    </div>
                </form>

                <p id="location-status" class="mt-3 text-xs text-gray-500">
                    Jika koordinat belum diisi, validasi lokasi akan dilewati sementara agar sistem tetap bisa dipakai.
                </p>

                <div class="mt-5 rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-sm font-semibold text-slate-800">Preview Lokasi</p>
                    <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-slate-600">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500">Titik pusat</p>
                            <p id="location-preview-center" class="font-semibold text-slate-800">-</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500">Lokasi browser saat ini</p>
                            <p id="location-preview-current" class="font-semibold text-slate-800">-</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500">Jarak ke pusat</p>
                            <p id="location-preview-distance" class="font-semibold text-slate-800">-</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500">Radius validasi</p>
                            <p id="location-preview-radius" class="font-semibold text-slate-800">{{ $locationSetting?->radius_meters ?? 300 }} meter</p>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <p class="mb-2 text-sm font-semibold text-slate-800">Radius Visual</p>
                    <div id="location-map"></div>
                </div>
            </div>

            {{-- Sinkronisasi ruang antrean harian --}}
            <div class="panel p-6">
                @php
                    $todayJakarta = now('Asia/Jakarta')->locale('id');
                    $dashboardTodayLabel = $todayJakarta->translatedFormat('l, d F Y');
                @endphp

                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4 mb-4">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">Sinkronisasi Ruang Antrean Hari Ini</h2>
                        <p class="text-sm text-gray-500">Monitoring status room dan waktu layanan dosen untuk hari ini.</p>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                        <span class="text-xs px-2.5 py-1 rounded-full bg-blue-100 text-blue-700 font-semibold">Auto Sync</span>
                        <span class="text-xs px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 font-semibold">Hari Ini</span>
                    </div>
                </div>

                <div class="mb-4 flex flex-wrap items-center gap-2">
                    <span class="text-sm text-slate-600">Tanggal pantau:</span>
                    <span class="text-sm font-semibold text-slate-800">{{ $dashboardTodayLabel }}</span>
                </div>

                
                <div class="admin-room-table-wrap overflow-x-auto rounded-xl border border-blue-100">
                    <table class="room-sync-table min-w-full text-sm">
                        <thead>
                            <tr>
                                <th class="px-3 py-3 text-left">Dosen/Pejabat</th>
                                <th class="px-3 py-3 text-left">Jenis Layanan</th>
                                <th class="px-3 py-3 text-center">Status</th>
                                <th class="px-3 py-3 text-center">Perkiraan Tutup</th>
                                <th class="px-3 py-3 text-center">Jam Buka</th>
                                <th class="px-3 py-3 text-center">Jam Tutup</th>
                            </tr>
                        </thead>
                        <tbody id="room-sync-body">
                            <tr>
                                <td colspan="6" class="px-3 py-6 text-center text-gray-500">
                                    Memuat sinkronisasi ruang antrean...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            
            {{-- Manajemen pengguna --}}
            <div class="panel p-6">
                <div class="flex flex-col sm:flex-row justify-between items-center mb-4 gap-3">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">Pengguna</h2>
                        <p class="text-sm text-gray-500">Kelola akun admin, dosen, mahasiswa, dan pejabat.</p>
                    </div>
                    <div class="admin-toolbar flex items-center gap-2">
                        <input type="text" id="searchUser" placeholder="Cari nama atau kode..."
                            class="soft-input px-3 py-2 text-sm w-60">
                        <a href="{{ route('users.create') }}"
                            class="btn-brand px-4 py-2 text-sm flex items-center gap-2">
                            <i class="fa-solid fa-plus"></i> Tambah Pengguna
                        </a>
                    </div>
                </div>

                @php
                    $roleCounts = collect($data['users'])->groupBy('role')->map->count();
                @endphp

                {{-- Filter role pengguna --}}
                <div class="mb-4 border-b border-gray-200">
                    <nav class="-mb-px flex flex-wrap gap-2">
                        
                        @foreach (['admin', 'dosen', 'mahasiswa', 'pejabat'] as $roleTab)
                            <button class="tab-btn py-2 px-4 text-sm font-medium text-gray-600 hover:text-blue-600 {{ $loop->first ? 'is-active' : '' }}"
                                data-tab="{{ $roleTab }}">
                                {{ ucfirst($roleTab) }}
                                <span class="ml-1.5 inline-flex items-center justify-center min-w-5 h-5 px-1.5 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700">
                                    {{ $roleCounts[$roleTab] ?? 0 }}
                                </span>
                            </button>
                        @endforeach
                    </nav>
                </div>

                {{-- Daftar kartu pengguna --}}
                <div>
                    
                    @foreach (['admin', 'dosen', 'mahasiswa', 'pejabat'] as $tabId)
                        <div class="tab-content {{ $loop->first ? '' : 'hidden' }}" id="{{ $tabId }}">
                            <div class="user-list-scroll">
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 user-item">
                                @foreach ($data['users'] as $user)
                                    
                                    @if ($user->role === $tabId)
                                        <div class="p-4 flex flex-col justify-between user-card">
                                            <div>
                                                <div class="flex items-start justify-between gap-3">
                                                    <div class="flex items-center gap-3">
                                                        <span
                                                            class="w-10 h-10 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center">
                                                            <i class="fa-solid fa-user"></i>
                                                        </span>
                                                        <div>
                                                            <p class="font-semibold text-gray-900 user-name">{{ $user->name }}</p>
                                                            <p class="text-sm text-gray-500 user-kode">{{ $user->kode }}</p>
                                                            <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                                            
                                                            @if (in_array($user->role, ['dosen', 'pejabat'], true) && $user->jabatan)
                                                                <p class="text-xs text-slate-600 inline-flex items-center gap-1 mt-0.5">
                                                                    <i class="fa-solid fa-id-badge text-slate-400"></i>
                                                                    {{ $user->jabatan }}
                                                                </p>
                                                            @endif
                                                            
                                                            @if ($user->role === 'pejabat' && $user->ruangan)
                                                                <p class="text-xs text-slate-600 inline-flex items-center gap-1 mt-0.5">
                                                                    <i class="fa-solid fa-door-open text-slate-400"></i>
                                                                    {{ $user->ruangan }}
                                                                </p>
                                                            @endif
                                                            
                                                            @if ($user->role === 'mahasiswa')
                                                                <p class="text-xs text-slate-600 inline-flex items-center gap-1 mt-0.5">
                                                                    <i class="fa-solid fa-user-graduate text-slate-400"></i>
                                                                    {{ $user->jabatan ?? 'Mahasiswa' }}
                                                                </p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                @php
                                                    $isAktif = $user->status === 'aktif';
                                                @endphp
                                                <span
                                                    class="text-xs font-semibold px-2.5 py-1 rounded-full mt-3 inline-flex items-center gap-1 {{ $isAktif ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                                    <i class="fa-solid {{ $isAktif ? 'fa-circle-check' : 'fa-circle-xmark' }}"></i>
                                                    {{ ucfirst($tabId) }} - {{ $isAktif ? 'Aktif' : 'Nonaktif' }}
                                                </span>
                                            </div>
                                            <div class="flex justify-end mt-4 space-x-2">
                                                
                                                <a href="{{ route('users.edit', $user->kode) }}"
                                                    title="Edit {{ $user->name }}"
                                                    class="user-action-btn border border-blue-200 text-blue-600 hover:bg-blue-50">
                                                    <i class="fa-solid fa-pen"></i>
                                                </a>
                                                <form action="{{ route('users.destroy', $user->kode) }}" method="POST"
                                                    onsubmit="return confirm('Yakin hapus pengguna {{ $user->name }} ({{ $user->kode }})?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" title="Hapus {{ $user->name }}"
                                                        {{ auth()->check() && auth()->user()->kode === $user->kode ? 'disabled' : '' }}
                                                        class="user-action-btn border border-rose-200 text-rose-600 {{ auth()->check() && auth()->user()->kode === $user->kode ? 'opacity-50 cursor-not-allowed' : 'hover:bg-rose-50' }}">
                                                        <i class="fa-solid fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                                @if (($roleCounts[$tabId] ?? 0) === 0)
                                    <div class="user-empty-state p-8 text-center text-gray-500 col-span-full">
                                        <i class="fa-regular fa-folder-open text-2xl mb-2 text-blue-400"></i>
                                        <p>Belum ada pengguna dengan role {{ ucfirst($tabId) }}.</p>
                                    </div>
                                @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            
            {{-- Master data layanan --}}
            <div class="panel p-6">
                <div class="flex flex-col sm:flex-row justify-between items-center mt-2 mb-4 gap-3">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 mb-1 sm:mb-0">Kategori Layanan</h2>
                        <p class="text-sm text-gray-500">Atur layanan dan estimasi waktu pelayanan.</p>
                    </div>
                    <div class="admin-toolbar flex items-center gap-2">
                        <input type="text" id="searchService" placeholder="Cari layanan..."
                            class="soft-input px-3 py-2 text-sm w-60">
                        <button onclick="window.location='{{ route('services.create') }}'"
                            class="btn-brand px-4 py-2 text-sm flex items-center gap-2">
                            <i class="fa-solid fa-plus"></i> Tambah Service
                        </button>
                    </div>
                </div>

                <div class="space-y-4" id="serviceList">
                    
                    @foreach ($data['service'] as $service)
                        <div class="flex justify-between items-center p-4 service-card">
                            <div class="flex-1">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="font-semibold text-gray-900 service-name">{{ $service->nama_layanan }}</p>
                                        <p class="text-sm text-gray-500">{{ $service->deskripsi }}</p>
                                    </div>
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                                        <i class="fa-regular fa-clock mr-1"></i>{{ $service->est }} menit
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2 ml-4">
                                
                                <a href="{{ route('services.edit', $service->id) }}"
                                    class="h-9 w-9 rounded-lg border border-blue-200 text-blue-600 hover:bg-blue-50 transition-colors inline-flex items-center justify-center">
                                    <i class="fa-solid fa-edit"></i>
                                </a>
                                <form action="{{ route('services.destroy', $service->id) }}" method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus service ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="h-9 w-9 rounded-lg border border-rose-200 text-rose-600 hover:bg-rose-50 transition-colors inline-flex items-center justify-center">
                                        <i class="fa-solid fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>


            
            {{-- Laporan dan analitik --}}
            <div class="panel p-6 mt-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Laporan & Analitik</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                </div>
            </div>

        </div>
    </div>

    {{-- Script interaktif admin untuk lokasi, sinkronisasi, tab, dan pencarian --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    {{-- State awal disiapkan dari PHP agar JavaScript tetap sinkron dengan data server --}}
    <script>
        const currentDateAdmin = document.getElementById('current-date-admin');
        const currentTimeAdmin = document.getElementById('current-time-admin');
        const roomSyncBody = document.getElementById('room-sync-body');
        const locationSettingForm = document.getElementById('location-setting-form');
        const useBrowserLocationBtn = document.getElementById('use-browser-location-btn');
        const resetLocationBtn = document.getElementById('reset-location-btn');
        const locationLatitudeInput = document.getElementById('location-center-latitude');
        const locationLongitudeInput = document.getElementById('location-center-longitude');
        const locationRadiusInput = document.querySelector('input[name="radius_meters"]');
        const locationStatus = document.getElementById('location-status');
        const locationPreviewCenter = document.getElementById('location-preview-center');
        const locationPreviewCurrent = document.getElementById('location-preview-current');
        const locationPreviewDistance = document.getElementById('location-preview-distance');
        const locationPreviewRadius = document.getElementById('location-preview-radius');
        const locationMapContainer = document.getElementById('location-map');
        let browserLocation = null;
        let locationMap = null;
        let locationCenterMarker = null;
        let locationRadiusCircle = null;
        let autoSaveTimer = null;
        let isSavingLocation = false;
        @php
            $initialLocation = [
                'latitude' => old('center_latitude', $locationSetting?->center_latitude),
                'longitude' => old('center_longitude', $locationSetting?->center_longitude),
                'radius' => old('radius_meters', $locationSetting?->radius_meters ?? 300),
            ];
        @endphp
        let initialLocation = @json($initialLocation);

        // Helper jarak dipakai untuk menghitung selisih antara browser admin dan titik pusat.
        function haversineMeters(lat1, lng1, lat2, lng2) {
            const earthRadius = 6371000;
            const toRad = (value) => value * Math.PI / 180;
            const dLat = toRad(lat2 - lat1);
            const dLng = toRad(lng2 - lng1);
            const a = Math.sin(dLat / 2) ** 2 +
                Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) * Math.sin(dLng / 2) ** 2;
            return 2 * earthRadius * Math.asin(Math.min(1, Math.sqrt(a)));
        }
        function formatDistance(meters) {
            if (!Number.isFinite(meters)) return '-';
            if (meters < 1000) {
                return `${Math.round(meters)} meter`;
            }
            return `${(meters / 1000).toFixed(2)} km`;
        }
        function getLocationInputs() {
            const latitude = Number(locationLatitudeInput?.value);
            const longitude = Number(locationLongitudeInput?.value);
            return {
                latitude: Number.isFinite(latitude) ? latitude : null,
                longitude: Number.isFinite(longitude) ? longitude : null,
            };
        }
        function getLocationRadius() {
            const radius = Number(locationRadiusInput?.value ?? 300);
            return Number.isFinite(radius) ? radius : 300;
        }
        function getMapFallbackCenter() {
            const center = getLocationInputs();
            if (center.latitude !== null && center.longitude !== null) {
                return [center.latitude, center.longitude];
            }

            if (browserLocation) {
                return [browserLocation.latitude, browserLocation.longitude];
            }

            return [-6.200000, 106.816666];
        }
        function ensureLocationMap() {
            if (!locationMapContainer || !window.L) return;

            if (!locationMap) {
                locationMap = L.map('location-map', {
                    scrollWheelZoom: false,
                    zoomControl: true,
                }).setView(getMapFallbackCenter(), 16);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap contributors',
                }).addTo(locationMap);

                locationMap.on('click', (event) => {
                    const latitude = Number(event?.latlng?.lat);
                    const longitude = Number(event?.latlng?.lng);

                    if (!Number.isFinite(latitude) || !Number.isFinite(longitude)) return;

                    locationLatitudeInput.value = latitude.toFixed(7);
                    locationLongitudeInput.value = longitude.toFixed(7);
                    updateLocationPreview();
                    updateLocationMap();
                    scheduleLocationAutoSave('Pusat lokasi dipindahkan dari peta.');
                });
            }
        }
        function updateLocationMap() {
            if (!locationMapContainer || !window.L) return;

            ensureLocationMap();
            if (!locationMap) return;

            const center = getLocationInputs();
            const radius = getLocationRadius();
            const hasCenter = center.latitude !== null && center.longitude !== null;

            if (!hasCenter) {
                if (locationCenterMarker) {
                    locationMap.removeLayer(locationCenterMarker);
                    locationCenterMarker = null;
                }

                if (locationRadiusCircle) {
                    locationMap.removeLayer(locationRadiusCircle);
                    locationRadiusCircle = null;
                }

                locationMap.setView(getMapFallbackCenter(), 12);
                locationMap.invalidateSize();
                return;
            }

            const latLng = [center.latitude, center.longitude];

            if (!locationCenterMarker) {
                locationCenterMarker = L.marker(latLng, {
                    draggable: true,
                }).addTo(locationMap);

                locationCenterMarker.on('dragend', () => {
                    const markerLatLng = locationCenterMarker.getLatLng();
                    locationLatitudeInput.value = markerLatLng.lat.toFixed(7);
                    locationLongitudeInput.value = markerLatLng.lng.toFixed(7);
                    updateLocationPreview();
                    updateLocationMap();
                    scheduleLocationAutoSave('Pusat lokasi dipindahkan dari marker.');
                });
            } else {
                locationCenterMarker.setLatLng(latLng);
            }

            if (!locationRadiusCircle) {
                locationRadiusCircle = L.circle(latLng, {
                    radius,
                    color: '#2563eb',
                    weight: 2,
                    fillColor: '#60a5fa',
                    fillOpacity: 0.18,
                }).addTo(locationMap);
            } else {
                locationRadiusCircle.setLatLng(latLng);
                locationRadiusCircle.setRadius(radius);
            }

            const bounds = locationRadiusCircle.getBounds();
            if (bounds?.isValid?.()) {
                locationMap.fitBounds(bounds, { padding: [32, 32] });
            } else {
                locationMap.setView(latLng, 16, { animate: false });
            }
            locationMap.invalidateSize();
        }
        function updateLocationPreview() {
            const center = getLocationInputs();
            const radius = getLocationRadius();

            if (locationPreviewCenter) {
                locationPreviewCenter.textContent = center.latitude !== null && center.longitude !== null
                    ? `${center.latitude.toFixed(7)}, ${center.longitude.toFixed(7)}`
                    : '-';
            }

            if (locationPreviewRadius) {
                locationPreviewRadius.textContent = `${Number.isFinite(radius) ? radius : 300} meter`;
            }

            if (locationPreviewCurrent) {
                if (browserLocation) {
                    locationPreviewCurrent.textContent = `${browserLocation.latitude.toFixed(7)}, ${browserLocation.longitude.toFixed(7)}`;
                } else {
                    locationPreviewCurrent.textContent = '-';
                }
            }

            if (locationPreviewDistance) {
                if (center.latitude !== null && center.longitude !== null && browserLocation) {
                    const distance = haversineMeters(
                        browserLocation.latitude,
                        browserLocation.longitude,
                        center.latitude,
                        center.longitude
                    );
                    const status = distance <= (Number.isFinite(radius) ? radius : 300) ? 'di dalam radius' : 'di luar radius';
                    locationPreviewDistance.textContent = `${formatDistance(distance)} (${status})`;
                } else {
                    locationPreviewDistance.textContent = '-';
                }
            }

            updateLocationMap();
        }
        function setLocationStatus(message, tone = 'info') {
            if (!locationStatus) return;

            const toneClass = tone === 'error'
                ? 'text-red-600'
                : tone === 'success'
                    ? 'text-emerald-600'
                    : 'text-gray-500';

            locationStatus.className = `mt-3 text-xs ${toneClass}`;
            locationStatus.textContent = message;
        }
        function getLocationCsrfToken() {
            return locationSettingForm?.querySelector('input[name="_token"]')?.value ?? '';
        }
        function buildLocationPayload() {
            const center = getLocationInputs();
            const radius = getLocationRadius();

            if (center.latitude === null || center.longitude === null) {
                return null;
            }

            return {
                center_latitude: center.latitude,
                center_longitude: center.longitude,
                radius_meters: radius,
            };
        }
        async function saveLocationSetting({ silent = false, reason = 'manual' } = {}) {
            if (!locationSettingForm || isSavingLocation) return false;

            const payload = buildLocationPayload();
            if (!payload) {
                setLocationStatus('Isi latitude dan longitude dulu sebelum menyimpan lokasi.', 'error');
                return false;
            }

            isSavingLocation = true;
            if (!silent) {
                setLocationStatus(reason === 'auto' ? 'Menyimpan lokasi otomatis...' : 'Menyimpan lokasi...', 'info');
            }

            try {
                const response = await fetch(locationSettingForm.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getLocationCsrfToken(),
                    },
                    body: new URLSearchParams({
                        _token: getLocationCsrfToken(),
                        center_latitude: String(payload.center_latitude),
                        center_longitude: String(payload.center_longitude),
                        radius_meters: String(payload.radius_meters),
                    }),
                });

                const data = await response.json().catch(() => null);

                if (!response.ok) {
                    const message = data?.message
                        ?? Object.values(data?.errors ?? {})?.flat?.()?.[0]
                        ?? 'Gagal menyimpan lokasi.';
                    throw new Error(message);
                }

                initialLocation = { ...payload };
                setLocationStatus(data?.message ?? 'Pengaturan lokasi berhasil disimpan.', 'success');
                return true;
            } catch (error) {
                console.error(error);
                const message = error?.message ?? 'Gagal menyimpan lokasi.';
                setLocationStatus(message, 'error');
                if (!silent) {
                    alert(message);
                }
                return false;
            } finally {
                isSavingLocation = false;
            }
        }
        function scheduleLocationAutoSave(reason = 'auto') {
            if (!locationSettingForm) return;

            window.clearTimeout(autoSaveTimer);
            autoSaveTimer = window.setTimeout(() => {
                saveLocationSetting({ silent: true, reason });
            }, 900);
        }
        function resetLocationToInitial() {
            if (!locationLatitudeInput || !locationLongitudeInput || !locationRadiusInput) return;

            window.clearTimeout(autoSaveTimer);

            if (initialLocation?.latitude !== null && initialLocation?.latitude !== undefined) {
                locationLatitudeInput.value = initialLocation.latitude;
            } else {
                locationLatitudeInput.value = '';
            }

            if (initialLocation?.longitude !== null && initialLocation?.longitude !== undefined) {
                locationLongitudeInput.value = initialLocation.longitude;
            } else {
                locationLongitudeInput.value = '';
            }

            if (initialLocation?.radius !== null && initialLocation?.radius !== undefined) {
                locationRadiusInput.value = initialLocation.radius;
            } else {
                locationRadiusInput.value = 300;
            }

            updateLocationPreview();
            setLocationStatus('Lokasi dikembalikan ke nilai awal.', 'info');
            if (buildLocationPayload()) {
                scheduleLocationAutoSave('reset');
            }
        }
        function getBrowserLocation() {
            return new Promise((resolve, reject) => {
                if (!navigator.geolocation) {
                    reject(new Error('Geolocation tidak didukung oleh browser ini.'));
                    return;
                }

                navigator.geolocation.getCurrentPosition(
                    resolve,
                    reject,
                    {
                        enableHighAccuracy: true,
                        timeout: 12000,
                        maximumAge: 0,
                    }
                );
            });
        }
        function getLocationErrorMessage(error) {
            switch (Number(error?.code)) {
                case 1:
                    return 'Izin lokasi ditolak. Izinkan akses lokasi lalu coba lagi.';
                case 2:
                    return 'Lokasi tidak tersedia. Coba aktifkan GPS atau pindah ke area dengan sinyal lebih baik.';
                case 3:
                    return 'Pengambilan lokasi melebihi batas waktu. Coba ulangi.';
                default:
                    return error?.message ?? 'Gagal membaca lokasi perangkat.';
            }
        }
        async function hydrateBrowserLocationForPreview() {
            try {
                const position = await getBrowserLocation();
                const coords = position.coords ?? {};
                const latitude = Number(coords.latitude);
                const longitude = Number(coords.longitude);

                if (!Number.isFinite(latitude) || !Number.isFinite(longitude)) {
                    return;
                }

                browserLocation = { latitude, longitude };
                updateLocationPreview();

                if (locationStatus && !locationStatus.textContent.includes('Lokasi berhasil diambil')) {
                    locationStatus.textContent = `Lokasi browser terbaca. Accuracy: ${Math.round(coords.accuracy ?? 0)} meter.`;
                }
            } catch (error) {
                console.info('Browser location preview skipped:', error?.message ?? error);
            }
        }
        async function fillCurrentLocation() {
            if (!useBrowserLocationBtn || !locationLatitudeInput || !locationLongitudeInput) return;

            useBrowserLocationBtn.disabled = true;
            useBrowserLocationBtn.classList.add('opacity-60', 'cursor-not-allowed');
            if (locationStatus) {
                locationStatus.textContent = 'Mengambil lokasi dari browser...';
            }

            try {
                const position = await getBrowserLocation();
                const coords = position.coords ?? {};
                const latitude = Number(coords.latitude);
                const longitude = Number(coords.longitude);
                if (!Number.isFinite(latitude) || !Number.isFinite(longitude)) {
                    throw new Error('Koordinat lokasi tidak valid.');
                }

                browserLocation = { latitude, longitude };
                locationLatitudeInput.value = latitude.toFixed(7);
                locationLongitudeInput.value = longitude.toFixed(7);
                updateLocationPreview();
                setLocationStatus(`Lokasi berhasil diambil. Accuracy: ${Math.round(coords.accuracy ?? 0)} meter.`, 'success');
                scheduleLocationAutoSave('browser');
            } catch (error) {
                console.error(error);
                const message = getLocationErrorMessage(error);
                setLocationStatus(message, 'error');
                alert(message);
            } finally {
                useBrowserLocationBtn.disabled = false;
                useBrowserLocationBtn.classList.remove('opacity-60', 'cursor-not-allowed');
            }
        }

        if (locationLatitudeInput) {
            locationLatitudeInput.addEventListener('input', updateLocationPreview);
        }

        if (locationLongitudeInput) {
            locationLongitudeInput.addEventListener('input', updateLocationPreview);
        }

        if (locationRadiusInput) {
            locationRadiusInput.addEventListener('input', updateLocationPreview);
        }

        if (resetLocationBtn) {
            resetLocationBtn.addEventListener('click', resetLocationToInitial);
        }

        if (locationSettingForm) {
            locationSettingForm.addEventListener('submit', async (event) => {
                event.preventDefault();
                window.clearTimeout(autoSaveTimer);
                await saveLocationSetting({ silent: false, reason: 'manual' });
            });
        }
        // Jam admin dibuat hidup agar tampilan selalu terasa aktual.
        function updateAdminClock() {
            const now = new Date();
            if (currentDateAdmin) {
                currentDateAdmin.textContent = now.toLocaleDateString('id-ID', {
                    weekday: 'long',
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric',
                    timeZone: 'Asia/Jakarta'
                });
            }
            if (currentTimeAdmin) {
                currentTimeAdmin.textContent = now.toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: false,
                    timeZone: 'Asia/Jakarta'
                }) + ' WIB';
            }
        }
        // Sinkronisasi ruang membaca ulang status per pejabat dari backend.
        async function syncAdminQueueStatus() {
            try {
                const url = new URL("{{ route('queue.status') }}", window.location.origin);
                const res = await fetch(url.toString());
                const data = await res.json();
                renderRoomSyncRows(data?.per_user_statuses ?? []);
            } catch (error) {
                console.error(error);
            }
        }
        // Render tabel sinkronisasi ruang berdasarkan data terbaru.
        function renderRoomSyncRows(rows) {
            if (!roomSyncBody) return;
            const formatTime = (value) => value ? `${value} WIB` : '-';
            const statusBadge = (status, label) => {
                if (status === 'open') {
                    return `<span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700"><i class="fa-solid fa-door-open"></i>${label ?? 'Antrean Dibuka'}</span>`;
                }
                if (status === 'occupied') {
                    return `<span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full bg-amber-100 text-amber-700"><i class="fa-solid fa-hourglass-half"></i>${label ?? 'Melayani'}</span>`;
                }
                return `<span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full bg-rose-100 text-rose-700"><i class="fa-solid fa-door-closed"></i>${label ?? 'Antrean Ditutup'}</span>`;
            };
            if (!Array.isArray(rows) || rows.length === 0) {
                roomSyncBody.innerHTML = `
                    <tr>
                        <td colspan="6" class="px-3 py-6 text-center text-gray-500">
                            Belum ada data sinkronisasi ruang antrean.
                        </td>
                    </tr>
                `;
                return;
            }
            roomSyncBody.innerHTML = rows.map((item) => `
                <tr class="border-b border-blue-50">
                    <td class="px-3 py-3">
                        <div class="font-semibold text-gray-900">${item.name ?? item.kode ?? '-'}</div>
                        <div class="text-xs text-gray-500">${item.kode ?? '-'}</div>
                    </td>
                    <td class="px-3 py-3">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs bg-blue-50 text-blue-700 font-medium">
                            ${item.service?.nama_layanan ?? '-'}
                        </span>
                    </td>
                    <td class="px-3 py-3 text-center">${statusBadge(item.queue_status, item.queue_status_label)}</td>
                    <td class="px-3 py-3 text-center font-medium text-indigo-700">${formatTime(item.waktu?.expected_jam_tutup)}</td>
                    <td class="px-3 py-3 text-center text-slate-700">${formatTime(item.waktu?.jam_buka)}</td>
                    <td class="px-3 py-3 text-center text-slate-700">${formatTime(item.waktu?.jam_tutup)}</td>
                </tr>
            `).join('');
        }
        updateLocationPreview();
        hydrateBrowserLocationForPreview();
        syncAdminQueueStatus();
        setInterval(syncAdminQueueStatus, 5000);
        updateAdminClock();
        setInterval(updateAdminClock, 1000);

        if (useBrowserLocationBtn) {
            useBrowserLocationBtn.addEventListener('click', fillCurrentLocation);
        }
        if (window.Echo) {
            window.Echo.channel('queue-status')
                .listen('.queue.status.updated', () => {
                    syncAdminQueueStatus();
                });
        }
        const tabs = document.querySelectorAll('.tab-btn');
        const contents = document.querySelectorAll('.tab-content');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                tabs.forEach(t => t.classList.remove('is-active', 'text-blue-600'));
                contents.forEach(c => c.classList.add('hidden'));

                tab.classList.add('is-active', 'text-blue-600');
                document.getElementById(tab.dataset.tab).classList.remove('hidden');
            });
        });
        document.getElementById('searchUser').addEventListener('input', function() {
            const query = this.value.toLowerCase();
            document.querySelectorAll('.user-card').forEach(card => {
                const name = card.querySelector('.user-name').textContent.toLowerCase();
                const kode = card.querySelector('.user-kode').textContent.toLowerCase();
                card.style.display = (name.includes(query) || kode.includes(query)) ? '' : 'none';
            });
        });
        document.getElementById('searchService').addEventListener('input', function() {
            const query = this.value.toLowerCase();
            document.querySelectorAll('.service-card').forEach(card => {
                const name = card.querySelector('.service-name').textContent.toLowerCase();
                const desc = card.querySelector('p.text-sm').textContent.toLowerCase();
                card.style.display = (name.includes(query) || desc.includes(query)) ? '' : 'none';
            });
        });
    </script>

    @include('partials.pwa-scripts')


</body>

</html>
