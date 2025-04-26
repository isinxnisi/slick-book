<?php
namespace Modules\ContentModule\Domain\Repositories;

use Modules\ContentModule\Domain\Entities\ContentEntity;

interface ContentRepositoryInterface
{
    public function save(ContentEntity $entity): ContentEntity;

    /** 一覧取得。filters 例: ['type'=>'slot','kind'=>'series'] */
    public function all(array $filters = []): array;

    /** ID 指定で取得。見つからなければ例外 */
    public function find(int $id): ContentEntity;

    public function delete(int $id): void;
}
