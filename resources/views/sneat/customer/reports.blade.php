@extends('sneat.layouts.app')

@section('title', 'Reports')

@php
    $currency = getSettings()->currency ?? '₦';
@endphp

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="profile-hero mb-4 gateway-hero">
                <div class="profile-hero__meta">
                    <div class="profile-avatar"><i class="bx bx-line-chart"></i></div>
                    <div class="profile-meta">
                        <span class="gateway-hero__kicker">Export center</span>
                        <strong>Transaction report</strong>
                        <span>Generate transaction, wallet, or referral reports for a date range.</span>
                    </div>
                </div>
                <div class="gateway-summary">
                    <div class="gateway-summary__card">
                        <span class="gateway-summary__label">Transactions</span>
                        <span class="gateway-summary__value">{{ $products->count() }}</span>
                    </div>
                    <div class="gateway-summary__card">
                        <span class="gateway-summary__label">Categories</span>
                        <span class="gateway-summary__value">{{ $categories->count() }}</span>
                    </div>
                </div>
            </div>

            @include('sneat.layouts.alerts')

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card modern-admin-card">
                        <div class="card-header">
                            <h3>Report builder</h3>
                            <p>Pick the data type and the date range, then download the export.</p>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="{{ route('customer.transaction.report') }}">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="profile-label" for="type">Report type</label>
                                        <select class="form-select form-select-{{ formControlSize() }}" id="type" name="type" required>
                                            <option value="">Select type</option>
                                            <option value="transaction" @selected(request('type') === 'transaction')>Transactions</option>
                                            <option value="wallet" @selected(request('type') === 'wallet')>Wallet</option>
                                            <option value="earning" @selected(request('type') === 'earning')>Referral earnings</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="profile-label" for="category">Category</label>
                                        <select class="form-select form-select-{{ formControlSize() }}" id="category" name="category">
                                            <option value="">All categories</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->display_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="profile-label" for="status">Status</label>
                                        <select class="form-select form-select-{{ formControlSize() }}" id="status" name="status">
                                            <option value="">All statuses</option>
                                            <option value="delivered" @selected(request('status') === 'delivered')>Delivered / Successful</option>
                                            <option value="failed" @selected(request('status') === 'failed')>Failed</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="profile-label" for="unique_element">Search term</label>
                                        <input type="text" class="form-control form-control-{{ formControlSize() }}" id="unique_element" name="unique_element" value="{{ request('unique_element') }}" placeholder="Phone, meter, email...">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="profile-label" for="from">From</label>
                                        <input type="date" class="form-control form-control-{{ formControlSize() }}" id="from" name="from" value="{{ request('from') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="profile-label" for="to">To</label>
                                        <input type="date" class="form-control form-control-{{ formControlSize() }}" id="to" name="to" value="{{ request('to') }}">
                                    </div>
                                </div>

                                <div class="profile-footer mt-4">
                                    <button type="submit" class="btn btn-admin-submit">Download report</button>
                                    <a href="{{ route('customer.transaction.report') }}" class="btn btn-label-secondary">Reset</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card profile-card h-100">
                        <div class="card-header">
                            <h3>How it works</h3>
                            <p>Each report type downloads immediately once submitted.</p>
                        </div>
                        <div class="card-body">
                            <div class="profile-side-row">
                                <span>Transactions</span>
                                <strong>Filter by service and status</strong>
                            </div>
                            <div class="profile-side-row">
                                <span>Wallet</span>
                                <strong>Wallet movement only</strong>
                            </div>
                            <div class="profile-side-row">
                                <span>Earnings</span>
                                <strong>Referral payouts</strong>
                            </div>
                            <div class="gateway-helper mt-3">
                                Use the date range to narrow the export before downloading.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
