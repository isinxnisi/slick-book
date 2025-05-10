<?php

namespace Modules\ContentModule\Core\Infrastructure\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class ContentModuleEventServiceProvider extends ServiceProvider
{
    protected $listen = [
        // ContentStateChanged::class => [/* no core listener here */],
    ];
}
