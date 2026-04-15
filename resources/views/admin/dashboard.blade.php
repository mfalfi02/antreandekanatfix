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

    {{-- Header --}}
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

    {{-- Dashboard Content --}}
    <div class="min-h-screen">
        <div class="max-w-7xl mx-auto space-y-8">

            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div
                    class="panel p-6 flex items-center gap-4 hover:scale-105 transition-transform">
                    <i class="fa-solid fa-users text-4xl text-blue-600"></i>
                    <div>
                        <p class="text-3xl font-bold text-gray-900">{{ $data['totalUsers'] }}</p>
                        <p class="text-sm text-gray-500">Total Pengguna</p>
                    </div>
                </div>

                <div
                    class="panel p-6 flex items-center gap-4 hover:scale-105 transition-transform">
                    <i class="fa-solid fa-signal text-4xl text-green-600"></i>
                    <div>
                        <p class="text-3xl font-bold text-gray-900">{{ $data['activeQueues'] }}</p>
                        <p class="text-sm text-gray-500">Antrean Aktif</p>
                    </div>
                </div>

                <div
                    class="panel p-6 flex items-center gap-4 hover:scale-105 transition-transform">
                    <i class="fa-solid fa-calendar-check text-4xl text-orange-600"></i>
                    <div>
                        <p class="text-3xl font-bold text-gray-900">{{ $data['completedQueues'] }}</p>
                        <p class="text-sm text-gray-500">Selesai Hari Ini</p>
                    </div>
                </div>

                <div
                    class="panel p-6 flex items-center gap-4 hover:scale-105 transition-transform">
                    <i class="fa-solid fa-layer-group text-4xl text-purple-600"></i>
                    <div>
                        <p class="text-3xl font-bold text-gray-900">{{ $data['totalServices'] }}</p>
                        <p class="text-sm text-gray-500">Kategori Layanan</p>
                    </div>
                </div>
            </div>

            <div class="panel p-6">
                @php
                    $dashboardMonths = [
                        1 => 'Januari',
                        2 => 'Februari',
                        3 => 'Maret',
                        4 => 'April',
                        5 => 'Mei',
                        6 => 'Juni',
                        7 => 'Juli',
                        8 => 'Agustus',
                        9 => 'September',
                        10 => 'Oktober',
                        11 => 'November',
                        12 => 'Desember',
                    ];
                    $selectedDashboardMonth = $dashboardMonth ?? now()->month;
                    $selectedDashboardYear = $dashboardYear ?? now()->year;
                    $availableDashboardYears = $availableDashboardYears ?? [now()->year];
                    $dashboardPeriodLabel = $dashboardPeriodLabel ?? ($dashboardMonths[$selectedDashboardMonth] . ' ' . $selectedDashboardYear);
                @endphp

                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4 mb-4">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">Sinkronisasi Ruang Antrean per Periode</h2>
                        <p class="text-sm text-gray-500">Monitoring status room dan waktu layanan dosen per bulan.</p>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                        <span class="text-xs px-2.5 py-1 rounded-full bg-blue-100 text-blue-700 font-semibold">Auto Sync</span>
                        <span class="text-xs px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 font-semibold">Periode {{ $dashboardPeriodLabel }}</span>
                    </div>
                </div>

                <form id="roomPeriodForm" method="GET" action="{{ route('adm') }}"
                    class="flex flex-col md:flex-row md:items-end gap-3 mb-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 flex-1">
                        <label class="block">
                            <span class="mb-1.5 block text-sm font-medium text-slate-700">Bulan</span>
                            <select name="month" class="soft-input w-full px-3 py-2 text-sm">
                                @foreach ($dashboardMonths as $monthNumber => $monthLabel)
                                    <option value="{{ $monthNumber }}" {{ (int) $selectedDashboardMonth === $monthNumber ? 'selected' : '' }}>
                                        {{ $monthLabel }}
                                    </option>
                                @endforeach
                            </select>
                        </label>
                        <label class="block">
                            <span class="mb-1.5 block text-sm font-medium text-slate-700">Tahun</span>
                            <select name="year" class="soft-input w-full px-3 py-2 text-sm">
                                @foreach ($availableDashboardYears as $year)
                                    <option value="{{ $year }}" {{ (int) $selectedDashboardYear === (int) $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </label>
                    </div>
                    <button type="submit" class="btn-brand px-4 py-2 text-sm inline-flex items-center justify-center gap-2">
                        <i class="fa-solid fa-filter"></i> Tampilkan
                    </button>
                </form>

                <div class="admin-room-table-wrap overflow-x-auto rounded-xl border border-blue-100">
                    <table class="room-sync-table min-w-full text-sm">
                        <thead>
                            <tr>
                                <th class="px-3 py-3 text-left">Dosen/Pejabat</th>
                                <th class="px-3 py-3 text-left">Jenis Layanan</th>
                                <th class="px-3 py-3 text-center">Status</th>
                                <th class="px-3 py-3 text-center">Expected Buka</th>
                                <th class="px-3 py-3 text-center">Perkiraan Tutup</th>
                                <th class="px-3 py-3 text-center">Jam Buka</th>
                                <th class="px-3 py-3 text-center">Jam Tutup</th>
                            </tr>
                        </thead>
                        <tbody id="room-sync-body">
                            <tr>
                                <td colspan="7" class="px-3 py-6 text-center text-gray-500">
                                    Memuat sinkronisasi ruang antrean...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Users Section --}}
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

                {{-- Tabs Navigation --}}
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

                {{-- Tab Content --}}
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

            {{-- Service Categories --}}
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


            {{-- Reports & Analytics --}}
            <div class="panel p-6 mt-8">
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
        const currentDateAdmin = document.getElementById('current-date-admin');
        const currentTimeAdmin = document.getElementById('current-time-admin');
        const roomSyncBody = document.getElementById('room-sync-body');

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

        async function syncAdminQueueStatus() {
            try {
                const periodForm = document.getElementById('roomPeriodForm');
                const params = new URLSearchParams();

                if (periodForm) {
                    const formData = new FormData(periodForm);
                    const month = formData.get('month');
                    const year = formData.get('year');

                    if (month) params.set('month', month);
                    if (year) params.set('year', year);
                }

                const url = new URL("{{ route('queue.status') }}", window.location.origin);
                params.forEach((value, key) => url.searchParams.set(key, value));

                const res = await fetch(url.toString());
                const data = await res.json();
                renderRoomSyncRows(data?.per_user_statuses ?? []);
            } catch (error) {
                console.error(error);
            }
        }

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
                        <td colspan="7" class="px-3 py-6 text-center text-gray-500">
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
                    <td class="px-3 py-3 text-center font-medium text-slate-700">${formatTime(item.waktu?.expected_jam_buka)}</td>
                    <td class="px-3 py-3 text-center font-medium text-indigo-700">${formatTime(item.waktu?.expected_jam_tutup)}</td>
                    <td class="px-3 py-3 text-center text-slate-700">${formatTime(item.waktu?.jam_buka)}</td>
                    <td class="px-3 py-3 text-center text-slate-700">${formatTime(item.waktu?.jam_tutup)}</td>
                </tr>
            `).join('');
        }

        syncAdminQueueStatus();
        updateAdminClock();
        setInterval(updateAdminClock, 1000);

        if (window.Echo) {
            window.Echo.channel('queue-status')
                .listen('.queue.status.updated', () => {
                    syncAdminQueueStatus();
                });
        }

        // === Tab Navigation ===
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

        // === Search Users ===
        document.getElementById('searchUser').addEventListener('input', function() {
            const query = this.value.toLowerCase();
            document.querySelectorAll('.user-card').forEach(card => {
                const name = card.querySelector('.user-name').textContent.toLowerCase();
                const kode = card.querySelector('.user-kode').textContent.toLowerCase();
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

    @include('partials.pwa-scripts')


</body>

</html>
