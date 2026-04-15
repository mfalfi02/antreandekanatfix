@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="rounded-2xl bg-gradient-to-r from-blue-700 via-cyan-700 to-teal-700 p-6 text-white shadow-lg">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h1 class="text-2xl font-bold">Statistik Layanan</h1>
                <p class="mt-1 text-sm text-blue-100">
                    Laporan bulanan: {{ $periodLabel ?? '-' }} (WIB). Distribusi layanan berdasarkan jumlah mahasiswa.
                </p>
            </div>

            <form method="GET" action="{{ route('reports.services') }}"
                class="flex flex-col gap-2 rounded-xl bg-white/10 p-3 backdrop-blur sm:flex-row sm:items-end">
                <div>
                    <label for="month" class="block text-xs font-semibold uppercase tracking-wide text-blue-100">Bulan</label>
                    <select name="month" id="month"
                        class="mt-1 rounded-lg border border-white/20 bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-cyan-300">
                        @foreach ([
                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                        ] as $value => $label)
                            <option value="{{ $value }}" {{ (int) $selectedMonth === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="year" class="block text-xs font-semibold uppercase tracking-wide text-blue-100">Tahun</label>
                    <select name="year" id="year"
                        class="mt-1 rounded-lg border border-white/20 bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-cyan-300">
                        @foreach ($availableYears ?? [] as $year)
                            <option value="{{ $year }}" {{ (int) $selectedYear === (int) $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit"
                    class="inline-flex items-center justify-center rounded-lg border border-cyan-200 bg-white px-4 py-2 text-sm font-semibold whitespace-nowrap text-slate-800 shadow-sm transition hover:bg-cyan-50 hover:border-cyan-300">
                    Tampilkan
                </button>
            </form>
        </div>
    </div>

    <div class="flex flex-wrap gap-2">
        <a href="{{ route('reports.services', ['month' => $previousPeriod->month, 'year' => $previousPeriod->year]) }}"
            class="rounded-lg border border-sky-200 bg-white px-4 py-2 text-sm font-semibold text-sky-700 shadow-sm transition hover:bg-sky-50">
            <i class="fa-solid fa-chevron-left mr-1"></i> Bulan Sebelumnya
        </a>
        <a href="{{ route('reports.services', ['month' => $nextPeriod->month, 'year' => $nextPeriod->year]) }}"
            class="rounded-lg border border-sky-200 bg-white px-4 py-2 text-sm font-semibold text-sky-700 shadow-sm transition hover:bg-sky-50">
            Bulan Berikutnya <i class="fa-solid fa-chevron-right ml-1"></i>
        </a>
        <a href="{{ route('reports.services.pdf', ['month' => $selectedMonth, 'year' => $selectedYear]) }}"
            class="rounded-lg border border-rose-200 bg-white px-4 py-2 text-sm font-semibold text-rose-700 shadow-sm transition hover:bg-rose-50">
            <i class="fa-solid fa-file-pdf mr-1"></i> PDF
        </a>
        <a href="{{ route('reports.services.excel', ['month' => $selectedMonth, 'year' => $selectedYear]) }}"
            class="rounded-lg border border-emerald-200 bg-white px-4 py-2 text-sm font-semibold text-emerald-700 shadow-sm transition hover:bg-emerald-50">
            <i class="fa-solid fa-file-excel mr-1"></i> Excel
        </a>
    </div>

    @php
        $totalLayanan = $services->count();
        $totalMahasiswa = $services->sum('mahasiswa_count');
        $topService = $services->sortByDesc('mahasiswa_count')->first();
    @endphp

    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="rounded-xl border border-blue-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Total Layanan</p>
            <p class="mt-2 text-3xl font-bold text-blue-700">{{ $totalLayanan }}</p>
        </div>
        <div class="rounded-xl border border-teal-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Total Mahasiswa</p>
            <p class="mt-2 text-3xl font-bold text-teal-700">{{ $totalMahasiswa }}</p>
        </div>
        <div class="rounded-xl border border-amber-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Layanan Terbanyak</p>
            <p class="mt-2 text-lg font-semibold text-amber-700">{{ $topService->nama_layanan ?? '-' }}</p>
            <p class="text-sm text-gray-500">{{ $topService->mahasiswa_count ?? 0 }} mahasiswa</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
        <div class="rounded-xl border border-sky-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Total Sesi Ruangan</p>
            <p class="mt-2 text-3xl font-bold text-sky-700">{{ $roomSummary['total_sesi'] ?? 0 }}</p>
        </div>
        <div class="rounded-xl border border-emerald-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Total Buka Ruangan</p>
            <p class="mt-2 text-3xl font-bold text-emerald-700">{{ $roomSummary['total_buka'] ?? 0 }}</p>
        </div>
        <div class="rounded-xl border border-rose-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Total Tutup Ruangan</p>
            <p class="mt-2 text-3xl font-bold text-rose-700">{{ $roomSummary['total_tutup'] ?? 0 }}</p>
        </div>
        <div class="rounded-xl border border-indigo-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Dosen Terlibat</p>
            <p class="mt-2 text-3xl font-bold text-indigo-700">{{ $roomSummary['total_dosen'] ?? 0 }}</p>
        </div>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
        <h2 class="mb-4 text-lg font-semibold text-gray-800">Grafik Layanan</h2>
        <canvas id="serviceChart" height="110"></canvas>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
        <h2 class="mb-4 text-lg font-semibold text-gray-800">Rincian Per Layanan</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <th class="px-4 py-3">Nama Layanan</th>
                        <th class="px-4 py-3 text-right">Jumlah Mahasiswa</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($services as $service)
                        <tr class="hover:bg-blue-50/40 transition">
                            <td class="px-4 py-3 font-medium text-gray-700">{{ $service->nama_layanan }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-blue-700">{{ $service->mahasiswa_count }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-4 py-6 text-center text-gray-500">Belum ada data layanan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
        <h2 class="mb-4 text-lg font-semibold text-gray-800">Laporan Bulanan Buka/Tutup Ruangan per Dosen</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <th class="px-4 py-3">Nama Dosen</th>
                        <th class="px-4 py-3 text-right">Total Sesi</th>
                        <th class="px-4 py-3 text-right">Total Buka</th>
                        <th class="px-4 py-3">Tanggal & Jam Buka</th>
                        <th class="px-4 py-3">Tanggal & Jam Tutup</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($roomMonthlyStats as $item)
                        <tr class="hover:bg-blue-50/40 transition">
                            <td class="px-4 py-3 font-medium text-gray-700">{{ $item->dosen_name }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-slate-700">{{ $item->total_sesi }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-emerald-700">{{ $item->total_buka }}</td>
                            <td class="px-4 py-3">
                                @if (!empty($item->open_schedules) && count($item->open_schedules) > 0)
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach ($item->open_schedules as $schedule)
                                            <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700">
                                                {{ $schedule }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">Tidak ada data buka.</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if (!empty($item->close_schedules) && count($item->close_schedules) > 0)
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach ($item->close_schedules as $schedule)
                                            <span class="inline-flex items-center rounded-full bg-rose-50 px-2.5 py-1 text-xs font-medium text-rose-700">
                                                {{ $schedule }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">Tidak ada data tutup.</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-gray-500">Belum ada data buka/tutup ruangan pada periode ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Pastikan Chart.js ter-include (boleh ditempatkan di layout/app.blade.php) --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // fallback jika variabel tidak dikirim (agar tidak error)
    const labels = @json($labels ?? []);
    const dataset = @json($data ?? []);

    const ctx = document.getElementById('serviceChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Jumlah Mahasiswa per Layanan',
                data: dataset,
                backgroundColor: 'rgba(14, 116, 144, 0.75)',
                borderColor: 'rgba(14, 116, 144, 1)',
                borderWidth: 1,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    labels: {
                        color: '#334155'
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { color: '#64748b' },
                    grid: { color: 'rgba(148, 163, 184, 0.25)' }
                },
                x: {
                    ticks: { color: '#64748b' },
                    grid: { display: false }
                }
            }
        }
    });
</script>
@endsection
