<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * タクソノミー・ターム Model
 *
 * @property int $id
 * @property int $taxonomy_id
 * @property string $name
 * @property string $slug
 * @property int|null $parent_id
 * @property int $sort_order
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxonomyTerms newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxonomyTerms newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxonomyTerms query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxonomyTerms whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxonomyTerms whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxonomyTerms whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxonomyTerms whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxonomyTerms whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxonomyTerms whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxonomyTerms whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxonomyTerms whereTaxonomyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxonomyTerms whereUpdatedAt($value)
 * @property bool $is_public
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxonomyTerms whereIsPublic($value)
 * @mixin \Eloquent
 */
class TaxonomyTerms extends Model
{
    protected $fillable = [
        'taxonomy_id',
        'name',
        'slug',
        'parent_id',
        'order',
        'description',
        'is_public',
    ];
}
