@extends('sneat.layouts.app')

@section('title', 'Profile')

@section('content')
    @php
        $user = auth()->user();
        $settings = getSettings();
        $currency = $settings->currency ?? '₦';
        $initials = strtoupper(substr($user->firstname ?? 'C', 0, 1) . substr($user->lastname ?? '', 0, 1));
        $walletBalance = $currency . number_format(walletBalance($user), 2);
        $referralBalance = $currency . number_format(referralBalance($user), 2);
        $kycStatus = getFinalKycStatus($user->customer->id) ?? 'pending';
        $kycLabel = formatKycStatusLabel($kycStatus);
        $kycBadge = in_array($kycStatus, ['verified', 'approved'], true) ? 'success' : ($kycStatus === 'declined' ? 'danger' : 'warning');
        $customerLevel = $user->customer?->level?->name ?? 'Not assigned';
    @endphp

    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="profile-hero mb-4 gateway-hero">
                <div class="profile-hero__meta">
                    <div class="profile-avatar">{{ $initials ?: 'C' }}</div>
                    <div class="profile-meta">
                        <span class="gateway-hero__kicker">Account settings</span>
                        <strong>{{ $user->firstname ?: $user->username }}</strong>
                        <span>{{ $user->email }}</span>
                    </div>
                </div>
                <div class="gateway-summary">
                    <div class="gateway-summary__card">
                        <span class="gateway-summary__label">Wallet balance</span>
                        <span class="gateway-summary__value">{{ $walletBalance }}</span>
                    </div>
                    <div class="gateway-summary__card">
                        <span class="gateway-summary__label">Referral earning</span>
                        <span class="gateway-summary__value">{{ $referralBalance }}</span>
                    </div>
                    <div class="gateway-summary__card">
                        <span class="gateway-summary__label">KYC status</span>
                        <span class="gateway-summary__value d-flex align-items-center justify-content-end gap-2">
                            <span>{{ $kycLabel }}</span>
                            @if($kycStatus === 'pending')
                                <a href="{{ route('update.kyc.details') }}" class="badge bg-danger text-white text-decoration-none">Fix</a>
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            @include('sneat.layouts.alerts')

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="profile-card card">
                        <div class="card-header">
                            <h3>Profile information</h3>
                            <p>Update your name, phone number, password, and transaction PIN.</p>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('profile.update') }}" method="POST" autocomplete="off">
                                @csrf
                                @method('PATCH')

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="profile-label" for="firstname">First Name</label>
                                        <input autocomplete="off" type="text" class="form-control form-control-{{ formControlSize() }}" id="firstname" name="firstname" value="{{ old('firstname', $user->firstname) }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="profile-label" for="middlename">Middle Name</label>
                                        <input autocomplete="off" type="text" class="form-control form-control-{{ formControlSize() }}" id="middlename" name="middlename" value="{{ old('middlename', $user->middlename) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="profile-label" for="lastname">Last Name</label>
                                        <input autocomplete="off" type="text" class="form-control form-control-{{ formControlSize() }}" id="lastname" name="lastname" value="{{ old('lastname', $user->lastname) }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="profile-label" for="phone">Phone Number</label>
                                        <input autocomplete="off" type="text" class="form-control form-control-{{ formControlSize() }}" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="profile-label" for="email">Email Address</label>
                                        <input type="email" class="form-control form-control-{{ formControlSize() }}" id="email" value="{{ $user->email }}" disabled>
                                    </div>

                                    @if($user->type === 'customer')
                                        <div class="col-12">
                                            <hr class="my-2">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="profile-label d-flex align-items-center justify-content-between gap-2" for="new_transaction_pin">
                                                <span>New Transaction PIN</span>
                                                <a href="{{ route('customer.reset.pin') }}" class="small text-primary text-decoration-none">Reset PIN</a>
                                            </label>
                                            <input autocomplete="new-password" type="password" class="form-control form-control-{{ formControlSize() }}" id="new_transaction_pin" name="new_transaction_pin" placeholder="Enter a new transaction PIN">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="profile-label" for="customer_level">Customer Level</label>
                                            <input type="text" class="form-control form-control-{{ formControlSize() }}" id="customer_level" value="{{ $customerLevel }}" disabled>
                                            <small class="text-muted d-inline-block mt-2">Need to move up? <a href="{{ route('customer.level.upgrade') }}" class="text-decoration-none">Upgrade account</a></small>
                                        </div>
                                    @endif

                                    <div class="col-12">
                                        <hr class="my-2">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="profile-label" for="current_password">Current Password</label>
                                        <input autocomplete="current-password" type="password" class="form-control form-control-{{ formControlSize() }}" id="current_password" name="current_password" placeholder="Enter current password">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="profile-label" for="new_password">New Password</label>
                                        <input autocomplete="new-password" type="password" class="form-control form-control-{{ formControlSize() }}" id="new_password" name="new_password" placeholder="Enter new password">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="profile-label" for="new_password_confirmation">Confirm New Password</label>
                                        <input autocomplete="new-password" type="password" class="form-control form-control-{{ formControlSize() }}" id="new_password_confirmation" name="new_password_confirmation" placeholder="Confirm new password">
                                    </div>
                                </div>

                                <div class="profile-footer">
                                    <button class="btn btn-admin-submit" type="submit">Update Profile</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="profile-side-card mb-4 h-100">
                        <div class="profile-side-row">
                            <span>Account type</span>
                            <strong>{{ ucfirst($user->type) }}</strong>
                        </div>
                        <div class="profile-side-row">
                            <span>Username</span>
                            <strong>{{ $user->username }}</strong>
                        </div>
                        <div class="profile-side-row">
                            <span>Phone</span>
                            <strong>{{ $user->phone ?: 'Not set' }}</strong>
                        </div>
                        <div class="profile-side-row">
                            <span>KYC</span>
                            <strong><span class="badge bg-label-{{ $kycBadge }}">{{ $kycLabel }}</span></strong>
                        </div>
                        <div class="profile-side-row">
                            <span>Wallet balance</span>
                            <strong>{{ $walletBalance }}</strong>
                        </div>
                        <div class="profile-side-row">
                            <span>Referral earning</span>
                            <strong>{{ $referralBalance }}</strong>
                        </div>
                        <div class="profile-side-row">
                            <span>Customer level</span>
                            <strong>{{ $customerLevel }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
