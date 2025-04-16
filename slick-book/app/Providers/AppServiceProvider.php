<?php

namespace App\Providers;

use App\Services\SeoService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $host = Request::getHost();
        $siteKey = array_search($host, config('multisite'), true);

        if ($siteKey) {
            Config::set('app.site', $siteKey);
        }

        Blade::component('layouts.blog', 'blog-layout');

        // 公開レイアウトにだけ SEO 変数を注入
        View::composer('layouts.blog', function ($view) {
            $site = app('CurrentSite'); // 管理済み
            $site->load('categories.children'); // eager load
            $seo = app(SeoService::class)->generateSeoForCurrentPage($site);

            $view->with([
                'currentSite' => $site,
                'seo' => $seo,
            ]);
        });
    }
}
