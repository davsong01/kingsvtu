@extends('sneat.layouts.app')

@section('title', 'Earnings Log')

@section('content')
    @php
        $summary = [
            ['label' => 'Total Credit', 'value' => number_format($success ?? 0, 2), 'tone' => 'emerald'],
            ['label' => 'Total Debit', 'value' => number_format($failed ?? 0, 2), 'tone' => 'rose'],
        ];
    @endphp

    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="admin-page-hero mb-4">
                <div>
                    <span class="admin-page-hero__kicker">Financials</span>
                    <h1>Earnings Log</h1>
                </div>
            </div>

            @include('sneat.layouts.alerts')

            <div class="row g-3 mb-4">
                @foreach($summary as $card)
                    <div class="col-md-6">
                        <div class="admin-stat-card admin-stat-card--{{ $card['tone'] }}">
                            <div class="admin-stat-card__label">{{ $card['label'] }}</div>
                            <div class="admin-stat-card__value">{!! getSettings()->currency !!}{{ $card['value'] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="gateway-card card mb-4">
                <div class="card-header">
                    <h3>Search earnings</h3>
                    <p>Filter by upline, downline, transaction, type, and date range.</p>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.earninglog') }}" method="GET">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="modern-admin-label" for="upline_email">Upline Email</label>
                                <input type="email" class="form-control form-control-{{ formControlSize() }}" id="upline_email" name="upline_email" placeholder="Enter upline email address" value="{{ request('upline_email') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="modern-admin-label" for="downline_email">Downline Email</label>
                                <input type="email" class="form-control form-control-{{ formControlSize() }}" id="downline_email" name="downline_email" placeholder="Enter downline email address" value="{{ request('downline_email') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="modern-admin-label" for="transaction_id">Transaction ID</label>
                                <input type="text" class="form-control form-control-{{ formControlSize() }}" id="transaction_id" name="transaction_id" placeholder="Enter transaction ID" value="{{ request('transaction_id') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="modern-admin-label" for="type">Type</label>
                                <select class="form-select form-select-{{ formControlSize() }}" name="type" id="type">
                                    <option value="">Select</option>
                                    <option value="credit" @selected(request('type') === 'credit')>Credit</option>
                                    <option value="debit" @selected(request('type') === 'debit')>Debit</option>
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
                                <a href="{{ route('admin.earninglog') }}" class="gateway-action">Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card modern-admin-card">
                <div class="card-header">
                    <h3>Earnings activity</h3>
                    <p>Referral credits and debits with transaction context.</p>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table financial-table align-middle">
                            <thead>
                                <tr>
                                    <th>Upline</th>
                                    <th>Downline</th>
                                    <th>Payment Details</th>
                                    <th>Type</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $transaction)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $transaction->customer->user->name ?? 'N/A' }}</div>
                                            <div class="gateway-helper">{{ $transaction->customer->user->email ?? 'N/A' }}</div>
                                            <div class="gateway-helper">{{ $transaction->customer_phone ?? 'N/A' }}</div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold">{{ $transaction->referredCustomer->user->name ?? 'N/A' }}</div>
                                            <div class="gateway-helper">{{ $transaction->referredCustomer->user->email ?? 'N/A' }}</div>
                                            <div class="gateway-helper">{{ $transaction->customer_phone ?? 'N/A' }}</div>
                                        </td>
                                        <td>
                                            <div class="gateway-helper">
                                                Transaction ID:
                                                @if($transaction->transaction)
                                                    <a target="_blank" href="{{ route('admin.single.transaction.view', $transaction->transaction->id) }}">{{ $transaction->transaction_id }}</a>
                                                @else
                                                    {{ $transaction->transaction_id }}
                                                @endif
                                            </div>
                                            <div class="gateway-helper">Amount: <span class="fw-semibold {{ $transaction->type === 'credit' ? 'text-success' : 'text-danger' }}">{{ $transaction->type === 'credit' ? '+' : '-' }}{!! getSettings()->currency !!}{{ number_format($transaction->amount, 2) }}</span></div>
                                            <div class="gateway-helper">Initial balance: {!! getSettings()->currency !!}{{ number_format($transaction->balance_before, 2) }}</div>
                                            <div class="gateway-helper">Final balance: {!! getSettings()->currency !!}{{ number_format($transaction->balance_after, 2) }}</div>
                                            <div class="gateway-helper">Date: {{ date('M jS, Y g:iA', strtotime($transaction->created_at)) }}</div>
                                        </td>
                                        <td>
                                            <span class="gateway-badge {{ $transaction->type === 'credit' ? 'gateway-badge--active' : 'gateway-badge--danger' }}">{{ ucfirst($transaction->type) }}</span>
                                        </td>
                                        <td>
                                            @if($transaction->transaction)
                                                <a class="gateway-action" target="_blank" href="{{ route('admin.single.transaction.view', $transaction->transaction->id) }}">View</a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5"><div class="alert alert-light border mb-0">No earnings found.</div></td>
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
