<?php

namespace Modules\ContentModule\Application\DTOs;

use DateTimeImmutable;

class ContentData
{
    public function __construct(
        public ?int $id,
        public ?string $scope_key,
        public string $title,
        public string $slug,
        public string $content_type,
        public string $content_kind,
        public ?string $body,
        public array $meta,
        public string $status,
        public ?DateTimeImmutable $published_at,
        public ?int $created_by,
        public ?int $updated_by,
        public DateTimeImmutable $created_at,
        public DateTimeImmutable $updated_at,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['scope_key'] ?? null,
            $data['title'] ?? '',
            $data['slug'] ?? '',
            $data['content_type'] ?? '',
            $data['content_kind'] ?? '',
            $data['body']  ?? null,
            $data['meta']  ?? [],
            $data['status'] ?? 'draft',
            isset($data['published_at'])
                ? new DateTimeImmutable($data['published_at'])
                : null,
            $data['created_by'] ?? null,
            $data['updated_by'] ?? null,
            new DateTimeImmutable($data['created_at'] ?? date('c')),
            new DateTimeImmutable($data['updated_at'] ?? date('c')),
        );
    }

    public function toArray(): array
    {
        return [
            'id'           => $this->id,
            'scope_key'    => $this->scope_key,
            'title'        => $this->title,
            'slug'         => $this->slug,
            'content_type' => $this->content_type,
            'content_kind' => $this->content_kind,
            'body'         => $this->body,
            'meta'         => $this->meta,
            'status'       => $this->status,
            'published_at' => $this->published_at?->format('Y-m-d H:i:s'),
            'created_by'   => $this->created_by,
            'updated_by'   => $this->updated_by,
            'created_at'   => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at'   => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * メタ値取得
     */
    public function getMetaValue(string $key): mixed
    {
        return $this->meta[$key] ?? null;
    }
}
