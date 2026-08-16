@extends('layouts.app')
@section('title', 'Reset Transaction Pin')

@section('page-css')
<style>
    .reset-pin-page {
        padding: 1.25rem 0 2rem;
    }

    .reset-pin-shell {
        overflow: hidden;
        border: 1px solid rgba(15, 23, 42, .08);
        border-radius: 1.5rem;
        background: linear-gradient(145deg, rgba(255, 255, 255, .96), rgba(248, 250, 252, .98));
        box-shadow: 0 1.5rem 3.5rem rgba(15, 23, 42, .08);
    }

    .reset-pin-hero {
        padding: 1.5rem 1.5rem 1.35rem;
        border-bottom: 1px solid rgba(15, 23, 42, .08);
        background:
            radial-gradient(circle at top right, rgba(31, 168, 104, .11), transparent 30%),
            linear-gradient(180deg, rgba(255, 255, 255, .98), rgba(248, 250, 252, .92));
    }

    .reset-pin-hero__badge {
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

    .reset-pin-hero__badge::before {
        content: "";
        width: .45rem;
        height: .45rem;
        border-radius: 50%;
        background: currentColor;
    }

    .reset-pin-hero h4 {
        margin: .85rem 0 .45rem;
        color: #172033;
        font-size: 1.5rem;
        font-weight: 800;
        letter-spacing: -.03em;
    }

    .reset-pin-hero p {
        margin-bottom: 0;
        color: #64748b;
        line-height: 1.65;
    }

    .reset-pin-content {
        padding: 1.5rem;
    }

    .reset-pin-card {
        height: 100%;
        border: 1px solid rgba(15, 23, 42, .08);
        border-radius: 1.25rem;
        background: #fff;
        box-shadow: 0 .35rem 1rem rgba(15, 23, 42, .04);
    }

    .reset-pin-card__body {
        padding: 1.25rem;
    }

    .reset-pin-form {
        display: grid;
        gap: .95rem;
    }

    .reset-pin-form .form-group {
        margin-bottom: 0;
    }

    .reset-pin-form label {
        margin-bottom: .45rem;
        color: #374151;
        font-size: .88rem;
        font-weight: 600;
    }

    .reset-pin-form .form-control {
        min-height: 2.85rem;
        border: 1px solid rgba(15, 23, 42, .12);
        border-radius: 1rem;
        background-color: #fff;
        box-shadow: none;
        color: #172033;
    }

    .reset-pin-form .form-control:focus {
        border-color: rgba(var(--bs-warning-rgb), .62);
        box-shadow: 0 0 0 .2rem rgba(var(--bs-warning-rgb), .16);
    }

    .reset-pin-password {
        display: flex;
        align-items: stretch;
    }

    .reset-pin-password .form-control {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }

    .reset-pin-password__toggle {
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

    .reset-pin-password__toggle:focus,
    .reset-pin-password__toggle:focus-visible {
        box-shadow: 0 0 0 .2rem rgba(var(--bs-warning-rgb), .16);
        outline: 0;
    }

    .reset-pin-submit {
        min-width: 180px;
        min-height: 3rem;
        border-radius: 1rem;
        box-shadow: 0 .75rem 1.5rem rgba(var(--bs-primary-rgb), .16);
    }

    .reset-pin-submit:hover {
        transform: translateY(-1px);
    }

    .reset-pin-side {
        display: grid;
        gap: .85rem;
        padding: 1.25rem;
    }

    .reset-pin-tip {
        display: flex;
        gap: .75rem;
        align-items: flex-start;
        padding: .95rem 1rem;
        border: 1px solid rgba(15, 23, 42, .08);
        border-radius: 1rem;
        background: rgba(255, 255, 255, .8);
    }

    .reset-pin-tip__icon {
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

    .reset-pin-tip h6 {
        margin-bottom: .2rem;
        color: #172033;
        font-size: .95rem;
        font-weight: 700;
    }

    .reset-pin-tip p {
        margin-bottom: 0;
        color: #64748b;
        font-size: .88rem;
        line-height: 1.55;
    }

    .reset-pin-email {
        display: block;
        margin-top: .2rem;
        color: #172033;
        font-weight: 700;
        word-break: break-word;
    }

    .reset-pin-ad {
        padding: 1rem 1.25rem 1.25rem;
    }

    .reset-pin-ad .ad-slot {
        overflow: hidden;
        border-radius: 1rem;
    }

    @media (max-width: 991.98px) {
        .reset-pin-content,
        .reset-pin-hero {
            padding-inline: 1.25rem;
        }
    }

    @media (max-width: 575.98px) {
        .reset-pin-page {
            padding-top: .75rem;
        }

        .reset-pin-hero h4 {
            font-size: 1.3rem;
        }

        .reset-pin-submit {
            width: 100%;
        }

        .reset-pin-password {
            flex-direction: column;
        }

        .reset-pin-password .form-control {
            border-top-right-radius: 1rem;
            border-bottom-right-radius: 1rem;
        }

        .reset-pin-password__toggle {
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
<div class="app-content content reset-pin-page">
    <div class="content-overlay"></div>
    <div class="content-wrapper">
        <div class="content-body">
            <section>
                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="reset-pin-shell">
                            <div class="reset-pin-hero">
                                <span class="reset-pin-hero__badge">Security reset</span>
                                <h4>Reset Transaction PIN</h4>
                                <p>
                                    Request a reset link and we’ll send it to your registered email address.
                                    You’ll need your account password to confirm the request.
                                </p>
                                @include('layouts.alerts')
                            </div>

                            <div class="reset-pin-content">
                                <div class="reset-pin-card">
                                    <div class="reset-pin-card__body">
                                        <form action="{{ route('process.transaction.pin.reset') }}" method="POST" class="reset-pin-form" autocomplete="off">
                                            @csrf

                                            <div class="form-group">
                                                <label for="password">Password</label>
                                                <div class="reset-pin-password">
                                                    <input autocomplete="off" type="password" class="form-control" id="password" name="password" placeholder="Enter your account password" required>
                                                    <button type="button" class="btn reset-pin-password__toggle" data-password-toggle="password">Show</button>
                                                </div>
                                            </div>

                                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 pt-1">
                                                <a href="{{ route('dashboard') }}" class="text-decoration-none fw-semibold text-secondary">
                                                    Back to dashboard
                                                </a>
                                                <button class="btn btn-primary reset-pin-submit" type="submit">Send reset link</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card reset-pin-card h-100">
                            <div class="card-body reset-pin-side">
                                <div class="reset-pin-tip">
                                    <div class="reset-pin-tip__icon">
                                        <i class="fa fa-envelope"></i>
                                    </div>
                                    <div>
                                        <h6>Email delivery</h6>
                                        <p>The reset link is sent to your registered email address for secure recovery.</p>
                                        <span class="reset-pin-email">{{ auth()->user()->email }}</span>
                                    </div>
                                </div>

                                <div class="reset-pin-tip">
                                    <div class="reset-pin-tip__icon">
                                        <i class="fa fa-clock"></i>
                                    </div>
                                    <div>
                                        <h6>Short-lived link</h6>
                                        <p>The reset link expires quickly, so complete the reset as soon as you receive it.</p>
                                    </div>
                                </div>

                                <div class="reset-pin-tip">
                                    <div class="reset-pin-tip__icon">
                                        <i class="fa fa-shield"></i>
                                    </div>
                                    <div>
                                        <h6>Account safety</h6>
                                        <p>Your password is required before we send any PIN reset request.</p>
                                    </div>
                                </div>
                            </div>

                            @if(!empty(getSettings()->google_ad_code))
                                <div class="reset-pin-ad">
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
