<?php

namespace App\Providers;

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

        View::composer('components.blog.sidebar', function ($view) {
            $site = app('CurrentSite');
            $site->load('categories.children'); // eager load
            $view->with('currentSite', $site);
        });
    }
}
