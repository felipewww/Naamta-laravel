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
        <input type="hidden" name="report" value="{{ isset($approval->report->form) ? \GuzzleHttp\json_decode($approval->report->form) : old('containers') }}">
        <div class="row">
            <div class="col-sm-12">
                <div class="white-box">
                    <h3 class="box-title m-b-0">Report Preview</h3>
                    <div id="drag-container" class="report-view"></div>
                </div>
            </div>
        </div>
        @if($isResponsible)
            <div class="row">
                <div class="col-sm-12">
                    <div class="white-box">
                        <button type="button" class="btn btn-danger center-block waves-effect waves-light pull-left rejectReport">Reject and submit Report</button>

                        <button type="button" class="btn btn-success center-block waves-effect waves-light pull-right sendReport">Approve and submit Report</button>
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


        <script src="{{ asset("js/template/signature_pad.js") }}"></script>
        <script src="{{ asset("js/template/dropzone.js") }}"></script>
        <!-- Reference https://github.com/szimek/signature_pad -->
        <script src="{{ asset("js/template/blanko-form-builder.js") }}"></script>
        <script src="{{ asset("js/template/blanko-form-creator.js") }}"></script>
        <script src="{{ asset("js/template/blanko-form-checkpoint.js") }}"></script>
        <script>
            var username = '{{ Auth::user()->name }}';
            createTabs($('input[name=report]').val(), {{ $isResponsible ? 'false' : 'true' }});
        </script>
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
            @if($isResponsible)
                $('.btn-submit').attr('disabled', 'disabled').css('opacity', '0.4')
                $('input, select, radio, textarea, checkbox, option').attr('disabled', 'disabled').css('opacity', '0.4')
                $('.comment-msg').removeAttr('disabled').css('opacity', '1')
            @endif
        </script>
    @endif
@endsection

@section('scripts')
    <script src="{{ asset("js/workflow.js") }}"></script>

@endsection
