<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MonthlyServiceReportExport implements FromArray, ShouldAutoSize
{
    public function __construct(private array $report)
    {
    }

    public function array(): array
    {
        $generatedAt = Carbon::now('Asia/Jakarta')->locale('id')->translatedFormat('d F Y H:i');
        $services = collect($this->report['services'] ?? []);
        $roomMonthlyStats = collect($this->report['roomMonthlyStats'] ?? []);
        $roomSummary = $this->report['roomSummary'] ?? [];
        $topService = $services->sortByDesc('mahasiswa_count')->first();

        $rows = [
            ['Laporan Bulanan Statistik Layanan'],
            ['Periode', $this->report['periodLabel'] ?? '-', 'Dibuat pada', $generatedAt . ' WIB'],
            [''],
            ['Ringkasan'],
            ['Total Layanan', $services->count(), 'Total Mahasiswa', $services->sum('mahasiswa_count')],
            ['Layanan Terbanyak', $topService->nama_layanan ?? '-', (int) ($topService->mahasiswa_count ?? 0), 'Dosen Terlibat', (int) ($roomSummary['total_dosen'] ?? 0)],
            [''],
            ['Distribusi Layanan'],
            ['Nama Layanan', 'Jumlah Mahasiswa'],
        ];

        foreach ($services as $service) {
            $rows[] = [
                $service->nama_layanan ?? '-',
                (int) ($service->mahasiswa_count ?? 0),
            ];
        }

        $rows[] = [''];
        $rows[] = ['Ringkasan Ruang Antrean'];
        $rows[] = ['Nama Dosen', 'Total Sesi', 'Total Buka', 'Jadwal Buka', 'Jadwal Tutup'];

        foreach ($roomMonthlyStats as $item) {
            $openSchedules = collect($item->open_schedules ?? [])->implode(' | ');
            $closeSchedules = collect($item->close_schedules ?? [])->implode(' | ');

            $rows[] = [
                $item->dosen_name ?? '-',
                (int) ($item->total_sesi ?? 0),
                (int) ($item->total_buka ?? 0),
                $openSchedules !== '' ? $openSchedules : 'Tidak ada data buka.',
                $closeSchedules !== '' ? $closeSchedules : 'Tidak ada data tutup.',
            ];
        }

        return $rows;
    }
}
