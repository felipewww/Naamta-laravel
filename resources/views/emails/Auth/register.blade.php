@extends('emails.sendmail_template')

@section('title')
    Register Confirmation
@endsection

@section('content')
    Hello <span style="font-weight: bold; font-style: italic; ">{{ $user->name }}</span>,
    <br>
    <br>
    We received a register request from your e-mail.
    <br>
    <br>
    Please, confirm your register <a href="{{ asset('/register/confirmation/'.$token) }}">clicking here</a>. If you don't recognize this action, please ignore this e-mail.
@endsection