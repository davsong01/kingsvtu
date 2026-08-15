@extends('sneat.layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
    @php
        $currency = getSettings()->currency ?? '₦';
        $customerName = data_get($customer, 'customer.user.username', data_get($customer, 'customer.user.firstname', 'N/A'));
        $customerEmail = data_get($customer, 'customer.user.email', 'N/A');
        $customerSpent = (float) data_get($customer, 'total_amount', 0);
        $customerTransactions = (int) data_get($customer, 'count', 0);
    @endphp

    

    <div id="analytics" class="row g-3 mt-1">
        @php
            $statCards = [
                ['label' => 'SERVER ADDRESS', 'value' => $server_address, 'icon' => 'bx-server', 'tone' => 'slate'],
                ['label' => 'REMOTE ADDRESS', 'value' => $remote_address, 'icon' => 'bx-globe', 'tone' => 'blue'],
                ['label' => 'TOTAL WALLET BALANCES', 'value' => $currency . number_format($total_wallet_balance, 2), 'icon' => 'bx-wallet', 'tone' => 'emerald'],
                ['label' => 'ALL TRANSACTIONS', 'value' => number_format($all_transactions_count), 'sub' => $currency . number_format($all_transactions_total, 2) . ' processed', 'icon' => 'bx-receipt', 'tone' => 'indigo'],
                ['label' => 'REFERRAL EARNINGS', 'value' => $currency . number_format($referral_earnings_total, 2), 'sub' => number_format($referral_earnings_count) . ' entries', 'icon' => 'bx-gift', 'tone' => 'amber'],
                ['label' => 'KYC VERIFIED USERS', 'value' => number_format($kyc_verified), 'icon' => 'bx-badge-check', 'tone' => 'green'],
                ['label' => 'REGISTERED USERS', 'value' => number_format($customers), 'icon' => 'bx-group', 'tone' => 'blue'],
                ['label' => 'ACTIVE USERS', 'value' => number_format($active_customers), 'icon' => 'bx-trending-up', 'tone' => 'emerald'],
            ];
        @endphp

        @foreach($statCards as $card)
            <div class="col-md-6 col-xl-3">
                <div class="admin-stat-card admin-stat-card--{{ $card['tone'] }}">
                    <div class="admin-stat-card__icon">
                        <i class="bx {{ $card['icon'] }}"></i>
                    </div>
                    <div class="admin-stat-card__label">{{ $card['label'] }}</div>
                    <div class="admin-stat-card__value">{{ $card['value'] }}</div>
                    @if(!empty($card['sub']))
                        <div class="admin-stat-card__sub">{{ $card['sub'] }}</div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-3 mt-1">
        <div class="col-xl-6 col-lg-12">
            <div class="admin-feature-card h-100">
                <div class="d-flex align-items-start justify-content-between gap-3 mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="admin-feature-card__icon">
                            <i class="bx bx-trophy"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold text-uppercase">Customer of the Month</div>
                            <h3 class="h5 fw-semibold mb-0">{{ $customer_month_label }}</h3>
                        </div>
                    </div>
                </div>

                @if(!empty($customer))
                    <div class="admin-feature-card__panel mb-4">
                        <div class="row g-3 align-items-start">
                            <div class="col-lg-7">
                                <div class="text-muted small fw-semibold text-uppercase mb-2">{{ $customer_month_label }}</div>
                                <div class="h4 fw-semibold mb-2">{{ $customerName }}</div>
                                <div class="text-secondary mb-2">{{ $customerEmail }}</div>
                                <div class="admin-feature-card__spent">{{ $currency }}{{ number_format($customerSpent, 1) }} spent</div>
                            </div>
                            <div class="col-lg-5">
                                <div class="admin-feature-card__meta-grid">
                                    <div class="admin-feature-card__meta">
                                        <div class="admin-feature-card__meta-value">{{ number_format($customerTransactions) }}</div>
                                        <div class="admin-feature-card__meta-label">Transactions</div>
                                    </div>
                                    <div class="admin-feature-card__meta">
                                        <div class="admin-feature-card__meta-value">{{ number_format($customers) }}</div>
                                        <div class="admin-feature-card__meta-label">Total Users</div>
                                    </div>
                                    {{-- <div class="admin-feature-card__meta">
                                        <div class="admin-feature-card__meta-value">{{ $currency }}{{ number_format($all_transactions_total, 1) }}</div>
                                        <div class="admin-feature-card__meta-label">Total Revenue</div>
                                    </div> --}}
                                    <div class="admin-feature-card__meta">
                                        <div class="admin-feature-card__meta-value">{{ $currency }}{{ number_format($total_wallet_balance, 1) }}</div>
                                        <div class="admin-feature-card__meta-label">Wallets Balance</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-light border mb-0">No customer activity available yet.</div>
                @endif
            </div>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6">
            <div class="admin-summary-card h-100">
                <div class="admin-summary-card__icon admin-summary-card__icon--blue">
                    <i class="bx bx-user"></i>
                </div>
                <div class="admin-summary-card__value">{{ number_format($customers) }}</div>
                <div class="admin-summary-card__label">Total Users</div>
            </div>
        </div>

        {{-- <div class="col-xl-2 col-lg-4 col-md-6">
            <div class="admin-summary-card h-100">
                <div class="admin-summary-card__icon admin-summary-card__icon--green">
                    <i class="bx bx-trending-up"></i>
                </div>
                <div class="admin-summary-card__value">{{ $currency }}{{ number_format($all_transactions_total, 1) }}</div>
                <div class="admin-summary-card__label">Total Revenue</div>
            </div>
        </div> --}}

        <div class="col-xl-2 col-lg-4 col-md-6">
            <div class="admin-summary-card h-100">
                <div class="admin-summary-card__icon admin-summary-card__icon--amber">
                    <i class="bx bx-wallet"></i>
                </div>
                <div class="admin-summary-card__value">{{ $currency }}{{ number_format($total_wallet_balance, 1) }}</div>
                <div class="admin-summary-card__label">Wallets Balance</div>
            </div>
        </div>
    </div>

    <div id="providers" class="row g-3 mt-1">
        <div class="col-12">
            <div class="admin-provider-card">
                <div class="sneat-card__body p-4 p-lg-4">
                    <div class="d-flex flex-column flex-md-row align-items-md-start justify-content-between gap-3 mb-4">
                        <div class="d-flex align-items-start gap-3">
                            <div class="admin-feature-card__icon admin-feature-card__icon--blue">
                                <i class="bx bx-server"></i>
                            </div>
                            <div>
                                <div class="text-muted small fw-semibold text-uppercase mb-1">Provider Summary</div>
                                <h3 class="h6 fw-semibold mb-1">API Providers</h3>
                                <p class="text-secondary small mb-0">Small health snapshot for connected providers and traffic.</p>
                            </div>
                        </div>
                        <span class="admin-provider-row__pill align-self-md-start">{{ $apis->count() }}</span>
                    </div>

                    <div class="admin-provider-row">
                        @forelse($apis as $api)
                            <div class="admin-provider-row__item">
                                <div>
                                    <div class="admin-provider-row__title">{{ $api->name }}</div>
                                    <div class="admin-provider-row__meta">
                                        {{ $api->status ?? 'active' }}
                                        @if(!is_null($api->balance))
                                            • {{ $currency }}{{ number_format($api->balance, 2) }}
                                        @endif
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="admin-provider-row__pill">{{ $api->transactions?->count() ?? 0 }}</div>
                                    <div class="admin-provider-row__meta mt-1">transactions</div>
                                </div>
                            </div>
                        @empty
                            <div class="alert alert-light border mb-0">No API providers found.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
