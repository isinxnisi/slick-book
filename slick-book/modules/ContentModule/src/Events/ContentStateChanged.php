<?php

namespace Modules\ContentModule\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ContentStateChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public int    $contentId,
        public string $transition,
    ) {}
}
