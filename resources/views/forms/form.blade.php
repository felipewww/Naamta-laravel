@extends('layouts.app')

@php
    $page = "forms";
    
    $route = route('forms.store');
    $method = 'POST';
    if(isset($form) && $form!=null){
        if($form->id!=null){
            $route = route('forms.update', ['id' => $form->id]);
            $method = 'PUT';
        }
        $noFilter = false;
    }else{
        $noFilter = true;
        $form = new App\Models\FormTemplate();
    }

@endphp

@if($noFilter)
    @section('scripts')
        <script>
            $(document).ready(function () {
                $('#save, #open-history, .form-holder > .filter').hide();
            });
        </script>
    @endsection
@endif

@section('styles')
    <link href="{{ asset("css/template/blanko-form-builder.css") }}" rel="stylesheet">
@endsection

@section('content')
<form  class="" role="form" method="POST" action="{{$route}}">
    {{ csrf_field() }}
                <input type="hidden" name="_method" value="{{ $method }}">
                <input type="hidden" name="containers" value="{{ isset($containers) ? $containers : old('containers') }}" required>
    <div class="row">
        <div class="col-sm-12">
            @if ($form->id != 1) {{-- show only if isn't a first form (register form) --}}
            <div class="white-box">
                <h3 class="box-title m-b-20">New Form Type</h3>

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
                        <select class="form-control" name="status">
                            <option value="1" {{ (isset($form) && $form->status == 1 ? "selected" : "") }} >Active</option>
                            <option value="0" {{ (isset($form) && $form->status == 0 ? "selected" : "") }} >Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="white-box">
                {{--<h3 class="box-title m-b-0">Registration Form2</h3>--}}
                <div id="drag-container"></div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="white-box">
            <button type="submit" class="btn btn-success center-block waves-effect waves-light pull-right" id="submit-form"><i class="fa fa-check m-r-20"></i>Submit Form</button>
            <div class="clearfix"></div>
        </div>
    </div>
</form>
<script>
    var username = '{{ Auth::user()->name }}';
</script>
<script src="{{ asset("js/template/signature_pad.js") }}"></script>
<script src="{{ asset("js/template/dropzone.js") }}"></script>

<script src="{{ asset("js/template/blanko-form-builder.js") }}"></script>
<script src="{{ asset("js/template/blanko-form-creator.js") }}"></script>
<script src="{{ asset("js/template/blanko-form-checkpoint.js") }}"></script>
<script src="{{ asset("js/template/jquery.mask.min.js") }}"></script>


<script>
    $('form').submit(function(e){
        e.preventDefault();
        $('input[name=containers]').val(toJson());
        $(this).unbind().submit();
    });

    if($('input[name=containers]').val() != ''){
        createTabs($('input[name=containers]').val(), false);
    }else{
        createTabs(toJson());
    }
    
</script>
@endsection
