@extends('layouts.app')

@php
    $page = "steps";

    $route = route('steps.store');
    $method = 'POST';

    if(isset($vars->step) && $vars->step!=null){
        $method = 'PUT';
        $route = route('steps.update', ['id' => $vars->step->id]);
    }else{
        $vars->step         = new App\Models\Step();
    }

@endphp

@include('partials.chosen')
@include('partials.dataTables')

@section('scripts')
    <script type="text/javascript" src="{{ asset('js/Steps.js')  }}"></script>
    <script type="text/javascript">
        Steps.from = '{{$vars->stepFrom}}';
    </script>
@endsection

@section('content')
<div class="row">
    <div class="col-sm-8 col-sm-offset-2">
        <div class="white-box">
            <div class="panel panel-default">
                <div class="panel-heading">Steps</div>
                <div class="panel-body">
                   <form class="form-horizontal" role="form" method="POST" action="{{ $route }}">
                       {{ csrf_field() }}

                       <input type="hidden" name="_stepFrom" value="{{$vars->stepFrom}}">
                       <input type="hidden" name="_method" value="{{$method}}">
                       <input type="hidden" name="application_id" value="{{$vars->application->id}}">

                       <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                           <label class="col-md-12">Title</label>
                           <div class="col-md-12">
                               <input required type="text" class="form-control" name="title" placeholder="Title" value="{{ $vars->step->title }}">
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
                               <input required type="text" class="form-control" name="description" placeholder="Description" value="{{ $vars->step->description }}">
                               @if ($errors->has('title'))
                                   <span class="help-block">
                                        <strong>{{ $errors->first('title') }}</strong>
                                    </span>
                               @endif
                           </div>
                       </div>

                       {{--<div class="form-group {{ $errors->has('title') ? ' has-error' : '' }}" id="forms">--}}
                           {{--<label class="col-sm-12">Previous Step</label>--}}
                           {{--<div class="col-sm-12">--}}
                               {{--<select class="form-control" name="previous_step">--}}
                                   {{--<option value="">Select previous step (if exists)</option>--}}
                                   {{--@foreach($vars->steps as $step)--}}
                                       {{--<option {{ $step->selected  }} value="{{ $step->id }}">{{  $step->title }}</option>--}}
                                   {{--@endforeach--}}
                               {{--</select>--}}

                               {{--@if ($errors->has('title'))--}}
                                   {{--<span class="help-block">--}}
                                        {{--<strong>{{ $errors->first('title') }}</strong>--}}
                                    {{--</span>--}}
                               {{--@endif--}}
                           {{--</div>--}}
                       {{--</div>--}}

                       <div class="form-group {{ $errors->has('title') ? ' has-error' : '' }}" id="forms">
                           <label class="col-sm-12">Responsible</label>
                           <div class="col-sm-12">
                               <select class="form-control" name="responsible">
                                   <option value="">Select an Responsible user type</option>
                                   @foreach($vars->userTypes as $utype)
                                       <option {{ $utype->selected  }} value="{{ $utype->id }}">{{  $utype->title }}</option>
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
                               <input {{ ( $vars->stepFrom == 'clone' || $vars->stepFrom == 'application' ) ? 'disabled="disabled"' : '' }} {{ ( $vars->morphs_from[0] == $vars->step->morphs_from ) ? 'checked' : ''  }} type="radio" required name="morphs_from" id="type_form" value="{{ $vars->morphs_from[0] }}">
                               <label for="type_form"> Form </label>
                           </div>
                           <div class="radio radio-success col-sm-2">
                               <input {{ ( $vars->stepFrom == 'clone' || $vars->stepFrom == 'application' ) ? 'disabled="disabled"' : '' }} {{ ( $vars->morphs_from[1] == $vars->step->morphs_from ) ? 'checked' : ''  }} type="radio" required name="morphs_from" id="type_approval" value="{{ $vars->morphs_from[1] }}" >
                               <label for="type_approval"> Approval </label>
                           </div>
                           @if ($errors->has('title'))
                               <span class="help-block">
                                    <strong>{{ $errors->first('title') }}</strong>
                                </span>
                           @endif
                       </div>
                       @if( $vars->stepFrom == 'application' )
                           @if( isset($vars->morphItem) )
                                @if( $vars->morphItem->deleted_at )
                                    <div class="alert alert-danger">
                                    The default <strong>{{$vars->itemName}}</strong> for this step was excluded at {{ \Carbon\Carbon::parse($vars->morphItem->deleted_at)->format('Y/m/d')}}
                                    but it still is used in this step.<br>You can see the selected item <a target="_blank" href="{{ $vars->seeItemLink }}">Clicking here</a>
                                @else
                                    <div class="alert alert-dark">
                                    You can see the selected <strong>{{$vars->itemName}}</strong> <a target="_blank" href="{{ $vars->seeItemLink }}">clicking here</a>
                                @endif
                                    </div> {{-- close both div.alert--}}
                           @endif

                           <div class="form-group select_list" id="list_forms">
                               <label class="col-sm-12">Select a Form</label>
                               <div class="col-sm-12">
                                   <select class="form-control chosen" name="morphs_items" multiple>
                                       <option value="">Select a Form</option>
                                       @foreach($forms as $form)
                                           <option {{ $form->disabled }} {{ $form->selected }} value="{{ $form->id }}">{{  $form->name }}</option>
                                       @endforeach
                                   </select>
                               </div>
                           </div>

                           <div class="form-group select_list" id="list_approvals">
                               <label class="col-sm-12">Select a Approval</label>
                               <div class="col-sm-12">
                                   <select class="form-control chosen" name="morphs_items[]">
                                       <option value="">Select a Approval</option>
                                       @foreach($approvals as $approval)
                                           <option {{ $approval->selected }} value="{{ $approval->id }}">{{ $approval->title }}</option>
                                       @endforeach
                                   </select>
                               </div>
                           </div>
                       @endif

                       <div class="form-group" id="show-approval">
                           <label class="col-sm-12">Uses E-mail?</label>
                           <div class="col-sm-12" id="approval">
                               <div id="emails_success">
                                   <h4>On Step Success</h4>
                                   <span class="btn btn-success button_add_mail" href="#">Add e-mail</span>
                               </div>
                                <hr>
                               <div id="emails_rejected">
                                   <h4>On Step Rejected</h4>
                                   <span class="btn btn-success button_add_mail" href="#">Add e-mail</span>
                               </div>
                              <hr>
                               <div id="mail-component" style="visibility: hidden;">

                                   <div class="col-sm-4">
                                    <h6>E-mail Template</h6>
                                   </div>
                                   <div class="col-sm-8">
                                    <h6>User Types</h6>
                                   </div>
                                  <div class="col-sm-4">
                                     <select class="form-control">
                                         <option>Select an E-mail template</option>
                                         @foreach($vars->emailTemplates as $tpl)
                                             <option value="{{$tpl->id}}">{{ $tpl->title }}</option>
                                         @endforeach
                                     </select>
                                   </div>
                                   <div class="col-sm-6">
                                   <select multiple class="form-control">
                                       <option>Select an user type</option>
                                       @foreach($vars->userTypes as $utype)
                                           <option value="{{$utype->id}}">{{ $utype->title }}</option>
                                       @endforeach
                                   </select>
                                   </div>
                                   <div class="col-sm-2">
                                   <span class="btn btn-danger pull-right delete_component">Delete</span>
                                   </div>
                                   <div class="clearfix"></div>
                               </div>

                               @if( $vars->usedEmails )
                                   <script>Steps.action = 'edit'</script>
                                   <div id="eventsEmails">
                                   @foreach($vars->usedEmails as $event => $usedMail)
                                       @foreach($usedMail as $email_id => $staffs_id)
                                           <div class="mail-component-{{ $event }}">
                                               <span class="btn btn-danger pull-right delete_component">Delete</span>
                                               <select name="usedemails[{{$event}}][{{$email_id}}][id]">
                                                   @foreach($vars->emailTemplates as $tpl)
                                                       <option {{ ( $tpl->id == $email_id ) ? 'selected' : ''  }} value="{{$tpl->id}}">{{ $tpl->title }}</option>
                                                   @endforeach
                                               </select>
                                               <select name="usedemails[{{$event}}][{{$email_id}}][staffs][]" multiple>
                                                   <option>Select an user type</option>
                                                   @foreach($vars->userTypes as $utype)
                                                       <option {{ ( gettype(array_search($utype->id, $staffs_id)) == 'integer'  ? 'selected' : '') }} value="{{$utype->id}}">{{ $utype->title }}</option>
                                                   @endforeach
                                               </select>
                                           </div>
                                       @endforeach
                                   @endforeach
                                   </div>
                               @endif
                           </div>
                       </div>


                       <div class="form-group text-center m-t-20">
                           <a href="{{ $backLink }}" class="btn btn-danger waves-effect waves-light m-r-20 ">Cancel</a>
                           <button type="submit" class="btn btn-save waves-effect waves-light m-r-20" value="Save">Save</button>
                       </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
