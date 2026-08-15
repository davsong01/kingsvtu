@extends('sneat.layouts.app')

@section('title', 'Customer Levels')

@section('page-css')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css') }}">
@endsection

@section('content')
    @php
        $summary = [
            ['label' => 'Total Levels', 'value' => number_format($levels->count()), 'icon' => 'bx-layer', 'tone' => 'blue'],
            ['label' => 'Active Levels', 'value' => number_format($levels->where('status', 1)->count()), 'icon' => 'bx-check-circle', 'tone' => 'green'],
            ['label' => 'Inactive Levels', 'value' => number_format($levels->where('status', 0)->count()), 'icon' => 'bx-block', 'tone' => 'amber'],
            ['label' => 'Customers Covered', 'value' => number_format($levels->sum('customers_count')), 'icon' => 'bx-group', 'tone' => 'indigo'],
        ];
    @endphp

    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="admin-page-hero mb-4">
                <div>
                    <span class="admin-page-hero__kicker">Level management</span>
                    <h1>Customer Levels</h1>
                    <p>Manage the customer levels, update upgrade amounts, and keep the hierarchy organized from a clean admin table.</p>
                </div>
                <a href="{{ route('customerlevel.create') }}" class="btn btn-admin-submit">Add level</a>
            </div>

            @include('sneat.layouts.alerts')

            <div class="row g-3 mb-4">
                @foreach($summary as $item)
                    <div class="col-md-6 col-xl-3">
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
                        <h3>Level list</h3>
                        <p>Review each level, its customer count, and the upgrade amount before editing or deleting.</p>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="gateway-badge gateway-badge--active">Active</span>
                        <span class="gateway-badge gateway-badge--inactive">Inactive</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="customer-levels-datatable" class="table table-striped dataex-html5-selectors gateway-table align-middle">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Name</th>
                                    <th>Order</th>
                                    <th>Customers Count</th>
                                    <th>Upgrade Amount</th>
                                    @if(hasAccess('customerlevel.edit') || hasAccess('customerlevel.destroy'))
                                        <th class="text-end">Action</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($levels as $level)
                                    @php
                                        $serialNumber = $loop->iteration;
                                        $statusClass = (int) $level->status === 1 ? 'gateway-badge--active' : 'gateway-badge--inactive';
                                    @endphp
                                    <tr>
                                        <td>{{ $serialNumber }}</td>
                                        <td>
                                            <div class="gateway-name">{{ $level->name }}</div>
                                            <div class="gateway-helper d-flex flex-wrap gap-2 mt-1">
                                                <span class="gateway-badge {{ $statusClass }}">{{ (int) $level->status === 1 ? 'Active' : 'Inactive' }}</span>
                                                @if($level->make_api_level === 'yes')
                                                    <span class="gateway-badge gateway-badge--warning">API</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>{{ $level->order }}</td>
                                        <td>{{ number_format($level->customers_count) }}</td>
                                        <td>{!! getSettings()['currency'] !!}{{ number_format($level->upgrade_amount) }}</td>
                                        @if(hasAccess('customerlevel.edit') || hasAccess('customerlevel.destroy'))
                                            <td class="text-end">
                                                <div class="gateway-row-actions justify-content-end">
                                                    @if(hasAccess('customerlevel.edit'))
                                                        <a href="{{ route('customerlevel.edit', $level->id) }}" class="gateway-action">View / Edit</a>
                                                    @endif
                                                    @if(hasAccess('customerlevel.destroy') && $level->customers_count < 1)
                                                        <form
                                                            action="{{ route('customerlevel.destroy', $level->id) }}"
                                                            method="POST"
                                                            onsubmit="return confirm('Are you sure you want to delete forever?');"
                                                        >
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="gateway-action gateway-action--danger">Delete</button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ (hasAccess('customerlevel.edit') || hasAccess('customerlevel.destroy')) ? 6 : 5 }}">
                                            <div class="alert alert-light border mb-0">No customer levels available.</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/datatables.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('app-assets/js/scripts/datatables/datatable.js') }}"></script>
@endsection
