<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * タググループ Model
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $purpose
 * @property string|null $color
 * @property string|null $icon
 * @property int|null $parent_id
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, TagGroup> $children
 * @property-read int|null $children_count
 * @property-read mixed $breadcrumb
 * @property-read TagGroup|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SiteTagGroup> $siteTagGroups
 * @property-read int|null $site_tag_groups_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Site> $sites
 * @property-read int|null $sites_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $tags
 * @property-read int|null $tags_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagGroup forSite($siteId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagGroup onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagGroup whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagGroup whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagGroup whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagGroup whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagGroup whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagGroup whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagGroup wherePurpose($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagGroup whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagGroup whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagGroup withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagGroup withoutTrashed()
 * @property-read mixed $breadcrumb_w_o_self
 * @mixin \Eloquent
 */
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

    public function getBreadcrumbWOSelfAttribute()
    {
        $names = [];
        $group = $this;
        while ($group) {
            array_unshift($names, $group->name);
            $group = $group->parent;
        }
        array_pop($names);
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

    public static function getSiteTagGroupTree($siteId, $purpose = null, $depth = 2)
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

    public static function setPostCountsForTree($groups, int $siteId, string $purpose = 'public'): void
    {
        foreach ($groups as $group) {
            // eager loaded タグコレクションから purpose をフィルタ
            $tagIds = $group->tags
                ->where('purpose', $purpose)
                ->pluck('id');

            // 投稿件数を取得
            $group->post_count = $tagIds->isEmpty()
                ? 0
                : Post::whereHas('tags', fn ($q) => $q->whereIn('tags.id', $tagIds))
                    ->where('site_id', $siteId)
                    ->published()
                    ->count();

            // 子にも再帰適用
            if ($group->relationLoaded('children') && $group->children->isNotEmpty()) {
                self::setPostCountsForTree($group->children, $siteId, $purpose);
            }
        }
    }
}
