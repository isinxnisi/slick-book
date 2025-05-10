<?php

namespace Modules\ContentModule\Core\Application\Services;

use Illuminate\Support\Facades\Auth;
use Symfony\Component\Workflow\WorkflowInterface;
use Modules\ContentModule\Core\Application\DTOs\ContentData;
use Modules\ContentModule\Core\Domain\Contracts\ContentStrategyInterface;
use Modules\ContentModule\Core\Domain\Repositories\ContentRepositoryInterface;
use Modules\ContentModule\Core\Domain\Entities\ContentEntity;
use Modules\ContentModule\Core\DSL\DslRegistry;
use Modules\ContentModule\Core\Events\ContentStateChanged;

class ContentService
{
    /** @var iterable<ContentStrategyInterface> */
    protected iterable $strategies;

    public function __construct(
        private ContentRepositoryInterface $repository,
        private WorkflowInterface $content,
        private DslRegistry $dslRegistry
    ) {}

    public function setStrategies(iterable $strategies): void
    {
        $this->strategies = $strategies;
    }

    public function findBySlug(string $slug): ?ContentEntity
    {
        return $this->repository->findBySlug($slug);
    }

    /**
     * Ajax 用にフォーム描画で直接戦略を取り出す
     *
     * @param string $type
     * @param string $kind
     * @return ContentStrategyInterface
     */
    public function resolveStrategy(string $type, string $kind): ContentStrategyInterface
    {
        // protected な getStrategy() を呼び出して返却
        return $this->getStrategy($type, $kind);
    }

    /** 新規作成・更新 */
    public function create(ContentData $data, array $taxonomyIds = []): ContentEntity
    {
        $data = $this->prepareData($data);
        $entity = ContentEntity::fromData($data);
        return $this->getStrategy($data->content_type, $data->content_kind)
            ->save($entity);
    }

    /**
     * 更新用メソッド
     */
    public function update(int $id, ContentData $data, array $taxonomyIds = []): ContentEntity
    {
        // ID をセット
        $data->id = $id;

        // バリデーション〜永続化
        $data = $this->prepareData($data);
        $entity = ContentEntity::fromData($data);
        return $this->getStrategy($data->content_type, $data->content_kind)
            ->save($entity);
    }

    /**
     * TYPE×KIND に合致するストラテジーを探す内部メソッド
     *
     * @throws \RuntimeException
     */
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
     * validate → DTO 再生成 の共通処理
     */
    protected function prepareData(ContentData $data): ContentData
    {
        $strategy  = $this->getStrategy($data->content_type, $data->content_kind);
        $validated = $strategy->validate($data->toArray());
        return ContentData::fromArray($validated);
    }

    /**
     * コンテンツ一覧を取得
     *
     * @param string      $type TYPE フィルタ
     * @param string|null $kind KIND フィルタ（任意）
     * @return ContentEntity[] 配列で返却
     */
    public function list(string $type, ?string $kind = null): array
    {
        $filters = ['type' => $type];
        if ($kind !== null) {
            $filters['kind'] = $kind;
        }

        // リポジトリが返す array<ContentEntity> をそのまま返却
        return $this->repository->all($filters);
    }

    /**
     * 単一コンテンツ取得
     *
     * @param int $id
     * @return ContentEntity
     */
    public function get(int $id): ContentEntity
    {
        return $this->repository->find($id);
    }

    /**
     * 削除
     *
     * @param int $id
     * @return ContentEntity
     */
    public function delete(int $id): void
    {
        $this->repository->delete($id);
    }

    public function applyTransition(int $id, string $transition): ContentEntity
    {
        $entity = $this->repository->find($id);
        if ($this->content->can($entity, $transition)) {
            $this->content->apply($entity, $transition);
            $this->repository->save($entity);
            // メソッド内ではジョブは dispatch せず、
            // イベントだけ fire
            event(new ContentStateChanged($id, $transition));
        }
        return $entity;
    }

    /**
     * TYPE×KIND からフォームセクションを取得
     * @return array 中間データ配列
     */
    public function renderFormFields(string $type, string $kind): array
    {
        // Registry から DslDefinition を取得
        $definition = $this->dslRegistry->get($type, $kind);
        // セクション情報を返す
        return $definition->getSections();
    }

    /**
     * 保存処理: TYPE×KIND とバリデート済データから Entity を生成して永続化
     * @param string $type
     * @param string $kind
     * @param array $data
     * @return ContentEntity
     */
    public function handleSave(string $type, string $kind, array $data): ContentEntity
    {
        // 1. Strategy を解決
        $strategy = $this->getStrategy($type, $kind);

        // 2. バリデーション（FormRequestでもチェック済ですが念のため）
        $validated = $strategy->validate($data);

        // 更新時は、元の Entity から meta を引き継ぎつつマージ
        if (! empty($validated['id'])) {
            $old = $this->repository->find($validated['id'])->getMeta();
            $validated['meta'] = array_merge($old, $validated['meta'] ?? []);
        }

        // 3. DTO を組み立て
        $dto = new ContentData(
            id: $validated['id']           ?? null,
            scope_key: $validated['scope_key']    ?? null,
            title: $validated['title'],
            slug: $validated['slug'],
            content_type: $type,
            content_kind: $kind,
            body: $validated['body']         ?? null,
            meta: $validated['meta']         ?? [],
            status: $validated['status']       ?? null,
            published_at: isset($validated['published_at'])
                ? new \DateTimeImmutable($validated['published_at'])
                : null,
            created_by: Auth::id(),
            updated_by: Auth::id(),
            created_at: new \DateTimeImmutable(),
            updated_at: new \DateTimeImmutable(),
        );

        // 4. Entity を生成して保存
        $entity = $strategy->save(new ContentEntity($dto));

        // 5. Workflowで状態遷移（status値→transitionマッピング）
        $config = config('workflow.content.auto_transitions', []);
        $status = $entity->getStatus();
        $transition = 'save'; // デフォルト
        if (isset($config[$status]) && $this->content->can($entity, $config[$status])) {
            $this->content->apply($entity, $config[$status]);
            $entity = $strategy->save($entity); // 遷移後の状態で再保存
            $transition = $config[$status];     // イベント名として使用
        }

        // 6. イベント発行（ContentStateChanged は (int $contentId, string $transition) を受け取る）
        event(new ContentStateChanged($entity->getId(), $transition));

        return $entity;
    }
}
