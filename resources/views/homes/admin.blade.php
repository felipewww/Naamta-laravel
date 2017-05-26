@extends('layouts.app')

@section('content')
    {{--<div id="page-wrapper">--}}
        <div class="container-fluid">
            {{--<div class="alert alert-custom">You have <a href="#">4 Applications</a> in need of your attention (of <a href="#">10 under your supervision</a>)</div>--}}

            <!-- .row -->
            <div class="row">
                <div class="col-md-9 p-r-20">
                    <h3 class="box-title">Active Applications</h3>
                    <div class="row">
                        @foreach($vars->activeApplications as $activeApplication)
                        <div class="col-md-6">
                            <div class="white-box">
                                <h3 class="box-title">Company: {{ $activeApplication->client->company }}</h3>

                                <h5><b>Current Step:</b>{{ $activeApplication->currStep->title }}</h5>
                                {{--<h5><b>Next Action Due Date:</b> March, 17, 2017</h5>--}}
                                <h5><b>Last Step Submitted:</b> {{ $activeApplication->lastDateSubmit }} </h5>
                                <h5><b>Responsible:</b> {{ $activeApplication->client->user->name }}, {{ $activeApplication->client->user->email }} </h5>
                                <ul class="list-inline two-part">
                                    <li></li>
                                    <li class="text-right">
                                        <a href="/application/{{$activeApplication->id}}/dashboard" class="btn btn-success">Details</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-md-3 p-l-20">
                    <h3 class="box-title">New Registrations</h3>
                    @foreach($vars->inactiveApplications as $inactiveApplication)
                    <div class="white-box">
                        <h4 class="m-b-20"><b>Company: {{ $inactiveApplication->client->company }}</b></h4>
                        <h5><b>Responsible:</b> {{ $inactiveApplication->client->user->name }}, {{ $inactiveApplication->client->user->email }} </h5>
                        <h5><b>Status:</b> {{ $inactiveApplication->statusText }} </h5>
                        <h5><b>Last Update:</b> {{ $inactiveApplication->updated_at->toDateTimeString() }} </h5>
                        @if( $inactiveApplication->reset_at )
                            <div>3 years flow</div>
                        @endif

                        <ul class="list-inline two-part">
                            <li></li>

                            @if( $inactiveApplication->status != 'denied' )
                            <li class="text-right">
                                <a href="/applications/{{ $inactiveApplication->id }}/edit" class="btn btn-success m-t-10">Details</a>
                            </li>
                            @endif
                        </ul>
                    </div>
                    @endforeach
                </div>
            </div>
            <!-- .row -->

            <!-- .right-sidebar -->
            <div class="right-sidebar">
                <div class="slimscrollright">
                    <div class="rpanel-title"> Service Panel <span><i class="ti-close right-side-toggle"></i></span> </div>
                    <div class="r-panel-body">
                        <ul class="m-t-20 chatonline">
                            <li><b>Chat option</b></li>
                            <li>
                                <a href="javascript:void(0)"><img src="../plugins/images/users/varun.jpg" alt="user-img" class="img-circle"> <span>Varun Dhavan <small class="text-success">online</small></span></a>
                            </li>
                            <li>
                                <a href="javascript:void(0)"><img src="../plugins/images/users/genu.jpg" alt="user-img" class="img-circle"> <span>Genelia Deshmukh <small class="text-warning">Away</small></span></a>
                            </li>
                            <li>
                                <a href="javascript:void(0)"><img src="../plugins/images/users/ritesh.jpg" alt="user-img" class="img-circle"> <span>Ritesh Deshmukh <small class="text-danger">Busy</small></span></a>
                            </li>
                            <li>
                                <a href="javascript:void(0)"><img src="../plugins/images/users/arijit.jpg" alt="user-img" class="img-circle"> <span>Arijit Sinh <small class="text-muted">Offline</small></span></a>
                            </li>
                            <li>
                                <a href="javascript:void(0)"><img src="../plugins/images/users/govinda.jpg" alt="user-img" class="img-circle"> <span>Govinda Star <small class="text-success">online</small></span></a>
                            </li>
                            <li>
                                <a href="javascript:void(0)"><img src="../plugins/images/users/hritik.jpg" alt="user-img" class="img-circle"> <span>John Abraham<small class="text-success">online</small></span></a>
                            </li>
                            <li>
                                <a href="javascript:void(0)"><img src="../plugins/images/users/john.jpg" alt="user-img" class="img-circle"> <span>Hritik Roshan<small class="text-success">online</small></span></a>
                            </li>
                            <li>
                                <a href="javascript:void(0)"><img src="../plugins/images/users/pawandeep.jpg" alt="user-img" class="img-circle"> <span>Pwandeep rajan <small class="text-success">online</small></span></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /.right-sidebar -->
        </div>
        <!-- /.container-fluid -->

    {{--</div>--}}
    <!-- /#page-wrapper -->
@endsection
