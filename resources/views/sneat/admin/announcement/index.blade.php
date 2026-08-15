@extends('sneat.layouts.app')

@section('title', 'Announcement')

@php
    $announcementSource = $announcements ?? [];
    $announcementItems = $announcementSource instanceof \Illuminate\Pagination\LengthAwarePaginator
        ? collect($announcementSource->items())
        : collect($announcementSource);
    $totalAnnouncements = $announcementItems->count();
    $activeAnnouncements = $announcementItems->where('status', 'active')->count();
    $scrollAnnouncements = $announcementItems->where('type', 'scroll')->count();
    $popupAnnouncements = $announcementItems->where('type', 'popup')->count();
@endphp

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            

            <div class="row g-3 g-xl-4 mb-4">
                <div class="col-md-3">
                    <div class="admin-settings-stat">
                        <div class="admin-settings-stat__label">Total announcements</div>
                        <div class="admin-settings-stat__value">{{ $totalAnnouncements }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="admin-settings-stat">
                        <div class="admin-settings-stat__label">Active</div>
                        <div class="admin-settings-stat__value">{{ $activeAnnouncements }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="admin-settings-stat">
                        <div class="admin-settings-stat__label">Scroll</div>
                        <div class="admin-settings-stat__value">{{ $scrollAnnouncements }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="admin-settings-stat">
                        <div class="admin-settings-stat__label">Popup</div>
                        <div class="admin-settings-stat__value">{{ $popupAnnouncements }}</div>
                    </div>
                </div>
            </div>

            @include('sneat.layouts.alerts')

            <div class="row g-4">
                @forelse($announcementItems as $announcement)
                    @php
                        $messagePreview = \Illuminate\Support\Str::limit(trim(strip_tags((string) ($announcement->message ?? ''))), 180);
                    @endphp
                    <div class="col-12 col-xl-6">
                        <div class="modern-admin-card card announcement-card h-100">
                            <div class="card-header announcement-card__header">
                                <div>
                                    <h3>{{ $announcement->title }}</h3>
                                    <p>Use this message to broadcast important updates to users.</p>
                                </div>
                                <div class="d-flex align-items-center gap-2 flex-wrap justify-content-end">
                                    <span class="gateway-badge {{ ($announcement->status ?? 'inactive') === 'active' ? 'gateway-badge--active' : 'gateway-badge--inactive' }}">
                                        {{ ucfirst($announcement->status ?? 'inactive') }}
                                    </span>
                                    <span class="gateway-badge gateway-badge--blue">{{ ucfirst($announcement->type ?? 'scroll') }}</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="announcement-meta mb-3">
                                    <div class="announcement-meta__item">
                                        <span>Type</span>
                                        <strong>{{ ucfirst($announcement->type ?? 'scroll') }}</strong>
                                    </div>
                                    <div class="announcement-meta__item">
                                        <span>Status</span>
                                        <strong>{{ ucfirst($announcement->status ?? 'inactive') }}</strong>
                                    </div>
                                </div>

                                <div class="announcement-message">
                                    {!! $messagePreview !== '' ? e($messagePreview) : '<span class="text-muted">No announcement content yet.</span>' !!}
                                </div>

                                <div class="gateway-row-actions mt-4">
                                    <a href="{{ route('announcement.edit', $announcement->id) }}" class="gateway-action">View / Edit</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="modern-admin-card card">
                            <div class="card-body">
                                <div class="gateway-helper">No announcements found yet. Create one to get started.</div>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>

            @if($announcementSource instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="mt-4">
                    {{ $announcementSource->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
