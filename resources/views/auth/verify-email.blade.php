@extends('layouts.app')
@section('content')

    <div class="col-md-12 col-12 px-5">
        <div class="card disable-rounded-right d-flex justify-content-center" style="margin: 10rem">
            <div class="card-header pb-1">
                <div class="card-title">
                    <h4 class="text-center my-2">Email Verification</h4>
                </div>
            </div>
            <div class="card-content">
                <div class="card-body">
                    @include('layouts.alerts')

                    <div class="mt-4">
                        <form method="POST" action="{{ route('verification.send') }}" class="text-center center">
                            @csrf
                            <p class="text-center text-muted">
                                Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.
                            </p>
                            <div>
                                <button class="btn btn-primary btn-large mt-3" type="submit">
                                    Resend Verification Email
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
