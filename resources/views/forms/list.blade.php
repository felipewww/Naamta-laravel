@extends('layouts.app')

@php
    $page = "forms";
@endphp

@include('partials.dataTables')

@section('content')
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">
                Forms
                 <a role="button" href="{{ URL::to($page . '/create') }}" class="btn btn-default btn-rounded btn-condensed btn-sm pull-right">Add</span></a>
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
