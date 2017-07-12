@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12 col-md-4">
                <div class="white-box">
                    <h3 class="box-title m-b-0"><b>Current Step :</b> {{$currentStep->title}}</h3>

                    <h4>Last Step Submitted:</h4>
                    <p class="m-b-40">
                        @if($previous_step->updated_at == null)
                            No step submitted yet
                        @else
                            {{ $previous_step->updated_at }}
                        @endif
                    </p>
                    <h4>Next Step:</h4>
                    <p class="m-b-40">Step : {{ ($currentStep->nextStep() != null) ? $currentStep->nextStep()->title : "It's the last step" }}</p>
                    <h4>Previous Step:</h4>
                    <p class="">Step :  {{($currentStep->previousStep()!=null ? $currentStep->previousStep()->title : "It's the first step")}}</p>
                    <br>
                    <h4>Responsible User Type: <strong>{{ $userTypeResponsible->title }}</strong></h4>
                    <ul>
                        @foreach($userResponsible as $ures)
                        <li>
                            {{ $ures->user->name }}, {{ $ures->user->email }}
                        </li>
                        @endforeach
                    </ul>

                    @if($isResponsible)
                        @if($currentStep->morphs_from === "App\Models\Approval")
                            @if($currentStep->approval->has_report === 0)
                                <button onclick="workflow.sendApproval('rejected', '{{$currentStep->id}}', null);" class="btn btn-danger pull-left">Reprove</button>
                                <button onclick="workflow.sendApproval('approved', '{{$currentStep->id}}', null);" class="btn btn-primary pull-right">Approve</button>
                            @else
                                <a href="/workflow/step/{{$currentStep->id}}/show" class="btn btn-success pull-right">Evaluate with report</a>
                            @endif
                        @else
                            @foreach($currentStep->mongoForms as $form)
                                <div style="display: block; margin-bottom: 10px;">
                                    <a href="/workflow/step/{{$currentStep->id}}/{{$form->mform_id}}/show" class="btn btn-success fill-form text-left"><strong>Fill Form:</strong> {{ $form->mongoform->name }}</a>
                                </div>
                            @endforeach
                                <a href="javascript:;" onclick="workflow.gotoNextStep({{$currentStep->id}})" class="btn btn-danger">Submit all forms</a>
                        @endif
                    @endif
                    <div class="clearfix"></div>
                </div>
                <div class="white-box">
                    <h3 class="box-title m-b-0"><b>Application Info</b></h3>

                    @if(\Illuminate\Support\Facades\Auth::user()->isAdmin())
                    <br>
                    <a class="btn btn-save" href="/applications/{{$application->id}}/edit">
                        Edit settings
                    </a>
                    <br>
                    @endif

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
                <h3 class="box-title m-b-0"><b>All Forms</b></h3>
                    <div class="table-responsive">
                        <table id="allforms" class="table table-striped">
                            <thead>
                            <tr>
                                <th>Form</th>
                                <th>Status</th>
                                <th>Last update</th>
                                <th style="width: 120px">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($stepsWithForm as $formStep)
                                @foreach($formStep->mongoForms as $stepMongoForm)
                                    <tr>
                                        <td>{{ $stepMongoForm->mongoform->name }}</td>
                                        <td>{{ $stepMongoForm->mongoform->status }}</td>
                                        <td>{{ $stepMongoForm->mongoform->updated_at }}</td>
                                        <td>
                                            @if($formStep->status !== "0" &&  $formStep->status !== "1")
                                                <a href="/workflow/step/{{$formStep->id}}/{{$stepMongoForm->mform_id}}/show" class="btn btn-warning btn-circle"><i class="fa fa-pencil"></i></a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
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
                                <th>Last Update</th>
                                <th style="width: 120px">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            {{--@foreach($approvalWithReport as $k => $approvalStep)--}}
                            @foreach($approvalWithReport as $approvalStep)
                                @if($approvalStep!=null && $approvalStep["report"]!=null)
                                    <tr>
                                        <td>{{ $approvalStep["report"]->title }}</td>
                                        <td>{{  $approvalStep["report"]->updated_at }}</td>
                                        <td>
                                            <a href="/workflow/step/{{$approvalStep["stepId"]}}/approval/{{$approvalStep['report']->id}}" class="btn btn-warning btn-circle"><i class="fa fa-eye"></i></a>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="white-box">
                    <h3 class="box-title m-b-0"><b>Continuous Compliance</b></h3>
                    <a href="/application/{{$application->id}}/continuousCompliances" class="btn btn-success pull-right">+ See more</a>
                    <table id="activeform" class="table table-striped">
                        <thead>
                        <tr>
                            <th>item</th>
                            <th>status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($cComplianceForms as $complianceForm)
                            <tr>
                                <td>{{ $complianceForm->form->name }}</td>
                                <td>{{ $complianceForm->status }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <div class="white-box">
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
                                @foreach($form->fieldsWithError as $field)
                                    <tr>
                                        <td>
                                            <h6>{{ $field->container->forms->name }}</h6>
                                            <h5 class="m-t-0">
                                                <b>{{$field->setting->label}}</b>
                                            </h5>
                                            <p>{{$field->setting->value}}</p>
                                        </td>
                                        <td>
                                            <a href="/workflow/step/{{$currentStep->id}}/{{$field->container->forms->_id}}/show" class="btn btn-success btn-circle"><i class="fa fa-pencil"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
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
    <script src="{{ asset("js/workflow.js") }}"></script>

@endsection

@section('scripts')
@endsection
