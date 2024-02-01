<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->

@include('layouts.head')
<!-- END: Head-->

<!-- BEGIN: Body-->
<body class="vertical-layout vertical-menu-modern semi-dark-layout 2-columns  navbar-sticky footer-static  " data-open="click" data-menu="vertical-menu-modern" data-col="2-columns" data-layout="semi-dark-layout">
     <!-- BEGIN: Header-->
    @include('layouts.navigation')
    <!-- END: Header-->

    <!-- BEGIN: Main Menu-->
    @include('layouts.menu')
    <!-- END: Main Menu-->

    @yield('content')
    <!-- demo chat-->
    @include('layouts.chat')
   
    @include('layouts.footer')
</body>
<!-- END: Body-->
</html>