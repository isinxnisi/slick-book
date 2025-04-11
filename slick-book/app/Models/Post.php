<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $table = 'posts';

    protected $fillable = [
        'title',
        'slug',
        'body',
        'html_body',
        'toc',
        'status',
        'site_id',
        'category_id',
        'published_user',
        'published_at',
        'created_user',
        'updated_user',
        'deleted_user',
        'deleted_at',
        'is_deleted',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'is_deleted' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_user');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_user');
    }

    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_user');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'post_tag')->withTimestamps();
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function getPublishedAttribute()
    {
        if (is_null($this->published_at)) {
            return $this->created_at;
        }
        return $this->published_at;
    }

    public function scopePublished($query)
    {
        return $query
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->where('status', 'published'); // ← 任意でstatus判定なども
    }
}
