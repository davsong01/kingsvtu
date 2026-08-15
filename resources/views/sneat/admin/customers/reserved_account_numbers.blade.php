@extends('sneat.layouts.app')

@section('title', 'Reserved Accounts')

@section('page-style')
    <link href="{{ asset('modern-assets/vendor/libs/select2/select2.css') }}" rel="stylesheet" />
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="admin-page-hero mb-4">
                <div>
                    <span class="admin-page-hero__kicker">Financials</span>
                    <h1>Reserved Accounts</h1>
                    <p>Review account reservations and the customers attached to them.</p>
                </div>
                <span class="gateway-badge gateway-badge--active">{{ $numbers->count() }} accounts</span>
            </div>

            @include('sneat.layouts.alerts')

            <div class="gateway-card card mb-4">
                <div class="card-header">
                    <h3>Filter reserved accounts</h3>
                    <p>Search by customer name, gateway, or account number.</p>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.reserved.accounts') }}" method="GET">
                        <div class="row g-3">
                            <div class="col-lg-4">
                                <label class="modern-admin-label" for="customer_name">Customer Name</label>
                                <input
                                    type="text"
                                    class="form-control form-control-{{ formControlSize() }}"
                                    id="customer_name"
                                    name="customer_name"
                                    placeholder="Search by name or email"
                                    value="{{ request('customer_name') }}"
                                >
                            </div>
                            <div class="col-lg-4">
                                <label class="modern-admin-label" for="payment_gateway">Payment Gateway</label>
                                <select class="form-select form-select-{{ formControlSize() }}" name="payment_gateway" id="payment_gateway" data-placeholder="Search gateway">
                                    <option value="">Select gateway</option>
                                    @foreach($gateways as $gateway)
                                        <option value="{{ $gateway->id }}" @selected(request('payment_gateway') == $gateway->id)>{{ $gateway->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-4">
                                <label class="modern-admin-label" for="account_number">Account Number</label>
                                <input
                                    type="text"
                                    class="form-control form-control-{{ formControlSize() }}"
                                    id="account_number"
                                    name="account_number"
                                    placeholder="Enter account number"
                                    value="{{ request('account_number') }}"
                                >
                            </div>
                            <div class="col-12 d-flex flex-wrap gap-2">
                                <button type="submit" class="btn btn-admin-submit">Filter</button>
                                <a href="{{ route('admin.reserved.accounts') }}" class="gateway-action">Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card modern-admin-card">
                <div class="card-header">
                    <h3>Reserved account list</h3>
                    <p>Account ownership, provider, and transaction totals in one place.</p>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table financial-table align-middle">
                            <thead>
                                <tr>
                                    <th>Customer Details</th>
                                    <th>Account Details</th>
                                    <th>Transactions</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($numbers as $number)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $number->customer->user->name }}</div>
                                            <div class="gateway-helper">{{ $number->customer->user->email }}</div>
                                            <div class="gateway-helper">Created: {{ $number->created_at }}</div>
                                            <div class="gateway-helper">By: {{ !empty($number->admin_id) ? $number->admin->user->firstname . ' ' . $number->admin->user->lastname : 'SYSTEM' }}</div>
                                        </td>
                                        <td>
                                            <div class="gateway-helper">{{ $number->account_name }}</div>
                                            <div class="gateway-helper">{{ $number->account_number }}</div>
                                            <div class="gateway-helper">{{ $number->bank_name }}</div>
                                            <div class="gateway-helper">BVN: {{ mask($number->bvn) }}</div>
                                            <span class="gateway-badge {{ $number->gateway->slug === 'monnify' ? 'gateway-badge--info' : 'gateway-badge--primary' }}">{{ $number->gateway->name }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('account.transactions', $number->id) }}">
                                                {!! getSettings()->currency !!}{{ number_format($number->transactions->sum('total_amount'), 2) }}
                                                <span class="gateway-helper">({{ number_format($number->transactions->count()) }})</span>
                                            </a>
                                        </td>
                                        <td>
                                            @if($number->transactions->count() < 1 && $number->gateway->slug === 'monnify')
                                                <a class="btn btn-outline-danger btn-sm" onclick="return confirm('You are about to delete a reserved account!')" href="{{ route('reserved_account.delete', $number->id) }}">Delete</a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4"><div class="alert alert-light border mb-0">No reserved accounts found.</div></td>
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

@section('page-script')
    <script src="{{ asset('modern-assets/vendor/libs/select2/select2.js') }}"></script>
    <script>
        (function () {
            const $gateway = $('#payment_gateway');

            if ($gateway.length && !$gateway.data('select2')) {
                const $wrapper = $gateway.wrap('<div class="position-relative"></div>').parent();

                $gateway.select2({
                    placeholder: $gateway.data('placeholder') || 'Search gateway',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $wrapper
                });
            }
        })();
    </script>
@endsection
