@extends('layouts.app')

@php
    $page = "usertypes";
    {{--$itens = $usertypes;--}}
@endphp

@include('partials.dataTables')

@section('content')
    @if ($error)
        <div class="alert alert-info">{{$error}}</div>
    @endif
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">
                User Types
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
