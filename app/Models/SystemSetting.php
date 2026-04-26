<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    /**
     * Menyimpan pengaturan global antrean, termasuk status ruang dan titik geofence yang dipakai lintas fitur.
     */
    protected $table = 'system_settings';

    protected $fillable = [
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
