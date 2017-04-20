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
                                <input type="text" class="form-control" name="name" disabled placeholder="Name"  @if(isset($user)) value="{{ $user->name }}" @endif >
                            </div>
                        </div>
                        <div class="row" style="margin-top: 15px;">
                            <div class="col-md-12">
                                <input type="text" class="form-control" name="email" disabled placeholder="Email" @if(isset($user)) value="{{ $user->email }}" @endif >
                            </div>
                        </div>
                        <div class="row" style="margin-top: 15px;">
                            <div class="col-md-12">
                                <select class="form-control" name="user_type" required="">
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" @if(isset($user) AND $user->roles()->first()->name == $role->name) selected @endif >{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row"style="margin-top: 15px;" >
                            <div class="col-md-12">
                                <select class="form-control" name="status">
                                    <option value="1" @if(isset($user) AND $user->status == 1) selected @endif  >Active</option>
                                    <option value="0" @if(isset($user) AND $user->status == 0) selected @endif >Inactive</option>
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
