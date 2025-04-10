<?php

namespace App\Http\Controllers;

use App\Models\Site;
use Illuminate\Support\Facades\Storage;

class SiteMediaController extends Controller
{
    public function public($type, $filename)
    {
        $site = app('CurrentSite');
        if (!$site) abort(404, 'Current site not set');

        $path = "sites/{$site->id}/{$type}/{$filename}";
        if (!Storage::disk('public')->exists($path)) abort(404, 'File not found');

        return response()->file(storage_path("app/public/{$path}"));
    }

    public function admin(Site $site, $type, $filename)
    {
        $path = "sites/{$site->id}/{$type}/{$filename}";
        if (!Storage::disk('public')->exists($path)) abort(404, 'File not found');

        return response()->file(storage_path("app/public/{$path}"));
    }
}
