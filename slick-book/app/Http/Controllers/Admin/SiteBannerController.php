<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Site;
use App\Models\SiteBanner;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SiteBannerController extends Controller
{
    public function index(Request $request): View
    {
        $siteId = $request->input('site', Site::orderBy('id')->first()?->id);
        $device = $request->input('device', 'pc_tablet');
        $sites = Site::orderBy('id')->get();

        $banners = SiteBanner::where('site_id', $siteId)
            ->where('device_type', $device)
            ->orderBy('section')
            ->orderBy('slot_no')
            ->orderBy('start_at')
            ->get()
            ->groupBy(['section', 'slot_no']);

        return view('admin.site-banners.index', compact('siteId', 'device', 'sites', 'banners'))
            ->with('bannersBySlot', $banners);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'site_id' => 'required|integer|exists:sites,id',
            'device_type' => 'required|string',
            'section' => 'required|string',
            'slot_no' => 'required|integer',
            'title' => 'nullable|string|max:255',
            'html' => 'nullable|string',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
        ]);

        $data['enabled'] = true;

        SiteBanner::create($data);

        return redirect()->route('site-banners.index', [
            'site' => $data['site_id'],
            'device' => $data['device_type'],
        ]);
    }

    public function update(Request $request, SiteBanner $siteBanner): RedirectResponse
    {
        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'html' => 'nullable|string',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
        ]);

        $siteBanner->update($data);

        return redirect()->route('site-banners.index', [
            'site' => $siteBanner->site_id,
            'device' => $siteBanner->device_type,
        ]);
    }

    public function destroy(SiteBanner $siteBanner): RedirectResponse
    {
        $siteBanner->delete();

        return back();
    }
}
