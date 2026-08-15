@extends('sneat.layouts.app')

@section('title', 'Customer Profile')

@section('page-css')
    <link href="{{ asset('modern-assets/vendor/libs/select2/select2.css') }}" rel="stylesheet" />
@endsection

@section('content')
    @php
        $customer = $user->customer;
        $currentTab = request('tab', 'account');
        $currency = getSettings()->currency ?? '₦';
        $fullName = trim(($user->firstname ?? '') . ' ' . ($user->middlename ?? '') . ' ' . ($user->lastname ?? '')) ?: 'Customer';
        $initials = strtoupper(substr($user->firstname ?? 'C', 0, 1) . substr($user->lastname ?? 'U', 0, 1));
        $customerLevelName = data_get($customer, 'level.name', 'Level 1');
        $accountStatus = strtolower((string) ($user->status ?? 'active'));
        $kycStatusValue = strtolower((string) getFinalKycStatus($customer->id));
        $kycRecords = [];

        foreach (['FIRST_NAME', 'MIDDLE_NAME', 'LAST_NAME', 'PHONE_NUMBER', 'BVN', 'IDCARDTYPE', 'IDCARD', 'COUNTRY', 'STATE', 'LGA', 'DOB', 'GENDER'] as $kycKey) {
            $kycRecords[$kycKey] = kycStatus($kycKey, $customer->id);
        }

        $kycFields = [
            'FIRST_NAME' => ['label' => 'First name', 'type' => 'text', 'value' => $user->firstname, 'required' => true],
            'MIDDLE_NAME' => ['label' => 'Middle name', 'type' => 'text', 'value' => $user->middlename, 'required' => false],
            'LAST_NAME' => ['label' => 'Last name', 'type' => 'text', 'value' => $user->lastname, 'required' => true],
            'PHONE_NUMBER' => ['label' => 'Phone number', 'type' => 'text', 'value' => $user->phone, 'required' => true],
            'COUNTRY' => ['label' => 'Country', 'type' => 'select', 'value' => 'Nigeria', 'options' => ['Nigeria'], 'required' => true],
            'STATE' => ['label' => 'State', 'type' => 'select', 'value' => $kycRecords['STATE']['value'] ?? null, 'options' => getStates(), 'required' => true],
            'LGA' => ['label' => 'Local Government Area', 'type' => 'select', 'value' => $kycRecords['LGA']['value'] ?? null, 'options' => $lgas, 'required' => true],
            'DOB' => ['label' => 'Date of birth', 'type' => 'date', 'value' => $kycRecords['DOB']['value'] ?? null, 'required' => true],
            'IDCARDTYPE' => ['label' => 'ID card type', 'type' => 'select', 'value' => $kycRecords['IDCARDTYPE']['value'] ?? null, 'options' => ["Driver's Licence", "Voter's Card"], 'required' => true],
            'IDCARD' => ['label' => 'ID card', 'type' => 'file', 'value' => $kycRecords['IDCARD']['value'] ?? null, 'required' => true],
            'BVN' => ['label' => 'BVN', 'type' => 'text', 'value' => $kycRecords['BVN']['value'] ?? null, 'required' => true],
            'GENDER' => ['label' => 'Gender', 'type' => 'select', 'value' => $kycRecords['GENDER']['value'] ?? null, 'options' => ['male', 'female'], 'required' => true],
        ];

        $providerBankMap = [
            'squad' => ['058' => 'Guaranty Trust Bank'],
            'monnify' => [
                '50515' => 'Moniepoint',
                '035' => 'Wema Bank',
            ],
            'paymentpoint' => ['20946' => 'Palmpay'],
        ];

        $providerSlugMap = $providers->pluck('slug', 'id');
    @endphp

    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="profile-hero mb-4">
                <div class="profile-hero__meta">
                    <div class="profile-avatar">{{ $initials }}</div>
                    <div class="profile-meta">
                        <span class="gateway-hero__kicker">Customer profile</span>
                        <strong>{{ $fullName }}</strong>
                        <span>{{ $user->email }} · {{ $user->phone }}</span>
                    </div>
                </div>
                <div class="gateway-summary">
                    <div class="gateway-summary__card">
                        <span class="gateway-summary__label">Status</span>
                        <span class="gateway-summary__value">{{ ucfirst($accountStatus) }}</span>
                    </div>
                    <div class="gateway-summary__card">
                        <span class="gateway-summary__label">Customer level</span>
                        <span class="gateway-summary__value">{{ $customerLevelName }}</span>
                    </div>
                    <div class="gateway-summary__card">
                        <span class="gateway-summary__label">KYC</span>
                        <span class="gateway-summary__value">{{ ucfirst($kycStatusValue) }}</span>
                    </div>
                </div>
            </div>

            @include('sneat.layouts.alerts')

            <div class="row g-3 mb-4">
                <div class="col-md-6 col-xl-3">
                    <div class="admin-stat-card admin-stat-card--emerald">
                        <div class="admin-stat-card__icon">
                            <i class="bx bx-wallet"></i>
                        </div>
                        <div class="admin-stat-card__label">Wallet balance</div>
                        <div class="admin-stat-card__value">{{ $balances['Wallet Balance'] ?? ($currency . '0.00') }}</div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="admin-stat-card admin-stat-card--blue">
                        <div class="admin-stat-card__icon">
                            <i class="bx bx-gift"></i>
                        </div>
                        <div class="admin-stat-card__label">Referral earning</div>
                        <div class="admin-stat-card__value">{{ $balances['Referral Earning'] ?? ($currency . '0.00') }}</div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="admin-stat-card admin-stat-card--indigo">
                        <div class="admin-stat-card__icon">
                            <i class="bx bx-receipt"></i>
                        </div>
                        <div class="admin-stat-card__label">Transactions total</div>
                        <div class="admin-stat-card__value">{{ $balances['Transaction Total'] ?? ($currency . '0.00') }}</div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="admin-stat-card admin-stat-card--amber">
                        <div class="admin-stat-card__icon">
                            <i class="bx bx-building-house"></i>
                        </div>
                        <div class="admin-stat-card__label">Reserved accounts</div>
                        <div class="admin-stat-card__value">{{ number_format($accounts->total()) }}</div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-xl-12">
                    <div class="profile-card card">
                        <div class="card-header">
                            <h3>Customer workspace</h3>
                            <p>Manage the account, KYC, reserved accounts, and recovery actions from one place.</p>
                        </div>
                        <div class="card-body">
                            <ul class="nav customer-tabs mb-4" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link {{ $currentTab === 'account' ? 'active' : '' }}" href="{{ route('customers.edit', ['id' => $user->id, 'tab' => 'account']) }}" role="tab">Account</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link {{ $currentTab === 'transactions' ? 'active' : '' }}" href="{{ route('customers.edit', ['id' => $user->id, 'tab' => 'transactions']) }}" role="tab">Transactions</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link {{ $currentTab === 'downlines' ? 'active' : '' }}" href="{{ route('customers.edit', ['id' => $user->id, 'tab' => 'downlines']) }}" role="tab">Downlines</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link {{ $currentTab === 'kyc' ? 'active' : '' }}" href="{{ route('customers.edit', ['id' => $user->id, 'tab' => 'kyc']) }}" role="tab">KYC Data</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link {{ $currentTab === 'reserved' ? 'active' : '' }}" href="{{ route('customers.edit', ['id' => $user->id, 'tab' => 'reserved']) }}" role="tab">Reserved Accounts</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link {{ $currentTab === 'actions' ? 'active' : '' }}" href="{{ route('customers.edit', ['id' => $user->id, 'tab' => 'actions']) }}" role="tab">Actions</a>
                                </li>
                            </ul>

                            <div class="tab-content">
                                <div class="tab-pane fade show {{ $currentTab === 'account' ? 'active' : '' }} customer-tab-pane" id="customer-account" role="tabpanel">
                                    <div class="row g-3 mb-4">
                                        @foreach($balances as $label => $balance)
                                            <div class="col-md-6 col-xl-3">
                                                <div class="admin-summary-card h-100">
                                                    <div class="admin-summary-card__icon admin-summary-card__icon--blue">
                                                        <i class="bx bx-line-chart"></i>
                                                    </div>
                                                    <div class="admin-summary-card__value">{{ $balance }}</div>
                                                    <div class="admin-summary-card__label">{{ $label }}</div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <form action="{{ route('customers.update', $user->id) }}" method="POST" autocomplete="off">
                                        @csrf
                                        <input type="hidden" name="tab" value="account">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="profile-label" for="firstname">First name</label>
                                                <input type="text" class="form-control form-control-{{ formControlSize() }}" id="firstname" name="firstname" value="{{ old('firstname', $user->firstname) }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="profile-label" for="lastname">Last name</label>
                                                <input type="text" class="form-control form-control-{{ formControlSize() }}" id="lastname" name="lastname" value="{{ old('lastname', $user->lastname) }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="profile-label" for="phone">Phone</label>
                                                <input type="text" class="form-control form-control-{{ formControlSize() }}" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="profile-label" for="status">Status</label>
                                                <select name="status" class="form-select form-select-{{ formControlSize() }}" id="status" required>
                                                    <option value="">Select status</option>
                                                    <option value="active" @selected(old('status', $user->status) === 'active')>Active</option>
                                                    <option value="suspended" @selected(old('status', $user->status) === 'suspended')>Suspended</option>
                                                    <option value="delete" @selected(old('status', $user->status) === 'delete')>Delete</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="profile-label" for="kyc_status">KYC status</label>
                                                <select name="kyc_status" class="form-select form-select-{{ formControlSize() }}" id="kyc_status" required>
                                                    <option value="verified" @selected(old('kyc_status', getFinalKycStatus($customer->id)) === 'verified')>Verified</option>
                                                    <option value="unverified" @selected(old('kyc_status', getFinalKycStatus($customer->id)) === 'unverified')>Unverified</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="profile-label" for="customerlevel">Customer level</label>
                                                <select name="customerlevel" class="form-select form-select-{{ formControlSize() }}" id="customerlevel">
                                                    <option value="">Select customer level</option>
                                                    @foreach ($customerLevels as $level)
                                                        <option value="{{ $level->id }}" @selected(old('customerlevel', $user->customer->customer_level) == $level->id)>{{ $level->name }}{{ $level->make_api_level == 'yes' ? ' (API ACCESS)' : '' }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="profile-label" for="email">Email</label>
                                                <input type="email" class="form-control form-control-{{ formControlSize() }}" id="email" value="{{ $user->email }}" disabled>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="profile-label" for="username">Username</label>
                                                <input type="text" class="form-control form-control-{{ formControlSize() }}" id="username" value="{{ $user->username }}" disabled>
                                            </div>
                                            <div class="col-12 d-flex justify-content-start">
                                                <button type="submit" class="btn btn-admin-submit">Update customer</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <div class="tab-pane fade {{ $currentTab === 'transactions' ? 'show active' : '' }} customer-tab-pane" id="customer-transactions" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table gateway-table align-middle">
                                            <thead>
                                                <tr>
                                                    <th>Product</th>
                                                    <th>Amount</th>
                                                    <th>Amount paid</th>
                                                    <th>Biller</th>
                                                    <th>Status</th>
                                                    <th>Transaction ID</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($transactions as $transaction)
                                                    <tr>
                                                        <td>
                                                            <div class="gateway-name">{{ $transaction->product_name }}</div>
                                                            <div class="gateway-helper">{{ optional($transaction->created_at)->toDateString() }}</div>
                                                        </td>
                                                        <td>{!! $currency . number_format($transaction->amount, 2) !!}</td>
                                                        <td>{!! $currency . number_format($transaction->total_amount, 2) !!}</td>
                                                        <td>{{ $transaction->unique_element ?? 'N/A' }}</td>
                                                        <td><span class="gateway-badge {{ $transaction->status === 'success' ? 'gateway-badge--active' : 'gateway-badge--warning' }}">{{ ucfirst($transaction->status) }}</span></td>
                                                        <td>
                                                            <a class="gateway-action" href="{{ route('admin.single.transaction.view', $transaction->id) }}">{{ $transaction->transaction_id }}</a>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="6">
                                                            <div class="alert alert-light border mb-0">No transactions found for this customer.</div>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="d-flex justify-content-end mt-4">
                                        {{ $transactions->links('pagination::bootstrap-5') }}
                                    </div>
                                </div>

                                <div class="tab-pane fade {{ $currentTab === 'downlines' ? 'show active' : '' }} customer-tab-pane" id="customer-downlines" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table gateway-table align-middle">
                                            <thead>
                                                <tr>
                                                    <th>S/N</th>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th>Phone</th>
                                                    <th>Total earned</th>
                                                    <th>Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($downlines as $ref)
                                                    <tr>
                                                        <td>{{ $downlines->firstItem() + $loop->index }}</td>
                                                        <td>{{ ucfirst(data_get($ref, 'referredCustomer.user.firstname', '')) . ' ' . ucfirst(data_get($ref, 'referredCustomer.user.lastname', '')) }}</td>
                                                        <td>{{ data_get($ref, 'referredCustomer.user.email', 'N/A') }}</td>
                                                        <td>{{ data_get($ref, 'referredCustomer.user.phone', 'N/A') }}</td>
                                                        <td>{!! $currency . number_format($ref->total, 2) !!}</td>
                                                        <td>{{ optional($ref->created_at)->toDateTimeString() }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="6">
                                                            <div class="alert alert-light border mb-0">No downlines available.</div>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="d-flex justify-content-end mt-4">
                                        {{ $downlines->links('pagination::bootstrap-5') }}
                                    </div>
                                </div>

                                <div class="tab-pane fade {{ $currentTab === 'kyc' ? 'show active' : '' }} customer-tab-pane" id="customer-kyc" role="tabpanel">
                                    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
                                        <div>
                                            <div class="gateway-helper text-uppercase fw-semibold">General KYC status</div>
                                            <h4 class="mb-1">{{ ucfirst($kycStatusValue) }}</h4>
                                            <p class="mb-0">Review the captured identity fields before approving or declining the record.</p>
                                        </div>
                                        <div class="d-flex flex-wrap gap-2">
                                            <a onclick="return confirm('You are about to approve KYC details');" href="{{ route('admin.customer.approve.kyc', ['customer' => $customer->id, 'tab' => 'kyc']) }}" class="gateway-action">Approve and create reserved accounts</a>
                                            <a onclick="return confirm('You are about to decline KYC details');" href="{{ route('admin.customer.decline.kyc', ['customer' => $customer->id, 'tab' => 'kyc']) }}" class="gateway-action gateway-action--danger">Decline</a>
                                        </div>
                                    </div>

                                    <form action="{{ route('admin.customer.update.kyc', $customer->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="tab" value="kyc">
                                        <div class="row g-3">
                                            @foreach($kycFields as $key => $field)
                                                @php
                                                    $record = $kycRecords[$key] ?? ['status' => 'unverified', 'value' => null];
                                                    $isVerified = ($record['status'] ?? 'unverified') === 'verified';
                                                @endphp

                                                <div class="col-md-6">
                                                    <label class="profile-label" for="{{ $key }}">{{ $field['label'] }}</label>
                                                    <span class="gateway-badge {{ $isVerified ? 'gateway-badge--active' : 'gateway-badge--warning' }} ms-2">{{ $isVerified ? 'Verified' : 'Unverified' }}</span>

                                                    @if($field['type'] === 'file')
                                                        @if($isVerified && !empty($record['value']))
                                                            <div class="mt-2">
                                                                <img src="{{ asset($record['value']) }}" alt="{{ $field['label'] }}" class="customer-id-preview img-fluid border">
                                                            </div>
                                                        @else
                                                            <input type="file" class="form-control form-control-{{ formControlSize() }} mt-2" id="{{ $key }}" name="{{ $key }}" {{ ($field['required'] ?? false) ? 'required' : '' }}>
                                                        @endif
                                                    @elseif($field['type'] === 'select')
                                                        @if($isVerified)
                                                            <input type="text" class="form-control form-control-{{ formControlSize() }} mt-2" value="{{ $record['value'] ?? '' }}" readonly>
                                                        @else
                                                            <select name="{{ $key }}" id="{{ $key }}" class="form-select form-select-{{ formControlSize() }} mt-2" {{ ($field['required'] ?? false) ? 'required' : '' }}>
                                                                <option value="">Select...</option>
                                                                @foreach($field['options'] as $option)
                                                                    <option value="{{ $option }}" @selected(($record['value'] ?? $field['value'] ?? null) == $option)>{{ $option }}</option>
                                                                @endforeach
                                                            </select>
                                                        @endif
                                                    @else
                                                        @if($isVerified)
                                                            <input type="text" class="form-control form-control-{{ formControlSize() }} mt-2" value="{{ $record['value'] ?? $field['value'] }}" readonly>
                                                        @else
                                                            <input
                                                                type="{{ $field['type'] }}"
                                                                name="{{ $key }}"
                                                                class="form-control form-control-{{ formControlSize() }} mt-2"
                                                                value="{{ old($key, $record['value'] ?? $field['value']) }}"
                                                                {{ ($field['required'] ?? false) ? 'required' : '' }}
                                                            >
                                                        @endif
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>

                                        <div class="d-flex justify-content-start mt-4">
                                            <button type="submit" class="btn btn-admin-submit">Update KYC data</button>
                                        </div>
                                    </form>
                                </div>

                                <div class="tab-pane fade {{ $currentTab === 'reserved' ? 'show active' : '' }} customer-tab-pane" id="customer-reserved" role="tabpanel">
                                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
                                        <div>
                                            <div class="gateway-helper text-uppercase fw-semibold">Reserved accounts</div>
                                            <h4 class="mb-1">{{ number_format($accounts->total()) }} account{{ $accounts->total() === 1 ? '' : 's' }}</h4>
                                            <p class="mb-0">Create or review reserved account numbers linked to this customer.</p>
                                        </div>
                                        <button type="button" class="btn btn-admin-submit" data-bs-toggle="modal" data-bs-target="#reservedAccountModal">Create reserved account</button>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table gateway-table align-middle">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Bank</th>
                                                    <th>Account</th>
                                                    <th>Transactions</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($accounts as $account)
                                                    <tr>
                                                        <td>{{ ucfirst($account->account_name) }}</td>
                                                        <td>
                                                            {{ ucfirst($account->bank_name) }}
                                                            <div class="gateway-helper">{{ $account->gateway->name ?? 'Gateway' }}</div>
                                                        </td>
                                                        <td>
                                                            {{ ucfirst($account->account_number) }}
                                                            <div class="gateway-helper">Created {{ optional($account->created_at)->toDateTimeString() }}</div>
                                                        </td>
                                                        <td>
                                                            <a class="gateway-action" href="{{ route('account.transactions', $account->id) }}">
                                                                {!! $currency !!}{{ number_format($account->transactions->sum('total_amount'), 2) }} ({{ number_format($account->transactions->count()) }})
                                                            </a>
                                                        </td>
                                                        <td>
                                                            @if($account->transactions->count() < 1 && ($account->gateway->slug ?? '') === 'monnify')
                                                                <a onclick="return confirm('You are about to delete a reserved account!')" class="gateway-action gateway-action--danger" href="{{ route('reserved_account.delete', ['account' => $account->id, 'tab' => 'reserved']) }}">Delete</a>
                                                            @else
                                                                <span class="gateway-badge gateway-badge--inactive">Locked</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5">
                                                            <div class="alert alert-light border mb-0">No reserved accounts have been created for this customer.</div>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="d-flex justify-content-end mt-4">
                                        {{ $accounts->links('pagination::bootstrap-5') }}
                                    </div>
                                </div>

                                <div class="tab-pane fade {{ $currentTab === 'actions' ? 'show active' : '' }} customer-tab-pane" id="customer-actions" role="tabpanel">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="profile-side-card h-100">
                                                <div class="profile-badge mb-3">Blacklist email</div>
                                                <div class="gateway-helper mb-3">{{ $user->email }}</div>
                                                @if($blacklistedEmail)
                                                    <div class="form-check form-switch mb-0">
                                                        <input
                                                            class="form-check-input"
                                                            type="checkbox"
                                                            role="switch"
                                                            id="blacklist-email-toggle"
                                                            @checked($blacklistedEmail)
                                                            onchange="toggleBlacklistStatus(this)"
                                                            data-id="{{ $blacklistEmailId }}"
                                                            data-value="{{ $blacklistEmailStatus }}"
                                                        >
                                                        <label class="form-check-label" for="blacklist-email-toggle">Active blacklist entry</label>
                                                    </div>
                                                @else
                                                    <form action="{{ route('customer-blacklist.store') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="type" value="email">
                                                        <input type="hidden" name="value" value="{{ $user->email }}">
                                                        <input type="hidden" name="status" value="active">
                                                        <button class="btn btn-outline-danger" type="submit">Add email to blacklist</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="profile-side-card h-100">
                                                <div class="profile-badge mb-3">Blacklist phone</div>
                                                <div class="gateway-helper mb-3">{{ $user->phone }}</div>
                                                @if($blacklistedPhone)
                                                    <div class="form-check form-switch mb-0">
                                                        <input
                                                            class="form-check-input"
                                                            type="checkbox"
                                                            role="switch"
                                                            id="blacklist-phone-toggle"
                                                            @checked($blacklistedPhone)
                                                            onchange="toggleBlacklistStatus(this)"
                                                            data-id="{{ $blacklistPhoneId }}"
                                                            data-value="{{ $blacklistPhoneStatus }}"
                                                        >
                                                        <label class="form-check-label" for="blacklist-phone-toggle">Active blacklist entry</label>
                                                    </div>
                                                @else
                                                    <form action="{{ route('customer-blacklist.store') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="type" value="phone">
                                                        <input type="hidden" name="value" value="{{ $user->phone }}">
                                                        <input type="hidden" name="status" value="active">
                                                        <button class="btn btn-outline-danger" type="submit">Add phone to blacklist</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>

                                        @if(hasAccess('admin.password.reset'))
                                            <div class="col-md-6">
                                                <div class="profile-side-card h-100">
                                                    <div class="profile-badge mb-3">Password reset</div>
                                                    <p class="profile-note mb-3">Generate a fresh password for this customer account.</p>
                                                    <button type="button" class="btn btn-admin-submit" data-bs-toggle="modal" data-bs-target="#resetPasswordModal">Open reset form</button>
                                                </div>
                                            </div>
                                        @endif

                                        @if(hasAccess('admin.transaction.pin.reset'))
                                            <div class="col-md-6">
                                                <div class="profile-side-card h-100">
                                                    <div class="profile-badge mb-3">Transaction PIN</div>
                                                    <p class="profile-note mb-3">Reset the transaction PIN without leaving the profile page.</p>
                                                    <button type="button" class="btn btn-admin-submit" data-bs-toggle="modal" data-bs-target="#resetPinModal">Open reset form</button>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="reservedAccountModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create reserved account for {{ $user->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('create.reserved.account', $customer->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="tab" value="reserved">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="profile-label" for="provider">Provider</label>
                                <select class="form-select form-select-{{ formControlSize() }}" name="provider" id="provider" required>
                                    <option value="">Select provider</option>
                                    @foreach ($providers as $provider)
                                        <option value="{{ $provider->id }}">{{ $provider->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="profile-label" for="bank">Banks</label>
                                <select class="form-select form-select-{{ formControlSize() }}" name="bank[]" id="bank" required multiple></select>
                            </div>
                        </div>

                        <input type="hidden" name="bvn" value="{{ $kycRecords['BVN']['value'] ?? '' }}">
                        <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-admin-submit">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if(hasAccess('admin.transaction.pin.reset'))
        <div class="modal fade" id="resetPinModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Reset transaction PIN</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.transaction.pin.reset', $user->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="tab" value="actions">
                        <div class="modal-body">
                            <label class="profile-label" for="new_transaction_pin">New transaction PIN</label>
                            <input type="text" class="form-control form-control-{{ formControlSize() }}" id="new_transaction_pin" name="new_transaction_pin" value="{{ old('new_transaction_pin') }}">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-admin-submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @if(hasAccess('admin.password.reset'))
        <div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Reset password</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.password.reset', $user->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="tab" value="actions">
                        <div class="modal-body">
                            <label class="profile-label" for="new_password">New password</label>
                            <input type="text" class="form-control form-control-{{ formControlSize() }}" id="new_password" name="new_password" value="{{ old('new_password') }}">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-admin-submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('page-script')
    <script src="{{ asset('modern-assets/vendor/libs/select2/select2.js') }}"></script>
    <script>
        const providerBankMap = @json($providerBankMap);
        const providerSlugMap = @json($providerSlugMap);
        const currentLga = @json($kycRecords['LGA']['value'] ?? null);

        function initReservedAccountSelect2() {
            const $provider = $('#provider');
            const $bank = $('#bank');

            if ($provider.length && !$provider.data('select2')) {
                $provider.wrap('<div class="position-relative"></div>').select2({
                    placeholder: 'Select provider',
                    dropdownParent: $provider.parent(),
                    width: '100%'
                });
            }

            if ($bank.length && !$bank.data('select2')) {
                $bank.wrap('<div class="position-relative"></div>').select2({
                    placeholder: 'Select banks',
                    dropdownParent: $bank.parent(),
                    width: '100%'
                });
            }
        }

        $('#STATE').on('change', function () {
            const state = $(this).val();
            const $lga = $('#LGA');

            if (!$lga.length || !state) {
                return;
            }

            $lga.empty().append('<option value="">Select...</option>');

            $.ajax({
                type: 'GET',
                url: "{{ url('/') }}/get-lga-by-statename/" + state + "/" + currentLga,
                success: function (data) {
                    $lga.append(data);
                }
            });
        });

        $('#provider').on('change', function () {
            const providerId = $(this).val();
            const slug = providerSlugMap[providerId];
            const banks = providerBankMap[slug] || {};
            const $bankSelect = $('#bank');

            $bankSelect.empty();

            if (Object.keys(banks).length === 0) {
                $bankSelect.append('<option value="">No banks available</option>');
                return;
            }

            $.each(banks, function (code, name) {
                $bankSelect.append(`<option value="${code}">${name}</option>`);
            });

            if ($bankSelect.data('select2')) {
                $bankSelect.trigger('change.select2');
            }
        });

        $('#reservedAccountModal').on('shown.bs.modal', function () {
            initReservedAccountSelect2();
        });

        initReservedAccountSelect2();

        function toggleBlacklistStatus(input) {
            const $input = $(input);

            if (!confirm('Are you sure you want to perform this action?')) {
                $input.prop('checked', !$input.prop('checked'));
                return;
            }

            $.ajax({
                url: '{{ route('black.list.status') }}',
                data: {
                    status: $input.attr('data-value'),
                    id: $input.attr('data-id')
                },
                success: function (response) {
                    if (response.code === 1) {
                        $input.attr('data-value', response.status);
                        $input.prop('checked', response.status === 'active');
                    } else {
                        alert(response.message || 'Request could not be completed!');
                        $input.prop('checked', !$input.prop('checked'));
                    }
                },
                error: function () {
                    alert('Request could not be completed!');
                    $input.prop('checked', !$input.prop('checked'));
                }
            });
        }
    </script>
@endsection
