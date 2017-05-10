@extends('layouts.app')

@php
    $page = "emails";
    
    $route = route('emails.store');
    $method = 'POST';
    if(isset($email) && $email!=null){
        $method = 'PUT';
        $route = route('emails.update', ['id' => $email->id]);
        
    }else{
        $email = new App\Models\EmailTemplate();
    }

@endphp

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Add Email</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ $route }}">
                        {{ csrf_field() }}
                        <input type="hidden" name="_method" value="{{ $method }}">
                        <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                            <label for="title" class="col-md-4 control-label">Title</label>

                            <div class="col-md-6">
                                <input id="title" type="text" class="form-control" name="title" value="{{ $email->title!=null ? $email->title : old('title') }}" required>

                                @if ($errors->has('title'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('title') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('text') ? ' has-error' : '' }}">
                            <label for="text" class="col-md-4 control-label">Text</label>
                            <div class="col-md-6">
                                <textarea id="text" type="text" class="form-control" name="text" required>{{ $email->text!=null ? $email->text : old('text') }}</textarea>

                                @if ($errors->has('text'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('text') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
                            <label for="text" class="col-md-4 control-label">Status</label>
                            <div class="col-md-6">
                                <select class="form-control" name="status">
                                    <option value="1" {{ (isset($email) && $email->status == 1 ? "selected" : "") }} >Active</option>
                                    <option value="0" {{ (isset($email) && $email->status == 0 ? "selected" : "") }} >Inactive</option>
                                </select>
                                @if ($errors->has('status'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('status') }}</strong>
                                    </span>
                                @endif
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
