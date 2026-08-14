@extends('sneat.layouts.app')

@section('title', 'Dashboard')

@section('content')
    @php
        $user = auth()->user();
        $settings = getSettings();
        $currency = $settings->currency ?? '₦';
        $balance = $user->type === 'customer' ? $currency . number_format(walletBalance($user), 2) : $currency . '0.00';
        $referralBalance = $user->type === 'customer' ? $currency . number_format(referralBalance($user), 2) : $currency . '0.00';
        $levelName = $user->customer?->level?->name ?? 'Not assigned';
        $kycStatus = getFinalKycStatus($user->customer->id) ?? 'pending';
        $kycLabel = formatKycStatusLabel($kycStatus);
        $kycBadge = in_array($kycStatus, ['verified', 'approved'], true) ? 'success' : ($kycStatus === 'declined' ? 'danger' : 'warning');
        $services = getCategories();
        $serviceCount = $services->count();
        $referralLink = url('/register') . '?referral=' . $user->username;
        $firstName = $user->firstname ?: $user->username;
        $quickActions = [
            ['route' => route('customer.load.wallet'), 'label' => 'Fund Wallet', 'detail' => 'Add money', 'icon' => 'bx-wallet', 'color' => 'primary'],
        ];

        $quickActions[] = ['route' => route('customer.transaction.report'), 'label' => 'Transactions', 'detail' => 'View history', 'icon' => 'bx-receipt', 'color' => 'info'];
        $quickActions[] = ['route' => route('update.kyc.details'), 'label' => 'KYC', 'detail' => 'Verification', 'icon' => 'bx-id-card', 'color' => 'warning'];
        $quickActionColumn = count($quickActions) === 4 ? 'col-lg-3' : 'col-lg-4';
    @endphp

    <div class="customer-dashboard mt-3">
        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
            <div>
                <h1 class="h2 fw-bold mb-1">Dashboard</h1>
                <p class="text-secondary mb-0">An overview of your wallet, services, and referral activity.</p>
            </div>
            <a href="{{ route('customer.transaction.report') }}" class="btn btn-label-primary align-self-start align-self-sm-center">
                <i class="bx bx-receipt me-1"></i> Transaction History
            </a>
        </div>

        @include('sneat.layouts.alerts')
        @include('shared.kyc-rejection-alert')

        @if(($settings->allow_google_dashboard_ad ?? 'no') === 'yes')
            {!! $settings->google_dashboard_ad_code !!}
        @endif

        <div class="row g-4 mb-4">
            <div class="col-xl-8">
                <div class="card dashboard-balance-card border-0 h-100 position-relative text-white">
                    <div class="card-body p-4 p-lg-5 position-relative d-flex flex-column">
                        <div class="d-flex align-items-center justify-content-between gap-3 mb-4">
                            <div class="d-flex align-items-center gap-3">
                                <span class="dashboard-wallet-mark">
                                    <i class="bx bx-wallet fs-4"></i>
                                </span>
                                <div>
                                    <h6 class="text-white mb-1">Wallet</h6>
                                    <span class="dashboard-wallet-status">Available</span>
                                </div>
                            </div>
                            <button
                                id="wallet-balance-toggle"
                                class="dashboard-wallet-visibility"
                                type="button"
                                aria-label="Hide wallet balance"
                                title="Hide wallet balance"
                            >
                                <i class="bx bx-show fs-5"></i>
                            </button>
                        </div>

                        <div class="mb-4">
                            <span class="d-block text-white text-opacity-50 small mb-2">Available balance</span>
                            <div id="wallet-balance-value" class="dashboard-wallet-balance" data-balance="{{ $balance }}">{{ $balance }}</div>
                        </div>

                        <div class="dashboard-wallet-actions d-flex flex-wrap gap-2 mb-4">
                            <a href="{{ route('customer.load.wallet') }}" class="dashboard-wallet-primary-action btn px-4">
                                <i class="bx bx-plus-circle me-1"></i> Fund Wallet
                            </a>
                        </div>

                        <div class="dashboard-wallet-footer d-flex align-items-end justify-content-between gap-3 pt-3 mt-auto">
                            <div>
                                <small class="d-block text-white text-opacity-50 mb-1">Account</small>
                                <span class="fw-semibold">{{ '@' . $user->username }}</span>
                            </div>
                            <a href="{{ route('customer.transaction.report') }}" class="dashboard-wallet-history small">
                                History <i class="bx bx-right-arrow-alt align-middle"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="row g-4 h-100">
                    <div class="col-sm-6 col-xl-12">
                        <div class="card h-100 position-relative">
                            <div class="card-body d-flex align-items-center justify-content-between gap-3">
                                <div>
                                    <span class="text-muted small">Referral earnings</span>
                                    <h4 class="mb-0 mt-1">{{ $referralBalance }}</h4>
                                    <a href="{{ route('downlines') }}" class="small stretched-link">Earning history</a>
                                </div>
                                <span class="avatar-initial rounded bg-label-success p-3">
                                    <i class="bx bx-group fs-4"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-12">
                        <div class="card h-100 position-relative">
                            <div class="card-body d-flex align-items-center justify-content-between gap-3">
                                <div>
                                    <span class="text-muted small">Account status</span>
                                    <div class="d-flex flex-wrap align-items-center gap-2 mt-2">
                                        <span class="badge bg-label-{{ $kycBadge }}">KYC: {{ $kycLabel }}</span>
                                        <span class="badge bg-label-primary">{{ $levelName }}</span>
                                    </div>
                                    <a href="{{ route('update.kyc.details') }}" class="small stretched-link">Manage KYC</a>
                                </div>
                                <span class="avatar-initial rounded bg-label-primary p-3">
                                    <i class="bx bx-shield-quarter fs-4"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(($settings->customer_of_the_month_status ?? 'yes') === 'yes')
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4 p-lg-5">
                <div class="d-flex flex-column flex-lg-row align-items-lg-start justify-content-between gap-4">
                    <div class="d-flex align-items-start gap-3">
                        <div class="dashboard-action-icon bg-label-warning flex-shrink-0">
                            <i class="bx bx-trophy fs-4"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold text-uppercase mb-2">Customer of the Month</div>
                                <h3 class="mb-2">{{ now()->format('F Y') }}</h3>
                                @if(!empty($customer))
                                    <div class="h4 fw-bold mb-1">{{ data_get($customer, 'customer.user.username', data_get($customer, 'customer.user.firstname', 'N/A')) }}</div>
                                    <div class="text-muted">{{ data_get($customer, 'customer.user.email', '') }}</div>
                                    <div class="text-muted mt-2">{{ getSettings()->currency ?? '₦' }}{{ number_format((float) data_get($customer, 'total_amount', 0), 2) }} spent</div>
                                @else
                                    <div class="h4 fw-bold mb-1">No customer of the month yet</div>
                                    <div class="text-muted">You can be the customer of this month, start transacting!</div>
                                    <div class="text-muted mt-2">Keep using KingsVTU to build your activity and show up here.</div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
        @endif

        <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="mb-0">Quick Actions</h5>
        </div>
        <div class="row g-3 mb-4">
            @foreach($quickActions as $action)
                <div class="col-6 {{ $quickActionColumn }}">
                    <a href="{{ $action['route'] }}" class="dashboard-action card border-0 text-decoration-none h-100">
                        <div class="card-body p-3 p-md-4">
                            <div class="d-flex align-items-start justify-content-between gap-2 mb-3">
                                <span class="dashboard-action-icon bg-label-{{ $action['color'] }}">
                                    <i class="bx {{ $action['icon'] }} fs-4"></i>
                                </span>
                                <i class="dashboard-action-arrow bx bx-right-arrow-alt text-muted fs-5"></i>
                            </div>
                            <h6 class="mb-1">{{ $action['label'] }}</h6>
                            <small class="text-muted">{{ $action['detail'] }}</small>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        <div class="row g-4">
            <div class="col-xl-8">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">Services</h5>
                        <span class="badge bg-label-secondary">{{ $serviceCount }} available</span>
                    </div>
                    <div class="card-body pt-2">
                        <div class="row g-2">
                            @foreach($services as $service)
                                <div class="col-sm-6">
                                    <a href="{{ route('open.transaction.page', $service->slug) }}" class="dashboard-service d-flex align-items-center gap-3 rounded p-3 text-decoration-none text-body">
                                        <span class="dashboard-service-icon bg-label-primary">
                                            @if($service->icon)
                                                {!! $service->icon !!}
                                            @else
                                                <i class="bx bx-grid-alt"></i>
                                            @endif
                                        </span>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0">{{ $service->display_name }}</h6>
                                        </div>
                                        <i class="bx bx-chevron-right text-muted"></i>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="mb-0">Referral Link</h5>
                            <span class="avatar-initial rounded bg-label-success p-2"><i class="bx bx-share-alt"></i></span>
                        </div>
                        <div class="input-group">
                            <input
                                id="referral-link"
                                class="dashboard-referral-link form-control bg-body-tertiary"
                                type="text"
                                value="{{ $referralLink }}"
                                aria-label="Referral link"
                                readonly
                            >
                            <button id="copy-referral-link" class="btn btn-primary" type="button" data-link="{{ $referralLink }}">
                                <i class="bx bx-copy"></i>
                                <span class="d-none d-sm-inline ms-1">Copy</span>
                            </button>
                        </div>
                        <a href="{{ route('alldownlines') }}" class="btn btn-label-primary w-100 mt-3">View Referrals</a>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        (function () {
            const toggle = document.getElementById('wallet-balance-toggle');
            const balance = document.getElementById('wallet-balance-value');

            if (!toggle || !balance) {
                return;
            }

            const storageKey = 'kingsvtu-wallet-balance-hidden';
            const applyVisibility = function (hidden) {
                const label = hidden ? 'Show wallet balance' : 'Hide wallet balance';

                balance.textContent = hidden ? '••••••' : balance.dataset.balance;
                balance.dataset.hidden = hidden ? 'true' : 'false';
                toggle.setAttribute('aria-label', label);
                toggle.setAttribute('title', label);
                toggle.innerHTML = `<i class="bx ${hidden ? 'bx-hide' : 'bx-show'} fs-5"></i>`;
            };

            try {
                applyVisibility(localStorage.getItem(storageKey) === 'true');
            } catch (error) {
                applyVisibility(false);
            }

            toggle.addEventListener('click', function () {
                const hidden = balance.dataset.hidden !== 'true';
                applyVisibility(hidden);

                try {
                    localStorage.setItem(storageKey, String(hidden));
                } catch (error) {}
            });
        })();

        document.getElementById('copy-referral-link')?.addEventListener('click', async function () {
            const button = this;
            const originalHtml = button.innerHTML;

            try {
                await navigator.clipboard.writeText(button.dataset.link);
                button.innerHTML = '<i class="bx bx-check"></i><span class="d-none d-sm-inline ms-1">Copied</span>';
                setTimeout(() => button.innerHTML = originalHtml, 2000);
            } catch (error) {
                window.prompt('Copy your referral link:', button.dataset.link);
            }
        });
    </script>
@endsection
