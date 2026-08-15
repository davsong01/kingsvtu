@extends('sneat.layouts.app')

@section('title', 'Products')

@section('content')
    @php
        $activeProducts = $products->where('status', 'active')->count();
        $variationProducts = $products->where('has_variations', 'yes')->count();
    @endphp

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
                        <strong>{{ number_format($products->count()) }}</strong>
                    </div>
                    <div class="admin-page-badge">
                        <span>Active products</span>
                        <strong>{{ number_format($activeProducts) }}</strong>
                    </div>
                    <div class="admin-page-badge">
                        <span>Variation products</span>
                        <strong>{{ number_format($variationProducts) }}</strong>
                    </div>
                    <a href="{{ route('product.create') }}" class="btn btn-admin-submit">Add product</a>
                </div>
            </div>

            @include('sneat.layouts.alerts')

            <div class="gateway-card card">
                <div class="card-header">
                    <h3>Product list</h3>
                    <p>Jump into edit, duplicate, or manage variations.</p>
                </div>
                <div class="card-body">
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
                                                    <div class="gateway-helper">Tx: {{ number_format($product->transactions->count()) }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold">{{ $product->category->name ?? 'N/A' }}</div>
                                            <div class="gateway-helper">{{ $product->category->display_name ?? 'N/A' }}</div>
                                        </td>
                                        <td>{{ $product->api->name ?? 'N/A' }}</td>
                                        <td>
                                            <div class="gateway-helper">All: {{ $product->variations()->count() }}</div>
                                            <div class="gateway-helper text-success">Active: {{ $product->variations()->where('status', 'active')->count() }}</div>
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
                                                        <a class="btn btn-sm btn-outline-success" href="{{ route('product.edit', $product->id) }}?tab=variations">Variations</a>
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
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/datatables.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('app-assets/js/scripts/datatables/datatable.js') }}"></script>
    <script>
        $('#product-table').DataTable({
            ordering: false,
        });
    </script>
@endsection
