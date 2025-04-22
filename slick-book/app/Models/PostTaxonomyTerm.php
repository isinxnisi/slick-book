<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 記事タクソノミー Model
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostTaxonomyTerm newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostTaxonomyTerm newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostTaxonomyTerm query()
 * @mixin \Eloquent
 */
class PostTaxonomyTerm extends Model
{
    protected $fillable = [
        'post_id',
        'taxonomy_term_id',
        'order',
    ];
}
