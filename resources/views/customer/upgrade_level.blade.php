@extends('layouts.app')
@section('title', 'Upgrade Level')

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
                                <section id="headers">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="card-title">Upgrade Level</h4>
                                                    @include('layouts.alerts')
                                                </div>
                                                <div class="card-content">
                                                    @if($benefits->count() > 0)
                                                    <div class="card-body card-dashboard">
                                                        <p class="card-text">Please go through the benefits attached to the levels below</p>
                                                        <div class="table-responsive">
                                                            <table class="table table-striped table-bordered complex-headers">
                                                                <thead>
                                                                    <tr>
                                                                        <th class="align-top">Benefits</th>
                                                                        @foreach ($levels as $level)
                                                                        <th class="align-top">{{ $level->name }}</th>
                                                                        @endforeach
                                                                    </tr>
                                                                    
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td style="color:black">Price</td>
                                                                        @foreach ($levels as $level)
                                                                        <td>{!! getSettings()['currency'] !!}{{$level->upgrade_amount}}</td>
                                                                        @endforeach
                                                                    </tr>
                                                                    @foreach ($benefits as $benefit)
                                                                    <tr>
                                                                        <td style="color:black">{!! $benefit->content !!}</td>
                                                                        @foreach ($levels as $level)
                                                                        <td>
                                                                            {{in_array($level->id, $benefit->customer_levels) ? 'Yes' : 'No'}}
                                                                        </td>
                                                                        @endforeach
                                                                    </tr>
                                                                    @endforeach
                                                                    <tr>
                                                                        <td style="color:black"></td>
                                                                        @foreach ($levels as $level)
                                                                        <td>
                                                                            {!! $level->extra_benefit !!}
                                                                        </td>
                                                                        @endforeach
                                                                    </tr>
                                                                    
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <form action="{{route('customer.level.upgrade.process')}}" method="POST" autocomplete="off">
                                                            @csrf
                                                            <div class="row">
                                                                <div class="col-md-12">   
                                                                    <fieldset class="form-group">
                                                                        <label for="level">Select Level to upgrade to</label>
                                                                        <select class="form-control" name="level" id="level" required>
                                                                        <option value="">Select</option>
                                                                        @foreach ($levels as $level)
                                                                            @if($level->id > auth()->user()->customer->level->id)
                                                                                <option value="{{ $level->id  }}" {{ auth()->user()->customer->level->id == $level->id ? 'selected' : ''}}>{{ $level->name }}({!!getSettings()['currency']!!}{{ $level->upgrade_amount }})</option>
                                                                            @endif
                                                                        @endforeach
                                                                    </select>
                                                                    </fieldset>
                                                                    
                                                                    <button class="btn btn-primary" type="submit">Upgrade</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                                
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
