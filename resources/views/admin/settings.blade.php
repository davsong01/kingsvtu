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
                                                                            <label for="official_email">Official Email</label>
                                                                            <input type="text" class="form-control" id="official_email" name="official_email" value="{{ $settings->official_email ?? old('official_email') }}" placeholder="Official email">
                                                                        </fieldset>
                                                                        <fieldset class="form-group">
                                                                            <label for="whatsapp_number">Whatsapp Number</label>
                                                                            <input type="text" class="form-control" id="whatsapp_number" name="whatsapp_number" value="{{ $settings->whatsapp_number ?? old('whatsapp_number') }}" placeholder="Whatsapp number">
                                                                        </fieldset>
                                                                        <fieldset class="form-group">
                                                                            <label for="login_email_notification">Customer Login Email Notification</label>
                                                                            <select name="login_email_notification" class="form-control" id="login_email_notification" required>
                                                                                <option value="">Select</option>
                                                                                <option value="yes" {{ $settings->login_email_notification == 'yes' ? 'selected' : ''}}>Yes</option>
                                                                                <option value="no" {{ $settings->login_email_notification == 'no' ? 'selected' : ''}}>No</option>
                                                                                
                                                                            </select>
                                                                        </fieldset>
                                                                        <fieldset class="form-group">
                                                                            <label for="transaction_email_notification">Transaction Email Notification</label>
                                                                            <select name="transaction_email_notification" class="form-control" id="transaction_email_notification" required>
                                                                                <option value="">Select</option>
                                                                                <option value="yes" {{ $settings->transaction_email_notification == 'yes' ? 'selected' : ''}}>Yes</option>
                                                                                <option value="no" {{ $settings->transaction_email_notification == 'no' ? 'selected' : ''}}>No</option>
                                                                                
                                                                            </select>
                                                                        </fieldset>
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
                                                                            <label for="seo_title">SEO Title</label>
                                                                            <input type="text" class="form-control" id="seo_title" name="seo_title" value="{{ $settings->seo_title ?? old('seo_title') }}" placeholder="SEO Title">
                                                                        </fieldset>
                                                                        <fieldset class="form-group">
                                                                            <label for="seo_description">SEO Description</label>
                                                                            <textarea class="form-control" id="seo_description" rows="3" name="seo_description" value="{{ $settings->seo_description ?? old('seo_description') }}" placeholder="SEO Description" required>{{ $settings->seo_description ?? old('seo_description') }}</textarea>
                                                                        </fieldset>
                                                                        <fieldset class="form-group">
                                                                            <label for="google_ad_code">Google Ad Code</label>
                                                                            <textarea class="form-control" id="google_ad_code" rows="3" name="google_ad_code" value="{{ $settings->google_ad_code ?? old('google_ad_code') }}" placeholder="Google ad code">{{ $settings->google_ad_code ?? old('google_ad_code') }}</textarea>
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