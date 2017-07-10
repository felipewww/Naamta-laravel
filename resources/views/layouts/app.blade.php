<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="icon" type="image/png" href="media/images/favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="media/images/favicon-16x16.png" sizes="16x16" />
    
    <!-- Styles -->
    <link href="{{ asset("bower_components/bootstrap/dist/css/bootstrap.min.css") }}" rel="stylesheet">
    <link href="{{ asset("css/template/style.css") }}" rel="stylesheet">
    <link href="{{ asset("css/template/colors/blue-dark.css") }}" rel="stylesheet">
    <link href="{{ asset("css/template/animate.css") }}" rel="stylesheet">
    <link href="{{ asset("css/template/icons/font-awesome/css/font-awesome.min.css") }}" rel="stylesheet">
    @yield('styles')

    <link href="{{ asset("css/naamta.css") }}" rel="stylesheet">

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
    <script src="{{ asset("js/template/jasny-bootstrap.js") }}"></script>
    <script src="{{ asset("js/script.js") }}"></script>

    <script>

        Script.env = '{{ app('env') }}';

        $(document).ready(function () {
            var btnLogout = document.getElementById("btnLogout");
            
            btnLogout.onclick = function () {
                $.ajax({
                    url: '/user/logout',
                    method: 'post',
                    data: { _token: window.Laravel.csrfToken },
                    complete: function (data) {
                        window.location.replace('/login');
                    }
                })
            }
        })
    </script>
    @yield('scripts')


</head>
<body>
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
                        @if (Auth::check())
                        <a href="#" class="dropdown-toggle u-dropdown" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{Auth::user()->name}} <span class="caret"></span></a>
                        <ul class="dropdown-menu animated fadeIn">
                            @if(Auth::user()!=null && Auth::user()->isAdmin())
                                <li><a href="{{ url('applications') }}"><i class="ti-view-list"></i> Applications</a></li>
                                <li role="separator" class="divider"></li>
                            @endif

                            @if(\Illuminate\Support\Facades\Auth::user()->hasRole('client'))
                                <li><a href="/client/{{\Illuminate\Support\Facades\Auth::user()->id}}/profile"><i class="ti-user"></i> My Profile</a></li>
                            @else
                                <li><a href="{{ route('users.edit', ['id' => Auth::id()]) }}"><i class="ti-user"></i> My Profile</a></li>
                            @endif

                            <li role="separator" class="divider"></li>

                            <li>
                                {{--<a href="/login" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fa fa-power-off"></i> Logout</a>--}}
                                <a id="btnLogout" href="javascript:;"><i class="fa fa-power-off"></i> Logout</a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                            </li>
                        </ul>
                        @endif
                    </div>
                </div>
                <ul class="wp-side-menu">
                    <li> <a href="{{ url('') }}"> Dashboard</a></li>
                    @if(Auth::user()!=null && Auth::user()->isAdmin())
                        <li class="nav-small-cap">USERS</li>
                        {{--<li> <a href="{{ url('/firstFormEdit') }}"> Edit Register Form</a></li>--}}
                        <li> <a href="{{ url('users') }}">Users List</a> </li>
                        <li> <a href="{{ url('usertypes') }}">User Types</a></li>
                    @endif
                    @if(Auth::user()!=null && (Auth::user()->isAdmin() || Auth::user()->isStaff()))
                        <li class="nav-small-cap">WORKFLOW</li>
                    @endif
                    @if(Auth::user()!=null &&Auth::user()->isAdmin())
                        <li> <a href="{{ url('steps') }}"> Steps</a> </li>
                        <li> <a href="{{ url('emails') }}"> Email Templates</a></li>
                    @endif
                    @if(Auth::user()!=null && (Auth::user()->isAdmin() || Auth::user()->isStaff()))
                        <li> <a href="{{ url('forms') }}"> Form Templates</a></li>
                        <li> <a href="{{ url('approvals') }}"> Approval Templates</a></li>
                    @endif
                </ul>
            </div>
        </div>
        <!-- Left navbar-header end -->
        <!-- Page Content -->
        <div id="page-wrapper">
            <div class="container-fluid">
                <div class="row bg-title">
                    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                        <h4 class="page-title">{{ $pageInfo->title }}</h4>
                    </div>
                    <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
                        <ol class="breadcrumb">
                            <li><a href="{{ $pageInfo->category->link }}">{{ $pageInfo->category->title }}</a></li>
                            <li class="active">{{ $pageInfo->subCategory->title }}</li>
                        </ol>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- .row -->
                <div class="row">
                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @yield('content')
                </div>
            </div>
            <!-- /.container-fluid -->
        </div>
        <!-- /#page-wrapper -->
    </div>

    @include('partials.modal_delete')
    @include('partials._modal_default')
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
</body>
</html>
