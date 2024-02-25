@extends('layouts.app')
@section('content')
    <!-- Content wrapper -->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <!-- Dashboard Ecommerce Starts -->
                <section id="dashboard-ecommerce">
                    <div class="row">
                        <!-- Greetings Content Starts -->
                        <div class="col-md-6 col-12 dashboard-greetings">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="greeting-text">Wallet Balance</h3>
                                    {{-- <p class="mb-0">Best seller of the month</p> --}}
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-end">
                                            <div class="dashboard-content-left">
                                                <?php
                                                $balance = auth()->user()->type == 'customer' ? getSettings()->currency . number_format(walletBalance(auth()->user())) : 0;
                                                $ref = auth()->user()->type == 'customer' ? getSettings()->currency . number_format(referralBalance(auth()->user())) : 0;
                                                ?>
                                                <h1 class="text-primary font-large-2 text-bold-500">{!! $balance !!}</h1>
                                                <div class="text-muted line-ellipsis">Referral Earnings</div>
                                                <h3 class="mb-2">{!! $ref !!}</h3>
                                                <a href="/customer-transactions" class="btn btn-primary glow">View Transactions</a>
                                            </div>
                                            {{-- <div class="dashboard-content-right">
                                                <img src="{{ asset('app-assets/images/icon/cup.png') }}" height="220"
                                                    width="220" class="img-fluid" alt="Dashboard Ecommerce" />
                                            </div> --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Multi Radial Chart Starts -->
                        <div class="col-md-6 col-12 dashboard-visit">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h4 class="card-title">Refer and Earn</h4>
                                    <i class="bx bx-dots-vertical-rounded font-medium-3 cursor-pointer"></i>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <p>
                                            Share your referral links with friends to earn handsome reward
                                            <div class="text-primary">{{ env('APP_URL').'/join-with-love/'. auth()->user()->username }}</div>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-12 dashboard-users">
                            <div class="row  ">
                                <!-- Statistics Cards Starts -->
                                <div class="col-12">
                                    <div class="row">
                                        <div class="col-sm-6 col-12 dashboard-users-success">
                                            <div class="card text-center">
                                                <div class="card-content">
                                                    <div class="card-body py-1">
                                                        <div
                                                            class="badge-circle badge-circle-lg badge-circle-light-success mx-auto mb-50">
                                                            <i class="bx bx-briefcase-alt font-medium-5"></i>
                                                        </div>
                                                        <div class="text-muted line-ellipsis">New Products</div>
                                                        <h3 class="mb-0">1.2k</h3>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-12 dashboard-users-danger">
                                            <div class="card text-center">
                                                <div class="card-content">
                                                    <div class="card-body py-1">
                                                        <div
                                                            class="badge-circle badge-circle-lg badge-circle-light-danger mx-auto mb-50">
                                                            <i class="bx bx-user font-medium-5"></i>
                                                        </div>
                                                        <div class="text-muted line-ellipsis">New Users</div>
                                                        <h3 class="mb-0">45.6k</h3>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                                <!-- Revenue Growth Chart Starts -->
                            </div>
                        </div>
                    </div>
                    <div class="row">
                       
                       
                        <!-- Earning Swiper Starts -->
                        <div class="col-xl-12 col-md-12 col-12 dashboard-earning-swiper" id="widget-earnings">
                            <div class="card">
                                <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                                    <h5 class="card-title"><i class="bx bx-dollar font-medium-5 align-middle"></i> <span
                                            class="align-middle">Earnings</span></h5>
                                    <i class="bx bx-dots-vertical-rounded font-medium-3 cursor-pointer"></i>
                                </div>
                                <div class="card-content">
                                    <div class="card-body py-1 px-0">
                                        <!-- earnings swiper starts -->
                                        <div class="widget-earnings-swiper swiper-container p-1">
                                            <div class="swiper-wrapper">
                                                <div class="swiper-slide rounded swiper-shadow py-50 px-2 d-flex align-items-center"
                                                    id="repo-design">
                                                    <i class="bx bx-pyramid mr-1 font-weight-normal font-medium-4"></i>
                                                    <div class="swiper-text">
                                                        <div class="swiper-heading">Repo Design</div>
                                                        <small class="d-block">Gitlab</small>
                                                    </div>
                                                </div>
                                                <div class="swiper-slide rounded swiper-shadow py-50 px-2 d-flex align-items-center"
                                                    id="laravel-temp">
                                                    <i class="bx bx-sitemap mr-50 font-large-1"></i>
                                                    <div class="swiper-text">
                                                        <div class="swiper-heading">Designer</div>
                                                        <small class="d-block">Women Clothes</small>
                                                    </div>
                                                </div>
                                                <div class="swiper-slide rounded swiper-shadow py-50 px-2 d-flex align-items-center"
                                                    id="admin-theme">
                                                    <i class="bx bx-check-shield mr-50 font-large-1"></i>
                                                    <div class="swiper-text">
                                                        <div class="swiper-heading">Best Sellers</div>
                                                        <small class="d-block">Clothing</small>
                                                    </div>
                                                </div>
                                                <div class="swiper-slide rounded swiper-shadow py-50 px-2 d-flex align-items-center"
                                                    id="ux-devloper">
                                                    <i class="bx bx-devices mr-50 font-large-1"></i>
                                                    <div class="swiper-text">
                                                        <div class="swiper-heading">Admin Template</div>
                                                        <small class="d-block">Global Network</small>
                                                    </div>
                                                </div>
                                                <div class="swiper-slide rounded swiper-shadow py-50 px-2 d-flex align-items-center"
                                                    id="marketing-guide">
                                                    <i class="bx bx-book-bookmark mr-50 font-large-1"></i>
                                                    <div class="swiper-text">
                                                        <div class="swiper-heading">Marketing Guide</div>
                                                        <small class="d-block">Books</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- earnings swiper ends -->
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                        <!-- Marketing Campaigns Starts -->
                        <div class="col-xl-12 col-12 dashboard-marketing-campaign">
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
                        </div>
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
@endsection
