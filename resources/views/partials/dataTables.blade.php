@section('styles')
    <link href="{{ asset("js/DataTables/datatables.min.css") }}" rel="stylesheet">
    @parent
@endsection

@section('scripts')
    <script src="{{ asset("js/DataTables/datatables.min.js") }}"></script>
    <script src="{{ asset("js/DataTablesExtensions.js") }}"></script>
    @parent
@endsection