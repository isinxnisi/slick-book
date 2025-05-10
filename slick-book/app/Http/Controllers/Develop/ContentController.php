<?php

namespace App\Http\Controllers\Develop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Modules\ContentModule\Application\Services\ContentService;
use Modules\ContentModule\Application\DTOs\ContentData;
use Modules\ContentModule\Application\Strategies\LayoutSeriesStrategy;
use Modules\ContentModule\Domain\Entities\ContentEntity;

class ContentController extends Controller
{
    /**
     * テスト用コンテンツ作成・フォーム描画確認
     */
    public function index(
        ContentService $service,
        LayoutSeriesStrategy $strategy
    ): Response {
        // DTO を配列から生成
        $dto = ContentData::fromArray([
            'site_id'      => 1,
            'title'        => 'テストシリーズ',
            'slug'         => 'series-test-' . time(),
            'content_type' => 'layout',
            'content_kind' => 'series',
            'body'         => null,
            'meta'         => ['order' => 1],
            'status'       => 'draft',
            'published_at' => null,
            'created_by'   => null,
            'updated_by'   => null,
        ]);

        // 永続化はせず、Entity だけ生成してフォーム描画テスト
        $entity = ContentEntity::fromData($dto);

        // Blade フォーム出力を取得
        $html = $strategy->renderFormFields($entity);

        // HTML をそのまま返す
        return response($html);
    }
}
