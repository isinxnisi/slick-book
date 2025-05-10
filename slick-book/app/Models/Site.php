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
 * @property string|null $sub_title
 * @property string|null $sub_message
 * @property-read mixed $site_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Post> $posts
 * @property-read int|null $posts_count
 * @property-read \App\Models\SiteSeoSetting|null $seoSetting
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Site whereSubMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Site whereSubTitle($value)
 * @mixin \Eloquent
 */
class Site extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'sub_title',
        'sub_message',
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

        if (empty($port) || $scheme == 'https' && $port == '443' || $scheme == 'http' && $port == '80') {

            return "{$scheme}://{$this->domain}";
        }

        return "{$scheme}://{$this->domain}:{$port}";
    }
}
