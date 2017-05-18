@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div>
                <div class="white-box">
                    <h3 class="box-title m-b-0"><b>Registration</b></h3>

                    <h4>Company's Name:</h4>
                    <p class="m-b-40">{{ $application->client->company }}</p>

                    <h4>Responsible:</h4>
                    <p class="m-b-40">{{ $application->client->user->name }}</p>
                    <a href="{{ asset('applications/'.$application->id.'/payment/first_form') }}" class="btn btn-custom2 m-t-10">View Form</a>
                    <ul class="list-inline text-center">
                        <li class="text-right"><a href="{{ asset('applications/'.$application->id.'/payment/allow') }}" class="btn btn-save m-t-10">Approve</a></li>
                        <li class="text-right"><a href="{{ asset('applications/'.$application->id.'/payment/deny') }}" class="btn btn-danger m-t-10">Deny</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection