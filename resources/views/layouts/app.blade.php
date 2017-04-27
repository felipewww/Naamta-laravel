<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    {{--<link href="{{ asset('css/app.css') }}" rel="stylesheet">--}}
    <link href="{{ asset("bower_components/bootstrap/dist/css/bootstrap.min.css") }}" rel="stylesheet">
    <link href="{{ asset("css/template/style.css") }}" rel="stylesheet">
    <link href="{{ asset("css/template/colors/blue-dark.css") }}" rel="stylesheet">
    <link href="{{ asset("css/template/animate.css") }}" rel="stylesheet">
    <link href="{{ asset("css/template/animate.css") }}" rel="stylesheet">
    <link href="{{ asset("css/template/icons/font-awesome/css/font-awesome.min.css") }}" rel="stylesheet">
    @yield('styles')

    <link href="{{ asset("css/naamta.css") }}" rel="stylesheet">

    {{--<link rel="stylesheet" type="text/css" media="screen" href="{{ asset("css/form-builder/form-builder.min.css") }}">--}}
    {{--<link rel="stylesheet" type="text/css" media="screen" href="{{ asset("css/form-builder/form-render.min.css") }}">--}}

    <!-- Scripts -->
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>
    <!-- BOWER -->
    <!-- jQuery -->
    <script src="{{ asset("bower_components/jquery/dist/jquery.min.js") }}"></script>
    <script src="{{ asset("bower_components/jquery-ui/jquery-ui.min.js") }}"></script>
    <script src="{{ asset("bower_components/bootstrap/dist/js/bootstrap.min.js") }}"></script>
    <script src="{{ asset("js/template/jquery.slimscroll.js") }}"></script>
    <script src="{{ asset("js/template/custom.js") }}"></script>
    <script src="{{ asset("js/script.js") }}"></script>
    {{--<script src="{{ asset("js/template/jasny-bootstrap.js") }}"></script>--}}

    @yield('scripts')

</head>
<body>
    {{--<div id="app">--}}
        {{--<nav class="navbar navbar-default navbar-static-top">--}}
            {{--<div class="container">--}}
                {{--<div class="navbar-header">--}}

                    {{--<!-- Collapsed Hamburger -->--}}
                    {{--<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">--}}
                        {{--<span class="sr-only">Toggle Navigation</span>--}}
                        {{--<span class="icon-bar"></span>--}}
                        {{--<span class="icon-bar"></span>--}}
                        {{--<span class="icon-bar"></span>--}}
                    {{--</button>--}}

                    {{--<!-- Branding Image -->--}}
                    {{--<a class="navbar-brand" href="{{ url('/') }}">--}}
                        {{--{{ config('app.name', 'Laravel') }}--}}
                    {{--</a>--}}
                {{--</div>--}}

                {{--<div class="collapse navbar-collapse" id="app-navbar-collapse">--}}
                    {{--<!-- Left Side Of Navbar -->--}}
                    {{--<ul class="nav navbar-nav">--}}
                        {{--&nbsp;--}}
                    {{--</ul>--}}

                    {{--<!-- Right Side Of Navbar -->--}}
                    {{--<ul class="nav navbar-nav navbar-right">--}}
                        {{--<!-- Authentication Links -->--}}
                        {{--@if (Auth::guest())--}}
                            {{--<li><a href="{{ route('login') }}">Login</a></li>--}}
                            {{--<li><a href="{{ route('register') }}">Register</a></li>--}}
                        {{--@else--}}
                            {{--<li class="dropdown">--}}
                                {{--<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">--}}
                                    {{--{{ Auth::user()->name }} <span class="caret"></span>--}}
                                {{--</a>--}}

                                {{--<ul class="dropdown-menu" role="menu">--}}
                                    {{--<li>--}}
                                        {{--<a href="{{ url('home') }}">--}}
                                            {{--Dashboard--}}
                                        {{--</a>--}}
                                    {{--</li>--}}
                                    {{--<li>--}}
                                        {{--<a href="{{ url('users') }}">--}}
                                            {{--System Users--}}
                                        {{--</a>--}}
                                    {{--</li>--}}
                                    {{--<li>--}}
                                        {{--<a href="{{ url('emails') }}">--}}
                                            {{--Emails--}}
                                        {{--</a>--}}
                                    {{--</li>--}}
                                    {{--<li>--}}
                                        {{--<a href="{{ url('steps') }}">--}}
                                            {{--Steps--}}
                                        {{--</a>--}}
                                    {{--</li>--}}
                                    {{--<li>--}}
                                        {{--<a href="{{ url('forms') }}">--}}
                                            {{--Forms--}}
                                        {{--</a>--}}
                                    {{--</li>--}}
                                    {{--<li>--}}
                                        {{--<a href="{{ url('usertypes') }}">--}}
                                            {{--User Types--}}
                                        {{--</a>--}}
                                    {{--</li>--}}
                                    {{--<li>--}}
                                        {{--<a href="#"--}}
                                            {{--onclick="event.preventDefault();--}}
                                                     {{--document.getElementById('logout-form').submit();">--}}
                                            {{--Logout--}}
                                        {{--</a>--}}

                                        {{--<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">--}}
                                            {{--{{ csrf_field() }}--}}
                                        {{--</form>--}}
                                    {{--</li>--}}
                                {{--</ul>--}}
                            {{--</li>--}}
                        {{--@endif--}}
                    {{--</ul>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</nav>--}}

        {{--@yield('content')--}}
    {{--</div>--}}

    {{--<!-- Scripts -->--}}
    {{--<script src="{{ asset('js/app.js') }}"></script>--}}
    {{--<script>--}}
        {{--const data = {--}}
            {{--name: 'Leonardo Carvalho',--}}
            {{--redirect: 'http://127.0.0.1/callback'--}}
        {{--};--}}

        {{--axios.post('/oauth/clients', data)--}}
            {{--.then(response => {--}}
                {{--console.log(response.data);--}}
            {{--})--}}
            {{--.catch (response => {--}}
                {{--// List errors on response...--}}
        {{--});--}}
    {{--</script>--}}

            <!-- Preloader -->
    <div class="preloader">
        <div class="cssload-speeding-wheel"></div>
    </div>
    <div id="wrapper">
        <!-- Top Navigation -->
        <nav class="navbar navbar-default navbar-static-top m-b-0">
            <div class="navbar-header"> <a class="navbar-toggle hidden-sm hidden-md hidden-lg " href="javascript:void(0)" data-toggle="collapse" data-target=".navbar-collapse"><i class="ti-menu"></i></a>
                <div class="top-left-part">
                    <a class="logo" href="{{ url('home') }}">
                        <b>
                            <img src="{{asset('media/images/naamta_logo.png')}}" alt="home" class="dark-logo" />
{{--                            <img src="{{asset('media/images/naamta_logo.png')}}" alt="home" class="light-logo" />--}}
                        </b>
                    </a>
                </div>

            </div>
            <!-- /.navbar-header -->
            <!-- /.navbar-top-links -->
            <!-- /.navbar-static-side -->
        </nav>
        <!-- End Top Navigation -->
        <!-- Left navbar-header -->
        <div class="sidebar" role="navigation">
            <div class="wp-sidebar-nav">
                <div class="user-profile">
                    <div class="dropdown user-pro-body">
                        <a href="#" class="dropdown-toggle u-dropdown" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Steave Gection <span class="caret"></span></a>
                        <ul class="dropdown-menu animated fadeIn">
                            <li><a href="index.html"><i class="ti-view-list"></i> Applications</a></li>
                            <li><a href="list-system-users.html"><i class="ti-settings"></i> System</a></li>
                            <li role="separator" class="divider"></li>
                            <li><a href="userprofile.html"><i class="ti-user"></i> My Profile</a></li>
                            <li role="separator" class="divider"></li>
                            <li><a href="javascript:document.forms['logout-form'].submit();"><i class="fa fa-power-off"></i>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
                <ul class="wp-side-menu">
                    <li class="nav-small-cap">USERS</li>
                    <li> <a href="{{ url('users') }}">Users List</a> </li>
                    <li> <a href="{{ url('usertypes') }}">User Types</a></li>
                    <li class="nav-small-cap">WORKFLOW</li>
                    <li> <a href="{{ url('steps') }}"> Steps</a> </li>
                    <li> <a href="{{ url('emails') }}"> Email Templates</a></li>
                    <li> <a href="#"> Aplications</a></li>
                    <li> <a href="{{ url('forms') }}"> Form Types</a></li>
                </ul>
            </div>
        </div>
        <!-- Left navbar-header end -->
        <!-- Page Content -->
        <div id="page-wrapper">
            <div class="container-fluid">
                <div class="row bg-title">
                    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                        <h4 class="page-title">New Form Type</h4> </div>
                    <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
                        <ol class="breadcrumb">
                            <li><a href="list-forms-template.html">Form Types</a></li>
                            <li class="active">New Form Type</li>
                        </ol>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- .row -->

                <div class="row">
                    @yield('content')
                </div>
            </div>
            <!-- /.container-fluid -->

        </div>
        <!-- /#page-wrapper -->
    </div>
</body>
</html>
