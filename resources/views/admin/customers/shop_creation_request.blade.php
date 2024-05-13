@extends('layouts.app')
@section('title', 'All shops')
@section('page-css')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/vendors.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css') }}"> 
    
    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/vendors.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <!-- END: Vendor CSS-->
    
@endsection
@section('content')
    <!-- Content wrapper -->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <section id="table-success">
                <div class="card">
                    <div class="card-header">
                        <!-- head -->
                        <h5 class="card-title">Shops</h5>
                        @include('layouts.alerts')

                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table-extended-success" class="table table-striped dataex-html5-selectors">
                                <thead>
                                    <tr>
                                        <th>Customer</th>
                                        <th>Merchant Details</th>
                                        <th>Shop Details</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($requests as $request)
                                        <tr>
                                            <td>
                                                <a target="_blank" href="{{ route('customers.edit', $request->customer->id) }}">{{ $request->customer->user->firstname . ' ' . $request->customer->user->lastname }}</a>
                                            </td>
                                            <td>
                                                <P>
                                                    <strong>Name:</strong> {{ $request->request_details['first_name'] . ' ' . $request->request_details['last_name'] }} <br>
                                                    <strong>Email:</strong>{{ $request->request_details['email'] }} <br>
                                                    <strong>Phone:</strong> {{ $request->request_details['phone'] }}
                                                </P>
                                            </td>
                                            <td>
                                                <P>
                                                    <strong>Shop Name:</strong> {{ $request->request_details['shop_name'] }} <br>
                                                    <strong>Shop Slug:</strong> {{ $request->request_details['shop_slug'] }} <br>
                                                    <strong>Whatsapp Number:</strong> {{ $request->request_details['whatsapp_number'] }} <br>
                                                    <strong>Official Email:</strong>{{ $request->request_details['official_email'] }} <br>
                                                    <strong>Currency:</strong> {{ $request->request_details['currency'] }}
                                                </P>
                                            </td>
                                            <td style="color:{{$request->status == 'approved' ? 'green' : 'red'}}">{{ ucfirst($request->status) }}</td>
                                            <td>
                                                @if($request->status == 'pending')
                                                <a href="{{ route('approve.shop.requests', $request->id) }}"><button type="button" class="btn btn-info btn-sm mr-1 mb-1"><i class="fa fa-check"></i><span class="align-middle ml-25">Approve Request</span></button>
                                                </a>

                                                <a href="{{ route('decline.shop.requests', $request->id) }}"><button type="button" class="btn btn-warning btn-sm mr-1 mb-1"><i class="fa fa-times"></i><span class="align-middle ml-25">Decline Request</span></button>
                                                </a>
                                                @endif
                                                @if($request->status == 'approved')
                                                <a target="_blank" href="{{ env('SHOPS_BASE_URL').$request->request_details['shop_slug'] }}"><button type="button" class="btn btn-info btn-sm mr-1 mb-1"><i class="fa fa-link"></i><span class="align-middle ml-25">Visit shop</span></button>
                                                </a>
                                                @else 
                                            
                                            @endif
                                            <a href="{{ route('delete.shop.requests', $request->id) }}"><button type="button" class="btn btn-danger btn-sm mr-1 mb-1"><i class="fa fa-times"></i><span class="align-middle ml-25">Delete Request</span></button></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{-- {{ $customers->render() }} --}}
                        </div>
                    </div>
                    <!-- datatable ends -->
                </div>
            </section>
        </div>
    </div>
@endsection
@section('page-script')
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/datatables.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/buttons.print.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/buttons.bootstrap.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/pdfmake.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/vfs_fonts.js') }}"></script>
    <script src="{{ asset('app-assets/js/scripts/datatables/datatable.js') }}"></script>
@endsection