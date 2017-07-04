@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- .row -->
    <div class="row">
        <div class="col-sm-12">
            <div class="white-box">
                <h3 class="box-title m-b-20" style="text-align: left;">
                    Your account is not valid yet.
                    <br>
                    We sent an e-mail to {{$user->email}}. Please, verify your e-mail.
                    <br>
                    If you didn't receive this e-mail, <a href="/register/confirmation/resend/{{$token}}/{{$user->id}}">click here</a> to resend it.
                </h3>
            </div>
        </div>
    </div>
</div>
@endsection