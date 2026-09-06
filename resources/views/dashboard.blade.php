@extends('layouts.admin')

@section('title', '仪表盘')
@section('page-title', '仪表盘')

@section('content')
    {{-- 欢迎横幅：品牌渐变 --}}
    <div class="card border-0 mb-4" style="background: linear-gradient(135deg, #4f46e5, #7c3aed); border-radius: 14px;">
        <div class="card-body p-4 text-white">
            <h4 class="mb-1 fw-bold">欢迎回来，{{ $admin->nickname }}</h4>
            <p class="mb-0 opacity-75" style="font-size: 14px;">
                这是你的后台概览。左侧菜单由你的角色权限动态生成，只展示你有权访问的功能。
            </p>
        </div>
    </div>

    {{-- 统计卡片：真实数据，一行四个 --}}
    <div class="row g-3 mb-4">
        @foreach ([
            ['label' => '管理员', 'value' => $stats['admins'], 'icon' => 'people', 'bg' => '#eef2ff', 'color' => '#4f46e5'],
            ['label' => '角色',   'value' => $stats['roles'],  'icon' => 'shield-lock', 'bg' => '#ecfdf5', 'color' => '#059669'],
            ['label' => '权限点', 'value' => $stats['permissions'], 'icon' => 'key', 'bg' => '#fff7ed', 'color' => '#d97706'],
            ['label' => '菜单',   'value' => $stats['menus'],  'icon' => 'list-ul', 'bg' => '#fdf2f8', 'color' => '#db2777'],
        ] as $item)
            <div class="col-6 col-lg-3">
                <div class="card h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div style="width: 46px; height: 46px; border-radius: 12px;
                                    background: {{ $item['bg'] }}; color: {{ $item['color'] }};
                                    display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;">
                            <i class="bi bi-{{ $item['icon'] }}"></i>
                        </div>
                        <div>
                            <div class="fs-3 fw-bold lh-1">{{ $item['value'] }}</div>
                            <div class="text-secondary" style="font-size: 13px;">{{ $item['label'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-3">
        {{-- 快捷操作 --}}
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-bolt"></i> 快捷操作
                </div>
                <div class="card-body">
                    @admin_access('admin.manage')
                        <a href="{{ route('admins.create') }}"
                           class="d-flex align-items-center justify-content-between text-decoration-none p-3 mb-2"
                           style="border: 1px solid #e2e8f0; border-radius: 12px;">
                            <span class="d-flex align-items-center gap-2" style="color: #1e293b;">
                                <i class="bi bi-person-plus" style="color: #4f46e5;"></i> 新增管理员
                            </span>
                            <i class="bi bi-chevron-right text-secondary"></i>
                        </a>
                    @endadmin_access
                    <a href="#" class="d-flex align-items-center justify-content-between p-3 mb-2 disabled"
                       style="border: 1px dashed #e2e8f0; border-radius: 12px; opacity: .6;">
                        <span class="d-flex align-items-center gap-2" style="color: #64748b;">
                            <i class="bi bi-plus-circle"></i> 角色管理（开发中）
                        </span>
                        <i class="bi bi-chevron-right text-secondary"></i>
                    </a>
                    <a href="#" class="d-flex align-items-center justify-content-between p-3 disabled"
                       style="border: 1px dashed #e2e8f0; border-radius: 12px; opacity: .6;">
                        <span class="d-flex align-items-center gap-2" style="color: #64748b;">
                            <i class="bi bi-plus-circle"></i> 菜单管理（开发中）
                        </span>
                        <i class="bi bi-chevron-right text-secondary"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- 最近加入的管理员 --}}
        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-clock-history"></i> 最近加入的管理员
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>用户名</th>
                            <th>昵称</th>
                            <th>最后登录</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($recentAdmins as $item)
                            <tr>
                                <td>
                                    <span class="d-inline-flex align-items-center gap-2">
                                        <span class="d-inline-flex justify-content-center align-items-center"
                                              style="width: 28px; height: 28px; border-radius: 50%; font-size: 12px;
                                                     background: #eef2ff; color: #4f46e5; font-weight: 600;">
                                            {{ mb_substr($item->nickname ?? $item->username, 0, 1) }}
                                        </span>
                                        {{ $item->username }}
                                    </span>
                                </td>
                                <td>{{ $item->nickname ?? '-' }}</td>
                                <td class="text-secondary">{{ $item->last_login_at?->diffForHumans() ?? '从未登录' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-secondary py-4">暂无数据</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
