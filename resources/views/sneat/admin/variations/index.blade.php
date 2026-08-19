@extends('sneat.layouts.app')

@section('title', 'Variations')

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="admin-page-hero mb-4">
                <div>
                    <span class="admin-page-hero__kicker">Catalogue</span>
                    <h1>Variations</h1>
                    <p>Browse every variation in the system, filter them quickly, and jump into the product editor when you need to make changes.</p>
                </div>
                <div class="admin-page-badges">
                    <div class="admin-page-badge">
                        <span>Total variations</span>
                        <strong>{{ number_format((int) ($summary['total'] ?? $variations->total())) }}</strong>
                    </div>
                    <div class="admin-page-badge">
                        <span>Active variations</span>
                        <strong>{{ number_format((int) ($summary['active'] ?? 0)) }}</strong>
                    </div>
                    <div class="admin-page-badge">
                        <span>Products covered</span>
                        <strong>{{ number_format((int) ($summary['products'] ?? 0)) }}</strong>
                    </div>
                </div>
            </div>

            @include('sneat.layouts.alerts')

            <div class="gateway-card card mb-4">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div>
                        <h3>Variation filters</h3>
                        <p>Search by variation name, code, product, provider, status, or creation date.</p>
                    </div>
                    <div class="text-muted small">
                        Showing {{ $variations->firstItem() ?? 0 }} - {{ $variations->lastItem() ?? 0 }} of {{ number_format($variations->total()) }}
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('variations.index') }}" class="row g-3">
                        <div class="col-lg-4 col-md-6">
                            <label class="modern-admin-label">Search</label>
                            <input type="text" name="search" class="form-control form-control-{{ formControlSize() }}" value="{{ $filters['search'] ?? '' }}" placeholder="Variation name, slug, or code">
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <label class="modern-admin-label">Product</label>
                            <select name="product" class="form-select form-select-{{ formControlSize() }}">
                                <option value="">All</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" @selected((string)($filters['product'] ?? '') === (string) $product->id)>
                                        {{ $product->display_name ?: $product->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <label class="modern-admin-label">Provider</label>
                            <select name="api" class="form-select form-select-{{ formControlSize() }}">
                                <option value="">All</option>
                                @foreach($apis as $api)
                                    <option value="{{ $api->id }}" @selected((string)($filters['api'] ?? '') === (string) $api->id)>
                                        {{ $api->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label class="modern-admin-label">Status</label>
                            <select name="status" class="form-select form-select-{{ formControlSize() }}">
                                <option value="">All</option>
                                <option value="active" @selected(($filters['status'] ?? '') === 'active')>Active</option>
                                <option value="inactive" @selected(($filters['status'] ?? '') === 'inactive')>Inactive</option>
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label class="modern-admin-label">Date from</label>
                            <input type="date" name="date_from" class="form-control form-control-{{ formControlSize() }}" value="{{ $filters['date_from'] ?? '' }}">
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label class="modern-admin-label">Date to</label>
                            <input type="date" name="date_to" class="form-control form-control-{{ formControlSize() }}" value="{{ $filters['date_to'] ?? '' }}">
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label class="modern-admin-label">Per page</label>
                            <select name="per_page" class="form-select form-select-{{ formControlSize() }}">
                                @foreach([10, 15, 25, 50, 100] as $size)
                                    <option value="{{ $size }}" @selected((int)($filters['per_page'] ?? 15) === $size)>{{ $size }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 d-flex flex-wrap gap-2">
                            <button type="submit" class="btn btn-admin-submit">Filter</button>
                            <a href="{{ route('variations.index') }}" class="btn btn-outline-secondary">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="gateway-card card">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div>
                        <h3>All variations</h3>
                        <p>Use the edit action to open the product variation editor for that item.</p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table gateway-table align-middle">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Variation</th>
                                    <th>Product</th>
                                    <th>Provider</th>
                                    <th>Status</th>
                                    <th>Transactions</th>
                                    <th>Date Added</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($variations as $variation)
                                    @php
                                        $serialNumber = method_exists($variations, 'firstItem') ? $variations->firstItem() + $loop->index : $loop->iteration;
                                        $variationLabel = $variation->system_name ?: $variation->api_name ?: $variation->slug ?: 'Variation #' . $variation->id;
                                    @endphp
                                    <tr>
                                        <td class="fw-semibold text-secondary">{{ $serialNumber }}</td>
                                        <td>
                                            <div class="fw-semibold">{{ $variationLabel }}</div>
                                            <div class="gateway-helper">Code: {{ $variation->api_code ?: 'N/A' }}</div>
                                            <div class="gateway-helper">Slug: {{ $variation->slug ?: 'N/A' }}</div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold">{{ $variation->product->display_name ?? $variation->product->name ?? 'N/A' }}</div>
                                            <div class="gateway-helper">{{ $variation->product->slug ?? 'N/A' }}</div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold">{{ $variation->api->name ?? 'N/A' }}</div>
                                            <div class="gateway-helper">{{ $variation->api->slug ?? 'N/A' }}</div>
                                        </td>
                                        <td>
                                            <span class="gateway-badge {{ $variation->status === 'active' ? 'gateway-badge--active' : 'gateway-badge--inactive' }}">
                                                {{ ucfirst($variation->status ?? 'inactive') }}
                                            </span>
                                        </td>
                                        <td>{{ number_format((int) ($variation->transaction_count ?? 0)) }}</td>
                                        <td>{{ $variation->created_at }}</td>
                                        <td class="text-end">
                                            <div class="d-inline-flex flex-wrap justify-content-end gap-2">
                                                @if(hasAccess('product.edit'))
                                                    <a href="{{ route('product.variations', $variation->product_id) }}" class="gateway-action">Edit</a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8">
                                            <div class="alert alert-light border mb-0">No variations found.</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mt-4">
                        <div class="text-muted small">
                            Showing {{ $variations->firstItem() ?? 0 }} - {{ $variations->lastItem() ?? 0 }} of {{ number_format($variations->total()) }}
                        </div>
                        {{ $variations->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
