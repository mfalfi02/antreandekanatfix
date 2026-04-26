<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends Model
{
    use HasFactory;

    /**
     * Menyimpan master layanan yang dipakai dashboard, laporan, dan validasi antrean.
     */
    protected $fillable = [
        'nama_layanan',
        'deskripsi',
        'status',
        'est'
    ];

    /**
     * Menghubungkan layanan ke antrean yang memakainya agar statistik bisa dihitung per layanan.
     */
    public function queues()
    {
        return $this->hasMany(Queue::class, 'service_id');
    }
}
