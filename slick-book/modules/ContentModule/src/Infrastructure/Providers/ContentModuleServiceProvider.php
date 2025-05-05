<?php

namespace Modules\ContentModule\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Modules\ContentModule\Application\Services\ContentService;
use Modules\ContentModule\Domain\Repositories\ContentRepositoryInterface;
use Modules\ContentModule\Infrastructure\Repositories\EloquentContentRepository;
use Symfony\Component\Workflow\Definition;
use Symfony\Component\Workflow\MarkingStore\MethodMarkingStore;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\Workflow;
use Symfony\Component\Workflow\WorkflowInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Modules\ContentModule\Infrastructure\Providers\ContentModuleEventServiceProvider;

class ContentModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // 1) デフォルト設定のマージ
        $this->mergeConfigFrom(__DIR__.'/../Config/content.php', 'content');
        $this->mergeConfigFrom(__DIR__.'/../Config/meta_schema.php', 'meta_schema');
        $this->mergeConfigFrom(__DIR__.'/../Config/workflow.php', 'workflow');

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

        // WorkflowInterface に紐づく 'content' ワークフローを登録
        $this->app->singleton('workflow.content', function($app) {
            $config = config('workflow.content');

            // (a) 状態と遷移の定義
            $definition = new Definition(
                $config['places'],
                array_map(
                    fn(string $name) => new Transition(
                        $name,
                        $config['transitions'][$name]['from'],
                        $config['transitions'][$name]['to']
                    ),
                    array_keys($config['transitions'])
                )
            );

            // (b) シングルステート用マーキングストア
            $markingStore = new MethodMarkingStore(
                /* $singleState = */ true,
                /* $property = */ $config['marking_store']['property']
            );

            // (c) Symfony の EventDispatcher を使用
            $dispatcher = new EventDispatcher();

            // (d) 第3引数に dispatcher、第4引数にワークフロー名を指定
            return new Workflow(
                $definition,
                $markingStore,
                $dispatcher,
                'content'
            );
        });

        // エイリアス登録：WorkflowInterface をタイプヒントで受けられるように
        $this->app->alias('workflow.content', WorkflowInterface::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Migrations');

        // 設定ファイルの公開タグ
        $this->publishes([
            __DIR__.'/../Config/content.php' => config_path('content.php'),
        ], 'content-config');

        // メタスキーマ設定ファイルの公開タグ
        $this->publishes([
            __DIR__.'/../Config/meta_schema.php' => config_path('meta_schema.php'),
        ], 'meta_schema-config');

        // ワークフロー
        $this->publishes([
            __DIR__.'/../Config/workflow.php' => config_path('workflow.php'),
        ], 'workflow-config');

        $this->publishes([
            __DIR__.'/../Config/routes/content_admin.php' => base_path('modules/ContentModule/src/Infrastructure/Config/routes/content_admin.php'),
        ], 'content-routes');

        // Blade ビューの読み込み
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'content-module');

        $this->loadViewComponentsAs('content-module', [
            \Illuminate\View\AnonymousComponent::class => 'components', // Laravel 10 以降
        ]);

        // ContentService への戦略注入
        $this->app->resolving(ContentService::class, function (ContentService $service, $app) {
            $service->setStrategies($app->tagged('content.strategies'));
        });

        // マイグレーションスタブを動的タイムスタンプ付きで公開
        $stubPath = __DIR__ . '/../../../database/migrations/stubs';
        $stubs = [
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

        $this->app->register(ContentModuleEventServiceProvider::class);
    }
}
