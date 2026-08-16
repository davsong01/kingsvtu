@extends('sneat.layouts.app')

@section('title', 'Transaction History')

@section('page-css')
    <link href="{{ asset('modern-assets/vendor/libs/select2/select2.css') }}" rel="stylesheet" />
@endsection

@php
    $currency = getSettings()->currency ?? '₦';
    $transactionItems = $transactions->getCollection();
    $successCount = $transactionItems->whereIn('status', ['success', 'completed', 'delivered'])->count();
    $failedCount = $transactionItems->where('status', 'failed')->count();
    $pendingCount = $transactionItems->whereIn('status', ['initiated', 'pending', 'attention-required'])->count();
@endphp

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="profile-hero mb-4 gateway-hero">
                <div class="profile-hero__meta">
                    <div class="profile-avatar"><i class="bx bx-receipt"></i></div>
                    <div class="profile-meta">
                        <span class="gateway-hero__kicker">Activity log</span>
                        <strong>Transaction history</strong>
                        <span>Review every completed, pending, or failed transaction from one place.</span>
                    </div>
                </div>
                <div class="gateway-summary">
                    <div class="gateway-summary__card">
                        <span class="gateway-summary__label">Total records</span>
                        <span class="gateway-summary__value">{{ $transactions->total() }}</span>
                    </div>
                    <div class="gateway-summary__card">
                        <span class="gateway-summary__label">Successful</span>
                        <span class="gateway-summary__value">{{ $successCount }}</span>
                    </div>
                    <div class="gateway-summary__card">
                        <span class="gateway-summary__label">Pending</span>
                        <span class="gateway-summary__value">{{ $pendingCount }}</span>
                    </div>
                </div>
            </div>

            @include('sneat.layouts.alerts')

            <div class="card modern-admin-card mb-4">
                <div class="card-header">
                    <h3>Filters</h3>
                    <p>Search by service, status, transaction ID, or date range.</p>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('customer.transaction.history') }}">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="profile-label" for="service">Service</label>
                                <select class="form-select form-select-{{ formControlSize() }} modern-select2" id="service" name="service" data-placeholder="Search service">
                                    <option value="">All services</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" @selected(request('service') == $product->id)>{{ $product->display_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="profile-label" for="status">Status</label>
                                <select class="form-select form-select-{{ formControlSize() }}" id="status" name="status">
                                    <option value="">All statuses</option>
                                    <option value="success" @selected(request('status') === 'success')>Success</option>
                                    <option value="completed" @selected(request('status') === 'completed')>Completed</option>
                                    <option value="delivered" @selected(request('status') === 'delivered')>Delivered</option>
                                    <option value="failed" @selected(request('status') === 'failed')>Failed</option>
                                    <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="profile-label" for="transaction_id">Transaction ID</label>
                                <input type="text" class="form-control form-control-{{ formControlSize() }}" id="transaction_id" name="transaction_id" value="{{ request('transaction_id') }}" placeholder="Search transaction ID">
                            </div>
                            <div class="col-md-3">
                                <label class="profile-label" for="unique_element">Recipient / biller</label>
                                <input type="text" class="form-control form-control-{{ formControlSize() }}" id="unique_element" name="unique_element" value="{{ request('unique_element') }}" placeholder="Search by recipient">
                            </div>
                            <div class="col-md-3">
                                <label class="profile-label" for="from">From</label>
                                <input type="date" class="form-control form-control-{{ formControlSize() }}" id="from" name="from" value="{{ request('from') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="profile-label" for="to">To</label>
                                <input type="date" class="form-control form-control-{{ formControlSize() }}" id="to" name="to" value="{{ request('to') }}">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-admin-submit w-100">Search</button>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <a href="{{ route('customer.transaction.history') }}" class="btn btn-label-secondary w-100">Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

                <div class="card modern-admin-card">
                    <div class="card-header d-flex align-items-center justify-content-between gap-3">
                        <div>
                            <h3 class="mb-1">Transactions</h3>
                            <p class="mb-0">Latest customer transactions and their current state.</p>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <span class="gateway-badge gateway-badge--active">Success {{ $successCount }}</span>
                        <span class="gateway-badge gateway-badge--inactive">Failed {{ $failedCount }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @forelse($transactionItems as $transaction)
                            @php
                                $status = strtolower((string) $transaction->status);
                                $statusClass = in_array($status, ['success', 'completed', 'delivered']) ? 'success' : (in_array($status, ['failed']) ? 'danger' : 'warning');
                                $serialNumber = $transactions->firstItem() + $loop->index;
                            @endphp
                            <div class="col-12">
                                <div class="customer-transaction-card">
                                    <div class="customer-transaction-card__head">
                                        <div>
                                            <div class="customer-transaction-card__meta">#{{ $serialNumber }}</div>
                                            <h5 class="mb-1">{{ $transaction->product->display_name ?? $transaction->reason }}</h5>
                                            <div class="customer-transaction-card__sub">
                                                {{ $transaction->transaction_id }}
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-label-{{ $statusClass }}">{{ ucfirst($transaction->status) }}</span>
                                            <div class="customer-transaction-card__amount mt-2">{{ $currency . number_format((float) $transaction->total_amount, 2) }}</div>
                                        </div>
                                    </div>

                                    <div class="customer-transaction-card__body">
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <div class="customer-transaction-card__label">Recipient</div>
                                                <div class="customer-transaction-card__value">{{ $transaction->unique_element ?? $transaction->customer_phone ?? 'N/A' }}</div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="customer-transaction-card__label">Date</div>
                                                <div class="customer-transaction-card__value">{{ optional($transaction->created_at)->format('M d, Y h:i A') }}</div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="customer-transaction-card__label">Service</div>
                                                <div class="customer-transaction-card__value">{{ $transaction->product->name ?? $transaction->reason }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="customer-transaction-card__footer">
                                        <a href="{{ route('transaction.status', $transaction->transaction_id) }}" class="gateway-action">View details</a>
                                        @if(!in_array($status, ['failed', 'initiated']))
                                            <a href="{{ route('transaction.receipt.download', $transaction->id) }}" class="gateway-action" target="_blank">Receipt</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-light border mb-0">No transactions found for the selected filters.</div>
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-4">
                        {{ $transactions->links('pagination::bootstrap-5') }}
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
            $('.modern-select2').each(function () {
                const $select = $(this);

                if ($select.data('select2')) {
                    return;
                }

                $select.wrap('<div class="position-relative"></div>').select2({
                    placeholder: $select.data('placeholder') || '',
                    allowClear: true,
                    width: '100%',
                });
            });
        })();
    </script>
@endsection
