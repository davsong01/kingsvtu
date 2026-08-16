@extends('sneat.layouts.app')

@section('title', 'Products')

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="admin-page-hero mb-4">
                <div>
                    <span class="admin-page-hero__kicker">Catalogue</span>
                    <h1>Products</h1>
                    <p>Review services, pricing, and variation coverage in a cleaner product management table.</p>
                </div>
                <div class="admin-page-badges">
                    <div class="admin-page-badge">
                        <span>Total products</span>
                        <strong>{{ number_format($totalProducts ?? $products->total()) }}</strong>
                    </div>
                    <div class="admin-page-badge">
                        <span>Active products</span>
                        <strong>{{ number_format($activeProducts ?? 0) }}</strong>
                    </div>
                    <div class="admin-page-badge">
                        <span>Variation products</span>
                        <strong>{{ number_format($variationProducts ?? 0) }}</strong>
                    </div>
                    <a href="{{ route('product.create') }}" class="btn btn-admin-submit">Add product</a>
                </div>
            </div>

            @include('sneat.layouts.alerts')

            <div class="gateway-card card">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div>
                        <h3>Product list</h3>
                        <p>Jump into edit, duplicate, or manage variations.</p>
                    </div>
                    <div class="text-muted small">
                        Showing {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }} of {{ number_format($products->total()) }}
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('product.index') }}" class="row g-3 mb-4">
                        <div class="col-lg-4 col-md-6">
                            <label class="modern-admin-label">Search</label>
                            <input
                                type="text"
                                name="search"
                                class="form-control form-control-{{ formControlSize() }}"
                                value="{{ $filters['search'] ?? '' }}"
                                placeholder="Name, display name, or slug"
                            >
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label class="modern-admin-label">Status</label>
                            <select name="status" class="form-select form-select-{{ formControlSize() }}">
                                <option value="">All</option>
                                <option value="active" {{ ($filters['status'] ?? '') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label class="modern-admin-label">Category</label>
                            <select name="category" class="form-select form-select-{{ formControlSize() }}">
                                <option value="">All</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ (string)($filters['category'] ?? '') === (string)$category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label class="modern-admin-label">API</label>
                            <select name="api" class="form-select form-select-{{ formControlSize() }}">
                                <option value="">All</option>
                                @foreach($apis as $api)
                                    <option value="{{ $api->id }}" {{ (string)($filters['api'] ?? '') === (string)$api->id ? 'selected' : '' }}>
                                        {{ $api->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label class="modern-admin-label">Variations</label>
                            <select name="has_variations" class="form-select form-select-{{ formControlSize() }}">
                                <option value="">All</option>
                                <option value="yes" {{ ($filters['has_variations'] ?? '') === 'yes' ? 'selected' : '' }}>Yes</option>
                                <option value="no" {{ ($filters['has_variations'] ?? '') === 'no' ? 'selected' : '' }}>No</option>
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label class="modern-admin-label">Per page</label>
                            <select name="per_page" class="form-select form-select-{{ formControlSize() }}">
                                @foreach([10, 15, 25, 50, 100] as $size)
                                    <option value="{{ $size }}" {{ (int)($filters['per_page'] ?? 15) === $size ? 'selected' : '' }}>
                                        {{ $size }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 d-flex flex-wrap gap-2">
                            <button type="submit" class="btn btn-admin-submit">Filter</button>
                            <a href="{{ route('product.index') }}" class="btn btn-outline-secondary">Reset</a>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table gateway-table align-middle" id="product-table">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>API</th>
                                    <th>Variations</th>
                                    <th>Status</th>
                                    <th>Date Added</th>
                                    @if(hasAccess('product.edit'))
                                        <th class="text-end">Action</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                    @php
                                        $productImage = !empty($product->image)
                                            ? (str_starts_with($product->image, 'http') ? $product->image : asset($product->image))
                                            : asset('site/upgrade.jpg');
                                        $variationCount = $product->variations->count();
                                    @endphp
                                    <tr>
                                        <td class="fw-semibold text-secondary">{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="flex-shrink-0">
                                                    <img
                                                        src="{{ $productImage }}"
                                                        alt="{{ $product->name }}"
                                                        class="product-table-thumb rounded-3 border"
                                                    >
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ $product->name }}</div>
                                                    <div class="gateway-helper">{{ $product->display_name }}</div>
                                                    <div class="gateway-helper">Slug: {{ $product->slug }}</div>
                                                    <div class="gateway-helper">Tx: {{ number_format($product->transactions_count ?? 0) }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold">{{ $product->category->name ?? 'N/A' }}</div>
                                            <div class="gateway-helper">{{ $product->category->display_name ?? 'N/A' }}</div>
                                        </td>
                                        <td>{{ $product->api->name ?? 'N/A' }}</td>
                                        <td>
                                            <div class="gateway-helper">All: {{ $product->variations_count ?? 0 }}</div>
                                            <div class="gateway-helper text-success">Active: {{ $product->active_variations_count ?? 0 }}</div>
                                        </td>
                                        <td>
                                            <span class="gateway-badge {{ $product->status === 'active' ? 'gateway-badge--active' : 'gateway-badge--inactive' }}">
                                                {{ ucfirst($product->status ?? 'inactive') }}
                                            </span>
                                        </td>
                                        <td>{{ $product->created_at }}</td>
                                        @if(hasAccess('product.edit'))
                                            <td class="text-end">
                                                <div class="d-inline-flex flex-wrap justify-content-end gap-2">
                                                    <a class="btn btn-sm btn-outline-primary" href="{{ route('product.edit', $product->id) }}">Edit</a>
                                                    <a class="btn btn-sm btn-outline-secondary" onclick="return confirm('{{ $product->name }} will be duplicated!')" href="{{ route('duplicate.product', $product->id) }}">Duplicate</a>
                                                    @if($product->has_variations === 'yes')
                                                        <a class="btn btn-sm btn-outline-success" href="{{ route('product.variations', $product->id) }}">Variations</a>
                                                        <a class="btn btn-sm btn-outline-info" href="{{ route('variations.pull', $product->id) }}">
                                                            {{ $variationCount > 0 ? 'Re-pull variations' : 'Pull variations' }}
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ hasAccess('product.edit') ? 8 : 7 }}">
                                            <div class="alert alert-light border mb-0">No products found.</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        document.querySelectorAll('form select').forEach(function (element) {
            element.addEventListener('change', function () {
                if (this.form) {
                    this.form.submit();
                }
            });
        });
    </script>
@endsection
