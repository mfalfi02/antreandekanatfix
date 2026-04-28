<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Bulanan Statistik Layanan</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            color: #1f2937;
            font-size: 11px;
            line-height: 1.45;
            margin: 0;
            background: #f8fbff;
        }
        .page {
            padding: 24px 28px;
        }
        .header {
            display: table;
            width: 100%;
            margin-bottom: 16px;
            border-bottom: 2px solid #dbeafe;
            padding-bottom: 14px;
        }
        .header-banner {
            background: linear-gradient(90deg, #1d4ed8 0%, #0891b2 55%, #0f766e 100%);
            color: #fff;
            border-radius: 10px;
            padding: 14px 16px;
            margin-bottom: 14px;
        }
        .header-cell {
            display: table-cell;
            vertical-align: middle;
        }
        .logo {
            width: 64px;
            height: 64px;
            object-fit: contain;
            margin-right: 14px;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 4px;
        }
        .subtitle {
            color: #475569;
            font-size: 11px;
        }
        .meta {
            text-align: right;
            color: #475569;
            font-size: 10px;
        }
        .meta strong {
            color: #0f172a;
            font-size: 11px;
        }
        .summary {
            width: 100%;
            border-collapse: collapse;
            margin: 14px 0 18px;
        }
        .summary td {
            border: 1px solid #dbe4f0;
            padding: 9px 10px;
            vertical-align: top;
            background: #fff;
        }
        .summary .label {
            color: #64748b;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: .04em;
        }
        .summary .value {
            font-size: 15px;
            font-weight: bold;
            margin-top: 4px;
            color: #0f172a;
        }
        .summary .accent-blue {
            border-top: 3px solid #3b82f6;
        }
        .summary .accent-teal {
            border-top: 3px solid #14b8a6;
        }
        .summary .accent-amber {
            border-top: 3px solid #f59e0b;
        }
        .summary .accent-indigo {
            border-top: 3px solid #6366f1;
        }
        .section {
            margin-bottom: 18px;
        }
        .section h2 {
            font-size: 13px;
            margin: 0 0 8px;
            color: #0f172a;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .table th,
        .table td {
            border: 1px solid #dbe4f0;
            padding: 7px 8px;
            vertical-align: top;
            word-wrap: break-word;
        }
        .table th {
            background: #eff6ff;
            color: #1e3a8a;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .04em;
        }
        .table tr:nth-child(even) td {
            background: #fafcff;
        }
        .table td.num,
        .table th.num {
            text-align: right;
        }
        .muted {
            color: #64748b;
        }
        .notice {
            background: #fffbeb;
            border: 1px solid #fde68a;
            color: #92400e;
            border-radius: 8px;
            padding: 9px 10px;
            margin-bottom: 10px;
            font-size: 10px;
        }
        .badge {
            display: inline-block;
            padding: 3px 7px;
            border-radius: 999px;
            background: #eff6ff;
            color: #1d4ed8;
            font-size: 9px;
            margin: 0 4px 4px 0;
        }
        .badge-green {
            background: #ecfdf5;
            color: #047857;
        }
        .badge-rose {
            background: #fff1f2;
            color: #be123c;
        }
    </style>
</head>
<body>
    {{-- Data di halaman ini disiapkan khusus untuk export PDF laporan bulanan --}}
    @php
        $logoPath = public_path('images/logosistem.jpeg');
        $logoData = file_exists($logoPath)
            ? 'data:image/jpeg;base64,' . base64_encode(file_get_contents($logoPath))
            : null;
        $generatedAt = \Carbon\Carbon::now('Asia/Jakarta')->locale('id')->translatedFormat('d F Y H:i');
        $totalLayanan = $services->count();
        $totalMahasiswa = $services->sum('mahasiswa_count');
        $topService = $services->sortByDesc('mahasiswa_count')->first();
    @endphp

    {{-- Layout PDF dibuat rapat agar tetap terbaca saat dicetak --}}
    <div class="page">
        <div class="header-banner">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 72px; vertical-align: middle; padding-right: 12px;">
                        @if ($logoData)
                            <img src="{{ $logoData }}" alt="Logo Sistem" class="logo">
                        @endif
                    </td>
                    <td style="vertical-align: middle;">
                        <div class="title" style="color: #fff; margin-bottom: 2px;">Laporan Bulanan Statistik Layanan</div>
                        <div class="subtitle" style="color: rgba(255,255,255,.88);">Periode {{ $periodLabel ?? '-' }} (WIB)</div>
                        <div class="subtitle" style="color: rgba(255,255,255,.78);">Sistem Antrean Dekanat Universitas Widya Dharma</div>
                    </td>
                    <td class="meta" style="color: rgba(255,255,255,.9); text-align: right; vertical-align: middle;">
                        <div>Dibuat pada</div>
                        <div><strong style="color: #fff;">{{ $generatedAt }} WIB</strong></div>
                    </td>
                </tr>
            </table>
        </div>

        <table class="summary">
            <tr>
                <td class="accent-blue">
                    <div class="label">Total Layanan</div>
                    <div class="value">{{ $totalLayanan }}</div>
                </td>
                <td class="accent-teal">
                    <div class="label">Total Mahasiswa</div>
                    <div class="value">{{ $totalMahasiswa }}</div>
                </td>
                <td class="accent-amber">
                    <div class="label">Layanan Terbanyak</div>
                    <div class="value" style="font-size: 13px;">{{ $topService->nama_layanan ?? '-' }}</div>
                    <div class="subtitle">{{ $topService->mahasiswa_count ?? 0 }} mahasiswa</div>
                </td>
            </tr>
        </table>

        {{-- Tabel ini menampilkan distribusi layanan dalam format PDF yang lebih padat --}}
        <div class="section">
            <h2>Distribusi Layanan</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama Layanan</th>
                        <th class="num" style="width: 120px;">Jumlah Mahasiswa</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($services as $service)
                        <tr>
                            <td>{{ $service->nama_layanan }}</td>
                            <td class="num">{{ $service->mahasiswa_count }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="muted">Belum ada data layanan pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Detail sesi buka/tutup per baris dipertahankan agar audit trail tetap jelas --}}
        <div class="section">
            <h2>Detail Sesi Buka/Tutup per Baris</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama Dosen</th>
                        <th>Sesi Buka</th>
                        <th>Sesi Tutup</th>
                        <th style="width: 90px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($roomMonthlySessionRows ?? [] as $item)
                        <tr>
                            <td>{{ $item->dosen_name }}</td>
                            <td>{{ $item->open_label ?? '-' }}</td>
                            <td>
                                @if (!empty($item->close_label) && $item->close_label !== '-')
                                    <span class="badge badge-rose">{{ $item->close_label }}</span>
                                @else
                                    <span class="muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if (($item->status_label ?? '') === 'Tutup')
                                    <span class="badge badge-rose">Tutup</span>
                                @else
                                    <span class="badge badge-green">Masih Buka</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="muted">Belum ada data sesi buka/tutup pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
