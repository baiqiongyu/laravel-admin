<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Menu;
use App\Models\Permission;
use App\Models\Role;

class DashboardController extends Controller
{
    public function index()
    {
        // 统计卡片：每个 count() 都是一次轻量 SQL（SELECT COUNT(*)）
        // 数据量大了以后可以换成 ->cachedCount() 或缓存，现阶段无需优化
        $stats = [
            'admins'      => Admin::count(),
            'roles'       => Role::count(),
            'permissions' => Permission::count(),
            'menus'       => Menu::count(),
        ];

        // 最近加入的管理员（列表页分页查询的"迷你版"）
        $recentAdmins = Admin::orderByDesc('id')->limit(5)->get();

        return view('dashboard', compact('stats', 'recentAdmins'));
    }
}
