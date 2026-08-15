@extends('sneat.layouts.app')

@section('title', 'Customer Level Benefits')

@section('page-css')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css') }}">
@endsection

@section('content')
    @php
        $summary = [
            ['label' => 'Total Benefits', 'value' => number_format($benefits->count()), 'icon' => 'bx-gift', 'tone' => 'emerald'],
            ['label' => 'Customer Levels', 'value' => number_format($levels->count()), 'icon' => 'bx-trophy', 'tone' => 'blue'],
        ];
    @endphp

    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="admin-page-hero mb-4">
                <div>
                    <span class="admin-page-hero__kicker">Level configuration</span>
                    <h1>Customer Level Benefits</h1>
                    <p>Manage the upgrade benefits shown on the customer level page from a clean admin table.</p>
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

            <div class="gateway-card card">
                <div class="card-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div>
                        <h3>Benefit list</h3>
                        <p>Review active level benefits and edit them when the upgrade rules change.</p>
                    </div>
                    <a href="{{ route('levelbenefit.create') }}" class="btn btn-admin-submit">Add benefit</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="benefits-datatable" class="table table-striped dataex-html5-selectors gateway-table align-middle">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Title</th>
                                    <th>Levels</th>
                                    <th>Content</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($benefits as $benefit)
                                    @php
                                        $serialNumber = $loop->iteration;
                                        $selectedLevels = $benefit->customer_levels ?? [];
                                        $selectedLevels = is_array($selectedLevels) ? $selectedLevels : (json_decode($selectedLevels, true) ?? []);
                                        $selectedLevelNames = $levels->whereIn('id', $selectedLevels)->pluck('name')->values();
                                    @endphp
                                    <tr>
                                        <td>{{ $serialNumber }}</td>
                                        <td>
                                            <div class="gateway-name">{{ $benefit->title }}</div>
                                            <div class="gateway-helper">Benefit ID: {{ $benefit->id }}</div>
                                        </td>
                                        <td>
                                            <div class="benefit-chip-list">
                                                @forelse($selectedLevelNames as $levelName)
                                                    <span class="benefit-chip">{{ $levelName }}</span>
                                                @empty
                                                    <span class="gateway-badge gateway-badge--inactive">No levels</span>
                                                @endforelse
                                            </div>
                                        </td>
                                        <td>
                                            <div class="gateway-helper">{{ \Illuminate\Support\Str::limit(strip_tags($benefit->content), 120) }}</div>
                                        </td>
                                        <td class="text-end">
                                            <div class="gateway-row-actions justify-content-end">
                                                <a href="{{ route('levelbenefit.edit', $benefit->id) }}" class="gateway-action">View / Edit</a>
                                                <form action="{{ route('levelbenefit.destroy', $benefit->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete forever?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="gateway-action gateway-action--danger">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">
                                            <div class="alert alert-light border mb-0">No customer level benefits available.</div>
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
