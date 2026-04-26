<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hergav - تسجيل الدخول</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Tahoma', Arial, sans-serif; background: linear-gradient(135deg, #1a3c5e 0%, #2d6a9f 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .card { background: white; border-radius: 16px; padding: 40px 36px; width: 100%; max-width: 440px; box-shadow: 0 20px 60px rgba(0,0,0,0.15); }
        .logo { text-align: center; margin-bottom: 28px; }
        .logo-icon { width: 64px; height: 64px; background: #1a3c5e; border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; font-size: 28px; font-weight: bold; color: white; }
        .logo h1 { font-size: 22px; font-weight: 700; color: #1a3c5e; }
        .logo p { color: #666; font-size: 13px; margin-top: 4px; }
        .company-number { display: inline-block; margin-top: 8px; background: #f0fdf4; border: 1px solid #86efac; border-radius: 8px; padding: 5px 14px; font-size: 13px; color: #16a34a; font-weight: 600; }
        .tabs { display: flex; margin-bottom: 24px; border-bottom: 2px solid #e0e0e0; }
        .tab { flex: 1; text-align: center; padding: 10px; cursor: pointer; font-size: 14px; font-weight: 600; color: #999; border-bottom: 2px solid transparent; margin-bottom: -2px; transition: all 0.2s; }
        .tab.active { color: #1a3c5e; border-bottom-color: #1a3c5e; }
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-size: 13px; font-weight: 500; margin-bottom: 6px; color: #333; }
        .form-group input { width: 100%; padding: 11px 14px; border: 1.5px solid #e0e0e0; border-radius: 8px; font-size: 14px; outline: none; font-family: 'Tahoma', Arial, sans-serif; }
        .form-group input:focus { border-color: #1a3c5e; }
        .btn { width: 100%; padding: 12px; background: #1a3c5e; color: white; border: none; border-radius: 8px; font-size: 15px; font-weight: 600; cursor: pointer; margin-top: 8px; font-family: 'Tahoma', Arial, sans-serif; }
        .btn:hover { background: #2d6a9f; }
        .btn-client { background: #d97706; }
        .btn-client:hover { background: #b45309; }
        .error { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: 10px 14px; border-radius: 8px; font-size: 13px; margin-bottom: 16px; }
        .form-panel { display: none; }
        .form-panel.active { display: block; }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo">
            <div class="logo-icon">H</div>
            <h1>شركة هرگاڤ للصرافة</h1>
            <p>نظام الإدارة المالية</p>
            <div class="company-number">
                رقم البورصة: 84 &nbsp;|&nbsp; <span dir="ltr">+964 750 445 7911</span>
            </div>
        </div>

        <!-- Tabs -->
        <div class="tabs">
            <div class="tab active" onclick="switchTab('staff')">👤 موظف / صراف</div>
            <div class="tab" onclick="switchTab('client')">🏦 عميل</div>
        </div>

        @if($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif

        <!-- Staff Login -->
        <div id="panel-staff" class="form-panel active">
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group">
                    <label>البريد الإلكتروني</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="أدخل البريد الإلكتروني" required>
                </div>
                <div class="form-group">
                    <label>كلمة المرور</label>
                    <input type="password" name="password" placeholder="أدخل كلمة المرور" required>
                </div>
                <button type="submit" class="btn">تسجيل دخول الموظف</button>
            </form>
        </div>

        <!-- Client Login -->
        <div id="panel-client" class="form-panel">
            <form method="POST" action="{{ route('portal.login.post') }}">
                @csrf
                <div class="form-group">
                    <label>البريد الإلكتروني أو رقم الهاتف</label>
                    <input type="text" name="login" value="{{ old('login') }}" placeholder="أدخل البريد أو الهاتف" required>
                </div>
                <div class="form-group">
                    <label>كلمة المرور</label>
                    <input type="password" name="password" placeholder="أدخل كلمة المرور" required>
                </div>
                <button type="submit" class="btn btn-client">تسجيل دخول العميل</button>
            </form>
        </div>
    </div>

    <script>
    function switchTab(tab) {
        document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.form-panel').forEach(p => p.classList.remove('active'));
        document.getElementById('panel-' + tab).classList.add('active');
        event.target.classList.add('active');
    }
    </script>
</body>
</html>