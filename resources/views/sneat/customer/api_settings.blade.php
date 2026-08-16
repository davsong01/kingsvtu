@extends('sneat.layouts.app')

@section('title', 'API Settings')

@php
    $user = auth()->user();
    $settings = getSettings();
    $currency = $settings->currency ?? '₦';
    $kycStatus = strtolower((string) (getFinalKycStatus($user->customer->id) ?? 'pending'));
    $apiStatus = strtolower((string) ($user->customer->api_access ?? 'inactive'));
@endphp

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="profile-hero mb-4 gateway-hero">
                <div class="profile-hero__meta">
                    <div class="profile-avatar"><i class="bx bx-code-alt"></i></div>
                    <div class="profile-meta">
                        <span class="gateway-hero__kicker">Developer tools</span>
                        <strong>API Settings</strong>
                        <span>Manage your API key, secret key, and documentation access from one clean workspace.</span>
                    </div>
                </div>
                <div class="gateway-summary">
                    <div class="gateway-summary__card">
                        <span class="gateway-summary__label">API access</span>
                        <span class="gateway-summary__value">{{ ucfirst($apiStatus) }}</span>
                    </div>
                    <div class="gateway-summary__card">
                        <span class="gateway-summary__label">KYC</span>
                        <span class="gateway-summary__value">{{ formatKycStatusLabel($kycStatus) }}</span>
                    </div>
                    <div class="gateway-summary__card">
                        <span class="gateway-summary__label">Wallet</span>
                        <span class="gateway-summary__value">{{ $currency . number_format(walletBalance($user), 2) }}</span>
                    </div>
                </div>
            </div>

            @include('sneat.layouts.alerts')

            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="card profile-card h-100">
                        <div class="card-header">
                            <h3>API credentials</h3>
                            <p>Generate fresh keys when you need to reconnect an integration.</p>
                        </div>
                        <div class="card-body">
                            <div class="profile-side-card mb-4">
                                <div class="profile-side-row">
                                    <span>API access</span>
                                    <strong>{{ ucfirst($apiStatus) }}</strong>
                                </div>
                                <div class="profile-side-row">
                                    <span>API key</span>
                                    <strong>{{ $user->api_key }}</strong>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="customer-api-key">
                                        <div class="customer-api-key__label">API Key</div>
                                        <div class="customer-api-key__value" id="api-key-value">{{ $user->api_key }}</div>
                                        <button type="button" class="btn btn-primary btn-sm customer-api-copy" data-copy-target="#api-key-value">Copy</button>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="customer-api-key">
                                        <div class="customer-api-key__label">Public Key</div>
                                        <div class="customer-api-key__value" id="public-key-value">Generate a new key pair to view the public key.</div>
                                        <button type="button" class="btn btn-primary btn-sm customer-api-copy" data-copy-target="#public-key-value" disabled>Copy</button>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="customer-api-key">
                                        <div class="customer-api-key__label">Secret Key</div>
                                        <div class="customer-api-key__value" id="secret-key-value">Generate a new key pair to view the secret key.</div>
                                        <button type="button" class="btn btn-primary btn-sm customer-api-copy" data-copy-target="#secret-key-value" disabled>Copy</button>
                                    </div>
                                </div>
                            </div>

                            <div class="profile-footer mt-4">
                                <button type="button" class="btn btn-admin-submit" id="generate-api-keys">Generate new API keys</button>
                                @if(!empty($settings->api_documentation_link))
                                    <a href="{{ $settings->api_documentation_link }}" target="_blank" class="gateway-action">API documentation</a>
                                @else
                                    <span class="gateway-action disabled" aria-disabled="true">API documentation</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="card profile-card h-100">
                        <div class="card-header">
                            <h3>Before you connect</h3>
                        </div>
                        <div class="card-body">
                            <div class="customer-api-note">
                                <ul class="mb-0 ps-3">
                                    <li>Rotate keys when you move to a new integration environment.</li>
                                    <li>Use the copy buttons below each field when pasting into your app.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        (function () {
            const setCopiedLabel = (button, label = 'Copied') => {
                const original = button.textContent;
                button.textContent = label;
                button.disabled = true;

                window.setTimeout(() => {
                    button.textContent = original;
                    button.disabled = false;
                }, 1400);
            };

            document.querySelectorAll('.customer-api-copy').forEach((button) => {
                button.addEventListener('click', async () => {
                    const target = document.querySelector(button.getAttribute('data-copy-target'));
                    const text = target ? target.textContent.trim() : '';

                    if (!text) {
                        return;
                    }

                    try {
                        await navigator.clipboard.writeText(text);
                        setCopiedLabel(button);
                    } catch (error) {
                        const fallback = document.createElement('textarea');
                        fallback.value = text;
                        document.body.appendChild(fallback);
                        fallback.select();
                        document.execCommand('copy');
                        document.body.removeChild(fallback);
                        setCopiedLabel(button);
                    }
                });
            });

            const generateButton = document.getElementById('generate-api-keys');
            generateButton?.addEventListener('click', async () => {
                try {
                    const response = await fetch('{{ route('profile.keys') }}', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });
                    const payload = await response.json();
                    const data = payload.data || {};

                    const apiKeyNode = document.getElementById('api-key-value');
                    const publicKeyNode = document.getElementById('public-key-value');
                    const secretKeyNode = document.getElementById('secret-key-value');

                    if (apiKeyNode && data.api_key) {
                        apiKeyNode.textContent = data.api_key;
                    }
                    if (publicKeyNode && data.public) {
                        publicKeyNode.textContent = data.public;
                    }
                    if (secretKeyNode && data.secret) {
                        secretKeyNode.textContent = data.secret;
                    }

                    document.querySelectorAll('[data-copy-target="#public-key-value"], [data-copy-target="#secret-key-value"]').forEach((button) => {
                        button.disabled = false;
                    });
                } catch (error) {
                    alert('Unable to generate API keys right now.');
                }
            });
        })();
    </script>
@endsection
