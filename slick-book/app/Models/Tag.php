<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image_path',
        'icon',
        'color',
        'is_visible',
        'canonical_name',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
    ];

    /**
     * タグが属するタググループ（多対多）
     */
    public function tagGroups()
    {
        return $this->belongsToMany(TagGroup::class)
                    ->withPivot('order')
                    ->withTimestamps()
                    ->orderBy('pivot_order'); // 並び順
    }

    /**
     * 同義語（別名）タグとのリレーション（1対多）
     */
    public function synonyms()
    {
        return $this->hasMany(Tag::class, 'canonical_id');
    }

    /**
     * 正規タグ（このタグが別名なら）
     */
    public function canonical()
    {
        return $this->belongsTo(Tag::class, 'canonical_id');
    }
}
