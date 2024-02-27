 <div class="main-menu menu-fixed menu-dark menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="navbar-header">
        <ul class="nav navbar-nav flex-row">
            <li class="nav-item mr-auto"><a class="navbar-brand" href="/">
                    {{-- <div class="brand-logo"><img class="logo" src="{{ asset(getSettings()->logo ) }}" /></div> --}}
                    <div class="brand-logo"><img style="width: 180px;" src="{{ asset(getSettings()->dashboard_logo ) }}" /></div>
                    <h2 class="brand-text mb-0"></h2>
                </a></li>
            <li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse"><i class="bx bx-x d-block d-xl-none font-medium-4 primary"></i><i class="toggle-icon bx bx-disc font-medium-4 d-none d-xl-block primary" data-ticon="bx-disc"></i></a></li>
        </ul>
    </div>
    <div class="shadow-bottom"></div>
    <div class="main-menu-content" style="margin-top: 20px;" >
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation" data-icon-style="lines">
            {{-- start --}}
            <?php 
                $balance = auth()->user()->type == 'customer' ? getSettings()->currency .number_format(walletBalance(auth()->user()), 2) : 0;
            ?>
            <li style="color: #fff;" class="navigation-header"><span>Wallet Balance</span><br>{!! $balance !!}</li>
            <li style="color: #fff;" class="navigation-header"><span>Customer Level</span><br><strong>{{ auth()->user()->customer->level->name }}</strong></li>

           
            <li class=" navigation-header"><span>Make Payment</span></li>
            <?php $categories = getCategories() ?>
            @foreach($categories as $category)

                <li class="{{ Request::path() == 'customer/'.$category->slug ? 'active' : '' }}"><a href="{{ route('open.transaction.page', $category->slug)}}"><i class="menu-livicon" data-icon="priority-low"></i><span class="menu-title">{{ $category->display_name }}</span></a>
                </li>
            @endforeach
            <li class=" navigation-header"><span>Self Service</span></li>
            <li class="{{ Request::path() == 'profile' ? 'active' : '' }}"><a href="{{ route('profile.edit')}}"><i class="menu-livicon" data-icon="priority-low"></i><span class="menu-title">My Profile</span></a></li>
             <li class="{{ Request::path() == 'level-upgrade' ? 'active' : '' }}"><a href="{{ route('customer.level.upgrade')}}"><i class="menu-livicon" data-icon="priority-low"></i><span class="menu-title">Upgrade Account</span></a></li>
            
            <li class="{{ Request::path() == 'load-wallet' ? 'active' : '' }}"><a href="{{ route('customer.load.wallet')}}"><i class="menu-livicon" data-icon="priority-low"></i><span class="menu-title">Load Wallet</span></a></li>
            <li class="{{ Request::path() == 'customer-transactions' ? 'active' : '' }}"><a href="{{ route('customer.transaction.history')}}"><i class="menu-livicon" data-icon="priority-low"></i><span class="menu-title">My Transactions</span></a></li>
            <li class=""><a href="{{ route('logout')}}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="bx bx-power-off mr-50" data-icon="priority-low"></i><span class="menu-title">Logout</span></a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>

            {{-- <li class=" nav-item"><a href="#"><i class="menu-livicon" data-icon="check"></i><span class="menu-title" data-i18n="Form Elements">Settings</span></a>
                <ul class="menu-content">
                    <li><a href="form-inputs.html"><i class="bx bx-right-arrow-alt"></i><span class="menu-item" data-i18n="Input">Input</span></a>
                    </li>
                    <li><a href="form-input-groups.html"><i class="bx bx-right-arrow-alt"></i><span class="menu-item" data-i18n="Input Groups">Input Groups</span></a>
                    </li>
                    <li><a href="form-number-input.html"><i class="bx bx-right-arrow-alt"></i><span class="menu-item" data-i18n="Number Input">Number Input</span></a>
                    </li>
                    <li><a href="form-select.html"><i class="bx bx-right-arrow-alt"></i><span class="menu-item" data-i18n="Select">Select</span></a>
                    </li>
                    <li><a href="form-radio.html"><i class="bx bx-right-arrow-alt"></i><span class="menu-item" data-i18n="Radio">Radio</span></a>
                    </li>
                    <li><a href="form-checkbox.html"><i class="bx bx-right-arrow-alt"></i><span class="menu-item" data-i18n="Checkbox">Checkbox</span></a>
                    </li>
                    <li><a href="form-switch.html"><i class="bx bx-right-arrow-alt"></i><span class="menu-item" data-i18n="Switch">Switch</span></a>
                    </li>
                    <li><a href="form-textarea.html"><i class="bx bx-right-arrow-alt"></i><span class="menu-item" data-i18n="Textarea">Textarea</span></a>
                    </li>
                    <li><a href="form-quill-editor.html"><i class="bx bx-right-arrow-alt"></i><span class="menu-item" data-i18n="Quill Editor">Quill Editor</span></a>
                    </li>
                    <li><a href="form-file-uploader.html"><i class="bx bx-right-arrow-alt"></i><span class="menu-item" data-i18n="File Uploader">File Uploader</span></a>
                    </li>
                    <li><a href="form-date-time-picker.html"><i class="bx bx-right-arrow-alt"></i><span class="menu-item" data-i18n="Date &amp; Time Picker">Date &amp; Time Picker</span></a>
                    </li>
                </ul>
            </li> --}}

            {{-- end  --}}

        </ul>
    </div>
</div>
