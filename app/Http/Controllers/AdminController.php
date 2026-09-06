<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAdminRequest;
use App\Http\Requests\UpdateAdminRequest;
use App\Models\Admin;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * 管理员管理（资源控制器）
 *
 * Route::resource 的 7 个标准动作 ↔ HTTP 动词对照：
 *   GET    /admins           → index   列表
 *   GET    /admins/create    → create  新建表单（弹窗加载）
 *   POST   /admins           → store   保存新建
 *   GET    /admins/{id}/edit → edit    编辑表单（弹窗加载）
 *   PUT    /admins/{id}      → update  保存编辑
 *   DELETE /admins/{id}      → destroy 删除
 *   GET    /admins/{id}      → show    详情（本项目用列表直接展示，没要这个）
 *
 * 本控制器支持两种调用方式（同一个路由，两种行为）：
 *   - 正常浏览器导航：返回完整页面 / 重定向
 *   - AJAX（带 X-Requested-With 头）：返回 JSON 或 partial 片段
 * 识别方式：$request->ajax() 看的就是 X-Requested-With 请求头
 */
class AdminController extends Controller
{
    // 列表：搜索 + 分页
    public function index(Request $request)
    {
        $keyword = $request->query('keyword');

        $admins = Admin::query()
            // 关键词搜索：用户名或昵称模糊匹配。when(条件, 回调) = 条件成立才拼接查询
            ->when($keyword, function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('username', 'like', "%{$keyword}%")
                        ->orWhere('nickname', 'like', "%{$keyword}%");
                });
            })
            // 预加载角色，避免列表循环里每个管理员都查一次库（N+1 问题）
            ->with('roles')
            // 搜索关键字会通过查询字符串带到分页链接上：/admins?keyword=xx&page=2
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('admins.index', compact('admins', 'keyword'));
    }

    // 新建表单
    public function create(Request $request)
    {
        $roles = Role::orderBy('id')->get();

        // AJAX 请求（弹窗加载）：只返回表单片段，不带布局
        if ($request->ajax()) {
            return view('admins._form', ['admin' => null, 'roles' => $roles]);
        }

        // 直接访问 /admins/create：弹窗模式下没必要单独跳页，直接回列表
        return redirect()->route('admins.index');
    }

    // 保存新建。参数类型声明为 FormRequest，验证自动完成，失败自动跳回
    public function store(StoreAdminRequest $request)
    {
        // validated() 只返回通过验证的字段，表单里偷偷多传的字段（如 status）会被丢弃
        $data = $request->validated();

        $admin = Admin::create([
            'username' => $data['username'],
            'nickname' => $data['nickname'] ?? null,
            // 密码必须哈希后入库，绝不存明文
            'password' => Hash::make($data['password']),
        ]);

        // sync：让中间表 admin_role 精确等于传入的角色 ID 集合（自动增删差集）
        $admin->roles()->sync($data['roles'] ?? []);

        // AJAX：返回 JSON；正常提交：重定向 + flash 消息
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "管理员 {$admin->username} 创建成功",
            ]);
        }

        return redirect()->route('admins.index')->with('success', "管理员 {$admin->username} 创建成功");
    }

    // 编辑表单。路由参数 {admin} 会被自动"路由模型绑定"成 Admin 实例
    public function edit(Request $request, Admin $admin)
    {
        $admin->load('roles');
        $roles = Role::orderBy('id')->get();

        if ($request->ajax()) {
            return view('admins._form', [
                'admin'          => $admin,
                'roles'          => $roles,
                'checkedRoleIds' => $admin->roles->pluck('id')->all(),
            ]);
        }

        return redirect()->route('admins.index');
    }

    // 保存编辑
    public function update(UpdateAdminRequest $request, Admin $admin)
    {
        $data = $request->validated();

        $admin->username = $data['username'];
        $admin->nickname = $data['nickname'] ?? null;

        // 密码留空 = 不改密码；填了才更新
        if (!blank($data['password'] ?? null)) {
            $admin->password = Hash::make($data['password']);
        }

        $admin->save();
        $admin->roles()->sync($data['roles'] ?? []);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "管理员 {$admin->username} 更新成功",
            ]);
        }

        return redirect()->route('admins.index')->with('success', "管理员 {$admin->username} 更新成功");
    }

    // 删除。防呆：不能删除自己，否则当场被踢出登录
    public function destroy(Request $request, Admin $admin)
    {
        if ($admin->id === $request->session()->get('admin_id')) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => '不能删除当前登录的账号'], 422);
            }
            return redirect()->route('admins.index')->with('error', '不能删除当前登录的账号');
        }

        $username = $admin->username;
        // 注意：删除模型不会自动清理中间表！必须先 detach，否则 admin_role 里留下孤儿数据
        $admin->roles()->detach();
        $admin->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => "管理员 {$username} 已删除"]);
        }

        return redirect()->route('admins.index')->with('success', "管理员 {$username} 已删除");
    }
}