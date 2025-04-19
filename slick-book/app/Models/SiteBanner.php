<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteBanner extends Model
{
    protected $fillable = [
        'site_id',
        'device_type',
        'section',
        'slot_no',
        'title',
        'html',
        'enabled',
        'start_at',
        'end_at',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];
}
