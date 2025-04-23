<?php

namespace Modules\ContentModule\Infrastructure\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\ContentModule\Domain\Entities\ContentEntity;

class ContentModel extends Model
{
    /**
     * テーブル名
     */
    protected $table = 'contents';

    /**
     * 複数代入許可カラム
     */
    protected $fillable = [
        'site_id',
        'title',
        'slug',
        'content_type',
        'status',
        'summary',
        'thumbnail_path',
        'published_at',
        'created_by',
        'updated_by',
    ];

    /**
     * 型キャスト
     */
    protected $casts = [
        'published_at' => 'datetime',
    ];

    /**
     * Eloquent Model からドメインエンティティへ変換
     */
    public function toEntity(): ContentEntity
    {
        return ContentEntity::fromModel($this);
    }
}
