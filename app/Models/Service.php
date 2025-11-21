<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_layanan',
        'deskripsi',
        'status',
        'est'
    ];

    // Relasi: satu layanan bisa punya banyak antrean
    public function queues()
    {
        return $this->hasMany(Queue::class, 'service_id');
    }
}
