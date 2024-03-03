<?php
use App\Models\BlackList;
?>
@extends('layouts.app')
@section('content')
    <!-- Content wrapper -->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-12 mb-2 mt-1">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb p-0 mb-0">
                                    <li class="breadcrumb-item"><a href="/"><i class="bx bx-home-alt"></i></a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item active">Customer Profile</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <section id="table-success">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Customer Profile</h5>
                        <div class="mt-3">
                            @include('layouts.alerts')
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="d-flex justify-content-center flex-column align-items-center">
                                    @if ($user->avatar)
                                        <img src="{{ $user->avatar }}" width="180px" height="180px"
                                            alt="{{ $user->firstname }}" class="users-avatar-shadow rounded-circle" />
                                    @else
                                        <div class="users-avatar-shadow rounded-circle">
                                            <svg xmlns="http://www.w3.org/2000/svg" height="180" viewBox="0 -960 960 960"
                                                width="180">
                                                <path fill="red"
                                                    d="M234-276q51-39 114-61.5T480-360q69 0 132 22.5T726-276q35-41 54.5-93T800-480q0-133-93.5-226.5T480-800q-133 0-226.5 93.5T160-480q0 59 19.5 111t54.5 93Zm246-164q-59 0-99.5-40.5T340-580q0-59 40.5-99.5T480-720q59 0 99.5 40.5T620-580q0 59-40.5 99.5T480-440Zm0 360q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q53 0 100-15.5t86-44.5q-39-29-86-44.5T480-280q-53 0-100 15.5T294-220q39 29 86 44.5T480-160Zm0-360q26 0 43-17t17-43q0-26-17-43t-43-17q-26 0-43 17t-17 43q0 26 17 43t43 17Zm0-60Zm0 360Z" />
                                            </svg>
                                        </div>
                                    @endif
                                    <h5 class="card-title mt-1">
                                        {{ ucfirst($user->firstname) . ' ' . ucfirst($user->middlename) . ' ' . ucfirst($user->lastname) }}
                                        ({{ $user->customer->customer_level ?? 'Level 1' }})</h5>
                                    <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                                    <a href="mailto:{{ $user->phone }}">{{ $user->phone }}</a>
                                </div>
                            </div>
                            <div class="col-sm-9">
                                <div class="card-body">
                                    <ul class="nav nav-tabs" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="account-tab" data-toggle="tab" href="#account"
                                                aria-controls="home" role="tab" aria-selected="true">
                                                <i class="bx bxs-bar-chart-alt-2 align-middle"></i>
                                                <span class="align-middle">Account</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="transactions-tab" data-toggle="tab" href="#transactions"
                                                aria-controls="home" role="tab" aria-selected="true">
                                                <i class="bx bxs-bar-chart-alt-2 align-middle"></i>
                                                <span class="align-middle">Transactions</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="downlines-tab" data-toggle="tab" href="#downlines"
                                                aria-controls="profile" role="tab" aria-selected="false">
                                                <i class="bx bx-user-plus align-middle"></i>
                                                <span class="align-middle">Downlines</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="ky-tabc" data-toggle="tab" href="#kyc"
                                                aria-controls="about" role="tab" aria-selected="false">
                                                <i class="bx bx-user align-middle"></i>
                                                <span class="align-middle">KYC</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="reserved-account-tab" data-toggle="tab"
                                                href="#reserved-account" aria-controls="about" role="tab"
                                                aria-selected="false">
                                                <i class="bx bx-wallet align-middle"></i>
                                                <span class="align-middle">Reserved Accounts</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="actions-tab" data-toggle="tab" href="#actions"
                                                aria-controls="about" role="tab" aria-selected="false">
                                                <i class="bx bx-repost align-middle"></i>
                                                <span class="align-middle">Actions</span>
                                            </a>
                                        </li>
                                    </ul>
                                    @php
                                        $colors = [
                                            'bg-primary',
                                            'bg-secondary',
                                            'bg-warning',
                                            'bg-danger',
                                            'bg-success',
                                            'bg-dark',
                                        ];
                                        shuffle($colors);
                                    @endphp
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="account" aria-labelledby="account-tab"
                                            role="tabpanel">
                                            <div class="row">
                                                @foreach ($balances as $key => $bal)
                                                    <div class="col-sm-4 col-md-4">
                                                        <div
                                                            class="card {{ $colors[array_rand($colors)] }} bg-lighten-2 p-0">
                                                            <div class="card-content">
                                                                <div class="card-body text-center">
                                                                    <p class="card-text white">
                                                                        {{ $key }}</p>
                                                                    <h5 class="card-title white">
                                                                        {!! $bal !!}
                                                                    </h5>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <h5 class="card-title mt-3">
                                                Profile Info
                                            </h5>
                                            <form
                                                action="{{ request()->route()->getPrefix() }}/customer/update/{{ $user->id }}"
                                                method="POST">
                                                <fieldset class="form-group">
                                                    <label for="firstname">First Name</label>
                                                    <input type="text" class="form-control" id="firstname"
                                                        placeholder="First name" value="{{ $user->firstname }}"
                                                        name="firstname">
                                                </fieldset>
                                                <fieldset class="form-group">
                                                    <label for="lastname">Last Name</label>
                                                    <input type="text" class="form-control" id="lastname"
                                                        placeholder="Last name" value="{{ $user->lastname }}"
                                                        name="lastname">
                                                </fieldset>
                                                <fieldset class="form-group">
                                                    <label for="status">Status</label>
                                                    <select name="status" class="form-control" id="status">
                                                        <option value="">Select Status</option>
                                                        <option value="active" @selected($user->status == 'active')>Active</option>
                                                        <option value="suspended" @selected($user->status == 'suspended')>Suspended
                                                        </option>
                                                        <option value="delete" @selected($user->status == 'delete')>Delete</option>
                                                        <option value="email-blacklist" @selected($user->status == 'email-blacklist')>Email
                                                            Blacklist</option>
                                                        <option value="phone-blacklist" @selected($user->status == 'phone-blacklist')>Phone
                                                            Blacklist</option>
                                                    </select>
                                                </fieldset>
                                                <fieldset class="form-group">
                                                    <label for="email">Email</label>
                                                    <input type="email" class="form-control" id="email"
                                                        placeholder="Email" value="{{ $user->email }}">
                                                </fieldset>
                                                <fieldset class="form-group">
                                                    <label for="username">Username</label>
                                                    <input type="text" class="form-control" id="username"
                                                        placeholder="Username" value="{{ $user->username }}"
                                                        name="username">
                                                </fieldset>
                                                <fieldset class="form-group">
                                                    <label for="phone">Phone</label>
                                                    <input type="phone" class="form-control" id="email"
                                                        placeholder="Phone number" value="{{ $user->phone }}"
                                                        name="phone">
                                                </fieldset>
                                                <fieldset class="form-group">
                                                    <button type="submit" class="btn btn-success">
                                                        Update
                                                    </button>
                                                </fieldset>
                                                @csrf
                                            </form>
                                        </div>
                                        <div class="tab-pane" id="transactions" aria-labelledby="transactions-tab"
                                            role="tabpanel">
                                            <div class="table-responsive">
                                                <table id="table-extended-success" class="table mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>Product</th>
                                                            <th>Amount</th>
                                                            <th>Amount Paid</th>
                                                            <th>Biller</th>
                                                            <th>Status</th>
                                                            <th>Transaction ID</th>
                                                            <th>Phone</th>
                                                            <th>Email</th>
                                                            <th>Date</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $trans = $user->customer
                                                                ->transactions()
                                                                ->latest()
                                                                ->paginate(10);
                                                        @endphp
                                                        @foreach ($trans as $transaction)
                                                            <tr>
                                                                <td>
                                                                    {{ $transaction->product_name }}
                                                                </td>
                                                                <td>{!! getSettings()->currency . number_format($transaction->amount) !!}</td>
                                                                <td>{!! getSettings()->currency . number_format($transaction->total_amount) !!}</td>
                                                                <td>{{ $transaction->unique_element }}</td>
                                                                <td>{{ $transaction->status }}</td>
                                                                <td>{{ $transaction->transaction_id }}</td>
                                                                <td>{{ $transaction->phone }}</td>
                                                                <td>{{ $transaction->email }}</td>
                                                                <td>{{ $transaction->created_at->toDateString('en-GB') }}
                                                                </td>
                                                                <td>
                                                                    <a class="btn btn-primary btn-sm mr-1 mb-1"
                                                                        href="/admin/single-transaction/{{ $transaction->id }}">
                                                                        <i class="bx bxs-eye"></i>
                                                                        <span class="align-middle ml-25">View</span>
                                                                    </a>

                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                                {{ $trans->render() }}
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="downlines" aria-labelledby="profile-tab"
                                            role="tabpanel">
                                            <table able class="table table-striped dataex-html5-selectors">
                                                <thead>
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Email</th>
                                                        <th>Phone</th>
                                                        <th>Total Earned</th>
                                                        <th>Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($downlines as $ref)
                                                        <tr>
                                                            <td>{{ ucfirst($ref->referredCustomer->user->firstname) . ' ' . ucfirst($ref->referredCustomer->user->lastname) }}
                                                            </td>
                                                            <td>{{ $ref->referredCustomer->user->email }}</td>
                                                            <td>{{ $ref->referredCustomer->user->phone }}</td>
                                                            <td>{!! getSettings()->currency . number_format($ref->total) !!}</td>
                                                            <td>{{ $ref->created_at->toDateTimeString() }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>

                                            </table>
                                        </div>
                                        <div class="tab-pane" id="kyc" aria-labelledby="about-tab"
                                            role="tabpanel">
                                            <h1>KYC</h1>
                                        </div>
                                        <div class="tab-pane" id="reserved-account" aria-labelledby="about-tab"
                                            role="tabpanel">
                                            <div class="row">
                                                @forelse ($accounts as $key => $account)
                                                    @if ($account->paymentgateway_id === 1)
                                                        <div class="col-sm-6 col-md-4">
                                                            <div class="card {{ $colors[$key] }} bg-lighten-2">
                                                                <div class="card-content">
                                                                    <div class="">
                                                                        <div
                                                                            class="d-flex align-items-center justify-content-center p-1">
                                                                            <h5 class="card-title white">
                                                                                {{ $account->account_number }}
                                                                            </h5>
                                                                        </div>
                                                                        <div class="card-body text-center pb-1 pt-0">
                                                                            <p class="card-text white">
                                                                                {{ $account->bank_name }}</p>
                                                                            <p class="card-text white">
                                                                                {{ $account->account_name }}</p>
                                                                            <div
                                                                                class="d-flex justify-content-center align-items-center gap-3">
                                                                                <button type="button"
                                                                                    class="btn btn-success">
                                                                                    Update
                                                                                </button>
                                                                                &nbsp;&nbsp;&nbsp;
                                                                                <button type="button"
                                                                                    class="btn btn-secondary">
                                                                                    Delete
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @empty
                                                    <p>No reserved account has been created by this customer</p>
                                                @endforelse
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="actions" aria-labelledby="about-tab"
                                            role="tabpanel">
                                            <div class="row">
                                                <div class="col-md-3 col-sm-4">
                                                    <div class="card bg-lighten-2 p-0 bg-warning">
                                                        <div class="card-body">
                                                            <div class="card-content">
                                                                <h5 class="card-title white">
                                                                    Verify Email
                                                                </h5>
                                                                <p class="text-center">
                                                                    @if ($user->email_verified_at)
                                                                        Verified
                                                                    @else
                                                                        Unverified
                                                                    @endif
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @php
                                                    // check blacklist status
                                                    $mail = BlackList::where('value', $user->email)->first();
                                                @endphp
                                                <div class="col-md-3 col-sm-4">
                                                    <div class="card bg-lighten-2 p-0 bg-dark">
                                                        <div class="card-body">
                                                            <div class="card-content">
                                                                <h5 class="card-title white">
                                                                    Blacklist Email
                                                                </h5>
                                                                @if ($mail)
                                                                    <div
                                                                        class="custom-control custom-switch custom-switch-success custom-switch-glow custom-control-inline mb-1">
                                                                        <input type="checkbox"
                                                                            class="custom-control-input"
                                                                            id="customSwitchGlow2"
                                                                            @checked($mail->status == 'active')
                                                                            onchange="toggleStatus()"
                                                                            data-id="{{ $mail->id }}"
                                                                            data-value="{{ $mail->status }}">
                                                                        <label class="custom-control-label"
                                                                            for="customSwitchGlow2">
                                                                        </label>
                                                                    </div>
                                                                @else
                                                                    <form action="{{ route('customer-blacklist.store') }}"
                                                                        method="POST">
                                                                        @csrf
                                                                        <input type="hidden" name="email"
                                                                            value="email">
                                                                        <input type="hidden" name="email"
                                                                            value="{{ $user->email }}">
                                                                        <input type="hidden" name="email"
                                                                            value="active">
                                                                        <button class="btn btn-danger" type="submit">Add
                                                                            to blacklist</button>
                                                                    </form>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @php
                                                    // check blacklist statu
                                                    $phone = BlackList::where('value', $user->phone)->first();
                                                @endphp
                                                <div class="col-md-3 col-sm-4">
                                                    <div class="card bg-lighten-2 p-0 bg-danger">
                                                        <div class="card-body">
                                                            <div class="card-content">
                                                                <h5 class="card-title white">
                                                                    Blacklist Phone
                                                                </h5>
                                                                @if ($phone)
                                                                    <div
                                                                        class="custom-control custom-switch custom-switch-success custom-switch-glow custom-control-inline mb-1">
                                                                        <input type="checkbox"
                                                                            class="custom-control-input"
                                                                            id="customSwitchGlow2"
                                                                            @checked($phone->status == 'active')
                                                                            onchange="toggleStatus()"
                                                                            data-id="{{ $phone->id }}"
                                                                            data-value="{{ $phone->status }}">
                                                                        <label class="custom-control-label"
                                                                            for="customSwitchGlow2">
                                                                        </label>
                                                                    </div>
                                                                @else
                                                                    <form action="{{ route('customer-blacklist.store') }}"
                                                                        method="POST">
                                                                        @csrf
                                                                        <input type="hidden" name="email"
                                                                            value="biller">
                                                                        <input type="hidden" name="email"
                                                                            value="{{ $user->phone }}">
                                                                        <input type="hidden" name="email"
                                                                            value="active">
                                                                        <button class="btn btn-danger"
                                                                            type="submit">Add to blacklist</button>
                                                                    </form>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    @section('page-script')
        <script>
            function toggleStatus() {
                let check = confirm('Are you sure you want to perform this action?');

                if (check) {
                    let status = $('#customSwitchGlow2').attr('data-value');
                    let id = $('#customSwitchGlow2').attr('data-id');

                    $.ajax({
                        url: '/admin/black-list-status',
                        data: {
                            status,
                            id
                        },
                        success: e => {
                            alert(e.message)
                            if (e.code == 1) {
                                let status = $('#customSwitchGlow2').attr('data-value', e.status);
                            }
                        },
                        error: () => alert('Request could not be completed!'),
                    });
                }
            }
        </script>

    @endsection
