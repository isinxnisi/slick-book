<?php

namespace Modules\ContentModule\Samples\Providers;

use Illuminate\Support\ServiceProvider;

class ContentModuleSampleServiceProvider extends ServiceProvider
{
    public function register(): void
    {}

    public function boot(): void
    {
        // ── サンプル用ルートを公開（コピー先は /routes/content_admin.php）
        $this->publishes([
            __DIR__ . '/../Routes/content_admin.php' => base_path('routes/content_admin.php'),
        ], 'content-routes');

        // ── Sample 側のイベントリスナーを登録
        $this->app->register(
            \Modules\ContentModule\Samples\Providers\ContentModuleSampleEventServiceProvider::class
        );
    }
}
