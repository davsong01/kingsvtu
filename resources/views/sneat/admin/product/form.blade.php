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
                    <a
                        href="{{ !empty($product->category_id) ? route('category.edit', $product->category_id) : '#' }}"
                        class="admin-page-badge admin-page-badge--link"
                        @if(empty($product->category_id)) aria-disabled="true" tabindex="-1" @endif
                    >
                        <span>Category</span>
                        <strong>{{ $currentCategoryName }}</strong>
                    </a>
                    <a
                        href="{{ !empty($product->api_id) ? route('api.edit', $product->api_id) : '#' }}"
                        class="admin-page-badge admin-page-badge--link"
                        @if(empty($product->api_id)) aria-disabled="true" tabindex="-1" @endif
                    >
                        <span>API</span>
                        <strong>{{ $currentApiName }}</strong>
                    </a>
                    <a
                        href="{{ $isEdit ? route('product.edit', $product->id) . '?tab=variations' : '#' }}"
                        class="admin-page-badge admin-page-badge--link"
                        @if(!$isEdit) aria-disabled="true" tabindex="-1" @endif
                    >
                        <span>Variations</span>
                        <strong>{{ $isEdit ? number_format($variations->count()) : '0' }}</strong>
                    </a>
                </div>
            </div>

            @include('sneat.layouts.alerts')

            <form action="{{ $formAction }}" method="POST" enctype="multipart/form-data" id="product-form">
                @csrf
                @if($isEdit)
                    @method('PATCH')
                @endif
                <input type="hidden" name="route" value="page1">

                <div class="row g-4">
                        <div class="col-xl-7">
                            <div class="modern-admin-card card h-10">
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
                                        <div class="col-md-6">
                                            <label class="modern-admin-label" for="seo_title">SEO Title</label>
                                            <input type="text" class="form-control form-control-{{ formControlSize() }}" id="seo_title" name="seo_title" value="{{ old('seo_title', $product->seo_title ?? '') }}" placeholder="SEO title">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="modern-admin-label" for="seo_keywords">SEO Keywords</label>
                                            <input type="text" class="form-control form-control-{{ formControlSize() }}" id="seo_keywords" name="seo_keywords" value="{{ old('seo_keywords', $product->seo_keywords ?? '') }}" placeholder="SEO keywords">
                                        </div>
                                        <div class="col-md-12">
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
                                                    <div class="col-md-12">
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
            </form>
        </div>
    </div>
@endsection

@section('page-script')
@endsection
