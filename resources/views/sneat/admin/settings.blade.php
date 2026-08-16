@extends('sneat.layouts.app')

@section('title', 'General Settings')

@section('page-css')
    <link href="{{ asset('modern-assets/vendor/libs/select2/select2.css') }}" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.0-rc.2/dist/quill.snow.css" rel="stylesheet" />
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            @php
                $selectedGateways = old('payment_gateway', is_array($settings->payment_gateway)
                    ? $settings->payment_gateway
                    : (json_decode($settings->payment_gateway, true) ?? []));
                $selectedGatewayCount = count($selectedGateways);
            @endphp

            <div class="admin-page-hero mb-4">
                <div>
                    <span class="admin-page-hero__kicker">Platform configuration</span>
                    <h1>General Settings</h1>
                </div>
                <div class="admin-page-badges">
                    <div class="admin-page-badge">
                        <span>Admin layout</span>
                        <strong>{{ ucfirst($settings->admin_layout ?? 'modern') }}</strong>
                    </div>
                    <div class="admin-page-badge">
                        <span>Customer layout</span>
                        <strong>{{ ucfirst($settings->customer_layout ?? 'modern') }}</strong>
                    </div>
                </div>
            </div>

            <div class="row g-3 g-xl-4 mb-4">
                <div class="col-md-4">
                    <div class="admin-settings-stat">
                        <div class="admin-settings-stat__label">Official Email</div>
                        <div class="admin-settings-stat__value">{{ $settings->official_email ?: 'Not set' }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="admin-settings-stat">
                        <div class="admin-settings-stat__label">Payment Gateways</div>
                        <div class="admin-settings-stat__value">{{ $selectedGatewayCount }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="admin-settings-stat">
                        <div class="admin-settings-stat__label">Referral System</div>
                        <div class="admin-settings-stat__value">{{ ucfirst($settings->referral_system_status ?? 'inactive') }}</div>
                    </div>
                </div>
            </div>

            @include('sneat.layouts.alerts')

            <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row g-4">
                    <div class="col-xl-12">
                        <div class="admin-settings-card card mb-4">
                            <div class="card-header">
                                <h3>Branding and contact</h3>
                                <p>These values shape your public identity and email touchpoints.</p>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="admin-form-label" for="official_email">Official Email</label>
                                        <input type="email" class="form-control form-control-{{ formControlSize() }}" id="official_email" name="official_email" value="{{ old('official_email', $settings->official_email ?? '') }}" placeholder="Official email">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="admin-form-label" for="whatsapp_number">WhatsApp Number</label>
                                        <input type="text" class="form-control form-control-{{ formControlSize() }}" id="whatsapp_number" name="whatsapp_number" value="{{ old('whatsapp_number', $settings->whatsapp_number ?? '') }}" placeholder="WhatsApp number">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="admin-form-label" for="currency">Currency</label>
                                        <select name="currency" class="form-select form-select-{{ formControlSize() }}" id="currency" required>
                                            @foreach($currencies as $currency)
                                                <option value="{{ $currency }}" {{ old('currency', $settings->currency ?? '') == $currency ? 'selected' : '' }}>{!! $currency !!}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="admin-form-label" for="logo">Logo</label>
                                        <div class="admin-upload-card">
                                            <div class="admin-upload-card__meta">
                                                <strong>Primary logo</strong>
                                                <input type="file" accept="image/*" class="form-control form-control-{{ formControlSize() }} mt-2" id="logo" name="logo">
                                            </div>
                                            <div class="admin-upload-preview">
                                                @if(!empty($settings->logo))
                                                    <img src="{{ asset($settings->logo) }}" alt="Logo preview">
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="admin-form-label" for="favicon">Favicon</label>
                                        <div class="admin-upload-card">
                                            <div class="admin-upload-card__meta">
                                                <strong>Browser icon</strong>
                                                <input type="file" accept="image/*" class="form-control form-control-{{ formControlSize() }} mt-2" id="favicon" name="favicon">
                                            </div>
                                            <div class="admin-upload-preview">
                                                @if(!empty($settings->favicon))
                                                    <img src="{{ asset($settings->favicon) }}" alt="Favicon preview">
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="admin-form-label" for="dashboard_logo">Dashboard Logo</label>
                                        <div class="admin-upload-card">
                                            <div class="admin-upload-card__meta">
                                                <strong>Dashboard logo</strong>
                                                <input type="file" accept="image/*" class="form-control form-control-{{ formControlSize() }} mt-2" id="dashboard_logo" name="dashboard_logo">
                                            </div>
                                            <div class="admin-upload-preview">
                                                @if(!empty($settings->dashboard_logo))
                                                    <img src="{{ asset($settings->dashboard_logo) }}" alt="Dashboard logo preview">
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="admin-settings-card card mb-4">
                            <div class="card-header">
                                <h3>Experience and security</h3>
                                <p>Control layout switching, email notifications, funding options, referral rules, and captcha.</p>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="admin-form-label" for="admin_layout">Admin Layout</label>
                                        <select name="admin_layout" class="form-select form-select-{{ formControlSize() }}" id="admin_layout">
                                            <option value="legacy" {{ old('admin_layout', $settings->admin_layout ?? 'modern') === 'legacy' ? 'selected' : '' }}>Legacy Layout</option>
                                            <option value="modern" {{ old('admin_layout', $settings->admin_layout ?? 'modern') === 'modern' ? 'selected' : '' }}>Modern Layout</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="admin-form-label" for="customer_layout">Customer Layout</label>
                                        <select name="customer_layout" class="form-select form-select-{{ formControlSize() }}" id="customer_layout">
                                            <option value="legacy" {{ old('customer_layout', $settings->customer_layout ?? 'modern') === 'legacy' ? 'selected' : '' }}>Legacy Layout</option>
                                            <option value="modern" {{ old('customer_layout', $settings->customer_layout ?? 'modern') === 'modern' ? 'selected' : '' }}>Modern Layout</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="admin-form-label" for="customer_of_the_month_status">Customer of the Month</label>
                                        <select name="customer_of_the_month_status" class="form-select form-select-{{ formControlSize() }}" id="customer_of_the_month_status">
                                            <option value="yes" {{ old('customer_of_the_month_status', $settings->customer_of_the_month_status ?? 'yes') === 'yes' ? 'selected' : '' }}>Show</option>
                                            <option value="no" {{ old('customer_of_the_month_status', $settings->customer_of_the_month_status ?? 'yes') === 'no' ? 'selected' : '' }}>Hide</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="admin-form-label" for="login_email_notification">Customer Login Email Notification</label>
                                        <select name="login_email_notification" class="form-select form-select-{{ formControlSize() }}" id="login_email_notification" required>
                                            <option value="yes" {{ old('login_email_notification', $settings->login_email_notification ?? '') === 'yes' ? 'selected' : '' }}>Yes</option>
                                            <option value="no" {{ old('login_email_notification', $settings->login_email_notification ?? '') === 'no' ? 'selected' : '' }}>No</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="admin-form-label" for="transaction_email_notification">Transaction Email Notification</label>
                                        <select name="transaction_email_notification" class="form-select form-select-{{ formControlSize() }}" id="transaction_email_notification" required>
                                            <option value="yes" {{ old('transaction_email_notification', $settings->transaction_email_notification ?? '') === 'yes' ? 'selected' : '' }}>Yes</option>
                                            <option value="no" {{ old('transaction_email_notification', $settings->transaction_email_notification ?? '') === 'no' ? 'selected' : '' }}>No</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="admin-form-label" for="allow_fund_with_card">Allow Wallet Funding With Card</label>
                                        <select name="allow_fund_with_card" class="form-select form-select-{{ formControlSize() }}" id="allow_fund_with_card">
                                            <option value="yes" {{ old('allow_fund_with_card', $settings->allow_fund_with_card ?? '') === 'yes' ? 'selected' : '' }}>Yes</option>
                                            <option value="no" {{ old('allow_fund_with_card', $settings->allow_fund_with_card ?? '') === 'no' ? 'selected' : '' }}>No</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="admin-form-label" for="card_funding_extra_charge">Card Funding Extra Charge ({{ getSettings()->currency }})</label>
                                        <input type="number" step="0.01" class="form-control form-control-{{ formControlSize() }}" id="card_funding_extra_charge" name="card_funding_extra_charge" value="{{ old('card_funding_extra_charge', $settings->card_funding_extra_charge ?? '') }}" placeholder="0.00">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="admin-form-label" for="allow_fund_with_reserved_account">Allow Wallet Funding With Reserved Account</label>
                                        <select name="allow_fund_with_reserved_account" class="form-select form-select-{{ formControlSize() }}" id="allow_fund_with_reserved_account">
                                            <option value="yes" {{ old('allow_fund_with_reserved_account', $settings->allow_fund_with_reserved_account ?? '') === 'yes' ? 'selected' : '' }}>Yes</option>
                                            <option value="no" {{ old('allow_fund_with_reserved_account', $settings->allow_fund_with_reserved_account ?? '') === 'no' ? 'selected' : '' }}>No</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="admin-form-label" for="referral_system_status">Referral System Status</label>
                                        <select name="referral_system_status" class="form-select form-select-{{ formControlSize() }}" id="referral_system_status">
                                            <option value="active" {{ old('referral_system_status', $settings->referral_system_status ?? '') === 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ old('referral_system_status', $settings->referral_system_status ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="admin-form-label" for="captcha_settings_status">Allow Security Captcha on forms</label>
                                        <select name="captcha_settings_status" class="form-select form-select-{{ formControlSize() }}" id="captcha_settings_status">
                                            <option value="yes" {{ isset($settings->captcha_settings['captcha_settings_status']) && $settings->captcha_settings['captcha_settings_status'] === 'yes' ? 'selected' : '' }}>Yes</option>
                                            <option value="no" {{ isset($settings->captcha_settings['captcha_settings_status']) && $settings->captcha_settings['captcha_settings_status'] === 'no' ? 'selected' : '' }}>No</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="admin-form-label" for="captcha_settings_provider">Security Captcha Provider</label>
                                        <select name="captcha_settings_provider" class="form-select form-select-{{ formControlSize() }}" id="captcha_settings_provider">
                                            <option value="simple" {{ isset($settings->captcha_settings['captcha_settings_provider']) && $settings->captcha_settings['captcha_settings_provider'] === 'simple' ? 'selected' : '' }}>Simple Captcha</option>
                                            <option value="google" {{ isset($settings->captcha_settings['captcha_settings_provider']) && $settings->captcha_settings['captcha_settings_provider'] === 'google' ? 'selected' : '' }}>Google Captcha</option>
                                            <option value="all" {{ isset($settings->captcha_settings['captcha_settings_provider']) && $settings->captcha_settings['captcha_settings_provider'] === 'all' ? 'selected' : '' }}>All</option>
                                        </select>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="admin-form-label" for="RECAPTCHA_SITE_KEY">Google Captcha Site Key</label>
                                        <input type="text" class="form-control form-control-{{ formControlSize() }}" name="RECAPTCHA_SITE_KEY" value="{{ $settings->captcha_settings['google']['RECAPTCHA_SITE_KEY'] ?? old('RECAPTCHA_SITE_KEY') }}" placeholder="Site key">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="admin-form-label" for="RECAPTCHA_SECRET_KEY">Google Captcha Secret Key</label>
                                        <input type="text" class="form-control form-control-{{ formControlSize() }}" name="RECAPTCHA_SECRET_KEY" value="{{ $settings->captcha_settings['google']['RECAPTCHA_SECRET_KEY'] ?? old('RECAPTCHA_SECRET_KEY') }}" placeholder="Secret key">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="admin-settings-card card mb-4">
                            <div class="card-header">
                                <h3>Payment gateways</h3>
                                <p>Select the active gateways that power wallet funding.</p>
                            </div>
                            <div class="card-body">
                                <label class="admin-form-label" for="payment_gateway">Active gateways</label>
                                <select name="payment_gateway[]" class="form-select admin-gateway-select" id="payment_gateway" required multiple data-placeholder="Select gateways">
                                    @foreach($payment_gateways as $gateway)
                                        <option value="{{ $gateway->id }}" {{ in_array($gateway->id, $selectedGateways) ? 'selected' : '' }}>
                                            {{ $gateway->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="admin-note mt-3 mb-0">Keep the active providers small and intentional for a cleaner payment flow.</p>
                            </div>
                        </div>

                        <div class="admin-settings-card card mb-4">
                            <div class="card-header">
                                <h3>SEO and integrations</h3>
                                <p>Keep your public metadata, support link, and ad code in one place.</p>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="admin-form-label" for="seo_title">SEO Title</label>
                                        <input type="text" class="form-control form-control-{{ formControlSize() }}" id="seo_title" name="seo_title" value="{{ old('seo_title', $settings->seo_title ?? '') }}" placeholder="SEO title">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="admin-form-label" for="support_link">Support Link</label>
                                        <input type="text" class="form-control form-control-{{ formControlSize() }}" id="support_link" name="support_link" value="{{ old('support_link', $settings->support_link ?? '') }}" placeholder="Support link">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="admin-form-label" for="api_documentation_link">API Documentation Link</label>
                                        <div class="input-group admin-copy-group">
                                            <input type="text" class="form-control form-control-{{ formControlSize() }}" id="api_documentation_link" name="api_documentation_link" value="{{ old('api_documentation_link', $settings->api_documentation_link ?? '') }}" placeholder="API documentation link">
                                            <button type="button" class="btn btn-outline-secondary" data-copy-target="api_documentation_link">
                                                Copy
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="admin-form-label" for="seo_description">SEO Description</label>
                                        <textarea class="form-control" id="seo_description" rows="4" name="seo_description" placeholder="SEO description" required>{{ old('seo_description', $settings->seo_description ?? '') }}</textarea>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="admin-form-label" for="google_ad_code">Google Ad Code</label>
                                        <textarea class="form-control" id="google_ad_code" rows="4" name="google_ad_code" placeholder="Google ad code">{{ old('google_ad_code', $settings->google_ad_code ?? '') }}</textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="admin-form-label" for="allow_google_ad">Allow Google Ad</label>
                                        <select name="allow_google_ad" class="form-select form-select-{{ formControlSize() }}" id="allow_google_ad">
                                            <option value="yes" {{ old('allow_google_ad', $settings->allow_google_ad ?? 'no') === 'yes' ? 'selected' : '' }}>Yes</option>
                                            <option value="no" {{ old('allow_google_ad', $settings->allow_google_ad ?? 'no') === 'no' ? 'selected' : '' }}>No</option>
                                        </select>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="admin-form-label" for="google_dashboard_ad_code">Google Dashboard Ad Code</label>
                                        <textarea class="form-control" id="google_dashboard_ad_code" rows="5" name="google_dashboard_ad_code" placeholder="Google dashboard ad code">{{ old('google_dashboard_ad_code', $settings->google_dashboard_ad_code ?? '') }}</textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="admin-form-label" for="allow_google_dashboard_ad">Allow Google Dashboard Ad</label>
                                        <select name="allow_google_dashboard_ad" class="form-select form-select-{{ formControlSize() }}" id="allow_google_dashboard_ad">
                                            <option value="yes" {{ old('allow_google_dashboard_ad', $settings->allow_google_dashboard_ad ?? 'no') === 'yes' ? 'selected' : '' }}>Yes</option>
                                            <option value="no" {{ old('allow_google_dashboard_ad', $settings->allow_google_dashboard_ad ?? 'no') === 'no' ? 'selected' : '' }}>No</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="admin-settings-card card">
                            <div class="card-header">
                                <h3>Platform notes</h3>
                                <p>Use the editor to manage the default notes shown during wallet funding flows.</p>
                            </div>
                            <div class="card-body">
                                <div class="row g-4">
                                    <div class="col-12">
                                        <div class="admin-note-section">
                                            <div class="admin-note-section__heading">
                                                <span>Bank Transfer Note</span>
                                                <small>Shown on bank transfer funding instructions</small>
                                            </div>
                                            <div id="toolbar-bank_transfer_note">
                                                <span class="ql-formats">
                                                    <select class="ql-font"></select>
                                                    <select class="ql-size"></select>
                                                </span>
                                                <span class="ql-formats">
                                                    <button class="ql-bold"></button>
                                                    <button class="ql-italic"></button>
                                                    <button class="ql-underline"></button>
                                                    <button class="ql-strike"></button>
                                                </span>
                                                <span class="ql-formats">
                                                    <select class="ql-color"></select>
                                                    <select class="ql-background"></select>
                                                </span>
                                                <span class="ql-formats">
                                                    <button class="ql-script" value="sub"></button>
                                                    <button class="ql-script" value="super"></button>
                                                </span>
                                                <span class="ql-formats">
                                                    <button class="ql-header" value="1"></button>
                                                    <button class="ql-header" value="2"></button>
                                                    <button class="ql-blockquote"></button>
                                                    <button class="ql-code-block"></button>
                                                </span>
                                                <span class="ql-formats">
                                                    <button class="ql-list" value="ordered"></button>
                                                    <button class="ql-list" value="bullet"></button>
                                                    <button class="ql-indent" value="-1"></button>
                                                    <button class="ql-indent" value="+1"></button>
                                                </span>
                                                <span class="ql-formats">
                                                    <button class="ql-direction" value="rtl"></button>
                                                    <select class="ql-align"></select>
                                                </span>
                                                <span class="ql-formats">
                                                    <button class="ql-link"></button>
                                                    <button class="ql-image"></button>
                                                    <button class="ql-video"></button>
                                                    <button class="ql-formula"></button>
                                                </span>
                                                <span class="ql-formats">
                                                    <button class="ql-clean"></button>
                                                </span>
                                            </div>
                                            <div id="editor-bank_transfer_note" class="admin-editor" data-target="bank_transfer_note"></div>
                                            <input name="bank_transfer_note" type="hidden" id="bank_transfer_note" value="{{ old('bank_transfer_note', $settings->bank_transfer_note ?? '') }}" />
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="admin-note-section">
                                            <div class="admin-note-section__heading">
                                                <span>Wallet Funding Note</span>
                                                <small>Shown on wallet funding instructions</small>
                                            </div>
                                            <div id="toolbar-wallet_funding_note">
                                                <span class="ql-formats">
                                                    <select class="ql-font"></select>
                                                    <select class="ql-size"></select>
                                                </span>
                                                <span class="ql-formats">
                                                    <button class="ql-bold"></button>
                                                    <button class="ql-italic"></button>
                                                    <button class="ql-underline"></button>
                                                    <button class="ql-strike"></button>
                                                </span>
                                                <span class="ql-formats">
                                                    <select class="ql-color"></select>
                                                    <select class="ql-background"></select>
                                                </span>
                                                <span class="ql-formats">
                                                    <button class="ql-script" value="sub"></button>
                                                    <button class="ql-script" value="super"></button>
                                                </span>
                                                <span class="ql-formats">
                                                    <button class="ql-header" value="1"></button>
                                                    <button class="ql-header" value="2"></button>
                                                    <button class="ql-blockquote"></button>
                                                    <button class="ql-code-block"></button>
                                                </span>
                                                <span class="ql-formats">
                                                    <button class="ql-list" value="ordered"></button>
                                                    <button class="ql-list" value="bullet"></button>
                                                    <button class="ql-indent" value="-1"></button>
                                                    <button class="ql-indent" value="+1"></button>
                                                </span>
                                                <span class="ql-formats">
                                                    <button class="ql-direction" value="rtl"></button>
                                                    <select class="ql-align"></select>
                                                </span>
                                                <span class="ql-formats">
                                                    <button class="ql-link"></button>
                                                    <button class="ql-image"></button>
                                                    <button class="ql-video"></button>
                                                    <button class="ql-formula"></button>
                                                </span>
                                                <span class="ql-formats">
                                                    <button class="ql-clean"></button>
                                                </span>
                                            </div>
                                            <div id="editor-wallet_funding_note" class="admin-editor" data-target="wallet_funding_note"></div>
                                            <input name="wallet_funding_note" type="hidden" id="wallet_funding_note" value="{{ old('wallet_funding_note', $settings->wallet_funding_note ?? '') }}" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-4">
                        <div class="admin-settings-footer">
                            <button class="btn btn-admin-submit" type="submit">Update Settings</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('page-script')
    <script src="{{ asset('modern-assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.0-rc.2/dist/quill.js"></script>
    <script>
        $('.admin-gateway-select').each(function () {
            const $select = $(this);
            $select.wrap('<div class="position-relative"></div>').select2({
                placeholder: $select.data('placeholder') || 'Select gateways',
                dropdownParent: $select.parent(),
                width: '100%'
            });
        });

        document.querySelectorAll('[data-copy-target]').forEach(function (button) {
            button.addEventListener('click', async function () {
                const targetId = this.getAttribute('data-copy-target');
                const input = document.getElementById(targetId);
                if (!input) {
                    return;
                }

                try {
                    await navigator.clipboard.writeText(input.value || '');
                    const original = this.textContent;
                    this.textContent = 'Copied';
                    setTimeout(() => {
                        this.textContent = original;
                    }, 1200);
                } catch (error) {
                    input.select();
                    document.execCommand('copy');
                }
            });
        });

        document.querySelectorAll('[data-target]').forEach(function (editorElement) {
            const target = editorElement.getAttribute('data-target');
            const toolbar = document.getElementById('toolbar-' + target);
            const hiddenInput = document.getElementById(target);
            const quill = new Quill('#editor-' + target, {
                theme: 'snow',
                placeholder: 'Enter note...',
                modules: {
                    toolbar: toolbar
                }
            });

            if (hiddenInput) {
                if (hiddenInput.value.trim() !== '') {
                    quill.root.innerHTML = hiddenInput.value;
                } else {
                    hiddenInput.value = quill.root.innerHTML;
                }
            }

            quill.on('text-change', function () {
                hiddenInput.value = quill.root.innerHTML;
            });
        });
    </script>
@endsection
