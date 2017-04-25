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
        <div class="col-md-12">
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
                            <label for="title" class="col-md-4 control-label">Staff</label>
                            <div class="col-md-6">
                                <select class="form-control" name="staff_id" required="">
                                    @foreach($staffs as $staff)
                                        <option value="{{ $staff->id }}" {{ (isset($application) && $application->staff_id == $staff->id ? "selected" : "") }}>{{ $staff->name }}</option>
                                    @endforeach
                                </select>
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
