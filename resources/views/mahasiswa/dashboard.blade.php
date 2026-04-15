<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Antrean Dekanat FTI</title>
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

        .combo-wrap {
            position: relative;
        }

        .combo-box {
            width: 100%;
            appearance: none;
            border: 1px solid #cfdcf1;
            border-radius: .75rem;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            padding: .62rem 2.5rem .62rem .9rem;
            color: #0f172a;
            font-weight: 500;
            box-shadow: 0 1px 0 rgba(15, 23, 42, .03);
            transition: border-color .2s ease, box-shadow .2s ease, background-color .2s ease;
        }

        .combo-box:hover {
            border-color: #b9cae7;
        }

        .combo-box:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, .18);
            background: #fff;
        }

        .combo-icon {
            position: absolute;
            right: .85rem;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            pointer-events: none;
            font-size: .78rem;
        }

        .staff-status-card {
            border: 1px solid #dbe6f6;
            border-radius: .9rem;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            box-shadow: 0 10px 24px -22px rgba(15, 23, 42, .45);
            transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
        }

        .staff-status-card:hover {
            transform: translateY(-2px);
            border-color: #bfdbfe;
            box-shadow: 0 16px 28px -24px rgba(37, 99, 235, .35);
        }

        .soft-scroll {
            scrollbar-width: thin;
            scrollbar-color: #c5d4eb #f6f9ff;
        }

        .soft-scroll::-webkit-scrollbar {
            width: 8px;
        }

        .soft-scroll::-webkit-scrollbar-thumb {
            background: #c5d4eb;
            border-radius: 999px;
        }

        .soft-scroll::-webkit-scrollbar-track {
            background: #f6f9ff;
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

        .btn-brand:active {
            transform: translateY(1px);
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

        .btn-danger:active {
            transform: translateY(1px);
        }

        @media (max-width: 768px) {
            body {
                padding: .9rem;
            }

            .panel {
                border-radius: .85rem;
            }

            .mhs-header-actions {
                width: 100%;
                justify-content: space-between;
                gap: .6rem;
                flex-wrap: wrap;
            }

            .mhs-header-actions form {
                width: 100%;
            }

            .mhs-header-actions .btn-danger {
                width: 100%;
                justify-content: center;
            }

            .staff-status-card {
                padding: .85rem;
            }

            #my-queue-list > div {
                align-items: flex-start;
            }

            #queue-call-toast-container {
                left: .75rem;
                right: .75rem;
            }

            #queue-call-toast-container > div {
                width: 100%;
            }
        }
    </style>
</head>

<body class="p-4 md:p-7">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- HEADER --}}
        <div class="panel p-5 md:p-6 flex flex-col gap-3 md:flex-row md:justify-between md:items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    Dashboard {{ ucfirst($data['user']->role) }}
                </h1>
                <p class="text-gray-600">
                    Selamat datang, {{ $data['user']->name }}
                </p>
            </div>
            <div class="mhs-header-actions flex items-center gap-4">
                <div class="text-right">
                    <p id="current-date-mahasiswa" class="text-xs text-gray-500">-</p>
                    <p id="current-time-mahasiswa" class="text-sm font-semibold text-gray-800">- WIB</p>
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

        {{-- STATUS PEJABAT --}}
        <div class="panel p-6 mt-6">
            <div class="mb-4">
                <h2 class="text-xl font-semibold text-gray-900">Status Dosen Dekanat</h2>
                <p class="text-sm text-gray-500">Lihat dosen yang sedang buka antrean beserta layanan yang aktif.</p>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 max-h-[28rem] overflow-y-auto soft-scroll pr-1">
                @foreach ($data['pejabat'] as $pejabat)
                    @php
                        $statusPejabat = $data['pejabat_statuses'][$pejabat->kode] ?? 'closed';
                        $servicePejabat = $data['pejabat_services'][$pejabat->kode] ?? null;
                        $expectedClosePejabat = $data['pejabat_expected_close'][$pejabat->kode] ?? null;
                    @endphp
                    <div class="staff-status-card p-4">
                        <div class="flex justify-between items-start gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div
                                    class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 flex-shrink-0">
                                    <i class="fa fa-user"></i>
                                </div>
                                <div class="min-w-0 space-y-1.5">
                                    <p class="font-semibold text-gray-900 truncate">{{ $pejabat->name }}</p>
                                    <p class="text-xs text-slate-600 font-semibold flex items-center gap-1.5 leading-relaxed">
                                        <i class="fa-solid fa-id-card text-slate-400"></i>
                                        {{ $pejabat->kode }}
                                    </p>
                                    <p class="text-sm text-slate-600 flex items-center gap-1.5 leading-relaxed">
                                        <i class="fa-solid fa-briefcase text-slate-400"></i>
                                        {{ ucfirst($pejabat->jabatan ?? '-') }}
                                    </p>
                                    <p class="text-xs text-slate-600 flex items-center gap-1.5 leading-relaxed">
                                        <i class="fa fa-map-marker-alt text-slate-400"></i> {{ $pejabat->ruangan ?? '-' }}
                                    </p>
                                </div>
                            </div>
                            <div id="status-pejabat-{{ $pejabat->kode }}" class="shrink-0">
                                @if ($statusPejabat == 'open')
                                    <span
                                        class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full bg-green-100 text-green-800 ring-1 ring-green-200 shadow-sm">
                                        <i class="fa-solid fa-door-open"></i>
                                        Antrean Dibuka
                                    </span>
                                @elseif($statusPejabat == 'occupied')
                                    <span
                                        class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full bg-yellow-100 text-yellow-800 ring-1 ring-yellow-200 shadow-sm">
                                        <i class="fa-solid fa-hourglass-half"></i>
                                        Melayani
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full bg-red-100 text-red-800 ring-1 ring-red-200 shadow-sm">
                                        <i class="fa-solid fa-door-closed"></i>
                                        Antrean Ditutup
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="mt-3 space-y-1.5">
                            <p id="service-pejabat-{{ $pejabat->kode }}" class="text-xs text-slate-600 flex items-center gap-1.5 leading-relaxed">
                                <i class="fa-solid fa-screwdriver-wrench text-slate-400"></i>
                                {{ $servicePejabat ?: '-' }}
                            </p>
                            <p id="expected-close-pejabat-{{ $pejabat->kode }}" class="text-xs text-slate-600 flex items-center gap-1.5 leading-relaxed">
                                <i class="fa-solid fa-clock text-slate-400"></i>
                                {{ $expectedClosePejabat ?: '-' }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- BAGIAN PENGONTROL UNTUK PEJABAT --}}
        @if ($data['user']->role === 'pejabat')
            <div class="panel p-6">
                <h2 class="text-xl font-semibold text-gray-900 flex items-center gap-2 mb-4">
                    <i class="fa-solid fa-play-circle text-gray-600"></i> Kontrol Antrean
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Jenis Layanan</label>
                        <div class="combo-wrap mt-2">
                            <select id="service-select" class="combo-box">
                                <option value="">Pilih jenis layanan</option>
                                <option value="all">Semua layanan</option>
                                @foreach ($data['services'] as $service)
                                    <option value="{{ $service->id }}">{{ $service->nama_layanan }}</option>
                                @endforeach
                            </select>
                            <i class="fa-solid fa-chevron-down combo-icon"></i>
                        </div>
                    </div>

                    <div class="flex items-center space-x-2">
                        <button id="open-queue-btn-1"
                            class="btn-brand px-4 py-2">Buka</button>
                        <button id="close-queue-btn-1"
                            class="btn-danger px-4 py-2 hidden">Tutup</button>
                    </div>
                </div>
            </div>

            {{-- Statistik --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="panel p-6 flex items-center gap-4">
                    <i class="fa-solid fa-users text-3xl text-gray-600"></i>
                    <div>
                        <p class="text-2xl font-bold">{{ $data['activeQueues'] }}</p>
                        <p class="text-sm text-gray-500">Menunggu</p>
                    </div>
                </div>

                <div class="panel p-6 flex items-center gap-4">
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
            @if (in_array($data['user']->role, ['mahasiswa', 'dosen']))
                @php
                    $currentQueue = collect($data['myQueues'] ?? [])->first(function ($queue) {
                        return in_array($queue->status ?? '', ['menunggu', 'diproses'], true);
                    });
                    $currentQueueStatus = match ($currentQueue->status ?? null) {
                        'diproses' => 'Melayani',
                        'menunggu' => 'Menunggu',
                        default => 'Tidak ada antrean aktif',
                    };
                @endphp
                <div class="panel p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Nomor Antrean Anda Saat Ini</h2>
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                        <div class="rounded-xl border border-blue-100 bg-blue-50 px-4 py-3">
                            <p class="text-xs uppercase tracking-wide text-blue-700 font-semibold">Nomor</p>
                            <p id="current-queue-number" class="mt-1 text-2xl font-extrabold text-blue-800">
                                {{ $currentQueue ? '#' . $currentQueue->nomor_antrian : '-' }}
                            </p>
                        </div>
                        <div class="rounded-xl border border-amber-100 bg-amber-50 px-4 py-3">
                            <p class="text-xs uppercase tracking-wide text-amber-700 font-semibold">Status</p>
                            <p id="current-queue-status" class="mt-1 text-base font-bold text-amber-800">
                                {{ $currentQueueStatus }}
                            </p>
                        </div>
                        <div class="rounded-xl border border-indigo-100 bg-indigo-50 px-4 py-3">
                            <p class="text-xs uppercase tracking-wide text-indigo-700 font-semibold">Layanan</p>
                            <p id="current-queue-service" class="mt-1 text-sm font-semibold text-indigo-800">
                                {{ $currentQueue->service->nama_layanan ?? '-' }}
                            </p>
                        </div>
                        <div class="rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-3">
                            <p class="text-xs uppercase tracking-wide text-emerald-700 font-semibold">Dosen</p>
                            <p id="current-queue-dosen" class="mt-1 text-sm font-semibold text-emerald-800">
                                {{ $currentQueue->dosen->name ?? '-' }}
                            </p>
                        </div>
                        <div class="rounded-xl border border-fuchsia-100 bg-fuchsia-50 px-4 py-3">
                            <p class="text-xs uppercase tracking-wide text-fuchsia-700 font-semibold">Estimasi Tunggu</p>
                            <p id="current-queue-estimate" class="mt-1 text-sm font-semibold text-fuchsia-800">
                                -
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Ambil Antrean --}}
                <div class="panel p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Ambil Nomor Antrean</h2>
                    <div class="space-y-4">
                        <label class="block text-sm font-medium text-gray-700">Pilih Dosen Tujuan</label>
                        <div class="combo-wrap mt-2">
                            <select id="dean-select" class="combo-box">
                                <option value="">Pilih dosen</option>
                                @foreach ($data['pejabat'] as $pejabat)
                                    <option value="{{ $pejabat->kode }}">{{ $pejabat->name }} ({{ $pejabat->kode }})</option>
                                @endforeach
                            </select>
                            <i class="fa-solid fa-chevron-down combo-icon"></i>
                        </div>

                        <label class="block text-sm font-medium text-gray-700">Jenis Layanan</label>
                        <div class="combo-wrap mt-2">
                            <select id="service-select" class="combo-box">
                                <option value="">Pilih layanan</option>
                                @foreach ($data['services'] as $service)
                                    <option value="{{ $service->id }}">{{ $service->nama_layanan }}</option>
                                @endforeach
                            </select>
                            <i class="fa-solid fa-chevron-down combo-icon"></i>
                        </div>
                        <p id="service-filter-hint" class="text-xs text-gray-500">Pilih dosen terlebih dahulu.</p>

                        <button id="join-queue-btn"
                            class="btn-brand w-full px-4 py-2">
                            Ambil Nomor
                        </button>
                        <p id="join-queue-lock-hint" class="text-xs text-amber-700 hidden"></p>
                    </div>
                </div>

                {{-- Status Antrean --}}
                <div class="panel p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-semibold text-gray-900">Antrean Hari Ini</h2>
                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-700">
                            Mulai ulang setiap hari
                        </span>
                    </div>
                    <div id="my-queue-list" class="space-y-3 max-h-96 overflow-y-auto">
                        @forelse($data['myQueues'] as $queue)
                            <div class="p-3 border rounded-lg flex justify-between items-center">
                                <div>
                                    <p class="font-semibold text-gray-900">#{{ $queue->nomor_antrian }} -
                                        {{ $queue->service->nama_layanan ?? '-' }}</p>
                                    <p class="text-sm text-gray-500">{{ ucfirst($queue->status) }}</p>
                                    <p class="text-xs text-gray-500">Dosen: {{ $queue->dosen->name ?? '-' }}</p>
                                    <p class="text-xs text-gray-400">
                                        Tanggal: {{ optional($queue->created_at)->format('d-m-Y') ?? '-' }} |
                                        Jam: {{ optional($queue->created_at)->format('H:i') ?? '-' }} WIB
                                    </p>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500">Belum ada antrean.</p>
                        @endforelse
                    </div>

                    <div class="mt-5 border-t pt-4">
                        <button type="button" id="previous-queue-toggle"
                            class="w-full inline-flex items-center justify-between rounded-lg border border-gray-200 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                            <span>Riwayat Hari Sebelumnya</span>
                            <span id="previous-queue-count" class="text-xs text-gray-500">
                                {{ count($data['historyQueues'] ?? []) }} data
                            </span>
                        </button>
                        <div id="previous-queue-panel" class="mt-3 hidden">
                            <div class="flex flex-col md:flex-row gap-2 md:items-center mb-3">
                                <input type="date" id="previous-queue-date-filter"
                                    class="w-full md:w-auto rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                                <button type="button" id="previous-queue-date-reset"
                                    class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                                    Reset Filter
                                </button>
                            </div>
                            <div id="previous-queue-list" class="space-y-3 max-h-72 overflow-y-auto">
                                @forelse($data['historyQueues'] ?? [] as $queue)
                                    <div class="p-3 border rounded-lg flex justify-between items-center bg-slate-50/60">
                                        <div>
                                            <p class="font-semibold text-gray-900">#{{ $queue->nomor_antrian }} -
                                                {{ $queue->service->nama_layanan ?? '-' }}</p>
                                            <p class="text-sm text-gray-500">{{ ucfirst($queue->status) }}</p>
                                            <p class="text-xs text-gray-500">Dosen: {{ $queue->dosen->name ?? '-' }}</p>
                                            <p class="text-xs text-gray-400">
                                                Tanggal: {{ optional($queue->created_at)->format('d-m-Y') ?? '-' }} |
                                                Jam: {{ optional($queue->created_at)->format('H:i') ?? '-' }} WIB
                                            </p>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-gray-500">Belum ada riwayat hari sebelumnya.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div id="queue-call-toast-container" class="fixed top-4 right-4 z-50 space-y-2 pointer-events-none"></div>
    <div id="queue-call-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/55 p-4">
        <div class="w-full max-w-md rounded-2xl border border-blue-200 bg-white p-6 shadow-2xl">
            <div class="flex items-start gap-3">
                <span class="mt-0.5 h-10 w-10 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center">
                    <i class="fa-solid fa-bullhorn"></i>
                </span>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-gray-900">Nomor antrean Anda dipanggil</p>
                    <p id="queue-call-modal-number" class="text-2xl font-extrabold text-blue-700 mt-1">#-</p>
                    <p id="queue-call-modal-dosen" class="text-sm text-gray-600 mt-1">Dosen: -</p>
                    <p id="queue-call-modal-service" class="text-sm text-gray-600">Layanan: -</p>
                </div>
            </div>
            <div class="mt-5 flex justify-end gap-2">
                <button id="queue-call-modal-close"
                    class="px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <script>
        const currentDateMahasiswa = document.getElementById('current-date-mahasiswa');
        const currentTimeMahasiswa = document.getElementById('current-time-mahasiswa');
        const queueCallToastContainer = document.getElementById('queue-call-toast-container');
        const queueCallModal = document.getElementById('queue-call-modal');
        const queueCallModalNumber = document.getElementById('queue-call-modal-number');
        const queueCallModalDosen = document.getElementById('queue-call-modal-dosen');
        const queueCallModalService = document.getElementById('queue-call-modal-service');
        const queueCallModalClose = document.getElementById('queue-call-modal-close');
        const deanSelect = document.getElementById('dean-select');
        const serviceSelect = document.getElementById('service-select');
        const joinQueueBtn = document.getElementById('join-queue-btn');
        const joinQueueLockHint = document.getElementById('join-queue-lock-hint');
        const myQueueList = document.getElementById('my-queue-list');
        const previousQueueToggle = document.getElementById('previous-queue-toggle');
        const previousQueuePanel = document.getElementById('previous-queue-panel');
        const previousQueueList = document.getElementById('previous-queue-list');
        const previousQueueCount = document.getElementById('previous-queue-count');
        const previousQueueDateFilter = document.getElementById('previous-queue-date-filter');
        const previousQueueDateReset = document.getElementById('previous-queue-date-reset');
        const serviceFilterHint = document.getElementById('service-filter-hint');
        const currentQueueNumberEl = document.getElementById('current-queue-number');
        const currentQueueStatusEl = document.getElementById('current-queue-status');
        const currentQueueServiceEl = document.getElementById('current-queue-service');
        const currentQueueDosenEl = document.getElementById('current-queue-dosen');
        const currentQueueEstimateEl = document.getElementById('current-queue-estimate');
        const csrfToken = "{{ csrf_token() }}";
        const currentUserKode = "{{ $data['user']->kode }}";
        const currentUserRole = "{{ $data['user']->role }}";
        const deanStatusMap = @json($data['pejabat_statuses'] ?? []);
        const deanServiceIdsMap = {};
        const allServiceOptions = serviceSelect ? Array.from(serviceSelect.options).map((opt) => ({
            value: opt.value,
            text: opt.text,
        })) : [];
        let queueStatusById = new Map();
        let queuePollingInitialized = false;
        const notifiedQueueIds = new Set();
        let previousQueueCache = @json($data['historyQueues'] ?? []);
        let activeQueueDeanSet = new Set();
        let isJoiningQueue = false;

        function toggleJoinButton(disabled) {
            if (!joinQueueBtn) return;
            joinQueueBtn.disabled = disabled;
            joinQueueBtn.classList.toggle('opacity-60', disabled);
            joinQueueBtn.classList.toggle('cursor-not-allowed', disabled);
        }

        function updateJoinQueueAvailability() {
            if (!joinQueueBtn) return;
            if (isJoiningQueue) return;

            const deanId = String(deanSelect?.value ?? '');
            const blockedByActiveQueue = deanId && activeQueueDeanSet.has(deanId);

            toggleJoinButton(Boolean(blockedByActiveQueue));

            if (!joinQueueLockHint) return;
            if (blockedByActiveQueue) {
                joinQueueLockHint.textContent = 'Anda masih memiliki antrean aktif pada pejabat ini. Selesaikan dulu, atau pilih pejabat lain.';
                joinQueueLockHint.classList.remove('hidden');
                return;
            }

            joinQueueLockHint.textContent = '';
            joinQueueLockHint.classList.add('hidden');
        }

        async function syncQueueStatus() {
            try {
                const res = await fetch("{{ route('queue.status') }}");
                const data = await res.json();
                if (Array.isArray(data?.per_user_statuses)) {
                    data.per_user_statuses.forEach((item) => {
                        const statusWrap = document.getElementById(`status-pejabat-${item.kode}`);
                        const serviceLine = document.getElementById(`service-pejabat-${item.kode}`);
                        const expectedCloseLine = document.getElementById(`expected-close-pejabat-${item.kode}`);
                        if (statusWrap) {
                            let badgeClass =
                                'inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full ring-1 shadow-sm';
                            let icon = 'fa-door-closed';
                            if (item.queue_status === 'open') {
                                badgeClass += ' bg-green-100 text-green-800 ring-green-200';
                                icon = 'fa-door-open';
                            } else if (item.queue_status === 'occupied') {
                                badgeClass += ' bg-yellow-100 text-yellow-800 ring-yellow-200';
                                icon = 'fa-hourglass-half';
                            } else {
                                badgeClass += ' bg-red-100 text-red-800 ring-red-200';
                            }
                            statusWrap.innerHTML =
                                `<span class="${badgeClass}"><i class="fa-solid ${icon}"></i>${item.queue_status_label}</span>`;
                        }
                        deanStatusMap[item.kode] = item.queue_status;
                        const serviceIds = Array.isArray(item.service?.ids) ?
                            item.service.ids.map((id) => Number(id)) :
                            (item.service?.id ? [Number(item.service.id)] : []);
                        deanServiceIdsMap[item.kode] = serviceIds;
                        if (serviceLine) {
                            serviceLine.innerHTML =
                                `<i class="fa-solid fa-screwdriver-wrench text-slate-400"></i> ${escapeHtml(item.service?.nama_layanan ?? '-')}`;
                        }
                        if (expectedCloseLine) {
                            expectedCloseLine.innerHTML =
                                `<i class="fa-solid fa-clock text-slate-400"></i> ${escapeHtml(item.waktu?.expected_jam_tutup ?? '-')}`;
                        }
                    });
                    syncServiceOptionsForSelectedDean();
                    updateJoinQueueAvailability();
                }
            } catch (error) {
                console.error(error);
            }
        }

        function syncServiceOptionsForSelectedDean() {
            if (!serviceSelect || !deanSelect) return;

            const deanId = deanSelect.value;
            const currentSelected = serviceSelect.value;
            const status = deanStatusMap[deanId] ?? 'closed';
            const openedServiceIds = deanServiceIdsMap[deanId] ?? [];
            let filteredOptions = allServiceOptions;

            if (deanId && !['open', 'occupied'].includes(status)) {
                // Ruangan dosen tutup: tetap tampil opsi, tapi informasikan lewat hint.
                filteredOptions = allServiceOptions;
                if (serviceFilterHint) {
                    serviceFilterHint.textContent = 'Dosen ini sedang menutup antrean.';
                }
            } else if (deanId && openedServiceIds.length > 0) {
                // Dosen membuka layanan tertentu: tampilkan placeholder + layanan yang dibuka.
                filteredOptions = allServiceOptions.filter((opt) =>
                    opt.value === '' || openedServiceIds.includes(Number(opt.value))
                );
                if (serviceFilterHint) {
                    serviceFilterHint.textContent = 'Layanan difilter sesuai layanan yang dibuka dosen.';
                }
            } else {
                // Dosen membuka semua layanan.
                filteredOptions = allServiceOptions;
                if (serviceFilterHint) {
                    serviceFilterHint.textContent = deanId ? 'Dosen membuka semua jenis layanan.' : 'Pilih dosen terlebih dahulu.';
                }
            }

            serviceSelect.innerHTML = filteredOptions
                .map((opt) => `<option value="${escapeHtml(opt.value)}">${escapeHtml(opt.text)}</option>`)
                .join('');

            if (filteredOptions.some((opt) => String(opt.value) === String(currentSelected))) {
                serviceSelect.value = currentSelected;
            } else {
                serviceSelect.value = '';
            }

            updateJoinQueueAvailability();
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        function updateMahasiswaClock() {
            const now = new Date();
            if (currentDateMahasiswa) {
                currentDateMahasiswa.textContent = now.toLocaleDateString('id-ID', {
                    weekday: 'long',
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric',
                    timeZone: 'Asia/Jakarta'
                });
            }
            if (currentTimeMahasiswa) {
                currentTimeMahasiswa.textContent = now.toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: false,
                    timeZone: 'Asia/Jakarta'
                }) + ' WIB';
            }
        }

        function showQueueCallToast(meta = {}) {
            if (!queueCallToastContainer) return;

            const nomor = meta.nomor_antrian ? `#${meta.nomor_antrian}` : '#-';
            const dosen = meta.dosen_name || '-';
            const layanan = meta.service_name || '-';

            const toast = document.createElement('div');
            toast.className =
                'pointer-events-auto w-[22rem] rounded-xl border border-blue-200 bg-white shadow-lg p-4 transform transition-all duration-300 translate-x-6 opacity-0';
            toast.innerHTML = `
                <div class="flex items-start gap-3">
                    <span class="mt-0.5 h-9 w-9 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center">
                        <i class="fa-solid fa-bullhorn"></i>
                    </span>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-gray-900">Nomor antrean Anda dipanggil</p>
                        <p class="text-sm text-blue-700 font-bold mt-0.5">${nomor}</p>
                        <p class="text-xs text-gray-600 mt-1">Dosen: ${escapeHtml(dosen)}</p>
                        <p class="text-xs text-gray-600">Layanan: ${escapeHtml(layanan)}</p>
                    </div>
                </div>
            `;

            queueCallToastContainer.appendChild(toast);
            requestAnimationFrame(() => {
                toast.classList.remove('translate-x-6', 'opacity-0');
            });

            setTimeout(() => {
                toast.classList.add('translate-x-6', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 6000);
        }

        function playQueueCallSound() {
            const AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (!AudioCtx) return;
            const ctx = new AudioCtx();
            const pattern = [880, 1047, 1319];

            pattern.forEach((freq, i) => {
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.value = freq;
                gain.gain.value = 0.0001;
                osc.connect(gain);
                gain.connect(ctx.destination);

                const start = ctx.currentTime + (i * 0.18);
                const end = start + 0.14;
                gain.gain.exponentialRampToValueAtTime(0.14, start + 0.02);
                gain.gain.exponentialRampToValueAtTime(0.0001, end);
                osc.start(start);
                osc.stop(end + 0.01);
            });
        }

        function showQueueCallModal(meta = {}) {
            if (!queueCallModal) return;
            const nomor = meta.nomor_antrian ? `#${meta.nomor_antrian}` : '#-';
            const dosen = meta.dosen_name || '-';
            const layanan = meta.service_name || '-';

            if (queueCallModalNumber) queueCallModalNumber.textContent = nomor;
            if (queueCallModalDosen) queueCallModalDosen.textContent = `Dosen: ${dosen}`;
            if (queueCallModalService) queueCallModalService.textContent = `Layanan: ${layanan}`;

            queueCallModal.classList.remove('hidden');
            queueCallModal.classList.add('flex');
        }

        function closeQueueCallModal() {
            if (!queueCallModal) return;
            queueCallModal.classList.add('hidden');
            queueCallModal.classList.remove('flex');
        }

        function renderMyQueueList(rows = []) {
            if (!myQueueList) return;
            if (!Array.isArray(rows) || rows.length === 0) {
                myQueueList.innerHTML = '<p class="text-gray-500">Belum ada antrean.</p>';
                return;
            }

            myQueueList.innerHTML = rows.map((queue) => {
                const createdAt = queue.created_at ? new Date(queue.created_at) : null;
                const tanggal = createdAt ?
                    createdAt.toLocaleDateString('id-ID', {
                        timeZone: 'Asia/Jakarta',
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric'
                    }) :
                    '-';
                const jam = createdAt ?
                    createdAt.toLocaleTimeString('id-ID', {
                        timeZone: 'Asia/Jakarta',
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: false
                    }) :
                    '-';

                return `
                    <div class="p-3 border rounded-lg flex justify-between items-center">
                        <div>
                            <p class="font-semibold text-gray-900">#${queue.nomor_antrian ?? '-'} - ${escapeHtml(queue.service?.nama_layanan ?? '-')}</p>
                            <p class="text-sm text-gray-500">${escapeHtml((queue.status ?? '').charAt(0).toUpperCase() + (queue.status ?? '').slice(1))}</p>
                            <p class="text-xs text-gray-500">Dosen: ${escapeHtml(queue.dosen?.name ?? '-')}</p>
                            <p class="text-xs text-gray-400">Tanggal: ${tanggal} | Jam: ${jam} WIB</p>
                        </div>
                    </div>
                `;
            }).join('');
        }

        function renderPreviousQueueList(rows = []) {
            if (!previousQueueList || !previousQueueCount) return;
            previousQueueCache = Array.isArray(rows) ? rows : [];
            const selectedDate = previousQueueDateFilter?.value ?? '';
            const filteredRows = selectedDate ?
                previousQueueCache.filter((queue) => {
                    const createdAt = queue?.created_at ? new Date(queue.created_at) : null;
                    if (!createdAt) return false;
                    return createdAt.toLocaleDateString('en-CA', {
                        timeZone: 'Asia/Jakarta'
                    }) === selectedDate;
                }) :
                previousQueueCache;

            previousQueueCount.textContent = `${filteredRows.length} data`;
            if (filteredRows.length === 0) {
                previousQueueList.innerHTML = '<p class="text-gray-500">Belum ada riwayat hari sebelumnya.</p>';
                return;
            }

            previousQueueList.innerHTML = filteredRows.map((queue) => {
                const createdAt = queue.created_at ? new Date(queue.created_at) : null;
                const tanggal = createdAt ?
                    createdAt.toLocaleDateString('id-ID', {
                        timeZone: 'Asia/Jakarta',
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric'
                    }) :
                    '-';
                const jam = createdAt ?
                    createdAt.toLocaleTimeString('id-ID', {
                        timeZone: 'Asia/Jakarta',
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: false
                    }) :
                    '-';

                return `
                    <div class="p-3 border rounded-lg flex justify-between items-center bg-slate-50/60">
                        <div>
                            <p class="font-semibold text-gray-900">#${queue.nomor_antrian ?? '-'} - ${escapeHtml(queue.service?.nama_layanan ?? '-')}</p>
                            <p class="text-sm text-gray-500">${escapeHtml((queue.status ?? '').charAt(0).toUpperCase() + (queue.status ?? '').slice(1))}</p>
                            <p class="text-xs text-gray-500">Dosen: ${escapeHtml(queue.dosen?.name ?? '-')}</p>
                            <p class="text-xs text-gray-400">Tanggal: ${tanggal} | Jam: ${jam} WIB</p>
                        </div>
                    </div>
                `;
            }).join('');
        }

        function renderCurrentQueueInfo(rows = []) {
            if (!currentQueueNumberEl || !currentQueueStatusEl || !currentQueueServiceEl || !currentQueueDosenEl || !currentQueueEstimateEl) return;

            const activeQueue = rows.find((q) => ['menunggu', 'diproses'].includes((q.status ?? '').toLowerCase()));
            if (!activeQueue) {
                currentQueueNumberEl.textContent = '-';
                currentQueueStatusEl.textContent = 'Tidak ada antrean aktif';
                currentQueueServiceEl.textContent = '-';
                currentQueueDosenEl.textContent = '-';
                currentQueueEstimateEl.textContent = '-';
                return;
            }

            const status = (activeQueue.status ?? '').toLowerCase();
            currentQueueNumberEl.textContent = activeQueue.nomor_antrian ? `#${activeQueue.nomor_antrian}` : '-';
            currentQueueStatusEl.textContent = status === 'diproses' ? 'Melayani' : 'Menunggu';
            currentQueueServiceEl.textContent = activeQueue.service?.nama_layanan ?? '-';
            currentQueueDosenEl.textContent = activeQueue.dosen?.name ?? '-';
            currentQueueEstimateEl.textContent = `${Number(activeQueue.estimated_wait_minutes ?? 0)} menit`;
        }

        function detectQueueCalledFromPolling(rows = []) {
            if (!['mahasiswa', 'dosen'].includes(currentUserRole)) return;

            rows.forEach((queue) => {
                const queueId = String(queue.id ?? '');
                if (!queueId) return;

                const prevStatus = queueStatusById.get(queueId);
                const currStatus = (queue.status ?? '').toLowerCase();
                queueStatusById.set(queueId, currStatus);

                if (!queuePollingInitialized) return;
                if (notifiedQueueIds.has(queueId)) return;

                // Fallback notifikasi jika realtime websocket tidak terkirim:
                // trigger saat status antrean mahasiswa berubah ke "diproses".
                if (currStatus === 'diproses' && prevStatus !== 'diproses') {
                    notifiedQueueIds.add(queueId);
                    showQueueCallToast({
                        nomor_antrian: queue.nomor_antrian,
                        dosen_name: queue.dosen?.name,
                        service_name: queue.service?.nama_layanan,
                    });
                    showQueueCallModal({
                        nomor_antrian: queue.nomor_antrian,
                        dosen_name: queue.dosen?.name,
                        service_name: queue.service?.nama_layanan,
                    });
                    playQueueCallSound();
                }
            });
        }

        async function syncMyQueues() {
            if (!myQueueList) return;
            try {
                const res = await fetch("{{ route('queue.my') }}");
                if (!res.ok) return;
                const data = await res.json();
                if (!['mahasiswa', 'dosen'].includes(data?.role)) return;
                const rows = Array.isArray(data.queues) ? data.queues : [];
                const historyRows = Array.isArray(data.history_queues) ? data.history_queues : [];
                activeQueueDeanSet = new Set(
                    rows
                    .filter((q) => ['menunggu', 'diproses'].includes((q.status ?? '').toLowerCase()))
                    .map((q) => String(q.kode_dosen ?? q.dosen?.kode ?? ''))
                    .filter(Boolean)
                );
                renderMyQueueList(rows);
                renderPreviousQueueList(historyRows);
                renderCurrentQueueInfo(rows);
                detectQueueCalledFromPolling(rows);
                updateJoinQueueAvailability();
                queuePollingInitialized = true;
            } catch (error) {
                console.error(error);
            }
        }

        async function joinQueue() {
            if (!joinQueueBtn) return;

            const deanId = deanSelect?.value;
            const serviceId = serviceSelect?.value;

            if (!deanId) {
                alert('Pilih dosen tujuan terlebih dahulu.');
                return;
            }

            if (activeQueueDeanSet.has(String(deanId))) {
                alert('Anda masih memiliki antrean aktif pada pejabat ini. Selesaikan dulu atau pilih pejabat lain.');
                return;
            }

            if (!['open', 'occupied'].includes(deanStatusMap[deanId] ?? 'closed')) {
                alert('Ruangan dosen yang dipilih sedang tutup. Silakan pilih dosen lain.');
                return;
            }

            if (!serviceId) {
                alert('Pilih jenis layanan terlebih dahulu.');
                return;
            }

            const openedServiceIds = deanServiceIdsMap[deanId] ?? [];
            if (openedServiceIds.length > 0 && !openedServiceIds.includes(Number(serviceId))) {
                alert('Layanan yang dipilih tidak sedang dibuka oleh dosen tersebut.');
                return;
            }

            isJoiningQueue = true;
            toggleJoinButton(true);

            try {
                const res = await fetch("{{ route('queue.join') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({
                        dean_id: deanId,
                        service_id: serviceId,
                    }),
                });

                const data = await res.json().catch(() => null);
                if (!res.ok) {
                    alert(data?.message ?? 'Gagal mengambil nomor antrean.');
                    return;
                }

                await syncMyQueues();
                const nomor = data?.queue?.nomor_antrian ?? '-';
                const dosenNama = data?.queue?.dosen?.name ?? '-';
                const dosenKode = data?.queue?.kode_dosen ?? data?.queue?.dosen?.kode ?? deanId;
                alert(`Nomor antrean berhasil diambil: #${nomor} untuk ${dosenNama} (${dosenKode}).`);
            } catch (error) {
                console.error(error);
                alert('Terjadi kesalahan saat mengambil nomor antrean.');
            } finally {
                isJoiningQueue = false;
                updateJoinQueueAvailability();
            }
        }

        syncQueueStatus();
        syncMyQueues();
        setInterval(syncMyQueues, 5000);
        updateMahasiswaClock();
        setInterval(updateMahasiswaClock, 1000);
        syncServiceOptionsForSelectedDean();

        if (deanSelect) {
            deanSelect.addEventListener('change', () => {
                syncServiceOptionsForSelectedDean();
                updateJoinQueueAvailability();
            });
        }

        if (window.Echo) {
            window.Echo.channel('queue-status')
                .listen('.queue.status.updated', (e) => {
                    syncQueueStatus();
                    syncMyQueues();

                    if (
                        ['mahasiswa', 'dosen'].includes(currentUserRole) &&
                        e?.meta?.event === 'queue_called' &&
                        e?.meta?.kode_user === currentUserKode
                    ) {
                        showQueueCallToast(e.meta);
                        showQueueCallModal(e.meta);
                        playQueueCallSound();
                    }
                });
        }

        if (queueCallModalClose) {
            queueCallModalClose.addEventListener('click', closeQueueCallModal);
        }

        if (queueCallModal) {
            queueCallModal.addEventListener('click', (event) => {
                if (event.target === queueCallModal) {
                    closeQueueCallModal();
                }
            });
        }

        if (joinQueueBtn) {
            joinQueueBtn.addEventListener('click', joinQueue);
        }

        if (previousQueueToggle && previousQueuePanel) {
            previousQueueToggle.addEventListener('click', () => {
                previousQueuePanel.classList.toggle('hidden');
            });
        }

        if (previousQueueDateFilter) {
            previousQueueDateFilter.addEventListener('change', () => renderPreviousQueueList(previousQueueCache));
        }

        if (previousQueueDateReset && previousQueueDateFilter) {
            previousQueueDateReset.addEventListener('click', () => {
                previousQueueDateFilter.value = '';
                renderPreviousQueueList(previousQueueCache);
            });
        }
    </script>

    @include('partials.pwa-scripts')
</body>

</html>
