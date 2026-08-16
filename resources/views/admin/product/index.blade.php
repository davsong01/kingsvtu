@extends('layouts.app')
@section('title', 'All Products')
@section('page-css')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/vendors.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css') }}"> 
    
    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/vendors.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <!-- END: Vendor CSS-->
    
@endsection
@section('content')
<!-- Content wrapper -->
 <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-12 mb-2 mt-1">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb p-0 mb-0">
                                    <li class="breadcrumb-item"><a href="{{ route('dashboard', request()->array)}}"><i class="bx bx-home-alt"></i></a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="{{ route('product.index') }}">Products</a>
                                    </li>
                                    <li class="breadcrumb-item active">All Products
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <!-- Column selectors with Export Options and print table -->
                <section id="column-selectors">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                @include('layouts.alerts')
                                <div class="card-header">
                                    <h4 class="card-title">All products</h4> <br>
                                    <a href="{{ route('product.create') }}"><button id="addRow" class="btn btn-primary mb-2 d-flex align-items-center"><i class="bx bx-plus"></i>&nbsp; Add Product</button></a>
                                </div>
                                <div class="card-content">
                                    <div class="card-body card-dashboard">
                                        <form method="GET" action="{{ route('product.index') }}" class="row mb-3">
                                            <div class="col-md-3 mb-2">
                                                <input type="text" name="search" class="form-control" value="{{ $filters['search'] ?? '' }}" placeholder="Search name, display name, slug">
                                            </div>
                                            <div class="col-md-2 mb-2">
                                                <select name="status" class="form-control">
                                                    <option value="">All status</option>
                                                    <option value="active" {{ ($filters['status'] ?? '') === 'active' ? 'selected' : '' }}>Active</option>
                                                    <option value="inactive" {{ ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2 mb-2">
                                                <select name="category" class="form-control">
                                                    <option value="">All categories</option>
                                                    @foreach($categories as $category)
                                                        <option value="{{ $category->id }}" {{ (string)($filters['category'] ?? '') === (string)$category->id ? 'selected' : '' }}>
                                                            {{ $category->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-2 mb-2">
                                                <select name="api" class="form-control">
                                                    <option value="">All APIs</option>
                                                    @foreach($apis as $api)
                                                        <option value="{{ $api->id }}" {{ (string)($filters['api'] ?? '') === (string)$api->id ? 'selected' : '' }}>
                                                            {{ $api->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-2 mb-2">
                                                <select name="has_variations" class="form-control">
                                                    <option value="">All variations</option>
                                                    <option value="yes" {{ ($filters['has_variations'] ?? '') === 'yes' ? 'selected' : '' }}>Yes</option>
                                                    <option value="no" {{ ($filters['has_variations'] ?? '') === 'no' ? 'selected' : '' }}>No</option>
                                                </select>
                                            </div>
                                            <div class="col-md-1 mb-2">
                                                <select name="per_page" class="form-control">
                                                    @foreach([10, 15, 25, 50, 100] as $size)
                                                        <option value="{{ $size }}" {{ (int)($filters['per_page'] ?? 15) === $size ? 'selected' : '' }}>{{ $size }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-12 d-flex align-items-center gap-2 mb-2">
                                                <button type="submit" class="btn btn-primary">Filter</button>
                                                <a href="{{ route('product.index') }}" class="btn btn-outline-secondary">Reset</a>
                                            </div>
                                        </form>
                                        <div class="table-responsive">
                                            <table class="table table-striped" id="dtable">
                                                <thead>
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Category</th>
                                                        <th>Variations</th>
                                                        <th>Status</th>
                                                        <th>Date Added</th>
                                                        @if(hasAccess('product.edit'))
                                                        <th>Actions</th>
                                                        @endif
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ( $products as $product )
                                                    <tr>
                                                        <td><img src="{{asset($product->image)}}" alt="{{$product->id}}" style="width: 50px;float:left">{{ $product->name }} <br>
                                                            <span style="color:blue"><small>({{ $product->display_name }}) </small></span><br> <strong>Slug: </strong>{{ $product->slug }} <br>
                                                            <strong>No. of Transactions</strong> {{ number_format($product->transactions_count ?? 0) }} <br>
                                                        </td>
                                                       
                                                        <td>{{ $product->category->name }} <br>
                                                            <strong>API:</strong> {{ $product->api->name }}
                                                        </td>
                                                        <td>
                                                            All: {{ $product->variations_count ?? 0 }} <br>
                                                            <span style="color:green">Active: {{ $product->active_variations_count ?? 0 }}</span>
                                                        </td>
                                                        <td>{{ $product->status }}</td>
                                                        <td>{{ $product->created_at }}</td>
                                                        @if(hasAccess('product.edit'))
                                                        <td>
                                                            <a class="btn btn-primary btn-sm mr-1 mb-1" href="{{ route('product.edit', $product->id) }}"><i class="bx bxs-pencil"></i><span class="align-middle ml-25">View</span></button></a>
                                                            <a class="btn btn-info btn-sm mr-1 mb-1" onclick="return confirm('{{$product->name}} will be duplicated!')" href="{{ route('duplicate.product', $product->id) }}"><i class="bx bxs-copy"></i><span class="align-middle ml-25">Duplicate</span></button></a>
                                                            @if($product->has_variations == 'yes')
                                                            <a class="btn btn-dark btn-sm mr-1 mb-1" href="{{ route('product.edit', $product->id) }}"><i class="bx bxs-copy"></i><span class="align-middle ml-25">Edit Variations</span></button></a>
                                                            @endif
                                                        </td>
                                                        @endif
                                                    </tr>
                                                    @empty
                                                    <tr>
                                                        <td colspan="{{ hasAccess('product.edit') ? 6 : 5 }}">No products found.</td>
                                                    </tr>
                                                    @endforelse
                                                </tbody>
                                                
                                            </table>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <div class="text-muted small">
                                                Showing {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }} of {{ number_format($products->total()) }}
                                            </div>
                                            {{ $products->links() }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- Column selectors with Export Options and print table -->
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
