@extends('layouts.auth')

@section('auth-title', 'Change transaction PIN')
@section('auth-subtitle', 'Enter your new transaction PIN to complete the reset.')

@section('body')
    <div class="auth-form">
        @include('layouts.alerts')

        <form action="{{ route('final.pin.reset') }}" method="POST" autocomplete="off">
            @csrf
            <div class="form-group">
                <label for="new_transaction_pin">New transaction PIN</label>
                <div class="input-group auth-password-field">
                    <input autocomplete="off" type="password" class="form-control" id="new_transaction_pin" name="new_transaction_pin" required>
                    <button type="button" class="btn auth-password-toggle" data-password-toggle="new_transaction_pin">Show</button>
                </div>
            </div>
            <button class="btn btn-primary w-100" type="submit">Update transaction PIN</button>
        </form>
    </div>
@endsection
