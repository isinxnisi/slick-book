<?php

namespace App\Http\Controllers;

use App\Models\Site;
use Illuminate\Support\Facades\Storage;

class SiteMediaController extends Controller
{
    public function public(?Site $site, $path)
    {
        $currentSite = app('CurrentSite');
        if (!$site || $currentSite->id != $site->id) {
            abort(404, 'Current site not set');
        };

        $fullPath = "sites/{$site->id}/{$path}";
        if (!Storage::disk('public')->exists($fullPath)) {
            abort(404, 'File not found');
        }

        return response()->file(storage_path("app/public/{$fullPath}"));
    }

    public function admin(Site $site, $path)
    {
        $fullPath = "sites/{$site->id}/{$path}";

        if (!Storage::disk('public')->exists($fullPath)) {
            abort(404, 'File not found');
        }

        return response()->file(storage_path("app/public/{$fullPath}"));
    }
}
// src/slick-book/storage/app/public/sites/1/categories/7/thumbnail/67fa982ae6d8e.png
