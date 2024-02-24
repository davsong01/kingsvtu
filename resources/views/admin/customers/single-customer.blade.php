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
                        <h5 class="card-title">Customer Profile</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ request()->route()->getPrefix() }}/customer/update/{{ $customer->id }}" method="POST">
                            <fieldset class="form-group">
                                <label for="firstname">First Name</label>
                                <input type="text" class="form-control" id="firstname" placeholder="First name"
                                    value="{{ $customer->firstname }}" name="firstname">
                            </fieldset>
                            <fieldset class="form-group">
                                <label for="lastname">Last Name</label>
                                <input type="text" class="form-control" id="lastname" placeholder="Last name"
                                    value="{{ $customer->lastname }}" name="lastname">
                            </fieldset>
                            <fieldset class="form-group">
                                <label for="status">Status</label>
                                <select name="status" class="form-control" id="status">
                                    <option value="">Select Status</option>
                                    <option value="active" @selected($customer->status == 'active')>Active</option>
                                    <option value="suspended" @selected($customer->status == 'suspended')>Suspended</option>
                                    <option value="email-blacklist" @selected($customer->status == 'email-blacklist')>Email Blacklist</option>
                                    <option value="phone-blacklist" @selected($customer->status == 'phone-blacklist')>Phone Blacklist</option>
                                </select>
                            </fieldset>
                            <fieldset class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" placeholder="Email"
                                    value="{{ $customer->email }}" readonly>
                            </fieldset>
                            <fieldset class="form-group">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" id="username" placeholder="Username"
                                    value="{{ $customer->username }}" name="username" readonly>
                            </fieldset>
                            <fieldset class="form-group">
                                <label for="phone">Phone</label>
                                <input type="phone" class="form-control" id="email" placeholder="Phone number"
                                    value="{{ $customer->phone }}" name="phone">
                            </fieldset>
                            <fieldset class="form-group">
                                <button type="submit" class="btn btn-success">
                                    Update
                                </button>
                            </fieldset>
                            @csrf
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </div>
