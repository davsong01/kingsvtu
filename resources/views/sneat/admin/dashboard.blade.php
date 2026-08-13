@extends('sneat.layouts.app')

@section('title', 'Admin Dashboard')

@section('page-css')
    <style>
        .admin-dashboard-hero {
            position: relative;
            overflow: hidden;
            padding: 1.5rem;
            border-radius: 1.5rem;
            background:
                radial-gradient(circle at top right, rgba(24, 168, 107, .16), transparent 28%),
                linear-gradient(135deg, rgba(17, 24, 39, .98), rgba(30, 41, 59, .97));
            color: #fff;
        }

        .admin-dashboard-hero__eyebrow {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            padding: .38rem .8rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, .12);
            font-size: .78rem;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .admin-dashboard-hero__search {
            display: flex;
            align-items: center;
            gap: .7rem;
            padding: .95rem 1rem;
            border-radius: 1rem;
            border: 1px solid rgba(255, 255, 255, .12);
            background: rgba(255, 255, 255, .08);
            min-width: min(100%, 340px);
        }

        .admin-dashboard-hero__search input {
            width: 100%;
            border: 0;
            outline: none;
            background: transparent;
            color: #fff;
        }

        .admin-dashboard-hero__search input::placeholder {
            color: rgba(255, 255, 255, .62);
        }

        .admin-dashboard-hero__search i {
            font-size: 1.1rem;
        }

        .admin-pills {
            display: flex;
            gap: .65rem;
            flex-wrap: nowrap;
            overflow-x: auto;
            padding-bottom: .25rem;
        }

        .admin-pills::-webkit-scrollbar {
            height: 6px;
        }

        .admin-pills::-webkit-scrollbar-thumb {
            background: rgba(15, 23, 42, .12);
            border-radius: 999px;
        }

        .admin-action-card {
            transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
        }

        .admin-action-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 1.4rem 2.75rem rgba(15, 23, 42, .12);
            border-color: rgba(24, 168, 107, .18);
        }

        .admin-action-card__icon {
            width: 3rem;
            height: 3rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 1rem;
            background: rgba(24, 168, 107, .12);
            color: #18a86b;
        }

        .admin-action-card__icon i {
            font-size: 1.35rem;
        }

        .admin-provider-row {
            display: grid;
            gap: .85rem;
        }

        .admin-provider-row__item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: .95rem 1rem;
            border: 1px solid var(--sneat-border);
            border-radius: 1rem;
            background: var(--sneat-surface-strong);
        }

        .admin-provider-row__title {
            font-weight: 700;
            margin-bottom: .15rem;
        }

        .admin-provider-row__meta {
            color: var(--sneat-muted);
            font-size: .9rem;
        }
    </style>
@endsection

@section('content')
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h2 fw-bold mb-1">Admin Dashboard</h1>
            <p class="text-secondary mb-0">A clean control room for customers, products, providers, and platform finance.</p>
        </div>

        <label class="admin-dashboard-hero__search">
            <i class="bx bx-search text-white-50"></i>
            <input type="search" placeholder="Search admin..." aria-label="Search admin dashboard">
        </label>
    </div>

    <div class="admin-pills mb-4">
        <a href="#analytics" class="sneat-pill is-active">Analytics</a>
        <a href="#customers" class="sneat-pill">Customers</a>
        <a href="#operations" class="sneat-pill">Operations</a>
        <a href="#providers" class="sneat-pill">Providers</a>
        <a href="#settings" class="sneat-pill">Settings</a>
    </div>

    <div id="analytics" class="row g-3">
        <div class="col-md-6 col-xl-3">
            <div class="sneat-card sneat-stat h-100">
                <div class="sneat-card__body">
                    <div class="sneat-stat__icon mb-3"><i class="bx bx-wallet"></i></div>
                    <div class="text-muted small mb-1">Total Wallet Balance</div>
                    <div class="h4 fw-bold mb-0">{{ getSettings()->currency ?? '₦' }}{{ number_format($total_wallet_balance, 2) }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="sneat-card sneat-stat h-100">
                <div class="sneat-card__body">
                    <div class="sneat-stat__icon mb-3"><i class="bx bx-user-check"></i></div>
                    <div class="text-muted small mb-1">Verified KYC</div>
                    <div class="h4 fw-bold mb-0">{{ number_format($kyc_verified) }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="sneat-card sneat-stat h-100">
                <div class="sneat-card__body">
                    <div class="sneat-stat__icon mb-3"><i class="bx bx-group"></i></div>
                    <div class="text-muted small mb-1">Registered Customers</div>
                    <div class="h4 fw-bold mb-0">{{ number_format($customers) }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="sneat-card sneat-stat h-100">
                <div class="sneat-card__body">
                    <div class="sneat-stat__icon mb-3"><i class="bx bx-trending-up"></i></div>
                    <div class="text-muted small mb-1">Active Customers</div>
                    <div class="h4 fw-bold mb-0">{{ number_format($active_customers) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div id="customers" class="row g-3 mt-1">
        <div class="col-xl-5">
            <div class="sneat-card h-100">
                <div class="sneat-card__body">
                    <div class="d-flex align-items-start justify-content-between gap-3 mb-4">
                        <div>
                            <div class="text-muted small mb-1">Featured Customer</div>
                            <h3 class="h5 fw-bold mb-1">Customer of the Month</h3>
                            <p class="text-secondary mb-0">The user with the strongest activity this month.</p>
                        </div>
                        <span class="sneat-badge">Live</span>
                    </div>

                    @if(!empty($customer))
                        <div class="d-flex align-items-center justify-content-between gap-3 mb-4">
                            <div>
                                <div class="display-6 fw-bold mb-1">{{ data_get($customer, 'customer.user.username', data_get($customer, 'customer.user.firstname', 'N/A')) }}</div>
                                <div class="text-muted">{{ number_format(data_get($customer, 'count', 0)) }} successful transactions</div>
                            </div>
                            <div class="text-end">
                                <div class="text-muted small mb-1">Estimated total</div>
                                <div class="h4 fw-bold mb-0">{{ getSettings()->currency ?? '₦' }}{{ number_format((float) data_get($customer, 'total_amount', 0), 2) }}</div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-light border mb-0">
                            No customer activity available yet.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-7">
            <div class="sneat-card h-100">
                <div class="sneat-card__body">
                    <div class="d-flex align-items-start justify-content-between gap-3 mb-4">
                        <div>
                            <div class="text-muted small mb-1">Provider Summary</div>
                            <h3 class="h5 fw-bold mb-1">API Providers</h3>
                            <p class="text-secondary mb-0">A simple snapshot of provider health and usage.</p>
                        </div>
                        <span class="sneat-badge">{{ $apis->count() }}</span>
                    </div>

                    <div class="admin-provider-row">
                        @forelse($apis as $api)
                            <div class="admin-provider-row__item">
                                <div>
                                    <div class="admin-provider-row__title">{{ $api->name }}</div>
                                    <div class="admin-provider-row__meta">
                                        {{ $api->status ?? 'active' }} 
                                        @if(!is_null($api->balance))
                                            • {{ getSettings()->currency ?? '₦' }}{{ number_format($api->balance, 2) }}
                                        @endif
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold">{{ $api->transactions?->count() ?? 0 }}</div>
                                    <div class="admin-provider-row__meta">transactions</div>
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

    <div id="operations" class="row g-3 mt-1">
        <div class="col-md-6 col-xl-4">
            <a href="{{ route('customers') }}" class="text-decoration-none">
                <div class="sneat-card admin-action-card h-100">
                    <div class="sneat-card__body d-flex align-items-start gap-3">
                        <div class="admin-action-card__icon"><i class="bx bx-group"></i></div>
                        <div class="flex-grow-1">
                            <h4 class="h6 fw-bold mb-1">Customers</h4>
                            <p class="text-secondary mb-0">Open the customer registry and support actions.</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-xl-4">
            <a href="{{ route('product.index') }}" class="text-decoration-none">
                <div class="sneat-card admin-action-card h-100">
                    <div class="sneat-card__body d-flex align-items-start gap-3">
                        <div class="admin-action-card__icon"><i class="bx bx-package"></i></div>
                        <div class="flex-grow-1">
                            <h4 class="h6 fw-bold mb-1">Products</h4>
                            <p class="text-secondary mb-0">Manage products, pricing, and variation setup.</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-xl-4">
            <a href="{{ route('category.index') }}" class="text-decoration-none">
                <div class="sneat-card admin-action-card h-100">
                    <div class="sneat-card__body d-flex align-items-start gap-3">
                        <div class="admin-action-card__icon"><i class="bx bx-store-alt"></i></div>
                        <div class="flex-grow-1">
                            <h4 class="h6 fw-bold mb-1">Categories</h4>
                            <p class="text-secondary mb-0">Keep services and product groupings organized.</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-xl-4">
            <a href="{{ route('api.index') }}" class="text-decoration-none">
                <div class="sneat-card admin-action-card h-100">
                    <div class="sneat-card__body d-flex align-items-start gap-3">
                        <div class="admin-action-card__icon"><i class="bx bx-link-alt"></i></div>
                        <div class="flex-grow-1">
                            <h4 class="h6 fw-bold mb-1">API Providers</h4>
                            <p class="text-secondary mb-0">Review provider status and balances at a glance.</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-xl-4">
            <a href="{{ route('admin.trans') }}" class="text-decoration-none">
                <div class="sneat-card admin-action-card h-100">
                    <div class="sneat-card__body d-flex align-items-start gap-3">
                        <div class="admin-action-card__icon"><i class="bx bx-receipt"></i></div>
                        <div class="flex-grow-1">
                            <h4 class="h6 fw-bold mb-1">Transactions</h4>
                            <p class="text-secondary mb-0">Inspect product purchase and funding activity.</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-xl-4">
            <a href="{{ route('settings.edit') }}" class="text-decoration-none">
                <div class="sneat-card admin-action-card h-100">
                    <div class="sneat-card__body d-flex align-items-start gap-3">
                        <div class="admin-action-card__icon"><i class="bx bx-cog"></i></div>
                        <div class="flex-grow-1">
                            <h4 class="h6 fw-bold mb-1">App Settings</h4>
                            <p class="text-secondary mb-0">Switch layout modes and update platform config.</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div id="settings" class="row g-3 mt-1">
        <div class="col-12">
            <div class="sneat-card">
                <div class="sneat-card__body d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                    <div>
                        <div class="text-muted small mb-1">Backward compatibility</div>
                        <h3 class="h5 fw-bold mb-1">Legacy layout still stays available</h3>
                        <p class="text-secondary mb-0">You can move admin back to the old shell later without breaking the dashboard route.</p>
                    </div>
                    <a href="{{ route('settings.edit') }}" class="btn btn-success btn-lg">Open Settings</a>
                </div>
            </div>
        </div>
    </div>
@endsection
