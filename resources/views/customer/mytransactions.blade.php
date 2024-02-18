@extends('layouts.app')
@section('title', 'Edit Profile')

@section('page-css')
{{-- <link rel="stylesheet" href="{{ asset('app-assets/vendors/css/pages/app-logistics-dashboard.css')}}" />
<!-- Helpers -->
    <script src="{{ asset('assets/vendor/js/helpers.js')}}"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
    <script src="{{ asset('assets/vendor/js/template-customizer.js')}}"></script>
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ asset('assets/js/config.js')}}"></script> --}}
<style>
    .reset-pin {
        font-size: 10px;
        float: right;
    }
    .title{
        color:black;
    }
</style>
@endsection
@section('content')
<!-- Content wrapper -->
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="content-wrapper">
        <div class="card-title">
            My Transactions
        </div>
        <div class="content-body">
            <div class="row">
                <!-- Marketing Campaigns Starts -->
                <div class="col-xl-12 col-12 dashboard-marketing-campaign">
                    <div class="card marketing-campaigns">
                        <div class="card-content">
                            <div class="card-body pb-0">
                                <div class="row">
                                    <div class="col-md-12">
                                        ssdds
                                    </div>
                                    @foreach ($transactions as $transaction)
                                    <div class="col-md-6" style="box-shadow: rgba(0, 0, 0, 0.15) 1.95px 1.95px 2.6px;padding-top:10px">
                                        <div class="d-inline-block">
                                            <!-- chart-1   -->
                                            <div class="d-flex market-statistics-1" style="position: relative;">
                                                <!-- chart-statistics-1 -->
                                                
                                                <!-- data -->
                                                <div class="statistics-data my-auto">
                                                    <div class="statistics">
                                                        <span class="title">Service</span> <br>
                                                        <small>
                                                            <span class="mr-50 text-bold-200">
                                                                <strong>{{ $transaction->product->name}}</strong>{{ ' | '. $transaction->variation->system_name }}
                                                                @if($transaction->status == 'failed')
                                                                    <span class="text-danger">{{ ucfirst($transaction->status) }}</span>
                                                                @elseif($transaction->status == 'initiated')
                                                                    <span class="text-warning">{{ ucfirst($transaction->status) }}</span>
                                                                @else 
                                                                    <span class="text-success">{{ ucfirst($transaction->status) }}</span>
                                                                @endif
                                                            </span> <br>
                                                            {{ $transaction->unique_element }}
                                                            
                                                        </small><br>
                                                        <span class="title">Amount Paid</span> <br>
                                                        <small>
                                                            {{ number_format($transaction->amount) }}</strong>
                                                        </small> <br>
                                                        <span class="title">Transaction Id</span> <br>
                                                        <small>
                                                            {{ $transaction->transaction_id }}</strong>
                                                        </small> <br>
                                                        <span class="title">Payment Method</span> <br>
                                                        <small>
                                                            {{ $transaction->payment_method }}</strong>
                                                        </small> <br>
                                                        <span class="title">Date</span> <br>
                                                        <small>
                                                            {{ date("M jS, Y g:iA", strtotime($transaction->created_at)) }}
                                                        </small> <br>

                                                    </div>
                                                    <div class="statistics-date">
                                                        <small class="text-muted"><a target="_blank" href="{{ route('transaction.status', $transaction->transaction_id) }}" class="btn btn-sm btn-primary glow mt-md-2 mb-1">View</a></small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>      
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script src="{{asset('asset/js/app-logistics-dashboard.js')}}"></script>  
@endsection