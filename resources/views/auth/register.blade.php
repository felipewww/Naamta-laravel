@extends('layouts.login')

@section('content')

<form class="form-horizontal form-material" role="form" method="POST" action="{{ route('register') }}">
    <h3 class="box-title m-b-20">Register</h3>

    {{ csrf_field() }}

    <div class="form-group{{ $errors->has('company') ? ' has-error' : '' }}">
        <div class="col-md-12">
            <input id="company" type="text" class="form-control" name="company" value="{{ old('company') }}" placeholder="Company" required autofocus>

            @if ($errors->has('company'))
                <span class="help-block">
                    <strong>{{ $errors->first('companies') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
        <div class="col-md-12">
            <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}"  placeholder="Name" required>

            @if ($errors->has('name'))
                <span class="help-block">
                    <strong>{{ $errors->first('name') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
        <div class="col-md-12">
            <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="Email" required>

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

    <div class="form-group">
        <div class="col-md-12">
            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="Confirm Password" required>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-12 col-md-offset-4">
            <button type="submit" class="btn btn-primary">
                Register
            </button>
        </div>
    </div>
</form>
@endsection
