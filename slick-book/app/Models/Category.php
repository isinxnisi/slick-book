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
}
