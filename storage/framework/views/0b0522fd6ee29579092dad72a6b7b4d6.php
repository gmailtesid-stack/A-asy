<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — E-ASY POS</title>
    <link rel="icon" type="image/png" href="<?php echo e(asset('images/logo.png')); ?>?v=1.0.2">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #0f172a 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        /* Animated background blobs */
        body::before {
            content: '';
            position: fixed;
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(37,99,235,.25) 0%, transparent 70%);
            top: -100px; left: -100px;
            animation: float1 8s ease-in-out infinite;
            pointer-events: none;
        }
        body::after {
            content: '';
            position: fixed;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(56,189,248,.2) 0%, transparent 70%);
            bottom: -50px; right: -50px;
            animation: float2 10s ease-in-out infinite;
            pointer-events: none;
        }
        @keyframes float1 { 0%,100% { transform: translate(0,0); } 50% { transform: translate(40px, 30px); } }
        @keyframes float2 { 0%,100% { transform: translate(0,0); } 50% { transform: translate(-30px, -20px); } }

        .login-card {
            background: rgba(255,255,255,.05);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 20px;
            padding: 2.5rem 2rem;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 25px 60px rgba(0,0,0,.4);
            position: relative;
            z-index: 10;
        }

        .login-logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-logo h1 {
            font-size: 2.5rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: -1px;
        }
        .login-logo h1 span { color: #38bdf8; }
        .login-logo p { color: #94a3b8; font-size: .875rem; margin-top: .25rem; }

        label {
            display: block;
            color: #cbd5e1;
            font-size: .8rem;
            font-weight: 600;
            margin-bottom: .4rem;
            text-transform: uppercase;
            letter-spacing: .05em;
        }
        .input-wrap { position: relative; margin-bottom: 1.25rem; }
        .input-wrap i:not(.toggle-password) {
            position: absolute;
            left: 14px; top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            font-size: 1rem;
            z-index: 5;
        }
        .toggle-password {
            position: absolute;
            right: 14px; top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            cursor: pointer;
            z-index: 20;
            transition: color .2s;
            font-size: 1.1rem;
        }
        .toggle-password:hover { color: #38bdf8; }
        
        input[type="email"],
        input[type="password"],
        input[type="text"] {
            width: 100%;
            padding: .85rem 2.75rem .85rem 2.75rem;
            background: rgba(255,255,255,.07) !important;
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 12px;
            color: #f1f5f9;
            font-size: .95rem;
            font-family: 'Inter', sans-serif;
            transition: all .2s;
            outline: none;
            display: block;
        }
        input:focus {
            border-color: #38bdf8;
            box-shadow: 0 0 0 4px rgba(56,189,248,.15);
            background: rgba(255,255,255,.1) !important;
        }
        input::placeholder { color: #475569; }

        .remember-row {
            display: flex;
            align-items: center;
            gap: .5rem;
            margin-bottom: 1.5rem;
        }
        .remember-row label {
            text-transform: none;
            font-size: .85rem;
            color: #94a3b8;
            margin: 0;
        }
        input[type="checkbox"] { accent-color: #2563eb; width: 16px; height: 16px; cursor: pointer; }

        .btn-login {
            width: 100%;
            padding: .85rem;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: transform .15s, box-shadow .2s;
            box-shadow: 0 4px 20px rgba(37,99,235,.4);
        }
        .btn-login:hover { transform: translateY(-1px); box-shadow: 0 8px 25px rgba(37,99,235,.5); }
        .btn-login:active { transform: translateY(0); }

        .error-box {
            background: rgba(220,38,38,.15);
            border: 1px solid rgba(220,38,38,.3);
            border-radius: 8px;
            padding: .75rem 1rem;
            color: #fca5a5;
            font-size: .85rem;
            margin-bottom: 1rem;
        }

        .demo-accounts {
            margin-top: 1.5rem;
            padding: 1rem;
            background: rgba(255,255,255,.04);
            border-radius: 10px;
            border: 1px solid rgba(255,255,255,.08);
        }
        .demo-accounts p { color: #64748b; font-size: .75rem; margin-bottom: .5rem; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; }
        .demo-item {
            display: flex; justify-content: space-between; align-items: center;
            padding: .3rem 0;
            border-bottom: 1px solid rgba(255,255,255,.05);
        }
        .demo-item:last-child { border: none; }
        .demo-item span { color: #94a3b8; font-size: .78rem; }
        .demo-item .badge {
            font-size: .65rem; padding: 2px 8px; border-radius: 20px; font-weight: 600;
        }
        .badge-admin   { background: #fef3c7; color: #92400e; }
        .badge-manager { background: #dcfce7; color: #166534; }
        .badge-kasir   { background: #dbeafe; color: #1e40af; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-logo">
            <img src="<?php echo e(asset('images/logo.png')); ?>" alt="E-ASY Logo" style="width: 180px; margin-bottom: 1rem;">
            <p>Point of Sale System — Multi Outlet</p>
        </div>

        <?php if($errors->any()): ?>
        <div class="error-box">
            <i class="bi bi-exclamation-triangle-fill"></i> <?php echo e($errors->first()); ?>

        </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('login.post')); ?>">
            <?php echo csrf_field(); ?>
            <label>Email</label>
            <div class="input-wrap">
                <i class="bi bi-envelope"></i>
                <input type="email" name="email" placeholder="Masukkan email Anda"
                       value="<?php echo e(old('email')); ?>" required autofocus>
            </div>

            <label>Password</label>
            <div class="input-wrap">
                <i class="bi bi-lock"></i>
                <input type="password" name="password" id="passwordInput" placeholder="Masukkan password" required>
                <i class="bi bi-eye toggle-password" id="togglePassword"></i>
            </div>

            <div class="remember-row">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Ingat saya</label>
            </div>

            <button type="submit" class="btn-login">Masuk ke E-ASY POS</button>
        </form>

        
        <div class="demo-accounts">
            <p>🔑 Akun Demo</p>
            <div class="demo-item">
                <span>admin@easy-pos.id / password</span>
                <span class="badge badge-admin">Super Admin</span>
            </div>
            <div class="demo-item">
                <span>manager.jkt@easy-pos.id / password</span>
                <span class="badge badge-manager">Manager</span>
            </div>
            <div class="demo-item">
                <span>kasir.jkt@easy-pos.id / password</span>
                <span class="badge badge-kasir">Kasir</span>
            </div>
            <div class="demo-item" style="border-top: 1px solid rgba(255,255,255,0.1); margin-top: 5px; padding-top: 5px;">
                <span>test@easy-pos.id / password</span>
                <span class="badge badge-admin" style="background: #38bdf8; color: #0f172a;">Test Admin</span>
            </div>
        </div>
    </div>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const passwordInput = document.querySelector('#passwordInput');

        togglePassword.addEventListener('click', function (e) {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('bi-eye');
            this.classList.toggle('bi-eye-slash');
        });

        // Auto-fill demo credentials on click
        document.querySelectorAll('.demo-item').forEach(function(item) {
            item.style.cursor = 'pointer';
            item.addEventListener('click', function() {
                const text = item.querySelector('span').textContent;
                const parts = text.split(' / ');
                if (parts.length === 2) {
                    document.querySelector('input[name="email"]').value = parts[0].trim();
                    document.querySelector('#passwordInput').value = parts[1].trim();
                }
            });
        });

        // CSRF auto-refresh every 25 minutes to prevent 419 on idle pages
        setInterval(function() {
            fetch('/login', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.text())
                .then(html => {
                    const m = html.match(/name="_token" value="([^"]+)"/);
                    if (m) {
                        document.querySelector('input[name="_token"]').value = m[1];
                    }
                })
                .catch(() => {}); // silent fail
        }, 25 * 60 * 1000);
    </script>
</body>
</html>
<?php /**PATH C:\Users\Admin\.gemini\antigravity\scratch\easy-pos\resources\views\auth\login.blade.php ENDPATH**/ ?>