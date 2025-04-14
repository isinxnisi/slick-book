<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property int $post_id
 * @property int $tag_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $order
 * @property-read \App\Models\Post $post
 * @property-read \App\Models\Tag $tag
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $tags
 * @property-read int|null $tags_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostTag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostTag query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostTag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostTag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostTag whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostTag wherePostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostTag whereTagId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostTag whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
