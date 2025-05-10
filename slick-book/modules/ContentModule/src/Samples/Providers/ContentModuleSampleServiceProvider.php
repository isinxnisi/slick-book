<?php

namespace Modules\ContentModule\Samples\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\ContentModule\Custom\Providers\ContentModuleServiceProvider as ContentModuleCustomServiceProvider;
use Modules\ContentModule\Samples\Providers\ContentModuleSampleEventServiceProvider;

class ContentModuleSampleServiceProvider extends ServiceProvider
{
    public function register(): void
    {}

    public function boot(): void
    {
        // Custom 層の設定に上書きマージ
        $this->mergeConfigFrom(
            __DIR__ . '/../Config/strategies.php',
            'content'
        );

        // ビューの読み込み
        $this->loadViewsFrom([
            resource_path('views/vendor/content-module'),
            __DIR__ . '/../../Samples/Resources/views',
        ], 'content-module');

        // サービスプロバイダを登録
        $this->app->register(ContentModuleCustomServiceProvider::class);

        // イベントリスナーを登録
        $this->app->register(ContentModuleSampleEventServiceProvider::class);
    }
}
