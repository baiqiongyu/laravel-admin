<?php

namespace App\Providers;

use App\Models\Admin;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 分页样式默认是 Tailwind，切换为 Bootstrap 5（和布局用的 CSS 框架一致）
        Paginator::useBootstrapFive();

        // 自定义 Blade 指令：@admin_access('admin.manage') ... @endadmin_access
        // 在视图里做权限判断（比如仪表盘的快捷入口只对有权限的人显示）
        // 路由级拦截依然交给 permission 中间件——视图层的隐藏只是"不显示"，不是安全边界
        // 注意：指令名用 snake_case 且模板里大小写必须完全一致，Blade 区分大小写！
        Blade::if('admin_access', function (string $permission) {
            $admin = session('admin_id') ? Admin::find(session('admin_id')) : null;

            return $admin?->hasPermission($permission) ?? false;
        });

        // 所有视图共享：当前登录管理员 + 其可见菜单
        View::composer('*', function ($view) {
            $admin = session('admin_id') ? Admin::find(session('admin_id')) : null;
            $menus = $admin ? $admin->visibleMenus() : collect();

            $view->with([
                'admin'    => $admin,
                'menus'    => $menus,
                'topMenus' => $menus->where('parent_id', 0),
            ]);
        });
    }
}
