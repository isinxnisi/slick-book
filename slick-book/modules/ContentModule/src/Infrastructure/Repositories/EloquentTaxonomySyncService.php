<?php
namespace Modules\ContentModule\Infrastructure\Repositories;

use Illuminate\Support\Facades\DB;
use Modules\ContentModule\Domain\Entities\ContentEntity;
use Modules\ContentModule\Domain\Repositories\TaxonomySyncServiceInterface;

class EloquentTaxonomySyncService implements TaxonomySyncServiceInterface
{
    /**
     * @param ContentEntity $content
     * @param int[] $termIds
     */
    public function sync(ContentEntity $content, array $termIds): void
    {
        // 既存の紐付けを削除
        DB::table('content_taxonomy_term')
            ->where('content_id', $content->id)
            ->delete();

        // 新規に挿入
        $insertData = [];
        foreach ($termIds as $index => $termId) {
            $insertData[] = [
                'content_id'        => $content->id,
                'taxonomy_term_id'  => $termId,
                'sort_order'        => $index,
                'created_at'        => now(),
                'updated_at'        => now(),
            ];
        }
        if (!empty($insertData)) {
            DB::table('content_taxonomy_term')->insert($insertData);
        }
    }
}
