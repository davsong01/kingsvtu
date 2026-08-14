@extends('layouts.auth')

@section('auth-title', 'Forgot password?')
@section('auth-subtitle', 'We’ll send a reset link to your email address.')

@section('body')
    <div class="auth-form">
        @include('layouts.alerts')

        <form action="{{ route('password.email') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="email">Email address</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="Enter your email address" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Email reset link</button>
        </form>

        <div class="auth-footer-note">
            I remember my password. <a href="{{ route('login') }}" class="auth-link">Sign in</a>
        </div>
    </div>
@endsection
