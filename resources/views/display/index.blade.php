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

        @media (min-width: 1440px) and (min-aspect-ratio: 16/9) {
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

        .display-shell {
            width: min(1800px, 100%);
            margin: 0 auto;
            display: grid;
            gap: 1.5rem;
        }

        .display-main {
            display: grid;
            gap: 1.5rem;
        }

        .slide-stage {
            min-height: clamp(620px, 72vh, 820px);
            position: relative;
        }

        .slide-track {
            position: relative;
            min-height: inherit;
        }

        .slide-item {
            display: none;
            min-height: inherit;
        }

        .slide-item.active {
            display: block;
            animation: slideFadeIn .35s ease;
        }

        @keyframes slideFadeIn {
            from {
                opacity: 0;
                transform: translateY(12px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .slide-content {
            min-height: inherit;
        }

        .slide-history-list {
            max-height: min(520px, 58vh);
        }

        .slide-empty {
            min-height: clamp(620px, 72vh, 820px);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 1rem;
            border: 1px dashed #bfdbfe;
            background: rgba(255, 255, 255, .72);
        }

        .slide-dots {
            display: flex;
            flex-wrap: wrap;
            gap: .45rem;
        }

        .slide-dot {
            width: .78rem;
            height: .78rem;
            border-radius: 999px;
            border: 0;
            background: #cbd5e1;
            transition: transform .2s ease, background-color .2s ease, width .2s ease;
        }

        .slide-dot.active {
            width: 2.2rem;
            background: #2563eb;
        }

        @media (max-width: 768px) {
            .slide-stage {
                min-height: 640px;
            }

            .slide-history-list {
                max-height: 300px;
            }
        }

        @media (min-width: 1440px) and (min-aspect-ratio: 16/9) {
            .display-shell {
                width: min(1880px, 100%);
                gap: 1rem;
            }

            .display-main {
                display: flex;
                flex-direction: column;
                gap: 1rem;
            }

            .staff-hero-panel {
                overflow: hidden;
            }

            .staff-hero-panel #staff-status {
                flex: 1;
                min-height: 0;
                overflow-y: auto;
                padding-right: .15rem;
            }

            .queue-panel {
                display: flex;
                flex-direction: column;
            }

            .queue-panel h2 {
                flex: 0 0 auto;
            }

            .queue-panel #queue-list {
                flex: 1;
                min-height: 0;
                max-height: none;
            }

            .queue-card {
                padding: .78rem .85rem;
            }

            .summary-card {
                padding: .85rem;
            }

            .summary-card .grid {
                gap: .5rem;
            }

            .staff-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: .9rem;
            }

            .staff-card,
            .summary-card {
                min-width: 0;
            }

            .staff-card .min-w-0,
            .summary-card .min-w-0 {
                min-width: 0;
            }

            .staff-hero-panel h2 {
                font-size: 2rem;
            }

            .staff-panel-note {
                font-size: .9rem;
            }

            .summary-card p.font-semibold.text-slate-900 {
                font-size: .95rem;
            }

            .summary-card .text-slate-600,
            .summary-card .text-slate-500,
            .summary-card .text-slate-400 {
                font-size: .75rem;
                line-height: 1.35;
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

        @media (max-width: 640px) {
            body {
                padding: .75rem;
            }

            .panel {
                border-radius: .9rem;
            }

            .display-title {
                font-size: 1.45rem !important;
                line-height: 1.08 !important;
            }

            .staff-hero-panel,
            .queue-card,
            .staff-card,
            .summary-card {
                border-radius: .85rem;
            }

            header.panel {
                gap: .9rem;
                padding: 1rem;
            }

            header.panel h1 {
                font-size: 1.35rem !important;
                line-height: 1.15 !important;
            }

            header.panel p {
                font-size: .85rem;
            }

            #current-time {
                font-size: 1.1rem !important;
            }

            #fullscreen-toggle {
                display: none;
            }

            .staff-hero-panel {
                padding: 1rem;
            }

            .staff-hero-panel h2 {
                font-size: 1.05rem !important;
                line-height: 1.2 !important;
            }

            .staff-panel-note {
                font-size: .8rem;
            }

            .queue-card {
                padding: .85rem;
                align-items: flex-start;
            }

            .queue-card p.font-semibold {
                font-size: .92rem;
            }

            .queue-card p.text-sm {
                font-size: .78rem;
            }

            .status-pill,
            .queue-label {
                font-size: .68rem;
                padding: .42rem .55rem;
            }

            .summary-card {
                padding: .75rem;
            }

            .summary-card .grid {
                gap: .45rem;
            }
        }
    </style>
</head>

<body class="p-3 md:p-7">
    <div class="display-shell">
        {{-- Header display publik yang mengarahkan pengunjung ke informasi antrean utama --}}
        <header class="panel px-4 py-4 md:px-7 md:py-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-blue-600">Layanan Antrean Dekanat</p>
                <h1 class="display-title text-2xl md:text-4xl font-bold text-slate-900 mt-1">Fakultas Teknologi Informasi</h1>
                <p class="text-sm md:text-base text-slate-600 mt-1">Universitas Widya Dharma Pontianak</p>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-5">
                <div class="text-sm text-slate-600 sm:text-right leading-tight">
                    <p id="current-date" class="font-semibold">-</p>
                    <p id="current-time" class="text-xl font-bold text-slate-800">-</p>
                </div>

                <a href="{{ route('welcome') }}"
                    class="btn-brand inline-flex items-center justify-center gap-2 px-4 py-2 text-sm sm:text-base">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Kembali</span>
                </a>
                <button type="button" id="fullscreen-toggle"
                    class="btn-ghost inline-flex items-center justify-center gap-2 px-4 py-2 text-sm sm:text-base">
                    <i class="fa-solid fa-expand"></i>
                    <span>Layar Penuh</span>
                </button>
            </div>
        </header>

        {{-- Slideshow ruang/dosen agar setiap kartu tampil penuh bergantian di layar TV --}}
        <section class="display-main">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="panel metric-card metric-blue p-4 md:p-5 flex items-center gap-4">
                    <i class="fa-solid fa-users text-3xl text-slate-500"></i>
                    <div>
                        <p id="metric-total-rooms" class="text-2xl font-bold text-slate-900">0</p>
                        <p class="text-sm text-slate-500">Ruang Tampil</p>
                    </div>
                </div>
                <div class="panel metric-card metric-green p-4 md:p-5 flex items-center gap-4">
                    <i class="fa-solid fa-door-open text-3xl text-emerald-600"></i>
                    <div>
                        <p id="metric-open-rooms" class="text-2xl font-bold text-slate-900">0</p>
                        <p class="text-sm text-slate-500">Ruang Buka</p>
                    </div>
                </div>
                <div class="panel metric-card metric-orange p-4 md:p-5 flex items-center gap-4">
                    <i class="fa-solid fa-hourglass-half text-3xl text-amber-600"></i>
                    <div>
                        <p id="metric-waiting" class="text-2xl font-bold text-slate-900">0</p>
                        <p class="text-sm text-slate-500">Menunggu</p>
                    </div>
                </div>
                <div class="panel metric-card metric-slate p-4 md:p-5 flex items-center gap-4">
                    <i class="fa-solid fa-clipboard-list text-3xl text-slate-600"></i>
                    <div>
                        <p id="metric-total-queues" class="text-2xl font-bold text-slate-900">0</p>
                        <p class="text-sm text-slate-500">Antrean Hari Ini</p>
                    </div>
                </div>
            </div>

            <div class="panel staff-hero-panel p-4 md:p-7">
                <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between mb-5">
                    <div>
                        <p class="staff-panel-kicker">
                            <i class="fa-solid fa-tv"></i>
                            Slideshow Ruang
                        </p>
                        <h2 class="text-xl md:text-3xl font-extrabold text-slate-900 flex items-center gap-2 mt-3">
                            <i class="fa-solid fa-layer-group text-blue-500"></i>
                            Informasi Ruang & Riwayat Antrean
                        </h2>
                        <p id="slide-caption" class="staff-panel-note mt-2 max-w-3xl">
                            Setiap ruang/dosen tampil bergantian setiap 3 detik.
                        </p>
                    </div>
                    <div class="flex items-center gap-2 self-start md:self-auto px-3 py-2 rounded-xl bg-white/80 border border-blue-100 text-sm font-semibold text-slate-700">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span id="slide-counter">0 / 0</span>
                    </div>
                </div>

                <div class="slide-stage">
                    <div id="slide-track" class="slide-track">
                        <div class="slide-empty text-slate-500 text-sm">Memuat data...</div>
                    </div>
                </div>

                <div class="mt-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                    <div id="slide-dots" class="slide-dots"></div>
                    <p id="slide-hint" class="text-xs md:text-sm text-slate-500">
                        Tampilan ini otomatis berganti setiap 3 detik.
                    </p>
                </div>
            </div>
        </section>
    </div>

    {{-- Script display yang mengarah ke polling data, jam real-time, dan refresh websocket --}}
    <script>
        const slideIntervalMs = 3000;
        let slideTimer = null;
        let slideIndex = 0;
        let staffSlidesCache = [];
        let summaryMapCache = {};

        function escapeHtml(value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        function formatJakartaDateTime(value) {
            if (!value) return '-';
            const date = new Date(value);
            if (Number.isNaN(date.getTime())) return '-';
            return date.toLocaleString('id-ID', {
                timeZone: 'Asia/Jakarta',
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                hour12: false,
            });
        }

        function getStatusMeta(status = '') {
            const normalized = String(status ?? '').toLowerCase();
            if (normalized === 'occupied') {
                return { className: 'status-pill status-occupied', label: 'Melayani', icon: 'fa-hourglass-half' };
            }
            if (normalized === 'open') {
                return { className: 'status-pill status-open', label: 'Buka', icon: 'fa-door-open' };
            }
            return { className: 'status-pill status-closed', label: 'Tutup', icon: 'fa-door-closed' };
        }

        function getQueueStatusBadge(status = '', statusLabel = '') {
            const normalized = String(status ?? '').toLowerCase();
            if (normalized === 'diproses') {
                return { className: 'queue-label label-process', label: statusLabel || 'Diproses' };
            }
            if (normalized === 'selesai') {
                return { className: 'queue-label label-done', label: statusLabel || 'Selesai' };
            }
            if (normalized === 'batal') {
                return { className: 'queue-label label-wait', label: statusLabel || 'Batal' };
            }
            return { className: 'queue-label label-wait', label: statusLabel || 'Menunggu' };
        }

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

        function renderMetrics(staffSlides = []) {
            const totalRooms = staffSlides.length;
            const openRooms = staffSlides.filter((d) => ['open', 'occupied'].includes(String(d.queue_status ?? '').toLowerCase())).length;
            const waitingQueues = staffSlides.reduce((count, d) => count + (Array.isArray(d.today_queues) ? d.today_queues.filter((q) => String(q.status ?? '') === 'menunggu').length : 0), 0);
            const totalQueues = staffSlides.reduce((count, d) => count + (Array.isArray(d.today_queues) ? d.today_queues.length : 0), 0);

            const totalRoomsEl = document.getElementById('metric-total-rooms');
            const openRoomsEl = document.getElementById('metric-open-rooms');
            const waitingEl = document.getElementById('metric-waiting');
            const totalQueuesEl = document.getElementById('metric-total-queues');

            if (totalRoomsEl) totalRoomsEl.textContent = String(totalRooms);
            if (openRoomsEl) openRoomsEl.textContent = String(openRooms);
            if (waitingEl) waitingEl.textContent = String(waitingQueues);
            if (totalQueuesEl) totalQueuesEl.textContent = String(totalQueues);
        }

        function buildSlideHtml(d, summary = {}) {
            const queues = Array.isArray(d.today_queues) ? d.today_queues : [];
            const statusMeta = getStatusMeta(d.queue_status);
            const roomName = [d.ruangan, d.jabatan].filter(Boolean).join(' • ');
            const currentQueueText = summary.nomor_saat_ini ? `#${summary.nomor_saat_ini}` : '-';
            const lastQueueText = summary.nomor_terakhir ? `#${summary.nomor_terakhir}` : '-';

            return `
                <article class="slide-item" data-slide-index="">
                    <div class="slide-content grid grid-cols-1 xl:grid-cols-[1.08fr_.92fr] gap-4 md:gap-5 h-full">
                        <div class="panel p-4 md:p-6 flex flex-col gap-4 min-h-0">
                            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="staff-panel-kicker w-fit">
                                        <i class="fa-solid fa-location-dot"></i>
                                        Ruang ${escapeHtml(d.ruangan ?? '-')}
                                    </p>
                                    <h3 class="mt-3 text-3xl md:text-5xl font-black text-slate-900 leading-tight break-words">
                                        ${escapeHtml(d.name ?? '-')}
                                    </h3>
                                    <p class="mt-2 text-slate-700 font-semibold text-base md:text-xl break-words">
                                        ${escapeHtml(roomName || '-')}
                                    </p>
                                </div>
                                <span class="${statusMeta.className}">
                                    <i class="fa-solid ${statusMeta.icon}"></i>
                                    ${escapeHtml(statusMeta.label)}
                                </span>
                            </div>

                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                <div class="rounded-2xl bg-indigo-50 border border-indigo-100 px-3 py-3">
                                    <p class="text-[11px] text-slate-500">Saat Ini</p>
                                    <p class="text-2xl font-black text-indigo-700">${currentQueueText}</p>
                                </div>
                                <div class="rounded-2xl bg-blue-50 border border-blue-100 px-3 py-3">
                                    <p class="text-[11px] text-slate-500">Terakhir</p>
                                    <p class="text-2xl font-black text-blue-700">${lastQueueText}</p>
                                </div>
                                <div class="rounded-2xl bg-amber-50 border border-amber-100 px-3 py-3">
                                    <p class="text-[11px] text-slate-500">Menunggu</p>
                                    <p class="text-2xl font-black text-amber-700">${summary.menunggu ?? 0}</p>
                                </div>
                                <div class="rounded-2xl bg-emerald-50 border border-emerald-100 px-3 py-3">
                                    <p class="text-[11px] text-slate-500">Total</p>
                                    <p class="text-2xl font-black text-emerald-700">${summary.total_hari_ini ?? queues.length}</p>
                                </div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 bg-white/90 p-5 md:p-6 shadow-sm">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="space-y-3">
                                        <p class="text-sm md:text-base uppercase tracking-[0.22em] text-blue-600 font-black">Informasi Ruang</p>
                                        <p class="text-base md:text-lg text-slate-800 leading-relaxed">
                                            <span class="font-semibold">Jabatan:</span> ${escapeHtml(d.jabatan ?? '-')}
                                        </p>
                                        <p class="text-base md:text-lg text-slate-800 leading-relaxed">
                                            <span class="font-semibold">Layanan:</span> ${escapeHtml(d.service?.nama_layanan ?? '-')}
                                        </p>
                                        <p class="text-base md:text-lg text-slate-800 leading-relaxed">
                                            <span class="font-semibold">Status:</span> ${escapeHtml(d.queue_status_label ?? '-')}
                                        </p>
                                    </div>
                                    <div class="space-y-3">
                                        <p class="text-sm md:text-base uppercase tracking-[0.22em] text-blue-600 font-black">Waktu Ruang</p>
                                        <p class="text-base md:text-lg text-slate-800 leading-relaxed">
                                            <span class="font-semibold">Buka:</span> ${escapeHtml(d.waktu?.jam_buka ?? d.waktu?.expected_jam_buka ?? '-')}
                                        </p>
                                        <p class="text-base md:text-lg text-slate-800 leading-relaxed">
                                            <span class="font-semibold">Tutup:</span> ${escapeHtml(d.waktu?.jam_tutup ?? d.waktu?.expected_jam_tutup ?? '-')}
                                        </p>
                                        <p class="text-base md:text-lg text-slate-800 leading-relaxed">
                                            <span class="font-semibold">Ruang:</span> ${escapeHtml(d.ruangan ?? '-')}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                <div class="rounded-xl bg-slate-50 border border-slate-200 px-3 py-2.5">
                                    <p class="text-[11px] text-slate-500">Menunggu Mahasiswa</p>
                                    <p class="font-black text-slate-900">${summary.menunggu_mahasiswa ?? 0}</p>
                                </div>
                                <div class="rounded-xl bg-slate-50 border border-slate-200 px-3 py-2.5">
                                    <p class="text-[11px] text-slate-500">Total Mahasiswa</p>
                                    <p class="font-black text-slate-900">${summary.total_hari_ini_mahasiswa ?? 0}</p>
                                </div>
                                <div class="rounded-xl bg-slate-50 border border-slate-200 px-3 py-2.5">
                                    <p class="text-[11px] text-slate-500">Nomor Saat Ini</p>
                                    <p class="font-black text-slate-900">${currentQueueText}</p>
                                </div>
                                <div class="rounded-xl bg-slate-50 border border-slate-200 px-3 py-2.5">
                                    <p class="text-[11px] text-slate-500">Nomor Terakhir</p>
                                    <p class="font-black text-slate-900">${lastQueueText}</p>
                                </div>
                            </div>
                        </div>

                        <div class="panel p-4 md:p-6 flex flex-col min-h-0">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <h4 class="text-lg md:text-xl font-bold text-slate-900">Riwayat Antrean Hari Ini</h4>
                                    <p class="text-xs md:text-sm text-slate-500">Semua antrean pada ruang ini tampil bergantian dalam satu slide.</p>
                                </div>
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-blue-100 text-blue-700">
                                    ${queues.length} data
                                </span>
                            </div>

                            <div class="slide-history-list custom-scroll mt-4 flex-1 overflow-y-auto pr-1 space-y-2">
                                ${queues.length === 0 ? `
                                    <div class="h-full flex items-center justify-center rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-6 text-center">
                                        <p class="text-slate-500 text-sm md:text-base">Belum ada riwayat antrean untuk ruang ini.</p>
                                    </div>
                                ` : queues.map((queue) => {
                                    const badge = getQueueStatusBadge(queue.status, queue.status_label);
                                    const timeValue = String(queue.status ?? '').toLowerCase() === 'selesai'
                                        ? queue.updated_at
                                        : queue.created_at;

                                    return `
                                        <div class="rounded-2xl border border-slate-200 bg-white p-3 md:p-4 shadow-sm">
                                            <div class="flex items-start justify-between gap-3">
                                                <div class="min-w-0">
                                                    <p class="font-bold text-slate-900 text-sm md:text-base">#${escapeHtml(queue.nomor_antrian ?? '-')} - ${escapeHtml(queue.user?.name ?? '-')}</p>
                                                    <p class="text-xs md:text-sm text-slate-600 mt-1">${escapeHtml(queue.service?.nama_layanan ?? '-')}</p>
                                                    <p class="text-[11px] md:text-xs text-slate-400 mt-1">
                                                        ${escapeHtml(formatJakartaDateTime(timeValue))} WIB
                                                    </p>
                                                </div>
                                                <span class="${badge.className}">${escapeHtml(badge.label)}</span>
                                            </div>
                                        </div>
                                    `;
                                }).join('')}
                            </div>
                        </div>
                    </div>
                </article>
            `;
        }

        function renderSlides(staffSlides = [], summaryMap = {}) {
            const slideTrack = document.getElementById('slide-track');
            const slideDots = document.getElementById('slide-dots');
            const slideCounter = document.getElementById('slide-counter');
            const slideCaption = document.getElementById('slide-caption');
            if (!slideTrack || !slideDots || !slideCounter || !slideCaption) return;

            staffSlidesCache = Array.isArray(staffSlides) ? staffSlides : [];
            summaryMapCache = summaryMap || {};

            if (staffSlidesCache.length === 0) {
                slideTrack.innerHTML = `
                    <div class="slide-empty">
                        <div class="text-center">
                            <i class="fa-solid fa-tv text-4xl text-blue-200"></i>
                            <p class="mt-3 text-slate-500 text-sm md:text-base">Belum ada ruang/dosen aktif untuk ditampilkan.</p>
                        </div>
                    </div>
                `;
                slideDots.innerHTML = '';
                slideCounter.textContent = '0 / 0';
                slideCaption.textContent = 'Menunggu ruang aktif berikutnya.';
                renderMetrics(staffSlidesCache);
                stopSlideshow();
                return;
            }

            slideTrack.innerHTML = staffSlidesCache.map((d, index) => {
                const summary = summaryMapCache[d.kode] ?? {};
                return buildSlideHtml(d, summary).replace('<article class="slide-item" data-slide-index="">', `<article class="slide-item" data-slide-index="${index}">`);
            }).join('');

            slideDots.innerHTML = staffSlidesCache.map((d, index) => `
                <button type="button" class="slide-dot ${index === 0 ? 'active' : ''}" data-slide-index="${index}"
                    aria-label="Buka slide ${index + 1} untuk ${escapeHtml(d.name ?? '-')}"></button>
            `).join('');

            renderMetrics(staffSlidesCache);
            updateActiveSlide(0);
            startSlideshow();
        }

        function updateActiveSlide(nextIndex) {
            if (staffSlidesCache.length === 0) return;

            slideIndex = ((nextIndex % staffSlidesCache.length) + staffSlidesCache.length) % staffSlidesCache.length;
            const slides = Array.from(document.querySelectorAll('.slide-item'));
            const dots = Array.from(document.querySelectorAll('.slide-dot'));
            const slideCounter = document.getElementById('slide-counter');
            const slideCaption = document.getElementById('slide-caption');

            slides.forEach((slide, index) => {
                slide.classList.toggle('active', index === slideIndex);
            });

            dots.forEach((dot, index) => {
                dot.classList.toggle('active', index === slideIndex);
            });

            const current = staffSlidesCache[slideIndex];
            if (slideCounter) {
                slideCounter.textContent = `${slideIndex + 1} / ${staffSlidesCache.length}`;
            }
            if (slideCaption) {
                const queuesCount = Array.isArray(current?.today_queues) ? current.today_queues.length : 0;
                slideCaption.textContent = `${current?.name ?? '-'} menampilkan ${queuesCount} antrean hari ini di ruang ${current?.ruangan ?? '-'}.`;
            }
        }

        function stopSlideshow() {
            if (slideTimer) {
                clearInterval(slideTimer);
                slideTimer = null;
            }
        }

        function startSlideshow() {
            stopSlideshow();
            if (staffSlidesCache.length <= 1) return;
            slideTimer = setInterval(() => {
                updateActiveSlide(slideIndex + 1);
            }, slideIntervalMs);
        }

        document.addEventListener('click', (event) => {
            const dot = event.target.closest('.slide-dot');
            if (!dot) return;
            const index = Number(dot.dataset.slideIndex ?? 0);
            if (!Number.isNaN(index)) {
                updateActiveSlide(index);
                startSlideshow();
            }
        });

        async function fetchDisplayData() {
            try {
                const res = await fetch("{{ route('display.queues') }}");
                const payload = await res.json();

                const staffSlides = Array.isArray(payload?.staff_slides)
                    ? payload.staff_slides
                    : (Array.isArray(payload?.active_staff) ? payload.active_staff : []);
                const dosenSummary = Array.isArray(payload?.dosen_queue_summary) ? payload.dosen_queue_summary : [];
                const summaryMap = Object.fromEntries(dosenSummary.map((item) => [item.kode, item]));

                renderSlides(staffSlides, summaryMap);
            } catch (error) {
                console.error(error);
            }
        }

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
