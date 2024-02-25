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
                                        <form action="{{ route('customer.transaction.history') }}" method="GET">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <fieldset class="form-group">
                                                        <label for="service">Service</label>
                                                        <select class="form-control" name="service" id="service">
                                                            <option value="">Select</option>
                                                            @foreach ($products as $product)
                                                                <option value="{{ $product->id }}" {{ \Request::get('service') == $product->id ? 'selected' : ''}}>{{ $product->display_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-4">
                                                    <fieldset class="form-group">
                                                        <label for="transaction_id">Transaction ID</label>
                                                        <input type="text" class="form-control" id="transaction_id" name="transaction_id" placeholder="Enter transaction ID" value="{{ \Request::get('transaction_id')}}">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-4">
                                                    <fieldset class="form-group">
                                                        <label for="unique_element">Unique Element</label>
                                                        <input type="text" class="form-control" id="unique_element" name="unique_element" placeholder="Enter unique element" value="{{ \Request::get('unique_element') }}">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-4">
                                                    <fieldset class="form-group">
                                                        <label for="status">Status</label>
                                                        <select class="form-control" name="status" id="status">
                                                            <option value="">Select</option>
                                                            <option value="delivered" {{ \Request::get('status') == 'delivered' ? 'selected' : ''}}>Delivered</option>
                                                            <option value="failed" {{ \Request::get('status') == 'failed' ? 'selected' : ''}}>Failed</option>
                                                        </select>
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-3">
                                                    <fieldset class="form-group">
                                                        <label for="from">From</label>
                                                        <input type="date" class="form-control" value="{{ \Request::get('from')}}" name="from">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-3">
                                                    <fieldset class="form-group">
                                                        <label for="to">To</label>
                                                        <input type="date" class="form-control" value="{{ \Request::get('to')}}" name="to">
                                                    </fieldset>
                                                </div>

                                                <div class="col-md-2">
                                                    <label for="to"></label>
                                                    <input type="submit" class="form-control btn btn-primary" value="Search">
                                                </div>
                                            </div>
                                        </form>
                                        <hr>
                                    </div>
                                    @foreach ($transactions as $transaction)
                                    <div class="col-md-6" style="box-shadow: rgba(0, 0, 0, 0.15) 1.95px 1.95px 2.6px;padding-top:10px;padding-bottom: 10px;">
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
                                                                @if($transaction->reason == 'LEVEL-UPGRADE')
                                                                LEVEL UPGRADE
                                                                @else
                                                                <strong>{{ $transaction->product->name}}</strong>@if($transaction?->variation?->system_name) {{ " | ". $transaction?->variation?->system_name }} @endif
                                                                @endif
                                                                (@if($transaction->status == 'failed')
                                                                    <span class="text-danger">{{ ucfirst($transaction->status) }}</span>
                                                                @elseif($transaction->status == 'initiated')
                                                                    <span class="text-warning">{{ ucfirst($transaction->status) }}</span>
                                                                @else
                                                                    <span class="text-success">{{ ucfirst($transaction->descr) }}</span>
                                                                @endif)
                                                            </span> 
                                                        </small><br>
                                                        <span class="title">Biller</span> <br>
                                                        <small>
                                                            {{ $transaction->unique_element }}</strong>
                                                        </small> <br>
                                                        <span class="title">Amount Paid</span> <br>
                                                        <small>
                                                            {!! getSettings()['currency']!!}{{ number_format($transaction->total_amount, 2) }}</strong>
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
                                                        <small class="text-muted"><a target="_blank" href="{{ route('transaction.status', $transaction->transaction_id) }}" class="btn btn-sm btn-primary glow mt-md-2 mb-1">View</a></small> <small class="text-muted"><a target="_blank" href="{{ route('transaction.receipt.download', $transaction->id) }}" class="btn btn-sm btn-info glow mt-md-2 mb-1">Download Transaction Receipt</a></small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    @endforeach
                                </div>
                            </div>
                            <div class="card-footer">
                                {!! $transactions->appends($_GET)->links() !!}
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
