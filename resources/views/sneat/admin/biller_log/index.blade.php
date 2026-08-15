@extends('sneat.layouts.app')

@section('title', 'Biller Logs')

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="admin-page-hero mb-4">
                <div>
                    <span class="admin-page-hero__kicker">Financials</span>
                    <h1>Biller Logs</h1>
                    <p>Review verified biller payloads in a cleaner, more readable table.</p>
                </div>
                <span class="gateway-badge gateway-badge--active">{{ $billers->count() }} logs</span>
            </div>

            @include('sneat.layouts.alerts')

            <div class="card modern-admin-card">
                <div class="card-header">
                    <h3>Verified billers</h3>
                    <p>Raw and refined payloads for each verification entry.</p>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table financial-table align-middle">
                            <thead>
                                <tr>
                                    <th>Biller</th>
                                    <th>Service ID</th>
                                    <th>Date Added</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($billers as $biller)
                                    @php
                                        $refinedData = json_decode($biller->refined_data, true) ?? [];
                                        $rawData = json_encode(json_decode($biller->raw_data, true), JSON_PRETTY_PRINT);
                                        $modalId = 'biller-log-' . $biller->id;
                                    @endphp
                                    <tr>
                                        <td>{{ $biller->billers_code }}</td>
                                        <td>{{ $biller->service_id }}</td>
                                        <td>{{ $biller->created_at }}</td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-2">
                                                <button type="button" class="gateway-action" data-bs-toggle="modal" data-bs-target="#{{ $modalId }}">
                                                    View Data
                                                </button>
                                                <form action="{{ route('billerlog.destroy', $biller->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete forever?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="gateway-action gateway-action--danger">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4"><div class="alert alert-light border mb-0">No biller logs found.</div></td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @foreach($billers as $biller)
                @php
                    $refinedData = json_decode($biller->refined_data, true) ?? [];
                    $rawData = json_encode(json_decode($biller->raw_data, true), JSON_PRETTY_PRINT);
                    $modalId = 'biller-log-' . $biller->id;
                @endphp
                <div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header">
                                <div>
                                    <h5 class="modal-title mb-1">Biller payload</h5>
                                    <div class="gateway-helper">{{ $biller->billers_code }} · Service ID {{ $biller->service_id }}</div>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row g-3">
                                    <div class="col-lg-6">
                                        <div class="modern-admin-card p-3 h-100">
                                            <div class="fw-semibold mb-2">Refined Data</div>
                                            @forelse($refinedData as $key => $value)
                                                <div class="mb-2">
                                                    <div class="gateway-helper text-uppercase fw-semibold">{{ $key }}</div>
                                                    <div>{{ is_array($value) ? json_encode($value) : $value }}</div>
                                                </div>
                                            @empty
                                                <div class="alert alert-light border mb-0">No refined data available.</div>
                                            @endforelse
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="modern-admin-card p-3 h-100">
                                            <div class="fw-semibold mb-2">Raw Data</div>
                                            <pre class="financial-code mb-0">{{ $rawData }}</pre>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
