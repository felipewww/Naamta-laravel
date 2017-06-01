@extends('emails.sendmail_template')

@section('title')
    Application Step updated
@endsection

@section('content')
    {!! $text !!}
@endsection