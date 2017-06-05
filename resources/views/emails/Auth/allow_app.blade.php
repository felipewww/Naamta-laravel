@extends('emails.sendmail_template')

@section('title')
    Application approved!
@endsection

@section('content')
    Hello {{ $user->name }},
    <br>
    <br>
    Your company's application <strong>{{ $client->company }}</strong> was approved. Now, you can access, see forms, steps and etc.
    <br>
    <br>
    Sign in <a href="{{ asset('/login') }}">here!</a>
@endsection
