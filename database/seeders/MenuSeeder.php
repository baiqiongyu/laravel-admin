<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 顶级：仪表盘
        Menu::updateOrCreate(['title' => '仪表盘'], [
            'parent_id' => 0, 'route' => 'dashboard',
            'permission_slug' => 'dashboard.view', 'sort' => 1, 'status' => 1,
        ]);

        // 顶级：系统管理（分组，不绑路由）
        $system = Menu::updateOrCreate(['title' => '系统管理'], [
            'parent_id' => 0, 'route' => null,
            'permission_slug' => null, 'sort' => 2, 'status' => 1,
        ]);

        // 子级：挂在系统管理下面
        $children = [
            ['title' => '管理员管理', 'route' => 'admins.index', 'permission_slug' => 'admin.manage', 'sort' => 1],
            ['title' => '角色管理',   'route' => null, 'permission_slug' => 'role.manage',  'sort' => 2],
            ['title' => '菜单管理',   'route' => null, 'permission_slug' => 'menu.manage',  'sort' => 3],
        ];
        foreach ($children as $c) {
            Menu::updateOrCreate(['title' => $c['title']], [
                ...$c, 'parent_id' => $system->id, 'status' => 1,
            ]);
        }
    }
}
