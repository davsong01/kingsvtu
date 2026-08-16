@extends('sneat.layouts.app')

@section('title', 'Withdraw Earnings')

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="profile-hero mb-4 gateway-hero">
                <div class="profile-hero__meta">
                    <div class="profile-avatar"><i class="bx bx-wallet"></i></div>
                    <div class="profile-meta">
                        <span class="gateway-hero__kicker">Referral withdrawal</span>
                        <strong>Withdraw commission</strong>
                        <span>Move your referral earnings into your wallet balance.</span>
                    </div>
                </div>
                <div class="gateway-summary">
                    <div class="gateway-summary__card">
                        <span class="gateway-summary__label">Referral balance</span>
                        <span class="gateway-summary__value">{{ getSettings()->currency . number_format(referralBalance(auth()->user()), 2) }}</span>
                    </div>
                    <div class="gateway-summary__card">
                        <span class="gateway-summary__label">Wallet balance</span>
                        <span class="gateway-summary__value">{{ getSettings()->currency . number_format(walletBalance(auth()->user()), 2) }}</span>
                    </div>
                </div>
            </div>

            @include('sneat.layouts.alerts')

            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="card profile-card">
                        <div class="card-header">
                            <h3>Withdraw earnings</h3>
                            <p>Enter the amount you want to transfer from referral earnings to your wallet.</p>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('process.withdrawal') }}" method="POST">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-md-8">
                                        <label class="profile-label" for="amount">Amount</label>
                                        <input type="number" class="form-control form-control-{{ formControlSize() }}" name="amount" value="{{ old('value', referralBalance(auth()->user())) }}" placeholder="Amount to withdraw" id="amount" min="0" max="{{ referralBalance(auth()->user()) }}" required>
                                    </div>
                                </div>

                                <div class="profile-footer mt-4">
                                    <button class="btn btn-admin-submit" type="submit">Submit withdrawal</button>
                                    <a href="{{ route('downlines') }}" class="btn btn-label-secondary">Back to downlines</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
