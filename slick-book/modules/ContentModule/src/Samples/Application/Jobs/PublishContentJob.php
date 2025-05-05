<?php

namespace Modules\ContentModule\Application\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\ContentModule\Domain\Repositories\ContentRepositoryInterface;
use Modules\ContentModule\Events\ContentPublished;
use Illuminate\Support\Facades\Cache;

class PublishContentJob implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    public function __construct(private int $contentId) {}

    public function handle(ContentRepositoryInterface $repository): void
    {
        // キャッシュクリア
        Cache::forget("content:{$this->contentId}");
    }
}
