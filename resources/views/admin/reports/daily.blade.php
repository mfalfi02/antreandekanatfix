@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="rounded-2xl bg-gradient-to-r from-slate-800 via-slate-700 to-slate-600 p-6 text-white shadow-lg">
        <h2 class="text-2xl font-bold">{{ $title }}</h2>
        <p class="mt-1 text-sm text-slate-200">Ringkasan operasional antrean tanggal {{ \Carbon\Carbon::parse($date)->format('d-m-Y') }} WIB</p>
    </div>

    @php
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
                            <td colspan="5" class="px-4 py-6 text-center text-gray-500">Belum ada antrean masuk hari ini.</td>
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
                        <th class="px-4 py-3">Expected Tutup</th>
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
                            <td colspan="8" class="px-4 py-6 text-center text-gray-500">Belum ada data ruang antrean hari ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
