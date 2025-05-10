<?php

namespace Modules\ContentModule\Core\Infrastructure\Eloquent\Repositories;

use Illuminate\Support\Facades\DB;
use Modules\ContentModule\Core\Domain\Entities\ContentEntity;
use Modules\ContentModule\Core\Domain\Repositories\TaxonomySyncServiceInterface;

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
            ->where('content_id', $content->getId())
            ->delete();

        // 新規に挿入
        $insertData = [];
        foreach ($termIds as $index => $termId) {
            $insertData[] = [
                'content_id'        => $content->getId(),
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
