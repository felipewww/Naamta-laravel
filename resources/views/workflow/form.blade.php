@extends('layouts.app')

@php
    $page = "forms";

    $route = route('forms.store');
    $method = 'POST';
    if(isset($form) && $form!=null){
        $route = route('forms.update', ['id' => $form->id]);
        $method = 'PUT';
    }else{
        $form = new App\Models\FormTemplate();
    }

@endphp

@section('styles')
    <link href="{{ asset("css/template/blanko-form-builder.css") }}" rel="stylesheet">
@endsection

@section('content')

<div class="row">
    <input type="hidden" name="containers" value="{{$containers}}">
    <input type="hidden" name="stepId" value="{{$stepId}}">
    <div class="col-sm-12">
        <div class="white-box">
            <h3 class="box-title m-b-20">Form Preview</h3>
            <div id="drag-container"></div>
        </div>
        <div class="white-box">
            <a href="/application/{{$appID}}/dashboard" class="btn btn-primary pull-right">Back</a>
            <button onclick="workflow.sendForm();" class="btn btn-primary pull-right btn-submit">Submit Form</button>
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
        $('.drag-validate').css('display', 'none')
    @endif
</script>

@endsection

@section('scripts')
    <script src="{{ asset("js/workflow.js") }}"></script>
@endsection
