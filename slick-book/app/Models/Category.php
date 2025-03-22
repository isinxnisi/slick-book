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
        'deleted_at',
    ];

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')->with('children');
    }
}
