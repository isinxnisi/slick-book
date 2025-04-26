<?php

namespace App\Http\Controllers\Develop;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\ContentModule\Application\Services\ContentService;
use Modules\ContentModule\Application\DTOs\ContentData;

class ContentController extends Controller
{
    /**
     * テスト用コンテンツ作成・取得
     */
    public function index(ContentService $service): JsonResponse
    {
        // DTO を配列から生成
        $dto = ContentData::fromArray([
            'site_id'      => 1,
            'title'        => 'テストコンテンツ',
            'slug'         => 'test-content-' . time(),
            'content_type' => 'slot',
            'content_kind' => 'article',
            'body'         => null,
            'meta'         => [],
            'status'       => 'draft',
            'published_at' => null,
            'created_by'   => null,
            'updated_by'   => null,
        ]);

        // ContentService で保存・取得
        $entity = $service->create($dto, []);

        // エンティティを配列化して JSON レスポンス
        return response()->json($entity->toArray());
    }
}
