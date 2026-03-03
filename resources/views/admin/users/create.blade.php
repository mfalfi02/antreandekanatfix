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
                <label class="block text-sm font-medium text-gray-700 mb-1 inline-flex items-center gap-2"><i class="fa-solid fa-user-tag text-slate-400"></i>Role</label>
                <select name="role" id="role"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                    required>
                    <option value="">Pilih Role</option>
                    <option value="mahasiswa" {{ old('role')=='mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                    <option value="dosen" {{ old('role')=='dosen' ? 'selected' : '' }}>Dosen</option>
                    <option value="pejabat" {{ old('role')=='pejabat' ? 'selected' : '' }}>Pejabat</option>
                    <option value="admin" {{ old('role')=='admin' ? 'selected' : '' }}>Admin</option>
                </select>
                <p id="role-hint-create" class="text-xs text-slate-500 mt-1 {{ old('role') === 'mahasiswa' ? '' : 'hidden' }}">
                    <i class="fa-solid fa-user-graduate text-slate-400 mr-1"></i>Role mahasiswa berfungsi sebagai pengantre.
                </p>
                @error('role') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Jabatan --}}
            <div id="jabatan-fields" class="{{ in_array(old('role'), ['dosen', 'pejabat'], true) ? '' : 'hidden' }}">
                <label class="block text-sm font-medium text-gray-700 mb-1 inline-flex items-center gap-2"><i class="fa-solid fa-id-badge text-slate-400"></i>Jabatan</label>
                <input type="text" name="jabatan" value="{{ old('jabatan') }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                @error('jabatan') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Ruangan --}}
            <div id="ruangan-fields" class="{{ old('role') === 'pejabat' ? '' : 'hidden' }}">
                <label class="block text-sm font-medium text-gray-700 mb-1 inline-flex items-center gap-2">
                    <i class="fa-solid fa-door-open text-slate-400"></i>Ruangan <span id="ruangan-required-label" class="text-red-500 {{ old('role') === 'pejabat' ? '' : 'hidden' }}">*</span>
                </label>
                <input type="text" name="ruangan" value="{{ old('ruangan') }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                <p class="text-xs text-gray-500 mt-1">Wajib diisi untuk role pejabat.</p>
                @error('ruangan') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Status --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    <option value="aktif" {{ old('status')=='aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ old('status')=='nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
                @error('status') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
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
const roleField = document.getElementById('role');
const jabatanField = document.getElementById('jabatan-fields');
const ruanganField = document.getElementById('ruangan-fields');
const ruanganLabel = document.getElementById('ruangan-required-label');
const ruanganInput = document.querySelector('input[name="ruangan"]');
const roleHintCreate = document.getElementById('role-hint-create');

function syncRoleFields(role) {
    const showJabatan = role === 'dosen' || role === 'pejabat';
    const showRuangan = role === 'pejabat';
    const showMahasiswaHint = role === 'mahasiswa';

    jabatanField.classList.toggle('hidden', !showJabatan);
    ruanganField.classList.toggle('hidden', !showRuangan);
    ruanganLabel.classList.toggle('hidden', !showRuangan);
    ruanganInput.required = showRuangan;
    roleHintCreate.classList.toggle('hidden', !showMahasiswaHint);
}

roleField.addEventListener('change', function() {
    syncRoleFields(this.value);
});

syncRoleFields(roleField.value);
</script>
@endsection
