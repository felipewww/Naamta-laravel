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
            <button onclick="workflow.sendForm();" class="btn btn-primary pull-right">Submit Form</button>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
<script src="{{ asset("js/template/signature_pad.js") }}"></script>
<!-- Reference https://github.com/szimek/signature_pad -->

<script src="{{ asset("js/template/blanko-form-builder.js") }}"></script>
<script src="{{ asset("js/template/blanko-form-creator.js") }}"></script>
<script src="{{ asset("js/template/blanko-form-checkpoint.js") }}"></script>
<script>
    console.log($('input[name=containers]').val());
    createTabs($('input[name=containers]').val(), true, true);
</script>
@endsection

@section('scripts')
    <script src="{{ asset("js/workflow.js") }}"></script>
@endsection
