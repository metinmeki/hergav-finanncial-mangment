@extends('layouts.app')
@section('title', __('app.users'))
@section('content')
<div class="page-header">
    <div class="page-title">
        <h1>{{ __('app.users') }}</h1>
        <p>{{ $users->count() }} {{ __('app.system_users') }}</p>
    </div>
    <button class="btn-primary" onclick="document.getElementById('userModal').style.display='flex'">+ {{ __('app.add_user') }}</button>
</div>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>{{ __('app.name') }}</th>
                <th>{{ __('app.email') }}</th>
                <th>{{ __('app.role') }}</th>
                <th>{{ __('app.status') }}</th>
                <th>{{ __('app.action') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td style="font-weight: 500;">{{ $user->name }}</td>
                <td style="color: #666;">{{ $user->email }}</td>
                <td><span class="badge {{ $user->role === 'admin' ? 'badge-warning' : 'badge-info' }}">{{ __('app.' . $user->role) }}</span></td>
                <td><span class="badge {{ $user->is_active ? 'badge-success' : 'badge-danger' }}">{{ $user->is_active ? __('app.active') : __('app.inactive') }}</span></td>
                <td>
                    @if($user->id !== auth()->id())
                    <form method="POST" action="{{ route('users.toggle', $user->id) }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn-secondary" style="padding: 6px 12px; font-size: 12px;">
                            {{ $user->is_active ? __('app.inactive') : __('app.active') }}
                        </button>
                    </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div id="userModal" class="modal-overlay" style="display: none;">
    <div class="modal">
        <h2>{{ __('app.add_user') }}</h2>
        <form method="POST" action="{{ route('users.store') }}">
            @csrf
            <div class="form-group">
                <label>{{ __('app.name') }}</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>{{ __('app.email') }}</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label>{{ __('app.password') }}</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="form-group">
                <label>{{ __('app.role') }}</label>
                <select name="role" class="form-control">
                    <option value="cashier">{{ __('app.cashier') }}</option>
                    <option value="admin">{{ __('app.admin') }}</option>
                </select>
            </div>
            <div style="display: flex; gap: 12px; margin-top: 8px;">
                <button type="button" onclick="document.getElementById('userModal').style.display='none'" class="btn-secondary" style="flex: 1; text-align: center;">{{ __('app.cancel') }}</button>
                <button type="submit" class="btn-primary" style="flex: 1; text-align: center;">{{ __('app.add_user') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection