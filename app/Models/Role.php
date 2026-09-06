<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    // 一个角色有多个权限
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }
}
