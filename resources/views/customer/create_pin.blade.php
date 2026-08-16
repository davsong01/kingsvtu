@extends('layouts.app')
@section('title', 'Create Transaction Pin')

@section('page-css')
<style>
    .create-pin-page {
        padding: 1.25rem 0 2rem;
    }

    .create-pin-shell {
        overflow: hidden;
        border: 1px solid rgba(15, 23, 42, .08);
        border-radius: 1.5rem;
        background: linear-gradient(145deg, rgba(255, 255, 255, .96), rgba(248, 250, 252, .98));
        box-shadow: 0 1.5rem 3.5rem rgba(15, 23, 42, .08);
    }

    .create-pin-hero {
        padding: 1.5rem 1.5rem 1.35rem;
        border-bottom: 1px solid rgba(15, 23, 42, .08);
        background:
            radial-gradient(circle at top right, rgba(31, 168, 104, .11), transparent 30%),
            linear-gradient(180deg, rgba(255, 255, 255, .98), rgba(248, 250, 252, .92));
    }

    .create-pin-hero__badge {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        padding: .35rem .7rem;
        border-radius: 999px;
        background: rgba(31, 168, 104, .1);
        color: #1fa868;
        font-size: .74rem;
        font-weight: 800;
        letter-spacing: .12em;
        text-transform: uppercase;
    }

    .create-pin-hero__badge::before {
        content: "";
        width: .45rem;
        height: .45rem;
        border-radius: 50%;
        background: currentColor;
    }

    .create-pin-hero h4 {
        margin: .85rem 0 .45rem;
        color: #172033;
        font-size: 1.5rem;
        font-weight: 800;
        letter-spacing: -.03em;
    }

    .create-pin-hero p {
        margin-bottom: 0;
        color: #64748b;
        line-height: 1.65;
    }

    .create-pin-content {
        padding: 1.5rem;
    }

    .create-pin-card {
        height: 100%;
        border: 1px solid rgba(15, 23, 42, .08);
        border-radius: 1.25rem;
        background: #fff;
        box-shadow: 0 .35rem 1rem rgba(15, 23, 42, .04);
    }

    .create-pin-card__body {
        padding: 1.25rem;
    }

    .create-pin-form {
        display: grid;
        gap: .95rem;
    }

    .create-pin-form .form-group {
        margin-bottom: 0;
    }

    .create-pin-form label {
        margin-bottom: .45rem;
        color: #374151;
        font-size: .88rem;
        font-weight: 600;
    }

    .create-pin-form .form-control {
        min-height: 2.85rem;
        border: 1px solid rgba(15, 23, 42, .12);
        border-radius: 1rem;
        background-color: #fff;
        box-shadow: none;
        color: #172033;
    }

    .create-pin-form .form-control:focus {
        border-color: rgba(var(--bs-warning-rgb), .62);
        box-shadow: 0 0 0 .2rem rgba(var(--bs-warning-rgb), .16);
    }

    .create-pin-password {
        display: flex;
        align-items: stretch;
    }

    .create-pin-password .form-control {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }

    .create-pin-password__toggle {
        min-width: 4.8rem;
        border: 1px solid rgba(15, 23, 42, .12);
        border-left: 0;
        border-top-right-radius: 1rem;
        border-bottom-right-radius: 1rem;
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
        background: #fff;
        color: #526173;
        font-size: .82rem;
        font-weight: 700;
    }

    .create-pin-password__toggle:focus,
    .create-pin-password__toggle:focus-visible {
        box-shadow: 0 0 0 .2rem rgba(var(--bs-warning-rgb), .16);
        outline: 0;
    }

    .create-pin-submit {
        min-width: 150px;
        min-height: 3rem;
        border-radius: 1rem;
        box-shadow: 0 .75rem 1.5rem rgba(var(--bs-primary-rgb), .16);
    }

    .create-pin-submit:hover {
        transform: translateY(-1px);
    }

    .create-pin-tips {
        display: grid;
        gap: .85rem;
        padding: 1.25rem;
    }

    .create-pin-tip {
        display: flex;
        gap: .75rem;
        align-items: flex-start;
        padding: .95rem 1rem;
        border: 1px solid rgba(15, 23, 42, .08);
        border-radius: 1rem;
        background: rgba(255, 255, 255, .8);
    }

    .create-pin-tip__icon {
        width: 2.25rem;
        height: 2.25rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
        border-radius: .75rem;
        background: rgba(31, 168, 104, .1);
        color: #1fa868;
        font-size: 1rem;
    }

    .create-pin-tip h6 {
        margin-bottom: .2rem;
        color: #172033;
        font-size: .95rem;
        font-weight: 700;
    }

    .create-pin-tip p {
        margin-bottom: 0;
        color: #64748b;
        font-size: .88rem;
        line-height: 1.55;
    }

    .create-pin-ad {
        padding: 1rem 1.25rem 1.25rem;
    }

    .create-pin-ad .ad-slot {
        overflow: hidden;
        border-radius: 1rem;
    }

    @media (max-width: 991.98px) {
        .create-pin-content,
        .create-pin-hero {
            padding-inline: 1.25rem;
        }
    }

    @media (max-width: 575.98px) {
        .create-pin-page {
            padding-top: .75rem;
        }

        .create-pin-hero h4 {
            font-size: 1.3rem;
        }

        .create-pin-submit {
            width: 100%;
        }

        .create-pin-password {
            flex-direction: column;
        }

        .create-pin-password .form-control {
            border-top-right-radius: 1rem;
            border-bottom-right-radius: 1rem;
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
        }

        .create-pin-password__toggle {
            width: 100%;
            min-width: 0;
            border-left: 1px solid rgba(15, 23, 42, .12);
            border-top-right-radius: 0;
            border-bottom-left-radius: 1rem;
        }
    }
</style>
@endsection

@section('content')
<div class="app-content content create-pin-page">
    <div class="content-overlay"></div>
    <div class="content-wrapper">
        <div class="content-body">
            <section>
                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="create-pin-shell">
                            <div class="create-pin-hero">
                                <span class="create-pin-hero__badge">Security setup</span>
                                <h4>Create Transaction PIN</h4>
                                <p>
                                    Set up your transaction PIN to approve wallet activity and purchases securely.
                                    You’ll only need your current password and the new 5-digit PIN.
                                </p>
                                @include('layouts.alerts')
                            </div>

                            <div class="create-pin-content">
                                <div class="create-pin-card">
                                    <div class="create-pin-card__body">
                                        <form action="{{ route('customer.process.create.pin') }}" method="POST" class="create-pin-form" autocomplete="off">
                                            @csrf

                                            <div class="form-group">
                                                <label for="password">Password</label>
                                                <div class="create-pin-password">
                                                    <input autocomplete="off" type="password" class="form-control" id="password" name="password" placeholder="Enter your account password" required>
                                                    <button type="button" class="btn create-pin-password__toggle" data-password-toggle="password">Show</button>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="transaction_pin">Transaction PIN</label>
                                                <input
                                                    autocomplete="off"
                                                    type="password"
                                                    inputmode="numeric"
                                                    maxlength="5"
                                                    class="form-control"
                                                    id="transaction_pin"
                                                    name="transaction_pin"
                                                    placeholder="Enter a 5-digit PIN"
                                                    required>
                                            </div>

                                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 pt-1">
                                                <a href="{{ route('dashboard') }}" class="text-decoration-none fw-semibold text-secondary">
                                                    Back to dashboard
                                                </a>
                                                <button class="btn btn-primary create-pin-submit" type="submit">Create PIN</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card create-pin-card h-100">
                            <div class="card-body create-pin-tips">
                                <div class="create-pin-tip">
                                    <div class="create-pin-tip__icon">
                                        <i class="fa fa-shield"></i>
                                    </div>
                                    <div>
                                        <h6>Use a memorable 5-digit PIN</h6>
                                        <p>Keep it simple for you, hard for everyone else, and avoid repeating digits.</p>
                                    </div>
                                </div>

                                <div class="create-pin-tip">
                                    <div class="create-pin-tip__icon">
                                        <i class="fa fa-lock"></i>
                                    </div>
                                    <div>
                                        <h6>We always verify your password</h6>
                                        <p>Your current password is required before the PIN can be created.</p>
                                    </div>
                                </div>

                                <div class="create-pin-tip">
                                    <div class="create-pin-tip__icon">
                                        <i class="fa fa-bolt"></i>
                                    </div>
                                    <div>
                                        <h6>Fast access after setup</h6>
                                        <p>Once your PIN is saved, you can proceed to funding and purchases without extra friction.</p>
                                    </div>
                                </div>
                            </div>

                            @if(!empty(getSettings()->google_ad_code))
                                <div class="create-pin-ad">
                                    <div class="ad-slot">
                                        {!! getSettings()->google_ad_code !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
