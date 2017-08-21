@extends('layouts.app')

@php
    $page = "applications";
    
    $route = route('applications.store');
    $method = 'POST';
    if(isset($application) && $application!=null){
        $method = 'PUT';
        $route = route('applications.update', ['id' => $application->id]);
        
    }else{
        $application = new App\Models\Application();
    }

@endphp

@section('content')
<div class="container">
    <div class="row">
        <div class="col-sm-8 col-sm-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Applications</div>
                <div class="panel-body">

                    {{--@if (count($errors) > 0)--}}
                        {{--<div class="alert alert-danger">--}}
                            {{--<ul>--}}
                                {{--@foreach ($errors->all() as $error)--}}
                                    {{--<li>{{ $error }}</li>--}}
                                {{--@endforeach--}}
                            {{--</ul>--}}
                        {{--</div>--}}
                    {{--@endif--}}

                    @if( $hasInactiveSteps )
                    <div class="alert alert-dark">
                        <strong>Warning!</strong> This application has one ore more steps inactivated and cannot be activated  yet. <a href="/applications/{{$application->id}}/edit">Click here to solve it.</a>
                    </div>
                    @endif
                   <form name="mainform" class="form-horizontal" role="form" method="POST" action="{{ $route }}">
                    {{--{!! Form::open(array('url' => $route, 'class' => 'form-horizontal')) !!}--}}
                        {{ csrf_field() }}
                        <input type="hidden" name="_method" value="{{ $method }}">
                        <div class="form-group" style="margin-top: 15px;">
                            <label for="company" class="col-md-4 control-label">Company</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="company" disabled placeholder="Company" value="{{ $application->client->company!=null ? $application->client->company : old('company') }}" >
                                @if ($errors->has('title'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('company') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: 15px;">
                            <label for="title" class="col-md-4 control-label">Description</label>
                            <div class="col-md-6">
                                <textarea class="form-control" name="description" placeholder="Description">{{ $application->description!=null ? $application->description : old('description') }}</textarea>
                            </div>
                        </div>
                        {{--<div class="form-group" style="margin-top: 15px;">--}}
                            {{--<label for="title" class="col-md-4 control-label">Responsible</label>--}}
                            {{--<div class="col-md-6">--}}
                                {{--<select class="form-control" name="staff_id" required="">--}}
                                    {{--<option value="0">Select an user</option>--}}
                                    {{--@foreach($staffs as $staff)--}}
                                        {{--<option value="{{ $staff->id }}" {{ (isset($application) && $application->staff_id == $staff->id ? "selected" : "") }}>{{ $staff->name }}</option>--}}
                                    {{--@endforeach--}}
                                {{--</select>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        <div class="form-group">
                            <div class="col-sm-6">
                                <div class="row">
                                    <label class="col-sm-12">Name</label>
                                    <div class="col-sm-10">
                                        <select class="form-control" name="" id="user_application">
                                            <option value="">Select a User</option>
                                            @foreach($staffs as $staff)
                                                <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="row">
                                    <label class="col-sm-12">User Type</label>
                                    <div class="col-sm-10">
                                        <select class="form-control" id="staff_application">
                                            <option value="">Select a Type</option>
                                            @foreach($userTypes as $userType)
                                                <option value="{{ $userType->id }}">{{$userType->title}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-2">
                                        <a href="#" onclick="event.preventDefault();addUserType()" class="btn btn-custom waves-effect waves-light m-r-20" id="addtable">Add</a>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <br>
                            <div class="col-sm-12">
                                <table class="table color-table muted-table">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>User Type</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="filltable">
                                        @foreach($usersApplication as $uApp)
                                            <tr>
                                                <td>
                                                    {{ $uApp->user->name }}
                                                    <input type="hidden" name="users_application[]" value="{{ $uApp->user->id }}, {{ $uApp->appType->id }}">
                                                </td>
                                                <td>{{ $uApp->appType->title }}</td>
                                                <td class="text-nowrap">
                                                    <a href="#" onclick="event.preventDefault();$(this).parent().parent().remove();" data-toggle="tooltip" data-original-title="Delete" class="removeTr"> <i class="fa fa-close text-danger"></i> </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="clearfix"></div>
                            <div class="panel-heading">Other receivers</div>
                            <div class="col-sm-12">

                                <div class="form-group" style="margin-top: 15px;">
                                    <label for="company" class="col-md-4 control-label">Receiver e-mail</label>
                                    <div class="col-md-6">
                                        <input id="newReceiver" type="text" class="form-control" >
                                    </div>
                                    <div class="col-sm-2">
                                        <a href="#" onclick="addOtherReceiver()" class="btn btn-custom waves-effect waves-light m-r-20">Add</a>
                                    </div>
                                </div>

                                <table class="table color-table muted-table">
                                    <thead>
                                    <tr>
                                        <th>Email</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody id="receiverTable">
                                    @foreach($application->customerEmails as $receiver)
                                        <tr>
                                            <td>{{ $receiver->email }}</td>
                                            <td class="text-nowrap">
                                                <a href="#" onclick="deleteReceiver({{ $receiver->id }}, this)" data-toggle="tooltip" data-original-title="Delete" class="removeTr"> <i class="fa fa-close text-danger"></i> </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
                            <label for="text" class="col-md-4 control-label">Status</label>
                            <div class="col-md-6">
                                <select class="form-control" name="status">
                                    {{--<option value="1" {{ (isset($email) && $email->status == 1 ? "selected" : "") }} >Active</option>--}}
                                    {{--<option value="0" {{ (isset($email) && $email->status == 0 ? "selected" : "") }} >Inactive</option>--}}
                                    <option {{ ($hasInactiveSteps) ? 'disabled' : '' }} value="1" {{ ($application->status == '1') ? "selected" : "" }} >Active</option>
                                    <option value="0" {{ ($application->status != '1') ? "selected" : ""  }} >Inactive</option>
                                </select>
                                @if ($errors->has('status'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('status') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    Submit
                                </button>
                        </div>
                    </form>
                    {{--{!! Form::close() !!}--}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>

    appID = '{{ $application->id }}';

    $(document).ready(function () {
        mainForm = document.forms.mainform;
        console.log('MF', mainForm);
    });

    var inputReceiver = document.createElement('input');

    function addUserType(){
        var userName =  $("#user_application").find(":selected");
        var staffName = $("#staff_application").find(":selected");
        if(userName.val()!="" && staffName.val()!=""){
            $('#filltable').prepend(
                '<tr>' +
                '<td>'+userName.text()+'<input type="hidden" name="users_application[]" value="'+userName.val()+', '+staffName.val()+'"></td>'+
                '<td>'+staffName.text()+'</td>'+
                '<td class="text-nowrap">'+
                '<a href="#" onclick="event.preventDefault();$(this).parent().parent().remove();" data-toggle="tooltip" data-original-title="Delete" class="removeTr"> <i class="fa fa-close text-danger"></i> </a>'+
                '</td>'+
                '</tr>'
            );
        }
        userName.prop("selected", false);
        staffName.prop("selected", false);
    }

    function addOtherReceiver() {
//        var inputReceiver = document.createElement('input');
//        inputReceiver.setAttribute('name', 'otherReceiver[]');
//        inputReceiver.setAttribute('type', 'hidden');
//        inputReceiver.setAttribute('value', email);
//
//        mainForm.appendChild(inputReceiver);

        var inputReceiver = document.getElementById("newReceiver");
        var newValue = inputReceiver.value;

        $.ajax({
            url: '/applications/'+appID+'/newReceiver',
            method: 'post',
            data: { _token: window.Laravel.csrfToken, email: newValue },
            dataType: 'json',
            success: function (data) {
                if (data.status) {
                    var table = document.getElementById('receiverTable');
                    var tr  = document.createElement('tr');

                    var td1 = document.createElement('td');
                    td1.innerHTML = newValue;

                    var td2 = document.createElement('td');
                    var a   = document.createElement('a');
                    var i   = document.createElement('i');

                    i.setAttribute('class', 'fa fa-close text-danger');

                    a.setAttribute('data-toggle','tooltip');
                    a.setAttribute('data-original-title','Delete');
                    a.setAttribute('class','removeTr');

                    a.onclick = function () {
                        deleteReceiver(data.id, this);
                    };

                    a.appendChild(i);
                    td2.appendChild(a);

                    tr.appendChild(td1);
                    tr.appendChild(td2);

                    table.appendChild(tr);
                }else{
                    Script.xmodal().setTitle("Error!").setContent("An error was occurred even request has been succeed. Please, contact system administrator").show()
                }
            },
            error: function (error) {
                Script.xmodal().setTitle("Error!").setContent("An error was occurred with this request. Please, contact system administrator").show()
            }
        });
    }

    function deleteReceiver(id, button) {
        $.ajax({
            url: '/applications/'+appID+'/deleteReceiver',
            method: 'post',
            data: { _token: window.Laravel.csrfToken, receiver_id: id },
            dataType: 'json',
            success: function (data) {
                console.log(data.status);
                if (data.status) {
                    var tr = $(button).closest('tr')[0];
//                    console.log("TR::", tr);
                    $(tr).remove();
                }else{
                    Script.xmodal().setTitle("Error!").setContent("An error was occurred even request has been succeed. Please, contact system administrator").show()
                }
                console.log("Receiver Success!");
            },
            error: function (error) {
                Script.xmodal().setTitle("Error!").setContent("An error was occurred with this request. Please, contact system administrator").show()
            }
        });
    }
</script>
@endsection
        