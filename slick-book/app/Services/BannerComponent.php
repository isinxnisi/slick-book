<?php

namespace App\Services;

use App\Models\SiteBanner;
use Illuminate\Support\Facades\Config;

class BannerComponent
{
    public function hasBanner(string $section, int $slotNo): bool
    {
        $site = app('CurrentSite');
        $now = now();

        return \App\Models\SiteBanner::where('site_id', $site->id)
            ->where('section', $section)
            ->where('slot_no', $slotNo)
            ->where('enabled', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('start_at')->orWhere('start_at', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('end_at')->orWhere('end_at', '>=', $now);
            })
            ->exists();
    }

    public function getBanner(string $device, string $section, int $slotNo): ?SiteBanner
    {
        $site = app('CurrentSite');

        $banner = SiteBanner::where('site_id', $site->id)
            ->where('device_type', $device)
            ->where('section', $section)
            ->where('slot_no', $slotNo)
            ->where('enabled', true)
            ->where(function ($q) {
                $now = now();
                $q->whereNull('start_at')->orWhere('start_at', '<=', $now);
            })
            ->where(function ($q) {
                $now = now();
                $q->whereNull('end_at')->orWhere('end_at', '>=', $now);
            })
            ->orderByDesc('start_at')
            ->first();

        if (empty($banner->html)) {
            return null;
        }

        return $banner;
    }
}
