<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Notifications\ResetPasswordNotification;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Menjadikan kode user sebagai identitas utama agar login, relasi, dan dashboard mengarah ke data yang sama.
     */
    protected $primaryKey = 'kode';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kode',
        'name',
        'email',
        'role',
        'password',
        'jabatan',
        'ruangan',
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

    /**
     * Menghubungkan user ke antrean yang mereka buat supaya dashboard dan laporan bisa menelusuri riwayatnya.
     */
    public function queues()
    {
        return $this->hasMany(Queue::class, 'kode_user', 'kode');
    }

    /**
     * Mengarahkan notifikasi reset password ke email user dengan template yang lebih rapi.
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token, $this->email));
    }
}
