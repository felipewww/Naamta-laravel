@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">System Users</div>

                <div class="panel-body">
                    
                    <!-- Mensagem de sucesso e erro -->
                    @if(Session::has('error_msg'))
                        <div class="alert alert-danger">
                            <strong>Error</strong> {{ Session::get('error_msg') }}
                        </div>
                    @elseif(Session::has('success_msg'))
                        <div class="alert alert-success">
                            <strong>Success!</strong> {{ Session::get('success_msg') }}
                        </div>
                    @endif
                    <!-- Fim da mensagem de sucesso e erro -->
                    
                    @if(isset($user))
                        <form action="{{ url('users/'.$user->id.'/edit') }}" method="POST">
                    @else
                        <form action="{{ url('users/create') }}" method="POST">
                    @endif
                    
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
                                    <option value="">Choose One...</option>
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
