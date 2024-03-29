@extends('layouts.app')

@php
    $page = "users";
@endphp

@include('partials.dataTables')

@section('content')
    @if(session('error'))
        <div class="alert alert-danger">User was not created. Verify if all fields are filled correctly or make sure the email already not exists in database</div>
    @endif

    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">
                System Users
                <a role="button" href="{{ URL::to($page . '/create') }}" class="btn btn-success pull-right m-r-20"><i class="fa fa-plus"></i> Add</span></a>
            </div>

            <div class="panel-body">
                <table class="setDataTables">
                    <tbody>
                    {{--The HTML into this TR represents the setup info where JS get and configure this datatables.--}}
                    <tr>
                        <td class="columns">{!! $dataTables['columns'] !!}</td>
                        <td class="info">{!! $dataTables['info'] !!}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection
