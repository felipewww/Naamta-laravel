@extends('layouts.app')

@php
    $page = "usertypes";
    $itens = $usertypes;
@endphp


@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    User Types
                    <a role="button" href="{{ URL::to($page . '/create') }}" class="btn btn-default btn-rounded btn-condensed btn-xs pull-right">Add</span></a>
                </div>

                <div class="panel-body">
                   <table class="table table-bordered table-striped table-actions datatable">
                        <thead>
                            <tr>
                                <th>Users Types</th>
                                <th>Status</th>
                                <th width="150">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($itens as $key => $item)
                            <tr id="trow_{{ $key }}">
                                <td>{{ $item->title }}</td>
                                <td>{{ ($item->status == 0 ? "Inactive" : "Active" ) }}</td>
                                <td class="text-center" >
                                    <form id="delete" action="{{ route('usertypes.destroy', ['id' => $item->id]) }}" method="POST" class="form-inline">
                                        {{ csrf_field() }}
                                        <input type="hidden" name="_method" value="DELETE">    
                                        <a role="button" href="{{ URL::to($page . '/' .$item->id . '/edit') }}" class="btn btn-default btn-rounded btn-condensed btn-sm">Edit</span></a>
                                        <button type="submit" class="btn btn-danger btn-rounded btn-condensed btn-sm" onClick="delete_row('trow_{{ $key }}');">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                  </table>                    
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
