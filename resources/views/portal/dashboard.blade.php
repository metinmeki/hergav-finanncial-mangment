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
        .header { background: #1a3c5e; color: white; padding: 16px 24px; display: flex; justify-content: space-between; align-items: center; }
        .header-logo { display: flex; align-items: center; gap: 12px; }
        .header-logo-icon { width: 36px; height: 36px; background: white; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #1a3c5e; font-size: 16px; }
        .header-logo h1 { font-size: 18px; font-weight: 700; }
        .header-logo p { font-size: 12px; opacity: 0.7; }
        .logout-btn { padding: 8px 16px; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 8px; color: white; cursor: pointer; font-size: 13px; text-decoration: none; }
        .main { max-width: 1200px; margin: 0 auto; padding: 24px; }
        .welcome { margin-bottom: 24px; }
        .welcome h2 { font-size: 20px; font-weight: 700; color: #1a3c5e; }
        .welcome p { color: #666; font-size: 14px; margin-top: 4px; }
        .balances { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; }
        .balance-card { background: white; border-radius: 12px; padding: 20px 24px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); }
        .balance-label { font-size: 13px; color: #666; margin-bottom: 4px; }
        .balance-value { font-size: 22px; font-weight: 700; }
        .balance-status { display: inline-block; margin-top: 8px; padding: 4px 10px; border-radius: 8px; font-size: 12px; font-weight: 600; }
        .card { background: white; border-radius: 12px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); overflow: hidden; margin-bottom: 16px; }
        .card-header { padding: 16px 24px; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; }
        .card-header h3 { font-size: 15px; font-weight: 600; color: #1a3c5e; }
        .print-btn { padding: 8px 16px; background: white; border: 1px solid #e0e0e0; border-radius: 8px; cursor: pointer; font-size: 13px; }
        .btn-primary { padding: 8px 16px; background: #1a3c5e; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 13px; font-weight: 600; text-decoration: none; display: inline-block; }
        .btn-secondary { padding: 8px 14px; background: white; border: 1px solid #e0e0e0; border-radius: 8px; cursor: pointer; font-size: 12px; color: #444; text-decoration: none; display: inline-block; }
        .form-control { padding: 8px 12px; border: 1.5px solid #e0e0e0; border-radius: 8px; font-size: 13px; outline: none; width: 100%; }
        table { width: 100%; border-collapse: collapse; }
        th { padding: 12px 16px; text-align: {{ $isRTL ? 'right' : 'left' }}; font-size: 13px; font-weight: 600; color: #444; background: #f8f9fa; border-bottom: 1px solid #e0e0e0; }
        td { padding: 12px 16px; font-size: 13px; border-bottom: 1px solid #f0f0f0; text-align: {{ $isRTL ? 'right' : 'left' }}; }
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
        .badge-success { background: #dcfce7; color: #16a34a; }
        .badge-danger { background: #fef2f2; color: #dc2626; }
        .amount-positive { color: #16a34a; font-weight: 600; }
        .amount-negative { color: #dc2626; font-weight: 600; }
        .stat-card { background: white; border-radius: 12px; padding: 16px 20px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); }
        .stat-label { font-size: 12px; color: #666; margin-bottom: 4px; }
        .stat-value { font-size: 18px; font-weight: 700; }
        .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 16px; }
        .lang-btn { padding: 6px 12px; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 6px; color: white; text-decoration: none; font-size: 12px; }
        @media print {
            .header form, .print-btn, form, .lang-btn { display: none; }
            .main { padding: 0; }
        }
        @media (max-width: 768px) {
            .balances { grid-template-columns: repeat(2, 1fr); }
            .grid-3 { grid-template-columns: 1fr; }
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
        <div style="display: flex; align-items: center; gap: 12px;">
            @if($isRTL)
                <a href="{{ route('lang.switch', 'en') }}" class="lang-btn">🇬🇧 English</a>
            @else
                <a href="{{ route('lang.switch', 'ar') }}" class="lang-btn">🇮🇶 العربية</a>
            @endif
            <span style="font-size: 14px; opacity: 0.8;">{{ $client->full_name }}</span>
            <form method="POST" action="{{ route('portal.logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="logout-btn">🚪 {{ $isRTL ? 'تسجيل الخروج' : 'Logout' }}</button>
            </form>
        </div>
    </div>

    <div class="main">
        <div class="welcome">
            <h2>{{ $isRTL ? 'مرحباً' : 'Welcome' }}, {{ $client->full_name }}</h2>
            <p>{{ $client->code }} · {{ now()->format('d F Y, H:i') }}</p>
        </div>

        <!-- Balances -->
        <div class="balances">
            @foreach($balances as $code => $b)
            <div class="balance-card" style="border-top: 4px solid {{ $b['balance'] < 0 ? '#dc2626' : $b['color'] }};">
                <div class="balance-label">{{ $code }} {{ $isRTL ? 'الرصيد' : 'Balance' }}</div>
                <div class="balance-value" style="color: {{ $b['balance'] < 0 ? '#dc2626' : $b['color'] }};">
                    {{ $b['symbol'] }} {{ number_format($b['balance'], $code === 'IQD' ? 0 : 2) }}
                </div>
                <span class="balance-status" style="background: {{ $b['balance'] < 0 ? '#fef2f2' : '#f0fdf4' }}; color: {{ $b['balance'] < 0 ? '#dc2626' : $b['color'] }};">
                    @if($b['balance'] < 0)
                        {{ $isRTL ? '⚠ مديون' : '⚠ Debt' }}
                    @elseif($b['balance'] == 0)
                        {{ $isRTL ? 'صفر' : 'Zero' }}
                    @else
                        {{ $isRTL ? '✓ نشط' : '✓ Active' }}
                    @endif
                </span>
            </div>
            @endforeach
        </div>

        <!-- Summary stats -->
        <div class="grid-3">
            <div class="stat-card" style="border-left: 4px solid #16a34a; border-right: 4px solid #16a34a;">
                <div class="stat-label">{{ $isRTL ? 'إجمالي الإيداعات' : 'Total Deposits' }}</div>
                <div class="stat-value amount-positive">$ {{ number_format($totalDeposits, 2) }}</div>
                <div style="font-size: 11px; color: #2563eb; margin-top: 4px;">IQD {{ number_format($totalDeposits * ($rates['IQD'] ?? 0), 0) }}</div>
            </div>
            <div class="stat-card" style="border-left: 4px solid #dc2626; border-right: 4px solid #dc2626;">
                <div class="stat-label">{{ $isRTL ? 'إجمالي السحوبات' : 'Total Withdrawals' }}</div>
                <div class="stat-value amount-negative">$ {{ number_format($totalWithdrawals, 2) }}</div>
                <div style="font-size: 11px; color: #2563eb; margin-top: 4px;">IQD {{ number_format($totalWithdrawals * ($rates['IQD'] ?? 0), 0) }}</div>
            </div>
            <div class="stat-card" style="border-left: 4px solid #1a3c5e; border-right: 4px solid #1a3c5e;">
                <div class="stat-label">{{ $isRTL ? 'الصافي' : 'Net' }}</div>
                <div class="stat-value {{ $net < 0 ? 'amount-negative' : 'amount-positive' }}">$ {{ number_format($net, 2) }}</div>
                <div style="font-size: 11px; color: #2563eb; margin-top: 4px;">IQD {{ number_format($net * ($rates['IQD'] ?? 0), 0) }}</div>
            </div>
        </div>

        <!-- Transaction History -->
        <div class="card">
            <div class="card-header">
                <h3>{{ $isRTL ? 'سجل معاملاتي' : 'My Transaction History' }}</h3>
                <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
                    <!-- Date range filter -->
                    <form method="GET" style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                        <input type="date" name="date_from" value="{{ $dateFrom }}" class="form-control" style="width: 150px;">
                        <span style="color: #666; font-size: 13px;">→</span>
                        <input type="date" name="date_to" value="{{ $dateTo }}" class="form-control" style="width: 150px;">
                        <button type="submit" class="btn-primary">{{ $isRTL ? 'عرض' : 'View' }}</button>
                        <a href="{{ route('portal.dashboard') }}" class="btn-secondary">{{ $isRTL ? 'الكل' : 'All' }}</a>
                    </form>
                    <button onclick="window.print()" class="print-btn">🖨️ {{ $isRTL ? 'طباعة' : 'Print' }}</button>
                </div>
            </div>

            <!-- Quick filters -->
            <div style="padding: 12px 24px; border-bottom: 1px solid #f0f0f0; display: flex; gap: 8px;">
                <a href="{{ route('portal.dashboard') }}" class="btn-secondary">{{ $isRTL ? 'الكل' : 'All' }}</a>
                <a href="?date_from={{ today()->format('Y-m-d') }}&date_to={{ today()->format('Y-m-d') }}" class="btn-secondary">{{ $isRTL ? 'اليوم' : 'Today' }}</a>
                <a href="?date_from={{ today()->subDays(6)->format('Y-m-d') }}&date_to={{ today()->format('Y-m-d') }}" class="btn-secondary">{{ $isRTL ? '7 أيام' : '7 days' }}</a>
                <a href="?date_from={{ today()->subDays(29)->format('Y-m-d') }}&date_to={{ today()->format('Y-m-d') }}" class="btn-secondary">{{ $isRTL ? '30 يوم' : '30 days' }}</a>
                <span style="color: #666; font-size: 13px; padding: 8px 0;">{{ $transactions->count() }} {{ $isRTL ? 'معاملة' : 'transactions' }}</span>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>{{ $isRTL ? 'التاريخ' : 'Date' }}</th>
                        <th>{{ $isRTL ? 'النوع' : 'Type' }}</th>
                        <th>{{ $isRTL ? 'الأصلي' : 'Original' }}</th>
                        <th>USD</th>
                        <th>IQD</th>
                        <th>EUR</th>
                        <th>TRY</th>
                        <th>{{ $isRTL ? 'قبل' : 'Before' }}</th>
                        <th>{{ $isRTL ? 'بعد' : 'After' }}</th>
                        <th>{{ $isRTL ? 'المرسل' : 'Sender' }}</th>
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
                        <td style="font-weight: 600; color: #1a3c5e;">
                            {{ $tx->original_currency ?? 'USD' }} {{ number_format($tx->original_amount ?? $tx->amount, 2) }}
                        </td>
                        <td class="{{ $tx->type === 'withdrawal' ? 'amount-negative' : 'amount-positive' }}">
                            $ {{ number_format($tx->amount, 2) }}
                        </td>
                        <td style="color: #2563eb;">IQD {{ number_format($tx->amount * ($rates['IQD'] ?? 0), 0) }}</td>
                        <td style="color: #d97706;">€ {{ number_format($tx->amount * ($rates['EUR'] ?? 0), 2) }}</td>
                        <td style="color: #7c3aed;">₺ {{ number_format($tx->amount * ($rates['TRY'] ?? 0), 2) }}</td>
                        <td style="color: #666;">$ {{ number_format($tx->balance_before, 2) }}</td>
                        <td class="{{ $tx->balance_after < 0 ? 'amount-negative' : 'amount-positive' }}">
                            $ {{ number_format($tx->balance_after, 2) }}
                        </td>
                        <td style="color: #666; font-size: 12px;">
                            {{ $tx->sender_name ?? '—' }}
                            {{ $tx->sender_phone ? '(' . $tx->sender_phone . ')' : '' }}
                        </td>
                        <td style="color: #666; font-size: 12px;">{{ $tx->notes ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" style="text-align: center; color: #999; padding: 40px;">
                            {{ $isRTL ? 'لا توجد معاملات في هذه الفترة' : 'No transactions found for this period.' }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>