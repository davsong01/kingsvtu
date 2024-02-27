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
                        <h5 class="card-title mb-2">Blacklists</h5>
                        <a href="{{ route('customer-blacklist.create') }}"><button id="addRow"
                                class="btn btn-primary mb-2 d-flex align-items-center"><i class="bx bx-plus"></i>&nbsp; Add
                                To Blacklist</button></a>
                        @include('layouts.alerts')
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table-extended-success" class="table mb-0">
                                <thead>
                                    <tr>
                                        <th>Details</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($customers as $customer)
                                        <tr>
                                            {{-- <td>
                                                <img class="rounded-circle mr-1" src="{{ $customer->avatar }}"
                                                    alt="image">
                                                Name:<a target="_blank" href="{{ request()->route()->getPrefix() }}/customer/edit/{{ $customer->id }}">
                                                    {{ $customer->firstname . ' ' . $customer->lastname }}</a> <br>
                                                   Email:  {{ $customer->email }} <br>
                                                   Phone Number: {{ $customer->phone }}
                                            </td> --}}

                                            <td>{{ $customer->value }}</td>
                                            {{-- <td>{{ ucfirst($customer->status) }}</td> --}}
                                            <td>{{ $customer->created_at->toDateString('en-GB') }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <span
                                                        class="bx bx-dots-vertical-rounded font-medium-3 dropdown-toggle nav-hide-arrow cursor-pointer"
                                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                        role="menu"></span>
                                                    <div class="dropdown-menku dropdown-menu-right">
                                                        <a
                                                            href="{{ request()->route()->getPrefix() }}/customer/edit/{{ $customer->id }}"><i
                                                                class="bx bx-eye-alt mr-1"></i> View</a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{ $customers->render() }}
                        </div>
                    </div>
                    <!-- datatable ends -->
                </div>
            </section>
        </div>
    </div>
