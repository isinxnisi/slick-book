<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DetectSite
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        $siteKey = array_search($host, config('multisite'), true);

        if ($siteKey) {
            config(['app.site' => $siteKey]); // サイトごとの設定適用
        }

        return $next($request);
    }
}
