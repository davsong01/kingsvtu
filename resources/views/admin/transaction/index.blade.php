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
                        <h5 class="card-title mb-2">Transactions</h5>
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
                                            class="text-success">Delivered</span>
                                    </div>
                                    {{-- <div class="statistics-date">
                                        <i
                                            class="bx bx-radio-circle font-small-1 text-success mr-25"></i>
                                        <small class="text-muted">May 12, 2019</small>
                                    </div> --}}
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
                                            class="font-medium-2 mr-50 text-bold-600">{!! getSettings()->currency. number_format($attention_required) !!}</span><br><span
                                            class="text-warning">Attention Required</span>
                                    </div>
                                    {{-- <div class="statistics-date">
                                        <i
                                            class="bx bx-radio-circle font-small-1 text-success mr-25"></i>
                                        <small class="text-muted">Jul 26, 2019</small>
                                    </div> --}}
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
                                        <s!!an
                                            class="font-medium-2 mr-50 text-bold-600">{!! getSettings()->currency. number_format($failed) !!}</s!!an><br><span
                                            class="text-danger">Failed</span>
                                    </div>
                                    {{-- <div class="statistics-date">
                                        <i
                                            class="bx bx-radio-circle font-small-1 text-success mr-25"></i>
                                        <small class="text-muted">Jul 26, 2019</small>
                                    </div> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="col-md-12">
                            <form action="{{ route('admin.trans') }}" method="GET">
                                {{-- @csrf --}}
                                <div class="row">
                                    <div class="col-md-3">
                                        <fieldset class="form-group">
                                            <label for="email">Customer Email</label>
                                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter customer email address" value="{{ \Request::get('email')}}">
                                        </fieldset>
                                    </div>
                                    <div class="col-md-3">
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
                                    <div class="col-md-3">
                                        <fieldset class="form-group">
                                            <label for="transaction_id">Transaction ID</label>
                                            <input type="text" class="form-control" id="transaction_id" name="transaction_id" placeholder="Enter transaction ID" value="{{ \Request::get('transaction_id')}}">
                                        </fieldset>
                                    </div>
                                    <div class="col-md-3">
                                        <fieldset class="form-group">
                                            <label for="unique_element">Unique Element</label>
                                            <input type="text" class="form-control" id="unique_element" name="unique_element" placeholder="Enter unique element" value="{{ \Request::get('unique_element') }}">
                                        </fieldset>
                                    </div>
                                    <div class="col-md-3">
                                        <fieldset class="form-group">
                                            <label for="status">Status</label>
                                            <select class="form-control" name="status" id="status">
                                                <option value="">Select</option>
                                                <option value="delivered" {{ \Request::get('status') == 'delivered' ? 'selected' : ''}}>Delivered</option>
                                                <option value="failed" {{ \Request::get('status') == 'failed' ? 'selected' : ''}}>Failed</option>
                                                <option value="attention-required" {{ \Request::get('status') == 'attention-required' ? 'selected' : ''}}>Attention Required</option>
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
                            {{-- <form method="post"> --}}
                                <table id="table-extended-success" class="table mb-0">
                                    <thead>
                                        <tr>
                                            <th><button class="btn btn-sm btn-warning m-0">Reorder</button></th>
                                            <th>Transaction ID</th>
                                            {{-- <th><button class="btn btn-sm btn-warning m-0">Reorder</button></th> --}}
                                            <th>Product</th>
                                            <th>Amount</th>
                                            <th>Amount Paid</th>
                                            <th>Biller</th>
                                            <th>Status</th>
                                            <th>Phone</th>
                                            <th>Email</th>
                                            <th>Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($transactions as $transaction)

                                            <tr>
                                                {{-- <td colspan="1">
                                                    <div class="checkbox checkbox-warning">
                                                        <input value="{{ $transaction->id }}" type="checkbox" id="colorCheckbox-{{ $transaction->id }}">
                                                        <label for="colorCheckbox-{{ $transaction->id }}"></label>
                                                    </div>
                                                </td> --}}
                                                <td>
                                                    {{ $transaction->product_name }}
                                                </td>
                                                <td>{!! getSettings()->currency. number_format($transaction->amount) !!}</td>
                                                <td>{!! getSettings()->currency. number_format($transaction->total_amount) !!}</td>
                                                <td>{{ $transaction->unique_element }}</td>
                                                <td>{{ $transaction->status }}</td>
                                                <td>{{ $transaction->customer_phone }}</td>
                                                <td>{{ $transaction->customer_email }}</td>
                                                <td>{{ date("M jS, Y g:iA", strtotime($transaction->created_at)) }}</td>

                                                <td>
                                                    <a class="btn btn-primary btn-sm mr-1 mb-1" href="/admin/single-transaction/{{  $transaction->id }}">
                                                        <i class="bx bxs-eye"></i>
                                                        <span class="align-middle ml-25">View</span>
                                                    </a>

                                                </td>
                                            </tr>
                                            {{-- @dump($transaction) --}}
                                        @endforeach
                                    </tbody>
                                </table>
                            {{-- </form> --}}
                            {{-- {{ $transactions->appends($query) }} --}}
                        </div>
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
