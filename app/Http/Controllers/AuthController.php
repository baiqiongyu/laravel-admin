<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // 显示登录页
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // 处理登录提交
    public function login(Request $request)
    {
        $data = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $admin = Admin::where('username', $data['username'])->first();

        // 账号不存在 / 被禁用 / 密码不对，统一提示"账号或密码错误"
        if (!$admin || $admin->status !== 1 || !Hash::check($data['password'], $admin->password)) {
            return back()->withErrors(['username' => '账号或密码错误'])->withInput();
        }

        // 登录成功：写入 Session
        $request->session()->put('admin_id', $admin->id);

        // 记录最后登录信息
        $admin->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        return redirect('/');
    }

    // 退出登录
    public function logout(Request $request)
    {
        $request->session()->forget('admin_id');
        return redirect()->route('login');
    }
}
