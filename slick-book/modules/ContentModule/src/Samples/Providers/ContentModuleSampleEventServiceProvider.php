<?php

namespace Modules\ContentModule\Samples\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\ContentModule\Core\Events\ContentStateChanged;
use Modules\ContentModule\Samples\Listeners\DispatchStateJobs;

class ContentModuleSampleEventServiceProvider extends ServiceProvider
{
    protected $listen = [
        // Core が発火するイベントに、Sample 側のリスナーを紐付け
        ContentStateChanged::class => [
            DispatchStateJobs::class,
        ],
    ];
}
