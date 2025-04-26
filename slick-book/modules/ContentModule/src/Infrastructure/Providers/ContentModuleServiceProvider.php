<?php
namespace Modules\ContentModule\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\ContentModule\Application\Services\ContentService;
use Modules\ContentModule\Application\Strategies\SlotArticleStrategy;
use Modules\ContentModule\Application\Strategies\LayoutSeriesStrategy;
use Modules\ContentModule\Domain\Repositories\ContentRepositoryInterface;
use Modules\ContentModule\Infrastructure\Repositories\EloquentContentRepository;

class ContentModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Repository インターフェース → Eloquent 実装のバインド
        $this->app->bind(
            ContentRepositoryInterface::class,
            EloquentContentRepository::class
        );

        // Strategy のバインドとタグ付け
        $this->app->bind(SlotArticleStrategy::class);
        $this->app->bind(LayoutSeriesStrategy::class);

        $this->app->tag([
            SlotArticleStrategy::class,
            LayoutSeriesStrategy::class,
        ], 'content.strategies');
    }

    public function boot(): void
    {
        // ビューの読み込み
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'content-module');

        // ContentService への戦略注入
        $this->app->resolving(ContentService::class, function (ContentService $service, $app) {
            $service->setStrategies($app->tagged('content.strategies'));
        });
    }
}
