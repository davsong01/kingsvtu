@extends('layouts.app')
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
                                    <li class="breadcrumb-item"><a href="/"><i class="bx bx-home-alt"></i></a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="{{ route('product.index') }}">Products</a>
                                    </li>
                                    <li class="breadcrumb-item active">Add Product
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
                                    <h4 class="card-title">Add Product</h4>
                                     <p>Add new product</p>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <form action="{{route('product.store')}}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <fieldset class="form-group">
                                                        <label for="name">Name</label>
                                                        <input type="text" class="form-control" id="name" placeholder="Enter name" required>
                                                    </fieldset>
                                                    <fieldset class="form-group">
                                                        <label for="display_name">Display Name</label>
                                                        <input type="text" class="form-control" id="display_name" placeholder="Enter display name" required>
                                                    </fieldset>
                                                    <fieldset class="form-group">
                                                        <label for="category">Category</label>
                                                        <select class="form-control" name="category" id="category" required>
                                                            <option value="">Select</option>
                                                            @foreach ($categories as $category)
                                                                <option value="{{ $category->id  }}" {{ old('category') == 'active' ? 'selected' : ''}}>{{ $category->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </fieldset>
                                                    
                                                    <fieldset class="form-group">
                                                        <label for="description">Description</label>
                                                        <textarea class="form-control" id="description" rows="3" placeholder="Description" required></textarea>
                                                    </fieldset>
                                                    <fieldset class="form-group">
                                                        <label for="seo_title">SEO Title</label>
                                                        <input type="text" class="form-control" id="seo_title" placeholder="Enter SEO Title">
                                                    </fieldset>
                                                    <fieldset class="form-group">
                                                        <label for="seo_keywords">SEO Keywords</label>
                                                        <input type="text" class="form-control" placeholder="Enter SEO Keywords" id="seo_keywords">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-6">
                                                    <fieldset class="form-group">
                                                        <label for="slug">Slug</label>
                                                        <input type="text" class="form-control" placeholder="Enter slug" id="slug" required>
                                                    </fieldset>
                                                    <fieldset class="form-group">
                                                        <label for="basicInputFile">Display Image</label>
                                                        <div class="custom-file">
                                                            <input type="file" accept="image/*" class="custom-file-input" id="image">
                                                            <label class="custom-file-label" for="image">Choose file</label>
                                                        </div>
                                                    </fieldset>
                                                    <fieldset class="form-group">
                                                        <label for="helperText">API to use</label>
                                                        <select class="form-control" name="api" id="api" required>
                                                            <option value="">Select</option>
                                                            @foreach ($apis as $item)
                                                                <option value="{{ $item->id  }}" {{ old('api') == 'active' ? 'selected' : ''}}>{{ $item->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </fieldset>
                                                    <fieldset class="form-group">
                                                        <label for="helperText">Status</label>
                                                        <select class="form-control" name="status" id="status" required>
                                                            <option value="">Select</option>
                                                            <option value="{{ old('status') == 'active' ? 'selected' : ''}}">Active</option>
                                                            <option value="{{ old('status') == 'inactive' ? 'selected' : ''}}">InActive</option>
                                                        </select>
                                                    </fieldset>
                                                    <fieldset class="form-group">
                                                        <label for="seo_description">SEO Description</label>
                                                        <textarea class="form-control" id="seo_description" rows="3" placeholder="SEO Description"></textarea>
                                                    </fieldset>
                                                   
                                                </div>
                                                <div class="col-md-12">
                                                <button class="btn btn-primary" type="submit">Submit</button>
    
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
@endsection
@section('page-script')
<script src="{{ asset('app-assets/js/scripts/pages/dashboard-analytics.js') }}"></script>

@endsection