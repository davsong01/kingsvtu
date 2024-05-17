
<?php 

use Carbon\Carbon;

?>
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
                                                    {{-- @if($request->status == 'approved') --}} <br>
                                                   
                                                    <strong style="color:blue">Sub start:</strong> {{ !empty($request->request_details['subscription_start']) ? date("M jS, Y", strtotime($request->request_details['subscription_start'])) : date("M jS, Y", strtotime(Carbon::now())) }} <br>
                                                    <strong style="color:blue">Sub end:</strong> {{ !empty($request->request_details['subscription_end']) ? date("M jS, Y", strtotime($request->request_details['subscription_end'])) : date("M jS, Y", strtotime(Carbon::now())) }} <br>
                                                    <strong>Shop Status:</strong><span style="color:{{ $request->shop_status == 'active' ? 'green' : 'red'}}">{{ ucfirst($request->shop_status )}}</span>
                                                    
                                                </P>
                                            </td>
                                            <td style="color:{{$request->status == 'approved' ? 'green' : 'red'}}">{{ ucfirst($request->status) }}</td>
                                            <td>

                                                <button type="button" class="btn btn-outline-primary btn-sm mr-1 mb-1" data-toggle="modal" data-target="#primary-{{$request->id}}"><i class="fa fa-edit"></i><span class="align-middle ml-25"></span>Edit</button>
                                                
                                                @if($request->status == 'pending')
                                                <a href="{{ route('approve.shop.requests', $request->id) }}"><button type="button" class="btn btn-info btn-sm mr-1 mb-1"><i class="fa fa-check"></i><span class="align-middle ml-25">Approve Request</span></button>
                                                </a>

                                                <a href="{{ route('decline.shop.requests', $request->id) }}"><button type="button" class="btn btn-warning btn-sm mr-1 mb-1"><i class="fa fa-times"></i><span class="align-middle ml-25">Decline Request</span></button>
                                                </a>
                                                @endif
                                                @if($request->status == 'approved')
                                                <a target="_blank" href="{{ env('SHOPS_BASE_URL').$request->request_details['shop_slug'] }}"><button type="button" class="btn btn-info btn-sm mr-1 mb-1"><i class="fa fa-link"></i><span class="align-middle ml-25">Visit shop</span></button>
                                                </a>

                                                <a target="_blank" href="{{route('shop.access', $request->id)}}"><button type="button" class="btn btn-dark btn-sm mr-1 mb-1"><i class="fa fa-unlock"></i><span class="align-middle ml-25">Access shop</span></button>
                                                </a>

                                                @endif 

                                            <div class="modal fade text-left" id="primary-{{$request->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-primary">
                                                        <h5 class="modal-title white" id="myModalLabel160">Update Shop Details</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <i class="bx bx-x"></i>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="{{route('update.shop.requests', $request->id) }}" method="POST" enctype="multipart/form-data">
                                                            @csrf
                                                            <section id="mode-holder">
                                                                <div class="row" id="mode-0">
                                                                    <div class="col-md-4">
                                                                        <fieldset class="form-group">
                                                                            <label for="official_email">Official Email</label>
                                                                            <input type="email" class="form-control tiny" id="official_email" name="official_email"  value="{{ $request->request_details['official_email'] ?? old('official_email') }}" placeholder="Official Email" required>
                                                                        </fieldset>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <fieldset class="form-group">
                                                                            <label for="phone">Phone</label>
                                                                            <input type="text" class="form-control tiny" id="phone" name="phone"  value="{{ $request->request_details['phone'] ?? old('phone') }}" placeholder="Phone" required>
                                                                        </fieldset>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <fieldset class="form-group">
                                                                            <label for="first_name">First name</label>
                                                                            <input type="text" class="form-control tiny" id="first_name" name="first_name"  value="{{ $request->request_details['first_name'] ?? old('first_name') }}" placeholder="first_name" required>
                                                                        </fieldset>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <fieldset class="form-group">
                                                                            <label for="last_name">Last name</label>
                                                                            <input type="text" class="form-control tiny" id="last_name" name="last_name"  value="{{ $request->request_details['last_name'] ?? old('last_name') }}" placeholder="last_name" required>
                                                                        </fieldset>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <fieldset class="form-group">
                                                                            <label for="shop_name">Shop name</label>
                                                                            <input type="text" class="form-control tiny" id="shop_name" name="shop_name"  value="{{ $request->request_details['shop_name'] ?? old('shop_name') }}" placeholder="shop_name" required>
                                                                        </fieldset>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <fieldset class="form-group">
                                                                            <label for="shop_slug">Shop Slug</label>
                                                                            <input type="text" class="form-control tiny" id="shop_slug" name="shop_slug"  value="{{ $request->request_details['shop_slug'] ?? old('shop_slug') }}" placeholder="shop_slug" required>
                                                                        </fieldset>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <fieldset class="form-group">
                                                                            <label for="whatsapp_number">Whatsapp Number</label>
                                                                            <input type="text" class="form-control tiny" id="whatsapp_number" name="whatsapp_number"  value="{{ $request->request_details['whatsapp_number'] ?? old('whatsapp_number') }}" placeholder="whatsapp_number" required>
                                                                        </fieldset>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <fieldset class="form-group">
                                                                            <label for="subscription_start">Subscription Start</label>
                                                                            <input type="date" class="form-control tiny" id="subscription_start" name="subscription_start"  value="{{ $request->request_details['subscription_start'] ?? old('subscription_start') }}" placeholder="subscription_start" required>
                                                                        </fieldset>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <fieldset class="form-group">
                                                                            <label for="subscription_end">Subscription End</label>
                                                                            <input type="date" class="form-control tiny" id="subscription_end" name="subscription_end"  value="{{ $request->request_details['subscription_end'] ?? old('subscription_end') }}" placeholder="subscription_end" required>
                                                                        </fieldset>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <fieldset class="form-group">
                                                                            <label for="custom_domain">Custom Domain</label>
                                                                            <input type="text" class="form-control tiny" id="custom_domain" name="custom_domain"  value="{{ $request->request_details['custom_domain'] ?? old('custom_domain') }}" placeholder="custom_domain">
                                                                        </fieldset>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <fieldset class="form-group">
                                                                        <label for="shop_status">Shop Status</label>
                                                                        <select class="form-control" name="shop_status" id="shop_status" required>
                                                                            <option value="">Select</option>
                                                                            <option value="active" {{ $request->shop_status == 'active' ? 'selected' : ''}}>Active</option>
                                                                            <option value="inactive" {{ $request->shop_status == 'inactive' ? 'selected' : ''}}>InActive</option>
                                                                        </select>
                                                                    </div>
                                                                </fieldset>
                                                                </div>
                                                            </section>
                                                            
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light-secondary" data-dismiss="modal">
                                                            <i class="bx bx-x d-block d-sm-none"></i>
                                                            <span class="d-none d-sm-block">Close</span>
                                                        </button>
                                                        <button type="submit" class="btn btn-primary ml-1"><span class="d-none d-sm-block">Submit</span>
                                                        </button>
                                                    </div>
                                                    </form>
                                                </div>
                                            </div>
                                            {{-- end update modal --}}
                                            <a href="{{ route('delete.shop.requests', $request->id) }}"><button type="button" class="btn btn-danger btn-sm mr-1 mb-1"><i class="fa fa-times"></i><span class="align-middle ml-25">Delete</span></button></a>
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