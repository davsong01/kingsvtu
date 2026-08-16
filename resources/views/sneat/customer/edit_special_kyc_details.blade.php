@extends('sneat.layouts.app')

@section('title', 'Special KYC Update')

@php
    $user = auth()->user();
    $settings = getSettings();
    $currency = $settings->currency ?? '₦';
    $fields = collect($fields ?? [])->values();

    $fieldCount = $fields->count();
    $selectFields = $fields->filter(fn ($field) => ($field['input_type'] ?? '') === 'select')->count();
@endphp

@section('page-css')
    <link href="{{ asset('modern-assets/vendor/libs/select2/select2.css') }}" rel="stylesheet" />
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="profile-hero mb-4 gateway-hero">
                <div class="profile-hero__meta">
                    <div class="profile-avatar"><i class="bx bx-id-card"></i></div>
                    <div class="profile-meta">
                        <span class="gateway-hero__kicker">Compliance update</span>
                        <strong>Special KYC information</strong>
                        <span>Provide the remaining details requested by the team to continue using your account.</span>
                    </div>
                </div>
                <div class="gateway-summary">
                    <div class="gateway-summary__card">
                        <span class="gateway-summary__label">Fields required</span>
                        <span class="gateway-summary__value">{{ $fieldCount }}</span>
                    </div>
                    <div class="gateway-summary__card">
                        <span class="gateway-summary__label">Select fields</span>
                        <span class="gateway-summary__value">{{ $selectFields }}</span>
                    </div>
                    <div class="gateway-summary__card">
                        <span class="gateway-summary__label">Wallet</span>
                        <span class="gateway-summary__value">{{ $currency . number_format(walletBalance($user), 2) }}</span>
                    </div>
                </div>
            </div>

            @include('sneat.layouts.alerts')

            @if(!empty($kycmessage))
                <div class="alert alert-info border mb-4">
                    {!! $kycmessage !!}
                </div>
            @endif

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card profile-card">
                        <div class="card-header">
                            <h3>Special KYC form</h3>
                            <p>Complete the fields below exactly as requested.</p>
                        </div>
                        <div class="card-body">
                            @if($fields->isNotEmpty())
                                <form method="POST" action="{{ route('submit.special.kyc') }}" autocomplete="off">
                                    @csrf
                                    <div class="row g-3">
                                        @foreach($fields as $field)
                                            @php
                                                $key = $field['key'] ?? '';
                                                $label = $field['label'] ?? $key;
                                                $inputType = $field['input_type'] ?? 'text';
                                                $options = $field['options'] ?? [];
                                                $currentValue = old($key);
                                            @endphp
                                            <div class="col-md-6">
                                                <div class="profile-side-card h-100">
                                                    <div class="d-flex align-items-start justify-content-between gap-3 mb-2">
                                                        <div>
                                                            <div class="gateway-helper text-uppercase fw-semibold mb-1">{{ $key }}</div>
                                                            <h6 class="mb-0">{{ $label }}</h6>
                                                        </div>
                                                        <span class="badge bg-label-primary">Required</span>
                                                    </div>

                                                    @if($inputType === 'text')
                                                        <input type="text" class="form-control form-control-{{ formControlSize() }}" name="{{ $key }}" id="{{ $key }}" value="{{ $currentValue }}" required>
                                                    @elseif($inputType === 'date')
                                                        <input type="date" class="form-control form-control-{{ formControlSize() }}" name="{{ $key }}" id="{{ $key }}" value="{{ $currentValue }}" required>
                                                    @elseif($inputType === 'select')
                                                        <select class="form-select form-select-{{ formControlSize() }} modern-select2" name="{{ $key }}" id="{{ $key }}" data-placeholder="Select {{ strtolower($label) }}" required>
                                                            <option value="">Select {{ $label }}</option>
                                                            @foreach($options as $value => $optionLabel)
                                                                <option value="{{ $value }}" @selected((string) $currentValue === (string) $value)>{{ $optionLabel }}</option>
                                                            @endforeach
                                                        </select>
                                                    @else
                                                        <input type="text" class="form-control form-control-{{ formControlSize() }}" name="{{ $key }}" id="{{ $key }}" value="{{ $currentValue }}" required>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="profile-footer mt-4">
                                        <button type="submit" class="btn btn-admin-submit">Submit details</button>
                                    </div>
                                </form>
                                <div class="mt-4 p-4 rounded-4 border bg-body-tertiary">
                                    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                                        <div>
                                            <h5 class="mb-1">Finished with the form?</h5>
                                            <p class="mb-0 text-secondary">Once you have completed your KYC details, notify the admin to review your profile.</p>
                                        </div>
                                        <form action="{{ route('customer.notify.admin.kyc') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-primary btn-sm">CLICK HERE TO NOTIFY ADMIN</button>
                                        </form>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-light border mb-0">No special KYC fields are currently required.</div>
                                <div class="mt-4">
                                    <form action="{{ route('customer.notify.admin.kyc') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-primary">CLICK HERE TO NOTIFY ADMIN</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card profile-card h-100">
                        <div class="card-header">
                            <h3>Helpful notes</h3>
                            <p>Keep the form inputs aligned with the exact request you received.</p>
                        </div>
                        <div class="card-body">
                            <div class="profile-side-row">
                                <span>Account</span>
                                <strong>{{ $user->email }}</strong>
                            </div>
                            <div class="profile-side-row">
                                <span>Status</span>
                                <strong>Special review</strong>
                            </div>
                            <div class="gateway-helper mt-3">
                                When you submit these details, your account will be reviewed and the remaining KYC items will be cleared from the queue.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script src="{{ asset('modern-assets/vendor/libs/select2/select2.js') }}"></script>
    <script>
        (function () {
            $('.modern-select2').each(function () {
                const $select = $(this);

                if ($select.data('select2')) {
                    return;
                }

                $select.wrap('<div class="position-relative"></div>').select2({
                    placeholder: $select.data('placeholder') || '',
                    allowClear: true,
                    width: '100%',
                });
            });
        })();
    </script>
@endsection
