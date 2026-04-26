<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Antrean Dekanat - Dashboard Pejabat</title>
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

        .metric-card {
            position: relative;
            overflow: hidden;
        }

        .metric-card::before {
            content: '';
            position: absolute;
            width: 86px;
            height: 86px;
            border-radius: 999px;
            right: -24px;
            top: -24px;
            opacity: .16;
        }

        .metric-blue::before {
            background: #0f60f0;
        }

        .metric-green::before {
            background: #15803d;
        }

        .metric-amber::before {
            background: #b45309;
        }

        .metric-slate::before {
            background: #334155;
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

        .service-picker-btn {
            width: 100%;
            border: 1px solid #cfdcf1;
            border-radius: .75rem;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            padding: .62rem .9rem;
            color: #0f172a;
            font-weight: 500;
            box-shadow: 0 1px 0 rgba(15, 23, 42, .03);
            transition: border-color .2s ease, box-shadow .2s ease, background-color .2s ease;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: .75rem;
            text-align: left;
        }

        .service-picker-btn:hover {
            border-color: #b9cae7;
        }

        .service-picker-btn:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, .18);
            background: #fff;
        }

        .service-chip {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            border-radius: 999px;
            padding: .22rem .55rem;
            background: #eaf2ff;
            color: #1e40af;
            font-size: .72rem;
            font-weight: 700;
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

            .dosen-header-actions {
                width: 100%;
                justify-content: space-between;
                gap: .6rem;
                flex-wrap: wrap;
            }

            .dosen-header-actions form {
                width: 100%;
            }

            .dosen-header-actions .btn-danger {
                width: 100%;
                justify-content: center;
            }

            #active-queue-list > div > div {
                flex-direction: column;
                align-items: flex-start;
                gap: .75rem;
            }

            #active-queue-list .flex.flex-col.gap-1 {
                width: 100%;
            }

            #active-queue-list .call-btn,
            #active-queue-list .complete-btn {
                width: 100%;
            }

            #completed-queue-list > div {
                flex-direction: column;
                align-items: flex-start;
                gap: .75rem;
            }
        }
    </style>
</head>

<body class="p-4 md:p-7">
    {{-- Header dashboard pejabat yang mengarah ke ringkasan status ruang dan aksi logout --}}
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- Ringkasan status ruang dan kontrol logout sebagai titik awal pemantauan --}}
        <div class="panel p-5 md:p-6 flex flex-col gap-3 md:flex-row md:justify-between md:items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Dashboard Pejabat</h1>
                <p class="text-gray-600">Selamat datang, {{ $data['user']->name }}</p>
            </div>
            <div class="dosen-header-actions flex items-center gap-4">
                <div class="text-right">
                    <p id="current-date-dosen" class="text-xs text-gray-500">-</p>
                    <p id="current-time-dosen" class="text-sm font-semibold text-gray-800">- WIB</p>
                </div>
                <div class="flex flex-col gap-1">
                    <div class="flex items-center gap-2">
                        <span class="text-sm">Status Ruangan:</span>
                        <span id="room-status" class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-red-100 text-red-800">
                            Tutup
                        </span>
                    </div>
                    <span id="queue-carryover-badge"
                        class="hidden text-[11px] font-semibold px-2 py-1 rounded-full bg-amber-100 text-amber-800 ring-1 ring-amber-200">
                        <i class="fa-solid fa-circle-info mr-1"></i>
                        Ruangan ditutup, sedang melayani sisa antrean
                    </span>
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

        {{-- Kontrol antrean utama yang mengarah ke buka, tutup, dan pilihan layanan --}}
        <div class="panel p-6 relative z-30 overflow-visible">
            <h2 class="text-xl font-semibold text-gray-900 flex items-center gap-2 mb-4">
                <i class="fa-solid fa-play-circle text-gray-600"></i> Kontrol Antrean
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Jenis Layanan</label>
                    <div class="mt-2 relative z-40">
                        <select id="service-select" class="hidden" multiple>
                            <option value="all">Semua jenis layanan</option>
                            @foreach ($data['services'] as $service)
                                <option value="{{ $service->id }}">{{ $service->nama_layanan }}</option>
                            @endforeach
                        </select>
                        <button type="button" id="service-picker-btn" class="service-picker-btn">
                            <span id="service-picker-label">Pilih satu/lebih layanan</span>
                            <i class="fa-solid fa-chevron-down text-slate-500 text-xs"></i>
                        </button>
                        <div id="service-picker-panel"
                            class="hidden absolute z-50 mt-2 w-full rounded-xl border border-slate-200 bg-white shadow-lg p-3">
                            <div id="service-picker-options" class="space-y-2 max-h-52 overflow-y-auto pr-1"></div>
                        </div>
                        <div id="service-selected-chips" class="mt-2 flex flex-wrap gap-1.5"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Pilih satu/lebih layanan. Jika pilih "Semua jenis layanan", layanan lain diabaikan.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Perkiraan Jam Tutup</label>
                    <div class="mt-2 flex items-center gap-2">
                        <select id="expected-close-hour"
                            class="block w-full px-3 py-2 border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            @for ($hour = 0; $hour < 24; $hour++)
                                <option value="{{ str_pad((string) $hour, 2, '0', STR_PAD_LEFT) }}">
                                    {{ str_pad((string) $hour, 2, '0', STR_PAD_LEFT) }}
                                </option>
                            @endfor
                        </select>
                        <span class="text-sm text-gray-500">:</span>
                        <select id="expected-close-minute"
                            class="block w-full px-3 py-2 border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            @for ($minute = 0; $minute < 60; $minute++)
                                <option value="{{ str_pad((string) $minute, 2, '0', STR_PAD_LEFT) }}">
                                    {{ str_pad((string) $minute, 2, '0', STR_PAD_LEFT) }}
                                </option>
                            @endfor
                        </select>
                        <span class="text-sm text-gray-500">WIB</span>
                    </div>
                    <input id="expected-close-input" type="hidden">
                    <p class="text-xs text-gray-500 mt-1">Pilih jam dan menit dalam format 24 jam, tanpa AM/PM.</p>
                </div>

                <div>
                    <button type="button" id="open-queue-btn-2"
                        class="btn-brand w-full px-4 py-2">Buka
                        Antrean</button>
                    <button type="button" id="close-queue-btn-2"
                        class="btn-danger w-full px-4 py-2 hidden">Tutup
                        Antrean</button>
                </div>
            </div>
        </div>

        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="panel metric-card metric-blue p-6 flex items-center gap-4">
                <i class="fa-solid fa-users text-3xl text-gray-600"></i>
                <div>
                    <p id="stat-active-queues" class="text-2xl font-bold">{{ $data['activeQueues'] }}</p>
                    <p class="text-sm text-gray-500">Menunggu</p>
                </div>
            </div>

            <div class="panel metric-card metric-green p-6 flex items-center gap-4">
                <i class="fa-solid fa-check-circle text-3xl text-green-600"></i>
                <div>
                    <p id="stat-completed-queues" class="text-2xl font-bold">{{ $data['completedQueues'] }}</p>
                    <p class="text-sm text-gray-500">Selesai</p>
                </div>
            </div>

            <div class="panel metric-card metric-amber p-6 flex items-center gap-4">
                <i class="fa-solid fa-clock text-3xl text-yellow-600"></i>
                <div>
                    <p id="stat-current-queue" class="text-2xl font-bold">{{ $data['currentQueueNumber'] ? '#' . $data['currentQueueNumber'] : '-' }}</p>
                    <p class="text-sm text-gray-500">Nomor Saat Ini</p>
                </div>
            </div>

            <div class="panel metric-card metric-slate p-6 flex items-center gap-4">
                <i class="fa-solid fa-hourglass-half text-3xl text-gray-600"></i>
                <div>
                    <p id="stat-service-estimate" class="text-lg font-bold">{{ $data['currentServiceEstimate'] ? $data['currentServiceEstimate'] . ' menit' : '-' }}</p>
                    <p class="text-sm text-gray-500">Estimasi Tunggu</p>
                </div>
            </div>
        </div>

        
        {{-- Daftar antrean aktif, selesai, dan riwayat untuk melihat alur layanan hari ini --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <div class="panel p-6">
                <div class="flex justify-between mb-4">
                    <h2 class="text-xl font-semibold text-gray-900">Antrean Aktif</h2>
                    <button id="complete-service-btn"
                        class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-700 transition hidden">
                        <i class="fa-solid fa-check-circle mr-2"></i> Selesai Layani
                    </button>
                </div>
                <div id="active-queue-list" class="space-y-3 max-h-96 overflow-y-auto">
                    @forelse ($data['myQueues'] as $queue)
                        @if (in_array($queue->status, ['menunggu', 'diproses']))
                            <div id="queue-item-{{ $queue->id }}"
                                class="p-3 border rounded-lg transition-all duration-300">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1 flex items-center gap-3">
                                        <div class="text-center min-w-[64px]">
                                            <div class="text-2xl font-bold text-gray-500 queue-number">#{{ $queue->nomor_antrian }}</div>
                                            <div class="text-[10px] text-gray-400">ID {{ $queue->id }}</div>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ $queue->user->name ?? '-' }}
                                            </p>
                                            <p class="text-sm text-gray-500 queue-service">
                                                {{ $queue->service->nama_layanan ?? '-' }}
                                            </p>
                                            <p class="text-xs text-gray-400">
                                                Dosen: {{ $data['user']->name ?? '-' }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex flex-col gap-1">
                                        @if ($queue->status === 'menunggu')
                                            <button data-id="{{ $queue->id }}"
                                                class="call-btn bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 mb-1">
                                                Panggil / Layani
                                            </button>
                                        @else
                                            <button data-id="{{ $queue->id }}"
                                                class="complete-btn bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700 mb-1">
                                                Selesai
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <p class="text-gray-500 text-sm">Belum ada antrean.</p>
                    @endforelse
                </div>
            </div>

            
            <div class="panel p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Selesai</h2>
                <div id="completed-queue-list" class="space-y-3 max-h-96 overflow-y-auto">
                    @forelse ($data['myQueues'] as $queue)
                        @if ($queue->status === 'selesai')
                            <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <i class="fa-solid fa-check-circle text-2xl text-green-600"></i>
                                    <div>
                                        <p class="font-semibold text-gray-900">#{{ $queue->nomor_antrian }} -
                                            {{ $queue->user->name ?? '-' }}</p>
                                        <p class="text-sm text-gray-500">{{ $queue->service->nama_layanan ?? '-' }}</p>
                                        <p class="text-xs text-gray-400">
                                            Dosen: {{ $data['user']->name ?? '-' }}
                                        </p>
                                    </div>
                                </div>
                                <span
                                    class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-green-100 text-green-800">
                                    {{ $queue->updated_at->format('H:i') }}
                                </span>
                            </div>
                        @endif
                    @empty
                        <p class="text-gray-500 text-sm">Belum ada antrean selesai.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="panel p-6 mt-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-900">Riwayat Hari Sebelumnya</h2>
                <span id="history-queue-count"
                    class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-gray-100 text-gray-700">
                    {{ count($data['historyQueues'] ?? []) }} data
                </span>
            </div>
            <div class="flex flex-col md:flex-row gap-2 md:items-center mb-3">
                <input type="date" id="history-date-filter"
                    class="w-full md:w-auto rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                <button type="button" id="history-date-reset"
                    class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                    Reset Filter
                </button>
            </div>
            <div id="history-queue-list" class="space-y-3 max-h-96 overflow-y-auto">
                @forelse ($data['historyQueues'] ?? [] as $queue)
                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-clock-rotate-left text-xl text-slate-500"></i>
                            <div>
                                <p class="font-semibold text-gray-900">#{{ $queue->nomor_antrian }} -
                                    {{ $queue->user->name ?? '-' }}</p>
                                <p class="text-sm text-gray-500">{{ $queue->service->nama_layanan ?? '-' }}</p>
                                <p class="text-xs text-gray-400">
                                    {{ ucfirst($queue->status ?? '-') }} •
                                    {{ optional($queue->created_at)->format('d-m-Y H:i') ?? '-' }} WIB
                                </p>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">Belum ada riwayat hari sebelumnya.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div id="pejabat-toast-container" class="fixed top-4 right-4 z-[70] space-y-2 pointer-events-none"></div>

    {{-- Script interaktif pejabat yang mengarah ke geolocation, service picker, polling, dan realtime --}}
    <script>
        // Elemen UI utama yang dipakai untuk update status dan daftar antrean pada layar ini.
        const currentDateDosen = document.getElementById('current-date-dosen');
        const currentTimeDosen = document.getElementById('current-time-dosen');
        const roomStatus = document.getElementById('room-status');
        const queueCarryoverBadge = document.getElementById('queue-carryover-badge');
        const pejabatToastContainer = document.getElementById('pejabat-toast-container');
        const serviceSelect = document.getElementById('service-select');
        const servicePickerBtn = document.getElementById('service-picker-btn');
        const servicePickerLabel = document.getElementById('service-picker-label');
        const servicePickerPanel = document.getElementById('service-picker-panel');
        const servicePickerOptions = document.getElementById('service-picker-options');
        const serviceSelectedChips = document.getElementById('service-selected-chips');
        const expectedCloseHourInput = document.getElementById('expected-close-hour');
        const expectedCloseMinuteInput = document.getElementById('expected-close-minute');
        const expectedCloseInput = document.getElementById('expected-close-input');

        const openBtns = [
            document.getElementById('open-queue-btn-2')
        ];

        const closeBtns = [
            document.getElementById('close-queue-btn-2')
        ];

        const token = "{{ csrf_token() }}";
        const myKode = "{{ $data['user']->kode }}";
        const activeQueueList = document.getElementById('active-queue-list');
        const completedQueueList = document.getElementById('completed-queue-list');
        const historyQueueList = document.getElementById('history-queue-list');
        const historyQueueCount = document.getElementById('history-queue-count');
        const historyDateFilter = document.getElementById('history-date-filter');
        const historyDateReset = document.getElementById('history-date-reset');
        const statActiveQueues = document.getElementById('stat-active-queues');
        const statCompletedQueues = document.getElementById('stat-completed-queues');
        const statCurrentQueue = document.getElementById('stat-current-queue');
        const statServiceEstimate = document.getElementById('stat-service-estimate');
        let historyQueuesCache = @json($data['historyQueues'] ?? []);
        let currentStatus = "{{ $data['queue_status'] ?? 'closed' }}";
        let isSubmitting = false;
        let activeQueueCount = Number(@json($data['activeQueues'] ?? 0));
        const notifiedQueueJoinedIds = new Set();
        let queuePollingInitialized = false;
        // Browser geolocation dipakai untuk validasi lokasi sebelum request buka antrean dikirim.
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
                    return 'Izin lokasi ditolak. Aktifkan akses lokasi untuk membuka antrean.';
                case 2:
                    return 'Lokasi perangkat tidak tersedia. Coba aktifkan GPS lalu ulangi.';
                case 3:
                    return 'Pengambilan lokasi melebihi batas waktu. Coba ulangi.';
                default:
                    return error?.message ?? 'Gagal membaca lokasi perangkat.';
            }
        }
        function getCurrentTimeHHMM() {
            const parts = new Intl.DateTimeFormat('id-ID', {
                timeZone: 'Asia/Jakarta',
                hour12: false,
                hour: '2-digit',
                minute: '2-digit'
            }).formatToParts(new Date());
            const h = parts.find(p => p.type === 'hour')?.value ?? '00';
            const m = parts.find(p => p.type === 'minute')?.value ?? '00';
            return `${h}:${m}`;
        }
        function normalizeIndoTime(value) {
            const raw = (value || '').trim();
            if (!raw) return '';

            const cleaned = raw.replace('.', ':');
            const match = cleaned.match(/^(\d{1,2}):(\d{2})$/);
            if (!match) return '';

            const hour = Number(match[1]);
            const minute = Number(match[2]);
            if (Number.isNaN(hour) || Number.isNaN(minute) || hour < 0 || hour > 23 || minute < 0 || minute > 59) {
                return '';
            }

            return `${String(hour).padStart(2, '0')}:${String(minute).padStart(2, '0')}`;
        }
        function setExpectedCloseValue(timeValue) {
            const normalized = normalizeIndoTime(timeValue);
            if (!normalized) {
                if (expectedCloseInput) expectedCloseInput.value = '';
                return '';
            }

            if (expectedCloseInput) expectedCloseInput.value = normalized;
            return normalized;
        }
        function syncExpectedCloseValue() {
            if (!expectedCloseHourInput || !expectedCloseMinuteInput) return '';

            const hour = expectedCloseHourInput.value;
            const minute = expectedCloseMinuteInput.value;

            const combined = `${hour}:${minute}`;
            return setExpectedCloseValue(combined);
        }
        function getDefaultExpectedCloseTime() {
            const currentTime = getCurrentTimeHHMM();
            const [hourPart, minutePart] = currentTime.split(':');
            const hour = Number(hourPart);
            const minute = Number(minutePart);

            if (Number.isNaN(hour) || Number.isNaN(minute)) {
                return currentTime;
            }

            const nextHour = (hour + 2) % 24;
            return `${String(nextHour).padStart(2, '0')}:${String(minute).padStart(2, '0')}`;
        }

        if (expectedCloseHourInput && expectedCloseMinuteInput) {
            const defaultTime = getDefaultExpectedCloseTime();
            const [defaultHour, defaultMinute] = defaultTime.split(':');
            expectedCloseHourInput.value = defaultHour;
            expectedCloseMinuteInput.value = defaultMinute;
            setExpectedCloseValue(defaultTime);
        }

        if (expectedCloseHourInput) {
            expectedCloseHourInput.addEventListener('change', () => syncExpectedCloseValue());
        }

        if (expectedCloseMinuteInput) {
            expectedCloseMinuteInput.addEventListener('change', () => syncExpectedCloseValue());
        }
        function getSelectedServiceValues() {
            if (!serviceSelect) return [];
            return Array.from(serviceSelect.selectedOptions).map((opt) => opt.value);
        }
        function setSelectedServiceValues(values = []) {
            if (!serviceSelect) return;
            const selectedSet = new Set(values.map((val) => String(val)));

            Array.from(serviceSelect.options).forEach((opt) => {
                opt.selected = selectedSet.has(String(opt.value));
            });
        }
        function renderSelectedServiceChips() {
            if (!serviceSelectedChips || !servicePickerLabel || !serviceSelect) return;
            const selectedValues = getSelectedServiceValues();
            const allSelected = selectedValues.includes('all');
            const selectedOptions = Array.from(serviceSelect.options).filter((opt) => opt.selected);

            if (selectedOptions.length === 0) {
                servicePickerLabel.textContent = 'Pilih satu/lebih layanan';
                serviceSelectedChips.innerHTML = '';
                return;
            }

            if (allSelected) {
                servicePickerLabel.textContent = 'Semua jenis layanan';
            } else if (selectedOptions.length === 1) {
                servicePickerLabel.textContent = selectedOptions[0].textContent;
            } else {
                servicePickerLabel.textContent = `${selectedOptions.length} layanan dipilih`;
            }

            serviceSelectedChips.innerHTML = selectedOptions.map((opt) => `
                <span class="service-chip">
                    <i class="fa-solid ${opt.value === 'all' ? 'fa-layer-group' : 'fa-screwdriver-wrench'} text-[10px]"></i>
                    ${escapeHtml(opt.textContent)}
                </span>
            `).join('');
        }
        function renderServicePickerOptions() {
            if (!servicePickerOptions || !serviceSelect) return;

            const selectedValues = new Set(getSelectedServiceValues().map((val) => String(val)));
            servicePickerOptions.innerHTML = Array.from(serviceSelect.options).map((opt) => {
                const value = String(opt.value);
                const checked = selectedValues.has(value) ? 'checked' : '';
                return `
                    <label class="flex items-center gap-2 text-sm text-slate-700">
                        <input type="checkbox" class="service-picker-checkbox rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                            data-value="${escapeHtml(value)}" ${checked}>
                        <span>${escapeHtml(opt.textContent)}</span>
                    </label>
                `;
            }).join('');
        }
        function toggleServiceSelection(value, checked) {
            const current = new Set(getSelectedServiceValues().map((val) => String(val)));
            const normalized = String(value);

            if (normalized === 'all') {
                if (checked) {
                    current.clear();
                    current.add('all');
                } else {
                    current.delete('all');
                }
            } else {
                current.delete('all');
                if (checked) {
                    current.add(normalized);
                } else {
                    current.delete(normalized);
                }
            }

            setSelectedServiceValues(Array.from(current));
            renderServicePickerOptions();
            renderSelectedServiceChips();
        }
        function initServicePicker() {
            if (!serviceSelect || !servicePickerBtn || !servicePickerPanel || !servicePickerOptions) return;

            renderServicePickerOptions();
            renderSelectedServiceChips();

            servicePickerBtn.addEventListener('click', () => {
                servicePickerPanel.classList.toggle('hidden');
            });

            servicePickerOptions.addEventListener('change', (event) => {
                const checkbox = event.target.closest('.service-picker-checkbox');
                if (!checkbox) return;
                toggleServiceSelection(checkbox.dataset.value ?? '', checkbox.checked);
            });

            document.addEventListener('click', (event) => {
                if (servicePickerPanel.classList.contains('hidden')) return;
                const target = event.target;
                if (servicePickerPanel.contains(target) || servicePickerBtn.contains(target)) return;
                servicePickerPanel.classList.add('hidden');
            });
        }
        // Jam header disegarkan real-time agar pejabat melihat waktu lokal yang akurat sebagai acuan aksi.
        function updateDosenClock() {
            const now = new Date();
            if (currentDateDosen) {
                currentDateDosen.textContent = now.toLocaleDateString('id-ID', {
                    weekday: 'long',
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric',
                    timeZone: 'Asia/Jakarta'
                });
            }
            if (currentTimeDosen) {
                currentTimeDosen.textContent = now.toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: false,
                    timeZone: 'Asia/Jakarta'
                }) + ' WIB';
            }
        }
        function escapeHtml(value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }
        // Toast dipakai untuk memberi notifikasi saat antrean baru masuk ke pejabat.
        function showPejabatToast(meta = {}) {
            if (!pejabatToastContainer) return;
            const nomor = meta?.nomor_antrian ? `#${meta.nomor_antrian}` : null;
            const mahasiswaName = meta?.mahasiswa_name ?? '-';
            const kodeUser = meta?.kode_user ?? '-';
            const serviceName = meta?.service_name ?? '-';

            const toast = document.createElement('div');
            toast.className =
                'pointer-events-auto w-[22rem] rounded-xl border border-blue-200 bg-white shadow-lg p-4 transform transition-all duration-300 translate-x-6 opacity-0';
            toast.innerHTML = `
                <div class="flex items-start gap-3">
                    <span class="mt-0.5 h-9 w-9 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center">
                        <i class="fa-solid fa-bell"></i>
                    </span>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-gray-900">Antrean baru masuk</p>
                        <p class="text-xs text-gray-600 mt-1">Mahasiswa: ${escapeHtml(mahasiswaName)}</p>
                        <p class="text-xs text-gray-600">Kode: ${escapeHtml(kodeUser)}</p>
                        <p class="text-xs text-gray-600">Layanan: ${escapeHtml(serviceName)}</p>
                        ${nomor ? `<p class="text-xs font-bold text-blue-700 mt-1">Nomor: ${escapeHtml(nomor)}</p>` : ''}
                    </div>
                </div>
            `;

            pejabatToastContainer.appendChild(toast);
            requestAnimationFrame(() => {
                toast.classList.remove('translate-x-6', 'opacity-0');
            });

            setTimeout(() => {
                toast.classList.add('translate-x-6', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 7000);
        }
        function playPejabatNotifSound(eventType = 'default') {
            const AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (!AudioCtx) return;
            const ctx = new AudioCtx();
            const pattern = eventType === 'queue_joined' ? [1047, 1319] : [880, 1047];

            pattern.forEach((freq, i) => {
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.value = freq;
                gain.gain.value = 0.0001;
                osc.connect(gain);
                gain.connect(ctx.destination);

                const start = ctx.currentTime + (i * 0.16);
                const end = start + 0.12;
                gain.gain.exponentialRampToValueAtTime(0.11, start + 0.02);
                gain.gain.exponentialRampToValueAtTime(0.0001, end);
                osc.start(start);
                osc.stop(end + 0.01);
            });
        }
        function renderDosenQueues(queues = []) {
            if (!activeQueueList || !completedQueueList) return;

            const activeRows = queues.filter(q => ['menunggu', 'diproses'].includes(q.status));
            const completedRows = queues.filter(q => q.status === 'selesai');

            if (activeRows.length === 0) {
                activeQueueList.innerHTML = '<p class="text-gray-500 text-sm">Belum ada antrean.</p>';
            } else {
                activeQueueList.innerHTML = activeRows.map((q) => `
                    <div id="queue-item-${q.id}" class="p-3 border rounded-lg transition-all duration-300">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 flex items-center gap-3">
                                <div class="text-center min-w-[64px]">
                                    <div class="text-2xl font-bold text-gray-500 queue-number">#${q.nomor_antrian ?? '-'}</div>
                                    <div class="text-[10px] text-gray-400">ID ${q.id}</div>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">${escapeHtml(q.user?.name ?? '-')}</p>
                                    <p class="text-sm text-gray-500 queue-service">
                                        ${escapeHtml(q.service?.nama_layanan ?? '-')}
                                    </p>
                                    <p class="text-xs text-gray-400">
                                        Dosen: ${escapeHtml("{{ $data['user']->name }}")}
                                    </p>
                                </div>
                            </div>
                            <div class="flex flex-col gap-1">
                                ${q.status === 'menunggu'
                                    ? `<button data-id="${q.id}" class="call-btn bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 mb-1">Panggil / Layani</button>`
                                    : `<button data-id="${q.id}" class="complete-btn bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700 mb-1">Selesai</button>`
                                }
                            </div>
                        </div>
                    </div>
                `).join('');
            }

            if (completedRows.length === 0) {
                completedQueueList.innerHTML = '<p class="text-gray-500 text-sm">Belum ada antrean selesai.</p>';
            } else {
                completedQueueList.innerHTML = completedRows.map((q) => {
                    const updatedAt = q.updated_at ? new Date(q.updated_at) : null;
                    const jam = updatedAt ? updatedAt.toLocaleTimeString('id-ID', {
                        timeZone: 'Asia/Jakarta',
                        hour12: false,
                        hour: '2-digit',
                        minute: '2-digit'
                    }) : '-';

                    return `
                        <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-check-circle text-2xl text-green-600"></i>
                                <div>
                                    <p class="font-semibold text-gray-900">#${q.nomor_antrian ?? '-'} - ${escapeHtml(q.user?.name ?? '-')}</p>
                                    <p class="text-sm text-gray-500">${escapeHtml(q.service?.nama_layanan ?? '-')}</p>
                                    <p class="text-xs text-gray-400">Dosen: ${escapeHtml("{{ $data['user']->name }}")}</p>
                                </div>
                            </div>
                            <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-green-100 text-green-800">${jam}</span>
                        </div>
                    `;
                }).join('');
            }
        }
        function renderDosenStats(stats = {}) {
            activeQueueCount = Number(stats.active ?? 0);
            if (statActiveQueues) {
                statActiveQueues.textContent = String(activeQueueCount);
            }
            if (statCompletedQueues) {
                statCompletedQueues.textContent = String(stats.completed ?? 0);
            }
            if (statCurrentQueue) {
                statCurrentQueue.textContent = stats.current_queue_number ? `#${stats.current_queue_number}` : '-';
            }
            if (statServiceEstimate) {
                statServiceEstimate.textContent = stats.current_service_estimate ? `${stats.current_service_estimate} menit` : '-';
            }
            updateQueueCarryoverBadge();
        }
        function updateQueueCarryoverBadge() {
            if (!queueCarryoverBadge) return;
            const shouldShow = currentStatus === 'closed' && activeQueueCount > 0;
            queueCarryoverBadge.classList.toggle('hidden', !shouldShow);
        }
        function renderDosenHistoryQueues(queues = []) {
            if (!historyQueueList || !historyQueueCount) return;
            historyQueuesCache = Array.isArray(queues) ? queues : [];
            const selectedDate = historyDateFilter?.value ?? '';
            const filteredRows = selectedDate ?
                historyQueuesCache.filter((q) => {
                    const createdAt = q?.created_at ? new Date(q.created_at) : null;
                    if (!createdAt) return false;
                    return createdAt.toLocaleDateString('en-CA', {
                        timeZone: 'Asia/Jakarta'
                    }) === selectedDate;
                }) :
                historyQueuesCache;
            historyQueueCount.textContent = `${filteredRows.length} data`;

            if (filteredRows.length === 0) {
                historyQueueList.innerHTML = '<p class="text-gray-500 text-sm">Belum ada riwayat hari sebelumnya.</p>';
                return;
            }

            historyQueueList.innerHTML = filteredRows.map((q) => {
                const createdAt = q.created_at ? new Date(q.created_at) : null;
                const tanggalJam = createdAt ? createdAt.toLocaleString('id-ID', {
                    timeZone: 'Asia/Jakarta',
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false,
                }) : '-';

                return `
                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-clock-rotate-left text-xl text-slate-500"></i>
                            <div>
                                <p class="font-semibold text-gray-900">#${q.nomor_antrian ?? '-'} - ${escapeHtml(q.user?.name ?? '-')}</p>
                                <p class="text-sm text-gray-500">${escapeHtml(q.service?.nama_layanan ?? '-')}</p>
                                <p class="text-xs text-gray-400">${escapeHtml((q.status ?? '').charAt(0).toUpperCase() + (q.status ?? '').slice(1))} • ${tanggalJam} WIB</p>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }
        function setSubmittingState(submitting) {
            isSubmitting = submitting;
            [...openBtns, ...closeBtns].forEach((btn) => {
                if (!btn) return;
                btn.disabled = submitting;
                btn.classList.toggle('opacity-60', submitting);
                btn.classList.toggle('cursor-not-allowed', submitting);
            });
        }
        function updateStatus(status) {
            currentStatus = status;

            if (status === 'open' || status === 'occupied') {

                roomStatus.textContent = status === 'occupied' ? 'Melayani' : 'Buka';
                roomStatus.classList.remove('bg-red-100', 'text-red-800', 'bg-green-100', 'text-green-800',
                    'bg-yellow-100', 'text-yellow-800');
                if (status === 'occupied') {
                    roomStatus.classList.add('bg-yellow-100', 'text-yellow-800');
                } else {
                    roomStatus.classList.add('bg-green-100', 'text-green-800');
                }

                openBtns.forEach(b => b.classList.add('hidden'));
                closeBtns.forEach(b => b.classList.remove('hidden'));

            } else {

                roomStatus.textContent = 'Tutup';
                roomStatus.classList.remove('bg-green-100', 'text-green-800');
                roomStatus.classList.add('bg-red-100', 'text-red-800');

                openBtns.forEach(b => b.classList.remove('hidden'));
                closeBtns.forEach(b => b.classList.add('hidden'));
            }

            updateQueueCarryoverBadge();
        }
        // Mengirim status buka/tutup ruang berikut layanan dan jam perkiraan ke backend.
        async function toggleQueue(status) {
            if (isSubmitting) return;
            const previousStatus = currentStatus;

            const payload = {
                status
            };

            if (status === 'open' || status === 'occupied') {
                const selectedServices = serviceSelect ? Array.from(serviceSelect.selectedOptions).map((opt) => opt.value) : [];
                if (selectedServices.length === 0) {
                    alert('Pilih jenis layanan terlebih dahulu.');
                    return;
                }
                const expectedOpen = getCurrentTimeHHMM();
                syncExpectedCloseValue();
                const expectedClose = normalizeIndoTime(expectedCloseInput?.value);
                if (!expectedClose) {
                    alert('Perkiraan jam tutup wajib diisi sebelum membuka antrean.');
                    return;
                }
                if (selectedServices.includes('all')) {
                    payload.service_scope = 'all';
                } else {
                    payload.service_scope = selectedServices.length > 1 ? 'multiple' : 'single';
                    payload.service_ids = selectedServices.map((id) => Number(id));
                    if (selectedServices.length === 1) {
                        payload.service_id = Number(selectedServices[0]);
                    }
                }
                payload.expected_jam_buka = expectedOpen;
                payload.expected_jam_tutup = expectedClose;
            }

            setSubmittingState(true);
            try {
                const position = await getBrowserLocation();
                const coords = position.coords ?? {};
                payload.latitude = coords.latitude;
                payload.longitude = coords.longitude;
                payload.accuracy = coords.accuracy;

                const res = await fetch("{{ route('queue.toggle') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify(payload)
                });

                const data = await res.json().catch(() => null);

                if (!res.ok) {
                    updateStatus(previousStatus);
                    if (data?.message) {
                        alert(data.message);
                    }
                    return;
                }
                updateStatus(data?.queue_status ?? status);
                await syncOwnStatus();

            } catch (e) {
                console.error(e);
                updateStatus(previousStatus);
                alert(getLocationErrorMessage(e));
            } finally {
                setSubmittingState(false);
            }
        }
        async function syncOwnStatus() {
            try {
                const res = await fetch("{{ route('queue.status') }}");
                if (!res.ok) return;
                const data = await res.json();
                if (data?.queue_status) {
                    updateStatus(data.queue_status);
                }
            } catch (e) {
                console.error(e);
            }
        }
        // Memuat ulang antrean aktif dan riwayat milik pejabat agar dashboard tetap sinkron.
        async function syncDosenQueues() {
            try {
                const res = await fetch("{{ route('queue.my') }}");
                if (!res.ok) return;
                const data = await res.json();
                if (data?.role !== 'pejabat') return;
                const rows = Array.isArray(data.queues) ? data.queues : [];
                if (!queuePollingInitialized) {
                    rows.forEach((queue) => {
                        const queueId = String(queue?.id ?? '');
                        if (queueId) {
                            notifiedQueueJoinedIds.add(queueId);
                        }
                    });
                    queuePollingInitialized = true;
                } else {
                    rows.forEach((queue) => {
                        const queueId = String(queue?.id ?? '');
                        if (!queueId || notifiedQueueJoinedIds.has(queueId)) return;
                        notifiedQueueJoinedIds.add(queueId);
                        showPejabatToast({
                            nomor_antrian: queue?.nomor_antrian,
                            kode_user: queue?.kode_user,
                            mahasiswa_name: queue?.user?.name,
                            service_name: queue?.service?.nama_layanan,
                        });
                        playPejabatNotifSound('queue_joined');
                    });
                }

                renderDosenQueues(rows);
                renderDosenHistoryQueues(Array.isArray(data.history_queues) ? data.history_queues : []);
                renderDosenStats(data.stats ?? {});
            } catch (e) {
                console.error(e);
            }
        }
        async function updateQueueStatus(queueId, action) {
            try {
                const route = action === 'call' ?
                    `{{ url('/queue') }}/${queueId}/call` :
                    `{{ url('/queue') }}/${queueId}/complete`;

                const res = await fetch(route, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    }
                });

                const data = await res.json().catch(() => null);
                if (!res.ok) {
                    alert(data?.message ?? 'Gagal memperbarui antrean.');
                    return;
                }

                if (data?.queue_status) {
                    updateStatus(data.queue_status);
                }
                await syncDosenQueues();
            } catch (e) {
                console.error(e);
                alert('Terjadi kesalahan saat memperbarui antrean.');
            }
        }

        if (activeQueueList) {
            activeQueueList.addEventListener('click', (event) => {
                const callBtn = event.target.closest('.call-btn');
                if (callBtn) {
                    const queueId = callBtn.dataset.id;
                    if (queueId) updateQueueStatus(queueId, 'call');
                    return;
                }

                const completeBtn = event.target.closest('.complete-btn');
                if (completeBtn) {
                    const queueId = completeBtn.dataset.id;
                    if (queueId) updateQueueStatus(queueId, 'complete');
                }
            });
        }

        openBtns.forEach(b => b.addEventListener('click', () => toggleQueue('open')));
        closeBtns.forEach(b => b.addEventListener('click', () => toggleQueue('closed')));
        initServicePicker();
        if (historyDateFilter) {
            historyDateFilter.addEventListener('change', () => renderDosenHistoryQueues(historyQueuesCache));
        }
        if (historyDateReset && historyDateFilter) {
            historyDateReset.addEventListener('click', () => {
                historyDateFilter.value = '';
                renderDosenHistoryQueues(historyQueuesCache);
            });
        }
        renderDosenHistoryQueues(historyQueuesCache);
        updateStatus("{{ $data['queue_status'] ?? 'closed' }}");
        syncOwnStatus();
        syncDosenQueues();
        setInterval(syncDosenQueues, 5000);
        updateDosenClock();
        setInterval(updateDosenClock, 1000);
        if (window.Echo) {
            window.Echo.channel('queue-status')
                .listen('.queue.status.updated', (e) => {
                    if (e.userKode === myKode) {
                        if (e?.meta?.event === 'queue_joined') {
                            const queueId = String(e?.meta?.queue_id ?? '');
                            if (!queueId || !notifiedQueueJoinedIds.has(queueId)) {
                                if (queueId) {
                                    notifiedQueueJoinedIds.add(queueId);
                                }
                                showPejabatToast(e?.meta ?? {});
                                playPejabatNotifSound('queue_joined');
                            }
                        }
                        updateStatus(e.queueStatus);
                        syncDosenQueues();
                    }
                });
        }
    </script>

    @include('partials.pwa-scripts')
</body>

</html>
