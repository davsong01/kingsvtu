@extends('layouts.auth')

@section('auth-title', 'Welcome back')
@section('auth-subtitle', 'Sign in to continue managing your KingsVTU account.')

@section('body')
    <div class="auth-form">
        @include('layouts.alerts')

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="email">Email address</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="Enter your email address" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-group auth-password-field">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                    <button type="button" class="btn auth-password-toggle" data-password-toggle="password">Show</button>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="remember">
                    <label class="form-check-label" for="remember">Keep me logged in</label>
                </div>
                <a href="{{ route('password.request') }}" class="auth-link">Forgot password?</a>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>

        <div class="auth-footer-note">
            Don’t have an account? <a href="{{ route('register') }}" class="auth-link">Create one</a>
        </div>
    </div>
@endsection
