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
    <div class="col-sm-8 col-sm-offset-2">
        <div class="white-box">
            <h3 class="box-title m-b-0">New Form Type</h3>
            <form  class="form-horizontal" role="form" method="POST" action="{{$route}}">
                {{ csrf_field() }}
                <input type="hidden" name="_method" value="{{ $method }}">
                <input type="hidden" name="form" value="{{ $form->form!=null ? $form->form : old('form') }}" required>
                <div class="form-group">
                    <label class="col-md-12">Title</label>
                    <div class="col-md-12">
                        <input id="name" type="text" class="form-control" name="name" value="{{ $form->name!=null ? $user->name : old('name') }}" required>
                        @if ($errors->has('name'))
                            <span class="help-block">
                                <strong>{{ $errors->first('name') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-12">Status</label>
                    <div class="col-sm-3">
                        <select class="form-control" required="" id="template">
                            <option>Active</option>
                            <option>Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="form-group text-center m-t-20">
                    <a href="list-forms-template.html" class="btn btn-success waves-effect waves-light m-r-20 ">Create</a>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="col-sm-12">
    <div class="white-box">
        <h3 class="box-title m-b-0">Form Builder</h3>
        <div id="drag-container"></div>
    </div>
</div>
<script src="{{ asset("js/template/blanko-form-builder.js") }}"></script>
<script src="{{ asset("js/template/blanko-form-creator.js") }}"></script>
@endsection
