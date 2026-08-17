@extends('sneat.layouts.app')

@section('title', $api ? ('Edit ' . $api->name) : 'Add API Provider')

@section('content')
    @php
        $isEdit = !empty($api?->id);
        $pageTitle = $isEdit ? 'Edit ' . data_get($api, 'name') : 'Add API Provider';
        $formAction = $isEdit ? route('api.update', $api->id) : route('api.store');
        $summarySlug = old('slug', data_get($api, 'slug', ''));
        $summaryStatus = old('status', data_get($api, 'status', 'inactive'));
        $summaryFile = old('file_name', data_get($api, 'file_name', ''));
        $canPullProducts = (bool) ($canPullProducts ?? false);
    @endphp

    @section('page-css')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <style>
            .provider-pull-btn {
                margin-top: .6rem;
            }

            .provider-pull-btn .btn {
                border-radius: 999px;
                padding-inline: 1rem;
            }

            .pull-products-modal .select2-container {
                width: 100% !important;
            }
        </style>
    @endsection

    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="admin-page-hero mb-4">
                <div>
                    <span class="admin-page-hero__kicker">Catalogue</span>
                    <h1>{{ $pageTitle }}</h1>
                    <p>Keep provider keys, URLs, and status controls tidy in the same modern admin shell.</p>
                </div>
                <div class="admin-page-badges">
                    <div class="admin-page-badge">
                        <span>Slug</span>
                        <strong>{{ $summarySlug ?: 'Not set' }}</strong>
                    </div>
                    <div class="admin-page-badge">
                        <span>Status</span>
                        <strong>{{ ucfirst($summaryStatus) }}</strong>
                        @if($isEdit && $canPullProducts)
                            <div class="provider-pull-btn">
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#pullProductsModal">
                                    <i class="bx bx-download me-50"></i> Pull products
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @include('sneat.layouts.alerts')

            <form action="{{ $formAction }}" method="POST">
                @csrf
                @if($isEdit)
                    @method('PATCH')
                @endif

                <div class="row g-4">
                    <div class="col-xl-7">
                        <div class="modern-admin-card card h-100">
                            <div class="card-header">
                                <h3>Provider identity</h3>
                                <p>Core provider details used throughout the catalogue.</p>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="name" class="modern-admin-label">Name</label>
                                        <input type="text" class="form-control form-control-{{ formControlSize() }}" id="name" name="name" value="{{ old('name', data_get($api, 'name', '')) }}" placeholder="Enter provider name" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="slug" class="modern-admin-label">Slug</label>
                                        <input type="text" class="form-control form-control-{{ formControlSize() }}" id="slug" name="slug" value="{{ old('slug', data_get($api, 'slug', '')) }}" placeholder="Enter provider slug" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="status" class="modern-admin-label">Status</label>
                                        <select class="form-select form-select-{{ formControlSize() }}" id="status" name="status" required>
                                            <option value="">Select status</option>
                                            <option value="active" @selected(old('status', $api->status ?? '') === 'active')>Active</option>
                                            <option value="inactive" @selected(old('status', $api->status ?? '') === 'inactive')>Inactive</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="file_name" class="modern-admin-label">File Name</label>
                                        <input type="text" class="form-control form-control-{{ formControlSize() }}" id="file_name" name="file_name" value="{{ old('file_name', data_get($api, 'file_name', '')) }}" placeholder="Enter provider controller file" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="warning_threshold_status" class="modern-admin-label">Warning Threshold Status</label>
                                        <select class="form-select form-select-{{ formControlSize() }}" id="warning_threshold_status" name="warning_threshold_status">
                                            <option value="">Select status</option>
                                            <option value="active" @selected(old('warning_threshold_status', data_get($api, 'warning_threshold_status', '')) === 'active')>Active</option>
                                            <option value="inactive" @selected(old('warning_threshold_status', data_get($api, 'warning_threshold_status', '')) === 'inactive')>Inactive</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="warning_threshold" class="modern-admin-label">Balance Warning Threshold</label>
                                        <input type="number" class="form-control form-control-{{ formControlSize() }}" id="warning_threshold" name="warning_threshold" value="{{ old('warning_threshold', data_get($api, 'warning_threshold', '')) }}" placeholder="Enter warning threshold">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-5">
                        <div class="modern-admin-card card h-100">
                            <div class="card-header">
                                <h3>Credentials and endpoints</h3>
                                <p>Provider keys and base URLs kept in one place.</p>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label for="api_key" class="modern-admin-label">API Key</label>
                                        <input type="text" class="form-control form-control-{{ formControlSize() }}" id="api_key" name="api_key" value="{{ old('api_key', data_get($api, 'api_key', '')) }}" placeholder="Enter API key">
                                    </div>
                                    <div class="col-12">
                                        <label for="secret_key" class="modern-admin-label">Secret Key</label>
                                        <input type="text" class="form-control form-control-{{ formControlSize() }}" id="secret_key" name="secret_key" value="{{ old('secret_key', data_get($api, 'secret_key', '')) }}" placeholder="Enter secret key">
                                    </div>
                                    <div class="col-12">
                                        <label for="public_key" class="modern-admin-label">Public Key</label>
                                        <input type="text" class="form-control form-control-{{ formControlSize() }}" id="public_key" name="public_key" value="{{ old('public_key', data_get($api, 'public_key', '')) }}" placeholder="Enter public key">
                                    </div>
                                    <div class="col-12">
                                        <label for="sandbox_base_url" class="modern-admin-label">Sandbox Base URL</label>
                                        <input type="text" class="form-control form-control-{{ formControlSize() }}" id="sandbox_base_url" name="sandbox_base_url" value="{{ old('sandbox_base_url', data_get($api, 'sandbox_base_url', '')) }}" placeholder="https://sandbox.example.com/">
                                    </div>
                                    <div class="col-12">
                                        <label for="live_base_url" class="modern-admin-label">Live Base URL</label>
                                        <input type="text" class="form-control form-control-{{ formControlSize() }}" id="live_base_url" name="live_base_url" value="{{ old('live_base_url', data_get($api, 'live_base_url', '')) }}" placeholder="https://api.example.com/">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modern-admin-footer mt-4">
                            <button class="btn btn-admin-submit" type="submit">{{ $isEdit ? 'Update provider' : 'Save provider' }}</button>
                            <a href="{{ route('api.index') }}" class="gateway-action">Back to providers</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($isEdit && $canPullProducts)
        <div class="modal fade pull-products-modal" id="pullProductsModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST" action="{{ route('api.pull.products') }}">
                        @csrf
                        <input type="hidden" name="api_id" value="{{ $api->id }}">

                        <div class="modal-header">
                            <div>
                                <h5 class="modal-title mb-0">Pull products</h5>
                                <small class="text-muted">Choose the target category for this provider pull.</small>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="pull_category_id" class="form-label">Category</label>
                                <select name="category_id" id="pull_category_id" class="form-select js-example-basic-single" data-placeholder="Search category">
                                    <option value="">Select category</option>
                                    @foreach($categories ?? [] as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}{{ filled($category->slug) ? ' (' . $category->slug . ')' : '' }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="pull_category_slug" class="form-label">Category slug</label>
                                <input type="text" class="form-control" id="pull_category_slug" name="category_slug" placeholder="Optional slug override">
                                <small class="text-muted">Use this only if the provider expects a slug different from the selected category.</small>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-refresh me-25"></i> Pull products
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('page-script')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            $('.js-example-basic-single').select2({
                dropdownParent: $('#pullProductsModal'),
                width: '100%',
            });
        });
    </script>
@endsection
