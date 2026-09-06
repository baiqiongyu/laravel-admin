@extends('layouts.admin')

@section('title', '管理员管理')
@section('page-title', '管理员管理')

@section('content')
    <div class="card">
        {{-- 工具栏：标题 + 新增按钮 + 搜索 --}}
        <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2"
             style="border-radius: 14px;">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-people" style="color: #4f46e5;"></i> 管理员列表
                <span class="badge role-badge">{{ $admins->total() }} 人</span>
            </div>
            <div class="d-flex gap-2">
                <form method="GET" action="{{ route('admins.index') }}" class="d-flex gap-2">
                    <div class="input-group" style="width: 240px;">
                        <span class="input-group-text bg-white" style="border-radius: 10px 0 0 10px; border-color: #e2e8f0;">
                            <i class="bi bi-search text-secondary"></i>
                        </span>
                        <input type="text" name="keyword" value="{{ $keyword }}" class="form-control"
                               placeholder="搜索用户名 / 昵称" style="border-left: none; border-radius: 0 10px 10px 0;">
                    </div>
                </form>
                <a href="{{ route('admins.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-1">
                    <i class="bi bi-plus-lg"></i> 新增管理员
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>用户名</th>
                    <th>昵称</th>
                    <th>角色</th>
                    <th>最后登录</th>
                    <th class="text-end">操作</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($admins as $item)
                    <tr>
                        <td class="text-secondary">{{ $item->id }}</td>
                        <td>
                            <span class="d-inline-flex align-items-center gap-2">
                                <span class="d-inline-flex justify-content-center align-items-center"
                                      style="width: 30px; height: 30px; border-radius: 50%; font-size: 12px;
                                             background: #eef2ff; color: #4f46e5; font-weight: 600;">
                                    {{ mb_substr($item->nickname ?? $item->username, 0, 1) }}
                                </span>
                                <span class="fw-medium">{{ $item->username }}</span>
                            </span>
                        </td>
                        <td>{{ $item->nickname ?? '-' }}</td>
                        <td>
                            @foreach ($item->roles as $role)
                                <span class="badge role-badge">{{ $role->name }}</span>
                            @endforeach
                        </td>
                        <td class="text-secondary">{{ $item->last_login_at?->diffForHumans() ?? '从未登录' }}</td>
                        <td class="text-end">
                            <a href="{{ route('admins.edit', $item) }}"
                               class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1">
                                <i class="bi bi-pencil"></i> 编辑
                            </a>

                            <form method="POST" action="{{ route('admins.destroy', $item) }}"
                                  class="d-inline" onsubmit="return confirm('确定删除 {{ $item->username }} 吗？')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1">
                                    <i class="bi bi-trash"></i> 删除
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="bi bi-inbox" style="font-size: 32px; color: #cbd5e1;"></i>
                            <div class="text-secondary mt-2">{{ $keyword ? "没有找到「{$keyword}」相关的管理员" : '暂无数据' }}</div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- 分页 --}}
        @if ($admins->hasPages())
            <div class="card-footer bg-white d-flex justify-content-center py-3"
                 style="border-radius: 0 0 14px 14px !important;">
                {{ $admins->links() }}
            </div>
        @endif
    </div>
@endsection
