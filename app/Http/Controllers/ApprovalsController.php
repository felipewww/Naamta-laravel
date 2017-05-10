<?php

namespace App\Http\Controllers;


use App\Library\DataTablesExtensions;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\URL;
use Validator;
use Session;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;

use Illuminate\Http\Request;

use App\Models\Approval;

class ApprovalsController extends Controller
{
    use DataTablesExtensions;
    private $approvals;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->approvals = Approval::all();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->pageInfo->title              = 'Approvals';
        $this->pageInfo->category->title    = 'Workflow';
        $this->pageInfo->subCategory->title = 'Approvals List';

        $request->user()->authorizeRoles(['admin', 'staff']);
        $this->dataTablesInit();
        return view('approvals.list', ['dataTables' => $this->dataTables, 'pageInfo' => $this->pageInfo]);
    }

    public function dataTablesConfig()
    {
        //echo route('approvals');
        $data = [];
        foreach ($this->approvals as $reg)
        {
            $newInfo = [
                $reg['id'],
                $reg['title'],
                $reg['description'],
                [
                    'rowActions' =>
                        [
                            [
                                'html' => '',
                                'attributes' => ['class' => 'btn btn-warning btn-circle fa fa-pencil m-l-10', 'href' => '/approvals/'.$reg->id.'/edit']
                            ],
                            [
                                'html' => '',
                                'attributes' => ['class' => 'btn btn-danger btn-circle fa fa-trash m-l-10 modal-delete', 'data-toggle'=>'modal', 'data-target' => '#modalDelete', 'data-action' => route('approvals.destroy' , $reg->id)]
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
            ['title' => 'Description'],
            ['title' => 'Actions'],
        ];
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $this->pageInfo->title              = 'Approvals';
        $this->pageInfo->category->title    = 'Workflow';
        $this->pageInfo->subCategory->title = 'Approval Create';

        // get all the nerds
        // load the view and pass the nerds
        return view('approvals.form', ['pageInfo' => $this->pageInfo]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        \DB::beginTransaction();
        try{
            Approval::create([
                'title'       => $request->title,
                'description' => $request->description
            ]);
            \Session::flash('success','Approval template created: ' . $request->title);
            \DB::commit();
        } catch(Exception $e){
            \Session::flash('error','Approval create failed: ' . $e);
            \DB::rollBack();
            throw $e;
        }

        return Redirect::to('approvals');
    }

    public function edit(Request $request, $id){

        $this->pageInfo->title              = 'Approvals';
        $this->pageInfo->category->title    = 'Workflow';
        $this->pageInfo->subCategory->title = 'Approval Edit';

        $approval = Approval::findOrFail($id);
       
        return view('approvals.form')->with(['approval' => $approval, 'pageInfo' => $this->pageInfo]);
    }
    
    public function update(Request $request, $id){
        try{
            Approval::where("id", $id)->update([
                'title'      => $request->title,
                'description'    => $request->description
            ]);

            \Session::flash('success_msg','Approval Edited: ' . $request->title);
        } catch(Exception $e){
            \Session::flash('error','Approvals update failed: ' . $e);
        }
        
        return redirect('approvals');
    }

    public function destroy(Request $request, $id){
        // delete
        try{
            Approval::where('id', $id)->delete();
            \Session::flash('message', 'Approval deleted!');
        }catch (Exception $e){
            \Session::flash('message', 'Approval delete failed!');
        }

        return redirect('approvals');
    }

}
