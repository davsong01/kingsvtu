@extends('layouts.app')
@section('title', 'Create Shop')

@section('page-css')
<style>
    .reset-pin {
        font-size: 10px;
        float: right;
    }
    .verified{
        color: green !important;
        font-size: 13px;
        margin-top: -6px;
        display: inline-block;
        margin-left: 5px;
    }
    .unverified{
        color: orange !important;
        font-size: 13px;
        margin-top: -6px;
        display: inline-block;
        margin-left: 5px;
    }
    .key-field {
        padding: 4px;
        margin-bottom: 20px;
        width: 100%;
        border: #5A8DEE 2px solid;
        border-radius: 5px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .key-field i {
        cursor: pointer;
        font-size: 18px;
        padding: 5px;
    }
</style>
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
                                @if(empty(auth()->user()->customer->shop_request))
                                <section id="nav-filled">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="card">
                                                <div class="col-md-12"> 
                                                    <div class="card-header" style="padding:1.4rem 0.7rem">
                                                        <h4 class="card-title" style="color:green">Create new shop</h4>
                                                        <p><strong>You are about to create a new shop! </strong> <br>
                                                            Prior to completing the accompanying form, kindly review the outlined steps: <br>
                                                            <strong>STEPS FOR ESTABLISHING YOUR NEW STORE</strong>
                                                            <ol>
                                                                <li>Provide your shop particulars. Your shop slug, serving as the subdomain for your shop, should be included. Note that spaces and special characters will be excluded. For instance, your shop could be accessed via {{url('/')}}/{shop_slug}</li>
                                                                <li>Furnish Shop Admin details.</li>
                                                                <li>Submit your information.</li>
                                                                <li>Following these steps, an administrator will review your shop request, and you will subsequently receive an email notification after the review. </li>
                                                            </ol>
                                                        </p>
                                                        @include('layouts.alerts')
                                                    </div>
                                                </div>
                                                <div class="card-content">
                                                    <div class="card-body">
                                                        <form action="{{route('customer.shop.store')}}" method="POST" autocomplete="off" enctype="multipart/form-data">
                                                            @csrf
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <strong style="color:black">Shop Details</strong>
                                                                    <hr>
                                                                </div>
                                                                <div class="col-md-6">   
                                                                    <fieldset class="form-group">
                                                                        <label for="shop_name">Shop Name</label>
                                                                        <input type="text" name="shop_name" class="form-control" value="{{ old('shop_name') }}" required>
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col-md-6">   
                                                                    <fieldset class="form-group">
                                                                        <label for="shop_name">Shop slug</label>
                                                                        <input type="text" name="shop_slug" class="form-control" value="{{ auth()->user()->username ?? old('shop_slug') }}" required>
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col-md-6">   
                                                                    <fieldset class="form-group">
                                                                        <label for="currency">Currency</label>
                                                                        <select name="currency" class="form-control" id="currency" required>
                                                                            <option value="">Select</option>
                                                                            @foreach($currencies as $currency)
                                                                                <option value="{{ $currency }}" {{ $currency == old('currency') ? 'selected' : ''}}>{!! $currency !!}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col-md-6">   
                                                                    <fieldset class="form-group">
                                                                        <label for="official_email">Official Email</label>
                                                                        <input type="email" name="official_email" class="form-control" value="{{ old('official_email') }}" required>
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col-md-6">   
                                                                    <fieldset class="form-group">
                                                                        <label for="whatsapp_number">Shop Whatsapp Number</label>
                                                                        <input type="text" name="whatsapp_number" class="form-control" value="{{ old('whatsapp_number') }}" required>
                                                                    </fieldset>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <strong style="color:black">Store Administrator Details</strong>
                                                                    <hr>
                                                                </div>
                                                                <div class="col-md-6">   
                                                                    <fieldset class="form-group">
                                                                        <label for="email">Email</label>
                                                                        <input type="email" name="email" class="form-control" value="{{ auth()->user()->email ?? old('email') }}" required>
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col-md-6">   
                                                                    <fieldset class="form-group">
                                                                        <label for="password">Password <span style="color:blue"><small>(Leave blank to use your current password)</small></span></label>
                                                                        <input type="text" name="last_name" class="form-control" value="{{ old('password') }}">
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col-md-6">   
                                                                    <fieldset class="form-group">
                                                                        <label for="first_name">First Name</label>
                                                                        <input type="text" name="first_name" class="form-control" value="{{ auth()->user()->firstname }}" required>
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col-md-6">   
                                                                    <fieldset class="form-group">
                                                                        <label for="last_name">Last Name</label>
                                                                        <input type="text" name="last_name" class="form-control" value="{{ auth()->user()->lastname }}" required>
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col-md-6">   
                                                                    <fieldset class="form-group">
                                                                        <label for="phone">Phone</label>
                                                                        <input type="text" name="phone" class="form-control" value="{{ auth()->user()->phone }}" required>
                                                                    </fieldset>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <button class="btn btn-primary">Submit</button>
                                                                </div>
                                                            </div>
                                                           
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                                @else
                                @if(auth()->user()->customer->shop_request->status == 'approved')
                                <div class="card-body"> 
                                    
                                    <div class="row">
                                        <div class="col-md-12">
                                            <strong style="color:black">Shop Details</strong>
                                            
                                            <hr>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <p>Below is the link to your shop, you can copy this link and share with your prospective customers
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="key-field" id="api">
                                                <span>{{ env('SHOPS_BASE_URL').auth()->user()->customer->shop_request->request_details['shop_slug'] }}</span>
                                                <i class="fa fa-copy text-danger copy"></i>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                                <a href="{{ env('SHOPS_BASE_URL').auth()->user()->customer->shop_request->request_details['shop_slug'] }}" class="btn btn-info" target="_blank">Visit shop</a>
                                            </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">   
                                            <fieldset class="form-group">
                                                <label for="shop_name">Shop Name</label>
                                                <input type="text" name="shop_name" class="form-control" value="{{ auth()->user()->customer->shop_request->request_details['shop_name']}}" disabled>
                                            </fieldset>
                                        </div>
                                        <div class="col-md-6">   
                                            <fieldset class="form-group">
                                                <label for="shop_name">Shop slug</label>
                                                <input type="text" name="shop_slug" class="form-control" value="{{auth()->user()->customer->shop_request->request_details['shop_slug'] }}" disabled>
                                            </fieldset>
                                        </div>
                                        
                                    </div>
                                @else
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12" style="color:green">
                                            Your shop creation request is undergoing review, please check back later
                                        </div>
                                    </div>
                                </div>
                                @endif
                            @endif
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
<script>
    $('.copy').click(function () {
        (async () => {
            try {
                var copyText = $(this).prev('span');
                let text = copyText.html();
                await navigator.clipboard.writeText(text);
                copyText.html('Shop link copied to clipboard!').css({color: 'green'});
                setTimeout(() => {
                    copyText.html(text).css({color: '#555'});
                }, 3000);
            } catch (error) {
                alert(error.message)
            }
        })();
    })
</script>
    
@endsection