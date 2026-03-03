<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Service;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(
            ['kode' => 'ADM001'],
            [
                'name' => 'Admin Dekanat',
                'email' => 'admin@dekanat.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'jabatan' => 'Admin Utama',
                'status' => 'aktif',
            ]
        );

        User::updateOrCreate(
            ['kode' => 'DSN001'],
            [
                'name' => 'Paskalia Kartini',
                'email' => 'dosen@dekanat.com',
                'password' => Hash::make('dosen123'),
                'role' => 'pejabat',
                'jabatan' => 'Kepala Dekanat',
                'ruangan' => 'Ruang Dekanat 1',
                'status' => 'aktif',
            ]
        );

        User::updateOrCreate(
            ['kode' => '22412890'],
            [
                'name' => 'ALFI',
                'email' => 'mfalfi@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'mahasiswa',
                'jabatan' => 'Mahasiswa',
                'status' => 'aktif',
            ]
        );

        User::updateOrCreate(
            ['kode' => 'DSN002'],
            [
                'name' => 'Dosen',
                'email' => 'mhs2@kampus.ac.id',
                'password' => Hash::make('password'),
                'role' => 'dosen',
                'jabatan' => 'dosen',
                'status' => 'aktif',
            ]
        );

        Service::updateOrCreate(
            ['nama_layanan' => 'Tanda Tangan'],
            ['deskripsi' => 'Layanan tanda tangan dokumen', 'status' => 'aktif', 'est' => 5]
        );

        Service::updateOrCreate(
            ['nama_layanan' => 'Konsultasi Akademik'],
            ['deskripsi' => 'Konsultasi terkait akademik', 'status' => 'aktif', 'est' => 15]
        );

        Service::updateOrCreate(
            ['nama_layanan' => 'Konsultasi Administrasi'],
            ['deskripsi' => 'Konsultasi terkait administrasi', 'status' => 'aktif', 'est' => 10]
        );

        Service::updateOrCreate(
            ['nama_layanan' => 'Konsultasi Skripsi'],
            ['deskripsi' => 'Konsultasi terkait skripsi', 'status' => 'aktif', 'est' => 20]
        );
    }
}
