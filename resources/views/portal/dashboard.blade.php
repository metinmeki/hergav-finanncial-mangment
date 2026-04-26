<!DOCTYPE html>
@php $lang = session('lang', 'en'); $isRTL = $lang === 'ar'; @endphp
<html lang="{{ $lang }}" dir="{{ $isRTL ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hergav - {{ $isRTL ? 'حسابي' : 'My Account' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: {{ $isRTL ? "'Tahoma', 'Arial'" : "'Segoe UI'" }}, sans-serif; background: #f5f5f5; }
        .header { background: #1a3c5e; color: white; padding: 16px 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; }
        .header-logo { display: flex; align-items: center; gap: 12px; }
        .header-logo-icon { width: 36px; height: 36px; background: white; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #1a3c5e; font-size: 16px; flex-shrink: 0; }
        .header-logo h1 { font-size: 18px; font-weight: 700; }
        .header-logo p { font-size: 12px; opacity: 0.7; }
        .logout-btn { padding: 8px 16px; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 8px; color: white; cursor: pointer; font-size: 13px; text-decoration: none; }
        .main { max-width: 1200px; margin: 0 auto; padding: 24px 16px; }
        .welcome { margin-bottom: 24px; }
        .welcome h2 { font-size: 20px; font-weight: 700; color: #1a3c5e; }
        .welcome p { color: #666; font-size: 14px; margin-top: 4px; }
        .section-title { font-size: 16px; font-weight: 700; color: #1a3c5e; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 2px solid #e0e0e0; display: flex; align-items: center; gap: 8px; }
        .balances { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; }
        .balance-card { background: white; border-radius: 12px; padding: 20px 24px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); }
        .balance-label { font-size: 13px; color: #666; margin-bottom: 4px; }
        .balance-value { font-size: 22px; font-weight: 700; }
        .balance-status { display: inline-block; margin-top: 8px; padding: 4px 10px; border-radius: 8px; font-size: 12px; font-weight: 600; }
        .card { background: white; border-radius: 12px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); overflow: hidden; margin-bottom: 24px; }
        .card-header { padding: 16px 24px; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; }
        .card-header h3 { font-size: 15px; font-weight: 600; color: #1a3c5e; }
        .btn-primary { padding: 8px 16px; background: #1a3c5e; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 13px; font-weight: 600; text-decoration: none; display: inline-block; }
        .btn-secondary { padding: 8px 14px; background: white; border: 1px solid #e0e0e0; border-radius: 8px; cursor: pointer; font-size: 12px; color: #444; text-decoration: none; display: inline-block; }
        .form-control { padding: 8px 12px; border: 1.5px solid #e0e0e0; border-radius: 8px; font-size: 13px; outline: none; }
        .table-wrapper { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        table { width: 100%; border-collapse: collapse; min-width: 600px; }
        th { padding: 12px 16px; text-align: {{ $isRTL ? 'right' : 'left' }}; font-size: 13px; font-weight: 600; color: #444; background: #f8f9fa; border-bottom: 1px solid #e0e0e0; white-space: nowrap; }
        td { padding: 12px 16px; font-size: 13px; border-bottom: 1px solid #f0f0f0; text-align: {{ $isRTL ? 'right' : 'left' }}; }
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; white-space: nowrap; }
        .badge-success { background: #dcfce7; color: #16a34a; }
        .badge-danger { background: #fef2f2; color: #dc2626; }
        .amount-positive { color: #16a34a; font-weight: 600; }
        .amount-negative { color: #dc2626; font-weight: 600; }
        .stat-card { background: white; border-radius: 12px; padding: 16px 20px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); }
        .stat-label { font-size: 12px; color: #666; margin-bottom: 4px; }
        .stat-value { font-size: 18px; font-weight: 700; }
        .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 16px; }
        .lang-btn { padding: 6px 12px; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 6px; color: white; text-decoration: none; font-size: 12px; }
        .section-divider { margin: 32px 0; border: none; border-top: 2px dashed #e0e0e0; }
        .quick-filters { padding: 12px 24px; border-bottom: 1px solid #f0f0f0; display: flex; gap: 8px; flex-wrap: wrap; }

        @media (max-width: 768px) {
            .balances { grid-template-columns: repeat(2, 1fr); gap: 10px; }
            .grid-3 { grid-template-columns: 1fr; }
            .balance-card { padding: 14px 16px; }
            .balance-value { font-size: 18px; }
            .main { padding: 12px; }
            .welcome h2 { font-size: 16px; }
            .section-title { font-size: 14px; }
            .card-header { flex-direction: column; align-items: flex-start; }
            .card-header form { width: 100%; flex-wrap: wrap; }
            .header { padding: 12px 16px; }
            .header-logo h1 { font-size: 16px; }
            th, td { padding: 10px 12px; font-size: 12px; }
        }

        @media print {
            .header form, .btn-primary, .btn-secondary, form { display: none; }
            .main { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-logo">
            <div class="header-logo-icon">H</div>
            <div>
                <h1>Hergav</h1>
                <p>{{ $isRTL ? 'بوابة العميل' : 'Client Portal' }}</p>
            </div>
        </div>
        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
            @if($isRTL)
                <a href="{{ route('lang.switch', 'en') }}" class="lang-btn">🇬🇧 English</a>
            @else
                <a href="{{ route('lang.switch', 'ar') }}" class="lang-btn">🇮🇶 العربية</a>
            @endif
            <span style="font-size: 14px; opacity: 0.8;">{{ $client->full_name }}</span>
            <form method="POST" action="{{ route('portal.logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="logout-btn">🚪 {{ $isRTL ? 'خروج' : 'Logout' }}</button>
            </form>
        </div>
    </div>

    <div class="main">
        <div class="welcome">
            <h2>{{ $isRTL ? 'مرحباً' : 'Welcome' }}, {{ $client->full_name }}</h2>
            <p>{{ $client->code }} · {{ now()->format('d F Y, H:i') }}</p>
        </div>

        <!-- Notifications -->
        @if($unseenTransactions->count() > 0 || $unseenBailments->count() > 0)
        <div id="notifications" style="margin-bottom: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; flex-wrap: wrap; gap: 8px;">
                <h3 style="font-size: 16px; font-weight: 700; color: #1a3c5e; display: flex; align-items: center; gap: 8px;">
                    🔔 {{ $isRTL ? 'إشعارات جديدة' : 'New Notifications' }}
                    <span style="background: #dc2626; color: white; font-size: 12px; padding: 2px 8px; border-radius: 20px;">
                        {{ $unseenTransactions->count() + $unseenBailments->count() }}
                    </span>
                </h3>
                <button onclick="markAllSeen()" style="padding: 6px 14px; background: #f5f5f5; border: 1px solid #e0e0e0; border-radius: 8px; cursor: pointer; font-size: 12px; color: #666;">
                    {{ $isRTL ? '✓ تحديد الكل كمقروء' : '✓ Mark all as read' }}
                </button>
            </div>

            @foreach($unseenTransactions as $tx)
            <div style="background: white; border-radius: 12px; padding: 16px 20px; margin-bottom: 10px; box-shadow: 0 1px 4px rgba(0,0,0,0.08); border-left: 4px solid {{ $tx->type === 'deposit' ? '#16a34a' : '#dc2626' }}; border-right: 4px solid {{ $tx->type === 'deposit' ? '#16a34a' : '#dc2626' }}; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                <div style="display: flex; align-items: center; gap: 14px;">
                    <div style="width: 44px; height: 44px; border-radius: 50%; background: {{ $tx->type === 'deposit' ? '#dcfce7' : '#fef2f2' }}; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;">
                        {{ $tx->type === 'deposit' ? '↓' : '↑' }}
                    </div>
                    <div>
                        <div style="font-size: 15px; font-weight: 700; color: {{ $tx->type === 'deposit' ? '#16a34a' : '#dc2626' }};">
                            {{ $tx->type === 'deposit' ? ($isRTL ? 'إيداع' : 'Deposit') : ($isRTL ? 'سحب' : 'Withdrawal') }}
                            {{ $tx->currency->symbol }} {{ number_format($tx->amount, $tx->currency->code === 'IQD' ? 0 : 2) }} {{ $tx->currency->code }}
                        </div>
                        @if($tx->sender_name)
                        <div style="font-size: 13px; color: #666; margin-top: 4px;">
                            {{ $tx->type === 'deposit' ? ($isRTL ? 'أودع بواسطة' : 'Deposited by') : ($isRTL ? 'سحب بواسطة' : 'Withdrawn by') }}:
                            <strong style="color: #1a3c5e;">{{ $tx->sender_name }}</strong>
                            @if($tx->sender_phone) (<span dir="ltr">{{ $tx->sender_phone }}</span>) @endif
                        </div>
                        @endif
                        <div style="font-size: 12px; color: #999; margin-top: 2px;">
                            {{ $tx->created_at->format('d F Y, H:i') }}
                            &nbsp;|&nbsp;
                            {{ $isRTL ? 'الرصيد الجديد' : 'New balance' }}: <strong>{{ $tx->currency->symbol }} {{ number_format($tx->balance_after, $tx->currency->code === 'IQD' ? 0 : 2) }}</strong>
                        </div>
                    </div>
                </div>
                <span style="background: #eff6ff; color: #2563eb; font-size: 11px; padding: 4px 10px; border-radius: 20px; font-weight: 600;">
                    {{ $isRTL ? 'جديد' : 'NEW' }}
                </span>
            </div>
            @endforeach

            @foreach($unseenBailments as $tx)
            <div style="background: #fffbeb; border-radius: 12px; padding: 16px 20px; margin-bottom: 10px; box-shadow: 0 1px 4px rgba(0,0,0,0.08); border-left: 4px solid {{ $tx->type === 'deposit' ? '#d97706' : '#dc2626' }}; border-right: 4px solid {{ $tx->type === 'deposit' ? '#d97706' : '#dc2626' }}; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                <div style="display: flex; align-items: center; gap: 14px;">
                    <div style="width: 44px; height: 44px; border-radius: 50%; background: {{ $tx->type === 'deposit' ? '#fef9c3' : '#fef2f2' }}; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;">🏦</div>
                    <div>
                        <div style="font-size: 15px; font-weight: 700; color: {{ $tx->type === 'deposit' ? '#d97706' : '#dc2626' }};">
                            {{ $isRTL ? 'أمانة —' : 'Bailment —' }}
                            {{ $tx->type === 'deposit' ? ($isRTL ? 'إيداع' : 'Deposit') : ($isRTL ? 'سحب' : 'Withdrawal') }}
                            {{ $tx->currency->symbol }} {{ number_format($tx->amount, $tx->currency->code === 'IQD' ? 0 : 2) }} {{ $tx->currency->code }}
                        </div>
                        @if($tx->sender_name)
                        <div style="font-size: 13px; color: #666; margin-top: 4px;">
                            {{ $tx->type === 'deposit' ? ($isRTL ? 'أودع بواسطة' : 'Deposited by') : ($isRTL ? 'سحب بواسطة' : 'Withdrawn by') }}:
                            <strong style="color: #1a3c5e;">{{ $tx->sender_name }}</strong>
                            @if($tx->sender_phone) (<span dir="ltr">{{ $tx->sender_phone }}</span>) @endif
                        </div>
                        @endif
                        <div style="font-size: 12px; color: #999; margin-top: 2px;">
                            {{ $tx->created_at->format('d F Y, H:i') }}
                            &nbsp;|&nbsp;
                            {{ $isRTL ? 'رصيد الأمانة الجديد' : 'New bailment balance' }}: <strong>{{ $tx->currency->symbol }} {{ number_format($tx->balance_after, $tx->currency->code === 'IQD' ? 0 : 2) }}</strong>
                        </div>
                    </div>
                </div>
                <span style="background: #fef9c3; color: #d97706; font-size: 11px; padding: 4px 10px; border-radius: 20px; font-weight: 600;">
                    {{ $isRTL ? 'أمانة جديدة' : 'NEW BAILMENT' }}
                </span>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Date filter -->
        <div class="card" style="margin-bottom: 24px;">
            <div class="card-header">
                <h3>{{ $isRTL ? 'فلتر التاريخ' : 'Date Filter' }}</h3>
                <form method="GET" style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                    <input type="date" name="date_from" value="{{ $dateFrom }}" class="form-control" style="width: 145px;">
                    <span style="color: #666; font-size: 13px;">→</span>
                    <input type="date" name="date_to" value="{{ $dateTo }}" class="form-control" style="width: 145px;">
                    <button type="submit" class="btn-primary">{{ $isRTL ? 'عرض' : 'View' }}</button>
                    <a href="{{ route('portal.dashboard') }}" class="btn-secondary">{{ $isRTL ? 'الكل' : 'All' }}</a>
                    <a href="?date_from={{ today()->format('Y-m-d') }}&date_to={{ today()->format('Y-m-d') }}" class="btn-secondary">{{ $isRTL ? 'اليوم' : 'Today' }}</a>
                    <a href="?date_from={{ today()->subDays(6)->format('Y-m-d') }}&date_to={{ today()->format('Y-m-d') }}" class="btn-secondary">{{ $isRTL ? '7 أيام' : '7 days' }}</a>
                    <a href="?date_from={{ today()->subDays(29)->format('Y-m-d') }}&date_to={{ today()->format('Y-m-d') }}" class="btn-secondary">{{ $isRTL ? '30 يوم' : '30 days' }}</a>
                    <button onclick="window.print()" type="button" class="btn-secondary">🖨️</button>
                </form>
            </div>
        </div>

        <!-- REGULAR ACCOUNT -->
        <div class="section-title">💰 {{ $isRTL ? 'حسابي العادي' : 'My Regular Account' }}</div>

        <div class="balances">
            @foreach($balances as $code => $b)
            <div class="balance-card" style="border-top: 4px solid {{ $b['balance'] < 0 ? '#dc2626' : $b['color'] }};">
                <div class="balance-label">{{ $code }} {{ $isRTL ? 'الرصيد' : 'Balance' }}</div>
                <div class="balance-value" style="color: {{ $b['balance'] < 0 ? '#dc2626' : $b['color'] }};">
                    {{ $b['symbol'] }} {{ number_format($b['balance'], $code === 'IQD' ? 0 : 2) }}
                </div>
                <span class="balance-status" style="background: {{ $b['balance'] < 0 ? '#fef2f2' : ($b['balance'] == 0 ? '#f5f5f5' : '#f0fdf4') }}; color: {{ $b['balance'] < 0 ? '#dc2626' : ($b['balance'] == 0 ? '#999' : $b['color']) }};">
                    {{ $b['balance'] < 0 ? ($isRTL ? '⚠ مدين' : '⚠ Debt') : ($b['balance'] == 0 ? ($isRTL ? 'صفر' : 'Zero') : ($isRTL ? '✓ رصيد' : '✓ Active')) }}
                </span>
            </div>
            @endforeach
        </div>

        <div class="grid-3">
            <div class="stat-card" style="border-left: 4px solid #16a34a; border-right: 4px solid #16a34a;">
                <div class="stat-label">{{ $isRTL ? 'إجمالي الإيداعات' : 'Total Deposits' }}</div>
                <div class="stat-value amount-positive">{{ number_format($totalDeposits, 2) }}</div>
            </div>
            <div class="stat-card" style="border-left: 4px solid #dc2626; border-right: 4px solid #dc2626;">
                <div class="stat-label">{{ $isRTL ? 'إجمالي السحوبات' : 'Total Withdrawals' }}</div>
                <div class="stat-value amount-negative">{{ number_format($totalWithdrawals, 2) }}</div>
            </div>
            <div class="stat-card" style="border-left: 4px solid #1a3c5e; border-right: 4px solid #1a3c5e;">
                <div class="stat-label">{{ $isRTL ? 'الصافي' : 'Net' }}</div>
                <div class="stat-value {{ $net < 0 ? 'amount-negative' : 'amount-positive' }}">{{ number_format($net, 2) }}</div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>{{ $isRTL ? 'سجل معاملاتي' : 'My Transaction History' }}</h3>
                <span style="font-size: 13px; color: #666;">{{ $transactions->count() }} {{ $isRTL ? 'معاملة' : 'transactions' }}</span>
            </div>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>{{ $isRTL ? 'التاريخ' : 'Date' }}</th>
                            <th>{{ $isRTL ? 'النوع' : 'Type' }}</th>
                            <th>{{ $isRTL ? 'العملة' : 'Currency' }}</th>
                            <th>{{ $isRTL ? 'المبلغ' : 'Amount' }}</th>
                            <th>{{ $isRTL ? 'قبل' : 'Before' }}</th>
                            <th>{{ $isRTL ? 'بعد' : 'After' }}</th>
                            <th>{{ $isRTL ? 'المرسل / المستلم' : 'Sender / Receiver' }}</th>
                            <th>{{ $isRTL ? 'ملاحظات' : 'Notes' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $tx)
                        <tr>
                            <td style="color: #666; font-size: 12px; white-space: nowrap;">{{ $tx->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <span class="badge {{ $tx->type === 'deposit' ? 'badge-success' : 'badge-danger' }}">
                                    {{ $tx->type === 'deposit' ? ($isRTL ? 'إيداع' : 'Deposit') : ($isRTL ? 'سحب' : 'Withdrawal') }}
                                </span>
                            </td>
                            <td style="font-weight: 600; color: #1a3c5e;">{{ $tx->currency->code }}</td>
                            <td class="{{ $tx->type === 'withdrawal' ? 'amount-negative' : 'amount-positive' }}">
                                {{ $tx->currency->symbol }} {{ number_format($tx->amount, $tx->currency->code === 'IQD' ? 0 : 2) }}
                            </td>
                            <td style="color: #666;">{{ number_format($tx->balance_before, 2) }}</td>
                            <td class="{{ $tx->balance_after < 0 ? 'amount-negative' : 'amount-positive' }}">{{ number_format($tx->balance_after, 2) }}</td>
                            <td>
                                <div style="font-size: 13px; font-weight: 600; color: #1a3c5e;">{{ $tx->sender_name ?? '—' }}</div>
                                @if($tx->sender_phone)
                                <div style="font-size: 12px; color: #999;" dir="ltr">{{ $tx->sender_phone }}</div>
                                @endif
                            </td>
                            <td style="color: #666; font-size: 12px;">{{ $tx->notes ?? '—' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" style="text-align: center; color: #999; padding: 40px;">
                                {{ $isRTL ? 'لا توجد معاملات' : 'No transactions found.' }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <hr class="section-divider">

        <!-- BAILMENT ACCOUNT -->
        <div class="section-title">🏦 {{ $isRTL ? 'حساب الأمانات' : 'My Bailment Account' }}</div>

        <div class="balances">
            @foreach($bailmentBalances as $code => $b)
            <div class="balance-card" style="border-top: 4px solid {{ $b['balance'] < 0 ? '#dc2626' : $b['color'] }}; background: #fffbeb;">
                <div class="balance-label">{{ $code }} {{ $isRTL ? 'أمانة' : 'Bailment' }}</div>
                <div class="balance-value" style="color: {{ $b['balance'] < 0 ? '#dc2626' : $b['color'] }};">
                    {{ $b['symbol'] }} {{ number_format($b['balance'], $code === 'IQD' ? 0 : 2) }}
                </div>
                <span class="balance-status" style="background: {{ $b['balance'] < 0 ? '#fef2f2' : ($b['balance'] == 0 ? '#f5f5f5' : '#fffbeb') }}; color: {{ $b['balance'] < 0 ? '#dc2626' : ($b['balance'] == 0 ? '#999' : $b['color']) }};">
                    {{ $b['balance'] < 0 ? ($isRTL ? '⚠ مدين' : '⚠ Debt') : ($b['balance'] == 0 ? ($isRTL ? 'صفر' : 'Zero') : ($isRTL ? '✓ أمانة' : '✓ Held')) }}
                </span>
            </div>
            @endforeach
        </div>

        <div class="grid-3">
            <div class="stat-card" style="border-left: 4px solid #16a34a; border-right: 4px solid #16a34a;">
                <div class="stat-label">{{ $isRTL ? 'إجمالي الإيداعات' : 'Total Deposits' }}</div>
                <div class="stat-value amount-positive">{{ number_format($bailmentTotalDeposits, 2) }}</div>
            </div>
            <div class="stat-card" style="border-left: 4px solid #dc2626; border-right: 4px solid #dc2626;">
                <div class="stat-label">{{ $isRTL ? 'إجمالي السحوبات' : 'Total Withdrawals' }}</div>
                <div class="stat-value amount-negative">{{ number_format($bailmentTotalWithdrawals, 2) }}</div>
            </div>
            <div class="stat-card" style="border-left: 4px solid #1a3c5e; border-right: 4px solid #1a3c5e;">
                <div class="stat-label">{{ $isRTL ? 'عدد المعاملات' : 'Transactions' }}</div>
                <div class="stat-value" style="color: #1a3c5e;">{{ $bailmentTransactions->count() }}</div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>{{ $isRTL ? 'سجل معاملات الأمانات' : 'Bailment Transaction History' }}</h3>
                <span style="font-size: 13px; color: #666;">{{ $bailmentTransactions->count() }} {{ $isRTL ? 'معاملة' : 'transactions' }}</span>
            </div>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>{{ $isRTL ? 'التاريخ' : 'Date' }}</th>
                            <th>{{ $isRTL ? 'النوع' : 'Type' }}</th>
                            <th>{{ $isRTL ? 'العملة' : 'Currency' }}</th>
                            <th>{{ $isRTL ? 'المبلغ' : 'Amount' }}</th>
                            <th>{{ $isRTL ? 'قبل' : 'Before' }}</th>
                            <th>{{ $isRTL ? 'بعد' : 'After' }}</th>
                            <th>{{ $isRTL ? 'المرسل / المستلم' : 'Sender / Receiver' }}</th>
                            <th>{{ $isRTL ? 'ملاحظات' : 'Notes' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bailmentTransactions as $tx)
                        <tr>
                            <td style="color: #666; font-size: 12px; white-space: nowrap;">{{ $tx->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <span class="badge {{ $tx->type === 'deposit' ? 'badge-success' : 'badge-danger' }}">
                                    {{ $tx->type === 'deposit' ? ($isRTL ? 'إيداع' : 'Deposit') : ($isRTL ? 'سحب' : 'Withdrawal') }}
                                </span>
                            </td>
                            <td style="font-weight: 600; color: #1a3c5e;">{{ $tx->currency->code }}</td>
                            <td class="{{ $tx->type === 'withdrawal' ? 'amount-negative' : 'amount-positive' }}">
                                {{ $tx->currency->symbol }} {{ number_format($tx->amount, $tx->currency->code === 'IQD' ? 0 : 2) }}
                            </td>
                            <td style="color: #666;">{{ number_format($tx->balance_before, 2) }}</td>
                            <td class="{{ $tx->balance_after < 0 ? 'amount-negative' : 'amount-positive' }}">{{ number_format($tx->balance_after, 2) }}</td>
                            <td>
                                <div style="font-size: 13px; font-weight: 600; color: #1a3c5e;">{{ $tx->sender_name }}</div>
                                @if($tx->sender_phone)
                                <div style="font-size: 12px; color: #999;" dir="ltr">{{ $tx->sender_phone }}</div>
                                @endif
                            </td>
                            <td style="color: #666; font-size: 12px;">{{ $tx->notes ?? '—' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" style="text-align: center; color: #999; padding: 40px;">
                                {{ $isRTL ? 'لا توجد معاملات أمانات' : 'No bailment transactions found.' }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
    function markAllSeen() {
        fetch('{{ route('portal.mark-seen') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then(() => {
            const n = document.getElementById('notifications');
            if(n) n.style.display = 'none';
        });
    }

    setTimeout(() => {
        if (document.getElementById('notifications')) {
            markAllSeen();
        }
    }, 10000);
    </script>
</body>
</html>