<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Antrean Dekanat - Dashboard Dosen</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
    </style>
</head>

<body class="p-4 md:p-7">

    <div class="max-w-7xl mx-auto space-y-6">

        {{-- Header --}}
        <div class="panel p-5 md:p-6 flex flex-col gap-3 md:flex-row md:justify-between md:items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Dashboard Dosen</h1>
                <p class="text-gray-600">Selamat datang, {{ $data['user']->name }}</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-right">
                    <p id="current-date-dosen" class="text-xs text-gray-500">-</p>
                    <p id="current-time-dosen" class="text-sm font-semibold text-gray-800">- WIB</p>
                </div>
                <span class="text-sm">Status Ruangan:</span>
                <span id="room-status" class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-red-100 text-red-800">
                    Tutup
                </span>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                        class="btn-danger px-4 py-2 flex items-center gap-2">
                        <i class="fa fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </div>

        {{-- Kontrol Antrean --}}
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
                            <option value="all">Semua jenis layanan</option>
                            @foreach ($data['services'] as $service)
                                <option value="{{ $service->id }}">{{ $service->nama_layanan }}</option>
                            @endforeach
                        </select>
                        <i class="fa-solid fa-chevron-down combo-icon"></i>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Perkiraan Jam Tutup</label>
                    <div class="mt-2 flex items-center gap-2">
                        <input id="expected-close-input" type="text" placeholder="09.00"
                            class="block w-full px-3 py-2 border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <span class="text-sm text-gray-500">WIB</span>
                    </div>
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

        {{-- Statistik --}}
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

        {{-- Antrean Aktif dan Selesai --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Antrean Aktif --}}
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
                                                {{ $queue->service->nama_layanan ?? '-' }} • NPM:
                                                {{ $queue->kode_user }}
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

            {{-- Antrean Selesai --}}
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
                                        <p class="text-[10px] text-gray-400">ID Data: {{ $queue->id }}</p>
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
    </div>

    <script>
        const currentDateDosen = document.getElementById('current-date-dosen');
        const currentTimeDosen = document.getElementById('current-time-dosen');
        const roomStatus = document.getElementById('room-status');
        const serviceSelect = document.getElementById('service-select');
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
        const statActiveQueues = document.getElementById('stat-active-queues');
        const statCompletedQueues = document.getElementById('stat-completed-queues');
        const statCurrentQueue = document.getElementById('stat-current-queue');
        const statServiceEstimate = document.getElementById('stat-service-estimate');
        let currentStatus = "{{ $data['queue_status'] ?? 'closed' }}";
        let isSubmitting = false;

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

        function toIndoTime(value) {
            return (value || '').replace(':', '.');
        }

        function normalizeIndoTime(value) {
            return (value || '').trim().replace('.', ':');
        }

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
                                        ${escapeHtml(q.service?.nama_layanan ?? '-')} • NPM: ${escapeHtml(q.kode_user ?? '-')}
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
                                    <p class="text-[10px] text-gray-400">ID Data: ${q.id ?? '-'}</p>
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
            if (statActiveQueues) {
                statActiveQueues.textContent = String(stats.active ?? 0);
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
        }

        async function toggleQueue(status) {
            if (isSubmitting) return;
            const previousStatus = currentStatus;

            const payload = {
                status
            };

            if (status === 'open' || status === 'occupied') {
                const selectedService = serviceSelect?.value;
                if (!selectedService) {
                    alert('Pilih jenis layanan terlebih dahulu.');
                    return;
                }
                const expectedOpen = getCurrentTimeHHMM();
                const expectedClose = normalizeIndoTime(expectedCloseInput?.value);
                if (!expectedClose) {
                    alert('Perkiraan jam tutup wajib diisi sebelum membuka antrean.');
                    return;
                }
                if (selectedService === 'all') {
                    payload.service_scope = 'all';
                } else {
                    payload.service_scope = 'single';
                    payload.service_id = selectedService;
                }
                payload.expected_jam_buka = expectedOpen;
                payload.expected_jam_tutup = expectedClose;
            }

            setSubmittingState(true);
            try {
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

        async function syncDosenQueues() {
            try {
                const res = await fetch("{{ route('queue.my') }}");
                if (!res.ok) return;
                const data = await res.json();
                if (data?.role !== 'dosen') return;
                renderDosenQueues(Array.isArray(data.queues) ? data.queues : []);
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
        updateStatus("{{ $data['queue_status'] ?? 'closed' }}");
        syncOwnStatus();
        syncDosenQueues();
        updateDosenClock();
        setInterval(updateDosenClock, 1000);
        // Laravel Echo realtime
        if (window.Echo) {
            window.Echo.channel('queue-status')
                .listen('.queue.status.updated', (e) => {
                    if (e.userKode === myKode) {
                        updateStatus(e.queueStatus);
                        syncDosenQueues();
                    }
                });
        }
    </script>
</body>

</html>
