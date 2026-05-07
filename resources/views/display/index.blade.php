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

        @media (min-width: 1440px) {
            body {
                overflow: hidden;
            }
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

        .staff-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: .75rem;
        }

        @media (min-width: 1024px) {
            .staff-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (min-width: 1536px) {
            .staff-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (min-width: 1920px) {
            .staff-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }

        .queue-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: .75rem;
        }

        @media (min-width: 1280px) {
            .queue-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (min-width: 1920px) {
            .queue-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        .queue-card {
            padding: .9rem;
        }

        @media (min-width: 1440px) {
            .queue-card {
                padding: .75rem .8rem;
            }
        }

        .staff-hero-panel {
            background:
                radial-gradient(circle at top right, rgba(15, 96, 240, .12), transparent 28%),
                linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
            border: 1px solid #bfd6ff;
            box-shadow: 0 18px 40px -24px rgba(15, 96, 240, .45);
        }

        @media (min-width: 1440px) {
            .staff-hero-panel {
                padding-top: 1.1rem;
                padding-bottom: 1.1rem;
            }
        }

        .staff-panel-kicker {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            padding: .28rem .65rem;
            border-radius: 999px;
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .03em;
            color: #1d4ed8;
            background: #dbeafe;
            border: 1px solid #bfdbfe;
        }

        .staff-panel-note {
            color: #475569;
            font-size: .88rem;
            line-height: 1.45;
        }

        @media (min-width: 1440px) {
            .staff-panel-note {
                font-size: .82rem;
            }
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

        .btn-ghost {
            background: rgba(255, 255, 255, .75);
            color: #0f172a;
            border: 1px solid #bfdbfe;
            border-radius: .65rem;
            font-weight: 600;
            transition: background-color .18s ease, transform .12s ease, border-color .18s ease;
        }

        .btn-ghost:hover {
            background: rgba(255, 255, 255, .96);
            border-color: #93c5fd;
        }
    </style>
</head>

<body class="p-4 md:p-7">
    <div class="max-w-[1800px] mx-auto space-y-6">
        {{-- Header display publik yang mengarahkan pengunjung ke informasi antrean utama --}}
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
                <button type="button" id="fullscreen-toggle"
                    class="btn-ghost inline-flex items-center justify-center gap-2 px-4 py-2">
                    <i class="fa-solid fa-expand"></i>
                    <span>Layar Penuh</span>
                </button>
            </div>
        </header>

        {{-- Daftar dosen aktif dan status antrean hari ini --}}
        <section class="space-y-6">
            <div class="panel staff-hero-panel p-6 md:p-7">
                <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between mb-5">
                    <div>
                        <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-users text-blue-500"></i>
                            Dosen Aktif & Ringkasan Antrean
                        </h2>
                    </div>
                    <div class="inline-flex items-center gap-2 self-start md:self-auto px-3 py-2 rounded-xl bg-white/80 border border-blue-100 text-sm font-semibold text-slate-700">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Pantauan Aktif
                    </div>
                </div>
                <ul id="staff-status" class="staff-grid">
                    <li class="text-slate-500 text-sm">Memuat data...</li>
                </ul>
            </div>

            <div class="panel p-4 md:p-5">
                <h2 class="text-lg font-bold text-slate-900 flex items-center mb-4">
                    <i class="fa-solid fa-list-check mr-2 text-slate-500"></i>
                    Status Antrean Hari Ini
                </h2>
                <ul id="queue-list" class="queue-grid max-h-[620px] overflow-y-auto pr-1 custom-scroll">
                    <li class="text-slate-500 text-sm">Memuat data...</li>
                </ul>
            </div>
        </section>
    </div>

    {{-- Script display yang mengarah ke polling data, jam real-time, dan refresh websocket --}}
    <script>
        // Elemen utama dipakai untuk mengisi layar publik secara dinamis dari response JSON.
        function escapeHtml(value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }
        // Jam ditampilkan live supaya layar publik terasa selalu aktif.
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
        // Render daftar antrean hari ini ke panel kanan display publik.
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
        // Render pejabat aktif beserta ringkasan antreannya ke panel kiri display publik.
        function renderStaffItems(activeStaff, summaryMap = {}) {
            const ulStaff = document.getElementById('staff-status');
            if (!ulStaff) return;

            if (!Array.isArray(activeStaff) || activeStaff.length === 0) {
                ulStaff.innerHTML = '<li class="text-slate-500 text-sm">Tidak ada dosen aktif membuka antrean.</li>';
                return;
            }

            ulStaff.innerHTML = activeStaff.map((d) => {
                const isClosed = d.queue_status === 'closed';
                const statusClass = isClosed
                    ? 'status-pill status-closed'
                    : d.queue_status === 'occupied'
                        ? 'status-pill status-occupied'
                        : 'status-pill status-open';
                const statusIcon = isClosed
                    ? 'fa-door-closed'
                    : d.queue_status === 'occupied'
                        ? 'fa-hourglass-half'
                        : 'fa-door-open';
                const summary = summaryMap[d.kode] ?? {};
                const serviceLabel = isClosed ? 'Layanan Terakhir' : 'Layanan';
                const timeLabel = isClosed ? 'Tutup' : 'Perkiraan Tutup';
                const serviceText = escapeHtml(d.service?.nama_layanan ?? (isClosed ? 'Antrean Ditutup' : '-'));
                const timeText = escapeHtml(
                    d.waktu?.jam_tutup
                    ?? d.waktu?.expected_jam_tutup
                    ?? '-'
                );

                return `
                    <li class="summary-card p-3 bg-gradient-to-br from-white to-slate-50">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 flex-shrink-0">
                                    <i class="fa-solid fa-user"></i>
                                </div>
                                <div class="min-w-0 space-y-1">
                                    <p class="font-semibold text-slate-900 text-[13px] leading-snug break-words">${escapeHtml(d.name ?? '-')}</p>
                                    <p class="text-[10px] text-slate-600 font-semibold flex items-center gap-1.5 leading-relaxed">
                                        <i class="fa-solid fa-id-card text-slate-400"></i>
                                        ${escapeHtml(d.kode ?? '-')}
                                    </p>
                                    <p class="text-[10px] text-slate-600 flex items-center gap-1.5 leading-relaxed">
                                        <i class="fa-solid fa-briefcase text-slate-400"></i>
                                        ${escapeHtml(d.jabatan ?? '-')}
                                    </p>
                                    <p class="text-[10px] text-slate-600 flex items-center gap-1.5 leading-relaxed">
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

                        <div class="mt-1.5 space-y-0.5">
                            <p class="text-[10px] text-slate-600 flex items-center gap-1.5 leading-relaxed">
                                <i class="fa-solid fa-screwdriver-wrench text-slate-400"></i>
                                ${serviceLabel}: ${serviceText}
                            </p>
                            <p class="text-[10px] text-slate-600 flex items-center gap-1.5 leading-relaxed">
                                <i class="fa-solid fa-clock text-slate-400"></i>
                                ${timeLabel}: ${timeText}
                            </p>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mt-3 text-center">
                            <div class="rounded-md bg-indigo-50 px-2 py-1.5">
                                <p class="text-[11px] text-slate-500">Saat Ini</p>
                                <p class="text-sm font-extrabold text-indigo-700">${summary.nomor_saat_ini ? '#' + summary.nomor_saat_ini : '-'}</p>
                            </div>
                            <div class="rounded-md bg-blue-50 px-2 py-1.5">
                                <p class="text-[11px] text-slate-500">Terakhir</p>
                                <p class="text-sm font-extrabold text-blue-700">${summary.nomor_terakhir ? '#' + summary.nomor_terakhir : '-'}</p>
                            </div>
                            <div class="rounded-md bg-amber-50 px-2 py-1.5">
                                <p class="text-[11px] text-slate-500 flex items-center justify-center gap-1">
                                    <i class="fa-solid fa-hourglass-half"></i> Menunggu
                                </p>
                                <p class="text-sm font-extrabold text-amber-700">${summary.menunggu ?? 0}</p>
                            </div>
                            <div class="rounded-md bg-emerald-50 px-2 py-1.5">
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
        // Memuat data dari endpoint JSON display dan mengisi semua kartu ringkasan.
        async function fetchDisplayData() {
            try {
                const res = await fetch("{{ route('display.queues') }}");
                const payload = await res.json();

                const queues = Array.isArray(payload) ? payload : (payload.queues || []);
                const activeStaff = Array.isArray(payload?.active_staff) ? payload.active_staff : [];
                const dosenSummary = Array.isArray(payload?.dosen_queue_summary) ? payload.dosen_queue_summary : [];
                const summaryMap = Object.fromEntries(dosenSummary.map((item) => [item.kode, item]));

                renderQueueItems(queues);
                renderStaffItems(activeStaff, summaryMap);
            } catch (error) {
                console.error(error);
            }
        }
        // Helper kecil supaya refresh bisa dipanggil dari polling maupun event realtime.
        async function refreshDisplayRealtime() {
            await fetchDisplayData();
        }
        async function toggleFullscreen() {
            const button = document.getElementById('fullscreen-toggle');

            try {
                if (!document.fullscreenElement) {
                    await document.documentElement.requestFullscreen();
                } else {
                    await document.exitFullscreen();
                }
            } catch (error) {
                console.error(error);
            }

            updateFullscreenButton();
        }
        function updateFullscreenButton() {
            const button = document.getElementById('fullscreen-toggle');
            if (!button) return;

            const isFullscreen = Boolean(document.fullscreenElement);
            button.innerHTML = isFullscreen
                ? '<i class="fa-solid fa-compress"></i><span>Keluar Fullscreen</span>'
                : '<i class="fa-solid fa-expand"></i><span>Layar Penuh</span>';
        }
        setInterval(updateClock, 1000);
        updateClock();
        refreshDisplayRealtime();
        if (window.Echo) {
            window.Echo.channel('queue-status')
                .listen('.queue.status.updated', () => {
                    refreshDisplayRealtime();
                });
        }
        const fullscreenToggle = document.getElementById('fullscreen-toggle');
        if (fullscreenToggle) {
            fullscreenToggle.addEventListener('click', toggleFullscreen);
        }
        document.addEventListener('fullscreenchange', updateFullscreenButton);
        updateFullscreenButton();
    </script>

    @include('partials.pwa-scripts')
</body>

</html>
