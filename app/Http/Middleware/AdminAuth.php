<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 安检：Session 里没有 admin_id = 没登录
        if (!$request->session()->has('admin_id')) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
