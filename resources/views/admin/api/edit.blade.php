@extends('layouts.app')
@section('page-css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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
                                    <li class="breadcrumb-item"><a href="{{ route('api.index') }}">API Providers</a>
                                    </li>
                                    <li class="breadcrumb-item active">Edit {{ $api->name }}
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <!-- Basic Inputs start -->
                <section id="basic-input">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                    <div class="card-header">
                                    <h4 class="card-title">Edit {{ $api->name }}</h4>
                                    @include('layouts.alerts')
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <form action="{{route('api.update', $api->id)}}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            @method('PATCH')
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <fieldset class="form-group">
                                                        <label for="name">Name</label>
                                                        <input type="text" class="form-control" id="name" name="name" value="{{ $api->name ?? old('name') }}" placeholder="Enter name" required>
                                                    </fieldset>
                                                    <fieldset class="form-group">
                                                        <label for="slug">Slug</label>
                                                        <input type="text" class="form-control" name="slug" value="{{ $api->slug ?? old('slug') }}" placeholder="Enter slug" id="slug" required>
                                                    </fieldset>
                                                   
                                                    <fieldset class="form-group">
                                                        <label for="warning_threshold_status">Warning Threshold Status</label>
                                                        <select class="form-control" name="warning_threshold_status" id="warning_threshold_status" required>
                                                            <option value="">Select</option>
                                                            <option value="active" {{ $api->warning_threshold_status == 'active' ? 'selected' : ''}}>Active</option>
                                                            <option value="inactive" {{ $api->warning_threshold_status == 'inactive' ? 'selected' : ''}}>InActive</option>
                                                        </select>
                                                    </fieldset>
                                                     <fieldset class="form-group">
                                                        <label for="warning_threshold">Balance Warning Threshold</label>
                                                        <input type="number" class="form-control" name="warning_threshold" value="{{ $api->warning_threshold ?? old('warning_threshold') }}" placeholder="Enter warning threshold" id="warning_threshold">
                                                    </fieldset>
                                                     <fieldset class="form-group">
                                                        <label for="sandbox_base_url">Sandbox Base URL</label>
                                                        <input type="text" class="form-control" name="sandbox_base_url" value="{{ $api->sandbox_base_url }}" placeholder="Enter sandbox base url" id="sandbox_base_url">
                                                    </fieldset>
                                                    <fieldset class="form-group">
                                                        <label for="live_base_url">Live Base URL</label>
                                                        <input type="text" class="form-control" name="live_base_url" value="{{ $api->live_base_url }}" placeholder="Enter live base url" id="live_base_url">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-6">
                                                    <fieldset class="form-group">
                                                        <label for="status">API Status</label>
                                                        <select class="form-control" name="status" id="status" required>
                                                            <option value="">Select</option>
                                                            <option value="active" {{ $api->status == 'active' ? 'selected' : ''}}>Active</option>
                                                            <option value="inactive" {{ $api->status == 'inactive' ? 'selected' : ''}}>InActive</option>
                                                        </select>
                                                    </fieldset>
                                                    @if(strtolower((string) $api->slug) === 'autosync')
                                                        <div class="form-group">
                                                            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#pullProductsModal">
                                                                <i class="bx bx-download"></i> Pull products
                                                            </button>
                                                        </div>
                                                    @endif
                                                    <fieldset class="form-group">
                                                        <label for="file_name">File Name</label>
                                                        <input type="text" class="form-control" name="file_name" value="{{ $api->file_name ?? old('file_name') }}" placeholder="Enter file name" id="file_name" required>
                                                    </fieldset>
                                                    <fieldset class="form-group">
                                                        <label for="api_key">API Key</label>
                                                        <input type="text" class="form-control" name="api_key" value="{{ $api->api_key }}" placeholder="Enter api key" id="api_key">
                                                    </fieldset>
                                                    <fieldset class="form-group">
                                                        <label for="secret_key">Secret/Device Key/Transaction PIN</label>
                                                        <input type="text" class="form-control" name="secret_key" value="{{ $api->secret_key }}" placeholder="Enter secret key" id="secret_key">
                                                    </fieldset>
                                                    <fieldset class="form-group">
                                                        <label for="public_key">Public Key/Device Id</label>
                                                        <input type="text" class="form-control" name="public_key" value="{{ $api->public_key }}"  placeholder="Enter public key" id="public_key">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-12">
                                                    <button class="btn btn-primary" type="submit">Update</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    @if(strtolower((string) $api->slug) === 'autosync')
        <div class="modal fade" id="pullProductsModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form action="{{ route('api.pull.products') }}" method="POST">
                        @csrf
                        <input type="hidden" name="api_id" value="{{ $api->id }}">
                        <div class="modal-header">
                            <h5 class="modal-title">Pull products</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="pull_category_id">Category</label>
                                <select class="form-control js-example-basic-single" name="category_id" id="pull_category_id" data-placeholder="Search category">
                                    <option value="">Select category</option>
                                    @foreach(($categories ?? []) as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}{{ filled($category->slug) ? ' (' . $category->slug . ')' : '' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mb-0">
                                <label for="pull_category_slug">Category slug</label>
                                <input type="text" class="form-control" name="category_slug" id="pull_category_slug" placeholder="Optional slug override">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Pull products</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection
@section('page-script')
<script src="{{ asset('app-assets/js/scripts/pages/dashboard-analytics.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(function () {
        $('.js-example-basic-single').select2();
    });
</script>

@endsection
