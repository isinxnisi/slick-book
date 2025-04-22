<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * サイトSEO設定
 *
 * @property int $id
 * @property int $site_id
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $meta_keywords
 * @property string|null $canonical_base
 * @property string $twitter_card_type
 * @property string|null $custom_head_tags
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $google_analytics_tags
 * @property-read \App\Models\Site $site
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiteSeoSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiteSeoSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiteSeoSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiteSeoSetting whereCanonicalBase($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiteSeoSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiteSeoSetting whereCustomHeadTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiteSeoSetting whereGoogleAnalyticsTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiteSeoSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiteSeoSetting whereMetaDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiteSeoSetting whereMetaKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiteSeoSetting whereMetaTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiteSeoSetting whereSiteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiteSeoSetting whereTwitterCardType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiteSeoSetting whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SiteSeoSetting extends Model
{
    protected $fillable = [
        'site_id',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_base',
        'twitter_card_type',
        'custom_head_tags',
        'google_analytics_tags',
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}
