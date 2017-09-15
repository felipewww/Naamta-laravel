@extends('layouts.app')

@section('content')
    <div class="container-fluid dashboard-applications">
        <div class="row">
            <div class="col-md-9 p-r-20">
                <h3 class="box-title">Deleted Applications</h3>
                <div class="row">
                    @foreach($apps as $app)
                    <div class="col-md-6">
                        <div class="white-box">
                            <h3 class="box-title">Company: {{ $app->client->company }}</h3>

                            <h5><b>Last Step Submitted:</b> {{ $app->lastDateSubmit }} </h5>
                            <h5><b>Responsible:</b> {{ $app->client->user->name }}, {{ $app->client->user->email }} </h5>
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
