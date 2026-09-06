<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $admin = Admin::find(session('admin_id'));

        // 判断逻辑统一收敛到 Admin::hasPermission()，
        // Blade 的 @canAdminAccess 指令用的也是同一份逻辑
        if (!$admin || !$admin->hasPermission($permission)) {
            abort(403, '没有权限执行此操作');
        }

        return $next($request);
    }
}
