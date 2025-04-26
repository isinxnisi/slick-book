<?php

namespace Modules\ContentModule\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\ContentModule\Application\Services\ContentService;
use Modules\ContentModule\Domain\Repositories\ContentRepositoryInterface;
use Modules\ContentModule\Infrastructure\Repositories\EloquentContentRepository;

class ContentModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // 1) デフォルト設定のマージ
        $this->mergeConfigFrom(__DIR__.'/../Config/content.php', 'content');

        // 2) リポジトリバインド
        $this->app->bind(
            ContentRepositoryInterface::class,
            EloquentContentRepository::class
        );

        // 3) Strategy のバインド＆タグ付け
        foreach (config('content.strategies', []) as $class) {
            $this->app->bind($class);
        }
        $this->app->tag(config('content.strategies', []), 'content.strategies');
    }

    public function boot(): void
    {
        // 設定ファイルの公開タグ
        $this->publishes([
            __DIR__.'/../Config/content.php' => config_path('content.php'),
        ], 'content-config');

        // Blade ビューの読み込み
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'content-module');

        // ContentService への戦略注入
        $this->app->resolving(ContentService::class, function (ContentService $service, $app) {
            $service->setStrategies($app->tagged('content.strategies'));
        });
    }
}
