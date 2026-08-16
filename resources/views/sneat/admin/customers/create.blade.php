@extends('sneat.layouts.app')

@section('title', 'Add Blacklist Entry')

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="admin-page-hero mb-4">
                <div>
                    <span class="admin-page-hero__kicker">Customer controls</span>
                    <h1>Add blacklist entry</h1>
                    <p>Create a blocked email or phone entry from the same modern admin shell.</p>
                </div>
            </div>

            @include('sneat.layouts.alerts')

            <div class="gateway-card card">
                <div class="card-header">
                    <h3>New blacklist item</h3>
                    <p>Add an email, phone number, or other blocked identifier.</p>
                </div>
                <div class="card-body">
                    <form action="{{ route('customer-blacklist.store') }}" method="POST">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="modern-admin-label" for="type">Type</label>
                                <select class="form-select form-select-{{ formControlSize() }}" name="type" id="type" required>
                                    <option value="">Select type</option>
                                    <option value="email" @selected(old('type') === 'email')>Email</option>
                                    <option value="phone" @selected(old('type') === 'phone')>Phone</option>
                                    <option value="biller" @selected(old('type') === 'biller')>Biller</option>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <label class="modern-admin-label" for="value">Blacklist value</label>
                                <input type="text" class="form-control form-control-{{ formControlSize() }}" name="value" id="value" value="{{ old('value') }}" placeholder="Enter item to blacklist" required>
                            </div>
                        </div>

                        <div class="d-flex justify-content-start mt-4 gap-2">
                            <button class="btn btn-admin-submit" type="submit">Save entry</button>
                            <a href="{{ route('customer-blacklist.index') }}" class="gateway-action">Back to list</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
