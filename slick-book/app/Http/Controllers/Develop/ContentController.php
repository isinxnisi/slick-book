<?php
namespace App\Http\Controllers\Develop;

use App\Http\Controllers\Controller;
use Modules\ContentModule\Domain\Repositories\ContentRepositoryInterface;

use function Psy\debug;

class ContentController extends Controller
{
    /**
     * Index
     */
    public function index(\Modules\ContentModule\Application\Services\ContentService $service)
    {
        // ダミーデータ
        $dto = \Modules\ContentModule\Application\DTOs\ContentData::fromArray([
            'site_id'      => 1,
            'title'        => 'テストコンテンツ',
            'slug'         => 'test-content-' . time(),
            'content_type' => 'article',
            'status'       => 'draft',
            'summary'      => 'テスト用の概要',
            'thumbnail_path' => null,
            'published_at' => null,
            'created_by'   => null,
            'updated_by'   => null,
        ]);

        $entity = $service->create($dto, []);

        return response()->json($entity);
    }

}
