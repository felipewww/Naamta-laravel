@extends('layouts.app')

@section('content')
    {{--<div id="page-wrapper">--}}
        <div class="container-fluid dashboard-applications">
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
                                <h5><b>Last Step Submitted:</b> {{ $activeApplication->lastDateSubmit }} </h5>
                                <h5><b>Responsible:</b> {{ $activeApplication->client->user->name }}, {{ $activeApplication->client->user->email }} </h5>

                                @if($activeApplication->client->user->primary_contact_name)
                                    <h5><b>Primary contact name:</b> {{ $activeApplication->client->user->primary_contact_name }}</h5>
                                @endif

                                @if($activeApplication->client->user->primary_contact_phone)
                                    <h5><b>Phone:</b> {{ $activeApplication->client->user->primary_contact_phone }}</h5>
                                @endif

                                @if($activeApplication->client->user->primary_transport)
                                    <h5><b>Transport:</b> {{ $activeApplication->client->user->primary_transport }}</h5>
                                @endif

                                @if($activeApplication->client->user->primary_scopecare)
                                    <h5><b>Scope of Care:</b> {{ $activeApplication->client->user->primary_scopecare }}</h5>
                                @endif

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
                    <h3 class="box-title">Completed Applications</h3>
                    <div class="row">
                        @foreach($vars->completedApplications as $completedApplication)
                            <div class="col-md-6">
                                <div class="white-box">
                                    <h3 class="box-title">Company: {{ $completedApplication->client->company }}</h3>
                                    <h5><b>Last Step Submitted:</b> {{ $completedApplication->updated_at }} </h5>
                                    <h5><b>Responsible:</b> {{ $completedApplication->client->user->name }}, {{ $completedApplication->client->user->email }} </h5>
                                    @if($completedApplication->client->user->primary_contact_name)
                                        <h5><b>Primary contact name:</b> {{ $completedApplication->client->user->primary_contact_name }}</h5>
                                    @endif

                                    @if($completedApplication->client->user->primary_contact_phone)
                                        <h5><b>Phone:</b> {{ $completedApplication->client->user->primary_contact_phone }}</h5>
                                    @endif

                                    @if($completedApplication->client->user->primary_transport)
                                        <h5><b>Transport:</b> {{ $completedApplication->client->user->primary_transport }}</h5>
                                    @endif

                                    @if($completedApplication->client->user->primary_scopecare)
                                        <h5><b>Scope of Care:</b> {{ $completedApplication->client->user->primary_scopecare }}</h5>
                                    @endif
                                    <ul class="list-inline two-part">
                                        <li></li>
                                        <li class="text-right">
                                            <a href="/application/{{$completedApplication->id}}/dashboard" class="btn btn-success">Details</a>
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

                        @if($inactiveApplication->client->user->primary_contact_name)
                            <h5><b>Primary contact name:</b> {{ $inactiveApplication->client->user->primary_contact_name }}</h5>
                        @endif

                        @if($inactiveApplication->client->user->primary_contact_phone)
                            <h5><b>Phone:</b> {{ $inactiveApplication->client->user->primary_contact_phone }}</h5>
                        @endif

                        @if($inactiveApplication->client->user->primary_transport)
                            <h5><b>Transport:</b> {{ $inactiveApplication->client->user->primary_transport }}</h5>
                        @endif

                        @if($inactiveApplication->client->user->primary_scopecare)
                            <h5><b>Scope of Care:</b> {{ $inactiveApplication->client->user->primary_scopecare }}</h5>
                        @endif

                        @if( $inactiveApplication->reset_at )
                            <div>3 years flow</div>
                        @endif

                        <ul class="list-inline two-part">
                            <li></li>
                            @if( $inactiveApplication->status != 'denied' )
                            <li class="text-right">
                                <a href="/applications/{{ $inactiveApplication->id }}/edit" class="btn btn-success m-t-10">Details</a>
                            </li>
                            @elseif($inactiveApplication->status == 'denied')
                            <li class="text-right">
                                <a href="/applications/{{ $inactiveApplication->id }}/payment/first_form" class="btn btn-success m-t-10">Registration info</a>
                            </li>
                            @endif
                        </ul>
                    </div>
                    @endforeach
                </div>
            </div>
            <!-- .row -->
        </div>
        <!-- /.container-fluid -->
    {{--</div>--}}
    <!-- /#page-wrapper -->
@endsection
