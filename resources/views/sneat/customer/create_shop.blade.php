@extends('sneat.layouts.app')

@section('title', 'Create Shop')

@php
    $user = auth()->user();
    $shopRequest = $user->customer?->shop_request;
    $shopStatus = $shopRequest->status ?? null;
    $currency = getSettings()->currency ?? '₦';
@endphp

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="profile-hero mb-4 gateway-hero">
                <div class="profile-hero__meta">
                    <div class="profile-avatar"><i class="bx bx-store"></i></div>
                    <div class="profile-meta">
                        <span class="gateway-hero__kicker">Multi-store</span>
                        <strong>{{ $shopRequest ? 'Shop request' : 'Create new shop' }}</strong>
                        <span>{{ $shopRequest ? 'Review the current request or shop details.' : 'Set up a new shop and send it for approval.' }}</span>
                    </div>
                </div>
                <div class="gateway-summary">
                    <div class="gateway-summary__card">
                        <span class="gateway-summary__label">Status</span>
                        <span class="gateway-summary__value">{{ ucfirst($shopStatus ?? 'Not submitted') }}</span>
                    </div>
                    <div class="gateway-summary__card">
                        <span class="gateway-summary__label">Email</span>
                        <span class="gateway-summary__value">{{ $user->email }}</span>
                    </div>
                </div>
            </div>

            @include('sneat.layouts.alerts')

            @if(empty($shopRequest))
                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="card profile-card">
                            <div class="card-header">
                                <h3>Shop details</h3>
                                <p>Tell us about the store you want to create.</p>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('customer.shop.store') }}" method="POST" autocomplete="off">
                                    @csrf
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="profile-label" for="shop_name">Shop name</label>
                                            <input type="text" name="shop_name" id="shop_name" class="form-control form-control-{{ formControlSize() }}" value="{{ old('shop_name') }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="profile-label" for="shop_slug">Shop slug</label>
                                            <input type="text" name="shop_slug" id="shop_slug" class="form-control form-control-{{ formControlSize() }}" value="{{ old('shop_slug', $user->username) }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="profile-label" for="currency">Currency</label>
                                            <select name="currency" id="currency" class="form-select form-select-{{ formControlSize() }}" required>
                                                <option value="">Select currency</option>
                                                @foreach($currencies as $currencyOption)
                                                    <option value="{{ $currencyOption }}" @selected(old('currency') === $currencyOption)>{{ $currencyOption }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="profile-label" for="official_email">Official email</label>
                                            <input type="email" name="official_email" id="official_email" class="form-control form-control-{{ formControlSize() }}" value="{{ old('official_email') }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="profile-label" for="whatsapp_number">WhatsApp number</label>
                                            <input type="text" name="whatsapp_number" id="whatsapp_number" class="form-control form-control-{{ formControlSize() }}" value="{{ old('whatsapp_number') }}" required>
                                        </div>
                                    </div>

                                    <div class="row g-3 mt-2">
                                        <div class="col-md-12">
                                            <div class="profile-side-card">
                                                <div class="profile-side-row">
                                                    <span>Admin email</span>
                                                    <strong>{{ $user->email }}</strong>
                                                </div>
                                                <div class="profile-side-row">
                                                    <span>Admin name</span>
                                                    <strong>{{ $user->firstname }} {{ $user->lastname }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="profile-label" for="first_name">First name</label>
                                            <input type="text" name="first_name" id="first_name" class="form-control form-control-{{ formControlSize() }}" value="{{ old('first_name', $user->firstname) }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="profile-label" for="last_name">Last name</label>
                                            <input type="text" name="last_name" id="last_name" class="form-control form-control-{{ formControlSize() }}" value="{{ old('last_name', $user->lastname) }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="profile-label" for="phone">Phone</label>
                                            <input type="text" name="phone" id="phone" class="form-control form-control-{{ formControlSize() }}" value="{{ old('phone', $user->phone) }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="profile-label" for="password">Password</label>
                                            <input type="text" name="password" id="password" class="form-control form-control-{{ formControlSize() }}" value="{{ old('password') }}" placeholder="Leave blank to use current password">
                                        </div>
                                    </div>

                                    <div class="profile-footer mt-4">
                                        <button type="submit" class="btn btn-admin-submit">Submit request</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card profile-card h-100">
                            <div class="card-header">
                                <h3>What happens next</h3>
                                <p>Your request will be reviewed by the admin team.</p>
                            </div>
                            <div class="card-body">
                                <div class="d-flex gap-3 mb-3">
                                    <span class="avatar-initial rounded bg-label-primary p-3"><i class="bx bx-pencil fs-4"></i></span>
                                    <div>
                                        <h6 class="mb-1">Submit details</h6>
                                        <div class="gateway-helper">We’ll use these values to create your store.</div>
                                    </div>
                                </div>
                                <div class="d-flex gap-3 mb-3">
                                    <span class="avatar-initial rounded bg-label-success p-3"><i class="bx bx-check-circle fs-4"></i></span>
                                    <div>
                                        <h6 class="mb-1">Approval</h6>
                                        <div class="gateway-helper">The request will be checked before it goes live.</div>
                                    </div>
                                </div>
                                <div class="d-flex gap-3">
                                    <span class="avatar-initial rounded bg-label-warning p-3"><i class="bx bx-link fs-4"></i></span>
                                    <div>
                                        <h6 class="mb-1">Go live</h6>
                                        <div class="gateway-helper">You’ll receive the shop link after approval.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($shopStatus === 'approved')
                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="card profile-card">
                            <div class="card-header">
                                <h3>Approved shop</h3>
                                <p>Your shop is active and ready to share.</p>
                            </div>
                            <div class="card-body">
                                <div class="profile-side-card mb-4">
                                    <div class="profile-side-row">
                                        <span>Shop name</span>
                                        <strong>{{ $shopRequest->request_details['shop_name'] ?? 'N/A' }}</strong>
                                    </div>
                                    <div class="profile-side-row">
                                        <span>Shop slug</span>
                                        <strong>{{ $shopRequest->request_details['shop_slug'] ?? 'N/A' }}</strong>
                                    </div>
                                    <div class="profile-side-row">
                                        <span>Status</span>
                                        <strong>{{ ucfirst($shopRequest->shop_status ?? 'active') }}</strong>
                                    </div>
                                </div>

                                <label class="profile-label">Shop link</label>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control form-control-{{ formControlSize() }}" id="shop-link" value="{{ env('SHOPS_BASE_URL') . ($shopRequest->request_details['shop_slug'] ?? '') }}" readonly>
                                    <button class="btn btn-label-primary" type="button" id="copy-shop-link">Copy</button>
                                    <a href="{{ env('SHOPS_BASE_URL') . ($shopRequest->request_details['shop_slug'] ?? '') }}" class="btn btn-admin-submit" target="_blank">Visit shop</a>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="profile-label">Subscription start</label>
                                        <input type="text" class="form-control form-control-{{ formControlSize() }}" value="{{ $shopRequest->request_details['subscription_start'] ?? 'N/A' }}" disabled>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="profile-label">Subscription end</label>
                                        <input type="text" class="form-control form-control-{{ formControlSize() }}" value="{{ $shopRequest->request_details['subscription_end'] ?? 'N/A' }}" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card profile-card h-100">
                            <div class="card-header">
                                <h3>Store state</h3>
                                <p>Keep the shop link handy for sharing.</p>
                            </div>
                            <div class="card-body">
                                <div class="profile-side-row">
                                    <span>Currency</span>
                                    <strong>{{ $shopRequest->request_details['currency'] ?? $currency }}</strong>
                                </div>
                                <div class="profile-side-row">
                                    <span>Official email</span>
                                    <strong>{{ $shopRequest->request_details['official_email'] ?? $user->email }}</strong>
                                </div>
                                <div class="profile-side-row">
                                    <span>WhatsApp</span>
                                    <strong>{{ $shopRequest->request_details['whatsapp_number'] ?? $user->phone }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="card profile-card">
                    <div class="card-body">
                        <div class="alert alert-warning mb-0">
                            Your shop creation request is undergoing review. Please check back later.
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        document.getElementById('copy-shop-link')?.addEventListener('click', async function () {
            const input = document.getElementById('shop-link');
            if (!input) return;

            try {
                await navigator.clipboard.writeText(input.value);
                this.textContent = 'Copied';
                setTimeout(() => this.textContent = 'Copy', 1500);
            } catch (error) {
                alert('Unable to copy link');
            }
        });
    </script>
@endsection
