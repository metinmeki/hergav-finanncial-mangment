<!DOCTYPE html>
<html lang="{{ session('lang', 'en') }}" dir="{{ session('lang', 'en') === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hergav - Client Portal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: {{ session('lang', 'en') === 'ar' ? "'Tahoma', 'Arial'" : "'Segoe UI'" }}, sans-serif; background: linear-gradient(135deg, #1a3c5e 0%, #2d6a9f 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card { background: white; border-radius: 16px; padding: 48px 40px; width: 100%; max-width: 420px; box-shadow: 0 20px 60px rgba(0,0,0,0.15); }
        .logo { text-align: center; margin-bottom: 32px; }
        .logo-icon { width: 64px; height: 64px; background: #1a3c5e; border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; font-size: 28px; font-weight: bold; color: white; }
        .logo h1 { font-size: 24px; font-weight: 700; color: #1a3c5e; }
        .logo p { color: #666; font-size: 14px; margin-top: 4px; }
        .badge-portal { display: inline-block; background: #eff6ff; color: #2563eb; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; margin-top: 8px; }
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-size: 14px; font-weight: 500; margin-bottom: 6px; color: #333; text-align: {{ session('lang', 'en') === 'ar' ? 'right' : 'left' }}; }
        .form-group input { width: 100%; padding: 12px 16px; border: 1.5px solid #e0e0e0; border-radius: 8px; font-size: 14px; outline: none; }
        .form-group input:focus { border-color: #1a3c5e; }
        .btn { width: 100%; padding: 13px; background: #1a3c5e; color: white; border: none; border-radius: 8px; font-size: 15px; font-weight: 600; cursor: pointer; margin-top: 8px; }
        .btn:hover { background: #2d6a9f; }
        .error { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: 10px 16px; border-radius: 8px; font-size: 14px; margin-bottom: 16px; }
        .hint { text-align: center; margin-top: 16px; font-size: 13px; color: #999; }
        .hint a { color: #1a3c5e; text-decoration: none; }
        .lang-switch { text-align: center; margin-bottom: 20px; }
        .lang-switch a { color: #1a3c5e; font-size: 13px; text-decoration: none; padding: 6px 12px; border: 1px solid #e0e0e0; border-radius: 6px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="lang-switch">
            @if(session('lang', 'en') === 'en')
                <a href="{{ route('lang.switch', 'ar') }}">🇮🇶 العربية</a>
            @else
                <a href="{{ route('lang.switch', 'en') }}">🇬🇧 English</a>
            @endif
        </div>

        <div class="logo">
    <div class="logo-icon">H</div>
    <h1>Hergav</h1>
    <p>{{ session('lang', 'en') === 'ar' ? 'بوابة العميل' : 'Client Portal' }}</p>
    <div style="margin-top: 10px; display: inline-block; background: #f0fdf4; border: 1px solid #86efac; border-radius: 8px; padding: 6px 16px; font-size: 13px; color: #16a34a; font-weight: 600;">
        Stock Exchange No: 84 &nbsp;|&nbsp; رقم البورصة: 84
    </div>
    <span class="badge-portal">🔒 {{ session('lang', 'en') === 'ar' ? 'وصول آمن' : 'Secure Client Access' }}</span>
</div>

        @if($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('portal.login.post') }}">
            @csrf
            <div class="form-group">
                <label>{{ session('lang', 'en') === 'ar' ? 'البريد الإلكتروني أو رقم الهاتف' : 'Email or Phone Number' }}</label>
                <input type="text" name="login" value="{{ old('login') }}" placeholder="{{ session('lang', 'en') === 'ar' ? 'أدخل البريد أو الهاتف' : 'Enter email or phone' }}" required>
            </div>
            <div class="form-group">
                <label>{{ session('lang', 'en') === 'ar' ? 'كلمة المرور' : 'Password' }}</label>
                <input type="password" name="password" placeholder="{{ session('lang', 'en') === 'ar' ? 'أدخل كلمة المرور' : 'Enter password' }}" required>
            </div>
            <button type="submit" class="btn">{{ session('lang', 'en') === 'ar' ? 'تسجيل الدخول' : 'Login to My Account' }}</button>
        </form>

        <div class="hint">
            {{ session('lang', 'en') === 'ar' ? 'تسجيل دخول الموظفين؟' : 'Staff login?' }}
            <a href="{{ route('login') }}">{{ session('lang', 'en') === 'ar' ? 'انقر هنا' : 'Click here' }}</a>
        </div>
    </div>
</body>
</html>