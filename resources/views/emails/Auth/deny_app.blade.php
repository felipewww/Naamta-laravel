@extends('emails.sendmail_template')

@section('title')
    Application Rejected
@endsection

@section('content')
    Hello {{ $user->name }},
    <br>
    <br>
    Sorry, your company's application <strong>({{ $client->company }})</strong> was reproved.
    Please, access the panel, validate if all information was filled correctly and send again.
@endsection
