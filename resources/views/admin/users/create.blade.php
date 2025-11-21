@extends('layouts.app')

@section('title','Tambah Pengguna')

@section('content')
<div class="max-w-3xl mx-auto mt-10">

    {{-- Kembali --}}
    <a href="{{ route('users.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium transition-colors mb-6">
        <i class="fa-solid fa-arrow-left mr-2"></i> Kembali
    </a>

    {{-- Card Form --}}
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">

        {{-- Header --}}
        <div class="bg-gradient-to-r from-blue-600 to-indigo-500 p-6 text-center text-white">
            <h2 class="text-2xl font-semibold">Tambah Pengguna Baru</h2>
            <p class="text-sm text-blue-100 mt-1">Lengkapi form berikut sesuai role yang dipilih</p>
        </div>

        {{-- Form --}}
        <form action="{{ route('users.store') }}" method="POST" class="p-6 space-y-5">
            @csrf

            {{-- Kode --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kode</label>
                <input type="text" name="kode" value="{{ old('kode') }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                    required>
                @error('kode') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Nama --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                <input type="text" name="name" value="{{ old('name') }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                    required>
                @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                    required>
                @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Password & Konfirmasi --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" name="password"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                        required>
                    @error('password') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                        required>
                </div>
            </div>

            {{-- Role --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <select name="role" id="role"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                    required>
                    <option value="">Pilih Role</option>
                    <option value="mahasiswa" {{ old('role')=='mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                    <option value="dosen" {{ old('role')=='dosen' ? 'selected' : '' }}>Dosen</option>
                    <option value="pejabat" {{ old('role')=='pejabat' ? 'selected' : '' }}>Pejabat</option>
                    <option value="admin" {{ old('role')=='admin' ? 'selected' : '' }}>Admin</option>
                </select>
                @error('role') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Jabatan --}}
            <div id="jabatan-fields" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan</label>
                <input type="text" name="jabatan" value="{{ old('jabatan') }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
            </div>

            {{-- Ruangan --}}
            <div id="ruangan-fields" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1">Ruangan</label>
                <input type="text" name="ruangan" value="{{ old('ruangan') }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
            </div>

            {{-- Status --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    <option value="aktif" {{ old('status')=='aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ old('status')=='nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            {{-- Submit --}}
            <div>
                <button type="submit"
                    class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-3 rounded-lg font-medium shadow hover:shadow-lg hover:scale-[1.02] transition">
                    <i class="fa-solid fa-user-plus mr-2"></i> Tambah Pengguna
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('role').addEventListener('change', function() {
    const jabatan = document.getElementById('jabatan-fields');
    const ruangan = document.getElementById('ruangan-fields');

    if (this.value === 'dosen' || this.value === 'pejabat') {
        jabatan.classList.remove('hidden');
        ruangan.classList.remove('hidden');
    } else {
        jabatan.classList.add('hidden');
        ruangan.classList.add('hidden');
    }
});
</script>
@endsection
