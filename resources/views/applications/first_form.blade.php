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
            <button class="btn btn-primary pull-right btn-submit submit-form" id="submit">Submit Form</button>
            @else
            <a href="/applications/{{$appID}}/edit" class="btn btn-primary pull-right">Return to app info</a>
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
<script src="{{ asset("js/template/jquery.mask.min.js") }}"></script>

<script>
    createTabs($('input[name=containers]').val(), true, "{{$isResponsible}}");

    @if(!$isResponsible)
        Script.env = 'local';
        $('.btn-submit').attr('disabled', 'disabled').css('opacity', '0.4');
        $('input, select, radio, textarea, checkbox, option').prop('disabled', true);
        $('.dropzone, .remove-file').remove();
        $('.comment-msg, .is-incorrect').removeAttr('disabled').css('opacity', '0');
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
            workflow.firstForm();
        }else{
            console.log('You shall not pass')
        }
    });
    

</script>

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
