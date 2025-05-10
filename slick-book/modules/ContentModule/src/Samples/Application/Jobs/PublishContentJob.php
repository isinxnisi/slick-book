<?php

namespace Modules\ContentModule\Samples\Application\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Modules\ContentModule\Core\Domain\Repositories\ContentRepositoryInterface;

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
