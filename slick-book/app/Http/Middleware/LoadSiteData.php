<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\View;
use App\Models\Site;

class LoadSiteData
{
    public function handle($request, Closure $next): mixed
    {
        $host = preg_replace('/:\d+$/', '', $request->getHttpHost());

        $site = Site::where('domain', $host)->first();

        if (!$site) {
            abort(404, 'Site not found');
        }

        View::share('currentSite', $site);
        app()->instance('CurrentSite', $site);

        return $next($request);
    }
}
