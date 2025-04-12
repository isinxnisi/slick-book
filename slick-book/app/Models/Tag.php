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
        'purpose',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
    ];

    protected $attributes = [
        'purpose' => 'public', // デフォルト値
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

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_tag')->withTimestamps();
    }

    public function scopeForSite($query, $siteId)
    {
        return $query->whereIn('id', function ($query) use ($siteId) {
            $query->select('tag_id')
                ->from('tag_tag_group')
                ->whereIn('tag_group_id', function ($q) use ($siteId) {
                    $q->select('tag_group_id')
                      ->from('site_tag_group')
                      ->where('site_id', $siteId);
                });
        });
    }

    public function tagGroupsForSite($siteId)
    {
        return $this->tagGroups()->forSite($siteId);
    }
}
