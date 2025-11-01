@extends('layouts.app')

@section('title','Manajemen Pengguna')

@section('content')
<div class="max-w-5xl mx-auto py-10 px-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Daftar Pengguna</h1>

        <div class="flex gap-2">
            <a href="{{ route('adm') }}" 
               class="inline-flex items-center px-4 py-2 rounded-lg bg-gray-500 text-white hover:bg-gray-600 transition">
                <i class="fa fa-arrow-left mr-2"></i> Kembali
            </a>

            <button id="addUserBtn" 
               class="inline-flex items-center px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">
                <i class="fa fa-plus mr-2"></i> Tambah Pengguna
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded-lg shadow mb-4">{{ session('success') }}</div>
    @endif

    <div class="overflow-x-auto bg-white rounded-xl shadow-md p-4">
        <table class="w-full table-auto border-collapse">
            <thead>
                <tr class="bg-gray-100 text-left">
                    <th class="px-4 py-2">Role</th>
                    <th class="px-4 py-2">Nama</th>
                    <th class="px-4 py-2">Email / Kode</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($users as $user)
                    @php
                        $role = ucfirst($user->role);
                        $status = $user->status == 'aktif' ? 'Aktif' : 'Tidak Aktif';
                        $badgeClass = $user->status == 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                    @endphp
                    <tr>
                        <td class="px-4 py-2 font-semibold">{{ $role }}</td>
                        <td class="px-4 py-2">{{ $user->name }}</td>
                        <td class="px-4 py-2">{{ $user->email ?? $user->kode }}</td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $badgeClass }}">{{ $status }}</span>
                        </td>
                        <td class="px-4 py-2 space-x-2">
                            <button class="editBtn bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600"
                                data-kode="{{ $user->kode }}"
                                data-role="{{ $role }}"
                                data-name="{{ $user->name }}"
                                data-email="{{ $user->email ?? '' }}"
                                data-status="{{ $status }}">
                                Edit
                            </button>

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

{{-- Modal Tambah/Edit --}}
<div id="userModal" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center z-50">
    <div class="bg-white p-6 rounded-xl w-full max-w-lg relative shadow-xl">
        <h3 id="modalTitle" class="text-2xl font-bold mb-4">Tambah Pengguna</h3>
        <form id="userForm" method="POST">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">

            <label class="block mb-2">Role</label>
            <select name="role" id="roleField" class="w-full border-gray-300 rounded-lg mb-4" required>
                <option value="">Pilih Role</option>
                <option value="Admin">Admin</option>
                <option value="Dosen">Dosen</option>
                <option value="Mahasiswa">Mahasiswa</option>
            </select>

            <label class="block mb-2">Nama</label>
            <input type="text" name="name" id="nameField" class="w-full border-gray-300 rounded-lg mb-4" required>

            <label class="block mb-2">Email</label>
            <input type="email" name="email" id="emailField" class="w-full border-gray-300 rounded-lg mb-4">

            <label class="block mb-2">Password</label>
            <input type="password" name="password" id="passwordField" class="w-full border-gray-300 rounded-lg mb-4">

            <div class="flex justify-end gap-2">
                <button type="button" id="closeModal" class="px-4 py-2 rounded-lg bg-gray-300 hover:bg-gray-400">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
const addUserBtn = document.getElementById('addUserBtn');
const userModal = document.getElementById('userModal');
const closeModal = document.getElementById('closeModal');
const roleField = document.getElementById('roleField');

// Buka modal Tambah
addUserBtn.addEventListener('click', () => {
    userModal.classList.remove('hidden');
    document.getElementById('modalTitle').innerText = "Tambah Pengguna";
    document.getElementById('userForm').action = "{{ route('users.store') }}";
    document.getElementById('formMethod').value = "POST";
    document.getElementById('userForm').reset();
});

// Tutup modal
closeModal.addEventListener('click', () => {
    userModal.classList.add('hidden');
});

// Edit User
document.querySelectorAll('.editBtn').forEach(btn => {
    btn.addEventListener('click', () => {
        const kode = btn.dataset.kode;
        const role = btn.dataset.role;
        const name = btn.dataset.name;
        const email = btn.dataset.email;
        const status = btn.dataset.status;

        document.getElementById('modalTitle').innerText = "Edit Pengguna";
        document.getElementById('userForm').action = `/admin/users/${kode}`;
        document.getElementById('formMethod').value = "PUT";

        roleField.value = role;
        document.getElementById('nameField').value = name;
        document.getElementById('emailField').value = email;
        document.getElementById('passwordField').value = "";

        userModal.classList.remove('hidden');
    });
});
</script>
@endsection
