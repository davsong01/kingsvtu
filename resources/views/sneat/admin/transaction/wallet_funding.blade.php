@extends('sneat.layouts.app')

@section('title', 'Wallet Funding Log')

@section('content')
    @php
        $summary = [
            ['label' => 'Successful', 'value' => number_format($success ?? 0, 2), 'tone' => 'emerald'],
            ['label' => 'Attention Required', 'value' => number_format($attention_required ?? 0, 2), 'tone' => 'amber'],
            ['label' => 'Failed', 'value' => number_format($failed ?? 0, 2), 'tone' => 'rose'],
        ];
    @endphp

    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="admin-page-hero mb-4">
                <div>
                    <span class="admin-page-hero__kicker">Financials</span>
                    <h1>Wallet Funding</h1>
                </div>
            </div>

            @include('sneat.layouts.alerts')

            <div class="row g-3 mb-4">
                @foreach($summary as $card)
                    <div class="col-md-4">
                        <div class="admin-stat-card admin-stat-card--{{ $card['tone'] }}">
                            <div class="admin-stat-card__label">{{ $card['label'] }}</div>
                            <div class="admin-stat-card__value">{!! getSettings()->currency !!}{{ $card['value'] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="gateway-card card mb-4">
                <div class="card-header">
                    <h3>Search funding records</h3>
                    <p>Filter by customer, transaction, gateway, status, or date range.</p>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.walletfundinglog') }}" method="GET">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="modern-admin-label" for="email">Customer Email</label>
                                <input type="email" class="form-control form-control-{{ formControlSize() }}" id="email" name="email" placeholder="Enter customer email address" value="{{ request('email') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="modern-admin-label" for="transaction_id">Transaction ID</label>
                                <input type="text" class="form-control form-control-{{ formControlSize() }}" id="transaction_id" name="transaction_id" placeholder="Enter transaction ID" value="{{ request('transaction_id') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="modern-admin-label" for="payment_provider">Payment Gateway</label>
                                <select class="form-select form-select-{{ formControlSize() }}" name="payment_provider" id="payment_provider">
                                    <option value="">Select</option>
                                    @foreach ($providers as $provider)
                                        <option value="{{ $provider->id }}" @selected(request('payment_provider') == $provider->id)>{{ $provider->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="modern-admin-label" for="status">Status</label>
                                <select class="form-select form-select-{{ formControlSize() }}" name="status" id="status">
                                    <option value="">Select</option>
                                    <option value="delivered" @selected(request('status') === 'delivered')>Delivered</option>
                                    <option value="failed" @selected(request('status') === 'failed')>Failed</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="modern-admin-label" for="from">From</label>
                                <input type="date" class="form-control form-control-{{ formControlSize() }}" id="from" name="from" value="{{ request('from') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="modern-admin-label" for="to">To</label>
                                <input type="date" class="form-control form-control-{{ formControlSize() }}" id="to" name="to" value="{{ request('to') }}">
                            </div>
                            <div class="col-md-3 d-flex align-items-end gap-2">
                                <button type="submit" class="btn btn-admin-submit flex-grow-1">Search</button>
                                <a href="{{ route('admin.walletfundinglog') }}" class="gateway-action">Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card modern-admin-card">
                <div class="card-header">
                    <h3>Funding log</h3>
                    <p>Gateway charging details and account funding history.</p>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table financial-table align-middle">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Transaction</th>
                                    <th>Payment Details</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $transaction)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $transaction->customer_name }}</div>
                                            <div class="gateway-helper">{{ $transaction->customer_email }}</div>
                                            <div class="gateway-helper">{{ $transaction->customer_phone }}</div>
                                            <span class="gateway-badge {{ in_array($transaction->status, ['success', 'delivered']) ? 'gateway-badge--active' : 'gateway-badge--danger' }}">
                                                {{ ucfirst($transaction->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="gateway-helper">Provider: <span class="fw-semibold">{{ $transaction->provider->name ?? 'N/A' }}</span></div>
                                            <div class="gateway-helper">Account number: {{ $transaction->account_number }}</div>
                                            <div class="gateway-helper">Amount: {!! getSettings()->currency !!}{{ number_format($transaction->amount, 2) }}</div>
                                            <div class="gateway-helper">Charge: {!! getSettings()->currency !!}{{ number_format($transaction->provider_charge, 2) }}</div>
                                            <div class="gateway-helper">Total amount: {!! getSettings()->currency !!}{{ number_format($transaction->total_amount, 2) }}</div>
                                            <div class="gateway-helper">Initial balance: {!! getSettings()->currency !!}{{ number_format($transaction->balance_before, 2) }}</div>
                                            <div class="gateway-helper">Final balance: {!! getSettings()->currency !!}{{ number_format($transaction->balance_after, 2) }}</div>
                                        </td>
                                        <td>
                                            <div class="gateway-helper">Transaction ID: {{ $transaction->transaction_id }}</div>
                                            <div class="gateway-helper">Request ID: {{ $transaction->reference_id }}</div>
                                            <div class="gateway-helper">Payment method: {{ $transaction->payment_method }}</div>
                                            <div class="gateway-helper">Date: {{ date('M jS, Y g:iA', strtotime($transaction->created_at)) }}</div>
                                        </td>
                                        <td>
                                            <a class="gateway-action" href="{{ route('admin.single.transaction.view', $transaction->id) }}">View</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4"><div class="alert alert-light border mb-0">No wallet funding records found.</div></td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0">
                    {{ $transactions->appends($query)->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
@endsection
