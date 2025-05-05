<?php

namespace Modules\ContentModule\Domain\Contracts;

use Modules\ContentModule\Domain\Entities\ContentEntity;

interface ContentStrategyInterface
{
    /**
     * このストラテジーが対応するコンテンツTYPEを返す
     * @return string
     */
    public function supportsType(): string;

    /**
     * このストラテジーが対応するコンテンツKINDを返す
     * @return string
     */
    public function supportsKind(): string;

    /**
     * 入力データのバリデーションと正規化を行い、配列で返す
     * @param array $data
     * @return array
     */
    public function validate(array $data): array;

    /**
     * ContentEntity を永続化し、保存後のエンティティを返す
     * @param ContentEntity $entity
     * @return ContentEntity
     */
    public function save(ContentEntity $entity): ContentEntity;

    /**
     * 管理画面側のフォームフィールドHTMLをレンダリングする
     * @param ContentEntity|null $entity
     * @return string
     */
    public function renderFormFields(?ContentEntity $entity = null): string;
}
