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
<form  class="" role="form" method="POST" action="{{$route}}">
    {{ csrf_field() }}
    <div class="row">
        <div class="col-sm-12">
            <div class="white-box">
                <h3 class="box-title m-b-20">New Form Type</h3>

                <input type="hidden" name="_method" value="{{ $method }}">
                <input type="hidden" name="containers" value="{{ isset($containers) ? $containers : old('containers') }}" required>
                <div class="form-group col-sm-6">
                    <label class="col-md-12">Title</label>
                    <div class="col-md-12">
                        <input id="name" type="text" class="form-control" name="name" value="{{ $form->name!=null ? $form->name : old('name') }}" required>
                        @if ($errors->has('name'))
                            <span class="help-block">
                                <strong>{{ $errors->first('name') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="form-group col-sm-6">
                    <label class="col-sm-12">Status</label>
                    <div class="col-sm-12">
                        <select class="form-control" required="" id="template">
                            <option>Active</option>
                            <option>Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="white-box">
                <h3 class="box-title m-b-0">Form Builder</h3>
                <div id="drag-container"></div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="white-box">
                <div class="form-group text-center m-t-20">
                    <button type="submit" class="btn btn-success waves-effect waves-light m-r-20 ">Submit Form</button>
                </div>
            </div>
        </div>
    </div>
</form>
<script src="{{ asset("js/template/signature_pad.js") }}"></script>
<!-- Reference https://github.com/szimek/signature_pad -->

<script src="{{ asset("js/template/blanko-form-builder.js") }}"></script>
<script src="{{ asset("js/template/blanko-form-creator.js") }}"></script>
<script src="{{ asset("js/template/blanko-form-checkpoint.js") }}"></script>

<script>
    $('form').submit(function(e){
        e.preventDefault();
        $('input[name=containers]').val(toJson());
        $(this).unbind().submit();
    })
    createTabs($('input[name=containers]').val());
</script>
@endsection
