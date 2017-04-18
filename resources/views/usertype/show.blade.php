@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">User Types</div>

                <div class="panel-body">
                    <div class='row'>
                        <div class="col-md-3 col-md-offset-9">
                            <a class='btn btn-primary' href='{{ url('userstype/create') }}'>Add User Type</a>
                        </div>
                    </div>
                   <table class="table">
                        <thead>
                            <tr>
                                <th>Users Types</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($usertype as $user)
                            <tr>
                                <td>{{ $user->title }}</td>
                                <td>@if($user->status == 0) Inactive @else Active @endif</td>
                                <td>
                                    <a class="btn btn-primary" href="{{ url('userstype/'.$user->id.'/edit') }}">Edit</a>  
                                    <form style="display:inline-block" method="POST" action="{{ url('userstype/'.$user->id.'/delete') }}"> 
                                        {{ csrf_field() }} 
                                        <input type="submit" class="btn btn-danger" name="delete" id="delete" value="delete" onclick="return confirm('Are you sure?')" >  
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                  </table>                    
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
