<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hergav - Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, #1a3c5e 0%, #2d6a9f 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card { background: white; border-radius: 16px; padding: 48px 40px; width: 100%; max-width: 420px; box-shadow: 0 20px 60px rgba(0,0,0,0.15); }
        .logo { text-align: center; margin-bottom: 32px; }
        .logo-icon { width: 64px; height: 64px; background: #1a3c5e; border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; font-size: 28px; font-weight: bold; color: white; }
        .logo h1 { font-size: 24px; font-weight: 700; color: #1a3c5e; }
        .logo p { color: #666; font-size: 14px; margin-top: 4px; }
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-size: 14px; font-weight: 500; margin-bottom: 6px; color: #333; }
        .form-group input { width: 100%; padding: 12px 16px; border: 1.5px solid #e0e0e0; border-radius: 8px; font-size: 14px; outline: none; transition: border-color 0.2s; }
        .form-group input:focus { border-color: #1a3c5e; }
        .btn { width: 100%; padding: 13px; background: #1a3c5e; color: white; border: none; border-radius: 8px; font-size: 15px; font-weight: 600; cursor: pointer; margin-top: 8px; }
        .btn:hover { background: #2d6a9f; }
        .error { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: 10px 16px; border-radius: 8px; font-size: 14px; margin-bottom: 16px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo">
            <div class="logo-icon">H</div>
            <h1>Hergav</h1>
            <p>Financial Management System</p>
        </div>

        @if($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="Enter email" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter password" required>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>
    </div>
</body>
</html>