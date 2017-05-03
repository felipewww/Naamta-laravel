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
                   <form class="form-horizontal" role="form" method="POST" action="{{ $route }}">
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
                        <div class="form-group" style="margin-top: 15px;">
                            <label for="title" class="col-md-4 control-label">Responsible</label>
                            <div class="col-md-6">
                                <select class="form-control" name="staff_id" required="">
                                    @foreach($staffs as $staff)
                                        <option value="{{ $staff->id }}" {{ (isset($application) && $application->staff_id == $staff->id ? "selected" : "") }}>{{ $staff->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
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
                                                    <input type="hidden" name="users_application[]" value="{{ $uApp->user->id }}, {{ $uApp->type->id }}">
                                                </td>
                                                <td>{{ $uApp->type->title }}</td>
                                                <td class="text-nowrap">
                                                    <a href="#" onclick="event.preventDefault();$(this).parent().parent().remove();" data-toggle="tooltip" data-original-title="Delete" class="removeTr"> <i class="fa fa-close text-danger"></i> </a>
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
                                    <option value="1" {{ (isset($email) && $email->status == 1 ? "selected" : "") }} >Active</option>
                                    <option value="0" {{ (isset($email) && $email->status == 0 ? "selected" : "") }} >Inactive</option>
                                </select>
                                @if ($errors->has('status'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('status') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Submit
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
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
</script>
@endsection
        