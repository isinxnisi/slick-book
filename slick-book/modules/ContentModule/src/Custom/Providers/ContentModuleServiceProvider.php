<?php

namespace Modules\ContentModule\Custom\Providers;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Modules\ContentModule\Core\Infrastructure\Providers\ContentModuleCoreServiceProvider;
use Modules\ContentModule\Samples\Providers\ContentModuleSampleServiceProvider;

class ContentModuleServiceProvider extends ContentModuleCoreServiceProvider
{
    public function register(): void
    {
        // Core の register ロジック
        parent::register();

        // サンプル機能を使いたい場合はここで登録
        if (config('content.use_samples', false)) {
            $this->app->register(ContentModuleSampleServiceProvider::class);
        }

        // ContentService への戦略注入
        $this->app->resolving(
            \Modules\ContentModule\Core\Application\Services\ContentService::class,
            function ($service, $app) {
                $service->setStrategies($app->tagged('content.strategies'));
            }
        );
    }

    public function boot(): void
    {
        // Core の boot ロジック
        parent::boot();

        // サンプル用ビューの読み込み（優先度：Sample→Custom公開ビュー→Coreフォールバック）
        $this->loadViewsFrom([
            resource_path('views/vendor/content-module'),
            __DIR__ . '/../../Samples/Resources/views',
        ], 'content-module');

        // Blade コンポーネント群も読み込む
        $this->loadViewComponentsAs('content-module', [
            \Illuminate\View\AnonymousComponent::class => 'components',
        ]);

        // マイグレーションスタブを動的タイムスタンプ付きで公開
        $stubPath = __DIR__ . '/../../../../database/stubs';
        $stubs = [
            'create_content_taxonomy_term_table.stub',
            'create_collections_table.stub',
            'create_collection_items_table.stub',
            'create_galleries_table.stub',
            'create_gallery_items_table.stub',
        ];
        $publish = [];
        foreach ($stubs as $i => $stubFilename) {
            $source = "{$stubPath}/{$stubFilename}";
            if (! File::exists($source)) {
                continue;
            }
            // now()+$i seconds でオフセット
            $timestamp = now()->addSeconds($i)->format('Y_m_d_His');
            $base      = Str::before($stubFilename, '.stub');
            $target    = database_path("migrations/{$timestamp}_{$base}.php");
            $publish[$source] = $target;
        }
        $this->publishes($publish, 'content-module-migrations');

        // 将来的にカスタマイズ用の publishes などを追加
        $this->publishes([
            __DIR__ . '/../../../Config/content.php'     => config_path('content.php'),
            __DIR__ . '/../../../Config/meta_schema.php' => config_path('meta_schema.php'),
            __DIR__ . '/../../../Config/workflow.php'    => config_path('workflow.php'),
        ], 'content-config');
    }
}
