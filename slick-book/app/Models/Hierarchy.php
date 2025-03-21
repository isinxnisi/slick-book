<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hierarchy extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id', 'title', 'description', 'order', 'status',
        'created_user', 'updated_user', 'deleted_user',
        'is_deleted'
    ];

    // 親カテゴリ（階層管理）
    public function parent()
    {
        return $this->belongsTo(Hierarchy::class, 'parent_id');
    }

    // 子カテゴリを取得
    public function children()
    {
        return $this->hasMany(Hierarchy::class, 'parent_id')->orderBy('order');
    }

    // すべての子孫を再帰的に取得
    public function descendants()
    {
        return $this->children()->with('descendants');
    }

    // ルート階層の取得（parent_id が NULL のもの）
    public static function roots()
    {
        return self::whereNull('parent_id')->with('descendants')->get();
    }
}
