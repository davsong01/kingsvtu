@extends('sneat.layouts.app')

@section('title', 'Blacklisted Customers')

@section('content')
    @php
        $summary = [
            ['label' => 'Total Entries', 'value' => number_format($totalBlacklist), 'icon' => 'bx-shield', 'tone' => 'blue'],
            ['label' => 'Email Entries', 'value' => number_format($emailBlacklist), 'icon' => 'bx-envelope', 'tone' => 'green'],
            ['label' => 'Phone Entries', 'value' => number_format($phoneBlacklist), 'icon' => 'bx-phone', 'tone' => 'amber'],
        ];
    @endphp

    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="admin-page-hero mb-4">
                <div>
                    <span class="admin-page-hero__kicker">Customer controls</span>
                    <h1>Blacklisted Customers</h1>
                    <p>Manage blocked emails and phone numbers from a cleaner, modern admin view.</p>
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
                        <h3>Blacklist entries</h3>
                        <p>Review blocked emails and phone numbers or add new items when needed.</p>
                    </div>
                    <a href="{{ route('customer-blacklist.create') }}" class="btn btn-admin-submit">Add to blacklist</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table gateway-table align-middle">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Value</th>
                                    <th>Type</th>
                                    <th>Date</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customers as $customer)
                                    @php
                                        $serialNumber = $customers->firstItem() + $loop->index;
                                    @endphp
                                    <tr>
                                        <td>{{ $serialNumber }}</td>
                                        <td>
                                            <div class="gateway-name">{{ $customer->value }}</div>
                                            <div class="gateway-helper">Blacklist ID: {{ $customer->id }}</div>
                                        </td>
                                        <td>{{ ucfirst($customer->type ?? 'N/A') }}</td>
                                        <td>{{ optional($customer->created_at)->toDateString() }}</td>
                                        <td class="text-end">
                                            <div class="gateway-row-actions justify-content-end">
                                                <form action="{{ route('customer-blacklist.destroy', $customer->id) }}" method="POST" onsubmit="return confirm('Remove this item from the blacklist?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm">Remove</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">
                                            <div class="alert alert-light border mb-0">No blacklist entries available.</div>
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
