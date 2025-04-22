<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 記事SEO設定 Model
 *
 * @property int $id
 * @property int $post_id
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $meta_keywords
 * @property string|null $canonical_url
 * @property string|null $twitter_card_type
 * @property string|null $ogp_image_path
 * @property bool $noindex
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Post $post
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostSeoSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostSeoSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostSeoSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostSeoSetting whereCanonicalUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostSeoSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostSeoSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostSeoSetting whereMetaDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostSeoSetting whereMetaKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostSeoSetting whereMetaTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostSeoSetting whereNoindex($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostSeoSetting whereOgpImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostSeoSetting wherePostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostSeoSetting whereTwitterCardType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostSeoSetting whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PostSeoSetting extends Model
{
    protected $fillable = [
        'post_id',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_base',
        'twitter_card_type',
        'custom_head_tags',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
