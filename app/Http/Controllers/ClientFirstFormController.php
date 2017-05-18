<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\ClientFirstForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        //todo - verify if this usar has a relation with the application as a client
        if(!Auth::user()->hasRole('client')){
            throw new \Error('Forbidden');
        }

        \DB::beginTransaction();
        $client         = Auth::user()->client;

        $application    = $client->application;

        $application->status = 'wt_payment';
        $application->save();

        $request->offsetSet('client_id', $client->id);
        $request->offsetSet('status', '1');

        ClientFirstForm::create($request->all());
        \DB::commit();

        return redirect()->to('/');
    }
}
