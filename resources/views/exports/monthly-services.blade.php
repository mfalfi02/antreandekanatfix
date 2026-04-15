<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Bulanan Layanan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #1f2937;
            font-size: 12px;
            line-height: 1.5;
        }
        h1, h2, h3, p {
            margin: 0;
        }
        .muted {
            color: #6b7280;
        }
        .header {
            margin-bottom: 18px;
        }
        .summary {
            width: 100%;
            border-collapse: collapse;
            margin: 14px 0 20px;
        }
        .summary td {
            border: 1px solid #dbe4f0;
            padding: 10px;
            vertical-align: top;
        }
        .summary .label {
            color: #6b7280;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .04em;
        }
        .summary .value {
            font-size: 18px;
            font-weight: bold;
            margin-top: 4px;
            color: #0f172a;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table th,
        .table td {
            border: 1px solid #dbe4f0;
            padding: 8px 10px;
            text-align: left;
            vertical-align: top;
        }
        .table th {
            background: #eff6ff;
            color: #1e3a8a;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .04em;
        }
        .table td.num,
        .table th.num {
            text-align: right;
        }
        .section {
            margin-bottom: 24px;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 999px;
            background: #eff6ff;
            color: #1d4ed8;
            font-size: 11px;
            margin: 0 4px 4px 0;
        }
        .small {
            font-size: 11px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    @php
        $generatedAt = \Carbon\Carbon::now('Asia/Jakarta')->locale('id')->translatedFormat('d F Y H:i');
        $totalLayanan = $services->count();
        $totalMahasiswa = $services->sum('mahasiswa_count');
        $topService = $services->sortByDesc('mahasiswa_count')->first();
    @endphp

    <div class="header">
        <h1>Laporan Bulanan Statistik Layanan</h1>
        <p class="muted">Periode {{ $periodLabel ?? '-' }}</p>
        <p class="small">Dibuat pada {{ $generatedAt }} WIB</p>
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
                <div class="value" style="font-size: 14px;">{{ $topService->nama_layanan ?? '-' }}</div>
                <div class="small">{{ $topService->mahasiswa_count ?? 0 }} mahasiswa</div>
            </td>
            <td>
                <div class="label">Dosen Terlibat</div>
                <div class="value">{{ $roomSummary['total_dosen'] ?? 0 }}</div>
            </td>
        </tr>
    </table>

    <div class="section">
        <h2>Distribusi Layanan</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Nama Layanan</th>
                    <th class="num">Jumlah Mahasiswa</th>
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
                        <td colspan="2">Belum ada data layanan pada periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

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
                    <th class="num">Total Sesi</th>
                    <th class="num">Total Buka</th>
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
                        <td colspan="5">Belum ada data buka/tutup ruangan pada periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>
