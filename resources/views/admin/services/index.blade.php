@extends('layouts.app')

@section('title', 'Manajemen Layanan')

@section('content')
<div class="max-w-6xl mx-auto py-10 px-6">
    {{-- Header halaman ini mengarahkan admin ke daftar layanan atau form tambah layanan --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Daftar Layanan</h1>
            <p class="text-sm text-gray-500">Kelola layanan antrean dan estimasi waktunya.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('adm') }}"
                class="inline-flex items-center px-4 py-2 rounded-lg bg-gray-500 text-white hover:bg-gray-600 transition">
                <i class="fa fa-arrow-left mr-2"></i> Kembali
            </a>
            <a href="{{ route('services.create') }}"
                class="inline-flex items-center px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">
                <i class="fa fa-plus mr-2"></i> Tambah Layanan
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 p-3 rounded-lg bg-green-100 text-green-800 border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    {{-- Tabel di bawah ini menampilkan master layanan yang dipakai di queue dan laporan --}}
    <div class="overflow-x-auto bg-white rounded-xl border border-blue-100 shadow-md p-4">
        <table class="w-full table-auto border-collapse">
            <thead>
                <tr class="bg-gray-100/80 text-left">
                    <th class="px-4 py-2">Nama Layanan</th>
                    <th class="px-4 py-2">Deskripsi</th>
                    <th class="px-4 py-2">Estimasi</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($services as $service)
                    @php
                        // Status aktif dipetakan ke badge agar kondisi layanan cepat terbaca.
                        $isAktif = $service->status === 'aktif';
                    @endphp
                    <tr class="hover:bg-blue-50/40">
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $service->nama_layanan }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $service->deskripsi ?: '-' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full bg-amber-100 text-amber-700">
                                <i class="fa-regular fa-clock"></i> {{ $service->est }} menit
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full {{ $isAktif ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                <i class="fa-solid {{ $isAktif ? 'fa-circle-check' : 'fa-circle-xmark' }}"></i>
                                {{ $isAktif ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            {{-- Aksi edit dan hapus mengarah ke form update dan destroy service --}}
                            <div class="inline-flex items-center gap-2">
                                <a href="{{ route('services.edit', $service->id) }}"
                                    class="h-9 w-9 rounded-lg border border-blue-200 text-blue-600 hover:bg-blue-50 transition inline-flex items-center justify-center"
                                    title="Edit {{ $service->nama_layanan }}">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <form action="{{ route('services.destroy', $service->id) }}" method="POST"
                                    onsubmit="return confirm('Yakin hapus layanan {{ $service->nama_layanan }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="h-9 w-9 rounded-lg border border-rose-200 text-rose-600 hover:bg-rose-50 transition inline-flex items-center justify-center"
                                        title="Hapus {{ $service->nama_layanan }}">
                                        <i class="fa-solid fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                            <div class="inline-flex flex-col items-center gap-2">
                                <i class="fa-regular fa-folder-open text-2xl text-blue-300"></i>
                                <span>Belum ada layanan.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
