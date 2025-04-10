<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteImage extends Model
{

    protected $fillable = [
        'site_id',
        'type',
        'path',
        'title',
        'description',
        'alt',
        'is_public',
        'order',
        'variant',
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}
