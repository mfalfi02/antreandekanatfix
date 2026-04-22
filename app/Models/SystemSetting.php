<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $table = 'system_settings';

    protected $fillable = [
        // Status global antrean dan titik geofence dipakai oleh admin serta validasi request.
        'queue_status',
        'center_latitude',
        'center_longitude',
        'radius_meters',
    ];

    protected $casts = [
        'center_latitude' => 'float',
        'center_longitude' => 'float',
        'radius_meters' => 'integer',
    ];
}
