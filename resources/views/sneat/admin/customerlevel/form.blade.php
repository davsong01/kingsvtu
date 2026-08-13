@extends('sneat.layouts.app')

@php
    $isEdit = !empty($customerlevel?->id);
    $pageTitle = $isEdit ? 'Edit Customer Level' : 'Add Customer Level';
    $formAction = $isEdit ? route('customerlevel.update', $customerlevel->id) : route('customerlevel.store');
    $selectedStatus = old('status', isset($customerlevel) ? (string) $customerlevel->status : '');
    $selectedMakeApi = old('make_api_level', $customerlevel->make_api_level ?? '');
    $editorContent = old('extra_benefit', $customerlevel->extra_benefit ?? '');
@endphp

@section('title', $pageTitle)

@section('page-css')
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.0-rc.2/dist/quill.snow.css" rel="stylesheet" />
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="admin-page-hero mb-4">
                <div>
                    <span class="admin-page-hero__kicker">Level management</span>
                    <h1>{{ $pageTitle }}</h1>
                    <p>{{ $isEdit ? 'Adjust the level details and upgrade content from the same clean form used for new entries.' : 'Create a new customer level using the same form layout used for edits.' }}</p>
                </div>
                <a href="{{ route('customerlevel.index') }}" class="gateway-action">Back to levels</a>
            </div>

            @include('sneat.layouts.alerts')

            <form action="{{ $formAction }}" method="POST" id="customer-level-form">
                @csrf
                @if($isEdit)
                    @method('PATCH')
                @endif

                <div class="row g-4">
                    <div class="col-xl-12">
                        <div class="modern-admin-card card h-100">
                            <div class="card-header">
                                <h3>Level details</h3>
                                <p>Capture the public level name, order, and amount required to upgrade.</p>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="name" class="modern-admin-label">Name</label>
                                        <input type="text" class="form-control form-control-{{ formControlSize() }}" id="name" name="name" value="{{ old('name', $customerlevel->name ?? '') }}" placeholder="Enter name" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="order" class="modern-admin-label">Order</label>
                                        <input type="number" class="form-control form-control-{{ formControlSize() }}" id="order" name="order" value="{{ old('order', $customerlevel->order ?? '') }}" placeholder="Enter order" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="upgrade_amount" class="modern-admin-label">Upgrade Amount</label>
                                        <input type="text" class="form-control form-control-{{ formControlSize() }}" id="upgrade_amount" name="upgrade_amount" value="{{ old('upgrade_amount', $customerlevel->upgrade_amount ?? '') }}" placeholder="Enter upgrade amount" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="status" class="modern-admin-label">Status</label>
                                        <select class="form-select form-select-{{ formControlSize() }}" name="status" id="status" required>
                                            <option value="">Select status</option>
                                            <option value="1" @selected($selectedStatus === '1')>Active</option>
                                            <option value="0" @selected($selectedStatus === '0')>Inactive</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="make_api_level" class="modern-admin-label">Make API Level</label>
                                        <select class="form-select form-select-{{ formControlSize() }}" name="make_api_level" id="make_api_level" required>
                                            <option value="">Select option</option>
                                            <option value="yes" @selected($selectedMakeApi === 'yes')>Yes</option>
                                            <option value="no" @selected($selectedMakeApi === 'no')>No</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-12">
                        <div class="modern-admin-card card h-100">
                            <div class="card-header">
                                <h3>Extra benefit</h3>
                                <p>Write the benefit text shown to customers during upgrade.</p>
                            </div>
                            <div class="card-body">
                                <div class="admin-note-section">
                                    <div class="admin-note-section__heading">
                                        <span>Benefit content</span>
                                        <small>Rich text supported</small>
                                    </div>
                                    <div class="admin-editor">
                                        <div id="toolbar-container">
                                            <span class="ql-formats">
                                                <select class="ql-font"></select>
                                                <select class="ql-size"></select>
                                            </span>
                                            <span class="ql-formats">
                                                <button class="ql-bold"></button>
                                                <button class="ql-italic"></button>
                                                <button class="ql-underline"></button>
                                                <button class="ql-strike"></button>
                                            </span>
                                            <span class="ql-formats">
                                                <select class="ql-color"></select>
                                                <select class="ql-background"></select>
                                            </span>
                                            <span class="ql-formats">
                                                <button class="ql-list" value="ordered"></button>
                                                <button class="ql-list" value="bullet"></button>
                                            </span>
                                            <span class="ql-formats">
                                                <button class="ql-link"></button>
                                                <button class="ql-clean"></button>
                                            </span>
                                        </div>
                                        <div id="benefit-editor" class="admin-editor__body">{!! $editorContent !!}</div>
                                    </div>
                                    <input type="hidden" name="extra_benefit" id="extra_benefit">
                                </div>
                            </div>
                        </div>

                        <div class="modern-admin-footer mt-4">
                            <button class="btn btn-admin-submit" type="submit">{{ $isEdit ? 'Update level' : 'Save level' }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('page-script')
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.0-rc.2/dist/quill.js"></script>
    <script>
        const quill = new Quill('#benefit-editor', {
            theme: 'snow',
            placeholder: 'Enter extra benefit...',
            modules: {
                toolbar: '#toolbar-container',
            },
        });

        const initialContent = {!! json_encode($editorContent) !!};
        if (initialContent) {
            quill.root.innerHTML = initialContent;
        }

        $('#customer-level-form').on('submit', function () {
            $('#extra_benefit').val(document.querySelector('#benefit-editor .ql-editor').innerHTML);
        });
    </script>
@endsection
