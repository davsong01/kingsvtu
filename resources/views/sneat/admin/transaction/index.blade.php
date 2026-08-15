@extends('sneat.layouts.app')

@section('title', 'Transactions')

@section('page-style')
    <link href="{{ asset('modern-assets/vendor/libs/select2/select2.css') }}" rel="stylesheet" />
@endsection

@section('content')
    @php
        $summary = [
            ['label' => 'Delivered', 'value' => number_format($success ?? 0, 2), 'tone' => 'emerald'],
            ['label' => 'Attention Required', 'value' => number_format($attention_required ?? 0, 2), 'tone' => 'amber'],
            ['label' => 'Failed', 'value' => number_format($failed ?? 0, 2), 'tone' => 'rose'],
        ];
    @endphp

    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="admin-page-hero mb-4">
                <div>
                    <span class="admin-page-hero__kicker">Financials</span>
                    <h1>Transactions</h1>
                    <p>Review purchases, filter by status or provider, and inspect each transaction in a cleaner admin shell.</p>
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
                    <h3>Search transactions</h3>
                    <p>Filter by API provider, customer, transaction details, and date range.</p>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.trans') }}" method="GET">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="modern-admin-label" for="api_id">API Provider</label>
                                <select class="form-select form-select-{{ formControlSize() }}" name="api_id" id="api_id">
                                    <option value="">Select</option>
                                    @foreach ($apis as $api)
                                        <option value="{{ $api->id }}" @selected(request('api_id') == $api->id)>{{ $api->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="modern-admin-label" for="channel">Channel</label>
                                <select class="form-select form-select-{{ formControlSize() }}" name="channel" id="channel">
                                    <option value="">Select</option>
                                    <option value="website" @selected(request('channel') === 'website')>Website</option>
                                    <option value="api" @selected(request('channel') === 'api')>API</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="modern-admin-label" for="email">Transaction Email</label>
                                <input type="email" class="form-control form-control-{{ formControlSize() }}" id="email" name="email" placeholder="Enter customer email address" value="{{ request('email') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="modern-admin-label" for="phone">Transaction Phone</label>
                                <input type="text" class="form-control form-control-{{ formControlSize() }}" id="phone" name="phone" placeholder="Enter customer phone number" value="{{ request('phone') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="modern-admin-label" for="service">Service</label>
                                <select class="form-select form-select-{{ formControlSize() }}" name="service" id="service" data-placeholder="Search service">
                                    <option value="">Select</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}" @selected(request('service') == $product->id)>{{ $product->display_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="modern-admin-label" for="transaction_id">Transaction ID</label>
                                <input type="text" class="form-control form-control-{{ formControlSize() }}" id="transaction_id" name="transaction_id" placeholder="Enter transaction ID" value="{{ request('transaction_id') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="modern-admin-label" for="unique_element">Unique Element</label>
                                <input type="text" class="form-control form-control-{{ formControlSize() }}" id="unique_element" name="unique_element" placeholder="Enter unique element" value="{{ request('unique_element') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="modern-admin-label" for="status">Status</label>
                                <select class="form-select form-select-{{ formControlSize() }}" name="status" id="status">
                                    <option value="">Select</option>
                                    <option value="delivered" @selected(request('status') === 'delivered')>Delivered</option>
                                    <option value="failed" @selected(request('status') === 'failed')>Failed</option>
                                    <option value="attention-required" @selected(request('status') === 'attention-required')>Attention Required</option>
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
                                <a href="{{ route('admin.trans') }}" class="gateway-action">Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card modern-admin-card">
                <div class="card-header">
                    <h3>Transaction log</h3>
                    <p>Latest activity with customer and payment context.</p>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table financial-table align-middle">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Customer</th>
                                    <th>Payment Details</th>
                                    <th>Transaction Details</th>
                                    <th>Unique Element</th>
                                    @if(hasAccess('admin.single.transaction.view'))
                                        <th>Action</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($transactions as $transaction)
                                    <tr>
                                        <td>{{ method_exists($transactions, 'firstItem') ? $transactions->firstItem() + $loop->index : $loop->iteration }}</td>
                                        <td>
                                            <div class="fw-semibold">{{ $transaction->customer_name }}</div>
                                            <div class="gateway-helper">Transaction ID: {{ $transaction->transaction_id }}</div>
                                            <div class="gateway-helper">Request ID: {{ $transaction->reference_id }}</div>
                                            <div class="gateway-helper">{{ $transaction->customer_email }}</div>
                                            <div class="gateway-helper">{{ $transaction->customer_phone }}</div>
                                            <div class="gateway-helper">{{ date('M jS, Y g:iA', strtotime($transaction->created_at)) }}</div>
                                            <span class="gateway-badge {{ in_array($transaction->status, ['success', 'delivered']) ? 'gateway-badge--active' : 'gateway-badge--danger' }}">
                                                {{ ucfirst($transaction->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="gateway-helper">Amount: {!! getSettings()->currency !!}{{ number_format($transaction->amount, 2) }}</div>
                                            <div class="gateway-helper">Charge: {!! getSettings()->currency !!}{{ number_format($transaction->provider_charge, 2) }}</div>
                                            <div class="gateway-helper">Total: {!! getSettings()->currency !!}{{ number_format($transaction->total_amount, 2) }}</div>
                                            <div class="gateway-helper">Initial balance: {!! getSettings()->currency !!}{{ number_format($transaction->balance_before, 2) }}</div>
                                            <div class="gateway-helper">Final balance: {!! getSettings()->currency !!}{{ number_format($transaction->balance_after, 2) }}</div>
                                        </td>
                                        <td>
                                            <div class="gateway-helper">Product: {{ $transaction->product_name }}</div>
                                            <div class="gateway-helper">Category: {{ $transaction->category->display_name ?? 'N/A' }}</div>
                                            @if($transaction->variation)
                                                <div class="gateway-helper">Variation: {{ $transaction->variation->system_name ?? 'N/A' }}</div>
                                            @endif
                                            <div class="gateway-helper">Provider: {{ $transaction->api->name ?? 'N/A' }}</div>
                                            <div class="gateway-helper">Discount: {!! getSettings()->currency !!}{{ number_format($transaction->discount, 2) }}</div>
                                        </td>
                                        <td>{{ $transaction->unique_element }}</td>
                                        @if(hasAccess('admin.single.transaction.view'))
                                            <td>
                                                <a class="gateway-action" href="{{ route('admin.single.transaction.view', $transaction->id) }}">View</a>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ hasAccess('admin.single.transaction.view') ? 6 : 5 }}">
                                            <div class="alert alert-light border mb-0">No transactions found with the current filters.</div>
                                        </td>
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

@section('page-script')
    <script src="{{ asset('modern-assets/vendor/libs/select2/select2.js') }}"></script>
    <script>
        (function () {
            const $service = $('#service');

            if ($service.length && !$service.data('select2')) {
                const $wrapper = $service.wrap('<div class="position-relative"></div>').parent();

                $service.select2({
                    placeholder: $service.data('placeholder') || 'Search service',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $wrapper
                });
            }
        })();
    </script>
@endsection
