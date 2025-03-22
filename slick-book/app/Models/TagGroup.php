<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TagGroup extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'purpose',
        'parent_id',
        'order',
        'color',
        'icon'
    ];

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('order');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'tag_tag_group');
    }
}