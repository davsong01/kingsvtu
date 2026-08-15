@extends('sneat.layouts.app')

@section('title', 'Permissions')

@section('page-css')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css') }}">
@endsection

@section('content')
    @php
        $summary = [
            ['label' => 'Total Permissions', 'value' => number_format($summary['totalPermissions'] ?? $permissions->count()), 'icon' => 'bx-lock-alt', 'tone' => 'blue'],
            ['label' => 'Menus', 'value' => number_format($summary['menus'] ?? 0), 'icon' => 'bx-menu', 'tone' => 'emerald'],
            ['label' => 'Links', 'value' => number_format($summary['links'] ?? 0), 'icon' => 'bx-link', 'tone' => 'indigo'],
            ['label' => 'Active', 'value' => number_format($summary['active'] ?? 0), 'icon' => 'bx-check-circle', 'tone' => 'green'],
        ];
    @endphp

    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="admin-page-hero mb-4">
                <div>
                    <span class="admin-page-hero__kicker">User management</span>
                    <h1>Permissions</h1>
                    <p>Manage individual permissions that power menu access and fine-grained actions.</p>
                </div>
                <a href="{{ route('permission.create') }}" class="btn btn-admin-submit">Add permission</a>
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
                <div class="card-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div>
                        <h3>Permission list</h3>
                        <p>Review each permission and update it when the system route map changes.</p>
                    </div>
                    <span class="gateway-badge gateway-badge--active">{{ $permissions->count() }} records</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="permissions-datatable" class="table table-striped dataex-html5-selectors gateway-table align-middle">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Name</th>
                                    <th>Route</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($permissions as $permission)
                                    @php
                                        $serialNumber = $loop->iteration;
                                        $statusClass = $permission->status === 'active' ? 'gateway-badge--active' : 'gateway-badge--inactive';
                                        $typeClass = $permission->type === 'menu' ? 'gateway-badge--blue' : 'gateway-badge--warning';
                                    @endphp
                                    <tr>
                                        <td>{{ $serialNumber }}</td>
                                        <td>
                                            <div class="gateway-name">{{ $permission->name }}</div>
                                            <div class="gateway-helper">Permission ID: {{ $permission->id }}</div>
                                        </td>
                                        <td class="gateway-url">{{ $permission->route }}</td>
                                        <td><span class="gateway-badge {{ $typeClass }}">{{ ucfirst($permission->type) }}</span></td>
                                        <td><span class="gateway-badge {{ $statusClass }}">{{ ucfirst($permission->status) }}</span></td>
                                        <td class="text-end">
                                            <div class="gateway-row-actions justify-content-end">
                                                @if(hasAccess('permission.edit'))
                                                    <a href="{{ route('permission.edit', $permission->id) }}" class="gateway-action">View / Edit</a>
                                                @endif
                                                @if(hasAccess('permission.destroy'))
                                                    <form action="{{ route('permission.destroy', $permission->id) }}" method="POST" onsubmit="return confirm('Delete this permission?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="gateway-action gateway-action--danger">Delete</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6">
                                            <div class="alert alert-light border mb-0">No permissions available.</div>
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
