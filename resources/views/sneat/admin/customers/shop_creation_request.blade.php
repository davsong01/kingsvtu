@extends('sneat.layouts.app')

@section('title', 'Shop Creation Requests')

@section('content')
    @php
        $summary = [
            ['label' => 'Total Requests', 'value' => number_format($totalRequests), 'icon' => 'bx-store', 'tone' => 'blue'],
            ['label' => 'Pending', 'value' => number_format($pendingRequests), 'icon' => 'bx-time', 'tone' => 'amber'],
            ['label' => 'Approved', 'value' => number_format($approvedRequests), 'icon' => 'bx-check-circle', 'tone' => 'green'],
        ];
    @endphp

    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="admin-page-hero mb-4">
                <div>
                    <span class="admin-page-hero__kicker">Shop operations</span>
                    <h1>Shop Creation Requests</h1>
                    <p>Review merchant shop applications, update details, and approve or decline requests from a cleaner admin table.</p>
                </div>
                <div class="admin-page-badges">
                    @foreach($summary as $item)
                        <div class="admin-page-badge">
                            <span>{{ $item['label'] }}</span>
                            <strong>{{ $item['value'] }}</strong>
                        </div>
                    @endforeach
                </div>
            </div>

            @include('sneat.layouts.alerts')

            <div class="gateway-card card">
                <div class="card-header d-flex flex-column flex-lg-row align-items-lg-start justify-content-between gap-3">
                    <div>
                        <h3>Requests list</h3>
                        <p>Inspect customer and shop data, then take the next action without leaving the page.</p>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="gateway-badge gateway-badge--warning">Pending</span>
                        <span class="gateway-badge gateway-badge--active">Approved</span>
                        <span class="gateway-badge gateway-badge--danger">Declined</span>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('customer.shop.requests') }}" class="row g-3 align-items-end mb-4">
                        <div class="col-xl-5 col-lg-6">
                            <label class="modern-admin-label" for="search">Search</label>
                            <input
                                type="text"
                                class="form-control form-control-{{ formControlSize() }}"
                                id="search"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="Search customer, shop, email, or phone"
                            >
                        </div>
                        <div class="col-xl-3 col-lg-3">
                            <label class="modern-admin-label" for="status">Request status</label>
                            <select class="form-select form-select-{{ formControlSize() }}" id="status" name="status">
                                <option value="">All statuses</option>
                                <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                                <option value="approved" @selected(request('status') === 'approved')>Approved</option>
                                <option value="declined" @selected(request('status') === 'declined')>Declined</option>
                            </select>
                        </div>
                        <div class="col-xl-2 col-lg-3">
                            <label class="modern-admin-label" for="shop_status">Shop status</label>
                            <select class="form-select form-select-{{ formControlSize() }}" id="shop_status" name="shop_status">
                                <option value="">All</option>
                                <option value="active" @selected(request('shop_status') === 'active')>Active</option>
                                <option value="inactive" @selected(request('shop_status') === 'inactive')>Inactive</option>
                            </select>
                        </div>
                        <div class="col-xl-2 col-lg-12 d-flex gap-2">
                            <button type="submit" class="btn btn-admin-submit flex-grow-1">Filter</button>
                            <a href="{{ route('customer.shop.requests') }}" class="gateway-action">Reset</a>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table gateway-table align-middle">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Customer</th>
                                    <th>Merchant Details</th>
                                    <th>Shop Details</th>
                                    <th>Status</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($requests as $request)
                                    @php
                                        $details = $request->request_details ?? [];
                                        $customerName = trim(data_get($request, 'customer.user.firstname', '') . ' ' . data_get($request, 'customer.user.lastname', '')) ?: 'Unnamed customer';
                                        $customerEmail = data_get($request, 'customer.user.email', 'No email');
                                        $customerPhone = data_get($request, 'customer.user.phone', 'No phone');
                                        $merchantName = trim(($details['first_name'] ?? '') . ' ' . ($details['last_name'] ?? '')) ?: 'N/A';
                                        $merchantEmail = $details['official_email'] ?? ($details['email'] ?? 'N/A');
                                        $merchantPhone = $details['phone'] ?? 'N/A';
                                        $shopName = $details['shop_name'] ?? 'N/A';
                                        $shopSlug = $details['shop_slug'] ?? null;
                                        $currency = $details['currency'] ?? (getSettings()->currency ?? '₦');
                                        $subscriptionStart = !empty($details['subscription_start']) ? date('M j, Y', strtotime($details['subscription_start'])) : 'Not set';
                                        $subscriptionEnd = !empty($details['subscription_end']) ? date('M j, Y', strtotime($details['subscription_end'])) : 'Not set';
                                        $requestStatus = strtolower((string) ($request->status ?? 'pending'));
                                        $requestStatusClass = match ($requestStatus) {
                                            'approved' => 'gateway-badge--active',
                                            'declined' => 'gateway-badge--danger',
                                            default => 'gateway-badge--warning',
                                        };
                                        $shopStatus = strtolower((string) ($request->shop_status ?? 'inactive'));
                                        $shopStatusClass = $shopStatus === 'active' ? 'gateway-badge--active' : 'gateway-badge--inactive';
                                        $serialNumber = $requests->firstItem() + $loop->index;
                                        $shopUrl = $shopSlug ? rtrim(env('SHOPS_BASE_URL'), '/') . '/' . ltrim($shopSlug, '/') : null;
                                    @endphp
                                    <tr>
                                        <td>{{ $serialNumber }}</td>
                                        <td>
                                            <div class="gateway-name">
                                                <a href="{{ route('customers.edit', data_get($request, 'customer.user.id')) }}">{{ $customerName }}</a>
                                            </div>
                                            <div class="gateway-helper">Email: {{ $customerEmail }}</div>
                                            <div class="gateway-helper">Phone: {{ $customerPhone }}</div>
                                        </td>
                                        <td>
                                            <div class="gateway-helper"><strong>Name:</strong> {{ $merchantName }}</div>
                                            <div class="gateway-helper"><strong>Email:</strong> {{ $merchantEmail }}</div>
                                            <div class="gateway-helper"><strong>Phone:</strong> {{ $merchantPhone }}</div>
                                        </td>
                                        <td>
                                            <div class="gateway-name">{{ $shopName }}</div>
                                            <div class="gateway-helper"><strong>Slug:</strong> {{ $shopSlug ?? 'N/A' }}</div>
                                            <div class="gateway-helper"><strong>Currency:</strong> {{ $currency }}</div>
                                            <div class="gateway-helper"><strong>Subscription:</strong> {{ $subscriptionStart }} to {{ $subscriptionEnd }}</div>
                                            <div class="gateway-helper"><strong>Shop URL:</strong> {{ $shopUrl ?? 'N/A' }}</div>
                                            <div class="gateway-helper"><span class="gateway-badge {{ $shopStatusClass }}">{{ ucfirst($shopStatus) }}</span></div>
                                        </td>
                                        <td>
                                            <span class="gateway-badge {{ $requestStatusClass }}">{{ ucfirst($requestStatus) }}</span>
                                        </td>
                                        <td class="text-end">
                                            <div class="gateway-row-actions justify-content-end">
                                                <button type="button" class="gateway-action" data-bs-toggle="modal" data-bs-target="#shop-request-{{ $request->id }}">
                                                    Update
                                                </button>
                                                @if($requestStatus === 'pending')
                                                    <a href="{{ route('approve.shop.requests', $request->id) }}" onclick="return confirm('Approve this shop request?');" class="gateway-action">Approve</a>
                                                    <a href="{{ route('decline.shop.requests', $request->id) }}" onclick="return confirm('Decline this shop request?');" class="gateway-action gateway-action--danger">Decline</a>
                                                @endif
                                                @if($requestStatus === 'approved' && $shopUrl)
                                                    <a href="{{ $shopUrl }}" target="_blank" class="gateway-action">Visit shop</a>
                                                    <a href="{{ route('shop.access', $request->id) }}" target="_blank" class="gateway-action">Access shop</a>
                                                @endif
                                                <a href="{{ route('delete.shop.requests', $request->id) }}" onclick="return confirm('Delete this request?');" class="gateway-action gateway-action--danger">Delete</a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6">
                                            <div class="alert alert-light border mb-0">No shop creation requests available.</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @foreach($requests as $request)
                        @php
                            $details = $request->request_details ?? [];
                        @endphp
                        <div class="modal fade" id="shop-request-{{ $request->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <div>
                                            <h5 class="modal-title">Update shop request</h5>
                                            <p class="mb-0 text-muted">Adjust the request details and shop status.</p>
                                        </div>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('update.shop.requests', $request->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="shop-request-modal-grid">
                                                <div class="row g-3">
                                                    <div class="col-md-4">
                                                        <label class="modern-admin-label" for="official_email-{{ $request->id }}">Official Email</label>
                                                        <input type="email" class="form-control form-control-{{ formControlSize() }}" id="official_email-{{ $request->id }}" name="official_email" value="{{ old('official_email', $details['official_email'] ?? '') }}" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="modern-admin-label" for="phone-{{ $request->id }}">Phone</label>
                                                        <input type="text" class="form-control form-control-{{ formControlSize() }}" id="phone-{{ $request->id }}" name="phone" value="{{ old('phone', $details['phone'] ?? '') }}" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="modern-admin-label" for="first_name-{{ $request->id }}">First name</label>
                                                        <input type="text" class="form-control form-control-{{ formControlSize() }}" id="first_name-{{ $request->id }}" name="first_name" value="{{ old('first_name', $details['first_name'] ?? '') }}" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="modern-admin-label" for="last_name-{{ $request->id }}">Last name</label>
                                                        <input type="text" class="form-control form-control-{{ formControlSize() }}" id="last_name-{{ $request->id }}" name="last_name" value="{{ old('last_name', $details['last_name'] ?? '') }}" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="modern-admin-label" for="shop_name-{{ $request->id }}">Shop name</label>
                                                        <input type="text" class="form-control form-control-{{ formControlSize() }}" id="shop_name-{{ $request->id }}" name="shop_name" value="{{ old('shop_name', $details['shop_name'] ?? '') }}" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="modern-admin-label" for="shop_slug-{{ $request->id }}">Shop slug</label>
                                                        <input type="text" class="form-control form-control-{{ formControlSize() }}" id="shop_slug-{{ $request->id }}" name="shop_slug" value="{{ old('shop_slug', $details['shop_slug'] ?? '') }}" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="modern-admin-label" for="whatsapp_number-{{ $request->id }}">Whatsapp number</label>
                                                        <input type="text" class="form-control form-control-{{ formControlSize() }}" id="whatsapp_number-{{ $request->id }}" name="whatsapp_number" value="{{ old('whatsapp_number', $details['whatsapp_number'] ?? '') }}" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="modern-admin-label" for="subscription_start-{{ $request->id }}">Subscription start</label>
                                                        <input type="date" class="form-control form-control-{{ formControlSize() }}" id="subscription_start-{{ $request->id }}" name="subscription_start" value="{{ old('subscription_start', $details['subscription_start'] ?? '') }}" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="modern-admin-label" for="subscription_end-{{ $request->id }}">Subscription end</label>
                                                        <input type="date" class="form-control form-control-{{ formControlSize() }}" id="subscription_end-{{ $request->id }}" name="subscription_end" value="{{ old('subscription_end', $details['subscription_end'] ?? '') }}" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="modern-admin-label" for="custom_domain-{{ $request->id }}">Custom domain</label>
                                                        <input type="text" class="form-control form-control-{{ formControlSize() }}" id="custom_domain-{{ $request->id }}" name="custom_domain" value="{{ old('custom_domain', $details['custom_domain'] ?? '') }}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="modern-admin-label" for="currency-{{ $request->id }}">Currency</label>
                                                        <input type="text" class="form-control form-control-{{ formControlSize() }}" id="currency-{{ $request->id }}" name="currency" value="{{ old('currency', $details['currency'] ?? (getSettings()->currency ?? '₦')) }}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="modern-admin-label" for="shop_status-{{ $request->id }}">Shop status</label>
                                                        <select class="form-select form-select-{{ formControlSize() }}" id="shop_status-{{ $request->id }}" name="shop_status" required>
                                                            <option value="active" @selected(old('shop_status', $request->shop_status) === 'active')>Active</option>
                                                            <option value="inactive" @selected(old('shop_status', $request->shop_status) === 'inactive')>Inactive</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer justify-content-start">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-admin-submit">Update request</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mt-4">
                        <div class="gateway-helper">
                            Showing {{ $requests->firstItem() ?? 0 }} to {{ $requests->lastItem() ?? 0 }} of {{ $requests->total() }} requests
                        </div>
                        {{ $requests->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
