<?php

namespace Modules\ContentModule\Core\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Symfony\Component\Workflow\Definition;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\Workflow;
use Symfony\Component\Workflow\MarkingStore\MethodMarkingStore;
use Symfony\Component\Workflow\WorkflowInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Modules\ContentModule\Core\Application\Services\ContentService;
use Modules\ContentModule\Core\Domain\Repositories\ContentRepositoryInterface;
use Modules\ContentModule\Core\Infrastructure\Eloquent\Repositories\EloquentContentRepository;

class ContentModuleCoreServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // デフォルト設定のマージ（Core/Config/*.php）
        $this->mergeConfigFrom(__DIR__ . '/../Config/content.php', 'content');
        $this->mergeConfigFrom(__DIR__ . '/../Config/meta_schema.php', 'meta_schema');
        $this->mergeConfigFrom(__DIR__ . '/../Config/workflow.php', 'workflow');

        // リポジトリバインド
        $this->app->bind(
            ContentRepositoryInterface::class,
            EloquentContentRepository::class
        );

        // Strategy のタグ付け
        foreach (config('content.strategies', []) as $class) {
            $this->app->bind($class);
        }
        $this->app->tag(config('content.strategies', []), 'content.strategies');

        // ワークフロー登録
        $this->app->singleton('workflow.content', function($app) {
            $config = config('workflow.content');

            // (a) 状態と遷移の定義
            $definition = new Definition(
                $config['places'],
                array_map(
                    fn(string $name) => new Transition(
                        $name,
                        $config['transitions'][$name]['from'] ?? [],
                        $config['transitions'][$name]['to'] ?? []
                    ),
                    array_keys($config['transitions'])
                )
            );

            // (b) シングルステート用マーキングストア
            $markingStore = new MethodMarkingStore(
                /* $singleState = */ true,
                /* $property = */ $config['marking_store']['property'] ?? null
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

        // エイリアス登録
        $this->app->alias('workflow.content', WorkflowInterface::class);
    }

    public function boot(): void
    {
        // Core マイグレーションの自動読み込み
        $this->loadMigrationsFrom(__DIR__ . '/../../../../database/migrations');

        // Core イベントプロバイダ登録
        $this->app->register(ContentModuleEventServiceProvider::class);
    }
}
