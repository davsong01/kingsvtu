@extends('layouts.auth')

@section('auth-title', 'Reset password')
@section('auth-subtitle', 'Choose a new password for your account.')

@section('body')
    <div class="auth-form">
        @include('layouts.alerts')

        <form action="{{ route('password.store') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="form-group">
                <label for="email">Email address</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') ?? $request->email }}" required autofocus autocomplete="username">
            </div>
            <div class="form-group">
                <label for="password">New password</label>
                <div class="input-group auth-password-field">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter new password" required>
                    <button type="button" class="btn auth-password-toggle" data-password-toggle="password">Show</button>
                </div>
            </div>
            <div class="form-group">
                <label for="password_confirmation">Confirm password</label>
                <div class="input-group auth-password-field">
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirm password" required>
                    <button type="button" class="btn auth-password-toggle" data-password-toggle="password_confirmation">Show</button>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100">Reset password</button>
        </form>
    </div>
@endsection
