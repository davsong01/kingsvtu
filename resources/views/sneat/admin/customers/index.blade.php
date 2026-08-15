@extends('sneat.layouts.app')

@section('title', 'Customers')

@section('content')
    @php
        $activeStatus = request('status') ?: $status ?? 'all';
        $filters = [
            ['label' => 'Total Customers', 'value' => number_format($totalCustomers), 'icon' => 'bx-group', 'tone' => 'blue'],
            ['label' => 'Active Customers', 'value' => number_format($activeCustomers), 'icon' => 'bx-check-circle', 'tone' => 'green'],
            ['label' => 'Suspended Customers', 'value' => number_format($suspendedCustomers), 'icon' => 'bx-block', 'tone' => 'amber'],
            ['label' => 'KYC Verified', 'value' => number_format($verifiedCustomers), 'icon' => 'bx-badge-check', 'tone' => 'indigo'],
        ];
    @endphp

    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="admin-page-hero mb-4">
                <div>
                    <span class="admin-page-hero__kicker">Customer management</span>
                    <h1>Customers</h1>
                    <p>Review accounts, filter by status, and keep customer operations organized in a cleaner admin shell.</p>
                </div>
               
            </div>

            @include('sneat.layouts.alerts')

            <div class="row g-3 mb-4">
                @foreach($filters as $filter)
                    <div class="col-md-6 col-xl-3">
                        <div class="admin-stat-card admin-stat-card--{{ $filter['tone'] }}">
                            <div class="admin-stat-card__icon">
                                <i class="bx {{ $filter['icon'] }}"></i>
                            </div>
                            <div class="admin-stat-card__label">{{ $filter['label'] }}</div>
                            <div class="admin-stat-card__value">{{ $filter['value'] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="gateway-card card mb-4">
                <div class="card-header d-flex flex-column flex-lg-row align-items-lg-start justify-content-between gap-3">
                    <div>
                        <h3>Search customers</h3>
                        <p>Filter by contact details, level, or joined date.</p>
                    </div>
                    <span class="gateway-badge gateway-badge--active">{{ $customers->total() }} results</span>
                </div>
                <div class="card-body">
                    <form action="{{ route('customers') }}" method="GET">
                        <div class="row g-3">
                            <div class="col-xl-4 col-lg-6">
                                <label for="email" class="modern-admin-label">Email address</label>
                                <input
                                    type="email"
                                    class="form-control form-control-{{ formControlSize() }}"
                                    id="email"
                                    name="email"
                                    placeholder="Enter email address"
                                    value="{{ request('email') }}"
                                >
                            </div>
                            <div class="col-xl-4 col-lg-6">
                                <label for="phone" class="modern-admin-label">Phone number</label>
                                <input
                                    type="text"
                                    class="form-control form-control-{{ formControlSize() }}"
                                    id="phone"
                                    name="phone"
                                    placeholder="Enter phone number"
                                    value="{{ request('phone') }}"
                                >
                            </div>
                            <div class="col-xl-4 col-lg-6">
                                <label for="username" class="modern-admin-label">Username</label>
                                <input
                                    type="text"
                                    class="form-control form-control-{{ formControlSize() }}"
                                    id="username"
                                    name="username"
                                    placeholder="Enter username"
                                    value="{{ request('username') }}"
                                >
                            </div>
                            <div class="col-xl-3 col-lg-4">
                                <label for="status" class="modern-admin-label">Status</label>
                                <select class="form-select form-select-{{ formControlSize() }}" name="status" id="status">
                                    <option value="">All statuses</option>
                                    <option value="active" @selected(request('status') === 'active' || $activeStatus === 'active')>Active</option>
                                    <option value="suspended" @selected(request('status') === 'suspended' || $activeStatus === 'suspended')>Suspended</option>
                                    <option value="api" @selected(request('status') === 'api' || $activeStatus === 'api')>API</option>
                                    <option value="email-blacklist" @selected(request('status') === 'email-blacklist' || $activeStatus === 'email-blacklist')>Email blacklist</option>
                                    <option value="phone-blacklist" @selected(request('status') === 'phone-blacklist' || $activeStatus === 'phone-blacklist')>Phone blacklist</option>
                                </select>
                            </div>
                            <div class="col-xl-3 col-lg-4">
                                <label for="customer_level" class="modern-admin-label">Customer level</label>
                                <select class="form-select form-select-{{ formControlSize() }}" name="customer_level" id="customer_level">
                                    <option value="">All levels</option>
                                    @foreach($customer_levels as $customer_level)
                                        <option value="{{ $customer_level->id }}" @selected(request('customer_level') == $customer_level->id)>{{ $customer_level->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xl-2 col-lg-4">
                                <label for="from" class="modern-admin-label">Joined from</label>
                                <input type="date" class="form-control form-control-{{ formControlSize() }}" id="from" name="from" value="{{ request('from') }}">
                            </div>
                            <div class="col-xl-2 col-lg-4">
                                <label for="to" class="modern-admin-label">To</label>
                                <input type="date" class="form-control form-control-{{ formControlSize() }}" id="to" name="to" value="{{ request('to') }}">
                            </div>
                            <div class="col-xl-2 col-lg-4 d-flex align-items-end gap-2">
                                <button type="submit" class="btn btn-admin-submit w-100">Filter</button>
                                <a href="{{ route('customers') }}" class="gateway-action">Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="gateway-card card">
                <div class="card-header d-flex flex-column flex-lg-row align-items-lg-start justify-content-between gap-3">
                    <div>
                        <h3>Customer list</h3>
                        <p>Open a profile, change customer level, or apply a bulk action.</p>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="gateway-badge gateway-badge--active">Active</span>
                        <span class="gateway-badge gateway-badge--warning">Suspended</span>
                        <span class="gateway-badge gateway-badge--danger">Blacklist</span>
                    </div>
                </div>
                <div class="card-body">
                    <form id="actionForm" method="POST" action="{{ route('change-customer-level') }}" class="mb-4">
                        @csrf
                        <div class="row g-3 align-items-end">
                            <div class="col-lg-5 col-xl-4">
                                <label for="action-select" class="modern-admin-label">Bulk change customer level</label>
                                <select id="action-select" class="form-select form-select-{{ formControlSize() }}" name="action" required>
                                    <option value="" selected disabled>Select customer level</option>
                                    @foreach($customer_levels as $level)
                                        <option value="{{ $level->id }}">{{ $level->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-3 col-xl-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-secondary w-100" id="submit-action">Apply to selected</button>
                            </div>
                            <div class="col-lg-4 col-xl-6 text-lg-end">
                                <div class="gateway-helper">Select one or more rows before applying a bulk change.</div>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table gateway-table align-middle">
                            <thead>
                                <tr>
                                    <th class="customer-select-col">
                                        <input type="checkbox" id="select-all" class="form-check-input form-check-input-{{ checkBoxControlSize() }}">
                                    </th>
                                    <th>S/N</th>
                                    <th>Customer</th>
                                    <th>Status</th>
                                    <th>Level</th>
                                    <th>Balance</th>
                                    <th>Joined</th>
                                    @if(hasAccess('customers.edit'))
                                        <th class="text-end">Action</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($customers as $customer)
                                    @php
                                        $customerLevel = data_get($customer, 'customer.level.name', 'N/A');
                                        $customerStatus = strtolower((string) $customer->status);
                                        $statusClass = match ($customerStatus) {
                                            'active' => 'gateway-badge--active',
                                            'suspended' => 'gateway-badge--warning',
                                            'email-blacklist', 'phone-blacklist' => 'gateway-badge--danger',
                                            default => 'gateway-badge--inactive',
                                        };
                                        $serialNumber = $customers->firstItem() + $loop->index;
                                    @endphp
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="customer-checkbox form-check-input form-check-input-{{ checkBoxControlSize() }}" value="{{ $customer->id }}">
                                        </td>
                                        <td>{{ $serialNumber }}</td>
                                        <td>
                                            <div class="gateway-name">
                                                {{ trim($customer->firstname . ' ' . $customer->lastname) ?: 'Unnamed customer' }}
                                            </div>
                                            <div class="gateway-helper">{{ $customer->username ?: 'No username set' }}</div>
                                            <div class="gateway-helper">Email: {{ $customer->email }}</div>
                                            <div class="gateway-helper">Phone: {{ $customer->phone }}</div>
                                        </td>
                                        <td>
                                            <span class="gateway-badge {{ $statusClass }}">{{ ucfirst(str_replace('-', ' ', $customerStatus ?: 'inactive')) }}</span>
                                        </td>
                                        <td>{{ $customerLevel }}</td>
                                        <td>
                                            <div class="gateway-helper">Wallet: {!! getSettings()->currency !!}{{ number_format(walletBalance($customer), 2) }}</div>
                                            <div class="gateway-helper">Referral: {!! getSettings()->currency !!}{{ number_format(referralBalance($customer), 2) }}</div>
                                        </td>
                                        <td>{{ optional($customer->created_at)->toDateString() }}</td>
                                        @if(hasAccess('customers.edit'))
                                            <td class="text-end">
                                                <a href="{{ route('customers.edit', $customer->id) }}" class="gateway-action">View profile</a>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ hasAccess('customers.edit') ? 8 : 7 }}">
                                            <div class="alert alert-light border mb-0">No customers found with the current filters.</div>
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

@section('page-script')
    <script>
        $('#select-all').on('change', function () {
            $('.customer-checkbox').prop('checked', this.checked);
        });

        $('#submit-action').on('click', function (e) {
            const selectedCustomerIds = $('.customer-checkbox:checked').map(function () {
                return $(this).val();
            }).get();

            if (!$('#action-select').val()) {
                e.preventDefault();
                alert('Please select a customer level.');
                return;
            }

            if (selectedCustomerIds.length === 0) {
                e.preventDefault();
                alert('Please select at least one customer.');
                return;
            }

            $('<input>').attr({
                type: 'hidden',
                name: 'customer_ids',
                value: selectedCustomerIds
            }).appendTo('#actionForm');
        });
    </script>
@endsection
