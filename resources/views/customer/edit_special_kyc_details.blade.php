@extends('layouts.app')
@section('title', 'Edit KYC data')

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
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="col-md-12"> 
                                        <div class="card-header" style="padding:1.4rem 0.7rem">
                                            <h4 class="card-title">Update KYC data</h4>
                                            @include('layouts.alerts')
                                        </div>
                                    </div>
                                    
                                    @if($kycmessage)
                                        <div class="alert alert-primary" style="background:#182851E5 !important">
                                            {!! $kycmessage !!}
                                        </div>
                                    @endif

                                    @if($fields)
                                        <form method="POST" action="{{ route('submit.special.kyc') }}">
                                            @csrf
                                            <div class="row">
                                                @foreach($fields as $field)
                                                <div class="col-md-6">   
                                                    <fieldset class="form-group">
                                                        <label for="{{ $field['key'] }}">{{ $field['label'] }}</label>
                                                        @if($field['input_type'] == 'text')
                                                        <input type="text" class="form-control" value="" required>
                                                        @elseif($field['input_type'] == 'date')
                                                        <input type="date" class="form-control" value="{{ $field['key'] }}" required>
                                                        @elseif($field['input_type'] == 'select' && !empty($field['options']))
                                                        <select name="{{ $field['key'] }}" class="form-control" id="{{ $field['key'] }}" required>
                                                            <option value="">Select</option>
                                                            @foreach($field['options'] as $value=>$key)
                                                                <option value="{{ $value }}">{{ $key }}</option>
                                                            @endforeach
                                                        </select>
                                                        @endif
                                                    </fieldset>
                                                </div>
                                                @endforeach
                                                <div class="col-md-12">
                                                    <button class="btn btn-primary">Submit</button>
                                                </div>
                                            </div>
                                        </form>
                                    @endif
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
<script>
    $('#state').on('change',function () {
        var state = $('#state').val();
        $('#lga option:not(:first)').remove();
        $.ajax({
            type: "GET",
            url: "{{url('/')}}/get-lga-by-statename/"+state,
            beforeSend: function () {

            },
            success: function(data) {
                $("#lga").append(data);
            }
        });
    });
</script>
<script>
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
            console.log('sss=>', selected[0]);
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
