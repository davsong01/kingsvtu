@extends('layouts.app')
@section('content')
    <!-- Content wrapper -->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <section id="table-success">
                <div class="card">
                    <div class="card-header">
                        <!-- head -->
                        <h5 class="card-title mb-2">Earnings Log</h5>
                        <div class="d-inline-block">
                            <!-- chart-1   -->
                            <div class="d-flex market-statistics-1">
                                <!-- chart-statistics-1 -->
                                <div id="donut-success-chart"></div>
                                <!-- data -->
                                <div class="statistics-data my-auto">
                                    <div class="statistics">
                                        <span
                                            class="font-medium-2 mr-50 text-bold-600">{!! getSettings()->currency. number_format($success) !!}</span>
                                            <br>
                                            <span
                                            class="text-success">Sucessful</span>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-inline-block mx-3">
                            <!-- chart-2 -->
                            <div class="d-flex mb-75 market-statistics-2">
                                <!-- chart statistics-2 -->
                                <div id="donut-danger-chart"></div>
                                <!-- data-2 -->
                                <div class="statistics-data my-auto">
                                    <div class="statistics">
                                        <span
                                            class="font-medium-2 mr-50 text-bold-600">{!! getSettings()->currency. number_format($failed) !!}</s!!an><br><span
                                            class="text-danger">Failed</span>
                                    </div>
                                   
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="col-md-12">
                            <form action="{{ route('admin.walletfundinglog') }}" method="GET">
                                {{-- @csrf --}}
                                <div class="row">
                                    <div class="col-md-3">
                                        <fieldset class="form-group">
                                            <label for="upline_email">Upline Email</label>
                                            <input type="upline_email" class="form-control" id="upline_email" name="upline_email" placeholder="Enter upline email address" value="{{ \Request::get('upline_email')}}">
                                        </fieldset>
                                    </div>
                                    <div class="col-md-3">
                                        <fieldset class="form-group">
                                            <label for="downline_email">Downline Email</label>
                                            <input type="downline_email" class="form-control" id="downline_email" name="downline_email" placeholder="Enter upline email address" value="{{ \Request::get('downline_email')}}">
                                        </fieldset>
                                    </div>
                                    <div class="col-md-3">
                                        <fieldset class="form-group">
                                            <label for="transaction_id">Transaction ID</label>
                                            <input type="text" class="form-control" id="transaction_id" name="transaction_id" placeholder="Enter transaction ID" value="{{ \Request::get('transaction_id')}}">
                                        </fieldset>
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <fieldset class="form-group">
                                            <label for="sttus">Status</label>
                                            <select class="form-control" name="type" id="type">
                                                <option value="type">Select</option>
                                                <option value="credit" {{ \Request::get('type') == 'credit' ? 'selected' : ''}}>Credit</option>
                                                <option value="debit" {{ \Request::get('type') == 'debit' ? 'selected' : ''}}>Debit</option>
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
                                    <div class="col-md-3">
                                        <input type="submit" class="form-control btn btn-primary mt-2" value="Search">
                                    </div>
                                </div>
                            </form>
                            <hr>
                        </div>
                        <div class="table-responsive">
                            <form method="post">
                                <table id="table-extended-success" class="table mb-0">
                                    <thead>
                                        <tr>
                                            <th>Upline</th>
                                            <th>Downline</th>
                                            <th>Payment Details</th>
                                            <th>Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($transactions as $transaction)
                                            <tr>
                                                <td>{{ $transaction->referredCustomer->user->name }} <br>
                                                    <a href="">{{ $transaction->referredCustomer->user->email  }}</a> <br>
                                                    {{ $transaction->customer_phone }} <br>
                                                    @if($transaction->status == 'success')
                                                    <button class="btn btn-primary btn-sm">{{ucfirst($transaction->status) }}</button>
                                                    @else
                                                    <button class="btn btn-danger btn-sm">{{ucfirst($transaction->status) }}</button>
                                                    @endif
                                                </td>
                                                <td>
                                                    <small>
                                                    <strong>Account Number: </strong>{{ $transaction->account_number }} <br>
                                                    <strong>Amount: </strong>{!! getSettings()->currency. number_format($transaction->amount, 2) !!} <br>
                                                    <strong>Charge: </strong>{!! getSettings()->currency. number_format($transaction->provider_charge, 2) !!} <br>
                                                    <strong>Total Amount: </strong>{!! getSettings()->currency. number_format($transaction->total_amount,2) !!} <br>
                                                    <strong>Initial Balance: </strong>{!! getSettings()->currency. number_format($transaction->balance_before, 2) !!} <br>
                                                    <strong>Final Balance: </strong>{!! getSettings()->currency. number_format($transaction->balance_after, 2) !!} <br>
                                                    <strong>Date: </strong>{{ date("M jS, Y g:iA", strtotime($transaction->created_at)) }}

                                                    </small>
                                                </td>
                                                <td>
                                                    <small>
                                                    <span style="color:crimson"><strong>TransactionID: </strong> {{ $transaction->transaction_id }}</span> <br>
                                                    <span style="color:rgb(27, 20, 220)"><strong>Request ID: </strong>{{ $transaction->reference_id }}</span> <br>
                                                    <span style="color:rgb(0, 145, 87)"><strong>Payment Method: </strong> {{ $transaction->payment_method }}
                                                    </small>

                                                </td>
                                            
                                                <td>
                                                    <a class="btn btn-primary btn-sm mr-1 mb-1" href="{{ route('admin.single.transaction.view', $transaction->id) }}">
                                                        <i class="fa fa-eye"></i>
                                                        <span class="align-middle ml-25">View</span>
                                                    </a>

                                                </td>
                                            </tr>
                                            {{-- @dump($transaction) --}}
                                        @endforeach
                                    </tbody>
                                </table>
                            </form>
                            {{-- {{ $transactions->appends($query) }} --}}
                        </div>
                    </div>
                     <div class="card-footer">
                        {!! $transactions->appends($_GET)->links() !!}
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
@section('page-script')
    {{-- <script src="{{asset('asset/js/app-logistics-dashboard.js')}}"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.js-example-basic-single').select2();
        });
    </script>
@endsection
