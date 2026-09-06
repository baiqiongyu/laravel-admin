<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * 更新管理员的验证规则
 *
 * 知识点：
 * 1. unique 规则的第三个参数 = 忽略的 ID。
 *    场景：编辑 admin（id=1）时，用户名没改，但 unique:admins,username
 *    会查到"库里已存在 admin"而报错。加上 ignore($this->admin) 后：
 *    "检查唯一性时，排除我自己"。
 * 2. 更新时密码改成 nullable：留空 = 不改密码。
 */
class UpdateAdminRequest extends FormRequest
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
        // 路由是 /admins/{admin}，Laravel 自动绑定 $this->admin
        $adminId = $this->route('admin')->id;

        return [
            'username' => [
                'required', 'string', 'max:50',
                Rule::unique('admins', 'username')->ignore($adminId),
            ],
            'nickname' => ['nullable', 'string', 'max:50'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'roles'    => ['nullable', 'array'],
            'roles.*'  => ['exists:roles,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'username.unique'    => '该用户名已被占用',
            'username.required'  => '请填写用户名',
            'password.confirmed' => '两次输入的密码不一致',
            'password.min'       => '密码至少 6 位',
            'roles.*.exists'     => '选择了不存在的角色',
        ];
    }
}
