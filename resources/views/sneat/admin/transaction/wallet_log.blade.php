@extends('sneat.layouts.app')

@section('title', 'Wallet Logs')

@section('content')
    @php
        $summary = [
            ['label' => 'Credit', 'value' => number_format($credit ?? 0, 2), 'tone' => 'emerald'],
            ['label' => 'Debit', 'value' => number_format($debit ?? 0, 2), 'tone' => 'rose'],
        ];
    @endphp

    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="admin-page-hero mb-4">
                <div>
                    <span class="admin-page-hero__kicker">Financials</span>
                    <h1>Wallet Logs</h1>
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
                    <h3>Search wallet logs</h3>
                    <p>Use filters to narrow down transactions before reviewing the table.</p>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.walletlog') }}" method="GET">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="modern-admin-label" for="email">Customer Email</label>
                                <input type="email" class="form-control form-control-{{ formControlSize() }}" id="email" name="email" placeholder="Enter customer email address" value="{{ request('email') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="modern-admin-label" for="transaction_id">Transaction ID</label>
                                <input type="text" class="form-control form-control-{{ formControlSize() }}" id="transaction_id" name="transaction_id" placeholder="Enter transaction ID" value="{{ request('transaction_id') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="modern-admin-label" for="type">Type</label>
                                <select class="form-select form-select-{{ formControlSize() }}" name="type" id="type">
                                    <option value="">Select</option>
                                    <option value="credit" @selected(request('type') === 'credit')>Credit</option>
                                    <option value="debit" @selected(request('type') === 'debit')>Debit</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="modern-admin-label" for="from">From</label>
                                <input type="date" class="form-control form-control-{{ formControlSize() }}" id="from" name="from" value="{{ request('from') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="modern-admin-label" for="to">To</label>
                                <input type="date" class="form-control form-control-{{ formControlSize() }}" id="to" name="to" value="{{ request('to') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="modern-admin-label" for="paginate">Paginate</label>
                                <select class="form-select form-select-{{ formControlSize() }}" name="paginate" id="paginate">
                                    <option value="yes" @selected(request('paginate', 'yes') === 'yes')>Yes</option>
                                    <option value="no" @selected(request('paginate') === 'no')>No</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="modern-admin-label" for="sort">Sort & Download</label>
                                <select class="form-select form-select-{{ formControlSize() }}" name="sort" id="sort">
                                    <option value="">Select</option>
                                    <option value="highest" @selected(request('sort') === 'highest')>Sort By Highest</option>
                                    <option value="lowest" @selected(request('sort') === 'lowest')>Sort By Lowest</option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end gap-2">
                                <button type="submit" class="btn btn-admin-submit flex-grow-1">Search</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card modern-admin-card">
                <div class="card-header">
                    <h3>Wallet movement</h3>
                    <p>Credit and debit entries with customer context.</p>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table financial-table align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Customer</th>
                                    <th>Transaction ID</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $transaction)
                                    <tr>
                                        <td>{{ method_exists($transactions, 'firstItem') ? $transactions->firstItem() + $loop->index : $loop->iteration }}</td>
                                        <td>
                                            <div class="fw-semibold">{{ $transaction->customer->user->name ?? 'N/A' }}</div>
                                            <div class="gateway-helper">{{ $transaction->customer->user->email ?? 'N/A' }}</div>
                                            <div class="gateway-helper">{{ $transaction->customer->user->phone ?? 'N/A' }}</div>
                                        </td>
                                        <td>
                                            @if($transaction->transaction_log)
                                                <a href="{{ route('admin.single.transaction.view', $transaction->transaction_log->id) }}" target="_blank">{{ $transaction->transaction_id }}</a>
                                            @else
                                                {{ $transaction->transaction_id }}
                                            @endif
                                            <div class="gateway-helper">Reason: {{ $transaction->reason }}</div>
                                            <div class="gateway-helper">Payment method: {{ $transaction->transaction_log->payment_method ?? 'N/A' }}</div>
                                        </td>
                                        <td>
                                            <span class="gateway-badge {{ $transaction->type === 'credit' ? 'gateway-badge--active' : 'gateway-badge--danger' }}">
                                                {{ ucfirst($transaction->type) }}
                                            </span>
                                        </td>
                                        <td>{!! getSettings()->currency !!}{{ number_format($transaction->amount) }}</td>
                                        <td>{{ date('M jS, Y g:iA', strtotime($transaction->created_at)) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6"><div class="alert alert-light border mb-0">No wallet logs found.</div></td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if(method_exists($transactions, 'links'))
                    <div class="card-footer bg-transparent border-0 pt-0">
                        {!! $transactions->appends($query)->links('pagination::bootstrap-5') !!}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
