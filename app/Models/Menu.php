<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    protected $fillable = [
        'parent_id', 'title', 'route',
        'permission_slug', 'sort', 'status',
    ];

    // 子菜单
    public function children(): HasMany
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('sort');
    }
}
