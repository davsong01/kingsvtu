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
                        <h5 class="card-title">Customers {{ isset($status) ? "($status)" : '' }}</h5>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table-extended-success" class="table mb-0">
                                <thead>
                                    <tr>
                                        <th>Details</th>
                                        <th>Username</th>
                                        <th>Status</th>
                                        <th>Level</th>
                                        <th>Balance</th>
                                        <th>Joined</th>
                                        @if(hasAccess('customers.edit'))
                                        <th>Action</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($customers as $customer)
                                        <tr>
                                            <td>
                                                <P>
                                                    Name:<a target="_blank" href="{{ request()->route()->getPrefix() }}/customer/edit/{{ $customer->id }}">
                                                        {{ $customer->firstname . ' ' . $customer->lastname }}</a> <br>
                                                    Email:  {{ $customer->email }} <br>
                                                    Phone Number: {{ $customer->phone }}
                                                </P>
                                            </td>
                                            
                                            <td>{{ $customer->username }}</td>
                                            <td> <small><strong>{{ ucfirst($customer->status) }}</strong></small></td>
                                            <td>{{ $customer->customer->level->name ?? 'N/A' }}</td>
                                            <td>
                                                Wallet: {!! getSettings()->currency !!}{{ number_format(walletBalance($customer)) }} <br>
                                                Referral:  {!! getSettings()->currency !!}{{ number_format(referralBalance($customer)) }} <br>
                                            </td>
                                            <td>{{ $customer->created_at->toDateString('en-GB') }}</td>
                                            @if(hasAccess('customers.edit'))
                                            <td>
                                                <a href="{{ route('customers.edit', $customer->id) }}"><button type="button" class="btn btn-info btn-sm mr-1 mb-1"><i class="fa fa-edit"></i><span class="align-middle ml-25">View</span></button>
                                                </a>
                                            </td>
                                            @endif
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
