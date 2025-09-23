@extends('layouts.app')

@section('title','Tambah Pengguna')

@section('content')
<a href="{{ route('dashboard.admin') }}" 
   class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium transition-colors">
    <i class="fa-solid fa-arrow-left mr-2"></i> Kembali
</a>

<div class="max-w-3xl mx-auto bg-white/60 backdrop-blur-md shadow-xl rounded-2xl overflow-hidden mt-10 border border-blue-100">
    
    {{-- Header --}}
    <div class="bg-gradient-to-r from-blue-600 via-indigo-500 to-purple-500 p-6 text-center text-white">
        <h2 class="text-3xl font-extrabold">Tambah Pengguna Baru</h2>
        <p class="text-sm text-blue-100 mt-1">Lengkapi form berikut sesuai dengan role yang dipilih</p>
    </div>

    {{-- Form --}}
    <form action="{{ route('users.store') }}" method="POST" class="p-8 space-y-6">
        @csrf

        {{-- Role --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Role</label>
            <select name="role" id="role" 
                class="w-full border border-blue-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" 
                required>
                <option value="">Pilih Role</option>
                <option value="Admin" {{ old('role') == 'Admin' ? 'selected' : '' }}>Admin</option>
                <option value="Dosen" {{ old('role') == 'Dosen' ? 'selected' : '' }}>Dosen</option>
                <option value="Mahasiswa" {{ old('role') == 'Mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
            </select>
        </div>

        {{-- Nama --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Nama</label>
            <input type="text" name="name" value="{{ old('name') }}" 
                   class="w-full border border-blue-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" 
                   required>
        </div>

        {{-- Email --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" 
                   class="w-full border border-blue-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" 
                   required>
        </div>

        {{-- Password --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
            <input type="password" name="password" 
                   class="w-full border border-blue-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" 
                   required>
        </div>

        {{-- Confirm Password --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Konfirmasi Password</label>
            <input type="password" name="password_confirmation" 
                   class="w-full border border-blue-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" 
                   required>
        </div>

        {{-- Mahasiswa Fields --}}
        <div id="mhs-fields" class="hidden space-y-3">
            <div>
                <label class="block text-sm font-semibold text-gray-700">NIM</label>
                <input type="text" name="nim" value="{{ old('nim') }}" 
                       class="w-full border border-blue-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 transition" />
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700">Status</label>
                <select name="status" class="w-full border border-blue-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 transition">
                    <option value="Aktif">Aktif</option>
                    <option value="Tidak Aktif">Tidak Aktif</option>
                </select>
            </div>
        </div>

        {{-- Dosen Fields --}}
        <div id="dosen-fields" class="hidden space-y-3">
            <div>
                <label class="block text-sm font-semibold text-gray-700">Kode Dosen</label>
                <input type="text" name="kode_dosen" value="{{ old('kode_dosen') }}" 
                       class="w-full border border-blue-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 transition" />
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700">Status</label>
                <select name="status" class="w-full border border-blue-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 transition">
                    <option value="Aktif">Aktif</option>
                    <option value="Tidak Aktif">Tidak Aktif</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700">Role Dosen</label>
                <input type="text" name="dosen_role" value="{{ old('dosen_role') }}" 
                       class="w-full border border-blue-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 transition" />
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700">Ruang</label>
                <input type="text" name="room" value="{{ old('room') }}" 
                       class="w-full border border-blue-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 transition" />
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700">Telepon</label>
                <input type="text" name="phone" value="{{ old('phone') }}" 
                       class="w-full border border-blue-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 transition" />
            </div>
        </div>

        {{-- Submit --}}
        <div class="pt-4">
            <button type="submit" 
                    class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl hover:scale-[1.02] transform transition">
                <i class="fa-solid fa-user-plus mr-2"></i> Tambah Pengguna
            </button>
        </div>
    </form>
</div>

<script>
document.getElementById('role').addEventListener('change', function() {
    document.getElementById('mhs-fields').classList.add('hidden');
    document.getElementById('dosen-fields').classList.add('hidden');

    if(this.value === 'Mahasiswa') document.getElementById('mhs-fields').classList.remove('hidden');
    if(this.value === 'Dosen') document.getElementById('dosen-fields').classList.remove('hidden');
});
</script>
@endsection
