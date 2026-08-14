@extends('sneat.layouts.app')

@section('title', 'Fund Wallet')

@php
    $user = auth()->user();
    $currency = getSettings()->currency ?? '₦';
    $reservedAccounts = $user->customer?->reserved_accounts ?? collect();
    $allowCard = getSettings()->allow_fund_with_card === 'yes';
    $allowBankTransfer = getSettings()->allow_fund_with_reserved_account === 'yes';
@endphp

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="profile-hero mb-4 gateway-hero">
                <div class="profile-hero__meta">
                    <div class="profile-avatar"><i class="bx bx-wallet"></i></div>
                    <div class="profile-meta">
                        <span class="gateway-hero__kicker">Wallet funding</span>
                        <strong>Fund wallet</strong>
                        <span>Add money to your wallet using card or bank transfer.</span>
                    </div>
                </div>
                <div class="gateway-summary">
                    <div class="gateway-summary__card">
                        <span class="gateway-summary__label">Wallet balance</span>
                        <span class="gateway-summary__value">{{ $currency . number_format(walletBalance($user), 2) }}</span>
                    </div>
                    <div class="gateway-summary__card">
                        <span class="gateway-summary__label">KYC</span>
                        <span class="gateway-summary__value">{{ ucfirst(str_replace('-', ' ', getFinalKycStatus($user->customer->id) ?? 'pending')) }}</span>
                    </div>
                </div>
            </div>

            @include('sneat.layouts.alerts')

            @if(getFinalKycStatus($user->customer->id) == 'unverified')
                <div class="alert alert-warning border mb-4">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                        <div>
                            <strong>KYC verification required</strong>
                            <div class="small">Please complete your KYC before funding your wallet.</div>
                        </div>
                        <a href="{{ route('update.kyc.details') }}" class="btn btn-danger btn-sm">Update KYC details</a>
                    </div>
                </div>
            @endif

            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="card profile-card h-100">
                        <div class="card-header">
                            <h3>Funding options</h3>
                            <p>Choose a method and continue with a clean checkout flow.</p>
                        </div>
                        <div class="card-body">
                            <ul class="nav nav-pills gap-2 mb-4" role="tablist">
                                @if($allowCard)
                                    <li class="nav-item" role="presentation">
                                        <button class="btn btn-label-primary active" data-bs-toggle="tab" data-bs-target="#fund-card" type="button" role="tab">Fund with card</button>
                                    </li>
                                @endif
                                @if($allowBankTransfer)
                                    <li class="nav-item" role="presentation">
                                        <button class="btn btn-label-primary {{ ! $allowCard ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#fund-bank" type="button" role="tab">Bank transfer</button>
                                    </li>
                                @endif
                            </ul>

                            <div class="tab-content">
                                @if($allowCard)
                                    <div class="tab-pane fade show active" id="fund-card" role="tabpanel">
                                        <div class="profile-side-card mb-4">
                                            <div class="profile-side-row">
                                                <span>Charge note</span>
                                                <strong>Card funding charges may apply</strong>
                                            </div>
                                            <div class="gateway-helper">
                                                {!! getSettings()->card_funding_note ?? 'Enter an amount and proceed to pay.' !!}
                                            </div>
                                        </div>

                                        <form action="{{ route('process-customer-load-wallet') }}" method="POST" id="wallet_load" class="purchase-form">
                                            @csrf
                                            <div class="mb-3">
                                                <label class="profile-label" for="amount">Amount</label>
                                                <input type="number" class="form-control form-control-{{ formControlSize() }}" id="amount" name="amount" placeholder="Enter amount" value="{{ old('amount') }}" required>
                                                <div class="purchase-amount-presets mt-3" id="wallet-amount-presets"></div>
                                            </div>
                                            <div class="profile-footer">
                                                <a class="btn btn-admin-submit" style="cursor:pointer;color:white" onclick="loadWallet()">Pay now</a>
                                            </div>
                                        </form>
                                    </div>
                                @endif

                                @if($allowBankTransfer)
                                    <div class="tab-pane fade {{ ! $allowCard ? 'show active' : '' }}" id="fund-bank" role="tabpanel">
                                        <div class="profile-side-card mb-4">
                                            <div class="gateway-helper mb-2">{!! getSettings()->wallet_funding_note ?? '' !!}</div>
                                            <div class="gateway-helper">{!! getSettings()->bank_transfer_note ?? '' !!}</div>
                                        </div>

                                        @if($reservedAccounts->count() > 0)
                                            <div class="table-responsive">
                                                <table class="table align-middle">
                                                    <thead>
                                                        <tr>
                                                            <th>Processor</th>
                                                            <th>Account Name</th>
                                                            <th>Bank</th>
                                                            <th>Account Number</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($reservedAccounts as $account)
                                                            <tr>
                                                                <td>{{ $account->gateway->name }}</td>
                                                                <td>{{ $account->account_name }}</td>
                                                                <td>{{ $account->bank_name }}</td>
                                                                <td>{{ $account->account_number }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="alert alert-light border mb-0">
                                                No reserved account number found yet. Please contact support if this persists.
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="card modern-admin-card h-100">
                        <div class="card-header">
                            <h3>Wallet tips</h3>
                            <p>Keep the funding flow simple and accurate.</p>
                        </div>
                        <div class="card-body">
                            <div class="d-flex gap-3 mb-3">
                                <span class="avatar-initial rounded bg-label-primary p-3">
                                    <i class="bx bx-credit-card fs-4"></i>
                                </span>
                                <div>
                                    <h6 class="mb-1">Card funding</h6>
                                    <div class="gateway-helper">Fast checkout from your preferred card.</div>
                                </div>
                            </div>
                            <div class="d-flex gap-3 mb-3">
                                <span class="avatar-initial rounded bg-label-success p-3">
                                    <i class="bx bx-building-house fs-4"></i>
                                </span>
                                <div>
                                    <h6 class="mb-1">Bank transfer</h6>
                                    <div class="gateway-helper">Use the reserved account details shown in the tab.</div>
                                </div>
                            </div>
                            <div class="d-flex gap-3">
                                <span class="avatar-initial rounded bg-label-warning p-3">
                                    <i class="bx bx-shield-quarter fs-4"></i>
                                </span>
                                <div>
                                    <h6 class="mb-1">KYC ready</h6>
                                    <div class="gateway-helper">Complete KYC first if funding is restricted on your account.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js"></script>
    <script type="text/javascript" src="https://sdk.monnify.com/plugin/monnify.js"></script>
    <script>
        (function () {
            const presets = [50, 100, 200, 500, 1000, 2000];
            const currency = @json($currency);
            const $container = $('#wallet-amount-presets');
            const $amount = $('#amount');

            if (!$container.length || !$amount.length) {
                return;
            }

            presets.forEach(function (amount) {
                const $button = $('<button>', {
                    type: 'button',
                    class: 'purchase-amount-presets__item',
                    text: currency + Number(amount).toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 }),
                    'data-amount': amount,
                });

                $button.on('click', function () {
                    $amount.val(amount).trigger('input');
                    $container.find('.purchase-amount-presets__item').removeClass('is-active');
                    $button.addClass('is-active');
                });

                $container.append($button);
            });

            $amount.on('input', function () {
                const currentAmount = Number($(this).val());
                $container.find('.purchase-amount-presets__item').each(function () {
                    $(this).toggleClass('is-active', Number($(this).data('amount')) === currentAmount);
                });
            });
        })();

        function loadWallet() {
            $.LoadingOverlay("show");
            document.forms["wallet_load"].submit();
        }
    </script>
@endsection
