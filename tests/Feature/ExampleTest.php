<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * 原版断言访问 / 返回 200，但根路由早已被 admin.auth 中间件保护——
     * 未登录会被 302 重定向到 /login。测试要跟随需求演进：
     * 这里改为断言"跳转到登录页"，正好覆盖了登录拦截这条链路。
     */
    public function test_root_redirects_guest_to_login(): void
    {
        $this->get('/')->assertRedirect('/login');
    }

    /** 健康检查端点 /up 是留给负载均衡器探活的，必须保持无需登录即可 200 */
    public function test_health_check_is_public(): void
    {
        $this->get('/up')->assertOk();
    }
}
