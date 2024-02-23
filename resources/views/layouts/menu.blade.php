 <div class="main-menu menu-fixed menu-dark menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="navbar-header">
        <ul class="nav navbar-nav flex-row">
            <li class="nav-item mr-auto"><a class="navbar-brand" href="/">
                    <div class="brand-logo"><img class="logo" src="{{ asset(getSettings()->logo ) }}" /></div>
                    <h2 class="brand-text mb-0"></h2>
                </a></li>
            <li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse"><i class="bx bx-x d-block d-xl-none font-medium-4 primary"></i><i class="toggle-icon bx bx-disc font-medium-4 d-none d-xl-block primary" data-ticon="bx-disc"></i></a></li>
        </ul>
    </div>
    <div class="shadow-bottom"></div>
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation" data-icon-style="lines">
            {{-- start --}}
            <?php 
                $balance = auth()->user()->type == 'customer' ? getSettings()->currency .number_format(walletBalance(auth()->user())) : 0;
            ?>
            <li style="color: #fff;" class="navigation-header"><span>Wallet Balance</span><br>{!! $balance !!}</li>

           
            <li class=" navigation-header"><span>Make Payment</span></li>
            <?php $categories = getCategories() ?>
            @foreach($categories as $category)
                <li class="{{ Request::path() == 'customer/'.$category->slug ? 'active' : '' }}"><a href="{{ route('open.transaction.page', $category->slug)}}">
                    {{-- <i class="menu-livicon" data-icon="priority-low"></i> --}}
                    <span class="menu-title">{{ $category->display_name }}</span></a>
                </li>
            @endforeach
            <li class="{{ Request::path() == 'profile' ? 'active' : '' }}"><a href="{{ route('profile.edit')}}">
                {{-- <i class="menu-livicon" data-icon="priority-low"></i> --}}
                <span class="menu-title">My Profile</span></a></li>
            <li class="{{ Request::path() == 'customer-transactions' ? 'active' : '' }}"><a href="{{ route('customer.transaction.history')}}">
                {{-- <i class="menu-livicon" data-icon="priority-low"></i> --}}
                <span class="menu-title">My Transactions</span></a></li>
            <li class=""><a href="{{ route('logout')}}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                {{-- <i class="bx bx-power-off mr-50" data-icon="priority-low"></i> --}}
                <span class="menu-title">Logout</span></a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>  
        </ul>
    </div>
</div>