<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\ClientFirstForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ClientFirstFormController extends Controller
{
    public function staffView(Request $request, $id)
    {
        $application = Application::findOrFail($id);
        $form = $application->client->firstForm;

        return view('applications.first_form',
            [
                'application'       => $application,
                'pageInfo'          => $this->pageInfo,
                'withAction'        => false,
                'form'              => $form,
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

        if ( $application->reset_at ) {
            $form = $application->client->firstForm;
            $form->update($request->all());

        }else{
            $data = $request->all();
            $data = $this->storeFiles($user->email, $request->allFiles(), $data);

            if ( app('env') == 'local' )
            {
                $fakeFiles = [
                    'taxpayer_id',
                    'medical_drug_license',
                    'customer_reference_letter_1',
                    'customer_reference_letter_2',
                    'signed_acknowledgment_doc'
                ];

                foreach ($fakeFiles as $inputName)
                {
                    if (!in_array($inputName, $data)){
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
