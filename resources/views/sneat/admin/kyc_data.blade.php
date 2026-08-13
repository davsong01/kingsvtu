@extends('sneat.layouts.app')

@section('title', 'KYC Management')

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            @php
                $statusCounts = [
                    ['label' => 'Total Customers', 'value' => number_format($totalCustomers), 'icon' => 'bx-group', 'tone' => 'blue'],
                    ['label' => 'Verified', 'value' => number_format($verifiedCount), 'icon' => 'bx-badge-check', 'tone' => 'green'],
                    ['label' => 'Awaiting Approval', 'value' => number_format($awaitingCount), 'icon' => 'bx-time-five', 'tone' => 'amber'],
                    ['label' => 'Unverified', 'value' => number_format($unverifiedCount), 'icon' => 'bx-error', 'tone' => 'slate'],
                ];
            @endphp

            <div class="admin-page-hero mb-4">
                <div>
                    <span class="admin-page-hero__kicker">Customer compliance</span>
                    <h1>KYC Management</h1>
                    <p>Review customer verification status, open records quickly, and keep KYC approvals organized from one clean admin page.</p>
                </div>
                <div class="admin-page-badges">
                    @foreach($statusCounts as $status)
                        <div class="admin-page-badge">
                            <span>{{ $status['label'] }}</span>
                            <strong>{{ $status['value'] }}</strong>
                        </div>
                    @endforeach
                </div>
            </div>

            @include('sneat.layouts.alerts')

            <div class="gateway-card card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.kyc') }}" class="row g-3 align-items-end">
                        <div class="col-xl-6 col-lg-6">
                            <label class="modern-admin-label" for="q">Search</label>
                            <input
                                type="text"
                                class="form-control form-control-{{ formControlSize() }}"
                                id="q"
                                name="q"
                                value="{{ request('q') }}"
                                placeholder="Search name, email, or phone"
                            >
                        </div>
                        <div class="col-xl-3 col-lg-3">
                            <label class="modern-admin-label" for="status">KYC Status</label>
                            <select class="form-select form-select-{{ formControlSize() }}" id="status" name="status">
                                <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>All Statuses</option>
                                <option value="verified" {{ request('status') === 'verified' ? 'selected' : '' }}>Verified</option>
                                <option value="awaiting-approval" {{ request('status') === 'awaiting-approval' ? 'selected' : '' }}>Awaiting Approval</option>
                                <option value="unverified" {{ request('status') === 'unverified' ? 'selected' : '' }}>Unverified</option>
                            </select>
                        </div>
                        <div class="col-xl-3 col-lg-3 d-flex gap-2">
                            <button type="submit" class="btn btn-admin-submit flex-grow-1">Filter</button>
                            <a href="{{ route('admin.kyc') }}" class="gateway-action">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="gateway-card card">
                <div class="card-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
                    <div>
                        <h3>KYC records</h3>
                        <p>Open a customer profile or review their verification state at a glance.</p>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="gateway-badge gateway-badge--active">Verified</span>
                        <span class="gateway-badge gateway-badge--warning">Awaiting</span>
                        <span class="gateway-badge gateway-badge--danger">Unverified</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="gateway-helper mb-3">
                        Showing {{ $customers->firstItem() ?? 0 }} to {{ $customers->lastItem() ?? 0 }} of {{ $customers->total() }} customers
                    </div>
                    <div class="table-responsive">
                        <table class="table gateway-table align-middle">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Customer</th>
                                    <th>Contact</th>
                                    <th>KYC Status</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customers as $customer)
                                    @php
                                        $serialNumber = $customers->firstItem() + $loop->index;
                                        $kycStatus = strtolower((string) ($customer->kyc_status ?? 'unverified'));
                                        $badgeClass = match ($kycStatus) {
                                            'verified' => 'gateway-badge--active',
                                            'awaiting-approval' => 'gateway-badge--warning',
                                            default => 'gateway-badge--danger',
                                        };
                                    @endphp
                                    <tr>
                                        <td>{{ $serialNumber }}</td>
                                        <td>
                                            <div class="gateway-name">
                                                {{ trim(($customer->user->firstname ?? '') . ' ' . ($customer->user->lastname ?? '')) ?: 'Unnamed customer' }}
                                            </div>
                                            <div class="gateway-helper">Customer ID: {{ $customer->id }}</div>
                                        </td>
                                        <td>
                                            <div class="gateway-helper">{{ $customer->user->email ?? 'No email' }}</div>
                                            <div class="gateway-helper">{{ $customer->user->phone ?? 'No phone' }}</div>
                                        </td>
                                        <td>
                                            <span class="gateway-badge {{ $badgeClass }}">
                                                {{ ucfirst(str_replace('-', ' ', $kycStatus)) }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            @if(hasAccess('customers.edit'))
                                                <a href="{{ route('customers.edit', $customer->user->id) }}" class="gateway-action">View</a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">
                                            <div class="alert alert-light border mb-0">No customer KYC records available.</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end mt-4">
                        {{ $customers->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
