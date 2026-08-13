@extends('sneat.layouts.app')

@section('title', 'Callback Analysis')

@section('content')
    @php
        $totalCalls = $calls->total();
        $analyzedCalls = $calls->where('status', 'analyzed')->count();
        $pendingCalls = $calls->where('status', 'pending')->count();
        $withTransaction = $calls->filter(fn ($call) => !empty($call->transaction))->count();
    @endphp

    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="admin-page-hero mb-4">
                <div>
                    <span class="admin-page-hero__kicker">Operations</span>
                    <h1>Callback Analysis</h1>
                    <p>Review raw reserved-account callbacks, rerun analysis, and jump into the related transaction when needed.</p>
                </div>
                <div class="d-flex flex-column align-items-stretch gap-3">
                    <div class="admin-page-badges">
                        <div class="admin-page-badge">
                            <span>Total callbacks</span>
                            <strong>{{ number_format($totalCalls) }}</strong>
                        </div>
                        <div class="admin-page-badge">
                            <span>Analyzed</span>
                            <strong>{{ number_format($analyzedCalls) }}</strong>
                        </div>
                        <div class="admin-page-badge">
                            <span>Pending</span>
                            <strong>{{ number_format($pendingCalls) }}</strong>
                        </div>
                        <div class="admin-page-badge">
                            <span>Linked transactions</span>
                            <strong>{{ number_format($withTransaction) }}</strong>
                        </div>
                    </div>

                    @if(!empty(getSettings()) && getSettings()->payment_gateway == 2)
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('callback-error-logs') }}" class="btn btn-admin-submit">Fetch Squad logs</a>
                        </div>
                    @endif
                </div>
            </div>

            @include('sneat.layouts.alerts')

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="admin-stat-card admin-stat-card--blue">
                        <div class="admin-stat-card__label">Total callbacks</div>
                        <div class="admin-stat-card__value">{{ number_format($totalCalls) }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="admin-stat-card admin-stat-card--emerald">
                        <div class="admin-stat-card__label">Analyzed</div>
                        <div class="admin-stat-card__value">{{ number_format($analyzedCalls) }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="admin-stat-card admin-stat-card--amber">
                        <div class="admin-stat-card__label">Pending</div>
                        <div class="admin-stat-card__value">{{ number_format($pendingCalls) }}</div>
                    </div>
                </div>
            </div>

            <div class="gateway-card card">
                <div class="card-header">
                    <div>
                        <h3>Raw callbacks</h3>
                        <p>Inspect callback metadata, review the raw JSON, and compare the re-queried response when analysis is pending.</p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table gateway-table align-middle" id="callback-table">
                            <thead>
                                <tr>
                                    <th>Details</th>
                                    <th>Raw</th>
                                    <th>Analysis</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($calls as $call)
                                    @php
                                        $raw = json_encode(json_decode($call->raw, true), JSON_PRETTY_PRINT);
                                        $analysis = json_encode(json_decode($call->raw_requery, true), JSON_PRETTY_PRINT);
                                        $statusClass = $call->status === 'analyzed' ? 'gateway-badge--active' : 'gateway-badge--inactive';
                                    @endphp
                                    <tr>
                                        <td class="callback-details">
                                            <div class="callback-record">
                                                <div class="callback-record__heading">{{ $call->gateway?->name ?? 'Unknown gateway' }}</div>
                                                <span class="gateway-badge {{ $statusClass }} mt-2">{{ ucfirst($call->status ?? 'pending') }}</span>
                                                <div class="callback-record__meta">
                                                    <span class="callback-record__label">Account</span>
                                                    <span class="callback-record__value">{{ $call->account_number }}</span>
                                                </div>
                                                <div class="callback-record__meta">
                                                    <span class="callback-record__label">Session</span>
                                                    <span class="callback-record__value">{{ $call->session_id }}</span>
                                                </div>
                                                <div class="callback-record__meta">
                                                    <span class="callback-record__label">Reference</span>
                                                    <span class="callback-record__value">{{ $call->transaction_reference }}</span>
                                                </div>
                                                <div class="callback-record__meta">
                                                    <span class="callback-record__label">Created</span>
                                                    <span class="callback-record__value">{{ date('M jS, Y g:i A', strtotime($call->created_at)) }}</span>
                                                </div>
                                                @if($call->status === 'analyzed')
                                                    <div class="callback-record__meta callback-record__meta--success">
                                                        <span class="callback-record__label">Analyzed</span>
                                                        <span class="callback-record__value">{{ date('M jS, Y g:i A', strtotime($call->updated_at)) }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            @if($call->status === 'analyzed')
                                                <div class="callback-record__actions">
                                                    @if($call->transaction)
                                                        <a class="gateway-action" href="{{ route('admin.single.transaction.view', $call->transaction->id) }}">View transaction</a>
                                                    @endif
                                                </div>
                                            @else
                                                <div class="callback-record__actions">
                                                    <a class="gateway-action" href="{{ route('callback.reset', $call->id) }}">Reset</a>
                                                    <a class="gateway-action" href="{{ route('admin.requery.callback', $call->transaction_reference) }}">Query</a>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="callback-section-title">Raw payload</div>
                                            <pre class="callback-json">{{ $raw }}</pre>
                                        </td>
                                        <td>
                                            <div class="callback-section-title">Re-query response</div>
                                            <pre class="callback-json">{{ $analysis }}</pre>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3">
                                            <div class="alert alert-light border mb-0">No callback logs found.</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        {{ $calls->links('pagination::bootstrap-5') }}
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
    <script>
        $('#callback-table').DataTable({
            ordering: false,
            paging: false,
            searching: false,
            info: false,
            dom: 'rt',
        });
    </script>
@endsection
