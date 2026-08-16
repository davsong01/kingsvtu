@php
    $settings = getSettings();
    $currency = $settings?->currency ?? '₦';
    $appName = $settings?->site_name ?? config('app.name', 'KingsVTU');
    $generatedAt = now();
    $issuedAt = \Carbon\Carbon::parse($transaction->created_at ?? now());
    $reason = data_get($transaction, 'reason');
    $systemReasons = ['LEVEL-UPGRADE', 'WALLET-FUNDING', 'ADMIN-DEBIT', 'ADMIN-CREDIT'];
    $isSpecial = in_array($reason, $systemReasons, true);
    $serviceName = $isSpecial
        ? ucfirst(str_replace('-', ' ', (string) $reason))
        : (data_get($transaction, 'product.display_name') ?? data_get($transaction, 'product.name') ?? 'Service');
    $variationName = data_get($transaction, 'variation.system_name');
    $status = strtolower((string) data_get($transaction, 'status', 'pending'));
    $statusLabel = str($status)->replace('-', ' ')->title();
    $statusStyles = match ($status) {
        'failed', 'declined' => ['color' => '#b42318', 'background' => '#fef3f2', 'border' => '#fecdca'],
        'delivered', 'successful', 'success', 'completed', 'approved' => ['color' => '#067647', 'background' => '#ecfdf3', 'border' => '#abefc6'],
        'pending', 'initiated', 'processing' => ['color' => '#b54708', 'background' => '#fffaeb', 'border' => '#fedf89'],
        default => ['color' => '#344054', 'background' => '#f2f4f7', 'border' => '#d0d5dd'],
    };

    $embedPublicImage = static function (?string $relativePath, int $maxWidth, int $maxHeight): ?string {
        if (!$relativePath || str_starts_with($relativePath, 'http')) {
            return null;
        }

        $absolutePath = public_path(ltrim($relativePath, '/'));

        if (!is_file($absolutePath) || !is_readable($absolutePath)) {
            return null;
        }

        try {
            $image = \Image::make($absolutePath);
            $image->resize($maxWidth, $maxHeight, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            return 'data:image/png;base64,' . base64_encode((string) $image->encode('png'));
        } catch (\Throwable $exception) {
            return null;
        }
    };

    $logoDataUri = $embedPublicImage($settings?->logo ?? null, 900, 180);
    $serviceLogoPath = $isSpecial ? 'site/upgrade.jpg' : (data_get($transaction, 'product.image') ?: 'site/upgrade.jpg');
    $serviceLogoDataUri = $embedPublicImage($serviceLogoPath, 180, 180);
    $reference = data_get($transaction, 'reference_id') ?: data_get($transaction, 'transaction_id');
    $extraInfo = json_decode((string) data_get($transaction, 'extra_info'), true) ?: [];
    $paymentMethod = filled(data_get($transaction, 'payment_method'))
        ? str(data_get($transaction, 'payment_method'))->replace('-', ' ')->title()
        : 'Not available';
    $statusClass = in_array($status, ['success', 'delivered', 'completed', 'approved'], true) ? 'status--success' : 'status--danger';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Receipt {{ data_get($transaction, 'transaction_id') }}</title>
    <style>
        @page {
            margin: 18px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #eef2f6;
            color: #344054;
            font-family: "DejaVu Sans", sans-serif;
            font-size: 10px;
            line-height: 1.35;
        }

        .watermark {
            position: fixed;
            z-index: 2;
            top: 43%;
            left: 5%;
            width: 90%;
            color: #101828;
            font-size: 68px;
            font-weight: bold;
            letter-spacing: 5px;
            opacity: .042;
            text-align: center;
            text-transform: uppercase;
            transform: rotate(-28deg);
        }

        .receipt {
            position: relative;
            z-index: 1;
            width: 100%;
            overflow: hidden;
            border: 1px solid #dfe5ec;
            border-radius: 12px;
            background: #ffffff;
        }

        .accent {
            height: 7px;
            background: #16a36a;
        }

        .header {
            padding: 18px 22px 14px;
            border-bottom: 1px solid #eaecf0;
        }

        .header-table,
        .summary-table,
        .section-table,
        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .brand-cell {
            width: 58%;
            vertical-align: middle;
        }

        .receipt-meta-cell {
            width: 42%;
            vertical-align: middle;
            text-align: right;
        }

        .logo {
            max-width: 150px;
            max-height: 48px;
        }

        .brand-fallback {
            color: #101828;
            font-size: 22px;
            font-weight: bold;
        }

        .receipt-title {
            margin: 0 0 4px;
            color: #101828;
            font-size: 20px;
            font-weight: bold;
        }

        .receipt-reference {
            margin-top: 6px;
            color: #344054;
            font-family: "DejaVu Sans Mono", monospace;
            font-size: 9px;
            font-weight: bold;
        }

        .muted {
            color: #667085;
        }

        .summary {
            padding: 16px 22px;
            background: #f8fafc;
            border-bottom: 1px solid #eaecf0;
        }

        .service-cell {
            width: 58%;
            vertical-align: middle;
        }

        .service-table {
            width: 100%;
            border-collapse: collapse;
        }

        .service-logo-cell {
            width: 58px;
            padding-right: 10px;
            vertical-align: middle;
        }

        .service-copy-cell {
            vertical-align: middle;
        }

        .service-logo {
            display: block;
            width: 42px;
            height: 42px;
            border: 1px solid #e4e7ec;
            border-radius: 8px;
            object-fit: contain;
        }

        .service-logo-fallback {
            width: 42px;
            height: 42px;
            border: 1px solid #c7d7fe;
            border-radius: 8px;
            background: #eef4ff;
            color: #3538cd;
            font-size: 14px;
            font-weight: bold;
            line-height: 42px;
            text-align: center;
        }

        .amount-cell {
            width: 42%;
            vertical-align: middle;
            text-align: right;
        }

        .status {
            display: inline-block;
            margin-bottom: 9px;
            padding: 4px 9px;
            border: 1px solid {{ $statusStyles['border'] }};
            border-radius: 12px;
            background: {{ $statusStyles['background'] }};
            color: {{ $statusStyles['color'] }};
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status--success {
            color: #067647;
        }

        .status--danger {
            color: #b42318;
        }

        .service-name {
            color: #101828;
            font-size: 16px;
            font-weight: bold;
        }

        .amount-label {
            margin-bottom: 4px;
            color: #667085;
            font-size: 9px;
            font-weight: bold;
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        .amount {
            color: #101828;
            font-size: 22px;
            font-weight: bold;
        }

        .content {
            padding: 14px 22px 12px;
        }

        .notice {
            margin-bottom: 12px;
            padding: 10px 12px;
            border: 1px solid #eaecf0;
            border-radius: 12px;
            background: #f8fafc;
        }

        .section {
            margin-top: 12px;
        }

        .section-title {
            margin-bottom: 8px;
            color: #101828;
            font-size: 11px;
            font-weight: bold;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .detail-box {
            border: 1px solid #eaecf0;
            border-radius: 12px;
            overflow: hidden;
        }

        .section-table td {
            width: 50%;
            padding: 9px 11px;
            vertical-align: top;
            border-bottom: 1px solid #f2f4f7;
        }

        .section-table tr:last-child td {
            border-bottom: 0;
        }

        .detail-cell-left {
            border-right: 1px solid #f2f4f7;
        }

        .detail-label {
            display: block;
            margin-bottom: 2px;
            color: #667085;
            font-size: 8px;
            font-weight: bold;
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        .detail-value {
            display: block;
            color: #101828;
            font-size: 10px;
            font-weight: bold;
            word-break: break-word;
        }

        .payment-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #eaecf0;
            border-radius: 12px;
            overflow: hidden;
        }

        .payment-table tr td {
            padding: 8px 10px;
            border-bottom: 1px solid #f2f4f7;
            font-size: 10px;
        }

        .payment-table tr td:first-child {
            color: #667085;
            font-weight: bold;
        }

        .payment-table tr td:last-child {
            color: #101828;
            font-weight: bold;
            text-align: right;
        }

        .payment-table tr.total-row td {
            background: #f8fafc;
        }

        .footer {
            padding: 10px 22px 12px;
            border-top: 1px solid #eaecf0;
            background: #f8fafc;
            color: #667085;
            font-size: 8px;
        }

        .footer-right {
            text-align: right;
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
                        @if($logoDataUri)
                            <img class="logo" src="{{ $logoDataUri }}" alt="Logo">
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
                                    @if($serviceLogoDataUri)
                                        <img class="service-logo" src="{{ $serviceLogoDataUri }}" alt="Service logo">
                                    @else
                                        <div class="service-logo-fallback">{{ str($serviceName)->substr(0, 2)->upper() }}</div>
                                    @endif
                                </td>
                                <td class="service-copy-cell">
                                    <span class="status {{ $statusClass }}">{{ $statusLabel }}</span>
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
                                <span class="detail-value">{{ $paymentMethod }}</span>
                            </td>
                            <td>
                                <span class="detail-label">Recipient / biller</span>
                                <span class="detail-value">{{ data_get($transaction, 'unique_element') ?? 'Not provided' }}</span>
                            </td>
                        </tr>
                        <tr>
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
                                <tr>
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
            </div>
        </div>

        <div class="footer">
            <table class="footer-table">
                <tr>
                    <td>This receipt was generated electronically and requires no signature.</td>
                    <td class="footer-right">
                        {{ $settings?->official_email ?? config('app.name', 'KingsVTU') }}<br>
                        Generated {{ $generatedAt->format('M j, Y · g:i A') }}
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
