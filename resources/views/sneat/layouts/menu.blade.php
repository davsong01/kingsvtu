<!-- Menu -->

<aside id="layout-menu" class="layout-menu menu-vertical menu">
  @php
    $dashboardLogo = getSettings()->dashboard_logo ?? getSettings()->logo ?? null;
  @endphp
  <div class="app-brand demo">
    <a href="/" class="app-brand-link">
      <span class="app-brand-logo demo">
        <div class="brand-logo">
          @if($dashboardLogo)
            <img src="{{ asset($dashboardLogo) }}" alt="KingsVTU" />
          @endif
        </div>
      </span>
    </a>
  </div>

  <ul class="menu-inner py-1">
    @include('shared.customer-menu-items', ['variant' => 'sneat'])
  </ul>
</aside>
