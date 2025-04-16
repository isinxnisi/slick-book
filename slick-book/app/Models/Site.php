<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * サイト
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $domain
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Category> $categories
 * @property-read int|null $categories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SiteImage> $images
 * @property-read int|null $images_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Site newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Site newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Site onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Site query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Site whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Site whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Site whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Site whereDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Site whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Site whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Site whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Site whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Site withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Site withoutTrashed()
 * @mixin \Eloquent
 */
class Site extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'domain',
        'description',
        'deleted_at',
    ];

    public function images()
    {
        return $this->hasMany(SiteImage::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class, 'site_id')->with('children');
    }

    public function seoSetting()
    {
        return $this->hasOne(SiteSeoSetting::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'site_id');
    }

    public function getSiteUrlAttribute()
    {
        $scheme = config('app.scheme', 'https');
        $port = config('app.port', '80');

        return "{$scheme}://{$this->domain}:{$port}";
    }
}
