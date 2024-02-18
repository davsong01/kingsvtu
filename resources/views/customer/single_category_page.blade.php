<?php 
    $verifiable = verifiableUniqueElements()
?>
@extends('layouts.app')
@section('title', $category->seo_title)
@section('keywords', $category->seo_keywords)
@section('description', $category->seo_description)
@section('page-css')
<style>
    .reset-pin {
        font-size: 10px;
        float: right;
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
                                <section id="nav-filled">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="card-title">{{ $category->description }}</h4>
                                                    @include('layouts.alerts')
                                                </div>
                                                <div class="card-content">
                                                    <div class="card-body">
                                                        <form action="{{route('initialize.transaction')}}" method="POST"  id="initialize">
                                                            @csrf
                                                            
                                                            <div class="row">
                                                                <div class="col-md-9">
                                                                    <div class="d-flex pb-1 justify-content-start align-items-center w-100">
                                                                        <img class="product-images" style="padding-right: 8px;height: 70px;" id="product-image" src="" alt="" class="product-image">
                                                                        <div>
                                                                            <h5 id="product-title" style="color:#174159;padding-top: 19px;"><strong></strong>
                                                                            </h5>
                                                                            <p style="" id="product-description" style="line-height: 1.4;"></p>
                                                                        </div>
                                                                    </div>
                                                                   
                                                                    <fieldset class="form-group">
                                                                        <label for="product">Select Service</label>
                                                                        <select class="form-control" name="product" id="product" required>
                                                                            <option value="">Select</option>
                                                                            @foreach ($category->products as $item)
                                                                                <option value="{{ $item->id  }}" data-image="{{ asset($item->image) }}" data-name="{{ $item->name }}" data-description="{{ $item->description }}" {{ old('product') == $item->id ? 'selected' : ''}} {{ old('product') == $item->id ? 'selected' : ''}}>{{ $item->display_name }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </fieldset>
                                                                    <fieldset class="form-group" id="variation-div" style="display:none">
                                                                        <label for="name" class="">Select Variation </label>
                                                                        <select class="form-control" id="variation" name="variation" required="">
                                                                            <option value="">Select...</option>
                                                                        </select>
                                                                    </fieldset>
                                                                    @if($category->slug == 'electricity')
                                                                    <fieldset class="form-group unique_element" style="display:none">
                                                                        <label for="meter_number">Meter Number</label>
                                                                        <input type="text" class="form-control" id="meter_number" name="meter_number" value="{{ old('meter_number')}}" required>
                                                                    </fieldset>
                                                                    @endif
                                                                    @if($category->slug == 'dstv')
                                                                    <fieldset class="form-group unique_element" style="display:none">
                                                                        <label for="iuc_number">IUC Number</label>
                                                                        <input type="text" class="form-control" id="iuc_number" name="iuc_number" value="{{ old('iuc_number')}}" required>
                                                                    </fieldset>
                                                                    @endif
                                                                
                                                                    <fieldset class="form-group">
                                                                        <label for="email">Email Address</label>
                                                                        <input type="text" class="form-control" id="email" name="email" value="{{ auth()->user()->email ?? old('email')}}" required>
                                                                    </fieldset>

                                                                    <fieldset class="form-group">
                                                                        <label for="phone">Phone Number</label>
                                                                        <input type="text" class="form-control" id="phone" name="phone" value="{{ auth()->user()->phone ?? old('phone')}}" required>
                                                                    </fieldset>
                                                                   
                                                                    <fieldset class="form-group">
                                                                        <label for="amount" class="">Amount</label>
                                                                        <input class="form-control" id="amount" name="amount" placeholder="Enter Amount" required="" type="number">
                                                                    </fieldset>
                                                                    <fieldset class="form-group">
                                                                        
                                                                        <label for="transaction_pin">Transaction PIN</label><span class="reset-pin"><a href="{{ route('customer.reset.pin') }}"> Reset Transaction Pin</a></span>
                                                                        <input type="password" class="form-control" id="transaction_pin" name="transaction_pin" required>
                                                                    </fieldset>
                                                                  </form>
                                                                    <button class="btn btn-primary" type="submit" style="display:{{ in_array($category->unique_element, $verifiable) ? 'none' : '' }}" onclick="submitForm()">Buy now</button>
                                                                    <a href="#" id="verify-link" onclick="verify(this)" class="btn btn-info" type="submit" style="display:none">Verify {{ ucfirst(str_replace("_"," ",$category->unique_element)) }}</a>
                                                                </div>

                                                                <div class="col-md-3">
                                                                    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pubxxx" crossorigin="anonymous"></script>
                                                                    <script>(adsbygoogle=window.adsbygoogle||[]).requestNonPersonalizedAds=1;</script><script>(adsbygoogle = window.adsbygoogle || []).push({});</script> 
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
<div class="modal fade" id="verify-modal" data-bs-backdrop="static" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog">
    <form class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title" id="verify-title"></h5>
    </div>
    <div class="modal-body">
        <div id="verify-details">
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="submitForm()">Continue Payment</button>
    </div>
    </form>
    </div>
</div>
@section('page-script')
<script src="{{ asset('app-assets/js/scripts/pages/dashboard-analytics.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js"></script>
<script>
    function verify(e){
        $.LoadingOverlay("show");
        $('#verify-modal').modal('hide');

        var url = "{{ url('customer-verify') }}";
        var formData =  {
            category_id: {{ $category->id }},
            meter_number: $("#meter_number").val(),
            iuc_number: $("#iuc_number").val(),
            variation: $("#variation").val(),
            product_id: $("#product").val(),
        };
        
        $.ajax({
            url: url,
            method: 'POST',
            dataType: 'json',
            data:formData,

            success: function (data) {
                // console.log(data);
                $.LoadingOverlay("hide");
                $("#verify-title").html(data.title);
                $("#verify-details").html(data.message);
            
                // $("#amount").prop('readonly', false);
                $('#verify-modal').modal('show');
            }
        });
    }

    function submitForm(){
        $.LoadingOverlay("show");
        document.forms["initialize"].submit();
    }

    $(document).ready(function () {
        
        var variations = [];
        
        $('#product').on('change', function () {
            $('#variation-div').show();
            $('#amount-div').hide();
    
            $("#amount").prop('readonly', false);
            $("#amount").val('');
    
            $('#variation').find('option').not(':first').remove();
    
            var product = $('#product').val();
            if (product == '') {
                return;
            } else {
                var image = $('#product').find(':selected').data('image');
                var title = $('#product').find(':selected').data('name');
                var description = $('#product').find(':selected').data('description');
                var bulk = $('#product').find(':selected').data('bulk');
                if (bulk == 'yes') {
                    $("#bulk-purchase").show();
                } else {
                    $("#bulk-purchase").hide();
                }
    
                $('#product-image-div').show();
                $("#product-image").attr("src", image);
                $("#product-title").html(title);
                $("#product-description").html(description);
    
                $.ajax({
                    url: "{{ url('customer-get-variations') }}/" + product,
                    success: function (data) {
                        if (data && data.length > 0) {
                            for (t = 0; t <= data.length; t++) {
                                console.log(data[t]);
                                $('#variation').append(
                                    `<option value="${data[t].id}" data-isFixed="${data[t].fixed_price}" data-amount="${data[t].system_price}"> ${data[t].system_name}</option>`
                                    );
                                variations.push({
                                    "id": data[t].id,
                                    "verifiable": data[t].verifiable,
                                    "unique_element": data[t].unique_element,
                                    "max": data[t].max,
                                    "min": data[t].min,
                                    "fixedPrice": data[t].fixed_price,
                                    "variation_amount": data[t].system_price
                                });
                            }
                        }
                    }
                });
            }
    
        });
    
        $('#variation').on('change', function (e) {
            $('#amount-div').show();
            var v = e.target.value;
            var selected = variations.filter((item) => {
                return item.id == v;
            });
            // console.log('sss=>', selected[0]);
            if (selected[0].verifiable == 'yes') {
                $("#verify-link").show();
                $(".unique_element").show();
            }else{
                $("#verify-link").hide();
                $(".unique_element").hide();
            }
           
            if (selected[0].fixedPrice == 'Yes') {
                $("#amount").attr({
                    "max": "",
                    "min": ""
                });
    
                $('#amount').val(selected[0].variation_amount);
                // $('#amount-label').text(selected[0].charged_currency+selected[0].charged_amount);
                $("#amount").attr({
                    "readonly": "true",
                });
    
            } else {
                $("#amount").prop('readonly', false);
                $("#amount").attr({
                    "max": selected[0].max,
                    "min": selected[0].min,
                });
            }
    
    
        });
    
        $('.select2').select2();

    });
</script>

@endsection
