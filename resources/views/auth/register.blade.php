@extends('layouts.auth')

@section('auth-title', 'Create account')
@section('auth-subtitle', 'Register your account and continue to KYC after logging in.')

@section('body')
    <div class="auth-form">
        @include('layouts.alerts')

        <form action="{{ route('register') }}" method="POST" autocomplete="on">
            @csrf
            <div class="row g-3">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="first_name">First name</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" value="{{ old('first_name') }}" placeholder="First name" required>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="last_name">Last name</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" value="{{ old('last_name') }}" placeholder="Last name" required>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="email">Email address</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="Email address" required>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="phone">Phone number</label>
                        <input type="tel" class="form-control" id="phone" name="phone" value="{{ old('phone') }}" placeholder="Phone number" required>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" id="username" name="username" value="{{ old('username') }}" placeholder="Username" required>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="referral">Referral username</label>
                        <input type="text" class="form-control" id="referral" name="referral" value="{{ old('referral', request()->referral) }}" placeholder="Referral username">
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-group auth-password-field">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                            <button type="button" class="btn auth-password-toggle" data-password-toggle="password">Show</button>
                        </div>
                    </div>
                </div>
            </div>

            <x-captcha group-class="mt-3" />

            <div class="mt-1 p-3 rounded-4 border bg-body-tertiary d-flex align-items-start gap-3">
                <input type="checkbox" class="form-check-input mt-1" id="privacy" name="privacy" required>
                <label class="form-check-label mb-0" for="privacy">
                    <span class="d-block fw-semibold text-body">I agree to the privacy policy</span>
                    <small class="text-secondary">I understand how my information is used and stored on KingsVTU.</small>
                </label>
            </div>

            <button type="submit" class="btn btn-primary w-100 mt-3">Register</button>
        </form>

        <div class="auth-footer-note">
            Already have an account? <a href="{{ route('login') }}" class="auth-link">Login</a>
        </div>
    </div>
@endsection
