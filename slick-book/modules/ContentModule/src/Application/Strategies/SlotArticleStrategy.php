<?php

namespace Modules\ContentModule\Application\Strategies;

use Modules\ContentModule\Domain\Contracts\ContentStrategyInterface;
use Modules\ContentModule\Domain\Entities\ContentEntity;
use Modules\ContentModule\Domain\Repositories\ContentRepositoryInterface;

class SlotArticleStrategy implements ContentStrategyInterface
{
    public function __construct(
        private ContentRepositoryInterface $repository
    ) {}

    public function supportsType(): string
    {
        return 'slot';
    }

    public function supportsKind(): string
    {
        return 'article';
    }

    public function validate(array $data): array
    {
        return validator($data, [
            'id'            => 'nullable|integer|exists:contents,id',
            'scope_key'     => 'nullable|string|max:64',
            'title'         => 'required|string|max:255',
            'slug'          => 'required|string|max:255|unique:contents,slug,' . ($data['id'] ?? 'NULL'),
            'body'          => 'nullable|string',
            'meta'          => 'array',
            'content_type'  => 'required|string',
            'content_kind'  => 'required|string',
            'status'        => 'required|in:draft,published,scheduled',
            'published_at'  => 'nullable|date_format:Y-m-d H:i:s',
        ])->validate();
    }

    public function save(ContentEntity $entity): ContentEntity
    {
        // リポジトリ経由で永続化
        return $this->repository->save($entity);
    }

    public function renderFormFields(?ContentEntity $entity = null): string
    {
        // Blade パーシャルをレンダー
        return view('content-module::admin.contents.forms.slot_article', [
            'entity' => $entity,
        ])->render();
    }
}
