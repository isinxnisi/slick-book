<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 記事 Model
 *
 * @property int $id
 * @property string $title
 * @property string|null $slug
 * @property string $body
 * @property string $html_body
 * @property string|null $toc
 * @property string $status
 * @property int|null $category_id
 * @property int $site_id
 * @property int|null $published_user
 * @property \Illuminate\Support\Carbon|null $published_at
 * @property int $created_user
 * @property \Illuminate\Support\Carbon $created_at
 * @property int|null $updated_user
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property bool $is_deleted
 * @property int|null $deleted_user
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Category|null $category
 * @property-read \App\Models\User $creator
 * @property-read \App\Models\User|null $deleter
 * @property-read mixed $published
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $tags
 * @property-read int|null $tags_count
 * @property-read \App\Models\User|null $updater
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post published()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereCreatedUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereDeletedUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereHtmlBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post wherePublishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post wherePublishedUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereSiteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereToc($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereUpdatedUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post withoutTrashed()
 * @property-read mixed $seo_description
 * @property-read mixed $thumbnail_image
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SiteImage> $images
 * @property-read int|null $images_count
 * @property-read \App\Models\SiteImage|null $postEyecatchImage
 * @property-read \App\Models\SiteImage|null $postThumbnailImage
 * @property-read \App\Models\PostSeoSetting|null $seoSetting
 * @property-read \App\Models\Site $site
 * @mixin \Eloquent
 */
class Post extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $table = 'posts';

    protected $fillable = [
        'title',
        'slug',
        'body',
        'html_body',
        'toc',
        'status',
        'site_id',
        'category_id',
        'published_user',
        'published_at',
        'created_user',
        'updated_user',
        'deleted_user',
        'deleted_at',
        'is_deleted',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'is_deleted' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_user');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_user');
    }

    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_user');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'post_tag')->withTimestamps();
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function seoSetting()
    {
        return $this->hasOne(PostSeoSetting::class);
    }

    public function getSeoDescriptionAttribute()
    {
        if (!empty($this->seoSetting->meta_description)) {
            return $this->seoSetting->meta_description;
        }
        return \Str::limit(preg_replace('/\\s+/u', ' ', strip_tags($this->html_body)), 150);
    }

    public function images()
    {
        return $this->hasMany(SiteImage::class, 'post_id', 'id')
            ->where('site_id', $this->site_id)
            ->whereNotNull('post_id');
    }

    public function postThumbnailImage()
    {
        return $this->hasOne(SiteImage::class, 'post_id', 'id')
            ->where('site_id', $this->site_id)
            ->where('type', 'post_thumbnail')
            ->whereNotNull('post_id');
    }

    public function postEyecatchImage()
    {
        return $this->hasOne(SiteImage::class, 'post_id', 'id')
            ->where('site_id', $this->site_id)
            ->where('type', 'post_eyecatch')
            ->whereNotNull('post_id');
    }

    public function getPublishedAttribute()
    {
        if (is_null($this->published_at)) {
            return $this->created_at;
        }
        return $this->published_at;
    }

    public function scopePublished($query)
    {
        return $query
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->where('status', 'published'); // ← 任意でstatus判定なども
    }

    public function getThumbnailImageAttribute()
    {
        $thumbnail = $this->postThumbnailImage;
        if ($thumbnail) {
            return new \App\Models\SiteImage([
                'site_id' => $thumbnail->site_id,
                'path' => $thumbnail->type . '/' . basename($thumbnail->path),
                'alt' => $thumbnail->title . 'サムネイル画像',
            ]);
        }

        // 再帰的にカテゴリ画像を取得
        $category = $this->category;
        while ($category) {
            if (!empty($category->image_path)) {
                return new \App\Models\SiteImage([
                    'site_id' => $category->site_id,
                    'path' => $category->image_path,
                    'alt' => $category->title . 'カテゴリの画像',
                ]);
            }
            $category = $category->parent;
        }

        // サイト画像（site_imagesテーブルの正式なレコード）
        if ($this->site && $this->site->images) {
            return $this->site->images->firstWhere('type', 'site_thumbnail');
        }

        return null;
    }
}
