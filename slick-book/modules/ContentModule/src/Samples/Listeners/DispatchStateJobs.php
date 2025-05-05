<?php

namespace Modules\ContentModule\Samples\Listeners;

use Modules\ContentModule\Core\Events\ContentStateChanged;
use Modules\ContentModule\Samples\Application\Jobs\ReviewContentJob;
use Modules\ContentModule\Samples\Application\Jobs\PublishContentJob;
use Modules\ContentModule\Samples\Application\Jobs\ArchiveContentJob;

class DispatchStateJobs
{
    public function handle(ContentStateChanged $event): void
    {
        match ($event->transition) {
            'to_review' => ReviewContentJob::dispatch($event->contentId),
            'publish'   => PublishContentJob::dispatch($event->contentId),
            'archive'   => ArchiveContentJob::dispatch($event->contentId),
            default     => null,
        };
    }
}
