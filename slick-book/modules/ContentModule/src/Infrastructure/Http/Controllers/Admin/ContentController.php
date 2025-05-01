<?php

namespace Modules\ContentModule\Infrastructure\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Modules\ContentModule\Application\Services\ContentService;
use Modules\ContentModule\Application\DTOs\ContentData;
use Modules\ContentModule\Domain\Entities\ContentEntity;
use Modules\ContentModule\Infrastructure\Http\Requests\StoreContentRequest;
use Modules\ContentModule\Infrastructure\Http\Requests\UpdateContentRequest;

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
        $kinds = config('content.kinds');

        return response()->view(
            'content-module::admin.contents.index',
            compact('items','types','kinds','type','kind')
        );
    }

    /**
     * フォーム描画（Ajax 用）
     */
    public function formFields(Request $request): Response
    {
        $type = $request->query('type', '');
        $kind = $request->query('kind', '');

        // DTO の生成（デフォルト値 + パラメータ）
        $dto = ContentData::fromArray(array_merge([
            'scope_key'    => null,
            'title'        => '',
            'slug'         => '',
            'content_type' => $type,
            'content_kind' => $kind,
            'body'         => null,
            'meta'         => [],
            'status'       => 'draft',
            'published_at' => null,
            'created_by'   => null,
            'updated_by'   => null,
            'created_at'   => now()->format('Y-m-d H:i:s'),
            'updated_at'   => now()->format('Y-m-d H:i:s'),
        ], $request->only(['type','kind'])));

        $entity = ContentEntity::fromData($dto);

        try {
            // Strategy がなければ例外を投げる
            $strategy = $this->service->resolveStrategy($type, $kind);
            $html = $strategy->renderFormFields($entity);
        } catch (\RuntimeException $e) {
            // フォールバック：空文字 or 汎用フォーム
            $html = '';
        }

        return response($html);
    }

    /**
     * 新規作成画面
     */
    public function create(Request $request): Response
    {
        $types = config('content.types');
        $kinds = config('content.kinds');

        // デフォルト TYPE/KIND
        $type = $request->query('type', array_key_first($types));
        $kind = $request->query('kind', array_key_first($kinds));

        // old() とデフォルト値をマージして DTO → Entity 化
        $defaults = [
            'content_type' => $type,
            'content_kind' => $kind,
            'title'        => '',
            'slug'         => '',
            'body'         => '',
            'meta'         => [],
            // 必要なら status, published_at なども…
        ];
        $input  = array_merge($defaults, $request->old() ?: []);
        $dto    = ContentData::fromArray($input);
        $entity = ContentEntity::fromData($dto);

        // Strategy の取得
        $strategy = $this->service->resolveStrategy($type, $kind);

        return response()->view(
            'content-module::admin.contents.form',
            compact('types','kinds','entity','strategy')
        );
    }


    /**
     * 編集画面
     */
    public function edit(int $id, Request $request): Response
    {
        $types = config('content.types');
        $kinds = config('content.kinds');

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

        return response()->view(
            'content-module::admin.contents.form',
            compact('types','kinds','entity','strategy')
        );
    }

    /**
     * 保存
     */
    public function store(StoreContentRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $dto  = ContentData::fromArray($data);
        $this->service->create($dto, []);
        return redirect()->route('admin.contents.index');
    }

    /**
     * 更新処理
     */
    public function update(UpdateContentRequest $request, int $id): RedirectResponse
    {
        $data = $request->validated();
        $data['id'] = $id;
        $dto = ContentData::fromArray($data);
        $this->service->update($id, $dto);
        return redirect()->route('admin.contents.index');
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
}
