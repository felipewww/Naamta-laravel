@extends('layouts.app')

@php
    $page = "approval";

@endphp

@section('styles')
    <link href="{{ asset("css/template/blanko-form-builder.css") }}" rel="stylesheet">
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="white-box">
                <h3 class="box-title m-b-20">Approval</h3>
                {{ $approval->title }}<br>
                {{ $approval->description }}
            </div>
        </div>
    </div>
    @if($approval->has_report===1)
        <input type="hidden" name="report" value=" {{ $report ? json_decode($report->form) : old('containers') }}">
        <div class="row">
            <div class="col-sm-12">
                <div class="white-box">
                    <h3 class="box-title m-b-0">Report Preview</h3>
                    <div id="drag-container" class="report-view"></div>
                </div>
            </div>
        </div>
        @php
            if ( $report )
                $forms_errors = json_decode($report->forms_errors);
        @endphp
        @if( isset($report->forms_errors) && !empty($report->forms_errors) )
            <div class="row">
                <div class="col-sm-12">
                    <div class="white-box">
                        <h3 class="box-title m-b-0">Forms Errors</h3>
                        <ul>
                            @foreach($forms_errors as $formWithError)
                                <li>
                                    <h3>{{ $formWithError->name }}</h3>
                                    @foreach($formWithError->fields as $fieldError)
                                        <br>
                                        <strong>Field:</strong> {{ $fieldError->label }}
                                        <br>
                                        <strong>Filled out:</strong> "{{ $fieldError->value }}"
                                        <br>
                                        <strong>Error type:</strong> {{ $fieldError->error }}
                                        <br>
                                    @endforeach
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif
        @if($isResponsible)
            @if($step->status == 'current')
            <div class="row">
                <div class="col-sm-12">
                    <div class="white-box">
                        <button type="button" class="btn btn-danger center-block waves-effect waves-light pull-left rejectReport">Reject and submit Report</button>
                        <button type="button" class="btn btn-success center-block waves-effect waves-light pull-right sendReport btn-submit m-l-20">Approve and submit Report</button>
                        <a style="margin-left: 10px;" href="/application/{{$appID}}/dashboard" class="btn btn-danger pull-right">Back</a>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <script>
                $('.sendReport').click(function(){
                    $('input[name=report]').val(toJson())
                    var form = toJson();
                    workflow.sendApproval('approved', '{{$stepId}}', form)
                })
                $('.rejectReport').click(function(){
                    $('input[name=report]').val(toJson())
                    var form = toJson();
                    workflow.sendApproval('reproved', '{{$stepId}}', form)
                })
            </script>
            @endif
        @else
            <div class="col-sm-12">
                <div class="white-box">
                    <a href="/application/{{$appID}}/dashboard" class="btn btn-danger pull-right">Back</a>
                    <div class="clearfix"></div>
                </div>
            </div>
        @endif


        <script src="{{ asset("js/template/signature_pad.js") }}"></script>
        <script src="{{ asset("js/template/dropzone.js") }}"></script>
        <!-- Reference https://github.com/szimek/signature_pad -->
        <script src="{{ asset("js/template/blanko-form-builder.js") }}"></script>
        <script src="{{ asset("js/template/blanko-form-creator.js") }}"></script>
        <script src="{{ asset("js/template/blanko-form-checkpoint.js") }}"></script>
        <script src="{{ asset("js/template/jquery.mask.min.js") }}"></script>
        <script>
            var username = '{{ Auth::user()->name }}';

            createTabs($('input[name=report]').val(), {{ $isResponsible ? 'false' : 'true' }});


        </script>
        <script>
            @if(!$isResponsible)
                $('.btn-submit').attr('disabled', 'disabled').css('opacity', '0.4')
            @endif
        </script>
    @endif
@endsection

@section('scripts')
    <script src="{{ asset("js/workflow.js") }}"></script>
    <script>
        $(document).ready(function () {
            $('#save, #open-history, .form-holder > .filter').hide();
        });

        @if(!$isResponsible)
        $(document).ready(function () {
            $('#list-container, #addTab').hide();
        });

        @endif
    </script>
@endsection
