<?php

namespace Modules\ContentModule\Application\Strategies;

use Modules\ContentModule\Domain\Contracts\ContentStrategyInterface;
use Modules\ContentModule\Domain\Entities\ContentEntity;
use Modules\ContentModule\Domain\Repositories\ContentRepositoryInterface;

class LayoutSeriesStrategy implements ContentStrategyInterface
{
    public function __construct(
        private ContentRepositoryInterface $repository
    ) {}

    public function supportsType(): string
    {
        return 'layout';
    }

    public function supportsKind(): string
    {
        return 'series';
    }

    public function validate(array $data): array
    {
        return validator($data, [
            'site_id'       => 'required|integer',
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
        return $this->repository->save($entity);
    }

    public function renderFormFields(?ContentEntity $entity = null): string
    {
        return view('content-module::admin.contents.forms.layout_series', [
            'entity' => $entity,
        ])->render();
    }
}
