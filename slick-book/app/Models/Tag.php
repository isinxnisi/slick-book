<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $color
 * @property string|null $icon
 * @property string|null $image_path
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string $purpose
 * @property-read Tag|null $canonical
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Post> $posts
 * @property-read int|null $posts_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Tag> $synonyms
 * @property-read int|null $synonyms_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TagGroup> $tagGroups
 * @property-read int|null $tag_groups_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag forSite($siteId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag wherePurpose($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag withoutTrashed()
 * @mixin \Eloquent
 */
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
