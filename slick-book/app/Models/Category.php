<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
