<?php

namespace Modules\ContentModule\Infrastructure\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
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
        // 基本バリデーション
        $data = $request->validate([
            'type' => ['required','string', Rule::in(array_keys(config('content.types')))],
            'kind' => ['required','string', function($attr,$value,$fail){
                $mapping = config('meta_schema.mapping');
                $type = request('type','');
                if (! isset($mapping["{$type}.{$value}"])) {
                    $fail('無効な種別です。');
                }
            }],
            'set'  => ['nullable','string', Rule::in(array_keys(config('meta_schema.sets'))), function($attr,$value,$fail){
                $mapping = config('meta_schema.mapping');
                $type = request('type','');
                $kind = request('kind','');
                $allowed = $mapping["{$type}.{$kind}"] ?? $mapping['default'];
                if ($value !== null && ! in_array($value, $allowed, true)) {
                    $fail('このスキーマセットは利用できません。');
                }
            }],
        ]);

        // TYPE×KIND の組み合わせチェック
        $mapping = config('meta_schema.mapping');
        $key     = "{$data['type']}.{$data['kind']}";
        if (! isset($mapping[$key])) {
            abort(404, '該当するフォーム定義が見つかりません。');
        }

        // スキーマセットチェック
        $allowed = $mapping[$key] ?? $mapping['default'];
        if (isset($data['set']) && ! in_array($data['set'], $allowed, true)) {
            abort(404, '該当するスキーマセットが見つかりません。');
        }

        // DTO／Entity 化
        $dto      = ContentData::fromArray([
            'content_type' => $data['type'],
            'content_kind' => $data['kind'],
            'title'        => '',
            'slug'         => '',
            'body'         => null,
            'meta'         => [],
            'status'       => 'draft',
        ]);
        $entity   = ContentEntity::fromData($dto);

        // フォーム生成
        $strategy = $this->service->resolveStrategy($data['type'], $data['kind']);
        $html     = $strategy->renderFormFields($entity);

        return response($html, 200);
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

        try {
            $this->service->create($dto, []);
            return redirect()->route('admin.contents.index');
        } catch (ValidationException $e) {
            return redirect()
                ->back()
                ->withErrors($e->validator)
                ->withInput();
        }
    }

    /**
     * 更新処理
     */
    public function update(UpdateContentRequest $request, int $id): RedirectResponse
    {
        $data = $request->validated();
        $data['id'] = $id;
        $dto = ContentData::fromArray($data);

        try {
            $this->service->update($id, $dto, []);
            return redirect()->route('admin.contents.index');
        } catch (ValidationException $e) {
            return redirect()
                ->back()
                ->withErrors($e->validator)
                ->withInput();
        }
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
        $this->service->toReview($id);
        return redirect()->back()
                         ->with('status', 'レビューを申請しました');
    }

    /**
     * 公開
     */
    public function publish(int $id): RedirectResponse
    {
        $entity = $this->service->get($id);
        $this->service->publish($id);
        return redirect()->back()
                         ->with('status', '公開しました');
    }

    /**
     * アーカイブ
     */
    public function archive(int $id): RedirectResponse
    {
        $entity = $this->service->get($id);
        $this->service->archive($id);
        return redirect()->back()
                         ->with('status', 'アーカイブしました');
    }
}
