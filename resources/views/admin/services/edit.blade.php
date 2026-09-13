@extends('layouts.app')

@section('title', 'Edit Layanan')

@section('content')
@php
    $backRoute = auth()->check() && auth()->user()->role === 'pejabat'
        ? route('dsn')
        : route('services.index');
@endphp
<div class="max-w-3xl mx-auto mt-10">
    {{-- Link kembali ini mengarah ke daftar layanan jika admin membatalkan perubahan --}}
    <a href="{{ $backRoute }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium transition-colors mb-6">
        <i class="fa-solid fa-arrow-left mr-2"></i> Kembali
    </a>

    {{-- Form edit ini mengirim data ke services.update untuk memperbarui master layanan --}}
    <div class="bg-white border border-blue-100 rounded-2xl shadow-md overflow-hidden">
        {{-- Header form menjelaskan bahwa halaman ini dipakai untuk mengubah layanan yang sudah ada --}}
        <div class="bg-gradient-to-r from-blue-600 to-indigo-500 p-6 text-center text-white">
            <h2 class="text-2xl font-bold">Edit Layanan</h2>
            <p class="text-sm text-blue-100 mt-1">Perbarui data layanan dan estimasi waktu</p>
        </div>

        {{-- Payload form ini diteruskan ke controller update lalu balik lagi ke daftar layanan --}}
        <form action="{{ route('services.update', $service->id) }}" method="POST" class="p-6 space-y-5">
            @csrf
            @method('PUT')

            {{-- Nama layanan menentukan label utama di dashboard dan laporan --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1 inline-flex items-center gap-2">
                    <i class="fa-solid fa-layer-group text-slate-400"></i>Nama Layanan
                </label>
                <input type="text" name="nama_layanan" value="{{ old('nama_layanan', $service->nama_layanan) }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                    required>
                <p class="text-xs text-gray-500 mt-1">Nama layanan yang tampil di dashboard.</p>
                @error('nama_layanan') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Deskripsi memberi konteks tambahan untuk pengguna dan staf --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1 inline-flex items-center gap-2">
                    <i class="fa-solid fa-align-left text-slate-400"></i>Deskripsi
                </label>
                <textarea name="deskripsi" rows="3"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">{{ old('deskripsi', $service->deskripsi) }}</textarea>
                @error('deskripsi') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Estimasi waktu dan status dipakai untuk pengaturan antrean dan statistik --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 inline-flex items-center gap-2">
                        <i class="fa-regular fa-clock text-slate-400"></i>Estimasi Waktu (menit)
                    </label>
                    <input type="number" name="est" min="1" value="{{ old('est', $service->est) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                        required>
                    @error('est') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 inline-flex items-center gap-2">
                        <i class="fa-solid fa-signal text-slate-400"></i>Status
                    </label>
                    <select name="status"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        <option value="aktif" {{ old('status', $service->status) === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ old('status', $service->status) === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    @error('status') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Tombol submit mengarah ke controller update, batal kembali ke daftar layanan --}}
            <div class="pt-2 flex justify-end gap-2">
                <a href="{{ $backRoute }}"
                    class="px-4 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300 transition">
                    Batal
                </a>
                <button type="submit"
                    class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition inline-flex items-center gap-2 shadow-sm">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
