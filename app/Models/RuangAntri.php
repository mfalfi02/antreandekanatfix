<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RuangAntri extends Model
{
    use HasFactory;

    protected $table = 'ruang_antri';
    protected $primaryKey = 'id_ruang_antri';

    protected $fillable = [
        'kode_dosen',
        'service_id',
        'service_ids',
        'status_ruang',
        'expected_jam_buka_ruang_antri',
        'expected_jam_tutup_ruang_antri',
        'jam_buka_ruang_antri',
        'jam_tutup_ruang_antri',
        'tanggal_buka_ruang_antri',
    ];

    protected $casts = [
        'service_ids' => 'array',
    ];

    /**
     * Relasi ke User (dosen)
     * kode_dosen → users.kode
     */
    public function dosen()
    {
        return $this->belongsTo(User::class, 'kode_dosen', 'kode');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}
