@extends('sneat.layouts.app')

@section('title', 'Variations - ' . $product->name)

@section('content')
    @php
        $currency = getSettings()['currency'] ?? '₦';
        $discountType = data_get($product, 'category.discount_type', 'flat');
        $pullLabel = $variationCount > 0 ? 'Re-pull variations' : 'Pull variations';
    @endphp

    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="admin-page-hero mb-4">
                <div>
                    <span class="admin-page-hero__kicker">Catalogue</span>
                    <h1>Variations</h1>
                    <p>Manage the variation set for {{ $product->display_name }}</p>
                </div>
                <div class="admin-page-badges">
                    <div class="admin-page-badge">
                        <span>Product</span>
                        <strong>{{ $product->name }}</strong>
                    </div>
                    <div class="admin-page-badge">
                        <span>Variation count</span>
                        <strong>{{ number_format($variationCount) }}</strong>
                    </div>
                    <a href="{{ route('product.edit', $product->id) }}" class="btn btn-outline-secondary">Back to product</a>
                </div>
            </div>

            @include('sneat.layouts.alerts')

            <div class="row g-4">
                <div class="col-12">
                    <div class="modern-admin-card card">
                        <div class="card-header">
                            <h3>Product details</h3>
                            <p>Read-only product information for quick context.</p>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="modern-admin-label">Name</label>
                                    <input type="text" class="form-control form-control-{{ formControlSize() }}" value="{{ $product->name }}" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="modern-admin-label">Display Name</label>
                                    <input type="text" class="form-control form-control-{{ formControlSize() }}" value="{{ $product->display_name }}" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="modern-admin-label">Slug</label>
                                    <input type="text" class="form-control form-control-{{ formControlSize() }}" value="{{ $product->slug }}" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="modern-admin-label">Category</label>
                                    <input type="text" class="form-control form-control-{{ formControlSize() }}" value="{{ $product->category->name ?? 'N/A' }}" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="modern-admin-label">API</label>
                                    <input type="text" class="form-control form-control-{{ formControlSize() }}" value="{{ $product->api->name ?? 'N/A' }}" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="modern-admin-label">Status</label>
                                    <input type="text" class="form-control form-control-{{ formControlSize() }}" value="{{ ucfirst($product->status ?? 'inactive') }}" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="modern-admin-label">Has Variations</label>
                                    <input type="text" class="form-control form-control-{{ formControlSize() }}" value="{{ ucfirst($product->has_variations ?? 'no') }}" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="modern-admin-label">Fixed Price</label>
                                    <input type="text" class="form-control form-control-{{ formControlSize() }}" value="{{ ucfirst($product->fixed_price ?? 'no') }}" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="modern-admin-label">Allow Quantity</label>
                                    <input type="text" class="form-control form-control-{{ formControlSize() }}" value="{{ ucfirst($product->allow_quantity ?? 'no') }}" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="modern-admin-label">Allow Subscription Type</label>
                                    <input type="text" class="form-control form-control-{{ formControlSize() }}" value="{{ ucfirst($product->allow_subscription_type ?? 'no') }}" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="modern-admin-label">Server Code</label>
                                    <input type="text" class="form-control form-control-{{ formControlSize() }}" value="{{ $product->servercode ?? 'N/A' }}" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="modern-admin-label">System Price ({{ $currency }})</label>
                                    <input type="text" class="form-control form-control-{{ formControlSize() }}" value="{{ $product->system_price ?? '0' }}" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="modern-admin-label">Minimum Amount ({{ $currency }})</label>
                                    <input type="text" class="form-control form-control-{{ formControlSize() }}" value="{{ $product->min ?? '0' }}" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="modern-admin-label">Maximum Amount ({{ $currency }})</label>
                                    <input type="text" class="form-control form-control-{{ formControlSize() }}" value="{{ $product->max ?? '0' }}" disabled>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="modern-admin-card card">
                        <div class="card-header d-flex align-items-center justify-content-between gap-3">
                            <div>
                                <h3>Variation manager</h3>
                                <p>Edit the live rows inline, then append new ones before saving once.</p>
                                <div class="mt-2">
                                    <span class="badge bg-label-primary">Current API: {{ $product->api->name ?? 'N/A' }}</span>
                                </div>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('variations.pull', $product->id) }}" class="btn btn-outline-info">{{ $pullLabel }}</a>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($product->has_variations !== 'yes')
                                <div class="alert alert-light border mb-0">This product does not use variations.</div>
                            @else
                                @if($variationCount < 1)
                                    <div class="alert alert-warning border mb-4">
                                        No variations have been pulled yet. Use the pull button above or add rows below and save everything together.
                                    </div>
                                @endif

                                <form action="{{ route('variations.update', $product->id) }}" method="POST" enctype="multipart/form-data" id="variation-form">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                                    <div class="mb-3">
                                        <h4 class="mb-1">Variation rows</h4>
                                        <p class="mb-0 text-muted">Each row saves the existing variation or creates a new one if left without an ID.</p>
                                    </div>

                                    <div id="variation-rows" class="d-grid gap-3">
                                        @forelse($variations as $variation)
                                            @php
                                                $variationTitle = $variation->system_name ?: $variation->api_name ?: 'Variation #' . $variation->id;
                                                $canDelete = ($variation->transaction_count ?? 0) < 1;
                                            @endphp
                                            <div class="variation-card card border rounded-4 shadow-sm" data-variation-card data-existing="1" data-variation-id="{{ $variation->id }}">
                                                <div class="card-body">
                                                    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-3">
                                                        <div>
                                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                                <h5 class="mb-0">{{ $variationTitle }}</h5>
                                                                <span class="badge bg-label-{{ $variation->status === 'active' ? 'success' : 'secondary' }}">
                                                                    {{ ucfirst($variation->status ?? 'inactive') }}
                                                                </span>
                                                            </div>
                                                            <div class="text-muted small">Slug: {{ $variation->slug ?? 'N/A' }}</div>
                                                        </div>
                                                        <div class="d-flex align-items-center gap-2">
                                                            @if($canDelete)
                                                                <button
                                                                    type="button"
                                                                    class="btn btn-outline-danger btn-sm variation-delete-btn"
                                                                    data-delete-url="{{ route('variation.delete', $variation->id) }}"
                                                                    data-variation-title="{{ $variationTitle }}"
                                                                >
                                                                    Delete
                                                                </button>
                                                            @else
                                                                <span class="badge bg-label-warning">Locked by transactions</span>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <div class="row g-3">
                                                        <input type="hidden" name="variation_id[]" value="{{ $variation->id }}">

                                                        <div class="col-lg-3 col-md-6">
                                                            <label class="modern-admin-label" for="api_name_{{ $variation->id }}">API Name</label>
                                                            <input type="text" class="form-control form-control-{{ formControlSize() }}" id="api_name_{{ $variation->id }}" name="api_name[]" value="{{ $variation->api_name }}">
                                                        </div>
                                                        <div class="col-lg-3 col-md-6">
                                                            <label class="modern-admin-label" for="system_name_{{ $variation->id }}">System Name</label>
                                                            <input type="text" class="form-control form-control-{{ formControlSize() }}" id="system_name_{{ $variation->id }}" name="system_name[]" value="{{ $variation->system_name }}">
                                                        </div>
                                                        <div class="col-lg-3 col-md-6">
                                                            <label class="modern-admin-label" for="slug_{{ $variation->id }}">Slug</label>
                                                            <input type="text" class="form-control form-control-{{ formControlSize() }}" id="slug_{{ $variation->id }}" name="slug[]" value="{{ $variation->slug }}">
                                                        </div>
                                                        <div class="col-lg-3 col-md-6">
                                                            <label class="modern-admin-label" for="ussd_string_{{ $variation->id }}">USSD String</label>
                                                            <input type="text" class="form-control form-control-{{ formControlSize() }}" id="ussd_string_{{ $variation->id }}" name="ussd_string[]" value="{{ $variation->ussd_string }}">
                                                        </div>

                                                        <div class="col-lg-3 col-md-4">
                                                            <label class="modern-admin-label" for="api_price_{{ $variation->id }}">API Price ({{ $currency }})</label>
                                                            <input type="number" class="form-control form-control-{{ formControlSize() }}" id="api_price_{{ $variation->id }}" name="api_price[]" value="{{ $variation->api_price }}">
                                                        </div>
                                                        <div class="col-lg-3 col-md-4">
                                                            <label class="modern-admin-label" for="system_price_{{ $variation->id }}">System Price ({{ $currency }})</label>
                                                            <input type="number" class="form-control form-control-{{ formControlSize() }}" id="system_price_{{ $variation->id }}" name="system_price[]" value="{{ $variation->system_price }}">
                                                        </div>
                                                        <div class="col-lg-3 col-md-4">
                                                            <label class="modern-admin-label" for="datasize_{{ $variation->id }}">Datasize</label>
                                                            <input type="number" class="form-control form-control-{{ formControlSize() }}" id="datasize_{{ $variation->id }}" name="datasize[]" value="{{ $variation->datasize }}">
                                                        </div>
                                                        <div class="col-lg-3 col-md-4">
                                                            <label class="modern-admin-label" for="min_{{ $variation->id }}">Min Amount</label>
                                                            <input type="number" class="form-control form-control-{{ formControlSize() }}" id="min_{{ $variation->id }}" name="min[]" value="{{ $variation->min }}">
                                                        </div>
                                                        <div class="col-lg-3 col-md-4">
                                                            <label class="modern-admin-label" for="max_{{ $variation->id }}">Max Amount</label>
                                                            <input type="number" class="form-control form-control-{{ formControlSize() }}" id="max_{{ $variation->id }}" name="max[]" value="{{ $variation->max }}">
                                                        </div>
                                                        <div class="col-lg-3 col-md-4">
                                                            <label class="modern-admin-label" for="fixed_price_{{ $variation->id }}">Fixed Price</label>
                                                            <select class="form-select form-select-{{ formControlSize() }}" name="fixed_price[]" id="fixed_price_{{ $variation->id }}">
                                                                <option value="">Select</option>
                                                                <option value="Yes" {{ $variation->fixed_price == 'Yes' ? 'selected' : '' }}>Yes</option>
                                                                <option value="No" {{ $variation->fixed_price == 'No' ? 'selected' : '' }}>No</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-lg-3 col-md-4">
                                                            <label class="modern-admin-label" for="status_{{ $variation->id }}">Status</label>
                                                            <select class="form-select form-select-{{ formControlSize() }}" name="status[]" id="status_{{ $variation->id }}">
                                                                <option value="active" {{ $variation->status == 'active' ? 'selected' : '' }}>Active</option>
                                                                <option value="inactive" {{ $variation->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-lg-3 col-md-4">
                                                            <label class="modern-admin-label" for="multistep_{{ $variation->id }}">Use Multistep</label>
                                                            <select class="form-select form-select-{{ formControlSize() }}" name="multistep[]" id="multistep_{{ $variation->id }}">
                                                                <option value="">Select</option>
                                                                <option value="yes" {{ $variation->multistep == 'yes' ? 'selected' : '' }}>Yes</option>
                                                                <option value="no" {{ $variation->multistep == 'no' ? 'selected' : '' }}>No</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="rounded-4 border p-3" style="background: linear-gradient(135deg, rgba(13, 110, 253, 0.06), rgba(32, 201, 151, 0.06));">
                                                                <div class="d-flex align-items-center justify-content-between mb-3">
                                                                    <div>
                                                                        <h6 class="mb-1">Customer level pricing</h6>
                                                                        <p class="mb-0 text-muted small">Set the live discount or margin for each customer group.</p>
                                                                    </div>
                                                                </div>
                                                                <div class="row g-3">
                                                                    @foreach($customerlevel as $level)
                                                                        <div class="col-lg-4 col-md-4">
                                                                            <label class="modern-admin-label" for="level_{{ $level->id }}_{{ $variation->id }}">
                                                                                {{ $level->name }}
                                                                                @if($discountType == 'flat')
                                                                                    Discounted Price ({{ $currency }})
                                                                                @else
                                                                                    Discounted Percentage (%)
                                                                                @endif
                                                                            </label>
                                                                            <input
                                                                                type="number"
                                                                                step=".01"
                                                                                class="form-control form-control-{{ formControlSize() }}"
                                                                                id="level_{{ $level->id }}_{{ $variation->id }}"
                                                                                name="level[{{ $level->id }}][]"
                                                                                value="{{ $variation->customer_level_price($level->id) }}"
                                                                            >
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="variation-empty-state alert alert-warning border mb-0">
                                                No variations have been pulled yet. Use the pull button above or add a row below and save everything together.
                                            </div>
                                        @endforelse
                                    </div>

                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mt-4">
                                        <div class="d-flex flex-wrap align-items-center gap-2">
                                            <button class="btn btn-outline-primary" type="button" id="add-variation-row">Add another variation</button>
                                            <div class="text-muted small">
                                                New rows can be removed before saving. Existing rows delete through AJAX if they have no transactions.
                                            </div>
                                        </div>
                                        <button class="btn btn-admin-submit" type="submit">Save variations</button>
                                    </div>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <template id="variation-row-template">
        <div class="variation-card card border rounded-4 shadow-sm" data-variation-card data-existing="0" data-variation-id="">
            <div class="card-body">
                <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <h5 class="mb-0">New variation</h5>
                            <span class="badge bg-label-secondary">New</span>
                        </div>
                        <div class="text-muted small">Fill the details below and save everything together.</div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-outline-danger btn-sm variation-remove-row">Remove</button>
                    </div>
                </div>

                <div class="row g-3">
                    <input type="hidden" name="variation_id[]" value="">

                    <div class="col-lg-3 col-md-6">
                        <label class="modern-admin-label" for="api_name_new___index__">API Name</label>
                        <input type="text" class="form-control form-control-{{ formControlSize() }}" id="api_name_new___index__" name="api_name[]" placeholder="API name" required>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="modern-admin-label" for="system_name_new___index__">System Name</label>
                        <input type="text" class="form-control form-control-{{ formControlSize() }}" id="system_name_new___index__" name="system_name[]" placeholder="System name" required>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="modern-admin-label" for="slug_new___index__">Slug</label>
                        <input type="text" class="form-control form-control-{{ formControlSize() }}" id="slug_new___index__" name="slug[]" placeholder="Variation slug">
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="modern-admin-label" for="ussd_string_new___index__">USSD String</label>
                        <input type="text" class="form-control form-control-{{ formControlSize() }}" id="ussd_string_new___index__" name="ussd_string[]" placeholder="Optional USSD string">
                    </div>

                    <div class="col-lg-3 col-md-4">
                        <label class="modern-admin-label" for="api_price_new___index__">API Price ({{ $currency }})</label>
                        <input type="number" class="form-control form-control-{{ formControlSize() }}" id="api_price_new___index__" name="api_price[]" placeholder="API price">
                    </div>
                    <div class="col-lg-3 col-md-4">
                        <label class="modern-admin-label" for="system_price_new___index__">System Price ({{ $currency }})</label>
                        <input type="number" class="form-control form-control-{{ formControlSize() }}" id="system_price_new___index__" name="system_price[]" placeholder="System price">
                    </div>
                    <div class="col-lg-3 col-md-4">
                        <label class="modern-admin-label" for="datasize_new___index__">Datasize</label>
                        <input type="number" class="form-control form-control-{{ formControlSize() }}" id="datasize_new___index__" name="datasize[]" placeholder="Datasize">
                    </div>
                    <div class="col-lg-3 col-md-4">
                        <label class="modern-admin-label" for="min_new___index__">Min Amount</label>
                        <input type="number" class="form-control form-control-{{ formControlSize() }}" id="min_new___index__" name="min[]" placeholder="Min">
                    </div>
                    <div class="col-lg-3 col-md-4">
                        <label class="modern-admin-label" for="max_new___index__">Max Amount</label>
                        <input type="number" class="form-control form-control-{{ formControlSize() }}" id="max_new___index__" name="max[]" placeholder="Max">
                    </div>
                    <div class="col-lg-3 col-md-4">
                        <label class="modern-admin-label" for="fixed_price_new___index__">Fixed Price</label>
                        <select class="form-select form-select-{{ formControlSize() }}" name="fixed_price[]" id="fixed_price_new___index__">
                            <option value="">Select</option>
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-4">
                        <label class="modern-admin-label" for="status_new___index__">Status</label>
                        <select class="form-select form-select-{{ formControlSize() }}" name="status[]" id="status_new___index__">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-4">
                        <label class="modern-admin-label" for="multistep_new___index__">Use Multistep</label>
                        <select class="form-select form-select-{{ formControlSize() }}" name="multistep[]" id="multistep_new___index__">
                            <option value="">Select</option>
                            <option value="yes">Yes</option>
                            <option value="no">No</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <div class="rounded-4 border p-3" style="background: linear-gradient(135deg, rgba(13, 110, 253, 0.06), rgba(32, 201, 151, 0.06));">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div>
                                    <h6 class="mb-1">Customer level pricing</h6>
                                    <p class="mb-0 text-muted small">Set the live discount or margin for each customer group.</p>
                                </div>
                            </div>
                            <div class="row g-3">
                                @foreach($customerlevel as $level)
                                    <div class="col-lg-4 col-md-4">
                                        <label class="modern-admin-label" for="level_{{ $level->id }}_new___index__">
                                            {{ $level->name }}
                                            @if($discountType == 'flat')
                                                Discounted Price ({{ $currency }})
                                            @else
                                                Discounted Percentage (%)
                                            @endif
                                        </label>
                                        <input
                                            type="number"
                                            step=".01"
                                            class="form-control form-control-{{ formControlSize() }}"
                                            id="level_{{ $level->id }}_new___index__"
                                            name="level[{{ $level->id }}][]"
                                            placeholder="Customer price"
                                        >
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
@endsection

@section('page-script')
    <script src="{{ asset('app-assets/js/scripts/pages/dashboard-analytics.js') }}"></script>
    <script>
        $(function () {
            const $variationRows = $('#variation-rows');
            const template = document.getElementById('variation-row-template').innerHTML.trim();
            let variationRowIndex = Math.max($variationRows.find('[data-variation-card]').length, 1);

            function renderEmptyStateIfNeeded() {
                if ($variationRows.find('[data-variation-card]').length > 0) {
                    return;
                }

                if ($variationRows.find('.variation-empty-state').length < 1) {
                    $variationRows.html(`
                        <div class="variation-empty-state alert alert-warning border mb-0">
                            No variations remain on the page. Add a new row below and save everything together.
                        </div>
                    `);
                }
            }

            function addVariationRow() {
                const index = variationRowIndex++;
                const html = template.replace(/__index__/g, index);

                $variationRows.find('.variation-empty-state').remove();
                $variationRows.append(html);
            }

            if ($variationRows.find('[data-variation-card]').length === 0) {
                addVariationRow();
            }

            $('#add-variation-row').on('click', function () {
                addVariationRow();
            });

            $(document).on('click', '.variation-remove-row', function () {
                $(this).closest('[data-variation-card]').remove();
                renderEmptyStateIfNeeded();
            });

            $(document).on('click', '.variation-delete-btn', function () {
                const $button = $(this);
                const deleteUrl = $button.data('delete-url');
                const variationTitle = $button.data('variation-title') || 'this variation';
                const $card = $button.closest('[data-variation-card]');

                if (!confirm(`You are about to delete ${variationTitle}.`)) {
                    return;
                }

                $button.prop('disabled', true);

                $.ajax({
                    url: deleteUrl,
                    method: 'GET',
                    dataType: 'json',
                    headers: {
                        Accept: 'application/json'
                    },
                    success: function (response) {
                        if (response && response.status === 'success') {
                            $card.remove();
                            renderEmptyStateIfNeeded();
                            return;
                        }

                        $button.prop('disabled', false);
                        alert((response && response.message) ? response.message : 'Unable to delete variation.');
                    },
                    error: function (xhr) {
                        $button.prop('disabled', false);
                        const message = xhr.responseJSON?.message || 'Unable to delete variation.';
                        alert(message);
                    }
                });
            });
        });
    </script>
@endsection
