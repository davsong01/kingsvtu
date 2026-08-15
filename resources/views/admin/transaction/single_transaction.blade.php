<?php
    if($transaction->status == 'failed'){
        $color = 'red';
    }elseif($transaction->status == 'initiated'){
        $color = '#FDAC41';
    }else {
        $color = 'green';
    }
?>
@extends('sneat.layouts.app')
@section('title', 'Transction Details')

@section('page-style')
<style>
    .reset-pin {
        font-size: 10px;
        float: right;
    }

    .heads {
        color: black
    }
    body {
        font-size: 1rem;
        font-weight: 398;
        color: black;
        font-size: smaller;
    }
    .table {
        color: black;
    }

    code{
        max-height: 250px;
        display: block;
        overflow:scroll;
        word-wrap: break-word;
        padding: 10px;
        margin:bottom:10px;
        height: 250px;
    }
    .well, .validate-div {
        min-height: 20px;
        padding: 12px;
        margin-bottom: 12px;
        background-color: #f5f5f5;
        border: 1px solid #e3e3e3;
        border-radius: 4px;
        box-shadow: inset 0 1px 1px rgba(0,0,0,.05);
        margin-top: 8px;
    }

    .transaction-panel .card-body {
        padding: 1rem 1.1rem;
    }

    .transaction-toggle-btn {
        margin-top: .5rem;
    }

    .transaction-code-shell {
        display: none !important;
        margin-top: .75rem;
    }

    .copy-inline-btn {
        border: 1px solid #d6dbe6;
        background: #fff;
        color: #4b5563;
        padding: .18rem .55rem;
        margin-right: .25rem;
        line-height: 1;
        font-size: .72rem;
        border-radius: .45rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
    }

    .copy-inline-btn:hover {
        color: #1fa868;
        border-color: #1fa868;
        background: rgba(31, 168, 104, .06);
    }

    .copy-inline-wrap {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: .25rem;
    }

    .copy-status {
        font-size: .72rem;
        color: #1fa868;
        margin-left: 0;
    }
</style>
@endsection
@section('content')
<!-- Content wrapper -->
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="content-wrapper">
        <div class="content-body">
            <!-- Basic Inputs start -->
            <section id="basic-input">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="content-body">
                                <!-- Nav Filled Starts -->
                                <section id="nav-filled">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="card">
                                                <div class="col-md-12">
                                                    <div class="card-header" style="padding:1.4rem 0.7rem">
                                                        <h4 class="card-title">Transaction Details</h4>
                                                        @include('sneat.layouts.alerts')
                                                    </div>
                                                </div>
                                                <div class="card-content">
                                                    <div class="card-body">
                                                        <div class="card mb-4">
                                                            <div class="card-body">
                                                                <div class="row align-items-start g-4">
                                                                    <div class="col-md-1">
                                                                        @if(in_array($transaction->reason, ['LEVEL-UPGRADE','WALLET-FUNDING','ADMIN-DEBIT','ADMIN-CREDIT']))
                                                                        <img id="product-image" width="60" height="60" src="{{ asset('site/upgrade.jpg') }}" alt="" class="product-image" style="margin:5px; box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;">
                                                                        @else
                                                                        <img id="product-image" width="60" height="60" src="{{ asset($transaction->product->image) }}" alt="" class="product-image" style="margin:5px; box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;">
                                                                        @endif

                                                                    </div>
                                                                    <div class="col-md-4">
                                                                    <h5 style="color:black"><strong>{{ $transaction->product_name }}</strong></h5>
                                                                        <h5 class="mb-1">{{ $transaction->transaction_id }}</h5>
                                                                        <div class="copy-inline-wrap mb-2">
                                                                            <span>{{ $transaction->transaction_id }}</span>
                                                                            <button type="button" class="btn btn-info btn-sm copy-inline-btn js-copy-value" data-copy-value="{{ $transaction->transaction_id }}" title="Copy transaction id" aria-label="Copy transaction id">
                                                                                Copy
                                                                            </button>
                                                                            <span class="copy-status" style="display:none;">Copied</span>
                                                                        </div>

                                                                        {{ $transaction->created_at }}
                                                                        @if(!in_array($transaction->reason, ['LEVEL-UPGRADE','WALLET-FUNDING']))
                                                                            <br>
                                                                            <a href="{{ route('transaction.receipt.download', ['transaction_id' => $transaction->id])}}" target="_blank" class="btn btn-primary btn-sm" style="color:#fff;"><i class="fa fa-download"></i> Download Receipt</a> <br>
                                                                        @endif
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <strong>Request Id:</strong> <br>
                                                                        <div class="copy-inline-wrap mb-2">
                                                                            <span>{{ $transaction->reference_id }}</span>
                                                                            <button type="button" class="btn btn-info btn-sm copy-inline-btn js-copy-value" data-copy-value="{{ $transaction->reference_id }}" title="Copy request id" aria-label="Copy request id">
                                                                                Copy
                                                                            </button>
                                                                            <span class="copy-status" style="display:none;">Copied</span>
                                                                        </div>
                                                                        <br>
                                                                        <strong>External Reference Id:</strong> <br>
                                                                        <div class="copy-inline-wrap mb-2">
                                                                            <span>{{ $transaction->external_reference_id }}</span>
                                                                            <button type="button" class="btn btn-info btn-sm copy-inline-btn js-copy-value" data-copy-value="{{ $transaction->external_reference_id }}" title="Copy external reference id" aria-label="Copy external reference id">
                                                                                Copy
                                                                            </button>
                                                                            <span class="copy-status" style="display:none;">Copied</span>
                                                                        </div>
                                                                        <br>
                                                                        <strong>IP Address: </strong><br>{{ $transaction->ip_address }} <br>
                                                                        @if(!empty($transaction->extras))
                                                                        <li class="d-flex mb-1">
                                                                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                                                <div class="me-2">
                                                                                    <p class="mb-0 lh-1 key"><strong>Extras:</strong> <br></p>
                                                                                </div>
                                                                                <div class="">{{ ucfirst($transaction->extras) }}</div>
                                                                            </div>
                                                                        </li>
                                                                        @endif
                                                                        @if(!empty($transaction->extra_info))
                                                                            @foreach ( json_decode($transaction->extra_info) as $key=>$value )
                                                                                <li class="d-flex mb-1">
                                                                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                                                    <div class="me-2">
                                                                                        <p class="mb-0 lh-1 key"><strong>{{ $key }}:</strong> </p>
                                                                                    </div>
                                                                                    <div class="item-progres value">{{ ucfirst($value) }}sd</div>
                                                                                </div>
                                                                            </li>
                                                                            @endforeach
                                                                        @endif
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <strong>User Status:</strong> <br>
                                                                        <span style="color:{{ $color }}"><strong>{{ ucfirst($transaction->descr) }}</strong></span><br><br>
                                                                        <strong>Real Status</strong> <br>
                                                                        <span style="color:{{ $color }}"><strong>{{ ucfirst($transaction->status) }}</strong></span><br><br>
                                                                        @if(!in_array($transaction->status, ['completed','success']))
                                                                        {{-- <a id="qw_resolve" class="btn btn-success btn-sm" style="color:#fff;"><svg fill="white" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24"><path d="M754-81q-8 0-15-2.5T726-92L522-296q-6-6-8.5-13t-2.5-15q0-8 2.5-15t8.5-13l85-85q6-6 13-8.5t15-2.5q8 0 15 2.5t13 8.5l204 204q6 6 8.5 13t2.5 15q0 8-2.5 15t-8.5 13l-85 85q-6 6-13 8.5T754-81Zm0-95 29-29-147-147-29 29 147 147ZM205-80q-8 0-15.5-3T176-92l-84-84q-6-6-9-13.5T80-205q0-8 3-15t9-13l212-212h85l34-34-165-165h-57L80-765l113-113 121 121v57l165 165 116-116-43-43 56-56H495l-28-28 142-142 28 28v113l56-56 142 142q17 17 26 38.5t9 45.5q0 24-9 46t-26 39l-85-85-56 56-42-42-207 207v84L233-92q-6 6-13 9t-15 3Zm0-96 170-170v-29h-29L176-205l29 29Zm0 0-29-29 15 14 14 15Zm549 0 29-29-29 29Z"/></svg> Resolve</a> --}}
                                                                        @endif
                                                                        {{-- Description <br> --}}
                                                                        {{-- <span style="color:{{ $color }}"><strong>{{ ucfirst($transaction->descr) }}</strong></span><br><br> --}}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="card mb-4">
                                                            <div class="card-body">
                                                                <div class="row g-4">
                                                                <div class="col-md-3">
                                                                    <strong class="heads">Wallet Trail:</strong> <br>

                                                                    @if($transaction->wallets)
                                                                        @foreach($transaction->wallets as $wallet)
                                                                            @if($wallet->type == 'credit')
                                                                            <span style="color:green"><strong>CREDIT :</strong> {{ $wallet->created_at}} ({!! getSettings()->currency. number_format($wallet->amount, 2) !!})
                                                                            </span>
                                                                            @endif
                                                                            @if($wallet->type == 'debit')
                                                                            <span style="color:red"><strong>DEBIT : </strong>{{ $wallet->created_at}}
                                                                                ({!! getSettings()->currency. number_format($wallet->amount, 2) !!})
                                                                            </span>
                                                                            @endif
                                                                            <br>
                                                                        @endforeach
                                                                    @endif

                                                                </div>
                                                                <div class="col-md-3">
                                                                    <strong class="heads">Payment Details</strong> <br>
                                                                    <strong>PAYMENT METHOD: </strong> {{ $transaction->payment_method}} <br>
                                                                    <strong>CHANNEL: </strong>{{ $transaction->channel}} <br>
                                                                    <strong>CUST. EMAIL: </strong>{{ $transaction->customer_email }} <br>
                                                                    <strong>PHONE: </strong>{{ $transaction->customer_phone }} <br>
                                                                    @if($transaction->variation)
                                                                        <strong>Variation: </strong>{{ $transaction->variation->system_name ?? 'null'}} <br>
                                                                    @endif
                                                                    @if(!empty($transaction->api))
                                                                    <br>
                                                                    <strong>Provider:</strong>{{ $transaction->api->name }} <br>
                                                                    @endif
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <strong class="heads d-block mb-2">Transaction Details</strong>
                                                                    @if(!in_array($transaction->reason, ['LEVEL-UPGRADE','WALLET-FUNDING']))
                                                                        <strong>Product:</strong> {{ $transaction->product_name }} <br>
                                                                        @if($transaction->category)
                                                                            <strong>Category:</strong> {{ $transaction->category->display_name }} <br>
                                                                        @endif
                                                                        @if($transaction->variation)
                                                                            <strong>Variation:</strong> {{ $transaction->variation->system_name ?? 'null' }} <br>
                                                                        @endif
                                                                        <strong>Unique Element:</strong> {{ $transaction->unique_element }} <br>
                                                                        <strong>Provider:</strong> {{ $transaction->api->name ?? 'N/A' }} <br>
                                                                    @else
                                                                        <strong>Service:</strong> {{ ucfirst(str_replace("-"," ",$transaction->reason)) }} <br>
                                                                    @endif
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <strong class="heads mb-0">Request Payload</strong>
                                                                    <div class="d-flex flex-column align-items-start gap-2 mt-2">
                                                                        <button type="button" class="btn btn-info btn-sm copy-inline-btn js-copy-section" data-copy-target="#requestPayloadBody code" title="Copy payload" aria-label="Copy payload">
                                                                            Copy
                                                                        </button>
                                                                        <span class="copy-status" style="display:none;">Copied</span>
                                                                        <button
                                                                            type="button"
                                                                            class="btn btn-outline-primary btn-sm transaction-toggle-btn"
                                                                            data-toggle-target="#requestPayloadBody"
                                                                            data-show-text="Show Payload"
                                                                            data-hide-text="Hide Payload"
                                                                            aria-expanded="false"
                                                                        >
                                                                            Show Payload
                                                                        </button>
                                                                    </div>
                                                                    <strong class="heads mb-0 d-block mt-4">API Response ({{ $transaction->api->name ?? null }})</strong>
                                                                    <div class="d-flex flex-column align-items-start gap-2 mt-2">
                                                                        <button type="button" class="btn btn-info btn-sm copy-inline-btn js-copy-section" data-copy-target="#apiResponseBody code" title="Copy response" aria-label="Copy response">
                                                                            Copy
                                                                        </button>
                                                                        <span class="copy-status" style="display:none;">Copied</span>
                                                                        <button
                                                                            type="button"
                                                                            class="btn btn-outline-primary btn-sm transaction-toggle-btn"
                                                                            data-toggle-target="#apiResponseBody"
                                                                            data-show-text="Show Response"
                                                                            data-hide-text="Hide Response"
                                                                            aria-expanded="false"
                                                                        >
                                                                            Show Response
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                                </div>
                                                                @if(!in_array($transaction->reason, ['LEVEL-UPGRADE','WALLET-FUNDING']))
                                                                <div class="col-12 mt-3">
                                                                    <div class="row g-3">
                                                                        <div class="col-md-6 transaction-data-section">
                                                                            <div id="requestPayloadBody" class="transaction-code-shell" style="display:none;">
                                                                                <code style="margin:10px 0">
                                                                                    {!! $transaction->request_data !!}
                                                                                </code>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6 transaction-data-section">
                                                                            <div id="apiResponseBody" class="transaction-code-shell" style="display:none;">
                                                                                <code style="margin:10px 0">
                                                                                    {!! $transaction->api_response!!}
                                                                                </code>

                                                                                @if(!empty($transaction->failure_reason))
                                                                                <div>
                                                                                    <span style="color:red"><strong><u>Failure Reason</u></strong></span> <br>
                                                                                    {!! $transaction->failure_reason!!}
                                                                                </div>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="card transaction-panel mb-3">
                                                            <div class="card-body">
                                                                <div class="table-responsive">
                                                                    <table id="table-extended-success" class="table mb-0">
                                                                        <thead>
                                                                            <tr>
                                                                                <th style="color:black">Item</th>
                                                                                <th style="color:black">Unit Cost</th>
                                                                                <th style="color:black">Quantity</th>
                                                                                <th style="color:black">Amount</th>
                                                                                <th style="color:black">Biller</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                        <tr>
                                                                            <td>
                                                                                @if(in_array($transaction->reason, ['LEVEL-UPGRADE','WALLET-FUNDING','ADMIN-DEBIT','ADMIN-CREDIT']))
                                                                                    {{ ucfirst(str_replace("-"," ",$transaction->reason))}}
                                                                                @else
                                                                                {{ $transaction->product->name }}@if(!empty($transaction->variation->system_name)) <strong> | {{$transaction->variation->system_name}} </strong> @endif
                                                                                @endif
                                                                            </td>
                                                                            <td>
                                                                                {!! getSettings()->currency. number_format($transaction->amount, 2) !!}
                                                                            </td>
                                                                                <td>
                                                                                {{ $transaction->quantity  }}
                                                                            </td>

                                                                            <td>
                                                                                <span style="color:black">Convenience Fee:</span> {!! getSettings()->currency. number_format($transaction->provider_charge, 2) !!} <br>
                                                                                <span style="color:black">Discount: </span>{!! getSettings()->currency. number_format($transaction->discount, 2) !!} <br>
                                                                                <span style="color:black">Provider Charge:</span>{!! getSettings()->currency. number_format($transaction->provider_charge, 2) !!} <br>
                                                                                <span style="color:black">Total Amount:</span> {!! getSettings()->currency. number_format($transaction->total_amount, 2) !!}
                                                                            </td>
                                                                            <td>{{ $transaction->unique_element }}
                                                                                <?php
                                                                                    if (isset($transaction->variation) &&  in_array($transaction->category->unique_element, verifiableUniqueElements())
                                                                                    ) {
                                                                                        $element = $transaction->category->unique_element;
                                                                                    } else if (isset($transaction->variation) &&  in_array($transaction->variation->slug, verifiableUniqueElements())
                                                                                    )  {
                                                                                        $element = specialVerifiableVariations()[$transaction->variation->slug];
                                                                                    }  else{
                                                                                        $element = null;
                                                                                    }
                                                                                ?>
                                                                                {{-- @if(isset($element)) <br>
                                                                                <button id="validate-biller" onclick="validateBiller('{{$transaction->variation_id}}','{{$element}}','{{$transaction->unique_element}}')" class="btn btn-info btn-sm">Validate Biller</button>
                                                                                @endif --}}
                                                                            </td>
                                                                        </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="card transaction-panel mb-3">
                                                            <div class="card-body">
                                                                <div class="row g-3">
                                                                    <div class="col-md-6">
                                                                        <div class="well">
                                                                            <address>
                                                                                <img src="{{url('/')}}/site/loading.gif" height="70" style="display:none; margin-left: auto; margin-right:auto;height:initial;" id="img_loading">
                                                                                <div id="q_res" style="max-height:300px;overflow:scroll;word-wrap: break-word">
                                                                                </div>
                                                                            </address>
                                                                        </div>
                                                                        <div class="gateway-helper mt-3 mb-3">
                                                                            <strong>Initial Balance:</strong> {!! getSettings()->currency.number_format($transaction->balance_before, 2) !!}<br>
                                                                            <strong>Final Balance:</strong> {!! getSettings()->currency. number_format($transaction->balance_after, 2) !!}
                                                                        </div>
                                                                        <button id="qw_credit" class="btn btn-success btn-sm" style="color:#fff;"><svg fill="white" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24"><path d="M480-80q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q65 0 123 19t107 53l-58 59q-38-24-81-37.5T480-800q-133 0-226.5 93.5T160-480q0 133 93.5 226.5T480-160q32 0 62-6t58-17l60 61q-41 20-86 31t-94 11Zm280-80v-120H640v-80h120v-120h80v120h120v80H840v120h-80ZM424-296 254-466l56-56 114 114 400-401 56 56-456 457Z"/></svg> Query Credit</button>

                                                                        <button id="qw_debit" class="btn btn-danger btn-sm" style="color:#fff;"><svg fill="white" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24"><path d="M200-440v-80h560v80H200Z"/></svg> Query Debit</button>
                                                                        
                                                                        <a id="qw-transaction" onclick="queryStatus('{{$transaction->id}}')" data-id="{{$transaction->id}}" class="btn btn-info btn-sm" style="color:#fff;"><svg fill="white" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24"><path d="m105-233-65-47 200-320 120 140 160-260 109 163q-23 1-43.5 5.5T545-539l-22-33-152 247-121-141-145 233ZM863-40 738-165q-20 14-44.5 21t-50.5 7q-75 0-127.5-52.5T463-317q0-75 52.5-127.5T643-497q75 0 127.5 52.5T823-317q0 26-7 50.5T795-221L920-97l-57 57ZM643-217q42 0 71-29t29-71q0-42-29-71t-71-29q-42 0-71 29t-29 71q0 42 29 71t71 29Zm89-320q-19-8-39.5-13t-42.5-6l205-324 65 47-188 296Z"/></svg></i> Re Query Transaction</a>

                                                                        
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="validate-div" style="display:none;">
                                                                            <address>
                                                                                <img src="{{url('/')}}/site/loading.gif" height="70" style="margin-left: auto; margin-right:auto;height:initial" id="img_loading2">
                                                                                <div id="q_res2" style="max-height:300px;overflow:scroll;word-wrap: break-word">
                                                                                </div>
                                                                            </address>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
@section('page-script')
<script src="{{ asset('app-assets/js/scripts/pages/dashboard-analytics.js') }}"></script>
<script>
    function copyTextToClipboard(text) {
        const value = String(text ?? '');

        if (navigator.clipboard && window.isSecureContext) {
            return navigator.clipboard.writeText(value);
        }

        return new Promise((resolve, reject) => {
            const textarea = document.createElement('textarea');
            textarea.value = value;
            textarea.setAttribute('readonly', '');
            textarea.style.position = 'fixed';
            textarea.style.top = '-9999px';
            textarea.style.left = '-9999px';
            document.body.appendChild(textarea);
            textarea.focus();
            textarea.select();

            try {
                const success = document.execCommand('copy');
                document.body.removeChild(textarea);
                success ? resolve() : reject(new Error('Copy command failed'));
            } catch (error) {
                document.body.removeChild(textarea);
                reject(error);
            }
        });
    }

    $('.transaction-toggle-btn').on('click', function () {
        const target = $(this).data('toggle-target');
        const showText = $(this).data('show-text');
        const hideText = $(this).data('hide-text');
        const $target = $(target);
        const isVisible = $target.is(':visible');

        $target.toggle(!isVisible);
        $(this).text(isVisible ? showText : hideText);
        $(this).attr('aria-expanded', String(!isVisible));
    });

    $(document).on('click', '.js-copy-value', function () {
        const value = $(this).data('copy-value') || '';
        const $status = $(this).siblings('.copy-status').first();

        copyTextToClipboard(value).then(function () {
            $status.stop(true, true).fadeIn(120).delay(800).fadeOut(160);
        });
    });

    $(document).on('click', '.js-copy-section', function () {
        const target = $(this).data('copy-target');
        const value = $(target).text().trim();
        const $status = $(this).siblings('.copy-status').first();

        copyTextToClipboard(value).then(() => {
            const $btn = $(this);
            const originalHtml = $btn.html();
            if ($status.length) {
                $status.stop(true, true).fadeIn(120).delay(800).fadeOut(160);
            } else {
                $btn.text('Copied');
                setTimeout(() => {
                    $btn.text(originalHtml.trim());
                }, 900);
            }
        });
    });

    $('#qw_credit').click(function () {
        var id ='{{$transaction->id}}';
        queryWallet(id, 'credit');
    });

    $('#qw_debit').click(function () {
        var id ='{{$transaction->id}}';
        queryWallet(id, 'debit');
    });

    function queryWallet(id, type){
        if(type == 'credit'){
			url = '{{url("/")}}/admin/query-wallet/'+id+'?type=credit';
        }else{
			url = '{{url("/")}}/admin/query-wallet/'+id+'?type=debit';
        }
        
		$.ajax({
			url : url,
			type : 'GET',
			beforeSend: function (){
				$('#q_res').hide();
				$('#img_loading').show();
				$('#validate-biller').html('Processing....');
			},
			success:function (data) {
                if(type == 'credit'){
                    $('#qw_credit').html('Query '+type+' <i class="fa fa-check"></i>');
                }else{
                    $('#qw_debit').html('Query '+type+' <i class="fa fa-check"></i>');
                }
                
				$('#img_loading').hide();
				$('#q_res').show();
				$('#q_res').html(data.message);
			}
		});
		e.preventDefault();
	}

    function queryStatus(id){
		var tid = id;
        url = '{{url("/")}}/admin/requery-transaction/'+tid;

		$.ajax({
			url : url,
			type : 'GET',
			beforeSend: function (){
				$('#q_res').hide();
				$('#img_loading').show();
                $('.validate-div').show();
				$('#img_loading2').show();
				$('#qw_status').html('Processing....');
			},
			success:function (data) {
				$('#qw_status').html('Requery Complete <i class="fa fa-check"></i>');
				$('#img_loading').hide();
				$('#q_res').show();
				$('#q_res').html(data.message);

                // $('#validate-div').show();
                // $('#validate-biller').html('Validate Biller <i class="fa fa-check"></i>');
				$('#img_loading2').hide();
				$('#validate-div').show();
				$('#q_res2').show();
				$('#q_res2').html(JSON.stringify(data.api_response, null, 5));

			}
		});
		e.preventDefault();
	}

    // function validateBiller(variation_id, element, value){
    //     var variation_id = variation_id;
    //     var element = element;
    //     var value = value;

    //     var data = {
    //         'variation':variation_id,
    //         'unique_element':{{$transaction->unique_element}},
    //         _token: {{ csrf_token() }},
    //     };

    //     var url = "{{ route('admin.verify.post') }}";
	// 	$.ajax({
	// 		url : url,
	// 		type : 'POST',
    //         data : data,
	// 		beforeSend: function (){
	// 			$('.validate-div').show();
	// 			$('#img_loading2').show();
	// 			$('#validate-biller').html('Processing....');
	// 		},
	// 		success:function (data) {
    //             console.log(data);
	// 			$('#validate-biller').html('Validate Biller <i class="fa fa-check"></i>');
	// 			$('#img_loading2').hide();
	// 			$('#validate-div').show();
	// 			$('#q_res2').show();
	// 			$('#q_res2').html(data.message);
	// 		}
	// 	});
	// 	e.preventDefault();
    // }

    // $('#qw-transaction').click(function () {
    //     let id = $(this).data('id')
    //     $.ajax({
	// 		url : `/admin/requery-transaction/${id}`,
	// 		beforeSend: function (){
	// 			$('.validate-div').show();
	// 			$('#img_loading2').show();
	// 			$('#validate-biller').html('Processing....');
	// 		},
	// 		success:function (data) {
	// 			$('#validate-biller').html('Validate Biller <i class="fa fa-check"></i>');
	// 			$('#img_loading2').hide();
	// 			$('#validate-div').show();
	// 			$('#q_res2').show();
	// 			$('#q_res2').html(data.message);
	// 		}
	// 	});
    // });
</script>
@endsection
