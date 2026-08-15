@extends('sneat.layouts.app')

@section('title', 'Add Announcement')

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            

            @include('sneat.layouts.alerts')

            <form action="{{ route('announcement.store') }}" method="POST">
                @csrf
                <div class="row g-4">
                    <div class="col-xl-8">
                        <div class="modern-admin-card card h-100">
                            <div class="card-header">
                                <h3>Announcement details</h3>
                                <p>Keep the title, type, and status aligned with the content you are publishing.</p>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label class="modern-admin-label" for="title">Title</label>
                                        <input type="text" class="form-control form-control-{{ formControlSize() }}" id="title" name="title" value="{{ old('title') }}" placeholder="Announcement title" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="modern-admin-label" for="status">Status</label>
                                        <select class="form-select form-select-{{ formControlSize() }}" name="status" id="status" required>
                                            <option value="">Select status</option>
                                            <option value="active" @selected(old('status') === 'active')>Active</option>
                                            <option value="inactive" @selected(old('status') === 'inactive')>Inactive</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="modern-admin-label" for="type">Type</label>
                                        <select class="form-select form-select-{{ formControlSize() }}" name="type" id="type" required>
                                            <option value="">Select type</option>
                                            <option value="scroll" @selected(old('type') === 'scroll')>Scroll</option>
                                            <option value="popup" @selected(old('type') === 'popup')>Popup</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="modern-admin-label" for="message">Message</label>
                                        <textarea class="form-control form-control-{{ formControlSize() }} announcement-textarea" id="message" name="message" rows="10" placeholder="Write the announcement content here">{{ old('message') }}</textarea>
                                        <div class="gateway-helper mt-2">Plain HTML is supported if you need links or simple formatting.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4">
                        <div class="modern-admin-card card h-100">
                            <div class="card-header">
                                <h3>Publishing note</h3>
                                <p>Announcements are visible based on status and type.</p>
                            </div>
                            <div class="card-body">
                                <div class="announcement-side-note">
                                    Use <strong>popup</strong> for modal-style alerts and <strong>scroll</strong> for banner messages.
                                </div>
                            </div>
                        </div>

                        <div class="modern-admin-footer mt-4">
                            <button class="btn btn-admin-submit" type="submit">Save announcement</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
