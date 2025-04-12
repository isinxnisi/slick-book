<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 
 *
 * @property int $id
 * @property int|null $parent_id
 * @property string $title
 * @property int $order
 * @property string|null $description
 * @property string $status
 * @property int $created_user
 * @property string $created
 * @property int|null $updated_user
 * @property string|null $updated
 * @property int|null $deleted_user
 * @property string|null $deleted
 * @property bool $is_deleted
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Hierarchy> $children
 * @property-read int|null $children_count
 * @property-read Hierarchy|null $parent
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hierarchy newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hierarchy newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hierarchy query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hierarchy whereCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hierarchy whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hierarchy whereCreatedUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hierarchy whereDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hierarchy whereDeletedUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hierarchy whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hierarchy whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hierarchy whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hierarchy whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hierarchy whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hierarchy whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hierarchy whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hierarchy whereUpdated($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hierarchy whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hierarchy whereUpdatedUser($value)
 * @mixin \Eloquent
 */
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
