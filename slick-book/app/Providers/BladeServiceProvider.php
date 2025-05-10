<?php

namespace App\Providers;

use App\Services\SeoService;
use App\Services\BannerComponent;
use App\Services\CurrentSiteDataProvider;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;

// class BladeServiceProvider extends ServiceProvider
// {
//     public function register(): void
//     {
//         // Blade 系のシングルトンは不要なら省略
//     }

//     public function boot(): void
//     {
//         // Blade のディレクティブを登録
//         if ($this->app->bound('blade.compiler')) {
//             Blade::directive('blogLayout', fn($expression) =>
//                 "<?php echo \\App\\Http\\View\\Components\\BannerComponent::renderLayout($expression); ?>"
//             );
//             Blade::directive('blog-layout', fn($expression) =>
//                 "<?php echo \\App\\Http\\View\\Components\\BannerComponent::renderLayout($expression); ?>"
//             );
//             // 他のディレクティブや component() 呼び出しもここに置く
//         }

//         // ビューコンポーザ／共有データ
//         View::composer('layouts.blog', function ($view) {
//             $data = app(CurrentSiteDataProvider::class)->get();
//             $seo = app(SeoService::class)->generateSeoForCurrentPage($data['currentSite']);
//             $view->with(array_merge($data, ['seo' => $seo]));
//         });

//         // BannerComponent を全ビューで使えるように共有
//         View::share('bannerComponent', app(BannerComponent::class));
//     }
// }
