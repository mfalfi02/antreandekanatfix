@extends('layouts.app')

@section('title','Edit Pengguna')

@section('content')
 <a href="{{ route('dashboard.admin') }}" class="...">&larr; Kembali</a>
<div class="max-w-2xl mx-auto bg-gradient-to-r from-blue-50 via-white to-blue-50 p-8 rounded-2xl shadow-2xl mt-12 border border-blue-200">
    <div class="text-center mb-6">
        <h2 class="text-3xl font-extrabold text-blue-800">✨ Edit Pengguna</h2>
        <p class="text-blue-600 mt-2">Perbarui data untuk {{ $role }}</p>
    </div>
 


    <form action="{{ route('users.update', ['role' => $role, 'id' => $user->id ?? $user->nim ?? $user->kode_dosen]) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Role (readonly) --}}
        <div>
            <label class="block text-sm font-medium text-blue-700 mb-2">Role</label>
            <input type="text" value="{{ $role }}" class="w-full border border-blue-300 rounded-lg p-3 bg-gray-100" readonly>
        </div>

        {{-- Nama & Email & Password --}}
        <div>
            <label>Nama</label>
            <input type="text" name="name" required class="w-full border p-2 rounded" value="{{ old('name', $user->name) }}" />
        </div>
        <div>
            <label>Email</label>
            <input type="email" name="email" required class="w-full border p-2 rounded" value="{{ old('email', $user->email) }}" />
        </div>
        <div>
            <label>Password <span class="text-gray-500 text-sm">(Kosongkan jika tidak ingin diubah)</span></label>
            <input type="password" name="password" class="w-full border p-2 rounded" />
        </div>
        <div>
            <label>Konfirmasi Password</label>
            <input type="password" name="password_confirmation" class="w-full border p-2 rounded" />
        </div>

        {{-- Mahasiswa Fields --}}
        @if($role === 'Mahasiswa')
        <div class="space-y-2">
            <div>
                <label>NIM</label>
                <input type="text" name="nim" class="w-full border p-2 rounded bg-gray-100" value="{{ $user->nim }}" readonly />
            </div>
            <div>
                <label>Status</label>
                <select name="status" class="w-full border p-2 rounded">
                    <option value="Aktif" {{ $user->status == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="Tidak Aktif" {{ $user->status == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
            </div>
        </div>
        @endif

        {{-- Dosen Fields --}}
        @if($role === 'Dosen')
        <div class="space-y-2">
            <div>
                <label>Kode Dosen</label>
                <input type="text" name="kode_dosen" class="w-full border p-2 rounded bg-gray-100" value="{{ $user->kode_dosen }}" readonly />
            </div>
            <div>
                <label>Status</label>
                <select name="status" class="w-full border p-2 rounded">
                    <option value="Aktif" {{ $user->status == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="Tidak Aktif" {{ $user->status == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
            </div>
            <div>
                <label>Role Dosen</label>
                <input type="text" name="dosen_role" class="w-full border p-2 rounded" value="{{ $user->role }}" />
            </div>
            <div>
                <label>Ruang</label>
                <input type="text" name="room" class="w-full border p-2 rounded" value="{{ $user->room }}" />
            </div>
            <div>
                <label>Telepon</label>
                <input type="text" name="phone" class="w-full border p-2 rounded" value="{{ $user->phone }}" />
            </div>
        </div>
        @endif

        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Perbarui Pengguna</button>
    </form>
</div>
@endsection
