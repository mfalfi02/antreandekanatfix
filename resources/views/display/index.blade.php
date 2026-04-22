<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layanan Dekanat - Display Antrean</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.pwa-head')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@600;700&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --bg-1: #f7fbff;
            --bg-2: #eef5ff;
            --panel: #ffffff;
            --line: #dbe6f6;
            --text: #0f172a;
            --muted: #5f6c80;
            --brand: #0f60f0;
            --brand-soft: #dbe9ff;
            --ok: #15803d;
            --ok-soft: #dcfce7;
            --warn: #b45309;
            --warn-soft: #fef3c7;
            --danger: #b91c1c;
            --danger-soft: #fee2e2;
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at 12% -8%, rgba(15, 96, 240, .15), transparent 36%),
                radial-gradient(circle at 88% -12%, rgba(56, 189, 248, .14), transparent 34%),
                linear-gradient(180deg, var(--bg-2), var(--bg-1));
            min-height: 100vh;
        }

        .display-title {
            font-family: 'Space Grotesk', sans-serif;
            letter-spacing: .02em;
        }

        .panel {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 1rem;
            box-shadow: 0 14px 30px -20px rgba(15, 23, 42, .35);
        }

        .status-pill {
            font-size: .75rem;
            line-height: 1;
            font-weight: 700;
            padding: .5rem .65rem;
            border-radius: .65rem;
            display: inline-flex;
            align-items: center;
            gap: .4rem;
        }

        .status-open {
            color: var(--ok);
            background: var(--ok-soft);
            border: 1px solid #bbf7d0;
        }

        .status-occupied {
            color: var(--warn);
            background: var(--warn-soft);
            border: 1px solid #fde68a;
        }

        .status-closed {
            color: var(--danger);
            background: var(--danger-soft);
            border: 1px solid #fecaca;
        }

        .metric-card {
            position: relative;
            overflow: hidden;
        }

        .metric-card::before {
            content: '';
            position: absolute;
            width: 96px;
            height: 96px;
            border-radius: 999px;
            right: -26px;
            top: -26px;
            opacity: .15;
        }

        .metric-blue::before {
            background: #0f60f0;
        }

        .metric-green::before {
            background: #15803d;
        }

        .metric-orange::before {
            background: #b45309;
        }

        .metric-slate::before {
            background: #334155;
        }

        .queue-card,
        .staff-card,
        .summary-card {
            border: 1px solid var(--line);
            border-radius: .9rem;
            background: #fff;
        }

        .queue-card {
            padding: .9rem;
        }

        .queue-label {
            font-size: .72rem;
            font-weight: 700;
            padding: .28rem .6rem;
            border-radius: 999px;
        }

        .label-wait {
            color: #92400e;
            background: #ffedd5;
        }

        .label-process {
            color: #1d4ed8;
            background: #dbeafe;
        }

        .label-done {
            color: #166534;
            background: #dcfce7;
        }

        .custom-scroll {
            scrollbar-width: thin;
            scrollbar-color: #c5d4eb #f6f9ff;
        }

        .custom-scroll::-webkit-scrollbar {
            width: 8px;
        }

        .custom-scroll::-webkit-scrollbar-thumb {
            background: #c5d4eb;
            border-radius: 12px;
        }

        .custom-scroll::-webkit-scrollbar-track {
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
    </style>
</head>

<body class="p-4 md:p-7">
    <div class="max-w-7xl mx-auto space-y-6">
        <header class="panel px-5 py-5 md:px-7 md:py-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-blue-600">Layanan Antrean Dekanat</p>
                <h1 class="display-title text-2xl md:text-4xl font-bold text-slate-900 mt-1">Fakultas Teknologi Informasi</h1>
                <p class="text-sm md:text-base text-slate-600 mt-1">Universitas Widya Dharma Pontianak</p>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-5">
                <div class="text-sm text-slate-600 sm:text-right">
                    <p id="current-date" class="font-semibold">-</p>
                    <p id="current-time" class="text-xl font-bold text-slate-800">-</p>
                </div>

                <a href="{{ route('welcome') }}"
                    class="btn-brand inline-flex items-center justify-center gap-2 px-4 py-2">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </header>

        <section class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-5">
            <div class="panel metric-card metric-blue p-4 md:p-5">
                <p class="text-xs text-slate-500">Total Antrean Hari Ini</p>
                <p id="total-queues" class="text-3xl font-extrabold text-blue-700 mt-2">0</p>
            </div>
            <div class="panel metric-card metric-green p-4 md:p-5">
                <p class="text-xs text-slate-500">Dosen Sedang Buka</p>
                <p id="available-staff" class="text-3xl font-extrabold text-emerald-700 mt-2">0</p>
            </div>
            <div class="panel metric-card metric-orange p-4 md:p-5">
                <p class="text-xs text-slate-500">Sedang Diproses</p>
                <p id="serving-queues" class="text-3xl font-extrabold text-amber-700 mt-2">0</p>
            </div>
            <div class="panel metric-card metric-slate p-4 md:p-5">
                <p class="text-xs text-slate-500">Sedang Menunggu</p>
                <p id="waiting-queues" class="text-3xl font-extrabold text-slate-700 mt-2">0</p>
            </div>
        </section>

        <section class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <div class="panel p-5 md:p-6">
                <h2 class="text-lg font-bold text-slate-900 flex items-center mb-4">
                    <i class="fa-solid fa-users mr-2 text-slate-500"></i>
                    Dosen Aktif & Ringkasan Antrean
                </h2>
                <ul id="staff-status" class="space-y-3 max-h-[620px] overflow-y-auto pr-1 custom-scroll">
                    <li class="text-slate-500 text-sm">Memuat data...</li>
                </ul>
            </div>

            <div class="panel p-5 md:p-6">
                <h2 class="text-lg font-bold text-slate-900 flex items-center mb-4">
                    <i class="fa-solid fa-list-check mr-2 text-slate-500"></i>
                    Status Antrean Hari Ini
                </h2>
                <ul id="queue-list" class="space-y-3 max-h-[620px] overflow-y-auto pr-1 custom-scroll">
                    <li class="text-slate-500 text-sm">Memuat data...</li>
                </ul>
            </div>
        </section>
    </div>

    <script>
        // Escape teks agar data dari server aman disisipkan ke HTML dinamis.
        function escapeHtml(value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        // Jam tampilan disegarkan tiap detik agar layar display selalu terlihat hidup.
        function updateClock() {
            const now = new Date();
            const dateOptions = {
                weekday: 'long',
                day: '2-digit',
                month: 'long',
                year: 'numeric',
                timeZone: 'Asia/Jakarta'
            };

            document.getElementById('current-date').textContent = now.toLocaleDateString('id-ID', dateOptions);
            document.getElementById('current-time').textContent = now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false,
                timeZone: 'Asia/Jakarta'
            }) + ' WIB';
        }

        // Render daftar antrean di panel kanan.
        function renderQueueItems(queues) {
            const ulQueue = document.getElementById('queue-list');
            if (!ulQueue) return;

            if (!Array.isArray(queues) || queues.length === 0) {
                ulQueue.innerHTML = '<li class="text-slate-500 text-sm">Belum ada antrean hari ini.</li>';
                return;
            }

            ulQueue.innerHTML = queues.map((q) => {
                const statusValue = (q.status || '').toLowerCase();
                let labelClass = 'queue-label label-wait';
                let labelText = 'Menunggu';

                if (statusValue === 'diproses') {
                    labelClass = 'queue-label label-process';
                    labelText = 'Diproses';
                } else if (statusValue === 'selesai') {
                    labelClass = 'queue-label label-done';
                    labelText = 'Selesai';
                }

                return `
                    <li class="queue-card flex items-center justify-between gap-3">
                        <div>
                            <p class="font-semibold text-slate-900">${escapeHtml(q.user?.name ?? q.nama ?? '-')}</p>
                            <p class="text-sm text-slate-600">${escapeHtml(q.service?.nama_layanan ?? '-')}</p>
                            <p class="text-xs text-slate-500">Dosen: ${escapeHtml(q.dosen?.name ?? '-')} (${escapeHtml(q.dosen?.kode ?? '-')})</p>
                        </div>
                        <span class="${labelClass}">${labelText}</span>
                    </li>
                `;
            }).join('');
        }

        // Render daftar dosen aktif beserta ringkasan antrean per dosen.
        function renderStaffItems(activeStaff, summaryMap = {}) {
            const ulStaff = document.getElementById('staff-status');
            if (!ulStaff) return;

            if (!Array.isArray(activeStaff) || activeStaff.length === 0) {
                ulStaff.innerHTML = '<li class="text-slate-500 text-sm">Tidak ada dosen aktif membuka antrean.</li>';
                return;
            }

            ulStaff.innerHTML = activeStaff.map((d) => {
                const statusClass = d.queue_status === 'occupied' ? 'status-pill status-occupied' : 'status-pill status-open';
                const statusIcon = d.queue_status === 'occupied' ? 'fa-hourglass-half' : 'fa-door-open';
                const summary = summaryMap[d.kode] ?? {};

                return `
                    <li class="summary-card p-4 bg-gradient-to-br from-white to-slate-50">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 flex-shrink-0">
                                    <i class="fa-solid fa-user"></i>
                                </div>
                                <div class="min-w-0 space-y-1.5">
                                    <p class="font-semibold text-slate-900 truncate">${escapeHtml(d.name ?? '-')}</p>
                                    <p class="text-xs text-slate-600 font-semibold flex items-center gap-1.5 leading-relaxed">
                                        <i class="fa-solid fa-id-card text-slate-400"></i>
                                        ${escapeHtml(d.kode ?? '-')}
                                    </p>
                                    <p class="text-xs text-slate-600 flex items-center gap-1.5 leading-relaxed">
                                        <i class="fa-solid fa-briefcase text-slate-400"></i>
                                        ${escapeHtml(d.jabatan ?? '-')}
                                    </p>
                                    <p class="text-xs text-slate-600 flex items-center gap-1.5 leading-relaxed">
                                        <i class="fa-solid fa-location-dot text-slate-400"></i>
                                        ${escapeHtml(d.ruangan ?? '-')}
                                    </p>
                                </div>
                            </div>
                            <span class="${statusClass}">
                                <i class="fa-solid ${statusIcon}"></i>
                                ${escapeHtml(d.queue_status_label ?? 'Aktif')}
                            </span>
                        </div>

                        <div class="mt-3 space-y-1.5">
                            <p class="text-xs text-slate-600 flex items-center gap-1.5 leading-relaxed">
                                <i class="fa-solid fa-screwdriver-wrench text-slate-400"></i>
                                ${escapeHtml(d.service?.nama_layanan ?? '-')}
                            </p>
                            <p class="text-xs text-slate-600 flex items-center gap-1.5 leading-relaxed">
                                <i class="fa-solid fa-clock text-slate-400"></i>
                                ${escapeHtml(d.waktu?.expected_jam_tutup ?? '-')}
                            </p>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2.5 mt-4 text-center">
                            <div class="rounded-md bg-indigo-50 px-2 py-2">
                                <p class="text-[11px] text-slate-500">Saat Ini</p>
                                <p class="text-sm font-extrabold text-indigo-700">${summary.nomor_saat_ini ? '#' + summary.nomor_saat_ini : '-'}</p>
                            </div>
                            <div class="rounded-md bg-blue-50 px-2 py-2">
                                <p class="text-[11px] text-slate-500">Terakhir</p>
                                <p class="text-sm font-extrabold text-blue-700">${summary.nomor_terakhir ? '#' + summary.nomor_terakhir : '-'}</p>
                            </div>
                            <div class="rounded-md bg-amber-50 px-2 py-2">
                                <p class="text-[11px] text-slate-500 flex items-center justify-center gap-1">
                                    <i class="fa-solid fa-hourglass-half"></i> Menunggu
                                </p>
                                <p class="text-sm font-extrabold text-amber-700">${summary.menunggu ?? 0}</p>
                            </div>
                            <div class="rounded-md bg-emerald-50 px-2 py-2">
                                <p class="text-[11px] text-slate-500 flex items-center justify-center gap-1">
                                    <i class="fa-solid fa-users"></i> Total Hari Ini
                                </p>
                                <p class="text-sm font-extrabold text-emerald-700">${summary.total_hari_ini ?? 0}</p>
                            </div>
                        </div>
                    </li>
                `;
            }).join('');
        }

        // Ambil data display terbaru dari endpoint JSON, lalu isi semua kartu ringkasan.
        async function fetchDisplayData() {
            try {
                const res = await fetch("{{ route('display.queues') }}");
                const payload = await res.json();

                const queues = Array.isArray(payload) ? payload : (payload.queues || []);
                const activeStaff = Array.isArray(payload?.active_staff) ? payload.active_staff : [];
                const dosenSummary = Array.isArray(payload?.dosen_queue_summary) ? payload.dosen_queue_summary : [];
                const summaryMap = Object.fromEntries(dosenSummary.map((item) => [item.kode, item]));

                const serving = queues.filter(q => (q.status || '').toLowerCase() === 'diproses').length;
                const waiting = queues.filter(q => (q.status || '').toLowerCase() === 'menunggu').length;

                document.getElementById('total-queues').textContent = queues.length;
                document.getElementById('available-staff').textContent = activeStaff.length;
                document.getElementById('serving-queues').textContent = serving;
                document.getElementById('waiting-queues').textContent = waiting;

                renderQueueItems(queues);
                renderStaffItems(activeStaff, summaryMap);
            } catch (error) {
                console.error(error);
            }
        }

        // Helper kecil agar refresh realtime bisa dipanggil dari polling maupun event Echo.
        async function refreshDisplayRealtime() {
            await fetchDisplayData();
        }

        // Clock lokal dan data antrean berjalan bersamaan supaya tampilan tetap akurat.
        setInterval(updateClock, 1000);
        updateClock();
        refreshDisplayRealtime();

        // Jika websocket tersedia, layar langsung reaktif saat status antrean berubah.
        if (window.Echo) {
            window.Echo.channel('queue-status')
                .listen('.queue.status.updated', () => {
                    refreshDisplayRealtime();
                });
        }
    </script>

    @include('partials.pwa-scripts')
</body>

</html>
