@php
    $settings = getSettings();
    $currency = $settings['currency'] ?? '₦';
    $appName = $settings['site_name'] ?? config('app.name', 'KingsVTU');
    $generatedAt = now();
    $issuedAt = \Carbon\Carbon::parse(data_get($transaction, 'created_at'));
    $reason = data_get($transaction, 'reason');
    $isSpecial = in_array($reason, ['LEVEL-UPGRADE', 'WALLET-FUNDING', 'ADMIN-DEBIT', 'ADMIN-CREDIT'], true);
    $serviceName = $isSpecial
        ? ucfirst(str_replace('-', ' ', (string) $reason))
        : (data_get($transaction, 'product.display_name') ?? data_get($transaction, 'product.name') ?? 'Service');
    $variationName = data_get($transaction, 'variation.system_name');
    $serviceLogo = !empty(data_get($transaction, 'product.image'))
        ? data_get($transaction, 'product.image')
        : 'site/upgrade.jpg';
    $dashboardLogo = !empty($settings['logo']) ? $settings['logo'] : null;
    $extraInfo = json_decode(data_get($transaction, 'extra_info'), true) ?: [];
    $reference = data_get($transaction, 'reference_id') ?: data_get($transaction, 'transaction_id');
    $status = ucfirst(data_get($transaction, 'status', ''));
    $statusClass = in_array(data_get($transaction, 'status'), ['success', 'delivered'], true) ? 'status--success' : 'status--danger';
@endphp
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>{{ data_get($transaction, 'product_name') }}</title>
        <style>
            @page {
                margin: 28px;
            }

            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
                font-family: Arial, Helvetica, sans-serif;
                color: #334155;
                background: #f8fafc;
            }

            .watermark {
                position: fixed;
                top: 42%;
                left: 50%;
                transform: translate(-50%, -50%) rotate(-25deg);
                font-size: 92px;
                font-weight: 800;
                letter-spacing: .08em;
                color: rgba(15, 23, 42, .04);
                white-space: nowrap;
                z-index: 0;
                pointer-events: none;
            }

            .receipt {
                position: relative;
                z-index: 1;
                max-width: 800px;
                margin: 0 auto;
                background: #fff;
                border: 1px solid #e2e8f0;
                border-radius: 22px;
                overflow: hidden;
                box-shadow: 0 18px 40px rgba(15, 23, 42, .08);
            }

            .accent {
                height: 10px;
                background: linear-gradient(90deg, #1fa868 0%, #17316a 100%);
            }

            .header,
            .content,
            .footer {
                padding-left: 28px;
                padding-right: 28px;
            }

            .header {
                padding-top: 28px;
                padding-bottom: 18px;
                border-bottom: 1px solid #e2e8f0;
                background: linear-gradient(180deg, rgba(31, 168, 104, .04), transparent);
            }

            .header-table,
            .summary-table,
            .footer-table,
            .section-table,
            .payment-table {
                width: 100%;
                border-collapse: collapse;
            }

            .brand-cell {
                width: 42%;
                vertical-align: top;
            }

            .receipt-meta-cell {
                text-align: right;
                vertical-align: top;
            }

            .logo {
                max-width: 160px;
                max-height: 52px;
                object-fit: contain;
                display: block;
            }

            .brand-fallback {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-width: 150px;
                min-height: 46px;
                padding: 10px 16px;
                border-radius: 14px;
                background: #0f1729;
                color: #fff;
                font-weight: 800;
                letter-spacing: .04em;
            }

            .receipt-title {
                font-size: 24px;
                font-weight: 800;
                color: #0f1729;
                margin-bottom: 4px;
            }

            .receipt-reference,
            .muted {
                color: #64748b;
                font-size: 12px;
            }

            .receipt-reference {
                margin-top: 4px;
                font-weight: 700;
                letter-spacing: .06em;
            }

            .summary {
                padding: 22px 28px 10px;
            }

            .summary-table {
                background: linear-gradient(135deg, rgba(31, 168, 104, .08), rgba(23, 49, 106, .06));
                border: 1px solid rgba(31, 168, 104, .16);
                border-radius: 18px;
                overflow: hidden;
            }

            .service-cell,
            .amount-cell {
                padding: 22px;
                vertical-align: middle;
            }

            .amount-cell {
                width: 32%;
                text-align: right;
                border-left: 1px solid rgba(148, 163, 184, .18);
                background: rgba(255, 255, 255, .72);
            }

            .service-table {
                width: 100%;
                border-collapse: collapse;
            }

            .service-logo-cell {
                width: 68px;
                vertical-align: top;
            }

            .service-logo {
                width: 56px;
                height: 56px;
                object-fit: cover;
                border-radius: 16px;
                border: 1px solid rgba(148, 163, 184, .2);
                background: #fff;
            }

            .service-logo-fallback {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 56px;
                height: 56px;
                border-radius: 16px;
                background: rgba(31, 168, 104, .12);
                color: #1fa868;
                font-weight: 800;
            }

            .status {
                display: inline-block;
                margin-bottom: 8px;
                padding: 4px 10px;
                border-radius: 999px;
                font-size: 10px;
                font-weight: 800;
                letter-spacing: .08em;
                text-transform: uppercase;
            }

            .status--success {
                color: #1fa868;
                background: rgba(31, 168, 104, .12);
            }

            .status--danger {
                color: #dc2626;
                background: rgba(220, 38, 38, .12);
            }

            .service-name {
                font-size: 22px;
                font-weight: 800;
                color: #0f1729;
                line-height: 1.2;
            }

            .amount-label {
                color: #64748b;
                font-size: 12px;
                text-transform: uppercase;
                letter-spacing: .08em;
                font-weight: 700;
                margin-bottom: 6px;
            }

            .amount {
                font-size: 28px;
                font-weight: 800;
                color: #17316a;
            }

            .content {
                padding-top: 8px;
                padding-bottom: 18px;
            }

            .notice {
                padding: 16px 18px;
                margin-bottom: 18px;
                border-radius: 16px;
                background: #f8fafc;
                border: 1px solid #e2e8f0;
                color: #334155;
            }

            .section {
                margin-top: 18px;
            }

            .section-title {
                font-size: 12px;
                font-weight: 800;
                color: #0f1729;
                letter-spacing: .1em;
                text-transform: uppercase;
                margin-bottom: 10px;
            }

            .detail-box,
            .balance-box {
                border: 1px solid #e2e8f0;
                border-radius: 18px;
                overflow: hidden;
                background: #fff;
            }

            .section-table td {
                width: 50%;
                padding: 14px 16px;
                border-bottom: 1px solid #eef2f7;
                vertical-align: top;
            }

            .detail-cell-left {
                border-right: 1px solid #eef2f7;
            }

            .detail-row-last td {
                border-bottom: 0;
            }

            .detail-label {
                display: block;
                color: #64748b;
                font-size: 11px;
                font-weight: 700;
                letter-spacing: .06em;
                text-transform: uppercase;
                margin-bottom: 4px;
            }

            .detail-value {
                display: block;
                color: #0f1729;
                font-size: 13px;
                font-weight: 700;
                word-break: break-word;
            }

            .payment-table {
                border: 1px solid #e2e8f0;
                border-radius: 18px;
                overflow: hidden;
            }

            .payment-table tr td {
                padding: 12px 16px;
                border-bottom: 1px solid #eef2f7;
                font-size: 13px;
            }

            .payment-table tr td:first-child {
                color: #64748b;
                font-weight: 700;
                width: 58%;
            }

            .payment-table tr td:last-child {
                text-align: right;
                color: #0f1729;
                font-weight: 800;
            }

            .payment-table tr.total-row td {
                background: #f8fafc;
                font-size: 14px;
            }

            .balance-box {
                margin-top: 14px;
                background: linear-gradient(180deg, #f8fafc, #fff);
            }

            .footer {
                padding-top: 14px;
                padding-bottom: 20px;
                border-top: 1px solid #e2e8f0;
                background: #f8fafc;
                color: #64748b;
                font-size: 10px;
            }

            .footer-right {
                text-align: right;
            }

            .footer-table td {
                vertical-align: top;
            }

            @media only screen and (max-width: 600px) {
                .brand-cell,
                .receipt-meta-cell,
                .service-cell,
                .amount-cell {
                    width: 100%;
                    display: block;
                    text-align: left;
                }

                .amount-cell {
                    border-left: 0;
                    border-top: 1px solid rgba(148, 163, 184, .18);
                }
            }
        </style>
    </head>
    <body>
        <div class="watermark">{{ $appName }}</div>
        <div class="receipt">
            <div class="accent"></div>

            <div class="header">
                <table class="header-table">
                    <tr>
                        <td class="brand-cell">
                            @if(!empty($dashboardLogo))
                                <img class="logo" src="{{ url('/').'/'.$dashboardLogo }}" alt="Logo">
                            @else
                                <div class="brand-fallback">{{ $appName }}</div>
                            @endif
                        </td>
                        <td class="receipt-meta-cell">
                            <div class="receipt-title">Transaction Receipt</div>
                            <div class="muted">Issued {{ $issuedAt->format('M j, Y · g:i A') }}</div>
                            <div class="receipt-reference">REF: {{ $reference }}</div>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="summary">
                <table class="summary-table">
                    <tr>
                        <td class="service-cell">
                            <table class="service-table">
                                <tr>
                                    <td class="service-logo-cell">
                                        <img class="service-logo" src="{{ url('/').'/'.$serviceLogo }}" alt="Service logo">
                                    </td>
                                    <td class="service-copy-cell">
                                        <span class="status {{ $statusClass }}">{{ $status }}</span>
                                        <div class="service-name">{{ $serviceName }}</div>
                                        @if(filled($variationName))
                                            <div class="muted">{{ $variationName }}</div>
                                        @elseif(filled(data_get($transaction, 'unique_element')))
                                            <div class="muted">{{ data_get($transaction, 'unique_element') }}</div>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td class="amount-cell">
                            <div class="amount-label">Total paid</div>
                            <div class="amount">{{ $currency }}{{ number_format((float) data_get($transaction, 'total_amount', 0), 2) }}</div>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="content">
                @if(filled(data_get($transaction, 'descr')) || filled(data_get($transaction, 'extras')))
                    <div class="notice">
                        @if(filled(data_get($transaction, 'descr')))
                            <strong>{{ ucfirst(data_get($transaction, 'descr')) }}</strong>
                        @endif
                        @if(filled(data_get($transaction, 'extras')))
                            <div>{{ ucfirst(data_get($transaction, 'extras')) }}</div>
                        @endif
                    </div>
                @endif

                <div class="section">
                    <div class="section-title">Transaction details</div>
                    <div class="detail-box">
                        <table class="section-table">
                            <tr>
                                <td class="detail-cell-left">
                                    <span class="detail-label">Transaction ID</span>
                                    <span class="detail-value">{{ data_get($transaction, 'transaction_id') }}</span>
                                </td>
                                <td>
                                    <span class="detail-label">Reference</span>
                                    <span class="detail-value">{{ $reference }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="detail-cell-left">
                                    <span class="detail-label">Payment method</span>
                                    <span class="detail-value">{{ filled(data_get($transaction, 'payment_method')) ? str(data_get($transaction, 'payment_method'))->replace('-', ' ')->title() : 'Not available' }}</span>
                                </td>
                                <td>
                                    <span class="detail-label">Recipient / biller</span>
                                    <span class="detail-value">{{ data_get($transaction, 'unique_element') ?? 'Not provided' }}</span>
                                </td>
                            </tr>
                            <tr class="detail-row-last">
                                <td class="detail-cell-left">
                                    <span class="detail-label">Customer phone</span>
                                    <span class="detail-value">{{ data_get($transaction, 'customer_phone') ?? 'Not provided' }}</span>
                                </td>
                                <td>
                                    <span class="detail-label">Customer email</span>
                                    <span class="detail-value">{{ data_get($transaction, 'customer_email') ?? 'Not provided' }}</span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if(count($extraInfo))
                    <div class="section">
                        <div class="section-title">Additional information</div>
                        <div class="detail-box">
                            <table class="section-table">
                                @foreach(array_chunk($extraInfo, 2, true) as $row)
                                    <tr class="{{ $loop->last ? 'detail-row-last' : '' }}">
                                        @foreach($row as $key => $value)
                                            <td class="{{ $loop->first ? 'detail-cell-left' : '' }}">
                                                <span class="detail-label">{{ str($key)->replace(['_', '-'], ' ')->title() }}</span>
                                                <span class="detail-value">{{ is_scalar($value) ? ucfirst((string) $value) : json_encode($value) }}</span>
                                            </td>
                                        @endforeach
                                        @if(count($row) === 1)
                                            <td></td>
                                        @endif
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                @endif

                <div class="section">
                    <div class="section-title">Payment summary</div>
                    <table class="payment-table">
                        <tr>
                            <td>Unit price</td>
                            <td>{{ $currency }}{{ number_format((float) data_get($transaction, 'unit_price', 0), 2) }}</td>
                        </tr>
                        <tr>
                            <td>Quantity</td>
                            <td>{{ number_format((int) data_get($transaction, 'quantity', 1)) }}</td>
                        </tr>
                        @if((float) data_get($transaction, 'provider_charge', 0) > 0)
                            <tr>
                                <td>Convenience fee</td>
                                <td>{{ $currency }}{{ number_format((float) data_get($transaction, 'provider_charge', 0), 2) }}</td>
                            </tr>
                        @endif
                        @if((float) data_get($transaction, 'discount', 0) > 0)
                            <tr>
                                <td>Discount</td>
                                <td>-{{ $currency }}{{ number_format((float) data_get($transaction, 'discount', 0), 2) }}</td>
                            </tr>
                        @endif
                        <tr class="total-row">
                            <td>Total paid</td>
                            <td>{{ $currency }}{{ number_format((float) data_get($transaction, 'total_amount', 0), 2) }}</td>
                        </tr>
                    </table>

                    <div class="balance-box">
                        <table class="section-table">
                            <tr>
                                <td class="detail-cell-left">
                                    <span class="detail-label">Wallet balance before</span>
                                    <span class="detail-value">{{ $currency }}{{ number_format((float) data_get($transaction, 'balance_before', 0), 2) }}</span>
                                </td>
                                <td>
                                    <span class="detail-label">Wallet balance after</span>
                                    <span class="detail-value">{{ $currency }}{{ number_format((float) data_get($transaction, 'balance_after', 0), 2) }}</span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="footer">
                <table class="footer-table">
                    <tr>
                        <td>This receipt was generated electronically and requires no signature.</td>
                        <td class="footer-right">
                            {{ $settings['official_email'] ?? config('app.name', 'KingsVTU') }}<br>
                            Generated {{ $generatedAt->format('M j, Y · g:i A') }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </body>
</html>
