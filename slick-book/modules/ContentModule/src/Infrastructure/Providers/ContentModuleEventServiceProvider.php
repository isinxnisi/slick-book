<?php

namespace Modules\ContentModule\Infrastructure\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\ContentModule\Events\ContentStateChanged;
use Modules\ContentModule\Listeners\DispatchStateJobs;

class ContentModuleEventServiceProvider extends ServiceProvider
{
    protected $listen = [
        ContentStateChanged::class => [
            DispatchStateJobs::class,
        ],
    ];
}
