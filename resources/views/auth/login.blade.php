@extends('layouts.login')

@section('content')
<form class="form-horizontal form-material" id="loginform" role="form" method="POST" action="{{ route('login') }}">
    {{ csrf_field() }}
    <h3 class="box-title m-b-20">Log in</h3>

    @if(session('resend'))
        <div class="alert text-center" style="border: 1px solid orangered">
            Forwarded email. Please check your email and try to sign in again.
        </div>
    @endif

    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
        <div class="col-md-12">
            @if(session('disallowed'))
                <div class="alert alert-danger">Your Application is no longer available. Please, contact Naamta.</div>
                {{--{{ dd('$errors') }}--}}
            @endif
            <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="Email Address" required autofocus>
            @if ($errors->has('email'))
                <span class="help-block">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
        <div class="col-md-12">
            <input id="password" type="password" class="form-control" name="password" placeholder="Password" required>

            @if ($errors->has('password'))
                <span class="help-block">
                    <strong>{{ $errors->first('password') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="form-group text-center m-t-20">
        <div class="col-xs-12">
            <button class="btn btn-info btn-lg btn-block text-uppercase waves-effect waves-light" type="submit">Log In</button>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-4">
            <a href="{{ route('register') }}" id="to-recover" class="text-dark pull-left"> Register</a>
        </div>
        <div class="col-md-8">
            <a href="{{ route('password.request') }}" id="to-recover" class="text-dark pull-right"> Forgot your password?</a>
        </div>
    </div>

</form>
@endsection
