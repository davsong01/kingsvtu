@extends('sneat.layouts.app')

@section('title', 'Referrals')

@php
    $referralItems = collect($refs ?? []);
    $currency = getSettings()->currency ?? '₦';
@endphp

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="profile-hero mb-4 gateway-hero">
                <div class="profile-hero__meta">
                    <div class="profile-avatar"><i class="bx bx-share-alt"></i></div>
                    <div class="profile-meta">
                        <span class="gateway-hero__kicker">Referral network</span>
                        <strong>All referrals</strong>
                        <span>People who registered with your referral code.</span>
                    </div>
                </div>
                <div class="gateway-summary">
                    <div class="gateway-summary__card">
                        <span class="gateway-summary__label">Total referrals</span>
                        <span class="gateway-summary__value">{{ $referralItems->count() }}</span>
                    </div>
                    <div class="gateway-summary__card">
                        <span class="gateway-summary__label">Referral balance</span>
                        <span class="gateway-summary__value">{{ $currency . number_format(referralBalance(auth()->user()), 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="card modern-admin-card">
                <div class="card-header">
                    <h3 class="mb-1">Referred customers</h3>
                    <p class="mb-0">A list of the people you brought in.</p>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Username</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Joined</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($referralItems as $ref)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $ref->username ?? data_get($ref, 'user.username', 'N/A') }}</td>
                                        <td>{{ trim(($ref->firstname ?? data_get($ref, 'user.firstname', '')).' '.($ref->lastname ?? data_get($ref, 'user.lastname', ''))) ?: 'N/A' }}</td>
                                        <td>{{ $ref->email ?? data_get($ref, 'user.email', 'N/A') }}</td>
                                        <td>{{ optional($ref->created_at)->format('M d, Y') ?? 'N/A' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">
                                            <div class="alert alert-light border mb-0">No referrals yet.</div>
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
