@extends('layouts.auth')

@section('auth-title', 'Confirm password')
@section('auth-subtitle', 'This is a secure area. Please confirm your password to continue.')

@section('body')
    <div class="auth-form">
        @include('layouts.alerts')

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf
            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-group auth-password-field">
                    <input id="password" class="form-control" type="password" name="password" required autocomplete="current-password">
                    <button type="button" class="btn auth-password-toggle" data-password-toggle="password">Show</button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <button type="submit" class="btn btn-primary w-100">Confirm</button>
        </form>
    </div>
@endsection
