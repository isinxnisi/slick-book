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

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(BannerComponent::class, function ($app) {
            return new BannerComponent();
        });
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

        // 公開レイアウト
        View::composer(['layouts.blog'], function ($view) {
            $data = app(CurrentSiteDataProvider::class)->get();
            $seo = app(SeoService::class)->generateSeoForCurrentPage($data['currentSite']);
            $view->with(array_merge($data, ['seo' => $seo]));
        });

        View::share('bannerComponent', app(BannerComponent::class));
    }
}
