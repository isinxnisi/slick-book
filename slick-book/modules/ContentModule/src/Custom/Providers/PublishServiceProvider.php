<?php

namespace Modules\ContentModule\Custom\Providers;

use Illuminate\Support\Str;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;

class ContentModuleServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // マイグレーションスタブを動的タイムスタンプ付きで公開
        $stubPath = __DIR__ . '/../../../../database/stubs';
        $stubs    = [
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

        // ── サンプル用ルートを公開（コピー先は /routes/content_admin.php）
        $this->publishes([
            __DIR__ . '/../Routes/content_admin.php' => base_path('routes/content_admin.php'),
        ], 'content-routes');
    }
}
