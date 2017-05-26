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
                    <h3 class="box-title m-b-0"><b>Current Step :</b> {{$currentStep->title}}</h3>

                    <h4>Last Step Submitted:</h4>
                    <p class="m-b-40">{{ $currentStep->created_at }}</p>
                    <h4>Next Step:</h4>
                    <p class="m-b-40">Step : {{($currentStep->nextStep()!=null ? $currentStep->nextStep()->title : "It's the last step")}}</p>

                    <h4>Previous Step:</h4>
                    <p class="">Step :  {{($currentStep->previousStep()!=null ? $currentStep->previousStep()->title : "It's the first step")}}</p>
                    @if($isResponsible)
                        @if($currentStep->morphs_from === "App\Models\Approval")
                            @if($currentStep->approval->has_report === 0)
                                <button onclick="workflow.sendApproval('reproved', {{$currentStep->id}});" class="btn btn-danger pull-left">Reprove</button>
                                <button onclick="workflow.sendApproval('approved', {{$currentStep->id}});" class="btn btn-primary pull-right">Approve</button>
                            @else
                                <a href="/workflow/step/{{$currentStep->id}}/show" class="btn btn-success pull-right">Approve with report</a>
                            @endif
                        @else
                            <a href="/workflow/step/{{$currentStep->id}}/show" class="btn btn-success pull-right">View Form</a>
                        @endif
                    @endif
                    <div class="clearfix"></div>
                </div>
                <div class="white-box">
                    <h3 class="box-title m-b-0"><b>Application Info</b></h3>

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
                    <h3 class="box-title m-b-0"><b>Review</b></h3>
                    <h4>Items to be reviewed</h4>
                    <table id="activeform" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($errorsFormsFields as $form)
                                @foreach($form["containers"] as $field)
                                    <tr>
                                        <td>
                                            <h5 class="m-t-0">
                                                <b>{{$field->setting->label}}</b>
                                            </h5>
                                            <p>{{$field->setting->value}}</p>
                                        </td>
                                        <td>
                                            <a href="/workflow/step/{{$form["formId"]}}/show" class="btn btn-success btn-circle"><i class="fa fa-pencil"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
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
                                @foreach($stepsWithForm as $k => $formStep)
                                    <tr>
                                        <td>Form {{ ($k + 1) }}</td>
                                        <td>{{$formStep->status !== "0" && $formStep->status !== "1" ? $formStep->status : "waiting" }}</td>
                                        <td>
                                            @if($formStep->status !== "0" &&  $formStep->status !== "1")
                                                <a href="/workflow/step/{{$formStep->id}}/show" class="btn btn-warning btn-circle"><i class="fa fa-pencil"></i></a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
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
                                @foreach($approvalWithReport as $k => $approvalStep)
                                    @if($approvalStep!=null && $approvalStep["report"]!=null)
                                        <tr>
                                            <td>{{ $approvalStep["report"]->title }}</td>
                                            <td>
                                                <a href="/workflow/step/{{$approvalStep["stepId"]}}/show" class="btn btn-warning btn-circle"><i class="fa fa-eye"></i></a>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
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

@section('scripts')
    <script src="{{ asset("js/workflow.js") }}"></script>
@endsection
