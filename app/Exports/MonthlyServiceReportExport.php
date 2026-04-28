<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;

class MonthlyServiceReportExport implements FromArray, ShouldAutoSize, WithEvents, WithTitle
{
    /**
     * Menyimpan payload laporan yang nanti diubah menjadi baris Excel.
     */
    public function __construct(private array $report)
    {
    }

    /**
     * Menyimpan posisi baris penting agar styling Excel tetap akurat.
     *
     * @var array<string, int>
     */
    private array $rowMap = [];

    /**
     * Memberi nama sheet agar mudah dikenali saat file dibuka.
     */
    public function title(): string
    {
        return 'Statistik Layanan';
    }

    /**
     * Mengubah ringkasan laporan menjadi array supaya hasil export masuk ke file Excel.
     */
    public function array(): array
    {
        $generatedAt = Carbon::now('Asia/Jakarta')->locale('id')->translatedFormat('d F Y H:i');
        $services = collect($this->report['services'] ?? []);
        $topService = $services->sortByDesc('mahasiswa_count')->first();
        $roomMonthlySessionRows = collect($this->report['roomMonthlySessionRows'] ?? []);
        $rows = [];
        $push = function (array $row) use (&$rows): void {
            $rows[] = $row;
        };

        $push(['Laporan Bulanan Statistik Layanan']);
        $push(['Periode', $this->report['periodLabel'] ?? '-', 'Dibuat pada', $generatedAt . ' WIB']);
        $push(['']);
        $this->rowMap['summary_title'] = count($rows) + 1;
        $push(['Ringkasan']);
        $push(['Total Layanan', $services->count(), 'Total Mahasiswa', $services->sum('mahasiswa_count')]);
        $push(['Layanan Terbanyak', $topService->nama_layanan ?? '-', (int) ($topService->mahasiswa_count ?? 0)]);
        $push(['']);
        $this->rowMap['service_title'] = count($rows) + 1;
        $push(['Distribusi Layanan']);
        $this->rowMap['service_header'] = count($rows) + 1;
        $push(['Nama Layanan', 'Jumlah Mahasiswa']);

        foreach ($services as $service) {
            $push([
                $service->nama_layanan ?? '-',
                (int) ($service->mahasiswa_count ?? 0),
            ]);
        }

        $push(['']);
        $this->rowMap['session_title'] = count($rows) + 1;
        $push(['Detail Sesi Buka/Tutup per Baris']);
        $this->rowMap['session_header'] = count($rows) + 1;
        $push(['Nama Dosen', 'Sesi Buka', 'Sesi Tutup', 'Status']);

        foreach ($roomMonthlySessionRows as $item) {
            $push([
                $item->dosen_name ?? '-',
                $item->open_label ?? '-',
                $item->close_label ?? '-',
                $item->status_label ?? '-',
            ]);
        }

        return $rows;
    }

    /**
     * Memoles sheet supaya file Excel lebih rapi tanpa mengubah strukturnya.
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastColumn = 'E';
                $sheet->mergeCells("A1:{$lastColumn}1");
                $sheet->getStyle("A1:{$lastColumn}1")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '1D4ED8'],
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $sheet->getStyle('A2:E2')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'EFF6FF'],
                    ],
                ]);

                foreach (['summary_title', 'service_title', 'session_title'] as $key) {
                    if (!isset($this->rowMap[$key])) {
                        continue;
                    }

                    $row = $this->rowMap[$key];
                    $sheet->getStyle("A{$row}:E{$row}")->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'color' => ['rgb' => '1E3A8A'],
                        ],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'DBEAFE'],
                        ],
                    ]);
                }

                foreach (['service_header', 'session_header'] as $key) {
                    if (!isset($this->rowMap[$key])) {
                        continue;
                    }

                    $row = $this->rowMap[$key];
                    $sheet->getStyle("A{$row}:E{$row}")->applyFromArray([
                        'font' => ['bold' => true],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'F8FAFC'],
                        ],
                    ]);
                }

                $sheet->getStyle('A1:E1000')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['rgb' => 'DBE4F0'],
                        ],
                    ],
                ]);

                $sheet->freezePane('A3');
            },
        ];
    }
}
