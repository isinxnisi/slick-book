<?php

namespace Modules\ContentModule\Core\Infrastructure\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\ContentModule\Core\Events\ContentStateChanged;
use Modules\ContentModule\Samples\Listeners\DispatchStateJobs;

class ContentModuleEventServiceProvider extends ServiceProvider
{
    protected $listen = [
        // ContentStateChanged::class => [/* no core listener here */],
    ];
}
