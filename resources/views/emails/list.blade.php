@extends('layouts.app')

@php
    $page = "emails";
    $itens = $emails;
@endphp

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    E-mails
                     <a role="button" href="{{ URL::to($page . '/create') }}" class="btn btn-default btn-rounded btn-condensed btn-sm pull-right">Add</span></a>
                </div>

                <div class="panel-body">
                   <table class="table table-bordered table-striped table-actions datatable">
                        <thead>
                            <tr>
                                <th width="50">id</th>
                                <th>Title</th>
                                <th>Status</th>
                                <th class="text-center" width="150">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        	@foreach($itens as $key => $item)
                            <tr id="trow_{{ $key }}">
                                <td class="text-center">{{ $item->id }}</td>
                                <td><strong>{{ $item->title }}</strong></td>
                                <td><strong>{{ $item->status }}</strong></td>
                                <td class="text-center" >
                                    <form id="delete" action="{{ route('emails.destroy', ['id' => $item->id]) }}" method="POST" class="form-inline">
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
