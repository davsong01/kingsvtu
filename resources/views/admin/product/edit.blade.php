<?php 

    use Illuminate\Support\Facades\Session;
    $page = Session::get('page') ?? 1;
?>
@extends('layouts.app')
@section('page-css')
<style>
    .tiny{
        padding: 1.5px !important;
        font-size: 11px !important;
    }

</style>
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
                                    <li class="breadcrumb-item"><a href="/"><i class="bx bx-home-alt"></i></a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="{{ route('product.index') }}">Products</a>
                                    </li>
                                    <li class="breadcrumb-item active">Edit {{$product->name}}
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
                                 <div class="content-body">
                                    <!-- Nav Filled Starts -->
                                    <section id="nav-filled">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h4 class="card-title">Edit {{$product->name}}</h4>
                                                        @include('layouts.alerts')
                                                    </div>
                                                    <div class="card-content">
                                                        <div class="card-body">
                                                            <p>
                                                                
                                                            </p>
                                                            <!-- Nav tabs -->
                                                           
                                                            <ul class="nav nav-tabs nav-fill" id="myTab" role="tablist">
                                                                <li class="nav-item">
                                                                    <a class="nav-link {{ $page == 1 ? 'active' : ''}}" id="home-tab-fill" data-toggle="tab" href="#product-details" role="tab" aria-controls="product-details" aria-selected="true">
                                                                        Product Details
                                                                    </a>
                                                                </li>
                                                                <li class="nav-item">
                                                                    <a class="nav-link {{ $page == 2 ? 'active' : ''}}" id="profile-tab-fill" data-toggle="tab" href="#variations" role="tab" aria-controls="variations" aria-selected="false">
                                                                        Variations
                                                                    </a>
                                                                </li>
                                                                {{-- <li class="nav-item">
                                                                    <a class="nav-link" id="messages-tab-fill" data-toggle="tab" href="#settings" role="tab" aria-controls="settings" aria-selected="false">
                                                                        General Settings
                                                                    </a>
                                                                </li> --}}
                                                            </ul>

                                                            <!-- Tab panes -->
                                                            <div class="tab-content pt-1">
                                                                <div class="tab-pane {{ $page == 1 ? 'active' : ''}}" id="product-details" role="tabpanel" aria-labelledby="home-tab-fill">
                                                                    <form action="{{route('product.update', $product->id)}}" method="POST" enctype="multipart/form-data">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <div class="row">
                                                                            <div class="col-md-6">
                                                                                <fieldset class="form-group">
                                                                                    <label for="name">Name</label>
                                                                                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter name" value="{{ $product->name ?? old('name')}}" required>
                                                                                </fieldset>
                                                                                <fieldset class="form-group">
                                                                                    <label for="display_name">Display Name</label>
                                                                                    <input type="text" class="form-control" id="display_name" name="display_name" placeholder="Enter display name" value="{{ $product->display_name ?? old('display_name')}}" required>
                                                                                </fieldset>
                                                                                <fieldset class="form-group">
                                                                                    <label for="category">Category</label>
                                                                                    <select class="form-control" name="category" id="category" required>
                                                                                        <option value="">Select</option>
                                                                                        @foreach ($categories as $category)
                                                                                            <option value="{{ $category->id  }}" {{ $product->category_id == $category->id ? 'selected' : ''}}>{{ $category->name }}</option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </fieldset>
                                                                                
                                                                                <fieldset class="form-group">
                                                                                    <label for="description">Description</label>
                                                                                    <textarea class="form-control" id="description" name="description" rows="3" placeholder="Description" value="{{ $product->description ??  old('description')}}"></textarea>
                                                                                </fieldset>
                                                                                <fieldset class="form-group">
                                                                                    <label for="seo_title">SEO Title</label>
                                                                                    <input type="text" class="form-control" id="seo_title"  name="seo_title" placeholder="Enter SEO Title" value="{{ $product->seo_title ?? old('seo_title')}}">
                                                                                </fieldset>
                                                                                <fieldset class="form-group">
                                                                                    <label for="seo_keywords">SEO Keywords</label>
                                                                                    <input type="text" class="form-control"  name="seo_keywords" placeholder="Enter SEO Keywords" id="seo_keywords" value="{{ $product->seo_keywords ?? old('seo_keywords')}}">
                                                                                </fieldset>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <fieldset class="form-group">
                                                                                    <label for="slug">Slug</label>
                                                                                    <input type="text" class="form-control" name="slug" placeholder="Enter slug" id="slug" value="{{ $product->slug ?? old('slug')}}" required>
                                                                                </fieldset>
                                                                                <fieldset class="form-group">
                                                                                    <label for="basicInputFile">Display Image</label>
                                                                                    <div class="custom-file">
                                                                                        <input type="file" accept="image/*" class="custom-file-input" id="image" name="image">
                                                                                        <label class="custom-file-label" for="image">Replace file</label>
                                                                                    </div>
                                                                                </fieldset>
                                                                                <fieldset class="form-group">
                                                                                    <label for="helperText">API to use</label>
                                                                                    <select class="form-control" name="api" id="api" required>
                                                                                        <option value="">Select</option>
                                                                                        @foreach ($apis as $item)
                                                                                            <option value="{{ $item->id  }}" {{ $product->api_id == $item->id ? 'selected' : ''}}>{{ $item->name }}</option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </fieldset>
                                                                                <fieldset class="form-group">
                                                                                    <label for="status">Status</label>
                                                                                    <select class="form-control" name="status" id="status" required>
                                                                                        <option value="">Select</option>
                                                                                        <option value="active" {{ $product->status == 'active' ? 'selected' : ''}}>Active</option>
                                                                                        <option value="inactive" {{ $product->status == 'inactive' ? 'selected' : ''}}>InActive</option>
                                                                                    </select>
                                                                                </fieldset>
                                                                                <fieldset class="form-group">
                                                                                    <label for="seo_description">SEO Description</label>
                                                                                    <textarea class="form-control" id="seo_description" rows="3" name="seo_description" value="{{ $product->seo_description ?? old('seo_description') }}" placeholder="SEO Description">{{ $product->seo_description ?? old('seo_description') }}</textarea>
                                                                                </fieldset>
                                                                                <input type="hidden" value="page1" name="route">
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <button class="btn btn-primary" type="submit">Update</button>
                                                                            </div>
                                                                        </div>
                                                                    </form>
                                                                </div>

                                                                <div class="tab-pane {{ isset($page) && $page == 2 ? 'active' : ''}}" id="variations" role="tabpanel" aria-labelledby="profile-tab-fill">
                                                                    @if($product->has_variations == 'yes')
                                                                        

                                                                        @if($product->variations->count() < 1)
                                                                            <a href="{{ route('variations.pull', $product->id) }}"><button id="addRow" class="btn btn-primary mb-2 d-flex align-items-center"><i class="bx bx-plus"></i>&nbsp; Pull Variations</button></a>
                                                                        @else
                                                                            <a href="{{ route('variations.pull', $product->id) }}"><button id="addRow" class="btn btn-primary mb-2 d-flex align-items-center"><i class="bx bx-plus"></i>&nbsp; Re Pull Variations</button></a>

                                                                            <form action="{{route('variations.update', $product->id)}}" method="POST" enctype="multipart/form-data">
                                                                                @csrf
                                                                                @foreach($product->variations as $variation)
                                                                                <div class="row">
                                                                                    <div class="col-md-2">
                                                                                        <fieldset class="form-group">
                                                                                            <label for="api_name">API Name</label>
                                                                                            <input type="text" class="form-control-sm tiny" id="api_name" name="api_name[{{ $variation->id }}]"  value="{{ $variation->api_name }}" disabled>
                                                                                        </fieldset>
                                                                                    </div>
                                                                                   
                                                                                    <div class="col-md-3">
                                                                                        <fieldset class="form-group">
                                                                                            <label for="name">System Name</label>
                                                                                            <input type="text" class="form-control-sm tiny" id="system_name" name="system_name[{{ $variation->id }}]"  value="{{ $variation->system_name }}">
                                                                                        </fieldset>
                                                                                    </div>
                                                                                    <div class="col-md-2">
                                                                                        <fieldset class="form-group">
                                                                                            <label for="name">Slug</label>
                                                                                            <input type="text" class="form-control-sm tiny" id="slug" name="slug[{{ $variation->id }}]"  value="{{ $variation->slug }}">
                                                                                        </fieldset>
                                                                                    </div>
                                                                                    <div class="col-md-2">
                                                                                        <fieldset class="form-group">
                                                                                            <label for="name">API Price</label>
                                                                                            <input type="text" class="form-control-sm tiny" id="api_price" name="api_price[{{ $variation->id }}]"  value="{{ $variation->api_price }}">
                                                                                        </fieldset>
                                                                                    </div>
                                                                                    <div class="col-md-2">
                                                                                        <fieldset class="form-group">
                                                                                            <label for="name">SYSTEM Price</label>
                                                                                            <input type="text" class="form-control-sm tiny" id="system_price" name="system_price[{{ $variation->id }}]"  value="{{ $variation->system_price }}">
                                                                                        </fieldset>
                                                                                    </div>
                                                                                    <div class="col-md-1">
                                                                                        <fieldset class="form-group">
                                                                                            <label for="status">Status</label>
                                                                                            <select class="form-control-sm tiny" name="status[{{ $variation->id }}]" id="status" required>
                                                                                                <option value="">Select</option>
                                                                                                <option value="active" {{ $variation->status == 'active' ? 'selected' : ''}}>Active</option>
                                                                                                <option value="inactive" {{ $variation->status == 'inactive' ? 'selected' : ''}}>InActive</option>
                                                                                                
                                                                                            </select>
                                                                                        </fieldset>
                                                                                        
                                                                                    </div>
                                                                                </div>
                                                                                <input type="hidden" name="variation_id[{{$variation->id}}]" value="{{$variation->id}}">
                                                                                @endforeach
                                                                                <div class="row">
                                                                                    <div class="col-md-12">
                                                                                        
                                                                                        <button class="btn btn-primary" type="submit">Submit</button>
                                                                                    </div>
                                                                                </div>
                                                                            </form>
                                                                        @endif
                                                                        
                                                                    @endif
                                                                </div>
                                                                <div class="tab-pane" id="settings" role="tabpanel" aria-labelledby="messages-tab-fill">
                                                                    <p>
                                                                        Biscuit powder jelly beans. Lollipop candy canes croissant icing chocolate cake. Cake fruitcake powder
                                                                        pudding pastry.
                                                                    </p>
                                                                </div>
                                                                
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                    <!-- Nav Filled Ends -->
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