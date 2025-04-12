<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * 
 *
 * @property int $id
 * @property int $site_id
 * @property int $tag_group_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Site $site
 * @property-read \App\Models\TagGroup $tagGroup
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiteTagGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiteTagGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiteTagGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiteTagGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiteTagGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiteTagGroup whereSiteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiteTagGroup whereTagGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiteTagGroup whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SiteTagGroup extends Model
{
    use HasFactory;

    protected $table = 'site_tag_group'; // 複数形ではないので明示

    protected $fillable = [
        'site_id',
        'tag_group_id',
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function tagGroup()
    {
        return $this->belongsTo(TagGroup::class);
    }
}
