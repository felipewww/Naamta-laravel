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

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Add Form Tempalte</div>

                <div class="panel-body">
                    
                    <form role="form" id="form-input" onsubmit="event.preventDefault(); OnSubmitGenerateContainer();">
                        <div class="form-group">
                            <button type="submit" id="btn">Add Container</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <form  class="form-horizontal" role="form" method="POST" action="{{$route}}">
                {{ csrf_field() }}
                <input type="hidden" name="_method" value="{{ $method }}">
                @if($form->id!=null)
                    <input type="hidden" name="id" value="{{ $form->id }}">
                @endif

                <div id="container"></div>
                
                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                        <button type="submit" class="btn btn-primary">
                            Generate Form
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Add Form Tempalte</div>
                <div class="panel-body">
                    <form role="form" id="form-input" onsubmit="event.preventDefault(); OnSubmitFormInput();">
                        {{ csrf_field() }}
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" id="required" name="required"> Required
                            </label>
                        </div>
                        <div class="form-group">
                            <label for="inputLabel">Label</label>
                            <input type="text" name="label" class="form-control" id="inputLabel" placeholder="Label">
                        </div>
                        <div class="form-group">
                            <label for="inputHelpText">Help Text</label>
                            <input type="text" name="help_text" class="form-control" id="inputHelpText" placeholder="Help Text">
                        </div>
                        <div class="form-group">
                            <label for="selectType">Type</label>
                            <select class="form-control" id="selectType" name="type">
                                <option>Text</option>
                                <option>Password</option>
                                <option>Email</option>
                                <option>Color</option>
                                <option>Tel</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="inputPlaceholder">Placeholder</label>
                            <input type="text" name="placeholder" class="form-control" id="inputPlaceholder" placeholder="Placeholder">
                        </div>
                        <div class="form-group">
                            <label for="inputClass">Class</label>
                            <input type="text" name="class" class="form-control" id="inputClass" placeholder="Class">
                        </div>
                        <div class="form-group">
                            <label for="inputName">Name</label>
                            <input type="text" name="name" class="form-control" id="inputName" placeholder="Name">
                        </div>
                        <div class="form-group">
                            <label for="maxLenght">Max Lenght</label>
                            <input type="text" name="max_lenght" class="form-control" id="inputMaxLenght" placeholder="Max Lenght">
                        </div>
                        <div class="form-group">
                            <button type="submit" id="btn">Add Input</button>
                        </div>
                        
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<script>

    function OnSubmitGenerateContainer()
    {
        var container = document.getElementById("container");
        var title = "Container " + (container.getElementsByClassName("panel").length + 1);
        _generateContainer(container, title, true)

        return false;
    }

    function OnSubmitFormInput()
    {
        var container = document.getElementById("id_container_0");

        var inputObj = new Object();

        inputObj.required    = document.getElementById("required").value;
        inputObj.label       = document.getElementById("inputLabel").value;
        inputObj.help        = document.getElementById("inputHelpText").value;
        inputObj.type        = document.getElementById("selectType").value;
        inputObj.placeholder = document.getElementById("inputPlaceholder").value;
        inputObj.class       = document.getElementById("inputClass").value;
        inputObj.name        = document.getElementById("inputName").value;
        inputObj.maxLenght   = document.getElementById("inputMaxLenght").value;
        
        _generateFormInput(JSON.stringify(inputObj), container, true)

        return false;
    }

    function _generateContainer(container, title, config = false)
    {
        var panelContainer = document.createElement("div");
        panelContainer.className = "panel panel-default";

        var panelHeading = document.createElement("div");
        panelHeading.className = "panel-heading";

        var panelTitle = document.createElement("h4");
        panelTitle.innerHTML = title;

        var panelBody = document.createElement("div");
        panelBody.id = "id_" + title.replace(/[^A-Z0-9]+/ig, "_").toLowerCase();
        panelBody.className = "panel-body";

        panelHeading.appendChild(panelTitle);

        if(config){
           panelHeading.appendChild(_generateFormConfig());
        }

        panelContainer.appendChild(panelHeading);
        
        panelContainer.appendChild(panelBody);
        
        container.appendChild(panelContainer);

        return false;
    }

    function _generateFormInput(obj, container, config = false)
    {
        obj = JSON.parse(obj);

        var formGroup = document.createElement("div");
        formGroup.className = "form-group";

        var input         = document.createElement("input");
        
        input.type        = obj.type;
        input.required    = obj.required;
        input.className   = "form-control ";
        input.className  += obj.class;
        input.id          = "input_" + obj.name;
        input.placeholder = obj.placeholder;
        input.name        = obj.name;
        input.maxLength   = obj.maxLength;

        if(config){
           formGroup.appendChild(_generateFormConfig());
        }

        if(obj.label){
            formGroup.appendChild(_generateFormLabel(obj.label, obj.name));
        }

        formGroup.appendChild(input);

        if(obj.help){
            formGroup.appendChild(_generateFormHelper(obj.help));
        }

        
        container.appendChild(formGroup);

        return false;
    }

    function _generateFormLabel(label, name){
        var l = document.createElement("label");
            l.innerHTML = label;
            l.htmlFor = "input_" + name;
        return l;
    }

    function _generateFormHelper(help){
        var h = document.createElement("p");
            h.className = "help-block";
            h.innerHTML = help;
        return h;
    }

    function _generateFormConfig(){

        var da = document.createElement("div");
            da.className = "field-actions";

        var aEdit = document.createElement("a");           
            aEdit.className = "toggle-form btn icon-pencil";
            aEdit.innerHTML = "Edit";
        
        var aCopy = document.createElement("a");           
            aCopy.className = "copy-button btn icon-copy";
            aCopy.innerHTML = "+";

        var aDelete = document.createElement("a");           
            aDelete.className = "del-button btn delete-confirm";
            aDelete.innerHTML = "x";

        da.appendChild(aEdit);
        da.appendChild(aCopy);
        da.appendChild(aDelete);
        
        return da;
    }

</script>