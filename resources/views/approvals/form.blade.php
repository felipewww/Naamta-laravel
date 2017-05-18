@extends('layouts.app')

@php
    $page = "approvals";

    $route = route('approvals.store');
    $method = 'POST';
    if(isset($approval) && $approval!=null){
        $method = 'PUT';
        $route = route('approvals.update', ['id' => $approval->id]);

    }else{
        $approval = new App\Models\Approval();
    }

@endphp

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Add Approval</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ $route }}">
                        {{ csrf_field() }}
                        <input type="hidden" name="_method" value="{{ $method }}">
                        <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                            <label for="title" class="col-md-4 control-label">Title</label>

                            <div class="col-md-6">
                                <input id="title" type="text" class="form-control" name="title" value="{{ $approval->title!=null ? $approval->title : old('title') }}" required>

                                @if ($errors->has('title'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('title') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                            <label for="text" class="col-md-4 control-label">Description</label>
                            <div class="col-md-6">
                                <textarea id="description" type="text" class="form-control" name="description" rows="5" required>{{ $approval->description!=null ? $approval->description : old('description') }}</textarea>
                                @if ($errors->has('description'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('description') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                                <button type="submit" class="btn btn-primary center-block">
                                    Submit
                                </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
