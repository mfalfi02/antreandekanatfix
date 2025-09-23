<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\Admin;
use App\Models\Service;
use App\Models\Queue;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Admin
        $admin1 = Admin::create([
            'name' => 'Admin Utama',
            'email' => 'admin@uwdp.ac.id',
            'password' => Hash::make('admin123'),
            
        ]);
        // Mahasiswa
        $mahasiswa1 = Mahasiswa::create([
            'nim' => 22412890,
            'name' => 'Muhammad Alfi',
            'email' => 'alfi@student.uwdp.ac.id',
            'password' => Hash::make('1234'),
            'status' => 'Aktif'
        ]);

        $mahasiswa2 = Mahasiswa::create([
            'nim' => 22412852,
            'name' => 'Lya',
            'email' => 'lya@student.untan.ac.id',
            'password' => Hash::make('cantik'),
            'status' => 'Aktif'
        ]);

        // Dosen
        $dosen1 = Dosen::create([
            'kode_dosen' => 'D001',
            'name' => 'Dr. Sari Indah',
            'email' => 'sari@untan.ac.id',
            'password' => Hash::make('password'),
            'status' => 'Aktif',
            'role' => 'Wakil Dekan I',
            'room' => 'Ruang WD I',
            'phone' => '081234567890'
        ]);

        $dosen2 = Dosen::create([
            'kode_dosen' => 'D002',
            'name' => 'Prof. Budi Santoso',
            'email' => 'budi@untan.ac.id',
            'password' => Hash::make('password'),
            'status' => 'Aktif',
            'role' => 'Dekan',
            'room' => 'Ruang Dekan',
            'phone' => '081987654321'
        ]);

        

        // Services
        $service1 = Service::create([
            'name' => 'Legalisir Dokumen',
            'description' => 'Legalisir ijazah dan transkrip',
            'est_time' => '15 menit'
        ]);

        $service2 = Service::create([
            'name' => 'Surat Aktif Kuliah',
            'description' => 'Surat keterangan aktif kuliah',
            'est_time' => '10 menit'
        ]);

        // Queues
        Queue::create([
            'nim' => $mahasiswa1->nim,
            'dosen_id' => $dosen1->kode_dosen,
            'service_id' => $service1->id,
            'status' => 'Menunggu'
        ]);

        Queue::create([
            'nim' => $mahasiswa2->nim,
            'dosen_id' => $dosen2->kode_dosen,
            'service_id' => $service2->id,
            'status' => 'Sedang Dilayani'
        ]);
    }
}
