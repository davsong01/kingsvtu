<?php 
    if($transaction->status == 'failed'){
        $color = 'red';
    }elseif($transaction->status == 'initiated'){
        $color = '#FDAC41';
    }else {
        $color = 'green';
    }
?>
@extends('layouts.app')
@section('title', 'Transction Details')

@section('page-css')
<style>
    .reset-pin {
        font-size: 10px;
        float: right;
    }

    .heads {
        color: black
    }
    body {
        font-size: 1rem;
        font-weight: 398;
        color: black;
        font-size: smaller;
    }
    .table {
        color: black;
    }
    pre {
        margin-top: 0;
        margin-bottom: 1rem;
        overflow: scroll;
        height: 200px;
        text-overflow: clip;
        max-height:350px
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
                                        <div class="col-md-12">
                                            <div class="card">
                                                <div class="col-md-12">
                                                    <div class="card-header" style="padding:1.4rem 0.7rem">
                                                        <h4 class="card-title">Transaction Details</h4>
                                                        @include('layouts.alerts')
                                                    </div>
                                                </div>
                                                <div class="card-content">
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-md-1">
                                                                @if(in_array($transaction->reason, ['LEVEL-UPGRADE','WALLET-FUNDING']))
                                                                <img id="product-image" width="60" height="60" src="{{ asset('site/upgrade.jpg') }}" alt="" class="product-image" style="margin:5px; box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;">
                                                                @else 
                                                                <img id="product-image" width="60" height="60" src="{{ asset($transaction->product->image) }}" alt="" class="product-image" style="margin:5px; box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;">
                                                                @endif

                                                            </div>
                                                                <div class="col-md-5">
                                                                    <h5 class="mb-1">
                                                                        {{ $transaction->transaction_id }}</h5> <br>
                                                                    {{ $transaction->created_at }}
                                                                </div>
                                                                <div class="col-md-3">
                                                                    Request Id: {{ $transaction->reference_id }}
                                                                </div>
                                                                <div class="col-md-3">
                                                                    User Status: <br>
                                                                    <span style="color:{{ $color }}">{{ ucfirst($transaction->descr) }}</span><br><br>
                                                                    Real Status <br>
                                                                    <span style="color:{{ $color }}"><strong>{{ ucfirst($transaction->status) }}</strong></span><br><br>
                                                                </div>
                                                            </div>
                                                            <hr>
                                                            <div class="row">
                                                                <div class="col-md-3">
                                                                    <strong class="heads">Wallet Trail:</strong> <br>
                                                                   
                                                                    @if($transaction->wallets)
                                                                        @foreach($transaction->wallets as $wallet)
                                                                            @if($wallet->type == 'credit')
                                                                            <span style="color:green">CREDIT : {{ $wallet->created_at}}</span>
                                                                            @endif
                                                                            @if($wallet->type == 'debit')
                                                                            <span style="color:red">DEBIT : {{ $wallet->created_at}}</span>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif

                                                                </div>
                                                                <div class="col-md-3">
                                                                    <strong class="heads">Payment Details</strong> <br>
                                                                    METHOD: {{ $transaction->payment_method}} <br>
                                                                    CHANNEL: {{ $transaction->channel}} <br>
                                                                    CUST. EMAIL: {{ $transaction->customer_email }} <br>
                                                                    PHONE: {{ $transaction->customer_phone }}
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <strong class="heads">Transaction Details</strong>
                                                                    <br>

                                                                    <strong>Product:
                                                                    </strong>{{ $transaction->product_name }} <br>
                                                                    <strong>Category:
                                                                    </strong>{{ $transaction->category->display_name }}
                                                                    <br>
                                                                    <strong>Variation:
                                                                    </strong>{{ $transaction->variation->system_name }}
                                                                    <br>
                                                                    <strong>Provider:
                                                                    </strong>{{ $transaction->api->name }} <br>
                                                                   
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <strong class="heads">API Response</strong> <br>
                                                                    <div>
                                                                        <pre>
                                                                            {!! $transaction->api_response!!}
                                                                        </pre>

                                                                    </div>

                                                                </div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="table-responsive">
                                                                    <table id="table-extended-success" class="table mb-0">
                                                                        <thead>
                                                                            <tr>
                                                                                <th style="color:black">Item</th>
                                                                                <th style="color:black">Unit Cost</th>
                                                                                <th style="color:black">Quantity</th>
                                                                                <th style="color:black">Amount</th>
                                                                                <th style="color:black">Biller</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                        <tr>
                                                                                <td>
                                                                                    @if(in_array($transaction->reason, ['LEVEL-UPGRADE','WALLET-FUNDING']))
                                                                                        {{ ucfirst(str_replace("-"," ",$transaction->reason))}}
                                                                                    @else
                                                                                    {{ $transaction->product->name }}@if(!empty($transaction->variation->system_name)) <strong> | {{$transaction->variation->system_name}} </strong> @endif 
                                                                                    @endif
                                                                                </td>
                                                                                <td>
                                                                                    {!! getSettings()->currency. number_format($transaction->amount, 2) !!}
                                                                                </td>
                                                                                    <td>
                                                                                    {{ $transaction->quantity  }}
                                                                                </td>
                                                                                <td>    
                                                                                    <span style="color:black">Convenience Fee:</span> {!! getSettings()->currency. number_format($transaction->provider_charge, 2) !!} <br>
                                                                                    <span style="color:black">Discount: </span>{!! getSettings()->currency. number_format($transaction->discount, 2) !!} <br>
                                                                                    <span style="color:black">Provider Charge:</span>{!! getSettings()->currency. number_format($transaction->provider_charge, 2) !!} <br>
                                                                                    <span style="color:black">Total Amount:</span> {!! getSettings()->currency. number_format($transaction->total_amount, 2) !!}
                                                                                        
                                                                                </td>
                                                                                <td>{{ $transaction->unique_element }}</td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
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