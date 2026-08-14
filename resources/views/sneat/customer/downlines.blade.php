@extends('sneat.layouts.app')

@section('title', 'Downlines')

@php
    $downlineItems = collect($refs ?? []);
    $isDetailView = filled($check ?? null);
    $currency = getSettings()->currency ?? '₦';
    $totalEarnings = $downlineItems->sum(function ($item) {
        if (is_object($item) && method_exists($item, 'total_earnings')) {
            return (float) $item->total_earnings();
        }

        return (float) data_get($item, 'amount', 0);
    });
    $totalEntries = $downlineItems->count();
    $uniqueDownlines = $isDetailView
        ? $downlineItems->count()
        : $downlineItems->pluck('referred_customer_id')->filter()->unique()->count();
@endphp

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="profile-hero mb-4 gateway-hero">
                <div class="profile-hero__meta">
                    <div class="profile-avatar"><i class="bx bx-group"></i></div>
                    <div class="profile-meta">
                        <span class="gateway-hero__kicker">Referral center</span>
                        <strong>{{ $isDetailView ? 'Downline transactions' : 'My downlines' }}</strong>
                        <span>{{ $isDetailView ? 'Transactions earned from a selected referral.' : 'Track your referrals and what they have generated for you.' }}</span>
                    </div>
                </div>
                <div class="gateway-summary">
                    <div class="gateway-summary__card">
                        <span class="gateway-summary__label">Total earnings</span>
                        <span class="gateway-summary__value">{{ $currency . number_format($totalEarnings, 2) }}</span>
                    </div>
                    <div class="gateway-summary__card">
                        <span class="gateway-summary__label">Entries</span>
                        <span class="gateway-summary__value">{{ $totalEntries }}</span>
                    </div>
                    <div class="gateway-summary__card">
                        <span class="gateway-summary__label">Downlines</span>
                        <span class="gateway-summary__value">{{ $uniqueDownlines }}</span>
                    </div>
                </div>
            </div>

            @include('sneat.layouts.alerts')

            <div class="card modern-admin-card">
                <div class="card-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div>
                        <h3 class="mb-1">{{ $isDetailView ? 'Referral transactions' : 'Referred customers' }}</h3>
                        <p class="mb-0">A cleaner summary of your referral network.</p>
                    </div>
                    <a href="{{ route('downlines.withdraw') }}" class="btn btn-admin-submit">
                        <i class="bx bx-transfer me-1"></i> Withdraw commission
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    @if(! $isDetailView)
                                        <th>Username</th>
                                        <th>Name</th>
                                        <th>Total Earnings</th>
                                        <th>Joined</th>
                                        <th>Action</th>
                                    @else
                                        <th>Service</th>
                                        <th>Amount</th>
                                        <th>Earning</th>
                                        <th>Date</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($downlineItems as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        @if(! $isDetailView)
                                            <td>{{ data_get($item, 'referredCustomer.user.username', 'N/A') }}</td>
                                            <td>{{ data_get($item, 'referredCustomer.user.firstname', 'N/A') }} {{ data_get($item, 'referredCustomer.user.lastname', '') }}</td>
                                            <td>{{ $currency . number_format((float) data_get($item, 'total_earnings', 0), 2) }}</td>
                                            <td>{{ optional(data_get($item, 'referredCustomer.user.created_at'))->format('M d, Y') ?? 'N/A' }}</td>
                                            <td>
                                                <a href="{{ route('downlines', data_get($item, 'referred_customer_id')) }}" class="btn btn-sm btn-label-primary">View details</a>
                                            </td>
                                        @else
                                            <td>{{ data_get($item, 'transaction.product_name', data_get($item, 'product_name', 'N/A')) }}</td>
                                            <td>{{ $currency . number_format((float) data_get($item, 'transaction.amount', data_get($item, 'amount', 0)), 2) }}</td>
                                            <td>{{ $currency . number_format((float) data_get($item, 'amount', 0), 2) }}</td>
                                            <td>{{ optional(data_get($item, 'created_at'))->format('M d, Y h:i A') ?? 'N/A' }}</td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ $isDetailView ? 5 : 6 }}">
                                            <div class="alert alert-light border mb-0">No referral records found yet.</div>
                                        </td>
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
