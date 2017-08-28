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
{{--            {{ $form->errorsCount }}--}}
            {{--@if( $form->errorsCount[0]->Pass || $form->errorsCount[0]->Audit || $form->errorsCount[0]->Fail )--}}
                {{--@if($form->errorsCount[0]->Pass)--}}
                    {{--<div>Passed: {{$form->errorsCount[0]->Pass}}</div>--}}
                {{--@endif--}}

                {{--@if($form->errorsCount[0]->Audit)--}}
                    {{--<div>Site Audit: {{$form->errorsCount[0]->Audit}}</div>--}}
                {{--@endif--}}

                {{--@if($form->errorsCount[0]->Fail)--}}
                    {{--<div>Failed: {{  $form->errorsCount[0]->Fail }}</div>--}}
                {{--@endif--}}
            {{--@endif--}}
            <ul>
                @foreach($form->fieldsWithError as $k => $field)

                    @if($k == 'errorsCount' )
                        <li>
                            <span>{{ ($form->fieldsWithError['errorsCount']['Pass']) ? 'Passed: '.$form->fieldsWithError['errorsCount']['Pass'] : '' }}</span>
                            <br>
                            <span>{{ ($form->fieldsWithError['errorsCount']['Fail']) ? 'Failed: '.$form->fieldsWithError['errorsCount']['Fail'] : '' }}</span>
                            <br>
                            <span>{{ ($form->fieldsWithError['errorsCount']['Audit']) ? 'Site Audit: '.$form->fieldsWithError['errorsCount']['Audit'] : '' }}</span>
                            <br>
                        </li>
                    @endif

                    @if($field instanceof \App\MModels\Field)
                        <li>
                            <br>
                            <strong>{{ $field->setting->label }}</strong>
                            <br>
                            Filled out: "{{ $field->setting->value }}"
                            <br>
                            Error type: {{ $field->setting->error }}
                        </li>
                    @endif

                @endforeach
            </ul>
        @endforeach
    @endif
@endsection