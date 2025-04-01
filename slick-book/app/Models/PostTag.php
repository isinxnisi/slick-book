<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostTag extends Model
{
    protected $table = 'post_tag';

    protected $fillable = [
        'post_id',
        'tag_id',
        'order',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function tag()
    {
        return $this->belongsTo(Tag::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class)
            ->withTimestamps()
            ->withPivot('order')
            ->orderBy('post_tag.order');
    }

}
