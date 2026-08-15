@extends('sneat.layouts.app')

@section('title', 'Unverified Customers')

@section('content')
    @php
        $summary = [
            ['label' => 'Unverified Customers', 'value' => number_format($totalCustomers), 'icon' => 'bx-badge-check', 'tone' => 'amber'],
            ['label' => 'Bulk Mode', 'value' => hasAccess('customers.verify') ? 'Enabled' : 'Disabled', 'icon' => 'bx-select-multiple', 'tone' => 'blue'],
        ];
    @endphp

    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="admin-page-hero mb-4">
                <div>
                    <span class="admin-page-hero__kicker">Customer compliance</span>
                    <h1>Unverified Customers</h1>
                    <p>Review accounts that have not yet verified email, then verify or remove them from a clean admin table.</p>
                </div>
                <div class="admin-page-badges">
                    @foreach($summary as $item)
                        <div class="admin-page-badge">
                            <span>{{ $item['label'] }}</span>
                            <strong>{{ $item['value'] }}</strong>
                        </div>
                    @endforeach
                </div>
            </div>

            @include('sneat.layouts.alerts')

            <div class="row g-3 mb-4">
                @foreach($summary as $item)
                    <div class="col-md-6">
                        <div class="admin-stat-card admin-stat-card--{{ $item['tone'] }}">
                            <div class="admin-stat-card__icon">
                                <i class="bx {{ $item['icon'] }}"></i>
                            </div>
                            <div class="admin-stat-card__label">{{ $item['label'] }}</div>
                            <div class="admin-stat-card__value">{{ $item['value'] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="gateway-card card">
                <div class="card-header d-flex flex-column flex-lg-row align-items-lg-start justify-content-between gap-3">
                    <div>
                        <h3>Bulk actions</h3>
                        <p>Use the checkboxes to verify or delete multiple unverified accounts at once.</p>
                    </div>
                    <span class="gateway-badge gateway-badge--warning">{{ $customers->total() }} records</span>
                </div>
                <div class="card-body">
                    @if(hasAccess('customers.verify'))
                        <form id="actionForm" method="POST" action="{{ route('verify-users-actions') }}" class="mb-4">
                            @csrf
                            <div class="row g-3 align-items-end">
                                <div class="col-lg-4 col-xl-3">
                                    <label for="action-select" class="modern-admin-label">Bulk action</label>
                                    <select id="action-select" class="form-select form-select-{{ formControlSize() }}" name="action" required>
                                        <option value="" disabled selected>Select action</option>
                                        <option value="verify">Verify</option>
                                        <option value="delete">Delete</option>
                                    </select>
                                </div>
                                <div class="col-lg-4 col-xl-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-admin-submit w-100" id="submit-action">Apply to selected</button>
                                </div>
                                <div class="col-lg-4 col-xl-7 text-lg-end">
                                    <div class="gateway-helper">Choose rows from the table below before applying the action.</div>
                                </div>
                            </div>
                        </form>
                    @endif

                    <div class="table-responsive">
                        <table class="table gateway-table align-middle">
                            <thead>
                                <tr>
                                    @if(hasAccess('customers.verify'))
                                        <th class="customer-select-col">
                                            <input type="checkbox" id="select-all" class="form-check-input form-check-input-{{ checkBoxControlSize() }}">
                                        </th>
                                    @endif
                                    <th>S/N</th>
                                    <th>Customer</th>
                                    <th>Status</th>
                                    <th>Joined</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customers as $customer)
                                    @php
                                        $serialNumber = $customers->firstItem() + $loop->index;
                                    @endphp
                                    <tr>
                                        @if(hasAccess('customers.verify'))
                                            <td>
                                                <input type="checkbox" class="customer-checkbox form-check-input form-check-input-{{ checkBoxControlSize() }}" value="{{ $customer->id }}">
                                            </td>
                                        @endif
                                        <td>{{ $serialNumber }}</td>
                                        <td>
                                            <div class="gateway-name">
                                                <a href="{{ route('customers.edit', $customer->id) }}">{{ trim($customer->firstname . ' ' . $customer->lastname) ?: 'Unnamed customer' }}</a>
                                            </div>
                                            <div class="gateway-helper">{{ $customer->username ?: 'No username set' }}</div>
                                            <div class="gateway-helper">{{ $customer->email }}</div>
                                            <div class="gateway-helper">{{ $customer->phone }}</div>
                                        </td>
                                        <td>
                                            <span class="gateway-badge gateway-badge--warning">Unverified</span>
                                        </td>
                                        <td>{{ optional($customer->created_at)->toDateString() }}</td>
                                        <td class="text-end">
                                            @if(hasAccess('customers.verify'))
                                                <div class="gateway-row-actions justify-content-end">
                                                    <a onclick="return confirm('Are you sure you want to verify this user?');" href="{{ route('customer.verify', $customer->id) }}" class="gateway-action">Verify</a>
                                                    <a onclick="return confirm('Are you sure you want to delete this user, this action is irreversible?');" href="{{ route('customer.delete', $customer->id) }}" class="gateway-action gateway-action--danger">Delete</a>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ hasAccess('customers.verify') ? 6 : 5 }}">
                                            <div class="alert alert-light border mb-0">No unverified customers found.</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        {{ $customers->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        $('#select-all').on('change', function () {
            $('.customer-checkbox').prop('checked', this.checked);
        });

        $('#submit-action').on('click', function (e) {
            if (!$('#action-select').val()) {
                e.preventDefault();
                alert('Please select an action.');
                return;
            }

            const selectedCustomerIds = $('.customer-checkbox:checked').map(function () {
                return $(this).val();
            }).get();

            if (selectedCustomerIds.length === 0) {
                e.preventDefault();
                alert('Please select at least one customer.');
                return;
            }

            $('<input>').attr({
                type: 'hidden',
                name: 'customer_ids',
                value: selectedCustomerIds
            }).appendTo('#actionForm');
        });
    </script>
@endsection
