<?php

namespace Modules\ContentModule\Core\Infrastructure\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ContentModel extends Model
{
    protected $table = 'contents';

    protected $fillable = [
        'scope_key',
        'title',
        'slug',
        'content_type',
        'content_kind',
        'body',
        'meta',
        'status',
        'published_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'meta'         => 'array',
        'published_at' => 'datetime',
    ];

    /**
     * taxonomy_terms テーブルとの多対多リレーション
     */
    public function taxonomyTerms(): BelongsToMany
    {
        return $this->belongsToMany(
            TaxonomyTermModel::class,
            'content_taxonomy_term',    // pivot table
            'content_id',               // this model's foreign key
            'taxonomy_term_id'          // related model's foreign key
        );
    }
}
