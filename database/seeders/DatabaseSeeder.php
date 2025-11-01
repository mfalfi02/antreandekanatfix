<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class databaseSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'kode' => 'ADM001',
            'name' => 'Admin Dekanat',
            'email' => 'admin@dekanat.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'jabatan' => 'Admin Utama',
            'status' => 'aktif',
        ]);
        User::create([
            'kode' => 'DSN001',
            'name' => 'Dosen Dekanat',
            'email' => 'dosen@dekanat.com',
            'password' => Hash::make('dosen123'),
            'role' => 'Pejabat',
            'jabatan' => 'Kepala Dekanat',
            'status' => 'aktif',
        ]);

        User::create([
            'kode' => 'MHS001',
            'name' => 'Mahasiswa A',
            'email' => 'mhs1@kampus.ac.id',
            'password' => Hash::make('password'),
            'role' => 'mahasiswa',
            'jabatan' => 'Mahasiswa',
            'status' => 'aktif',
        ]);

        User::create([
            'kode' => 'DSN002',
            'name' => 'Dosen',
            'email' => 'mhs2@kampus.ac.id',
            'password' => Hash::make('password'),
            'role' => 'dosen',
            'jabatan' => 'dosen',
            'status' => 'aktif',
        ]);
    }
}
