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
                                <input type="text" class="form-control" name="name" placeholder="Name" value="{{ $user->name!=null ? $user->name : old('name') }}" >
                            </div>
                        </div>
                        <div class="row" style="margin-top: 15px;">
                            <div class="col-md-12">
                                <input type="text" class="form-control" name="email" disabled placeholder="Email" value="{{ $user->email!=null ? $user->email : old('email') }}">
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
