@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto py-10 px-6">
        <div class="bg-white shadow-md rounded-xl overflow-hidden">
            <!-- Header -->
            <div class="bg-blue-600 px-6 py-4">
                <h2 class="text-xl font-bold text-white flex items-center gap-2">
                    <i class="fa fa-edit"></i> Edit Service
                </h2>
            </div>

            <!-- Body -->
            <div class="p-6">

                <!-- Error alert -->
                @if ($errors->any())
                    <div class="mb-6 bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded-lg">
                        <strong class="font-semibold">Terjadi kesalahan!</strong>
                        <ul class="list-disc pl-5 mt-2 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('services.update', $service->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Nama Service -->
                    <div>
                        <label class="block font-medium text-gray-700">Nama Service <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="name"
                            class="mt-1 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 @error('name') border-red-500 @enderror"
                            value="{{ old('name', $service->name) }}" placeholder="Masukkan nama service">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Deskripsi -->
                    <div>
                        <label class="block font-medium text-gray-700">Deskripsi</label>
                        <textarea name="description" rows="3"
                            class="mt-1 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 @error('description') border-red-500 @enderror"
                            placeholder="Tuliskan deskripsi service (opsional)">{{ old('description', $service->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Estimasi Waktu -->
                    <div>
                        <label class="block font-medium text-gray-700">Estimasi Waktu <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="est_time"
                            class="mt-1 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 @error('est_time') border-red-500 @enderror"
                            value="{{ old('est_time', $service->est_time) }}" placeholder="Contoh: 30 menit">
                        @error('est_time')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tombol -->
                    <div class="flex justify-between items-center pt-4">
                        <a href="{{ route('services.index') }}"
                            class="inline-flex items-center px-4 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300 transition">
                            <i class="fa fa-arrow-left mr-2"></i> Kembali
                        </a>
                        <button type="submit"
                            class="inline-flex items-center px-5 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 transition">
                            <i class="fa fa-save mr-2"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
