@extends('layouts.app')

@php
    $page = "forms";
    
    $route = route('forms.store');
    $method = 'POST';
    if(isset($form) && $form!=null){
        if($form->id!=null){
            $route = route('forms.update', ['id' => $form->id]);
            $method = 'PUT';
        }
        $noFilter = false;
    }else{
        $noFilter = true;
        $form = new App\Models\FormTemplate();
    }

@endphp

@if($noFilter)
    @section('scripts')
        <script>
            $(document).ready(function () {
                $('#save, #open-history, .form-holder > .filter').hide();
            });
        </script>
    @endsection
@endif

@section('styles')
    <link href="{{ asset("css/template/blanko-form-builder.css") }}" rel="stylesheet">
@endsection

@section('content')
<form  class="" role="form" method="POST" action="{{$route}}">
    {{ csrf_field() }}
                <input type="hidden" name="_method" value="{{ $method }}">
                <input type="hidden" name="containers" value="{{ isset($containers) ? $containers : old('containers') }}" required>
    <div class="row">
        <div class="col-sm-12">
            @if ($form->id != 1) {{-- show only if isn't a first form (register form) --}}
            <div class="white-box">
                <h3 class="box-title m-b-20">New Form Type</h3>

                <div class="form-group col-sm-6">
                    <label class="col-md-12">Title</label>
                    <div class="col-md-12">
                        <input id="name" type="text" class="form-control" name="name" value="{{ $form->name!=null ? $form->name : old('name') }}" required>
                        @if ($errors->has('name'))
                            <span class="help-block">
                                <strong>{{ $errors->first('name') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="form-group col-sm-6">
                    <label class="col-sm-12">Status</label>
                    <div class="col-sm-12">
                        <select class="form-control" name="status">
                            <option value="1" {{ (isset($form) && $form->status == 1 ? "selected" : "") }} >Active</option>
                            <option value="0" {{ (isset($form) && $form->status == 0 ? "selected" : "") }} >Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="white-box">
                <h3 class="box-title m-b-0">Registration Form</h3>
                <div id="drag-container"></div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="white-box">
            <button type="submit" class="btn btn-success center-block waves-effect waves-light pull-right" id="submit-form"><i class="fa fa-check m-r-20"></i>Submit Form</button>
            <div class="clearfix"></div>
        </div>
    </div>
</form>
<script>
    var username = '{{ Auth::user()->name }}';
</script>
<script src="{{ asset("js/template/signature_pad.js") }}"></script>
<script src="{{ asset("js/template/dropzone.js") }}"></script>

<script src="{{ asset("js/template/blanko-form-builder.js") }}"></script>
<script src="{{ asset("js/template/blanko-form-creator.js") }}"></script>
<script src="{{ asset("js/template/blanko-form-checkpoint.js") }}"></script>
<script src="{{ asset("js/template/jquery.mask.min.js") }}"></script>


<script>
    $('form').submit(function(e){
        e.preventDefault();
        $('input[name=containers]').val(toJson());
        $(this).unbind().submit();
    })
    if($('input[name=containers]').val() != ''){
        //$(this).val('[{"config":{"_id":"1","title":"Institutional","tabId":"654321"},"fields":[{"_id":"","type":"paragraph","isEditable":true,"setting":{"options":[],"rule":{"ruleAction":"hide","ruleTarget":"all","conditions":[]},"ordenate":1,"isRequired":false,"label":"To view the instructions for filling out this form, select the applicable Service Classification below:\n\nMedical Transport Service\nMedical Escort Service","class":""},"comments":[]},{"_id":"","type":"radio-group","isEditable":true,"setting":{"options":[{"label":"     Initial     ","value":"Initial","prop":false},{"label":"     Renewal     ","value":"Renewal","prop":false}],"rule":{"ruleAction":"hide","ruleTarget":"all","conditions":[]},"ordenate":2,"isRequired":true,"label":"Initial Accreditation or Renewal","help":"","class":""},"comments":[]},{"_id":"","type":"checkbox-group","isEditable":true,"setting":{"options":[{"label":"Medical Transport","value":"\tMedical Transport","prop":false},{"label":"Medical Escort","value":"Medical Escort","prop":false}],"rule":{"ruleAction":"hide","ruleTarget":"all","conditions":[]},"ordenate":3,"isRequired":true,"label":"Services to be Accredited","help":"<span style=\"color: rgb(56, 56, 56); font-family: Verdana; font-size: 12px;\">Check all that apply</span>","class":""},"comments":[]},{"_id":"","type":"file-upload","isEditable":true,"setting":{"options":[],"rule":{"ruleAction":"hide","ruleTarget":"all","conditions":[]},"ordenate":4,"isRequired":false,"label":"Taxpayer ID #","help":"<span style=\"color: rgb(56, 56, 56); font-family: Verdana; font-size: 12px;\">Upload documentation of Taxpayer ID #</span>","class":""},"comments":[]},{"_id":"","type":"text-field","isEditable":true,"setting":{"options":[],"rule":{"ruleAction":"hide","ruleTarget":"all","conditions":[]},"ordenate":5,"isRequired":true,"label":"Street Addres","help":"","value":"","checked":false,"class":""},"comments":[]},{"_id":"","type":"text-field","isEditable":true,"setting":{"options":[],"rule":{"ruleAction":"hide","ruleTarget":"all","conditions":[]},"ordenate":6,"isRequired":true,"label":"Mailing Address","help":"If different than street address","value":"","checked":false,"class":""},"comments":[]},{"_id":"","type":"phone-field","isEditable":true,"setting":{"options":[],"rule":{"ruleAction":"hide","ruleTarget":"all","conditions":[]},"ordenate":7,"isRequired":true,"label":"Phone Number","help":"<span style=\"color: rgb(56, 56, 56); font-family: Verdana; font-size: 12px;\">If outside USA, include country code</span>","value":"","checked":false,"class":""},"comments":[]},{"_id":"","type":"phone-field","isEditable":true,"setting":{"options":[],"rule":{"ruleAction":"hide","ruleTarget":"all","conditions":[]},"ordenate":8,"isRequired":true,"label":"Fax Number","help":"<span style=\"color: rgb(56, 56, 56); font-family: Verdana; font-size: 12px;\">If outside USA, include country code</span>","value":"","checked":false,"class":""},"comments":[]},{"_id":"","type":"select","isEditable":true,"setting":{"options":[{"label":"Sole Proprietorship","value":"Sole Proprietorship","prop":true},{"label":"Partnership","value":"Partnership","prop":false},{"label":"Non-Profit","value":"Non-Profit","prop":false},{"label":"Corporation (INC)","value":"Corporation (INC)","prop":false},{"label":"Government Entity","value":"Government Entity","prop":false},{"label":"LLC","value":"LLC","prop":false},{"label":"Other","value":"Other","prop":false}],"rule":{"ruleAction":"hide","ruleTarget":"all","conditions":[]},"ordenate":9,"isRequired":true,"label":"Business Type","help":"","value":"Sole Proprietorship","class":""},"comments":[]},{"_id":"","type":"text-field","isEditable":true,"setting":{"options":[],"rule":{"ruleAction":"show","ruleTarget":"all","conditions":[{"page":{"_id":"1","label":"Institutional"},"field":{"_id":"8","index":"9","label":"Select"},"comparison":{"value":"==","label":"Is"},"value":{"value":"Other","label":"Other"}}]},"ordenate":10,"isRequired":false,"label":"Specify your business type here","help":"","value":"","checked":false,"class":""},"comments":[]},{"_id":"","type":"text-field","isEditable":true,"setting":{"options":[],"rule":{"ruleAction":"hide","ruleTarget":"all","conditions":[]},"ordenate":11,"isRequired":true,"label":"Website","help":"","value":"","checked":false,"class":""},"comments":[]},{"_id":"","type":"text-field","isEditable":true,"setting":{"options":[],"rule":{"ruleAction":"hide","ruleTarget":"all","conditions":[]},"ordenate":12,"isRequired":true,"label":"Affiliations/Ownerships","help":"<span style=\"color: rgb(56, 56, 56); font-family: Verdana; font-size: 12px;\">List any affiliations with or ownership of other services, businesses, or companies (i.e., government agency, hospital, \"dba\", charter services, medical transport services, corporations, subsidiaries.)</span>","value":"","checked":false,"class":""},"comments":[]}]},{"config":{"_id":"0","title":"Contact Information","tabId":"65432"},"fields":[{"_id":"","type":"text-field","isEditable":true,"setting":{"options":[],"rule":{"ruleAction":"hide","ruleTarget":"all","conditions":[]},"ordenate":13,"isRequired":true,"label":"Primary Contact Name","help":"","value":"","checked":false,"class":""},"comments":[]},{"_id":"","type":"phone-field","isEditable":true,"setting":{"options":[],"rule":{"ruleAction":"hide","ruleTarget":"all","conditions":[]},"ordenate":14,"isRequired":true,"label":"Primary Contact Phone Number","help":"<span style=\"color: rgb(56, 56, 56); font-family: Verdana; font-size: 12px;\">If outside USA, include country code</span>","value":"","checked":false,"class":""},"comments":[]},{"_id":"","type":"email-field","isEditable":true,"setting":{"options":[],"rule":{"ruleAction":"hide","ruleTarget":"all","conditions":[]},"ordenate":15,"isRequired":true,"label":"Primary Contact Email","help":"","value":"","checked":false,"class":""},"comments":[]},{"_id":"","type":"text-field","isEditable":true,"setting":{"options":[],"rule":{"ruleAction":"hide","ruleTarget":"all","conditions":[]},"ordenate":16,"isRequired":true,"label":"Compliance Officer Name","help":"<span style=\"color: rgb(56, 56, 56); font-family: Verdana; font-size: 12px;\">The Compliance Officer is responsible for overseeing and managing adherence to the Standards demonstrated through the Application and Continuous Compliance reporting processes.</span><br style=\"color: rgb(56, 56, 56); font-family: Verdana; font-size: 12px;\"><span style=\"color: rgb(56, 56, 56); font-family: Verdana; font-size: 12px;\"><b>This person may also be the Primary Contact.</b></span>","value":"","checked":false,"class":""},"comments":[]},{"_id":"","type":"phone-field","isEditable":true,"setting":{"options":[],"rule":{"ruleAction":"hide","ruleTarget":"all","conditions":[]},"ordenate":17,"isRequired":true,"label":"Primary Contact Phone Number","help":"<span style=\"color: rgb(56, 56, 56); font-family: Verdana, Verdana, Arial, Helvetica, sans-serif; font-size: 12px;\"><span style=\"line-height: 1.6em;\">If outside USA, include country code</span></span><br>","value":"","checked":false,"class":""},"comments":[]},{"_id":"","type":"email-field","isEditable":true,"setting":{"options":[],"rule":{"ruleAction":"hide","ruleTarget":"all","conditions":[]},"ordenate":18,"isRequired":true,"label":"Compliance Officer Email","help":"<span style=\"color: rgb(56, 56, 56); font-family: Verdana; font-size: 12px;\">If different than primary contact email</span>","value":"","checked":false,"class":""},"comments":[]},{"_id":"","type":"text-field","isEditable":true,"setting":{"options":[],"rule":{"ruleAction":"hide","ruleTarget":"all","conditions":[]},"ordenate":19,"isRequired":true,"label":" Application Access Permissions","help":" Help Text Goes Here ","value":"","checked":false,"class":""},"comments":[]}]},{"config":{"_id":"3","title":"Business Information","tabId":"6543"},"fields":[{"_id":"","type":"text-field","isEditable":true,"setting":{"options":[],"rule":{"ruleAction":"hide","ruleTarget":"all","conditions":[]},"ordenate":20,"isRequired":true,"label":" How long has your organization been conducting medical transport and/or medical escort missions?","help":"<span style=\"color: rgb(56, 56, 56); font-family: Verdana; font-size: 12px;\">Minimum requirement of 6 months</span>","value":"","checked":false,"class":""},"comments":[]},{"_id":"","type":"text-field","isEditable":true,"setting":{"options":[],"rule":{"ruleAction":"hide","ruleTarget":"all","conditions":[]},"ordenate":21,"isRequired":true,"label":"Transports per Year","help":"<span style=\"color: rgb(56, 56, 56); font-family: Verdana; font-size: 12px;\">Report the number of transports and/or medical escort missions performed in the past 12 months</span>","value":"","checked":false,"class":""},"comments":[]},{"_id":"","type":"text-field","isEditable":true,"setting":{"options":[],"rule":{"ruleAction":"hide","ruleTarget":"all","conditions":[]},"ordenate":22,"isRequired":true,"label":"Base location(s)","help":"<span style=\"color: rgb(56, 56, 56); font-family: Verdana; font-size: 12px;\">List all base locations</span><br>","value":"","checked":false,"class":""},"comments":[]},{"_id":"","type":"text-field","isEditable":true,"setting":{"options":[],"rule":{"ruleAction":"hide","ruleTarget":"all","conditions":[]},"ordenate":23,"isRequired":true,"label":"List Communications Center(s) & Address(es) ","help":"","value":"","checked":false,"class":""},"comments":[]},{"_id":"","type":"text-field","isEditable":true,"setting":{"options":[],"rule":{"ruleAction":"hide","ruleTarget":"all","conditions":[]},"ordenate":24,"isRequired":true,"label":"Describe your business arrangements for all outsourced resource(s) reported above","help":"","value":"","checked":false,"class":""},"comments":[]}]},{"config":{"_id":"4","title":"Medical Information","tabId":"654"},"fields":[{"_id":"","type":"checkbox-group","isEditable":true,"setting":{"options":[{"label":"Adult","value":"Adult","prop":false},{"label":"Pediatric","value":"Pediatric","prop":false},{"label":"Neonatal","value":"Neonatal","prop":false}],"rule":{"ruleAction":"hide","ruleTarget":"all","conditions":[]},"ordenate":25,"isRequired":true,"label":"Patient Population ","help":"<span style=\"color: rgb(56, 56, 56); font-family: Verdana; font-size: 12px;\">Check all that apply</span>","class":""},"comments":[]},{"_id":"","type":"text-field","isEditable":true,"setting":{"options":[],"rule":{"ruleAction":"hide","ruleTarget":"all","conditions":[]},"ordenate":26,"isRequired":false,"label":"Text Field ","help":" Help Text Goes Here ","value":"Medical Director\'s Name","checked":false,"class":""},"comments":[]},{"_id":"","type":"text-field","isEditable":true,"setting":{"options":[],"rule":{"ruleAction":"hide","ruleTarget":"all","conditions":[]},"ordenate":27,"isRequired":true,"label":"Where is Medical Director based?","help":"","value":"","checked":false,"class":""},"comments":[]},{"_id":"","type":"file-upload","isEditable":true,"setting":{"options":[],"rule":{"ruleAction":"hide","ruleTarget":"all","conditions":[]},"ordenate":28,"isRequired":false,"label":"Medical Director\'s Drug Controlled Substances License #","help":"<span style=\"color: rgb(56, 56, 56); font-family: Verdana; font-size: 12px;\">Upload copy of Drug Controlled Substances License[s]</span>","class":""},"comments":[]}]},{"config":{"_id":"5","title":"Accreditation Criteria","tabId":"65"},"fields":[{"_id":"","type":"file-upload","isEditable":true,"setting":{"options":[],"rule":{"ruleAction":"hide","ruleTarget":"all","conditions":[]},"ordenate":29,"isRequired":false,"label":"Customer reference letter #1","help":"<span style=\"color: rgb(56, 56, 56); font-family: Verdana; font-size: 12px;\">Upload customer reference letter</span>","class":""},"comments":[]},{"_id":"","type":"file-upload","isEditable":true,"setting":{"options":[],"rule":{"ruleAction":"hide","ruleTarget":"all","conditions":[]},"ordenate":30,"isRequired":false,"label":"Customer reference letter #2","help":"<span style=\"color: rgb(56, 56, 56); font-family: Verdana; font-size: 12px;\">Upload customer reference letter</span>","class":""},"comments":[]},{"_id":"","type":"file-upload","isEditable":true,"setting":{"options":[],"rule":{"ruleAction":"hide","ruleTarget":"all","conditions":[]},"ordenate":31,"isRequired":false,"label":"Signed Acknowledgment Document","help":"<em style=\"color: rgb(56, 56, 56); font-family: Verdana; font-size: 12px;\">Upload signed \"Acknowledgment Document\"</em>","class":""},"comments":[]}]},{"config":{"_id":"6","title":"Registration Fee","tabId":"6"},"fields":[{"_id":"","type":"paragraph","isEditable":true,"setting":{"options":[],"rule":{"ruleAction":"hide","ruleTarget":"all","conditions":[]},"ordenate":32,"isRequired":false,"label":"A $695 (USD) non-refundable fee is required at the time of registration.\n\nNote: All registration and application fees must be paid in U.S. dollars. Any currency exchange/transfer fees are the responsibility of the applying organization.\n\nClick Here for Currency Converter (exchange fees not included)","class":""},"comments":[]},{"_id":"","type":"select","isEditable":true,"setting":{"options":[{"label":"Check","value":"Check","prop":true},{"label":"Paypal","value":"Paypal","prop":false},{"label":"Wire Transfer","value":"Wire Transfer","prop":false}],"rule":{"ruleAction":"hide","ruleTarget":"all","conditions":[]},"ordenate":33,"isRequired":true,"label":"Method of Payment","help":"<span style=\"color: rgb(56, 56, 56); font-family: Verdana; font-size: 12px;\">Select method of payment</span>","value":"Check","class":""},"comments":[]},{"_id":"","type":"paragraph","isEditable":true,"setting":{"options":[],"rule":{"ruleAction":"show","ruleTarget":"all","conditions":[{"page":{"_id":"6","label":"Registration Fee"},"field":{"_id":"39","index":"33","label":"Select"},"comparison":{"value":"==","label":"Is"},"value":{"value":"Check","label":"Check"}}]},"ordenate":34,"isRequired":false,"label":"Send copy of this registration form together with check to:\n\nNAAMTA\nPO Box 111\nAmerican Fork, UT 84003\nUSA\n\n(Receipt will be sent once payment received and processed.)","class":""},"comments":[]},{"_id":"","type":"text-field","isEditable":true,"setting":{"options":[],"rule":{"ruleAction":"show","ruleTarget":"all","conditions":[{"page":{"_id":"6","label":"Registration Fee"},"field":{"_id":"39","index":"33","label":"Select"},"comparison":{"value":"==","label":"Is"},"value":{"value":"Paypal","label":"Paypal"}}]},"ordenate":35,"isRequired":true,"label":"Email Paypal Registration Invoice to","help":"<span style=\"color: rgb(56, 56, 56); font-family: Verdana; font-size: 12px;\">Provide Email Address</span>","value":"","checked":false,"class":""},"comments":[]},{"_id":"","type":"text-field","isEditable":true,"setting":{"options":[],"rule":{"ruleAction":"show","ruleTarget":"all","conditions":[{"page":{"_id":"6","label":"Registration Fee"},"field":{"_id":"39","index":"33","label":"Select"},"comparison":{"value":"==","label":"Is"},"value":{"value":"Wire Transfer","label":"Wire Transfer"}}]},"ordenate":36,"isRequired":true,"label":"Email Wire Transfer Registration Instructions to","help":"<span style=\"color: rgb(56, 56, 56); font-family: Verdana; font-size: 12px;\">Provide Email Address</span>","value":"","checked":false,"class":""},"comments":[]}]}]');
        createTabs($('input[name=containers]').val(), false);
    }else{
        createTabs(toJson());
    }
    
</script>
@endsection
