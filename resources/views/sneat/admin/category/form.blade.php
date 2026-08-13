@extends('sneat.layouts.app')

@section('title', $category ? ('Edit ' . $category->name) : 'Add Category')

@section('content')
    @php
        $isEdit = !empty($category?->id);
        $pageTitle = $isEdit ? 'Edit ' . data_get($category, 'name') : 'Add Category';
        $formAction = $isEdit ? route('category.update', $category->id) : route('category.store');
    @endphp

    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="admin-page-hero mb-4">
                <div>
                    <span class="admin-page-hero__kicker">Catalogue</span>
                    <h1>{{ $pageTitle }}</h1>
                    <p>Keep category metadata, SEO details, and visibility settings together in a clean form.</p>
                </div>
                <a href="{{ route('category.index') }}" class="gateway-action">Back to categories</a>
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
                                <h3>Category details</h3>
                                <p>Primary fields used across the catalogue and storefront.</p>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="modern-admin-label" for="name">Name</label>
                                        <input type="text" class="form-control form-control-{{ formControlSize() }}" id="name" name="name" value="{{ old('name', data_get($category, 'name', '')) }}" placeholder="Enter category name" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="modern-admin-label" for="display_name">Display Name</label>
                                        <input type="text" class="form-control form-control-{{ formControlSize() }}" id="display_name" name="display_name" value="{{ old('display_name', data_get($category, 'display_name', '')) }}" placeholder="Enter display name" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="modern-admin-label" for="slug">Slug</label>
                                        <input type="text" class="form-control form-control-{{ formControlSize() }}" id="slug" name="slug" value="{{ old('slug', data_get($category, 'slug', '')) }}" placeholder="Enter slug" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="modern-admin-label" for="unique_element">Unique Element</label>
                                        <select class="form-select form-select-{{ formControlSize() }}" name="unique_element" id="unique_element" required>
                                            <option value="">Select element</option>
                                            @foreach(getUniqueElements() as $element)
                                                <option value="{{ $element }}" @selected(old('unique_element', $category->unique_element ?? '') === $element)>{{ ucfirst(str_replace('_', ' ', $element)) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="modern-admin-label" for="status">Status</label>
                                        <select class="form-select form-select-{{ formControlSize() }}" name="status" id="status" required>
                                            <option value="">Select status</option>
                                            <option value="active" @selected(old('status', data_get($category, 'status', '')) === 'active')>Active</option>
                                            <option value="inactive" @selected(old('status', data_get($category, 'status', '')) === 'inactive')>Inactive</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="modern-admin-label" for="discount_type">Discount Type</label>
                                        <select class="form-select form-select-{{ formControlSize() }}" name="discount_type" id="discount_type" required>
                                            <option value="">Select type</option>
                                            <option value="flat" @selected(old('discount_type', data_get($category, 'discount_type', '')) === 'flat')>Flat</option>
                                            <option value="percentage" @selected(old('discount_type', data_get($category, 'discount_type', '')) === 'percentage')>Percentage</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="modern-admin-label" for="order">Order</label>
                                        <input type="number" class="form-control form-control-{{ formControlSize() }}" id="order" name="order" value="{{ old('order', data_get($category, 'order', '')) }}" placeholder="Enter order" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="modern-admin-label" for="icon">SVG code</label>
                                        <textarea class="form-control form-control-{{ formControlSize() }}" id="icon" name="icon" rows="11" placeholder="Paste SVG or icon code" required>{{ old('icon', data_get($category, 'icon', '')) }}</textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="modern-admin-label" for="description">Description</label>
                                        <textarea class="form-control form-control-{{ formControlSize() }}" id="description" name="description" rows="4" placeholder="Category description">{{ old('description', data_get($category, 'description', '')) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-5">
                        <div class="modern-admin-card card h-100">
                            <div class="card-header">
                                <h3>SEO details</h3>
                                <p>Optional metadata for search and content indexing.</p>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="modern-admin-label" for="seo_title">SEO Title</label>
                                        <input type="text" class="form-control form-control-{{ formControlSize() }}" id="seo_title" name="seo_title" value="{{ old('seo_title', data_get($category, 'seo_title', '')) }}" placeholder="SEO title">
                                    </div>
                                    <div class="col-12">
                                        <label class="modern-admin-label" for="seo_keywords">SEO Keywords</label>
                                        <input type="text" class="form-control form-control-{{ formControlSize() }}" id="seo_keywords" name="seo_keywords" value="{{ old('seo_keywords', data_get($category, 'seo_keywords', '')) }}" placeholder="SEO keywords">
                                    </div>
                                    <div class="col-12">
                                        <label class="modern-admin-label" for="seo_description">SEO Description</label>
                                        <textarea class="form-control form-control-{{ formControlSize() }}" id="seo_description" name="seo_description" rows="4" placeholder="SEO description">{{ old('seo_description', data_get($category, 'seo_description', '')) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modern-admin-footer mt-4 justify-content-start">
                            <button class="btn btn-admin-submit" type="submit">{{ $isEdit ? 'Update category' : 'Save category' }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
