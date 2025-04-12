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
        return $this->belongsToMany(Tag::class)
            ->withTimestamps()
            ->withPivot('order')
            ->orderBy('tag_tag_group.order');
    }

    public function getBreadcrumbAttribute()
    {
        $names = [];
        $group = $this;
        while ($group) {
            array_unshift($names, $group->name);
            $group = $group->parent;
        }
        return implode(' > ', $names);
    }

    public function sites()
    {
        return $this->belongsToMany(Site::class, 'site_tag_group');
    }

    public function siteTagGroups()
    {
        return $this->hasMany(SiteTagGroup::class);
    }

    public function scopeForSite($query, $siteId)
    {
        return $query->whereIn('tag_groups.id', function ($q) use ($siteId) {
            $q->select('tag_group_id')
              ->from('site_tag_group')
              ->where('site_id', $siteId);
        });
    }

    public function getSiteTagGroupTree($siteId, $purpose = null, $depth = 2)
    {
        // サイトに紐づくトップレベルのタググループを取得
        $groups = self::whereNull('parent_id')
            ->whereIn('id', function ($query) use ($siteId) {
                $query->select('tag_group_id')
                    ->from('site_tag_group')
                    ->where('site_id', $siteId);
            });
    
        // 共通のタグ取得クロージャ
        $tagQuery = function ($query) use ($purpose) {
            if (!is_null($purpose)) {
                $query->where('purpose', $purpose);
            }
            $query->orderBy('name');
        };
    
        // 再帰的に eager load 用の配列を生成するローカル関数
        $buildChildrenWith = function ($currentDepth) use (&$buildChildrenWith, $tagQuery) {
            if ($currentDepth <= 0) {
                return [];
            }
            return [
                'parent',
                'tags' => $tagQuery,
                'children' => function ($query) use ($currentDepth, $buildChildrenWith) {
                    $query->with($buildChildrenWith($currentDepth - 1));
                }
            ];
        };
    
        // eager load の設定。トップレベルは別途指定
        $withRelations = [
            'parent',
            'tags' => $tagQuery,
            'children' => function ($query) use ($depth, $buildChildrenWith) {
                $query->with($buildChildrenWith($depth - 1));
            }
        ];
    
        return $groups->with($withRelations)
            ->orderBy('order')
            ->get();
    }
}
