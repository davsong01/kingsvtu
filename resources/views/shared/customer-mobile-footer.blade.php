@auth
    @if(auth()->user()->type === 'customer')
        @php
            $mobileNavItems = customerMobileNavItems();
            $mobileNavAccent = getSettings()?->active_color ?: '#0B7D4F';
        @endphp

        <nav
            class="customer-mobile-nav"
            style="--customer-mobile-accent: {{ $mobileNavAccent }};"
            aria-label="Customer quick navigation">
            <div class="customer-mobile-nav__inner">
                @foreach($mobileNavItems as $item)
                    @php($isActive = menuItemIsActive($item['active_paths']))
                    <a
                        href="{{ $item['href'] }}"
                        class="customer-mobile-nav__item {{ $isActive ? 'is-active' : '' }}"
                        @if(!empty($item['modal_target'])) data-bs-toggle="modal" data-bs-target="{{ $item['modal_target'] }}" @endif
                        @if($isActive) aria-current="page" @endif>
                        <span class="customer-mobile-nav__icon">
                            <i class="{{ menuIconClass($item['icon_key']) }}" aria-hidden="true"></i>
                        </span>
                        <span class="customer-mobile-nav__label">{{ $item['label'] }}</span>
                        @if($isActive)
                            <span class="customer-mobile-nav__dot" aria-hidden="true"></span>
                        @endif
                    </a>
                @endforeach
            </div>
        </nav>

        <div class="modal fade customer-services-modal" id="customer-services-modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header border-0 pb-0">
                        
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body pt-3">
                        <div class="customer-services-modal__services mt-4">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h6 class="mb-0">Services</h6>
                                <span class="badge bg-label-secondary">{{ getCategories()->count() }} available</span>
                            </div>
                            <div class="customer-services-modal__service-list">
                                @foreach(getCategories() as $category)
                                    <a href="{{ route('open.transaction.page', $category->slug) }}" class="customer-services-modal__service">
                                        <span class="customer-services-modal__service-icon">
                                            <i class="{{ menuIconClass(modernServiceIconKey($category), 'sneat') }}" aria-hidden="true"></i>
                                        </span>
                                        <span class="flex-grow-1">
                                            <span class="d-block fw-semibold text-body">{{ $category->display_name }}</span>
                                            <small class="text-muted">Open purchase page</small>
                                        </span>
                                        <i class="bx bx-chevron-right text-muted"></i>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endauth
