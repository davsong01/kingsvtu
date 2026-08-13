@extends('sneat.layouts.app')

@section('title', 'Payment Gateways')

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            @php
                $gatewayCount = $paymentgateway->count();
                $activeCount = $paymentgateway->where('status', 'active')->count();
                $inactiveCount = $paymentgateway->where('status', 'inactive')->count();
                $callbackUrl = url('/log-p-callback/' . optional($paymentgateway->first())->id);
            @endphp

            <div class="gateway-page-hero mb-4">
                <div>
                    <span class="gateway-page-hero__kicker">Payment infrastructure</span>
                    <h1>Payment Gateways</h1>
                    <p>Manage gateway credentials, callback endpoints, and reserved account tooling from a cleaner control panel.</p>
                </div>
                <div class="d-grid gap-3">
                    <div class="gateway-stat">
                        <div class="gateway-stat__label">Total gateways</div>
                        <div class="gateway-stat__value">{{ $gatewayCount }}</div>
                    </div>
                    <div class="gateway-stat">
                        <div class="gateway-stat__label">Active</div>
                        <div class="gateway-stat__value">{{ $activeCount }}</div>
                    </div>
                </div>
            </div>

            @include('sneat.layouts.alerts')

            <div class="gateway-card card">
                <div class="card-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
                    <div>
                        <h3>Gateway list</h3>
                        <p>View each provider and jump into the edit screen when needed.</p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table gateway-table align-middle">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Callback URL</th>
                                    <th>Status</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($paymentgateway as $gateway)
                                    <tr>
                                        <td>
                                            <div class="gateway-name">{{ $gateway->name }}</div>
                                            <div class="gateway-helper">{{ $gateway->slug }}</div>
                                        </td>
                                        <td>
                                            <div class="gateway-url">{{ url('/log-p-callback/' . $gateway->id) }}</div>
                                            <div class="gateway-row-actions mt-2">
                                                <button
                                                    type="button"
                                                    class="gateway-action gateway-copy-btn"
                                                    data-copy-text="{{ url('/log-p-callback/' . $gateway->id) }}"
                                                >
                                                    Copy callback URL
                                                </button>
                                            </div>
                                            @if($gateway->id == 2)
                                                <div class="mt-2">
                                                    <a class="gateway-action" href="{{ route('admin.generate.reserved.accounts') }}">Generate reserved accounts</a>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="gateway-badge {{ $gateway->status === 'active' ? 'gateway-badge--active' : 'gateway-badge--inactive' }}">
                                                {{ ucfirst($gateway->status ?? 'inactive') }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            @if(hasAccess('paymentgateway.edit'))
                                                <a href="{{ route('paymentgateway.edit', $gateway->id) }}" class="gateway-action">View / Edit</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        document.querySelectorAll('[data-copy-text]').forEach(function (button) {
            button.addEventListener('click', async function () {
                const text = this.getAttribute('data-copy-text');
                if (!text) {
                    return;
                }

                try {
                    await navigator.clipboard.writeText(text);
                    const original = this.textContent;
                    this.textContent = 'Copied';
                    setTimeout(() => {
                        this.textContent = original;
                    }, 1200);
                } catch (error) {
                    const textarea = document.createElement('textarea');
                    textarea.value = text;
                    textarea.style.position = 'fixed';
                    textarea.style.opacity = '0';
                    document.body.appendChild(textarea);
                    textarea.focus();
                    textarea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textarea);
                }
            });
        });
    </script>
@endsection
