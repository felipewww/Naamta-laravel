@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="white-box">
                    <h3 class="box-title m-b-0"><b>Registration Details</b></h3>
{{--{{ dd($application->client) }}--}}
                    <h4>Company's Name:</h4>
                    <p class="m-l-10 m-b-20">{{ $application->client->company }}</p>

                    <h4>Responsible:</h4>
                    <p class="m-l-10 m-b-20">{{ $application->client->user->name }}</p>

                    <h4>Created at</h4>
                    <p class="m-l-10 m-b-20">{{ $application->created_at }}</p>

                    <h4>Status</h4>
                    <p class="m-l-10 m-b-20">Waiting first form verification</p>

                    <a href="{{ asset('applications/'.$application->id.'/payment/first_form') }}" class="btn btn-custom">View Form</a>

                    <ul class="list-inline text-center">
                        <li class="text-right"><a href="{{ asset('applications/'.$application->id.'/payment/deny') }}" class="btn btn-danger m-t-10 m-r-20 p-10" style="width: 120px">Deny</a></li>
                        <li class="text-right"><a href="{{ asset('applications/'.$application->id.'/payment/allow') }}" class="btn btn-save m-t-10 p-10" style="width: 120px">Approve</a></li>
                    </ul>
                    <ul class="list-inline text-right">
                        <li class="text-right"><a href="{{ asset('applications/'.$application->id.'/payment/delete') }}" class="btn m-t-10 text-danger">Delete</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection