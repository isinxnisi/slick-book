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
        return $this->hasMany(self::class, 'parent_id')->with('children');
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

    public function posts()
    {
        return $this->hasMany(Post::class, 'category_id');
    }
}
