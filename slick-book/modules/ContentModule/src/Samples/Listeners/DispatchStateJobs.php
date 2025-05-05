<?php

namespace Modules\ContentModule\Listeners;

use Modules\ContentModule\Events\ContentStateChanged;
use Modules\ContentModule\Application\Jobs\ReviewContentJob;
use Modules\ContentModule\Application\Jobs\PublishContentJob;
use Modules\ContentModule\Application\Jobs\ArchiveContentJob;

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
