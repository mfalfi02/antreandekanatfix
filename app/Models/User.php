<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'kode'; // karena primary key-nya string
    public $incrementing = false; // non-integer PK
    protected $keyType = 'string'; // tipe data string

    protected $fillable = [
        'kode',
        'name',
        'email',
        'password',
        'role',
        'jabatan',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getAuthIdentifierName()
{
    return 'kode';
}
public function username()
{
    return 'kode';
}


    // Relasi: satu user bisa punya banyak antrean
    public function queues()
    {
        return $this->hasMany(Queue::class, 'kode_user', 'kode');
    }
}
