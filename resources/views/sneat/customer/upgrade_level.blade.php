@extends('sneat.layouts.app')

@section('title', 'Upgrade Account')

@php
    $user = auth()->user();
    $currentLevel = $user->customer?->level;
    $currentLevelId = $currentLevel->id ?? 0;
    $currency = getSettings()->currency ?? '₦';
    $upgradableLevels = $levels->where('id', '>', $currentLevelId);
@endphp

@section('content')
    @php
        $levelBenefitsMap = [];
        foreach ($levels as $level) {
            $levelBenefitsMap[$level->id] = $benefits->filter(function ($benefit) use ($level) {
                $customerLevels = is_array($benefit->customer_levels ?? null)
                    ? $benefit->customer_levels
                    : (json_decode($benefit->customer_levels ?? '[]', true) ?: []);

                $customerLevels = collect($customerLevels)->map(fn ($customerLevel) => (int) $customerLevel)->all();

                return in_array((int) $level->id, $customerLevels, true);
            })->values()->map(function ($benefit) {
                return [
                    'title' => trim(strip_tags((string) ($benefit->title ?? 'Benefit'))),
                    'content' => trim(strip_tags((string) $benefit->content)),
                ];
            })->filter(function ($benefit) {
                return !empty($benefit['title']) || !empty($benefit['content']);
            })->values()->all();
        }
    @endphp

    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="profile-hero mb-4 gateway-hero">
                <div class="profile-hero__meta">
                    <div class="profile-avatar"><i class="bx bx-up-arrow-alt"></i></div>
                    <div class="profile-meta">
                        <span class="gateway-hero__kicker">Account growth</span>
                        <strong>Upgrade your level</strong>
                        <span>Move to a higher plan and unlock the benefits attached to it.</span>
                    </div>
                </div>
                <div class="gateway-summary">
                    <div class="gateway-summary__card">
                        <span class="gateway-summary__label">Current level</span>
                        <span class="gateway-summary__value">{{ $currentLevel->name ?? 'Not assigned' }}</span>
                    </div>
                    <div class="gateway-summary__card">
                        <span class="gateway-summary__label">Wallet balance</span>
                        <span class="gateway-summary__value">{{ $currency . number_format(walletBalance($user), 2) }}</span>
                    </div>
                    <div class="gateway-summary__card">
                        <span class="gateway-summary__label">Available upgrades</span>
                        <span class="gateway-summary__value">{{ $upgradableLevels->count() }}</span>
                    </div>
                </div>
            </div>

            @include('sneat.layouts.alerts')

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card modern-admin-card h-100">
                        <div class="card-header">
                            <h3>Level comparison</h3>
                            <p>Review the available levels and the upgrade amount before proceeding.</p>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                @foreach($levels as $level)
                                    <div class="col-md-6">
                                        <div class="profile-side-card h-100">
                                            <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
                                                <div>
                                                    <div class="profile-badge mb-2">{{ $level->id <= $currentLevelId ? 'Current / lower' : 'Upgrade option' }}</div>
                                                    <h5 class="mb-1">{{ $level->name }}</h5>
                                                    <div class="gateway-helper">{{ $currency . number_format($level->upgrade_amount, 2) }} to upgrade</div>
                                                </div>
                                                <span class="avatar-initial rounded bg-label-primary p-3">
                                                    <i class="bx bx-award fs-4"></i>
                                                </span>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between gap-2">
                                                <div class="gateway-helper">
                                                    {{ count($levelBenefitsMap[$level->id]) }} benefit{{ count($levelBenefitsMap[$level->id]) === 1 ? '' : 's' }} available
                                                </div>
                                                @if(count($levelBenefitsMap[$level->id]) > 0)
                                                    <button
                                                        type="button"
                                                        class="btn btn-sm btn-label-primary"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#level-benefits-modal"
                                                        data-level-name="{{ $level->name }}"
                                                        data-level-benefits='@json($levelBenefitsMap[$level->id])'>
                                                        View benefits
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card profile-card h-100">
                        <div class="card-header">
                            <h3>Upgrade form</h3>
                            <p>Select the target level and confirm the debit from your wallet.</p>
                        </div>
                        <div class="card-body">
                            @if($upgradableLevels->isEmpty())
                                <div class="alert alert-light border mb-0">No higher level is currently available.</div>
                            @else
                                <form action="{{ route('customer.level.upgrade.process') }}" method="POST" autocomplete="off">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="profile-label" for="level">Select level</label>
                                        <select class="form-select form-select-{{ formControlSize() }}" name="level" id="level" required>
                                            <option value="">Select a level</option>
                                            @foreach($upgradableLevels as $level)
                                                <option value="{{ $level->id }}">{{ $level->name }} ({{ $currency . number_format($level->upgrade_amount, 2) }})</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="profile-side-card mb-3">
                                        <div class="profile-side-row">
                                            <span>Debit source</span>
                                            <strong>Wallet</strong>
                                        </div>
                                        <div class="profile-side-row">
                                            <span>Current balance</span>
                                            <strong>{{ $currency . number_format(walletBalance($user), 2) }}</strong>
                                        </div>
                                    </div>

                                    <div class="profile-footer">
                                        <button class="btn btn-admin-submit" type="submit">Upgrade now</button>
                                        <a href="{{ route('dashboard') }}" class="btn btn-label-secondary">Cancel</a>
                                    </div>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="level-benefits-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <div>
                        <div class="customer-services-modal__eyebrow">Level benefits</div>
                        <h5 class="modal-title mb-0" id="level-benefits-title">Benefits</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-3">
                    <div class="profile-side-card">
                        <div class="vstack gap-3" id="level-benefits-list"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        document.getElementById('level-benefits-modal')?.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const title = button?.getAttribute('data-level-name') || 'Benefits';
            const benefits = JSON.parse(button?.getAttribute('data-level-benefits') || '[]');
            const titleNode = document.getElementById('level-benefits-title');
            const listNode = document.getElementById('level-benefits-list');

            if (titleNode) {
                titleNode.textContent = title + ' benefits';
            }

            if (listNode) {
                listNode.innerHTML = benefits.length
                    ? benefits.map((benefit) => `
                        <div class="customer-benefit-item">
                            <div class="customer-benefit-item__title">${benefit.title || 'Benefit'}</div>
                            ${benefit.content ? `<div class="customer-benefit-item__body">${benefit.content}</div>` : ''}
                        </div>
                    `).join('')
                    : '<div class="text-muted">No benefits are attached to this level yet.</div>';
            }
        });
    </script>
@endsection
