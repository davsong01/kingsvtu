@extends('sneat.layouts.app')

@section('title', 'Transaction Details')

@section('page-style')
    <style>
        .transaction-hero-card .card-body {
            padding: 1.5rem;
        }

        .transaction-preview {
            width: 72px;
            height: 72px;
            border-radius: 1rem;
            overflow: hidden;
            border: 1px solid rgba(15, 23, 42, .08);
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .transaction-preview img {
            max-width: 100%;
            max-height: 100%;
            object-fit: cover;
        }
    </style>
@endsection

@section('content')
    @php
        $statusTone = in_array($transaction->status, ['success', 'delivered']) ? 'gateway-badge--active' : 'gateway-badge--danger';
        $isSpecialTransaction = in_array($transaction->reason, ['LEVEL-UPGRADE', 'WALLET-FUNDING', 'ADMIN-DEBIT', 'ADMIN-CREDIT'], true);
        $imagePath = $isSpecialTransaction ? asset('site/upgrade.jpg') : asset(optional($transaction->product)->image ?? 'site/upgrade.jpg');
    @endphp

    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="admin-page-hero mb-4">
                <div>
                    <span class="admin-page-hero__kicker">Financials</span>
                    <h1>Transaction Details</h1>
                    <p>Inspect a single transaction in a cleaner modern layout.</p>
                </div>
                <span class="gateway-badge {{ $statusTone }}">{{ ucfirst($transaction->status) }}</span>
            </div>

            @include('sneat.layouts.alerts')

            <div class="gateway-card card transaction-hero-card mb-4">
                <div class="card-body">
                    <div class="row g-4 align-items-center">
                        <div class="col-lg-2 col-md-3">
                            <div class="transaction-preview">
                                <img src="{{ $imagePath }}" alt="{{ $transaction->product_name }}">
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-9">
                            <div class="gateway-name mb-2">{{ $transaction->product_name }}</div>
                            <div class="gateway-helper">Transaction ID: {{ $transaction->transaction_id }}</div>
                            <div class="gateway-helper">Request ID: {{ $transaction->reference_id }}</div>
                            <div class="gateway-helper">Created: {{ $transaction->created_at }}</div>
                            <div class="d-flex flex-wrap gap-2 mt-3">
                                <a href="{{ route('transaction.receipt.download', ['transaction_id' => $transaction->id]) }}" target="_blank" class="gateway-action">Download Receipt</a>
                                <a href="{{ url()->previous() }}" class="gateway-action">Back</a>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="admin-stat-card admin-stat-card--emerald">
                                        <div class="admin-stat-card__label">Amount</div>
                                        <div class="admin-stat-card__value">{!! getSettings()->currency !!}{{ number_format($transaction->amount, 2) }}</div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="admin-stat-card admin-stat-card--amber">
                                        <div class="admin-stat-card__label">Charge</div>
                                        <div class="admin-stat-card__value">{!! getSettings()->currency !!}{{ number_format($transaction->provider_charge, 2) }}</div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="admin-stat-card admin-stat-card--blue">
                                        <div class="admin-stat-card__label">Total</div>
                                        <div class="admin-stat-card__value">{!! getSettings()->currency !!}{{ number_format($transaction->total_amount, 2) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="profile-side-card h-100">
                        <div class="profile-side-row">
                            <span>Customer</span>
                            <strong>{{ $transaction->customer_name }}</strong>
                        </div>
                        <div class="profile-side-row">
                            <span>Email</span>
                            <strong>{{ $transaction->customer_email }}</strong>
                        </div>
                        <div class="profile-side-row">
                            <span>Phone</span>
                            <strong>{{ $transaction->customer_phone }}</strong>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="profile-side-card h-100">
                        <div class="profile-side-row">
                            <span>Reason</span>
                            <strong>{{ $transaction->reason ?? 'N/A' }}</strong>
                        </div>
                        <div class="profile-side-row">
                            <span>Status</span>
                            <strong>{{ ucfirst($transaction->status) }}</strong>
                        </div>
                        <div class="profile-side-row">
                            <span>Payment Method</span>
                            <strong>{{ $transaction->payment_method ?? 'N/A' }}</strong>
                        </div>
                        <div class="profile-side-row">
                            <span>Provider</span>
                            <strong>{{ $transaction->api->name ?? 'N/A' }}</strong>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="profile-side-card h-100">
                        <div class="profile-side-row">
                            <span>Balance Before</span>
                            <strong>{!! getSettings()->currency !!}{{ number_format($transaction->balance_before, 2) }}</strong>
                        </div>
                        <div class="profile-side-row">
                            <span>Balance After</span>
                            <strong>{!! getSettings()->currency !!}{{ number_format($transaction->balance_after, 2) }}</strong>
                        </div>
                        <div class="profile-side-row">
                            <span>Discount</span>
                            <strong>{!! getSettings()->currency !!}{{ number_format($transaction->discount, 2) }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card modern-admin-card mt-4">
                <div class="card-header">
                    <h3>Transaction breakdown</h3>
                    <p>Basic line item and service context for this request.</p>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table financial-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Unit Cost</th>
                                    <th>Quantity</th>
                                    <th>Amount</th>
                                    <th>Biller</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        @if($isSpecialTransaction)
                                            {{ ucfirst(str_replace('-', ' ', $transaction->reason)) }}
                                        @else
                                            {{ $transaction->product->name ?? $transaction->product_name }}
                                        @endif
                                    </td>
                                    <td>{!! getSettings()->currency !!}{{ number_format($transaction->amount, 2) }}</td>
                                    <td>{{ $transaction->quantity ?? 1 }}</td>
                                    <td>{!! getSettings()->currency !!}{{ number_format($transaction->total_amount, 2) }}</td>
                                    <td>{{ $transaction->unique_element ?? 'N/A' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
