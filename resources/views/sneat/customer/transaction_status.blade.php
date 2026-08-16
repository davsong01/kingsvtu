@extends('sneat.layouts.app')

@section('title', 'Transaction Status')

@section('page-css')
    <style>
        .transaction-shell {
            max-width: 1100px;
            margin-inline: auto;
        }

        .transaction-hero {
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(15, 23, 42, .08);
            border-radius: 1.2rem;
            background:
                radial-gradient(circle at top right, rgba(31, 168, 104, .14), transparent 36%),
                linear-gradient(180deg, rgba(255, 255, 255, .98), rgba(248, 250, 252, .94));
            box-shadow: 0 1.25rem 2.6rem rgba(15, 23, 42, .08);
        }

        .transaction-hero__meta {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
        }

        .transaction-thumb {
            width: 72px;
            height: 72px;
            flex: 0 0 auto;
            border-radius: 1rem;
            border: 1px solid rgba(15, 23, 42, .08);
            background: #fff;
            overflow: hidden;
            display: grid;
            place-items: center;
        }

        .transaction-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .transaction-stat-card {
            height: 100%;
            border: 1px solid rgba(15, 23, 42, .08);
            border-radius: 1rem;
            background: linear-gradient(180deg, #fff, #f8fafc);
            box-shadow: 0 .75rem 1.8rem rgba(15, 23, 42, .05);
        }

        .transaction-stat-label {
            color: #64748b;
            font-size: .78rem;
            font-weight: 600;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .transaction-stat-value {
            color: #0f1729;
            font-size: 1.05rem;
            font-weight: 700;
            word-break: break-word;
        }

        .transaction-copy-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
        }

        .transaction-detail-card {
            border: 1px solid rgba(15, 23, 42, .08);
            border-radius: 1rem;
            box-shadow: 0 .75rem 1.8rem rgba(15, 23, 42, .04);
        }

        .transaction-table {
            margin-bottom: 0;
        }

        .transaction-table tr td {
            padding: .85rem 0;
            border-top: 1px solid rgba(15, 23, 42, .06);
        }

        .transaction-table tr:first-child td {
            border-top: 0;
        }

        .transaction-table td:first-child {
            color: #64748b;
            font-size: .85rem;
            font-weight: 600;
            width: 40%;
        }

        .transaction-table td:last-child {
            color: #0f1729;
            font-weight: 600;
        }

        .transaction-badge {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .4rem .75rem;
            border-radius: 999px;
            font-size: .8rem;
            font-weight: 700;
        }

        .transaction-badge--success {
            color: #067647;
            background: #ecfdf3;
        }

        .transaction-badge--warning {
            color: #b54708;
            background: #fffaeb;
        }

        .transaction-badge--danger {
            color: #b42318;
            background: #fef3f2;
        }
    </style>
@endsection

@section('content')
    @php
        $settings = getSettings();
        $currency = $settings->currency ?? '₦';
        $status = strtolower((string) ($transaction?->status ?? 'pending'));
        $statusLabel = str($status)->replace('-', ' ')->title();
        $statusClass = in_array($status, ['success', 'delivered', 'completed', 'approved'], true)
            ? 'transaction-badge--success'
            : (in_array($status, ['failed', 'declined'], true) ? 'transaction-badge--danger' : 'transaction-badge--warning');
        $systemReasons = ['LEVEL-UPGRADE', 'WALLET-FUNDING', 'ADMIN-DEBIT', 'ADMIN-CREDIT'];
        $reason = (string) ($transaction?->reason ?? '');
        $isSpecial = in_array($reason, $systemReasons, true);
        $serviceName = $isSpecial
            ? str($reason)->replace('-', ' ')->title()
            : ($transaction?->product?->display_name ?? $transaction?->product?->name ?? 'Transaction');
        $variationName = $transaction?->variation?->system_name;
        $serviceImage = $isSpecial ? asset('site/upgrade.jpg') : asset($transaction?->product?->image ?: 'site/upgrade.jpg');
        $canDownloadReceipt = !in_array($reason, ['LEVEL-UPGRADE', 'WALLET-FUNDING'], true) && $status !== 'failed';
    @endphp

    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y transaction-shell">
            @include('sneat.layouts.alerts')

            <div class="transaction-hero mb-4">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                        <div class="transaction-hero__meta">
                            <div class="transaction-thumb">
                                <img src="{{ $serviceImage }}" alt="{{ $serviceName }}">
                            </div>
                            <div>
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                    <span class="transaction-badge {{ $statusClass }}">
                                        <i class="bx bx-check-circle"></i>
                                        {{ $statusLabel }}
                                    </span>
                                    <span class="badge bg-label-secondary">{{ $transaction?->payment_method ? str($transaction->payment_method)->replace('-', ' ')->title() : 'Payment details' }}</span>
                                </div>
                                <h1 class="h3 fw-bold mb-2">{{ $serviceName }}</h1>
                                <p class="text-secondary mb-0">
                                    {{ $transaction?->descr ?: 'Transaction details and status summary.' }}
                                </p>
                            </div>
                        </div>

                        <div class="text-lg-end">
                            <div class="display-6 fw-bold mb-1">{{ $currency }}{{ number_format((float) ($transaction?->total_amount ?? 0), 2) }}</div>
                            <div class="text-secondary small">Total amount</div>
                            <div class="d-flex flex-wrap gap-2 justify-content-lg-end mt-3">
                                @if($canDownloadReceipt)
                                    <a href="{{ route('transaction.receipt.download', $transaction->id) }}" target="_blank" class="btn btn-primary btn-sm">
                                        <i class="bx bx-download me-1"></i> Download Receipt
                                    </a>
                                @endif
                                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="bx bx-arrow-back me-1"></i> Back
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="transaction-stat-card card h-100">
                        <div class="card-body p-4">
                            <div class="transaction-stat-label mb-2">Transaction ID</div>
                            <div class="transaction-copy-row">
                                <div class="transaction-stat-value">{{ $transaction?->transaction_id ?? 'N/A' }}</div>
                                <button type="button" class="btn btn-sm btn-outline-primary copy-text-btn" data-copy="{{ $transaction?->transaction_id ?? '' }}">Copy</button>
                            </div>

                            <div class="transaction-stat-label mt-4 mb-2">Reference ID</div>
                            <div class="transaction-copy-row">
                                <div class="transaction-stat-value">{{ $transaction?->reference_id ?? 'N/A' }}</div>
                                <button type="button" class="btn btn-sm btn-outline-primary copy-text-btn" data-copy="{{ $transaction?->reference_id ?? '' }}">Copy</button>
                            </div>

                            <div class="transaction-stat-label mt-4 mb-2">Request ID</div>
                            <div class="transaction-copy-row">
                                <div class="transaction-stat-value">{{ $transaction?->request_id ?? 'N/A' }}</div>
                                <button type="button" class="btn btn-sm btn-outline-primary copy-text-btn" data-copy="{{ $transaction?->request_id ?? '' }}">Copy</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="transaction-stat-card card h-100">
                        <div class="card-body p-4">
                            <div class="transaction-stat-label mb-2">Recipient / biller</div>
                            <div class="transaction-stat-value">{{ $transaction?->unique_element ?: 'Not provided' }}</div>

                            <div class="transaction-stat-label mt-4 mb-2">Customer phone</div>
                            <div class="transaction-stat-value">{{ $transaction?->customer_phone ?: 'Not provided' }}</div>

                            <div class="transaction-stat-label mt-4 mb-2">Customer email</div>
                            <div class="transaction-stat-value">{{ $transaction?->customer_email ?: 'Not provided' }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="transaction-stat-card card h-100">
                        <div class="card-body p-4">
                            <div class="transaction-stat-label mb-2">Initial balance</div>
                            <div class="transaction-stat-value">{{ $currency }}{{ number_format((float) ($transaction?->balance_before ?? 0), 2) }}</div>

                            <div class="transaction-stat-label mt-4 mb-2">Final balance</div>
                            <div class="transaction-stat-value">{{ $currency }}{{ number_format((float) ($transaction?->balance_after ?? 0), 2) }}</div>

                            <div class="transaction-stat-label mt-4 mb-2">Amount</div>
                            <div class="transaction-stat-value">{{ $currency }}{{ number_format((float) ($transaction?->amount ?? 0), 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card transaction-detail-card mt-4">
                <div class="card-header">
                    <h3 class="mb-1">Transaction Details</h3>
                    <p class="mb-0 text-secondary">Request metadata and line-item breakdown.</p>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <div class="p-3 rounded-4 bg-body-tertiary h-100">
                                <table class="table transaction-table mb-0">
                                    <tbody>
                                        <tr>
                                            <td>Product</td>
                                            <td>{{ $serviceName }}</td>
                                        </tr>
                                        <tr>
                                            <td>Variation</td>
                                            <td>{{ $variationName ?: 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td>Payment method</td>
                                            <td>{{ $transaction?->payment_method ? str($transaction->payment_method)->replace('-', ' ')->title() : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td>Created at</td>
                                            <td>{{ optional($transaction?->created_at)->format('M j, Y g:i A') }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="p-3 rounded-4 bg-body-tertiary h-100">
                                <table class="table transaction-table mb-0">
                                    <tbody>
                                        <tr>
                                            <td>Unit price</td>
                                            <td>{{ $currency }}{{ number_format((float) ($transaction?->unit_price ?? 0), 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Quantity</td>
                                            <td>{{ number_format((int) ($transaction?->quantity ?? 1)) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Discount</td>
                                            <td>{{ $currency }}{{ number_format((float) ($transaction?->discount ?? 0), 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Provider charge</td>
                                            <td>{{ $currency }}{{ number_format((float) ($transaction?->provider_charge ?? 0), 2) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    @if(filled($transaction?->descr) || filled($transaction?->extras))
                        <div class="row g-4 mt-0">
                            <div class="col-12">
                                <div class="p-3 rounded-4 border bg-white mt-4">
                                    <h4 class="h6 fw-bold mb-3">Notes</h4>
                                    @if(filled($transaction?->descr))
                                        <p class="mb-2 text-secondary"><strong class="text-body">Description:</strong> {{ $transaction->descr }}</p>
                                    @endif
                                    @if(filled($transaction?->extras))
                                        <p class="mb-0 text-secondary"><strong class="text-body">Extras:</strong> {{ $transaction->extras }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        document.querySelectorAll('.copy-text-btn').forEach((button) => {
            button.addEventListener('click', async () => {
                const value = button.dataset.copy || '';

                if (!value) {
                    return;
                }

                try {
                    await navigator.clipboard.writeText(value);
                    const original = button.textContent;
                    button.textContent = 'Copied';
                    setTimeout(() => button.textContent = original, 1500);
                } catch (error) {
                    window.prompt('Copy value', value);
                }
            });
        });
    </script>
@endsection
