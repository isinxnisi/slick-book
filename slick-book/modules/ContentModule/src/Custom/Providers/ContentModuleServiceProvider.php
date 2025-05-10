<?php

namespace Modules\ContentModule\Custom\Providers;

use Modules\ContentModule\Core\Infrastructure\Providers\ContentModuleCoreServiceProvider;
use Modules\ContentModule\Core\DSL\{Loader, DslRegistry};

class ContentModuleServiceProvider extends ContentModuleCoreServiceProvider
{
    public function register(): void
    {
        // Core の register ロジック
        parent::register();

        // ContentService への戦略注入
        $this->app->resolving(
            \Modules\ContentModule\Core\Application\Services\ContentService::class,
            function ($service, $app) {
                $service->setStrategies($app->tagged('content.strategies'));
            }
        );

        // DSL Loader と Registry を IoC バインド
        $this->app->singleton(Loader::class, fn($app) => new Loader());
        $this->app->singleton(
            DslRegistry::class,
            function ($app) {
                $loader     = $app->make(Loader::class);
                $metaSchema = config('meta_schema');
                return new DslRegistry($loader, $metaSchema);
            }
        );

        // 既存の Core バインドを Custom 側クラスに差し替え
        $this->app->singleton(
            \Modules\ContentModule\Core\DSL\DslRuleProvider::class,
            fn($app) => $app->make(\Modules\ContentModule\Custom\DSL\DslRuleProvider::class)
        );
    }

    public function boot(): void
    {
        // Core の boot ロジック
        parent::boot();

        // Blade コンポーネント群も読み込む
        $this->loadViewComponentsAs('content-module', [
            \Illuminate\View\AnonymousComponent::class => 'components',
        ]);

        // Registry を初期化して DSL 定義を読み込ませる
        $this->app->make(DslRegistry::class);
    }
}
