<?php

namespace Modules\ContentModule\Domain\Entities;

use Modules\ContentModule\Application\DTOs\ContentData;
use Modules\ContentModule\Infrastructure\Eloquent\Models\ContentModel;
use DateTimeImmutable;

class ContentEntity
{
    private ContentData $data;

    public function __construct(ContentData $data)
    {
        $this->data = $data;
    }

    /**
     * DTO から直接エンティティを生成する
     */
    public static function fromData(ContentData $data): self
    {
        return new self($data);
    }

    /**
     * 配列データからエンティティを生成する
     */
    public static function fromArray(array $data): self
    {
        return new self(ContentData::fromArray($data));
    }

    /**
     * Eloquent モデルからエンティティを生成する
     */
    public static function fromModel(ContentModel $model): self
    {
        return new self(ContentData::fromArray($model->toArray()));
    }

    /**
     * エンティティを配列化する
     */
    public function toArray(): array
    {
        return $this->data->toArray();
    }

    public function getId(): ?int
    {
        return $this->data->id;
    }

    public function getScopeKey(): ?string
    {
        return $this->data->scope_key;
    }

    public function getTitle(): string
    {
        return $this->data->title;
    }

    public function getSlug(): string
    {
        return $this->data->slug;
    }

    public function getContentType(): string
    {
        return $this->data->content_type;
    }

    public function getContentKind(): string
    {
        return $this->data->content_kind;
    }

    public function getBody(): ?string
    {
        return $this->data->body;
    }

    public function getMeta(): array
    {
        return $this->data->meta;
    }

    public function getStatus(): string
    {
        return $this->data->status;
    }

    public function getPublishedAt(): ?DateTimeImmutable
    {
        return $this->data->published_at;
    }

    public function getCreatedBy(): ?int
    {
        return $this->data->created_by;
    }

    public function getUpdatedBy(): ?int
    {
        return $this->data->updated_by;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->data->created_at;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->data->updated_at;
    }
}
