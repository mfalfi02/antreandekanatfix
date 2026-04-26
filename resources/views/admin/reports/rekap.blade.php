@extends('layouts.app')

@section('content')
<div class="space-y-6">
    {{-- Ringkasan ini mengarahkan admin ke indikator operasional paling penting dalam satu layar --}}
    <div class="rounded-2xl bg-gradient-to-r from-emerald-700 via-teal-700 to-cyan-700 p-6 text-white shadow-lg">
        <h1 class="text-2xl font-bold">Rekap Laporan</h1>
        <p class="mt-1 text-sm text-emerald-100">Ringkasan operasional sistem antrean dekanat secara cepat dan terstruktur.</p>
    </div>

    {{-- Kartu statistik ini dipakai untuk membaca kondisi sistem tanpa membuka laporan detail --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
        
        <div class="rounded-xl border border-blue-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Total Pengguna</p>
            <p class="mt-2 text-3xl font-bold text-blue-700">{{ $totalUsers }}</p>
            <p class="text-sm text-gray-500">Akun aktif dalam sistem</p>
        </div>

        <div class="rounded-xl border border-indigo-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Kategori Layanan</p>
            <p class="mt-2 text-3xl font-bold text-indigo-700">{{ $serviceCategories }}</p>
            <p class="text-sm text-gray-500">Jenis layanan tersedia</p>
        </div>

        <div class="rounded-xl border border-green-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Selesai Hari Ini</p>
            <p class="mt-2 text-3xl font-bold text-green-700">{{ $completedToday }}</p>
            <p class="text-sm text-gray-500">Antrean selesai diproses</p>
        </div>

        <div class="rounded-xl border border-violet-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Aktivitas Ruang Hari Ini</p>
            <p class="mt-2 text-3xl font-bold text-violet-700">{{ $totalRuangAntriToday }}</p>
            <p class="text-sm text-gray-500">Total sesi ruang antrean</p>
        </div>

        <div class="rounded-xl border border-amber-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Ruang Antrean Aktif</p>
            <p class="mt-2 text-3xl font-bold text-amber-700">{{ $activeRuangAntriNow }}</p>
            <p class="text-sm text-gray-500">Sedang open/occupied saat ini</p>
        </div>
    </div>

    {{-- Highlight ini membantu admin melihat perbandingan sederhana antar data operasional --}}
    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
        <h2 class="text-lg font-semibold text-gray-800">Highlight Operasional</h2>
        <div class="mt-3 grid grid-cols-1 gap-3 text-sm text-gray-600 md:grid-cols-2">
            <div class="rounded-lg bg-gray-50 p-3">
                
                Rasio antrean selesai terhadap ruang aktif:
                <span class="font-semibold text-gray-800">
                    {{ $activeRuangAntriNow > 0 ? number_format($completedToday / $activeRuangAntriNow, 2) : '0.00' }}
                </span>
            </div>
            <div class="rounded-lg bg-gray-50 p-3">
                
                Rata-rata aktivitas ruang per layanan:
                <span class="font-semibold text-gray-800">
                    {{ $serviceCategories > 0 ? number_format($totalRuangAntriToday / $serviceCategories, 2) : '0.00' }}
                </span>
            </div>
        </div>
    </div>
</div>
@endsection
