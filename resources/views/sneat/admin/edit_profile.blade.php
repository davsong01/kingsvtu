@extends('sneat.layouts.app')

@section('title', 'Edit Profile')

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            @php
                $user = auth()->user();
                $initials = strtoupper(substr($user->firstname ?? 'A', 0, 1) . substr($user->lastname ?? '', 0, 1));
            @endphp

            <div class="profile-hero mb-4 gateway-hero">
                <div class="profile-hero__meta">
                    <div class="profile-avatar">{{ $initials ?: 'A' }}</div>
                    <div class="profile-meta">
                        <span class="gateway-hero__kicker">Account settings</span>
                        <strong>{{ $user->firstname }} {{ $user->lastname }}</strong>
                        <span>{{ $user->email }}</span>
                    </div>
                </div>
                <div class="gateway-summary">
                    <div class="gateway-summary__card">
                        <span class="gateway-summary__label">Role</span>
                        <span class="gateway-summary__value">{{ ucfirst($user->type) }}</span>
                    </div>
                    <div class="gateway-summary__card">
                        <span class="gateway-summary__label">Status</span>
                        <span class="gateway-summary__value">{{ ucfirst($user->status ?? 'active') }}</span>
                    </div>
                </div>
            </div>

            @include('sneat.layouts.alerts')

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="profile-card card">
                        <div class="card-header">
                            <h3>Profile information</h3>
                            <p>Update your display name and contact details.</p>
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
                    <div class="profile-side-card mb-4">
                        <div class="profile-side-row">
                            <span>Account type</span>
                            <strong>{{ ucfirst($user->type) }}</strong>
                        </div>
                        <div class="profile-side-row">
                            <span>Firstname</span>
                            <strong>{{ $user->firstname ?: 'Not set' }}</strong>
                        </div>
                        <div class="profile-side-row">
                            <span>Lastname</span>
                            <strong>{{ $user->lastname ?: 'Not set' }}</strong>
                        </div>
                        <div class="profile-side-row">
                            <span>Phone</span>
                            <strong>{{ $user->phone ?: 'Not set' }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
