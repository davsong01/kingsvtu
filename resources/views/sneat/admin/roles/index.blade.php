@extends('sneat.layouts.app')

@section('title', 'Roles')

@section('page-css')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css') }}">
@endsection

@section('content')
    @php
        $summary = [
            ['label' => 'Total Roles', 'value' => number_format($summary['totalRoles'] ?? $roles->count()), 'icon' => 'bx-shield-quarter', 'tone' => 'blue'],
            ['label' => 'Active Roles', 'value' => number_format($summary['activeRoles'] ?? 0), 'icon' => 'bx-check-circle', 'tone' => 'green'],
            ['label' => 'Inactive Roles', 'value' => number_format($summary['inactiveRoles'] ?? 0), 'icon' => 'bx-block', 'tone' => 'amber'],
        ];
    @endphp

    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="admin-page-hero mb-4">
                <div>
                    <span class="admin-page-hero__kicker">User management</span>
                    <h1>Roles</h1>
                    <p>Manage role groups and the permissions they bundle together in a cleaner table.</p>
                </div>
                <a href="{{ route('role.create') }}" class="btn btn-admin-submit">Add role</a>
            </div>

            @include('sneat.layouts.alerts')

            <div class="row g-3 mb-4">
                @foreach($summary as $item)
                    <div class="col-md-4">
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
                <div class="card-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div>
                        <h3>Role list</h3>
                    </div>
                    <span class="gateway-badge gateway-badge--active">{{ $roles->count() }} records</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="roles-datatable" class="table table-striped dataex-html5-selectors gateway-table align-middle">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($roles as $role)
                                    @php
                                        $serialNumber = $loop->iteration;
                                        $permissionCount = collect(explode(',', (string) $role->permissions))->filter()->count();
                                        $statusClass = $role->status === 'active' ? 'gateway-badge--active' : 'gateway-badge--inactive';
                                    @endphp
                                    <tr>
                                        <td>{{ $serialNumber }}</td>
                                        <td>{{ $role->name }} <br> <small>{{ $permissionCount }} permissions </small></td>
                                        <td><span class="gateway-badge {{ $statusClass }}">{{ ucfirst($role->status) }}</span></td>
                                        <td class="text-end">
                                            <a href="{{ route('role.edit', $role->id) }}" class="gateway-action">View / Edit</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">
                                            <div class="alert alert-light border mb-0">No roles available.</div>
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
