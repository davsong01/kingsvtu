@extends('layouts.app')
@section('content')
    <!-- Content wrapper -->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <section id="dashboard-ecommerce">
                    <div class="row">
                        <div class="col-md-12">
                            @include('layouts.alerts')
                        </div>
                        @if(!empty($customer))
                        <div class="col-xl-3 col-12 dashboard-users">
                            <a href="{{ route('customers.edit', $customer->customer_id)}}">
                            <div class="card text-center">
                                <div class="card-content">
                                    <div class="card-body py-1" style="min-height: 133px;">
                                        <span style="margin-top:5px"></span>
                                        <div class="text-muted line-ellipsis"><strong>Customer of the month</strong></div>
                                        <h4 class="text-primary text-bold-500">{{$customer->customer->user->username}}</h4>
                                        <h4 class="mb-0">{{number_format($customer->count)}}+ Transactions</h4>
                                    </div>
                                </div>
                            </div>
                            </a>
                        </div>
                        @endif
                        <div class="col-xl-3 col-12 dashboard-users">
                            <div class="card text-center">
                                <div class="card-content">
                                    <div class="card-body py-1">
                                        <div class="badge-circle badge-circle-lg badge-circle-light-success mx-auto mb-50">
                                            <i class="fa fa-server font-medium-5"></i>
                                        </div>
                                        <div class="text-muted line-ellipsis">SERVER ADDRESS</div>
                                        <h4 class="mb-0">{{ $_SERVER['SERVER_ADDR'] ?? 'NOT SET' }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-12 dashboard-users">
                            <div class="card text-center">
                                <div class="card-content">
                                    <div class="card-body py-1">
                                        <div class="badge-circle badge-circle-lg badge-circle-light-success mx-auto mb-50">
                                            <i class="fa fa-server font-medium-5"></i>
                                        </div>
                                        <div class="text-muted line-ellipsis">REMOTE ADDRESS</div>
                                        <h4 class="mb-0">{{ $_SERVER['REMOTE_ADDR'] ?? ' ' }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-12 dashboard-users">
                            <div class="card text-center">
                                <div class="card-content">
                                    <div class="card-body py-1">
                                        <div class="badge-circle badge-circle-lg badge-circle-light-success mx-auto mb-50">
                                            <svg fill="#39DA8A" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24"><path d="M200-200v-560 560Zm0 80q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v100h-80v-100H200v560h560v-100h80v100q0 33-23.5 56.5T760-120H200Zm320-160q-33 0-56.5-23.5T440-360v-240q0-33 23.5-56.5T520-680h280q33 0 56.5 23.5T880-600v240q0 33-23.5 56.5T800-280H520Zm280-80v-240H520v240h280Zm-160-60q25 0 42.5-17.5T700-480q0-25-17.5-42.5T640-540q-25 0-42.5 17.5T580-480q0 25 17.5 42.5T640-420Z"/></svg>
                                        </div>
                                        <div class="text-muted line-ellipsis"><strong>Total Wallet Balances</strong></div>
                                        <h4 class="mb-0">{!!getSettings()->currency!!}{{number_format($total_wallet_balance, 2) }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-12 dashboard-users">
                            <div class="card text-center">
                                <div class="card-content">
                                    <div class="card-body py-1">
                                        <div class="text-muted line-ellipsis"><strong>All Transactions</strong></div>
                                        <div style="margin-top: 4px;" id="transaction-response"></div>
                                        
                                        <p>
                                            <div style="margin-top:15px" onclick="viewTransactions()" id="view-transactions" class="btn btn-info">View <i style="display:none" id="transaction-spinner" class="fa fa-spinner fa-spin"></i></div>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-12 dashboard-users">
                            <div class="card text-center">
                                <div class="card-content">
                                    <div class="card-body py-1">
                                        <div class="text-muted line-ellipsis"><strong>Referral Earnings</strong></div>
                                        <span style="margin-top:50px"></span>
                                        <div style="margin-top: 10px;" id="referral-response"></div>

                                        <p>
                                            <div style="margin-top:20px" onclick="viewReferral()" id="view-referrals" class="btn btn-info">View <i style="display:none" id="referral-spinner" class="fa fa-spinner fa-spin"></i></div>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-12 dashboard-users">
                            <div class="card text-center">
                                <div class="card-content">
                                    <div class="card-body py-1">
                                        <div class="badge-circle badge-circle-lg badge-circle-light-success mx-auto mb-50">
                                            <i class="bx bx-briefcase-alt font-medium-5"></i>
                                        </div>
                                        <div class="text-muted line-ellipsis">KYC Verified Users</div>
                                        <h3 class="mb-0">{{ number_format($kyc_verified) }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-12 dashboard-users">
                            <div class="card text-center">
                                <div class="card-content">
                                    <div class="card-body py-1">
                                        <div class="badge-circle badge-circle-lg badge-circle-light-success mx-auto mb-50">
                                            <i class="bx bx-user font-medium-5"></i>
                                        </div>
                                        <div class="text-muted line-ellipsis">Registered Users</div>
                                        <h3 class="mb-0">{{ number_format($customers) }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-12 dashboard-users">
                            <div class="card text-center">
                                <div class="card-content">
                                    <div class="card-body py-1">
                                        <div class="badge-circle badge-circle-lg badge-circle-light-success mx-auto mb-50">
                                            <i class="bx bx-user font-medium-5"></i>
                                        </div>
                                        <div class="text-muted line-ellipsis">Active Users</div>
                                        <h3 class="mb-0">{{ number_format($active_customers) }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <!-- Earning Swiper Starts -->
                        <div class="col-md-12 dashboard-earning-swiper" id="widget-earnings">
                            <div class="card">
                                <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                                    <h5 class="card-title"><span
                                            class="align-middle"> API Provider Stats</span></h5>
                                </div>
                                <div class="card-content">
                                    <div class="card-body py-1 px-0">
                                        <!-- earnings swiper starts -->
                                        <div class="widget-earnings-swiper swiper-container p-1">
                                            <div class="swiper-wrapper">
                                                @foreach($apis as $api)
                                                <div class="swiper-slide rounded swiper-shadow py-50 px-2 d-flex align-items-center"
                                                    id="repo-design">
                            
                                                    <div class="col-md-3">
                                                        <div class="swiper-text">
                                                            <div class="swiper-heading">Provider name</div>
                                                            <small style="color:black"><strong>{{ $api->name }}</strong></small>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="swiper-text">
                                                            <div class="swiper-heading">Balance</div>
                                                            <small style="color:black"><strong>@if($api->balance)
                                                                {!! getSettings()->currency !!} {{ number_format($api->balance, 2) }} @else N/A @endif
                                                            </strong></small>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="swiper-text">
                                                            <div class="swiper-heading">No of Transactions</div>
                                                            <small style="color:black"><strong>{{ $api->transactions->count()}} ({!! getSettings()->currency !!}{{ number_format($api->transactions->sum('total_amount'))}})</strong></small>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-md-3">
                                                        <div class="swiper-text">
                                                            <div class="swiper-heading">No Products</div>
                                                            <small style="color:black"><strong>{{ $api->products->count()}}</strong></small>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <!-- earnings swiper ends -->
                                    </div>
                                </div>

                            </div>
                        </div>
                        <!-- Marketing Campaigns Starts -->
                        {{-- <div class="col-xl-12 col-12 dashboard-marketing-campaign">
                            <div class="card marketing-campaigns">
                                <div class="card-header d-flex justify-content-between align-items-center pb-1">
                                    <h4 class="card-title">Marketing campaigns</h4>
                                    <i class="bx bx-dots-vertical-rounded font-medium-3 cursor-pointer"></i>
                                </div>
                                <div class="card-content">
                                    <div class="card-body pb-0">
                                        <div class="row">
                                            <div class="col-md-9 col-12">
                                                <div class="d-inline-block">
                                                    <!-- chart-1   -->
                                                    <div class="d-flex market-statistics-1">
                                                        <!-- chart-statistics-1 -->
                                                        <div id="donut-success-chart"></div>
                                                        <!-- data -->
                                                        <div class="statistics-data my-auto">
                                                            <div class="statistics">
                                                                <span
                                                                    class="font-medium-2 mr-50 text-bold-600">25,756</span><span
                                                                    class="text-success">(+16.2%)</span>
                                                            </div>
                                                            <div class="statistics-date">
                                                                <i
                                                                    class="bx bx-radio-circle font-small-1 text-success mr-25"></i>
                                                                <small class="text-muted">May 12, 2019</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="d-inline-block">
                                                    <!-- chart-2 -->
                                                    <div class="d-flex mb-75 market-statistics-2">
                                                        <!-- chart statistics-2 -->
                                                        <div id="donut-danger-chart"></div>
                                                        <!-- data-2 -->
                                                        <div class="statistics-data my-auto">
                                                            <div class="statistics">
                                                                <span
                                                                    class="font-medium-2 mr-50 text-bold-600">5,352</span><span
                                                                    class="text-danger">(-4.9%)</span>
                                                            </div>
                                                            <div class="statistics-date">
                                                                <i
                                                                    class="bx bx-radio-circle font-small-1 text-success mr-25"></i>
                                                                <small class="text-muted">Jul 26, 2019</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-12 text-md-right">
                                                <button class="btn btn-sm btn-primary glow mt-md-2 mb-1">View
                                                    Report</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <!-- table start -->
                                    <table id="table-marketing-campaigns"
                                        class="table table-borderless table-marketing-campaigns mb-0">
                                        <thead>
                                            <tr>
                                                <th>Campaign</th>
                                                <th>Growth</th>
                                                <th>Charges</th>
                                                <th>Status</th>
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="py-1 line-ellipsis">
                                                    <img class="rounded-circle mr-1"
                                                        src="{{ asset('') }}app-assets/images/icon/fs.png"
                                                        alt="card" height="24" width="24">Fastrack Watches
                                                </td>
                                                <td class="py-1">
                                                    <i
                                                        class="bx bx-trending-up text-success align-middle mr-50"></i><span>30%</span>
                                                </td>
                                                <td class="py-1">$5,536</td>
                                                <td class="text-success py-1">Active</td>
                                                <td class="text-center py-1">
                                                    <div class="dropdown">
                                                        <span
                                                            class="bx bx-dots-vertical-rounded font-medium-3 dropdown-toggle nav-hide-arrow cursor-pointer"
                                                            data-toggle="dropdown" aria-haspopup="true"
                                                            aria-expanded="false" role="menu"></span>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            <a class="dropdown-item" href="#"><i
                                                                    class="bx bx-edit-alt mr-1"></i> edit</a>
                                                            <a class="dropdown-item" href="#"><i
                                                                    class="bx bx-trash mr-1"></i> delete</a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="py-1 line-ellipsis">
                                                    <img class="rounded-circle mr-1"
                                                        src="{{ asset('') }}app-assets/images/icon/puma.png"
                                                        alt="card" height="24" width="24">Puma Shoes
                                                </td>
                                                <td class="py-1">
                                                    <i
                                                        class="bx bx-trending-down text-danger align-middle mr-50"></i><span>15.5%</span>
                                                </td>
                                                <td class="py-1">$1,569</td>
                                                <td class="text-success py-1">Active</td>
                                                <td class="text-center py-1">
                                                    <div class="dropdown">
                                                        <span
                                                            class="bx bx-dots-vertical-rounded font-medium-3 dropdown-toggle nav-hide-arrow cursor-pointer"
                                                            data-toggle="dropdown" aria-haspopup="true"
                                                            aria-expanded="false" role="menu">
                                                        </span>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            <a class="dropdown-item" href="#"><i
                                                                    class="bx bx-edit-alt mr-1"></i> edit</a>
                                                            <a class="dropdown-item" href="#"><i
                                                                    class="bx bx-trash mr-1"></i> delete</a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="py-1 line-ellipsis">
                                                    <img class="rounded-circle mr-1"
                                                        src="{{ asset('') }}app-assets/images/icon/nike.png"
                                                        alt="card" height="24" width="24">Nike Air Jordan
                                                </td>
                                                <td class="py-1">
                                                    <i
                                                        class="bx bx-trending-up text-success align-middle mr-50"></i><span>70.30%</span>
                                                </td>
                                                <td class="py-1">$23,859</td>
                                                <td class="text-danger py-1">Closed</td>
                                                <td class="text-center py-1">
                                                    <div class="dropdown">
                                                        <span
                                                            class="bx bx-dots-vertical-rounded font-medium-3 dropdown-toggle nav-hide-arrow cursor-pointer"
                                                            data-toggle="dropdown" aria-haspopup="true"
                                                            aria-expanded="false" role="menu">
                                                        </span>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            <a class="dropdown-item" href="#"><i
                                                                    class="bx bx-edit-alt mr-1"></i> edit</a>
                                                            <a class="dropdown-item" href="#"><i
                                                                    class="bx bx-trash mr-1"></i> delete</a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="py-1 line-ellipsis">
                                                    <img class="rounded-circle mr-1"
                                                        src="{{ asset('') }}app-assets/images/icon/one-plus.png"
                                                        alt="card" height="24" width="24">Oneplus 7 pro
                                                </td>
                                                <td class="py-1">
                                                    <i
                                                        class="bx bx-trending-up text-success align-middle mr-50"></i><span>10.4%</span>
                                                </td>
                                                <td class="py-1">$9,523</td>
                                                <td class="text-success py-1">Active</td>
                                                <td class="text-center py-1">
                                                    <div class="dropdown">
                                                        <span
                                                            class="bx bx-dots-vertical-rounded font-medium-3 dropdown-toggle nav-hide-arrow cursor-pointer"
                                                            data-toggle="dropdown" aria-haspopup="true"
                                                            aria-expanded="false" role="menu">
                                                        </span>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            <a class="dropdown-item" href="#"><i
                                                                    class="bx bx-edit-alt mr-1"></i> edit</a>
                                                            <a class="dropdown-item" href="#"><i
                                                                    class="bx bx-trash mr-1"></i> delete</a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="py-1 line-ellipsis">
                                                    <img class="rounded-circle mr-1"
                                                        src="{{ asset('') }}app-assets/images/icon/google.png"
                                                        alt="card" height="24" width="24">Google Pixel 4 xl
                                                </td>
                                                <td class="py-1"><i
                                                        class="bx bx-trending-down text-danger align-middle mr-50"></i><span>-62.38%</span>
                                                </td>
                                                <td class="py-1">12,897</td>
                                                <td class="text-danger py-1">Closed</td>
                                                <td class="text-center py-1">
                                                    <div class="dropup">
                                                        <span
                                                            class="bx bx-dots-vertical-rounded font-medium-3 dropdown-toggle nav-hide-arrow cursor-pointer"
                                                            data-toggle="dropdown" aria-haspopup="true"
                                                            aria-expanded="false" role="menu">
                                                        </span>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            <a class="dropdown-item" href="#"><i
                                                                    class="bx bx-edit-alt mr-1"></i> edit</a>
                                                            <a class="dropdown-item" href="#"><i
                                                                    class="bx bx-trash mr-1"></i> delete</a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <!-- table ends -->
                                </div>
                            </div>
                        </div> --}}
                    </div>
                </section>
                <!-- Dashboard Ecommerce ends -->

            </div>
        </div>
    </div>
    </div>
@endsection
@section('page-script')
    <script src="{{ asset('app-assets/js/scripts/pages/dashboard-analytics.js') }}"></script>
    <script>
        function viewReferral(){
            $.ajax({
                type: "GET",
                url: "{{url('/')}}/admin/dashboard-widgets/referrals",
                beforeSend: function () {
                    $('#referral-spinner').show();
                },
                success: function(data, textStatus, jQxhr) {
                    $('#referral-response').html(data.data);
                    $('#view-referrals').hide();
                    $('#referral-spinner').hide();
                },
                error: function(jQxhr, textStatus, errorThrown) {
                    $('#referral-spinner').hide();
                }
            });
        }

        function viewTransactions() {
            $.ajax({
                type: "GET",
                url: "{{url('/')}}/admin/dashboard-widgets/transactions",
                beforeSend: function () {
                    $('#transaction-spinner').show();
                },
                success: function(data, textStatus, jQxhr) {
                    $('#transaction-response').html(data.data);
                    $('#view-transactions').hide();
                    $('#transaction-spinner').hide();
                },
                error: function(jQxhr, textStatus, errorThrown) {
                    $('#transaction-spinner').hide();
                }
            });
        }
    </script>
@endsection
