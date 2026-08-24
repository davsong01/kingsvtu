@extends('sneat.layouts.app')

@section('title', 'Update KYC')

@php
    $user = auth()->user();
    $settings = getSettings();
    $currency = $settings->currency ?? '₦';
    $statusMap = $kycStatuses->keyBy('key');
    $finalKycStatus = getFinalKycStatus($user->customer->id);
    $profileDefaults = [
        'FIRST_NAME' => $user->firstname ?? '',
        'MIDDLE_NAME' => $user->middlename ?? '',
        'LAST_NAME' => $user->lastname ?? '',
        'PHONE_NUMBER' => $user->phone ?? '',
    ];

    $statusFor = function (string $key, string $default = 'pending') use ($statusMap) {
        return strtolower((string) data_get($statusMap->get($key), 'status', $default));
    };

    $valueFor = function (string $key, string $default = '') use ($statusMap, $profileDefaults) {
        return data_get($statusMap->get($key), 'value', $default !== '' ? $default : ($profileDefaults[$key] ?? ''));
    };

    $fieldBadgeTone = function (string $status) {
        return match ($status) {
            'verified' => 'success',
            'rejected', 'declined' => 'danger',
            'awaiting-approval', 'pending-review' => 'warning',
            default => 'secondary',
        };
    };

    $countryStatus = $statusFor('COUNTRY');
    $stateStatus = $statusFor('STATE');
    $lgaStatus = $statusFor('LGA');
    $idCardTypeStatus = $statusFor('IDCARDTYPE');
    $idCardStatus = $statusFor('IDCARD');
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
                        <span class="gateway-hero__kicker">Identity verification</span>
                        <strong>Update KYC details</strong>
                        <span>Complete or correct your verification records before funding or upgrades.</span>
                    </div>
                </div>
                <div class="gateway-summary">
                    <div class="gateway-summary__card">
                        <span class="gateway-summary__label">KYC status</span>
                        <span class="gateway-summary__value">{{ formatKycStatusLabel($finalKycStatus ?: 'pending') }}</span>
                    </div>
                    <div class="gateway-summary__card">
                        <span class="gateway-summary__label">Email</span>
                        <span class="gateway-summary__value">{{ $user->email }}</span>
                    </div>
                </div>
            </div>

            @include('sneat.layouts.alerts')

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card profile-card">
                        <div class="card-header">
                            <h3>KYC form</h3>
                            <p>Fields marked verified are locked until admin review updates them.</p>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('update.kyc.details.process') }}" method="POST" autocomplete="off" enctype="multipart/form-data">
                                @csrf

                                <div class="row g-3">
                                    @php
                                        $fields = [
                                            ['key' => 'FIRST_NAME', 'label' => 'First Name', 'type' => 'text'],
                                            ['key' => 'MIDDLE_NAME', 'label' => 'Middle Name', 'type' => 'text'],
                                            ['key' => 'LAST_NAME', 'label' => 'Last Name', 'type' => 'text'],
                                            ['key' => 'PHONE_NUMBER', 'label' => 'Phone Number', 'type' => 'text'],
                                            // ['key' => 'DOB', 'label' => 'Date of Birth', 'type' => 'date'],
                                            ['key' => 'BVN', 'label' => 'BVN', 'type' => 'text'],
                                        ];
                                    @endphp

                                    @foreach($fields as $field)
                                        @php
                                            $fieldStatus = $statusFor($field['key']);
                                            $fieldValue = $valueFor($field['key']);
                                            $isVerified = $fieldStatus === 'verified';
                                        @endphp
                                        <div class="col-md-6">
                                            <label class="profile-label d-flex align-items-center justify-content-between gap-2" for="{{ $field['key'] }}">
                                                <span>{{ $field['label'] }}</span>
                                                <span class="badge bg-label-{{ $fieldBadgeTone($fieldStatus) }}">{{ ucfirst(str_replace('-', ' ', $fieldStatus)) }}</span>
                                            </label>
                                            <input
                                                type="{{ $field['type'] }}"
                                                name="{{ $field['key'] }}"
                                                id="{{ $field['key'] }}"
                                                class="form-control form-control-{{ formControlSize() }}"
                                                value="{{ old($field['key'], $fieldValue ?: ($profileDefaults[$field['key']] ?? '')) }}"
                                                @disabled($isVerified)
                                            >
                                        </div>
                                    @endforeach

                                    <div class="col-md-6">
                                        <label class="profile-label d-flex align-items-center justify-content-between gap-2" for="COUNTRY">
                                            <span>Country</span>
                                            <span class="badge bg-label-{{ $fieldBadgeTone($countryStatus) }}">{{ ucfirst(str_replace('-', ' ', $countryStatus)) }}</span>
                                        </label>
                                        <select name="COUNTRY" id="COUNTRY" class="form-select form-select-{{ formControlSize() }} modern-select2" data-placeholder="Search country" @disabled($countryStatus === 'verified')>
                                            <option value="">Select country</option>
                                            <option value="Nigeria" @selected(old('COUNTRY', $valueFor('COUNTRY')) === 'Nigeria')>Nigeria</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="profile-label d-flex align-items-center justify-content-between gap-2" for="STATE">
                                            <span>State</span>
                                            <span class="badge bg-label-{{ $fieldBadgeTone($stateStatus) }}">{{ ucfirst(str_replace('-', ' ', $stateStatus)) }}</span>
                                        </label>
                                        <select name="STATE" id="STATE" class="form-select form-select-{{ formControlSize() }} modern-select2" data-placeholder="Search state" @disabled($stateStatus === 'verified')>
                                            <option value="">Select state</option>
                                            @foreach(getStates() as $state)
                                                <option value="{{ $state }}" @selected(old('STATE', $valueFor('STATE')) === $state)>{{ $state }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="profile-label d-flex align-items-center justify-content-between gap-2" for="LGA">
                                            <span>Local Government Area</span>
                                            <span class="badge bg-label-{{ $fieldBadgeTone($lgaStatus) }}">{{ ucfirst(str_replace('-', ' ', $lgaStatus)) }}</span>
                                        </label>
                                        <select name="LGA" id="LGA" class="form-select form-select-{{ formControlSize() }} modern-select2" data-placeholder="Search LGA" @disabled($lgaStatus === 'verified')>
                                            <option value="">Select LGA</option>
                                            @foreach(($lgas ?? $oldlgas ?? []) as $lga)
                                                <option value="{{ $lga }}" @selected(old('LGA', $valueFor('LGA')) === $lga)>{{ $lga }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="profile-label d-flex align-items-center justify-content-between gap-2" for="IDCARDTYPE">
                                            <span>ID Card Type</span>
                                            <span class="badge bg-label-{{ $fieldBadgeTone($idCardTypeStatus) }}">{{ ucfirst(str_replace('-', ' ', $idCardTypeStatus)) }}</span>
                                        </label>
                                        <select name="IDCARDTYPE" id="IDCARDTYPE" class="form-select form-select-{{ formControlSize() }}" @disabled($idCardTypeStatus === 'verified')>
                                            <option value="">Select card type</option>
                                            <option value="Nin Slip" @selected(old('IDCARDTYPE', $valueFor('IDCARDTYPE')) === 'Nin Slip')>Nin Slip</option>
                                            <option value="Driver's Licence" @selected(old('IDCARDTYPE', $valueFor('IDCARDTYPE')) === "Driver's Licence")>Driver's Licence</option>
                                            <option value="International Passport" @selected(old('IDCARDTYPE', $valueFor('IDCARDTYPE')) === 'International Passport')>International Passport</option>
                                            <option value="Voter's Card" @selected(old('IDCARDTYPE', $valueFor('IDCARDTYPE')) === "Voter's Card")>Voter's Card</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="profile-label d-flex align-items-center justify-content-between gap-2" for="IDCARD">
                                            <span>ID Card Upload</span>
                                            <span class="badge bg-label-{{ $fieldBadgeTone($idCardStatus) }}">{{ ucfirst(str_replace('-', ' ', $idCardStatus)) }}</span>
                                        </label>
                                        @if(!empty($valueFor('IDCARD')))
                                            <div class="mb-2">
                                                <img src="{{ $valueFor('IDCARD') }}" alt="ID card" style="width:72px;height:72px;object-fit:cover;border-radius:.85rem;">
                                            </div>
                                        @endif
                                        <input type="file" name="IDCARD" id="IDCARD" class="form-control form-control-{{ formControlSize() }}" @disabled($idCardStatus === 'verified')>
                                    </div>
                                </div>

                                <div class="profile-footer mt-4">
                                    @if($finalKycStatus !== 'verified')
                                        <button class="btn btn-admin-submit" type="submit">Submit KYC</button>
                                    @else
                                        <a href="{{ route('customer.load.wallet') }}" class="btn btn-label-success">Fund wallet</a>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card profile-card h-100">
                        <div class="card-header">
                            <h3>KYC checklist</h3>
                            <p>Keep the submitted details aligned with your documents.</p>
                        </div>
                        <div class="card-body">
                            <div class="profile-side-row">
                                <span>BVN</span>
                                <strong>{{ $valueFor('BVN', 'Not set') }}</strong>
                            </div>
                            <div class="profile-side-row">
                                <span>First name</span>
                                <strong>{{ $valueFor('FIRST_NAME', 'Not set') }}</strong>
                            </div>
                            <div class="profile-side-row">
                                <span>Last name</span>
                                <strong>{{ $valueFor('LAST_NAME', 'Not set') }}</strong>
                            </div>
                            <div class="profile-side-row">
                                <span>Phone</span>
                                <strong>{{ $valueFor('PHONE_NUMBER', $user->phone) }}</strong>
                            </div>
                            <div class="profile-side-row">
                                <span>Country</span>
                                <strong>{{ $valueFor('COUNTRY', 'Nigeria') }}</strong>
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
