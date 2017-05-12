@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- .row -->
    <div class="row">
        <div class="col-sm-12">
            {{--<div class="white-box">
                <h3 class="box-title m-b-20" style="text-align: left;"><b>Latest Comments</b></h3>
                <button class="btn btn-success m-t-0" style="float: right">See Full History</button>
                <p><b>Steave : </b>Lorem ipsum dolor sit amet, consectetur adipisicing elit. </p>
                <p><b>Steave : </b>Nam sit amet ex quis ipsum lacinia feugiat id finibus turpis. </p>
            </div>--}}
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 col-md-4">
            <div class="white-box">
                <h3 class="box-title m-b-0"><b>Current Step :</b> {{$application->steps->first()->title}}</h3>

                <h4>Last Step Submitted:</h4>
                <p class="m-b-40">{{ $application->steps->first()->created_at }}</p>

                <h4>Next Step:</h4>
                <p class="m-b-40">Step : {{$application->steps->get(1)->title}}</p>

                <h4>Previous Step:</h4>
                <p class="">Step : Undefined</p>

            </div>
            <div class="white-box">
                <h3 class="box-title m-b-0"><b>Application Info</b></h3>

                <h4>Responsible:</h4>
                <p class="m-b-40">{{ $application->responsible->name }}</p>

                <h4>Application Started:</h4>
                <p class="m-b-40">{{ $application->created_at }}</p>

                <h4>Applicant:</h4>
                <p class="m-b-40">{{ $application->client->company }}</p>

                <h4>Applicant’s Representative:</h4>
                <p class="">{{ $application->client->user->name }}</p>
            </div>
        </div>
        <div class="col-sm-12 col-md-4">
            <div class="white-box">
                <h3 class="box-title m-b-0"><b>Active Form :</b> Form 3</h3>
                <h4>Items to be reviewed</h4>
                {{--<div class="table-responsive">
                    <table id="activeform" class="table table-striped">
                        <thead>
                        <tr>
                            <th>Item</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <h5 class="m-t-0"><b>Question</b></h5>
                                Lorem Ipsum Dolor
                                <h5><b>Answer</b></h5>
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                            </td>
                            <td>
                                <button class="btn btn-success btn-circle"><i class="fa fa-pencil"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <h5 class="m-t-0"><b>Question</b></h5>
                                Lorem Ipsum Dolor
                                <h5><b>Answer</b></h5>
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                            </td>
                            <td>
                                <button class="btn btn-success btn-circle"><i class="fa fa-pencil"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <h5 class="m-t-0"><b>Question</b></h5>
                                Lorem Ipsum Dolor
                                <h5><b>Answer</b></h5>
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                            </td>
                            <td>
                                <button class="btn btn-success btn-circle"><i class="fa fa-pencil"></i></button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>--}}
            </div>
        </div>
        <div class="col-sm-12 col-md-4">
            <div class="white-box">
                <h3 class="box-title m-b-0"><b>All Forms</b></h3>
                <div class="table-responsive">
                    <table id="allforms" class="table table-striped">
                        <thead>
                        <tr>
                            <th>Form</th>
                            <th>Status</th>
                            <th style="width: 120px">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Form 1</td>
                                <td>Approved</td>
                                <td>
                                    <button class="btn btn-warning btn-circle"><i class="fa fa-pencil"></i></button>
                                </td>
                            </tr>
                            {{--<tr>
                                <td>Form 2</td>
                                <td>Approved</td>
                                <td>
                                    <button class="btn btn-warning btn-circle"><i class="fa fa-pencil"></i></button>
                                </td>
                            </tr>--}}
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="white-box">
                <h3 class="box-title m-b-0"><b>Reports</b></h3>
                <div class="table-responsive">
                    <table id="allforms" class="table table-striped">
                        <thead>
                        <tr>
                            <th>Report</th>
                            <th style="width: 120px">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                            {{--<tr>
                                <td>Report 1</td>
                                <td>
                                    <button class="btn btn-warning btn-circle"><i class="fa fa-eye"></i></button>
                                </td>
                            </tr>--}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- /.row -->
</div>
<!-- /.container-fluid -->

@endsection
