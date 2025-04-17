<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Site;
use App\Models\SiteSeoSetting;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class SiteSeoSettingController extends Controller
{
    public function edit(Request $request)
    {
        $siteId = $request->input('site', Site::orderBy('id')->first()?->id);
        $sites = Site::orderBy('id')->get();
        $site = Site::findOrFail($siteId);
        $seo = $site->seoSetting ?? new SiteSeoSetting(['site_id' => $site->id]);
        return view('admin.seo-settings.edit', compact('siteId', 'site', 'sites', 'seo'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'canonical_base' => 'nullable|url',
            'twitter_card_type' => 'required|in:summary,summary_large_image',
            'custom_head_tags' => 'nullable|string',
            'google_analytics_tags' => 'nullable|string',
        ]);

        $siteId = $request->input('site', Site::orderBy('id')->first()?->id);
        $site = Site::findOrFail($siteId);
        $site->seoSetting()->updateOrCreate(['site_id' => $site->id], $validated);

        foreach ($request->file('images', []) as $type => $file) {
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs("sites/{$site->id}/{$type}", $filename, 'public');

            $site->images()->updateOrCreate(
                ['type' => $type],
                ['path' => $path]
            );
        }

        return redirect()->route('site-seo-settings.edit', $site)->with('success', 'SEO設定を更新しました。');
    }
}
