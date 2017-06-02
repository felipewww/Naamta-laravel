<?php

namespace App\Http\Controllers;

use App\MModels\FirstForm;
use App\MModels\Form;
use App\Models\Application;
use App\Models\ClientFirstForm;
use App\Models\FormTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ClientFirstFormController extends Controller
{
    public $fileFields = [
        'taxpayer_id',
        'medical_drug_license',
        'customer_reference_letter_1',
        'customer_reference_letter_2',
        'signed_acknowledgment_doc'
    ];

    public function staffView(Request $request, $id)
    {
        $user = Auth::user();

        if(!$user->hasRole(['admin','staff'])) { return redirect('/'); }

        $application = Application::findOrFail($id);
        $form = Form::with([
            'containers',
            'containers.config',
            'containers.fields',
            'containers.fields.comments',
            'containers.fields.setting',
            'containers.fields.setting.rule',
            'containers.fields.setting.rule.conditions'])
            ->findOrFail($application->client->mform_register_id);

        return view('applications.first_form',
            [
                'application'       => $application,
                'pageInfo'          => $this->pageInfo,
                'containers'        => $form->containers,
                'isResponsible'     => false,
                'appID'             => $id
            ]
        );
    }

    public function _firstFormSave(Request $request)
    {
        try{
            \DB::beginTransaction();
            $user = Auth::user();
            //todo - verify if this usar has a relation with the application as a client
            if(!$user->hasRole('client')){
                throw new \Error('Forbidden');
            }

            $converted = \GuzzleHttp\json_decode($request->form_json);
            $this->_updateFormToMongo($converted);

            $application = $user->client->application;
            $application->status = 'wt_payment'; //waiting payment, here, means waiting validation first form besides payment.
            $application->save();

            \DB::commit();
            return json_encode(['status' => true, 'message' => 'Form saved']);
        }catch (Exception $e){
            return json_encode(['status' => 'error', 'message' => 'Error']);
        }
    }
}
