@extends('layouts.app')

@section('scripts')
    <script type="text/javascript">
        function saveVerifier(select, position) {
            var id = select.children[select.selectedIndex].value;
//            console.log({e:select});
//            console.log(id);
            $.ajax({
                url: '/applications/{{ $application->id }}/saveVerifier',
                method: 'post',
                data: { user_id: id, position: position, _token: window.Laravel['csrfToken'] },
                success: function (data) {
//                    alert('ok!');
                },
                error: function (error) {
                    console.log(error);
                    alert('Error. Please, contact administrator.')
                }
            })
        }
    </script>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="white-box">
                    <h3 class="box-title m-b-0"><b>Registration Details</b></h3>
{{--{{ dd($application->client) }}--}}
                    <h4>Company's Name:</h4>
                    <p class="m-l-10 m-b-20">{{ $application->client->company }}</p>

                    <h4>Responsible:</h4>
                    <p class="m-l-10 m-b-20">{{ $application->client->user->name }}</p>

                    <h4>Created at</h4>
                    <p class="m-l-10 m-b-20">{{ $application->created_at }}</p>

                    <h4>Status</h4>
                    <p class="m-l-10 m-b-20">Waiting first form verification</p>

                    <h4>Verified by:</h4>
                    <div class="form-group col-md-6 ">
                        <select class="form-control" onchange="saveVerifier(this, 0)">
                            <option value="0">Select a user</option>
                            @foreach($sysUsers as $user)
                                <option value="{{$user->id}}" {{ $verifiers[0] == $user->id ? 'selected="selected"' : ''  }} >{{$user->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-6 ">
                        <select class="form-control" onchange="saveVerifier(this, 1)">
                            <option value="0">Select a user</option>
                            @foreach($sysUsers as $user)
                                <option value="{{$user->id}}" {{ $verifiers[1] == $user->id ? 'selected="selected"' : ''  }}>{{$user->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <a href="{{ asset('applications/'.$application->id.'/payment/first_form') }}" class="btn btn-custom">View Form</a>

                    <ul class="list-inline text-center">
                        <li class="text-right"><a href="{{ asset('applications/'.$application->id.'/payment/deny') }}" class="btn btn-danger m-t-10 m-r-20 p-10" style="width: 120px">Deny</a></li>
                        <li class="text-right"><a href="{{ asset('applications/'.$application->id.'/payment/allow') }}" class="btn btn-save m-t-10 p-10" style="width: 120px">Approve</a></li>
                    </ul>
                    <ul class="list-inline text-right">
                        <li class="text-right"><a href="{{ asset('applications/'.$application->id.'/payment/delete') }}" class="btn m-t-10 text-danger">Delete</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection