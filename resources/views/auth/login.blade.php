<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>登录 - 后台管理系统</title>
    {{-- Bootstrap Icons：与后台内部页面统一图标语言 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* ---------- 基础重置 ---------- */
        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --brand-1: #4f46e5;   /* 靛蓝 */
            --brand-2: #7c3aed;   /* 紫 */
            --text-main: #1e293b;
            --text-sub: #64748b;
            --border: #e2e8f0;
            --danger: #dc2626;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI",
            "PingFang SC", "Microsoft YaHei", sans-serif;
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
        }

        /* ---------- 左侧品牌区 ---------- */
        .brand-panel {
            flex: 1.2;
            background: linear-gradient(135deg, var(--brand-1), var(--brand-2));
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 64px;
            position: relative;
            overflow: hidden;
        }

        /* 装饰性大圆：超出容器被 overflow hidden 裁掉，营造层次感 */
        .brand-panel::before,
        .brand-panel::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, .08);
        }
        .brand-panel::before { width: 420px; height: 420px; top: -160px; right: -120px; }
        .brand-panel::after  { width: 300px; height: 300px; bottom: -100px; left: -80px; }

        .brand-logo {
            width: 56px; height: 56px;
            background: rgba(255, 255, 255, .18);
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 28px;
            margin-bottom: 28px;
        }

        .brand-panel h1 { font-size: 32px; font-weight: 700; letter-spacing: 1px; margin-bottom: 14px; }
        .brand-panel p  { font-size: 16px; opacity: .85; line-height: 1.8; max-width: 420px; }

        .brand-features {
            list-style: none;
            margin-top: 48px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .brand-features li {
            display: flex; align-items: center; gap: 12px;
            font-size: 15px; opacity: .9;
        }
        .brand-features .dot {
            width: 8px; height: 8px; border-radius: 50%;
            background: #a5f3fc; flex-shrink: 0;
        }

        .brand-footer {
            position: absolute;
            bottom: 28px; left: 64px; right: 64px;
            font-size: 13px; opacity: .6;
        }

        /* ---------- 右侧表单区 ---------- */
        .form-panel {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px 24px;
            background: #fff;
        }

        .login-card { width: 100%; max-width: 380px; }

        .login-card h2 { font-size: 24px; font-weight: 700; margin-bottom: 6px; }
        .login-card .sub { color: var(--text-sub); font-size: 14px; margin-bottom: 32px; }

        /* 字段组：标签 + 带图标输入框 */
        .field { margin-bottom: 20px; }
        .field label { display: block; font-size: 14px; font-weight: 500; margin-bottom: 8px; }

        .input-wrap { position: relative; }

        .input-wrap .icon {
            position: absolute; left: 14px; top: 50%;
            transform: translateY(-50%);
            color: var(--text-sub);
            font-size: 15px;
            pointer-events: none;   /* 图标不拦截点击 */
        }

        .input-wrap input {
            width: 100%;
            padding: 12px 44px 12px 42px;   /* 左侧给图标留位，右侧给切换按钮留位 */
            font-size: 15px;
            border: 1px solid var(--border);
            border-radius: 10px;
            outline: none;
            transition: border-color .15s, box-shadow .15s;
        }

        /* 聚焦反馈：边框变品牌色 + 柔和光晕 */
        .input-wrap input:focus {
            border-color: var(--brand-1);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, .12);
        }

        .input-wrap.has-error input {
            border-color: var(--danger);
        }

        /* 密码可见切换按钮 */
        .toggle-pwd {
            position: absolute; right: 10px; top: 50%;
            transform: translateY(-50%);
            border: none; background: none;
            color: var(--text-sub); font-size: 15px;
            cursor: pointer; padding: 4px 6px;
        }
        .toggle-pwd:hover { color: var(--brand-1); }

        .field .error-msg {
            color: var(--danger);
            font-size: 13px;
            margin-top: 6px;
            display: flex; align-items: center; gap: 4px;
        }

        .submit-btn {
            width: 100%;
            padding: 13px;
            margin-top: 8px;
            font-size: 16px;
            color: #fff;
            background: linear-gradient(135deg, var(--brand-1), var(--brand-2));
            border: none; border-radius: 10px;
            cursor: pointer;
            transition: opacity .15s, transform .1s;
        }
        .submit-btn:hover  { opacity: .92; }
        .submit-btn:active { transform: scale(.985); }

        .form-footer {
            margin-top: 28px;
            text-align: center;
            font-size: 13px;
            color: var(--text-sub);
        }

        /* ---------- 响应式：窄屏隐藏品牌区，只留表单 ---------- */
        @media (max-width: 860px) {
            .brand-panel { display: none; }
            .form-panel  { flex: 1; }
        }
    </style>
</head>
<body>

{{-- 左侧品牌区 --}}
<div class="brand-panel">
    <div class="brand-logo"><i class="bi bi-lightning-charge-fill"></i></div>
    <h1>后台管理系统</h1>
    <p>一套基于 Laravel 构建的通用后台模板，内置权限管理、角色分配与动态菜单，开箱即用。</p>
    <ul class="brand-features">
        <li><span class="dot"></span>RBAC 权限体系，精细化控制每个功能入口</li>
        <li><span class="dot"></span>动态菜单渲染，不同角色看到不同后台</li>
        <li><span class="dot"></span>模块化设计，边学边扩展</li>
    </ul>
    <div class="brand-footer">© {{ date('Y') }} Laravel Admin · Powered by Laravel</div>
</div>

{{-- 右侧表单区 --}}
<div class="form-panel">
    <div class="login-card">
        <h2>欢迎回来 👋</h2>
        <p class="sub">请登录你的管理员账号</p>

        <form method="POST" action="{{ route('login.submit') }}">
            @csrf

            <div class="field">
                <label for="username">用户名</label>
                <div class="input-wrap {{ $errors->has('username') ? 'has-error' : '' }}">
                    <span class="icon"><i class="bi bi-person"></i></span>
                    <input type="text" id="username" name="username"
                           placeholder="请输入用户名"
                           value="{{ old('username') }}"
                           autofocus required>
                </div>
                @error('username')
                <p class="error-msg"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                @enderror
            </div>

            <div class="field">
                <label for="password">密码</label>
                <div class="input-wrap {{ $errors->has('password') ? 'has-error' : '' }}">
                    <span class="icon"><i class="bi bi-lock"></i></span>
                    <input type="password" id="password" name="password"
                           placeholder="请输入密码"
                           autocomplete="current-password" required>
                    {{-- type="button" 避免触发表单提交 --}}
                    <button type="button" class="toggle-pwd" data-target="password"><i class="bi bi-eye"></i></button>
                </div>
                @error('password')
                <p class="error-msg"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="submit-btn">登 录</button>
        </form>

        <div class="form-footer">忘记密码？请联系超级管理员重置</div>
    </div>
</div>

<script>
    // 密码可见切换：点眼睛按钮，在 password / text 之间切换 input 类型
    document.querySelectorAll('.toggle-pwd').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var input = document.getElementById(this.dataset.target);
            input.type = input.type === 'password' ? 'text' : 'password';
        });
    });
</script>
</body>
</html>
