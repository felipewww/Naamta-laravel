@extends('layouts.app')

@php
    {{--$route = '';--}}
    {{--$method = '';--}}
    $page = "steps";

    $route = route('steps.store');
    $method = 'POST';
    if(isset($step) && $step!=null){
        $method = 'PUT';
        $route = route('steps.update', ['id' => $step->id]);
    }else{
        $email = new App\Models\User();
    }

@endphp

@include('partials.chosen')
@include('partials.dataTables')

@section('scripts')
    <script type="text/javascript" src="{{ asset('js/Steps.js')  }}"></script>
@endsection

@section('content')
<div class="row">
    <div class="col-sm-8 col-sm-offset-2">
        <div class="white-box">
            <div class="panel panel-default">
                <div class="panel-heading">System Users</div>
                <div class="panel-body">
                   <form class="form-horizontal" role="form" method="POST" action="{{ $route }}">
                       {{ csrf_field() }}

                       <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                           <label class="col-md-12">Title</label>
                           <div class="col-md-12">
                               <input required type="text" class="form-control" name="title" placeholder="Title" value="">
                           @if ($errors->has('title'))
                               <span class="help-block">
                                        <strong>{{ $errors->first('title') }}</strong>
                                    </span>
                           @endif
                            </div>
                       </div>

                       <div class="form-group {{ $errors->has('title') ? ' has-error' : '' }}">
                           <label class="col-md-12">Description</label>
                           <div class="col-md-12">
                               <input required type="text" class="form-control" name="description" placeholder="Description" value="">
                               @if ($errors->has('title'))
                                   <span class="help-block">
                                        <strong>{{ $errors->first('title') }}</strong>
                                    </span>
                               @endif
                           </div>
                       </div>

                       <div class="form-group {{ $errors->has('title') ? ' has-error' : '' }}" id="forms">
                           <label class="col-sm-12">Select a Form</label>
                           <div class="col-sm-12">
                               <select class="form-control" name="previous_step">
                                   <option value="">Select previous step (if exists)</option>
                                   @foreach($vars->steps as $step)
                                       <option value="{{  $step->id }}">{{  $step->title }}</option>
                                   @endforeach
                               </select>

                               @if ($errors->has('title'))
                                   <span class="help-block">
                                        <strong>{{ $errors->first('title') }}</strong>
                                    </span>
                               @endif
                           </div>
                       </div>

                       <div class="form-group {{ $errors->has('title') ? ' has-error' : '' }}">
                           <div class="radio radio-success col-sm-2">
                               <input type="radio" required name="morphs_from" id="type_form" value="{{ $vars->morphs_from[0] }}">
                               <label for="type_form"> Form </label>
                           </div>
                           <div class="radio radio-success col-sm-2">
                               <input type="radio" required name="morphs_from" id="type_screen" value="{{ $vars->morphs_from[1] }}" >
                               <label for="type_screen"> Screen </label>
                           </div>
                           @if ($errors->has('title'))
                               <span class="help-block">
                                        <strong>{{ $errors->first('title') }}</strong>
                                    </span>
                           @endif
                       </div>

                       <div class="form-group" id="forms">
                           <label class="col-sm-12">Select a Form</label>
                           <div class="col-sm-12">
                               <select class="form-control" name="forms">
                                   <option>Select a Form</option>
                                   @foreach($vars->forms as $form)
                                       <option value="{{$form->id}}">{{ $form->name }}</option>
                                   @endforeach
                               </select>
                           </div>
                       </div>

                       <div class="form-group" id="show-screen">
                           <label class="col-sm-12">Select a Screen</label>
                           <div class="col-sm-12" id="screen">
                               <select class="form-control" name="screens">
                                   <option>Select a Screen</option>
                                   @foreach($vars->screens as $screen)
                                       <option value="{{$screen->id}}">{{ $screen->title }}</option>
                                   @endforeach
                               </select>
                           </div>
                       </div>

                       <div class="form-group" id="show-screen">
                           <label class="col-sm-12">Uses E-mail?</label>
                           <div class="col-sm-12" id="screen">
                               <div id="emails_success">
                                   <h2>on step success</h2>
                                   <span class="button_add_mail" href="#">Add e-mail</span>
                               </div>

                               <div id="emails_rejected">
                                   <h2>on step rejected</h2>
                                   <span class="button_add_mail" href="#">Add e-mail</span>
                               </div>

                               <div id="mail-component" style="visibility: hidden;">
                                   <h2 class="delete_component">Delete</h2>
                                   <select style="width: 20%;">
                                       <option>Select an E-mail template</option>
                                       @foreach($vars->emailTemplates as $tpl)
                                           <option value="{{$tpl->id}}">{{ $tpl->title }}</option>
                                       @endforeach
                                   </select>
                                   <select multiple style="width: 80%;">
                                       <option>Select an user type</option>
                                       @foreach($vars->userTypes as $utype)
                                           <option value="{{$utype->id}}">{{ $utype->title }}</option>
                                       @endforeach
                                   </select>
                               </div>
                           </div>
                       </div>

                       {{--<h1>Uses email?</h1>--}}
                       {{--<div class="row" style="margin-top: 15px;">--}}
                       {{--</div>--}}

                       {{--<div id="emails_success">--}}
                           {{--<h2>on step success</h2>--}}
                           {{--<span class="button_add_mail" href="#">Add e-mail</span>--}}
                       {{--</div>--}}

                       {{--<div id="emails_rejected">--}}
                           {{--<h2>on step rejected</h2>--}}
                           {{--<span class="button_add_mail" href="#">Add e-mail</span>--}}
                       {{--</div>--}}

                       {{--<div id="mail-component" style="visibility: hidden;">--}}
                           {{--<h2 class="delete_component">Delete</h2>--}}
                           {{--<select style="width: 20%;">--}}
                               {{--<option>Select an E-mail template</option>--}}
                               {{--@foreach($vars->emailTemplates as $tpl)--}}
                                   {{--<option value="{{$tpl->id}}">{{ $tpl->title }}</option>--}}
                               {{--@endforeach--}}
                           {{--</select>--}}
                           {{--<select multiple style="width: 80%;">--}}
                               {{--<option>Select an user type</option>--}}
                               {{--@foreach($vars->userTypes as $utype)--}}
                                   {{--<option value="{{$utype->id}}">{{ $utype->title }}</option>--}}
                               {{--@endforeach--}}
                           {{--</select>--}}
                       {{--</div>--}}

                       <div class="form-group text-center m-t-20">
                           <a href="/steps" class="btn btn-danger waves-effect waves-light m-r-20 ">Cancel</a>
                           <input type="submit" class="btn btn-success waves-effect waves-light m-r-20" value="Save">
                       </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
