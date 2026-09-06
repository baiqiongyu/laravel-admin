# 第 5 课：管理员管理模块（CRUD）— 完成记录

## 本课完成内容

在已有 RBAC 骨架（登录认证、权限中间件、动态菜单）之上，完成了后台第一个完整 CRUD 模块：**管理员管理**。

## 新增/修改的文件

| 文件 | 说明 |
|---|---|
| `routes/web.php` | 新增 `Route::resource('admins')`，套 `admin.auth` + `permission:admin.manage` 双层中间件 |
| `app/Http/Requests/StoreAdminRequest.php` | 创建验证：`unique` 用户名唯一、`confirmed` 密码确认、`exists` 角色防篡改 |
| `app/Http/Requests/UpdateAdminRequest.php` | 更新验证：`Rule::unique()->ignore($id)` 排除自身，密码 nullable（留空不改） |
| `app/Http/Controllers/AdminController.php` | 资源控制器 6 个动作：搜索、`with('roles')` 预加载防 N+1、`paginate(10)`、`Hash::make`、`sync()` 同步角色、删除前 `detach()`、禁止删除自己 |
| `resources/views/admins/index.blade.php` | 列表页：搜索框、角色徽章、`@forelse` 空态、删除确认、`$admins->links()` 分页 |
| `resources/views/admins/form.blade.php` | 表单局部模板（create/edit 共用），`old()` 回填、`@error` 错误提示 |
| `resources/views/admins/create.blade.php` / `edit.blade.php` | 引用 form 局部模板 |
| `resources/views/layouts/admin.blade.php` | 布局新增全局 flash 消息（success/error） |
| `app/Providers/AppServiceProvider.php` | `Paginator::useBootstrapFive()` 分页样式切为 Bootstrap |
| `database/seeders/MenuSeeder.php` | 管理员管理菜单绑定 `admins.index` 路由（幂等，已重新 seed） |
| `tests/Feature/AdminManagementTest.php` | 7 个功能测试全部通过（19 断言） |

## 验证结果

- `php artisan route:list`：admins 资源路由 6 条全部注册成功
- `php artisan test --filter=AdminManagementTest`：**7 passed (19 assertions)**
- 覆盖场景：未登录跳转、超管访问、editor 403、创建+哈希校验、重复用户名报错、防删自己、删除清理中间表

## 环境备忘

- Herd PHP 路径：`C:\Users\Laptop\.config\herd\bin\php84\php.exe`（bash 里需用全路径）

## 下一课候选

1. 角色管理模块（复用本课套路，练习独立实现）
2. 菜单管理（无限级分类的递归渲染）
3. 操作日志（Observer / 事件监听入门）
