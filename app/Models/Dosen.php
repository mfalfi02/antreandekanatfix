<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;

class Dosen extends Authenticatable
{
    use HasFactory;

    protected $primaryKey = 'kode_dosen';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kode_dosen',
        'name',
        'email',
        'password',
        'status',
        'role',
        'room',
        'phone'
    ];

    // Auto-hash password
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function queues()
    {
        return $this->hasMany(Queue::class);
    }
}
