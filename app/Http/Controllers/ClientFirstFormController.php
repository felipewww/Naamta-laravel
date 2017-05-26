<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\ClientFirstForm;
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
        $form = $application->client->firstForm;
        $required = '';

        return view('applications.first_form',
            [
                'application'       => $application,
                'pageInfo'          => $this->pageInfo,
                'withAction'        => false,
                'form'              => $form,
                'required'          => $required,
                'showFiles'         => true
            ]
        );
    }

    public function firstFormSave(Request $request)
    {
        $user = Auth::user();

        //todo - verify if this usar has a relation with the application as a client
        if(!$user->hasRole('client')){
            throw new \Error('Forbidden');
        }

        \DB::beginTransaction();
        $client         = Auth::user()->client;

        $application    = $client->application;

        $application->status = 'wt_payment';
        $application->save();

        $request->offsetSet('client_id', $client->id);
        $request->offsetSet('status', '1');

        $data = $request->all();
        $data = $this->storeFiles($user->email, $request->allFiles(), $data);


        if ( $application->reset_at || $application->client->firstForm) {
            $form = $application->client->firstForm;
            $form->update($data);

        }else{

            if ( app('env') == 'local' )
            {
                foreach ($this->fileFields as $inputName)
                {
                    if (!array_key_exists($inputName, $data))
                    {
                        $data[$inputName] = 'File not uploaded - LOCALTESTS';
                    }
                }
            }

            ClientFirstForm::create($data);
        }
        \DB::commit();

        return redirect()->to('/');
    }
}
