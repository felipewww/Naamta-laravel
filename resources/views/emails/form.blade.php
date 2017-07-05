@extends('layouts.app')

@section('styles')
    <link href="https://cdn.quilljs.com/1.2.4/quill.snow.css" rel="stylesheet">
@endsection

@section('scripts')
    {{--<script type="text/javascript" src="{{ asset('js/quill.js') }}"></script>--}}
    <script type="text/javascript" src="{{ asset('js/Emails.js') }}"></script>
    <!-- Include the Quill library -->
    <script src="https://cdn.quilljs.com/1.2.4/quill.js"></script>
@endsection

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
                            <label for="text" class="col-md-4 control-label">Shortcodes</label>
                            <div class="col-md-6 m-b-10">
                                    <a class="btn btn-default shortcode" onclick="insertTextAtCursor('[ClientName]')"><small>Client Name</small></a>
                                    <a class="btn btn-default shortcode" onclick="insertTextAtCursor('[ClientEmail]')"><small> Client Email</small></a>
                                    <a class="btn btn-default shortcode" onclick="insertTextAtCursor('[ClientCompany]')"><small>Client Company</small></a>
                            </div>
                            <label for="text" class="col-md-4 control-label">E-mail Text<br></label>
                            <div class="col-md-6">
                                {{--<textarea id="text" type="text" class="form-control" name="text" required>{{ $email->text!=null ? $email->text : old('text') }}</textarea>--}}
                                <textarea id="text-hidden" type="text" class="hidden" name="text" required></textarea>
                                
                                <div id="text">{!! $email->text !!}</div>
{{--{{$asd}}--}}
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
                                <button id="sendform" type="submit" class="btn btn-primary">
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
<script type="text/javascript">
var sel;
function insertTextAtCursor(text) {
    var range, html;
    if (window.getSelection) {
        sel = window.getSelection();
        if($(sel.baseNode).closest('#text')[0] == $('#text')[0] ){
            if (sel.getRangeAt && sel.rangeCount) {
                range = sel.getRangeAt(0);
                range.deleteContents();
                range.insertNode( document.createTextNode(text) );
            }
        }
    } else if (document.selection && document.selection.createRange) {
        document.selection.createRange().text = text;
    }
}
</script>
@endsection
