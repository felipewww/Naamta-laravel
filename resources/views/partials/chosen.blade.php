@section('styles')
    <link href="{{ asset("js/chosen_1-7-0/chosen.min.css") }}" rel="stylesheet">
    @parent
@endsection

@section('scripts')
    <script src="{{ asset("js/chosen_1-7-0/chosen.jquery.min.js") }}"></script>
    <script src="{{ asset("js/ChosenExtensions.js") }}"></script>
    @parent
@endsection