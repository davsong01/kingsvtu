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
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Username</th>
                                        <th>Status</th>
                                        <th>Account Type</th>
                                        <th>Joined</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($customers as $customer)
                                        <tr>
                                            <td class="text-bold-600 pr-0">
                                                <img class="rounded-circle mr-1" src="{{ $customer->avatart }}"
                                                    alt="image">
                                                <a
                                                    href="{{ request()->route()->getPrefix() }}/customer/edit/{{ $customer->id }}">
                                                    {{ $customer->fisrtname . ' ' . $customer->lastname }}</a>
                                            </td>
                                            <td>{{ $customer->email }}</td>
                                            <td>{{ $customer->phone }}</td>
                                            <td>{{ $customer->username }}</td>
                                            <td>{{ $customer->status }}</td>
                                            <td>{{ ucfirst($customer->type) }}</td>
                                            <td>{{ $customer->created_at->toDateString('en-GB') }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <span
                                                        class="bx bx-dots-vertical-rounded font-medium-3 dropdown-toggle nav-hide-arrow cursor-pointer"
                                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                        role="menu"></span>
                                                    <div class="dropdown-menu dropdown-menu-right">
                                                        <a class="dropdown-item"
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
