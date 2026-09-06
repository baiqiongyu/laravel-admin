<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Admin extends Model
{
    protected $fillable = [
        'username', 'password', 'nickname',
        'status', 'last_login_at', 'last_login_ip',
    ];

    protected $hidden = ['password'];

    protected function casts(): array
    {
        return [
            'status' => 'integer',
            'last_login_at' => 'datetime',
        ];
    }

    // 一个管理员有多个角色
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    // 是否超级管理员
    public function isSuperAdmin(): bool
    {
        return $this->roles()->where('slug', 'super-admin')->exists();
    }

    // 是否拥有某权限：超管直接放行，普通角色查 角色→权限 两级
    // 中间件和 Blade 指令共用这一个方法（单一事实来源，避免逻辑两处漂移）
    public function hasPermission(string $slug): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return $this->roles()
            ->whereHas('permissions', fn ($q) => $q->where('slug', $slug))
            ->exists();
    }

    // 当前管理员能看到的菜单
    public function visibleMenus()
    {
        $all = Menu::where('status', 1)->orderBy('sort')->get();

        // 超管：全部菜单
        if ($this->isSuperAdmin()) {
            return $all;
        }

        // 普通角色：收集拥有的权限 slugs
        $slugs = $this->roles()->with('permissions')->get()
            ->flatMap(fn ($role) => $role->permissions->pluck('slug'))
            ->unique();

        $visible = $all->filter(function ($menu) use ($slugs) {
            return blank($menu->permission_slug) || $slugs->contains($menu->permission_slug);
        });

        // 没绑路由的顶级分组，如果子级全被权限过滤掉了，就整个隐藏
        return $visible->reject(function ($menu) use ($visible) {
            return $menu->parent_id === 0
                && blank($menu->route)
                && $visible->where('parent_id', $menu->id)->isEmpty();
        })->values();
    }
}
