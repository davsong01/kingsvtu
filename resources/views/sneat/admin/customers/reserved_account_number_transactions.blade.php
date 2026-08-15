@extends('sneat.layouts.app')

@section('title', 'Reserved Account Transactions')

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="admin-page-hero mb-4">
                <div>
                    <span class="admin-page-hero__kicker">Financials</span>
                    <h1>Reserved Account Transactions</h1>
                    <p>Transactions tied to <strong>{{ $account->account_name }}</strong>.</p>
                </div>
            </div>

            @include('sneat.layouts.alerts')

            <div class="gateway-card card mb-4">
                <div class="card-header">
                    <h3>Account summary</h3>
                    <p>{{ $account->account_number }} · {{ $account->bank_name }} · {{ $account->payment_gateway->name }}</p>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4"><div class="admin-stat-card admin-stat-card--blue"><div class="admin-stat-card__label">Account Name</div><div class="admin-stat-card__value">{{ $account->account_name }}</div></div></div>
                        <div class="col-md-4"><div class="admin-stat-card admin-stat-card--emerald"><div class="admin-stat-card__label">Account Number</div><div class="admin-stat-card__value">{{ $account->account_number }}</div></div></div>
                        <div class="col-md-4"><div class="admin-stat-card admin-stat-card--amber"><div class="admin-stat-card__label">Total Transactions</div><div class="admin-stat-card__value">{{ number_format(count($transactions)) }}</div></div></div>
                    </div>
                </div>
            </div>

            <div class="card modern-admin-card">
                <div class="card-header">
                    <h3>Transaction list</h3>
                    <p>Customer details and payment history linked to this reserved account.</p>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table financial-table align-middle">
                            <thead>
                                <tr>
                                    <th>Customer Details</th>
                                    <th>Transaction Details</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $transaction)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $transaction->customer_name }}</div>
                                            <div class="gateway-helper">{{ $transaction->customer_email }}</div>
                                            <span class="gateway-badge {{ $transaction->status === 'success' ? 'gateway-badge--active' : 'gateway-badge--danger' }}">{{ ucfirst($transaction->status) }}</span>
                                        </td>
                                        <td>
                                            <div class="gateway-helper">{{ $account->account_name }}</div>
                                            <div class="gateway-helper">{{ $account->account_number }}</div>
                                            <div class="gateway-helper">{{ $account->bank_name }}</div>
                                            <div class="gateway-helper">Provider: {{ $account->payment_gateway->name }}</div>
                                            <div class="gateway-helper">Reference: {{ $transaction->transaction_id }}</div>
                                            <div class="gateway-helper">Method: {{ $transaction->payment_method }}</div>
                                        </td>
                                        <td>{{ $transaction->created_at }}</td>
                                        <td>{!! getSettings()->currency !!}{{ number_format($transaction->amount, 2) }}</td>
                                        <td>
                                            <a class="gateway-action" href="{{ route('transaction.status', $transaction->transaction_id) }}">View</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5"><div class="alert alert-light border mb-0">No transactions found for this reserved account.</div></td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
