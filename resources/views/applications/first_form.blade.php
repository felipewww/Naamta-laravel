@extends('layouts.app')

<?php
    $status = ( $withAction ) ? '' : 'disabled="disabled"';

    if(!isset($form)){
        $form = new \App\Models\ClientFirstForm();
    }
?>

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="">
                <div class="white-box">
                    <h3 class="box-title m-b-0"><b>Registration form</b></h3>

                    @if( $application->reset_at )
                        <div class="alert alert-warning">Your application is overdue. Please, validate your account info and resend it.
                            Soon our staff will contact you.
                        </div>
                    @endif

                    <form  class="form-horizontal" method="post" action="{{ asset('applications/client/first_form') }}">
                        {{ csrf_field() }}

                        {{--<div class="form-group">--}}
                            {{--<label class="col-md-12">FIELD_NAME</label>--}}
                            {{--<div class="col-md-12">--}}
                                {{--<input {{$status}} {{$required}} type="text" class="form-control" name="FIELD_NAME" placeholder="" value="{{$form->FIELD_NAME}}">--}}
                            {{--</div>--}}
                        {{--</div>--}}

                        <div class="BASIC">
                            <div class="form-group">
                                <label class="col-md-12">services_accredited</label>
                                <div class="col-md-12">
                                    <select {{$status}} {{$required}} name="services_accredited">
                                        <option value="medical_transport">Medical Transport</option>
                                        <option value="medical_escort">Medical Escort</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-12">taxpayer_id</label>
                                <div class="col-md-12">
                                    <input {{$status}} {{$required}} type="text" class="form-control" name="taxpayer_id" placeholder="" value="{{$form->taxpayer_id}}">
                                    {{--<input {{$status}} {{$required}} type="file" class="form-control" name="taxpayer_id" placeholder="" value="{{$form->taxpayer_id}}">--}}
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-12">address_street</label>
                                <div class="col-md-12">
                                    <input {{$status}} {{$required}} type="text" class="form-control" name="address_street" placeholder="" value="{{$form->address_street}}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-12">address_mailing</label>
                                <div class="col-md-12">
                                    <input {{$status}} {{$required}} type="text" class="form-control" name="address_mailing" placeholder="" value="{{$form->address_mailing}}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-12">phone_number</label>
                                <div class="col-md-12">
                                    <input {{$status}} {{$required}} type="text" class="form-control" name="phone_number" placeholder="" value="{{$form->phone_number}}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-12">business_type</label>
                                <div class="col-md-12">
                                    <select>
                                        <option value="sole_proprietorship">Sole Proprietorship</option>
                                        <option value="partnership">Partnership</option>
                                        <option value="non_profit">Non-profit</option>
                                        <option value="corporation">Corporation (INC)</option>
                                        <option value="government_entity">Government Entity</option>
                                        <option value="llc">LLC</option>
                                        <option value="other">Other</option>
                                    </select>
                                    <input {{$status}} {{$required}} type="text" class="form-control" name="business_type" placeholder="" value="{{$form->business_type}}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-12">website</label>
                                <div class="col-md-12">
                                    <input {{$status}} {{$required}} type="text" class="form-control" name="website" placeholder="" value="{{$form->website}}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-12">ownerships</label>
                                <div class="col-md-12">
                                    <input {{$status}} {{$required}} type="text" class="form-control" name="ownerships" placeholder="" value="{{$form->ownerships}}">
                                </div>
                            </div>
                        </div>

                        <div class="CONTACT_INFO">
                            <div class="form-group">
                                <label class="col-md-12">contact_name</label>
                                <div class="col-md-12">
                                    <input {{$status}} {{$required}} type="text" class="form-control" name="contact_name" placeholder="" value="{{$form->contact_name}}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-12">contact_email</label>
                                <div class="col-md-12">
                                    <input {{$status}} {{$required}} type="text" class="form-control" name="contact_email" placeholder="" value="{{$form->contact_email}}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-12">contact_phone</label>
                                <div class="col-md-12">
                                    <input {{$status}} {{$required}} type="text" class="form-control" name="contact_phone" placeholder="" value="{{$form->contact_phone}}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-12">compliance_name</label>
                                <div class="col-md-12">
                                    <input {{$status}} {{$required}} type="text" class="form-control" name="compliance_name" placeholder="" value="{{$form->compliance_name}}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-12">compliance_email</label>
                                <div class="col-md-12">
                                    <input {{$status}} {{$required}} type="text" class="form-control" name="compliance_email" placeholder="" value="{{$form->compliance_email}}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-12">compliance_phone</label>
                                <div class="col-md-12">
                                    <input {{$status}} {{$required}} type="text" class="form-control" name="compliance_phone" placeholder="" value="{{$form->compliance_phone}}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-12">application_access</label>
                                <div class="col-md-12">
                                    <input {{$status}} {{$required}} type="text" class="form-control" name="application_access" placeholder="" value="{{$form->application_access}}">
                                </div>
                            </div>
                        </div>

                        <div class="BUSINESS_INFO">
                            <div class="form-group">
                                <label class="col-md-12">since (minimum six months)</label>
                                <div class="col-md-12">
                                    <input {{$status}} {{$required}} type="text" class="form-control" name="since" placeholder="" value="{{$form->since}}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-12">transports_per_year</label>
                                <div class="col-md-12">
                                    <input {{$status}} {{$required}} type="text" class="form-control" name="transports_per_year" placeholder="" value="{{$form->transports_per_year}}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-12">base_locations</label>
                                <div class="col-md-12">
                                    <input {{$status}} {{$required}} type="text" class="form-control" name="base_locations" placeholder="" value="{{$form->base_locations}}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-12">communications_center</label>
                                <div class="col-md-12">
                                    <input {{$status}} {{$required}} type="text" class="form-control" name="communications_center" placeholder="" value="{{$form->communications_center}}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-12">description</label>
                                <div class="col-md-12">
                                    <input {{$status}} {{$required}} type="text" class="form-control" name="description" placeholder="" value="{{$form->description}}">
                                </div>
                            </div>
                        </div>

                        <div class="MEDICAL_INFO">
                            <div class="form-group">
                                <label class="col-md-12">patient_population</label>
                                <div class="col-md-12">
                                    <select {{$status}} {{$required}} name="{{$form->patient_population}}">
                                        <option value="adult">adult</option>
                                        <option value="pediatric">pediatric</option>
                                        <option value="neonatal">neonatal</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-12">medical_director_name</label>
                                <div class="col-md-12">
                                    <input {{$status}} {{$required}} type="text" class="form-control" name="medical_director_name" placeholder="" value="{{$form->medical_director_name}}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-12">medical_based</label>
                                <div class="col-md-12">
                                    <input {{$status}} {{$required}} type="text" class="form-control" name="medical_based" placeholder="" value="{{$form->medical_based}}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-12">medical_drug_license</label>
                                <div class="col-md-12">
                                    <input {{$status}} {{$required}} type="text" class="form-control" name="medical_drug_license" placeholder="" value="{{$form->medical_drug_license}}">
                                    {{--<input {{$status}} {{$required}} type="file" class="form-control" name="medical_drug_license" placeholder="" value="{{$form->medical_drug_license}}">--}}
                                </div>
                            </div>
                        </div>

                        <div class="CRITERIA">
                            <div class="form-group">
                                <label class="col-md-12">customer_reference_letter_1</label>
                                <div class="col-md-12">
                                    <input {{$status}} {{$required}} type="text" class="form-control" name="customer_reference_letter_1" placeholder="" value="{{$form->customer_reference_letter_1}}">
                                    {{--<input {{$status}} {{$required}} type="file" class="form-control" name="customer_reference_letter_1" placeholder="" value="{{$form->customer_reference_letter_1}}">--}}
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-12">customer_reference_letter_2</label>
                                <div class="col-md-12">
                                    <input {{$status}} {{$required}} type="text" class="form-control" name="customer_reference_letter_2" placeholder="" value="{{$form->customer_reference_letter_2}}">
                                    {{--<input {{$status}} {{$required}} type="file" class="form-control" name="customer_reference_letter_2" placeholder="" value="{{$form->customer_reference_letter_2}}">--}}
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-12">signed_acknowledgment_doc</label>
                                <div class="col-md-12">
                                    <input {{$status}} {{$required}} type="text" class="form-control" name="signed_acknowledgment_doc" placeholder="" value="{{$form->signed_acknowledgment_doc}}">
                                    {{--<input {{$status}} {{$required}} type="file" class="form-control" name="signed_acknowledgment_doc" placeholder="" value="{{$form->signed_acknowledgment_doc}}">--}}
                                </div>
                            </div>
                        </div>

                        @if($withAction)
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Submit
                                </button>
                            </div>
                        </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection