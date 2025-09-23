@extends('layouts.app')

@section('title','Manajemen Pengguna')

@section('content')
<div class="max-w-7xl mx-auto mt-10 space-y-6">

    {{-- Header --}}
    <a href="{{ route('dashboard.admin') }}" class="...">&larr; Kembali</a>

    <div class="flex justify-between items-center">
    <h2 class="text-3xl font-bold">Manajemen Pengguna</h2>
    <a href="{{ route('users.create') }}" 
       class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center gap-2">
        <i class="fa-solid fa-plus"></i> Tambah Pengguna
    </a>
</div>


    {{-- Success Message --}}
    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded-lg shadow">{{ session('success') }}</div>
    @endif

    {{-- Tabel Pengguna --}}
    <div class="overflow-x-auto bg-white rounded-xl shadow-md p-4">
        <table class="w-full table-auto border-collapse">
            <thead>
                <tr class="bg-gray-100 text-left">
                    <th class="px-4 py-2">Role</th>
                    <th class="px-4 py-2">Nama</th>
                    <th class="px-4 py-2">Email / NIM / Kode Dosen</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @php
                    $allUsers = collect();
                    $allUsers = $allUsers->merge($admins)->merge($dosens)->merge($mahasiswas);
                @endphp

                @foreach($allUsers as $user)
                    @php
                        $role = $user->role ?? (isset($user->nim) ? 'Mahasiswa' : (isset($user->kode_dosen) ? 'Dosen' : 'Admin'));
                        $status = $user->status ?? 'Tidak Aktif';
                        $badgeClass = $status == 'Aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                        $identifier = $user->id ?? $user->nim ?? $user->kode_dosen;
                    @endphp
                    <tr>
                        <td class="px-4 py-2 font-semibold">{{ $role }}</td>
                        <td class="px-4 py-2">{{ $user->name }}</td>
                        <td class="px-4 py-2">
                            @if(isset($user->email))
                                {{ $user->email }}
                            @elseif(isset($user->nim))
                                {{ $user->nim }}
                            @elseif(isset($user->kode_dosen))
                                {{ $user->kode_dosen }}
                            @endif
                        </td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $badgeClass }}">{{ $status }}</span>
                        </td>
                        <td class="px-4 py-2 space-x-2">
                            <button class="editBtn bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600" 
                                data-id="{{ $identifier }}" 
                                data-role="{{ $role }}"
                                data-name="{{ $user->name }}"
                                data-email="{{ $user->email ?? '' }}"
                                data-nim="{{ $user->nim ?? '' }}"
                                data-kode_dosen="{{ $user->kode_dosen ?? '' }}"
                                data-status="{{ $status }}"
                                data-room="{{ $user->room ?? '' }}"
                                data-phone="{{ $user->phone ?? '' }}"
                                data-dosen_role="{{ $user->role ?? '' }}"
                            >
                                Edit
                            </button>
                            <button class="deleteBtn bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600"
                                data-id="{{ $identifier }}" data-role="{{ $role }}">
                                Hapus
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Tambah/Edit --}}
<div id="userModal" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center z-50">
    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl w-full max-w-lg relative shadow-xl">
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

            {{-- Mahasiswa --}}
            <div id="mhsFields" class="hidden">
                <label class="block mb-2">NIM</label>
                <input type="text" name="nim" id="nimField" class="w-full border-gray-300 rounded-lg mb-4">

                <label class="block mb-2">Status</label>
                <select name="status_mhs" id="statusMhsField" class="w-full border-gray-300 rounded-lg mb-4">
                    <option value="Aktif">Aktif</option>
                    <option value="Tidak Aktif">Tidak Aktif</option>
                </select>
            </div>

            {{-- Dosen --}}
            <div id="dosenFields" class="hidden">
                <label class="block mb-2">Kode Dosen</label>
                <input type="text" name="kode_dosen" id="kodeDosenField" class="w-full border-gray-300 rounded-lg mb-4">

                <label class="block mb-2">Status</label>
                <select name="status" id="statusDosenField" class="w-full border-gray-300 rounded-lg mb-4">
                    <option value="Aktif">Aktif</option>
                    <option value="Tidak Aktif">Tidak Aktif</option>
                </select>

                <label class="block mb-2">Role Dosen</label>
                <input type="text" name="dosen_role" id="roleDosenField" class="w-full border-gray-300 rounded-lg mb-4">

                <label class="block mb-2">Ruang</label>
                <input type="text" name="room" id="roomField" class="w-full border-gray-300 rounded-lg mb-4">

                <label class="block mb-2">Telepon</label>
                <input type="text" name="phone" id="phoneField" class="w-full border-gray-300 rounded-lg mb-4">
            </div>

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

    const mhsFields = document.getElementById('mhsFields');
    const dosenFields = document.getElementById('dosenFields');

    function toggleFields() {
        mhsFields.classList.add('hidden');
        dosenFields.classList.add('hidden');
        if(roleField.value === 'Mahasiswa') mhsFields.classList.remove('hidden');
        if(roleField.value === 'Dosen') dosenFields.classList.remove('hidden');
    }

    roleField.addEventListener('change', toggleFields);

    addUserBtn.addEventListener('click', () => {
        userModal.classList.remove('hidden');
        document.getElementById('modalTitle').innerText = "Tambah Pengguna";
        document.getElementById('userForm').action = "{{ route('users.store') }}";
        document.getElementById('formMethod').value = "POST";
        roleField.value = "";
        toggleFields();
        document.getElementById('userForm').reset();
    });

    closeModal.addEventListener('click', () => {
        userModal.classList.add('hidden');
    });

    // Edit User
    document.querySelectorAll('.editBtn').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;
            const role = btn.dataset.role;
            document.getElementById('modalTitle').innerText = "Edit Pengguna";
            document.getElementById('userForm').action = `/admin/users/${role}/${id}`;
            document.getElementById('formMethod').value = "PUT";

            roleField.value = role;
            toggleFields();

            document.getElementById('nameField').value = btn.dataset.name;
            document.getElementById('emailField').value = btn.dataset.email;
            document.getElementById('nimField').value = btn.dataset.nim;
            document.getElementById('kodeDosenField').value = btn.dataset.kode_dosen;
            document.getElementById('statusMhsField').value = btn.dataset.status;
            document.getElementById('statusDosenField').value = btn.dataset.status;
            document.getElementById('roleDosenField').value = btn.dataset.dosen_role;
            document.getElementById('roomField').value = btn.dataset.room;
            document.getElementById('phoneField').value = btn.dataset.phone;
            document.getElementById('passwordField').value = "";
            
            userModal.classList.remove('hidden');
        });
    });

    // Delete User
    document.querySelectorAll('.deleteBtn').forEach(btn => {
        btn.addEventListener('click', () => {
            if(confirm("Apakah yakin ingin menghapus pengguna ini?")) {
                const id = btn.dataset.id;
                const role = btn.dataset.role;
                const form = document.createElement('form');
                form.method = "POST";
                form.action = `/admin/users/${role}/${id}`;
                form.innerHTML = `
                    @csrf
                    @method('DELETE')
                `;
                document.body.appendChild(form);
                form.submit();
            }
        });
    });

</script>
@endsection
