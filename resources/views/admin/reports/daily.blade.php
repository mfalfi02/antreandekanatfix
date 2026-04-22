@extends('layouts.app')

@section('content')
<div class="space-y-6">
    @php
        // Normalisasi tanggal ke zona waktu server operasional agar filter harian selalu konsisten.
        $reportDate = \Carbon\Carbon::parse($date)->setTimezone('Asia/Jakarta')->locale('id');
    @endphp

    <div class="rounded-2xl bg-gradient-to-r from-slate-800 via-slate-700 to-slate-600 p-6 text-white shadow-lg">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <h2 class="text-2xl font-bold">{{ $title }}</h2>
                <p class="mt-1 text-sm text-slate-200">Ringkasan operasional antrean tanggal {{ $reportDate->format('d-m-Y') }} WIB</p>
                <p class="mt-1 text-xs text-slate-300">{{ $reportDate->translatedFormat('l, d F Y') }}</p>
            </div>

            <form method="GET" action="{{ route('reports.daily') }}" class="flex flex-col sm:flex-row sm:items-end gap-3">
                <label class="block">
                    <span class="mb-1.5 block text-xs font-medium text-slate-200">Pilih Tanggal</span>
                    <input
                        type="date"
                        name="date"
                        value="{{ $reportDate->format('Y-m-d') }}"
                        class="rounded-xl border border-slate-500 bg-slate-800/70 px-3 py-2 text-sm text-white shadow-sm outline-none transition focus:border-sky-300 focus:ring-2 focus:ring-sky-200/30"
                    >
                </label>
                <button type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-4 py-2 text-sm font-semibold text-slate-800 shadow-sm transition hover:bg-slate-100">
                    <i class="fa-solid fa-filter"></i>
                    Tampilkan
                </button>
            </form>
        </div>

        <div class="mt-4 flex flex-wrap gap-2">
            <a href="{{ route('reports.daily', ['date' => $previousDate]) }}"
                class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-white/15">
                <i class="fa-solid fa-chevron-left"></i>
                Hari Sebelumnya
            </a>
            <a href="{{ route('reports.daily', ['date' => $nextDate]) }}"
                class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-white/15">
                Hari Berikutnya
                <i class="fa-solid fa-chevron-right"></i>
            </a>
        </div>
    </div>

    @php
        // Hitung metrik utama sekali di server supaya kartu ringkasan tidak perlu proses tambahan di browser.
        $totalQueues = $queues->count();
        $totalRoomSessions = $roomSessions->count();
        $activeRooms = $roomSessions->whereIn('status_ruang', ['open', 'occupied'])->count();
    @endphp

    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="rounded-xl border border-sky-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Antrean Masuk</p>
            <p class="mt-2 text-3xl font-bold text-sky-700">{{ $totalQueues }}</p>
        </div>
        <div class="rounded-xl border border-violet-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Sesi Ruang Antrean</p>
            <p class="mt-2 text-3xl font-bold text-violet-700">{{ $totalRoomSessions }}</p>
        </div>
        <div class="rounded-xl border border-emerald-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Ruang Aktif</p>
            <p class="mt-2 text-3xl font-bold text-emerald-700">{{ $activeRooms }}</p>
        </div>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
        <h3 class="mb-4 text-lg font-semibold text-gray-800">Antrean Masuk</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Layanan</th>
                        <th class="px-4 py-3">Dilayani Oleh</th>
                        <th class="px-4 py-3">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($queues as $q)
                        <tr class="hover:bg-sky-50/40 transition">
                            <td class="px-4 py-3 text-gray-500">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3 font-medium text-gray-700">{{ $q->user->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $q->service->nama_layanan ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $q->dosen_pelayan ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $q->created_at->format('H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-gray-500">Belum ada antrean masuk pada tanggal ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
        <h3 class="mb-4 text-lg font-semibold text-gray-800">Sinkronisasi Ruang Antrean</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">Dosen</th>
                        <th class="px-4 py-3">Jenis Layanan</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Expected Buka</th>
                        <th class="px-4 py-3">Perkiraan Tutup</th>
                        <th class="px-4 py-3">Jam Buka</th>
                        <th class="px-4 py-3">Jam Tutup</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($roomSessions as $room)
                        <tr class="hover:bg-violet-50/40 transition">
                            <td class="px-4 py-3 text-gray-500">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3 font-medium text-gray-700">{{ $room->dosen->name ?? $room->kode_dosen }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $room->service->nama_layanan ?? 'Semua Jenis Layanan' }}</td>
                            <td class="px-4 py-3">
                                @php
                                    // Mapping status dipakai untuk memberi warna label yang langsung terbaca.
                                    $statusClass = match ($room->status_ruang) {
                                        'open' => 'bg-emerald-100 text-emerald-700',
                                        'occupied' => 'bg-amber-100 text-amber-700',
                                        default => 'bg-rose-100 text-rose-700',
                                    };
                                @endphp
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">
                                    {{ strtoupper($room->status_ruang) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $room->expected_jam_buka_ruang_antri ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $room->expected_jam_tutup_ruang_antri ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $room->jam_buka_ruang_antri ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $room->jam_tutup_ruang_antri ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-6 text-center text-gray-500">Belum ada data ruang antrean pada tanggal ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
