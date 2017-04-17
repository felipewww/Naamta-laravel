@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">System Users</div>

                <div class="panel-body">
                   <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->roles()->first()->name }}</td>
                                <td>@if($user->status == 0) Inactive @else Active @endif</td>
                                <td>
                                    <a class="btn btn-primary" href="{{ url('users/'.$user->id.'/edit') }}">Edit</a>  
                                    <form style="display:inline-block" method="POST" action="{{ url('users/'.$user->id.'/delete') }}"> 
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
