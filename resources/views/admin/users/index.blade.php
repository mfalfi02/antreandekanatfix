@extends('layouts.app')

@section('title','Manajemen Pengguna')

@section('content')
<div class="max-w-5xl mx-auto py-10 px-6">
    {{-- Header halaman ini mengarahkan admin ke daftar atau form tambah pengguna --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Daftar Pengguna</h1>

        <div class="flex gap-2">
            <a href="{{ route('adm') }}" 
               class="inline-flex items-center px-4 py-2 rounded-lg bg-gray-500 text-white hover:bg-gray-600 transition">
                <i class="fa fa-arrow-left mr-2"></i> Kembali
            </a>

            <a href="{{ route('users.create') }}"
               class="inline-flex items-center px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">
                <i class="fa fa-plus mr-2"></i> Tambah Pengguna
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded-lg shadow mb-4">{{ session('success') }}</div>
    @endif

    {{-- Tabel di bawah ini menampilkan data user dari controller dan mengarah ke aksi edit/hapus --}}
    <div class="overflow-x-auto bg-white rounded-xl shadow-md p-4">
        <table class="w-full table-auto border-collapse">
            <thead>
                <tr class="bg-gray-100 text-left">
                    <th class="px-4 py-2">Role</th>
                    <th class="px-4 py-2">Nama</th>
                    <th class="px-4 py-2">Identitas</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($users as $user)
                    @php
                        // Status dan badge role dipakai agar identitas dan kondisi akun cepat terbaca admin.
                        $role = ucfirst($user->role);
                        $status = $user->status == 'aktif' ? 'Aktif' : 'Tidak Aktif';
                        $badgeClass = $user->status == 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                        $roleBadgeClass = match ($user->role) {
                            'admin' => 'bg-slate-100 text-slate-700',
                            'pejabat' => 'bg-blue-100 text-blue-700',
                            'dosen' => 'bg-indigo-100 text-indigo-700',
                            default => 'bg-amber-100 text-amber-700',
                        };
                    @endphp
                    <tr>
                        <td class="px-4 py-2">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold {{ $roleBadgeClass }}">
                                <i class="fa-solid {{ $user->role === 'mahasiswa' ? 'fa-user-graduate' : 'fa-user' }}"></i>
                                {{ $role }}
                            </span>
                        </td>
                        <td class="px-4 py-2 font-medium text-gray-800">{{ $user->name }}</td>
                        <td class="px-4 py-2">
                            <p class="text-sm text-gray-700">{{ $user->email ?? '-' }}</p>
                            <p class="text-xs text-gray-500">Kode: {{ $user->kode }}</p>
                            @if (in_array($user->role, ['dosen', 'pejabat'], true) && $user->jabatan)
                                <p class="text-xs text-slate-600 inline-flex items-center gap-1 mt-0.5">
                                    <i class="fa-solid fa-id-badge text-slate-400"></i> {{ $user->jabatan }}
                                </p>
                            @endif
                            @if ($user->role === 'pejabat' && $user->ruangan)
                                <p class="text-xs text-slate-600 inline-flex items-center gap-1 mt-0.5">
                                    <i class="fa-solid fa-door-open text-slate-400"></i> {{ $user->ruangan }}
                                </p>
                            @endif
                            @if ($user->role === 'mahasiswa')
                                <p class="text-xs text-slate-600 inline-flex items-center gap-1 mt-0.5">
                                    <i class="fa-solid fa-user-graduate text-slate-400"></i> {{ $user->jabatan ?? 'Mahasiswa' }}
                                </p>
                            @endif
                        </td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $badgeClass }}">{{ $status }}</span>
                        </td>
                        <td class="px-4 py-2 space-x-2">
                            {{-- Aksi edit mengarah ke form update, sedangkan hapus mengarah ke destroy --}}
                            <a href="{{ route('users.edit', $user->kode) }}"
                                class="inline-flex items-center bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600">
                                Edit
                            </a>

                            <form action="{{ route('users.destroy', $user->kode) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Apakah yakin ingin menghapus pengguna ini?')" 
                                        class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection
