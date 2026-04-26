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
        .summary {
            width: 100%;
            border-collapse: collapse;
            margin: 14px 0 18px;
        }
        .summary td {
            border: 1px solid #dbe4f0;
            padding: 9px 10px;
            vertical-align: top;
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
        .table td.num,
        .table th.num {
            text-align: right;
        }
        .muted {
            color: #64748b;
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
        <div class="header">
            <div class="header-cell" style="width: 72%;">
                <table style="border-collapse: collapse;">
                    <tr>
                        <td style="width: 78px; padding-right: 12px; vertical-align: middle;">
                            @if ($logoData)
                                <img src="{{ $logoData }}" alt="Logo Sistem" class="logo">
                            @endif
                        </td>
                        <td style="vertical-align: middle;">
                            <div class="title">Laporan Bulanan Statistik Layanan</div>
                            <div class="subtitle">Periode {{ $periodLabel ?? '-' }} (WIB)</div>
                            <div class="subtitle">Sistem Antrean Dekanat Universitas Widya Dharma</div>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="header-cell meta">
                <div>Dibuat pada</div>
                <div><strong>{{ $generatedAt }} WIB</strong></div>
            </div>
        </div>

        <table class="summary">
            <tr>
                <td>
                    <div class="label">Total Layanan</div>
                    <div class="value">{{ $totalLayanan }}</div>
                </td>
                <td>
                    <div class="label">Total Mahasiswa</div>
                    <div class="value">{{ $totalMahasiswa }}</div>
                </td>
                <td>
                    <div class="label">Layanan Terbanyak</div>
                    <div class="value" style="font-size: 13px;">{{ $topService->nama_layanan ?? '-' }}</div>
                    <div class="subtitle">{{ $topService->mahasiswa_count ?? 0 }} mahasiswa</div>
                </td>
                <td>
                    <div class="label">Dosen Terlibat</div>
                    <div class="value">{{ $roomSummary['total_dosen'] ?? 0 }}</div>
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

        {{-- Ringkasan ruang antrean di PDF membantu pembaca melihat pola operasional harian/bulanan --}}
        <div class="section">
            <h2>Ringkasan Ruang Antrean</h2>
            <table class="summary">
                <tr>
                    <td>
                        <div class="label">Total Sesi Ruangan</div>
                        <div class="value">{{ $roomSummary['total_sesi'] ?? 0 }}</div>
                    </td>
                    <td>
                        <div class="label">Total Buka Ruangan</div>
                        <div class="value">{{ $roomSummary['total_buka'] ?? 0 }}</div>
                    </td>
                    <td>
                        <div class="label">Total Tutup Ruangan</div>
                        <div class="value">{{ $roomSummary['total_tutup'] ?? 0 }}</div>
                    </td>
                </tr>
            </table>

            <table class="table">
                <thead>
                    <tr>
                        <th>Nama Dosen</th>
                        <th class="num" style="width: 80px;">Sesi</th>
                        <th class="num" style="width: 80px;">Buka</th>
                        <th>Jadwal Buka</th>
                        <th>Jadwal Tutup</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($roomMonthlyStats as $item)
                        <tr>
                            <td>{{ $item->dosen_name }}</td>
                            <td class="num">{{ $item->total_sesi }}</td>
                            <td class="num">{{ $item->total_buka }}</td>
                            <td>
                                @if (!empty($item->open_schedules) && count($item->open_schedules) > 0)
                                    @foreach ($item->open_schedules as $schedule)
                                        <span class="badge">{{ $schedule }}</span>
                                    @endforeach
                                @else
                                    <span class="muted">Tidak ada data buka.</span>
                                @endif
                            </td>
                            <td>
                                @if (!empty($item->close_schedules) && count($item->close_schedules) > 0)
                                    @foreach ($item->close_schedules as $schedule)
                                        <span class="badge">{{ $schedule }}</span>
                                    @endforeach
                                @else
                                    <span class="muted">Tidak ada data tutup.</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="muted">Belum ada data buka/tutup ruangan pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
