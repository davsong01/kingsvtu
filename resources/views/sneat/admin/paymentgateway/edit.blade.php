@extends('sneat.layouts.app')

@section('title', 'Edit ' . $paymentgateway->name)

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="gateway-hero mb-4">
                <div>
                    <span class="gateway-hero__kicker">Payment configuration</span>
                    <h1>Edit {{ $paymentgateway->name }}</h1>
                    <p>Refine gateway keys, charges, and reserved account billing from a clean admin screen.</p>
                </div>
                <div class="gateway-summary">
                    <div class="gateway-summary__card">
                        <span class="gateway-summary__label">Slug</span>
                        <span class="gateway-summary__value">{{ $paymentgateway->slug }}</span>
                    </div>
                    <div class="gateway-summary__card">
                        <span class="gateway-summary__label">Base URL</span>
                        <span class="gateway-summary__value">{{ $paymentgateway->base_url ?: 'Not set' }}</span>
                    </div>
                    <div class="gateway-summary__card">
                        <span class="gateway-summary__label">Charge</span>
                        <span class="gateway-summary__value">{{ $paymentgateway->charge }}%</span>
                    </div>
                </div>
            </div>

            @include('sneat.layouts.alerts')

            <form action="{{ route('paymentgateway.update', $paymentgateway->id) }}" method="POST">
                @csrf
                @method('PATCH')

                <div class="row g-4">
                    <div class="col-12">
                        <div class="modern-admin-card card mb-4">
                            <div class="card-header">
                                <h3>Gateway identity</h3>
                                <p>Core connection details for the provider.</p>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="modern-admin-label" for="name">Name</label>
                                        <input type="text" class="form-control form-control-{{ formControlSize() }}" id="name" name="name" value="{{ old('name', $paymentgateway->name ?? '') }}" placeholder="Gateway name" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="modern-admin-label" for="slug">Slug</label>
                                        <input type="text" class="form-control form-control-{{ formControlSize() }}" id="slug" name="slug" value="{{ old('slug', $paymentgateway->slug ?? '') }}" placeholder="Gateway slug" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="modern-admin-label" for="base_url">Base URL</label>
                                        <input type="text" class="form-control form-control-{{ formControlSize() }}" id="base_url" name="base_url" value="{{ old('base_url', $paymentgateway->base_url ?? '') }}" placeholder="https://.../api/v1" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="modern-admin-label" for="merchant_email">Merchant Email</label>
                                        <input type="text" class="form-control form-control-{{ formControlSize() }}" id="merchant_email" name="merchant_email" value="{{ old('merchant_email', $paymentgateway->merchant_email ?? '') }}" placeholder="Merchant email">
                                    </div>
                                    <div class="col-12">
                                        <label class="modern-admin-label" for="contract_id">Contract ID</label>
                                        <input type="text" class="form-control form-control-{{ formControlSize() }}" id="contract_id" name="contract_id" value="{{ old('contract_id', $paymentgateway->contract_id ?? '') }}" placeholder="Contract ID">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="modern-admin-card card mb-4">
                            <div class="card-header">
                                <h3>Charges and keys</h3>
                                <p>Keep gateway credentials and fee rules tidy.</p>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="modern-admin-label" for="password">Gateway Password</label>
                                        <input type="text" class="form-control form-control-{{ formControlSize() }}" id="password" name="password" value="{{ old('password', $paymentgateway->password ?? '') }}" placeholder="Password">
                                    </div>
                                    <div class="col-12">
                                        <label class="modern-admin-label" for="api_key">API Key</label>
                                        <input type="text" class="form-control form-control-{{ formControlSize() }}" id="api_key" name="api_key" value="{{ old('api_key', $paymentgateway->api_key ?? '') }}" placeholder="API key">
                                    </div>
                                    <div class="col-12">
                                        <label class="modern-admin-label" for="secret_key">Secret Key</label>
                                        <input type="text" class="form-control form-control-{{ formControlSize() }}" id="secret_key" name="secret_key" value="{{ old('secret_key', $paymentgateway->secret_key ?? '') }}" placeholder="Secret key">
                                    </div>
                                    <div class="col-12">
                                        <label class="modern-admin-label" for="public_key">Public Key</label>
                                        <input type="text" class="form-control form-control-{{ formControlSize() }}" id="public_key" name="public_key" value="{{ old('public_key', $paymentgateway->public_key ?? '') }}" placeholder="Public key">
                                    </div>
                                    <div class="col-12">
                                        <label class="modern-admin-label" for="charge">Gateway Charge (%)</label>
                                        <input type="number" class="form-control form-control-{{ formControlSize() }}" id="charge" name="charge" step=".10" min="0" max="100" value="{{ old('charge', $paymentgateway->charge ?? '') }}" placeholder="0.00" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="modern-admin-label" for="charge_cap">Charge Cap</label>
                                        <input type="number" class="form-control form-control-{{ formControlSize() }}" id="charge_cap" name="charge_cap" step=".10" value="{{ old('charge_cap', $paymentgateway->charge_cap ?? '') }}" placeholder="0.00">
                                    </div>
                                    <div class="col-6">
                                        <label class="modern-admin-label" for="reserved_account_payment_charge_type">Reserved Account Charge Type</label>
                                        <select class="form-select form-select-{{ formControlSize() }}" name="reserved_account_payment_charge_type" id="reserved_account_payment_charge_type" required>
                                            <option value="flat" {{ old('reserved_account_payment_charge_type', $paymentgateway->reserved_account_payment_charge_type ?? '') === 'flat' ? 'selected' : '' }}>Flat</option>
                                            <option value="percentage" {{ old('reserved_account_payment_charge_type', $paymentgateway->reserved_account_payment_charge_type ?? '') === 'percentage' ? 'selected' : '' }}>Percentage</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="modern-admin-label" for="reserved_account_payment_charge">Reserved Account Charge</label>
                                        <input type="number" class="form-control form-control-{{ formControlSize() }}" id="reserved_account_payment_charge" name="reserved_account_payment_charge" step=".10" value="{{ old('reserved_account_payment_charge', $paymentgateway->reserved_account_payment_charge ?? '') }}" placeholder="0.00" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="modern-admin-label" for="reserved_account_payment_charge_cap">Charge Cap</label>
                                        <input type="number" class="form-control form-control-{{ formControlSize() }}" id="reserved_account_payment_charge_cap" name="reserved_account_payment_charge_cap" step=".10" value="{{ old('reserved_account_payment_charge_cap', $paymentgateway->reserved_account_payment_charge_cap ?? '') }}" placeholder="0.00">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modern-admin-card card">
                            <div class="card-body">
                                <p class="modern-admin-note mb-0">Changing gateway keys affects live payments immediately. Update carefully.</p>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modern-admin-footer">
                    <button class="btn btn-admin-submit" type="submit">Update Settings</button>
                </div>
            </form>
        </div>
    </div>
@endsection
