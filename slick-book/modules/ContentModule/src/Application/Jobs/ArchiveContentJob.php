<?php

namespace Modules\ContentModule\Application\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\ContentModule\Domain\Repositories\ContentRepositoryInterface;
use Modules\ContentModule\Events\ContentArchived;
use Illuminate\Support\Facades\Cache;

class ArchiveContentJob implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    public function __construct(private int $contentId) {}

    public function handle(ContentRepositoryInterface $repository): void
    {
        // 1) コンテンツ取得
        $entity = $repository->find($this->contentId);

        // 2) パッケージ内イベントを発行
        event(new ContentArchived($entity));

        // 3) キャッシュクリア（パッケージ標準で用意）
        Cache::forget("content:{$this->contentId}");
    }
}
