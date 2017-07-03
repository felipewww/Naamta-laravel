<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="shortcut icon" href="media/images/favicon.ico" type="image/x-icon">
    <link rel="icon" href="media/images/favicon.ico" type="image/x-icon">

    <!-- Styles -->
    <link href="{{ asset("bower_components/bootstrap/dist/css/bootstrap.min.css") }}" rel="stylesheet">
    <link href="{{ asset("css/template/animate.css") }}" rel="stylesheet">
    <link href="{{ asset("css/template/style.css") }}" rel="stylesheet">
    <link href="{{ asset("css/template/colors/blue-dark.css") }}" rel="stylesheet">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    @yield('styles')
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
    <script src="{{ asset("js/template/wp-sidebar.js") }}"></script>
    <script src="{{ asset("js/template/jquery.slimscroll.js") }}"></script>
    <script src="{{ asset("js/template/waves.js") }}"></script>
    <script src="{{ asset("js/template/custom.js") }}"></script>
    <script src="{{ asset("bower_components/styleswitcher/jQuery.style.switcher.js") }}"></script>
    @yield('scripts')
    <script src="{{ asset("js/script.js") }}"></script>


</head>
<body>
    <!-- Preloader -->
    <div class="preloader">
        <div class="cssload-speeding-wheel"></div>
    </div>
    <section id="wrapper" class="login-register">
        <div class="logo"><img src="{{asset('media/images/naamta_logo.png')}}" alt="home" class="dark-logo" height="120" /></div>
        <div class="login-box">
            <div class="white-box">
                @yield('content')
            </div>
        </div>
    </section>
</body>
</html>
