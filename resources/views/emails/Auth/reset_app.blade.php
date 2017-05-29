@extends('emails.sendmail_template')

@section('title')
    Application out of date
@endsection

@section('content')
    Hello {{ $user->name }},
    <br>
    <br>
    Your company's application <strong>({{ $client->company }})</strong>is out of date and requires some confirmations,
    please, access panel and validate it.
@endsection
