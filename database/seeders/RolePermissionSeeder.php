<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. 权限点
        $permissions = [
            ['name' => '仪表盘', 'slug' => 'dashboard.view', 'group' => '系统'],
            ['name' => '管理员管理', 'slug' => 'admin.manage', 'group' => '系统'],
            ['name' => '角色管理', 'slug' => 'role.manage', 'group' => '系统'],
            ['name' => '菜单管理', 'slug' => 'menu.manage', 'group' => '系统'],
        ];
        foreach ($permissions as $p) {
            Permission::firstOrCreate(['slug' => $p['slug']], $p);
        }

        // 2. 超级管理员角色 = 拥有全部权限
        $superAdmin = Role::firstOrCreate(
            ['slug' => 'super-admin'],
            ['name' => '超级管理员']
        );
        $superAdmin->permissions()->sync(Permission::pluck('id'));

        // 3. admin 账号绑定超级管理员角色
        $admin = Admin::where('username', 'admin')->first();
        if ($admin) {
            $admin->roles()->sync([$superAdmin->id]);
        }

        // 4. 演示用：editor 角色（无任何权限）+ editor 账号（稍后测 403）
        $editorRole = Role::firstOrCreate(['slug' => 'editor'], ['name' => '编辑']);
        // editor 角色：只有仪表盘权限（用于演示菜单差异）
        $editorRole->permissions()->sync([
            Permission::where('slug', 'dashboard.view')->value('id'),
        ]);
        $editorAdmin = Admin::firstOrCreate(
            ['username' => 'editor'],
            [
                'password' => Hash::make('editor123'),
                'nickname' => '测试编辑',
            ]
        );
        $editorAdmin->roles()->sync([$editorRole->id]);
    }
}
