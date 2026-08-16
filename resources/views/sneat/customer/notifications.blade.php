@extends('sneat.layouts.app')

@section('title', 'Notifications')

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="profile-hero mb-4 gateway-hero">
                <div class="profile-hero__meta">
                    <div class="profile-avatar"><i class="bx bx-bell"></i></div>
                    <div class="profile-meta">
                        <span class="gateway-hero__kicker">Announcements</span>
                        <strong>Notifications</strong>
                        <span>Your announcement updates and alerts appear here.</span>
                    </div>
                </div>
                <div class="gateway-summary">
                    <div class="gateway-summary__card">
                        <span class="gateway-summary__label">Unread</span>
                        <span class="gateway-summary__value">{{ $unreadCount }}</span>
                    </div>
                </div>
            </div>

            <div class="card modern-admin-card">
                <div class="card-header d-flex align-items-center justify-content-between gap-3">
                    <div>
                        <h3 class="mb-1">Announcement notifications</h3>
                        <p class="mb-0">Linked directly to announcements published by the admin.</p>
                    </div>
                    @if($unreadCount > 0)
                        <form action="{{ route('customer.notifications.read-all') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-admin-submit">Mark all as read</button>
                        </form>
                    @endif
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @forelse($notifications as $notification)
                            @php
                                $data = $notification->data ?? [];
                                $isUnread = is_null($notification->read_at);
                            @endphp
                            <div id="notification-{{ $notification->id }}" class="list-group-item border rounded-4 mb-3 {{ $isUnread ? 'bg-light' : '' }}">
                                <div class="d-flex gap-3">
                                    <span class="navbar-notification-item__icon">
                                        <i class="bx bx-megaphone"></i>
                                    </span>
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-start justify-content-between gap-3">
                                            <div>
                                                <h6 class="mb-1">{{ $data['title'] ?? 'Announcement' }}</h6>
                                                <p class="mb-2 text-muted">{{ \Illuminate\Support\Str::limit(strip_tags($data['message'] ?? ''), 180) }}</p>
                                                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                            </div>
                                            @if($isUnread)
                                                <form action="{{ route('customer.notifications.read', $notification->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-label-primary">Mark as read</button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="alert alert-light border mb-0">No notifications yet.</div>
                        @endforelse
                    </div>

                    @if($notifications instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        <div class="mt-4">
                            {{ $notifications->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
