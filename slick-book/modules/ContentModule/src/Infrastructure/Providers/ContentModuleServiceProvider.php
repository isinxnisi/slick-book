<?php
namespace Modules\ContentModule\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\ContentModule\Domain\Repositories\ContentRepositoryInterface;
use Modules\ContentModule\Infrastructure\Repositories\EloquentContentRepository;
use Modules\ContentModule\Domain\Repositories\TaxonomySyncServiceInterface;
use Modules\ContentModule\Infrastructure\Repositories\EloquentTaxonomySyncService;

class ContentModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // コンテンツ永続化リポジトリ
        $this->app->bind(
            ContentRepositoryInterface::class,
            EloquentContentRepository::class
        );

        // タクソノミー同期サービス
        $this->app->bind(
            TaxonomySyncServiceInterface::class,
            EloquentTaxonomySyncService::class
        );
    }

    public function boot(): void
    {
        // マイグレーションロード
        $this->loadMigrationsFrom(__DIR__ . '/../Migrations');
    }
}
