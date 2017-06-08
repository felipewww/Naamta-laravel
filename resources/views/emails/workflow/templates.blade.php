@extends('emails.sendmail_template')

@section('title')
    Application Step updated
@endsection

@section('content')
    {!! $text !!}
    @if(!empty($allFormsWithErrors))
        <h3>You have some fields not passed in some forms. Please access the dashboard to see more.</h3>
        @foreach($allFormsWithErrors as $form)
            <h4>Form: {{$form->name}} </h4>
            <ul>
                @foreach($form->fieldsWithError as $field)
                    <li>
                        <strong>{{ $field->setting->label }}</strong>
                        <br>
                        Filled out: "{{ $field->setting->value }}"
                        <br>
                        Error type: {{ $field->setting->error }}
                    </li>
                @endforeach
            </ul>
        @endforeach
    @endif
@endsection