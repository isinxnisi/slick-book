<?php

namespace Modules\ContentModule\Core\Infrastructure\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TaxonomyTermModel extends Model
{
    protected $table = 'taxonomy_terms';

    protected $fillable = [
        'scope_key',
        'name',
        'slug',
    ];

    /**
     * contents テーブルとの多対多リレーション
     */
    public function contents(): BelongsToMany
    {
        return $this->belongsToMany(
            ContentModel::class,
            'content_taxonomy_term',
            'taxonomy_term_id',
            'content_id'
        );
    }
}
