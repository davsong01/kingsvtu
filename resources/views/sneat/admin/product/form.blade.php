@extends('sneat.layouts.app')

@section('title', $product ? ('Edit ' . $product->name) : 'Add Product')

@section('content')
    @php
        $isEdit = !empty($product?->id);
        $pageTitle = $isEdit ? 'Edit ' . data_get($product, 'name') : 'Add Product';
        $formAction = $isEdit ? route('product.update', $product->id) : route('product.store');
        $selectedCategory = old('category', data_get($product, 'category_id', ''));
        $selectedApi = old('api', data_get($product, 'api_id', ''));
        $selectedHasVariations = old('has_variations', data_get($product, 'has_variations', ''));
        $selectedStatus = old('status', data_get($product, 'status', ''));
        $selectedFixedPrice = old('fixed_price', data_get($product, 'fixed_price', ''));
        $selectedAllowQuantity = old('allow_quantity', data_get($product, 'allow_quantity', ''));
        $selectedAllowSubscription = old('allow_subscription_type', data_get($product, 'allow_subscription_type', ''));
        $selectedMultistep = old('multistep', data_get($product, 'multistep', ''));
        $tab = request('tab', 'details');
        $imageUrl = $isEdit && !empty($product->image) ? asset($product->image) : null;
        $currentCategoryName = data_get($product, 'category.name', 'Not set');
        $currentApiName = data_get($product, 'api.name', 'Not set');
    @endphp

    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="admin-page-hero mb-4">
                <div>
                    <span class="admin-page-hero__kicker">Catalogue</span>
                    <h1>{{ $pageTitle }}</h1>
                    <p>Keep product metadata, pricing, and variation management in one modern control surface.</p>
                </div>
                <div class="admin-page-badges">
                    <div class="admin-page-badge">
                        <span>Category</span>
                        <strong>{{ $currentCategoryName }}</strong>
                    </div>
                    <div class="admin-page-badge">
                        <span>API</span>
                        <strong>{{ $currentApiName }}</strong>
                    </div>
                    <div class="admin-page-badge">
                        <span>Variations</span>
                        <strong>{{ $isEdit ? number_format($variations->count()) : '0' }}</strong>
                    </div>
                </div>
            </div>

            @include('sneat.layouts.alerts')

            <form action="{{ $formAction }}" method="POST" enctype="multipart/form-data" id="product-form">
                @csrf
                @if($isEdit)
                    @method('PATCH')
                @endif
                <input type="hidden" name="route" value="page1">

                @if($tab !== 'variations')
                    <div class="row g-4">
                        <div class="col-xl-7">
                            <div class="modern-admin-card card h-100">
                                <div class="card-header">
                                    <h3>Product details</h3>
                                    <p>Identity, visibility, and display setup for the service.</p>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="modern-admin-label" for="name">Name</label>
                                            <input type="text" class="form-control form-control-{{ formControlSize() }}" id="name" name="name" value="{{ old('name', data_get($product, 'name', '')) }}" placeholder="Enter product name" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="modern-admin-label" for="display_name">Display Name</label>
                                            <input type="text" class="form-control form-control-{{ formControlSize() }}" id="display_name" name="display_name" value="{{ old('display_name', data_get($product, 'display_name', '')) }}" placeholder="Enter display name" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="modern-admin-label" for="slug">Slug</label>
                                            <input type="text" class="form-control form-control-{{ formControlSize() }}" id="slug" name="slug" value="{{ old('slug', data_get($product, 'slug', '')) }}" placeholder="Enter slug" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="modern-admin-label" for="category">Category</label>
                                            <select class="form-select form-select-{{ formControlSize() }}" name="category" id="category" required>
                                                <option value="">Select category</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}" @selected($selectedCategory == $category->id)>{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="modern-admin-label" for="api">API to use</label>
                                            <select class="form-select form-select-{{ formControlSize() }}" name="api" id="api" required>
                                                <option value="">Select API</option>
                                                @foreach($apis as $item)
                                                    <option value="{{ $item->id }}" @selected($selectedApi == $item->id)>{{ $item->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="modern-admin-label" for="status">Status</label>
                                            <select class="form-select form-select-{{ formControlSize() }}" name="status" id="status" required>
                                                <option value="">Select status</option>
                                                <option value="active" @selected($selectedStatus === 'active')>Active</option>
                                                <option value="inactive" @selected($selectedStatus === 'inactive')>Inactive</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="modern-admin-label" for="has_variations">Has Variations</label>
                                            <select class="form-select form-select-{{ formControlSize() }}" name="has_variations" id="has_variations" required>
                                                <option value="">Select</option>
                                                <option value="yes" @selected($selectedHasVariations === 'yes')>Yes</option>
                                                <option value="no" @selected($selectedHasVariations === 'no')>No</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="modern-admin-label" for="fixed_price">Fixed Price</label>
                                            <select class="form-select form-select-{{ formControlSize() }}" name="fixed_price" id="fixed_price">
                                                <option value="">Select</option>
                                                <option value="yes" @selected($selectedFixedPrice === 'yes')>Yes</option>
                                                <option value="no" @selected($selectedFixedPrice === 'no')>No</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="modern-admin-label" for="allow_quantity">Allow Quantity</label>
                                            <select class="form-select form-select-{{ formControlSize() }}" name="allow_quantity" id="allow_quantity">
                                                <option value="">Select</option>
                                                <option value="yes" @selected($selectedAllowQuantity === 'yes')>Yes</option>
                                                <option value="no" @selected($selectedAllowQuantity === 'no')>No</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="modern-admin-label" for="allow_subscription_type">Allow Subscription Type</label>
                                            <select class="form-select form-select-{{ formControlSize() }}" name="allow_subscription_type" id="allow_subscription_type">
                                                <option value="">Select</option>
                                                <option value="yes" @selected($selectedAllowSubscription === 'yes')>Yes</option>
                                                <option value="no" @selected($selectedAllowSubscription === 'no')>No</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="modern-admin-label" for="multistep">Use Multistep</label>
                                            <select class="form-select form-select-{{ formControlSize() }}" name="multistep" id="multistep">
                                                <option value="">Select</option>
                                                <option value="yes" @selected($selectedMultistep === 'yes')>Yes</option>
                                                <option value="no" @selected($selectedMultistep === 'no')>No</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="modern-admin-label" for="servercode">Server Code / Token</label>
                                            <input type="text" class="form-control form-control-{{ formControlSize() }}" id="servercode" name="servercode" value="{{ old('servercode', data_get($product, 'servercode', '')) }}" placeholder="Enter server code">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="modern-admin-label" for="ussd_string">USSD String</label>
                                            <input type="text" class="form-control form-control-{{ formControlSize() }}" id="ussd_string" name="ussd_string" value="{{ old('ussd_string', data_get($product, 'ussd_string', '')) }}" placeholder="Enter USSD string">
                                        </div>
                                        <div class="col-12">
                                            <label class="modern-admin-label" for="image">Display Image</label>
                                            <div class="admin-upload-card">
                                                <div class="admin-upload-card__meta">
                                                    <strong>{{ $isEdit ? 'Replace product image' : 'Choose product image' }}</strong>
                                                    <input type="file" accept="image/*" class="form-control form-control-{{ formControlSize() }} mt-2" id="image" name="image" {{ $isEdit ? '' : 'required' }}>
                                                </div>
                                                <div class="admin-upload-preview">
                                                    @if($imageUrl)
                                                        <img src="{{ $imageUrl }}" alt="Product image preview">
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modern-admin-card card mt-4">
                                <div class="card-header">
                                    <h3>Content and SEO</h3>
                                    <p>Optional content for storefront and search visibility.</p>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="modern-admin-label" for="description">Description</label>
                                            <textarea class="form-control form-control-{{ formControlSize() }}" id="description" name="description" rows="4" placeholder="Product description">{{ old('description', $product->description ?? '') }}</textarea>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="modern-admin-label" for="seo_title">SEO Title</label>
                                            <input type="text" class="form-control form-control-{{ formControlSize() }}" id="seo_title" name="seo_title" value="{{ old('seo_title', $product->seo_title ?? '') }}" placeholder="SEO title">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="modern-admin-label" for="seo_keywords">SEO Keywords</label>
                                            <input type="text" class="form-control form-control-{{ formControlSize() }}" id="seo_keywords" name="seo_keywords" value="{{ old('seo_keywords', $product->seo_keywords ?? '') }}" placeholder="SEO keywords">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="modern-admin-label" for="seo_description">SEO Description</label>
                                            <textarea class="form-control form-control-{{ formControlSize() }}" id="seo_description" name="seo_description" rows="3" placeholder="SEO description">{{ old('seo_description', $product->seo_description ?? '') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-5">
                            <div class="modern-admin-card card h-100">
                                <div class="card-header">
                                    <h3>Pricing and visibility</h3>
                                    <p>Pricing controls and customer level specific rates.</p>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="modern-admin-label" for="system_price">System Price</label>
                                            <input type="number" class="form-control form-control-{{ formControlSize() }}" id="system_price" name="system_price" value="{{ old('system_price', $product->system_price ?? '') }}" placeholder="0.00">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="modern-admin-label" for="min">Minimum Amount</label>
                                            <input type="number" class="form-control form-control-{{ formControlSize() }}" id="min" name="min" value="{{ old('min', $product->min ?? '') }}" placeholder="0.00">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="modern-admin-label" for="max">Maximum Amount</label>
                                            <input type="number" class="form-control form-control-{{ formControlSize() }}" id="max" name="max" value="{{ old('max', $product->max ?? '') }}" placeholder="0.00">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="modern-admin-label" for="quantity_graduation">Quantity Graduation</label>
                                            <input type="text" class="form-control form-control-{{ formControlSize() }}" id="quantity_graduation" name="quantity_graduation" value="{{ old('quantity_graduation', data_get($product, 'quantity_graduation', '')) }}" placeholder="Comma separated values">
                                        </div>
                                        <div class="col-12">
                                            <label class="modern-admin-label" for="referral_percentage">Referral Percentage (%)</label>
                                            <input type="number" step="0.01" class="form-control form-control-{{ formControlSize() }}" id="referral_percentage" name="referral_percentage" value="{{ old('referral_percentage', data_get($product, 'referral_percentage', '')) }}" placeholder="Enter referral percentage">
                                        </div>
                                    </div>

                                    <div class="mt-4">
                                        <div class="admin-note-section">
                                            <div class="admin-note-section__heading">
                                                <span>Customer level pricing</span>
                                                <small>Per level override</small>
                                            </div>
                                            <div class="row g-3">
                                                @foreach($customerlevel as $level)
                                                    <div class="col-md-6">
                                                        <label class="modern-admin-label" for="productlevel_{{ $level->id }}">
                                                            {{ $level->name }}
                                                            @if(data_get($product, 'category.discount_type') === 'flat')
                                                                Discounted Price
                                                            @else
                                                                Discounted Percentage (%)
                                                            @endif
                                                        </label>
                                                        <input type="number" step="0.01" class="form-control form-control-{{ formControlSize() }}" id="productlevel_{{ $level->id }}" name="productlevel[{{ $level->id }}]" value="{{ old('productlevel.' . $level->id, $isEdit ? $product->customer_level_price($level->id) : '') }}" placeholder="Enter price">
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modern-admin-footer mt-4">
                                <button class="btn btn-admin-submit" type="submit">{{ $isEdit ? 'Update product' : 'Save product' }}</button>
                                <a href="{{ route('product.index') }}" class="gateway-action">Back to products</a>
                            </div>
                        </div>
                    </div>
                @endif

                @if($isEdit && $selectedHasVariations === 'yes')
                        <div class="modern-admin-card card mt-4">
                            <div class="card-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
                                <div>
                                    <h3>Variation management</h3>
                                    <p>Pull, add, or refine variation rules for this product.</p>
                                </div>
                                <div class="d-flex flex-wrap gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#primary">Add variations</button>
                                    @if($variations->count() < 1)
                                        <a href="{{ route('variations.pull', $product->id) }}" class="btn btn-sm btn-outline-success">Pull variations</a>
                                    @else
                                        <a href="{{ route('variations.pull', $product->id) }}" class="btn btn-sm btn-outline-success">Re-pull variations</a>
                                    @endif
                                </div>
                            </div>
                        <div class="card-body">
                            @include('admin.product.add_variations_form')

                            @if($variations->count() > 0)
                                <form action="{{ route('variations.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="product-variation-stack">
                                        @foreach($variations as $variation)
                                            <div class="product-variation-card card">
                                                <div class="card-header d-flex flex-column flex-md-row align-items-md-start justify-content-between gap-2">
                                                    <div>
                                                        <h3>{{ $variation->system_name }}</h3>
                                                        <p>Slug: {{ $variation->slug }} | API name: {{ $variation->api_name }}</p>
                                                    </div>
                                                    <div class="product-variation-card__actions">
                                                        <span class="gateway-badge {{ $variation->status === 'active' ? 'gateway-badge--active' : 'gateway-badge--inactive' }}">
                                                            {{ ucfirst($variation->status ?? 'inactive') }}
                                                        </span>
                                                        @if($variation->transaction->count() < 1)
                                                            <a class="btn btn-outline-danger btn-sm product-variation-delete" onclick="return confirm('You are about to delete a variation')" href="{{ route('variation.delete', $variation->id) }}">Delete</a>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="card-body">
                                                    <input type="hidden" name="variation_id[{{ $variation->id }}]" value="{{ $variation->id }}">
                                                    <div class="row g-3">
                                                        <div class="col-12 col-lg-4">
                                                            <div class="product-variation-panel">
                                                                <div class="product-variation-panel__title">Variation details</div>
                                                                <div class="row g-2">
                                                                    <div class="col-12">
                                                                        <label class="modern-admin-label" for="api_name_{{ $variation->id }}">API name</label>
                                                                        <input type="text" id="api_name_{{ $variation->id }}" class="form-control form-control-{{ formControlSize() }}" name="api_name[{{ $variation->id }}]" value="{{ $variation->api_name }}" placeholder="API name">
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <label class="modern-admin-label" for="system_name_{{ $variation->id }}">System name</label>
                                                                        <input type="text" id="system_name_{{ $variation->id }}" class="form-control form-control-{{ formControlSize() }}" name="system_name[{{ $variation->id }}]" value="{{ $variation->system_name }}" placeholder="System name">
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <label class="modern-admin-label" for="slug_{{ $variation->id }}">Slug</label>
                                                                        <input type="text" id="slug_{{ $variation->id }}" class="form-control form-control-{{ formControlSize() }}" name="slug[{{ $variation->id }}]" value="{{ $variation->slug }}" placeholder="Slug">
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <label class="modern-admin-label" for="status_{{ $variation->id }}">Status</label>
                                                                        <select id="status_{{ $variation->id }}" class="form-select form-select-{{ formControlSize() }}" name="status[{{ $variation->id }}]">
                                                                            <option value="active" @selected($variation->status === 'active')>Active</option>
                                                                            <option value="inactive" @selected($variation->status === 'inactive')>Inactive</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-12 col-lg-4">
                                                            <div class="product-variation-panel">
                                                                <div class="product-variation-panel__title">Pricing</div>
                                                                <div class="row g-2">
                                                                    <div class="col-12">
                                                                        <label class="modern-admin-label" for="api_price_{{ $variation->id }}">API price</label>
                                                                        <input type="number" id="api_price_{{ $variation->id }}" class="form-control form-control-{{ formControlSize() }}" step="0.01" name="api_price[{{ $variation->id }}]" value="{{ $variation->api_price }}" placeholder="API price">
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <label class="modern-admin-label" for="system_price_{{ $variation->id }}">System price</label>
                                                                        <input type="number" id="system_price_{{ $variation->id }}" class="form-control form-control-{{ formControlSize() }}" step="0.01" name="system_price[{{ $variation->id }}]" value="{{ $variation->system_price }}" placeholder="System price">
                                                                    </div>
                                                                    @foreach($customerlevel as $level)
                                                                        <div class="col-12">
                                                                            <label class="modern-admin-label" for="level_{{ $level->id }}_{{ $variation->id }}">{{ $level->name }}</label>
                                                                            <input type="number" id="level_{{ $level->id }}_{{ $variation->id }}" step="0.01" class="form-control form-control-{{ formControlSize() }}" name="level[{{ $level->id }}][{{ $variation->id }}]" value="{{ $variation->customer_level_price($level->id) }}" placeholder="{{ $level->name }}">
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-12 col-lg-4">
                                                            <div class="product-variation-panel">
                                                                <div class="product-variation-panel__title">Amounts and extras</div>
                                                                <div class="row g-2">
                                                                    <div class="col-12">
                                                                        <label class="modern-admin-label" for="min_{{ $variation->id }}">Minimum</label>
                                                                        <input type="number" id="min_{{ $variation->id }}" class="form-control form-control-{{ formControlSize() }}" name="min[{{ $variation->id }}]" value="{{ $variation->min }}" placeholder="Min">
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <label class="modern-admin-label" for="max_{{ $variation->id }}">Maximum</label>
                                                                        <input type="number" id="max_{{ $variation->id }}" class="form-control form-control-{{ formControlSize() }}" name="max[{{ $variation->id }}]" value="{{ $variation->max }}" placeholder="Max">
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <label class="modern-admin-label" for="datasize_{{ $variation->id }}">Datasize</label>
                                                                        <input type="number" id="datasize_{{ $variation->id }}" class="form-control form-control-{{ formControlSize() }}" name="datasize[{{ $variation->id }}]" value="{{ $variation->datasize }}" placeholder="Datasize">
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <label class="modern-admin-label" for="fixed_price_{{ $variation->id }}">Fixed price</label>
                                                                        <select id="fixed_price_{{ $variation->id }}" class="form-select form-select-{{ formControlSize() }}" name="fixed_price[{{ $variation->id }}]">
                                                                            <option value="Yes" @selected($variation->fixed_price === 'Yes')>Yes</option>
                                                                            <option value="No" @selected($variation->fixed_price === 'No')>No</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <label class="modern-admin-label" for="multistep_{{ $variation->id }}">Multistep</label>
                                                                        <select id="multistep_{{ $variation->id }}" class="form-select form-select-{{ formControlSize() }}" name="multistep[{{ $variation->id }}]">
                                                                            <option value="yes" @selected($variation->multistep === 'yes')>Yes</option>
                                                                            <option value="no" @selected($variation->multistep === 'no')>No</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <label class="modern-admin-label" for="ussd_string_{{ $variation->id }}">USSD string</label>
                                                                        <input type="text" id="ussd_string_{{ $variation->id }}" class="form-control form-control-{{ formControlSize() }}" name="ussd_string[{{ $variation->id }}]" value="{{ $variation->ussd_string }}" placeholder="USSD string">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <div class="modern-admin-footer mt-4">
                                        <button class="btn btn-admin-submit" type="submit">Update variations</button>
                                    </div>
                                </form>
                            @endif
                        </div>
                    </div>
                @endif
            </form>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        $('#api').on('change', function () {
            const currentId = '{{ data_get($product, "api.id", "") }}';
            const hasVariations = '{{ $selectedHasVariations }}';

            if ($(this).val() !== currentId && hasVariations === 'yes') {
                // Variation copy controls can be added later if needed.
            }
        });
    </script>
@endsection
