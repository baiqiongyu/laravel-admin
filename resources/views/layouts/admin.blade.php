<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', '后台管理')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons：专业图标库，和 Bootstrap 同源 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* ===== 品牌设计令牌（与登录页一致）===== */
        :root {
            --brand-1: #4f46e5;
            --brand-2: #7c3aed;
            --brand-grad: linear-gradient(135deg, #4f46e5, #7c3aed);
            --text-main: #1e293b;
            --text-sub: #64748b;
            --border: #e2e8f0;
            --page-bg: #f1f5f9;
            --sidebar-bg: #0f172a;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI",
            "PingFang SC", "Microsoft YaHei", sans-serif;
            color: var(--text-main);
            background: var(--page-bg);
        }

        /* ===== 整体骨架 ===== */
        .admin-shell { display: flex; min-height: 100vh; }

        /* ===== 侧边栏 ===== */
        .sidebar {
            width: 232px;
            flex-shrink: 0;
            background: var(--sidebar-bg);
            color: #cbd5e1;
            display: flex;
            flex-direction: column;
            position: sticky;
            top: 0;
            height: 100vh;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 20px 20px 18px;
            border-bottom: 1px solid rgba(255, 255, 255, .07);
        }
        .sidebar-brand .mark {
            width: 36px; height: 36px;
            border-radius: 10px;
            background: var(--brand-grad);
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 17px;
            box-shadow: 0 4px 12px rgba(79, 70, 229, .4);
        }
        .sidebar-brand .name { color: #fff; font-weight: 700; font-size: 15px; letter-spacing: .5px; }

        .sidebar-nav { padding: 14px 12px; overflow-y: auto; flex: 1; }

        /* 分组标题 */
        .nav-group-title {
            font-size: 12px;
            color: #64748b;
            padding: 14px 10px 6px;
            letter-spacing: 1px;
        }

        /* 菜单链接 */
        .nav-link-item {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #94a3b8;
            text-decoration: none;
            font-size: 14px;
            padding: 10px 12px;
            border-radius: 10px;
            transition: background .15s, color .15s;
        }
        .nav-link-item i { font-size: 15px; width: 18px; text-align: center; }
        .nav-link-item:hover { color: #fff; background: rgba(255, 255, 255, .06); }

        /* 当前激活项：品牌渐变高亮 */
        .nav-link-item.active {
            color: #fff;
            background: var(--brand-grad);
            box-shadow: 0 4px 12px rgba(79, 70, 229, .35);
        }

        /* 侧边栏底部当前用户 */
        .sidebar-user {
            padding: 14px 16px;
            border-top: 1px solid rgba(255, 255, 255, .07);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .sidebar-user .avatar {
            width: 34px; height: 34px;
            border-radius: 50%;
            background: var(--brand-grad);
            color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; font-weight: 600;
            flex-shrink: 0;
        }
        .sidebar-user .who { overflow: hidden; }
        .sidebar-user .who .n { color: #e2e8f0; font-size: 13px; font-weight: 600; }
        .sidebar-user .who .logout-btn {
            background: none; border: none; color: #64748b;
            font-size: 12px; padding: 0; cursor: pointer;
        }
        .sidebar-user .who .logout-btn:hover { color: #fff; }

        /* ===== 主区 ===== */
        .main-area { flex: 1; min-width: 0; display: flex; flex-direction: column; }

        /* 顶栏 */
        .topbar {
            background: #fff;
            border-bottom: 1px solid var(--border);
            padding: 14px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .topbar .page-title { font-size: 17px; font-weight: 700; margin: 0; }
        .topbar .breadcrumb-sm { font-size: 13px; color: var(--text-sub); }

        .content-wrap { padding: 24px 28px 40px; }

        /* ===== Bootstrap 组件品牌化覆盖 ===== */
        .card {
            border: 1px solid var(--border);
            border-radius: 14px;
            box-shadow: 0 1px 3px rgba(15, 23, 42, .04);
        }
        .card-header {
            background: #fff;
            border-bottom: 1px solid var(--border);
            font-weight: 600;
            border-radius: 14px 14px 0 0 !important;
        }

        .btn-primary {
            --bs-btn-bg: var(--brand-1);
            --bs-btn-border-color: var(--brand-1);
            --bs-btn-hover-bg: #4338ca;
            --bs-btn-hover-border-color: #4338ca;
            --bs-btn-active-bg: #3730a3;
            --bs-btn-active-border-color: #3730a3;
            --bs-btn-disabled-bg: #a5b4fc;
            --bs-btn-disabled-border-color: #a5b4fc;
            border-radius: 10px;
        }
        .btn-outline-primary {
            --bs-btn-color: var(--brand-1);
            --bs-btn-border-color: #c7d2fe;
            --bs-btn-hover-bg: var(--brand-1);
            --bs-btn-hover-border-color: var(--brand-1);
            --bs-btn-active-bg: #4338ca;
            border-radius: 10px;
        }
        .btn-outline-danger { border-radius: 10px; }
        .btn-outline-secondary { border-radius: 10px; }

        .form-control, .form-select {
            border-radius: 10px;
            border-color: var(--border);
            padding: 9px 14px;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--brand-1);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, .12);
        }
        .form-label { font-weight: 500; font-size: 14px; }

        .badge { border-radius: 999px; font-weight: 500; padding: .42em .8em; }
        .badge.role-badge {
            background: #eef2ff;
            color: var(--brand-1);
            border: 1px solid #c7d2fe;
        }

        /* 表格 */
        .table { --bs-table-hover-bg: #f8fafc; margin-bottom: 0; }
        .table thead th {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .6px;
            color: var(--text-sub);
            font-weight: 600;
            border-bottom: 1px solid var(--border);
            padding: 12px 16px;
            white-space: nowrap;
        }
        .table tbody td {
            padding: 13px 16px;
            vertical-align: middle;
            color: var(--text-main);
        }

        /* 分页 */
        .pagination { --bs-pagination-border-radius: 8px; }
        .page-link { color: var(--brand-1); border-radius: 8px !important; margin: 0 2px; border-color: var(--border); }
        .page-item.active .page-link {
            background: var(--brand-1);
            border-color: var(--brand-1);
        }

        /* 警告条 */
        .alert { border-radius: 12px; border: none; }
        .alert-success { background: #ecfdf5; color: #047857; }
        .alert-danger  { background: #fef2f2; color: #b91c1c; }

        /* 响应式：窄屏侧边栏收窄为纯图标 */
        @media (max-width: 860px) {
            .sidebar { width: 64px; }
            .sidebar-brand .name,
            .sidebar-user .who,
            .nav-group-title,
            .nav-link-item span { display: none; }
            .nav-link-item { justify-content: center; }
            .sidebar-brand, .sidebar-user { justify-content: center; padding: 14px 8px; }
        }
    </style>
</head>
<body>
<div class="admin-shell">
    {{-- ===== 侧边栏 ===== --}}
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="mark"><i class="bi bi-lightning-charge-fill"></i></div>
            <div class="name">后台管理系统</div>
        </div>

        <nav class="sidebar-nav">
            @php
                // 知识点：request()->route()->getName() 拿到当前路由名，
                // 用来和菜单表的 route 字段比对，实现"激活态"高亮
                $currentRoute = request()->route()?->getName();

                // 图标映射：权限 slug → Bootstrap Icons 图标名（新增菜单时在这里补一行即可）
                $iconMap = [
                    'dashboard.view' => 'speedometer2',
                    'admin.manage'   => 'people',
                    'role.manage'    => 'shield-lock',
                    'menu.manage'    => 'list-ul',
                ];
            @endphp

            @foreach ($topMenus as $menu)
                @php
                    $children = $menus->where('parent_id', $menu->id);
                @endphp
                @if ($children->isNotEmpty())
                    {{-- 分组：只要任意子项激活，整组标记激活 --}}
                    @php
                        $groupActive = $children->contains(fn ($c) => $c->route === $currentRoute);
                    @endphp
                    <div class="nav-group-title">{{ $menu->title }}</div>
                    @foreach ($children as $child)
                        <a class="nav-link-item {{ $child->route === $currentRoute ? 'active' : '' }}"
                           href="{{ $child->route ? route($child->route) : '#' }}">
                            <i class="bi bi-{{ $iconMap[$child->permission_slug] ?? 'circle' }}"></i>
                            <span>{{ $child->title }}</span>
                        </a>
                    @endforeach
                @else
                    <a class="nav-link-item {{ $menu->route === $currentRoute ? 'active' : '' }}"
                       href="{{ $menu->route ? route($menu->route) : '#' }}">
                        <i class="bi bi-{{ $iconMap[$menu->permission_slug] ?? 'speedometer2' }}"></i>
                        <span>{{ $menu->title }}</span>
                    </a>
                @endif
            @endforeach
        </nav>

        {{-- 底部：当前登录用户 + 退出 --}}
        <div class="sidebar-user">
            <div class="avatar">{{ mb_substr($admin->nickname ?? $admin->username ?? 'A', 0, 1) }}</div>
            <div class="who">
                <div class="n">{{ $admin->nickname ?? $admin->username }}</div>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="logout-btn">退出登录</button>
                </form>
            </div>
        </div>
    </aside>

    {{-- ===== 主区 ===== --}}
    <div class="main-area">
        <header class="topbar">
            <h1 class="page-title">@yield('page-title', '仪表盘')</h1>
            <span class="breadcrumb-sm">
                {{ now()->format('Y年m月d日 l') }}
            </span>
        </header>

        <main class="content-wrap">
            @if (session('success'))
                <div class="alert alert-success d-flex align-items-center gap-2">
                    <i class="bi bi-check-circle-fill"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger d-flex align-items-center gap-2">
                    <i class="bi bi-x-circle-fill"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @yield('content')
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
{{-- 留给各页面用 @push('scripts') 注入自定义 JS --}}
@stack('scripts')
</body>
</html>
