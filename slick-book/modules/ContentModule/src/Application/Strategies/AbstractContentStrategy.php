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

    /** @var iterable<ContentStrategyInterface> */
    protected iterable $strategies;

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

    public function setStrategies(iterable $strategies): void
    {
        $this->strategies = $strategies;
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

    protected function getStrategy(string $type, string $kind): ContentStrategyInterface
    {
        foreach ($this->strategies as $s) {
            if ($s->supportsType() === $type && $s->supportsKind() === $kind) {
                return $s;
            }
        }
        throw new \RuntimeException("No strategy for type={$type}, kind={$kind}");
    }

    /**
     * TYPE×KINDに応じたスキーマセットからフィールド定義を取得
     *
     * @return array フィールド定義の配列（order 昇順ソート済み）
     */
    protected function getMetaFields(string $type, string $kind, ?string $overrideSet = null): array
    {
        // mapping と schemas を取得
        $mapping = config('meta_schema.mapping');
        $schemas = config('meta_schema.schemas');

        // 各セット
        if ($overrideSet && isset($schemas[$overrideSet])) {
            $sets = [$overrideSet];
        } else {
            // 対象キー
            $key  = "{$type}.{$kind}";
            $sets = $mapping[$key] ?? $mapping['default'];
        }

        // 各セットの fields をマージ
        $fields = [];
        foreach ($sets as $set) {
            if (! empty($schemas[$set]['fields'])) {
                $fields = array_merge($fields, $schemas[$set]['fields']);
            }
        }

        // order キーでソート
        usort($fields, fn($a, $b) => $a['order'] <=> $b['order']);
        return $fields;
    }

    /**
     * フォーム部品をレンダリング
     */
    public function renderFormFields(?ContentEntity $entity = null, ?string $schemaSet = null): string
    {
        $type   = $entity?->getContentType() ?? '';
        $kind   = $entity?->getContentKind() ?? '';
        $fields = $this->getMetaFields($type, $kind, $schemaSet);

        // ベースパスとビュー名の組み立て
        $viewBase   = 'content-module::admin.contents.forms.';
        $customView = ($type && $kind)
            ? "{$viewBase}{$type}_{$kind}"
            : "{$viewBase}_base";

        // カスタムビューが存在しなければ _base にフォールバック
        $viewName   = view()->exists($customView)
            ? $customView
            : "{$viewBase}_base";

        return view($viewName, compact('entity', 'fields'))->render();
    }
}
