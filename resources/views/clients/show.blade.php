@extends('layouts.app')
@section('title', $client->full_name)
@section('content')

<div class="page-header">
    <div style="display: flex; align-items: center; gap: 16px;">
        <a href="{{ route('clients.index') }}" class="btn-secondary">← {{ $trans['back'] }}</a>
        <div class="page-title">
            <h1>{{ $client->full_name }}</h1>
            <p>{{ $client->code }} · {{ $client->phone }}</p>
        </div>
    </div>
    <div style="display: flex; gap: 10px;">
        <button onclick="window.print()" class="btn-secondary">🖨️ {{ $trans['print_statement'] }}</button>
        <button onclick="document.getElementById('transferModal').style.display='flex'" class="btn-secondary" style="color: #2563eb; border-color: #2563eb;">
            ⇄ {{ $isRTL ? 'تحويل' : 'Transfer' }}
        </button>
        <button onclick="document.getElementById('txModal').style.display='flex'" class="btn-primary">+ {{ $trans['add_transaction'] }}</button>
    </div>
</div>

<div class="grid-2" style="margin-bottom: 20px;">
    <div style="display: flex; flex-direction: column; gap: 16px;">
        <div class="card card-body">
            <h2 style="font-size: 15px; font-weight: 600; color: #1a3c5e; margin-bottom: 16px;">{{ $trans['client_info'] }}</h2>
            @foreach([
                [$trans['client_code'], $client->code],
                [$trans['status'], $client->status],
                [$trans['phone'], $client->phone],
                [$trans['address'], $client->address],
                [$trans['national_id'], $client->national_id],
                [$trans['member_since'], $client->created_at->format('Y-m-d')],
            ] as [$label, $value])
            <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f5f5f5;">
                <span style="font-size: 13px; color: #666;">{{ $label }}</span>
                <span style="font-size: 13px; font-weight: 500;">{{ $value }}</span>
            </div>
            @endforeach
        </div>

        @if(!$client->login_enabled)
        <div class="card card-body" style="border-left: 4px solid #2563eb;">
            <h2 style="font-size: 15px; font-weight: 600; color: #1a3c5e; margin-bottom: 16px;">{{ $trans['enable_portal'] }}</h2>
            <form method="POST" action="{{ route('clients.enable-login', $client->id) }}">
                @csrf
                <div class="form-group">
                    <label>{{ $trans['email'] }}</label>
                    <input type="email" name="email" class="form-control" placeholder="client@email.com" value="{{ $client->email }}">
                </div>
                <div class="form-group">
                    <label>{{ $trans['phone'] }}</label>
                    <input type="text" name="phone" class="form-control" value="{{ $client->phone }}">
                </div>
                <div class="form-group">
                    <label>{{ $trans['set_password'] }}</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn-primary" style="width: 100%;">{{ $trans['enable_access'] }}</button>
            </form>
        </div>
        @else
        <div class="card card-body" style="border-left: 4px solid #16a34a;">
            <h2 style="font-size: 15px; font-weight: 600; color: #1a3c5e; margin-bottom: 12px;">{{ $trans['portal_access'] }}</h2>
            <div style="color: #16a34a; font-weight: 600; margin-bottom: 12px;">✓ {{ $trans['portal_enabled'] }}</div>
            <div style="font-size: 13px; color: #666; margin-bottom: 16px;">
                Login: {{ $client->email ?? $client->phone }}<br>
                URL: <a href="{{ route('portal.login') }}" target="_blank" style="color: #1a3c5e;">{{ route('portal.login') }}</a>
            </div>
            <form method="POST" action="{{ route('clients.disable-login', $client->id) }}">
                @csrf
                <button type="submit" class="btn-secondary" style="width: 100%; color: #dc2626; border-color: #dc2626;">{{ $trans['disable_portal'] }}</button>
            </form>
        </div>
        @endif
    </div>

    <!-- Balances -->
    <div style="display: flex; flex-direction: column; gap: 12px;">
        @foreach($balances as $code => $b)
        <div class="stat-card" style="border-left: 4px solid {{ $b['balance'] < 0 ? '#dc2626' : $b['color'] }}; border-right: 4px solid {{ $b['balance'] < 0 ? '#dc2626' : $b['color'] }}; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <div class="label">{{ $code }} {{ $isRTL ? 'الرصيد' : 'Balance' }}</div>
                <div class="value" style="color: {{ $b['balance'] < 0 ? '#dc2626' : $b['color'] }};">
                    {{ $b['symbol'] }} {{ number_format($b['balance'], $code === 'IQD' ? 0 : 2) }}
                </div>
            </div>
            <div style="padding: 8px 14px; border-radius: 8px; background: {{ $b['balance'] < 0 ? '#fef2f2' : '#f0fdf4' }}; font-size: 13px; font-weight: 600; color: {{ $b['balance'] < 0 ? '#dc2626' : $b['color'] }};">
                {{ $b['balance'] < 0 ? ($isRTL ? '⚠ مدين' : '⚠ Debt') : ($b['balance'] == 0 ? ($isRTL ? 'صفر' : 'Zero') : ($isRTL ? '✓ رصيد' : '✓ Has funds')) }}
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Transaction History -->
<div class="card">
    <div style="padding: 20px 24px; border-bottom: 1px solid #f0f0f0;">
        <h2 style="font-size: 15px; font-weight: 600; color: #1a3c5e;">{{ $trans['transaction_history'] }}</h2>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>{{ $trans['date'] }}</th>
                <th>{{ $trans['type'] }}</th>
                <th>{{ $trans['currency'] }}</th>
                <th>{{ $trans['amount'] }}</th>
                <th>{{ $trans['before'] }}</th>
                <th>{{ $trans['after'] }}</th>
                <th>{{ $isRTL ? 'اسم المرسل / المستلم' : 'Sender / Receiver' }}</th>
                <th>{{ $trans['notes'] }}</th>
                <th>{{ $trans['cashier'] }}</th>
                <th>{{ $isRTL ? 'وصل' : 'Receipt' }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($client->transactions->sortByDesc('created_at') as $tx)
            <tr>
                <td style="color: #666; font-size: 12px; white-space: nowrap;">{{ $tx->created_at->format('Y-m-d H:i') }}</td>
                <td>
                    @if($tx->type === 'deposit')
                        <span class="badge badge-success">{{ $trans['deposit'] }}</span>
                    @elseif($tx->type === 'withdrawal')
                        <span class="badge badge-danger">{{ $trans['withdrawal'] }}</span>
                    @elseif($tx->type === 'transfer_in')
                        <span class="badge badge-info">
                            ← {{ $isRTL ? 'تحويل وارد' : 'Transfer In' }}
                            @if($tx->transferFromClient)
                            <br><span style="font-size: 11px;">{{ $tx->transferFromClient->full_name }}</span>
                            @endif
                        </span>
                    @elseif($tx->type === 'transfer_out')
                        <span class="badge badge-warning">
                            → {{ $isRTL ? 'تحويل صادر' : 'Transfer Out' }}
                            @if($tx->transferToClient)
                            <br><span style="font-size: 11px;">{{ $tx->transferToClient->full_name }}</span>
                            @endif
                        </span>
                    @endif
                </td>
                <td style="font-weight: 600; color: #1a3c5e;">{{ $tx->currency->code ?? $tx->original_currency }}</td>
                <td class="{{ in_array($tx->type, ['withdrawal', 'transfer_out']) ? 'amount-negative' : 'amount-positive' }}">
                    {{ $tx->currency->symbol ?? '' }} {{ number_format($tx->amount, $tx->currency->code === 'IQD' ? 0 : 2) }}
                </td>
                <td style="color: #666;">{{ number_format($tx->balance_before, 2) }}</td>
                <td class="{{ $tx->balance_after < 0 ? 'amount-negative' : 'amount-positive' }}">
                    {{ number_format($tx->balance_after, 2) }}
                </td>
                <td>
                    <div style="font-size: 13px; font-weight: 600; color: #1a3c5e;">{{ $tx->sender_name ?? '—' }}</div>
                    @if($tx->sender_phone)
                    <div style="font-size: 12px; color: #999;">{{ $tx->sender_phone }}</div>
                    @endif
                </td>
                <td style="color: #666; font-size: 12px;">{{ $tx->notes ?? '—' }}</td>
                <td style="color: #666; font-size: 12px;">{{ $tx->createdBy->name }}</td>
                <td>
                    @if($tx->type === 'transfer_out')
                        <a href="{{ route('receipts.transfer', $tx->id) }}" target="_blank"
                           style="padding: 4px 10px; background: #2563eb; color: white; border-radius: 6px; font-size: 11px; text-decoration: none; white-space: nowrap;">
                            🖨️ {{ $isRTL ? 'وصل' : 'Receipt' }}
                        </a>
                    @elseif(in_array($tx->type, ['deposit', 'withdrawal']))
                        <a href="{{ route('receipts.transaction', $tx->id) }}" target="_blank"
                           style="padding: 4px 10px; background: #1a3c5e; color: white; border-radius: 6px; font-size: 11px; text-decoration: none; white-space: nowrap;">
                            🖨️ {{ $isRTL ? 'وصل' : 'Receipt' }}
                        </a>
                    @else
                        <span style="color: #999; font-size: 12px;">—</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="10" style="text-align: center; color: #999; padding: 40px;">{{ $trans['no_transactions'] }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Add Transaction Modal -->
<div id="txModal" class="modal-overlay" style="display: none;">
    <div class="modal" style="width: 500px;">
        <h2>{{ $trans['add_transaction'] }}</h2>
        <p style="font-size: 13px; color: #666; margin-bottom: 24px; margin-top: -16px;">{{ $client->full_name }}</p>
        <form method="POST" action="{{ route('transactions.store') }}">
            @csrf
            <input type="hidden" name="client_id" value="{{ $client->id }}">

            <div class="form-group">
                <label>{{ $trans['type'] }}</label>
                <div style="display: flex; gap: 8px;">
                    <label style="flex:1; text-align:center; padding:10px; border:2px solid #e0e0e0; border-radius:8px; cursor:pointer; font-size:13px; font-weight:600;" id="btn-deposit">
                        <input type="radio" name="type" value="deposit" style="display:none;" onchange="selectType('deposit')" checked>
                        ↓ {{ $trans['deposit'] }}
                    </label>
                    <label style="flex:1; text-align:center; padding:10px; border:2px solid #e0e0e0; border-radius:8px; cursor:pointer; font-size:13px; font-weight:600;" id="btn-withdrawal">
                        <input type="radio" name="type" value="withdrawal" style="display:none;" onchange="selectType('withdrawal')">
                        ↑ {{ $trans['withdrawal'] }}
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label>{{ $trans['currency'] }}</label>
                <div style="display: flex; gap: 8px;">
                    @foreach($currencies as $currency)
                    <label style="flex:1; text-align:center; padding:8px; border:2px solid #e0e0e0; border-radius:8px; cursor:pointer; font-size:13px; font-weight:600;" id="cur-{{ $currency->code }}">
                        <input type="radio" name="currency_code" value="{{ $currency->code }}" style="display:none;" onchange="selectCurrency('{{ $currency->code }}')" {{ $currency->code === 'USD' ? 'checked' : '' }}>
                        {{ $currency->symbol }} {{ $currency->code }}
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="form-group">
                <label>{{ $trans['amount'] }}</label>
                <input type="number" name="amount" id="amount" class="form-control" step="0.01" min="0.01" placeholder="0.00" required>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label>{{ $isRTL ? 'اسم المرسل / المستلم' : 'Sender / Receiver Name' }} <span style="color: #dc2626;">*</span></label>
                    <input type="text" name="sender_name" class="form-control" placeholder="{{ $isRTL ? 'الاسم مطلوب' : 'Name is required' }}" required>
                </div>
                <div class="form-group">
                    <label>{{ $isRTL ? 'هاتف المرسل / المستلم' : 'Sender / Receiver Phone' }}</label>
                    <input type="text" name="sender_phone" class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label>{{ $trans['notes_optional'] }}</label>
                <input type="text" name="notes" class="form-control">
            </div>

            <div style="display: flex; gap: 12px; margin-top: 8px;">
                <button type="button" onclick="document.getElementById('txModal').style.display='none'" class="btn-secondary" style="flex:1; text-align:center;">{{ $trans['cancel'] }}</button>
                <button type="submit" id="submitBtn" class="btn-primary" style="flex:1; text-align:center; background:#16a34a;">{{ $trans['confirm_save'] }}</button>
            </div>
        </form>
    </div>
</div>

<!-- Transfer Modal -->
<div id="transferModal" class="modal-overlay" style="display: none;">
    <div class="modal" style="width: 500px;">
        <h2>⇄ {{ $isRTL ? 'تحويل بين العملاء' : 'Transfer Between Clients' }}</h2>
        <p style="font-size: 13px; color: #666; margin-bottom: 24px; margin-top: -16px;">
            {{ $isRTL ? 'من' : 'From' }}: <strong>{{ $client->full_name }}</strong>
        </p>
        <form method="POST" action="{{ route('transactions.transfer') }}">
            @csrf
            <input type="hidden" name="from_client_id" value="{{ $client->id }}">

            <div class="form-group">
                <label>{{ $isRTL ? 'إلى العميل' : 'To Client' }} <span style="color: #dc2626;">*</span></label>
                <select name="to_client_id" class="form-control" required>
                    <option value="">{{ $isRTL ? 'اختر العميل' : 'Select client' }}</option>
                    @foreach(\App\Models\Client::where('branch_id', auth()->user()->branch_id)->where('id', '!=', $client->id)->orderBy('full_name')->get() as $c)
                    <option value="{{ $c->id }}">{{ $c->full_name }} ({{ $c->code }})</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>{{ $isRTL ? 'العملة' : 'Currency' }}</label>
                <div style="display: flex; gap: 8px;">
                    @foreach($currencies as $currency)
                    <label style="flex:1; text-align:center; padding:8px; border:2px solid #e0e0e0; border-radius:8px; cursor:pointer; font-size:13px; font-weight:600;" id="tcur-{{ $currency->code }}">
                        <input type="radio" name="currency_code" value="{{ $currency->code }}" style="display:none;" onchange="selectTCurrency('{{ $currency->code }}')" {{ $currency->code === 'USD' ? 'checked' : '' }}>
                        {{ $currency->symbol }} {{ $currency->code }}
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="form-group">
                <label>{{ $isRTL ? 'المبلغ' : 'Amount' }} <span style="color: #dc2626;">*</span></label>
                <input type="number" name="amount" class="form-control" step="0.01" min="0.01" placeholder="0.00" required>
            </div>

            <div class="form-group">
                <label>{{ $isRTL ? 'ملاحظات' : 'Notes' }}</label>
                <input type="text" name="notes" class="form-control" placeholder="{{ $isRTL ? 'سبب التحويل' : 'Reason for transfer' }}">
            </div>

            <div style="display: flex; gap: 12px; margin-top: 8px;">
                <button type="button" onclick="document.getElementById('transferModal').style.display='none'" class="btn-secondary" style="flex:1; text-align:center;">{{ $trans['cancel'] }}</button>
                <button type="submit" class="btn-primary" style="flex:1; text-align:center; background:#2563eb;">⇄ {{ $isRTL ? 'تحويل الآن' : 'Transfer Now' }}</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
function selectType(type) {
    ['deposit','withdrawal'].forEach(t => {
        const btn = document.getElementById('btn-' + t);
        btn.style.borderColor = '#e0e0e0';
        btn.style.color = '#444';
        btn.style.background = 'white';
    });
    const colors = { deposit: '#16a34a', withdrawal: '#dc2626' };
    const btn = document.getElementById('btn-' + type);
    btn.style.borderColor = colors[type];
    btn.style.color = colors[type];
    btn.style.background = colors[type] + '15';
    document.getElementById('submitBtn').style.background = colors[type];
}

function selectCurrency(code) {
    ['USD','IQD','EUR','TRY'].forEach(c => {
        const el = document.getElementById('cur-' + c);
        if(el) { el.style.borderColor = '#e0e0e0'; el.style.color = '#444'; el.style.background = 'white'; }
    });
    const el = document.getElementById('cur-' + code);
    if(el) { el.style.borderColor = '#1a3c5e'; el.style.color = '#1a3c5e'; el.style.background = '#e8f0fe'; }
}

function selectTCurrency(code) {
    ['USD','IQD','EUR','TRY'].forEach(c => {
        const el = document.getElementById('tcur-' + c);
        if(el) { el.style.borderColor = '#e0e0e0'; el.style.color = '#444'; el.style.background = 'white'; }
    });
    const el = document.getElementById('tcur-' + code);
    if(el) { el.style.borderColor = '#2563eb'; el.style.color = '#2563eb'; el.style.background = '#eff6ff'; }
}

selectType('deposit');
selectCurrency('USD');
selectTCurrency('USD');
</script>
@endsection