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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxonomyTerm newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxonomyTerm newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxonomyTerm query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxonomyTerm whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxonomyTerm whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxonomyTerm whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxonomyTerm whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxonomyTerm whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxonomyTerm whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxonomyTerm whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxonomyTerm whereTaxonomyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxonomyTerm whereUpdatedAt($value)
 * @property bool $is_public
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxonomyTerm whereIsPublic($value)
 * @mixin \Eloquent
 */
class TaxonomyTerm extends Model
{
    protected $table = 'taxonomy_terms';

    protected $fillable = [
        'taxonomy_id',
        'name',
        'slug',
        'parent_id',
        'order',
        'description',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    public function taxonomy()
    {
        return $this->belongsTo(Taxonomy::class);
    }

    public function scopeVisible($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeForTaxonomy($query, $slug)
    {
        return $query->whereHas('taxonomy', fn($q) => $q->where('slug', $slug));
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')
            ->orderBy('order')
            ->with('children');
    }

    public function childrenRecursive()
    {
        return $this->hasMany(self::class, 'parent_id')
            ->orderBy('order')
            ->with('childrenRecursive');
    }

    public function getBreadcrumbAttribute(): string
    {
        $terms = [];
        $term = $this;
        while ($term) {
            array_unshift($terms, $term->name);
            $term = $term->parent;
        }
        return implode(' > ', $terms);
    }

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_taxonomy_term')
            ->withPivot('order');
    }

    public function getDescendantIdsAttribute()
    {
        return collect([$this->id])->merge(
            $this->childrenRecursive->flatMap(fn($child) => $child->descendant_ids)
        );
    }

    public function thumbnail()
    {
        return $this->hasOne(SiteImage::class, 'taxonomy_term_id')
            ->where('type', 'taxonomy_term_thumbnail')
            ->where('is_public', true)
            ->orderBy('order');
    }

    public static function setPostCountsForTree($terms, int $siteId): void
    {
        foreach ($terms as $term) {
            $termIds = $term->descendant_ids;

            $term->post_count = Post::whereHas('taxonomyTerms', function ($q) use ($termIds) {
                $q->whereIn('taxonomy_term_id', $termIds);
            })->where('site_id', $siteId)->published()->count();

            if ($term->relationLoaded('children') && $term->children->isNotEmpty()) {
                self::setPostCountsForTree($term->children, $siteId);
            }
        }
    }

}
