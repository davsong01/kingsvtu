@extends('sneat.layouts.app')

@section('title', 'Admins')

@section('page-css')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css') }}">
@endsection

@section('content')
    @php
        $summary = [
            ['label' => 'Total Admins', 'value' => number_format($summary['totalAdmins'] ?? $admins->count()), 'icon' => 'bx-user', 'tone' => 'blue'],
            ['label' => 'Active Admins', 'value' => number_format($summary['activeAdmins'] ?? 0), 'icon' => 'bx-check-circle', 'tone' => 'green'],
            ['label' => 'Suspended Admins', 'value' => number_format($summary['suspendedAdmins'] ?? 0), 'icon' => 'bx-block', 'tone' => 'amber'],
            ['label' => 'With Roles', 'value' => number_format($summary['withRoles'] ?? 0), 'icon' => 'bx-shield-quarter', 'tone' => 'indigo'],
        ];
    @endphp

    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="admin-page-hero mb-4">
                <div>
                    <span class="admin-page-hero__kicker">User management</span>
                    <h1>Admins</h1>
                    <p>Manage administrator accounts, roles, and account status from a cleaner admin table.</p>
                </div>
                <a href="{{ route('newAdmin') }}" class="btn btn-admin-submit">Add admin</a>
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
                        <h3>Admin list</h3>
                        <p>Open any admin account and adjust access from the same workspace.</p>
                    </div>
                    <span class="gateway-badge gateway-badge--active">{{ $admins->count() }} records</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="admins-datatable" class="table table-striped dataex-html5-selectors gateway-table align-middle">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    @if(hasAccess('viewAdmin'))
                                        <th class="text-end">Action</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($admins as $admin)
                                    @php
                                        $serialNumber = $loop->iteration;
                                        $user = $admin->user;
                                        $status = strtolower((string) ($user->status ?? 'inactive'));
                                        $statusClass = $status === 'active' ? 'gateway-badge--active' : 'gateway-badge--inactive';
                                    @endphp
                                    <tr>
                                        <td>{{ $serialNumber }}</td>
                                        <td>
                                            <div class="gateway-name">{{ trim(($user->firstname ?? '') . ' ' . ($user->lastname ?? '')) ?: 'Unnamed admin' }}</div>
                                            <div class="gateway-helper">{{ $admin->permissions ? 'Has roles assigned' : 'No roles assigned' }}</div>
                                        </td>
                                        <td>{{ $user->email ?? 'No email' }}</td>
                                        <td><span class="gateway-badge {{ $statusClass }}">{{ ucfirst($status) }}</span></td>
                                        <td>{{ optional($admin->created_at)->toDateString() }}</td>
                                        @if(hasAccess('viewAdmin'))
                                            <td class="text-end">
                                                <a href="{{ route('viewAdmin', ['admin' => $user->id]) }}" class="gateway-action">View / Edit</a>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ hasAccess('viewAdmin') ? 6 : 5 }}">
                                            <div class="alert alert-light border mb-0">No admins available.</div>
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
