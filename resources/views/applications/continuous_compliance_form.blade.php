@extends('layouts.app')

@section('styles')
    <link href="{{ asset("css/template/blanko-form-builder.css") }}" rel="stylesheet">
@endsection

@section('content')

<div class="row">
    <input type="hidden" name="containers" value="{{$containers}}">
    <div class="col-sm-12">
        <div class="white-box">
            <h3 class="box-title m-b-20">Form Preview</h3>
            <div id="drag-container"></div>
        </div>
        <div class="white-box">
            @if( $isResponsible )
            <button onclick="workflow.continuousCompliance();" class="btn btn-primary pull-right btn-submit">Send Form</button>
            {{--@else--}}
            <a href="/application/{{$application->id}}/dashboard" class="btn btn-danger pull-right">Return to app info</a>
            @endif
            <div class="clearfix"></div>
        </div>
    </div>
</div>
<script>
    var username  = '{{ Auth::user()->name }}';
    var appFolder = '{{ Auth::user()->email }}';
</script>
<script src="{{ asset("js/template/signature_pad.js") }}"></script>
<script src="{{ asset("js/template/dropzone.js") }}"></script>
<!-- Reference https://github.com/szimek/signature_pad -->

<script src="{{ asset("js/template/blanko-form-builder.js") }}"></script>
<script src="{{ asset("js/template/blanko-form-creator.js") }}"></script>
<script src="{{ asset("js/template/blanko-form-checkpoint.js") }}"></script>

<script>
    createTabs($('input[name=containers]').val(), true, "{{$isResponsible}}");
    @if(!$isResponsible)
        $('.btn-submit').attr('disabled', 'disabled').css('opacity', '0.4')
        $('input, select, radio, textarea, checkbox, option').attr('disabled', 'disabled').css('opacity', '0.4')
        $('.comment-msg, .is-incorrect').removeAttr('disabled').css('opacity', '0')
        $('.is-incorrect').css('display', '0')
    @else
        $('.drag-validate').css('display', 'none');
    @endif
</script>

@endsection

@section('scripts')
    <script src="{{ asset("js/workflow.js") }}"></script>
@endsection
