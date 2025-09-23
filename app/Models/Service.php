<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    protected $fillable = ['name','description','est_time'];

    public function queues()
    {
        return $this->hasMany(Queue::class);
    
        // jika foreign key berbeda, sesuaikan argumen kedua
        return $this->hasMany(\App\Models\Queue::class, 'service_id');
    
    }
    
}


