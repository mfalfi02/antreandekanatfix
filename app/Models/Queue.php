<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Queue extends Model
{
    use HasFactory;

    /**
     * Menyimpan data inti antrean yang mengalir ke dashboard mahasiswa, dosen, display publik, dan laporan.
     */
    protected $fillable = [
        'kode_user',
        'kode_dosen',
        'service_id',
        'nomor_antrian',
        'status',
    ];

    /**
     * Menghubungkan antrean ke user pengantre agar nama dan identitasnya bisa ditampilkan di UI.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'kode_user', 'kode');
    }

    /**
     * Menghubungkan antrean ke layanan yang dipilih supaya estimasi dan label layanan bisa dirender.
     */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /**
     * Menghubungkan antrean ke dosen atau pejabat tujuan agar status ruang dan laporan dapat ditelusuri.
     */
    public function dosen()
    {
        return $this->belongsTo(User::class, 'kode_dosen', 'kode');
    }
}
