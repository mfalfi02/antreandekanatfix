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

        $mahasiswa = [
            ['kode' => '22412902', 'name' => 'Rachel Rattu Vebyola'],
            ['kode' => '22412875', 'name' => 'Frisilia Agnes Maria Sinaga'],
            ['kode' => '22412858', 'name' => 'Ajay Antholin'],
            ['kode' => '22412886', 'name' => 'Julita'],
            ['kode' => '22412883', 'name' => 'Jessen Hero Pratama'],
            ['kode' => '22412872', 'name' => 'Eva Gultom'],
            ['kode' => '22412861', 'name' => 'Arief Cahyadi'],
            ['kode' => '22412901', 'name' => 'Prabu Yapta Sider'],
            ['kode' => '22412895', 'name' => 'Mixsel Andrean Christho'],
            ['kode' => '22412881', 'name' => 'Gregorius Adrian Padua'],
            ['kode' => '22412866', 'name' => 'Daniel'],
            ['kode' => '22412876', 'name' => 'Gebby Patricia Pratama'],
            ['kode' => '22412923', 'name' => 'Yensen Gunawan'],
            ['kode' => '22412926', 'name' => 'Yosafat Wineto Padagi'],
            ['kode' => '22412925', 'name' => 'Yohanes Dio'],
            ['kode' => '22412912', 'name' => 'Trissianto'],
            ['kode' => '22412924', 'name' => 'Yogi'],
            ['kode' => '22412914', 'name' => 'Valentina Sada Arih Ginting'],
            ['kode' => '22412892', 'name' => 'Maria Stella'],
            ['kode' => '22412878', 'name' => 'Gerry Steven Martin'],
            ['kode' => '22412896', 'name' => 'Nadila Loda'],
            ['kode' => '22412871', 'name' => 'Erna Juliyanti'],
        ];

        foreach ($mahasiswa as $item) {
            User::updateOrCreate(
                ['kode' => $item['kode']],
                [
                    'name' => $item['name'],
                    'email' => $item['kode'] . '@kampus.ac.id',
                    'password' => Hash::make('Password'),
                    'role' => 'mahasiswa',
                    'jabatan' => 'Mahasiswa',
                    'status' => 'aktif',
                ]
            );
        }

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
