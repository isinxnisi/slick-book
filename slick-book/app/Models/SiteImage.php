<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * サイト 画像テーブル
 *
 * @property int $id
 * @property int $site_id
 * @property string $type
 * @property string $path
 * @property string|null $title
 * @property string|null $description
 * @property string|null $alt
 * @property bool $is_public
 * @property int $order
 * @property string|null $variant
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Site $site
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiteImage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiteImage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiteImage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiteImage whereAlt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiteImage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiteImage whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiteImage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiteImage whereIsPublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiteImage whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiteImage wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiteImage whereSiteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiteImage whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiteImage whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiteImage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiteImage whereVariant($value)
 * @property int|null $post_id
 * @property-read \App\Models\Post|null $post
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiteImage wherePostId($value)
 * @mixin \Eloquent
 */
class SiteImage extends Model
{

    protected $fillable = [
        'site_id',
        'post_id',
        'taxonomy_term_id',
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

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function taxonomyTerm()
    {
        return $this->belongsTo(TaxonomyTerm::class);
    }
}
