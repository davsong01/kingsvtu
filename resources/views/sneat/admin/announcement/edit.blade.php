@extends('sneat.layouts.app')

@section('title', 'Edit Announcement')

@section('page-css')
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.0-rc.2/dist/quill.snow.css" rel="stylesheet" />
@endsection

@php
    $editorContent = old('message', $announcement->message ?? '');
@endphp

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="admin-page-hero mb-4">
                <div>
                    <span class="admin-page-hero__kicker">Platform messaging</span>
                    <h1>Edit Announcement</h1>
                    <p>Refine the announcement title, status, type, and message.</p>
                </div>
                <a href="{{ route('announcement.index') }}" class="gateway-action">Back to announcements</a>
            </div>

            @include('sneat.layouts.alerts')

            <form action="{{ route('announcement.update', $announcement->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="row g-4">
                    <div class="col-xl-8">
                        <div class="modern-admin-card card h-100">
                            <div class="card-header">
                                <h3>Announcement details</h3>
                                <p>Update the live message without changing its destination.</p>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label class="modern-admin-label" for="title">Title</label>
                                        <input type="text" class="form-control form-control-{{ formControlSize() }}" id="title" name="title" value="{{ old('title', $announcement->title ?? '') }}" placeholder="Announcement title" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="modern-admin-label" for="status">Status</label>
                                        <select class="form-select form-select-{{ formControlSize() }}" name="status" id="status" required>
                                            <option value="">Select status</option>
                                            <option value="active" @selected(old('status', $announcement->status ?? '') === 'active')>Active</option>
                                            <option value="inactive" @selected(old('status', $announcement->status ?? '') === 'inactive')>Inactive</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="modern-admin-label" for="type">Type</label>
                                        <select class="form-select form-select-{{ formControlSize() }}" name="type" id="type" required>
                                            <option value="">Select type</option>
                                            <option value="scroll" @selected(old('type', $announcement->type ?? '') === 'scroll')>Scroll</option>
                                            <option value="popup" @selected(old('type', $announcement->type ?? '') === 'popup')>Popup</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="modern-admin-label" for="message">Message</label>
                                        <div class="admin-note-section">
                                            <div class="admin-note-section__heading">
                                                <span>Announcement content</span>
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
                                                <div id="announcement-editor" class="admin-editor__body">{!! $editorContent !!}</div>
                                            </div>
                                            <input type="hidden" name="message" id="announcement_message">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4">
                        <div class="modern-admin-card card h-100">
                            <div class="card-header">
                                <h3>Current details</h3>
                                <p>Quick reference for this announcement.</p>
                            </div>
                            <div class="card-body">
                                <div class="announcement-meta announcement-meta--stacked">
                                    <div class="announcement-meta__item">
                                        <span>Title</span>
                                        <strong>{{ $announcement->title }}</strong>
                                    </div>
                                    <div class="announcement-meta__item">
                                        <span>Type</span>
                                        <strong>{{ ucfirst($announcement->type ?? 'scroll') }}</strong>
                                    </div>
                                    <div class="announcement-meta__item">
                                        <span>Status</span>
                                        <strong>{{ ucfirst($announcement->status ?? 'inactive') }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modern-admin-footer mt-4">
                            <button class="btn btn-admin-submit" type="submit">Update announcement</button>
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
        const quill = new Quill('#announcement-editor', {
            theme: 'snow',
            placeholder: 'Enter announcement content...',
            modules: {
                toolbar: '#toolbar-container',
            },
        });

        const initialContent = {!! json_encode($editorContent) !!};
        if (initialContent) {
            quill.root.innerHTML = initialContent;
        }

        $('form').on('submit', function () {
            $('#announcement_message').val(document.querySelector('#announcement-editor .ql-editor').innerHTML);
        });
    </script>
@endsection
