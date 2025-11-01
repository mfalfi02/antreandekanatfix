@extends('layouts.app')

@section('title', 'Edit Pengguna')

@section('content')
<a href="{{ route('users.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium transition-colors">
    <i class="fa-solid fa-arrow-left mr-2"></i> Kembali
</a>

<div class="max-w-3xl mx-auto bg-white/60 backdrop-blur-md shadow-xl rounded-2xl overflow-hidden mt-10 border border-blue-100">
    
    {{-- Header --}}
    <div class="bg-gradient-to-r from-blue-600 via-indigo-500 to-purple-500 p-6 text-center text-white">
        <h2 class="text-3xl font-extrabold">Edit Pengguna</h2>
        <p class="text-sm text-blue-100 mt-1">Perbarui informasi pengguna sesuai kebutuhan</p>
    </div>

    {{-- Form --}}
    <form action="{{ route('users.update', $user->kode) }}" method="POST" class="p-8 space-y-6">
        @csrf
        @method('PUT')

        {{-- Kode --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Kode</label>
            <input type="text" name="kode" value="{{ old('kode', $user->kode) }}" 
                class="w-full border border-blue-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" 
                required>
            @error('kode')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Nama --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Nama</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" 
                class="w-full border border-blue-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" 
                required>
            @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" 
                class="w-full border border-blue-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
            @error('email')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Password <span class="text-gray-400 text-xs">(Kosongkan jika tidak ingin diubah)</span></label>
            <input type="password" name="password" 
                class="w-full border border-blue-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
            @error('password')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Role --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Role</label>
            <select name="role" id="role" class="w-full border border-blue-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" required>
                <option value="">Pilih Role</option>
                <option value="mahasiswa" {{ old('role', $user->role)=='mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                <option value="dosen" {{ old('role', $user->role)=='dosen' ? 'selected' : '' }}>Dosen</option>
                <option value="pejabat" {{ old('role', $user->role)=='pejabat' ? 'selected' : '' }}>Pejabat</option>
                <option value="admin" {{ old('role', $user->role)=='admin' ? 'selected' : '' }}>Admin</option>
            </select>
            @error('role')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Jabatan (untuk dosen/pejabat) --}}
        <div id="jabatan-fields" class="{{ in_array(old('role', $user->role), ['dosen','pejabat']) ? '' : 'hidden' }}">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Jabatan</label>
            <input type="text" name="jabatan" value="{{ old('jabatan', $user->jabatan) }}" 
                class="w-full border border-blue-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
        </div>

        {{-- Status --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Status</label>
            <select name="status" class="w-full border border-blue-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                <option value="aktif" {{ old('status', $user->status)=='aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="nonaktif" {{ old('status', $user->status)=='nonaktif' ? 'selected' : '' }}>Nonaktif</option>
            </select>
        </div>

        {{-- Submit --}}
        <div class="pt-4">
            <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl hover:scale-[1.02] transform transition">
                <i class="fa-solid fa-user-pen mr-2"></i> Perbarui Pengguna
            </button>
        </div>
    </form>
</div>

<script>
document.getElementById('role').addEventListener('change', function() {
    const jabatan = document.getElementById('jabatan-fields');
    if(this.value === 'dosen' || this.value === 'pejabat') {
        jabatan.classList.remove('hidden');
    } else {
        jabatan.classList.add('hidden');
    }
});
</script>
@endsection
