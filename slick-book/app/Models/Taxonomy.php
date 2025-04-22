<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * タクソノミー Model
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $type
 * @property string|null $purpose
 * @property bool $is_hierarchical
 * @property int|null $site_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Taxonomy newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Taxonomy newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Taxonomy query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Taxonomy whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Taxonomy whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Taxonomy whereIsHierarchical($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Taxonomy whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Taxonomy wherePurpose($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Taxonomy whereSiteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Taxonomy whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Taxonomy whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Taxonomy whereUpdatedAt($value)
 * @property bool $is_public
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Taxonomy whereIsPublic($value)
 * @mixin \Eloquent
 */
class Taxonomy extends Model
{
    protected $table = 'taxonomies';

    protected $fillable = [
        'name',
        'slug',
        'type',
        'purpose',
        'is_hierarchical',
        'site_id',
    ];

    public function scopeForSite($query, $siteId)
    {
        return $query->where('site_id', $siteId);
    }

}
