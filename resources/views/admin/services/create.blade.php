@extends('layouts.app')

@section('title', 'Tambah Layanan')

@section('content')
<div class="max-w-2xl mx-auto bg-white shadow-lg rounded-xl p-6 mt-10">
    <h2 class="text-xl font-bold text-gray-800 mb-4">Tambah Layanan Baru</h2>

    {{-- Pesan error --}}
    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('services.store') }}" method="POST">
        @csrf

        {{-- Nama Layanan --}}
        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700">Nama Layanan</label>
            <input type="text" name="nama_layanan" id="nama_layanan"
                class="w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                value="{{ old('name') }}" required>
        </div>

        {{-- Deskripsi --}}
        <div class="mb-4">
            <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
            <textarea name="deskripsi" id="deskripsi" rows="3"
                class="w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                required>{{ old('description') }}</textarea>
        </div>

        {{-- Estimasi Waktu --}}
        <div class="mb-4">
            <label for="est_time" class="block text-sm font-medium text-gray-700">Estimasi Waktu (menit)</label>
            <input type="number" name="est_time" id="est_time" min="1"
                class="w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                value="{{ old('est_time') }}" required>
        </div>

        {{-- Status --}}
        <div class="mb-4">
            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
            <select name="status" id="status"
                class="w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="aktif" {{ old('status')=='aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="nonaktif" {{ old('status')=='nonaktif' ? 'selected' : '' }}>Nonaktif</option>
            </select>
        </div>

        {{-- Tombol --}}
        <div class="flex justify-end space-x-2">
            <a href="{{ route('services.index') }}"
                class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                Batal
            </a>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                Simpan
            </button>
        </div>
    </form>
</div>
@endsection
