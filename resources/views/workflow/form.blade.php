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
            <button class="btn btn-primary pull-right btn-submit submit-form">Submit Form</button>
            <a href="/application/{{$appID}}/dashboard" class="btn btn-danger m-r-20 pull-right btn-submit">Back</a>
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
<script src="{{ asset("js/template/jquery.mask.min.js") }}"></script>

<script>

    {{--{{ dd(\Illuminate\Support\Facades\Auth::user()->isAdmin()) }}--}}
{{--    {{dd(\Illuminate\Support\Facades\Auth::user()->isClient())}}--}}

    //If the logged user isn't a admin
    {{--{{ $isResponsible = (\Illuminate\Support\Facades\Auth::user()->isAdmin() || \Illuminate\Support\Facades\Auth::user()->isStaff()) ? false : true }}--}}
{{--    {{ $isResponsible = \Illuminate\Support\Facades\Auth::user()->isClient() }}--}}

    createTabs($('input[name=containers]').val(), true, "{{  \Illuminate\Support\Facades\Auth::user()->isClient() }}");

    //var isClient =  \Illuminate\Support\Facades\Auth::user()->isClient();

    @if(!$allowEditForm)
        $('input, select, radio, textarea, checkbox, option').prop('disabled', true);
        $('.submit-form').attr('disabled', 'disabled').css('opacity', '0.4');
    @endif

    @if(!$isResponsible)
        Script.env = "{{ app('env') }}";
        $('.submit-form').attr('disabled', 'disabled').css('opacity', '0.4');
        $('input, select, radio, textarea, checkbox, option').prop('disabled', true);
        $('canvas').css('pointer-events', 'none');
        $('button.clear').remove();
        $('.dropzone, .remove-file').remove();
        $('.comment-msg, .is-incorrect').removeAttr('disabled');
        $('.is-incorrect').css('display', '0');
        $('[id*="text-field"] .update-value').each(function(){
            $(this).after('<p class="read-value">'+ $(this).val() +'</p>');
            $(this).hide();
        })
    @else
        $('.drag-validate').css('display', 'none');
    @endif
    
    $('.submit-form').on('click', function(e){
        e.preventDefault();
        if( validateForm() ){
            workflow.sendForm();
        }else{
            console.log('You shall not pass')
        }
        
    });

    {{--@if(!$isResponsible)--}}
        {{--$('.submit-form').attr('disabled', 'disabled').css('opacity', '0.4');--}}
        {{--$('.is-incorrect').css('display', '0');--}}
    {{--@else--}}
        {{--$('.drag-validate').css('display', 'none')--}}
    {{--@endif--}}
</script>

@endsection

@section('scripts')
    <script src="{{ asset("js/workflow.js") }}"></script>
@endsection
