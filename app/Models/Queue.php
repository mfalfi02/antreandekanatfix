<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Queue extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_user',
        'service_id',
        'nomor_antrian',
        'status',
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'kode_user', 'kode');
    }

    // Relasi ke Service
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}
