<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory;

    protected $table = 'posts';

    protected $fillable = [
        'title',
        'slug',
        'body',
        'html_body',
        'toc',
        'status',
        'created_user',
        'updated_user',
        'deleted_user',
        'deleted',
        'is_deleted',
    ];

    protected $casts = [
        'created' => 'datetime',
        'updated' => 'datetime',
        'deleted' => 'datetime',
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
}
