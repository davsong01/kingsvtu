@php
    $navbarUser = auth()->user();
    if ($navbarUser?->type === 'customer') {
        app(\App\Services\AnnouncementNotificationService::class)->backfillForUser($navbarUser);
    }
    $notifications = $navbarUser?->notifications()?->latest()?->limit(5)->get() ?? collect();
    $unreadCount = $navbarUser?->unreadNotifications()?->count() ?? 0;
@endphp

<li class="nav-item dropdown-notifications navbar-dropdown dropdown me-1 me-xl-2">
    <a
        class="nav-link dropdown-toggle hide-arrow p-0"
        href="javascript:void(0);"
        data-bs-toggle="dropdown"
        data-bs-auto-close="outside"
        aria-expanded="false"
        aria-label="Notifications"
        title="Notifications"
    >
        <span class="position-relative d-inline-flex align-items-center justify-content-center">
            <i class="icon-base bx bx-bell icon-md"></i>
            @if($unreadCount > 0)
                <span class="badge rounded-pill bg-danger badge-dot badge-notifications border"></span>
            @endif
        </span>
    </a>

    <ul class="dropdown-menu dropdown-menu-end navbar-notification-menu p-0">
        <li class="dropdown-menu-header border-bottom">
            <div class="dropdown-header d-flex align-items-center gap-2 py-3 px-4">
                <div>
                    <h6 class="mb-0">Notifications</h6>
                    <small class="text-muted">{{ $unreadCount }} unread</small>
                </div>
            </div>
        </li>

        <li class="navbar-notification-menu__body">
            @forelse($notifications as $notification)
                @php
                    $data = $notification->data ?? [];
                    $isUnread = is_null($notification->read_at);
                @endphp
                <div class="navbar-notification-item {{ $isUnread ? 'is-unread' : '' }}">
                    <span class="navbar-notification-item__icon">
                        <i class="icon-base bx bx-bell"></i>
                    </span>
                    <div class="flex-grow-1 min-w-0">
                        <h6 class="mb-1 text-truncate">{{ $data['title'] ?? 'Notification' }}</h6>
                        <p class="navbar-notification-item__message mb-1 text-muted small">{{ \Illuminate\Support\Str::limit(strip_tags($data['message'] ?? ''), 95) }}</p>
                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                    </div>
                </div>
            @empty
                <div class="p-4 text-center text-muted">
                    <i class="icon-base bx bx-bell-off icon-md d-block mb-2"></i>
                    <div class="small">No notifications yet</div>
                </div>
            @endforelse
        </li>
    </ul>
</li>
