@extends('layouts.app')

@php
    $page = "users";
    
    $route = route('users.store');
    $method = 'POST';
    if(isset($user) && $user!=null){
        $method = 'PUT';
        $route = route('users.update', ['id' => $user->id]);
        
    }else{
        $email = new App\Models\User();
    }
@endphp

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">System Users</div>
                <div class="panel-body">
                   <form class="form-horizontal" role="form" method="POST" action="{{ $route }}">
                        {{ csrf_field() }}
                        <input type="hidden" name="_method" value="{{ $method }}">
                        <div class="row" style="margin-top: 15px;">
                            <div class="col-md-12">
                                <input type="text" class="form-control" name="name" disabled placeholder="Name" value="{{ $user->name!=null ? $user->name : old('name') }}" >
                            </div>
                        </div>
                        <div class="row" style="margin-top: 15px;">
                            <div class="col-md-12">
                                <input type="text" class="form-control" name="email" disabled placeholder="Email" value="{{ $user->email!=null ? $user->email : old('email') }}">
                            </div>
                        </div>
                        <div class="row" style="margin-top: 15px;">
                            <div class="col-md-12">
                                <select class="form-control" name="user_type" required="">
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ (isset($user) && $user->roles()->first()->id == $role->id ? "selected" : "") }}>{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row"style="margin-top: 15px;" >
                            <div class="col-md-12">
                                <select class="form-control" name="status">
                                    <option value="1" {{ (isset($userType) && $userType->status == 1 ? "selected" : "") }} >Active</option>
                                    <option value="0" {{ (isset($userType) && $userType->status == 0 ? "selected" : "") }} >Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="row"style="margin-top: 15px;" >
                            <div class="col-md-12">
                                <select class="form-control" name="see_apps">
                                    <option value="1" {{ (isset($user) && $user->see_apps == 1 ? "selected" : "") }} >See all apps</option>
                                    <option value="0" {{ (isset($user) && $user->see_apps == 0 ? "selected" : "") }} >See only my apps</option>
                                </select>
                            </div>
                        </div>
                        <div class="row"style="margin-top: 15px;" >
                            <div class="col-md-12">
                                {{ csrf_field() }} 
                                <input type='submit' name='submit' class="btn btn-primary" value='Submit'>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
