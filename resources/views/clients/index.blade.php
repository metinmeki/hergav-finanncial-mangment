@extends('layouts.app')
@section('title', $trans['clients'])
@section('content')
<div class="page-header">
    <div class="page-title">
        <h1>{{ $trans['clients'] }}</h1>
        <p>{{ $clients->count() }} {{ $trans['total_clients_count'] }}</p>
    </div>
    <button class="btn-primary" onclick="document.getElementById('addModal').style.display='flex'">+ {{ $trans['add_client'] }}</button>
</div>

<form method="GET" style="margin-bottom: 16px;">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ $trans['search_clients'] }}" class="form-control">
</form>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>{{ $trans['client_code'] }}</th>
                <th>{{ $trans['full_name'] }}</th>
                <th>{{ $trans['phone'] }}</th>
                <th>{{ $trans['usd_balance'] }}</th>
                <th>{{ $trans['iqd_balance'] }}</th>
                <th>{{ $trans['eur_balance'] }}</th>
                <th>{{ $trans['try_balance'] }}</th>
                <th>{{ $trans['status'] }}</th>
                <th>{{ $trans['action'] }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($clients as $client)
            @php
                $usdAccount = $client->accounts->first();
                $usdBal = $usdAccount ? $usdAccount->balance : 0;
                $iqdBal = isset($rates['IQD']) ? $usdBal * $rates['IQD'] : 0;
                $eurBal = isset($rates['EUR']) ? $usdBal * $rates['EUR'] : 0;
                $tryBal = isset($rates['TRY']) ? $usdBal * $rates['TRY'] : 0;
            @endphp
            <tr>
                <td style="font-weight: 600; color: #1a3c5e;">{{ $client->code }}</td>
                <td style="font-weight: 500;">{{ $client->full_name }}</td>
                <td style="color: #666;">{{ $client->phone }}</td>
                <td class="{{ $usdBal < 0 ? 'amount-negative' : 'amount-positive' }}">$ {{ number_format($usdBal, 2) }}</td>
                <td class="{{ $iqdBal < 0 ? 'amount-negative' : '' }}" style="color: #2563eb;">IQD {{ number_format($iqdBal, 0) }}</td>
                <td class="{{ $eurBal < 0 ? 'amount-negative' : '' }}" style="color: #d97706;">€ {{ number_format($eurBal, 2) }}</td>
                <td class="{{ $tryBal < 0 ? 'amount-negative' : '' }}" style="color: #7c3aed;">₺ {{ number_format($tryBal, 2) }}</td>
                <td><span class="badge {{ $client->status === 'active' ? 'badge-success' : 'badge-danger' }}">{{ $trans[$client->status] }}</span></td>
                <td><a href="{{ route('clients.show', $client->id) }}" class="btn-primary" style="padding: 6px 14px; font-size: 12px;">{{ $trans['view'] }}</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div id="addModal" class="modal-overlay" style="display: none;">
    <div class="modal">
        <h2>{{ $trans['add_client'] }}</h2>
        <form method="POST" action="{{ route('clients.store') }}">
            @csrf
            <div class="form-group">
                <label>{{ $trans['client_code'] }}</label>
                <input type="text" name="code" class="form-control" placeholder="e.g. CLT001" required>
            </div>
            <div class="form-group">
                <label>{{ $trans['full_name'] }}</label>
                <input type="text" name="full_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>{{ $trans['full_name_ar'] }}</label>
                <input type="text" name="full_name_ar" class="form-control" dir="rtl">
            </div>
            <div class="form-group">
                <label>{{ $trans['phone'] }}</label>
                <input type="text" name="phone" class="form-control">
            </div>
            <div class="form-group">
                <label>{{ $trans['address'] }}</label>
                <input type="text" name="address" class="form-control">
            </div>
            <div class="form-group">
                <label>{{ $trans['national_id'] }}</label>
                <input type="text" name="national_id" class="form-control">
            </div>
            <div style="display: flex; gap: 12px; margin-top: 24px;">
                <button type="button" onclick="document.getElementById('addModal').style.display='none'" class="btn-secondary" style="flex: 1; text-align: center;">{{ $trans['cancel'] }}</button>
                <button type="submit" class="btn-primary" style="flex: 1; text-align: center;">{{ $trans['add_client'] }}</button>
            </div>
        </form>
    </div>
</div>
@endsection