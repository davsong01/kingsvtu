@extends('sneat.layouts.app')

@section('title', 'Dashboard')

@section('page-css')
    <style>
        .customer-dashboard-hero {
            position: relative;
            overflow: hidden;
            padding: 1.5rem;
            border-radius: 1.5rem;
            background:
                radial-gradient(circle at 90% 10%, rgba(255, 255, 255, .16), transparent 28%),
                linear-gradient(135deg, #0f172a 0%, #111827 48%, #1e293b 100%);
            color: #fff;
        }

        .customer-dashboard-hero__title {
            font-size: clamp(2rem, 3vw, 2.9rem);
            line-height: 1.05;
            letter-spacing: -.04em;
            margin-bottom: .7rem;
        }

        .customer-dashboard-hero__balance {
            font-size: clamp(2.1rem, 3.7vw, 3.5rem);
            line-height: 1;
            font-weight: 800;
            letter-spacing: -.05em;
        }

        .customer-dashboard-hero__muted {
            color: rgba(255, 255, 255, .72);
        }

        .customer-dashboard-hero__actions {
            display: flex;
            gap: .75rem;
            flex-wrap: wrap;
        }

        .customer-dashboard-hero__actions .btn {
            border-radius: .9rem;
        }

        .customer-referral {
            word-break: break-all;
            font-size: .92rem;
        }

        .customer-service-card {
            transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
        }

        .customer-service-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 1.25rem 2.5rem rgba(15, 23, 42, .11);
            border-color: rgba(24, 168, 107, .18);
        }

        .customer-service-card__icon {
            width: 3rem;
            height: 3rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 1rem;
            background: rgba(24, 168, 107, .12);
            color: #18a86b;
        }

        .customer-service-card__icon i {
            font-size: 1.25rem;
        }

        .customer-mini-stat {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .customer-mini-stat__value {
            font-size: 1.25rem;
            font-weight: 800;
            letter-spacing: -.03em;
        }

        .customer-category-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .9rem;
        }

        @media (min-width: 768px) {
            .customer-category-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (min-width: 1200px) {
            .customer-category-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }
    </style>
@endsection

@section('content')
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
        <div>
            <div class="text-uppercase text-muted small fw-semibold mb-2">Welcome back</div>
            <h1 class="h2 fw-bold mb-1">Dashboard</h1>
            <p class="text-secondary mb-0">Your modern dashboard, with the old shell still available in the background.</p>
        </div>

        <div class="sneat-search">
            <i class="bx bx-search text-muted"></i>
            <input type="search" placeholder="Search services..." aria-label="Search services">
        </div>
    </div>

    <div class="row g-3">
        <div class="col-xl-8">
            <div class="customer-dashboard-hero h-100">
                <div class="d-flex flex-column flex-lg-row justify-content-between gap-4">
                    <div class="flex-grow-1">
                        <div class="customer-dashboard-hero__muted text-uppercase small fw-semibold mb-2">Wallet Overview</div>
                        <div class="customer-dashboard-hero__title">Keep money moving without the clutter.</div>

                        @php
                            $balance = auth()->user()->type === 'customer' ? getSettings()->currency . number_format(walletBalance(auth()->user()), 2) : '0';
                            $referralBalance = auth()->user()->type === 'customer' ? getSettings()->currency . number_format(referralBalance(auth()->user()), 2) : '0';
                            $referralLink = url('/register') . '?referral=' . auth()->user()->username;
                        @endphp

                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <div class="sneat-card" style="background: rgba(255,255,255,.08); border-color: rgba(255,255,255,.12); box-shadow: none;">
                                    <div class="sneat-card__body">
                                        <div class="customer-dashboard-hero__muted small mb-1">Wallet Balance</div>
                                        <div class="customer-dashboard-hero__balance">{{ $balance }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="sneat-card" style="background: rgba(255,255,255,.08); border-color: rgba(255,255,255,.12); box-shadow: none;">
                                    <div class="sneat-card__body">
                                        <div class="customer-dashboard-hero__muted small mb-1">Referral Earnings</div>
                                        <div class="customer-dashboard-hero__balance">{{ $referralBalance }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="customer-dashboard-hero__actions mt-4">
                            <a href="{{ route('customer.load.wallet') }}" class="btn btn-light btn-lg">
                                <i class="bx bx-wallet me-2"></i>Fund Wallet
                            </a>
                            <a href="{{ route('customer.transaction.history') }}" class="btn btn-outline-light btn-lg">
                                <i class="bx bx-receipt me-2"></i>History
                            </a>
                            <a href="{{ route('customer.transaction.report') }}" class="btn btn-outline-light btn-lg">
                                <i class="bx bx-bar-chart-square me-2"></i>Reports
                            </a>
                        </div>
                    </div>

                    <div class="flex-shrink-0" style="min-width: min(100%, 320px);">
                        <div class="sneat-card h-100" style="background: rgba(255,255,255,.08); border-color: rgba(255,255,255,.12); box-shadow: none;">
                            <div class="sneat-card__body">
                                <div class="text-uppercase small fw-semibold customer-dashboard-hero__muted mb-2">Referral Link</div>
                                <div class="customer-referral text-white mb-3" id="referral-link">{{ $referralLink }}</div>
                                <button type="button" class="btn btn-success w-100" onclick="copyReferralLink()">
                                    <i class="bx bx-copy me-2"></i>Copy Link
                                </button>

                                @if(!empty($customer))
                                    <hr class="border-white border-opacity-10 my-4">
                                    <div class="customer-dashboard-hero__muted small mb-2">Customer of the Month</div>
                                    <div class="h5 fw-bold mb-1">{{ data_get($customer, 'customer.user.username', data_get($customer, 'customer.user.firstname', 'N/A')) }}</div>
                                    <div class="customer-dashboard-hero__muted">{{ number_format(data_get($customer, 'count', 0)) }} successful transactions</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="sneat-card h-100">
                <div class="sneat-card__body">
                    <div class="d-flex align-items-start justify-content-between gap-3 mb-4">
                        <div>
                            <div class="text-muted small mb-1">Account Snapshot</div>
                            <h3 class="h5 fw-bold mb-1">Quick Status</h3>
                        </div>
                        <span class="sneat-badge">Live</span>
                    </div>

                    <div class="row g-3">
                        <div class="col-6">
                            <div class="sneat-card sneat-card--solid">
                                <div class="sneat-card__body">
                                    <div class="text-muted small mb-1">Customer Level</div>
                                    <div class="customer-mini-stat__value">{{ auth()->user()->customer?->level?->name ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="sneat-card sneat-card--solid">
                                <div class="sneat-card__body">
                                    <div class="text-muted small mb-1">API Access</div>
                                    <div class="customer-mini-stat__value">{{ auth()->user()->customer?->api_access ?? 'inactive' }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="sneat-card sneat-card--solid">
                                <div class="sneat-card__body">
                                    <div class="text-muted small mb-1">Downlines</div>
                                    <div class="customer-mini-stat__value">See Menu</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="sneat-card sneat-card--solid">
                                <div class="sneat-card__body">
                                    <div class="text-muted small mb-1">KYC</div>
                                    <div class="customer-mini-stat__value">{{ auth()->user()->customer?->kyc_status ?? 'pending' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mt-1">
        <div class="col-lg-4">
            <div class="sneat-card h-100">
                <div class="sneat-card__body">
                    <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
                        <div>
                            <div class="text-muted small mb-1">Featured</div>
                            <h3 class="h5 fw-bold mb-1">Customer of the Month</h3>
                        </div>
                        <i class="bx bx-crown text-warning fs-4"></i>
                    </div>
                    @if(!empty($customer))
                        <div class="d-flex align-items-end justify-content-between gap-3">
                            <div>
                                <div class="h4 fw-bold mb-1">{{ data_get($customer, 'customer.user.username', 'N/A') }}</div>
                                <div class="text-muted">{{ number_format(data_get($customer, 'count', 0)) }} transactions</div>
                            </div>
                            <div class="text-end">
                                <div class="text-muted small mb-1">Volume</div>
                                <div class="h5 fw-bold mb-0">{{ getSettings()->currency ?? '₦' }}{{ number_format((float) data_get($customer, 'total_amount', 0), 2) }}</div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-light border mb-0">No customer activity available yet.</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="sneat-card h-100">
                <div class="sneat-card__body">
                    <div class="d-flex align-items-start justify-content-between gap-3 mb-4">
                        <div>
                            <div class="text-muted small mb-1">Services</div>
                            <h3 class="h5 fw-bold mb-1">Quick Access</h3>
                            <p class="text-secondary mb-0">Open the services you use most often from the dashboard.</p>
                        </div>
                        <span class="sneat-badge">{{ getCategories()->count() }}</span>
                    </div>

                    <div class="customer-category-grid">
                        <a href="{{ route('customer.load.wallet') }}" class="text-decoration-none">
                            <div class="sneat-card customer-service-card h-100">
                                <div class="sneat-card__body d-flex align-items-start gap-3">
                                    <div class="customer-service-card__icon"><i class="bx bx-wallet"></i></div>
                                    <div>
                                        <h4 class="h6 fw-bold mb-1">Fund Wallet</h4>
                                        <p class="text-secondary mb-0">Top up your balance quickly.</p>
                                    </div>
                                </div>
                            </div>
                        </a>

                        <a href="{{ route('customer.transaction.history') }}" class="text-decoration-none">
                            <div class="sneat-card customer-service-card h-100">
                                <div class="sneat-card__body d-flex align-items-start gap-3">
                                    <div class="customer-service-card__icon"><i class="bx bx-receipt"></i></div>
                                    <div>
                                        <h4 class="h6 fw-bold mb-1">Transaction History</h4>
                                        <p class="text-secondary mb-0">Review your recent activity.</p>
                                    </div>
                                </div>
                            </div>
                        </a>

                        <a href="{{ route('customer.transaction.report') }}" class="text-decoration-none">
                            <div class="sneat-card customer-service-card h-100">
                                <div class="sneat-card__body d-flex align-items-start gap-3">
                                    <div class="customer-service-card__icon"><i class="bx bx-bar-chart-square"></i></div>
                                    <div>
                                        <h4 class="h6 fw-bold mb-1">Reports</h4>
                                        <p class="text-secondary mb-0">Track service usage trends.</p>
                                    </div>
                                </div>
                            </div>
                        </a>

                        <a href="{{ route('update.kyc.details') }}" class="text-decoration-none">
                            <div class="sneat-card customer-service-card h-100">
                                <div class="sneat-card__body d-flex align-items-start gap-3">
                                    <div class="customer-service-card__icon"><i class="bx bx-badge-check"></i></div>
                                    <div>
                                        <h4 class="h6 fw-bold mb-1">KYC Info</h4>
                                        <p class="text-secondary mb-0">Keep your account verified.</p>
                                    </div>
                                </div>
                            </div>
                        </a>

                        @foreach(getCategories() as $category)
                            <a href="{{ route('open.transaction.page', $category->slug) }}" class="text-decoration-none">
                                <div class="sneat-card customer-service-card h-100">
                                    <div class="sneat-card__body d-flex align-items-start gap-3">
                                        <div class="customer-service-card__icon">
                                            @if($category->icon)
                                                {!! $category->icon !!}
                                            @else
                                                <i class="bx bx-grid-alt"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <h4 class="h6 fw-bold mb-1">{{ $category->display_name }}</h4>
                                            <p class="text-secondary mb-0">Open this service from Sneat.</p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        function copyReferralLink() {
            (async () => {
                try {
                    const copyText = document.getElementById('referral-link');
                    const text = copyText.innerText.trim();
                    await navigator.clipboard.writeText(text);
                    copyText.innerText = 'Link copied!';
                    setTimeout(() => {
                        copyText.innerText = text;
                    }, 3000);
                } catch (error) {
                    alert(error.message);
                }
            })();
        }
    </script>
@endsection
