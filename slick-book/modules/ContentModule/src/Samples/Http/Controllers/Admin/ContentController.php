<?php

namespace Modules\ContentModule\Samples\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Modules\ContentModule\Core\Application\Services\ContentService;
use Modules\ContentModule\Core\Application\DTOs\ContentData;
use Modules\ContentModule\Core\Domain\Entities\ContentEntity;
use Modules\ContentModule\Samples\Http\Requests\StoreContentRequest;
use Modules\ContentModule\Samples\Http\Requests\UpdateContentRequest;

class ContentController extends Controller
{
    private ContentService $service;

    public function __construct(ContentService $service)
    {
        $this->service = $service;
    }

    /**
     * 一覧表示
     */
    public function index(Request $request): Response
    {
        $type  = $request->query('type', array_key_first(config('content.types')));
        $kind  = $request->query('kind');

        $items = $this->service->list($type, $kind);
        $types = config('content.types');
        // TYPEに紐づくKINDだけ取得
        $kinds = config("content.kinds.{$type}", []);

        return response()->view(
            'content-module::admin.contents.index',
            compact('items', 'types', 'kinds', 'type', 'kind')
        );
    }

    /**
     * フォーム描画（Ajax 用）
     */
    public function formFields(Request $request): Response
    {
        // Ajax エンドポイントも Strategy で切り替えて返す
        $type     = $request->validate([ 'type'=>'required|string', 'kind'=>'required|string' ])['type'];
        $kind     = $request->input('kind');
        $dto      = ContentData::fromArray([
            'content_type' => $type,
            'content_kind' => $kind,
            'title'        => '',
            'slug'         => '',
            'body'         => null,
            'meta'         => [],
            'status'       => 'draft',
        ]);
        $entity   = ContentEntity::fromData($dto);

        $strategy = $this->service->resolveStrategy($type, $kind);
        return response($strategy->renderFormFields($entity));
    }

    /**
     * 新規作成画面
     */
    public function create(Request $request): Response
    {
        $type  = $request->query('type', array_key_first(config('content.types')));

        $types = config('content.types');
        $kinds = config("content.kinds.{$type}", []);
        $kind  = array_key_first($kinds);

        // old() とデフォルト値をマージして DTO → Entity 化
        $defaults = [
            'content_type' => $type,
            'content_kind' => $kind,
            'title'        => '',
            'slug'         => '',
            'body'         => '',
            'meta'         => [],
            'status'       => 'draft',
        ];
        $input  = array_merge($defaults, $request->old() ?: []);
        $dto    = ContentData::fromArray($input);
        $entity = ContentEntity::fromData($dto);

        // Strategy の取得
        $strategy = $this->service->resolveStrategy($type, $kind);

        // Strategy 毎に切り替わるフォーム HTML を取得
        $formHtml = $strategy->renderFormFields($entity);

        return response()->view(
            'content-module::admin.contents.form',
            compact('types', 'kinds', 'type', 'kind', 'entity', 'formHtml')
        );
    }


    /**
     * 編集画面
     */
    public function edit(int $id, Request $request): Response
    {
        // 永続化済みデータを取得
        $originalEntity = $this->service->get($id);
        $original      = $originalEntity->toArray();

        // old() があればマージ
        $input  = array_merge($original, $request->old() ?: []);
        $dto    = ContentData::fromArray($input);
        $entity = ContentEntity::fromData($dto);

        $type     = $entity->getContentType();
        $kind     = $entity->getContentKind();
        $strategy = $this->service->resolveStrategy($type, $kind);

        $types = config('content.types');
        $kinds = config("content.kinds.{$type}", []);

        // Strategy 毎に切り替わるフォーム HTML を取得
        $formHtml = $strategy->renderFormFields($entity);

        return response()->view(
            'content-module::admin.contents.form',
            compact('types', 'kinds', 'type', 'kind', 'entity', 'formHtml')
        );
    }

    /**
     * 保存
     */
    public function store(StoreContentRequest $request): RedirectResponse
    {
        // TYPE と KIND はフォームの hidden input から取得
        $type = $request->input('content_type');
        $kind = $request->input('content_kind');

        // バリデート済みデータを取得
        $data = $request->validated();
        $data['status'] = 'draft';

        // 作成または更新を一括で処理
        $this->service->handleSave($type, $kind, $data);

        return redirect()
            ->route('admin.contents.index', ['type' => $type, 'kind' => $kind])
            ->with('status', 'コンテンツを保存しました');
    }

    /**
     * 更新処理
     */
    public function update(UpdateContentRequest $request, int $id): RedirectResponse
    {
        // TYPE と KIND はフォームの hidden input から取得
        $type = $request->input('content_type');
        $kind = $request->input('content_kind');
        $originalEntity = $this->service->get($id);

        // バリデート済みデータを取得
        $data = $request->validated();
        $data['id'] = $id;
        $data['status'] = $originalEntity->getStatus();

        // 作成または更新を一括で処理
        $this->service->handleSave($type, $kind, $data);

        return redirect()
            ->route('admin.contents.index', ['type' => $type, 'kind' => $kind])
            ->with('status', 'コンテンツを更新しました');
    }

    /**
     * 削除処理
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->service->delete($id);
        return redirect()->route('admin.contents.index')
            ->with('status', '削除しました');
    }

    /**
     * レビュー申請
     */
    public function toReview(int $id): RedirectResponse
    {
        $entity = $this->service->get($id);
        $this->service->applyTransition($id, 'to_review');
        return redirect()->back()
            ->with('status', 'レビューを申請しました');
    }

    /**
     * 公開
     */
    public function publish(int $id): RedirectResponse
    {
        $entity = $this->service->get($id);
        $this->service->applyTransition($id, 'publish');
        return redirect()->back()
            ->with('status', '公開しました');
    }

    /**
     * アーカイブ
     */
    public function archive(int $id): RedirectResponse
    {
        $entity = $this->service->get($id);
        $this->service->applyTransition($id, 'archive');
        return redirect()->back()
            ->with('status', 'アーカイブしました');
    }
}
