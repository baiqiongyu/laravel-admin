{{--
  管理员表单（局部模板）：create.blade.php 和 edit.blade.php 共用
  约定变量：
    $admin         - Admin 实例或 null（null = 创建模式）
    $roles         - 全部角色列表
    $checkedRoleIds - 已勾选的角色 ID 数组（仅编辑时有）
    $errors        - Laravel 自动提供的验证错误集合
--}}
@php($isEdit = $admin !== null)

<form method="POST"
      action="{{ $isEdit ? route('admins.update', $admin) : route('admins.store') }}">
    @csrf
    {{-- HTML 表单只支持 GET/POST，PUT 用 @method 伪装 --}}
    @if ($isEdit)
        @method('PUT')
    @endif

    {{-- 用户名 --}}
    <div class="mb-3">
        <label class="form-label">用户名 *</label>
        <input type="text" name="username" value="{{ old('username', $admin->username ?? '') }}"
               class="form-control @error('username') is-invalid @enderror">
        @error('username')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- 昵称 --}}
    <div class="mb-3">
        <label class="form-label">昵称</label>
        <input type="text" name="nickname" value="{{ old('nickname', $admin->nickname ?? '') }}"
               class="form-control @error('nickname') is-invalid @enderror">
        @error('nickname')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- 密码：编辑时留空 = 不修改 --}}
    <div class="mb-3">
        <label class="form-label">密码 {{ $isEdit ? '（留空则不修改）' : '*' }}</label>
        <input type="password" name="password"
               class="form-control @error('password') is-invalid @enderror">
        @error('password')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- 密码确认：对应验证规则 confirmed --}}
    <div class="mb-3">
        <label class="form-label">确认密码</label>
        <input type="password" name="password_confirmation" class="form-control">
    </div>

    {{-- 角色复选框组：name="roles[]" 提交后是数组 --}}
    <div class="mb-3">
        <label class="form-label d-block">角色</label>
        @foreach ($roles as $role)
            <div class="form-check form-check-inline">
                @php($checked = in_array($role->id, old('roles', $checkedRoleIds ?? [])))
                <input class="form-check-input" type="checkbox" name="roles[]"
                       id="role-{{ $role->id }}" value="{{ $role->id }}" {{ $checked ? 'checked' : '' }}>
                <label class="form-check-label" for="role-{{ $role->id }}">{{ $role->name }}</label>
            </div>
        @endforeach
        @error('roles.*')
        <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="d-flex gap-2 pt-2">
        <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-1">
            <i class="bi bi-check-lg"></i> {{ $isEdit ? '保存修改' : '创建' }}
        </button>
        <a href="{{ route('admins.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1">
            <i class="bi bi-arrow-left"></i> 返回列表
        </a>
    </div>
</form>
