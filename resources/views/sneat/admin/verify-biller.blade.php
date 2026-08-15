@extends('sneat.layouts.app')

@section('title', 'Verify Biller')

@section('page-style')
    <link href="{{ asset('modern-assets/vendor/libs/select2/select2.css') }}" rel="stylesheet" />
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="admin-page-hero mb-4">
                <div>
                    <span class="admin-page-hero__kicker">Financials</span>
                    <h1>Verify Biller</h1>
                    <p>Run a biller verification check in a cleaner workspace and inspect the raw response inline.</p>
                </div>
            </div>

            @include('sneat.layouts.alerts')

            <div class="gateway-card card mb-4">
                <div class="card-header">
                    <h3>Verification response</h3>
                    <p>The formatted response and raw payload will appear here after submission.</p>
                </div>
                <div class="card-body">
                    <div class="row g-3 api-call mb-4">
                        <div class="col-md-6">
                            <div class="financial-code format d-none"></div>
                        </div>
                        <div class="col-md-6">
                            <div class="financial-code raw d-none"></div>
                        </div>
                        <div class="col-12 loading d-none text-warning fst-italic">
                            Please wait while we verify the biller...
                        </div>
                    </div>

                    <form action="{{ route('admin.verify.post') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="modern-admin-label" for="product">Select Product</label>
                                <select class="form-select form-select-{{ formControlSize() }}" name="product" id="product" required data-placeholder="Search product">
                                    <option value="">Select Product</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->slug }}">{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="modern-admin-label" for="api">Select API</label>
                                <select class="form-select form-select-{{ formControlSize() }}" name="api" id="api" required>
                                    <option value="">Select API</option>
                                    @foreach ($api as $provider)
                                        <option value="{{ $provider->id }}">{{ $provider->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="modern-admin-label" for="type">Select Type</label>
                                <select class="form-select form-select-{{ formControlSize() }}" name="type" id="type" required>
                                    <option value="">Select Type</option>
                                    <option value="prepaid">Prepaid</option>
                                    <option value="postpaid">Postpaid</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="modern-admin-label" for="value">Value</label>
                                <input type="text" class="form-control form-control-{{ formControlSize() }}" id="value" name="value" placeholder="Value to verify" required>
                            </div>
                        </div>
                        <div class="modern-admin-footer mt-4">
                            <button class="btn btn-admin-submit" type="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script src="{{ asset('modern-assets/vendor/libs/select2/select2.js') }}"></script>
    <script>
        (function () {
            const $product = $('#product');

            if ($product.length && !$product.data('select2')) {
                $product.wrap('<div class="position-relative"></div>').select2({
                    placeholder: $product.data('placeholder') || 'Search product',
                    allowClear: true,
                    width: '100%'
                });
            }
        })();

        $('form').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: this.action,
                type: 'post',
                data: $(this).serializeArray(),
                beforeSend: () => {
                    $('.api-call .loading').removeClass('d-none');
                    $('.api-call .raw, .api-call .format').addClass('d-none').empty();
                },
                success: res => {
                    $('.api-call .loading').addClass('d-none');
                    $('.api-call .format').removeClass('d-none').html(res.message || 'Verification complete');
                    $('.api-call .raw').removeClass('d-none').html(JSON.stringify(res.raw_response ?? {}, null, 2));
                },
                error: () => {
                    $('.api-call .loading').addClass('d-none');
                    alert('Hmph, something went south!');
                },
            });
        });
    </script>
@endsection
