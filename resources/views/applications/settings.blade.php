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
    <div class="row">
        <div class="col-sm-8 col-sm-offset-2">
            <div class="white-box">
                <form class="form-horizontal">
                    <h3>Application 1</h3>
                    <div class="form-group">
                        <label class="col-md-12">Description</label>
                        <div class="col-md-12">
                            <textarea class="form-control" rows="5" required="">Praesent aliquet lacus blandit massa pulvinar, sed maximus lacus accumsan. Cras faucibus ex eget massa efficitur, in fringilla quam dictum. Morbi at tempor elit. Maecenas bibendum erat justo, et cursus orci auctor in. Duis vulputate tortor id ligula posuere, sit amet dignissim arcu volutpat. </textarea>
                        </div>
                    </div>
                    <div>
                        <div class="form-group col-sm-6">
                            <label class="col-sm-12">Name</label>
                            <div class="col-sm-10">
                                <select class="form-control" required="" id="name">
                                    <option>Name 1</option>
                                    <option>Name 2</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-sm-6">
                            <label class="col-sm-12">User Type</label>
                            <div class="col-sm-10">
                                <select class="form-control" required="" id="staff">
                                    <option>Staff 1</option>
                                    <option>Staff 2</option>
                                    <option>Staff 3</option>
                                    <option>Staff 4</option>
                                    <option>Staff 5</option>
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <a  href="#" class="btn btn-custom waves-effect waves-light m-r-20" id="addtable">Add</a>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <table class="table color-table muted-table">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>User Type</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody id="filltable">
                            <tr>
                                <td>Name 1</td>
                                <td>Staff 1</td>
                                <td class="text-nowrap">
                                    <a href="#" data-toggle="tooltip" data-original-title="Delete" class="removeTr"> <i class="fa fa-close text-danger"></i> </a>
                                </td>
                            </tr>
                            <tr>
                                <td>Name 2</td>
                                <td>Staff 2</td>
                                <td class="text-nowrap">
                                    <a href="#" data-toggle="tooltip" data-original-title="Delete" class="removeTr"> <i class="fa fa-close text-danger"></i> </a>
                                </td>
                            </tr>
                            <tr>
                                <td>Name 3</td>
                                <td>Staff 4</td>
                                <td class="text-nowrap">
                                    <a href="#" data-toggle="tooltip" data-original-title="Delete" class="removeTr"> <i class="fa fa-close text-danger"></i> </a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
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
                    <div>
                        <div class="form-group text-center m-t-20">
                            <a href="list-of-applications.html" class="btn btn-danger waves-effect waves-light m-r-20 ">Cancel</a>
                            <a href="list-of-applications.html" class="btn btn-success waves-effect waves-light m-r-20 ">Save</a>
                        </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
{{--<script>--}}
    {{--function addUserType(){--}}
        {{--var userName =  $("#user_application").find(":selected");--}}
        {{--var staffName = $("#staff_application").find(":selected");--}}
        {{--if(userName.val()!="" && staffName.val()!=""){--}}
            {{--$('#filltable').prepend(--}}
                {{--'<tr>' +--}}
                    {{--'<td>'+userName.text()+'<input type="hidden" name="users_application[]" value="'+userName.val()+', '+staffName.val()+'"></td>'+--}}
                    {{--'<td>'+staffName.text()+'</td>'+--}}
                    {{--'<td class="text-nowrap">'+--}}
                        {{--'<a href="#" onclick="event.preventDefault();$(this).parent().parent().remove();" data-toggle="tooltip" data-original-title="Delete" class="removeTr"> <i class="fa fa-close text-danger"></i> </a>'+--}}
                    {{--'</td>'+--}}
                {{--'</tr>'--}}
            {{--);--}}
        {{--}--}}
        {{--userName.prop("selected", false);--}}
        {{--staffName.prop("selected", false);--}}
    {{--}--}}
{{--</script>--}}
@endsection
        