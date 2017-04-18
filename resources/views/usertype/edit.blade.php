@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Users Type</div>

                <div class="panel-body">
                    
                    <!-- Errors -->
                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
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
                    
                    @if(isset($usertype))
                        <form action="{{ url('userstype/'.$usertype->id.'/edit') }}" method="POST">
                    @else
                        <form action="{{ url('userstype/create') }}" method="POST">
                    @endif
                    
                        <div class="row" style="margin-top: 15px;">
                            <div class="col-md-12">
                                <input type="text" class="form-control" name="name" placeholder="Title" required="" @if(isset($usertype)) value="{{ $usertype->title }}" @endif >
                            </div>
                        </div>
                        <div class="row"style="margin-top: 15px;" >
                            <div class="col-md-12">
                                <select class="form-control" name="status">
                                    <option value="1" @if(isset($usertype) AND $usertype->status == 1) selected @endif >Active</option>
                                    <option value="0" @if(isset($usertype) AND $usertype->status == 0) selected @endif >Inactive</option>
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
