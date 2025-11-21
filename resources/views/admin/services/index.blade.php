@extends('layouts.app')

@section('content')
    <div class="max-w-5xl mx-auto py-10 px-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Daftar Services</h1>
            <div class="flex gap-2">
                <!-- Tombol kembali -->
                <a href="{{ route('adm') }}"
                    class="inline-flex items-center px-4 py-2 rounded-lg bg-gray-500 text-white hover:bg-gray-600 transition">
                    <i class="fa fa-arrow-left mr-2"></i> Kembali
                </a>
                <!-- Tombol tambah -->
                <a href="{{ route('services.create') }}"
                    class="inline-flex items-center px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">
                    <i class="fa fa-plus mr-2"></i> Tambah Service
                </a>
            </div>
        </div>

        <!-- Alert sukses -->
        @if (session('success'))
            <div class="mb-6 p-4 rounded-lg bg-green-100 border border-green-300 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        <!-- Tabel -->
        <div class="overflow-x-auto bg-white shadow-md rounded-lg">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold">Nama</th>
                        <th class="px-6 py-3 text-left font-semibold">Deskripsi</th>
                        <th class="px-6 py-3 text-left font-semibold">Estimasi Waktu</th>
                        <th class="px-6 py-3 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($services as $service)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">{{ $service->nama_layanan }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $service->deskripsi }}</td>
                            <td class="px-6 py-4">{{ $service->est }} Menit</td>
                            <td class="px-6 py-4 flex justify-center gap-2">
                                <a href="{{ route('services.edit', $service->id) }}"
                                    class="px-3 py-1 rounded-md bg-yellow-500 text-white hover:bg-yellow-600 transition">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <form action="{{ route('services.destroy', $service->id) }}" method="POST"
                                    onsubmit="return confirm('Yakin hapus?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="px-3 py-1 rounded-md bg-red-600 text-white hover:bg-red-700 transition">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                Belum ada service.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
