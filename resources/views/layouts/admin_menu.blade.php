 <div class="main-menu menu-fixed menu-dark menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="navbar-header">
        <ul class="nav navbar-nav flex-row">
            <li class="nav-item mr-auto"><a class="navbar-brand" href="/">
                    {{-- <div class="brand-logo"><img class="logo" src="{{ asset(getSettings()->logo ) }}" /></div> --}}
                    <div class="brand-logo"><img style="height: auto;" class="logo" src="{{ asset('site/dashboard_logo.jpeg')}}" /></div>

                    <h2 class="brand-text mb-0"></h2>
                </a></li>
            <li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse"><i class="bx bx-x d-block d-xl-none font-medium-4 primary"></i><i class="toggle-icon bx bx-disc font-medium-4 d-none d-xl-block primary" data-ticon="bx-disc"></i></a></li>
        </ul>
    </div>
    <div class="shadow-bottom"></div>
    <div class="main-menu-content" style="margin-top: 20px;" >
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation" data-icon-style="lines">
            <li class="{{ Route::is('dashboard') ? 'active' : ''}} nav-item"><a href="{{ route('dashboard') }}"><i class="menu-livicon" data-icon="settings"></i><span class="menu-title" data-i18n="Form Layout"> Dashboard</span></a>
            </li>
            <li class="nav-item"><a href="#"><i class="bx bx-folder-open" data-icon="check"></i><span class="menu-title" data-i18n="Form Elements">Catalogue</span></a>
                <ul class="menu-content">
                    <li class="{{ Route::is('api.*') ? 'active' : '' }}"><a href="{{ route('api.index') }}"><i class="bx bx-right-arrow-alt"></i><span class="menu-item" >API Providers</span></a>
                    </li>
                    <li class="{{ Route::is('category.*') ? 'active' : '' }}"><a href="{{ route('category.index') }}"><i class="bx bx-right-arrow-alt"></i><span class="menu-item" data-i18n="Input Groups">Categories</span></a>
                    </li>
                    <li class="{{ Route::is('product.*') ? 'active' : '' }}"><a href="{{ route('product.index') }}"><i class="bx bx-right-arrow-alt"></i><span class="menu-item" data-i18n="Input">Products</span></a>
                    </li>
                </ul>
            </li>
            <li class="nav-item"><a href="#"><i class="bx bx-folder-open" data-icon="check"></i><span class="menu-title" data-i18n="Form Elements">Customers</span></a>
                <ul class="menu-content">
                    <li class="{{ Route::is('customers') ? 'active' : '' }}"><a href="{{ route('customers') }}"><i class="bx bx-right-arrow-alt"></i><span class="menu-item" >All customers</span></a>
                    </li>
                    <li class="{{ Route::is('customers.active.*') ? 'active' : '' }}"><a href="{{ route('customers.active', 'active') }}"><i class="bx bx-right-arrow-alt"></i><span class="menu-item" data-i18n="Input">Active Customers</span></a>
                    </li>
                    <li class="{{ Request::is('customers-suspended/suspended') ? 'active' : '' }}"><a href="{{ route('customers.suspended','suspended') }}"><i class="bx bx-right-arrow-alt"></i><span class="menu-item" data-i18n="Input Groups">Suspended</span></a>
                    </li>
                    <li class=""><a href="{{ request()->route()->getPrefix() }}/customers/email-blacklist"><i class="bx bx-right-arrow-alt"></i><span class="menu-item" data-i18n="Input Groups">Blacklist Emails</span></a>
                    </li>
                    <li class="{{ Route::is('customers/.*') ? 'active' : '' }}"><a href="{{ request()->route()->getPrefix() }}/customers/phone-blacklist"><i class="bx bx-right-arrow-alt"></i><span class="menu-item" data-i18n="Input Groups">Blacklist Phones</span></a>
                    </li>

                    <li class="{{ Route::is('customerlevel.*') ? 'active' : '' }}"><a href="{{ route('customerlevel.index') }}"><i class="bx bx-right-arrow-alt"></i><span class="menu-item" data-i18n="Input Groups">Customer Levels</span></a>
                    </li>
                </ul>
            </li>
            <li class="nav-item"><a href="#"><i class="bx bx-folder-open" data-icon="check"></i><span class="menu-title" data-i18n="Form Elements">User Management</span></a>
                <ul class="menu-content">
                    <li class="{{ Route::is('admins.*') ? 'active' : '' }}"><a href="{{ request()->route()->getPrefix() }}/admins"><i class="bx bx-right-arrow-alt"></i><span class="menu-item" >All Admins</span></a>
                    </li>
                    {{-- <li class="{{ Route::is('customers.*') ? 'active' : '' }}"><a href="{{ request()->route()->getPrefix() }}/customers/active"><i class="bx bx-right-arrow-alt"></i><span class="menu-item" data-i18n="Input">Active Customers</span></a>
                    </li>
                    <li class="{{ Route::is('customers') ? 'active' : '' }}"><a href="{{ request()->route()->getPrefix() }}/customers/api"><i class="bx bx-right-arrow-alt"></i><span class="menu-item" data-i18n="Input">API Customers</span></a>
                    </li>
                    <li class="{{ Route::is('customers.*') ? 'active' : '' }}"><a href="{{ request()->route()->getPrefix() }}/customers/suspended"><i class="bx bx-right-arrow-alt"></i><span class="menu-item" data-i18n="Input Groups">Suspended</span></a>
                    </li>
                    <li class="{{ Route::is('customers.*') ? 'active' : '' }}"><a href="{{ request()->route()->getPrefix() }}/customers/email-blacklist"><i class="bx bx-right-arrow-alt"></i><span class="menu-item" data-i18n="Input Groups">Blacklist Emails</span></a>
                    </li>
                    <li class="{{ Route::is('customers.*') ? 'active' : '' }}"><a href="{{ request()->route()->getPrefix() }}/customers/phone-blacklist"><i class="bx bx-right-arrow-alt"></i><span class="menu-item" data-i18n="Input Groups">Blacklist Phones</span></a>
                    </li> --}}
                </ul>
            </li>
            <li class="{{ Request::path() == 'profile' ? 'active' : '' }}"><a href="{{ route('profile.edit')}}"><i class="menu-livicon" data-icon="priority-low"></i><span class="menu-title">My Profile</span></a></li>
            <li class="{{ Request::path() == 'settings' ? 'active' : '' }}"><a href="{{ route('settings.edit')}}"><i class="menu-livicon" data-icon="priority-low"></i><span class="menu-title">General Settings</span></a></li>

            <li class=""><a href="{{ route('logout')}}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="bx bx-power-off mr-50" data-icon="priority-low"></i><span class="menu-title">Logout</span></a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </ul>
    </div>
</div>
