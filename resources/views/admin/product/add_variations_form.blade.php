<div class="modal fade variation-modal" id="primary" tabindex="-1" aria-labelledby="variationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="variation-modal__header">
                <div>
                    <span class="variation-modal__eyebrow">Product variations</span>
                    <h5 class="variation-modal__title" id="variationModalLabel">Add variations for {{ $product->name }}</h5>
                    <p class="variation-modal__subtitle">Create one or more variation rows, then submit them together.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('manual.variations.add', $product->id) }}" method="POST" enctype="multipart/form-data" id="variation-modal-form">
                @csrf
                <div class="modal-body variation-modal__body">
                    <div id="mode-holder" class="variation-modal__stack">
                        <div class="variation-modal__entry row g-2 align-items-end" id="mode-0">
                            <div class="col-md-3">
                                <label class="modern-admin-label" for="system_name_0">System Name</label>
                                <input type="text" class="form-control form-control-{{ formControlSize() }} variation-modal__control" id="system_name_0" name="system_name[]" placeholder="Variation name" required>
                            </div>
                            <div class="col-md-3">
                                <label class="modern-admin-label" for="slug_0">Slug</label>
                                <input type="text" class="form-control form-control-{{ formControlSize() }} variation-modal__control" id="slug_0" name="slug[]" placeholder="Variation slug" required>
                            </div>
                            <div class="col-md-2">
                                <label class="modern-admin-label" for="fixed_price_0">Fixed Price</label>
                                <select class="form-select form-select-{{ formControlSize() }} variation-modal__control" name="fixed_price[]" id="fixed_price_0" required>
                                    <option value="">Select</option>
                                    <option value="Yes" {{ old('fixed_price') == 'Yes' ? 'selected' : '' }}>Yes</option>
                                    <option value="No" {{ old('fixed_price') == 'No' ? 'selected' : '' }}>No</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="modern-admin-label" for="status_0">Status</label>
                                <select class="form-select form-select-{{ formControlSize() }} variation-modal__control" name="status[]" id="status_0" required>
                                    <option value="">Select</option>
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="modern-admin-label" for="multistep_0">Use Multistep</label>
                                <select class="form-select form-select-{{ formControlSize() }} variation-modal__control" name="multistep[]" id="multistep_0">
                                    <option value="">Select</option>
                                    <option value="yes" {{ old('multistep') == 'yes' ? 'selected' : '' }}>Yes</option>
                                    <option value="no" {{ old('multistep') == 'no' ? 'selected' : '' }}>No</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="modern-admin-label" for="ussd_string_0">USSD String</label>
                                <input type="text" class="form-control form-control-{{ formControlSize() }} variation-modal__control" id="ussd_string_0" name="ussd_string[]" value="{{ old('ussd_string') }}" placeholder="Optional USSD string">
                            </div>
                            <div class="col-md-2">
                                <label class="modern-admin-label" for="min_0">Min Amount</label>
                                <input type="number" class="form-control form-control-{{ formControlSize() }} variation-modal__control" id="min_0" name="min[]" value="{{ old('min') }}" placeholder="Min">
                            </div>
                            <div class="col-md-2">
                                <label class="modern-admin-label" for="max_0">Max Amount</label>
                                <input type="number" class="form-control form-control-{{ formControlSize() }} variation-modal__control" id="max_0" name="max[]" value="{{ old('max') }}" placeholder="Max">
                            </div>
                            <div class="col-md-2">
                                <label class="modern-admin-label" for="system_price_0">System Price ({!! getSettings()['currency'] !!})</label>
                                <input type="number" class="form-control form-control-{{ formControlSize() }} variation-modal__control" id="system_price_0" name="system_price[]" value="" placeholder="Variation price" required>
                            </div>
                            <div class="col-md-2">
                                <label class="modern-admin-label" for="datasize_0">Datasize</label>
                                <input type="number" class="form-control form-control-{{ formControlSize() }} variation-modal__control" id="datasize_0" name="datasize[]" value="" placeholder="Variation datasize">
                            </div>
                            @foreach($customerlevel as $level)
                                <div class="col-md-3">
                                    <label class="modern-admin-label" for="level_{{ $level->id }}_0">
                                        {{ $level->name }}
                                        @if($product->category->discount_type == 'flat')
                                            Discounted Price ({!! getSettings()['currency'] !!})
                                        @else
                                            Discounted Percentage (%)
                                        @endif
                                    </label>
                                    <input type="number" class="form-control form-control-{{ formControlSize() }} variation-modal__control" id="level_{{ $level->id }}_0" step=".01" name="level[{{ $level->id }}][]" value="" required>
                                </div>
                            @endforeach

                            <div class="col-md-2">
                                <div class="variation-modal__button-cell">
                                    <button class="btn btn-sm btn-admin-submit variation-modal__add-btn" type="button" id="add-mode">
                                        <i class="bx bx-plus me-1"></i>
                                        Add More
                                    </button>
                                </div>
                            </div>
                            <div class="col-12">
                                <hr class="variation-modal__divider">
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                </div>
                <div class="modal-footer variation-modal__footer">
                    <button type="button" class="gateway-action" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-admin-submit">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
