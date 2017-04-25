<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Library\DataTablesExtensions;
use Validator;
use Session;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use App\Models\EmailTemplate;

class EmailsController extends Controller
{
    use DataTablesExtensions;

    private $emails;
    private $user;
    private $rules = [
        'title' => 'required|min:3|max:255',
        'text' => 'required|min:10'
    ];

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = \Auth::user()->authorizeRoles(['admin']);;
            return $next($request);
        });
        
        $this->emails = EmailTemplate::all();
    }

    /**
     * Show the application Email Template list.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->dataTablesInit();
        return view('emails.list', ['dataTables' => $this->dataTables ]);
    }

    public function dataTablesConfig()
    {
        $data = [];
        foreach ($this->emails as $reg)
        {
            $newInfo = [
                $reg['id'],
                $reg['title'],
                ($reg['status']) ? 'Active' : 'Inactive',
                [
                    'rowActions' =>
                        [
                            [
                                'html' => '',
                                'attributes' => ['class' => 'fa fa-pencil']
                            ],
                            [
                                'html' => '',
                                'attributes' => ['class' => 'fa fa-trash']
                            ]
                        ]
                ]
            ];

            array_push($data, $newInfo);
        }

        $this->data_info = $data;
        $this->data_cols = [
            ['title' => 'id', 'width' => '40px'],
            ['title' => 'Title'],
            ['title' => 'Status'],
            ['title' => 'Actions', 'width' => '100px'],
        ];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        // load the view and pass the email
        return view('emails.form');
    }

     /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        Validator::make($request->all(), $this->rules)->validate();
       
        try{

            EmailTemplate::create([
                'title'  => $request->title,
                'text'   => $request->text,
                'status' => (int)$request->status,
            ]);

            \Session::flash('success','Email template created: ' . $request->title);

        } catch(Exception $e){

            \Session::flash('error','Email create failed: ' . $e);

        }

        return Redirect::to('emails');
    }

     /**
     * Show the form for editing the specified resource.
     *
     * @return Response
     */
    public function edit($id)
    {
        // Get email template
        EmailTemplate::findOrFail($id);

        // show the view and pass the email to it
        return view('emails.form')
            ->with(array('email' => $email));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function update(Request $request, $id)
    {

        $validator = Validator::make($request->all(), $this->rules)->validate();
        
        try{
            EmailTemplate::where('id', $id)->update([
                'title'  => $request->title,
                'text'   => $request->text,
                'status' => (int)$request->status
            ]);

            \Session::flash('success','Email template updated: ' . $request->title);
        } catch(Exception $e){
            \Session::flash('error','Email update failed: ' . $e);
        }

        return Redirect::to('emails');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        // delete
        try{
            EmailTemplate::where('id', $id)->delete();
            Session::flash('message', 'Email deleted!');
        }catch (Exception $e){
            Session::flash('message', 'Email delete failed!');
        }

        return Redirect::to('emails');
    }
}
