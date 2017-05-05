@extends('layouts.app')

@php
    $page = "steps";
@endphp

@include('partials.dataTables')

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/3.5.0/lodash.min.js"></script>
    <script src="/js/template/drag.js"></script>

    <script src="/js/template/cbpFWTabs.js"></script>
    <script src="/js/sysSteps.js"></script>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="">
                <!-- Tabstyle start -->
                <section>
                    <div class="sttabs">
                        <a href="/steps/create" class="btn btn-info" style="width: 120px; float: right;"><i class="fa fa-plus"></i> Add Step</a>
                        <a href="#" onclick="sysSteps.save()" class="btn btn-save m-r-20" style="width: 120px; float: right;"><i class="fa fa-check"></i> Save</a>
                        <nav>
                            <ul class="col-md-3">
                                <li><a href="#active"><span><h3>Active Steps</h3></span></a></li>
                                <li><a href="#inactive"><span><h3>Inactive Steps</h3></span></a></li>
                            </ul>
                        </nav>
                        <div class="clearfix"></div>
                        <div class="content-wrap">
                            <section id="active">
                                <!-- .row -->
                                <div class="row">
                                    <div id="sortables" class="col-md-12">
                                        @foreach($vars->steps as $step)
                                        <div class="col-md-2 step-sortable" data-stepid="{{$step->id}}">
                                            <div class="panel draggable" draggable="true">
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
                                                    <h5 class="m-t-0 m-b-20"><b>Form:</b> Form 1</h5>

                                                    <div class="row action">
                                                        <div class="col-md-6">
                                                            <a href="#" onclick="sysSteps.changeStatus(this)" data-status="1" class="btn btn-danger">Inactivate</a>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <a href="/steps/{{ $step->id }}/edit" class="btn btn-circle pull-right btn-custom2"><i class="fa fa-pencil"></i></a>
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
                            <section id="inactive">
                                <div class="row">
                                    <div id="not-sortables" class="col-md-12">
                                        @foreach($vars->inactives as $step)
                                            <div class="col-md-2 step-sortable" data-stepid="{{$step->id}}">
                                                <div class="panel draggable" draggable="true">
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
                                                        <h5 class="m-t-0 m-b-20"><b>Form:</b> Form 1</h5>

                                                        <div class="row action">
                                                            <div class="col-md-6">
                                                                <a href="#" onclick="sysSteps.changeStatus(this)" data-status="0" class="btn btn-def">Activate</a>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <a href="editstep.html" class="btn btn-circle pull-right btn-custom2"><i class="fa fa-pencil"></i></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
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
