<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSeoSetting extends Model
{
    protected $fillable = [
        'site_id',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_base',
        'twitter_card_type',
        'custom_head_tags',
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}
