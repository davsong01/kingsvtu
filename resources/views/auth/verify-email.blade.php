@extends('layouts.auth')

@section('auth-title', 'Verify your email')
@section('auth-subtitle', 'Check your inbox for the verification link we sent.')

@section('body')
    <div class="auth-form">
        @include('layouts.alerts')

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <p class="auth-copy-muted">
                Thanks for signing up. Before getting started, please verify your email address by clicking the link we just sent.
            </p>
            <button class="btn btn-primary w-100" type="submit">Resend verification email</button>
        </form>

        <div class="auth-footer-note">
            Need a different account? <a href="{{ route('login') }}" class="auth-link">Sign in</a>
        </div>
    </div>
@endsection
