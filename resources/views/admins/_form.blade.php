{{--
  管理员表单（弹窗版局部模板）
  通过 AJAX 加载到模态框中使用，form 标签自带 @csrf 和 action/method

  约定变量：
    $admin         - Admin 实例或 null（null = 创建模式）
    $roles         - 全部角色列表
    $checkedRoleIds - 已勾选的角色 ID 数组（仅编辑时有）
--}}
@php($isEdit = $admin !== null)

<form method="POST"
      action="{{ $isEdit ? route('admins.update', $admin) : route('admins.store') }}"
      class="admin-ajax-form"
      data-mode="{{ $isEdit ? 'edit' : 'create' }}">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    {{-- 用户名 --}}
    <div class="mb-3">
        <label class="form-label">用户名 <span class="text-danger">*</span></label>
        <input type="text" name="username"
               value="{{ old('username', $admin->username ?? '') }}"
               class="form-control @error('username') is-invalid @enderror"
               placeholder="登录账号">
        @error('username')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- 昵称 --}}
    <div class="mb-3">
        <label class="form-label">昵称</label>
        <input type="text" name="nickname"
               value="{{ old('nickname', $admin->nickname ?? '') }}"
               class="form-control @error('nickname') is-invalid @enderror"
               placeholder="显示名称（可选）">
        @error('nickname')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- 密码：编辑时留空 = 不修改 --}}
    <div class="mb-3">
        <label class="form-label">密码 {{ $isEdit ? '（留空则不修改）' : '' }}<span class="{{ $isEdit ? 'd-none' : 'text-danger' }}">*</span></label>
        <input type="password" name="password"
               class="form-control @error('password') is-invalid @enderror"
               placeholder="{{ $isEdit ? '留空表示不修改' : '至少 8 位' }}"
               autocomplete="new-password">
        @error('password')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- 密码确认：对应验证规则 confirmed --}}
    <div class="mb-3">
        <label class="form-label">确认密码</label>
        <input type="password" name="password_confirmation" class="form-control"
               placeholder="再次输入密码" autocomplete="new-password">
    </div>

    {{-- 角色复选框组：name="roles[]" 提交后是数组 --}}
    <div class="mb-2">
        <label class="form-label d-block">角色</label>
        <div class="d-flex flex-wrap gap-3">
            @foreach ($roles as $role)
                <div class="form-check">
                    @php($checked = in_array($role->id, old('roles', $checkedRoleIds ?? [])))
                    <input class="form-check-input" type="checkbox" name="roles[]"
                           id="role-{{ $role->id }}" value="{{ $role->id }}" {{ $checked ? 'checked' : '' }}>
                    <label class="form-check-label" for="role-{{ $role->id }}">{{ $role->name }}</label>
                </div>
            @endforeach
        </div>
        @error('roles.*')
        <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    {{-- 弹窗底部按钮（替代 layout 里的"返回列表"按钮） --}}
    <div class="d-flex justify-content-end gap-2 pt-3 mt-2 border-top">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
            <i class="bi bi-x-lg"></i> 取消
        </button>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-lg"></i> {{ $isEdit ? '保存修改' : '创建' }}
        </button>
    </div>
</form>