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
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="">
                <!-- Tabstyle start -->
                <section>
                    <div class="sttabs">
                        <a href="#" class="btn btn-save m-r-20" style="width: 120px; float: right;" onclick="appSteps.save()"><i class="fa fa-check"></i> Save</a>
                        <a href="/applications/{{ $application->id }}/settings" class="btn btn-save m-r-20" style="width: 120px; float: right;"><i class="fa fa-check"></i>Settings </a>
                        <div class="clearfix"></div>
                        {{--<div class="content-wrap">--}}
                        <div class="">
                            <section id="active">
                                <!-- .row -->
                                <div class="row">
                                    <div id="sortables" class="col-md-12">
                                        @foreach($steps as $step)
                                        <div class="col-md-2 step-sortable" data-stepid="{{$step->id}}">
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