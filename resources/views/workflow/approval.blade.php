@extends('layouts.app')

@php
    $page = "approval";
@endphp

@section('styles')

@endsection

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="white-box">
            <h3 class="box-title m-b-20">Approval</h3>
            {{ $approval->title }}<br>
            {{ $approval->description }}
        </div>
    </div>
</div>
@endsection
