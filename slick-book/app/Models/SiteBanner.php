<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * サイト・バナー Model
 *
 * @property int $id
 * @property int $site_id
 * @property string $device_type
 * @property string $section
 * @property int $slot_no
 * @property string|null $title
 * @property string|null $html
 * @property bool $enabled
 * @property \Illuminate\Support\Carbon|null $start_at
 * @property \Illuminate\Support\Carbon|null $end_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiteBanner newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiteBanner newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiteBanner query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiteBanner whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiteBanner whereDeviceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiteBanner whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiteBanner whereEndAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiteBanner whereHtml($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiteBanner whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiteBanner whereSection($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiteBanner whereSiteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiteBanner whereSlotNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiteBanner whereStartAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiteBanner whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiteBanner whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SiteBanner extends Model
{
    protected $fillable = [
        'site_id',
        'device_type',
        'section',
        'slot_no',
        'title',
        'html',
        'enabled',
        'start_at',
        'end_at',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];
}
