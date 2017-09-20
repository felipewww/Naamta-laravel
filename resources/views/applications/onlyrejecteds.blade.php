@extends('layouts.app')

@section('content')
    <div class="container-fluid dashboard-applications">
        <div class="row">
            <div class="col-md-9 p-r-20">
                <h3 class="box-title">Rejected Applications</h3>
                <div class="row">
                    @foreach($apps as $app)
                    <div class="col-md-6">
                        <div class="white-box">
                            <h3 class="box-title">Company: {{ $app->client->company }}</h3>

                            <h5><b>Last Step Submitted:</b> {{ $app->lastDateSubmit }} </h5>
                            <h5><b>Responsible:</b> {{ $app->client->user->name }}, {{ $app->client->user->email }} </h5>

                            @if($app->client->user->primary_contact_name)
                                <h5><b>Primary contact name:</b> {{ $app->client->user->primary_contact_name }}</h5>
                            @endif

                            @if($app->client->user->primary_contact_phone)
                                <h5><b>Phone:</b> {{ $app->client->user->primary_contact_phone }}</h5>
                            @endif

                            @if($app->client->user->primary_transport)
                                <h5><b>Transport:</b> {{ $app->client->user->primary_transport }}</h5>
                            @endif

                            @if($app->client->user->primary_scopecare)
                                <h5><b>Scope of Care:</b> {{ $app->client->user->primary_scopecare }}</h5>
                            @endif

                            <ul class="list-inline two-part">
                                <li></li>
                                <li class="text-right">
                                    <a href="/application/{{$app->id}}/dashboard" class="btn btn-success">Details</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
