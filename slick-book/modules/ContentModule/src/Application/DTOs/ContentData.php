<?php

namespace Modules\ContentModule\Application\DTOs;

class ContentData
{
    public function __construct(
        public int $site_id,
        public string $title,
        public string $slug,
        public string $content_type,
        public string $status,
        public ?string $summary,
        public ?string $thumbnail_path,
        public ?string $published_at,
        public ?int $created_by,
        public ?int $updated_by,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['site_id'],
            $data['title'],
            $data['slug'],
            $data['content_type'],
            $data['status'] ?? 'draft',
            $data['summary']  ?? null,
            $data['thumbnail_path'] ?? null,
            $data['published_at'] ?? null,
            $data['created_by']  ?? null,
            $data['updated_by']  ?? null,
        );
    }
}
