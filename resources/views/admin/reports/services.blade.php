@extends('layouts.app')

@section('content')
<div class="space-y-6">
    {{-- Header report yang mengarahkan admin untuk memfilter bulan, pindah periode, atau mengekspor data --}}
    <div class="rounded-2xl bg-gradient-to-r from-blue-700 via-cyan-700 to-teal-700 p-6 text-white shadow-lg">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h1 class="text-2xl font-bold">Statistik Layanan</h1>
                <p class="mt-1 text-sm text-blue-100">
                    Laporan bulanan: {{ $periodLabel ?? '-' }} (WIB). Distribusi layanan berdasarkan jumlah mahasiswa.
                </p>
            </div>

            {{-- Filter periode bulanan yang mengarah ke laporan, PDF, dan Excel untuk periode yang sama --}}
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

    {{-- Ringkasan utama untuk memberi konteks cepat sebelum admin membaca tabel detail --}}
    @php
        $totalLayanan = $services->count();
        $totalMahasiswa = $services->sum('mahasiswa_count');
        $topService = $services->sortByDesc('mahasiswa_count')->first();
    @endphp

    {{-- Kartu ringkasan layanan dan sesi ruang yang tersimpan untuk periode terpilih --}}
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

    {{-- Kartu ringkasan tambahan yang menunjukkan aktivitas ruang per dosen --}}
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

    {{-- Grafik distribusi layanan yang dibaca dari data hasil olahan controller --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="mb-4 flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-800">Grafik Layanan</h2>
                <p class="mt-1 text-sm text-slate-500">Urutan dibuat dari layanan paling ramai agar pola pemakaian cepat terbaca.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <span class="inline-flex items-center rounded-full bg-sky-50 px-3 py-1 text-xs font-semibold text-sky-700">
                    <i class="fa-solid fa-chart-column mr-1.5"></i> {{ $totalLayanan }} layanan
                </span>
                <span class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                    <i class="fa-solid fa-users mr-1.5"></i> {{ $totalMahasiswa }} mahasiswa
                </span>
            </div>
        </div>

        <div class="relative overflow-hidden rounded-2xl border border-slate-100 bg-gradient-to-br from-slate-50 via-white to-cyan-50 p-4">
            <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(14,165,233,.12),transparent_32%),radial-gradient(circle_at_bottom_left,rgba(59,130,246,.10),transparent_30%)]"></div>

            @if (($chartSeries ?? collect())->count() > 0)
                <div class="relative min-h-[360px]">
                    <div id="serviceChart"></div>
                </div>
            @else
                <div class="relative flex h-[240px] flex-col items-center justify-center rounded-xl border border-dashed border-slate-200 bg-white/70 text-center">
                    <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-sky-50 text-sky-600">
                        <i class="fa-solid fa-chart-simple text-xl"></i>
                    </div>
                    <p class="text-sm font-semibold text-slate-700">Belum ada data layanan pada periode ini.</p>
                    <p class="mt-1 text-sm text-slate-500">Begitu data masuk, grafik akan tampil otomatis di sini.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Tabel layanan yang mengarahkan admin ke rincian jumlah mahasiswa per layanan --}}
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

    {{-- Tabel sesi buka/tutup yang membantu admin menelusuri aktivitas ruang per dosen --}}
    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
        <div class="mb-4 flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">Laporan Bulanan Buka/Tutup Ruangan per Sesi</h2>
                <p class="mt-1 text-sm text-gray-500">Setiap sesi buka ditampilkan dalam satu baris agar riwayat dosen tidak menumpuk.</p>
                <p class="mt-2 inline-flex items-center rounded-full bg-amber-50 px-3 py-1 text-xs font-medium text-amber-700">
                    <i class="fa-solid fa-triangle-exclamation mr-1.5"></i>
                    Sesi tanpa jam tutup akan dianggap tutup otomatis saat ganti hari.
                </p>
            </div>

            <div class="w-full lg:w-96">
                <label for="searchDosen" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">
                    Cari nama dosen
                </label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-gray-400">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input
                        type="text"
                        id="searchDosen"
                        class="w-full rounded-lg border border-slate-200 bg-white py-2.5 pl-10 pr-3 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                        placeholder="Ketik nama dosen..."
                    >
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm" id="roomSessionTable">
                <thead>
                    <tr class="border-b bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <th class="px-4 py-3">Nama Dosen</th>
                        <th class="px-4 py-3">Sesi Buka</th>
                        <th class="px-4 py-3">Sesi Tutup</th>
                        <th class="px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($roomMonthlySessionRows ?? [] as $item)
                        <tr class="hover:bg-blue-50/40 transition" data-dosen-name="{{ strtolower($item->dosen_name ?? '') }}">
                            <td class="px-4 py-3 font-medium text-gray-700">{{ $item->dosen_name }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full bg-sky-50 px-2.5 py-1 text-xs font-medium text-sky-700">
                                    {{ $item->open_label ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if (!empty($item->close_label) && $item->close_label !== '-')
                                    <span class="inline-flex items-center rounded-full bg-rose-50 px-2.5 py-1 text-xs font-medium text-rose-700">
                                        {{ $item->close_label }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if (($item->status_label ?? '') === 'Tutup')
                                    <span class="inline-flex items-center rounded-full bg-rose-50 px-2.5 py-1 text-xs font-medium text-rose-700">Tutup</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">Masih Buka</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-gray-500">Belum ada data buka/tutup ruangan pada periode ini.</td>
                        </tr>
                    @endforelse
                    <tr id="roomSessionEmptySearch" class="hidden">
                        <td colspan="4" class="px-4 py-6 text-center text-gray-500">Tidak ada dosen yang cocok dengan pencarian.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
    // Data grafik diambil dari controller lalu dirender sebagai radial bar agar tampil lebih modern.
    const labels = @json($chartLabels ?? $labels ?? []);
    const series = @json($chartSeries ?? []);
    const counts = @json($chartData ?? $data ?? []);
    const totalMahasiswa = @json($chartTotal ?? ($chartData ?? collect())->sum());
    const chartEl = document.getElementById('serviceChart');

    if (chartEl && series.length) {
        const colors = ['#0ea5e9', '#2563eb', '#14b8a6', '#8b5cf6', '#f59e0b', '#334155'];

        const chart = new ApexCharts(chartEl, {
            chart: {
                type: 'radialBar',
                height: 380,
                toolbar: { show: false },
                sparkline: { enabled: false },
                fontFamily: '"Plus Jakarta Sans", sans-serif',
            },
            series: series,
            labels: labels,
            colors: colors.slice(0, series.length),
            plotOptions: {
                radialBar: {
                    startAngle: -135,
                    endAngle: 135,
                    offsetY: 0,
                    hollow: {
                        margin: 10,
                        size: '28%',
                        background: 'transparent',
                    },
                    track: {
                        background: '#e2e8f0',
                        strokeWidth: '90%',
                        margin: 8,
                    },
                    dataLabels: {
                        name: {
                            show: true,
                            fontSize: '14px',
                            fontWeight: 700,
                            color: '#334155',
                        },
                        value: {
                            show: true,
                            fontSize: '22px',
                            fontWeight: 800,
                            color: '#0f172a',
                            formatter: function (val) {
                                return `${val}%`;
                            }
                        },
                        total: {
                            show: true,
                            label: 'Total Mahasiswa',
                            fontSize: '13px',
                            fontWeight: 700,
                            color: '#64748b',
                            formatter: function () {
                                return totalMahasiswa;
                            }
                        }
                    }
                }
            },
            stroke: {
                lineCap: 'round',
            },
            legend: {
                show: true,
                position: 'bottom',
                fontSize: '13px',
                fontWeight: 600,
                markers: {
                    width: 10,
                    height: 10,
                    radius: 999
                },
                itemMargin: {
                    horizontal: 10,
                    vertical: 6
                },
                labels: {
                    colors: '#334155'
                },
                formatter: function (seriesName, opts) {
                    const count = counts[opts.seriesIndex] ?? 0;
                    const percent = series[opts.seriesIndex] ?? 0;
                    return `${seriesName} (${count} | ${percent}%)`;
                }
            },
            tooltip: {
                enabled: true,
                y: {
                    formatter: function (value, opts) {
                        const count = counts[opts.seriesIndex] ?? 0;
                        return `${count} mahasiswa (${value}%)`;
                    }
                }
            },
            states: {
                hover: {
                    filter: {
                        type: 'lighten',
                        value: 0.1
                    }
                }
            }
        });

        chart.render();
    }

    const searchInput = document.getElementById('searchDosen');
    const sessionRows = Array.from(document.querySelectorAll('#roomSessionTable tbody tr[data-dosen-name]'));
    const emptySearchRow = document.getElementById('roomSessionEmptySearch');

    if (searchInput && sessionRows.length) {
        const filterRows = () => {
            const keyword = searchInput.value.trim().toLowerCase();
            let visibleCount = 0;

            sessionRows.forEach((row) => {
                const match = row.dataset.dosenName.includes(keyword);
                row.classList.toggle('hidden', !match);
                if (match) visibleCount += 1;
            });

            if (emptySearchRow) {
                emptySearchRow.classList.toggle('hidden', visibleCount > 0 || keyword.length === 0);
            }
        };

        searchInput.addEventListener('input', filterRows);
    }
</script>
@endsection
