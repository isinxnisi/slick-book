<?php

namespace Modules\ContentModule\Domain\Entities;

use Modules\ContentModule\Infrastructure\Eloquent\Models\ContentModel;

class ContentEntity
{
    public function __construct(
        public int    $id,
        public int    $site_id,
        public string $title,
        public string $slug,
        public string $content_type,
        public string $status,
        public ?string $summary,
        public ?string $thumbnail_path,
        public ?\DateTime $published_at,
        public ?int   $created_by,
        public ?int   $updated_by,
    ) {}

    /**
     * DTO から生成
     */
    public static function fromData(object $data): self
    {
        return new self(
            0,
            $data->site_id,
            $data->title,
            $data->slug,
            $data->content_type,
            $data->status ?? 'draft',
            $data->summary  ?? null,
            $data->thumbnail_path ?? null,
            isset($data->published_at) ? new \DateTime($data->published_at) : null,
            $data->created_by  ?? null,
            $data->updated_by  ?? null,
        );
    }

    /**
     * Eloquent モデルから生成
     */
    public static function fromModel(ContentModel $model): self
    {
        return new self(
            $model->id,
            $model->site_id,
            $model->title,
            $model->slug,
            $model->content_type,
            $model->status,
            $model->summary,
            $model->thumbnail_path,
            $model->published_at?->toDateTime() ?? null,
            $model->created_by,
            $model->updated_by,
        );
    }

    /**
     * 配列化（リポジトリで updateOrCreate 用）
     */
    public function toArray(): array
    {
        return [
            'site_id'        => $this->site_id,
            'title'          => $this->title,
            'slug'           => $this->slug,
            'content_type'   => $this->content_type,
            'status'         => $this->status,
            'summary'        => $this->summary,
            'thumbnail_path' => $this->thumbnail_path,
            'published_at'   => $this->published_at?->format('Y-m-d H:i:s'),
            'created_by'     => $this->created_by,
            'updated_by'     => $this->updated_by,
        ];
    }
}
