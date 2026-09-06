<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Permission;
use App\Models\Role;
use Database\Seeders\AdminSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * 管理员管理模块功能测试
 *
 * 知识点：
 * 1. RefreshDatabase：每个测试前重建数据库（用的是内存 sqlite，飞快）
 * 2. $this->post / $this->get 模拟 HTTP 请求，不用真的起服务器
 * 3. 测试前先跑种子，保证角色/权限数据齐全
 */
class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    private Admin $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();
        // 先建账号（AdminSeeder），再绑角色权限（RolePermissionSeeder）——顺序不能反
        $this->seed(AdminSeeder::class);
        $this->seed(RolePermissionSeeder::class);
        $this->superAdmin = Admin::where('username', 'admin')->first();
    }

    /** 模拟已登录：直接往 Session 塞 admin_id（和登录成功后效果一样） */
    private function loginAs(Admin $admin): void
    {
        session(['admin_id' => $admin->id]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admins')->assertRedirect('/login');
    }

    public function test_super_admin_can_view_admin_list(): void
    {
        $this->loginAs($this->superAdmin);

        $this->get('/admins')->assertOk()->assertSee('管理员管理');
    }

    public function test_editor_without_permission_gets_403(): void
    {
        $editor = Admin::where('username', 'editor')->first();
        $this->loginAs($editor);

        $this->get('/admins')->assertForbidden();
    }

    public function test_super_admin_can_create_admin(): void
    {
        $this->loginAs($this->superAdmin);
        $editorRoleId = Role::where('slug', 'editor')->value('id');

        $response = $this->post('/admins', [
            'username'              => 'newadmin',
            'nickname'              => '新管理员',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'roles'                 => [$editorRoleId],
        ]);

        $response->assertRedirect('/admins');

        $newAdmin = Admin::where('username', 'newadmin')->first();
        $this->assertNotNull($newAdmin);
        // 密码必须被哈希过，而不是明文
        $this->assertTrue(Hash::check('password123', $newAdmin->password));
        // 角色同步成功
        $this->assertTrue($newAdmin->roles->contains('slug', 'editor'));
    }

    public function test_validation_fails_with_duplicate_username(): void
    {
        $this->loginAs($this->superAdmin);

        $this->post('/admins', [
            'username'              => 'admin', // 已存在的用户名
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ])->assertSessionHasErrors('username');
    }

    public function test_admin_cannot_delete_himself(): void
    {
        $this->loginAs($this->superAdmin);

        $this->delete("/admins/{$this->superAdmin->id}")
            ->assertRedirect('/admins');

        $this->assertDatabaseHas('admins', ['id' => $this->superAdmin->id]);
    }

    public function test_admin_can_delete_others(): void
    {
        $this->loginAs($this->superAdmin);
        $editor = Admin::where('username', 'editor')->first();

        $this->delete("/admins/{$editor->id}")->assertRedirect('/admins');

        // 管理员删了，中间表的关联也应一并清掉
        $this->assertDatabaseMissing('admins', ['id' => $editor->id]);
        $this->assertDatabaseMissing('admin_role', ['admin_id' => $editor->id]);
    }
}
