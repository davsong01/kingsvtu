@extends('layouts.auth')
@section('body')

    <div class="col-md-12 col-12 px-0">
        <div class="card disable-rounded-right d-flex justify-content-center">
            <div class="card-header pb-1">
                <div class="card-title">
                    <h4 class="text-center mb-2">Create An Account</h4>
                </div>
            </div>
            <div class="card-content">
                <div class="card-body">
                    @include('layouts.alerts')
                    <div class="mb-4 text-sm text-gray-600">
                        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
                    </div>
                    @if (session('status') == 'verification-link-sent')
                        <div class="mb-4 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                        </div>
                    @endif

                    <div class="mt-4 flex items-center justify-between">
                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf

                            <div>
                                <x-primary-button>
                                    {{ __('Resend Verification Email') }}
                                </x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
