<?php

namespace Modules\ContentModule\Application\Strategies;

use Modules\ContentModule\Domain\Contracts\ContentStrategyInterface;
use Modules\ContentModule\Domain\Entities\ContentEntity;
use Modules\ContentModule\Domain\Repositories\ContentRepositoryInterface;

abstract class AbstractContentStrategy implements ContentStrategyInterface
{
    // クラス定数で TYPE／KIND を定義
    public const TYPE = '';
    public const KIND = '';

    public function __construct(
        protected ContentRepositoryInterface $repository
    ){}

    public function supportsType(): string
    {
        return static::TYPE;
    }

    public function supportsKind(): string
    {
        return static::KIND;
    }

    public function save(ContentEntity $entity): ContentEntity
    {
        return $this->repository->save($entity);
    }

    protected function baseRules(array $data): array
    {
        return [
            'id'           => 'nullable|integer|exists:contents,id',
            'scope_key'    => 'nullable|string|max:64',
            'title'        => 'required|string|max:255',
            'slug'         => 'required|string|max:255|unique:contents,slug,' . ($data['id'] ?? 'NULL'),
            'body'         => 'nullable|string',
            'meta'         => 'array',
            'content_type' => 'required|string',
            'content_kind' => 'required|string',
            'status'       => 'required|in:draft,published,scheduled',
            'published_at' => 'nullable|date_format:Y-m-d H:i:s',
        ];
    }

    public function validate(array $data): array
    {
        return validator($data, $this->baseRules($data))->validate();
    }
}
