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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Taxonomies newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Taxonomies newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Taxonomies query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Taxonomies whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Taxonomies whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Taxonomies whereIsHierarchical($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Taxonomies whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Taxonomies wherePurpose($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Taxonomies whereSiteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Taxonomies whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Taxonomies whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Taxonomies whereUpdatedAt($value)
 * @property bool $is_public
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Taxonomies whereIsPublic($value)
 * @mixin \Eloquent
 */
class Taxonomies extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'type',
        'purpose',
        'is_hierarchical',
        'site_id',
    ];
}
