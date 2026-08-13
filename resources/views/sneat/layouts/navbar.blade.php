@php
  $navbarUser = auth()->user();
  $navbarFullName = trim(collect([
    $navbarUser?->firstname,
    $navbarUser?->middlename,
    $navbarUser?->lastname,
  ])->filter()->implode(' '));
  $navbarFullName = $navbarFullName ?: ($navbarUser?->username ?: 'Customer');
  $navbarInitialParts = collect([$navbarUser?->firstname, $navbarUser?->lastname])->filter()->values();

  if ($navbarInitialParts->isEmpty()) {
    $navbarInitialParts = collect(preg_split('/\s+/', $navbarFullName))->filter()->values();
  }

  $navbarInitials = $navbarInitialParts
    ->take(2)
    ->map(fn ($part) => Str::upper(Str::substr($part, 0, 1)))
    ->implode('');
  $navbarInitials = $navbarInitials ?: 'CU';
  $navbarAvatarColors = ['#2563eb', '#0891b2', '#059669', '#d97706', '#dc2626', '#4f46e5', '#0f766e'];
  $navbarAvatarKey = $navbarUser?->id ?: $navbarFullName;
  $navbarAvatarColor = $navbarAvatarColors[abs(crc32((string) $navbarAvatarKey)) % count($navbarAvatarColors)];
@endphp

<div class="header-navbar-shadow"></div>
<nav class="layout-navbar container-xxl navbar-detached navbar navbar-expand-lg align-items-center bg-navbar-theme" id="layout-navbar">
  <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
    <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
      <i class="icon-base bx bx-menu icon-md"></i>
    </a>
  </div>

  <div class="sneat-navbar-welcome">
    <span>Welcome,</span>
    <span class="sneat-navbar-welcome__name">{{ $navbarUser?->username ?: $navbarFullName }}</span>
  </div>

  <div class="sneat-navbar-actions ms-auto">
    <button
      type="button"
      class="sneat-icon-btn"
      id="sneat-theme-toggle"
      aria-label="Toggle light and dark theme">
      <i class="icon-base bx bx-moon icon-md" id="sneat-theme-toggle-icon"></i>
    </button>

    <ul class="navbar-nav flex-row align-items-center gap-2 ms-2">
      @include('sneat.layouts.notifications')

      <li class="nav-item navbar-dropdown dropdown-user dropdown">
        <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Open account menu">
          <div class="avatar">
            <span class="navbar-user-initials" style="background-color: {{ $navbarAvatarColor }};">{{ $navbarInitials }}</span>
          </div>
        </a>
        <ul class="dropdown-menu dropdown-menu-end navbar-user-menu">
          <li>
            <a class="dropdown-item py-3" href="{{ route('profile.edit') }}">
              <div class="d-flex">
                <div class="flex-shrink-0 me-3">
                  <div class="avatar">
                    <span class="navbar-user-initials navbar-user-initials--large" style="background-color: {{ $navbarAvatarColor }};">{{ $navbarInitials }}</span>
                  </div>
                </div>
                <div class="flex-grow-1 min-w-0">
                  <h6 class="mb-1 text-truncate">{{ $navbarFullName }}</h6>
                  <small class="navbar-user-email text-body-secondary">{{ $navbarUser?->email }}</small>
                </div>
              </div>
            </a>
          </li>
          <li>
            <div class="dropdown-divider my-1"></div>
          </li>
          <li>
            <a class="dropdown-item" href="{{ route('profile.edit') }}">
              <i class="icon-base bx bx-user icon-md me-3"></i><span>My Profile</span>
            </a>
          </li>
          @if($navbarUser?->type !== 'admin')
            <li>
              <a class="dropdown-item" href="{{ route('customer.load.wallet') }}">
                <i class="icon-base bx bx-wallet icon-md me-3"></i><span>Fund Wallet</span>
              </a>
            </li>
            <li>
              <a class="dropdown-item" href="{{ route('customer.transaction.history') }}">
                <i class="icon-base bx bx-receipt icon-md me-3"></i><span>Transaction History</span>
              </a>
            </li>
          @endif
          <li>
            <div class="dropdown-divider my-1"></div>
          </li>
          <li>
            <form action="{{ route('logout') }}" method="POST">
              @csrf
              <button class="dropdown-item text-danger" type="submit">
                <i class="icon-base bx bx-power-off icon-md me-3"></i><span>Log Out</span>
              </button>
            </form>
          </li>
        </ul>
      </li>
    </ul>
  </div>
</nav>
