@extends('layouts.app')

@include('partials.chosen')

@section('content')
    <div class="container-fluid">
        <!-- .row -->
        <div class="row">
            <div class="col-sm-12">
                <div class="white-box">
                    <h3 class="box-title m-b-0"><b>Application Info</b></h3>

                    <div class="col-md-4">
                        <h4>Application Started:</h4>
                        <p class="m-b-40">{{ $application->created_at }}</p>
                    </div>

                    <div class="col-md-4">
                        <h4>Applicant:</h4>
                        <p class="m-b-40">{{ $application->client->company }}</p>
                    </div>

                    <div class="col-md-4">
                        <h4>Applicant’s Representative:</h4>
                        <p class="">{{ $application->client->user->name }}</p>
                    </div>

                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="white-box">
                @if($isAdmin)
                    <h3 class="box-title m-b-0"><b>Continuous Compliance</b></h3>
                    <h2>Create new</h2>
                    <form class="form-horizontal" method="post" action="addContinuousCompliance">
                        {{csrf_field()}}
                        <div class="form-group col-md-2">
                            <label class="">Interval</label>
                            <div class="">
                                <input class="form-control" name="interval" type="number" placeholder="Interval in days">
                            </div>
                        </div>

                        <div class="form-group col-md-2">
                            <label class="">How many times?</label>
                            <div class="">
                                <input class="form-control" name="times" type="number" placeholder="How many times?">
                            </div>
                        </div>

                        <div class="form-group col-md-7">
                            <label class="col-sm-12">Form Template</label>
                            <div class="col-sm-12">
                                <select class="chosen" name="form_template_id">
                                    @foreach(\App\Models\FormTemplate::where('status',1)->get() as $form)
                                        <option value="{{$form->id}}">{{$form->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group col-md-1" style="margin-top: 26px;">
                            {{--<h2>asd</h2>--}}
                            <button class="btn btn-save waves-effect waves-light m-r-20" type="submit">Save</button>
                        </div>

                    </form>

                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Form</th>
                            <th>Interval</th>
                            <th>Times</th>
                            <th>Last time</th>
                            <th>How many times</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($cCompliancesRegistered as $registered)
                            <tr>
                                <td>{{$registered->form()->withTrashed()->first()->name}}</td>
                                <td>{{$registered->interval}}</td>
                                <td>{{$registered->times}}</td>
                                <td>{{$registered->updated_at}}</td>
                                <td>{{$registered->times_executed}}</td>
                                <td>
                                    <a href="deleteContinuousCompliances/{{$registered->id}}">Delete</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @endif
                    <div class="clearfix"></div>
                    <h2>Continuous Compliance</h2>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Available at</th>
                                <th>Filled at</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cComplianceForms as $data)
                            <tr>
                                <td>{{$data->form->name}}</td>
                                <td>{{$data->status}}</td>
                                <td>{{$data->created_at}}</td>
                                <td>{{$data->updated_at}}</td>
                                <td>
                                    <a href="/applications/{{$application->id}}/continuous/{{$data->id}}">See</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
    <script src="{{ asset("js/workflow.js") }}"></script>

@endsection
