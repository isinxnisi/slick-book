<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
}
