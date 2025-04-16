<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostSeoSetting extends Model
{
    protected $fillable = [
        'post_id',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_base',
        'twitter_card_type',
        'custom_head_tags',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
