<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    protected $fillable = ['name','email','password','role'];

    // Relasi ke Mahasiswa
    public function mahasiswa()
    {
        return $this->hasOne(Mahasiswa::class);
    }

    // Relasi ke Dosen
    public function dosen()
    {
        return $this->hasOne(Dosen::class);
    }

    // Relasi ke Admin
    public function admin()
    {
        return $this->hasOne(Admin::class);
    }
}
