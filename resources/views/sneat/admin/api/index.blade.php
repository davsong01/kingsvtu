@extends('sneat.layouts.app')

@section('title', 'API Providers')

@section('content')
    @php
        $currency = getSettings()->currency ?? '₦';
        $providerCount = $apis->count();
        $activeCount = $apis->where('status', 'active')->count();
        $averageScore = $availabilitySummary['average_score'] ?? null;
        $lastCheckedAt = $availabilitySummary['last_checked_at'] ?? null;
        $scoreLabel = $averageScore === null
            ? 'Not checked'
            : ($averageScore <= 20
                ? 'Critical'
                : ($averageScore <= 40
                    ? 'Unstable'
                    : ($averageScore <= 60
                        ? 'Average'
                        : ($averageScore <= 80 ? 'Stable' : 'Healthy'))));
    @endphp

    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="api-page-hero mb-4">
                <div>
                    <div class="api-page-hero__kicker">Catalogue</div>
                    <h1 class="mb-2">API Providers</h1>
                </div>

                <div class="api-page-hero__actions">
                    {{-- <div class="admin-summary-card">
                        <div class="admin-summary-card__label">Total providers</div>
                        <div class="admin-summary-card__value">{{ number_format($providerCount) }}</div>
                    </div>
                    <div class="admin-summary-card">
                        <div class="admin-summary-card__label">Active providers</div>
                        <div class="admin-summary-card__value">{{ number_format($activeCount) }}</div>
                    </div> --}}
                    <a href="{{ $monitorUrl }}" target="_blank" rel="noopener" class="btn btn-primary btn-sm api-monitor-btn">
                        <i class="bx bx-refresh me-25"></i>
                        Run health monitor
                    </a>
                </div>
            </div>

            @include('sneat.layouts.alerts')

            <div class="row g-3 mb-4">
                <div class="col-md-6 col-xl-3">
                    <div class="admin-stat-card admin-stat-card--blue">
                        <div class="admin-stat-card__icon">
                            <i class="bx bx-network-chart"></i>
                        </div>
                        <div class="admin-stat-card__label">Provider health</div>
                        <div class="admin-stat-card__value">{{ $averageScore !== null ? $averageScore . '%' : 'N/A' }}</div>
                        <div class="admin-stat-card__sub">{{ $scoreLabel }}</div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-3">
                    <div class="admin-stat-card admin-stat-card--emerald">
                        <div class="admin-stat-card__icon">
                            <i class="bx bx-shield-quarter"></i>
                        </div>
                        <div class="admin-stat-card__label">Healthy providers</div>
                        <div class="admin-stat-card__value">{{ number_format((int) $availabilitySummary['healthy_providers']) }}</div>
                        <div class="admin-stat-card__sub">{{ number_format((int) $availabilitySummary['checked_providers']) }} checked</div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-3">
                    <div class="admin-stat-card admin-stat-card--amber">
                        <div class="admin-stat-card__icon">
                            <i class="bx bx-check-circle"></i>
                        </div>
                        <div class="admin-stat-card__label">Successful checks</div>
                        <div class="admin-stat-card__value">{{ number_format((int) $availabilitySummary['successful_transactions']) }}</div>
                        <div class="admin-stat-card__sub">{{ number_format((int) $availabilitySummary['availability_check_transactions_count']) }} sampled</div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-3">
                    <div class="admin-stat-card admin-stat-card--slate">
                        <div class="admin-stat-card__icon">
                            <i class="bx bx-time-five"></i>
                        </div>
                        <div class="admin-stat-card__label">Last checked</div>
                        <div class="admin-stat-card__value">{{ $lastCheckedAt ? $lastCheckedAt->format('M j, Y') : 'Never' }}</div>
                        <div class="admin-stat-card__sub">{{ $lastCheckedAt ? $lastCheckedAt->format('g:i A') : 'No availability data yet' }}</div>
                    </div>
                </div>
            </div>

            <div class="admin-provider-card mb-4">
                <div class="sneat-card__body p-4 p-lg-4">
                    <div class="d-flex flex-column flex-md-row align-items-md-start justify-content-between gap-3 mb-4">
                        <div class="d-flex align-items-start gap-3">
                            <div class="admin-feature-card__icon admin-feature-card__icon--blue">
                                <i class="bx bx-server"></i>
                            </div>
                            <div>
                                <div class="text-muted small fw-semibold text-uppercase mb-1">Provider summary</div>
                                <h3 class="h6 fw-semibold mb-1">API Providers</h3>
                                <p class="text-secondary small mb-0">Each provider card includes balance refresh, callback copy, and health state at a glance.</p>
                            </div>
                        </div>

                        <span class="admin-provider-row__pill align-self-md-start">{{ number_format($providerCount) }}</span>
                    </div>

                    <div class="api-provider-grid">
                        @forelse($apis as $api)
                            @php
                                $providerCurrency = $api->slug === 'paystack' ? 'NGN' : $currency;
                                $callbackUrl = route('log.provider.callback', $api->id);
                                $balanceValue = $api->balance !== null ? $providerCurrency . ' ' . number_format((float) $api->balance, 2) : 'No cached balance yet';
                            @endphp

                            <div class="api-provider-card card mb-0">
                                <div class="card-body">
                                    <div class="api-provider-card__header">
                                        <div>
                                            <div class="api-provider-card__name">{{ $api->name }}</div>
                                            @if($api->slug)
                                                <div class="api-provider-card__slug">{{ $api->slug }}</div>
                                            @endif
                                        </div>

                                        <div class="d-flex align-items-center gap-2 flex-wrap justify-content-end">
                                            <span class="api-provider-card__status {{ $api->status === 'active' ? 'is-active' : 'is-inactive' }}">
                                                <i class="bx {{ $api->status === 'active' ? 'bx-check-circle' : 'bx-x-circle' }}"></i>
                                                {{ ucfirst($api->status ?? 'inactive') }}
                                            </span>
                                            <span class="api-provider-card__status is-neutral">
                                                <i class="bx bx-package"></i>
                                                {{ number_format((int) ($api->products_count ?? 0)) }} products
                                            </span>
                                        </div>
                                    </div>

                                    <div class="api-provider-health">
                                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                                            <div class="text-uppercase small fw-semibold text-secondary">Availability monitor</div>

                                            @if($api->availability_checked_at && $api->availability_status_class)
                                                <span class="api-health-badge api-health-badge--{{ $api->availability_status_class }}">
                                                    <i class="bx bx-pulse"></i>
                                                    {{ $api->availability_status_label }}
                                                </span>
                                            @endif
                                        </div>

                                        <div class="api-provider-health__grid">
                                            <div class="api-provider-health__item">
                                                <span class="api-provider-health__label">Availability score</span>
                                                <strong class="api-provider-health__value">{{ $api->availability_score !== null ? $api->availability_score . '%' : 'N/A' }}</strong>
                                            </div>
                                            <div class="api-provider-health__item">
                                                <span class="api-provider-health__label">Checked transactions</span>
                                                <strong class="api-provider-health__value">{{ number_format((int) ($api->availability_check_transactions_count ?? 0)) }}</strong>
                                            </div>
                                            <div class="api-provider-health__item">
                                                <span class="api-provider-health__label">Successful</span>
                                                <strong class="api-provider-health__value">{{ number_format((int) ($api->successful_transactions ?? 0)) }}</strong>
                                            </div>
                                            <div class="api-provider-health__item">
                                                <span class="api-provider-health__label">Failed</span>
                                                <strong class="api-provider-health__value">{{ number_format((int) ($api->failed_transactions ?? 0)) }}</strong>
                                            </div>
                                        </div>

                                        <div class="api-provider-health__meta mt-3">
                                            Last checked:
                                            <strong>{{ $api->availability_checked_at ? $api->availability_checked_at->format('M j, Y g:i A') : 'Not checked yet' }}</strong>
                                        </div>
                                    </div>

                                    <div class="api-provider-webhook">
                                        <div class="api-provider-webhook__label">Callback URL</div>
                                        <input
                                            type="text"
                                            id="callback-url-{{ $api->id }}"
                                            class="form-control form-control-sm"
                                            value="{{ $callbackUrl }}"
                                            readonly
                                        >
                                        <button
                                            type="button"
                                            class="btn btn-primary btn-sm api-provider-copy"
                                            data-copy-target="callback-url-{{ $api->id }}"
                                        >
                                            Copy callback URL
                                        </button>
                                    </div>

                                    @if(hasAccess('api.edit') || hasAccess('api.balance'))
                                        <div class="api-provider-actions">
                                            @if(hasAccess('api.edit'))
                                                <a href="{{ route('api.edit', $api->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="bx bx-edit-alt me-25"></i>
                                                    View / Edit
                                                </a>
                                            @endif

                                            @if(hasAccess('api.balance'))
                                                <button
                                                    type="button"
                                                    id="api-{{ $api->id }}"
                                                    class="btn btn-sm btn-outline-info"
                                                    onclick="getBalance('{{ $api->id }}')"
                                                >
                                                    <span id="icon-{{ $api->id }}">
                                                        <i class="bx bx-refresh"></i>
                                                    </span>
                                                    Refresh balance
                                                </button>

                                                <span
                                                    id="balance-{{ $api->id }}"
                                                    class="api-provider-balance {{ $api->balance !== null ? '' : 'api-provider-balance--muted' }}"
                                                    data-provider-slug="{{ $api->slug }}"
                                                >
                                                    {{ $balanceValue }}
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="alert alert-light border mb-0">No API providers found.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        function copyToClipboard(targetId, button) {
            const input = document.getElementById(targetId);
            if (!input) {
                return;
            }

            const text = input.value || input.textContent || '';
            if (!text) {
                return;
            }

            const original = button.innerHTML;

            const restoreButton = () => {
                button.innerHTML = original;
                button.disabled = false;
            };

            const finish = () => {
                button.innerHTML = 'Copied';
                setTimeout(restoreButton, 1200);
            };

            button.disabled = true;

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(finish).catch(() => {
                    const textarea = document.createElement('textarea');
                    textarea.value = text;
                    textarea.style.position = 'fixed';
                    textarea.style.opacity = '0';
                    document.body.appendChild(textarea);
                    textarea.focus();
                    textarea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textarea);
                    finish();
                }).finally(() => {
                    button.disabled = false;
                });
                return;
            }

            const textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.focus();
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
            finish();
            button.disabled = false;
        }

        document.querySelectorAll('[data-copy-target]').forEach(function (button) {
            button.addEventListener('click', function () {
                copyToClipboard(this.getAttribute('data-copy-target'), this);
            });
        });

        function getBalance(id) {
            const button = $('#api-' + id);
            const icon = $('#icon-' + id);
            const balance = $('#balance-' + id);
            const providerSlug = balance.data('providerSlug');

            function setBalanceFailure(message) {
                balance
                    .removeClass('api-provider-balance--muted')
                    .addClass('text-danger')
                    .text(message)
                    .show();
            }

            button.prop('disabled', true);
            icon.html("<i class='bx bx-loader-alt bx-spin'></i>");

            $.ajax({
                url: "{{ url('admin/api-balance') }}/" + id,
                method: 'GET',
                dataType: 'json',
                success: function (data) {
                    if (data.status === 'success') {
                        const display = data.balance_display
                            || data.balance
                            || (typeof data.balance_value === 'number'
                                ? (data.currency || (providerSlug === 'paystack' ? 'NGN' : '{{ $currency }}')) + ' ' + Number(data.balance_value).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })
                                : null);

                        if (display) {
                            balance
                                .removeClass('api-provider-balance--muted text-danger')
                                .text(display)
                                .show();
                        } else {
                            setBalanceFailure('Balance unavailable.');
                        }
                        return;
                    }

                    setBalanceFailure(data.message || 'Balance check failed.');
                },
                error: function (xhr) {
                    setBalanceFailure(xhr.responseJSON?.message || 'Balance check failed.');
                },
                complete: function () {
                    button.prop('disabled', false);
                    icon.html("<i class='bx bx-refresh'></i>");
                }
            });
        }
    </script>
@endsection
