<!DOCTYPE html>
<html lang="{{ $currentLang }}" dir="{{ $isRTL ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hergav - @yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background: #f5f5f5; }
        .layout { display: flex; min-height: 100vh; }
        .sidebar { width: 240px; background: #1a3c5e; color: white; display: flex; flex-direction: column; position: fixed; height: 100vh; z-index: 100; left: 0; top: 0; }
        .sidebar-logo { padding: 24px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); display: flex; align-items: center; gap: 12px; }
        .sidebar-logo-icon { width: 40px; height: 40px; background: white; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #1a3c5e; font-size: 18px; flex-shrink: 0; }
        .sidebar-logo-text h2 { font-size: 18px; font-weight: 700; }
        .sidebar-logo-text span { font-size: 11px; opacity: 0.7; }
        .sidebar-nav { padding: 16px 0; flex: 1; }
        .nav-item { display: flex; align-items: center; gap: 12px; padding: 12px 20px; color: white; text-decoration: none; font-size: 14px; border-left: 3px solid transparent; transition: all 0.2s; }
        .nav-item:hover { background: rgba(255,255,255,0.1); }
        .nav-item.active { background: rgba(255,255,255,0.15); border-left-color: white; }
        .sidebar-bottom { padding: 16px 20px; border-top: 1px solid rgba(255,255,255,0.1); }
        .logout-btn { width: 100%; padding: 10px; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 8px; color: white; cursor: pointer; font-size: 14px; text-align: center; text-decoration: none; display: block; }
        .main { margin-left: 240px; flex: 1; padding: 32px; }
        .page-header { margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; }
        .page-title h1 { font-size: 24px; font-weight: 700; color: #1a3c5e; }
        .page-title p { color: #666; font-size: 14px; margin-top: 4px; }
        .btn-primary { padding: 10px 20px; background: #1a3c5e; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 14px; font-weight: 600; text-decoration: none; display: inline-block; }
        .btn-primary:hover { background: #2d6a9f; color: white; }
        .btn-secondary { padding: 10px 16px; background: white; border: 1px solid #e0e0e0; border-radius: 8px; cursor: pointer; font-size: 13px; font-weight: 500; text-decoration: none; color: #444; display: inline-block; }
        .card { background: white; border-radius: 12px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); overflow: hidden; }
        .card-body { padding: 24px; }
        .table { width: 100%; border-collapse: collapse; }
        .table th { padding: 12px 16px; text-align: left; font-size: 13px; font-weight: 600; color: #444; background: #f8f9fa; border-bottom: 1px solid #e0e0e0; }
        .table td { padding: 12px 16px; font-size: 14px; border-bottom: 1px solid #f0f0f0; }
        .table tr:nth-child(even) td { background: #fafafa; }
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .badge-success { background: #dcfce7; color: #16a34a; }
        .badge-danger { background: #fef2f2; color: #dc2626; }
        .badge-info { background: #eff6ff; color: #2563eb; }
        .badge-warning { background: #fffbeb; color: #d97706; }
        .alert-success { background: #dcfce7; border: 1px solid #86efac; color: #16a34a; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; }
        .alert-error { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; }
        .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 999; }
        .modal { background: white; border-radius: 16px; padding: 32px; width: 440px; max-width: 95vw; max-height: 90vh; overflow-y: auto; }
        .modal h2 { font-size: 18px; font-weight: 700; color: #1a3c5e; margin-bottom: 24px; }
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-size: 13px; font-weight: 500; margin-bottom: 6px; color: #333; }
        .form-control { width: 100%; padding: 10px 14px; border: 1.5px solid #e0e0e0; border-radius: 8px; font-size: 14px; outline: none; }
        .form-control:focus { border-color: #1a3c5e; }
        .amount-positive { color: #16a34a; font-weight: 600; }
        .amount-negative { color: #dc2626; font-weight: 600; }
        .stat-card { background: white; border-radius: 12px; padding: 20px 24px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); }
        .stat-card .label { font-size: 13px; color: #666; margin-bottom: 8px; }
        .stat-card .value { font-size: 22px; font-weight: 700; }
        .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
        .grid-2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }
        select.form-control { background: white; }

        /* RTL Support */
        [dir="rtl"] .sidebar { left: auto; right: 0; }
        [dir="rtl"] .main { margin-left: 0; margin-right: 240px; }
        [dir="rtl"] .nav-item { border-left: none; border-right: 3px solid transparent; flex-direction: row-reverse; }
        [dir="rtl"] .nav-item.active { border-left: none; border-right-color: white; }
        [dir="rtl"] .page-header { flex-direction: row-reverse; }
        [dir="rtl"] .table th { text-align: right; }
        [dir="rtl"] .table td { text-align: right; }
        [dir="rtl"] .modal h2 { text-align: right; }
        [dir="rtl"] .form-group label { text-align: right; }
        [dir="rtl"] .page-title h1 { text-align: right; }
        [dir="rtl"] body { font-family: 'Tahoma', 'Arial', sans-serif; }
    </style>
</head>
<body>
<div class="layout">
    <div class="sidebar">
        <div class="sidebar-logo">
            <div class="sidebar-logo-icon">H</div>
            <div class="sidebar-logo-text">
                <h2>Hergav</h2>
                <span>{{ $trans['main_branch'] }}</span>
            </div>
        </div>

        <!-- Language Toggle -->
        <div style="padding: 10px 16px; border-bottom: 1px solid rgba(255,255,255,0.1);">
            @if($currentLang === 'en')
                <a href="{{ route('lang.switch', 'ar') }}" style="display: block; text-align: center; padding: 8px; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 8px; color: white; text-decoration: none; font-size: 13px; font-weight: 600;">
                    🇮🇶 العربية
                </a>
            @else
                <a href="{{ route('lang.switch', 'en') }}" style="display: block; text-align: center; padding: 8px; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 8px; color: white; text-decoration: none; font-size: 13px; font-weight: 600;">
                    🇬🇧 English
                </a>
            @endif
        </div>

        <nav class="sidebar-nav">
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">📊 {{ $trans['dashboard'] }}</a>
            <a href="{{ route('clients.index') }}" class="nav-item {{ request()->routeIs('clients.*') ? 'active' : '' }}">👥 {{ $trans['clients'] }}</a>
            <a href="{{ route('transactions.index') }}" class="nav-item {{ request()->routeIs('transactions.*') ? 'active' : '' }}">💰 {{ $trans['transactions'] }}</a>
            <a href="{{ route('exchange.index') }}" class="nav-item {{ request()->routeIs('exchange.*') ? 'active' : '' }}">💱 {{ $trans['exchange_rates'] }}</a>
            <a href="{{ route('users.index') }}" class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">👤 {{ $trans['users'] }}</a>
        </nav>

        <div class="sidebar-bottom">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn">🚪 {{ $trans['logout'] }}</button>
            </form>
        </div>
    </div>

    <div class="main">
        @if(session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert-error">{{ $errors->first() }}</div>
        @endif
        @yield('content')
    </div>
</div>
@yield('scripts')
</body>
</html>