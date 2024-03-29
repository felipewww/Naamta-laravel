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
    <div class="col-sm-12">
        <div class="white-box">
            <h3 class="box-title m-b-20">Form Preview</h3>
            <div id="drag-container" class="preview-form"></div>
        </div>
    </div>
</div>
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
var appFolder = "teste";
var json = '<?= isset($containers) ? str_replace("'", "’", $containers) : old('containers') ?>';

<?php
//    $containers = str_replace("'", "\'", $containers );
//    $containers = str_replace('"', '\"', $containers );
//    $containers = str_replace('\n', '\\\n', $containers );
?>
createTabs(json, true, "{{ Auth::user()->isClient() ? true : false }}");
</script>
@endsection
