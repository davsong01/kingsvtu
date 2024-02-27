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
                                <section id="nav-filled">
                                    <div class="row">
                                        <div class="col-md-9">
                                            <div class="card">
                                                <div class="col-md-12">
                                                    <div class="card-header" style="padding:1.4rem 0.7rem">
                                                        <h4 class="card-title">Fund Wallet</h4>
                                                        @include('layouts.alerts')
                                                    </div>
                                                </div>
                                                    <div class="card-content">
                                                       <div class="card-body">
                                                            <ul class="nav nav-tabs nav-fill" id="myTab" role="tablist">
                                                                @if(getSettings()->allow_fund_with_card == 'yes')
                                                                <li class="nav-item">
                                                                    <a class="nav-link active" id="home-tab-fill" data-toggle="tab" href="#product-details" role="tab" aria-controls="product-details" aria-selected="true">
                                                                        Fund with Card
                                                                    </a>
                                                                </li>
                                                                @endif
                                                                @if(getSettings()->allow_fund_with_reserved_account == 'yes')
                                                                <li class="nav-item">
                                                                    <a class="nav-link" id="profile-tab-fill" data-toggle="tab" href="#variations" role="tab" aria-controls="variations" aria-selected="false">
                                                                        Fund with bank transfer
                                                                    </a>
                                                                </li>
                                                                @endif
                                                            </ul>
                                                            <div class="tab-content pt-1">
                                                                @if(getSettings()->allow_fund_with_card == 'yes')
                                                                <div class="tab-pane {{ getSettings()->allow_fund_with_card == 'yes' ? 'active' : ''}}" id="product-details" role="tabpanel" aria-labelledby="home-tab-fill">
                                                                    <p>Credit your wallet now, and spend from it later. No Need to enter card details everytime you want to make a Payment. Make Faster Payments. </p>
                                                                    <form action="" method="POST" enctype="multipart/form-data">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <div class="row">
                                                                            <div class="col-md-12">
                                                                                <fieldset class="form-group">
                                                                                    <label for="amount">Enter Amount</label>
                                                                                    <input type="number" class="form-control" id="amount" name="amount" placeholder="Enter amount" value="{{ old('amount')}}" required>
                                                                                </fieldset>
                                                                                
                                                                            </div>
                                                                           
                                                                            <div class="col-md-12">
                                                                                <a class="btn btn-primary" style="color:white" onclick="payWithMonnify()">Pay now</a>
                                                                            </div>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                                @endif
                                                                @if(getSettings()->allow_fund_with_reserved_account == 'yes')
                                                                    <div class="tab-pane {{ getSettings()->allow_fund_with_card  !== 'yes' ? 'active' : ''}}" id="variations" role="tabpanel" aria-labelledby="profile-tab-fill">
                                                                        @if(empty(auth()->user()->customer->kyc_status) || auth()->user()->customer->kyc_status == 'unverified')
                                                                        Hang on a second! You need to fill in your KYC information for verification before you can fund via a reserved account number <br>
                                                                        <a href="" class="btn btn-primary btn-sm">Update KYC details here</a>
                                                                        @else
                                                                            <p>To fund your wallet, make payment into this account. Your Wallet will be credited automatically. Account number is dedicated to crediting your wallet.
                                                                                IMPORTANT: payments made into this account are automated. This means that once you transfer, your wallet is credited automatically.
                                                                                P.S: Just like every other transfers, you could experience a slight delay in wallet funding. You only need to hold on patiently as your wallet would be credited once processed. You do not need to contact support after funding your wallet, It is automated. 
                                                                            </p>    
                                                                            <div>
                                                                                <h5>Wallet Funding Account Details</h5>
                                                                                <div class="table-responsive mt-2 d-lg-block d-none">
                                                                                    <table class="table table-striped">
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th style="color:#495463;">Account Name</th>
                                                                                                <th style="color:#495463;">Bank Name</th>
                                                                                                <th style="color:#495463;">Account Number</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>
                                                                                                                                                        <tr>
                                                                                                    <td style="color:#173D52;">David Oghi</td>
                                                                                                    <td style="color:#173D52;">Providus Bank</td>
                                                                                                    <td style="color:#173D52;">9989018147</td>
                                                                                                </tr>
                                                                                                                                                        <tr>
                                                                                                    <td style="color:#173D52;">David Oghi</td>
                                                                                                    <td style="color:#173D52;">Wema bank</td>
                                                                                                    <td style="color:#173D52;">7159221111</td>
                                                                                                </tr>
                                                                                                                                                </tbody>
                                                                                    </table>
                                                                                </div>
                                                                            </div> 
                                                                        
                                                                        @endif
                                                                    </div>
                                                                @endif
                                                            </div>
                                                       </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                Sidebar Advert
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
<script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js"></script>
<script type="text/javascript" src="https://sdk.monnify.com/plugin/monnify.js"></script>
<script>
        function payWithMonnify() {
            amount = $('#amount').val();
            
            MonnifySDK.initialize({
                amount: amount ,
                currency: "NGN",
                reference: new String((new Date()).getTime()),
                customerFullName: "{{auth()->user()->firstname}}"+" "+"{{auth()->user()->middlename}}"+" "+"{{auth()->user()->lastname}}",
                customerEmail: "{{ auth()->user()->email }}",
                apiKey: "{{ $gateway->api_key}}",
                contractCode: "{{ $gateway->contract_id}}",
                paymentDescription: "Wallet Funding",
                metadata: {
                    "customer_id": "{{ auth()->user()->customer->id }}",
                    "reason": "Wallet Funding",
                },
                
                onLoadStart: () => {
                    console.log("loading has started");
                },
                onLoadComplete: () => {
                    console.log("SDK is UP");
                },

                onComplete: function(response) {
                    $.LoadingOverlay("show");
                    $.ajax({
                        url: "{{url('/log-p-callback')}}"+"/{{ $gateway->id}}",
                        method: 'POST',
                        dataType: 'json',
                        data:response,

                        success: function (data) {
                            // $("#verify-title").html(data.title);
                            // $("#verify-details").html(data.message);

                            // if(data.status == '1'){
                            //     if(allow_subscription_type == 'yes' && element == 'iuc_number'){
                            //         $('#bouquet').append(`<option value="change" data-amount="${amount}">Change Bouquet</option><option data-amount="${data.renewal_amount}" value="renew">Renew Bouquet</option>`);
                            //         $("#bouquet-div").show();
                            //         $("#bouquet").attr({
                            //             "required":true
                            //         });
                            //     }else{
                            //         $("#bouquet-div").hide();
                            //         $("#bouquet").attr({
                            //             "required":false
                            //         });
                            //     }

                            //     $("#continue_payment").show();

                            // }else{
                            //     $("#continue_payment").hide();
                            // }
                            // $('#verify-modal').modal('show');
                        }
                    });
                    
                },
                onClose: function(data) {
                    //Implement what should happen when the modal is closed here
                    console.log(data);
                }
            });
        }
    </script>
@endsection
