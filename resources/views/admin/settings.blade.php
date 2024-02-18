@extends('layouts.app')
@section('page-css')
@endsection
@section('content')
<!-- Content wrapper -->
 <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
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
                                                        <h4 class="card-title">Settings</h4>
                                                        @include('layouts.alerts')
                                                    </div>
                                                    <div class="card-content">
                                                        <div class="card-body">
                                                            <form action="{{route('settings.update')}}" method="POST" enctype="multipart/form-data">
                                                                @csrf
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <fieldset class="form-group">
                                                                            <label for="currency">Currency</label>
                                                                            <select name="currency" class="form-control" id="currency" required>
                                                                                <option value="">Select</option>
                                                                                @foreach($currencies as $currency)
                                                                                    <option value="{{ $currency }}" {{ $currency == $settings->currency ? 'selected' : ''}}>{!! $currency !!}</option>
                                                                                @endforeach
                                                                            </select>
                                                                           
                                                                        </fieldset>
                                                                        
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <fieldset class="form-group">
                                                                            <label for="basicInputFile">Logo</label>
                                                                            <div class="custom-file">
                                                                                <input type="file" accept="image/*" class="custom-file-input" id="image" name="logo">
                                                                                <label class="custom-file-label" for="image">Replace Logo</label>
                                                                            </div>
                                                                        </fieldset>

                                                                        <fieldset class="form-group">
                                                                            <label for="basicInputFile">Favicon</label>
                                                                            <div class="custom-file">
                                                                                <input type="file" accept="image/*" class="custom-file-input" id="favicon" name="favicon">
                                                                                <label class="custom-file-label" for="image">Replace Favicon</label>
                                                                            </div>
                                                                        </fieldset>
                                                                        <fieldset class="form-group">
                                                                            <label for="seo_description">SEO Description</label>
                                                                            <textarea class="form-control" id="seo_description" rows="3" name="seo_description" value="{{ $settings->seo_description ?? old('seo_description') }}" placeholder="SEO Description">{{ $settings->seo_description ?? old('seo_description') }}</textarea>
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