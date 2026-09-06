<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * 创建管理员的验证规则
 *
 * 知识点：
 * 1. FormRequest 验证失败时，Laravel 会自动带着错误信息跳回上一页（不用你手写）
 * 2. authorize() 返回 true 才会继续验证，false 会抛 403
 *    （登录/权限检查已经由路由中间件做了，这里直接放行）
 */
class StoreAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'max:50', 'unique:admins,username'],
            'nickname' => ['nullable', 'string', 'max:50'],
            // confirmed = 要求表单里有 password_confirmation 字段且两次一致
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            // roles 是复选框数组：roles[] => ['exists:表,列'] 保证提交的角色 ID 都真实存在（防篡改）
            'roles'    => ['nullable', 'array'],
            'roles.*'  => ['exists:roles,id'],
        ];
    }

    // 自定义错误提示（可选，不写就用默认英文）
    public function messages(): array
    {
        return [
            'username.unique'   => '该用户名已被占用',
            'username.required' => '请填写用户名',
            'password.confirmed' => '两次输入的密码不一致',
            'password.min'      => '密码至少 6 位',
            'roles.*.exists'    => '选择了不存在的角色',
        ];
    }
}
