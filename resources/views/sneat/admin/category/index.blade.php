@extends('sneat.layouts.app')

@section('title', 'Categories')

@section('content')
    @php
        $activeCategories = $categories->where('status', 'active')->count();
    @endphp

    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="admin-page-hero mb-4">
                <div>
                    <span class="admin-page-hero__kicker">Catalogue</span>
                    <h1>Categories</h1>
                    <p>Keep service groups, ordering, and visibility controls organized in a clean table view.</p>
                </div>
                <div class="admin-page-badges">
                    <div class="admin-page-badge">
                        <span>Total categories</span>
                        <strong>{{ number_format($categories->count()) }}</strong>
                    </div>
                    <div class="admin-page-badge">
                        <span>Active categories</span>
                        <strong>{{ number_format($activeCategories) }}</strong>
                    </div>
                    <a href="{{ route('category.create') }}" class="btn btn-admin-submit">Add category</a>
                </div>
            </div>

            @include('sneat.layouts.alerts')

            <div class="gateway-card card">
                <div class="card-header">
                    <div>
                        <h3>Category list</h3>
                        <p>View category metadata, edit, or remove unused entries.</p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table gateway-table align-middle" id="category-table">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Name</th>
                                    <th>Order</th>
                                    <th>Products</th>
                                    <th>Status</th>
                                    <th>Date Added</th>
                                    @if(hasAccess('category.edit') || hasAccess('category.destroy'))
                                        <th class="text-end">Action</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories as $category)
                                    @php $serialNumber = method_exists($categories, 'firstItem') ? $categories->firstItem() + $loop->index : $loop->iteration; @endphp
                                    <tr>
                                        <td>{{ $serialNumber }}</td>
                                        <td>
                                            <div class="fw-semibold">{{ $category->name }}</div>
                                            <div class="gateway-helper">{{ $category->display_name ?: 'No display name' }}</div>
                                            <div class="gateway-helper">Slug: {{ $category->slug }}</div>
                                        </td>
                                        <td>{{ $category->order }}</td>
                                        <td>{{ number_format((int) ($category->products_count ?? 0)) }}</td>
                                        <td>
                                            <span class="gateway-badge {{ $category->status === 'active' ? 'gateway-badge--active' : 'gateway-badge--inactive' }}">
                                                {{ ucfirst($category->status ?? 'inactive') }}
                                            </span>
                                        </td>
                                        <td>{{ $category->created_at }}</td>
                                        @if(hasAccess('category.edit') || hasAccess('category.destroy'))
                                            <td class="text-end">
                                                <div class="d-inline-flex flex-wrap justify-content-end gap-2">
                                                    @if(hasAccess('category.edit'))
                                                        <a href="{{ route('category.edit', $category->id) }}" class="gateway-action">View / Edit</a>
                                                    @endif
                                                    @if($category->products_count < 1 && hasAccess('category.destroy'))
                                                        <form action="{{ route('category.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete forever?');" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="gateway-action gateway-action--danger border-0 bg-transparent">Delete</button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ hasAccess('category.edit') || hasAccess('category.destroy') ? 6 : 5 }}">
                                            <div class="alert alert-light border mb-0">No categories found.</div>
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
            $('#category-table').DataTable({
                ordering: false,
                paging: false,
                info: false,
                dom: 'frt',
            });
        </script>
@endsection
