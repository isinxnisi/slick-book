<?php

namespace Modules\ContentModule\Infrastructure\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;

class ContentModel extends Model
{
    protected $table = 'contents';

    protected $fillable = [
        'site_id',
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
        'meta'         => AsArrayObject::class,
        'published_at' => 'datetime',
    ];
}
