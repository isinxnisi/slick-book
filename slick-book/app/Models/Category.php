<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property int $site_id
 * @property int|null $parent_id
 * @property string $title
 * @property string $slug
 * @property string|null $description
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property string|null $image_path
 * @property string|null $icon
 * @property string|null $color
 * @property bool $is_visible
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Category> $children
 * @property-read int|null $children_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Category> $childrenRecursive
 * @property-read int|null $children_recursive_count
 * @property-read mixed $breadcrumb
 * @property-read mixed $breadcrumb_w_o_self
 * @property-read mixed $descendant_ids
 * @property-read Category|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Post> $posts
 * @property-read int|null $posts_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereIsVisible($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereSiteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Category extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'site_id',
        'parent_id',
        'order',
        'image_path',
        'icon',
        'color',
        'is_visible',
        'deleted_at',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
    ];

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')
            ->orderBy('order')
            ->with('children');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function getBreadcrumbAttribute()
    {
        $titles = [];
        $category = $this;
        while ($category) {
            array_unshift($titles, $category->title);
            $category = $category->parent;
        }
        return implode(' > ', $titles);
    }

    public function getBreadcrumbWOSelfAttribute()
    {
        $titles = [];
        $category = $this;
        while ($category) {
            array_unshift($titles, $category->title);
            $category = $category->parent;
        }
        array_pop($titles);
        return implode(' > ', $titles);
    }

    public function childrenRecursive()
    {
        return $this->hasMany(self::class, 'parent_id')
            ->orderBy('order')
            ->with('childrenRecursive');
    }

    public function getDescendantIdsAttribute()
    {
        return collect([$this->id])->merge(
            $this->childrenRecursive->flatMap(fn($child) => $child->descendant_ids)
        );
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'category_id');
    }
}
