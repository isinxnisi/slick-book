<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
