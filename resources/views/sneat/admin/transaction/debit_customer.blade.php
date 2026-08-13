@extends('sneat.layouts.app')

@section('title', 'Debit Customer')

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="admin-page-hero mb-4">
                <div>
                    <span class="admin-page-hero__kicker">Financials</span>
                    <h1>Debit Customer</h1>
                    <p>Remove wallet funds with the same cleaner admin form and audit trail.</p>
                </div>
            </div>

            @include('sneat.layouts.alerts')

            <div class="row g-4">
                <div class="col-xl-8">
                    <div class="modern-admin-card card">
                        <div class="card-header">
                            <h3>Wallet debit</h3>
                            <p>Provide the customer email, amount, and the reason for the entry.</p>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.process.credit.debit') }}" method="POST">
                                @csrf
                                <input type="hidden" name="type" value="debit">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="modern-admin-label" for="email">Email</label>
                                        <input type="email" class="form-control form-control-{{ formControlSize() }}" id="email" name="email" value="{{ old('email') }}" placeholder="Enter customer email..." required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="modern-admin-label" for="amount">Amount</label>
                                        <input type="number" class="form-control form-control-{{ formControlSize() }}" id="amount" name="amount" value="{{ old('amount') }}" placeholder="Enter amount" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="modern-admin-label" for="reason">Reason</label>
                                        <textarea class="form-control form-control-{{ formControlSize() }}" id="reason" name="reason" rows="4" placeholder="Enter reason for this transaction">{{ old('reason') }}</textarea>
                                    </div>
                                </div>
                                <div class="modern-admin-footer mt-4">
                                    <button class="btn btn-admin-submit" type="submit">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="profile-side-card h-100">
                        <div class="profile-side-row">
                            <span>Type</span>
                            <strong>Debit</strong>
                        </div>
                        
                        <div class="profile-side-row">
                            <span>Note</span>
                            <strong>Use a reason for audit reasons.</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
