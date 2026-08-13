@extends('sneat.layouts.app')

@section('title', 'Edit ' . $levelbenefit->title)

@section('page-css')
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.0-rc.2/dist/quill.snow.css" rel="stylesheet" />
@endsection

@section('content')
    @php
        $selectedLevels = collect(old('customer_levels', $levelbenefit->customer_levels ?? []))->map(fn ($level) => (int) $level)->all();
    @endphp

    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="admin-page-hero mb-4">
                <div>
                    <span class="admin-page-hero__kicker">Level configuration</span>
                    <h1>Edit {{ $levelbenefit->title }}</h1>
                    <p>Adjust the benefit copy or update the customer levels that should see it.</p>
                </div>
            </div>

            @include('sneat.layouts.alerts')

            <form action="{{ route('levelbenefit.update', $levelbenefit->id) }}" method="POST" id="benefit-form">
                @csrf
                @method('PATCH')
                <div class="row g-4">
                    <div class="col-xl-8">
                        <div class="modern-admin-card card h-100">
                            <div class="card-header">
                                <h3>Benefit content</h3>
                                <p>Refine the headline and rich content.</p>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="modern-admin-label" for="title">Title</label>
                                        <input type="text" class="form-control form-control-{{ formControlSize() }}" name="title" id="title" value="{{ old('title', $levelbenefit->title) }}" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="modern-admin-label">Content</label>
                                        <div class="modern-quill-shell">
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
                                                    <button class="ql-list" value="ordered"></button>
                                                    <button class="ql-list" value="bullet"></button>
                                                </span>
                                                <span class="ql-formats">
                                                    <button class="ql-link"></button>
                                                    <button class="ql-clean"></button>
                                                </span>
                                            </div>
                                            <div id="benefit-editor" class="modern-quill-editor">{!! old('content', $levelbenefit->content) !!}</div>
                                        </div>
                                        <input type="hidden" name="content" id="content">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4">
                        <div class="modern-admin-card card h-100">
                            <div class="card-header">
                                <h3>Target levels</h3>
                                <p>Select the levels that should receive this benefit.</p>
                            </div>
                            <div class="card-body">
                                <div class="shop-request-form-grid">
                                    @foreach($levels as $level)
                                        <div class="form-check">
                                            <input class="form-check-input form-check-input-{{ checkBoxControlSize() }}" type="checkbox" name="customer_levels[]" id="level-{{ $level->id }}" value="{{ $level->id }}" @checked(in_array($level->id, $selectedLevels, true))>
                                            <label class="form-check-label" for="level-{{ $level->id }}">{{ $level->name }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="modern-admin-footer mt-4">
                            <button class="btn btn-admin-submit" type="submit">Update benefit</button>
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
            placeholder: 'Enter content...',
            modules: {
                toolbar: '#toolbar-container',
            },
        });

        $('#benefit-form').on('submit', function () {
            $('#content').val(document.querySelector('#benefit-editor .ql-editor').innerHTML);
        });
    </script>
@endsection
