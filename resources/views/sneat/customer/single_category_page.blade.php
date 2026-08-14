@php
    $verifiable = verifiableUniqueElements();
    $settings = getSettings();
    $currency = $settings->currency ?? '₦';
    $uniqueLabel = match ($category->slug) {
        'electricity' => 'Meter Number',
        'tv' => 'IUC Number',
        default => 'Profile ID',
    };
@endphp

@extends('sneat.layouts.app')

@section('title', $category->seo_title)
@section('keywords', $category->seo_keywords)
@section('description', $category->seo_description)

@section('page-css')
    <link rel="stylesheet" href="{{ asset('modern-assets/vendor/libs/select2/select2.css') }}" />
@endsection

@section('content')
    @include('sneat.customer.partials.page-header', [
        'eyebrow' => 'Service',
        'title' => $category->description,
        'subtitle' => 'Fill in the details below to complete the transaction.',
    ])

    @include('sneat.layouts.alerts')

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card purchase-card">
                <form action="{{ route('initialize.transaction') }}" method="POST" id="initialize" class="purchase-form">
                    @csrf
                    <div class="card-header d-flex align-items-center gap-3 border-bottom">
                        <span class="purchase-heading-icon bg-label-primary">
                            <i class="bx bx-cart-add fs-4"></i>
                        </span>
                        <div>
                            <h5 class="mb-1">Purchase details</h5>
                            <small class="text-muted">Fields marked as required must be completed.</small>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-12" id="product-image-div" style="display:none">
                                <div class="purchase-product-preview d-flex align-items-center gap-3 p-3 rounded">
                                    <img id="product-image" src="" alt="" class="rounded">
                                    <div class="min-w-0">
                                        <h6 id="product-title" class="mb-1"></h6>
                                        <p id="product-description" class="text-muted small mb-0"></p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="product" class="form-label">Select service</label>
                                <select class="form-select modern-select2" name="product" id="product" data-placeholder="Search for a service" required>
                                    <option value="">Select a service</option>
                                    @foreach ($category->products as $item)
                                        <option value="{{ $item->id }}"
                                            data-allow_subscription_type="{{ $item->allow_subscription_type }}"
                                            data-allow_quantity="{{ $item->allow_quantity }}"
                                            data-min="{{ $item->min }}"
                                            data-max="{{ $item->max }}"
                                            data-system_price="{{ $item->system_price }}"
                                            data-fixed_price="{{ $item->fixed_price }}"
                                            data-has_variation="{{ $item->has_variations }}"
                                            data-image="{{ asset($item->image) }}"
                                            data-name="{{ $item->name }}"
                                            data-quantity_graduation="{{ $item->quantity_graduation }}"
                                            data-description="{{ $item->description }}">
                                            {{ $item->display_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6" id="variation-div" style="display:none">
                                <label for="variation" class="form-label">Select variation</label>
                                <select class="form-select modern-select2" id="variation" name="variation" data-placeholder="Search for a variation" required>
                                    <option value="">Select a variation</option>
                                </select>
                            </div>

                            <div class="col-md-6 unique_element_div" style="display:none">
                                <label for="unique_element" class="form-label">{{ $uniqueLabel }}</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="unique_element" name="unique_element" value="{{ old('unique_element') }}">
                                    <button class="btn btn-outline-primary" id="verify-link" onclick="verify(this)" type="button" style="display:none">Verify</button>
                                </div>
                            </div>

                            <div class="col-md-6" id="bouquet-div" style="display:none">
                                <label for="bouquet" class="form-label">Subscription type</label>
                                <select class="form-select modern-select2" id="bouquet" name="bouquet" data-placeholder="Search subscription types" required>
                                    <option value="">Select a subscription type</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">Email address</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ auth()->user()->email ?? old('email') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone number</label>
                                <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}" required>
                            </div>

                            <div class="col-md-6" id="amount-div" style="display:none">
                                <label for="amount" class="form-label">Amount ({{ $currency }})</label>
                                <input class="form-control" id="amount" name="amount" placeholder="Enter amount" type="number" required>
                                <small class="text-danger" id="discount" style="display:none"></small>
                            </div>

                            <div class="col-md-6" id="quantity-div" style="display:none">
                                <label for="quantity" class="form-label">Quantity</label>
                                <select class="form-select modern-select2" id="quantity" name="quantity" data-placeholder="Search quantities" required>
                                    <option value="">Select a quantity</option>
                                </select>
                            </div>

                            @if (auth()->user()->transaction_pin)
                                <div class="col-md-6">
                                    <label for="transaction_pin" class="form-label">Transaction PIN</label>
                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                                        <span></span>
                                        <a class="small text-primary text-decoration-none" href="{{ route('customer.reset.pin') }}">Reset Transaction Pin</a>
                                    </div>
                                    <input type="password" class="form-control" id="transaction_pin" name="transaction_pin" required>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="card-footer d-flex justify-content-end border-top bg-transparent p-4">
                        <button class="purchase-submit btn btn-primary" id="buy-button" type="button" onclick="submitForm()">
                            <i class="bx bx-check-circle me-1"></i>
                            <span>Buy now</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center gap-3 border-bottom">
                    <span class="purchase-heading-icon bg-label-info">
                        <i class="bx bx-info-circle fs-4"></i>
                    </span>
                    <div>
                        <h5 class="mb-1">Before you buy</h5>
                        <small class="text-muted">Review the transaction details carefully.</small>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex gap-3 mb-3">
                        <i class="bx bx-check-circle text-primary fs-5 mt-1"></i>
                        <span>Select the correct service and package.</span>
                    </div>
                    <div class="d-flex gap-3 mb-3">
                        <i class="bx bx-check-circle text-primary fs-5 mt-1"></i>
                        <span>Confirm the recipient details before payment.</span>
                    </div>
                    <div class="d-flex gap-3">
                        <i class="bx bx-check-circle text-primary fs-5 mt-1"></i>
                        <span>Complete verification when it is requested.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<div class="modal fade" id="verify-modal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verify-title"></h5>
            </div>
            <div class="modal-body">
                <div id="verify-details"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal" onclick="closeModal()">Close</button>
            </div>
        </form>
    </div>
</div>

@section('page-script')
    <script src="{{ asset('modern-assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js"></script>
    <script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
    <script>
        function closeModal() {
            const modal = document.getElementById('verify-modal');
            if (modal) {
                bootstrap.Modal.getOrCreateInstance(modal).hide();
            }
        }

        function verify() {
            $("#amount").attr({ "required": true });

            var unique_element = $("#unique_element").val();
            var category_slug = $("#category_slug").val();

            if (unique_element == '') {
                alert("Please enter biller to verify");
                return;
            }

            $.LoadingOverlay("show");
            closeModal();
            var amount = $('#amount').val();

            var url = "{{ url('customer-verify') }}";
            var element = "{{ $category->unique_element }}";
            var allow_subscription_type = $('#product').find(':selected').data('allow_subscription_type');

            var formData = {
                category_id: {{ $category->id }},
                unique_element: $("#unique_element").val(),
                variation: $("#variation").val(),
                product_id: $("#product").val(),
            };

            $.ajax({
                url: url,
                method: 'POST',
                dataType: 'json',
                data: formData,
                success: function (data) {
                    $.LoadingOverlay("hide");
                    $("#verify-title").html(data.title);
                    $("#verify-details").html(data.message);

                    if (data.status == '1') {
                        if (allow_subscription_type == 'yes' && element == 'iuc_number') {
                            $('#bouquet').append(`<option value="change" data-amount="${amount}">Change Bouquet</option><option data-amount="${data.renewal_amount}" value="renew">Renew Bouquet</option>`);
                            $("#bouquet-div").show();
                            $("#bouquet").attr({ "required": true });
                        } else {
                            $("#bouquet-div").hide();
                            $("#bouquet").attr({ "required": false });
                        }

                        $("#continue_payment").show();
                    } else {
                        $("#continue_payment").hide();
                    }

                    bootstrap.Modal.getOrCreateInstance(document.getElementById('verify-modal')).show();
                }
            });
        }

        function submitForm() {
            var inputs = document.getElementById("initialize").getElementsByTagName("input");

            for (var i = 0; i < inputs.length; i++) {
                var input = inputs[i];
                if (input.hasAttribute("required") && input.value.trim() === "") {
                    alert("Please fill all inputs");
                    return;
                }
            }

            $.LoadingOverlay("show");
            document.forms["initialize"].submit();
        }

        $("#amount").keyup(function () {
            var has_variation = $('#product').find(':selected').data('has_variation');
            var product_id = $('#product').val();
            var variation_id = $('#variation').val();
            var amount = $('#amount').val();

            if (amount > 0) {
                var formData = has_variation == 'yes'
                    ? { variation_id: variation_id, amount: amount }
                    : { product_id: product_id, amount: amount };

                $.ajax({
                    url: "{{ url('customer-get-discount') }}",
                    method: 'POST',
                    dataType: 'json',
                    data: formData,
                    success: function (data) {
                        if (data.discount > 0) {
                            $('#discount').show();
                            $('#discount').html(data.message);
                        }
                    }
                });
            } else {
                $('#discount').hide();
            }
        });

        $(document).ready(function () {
            $('.modern-select2').each(function () {
                const $select = $(this);

                $select.wrap('<div class="position-relative"></div>').select2({
                    placeholder: $select.data('placeholder') || 'Select an option',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $select.parent()
                });
            });

            var variations = [];

            $('#product').on('change', function () {
                var fixed_price = $('#product').find(':selected').data('fixed_price');
                var has_variation = $('#product').find(':selected').data('has_variation');
                var system_price = $('#product').find(':selected').data('system_price');
                var allow_quantity = $('#product').find(':selected').data('allow_quantity');
                var max = $('#product').find(':selected').data('max');
                var min = $('#product').find(':selected').data('min');
                var quantity_graduation = $('#product').find(':selected').data('quantity_graduation');
                var product = $('#product').val();

                $("#verify-link").hide();
                $(".unique_element_div").hide();

                if (product == '') {
                    $('#variation-div').hide();
                    $('#amount-div').hide();
                    $('#quantity-div').hide();
                    return;
                }

                var image = $('#product').find(':selected').data('image');
                var title = $('#product').find(':selected').data('name');
                var description = $('#product').find(':selected').data('description');

                $('#product-image-div').show();
                $("#product-image").attr("src", image);
                $("#product-title").html(title);
                $("#product-description").html(description);

                if (has_variation == 'yes') {
                    $('#variation-div').show();
                    $('#amount-div').hide();
                    $("#amount").prop('readonly', false).val('');
                    $('#variation').find('option').not(':first').remove();
                    variations = [];

                    $.ajax({
                        url: "{{ url('customer-get-variations') }}/" + product,
                        success: function (data) {
                            if (data && data.length > 0) {
                                for (let t = 0; t < data.length; t++) {
                                    $('#variation').append(`<option value="${data[t].id}" data-min="${data[t].min}" data-max="${data[t].max}" data-isFixed="${data[t].fixed_price}" data-amount="${data[t].system_price}">${data[t].system_name}</option>`);
                                    variations.push({
                                        id: data[t].id,
                                        verifiable: data[t].verifiable,
                                        unique_element: data[t].unique_element,
                                        max: data[t].max,
                                        min: data[t].min,
                                        fixedPrice: data[t].fixed_price,
                                        variation_amount: data[t].system_price,
                                        discount: data[t].discount.discount > 0 ? data[t].discount.message : '',
                                        discount_rate: data[t].discount.discount
                                    });
                                }
                            }
                        }
                    });
                } else {
                    $('#amount-div').show();
                    $('#quantity option:not(:first)').remove();
                    $("#variation").hide();
                    $("#variation-div").hide();

                    if (fixed_price == 'yes') {
                        $("#amount").attr({ "max": "", "min": "" }).val(system_price).attr({ "readonly": "true" });
                    } else {
                        $("#amount").prop('readonly', false).attr({ "max": max, "min": min });
                    }
                }

                if (allow_quantity == 'yes') {
                    $('#quantity-div').show();
                    $('#quantity').show();
                    var data = quantity_graduation.split(",");

                    if (data && data.length > 0) {
                        for (let t = 0; t < data.length; t++) {
                            $('#quantity').append(`<option value="${data[t]}">${data[t]}</option>`);
                        }
                    }
                } else {
                    $('#quantity-div').hide();
                    $('#quantity').hide();
                }
            });

            $('#bouquet').on('change', function () {
                var amount = $('#bouquet').find(':selected').data('amount');
                var old_amount = $('#amount').val();
                if ($('#bouquet') == 'change') {
                    $('#amount').val(old_amount);
                } else {
                    $('#amount').val(amount);
                }
            });

            $('#variation').on('change', function () {
                $('#amount-div').show();
                $("#bouquet-div").hide();
                $("#bouquet").attr({ "required": false });

                var selected = variations.filter((item) => item.id == this.value);

                if (selected[0] && selected[0].verifiable == 'yes') {
                    $("#verify-link").show();
                    $(".unique_element_div").show();
                    $("#unique_element").attr({ "required": true });
                    $("#verify-link").html('Verify ' + selected[0].unique_element.replace("_", " "));
                } else {
                    $("#verify-link").hide();
                    $(".unique_element_div").hide();
                    $("#unique_element").attr({ "required": false });
                }

                if (selected[0] && selected[0].discount_rate > 0 && selected[0].fixedPrice == 'Yes') {
                    $("#discount").html(selected[0].discount).show();
                } else {
                    $("#discount").html("").hide();
                }

                if (selected[0] && selected[0].fixedPrice == 'Yes') {
                    $("#amount").attr({ "max": "", "min": "" }).val(selected[0].variation_amount).attr({ "readonly": "true" });
                } else if (selected[0]) {
                    $("#amount").prop('readonly', false).attr({ "max": selected[0].max, "min": selected[0].min });
                }
            });

            function hideAllUniqueElement() {
                $("#verify-link").hide();
                $(".unique_element_div").hide();
                $("#buy-button").show();
                $("#unique_element").attr({ "required": false });
            }

            function showAllUniqueElement() {
                $("#verify-link").show();
                $(".unique_element_div").show();
                $("#unique_element").attr({ "required": true });
            }
        });
    </script>
@endsection
