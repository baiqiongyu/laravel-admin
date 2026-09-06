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
                {{-- 弹窗触发按钮：type="button" 避免表单语义混淆 --}}
                <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-1"
                        onclick="openAdminModal('create')">
                    <i class="bi bi-plus-lg"></i> 新增管理员
                </button>
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
                            {{-- 编辑：弹窗触发 --}}
                            <button type="button"
                                    class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1"
                                    onclick="openAdminModal('edit', {{ $item->id }})">
                                <i class="bi bi-pencil"></i> 编辑
                            </button>

                            {{--
                                删除：用 data-* 属性 + 事件委托触发弹窗（不用 onclick 字符串拼接，
                                避免用户名里有单引号引发的 JS 语法错误）
                            --}}
                            @php $isSelf = (int) session('admin_id') === (int) $item->id; @endphp
                            <button type="button"
                                    class="btn-delete-admin btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1 {{ $isSelf ? 'disabled' : '' }}"
                                    data-id="{{ $item->id }}"
                                    data-name="{{ $item->username }}"
                                    {{ $isSelf ? 'aria-disabled="true"' : '' }}
                                    title="{{ $isSelf ? '不能删除当前登录账号' : '删除 '.$item->username }}">
                                <i class="bi bi-trash"></i> 删除
                            </button>
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

        @if ($admins->hasPages())
            <div class="card-footer bg-white d-flex justify-content-center py-3"
                 style="border-radius: 0 0 14px 14px !important;">
                {{ $admins->links() }}
            </div>
        @endif
    </div>

    {{-- ===================== 管理员表单模态框 ===================== --}}
    <div class="modal fade" id="adminModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 14px; border: none;">
                <div class="modal-header" style="border-bottom: 1px solid var(--border);">
                    <h5 class="modal-title d-flex align-items-center gap-2">
                        <i class="bi bi-person-plus" id="adminModalIcon" style="color: #4f46e5;"></i>
                        <span id="adminModalLabel">管理员</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="adminModalBody">
                    {{-- 表单片段通过 JS 异步注入 --}}
                    <div class="text-center py-5 text-secondary">
                        <div class="spinner-border spinner-border-sm" role="status"></div>
                        <span class="ms-2">加载中…</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== 删除确认模态框 ===================== --}}
    {{-- 危险操作：红色主题 + 警示图标 + "不可恢复"提醒，比浏览器原生 confirm() 更专业 --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content" style="border-radius: 14px; border: none;">
                <div class="modal-body text-center pt-4 pb-3">
                    <div class="d-inline-flex justify-content-center align-items-center mb-3"
                         style="width: 64px; height: 64px; border-radius: 50%; background: #fef2f2;">
                        <i class="bi bi-exclamation-triangle-fill" style="font-size: 32px; color: #ef4444;"></i>
                    </div>
                    <h5 class="modal-title mb-2">确认删除</h5>
                    <p class="text-secondary mb-1">即将删除管理员 <strong id="deleteTargetName" class="text-dark"></strong></p>
                    <p class="text-secondary mb-0" style="font-size: 13px;">
                        <i class="bi bi-info-circle"></i> 此操作不可恢复，请谨慎
                    </p>
                </div>
                <div class="modal-footer justify-content-center" style="border-top: 1px solid var(--border);">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"
                            style="border-radius: 10px;">取消</button>
                    <button type="button" id="confirmDeleteBtn" class="btn btn-danger d-inline-flex align-items-center gap-1"
                            style="border-radius: 10px;">
                        <i class="bi bi-trash"></i> 确认删除
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        /**
         * 打开管理员新增/编辑模态框
         * @param {string} mode  'create' | 'edit'
         * @param {number} id    仅 edit 时使用
         */
        async function openAdminModal(mode, id = null) {
            const modalEl   = document.getElementById('adminModal');
            const modalBody = document.getElementById('adminModalBody');
            const label     = document.getElementById('adminModalLabel');
            const icon      = document.getElementById('adminModalIcon');

            // 1. 根据模式更新标题 + 图标
            if (mode === 'create') {
                label.textContent = '新增管理员';
                icon.className = 'bi bi-person-plus';
            } else {
                label.textContent = '编辑管理员';
                icon.className = 'bi bi-pencil-square';
            }

            // 2. 重置 body 显示加载态
            modalBody.innerHTML = `
                <div class="text-center py-5 text-secondary">
                    <div class="spinner-border spinner-border-sm" role="status"></div>
                    <span class="ms-2">加载中…</span>
                </div>
            `;

            // 3. 显示模态框（先开，再异步加载内容，体验更顺滑）
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();

            // 4. AJAX 拉取表单片段
            const url = mode === 'create'
                ? '/admins/create'
                : `/admins/${id}/edit`;

            try {
                const res = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!res.ok) throw new Error('加载失败');
                modalBody.innerHTML = await res.text();
            } catch (e) {
                modalBody.innerHTML = `
                    <div class="alert alert-danger mb-0">
                        <i class="bi bi-exclamation-triangle"></i> 表单加载失败，请重试
                    </div>`;
            }
        }

        /**
         * 监听模态框内表单的提交（事件委托，因为表单是动态注入的）
         */
        document.addEventListener('submit', async (e) => {
            const form = e.target;
            if (!form.classList?.contains('admin-ajax-form')) return;

            e.preventDefault();

            const submitBtn = form.querySelector('button[type="submit"]');
            const originalHtml = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> 提交中…';

            try {
                // FormData 会自动包含 _token、_method、所有字段、文件
                const data = new FormData(form);
                const res = await fetch(form.action, {
                    method: 'POST',  // _method 字段会被 Laravel 识别为真实方法
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: data,
                });

                if (res.ok) {
                    // 成功：关闭模态框 + 刷新列表（flash 在 layout 中自动渲染）
                    const json = await res.json();
                    bootstrap.Modal.getInstance(document.getElementById('adminModal')).hide();
                    showToast(json.message || '操作成功');
                    setTimeout(() => location.reload(), 600);
                } else if (res.status === 422) {
                    // 验证失败：渲染错误到表单
                    const err = await res.json();
                    renderFormErrors(form, err.errors);
                } else {
                    throw new Error('请求失败');
                }
            } catch (err) {
                alert('操作失败：' + err.message);
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHtml;
            }
        });

        /**
         * 把后端返回的 errors 对象渲染到表单对应字段下
         */
        function renderFormErrors(form, errors) {
            // 先清掉旧的错误态
            form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            form.querySelectorAll('.invalid-feedback.dynamic').forEach(el => el.remove());

            for (const [field, messages] of Object.entries(errors)) {
                // 角色复选框组的错误名是 roles.0 / roles.1 / roles.*，统一映射到 roles
                const name = field.split('.')[0];
                const input = form.querySelector(`[name="${name}"]`);
                if (!input) continue;

                input.classList.add('is-invalid');

                // 找到合适的容器插入错误消息
                const container = input.closest('.mb-3, .mb-2, div');
                const fb = document.createElement('div');
                fb.className = 'invalid-feedback dynamic';
                fb.textContent = messages[0];
                container.appendChild(fb);
            }
        }

        /**
         * 简易 toast（成功提示）
         */
        function showToast(message) {
            const toast = document.createElement('div');
            toast.className = 'position-fixed top-0 start-50 translate-middle-x mt-3 px-4 py-2 text-white';
            toast.style.cssText = 'background: #10b981; border-radius: 10px; z-index: 9999; box-shadow: 0 6px 20px rgba(16,185,129,.4);';
            toast.innerHTML = `<i class="bi bi-check-circle-fill me-1"></i> ${message}`;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 2500);
        }

        // ========== 删除确认流程 ==========

        // 用闭包保存当前要删的目标，confirmDelete 写入，executeDelete 读取
        let deleteTarget = null;

        /**
         * 打开删除确认模态框。由事件委托调用（data-id / data-name 传入）
         */
        function confirmDelete(id, name) {
            deleteTarget = { id, name };
            document.getElementById('deleteTargetName').textContent = name;
            bootstrap.Modal.getOrCreateInstance(document.getElementById('deleteModal')).show();
        }

        /**
         * 真正发起 DELETE 请求。需要 _token（防CSRF）和 _method=DELETE（Laravel 路由不识别原生 DELETE 表单）
         */
        async function executeDelete() {
            if (!deleteTarget) return;
            const { id, name } = deleteTarget;
            const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
            const btn = document.getElementById('confirmDeleteBtn');
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> 删除中…';

            try {
                const res = await fetch(`/admins/${id}`, {
                    // 浏览器原生 form 才支持 method=DELETE；fetch 用 POST 加 _method 欺骗 Laravel
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: new URLSearchParams({ _method: 'DELETE' }),
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    modal.hide();
                    showToast(data.message);
                    setTimeout(() => location.reload(), 600);
                } else {
                    // 后端校验拒绝（如：不能删自己）
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                    showError(data.message || '删除失败');
                }
            } catch (e) {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
                showError('网络错误：' + e.message);
            }
        }

        /**
         * 失败提示（红色 toast）
         */
        function showError(message) {
            const toast = document.createElement('div');
            toast.className = 'position-fixed top-0 start-50 translate-middle-x mt-3 px-4 py-2 text-white';
            toast.style.cssText = 'background: #ef4444; border-radius: 10px; z-index: 9999; box-shadow: 0 6px 20px rgba(239,68,68,.4);';
            toast.innerHTML = `<i class="bi bi-x-circle-fill me-1"></i> ${message}`;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3500);
        }

        // 事件委托：列表里所有 .btn-delete-admin 按钮的点击都路由到这里
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.btn-delete-admin');
            if (!btn || btn.classList.contains('disabled')) return;
            e.preventDefault();
            confirmDelete(btn.dataset.id, btn.dataset.name);
        });

        // 确认按钮的点击 → 真正删除
        document.getElementById('confirmDeleteBtn').addEventListener('click', executeDelete);
    </script>
@endpush