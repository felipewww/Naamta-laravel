@extends('layouts.app')

@php
    $page = "applications";
    
    $route = route('applications.store');
    $method = 'POST';
    if(isset($application) && $application!=null){
        $method = 'PUT';
        $route = route('applications.update', ['id' => $application->id]);
        
    }else{
        $application = new App\Models\Application();
    }

@endphp

@section('scripts')
    <script type="text/javascript" src="/js/appSteps.js"></script>
    <script>
        appSteps.appID = {{ $application->id }};
    </script>
@endsection

@section('content')
    @include('partials._modal_steps')

    <div class="row">
        <div class="col-md-12">
            <div class="">
                <!-- Tabstyle start -->
                <section>
                    <div class="sttabs">
                        <div>
                            <span class="btn btn-save m-r-20" style="width: 120px; float: right;" onclick="appSteps.save()"><i class="fa fa-check"></i> Save</span>
                            <a href="/applications/{{ $application->id }}/settings" class="btn btn-custom2 m-r-20" style="width: 120px; float: right;"><i class="fa fa-cog"></i> Settings </a>
                            <div class="clearfix"></div>
                        </div>
                        <br>
                        {{--<div class="content-wrap">--}}
                        <div class="">
                            <section id="active">
                                <!-- .row -->
                                <div class="row">
                                    <div id="sortables" class="col-md-12">
                                        @foreach($steps as $step)
                                        <div class="col-sm-6 col-md-3 col-lg-2 step-sortable" data-stepid="{{$step->id}}">
                                            <div class="panel">
                                                <div class="white-box">
                                                    <div class="panel-heading p-b-10 p-t-10">
                                                        <h3 class="box-title m-b-0">{{ $step->title }}</h3>
                                                    </div>
                                                    <h5><b>Description:</b></h5>
                                                    <p>{{ $step->description }}</p>
                                                    <h5 class="m-t-20 m-b-0">
                                                        <b>Uses e-mail:</b>
                                                        @if( $step->usesEmails->isNotempty() )
                                                            Yes
                                                        @else
                                                            No
                                                        @endif
                                                    </h5>
                                                    <h5 class="m-t-20 m-b-0">
                                                        <b>Status:</b>
                                                        @if( $step->status )
                                                            Active
                                                        @else
                                                            Inactive
                                                        @endif
                                                    </h5>
                                                    <h5 class="m-t-20 m-b-0">
                                                        <b>Type:</b>
                                                        @if( $step->morphs_from == \App\Models\FormTemplate::class )
                                                            <span style="color: blue; font-weight: bold;">Forms</span>
                                                        @else
                                                            Approvals
                                                        @endif
                                                    </h5>
                                                    <br>
                                                    @if( $step->usesEmails->isNotempty() )
                                                        <table class="table table-responsive color-table muted-table">
                                                            <thead>
                                                            <tr>
                                                                <th>Template</th>
                                                                <th>User</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            @foreach($step->usesEmails as $stepEmail)
                                                                <tr>
                                                                    <td>{{ $stepEmail->template->title }}</td>
                                                                    <td>{{ $stepEmail->receivedBy->title }}</td>
                                                                </tr>
                                                            @endforeach
                                                            </tbody>
                                                        </table>
                                                    @endif

                                                    <div class="row action">
                                                        <div class="col-md-6">
                                                            <a href="/applications/step/{{ $step->id  }}" class="btn btn-circle pull-right btn-custom2"><i class="fa fa-pencil"></i></a>
                                                        </div>
                                                        <div class="col-md-6">
                                                            @if( $step->status )
                                                                <span data-currentstatus="{{ $step->status }}" onclick="appSteps.changeStatus(this, {{ $step->id }})" class="btn btn-circle pull-right btn-custom"><i class="fa fa-ban"></i></span>
                                                            @else
                                                                <span data-currentstatus="{{ $step->status }}" onclick="appSteps.changeStatus(this, {{ $step->id }})" class="btn btn-circle pull-right btn-save"><i class="fa fa-check"></i></span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                <!-- /.row -->
                            </section>
                        </div>
                        <!-- /content -->
                    </div>
                    <!-- /tabs -->
                </section>
            </div>
        </div>
    </div>
@endsection