@extends('layouts.app')

<?php
    $status = ( $withAction ) ? '' : 'disabled="disabled"';

    if(!isset($form)){
        $form = new \App\Models\ClientFirstForm();
    }
?>

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="">
                <div class="white-box">
                    <h3 class="box-title m-b-0"><b>Registration form</b></h3>

                    <form  class="form-horizontal" method="post" action="{{ asset('applications/client/first_form') }}">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label class="col-md-12">First field</label>
                            <div class="col-md-12">
                                <input {{$status}} required type="text" class="form-control" name="first_field" placeholder="First field" value="{{$form->first_field}}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-12">Second field</label>
                            <div class="col-md-12">
                                <input {{$status}} required type="text" class="form-control" name="second_field" placeholder="Second field" value="{{$form->second_field}}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-12">Third field</label>
                            <div class="col-md-12">
                                <input {{$status}} required type="text" class="form-control" name="third_field" placeholder="Third field" value="{{$form->third_field}}">
                            </div>
                        </div>

                        @if($withAction)
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Submit
                                </button>
                            </div>
                        </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection