<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Member;
use App\Models\Role;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $customMessages = [
        'required' => 'Please input the :attribute.',
        'unique' => 'This :attribute has alpready been taken.',
        'max' => ':Attribute may not be more than :max characters.',
    ];

    public function index()
    {
        $company = auth()->user()->company_id;
        $data = Member::with('school', 'user', 'school.district')
            ->where('company_id', $company)
            ->orderBy('created_at', 'ASC')->get();
        if (request()->ajax()) {
            return datatables()->of($data)
                ->addColumn('action', 'admin.settings.action_location')
                ->addColumn('image', 'admin1.users.image')
                // ->addColumn('image', function ($row) {
                //     $url= 'uploads/employee/rXbGc2oXcxMmzXss8zasrQF59FjSSEs7ENjg4Yjy.jpg';
                //      return '<div>  <img src="uploads/employee/rXbGc2oXcxMmzXss8zasrQF59FjSSEs7ENjg4Yjy.jpg" alt="logo" width="50" ></div>';
                //      })
                ->rawColumns(['action', 'image'])
                ->addIndexColumn()
                ->make(true);
        }
        // return response()->json([
        //     'data'=>$data
        // ]);
        return view('admin.settings.member.member');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $role = Role::all();
        $job = Job::all();
        $company = auth()->user()->company_id;
        $school = School::where('company_id', $company)
            ->orderBy('created_at', 'ASC')->get();
        $sm = User::where('role_id', 6)
            ->where('company_id', $company)
            ->get();

        return view('admin.settings.member.create', compact('role', 'school', 'company', 'job', 'sm'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate([
            'name' => 'required|string',
            // 'email'      => 'required|string|unique:users',
            'school_id' => 'required',
            'parent_id' => 'required',
            // 'role_id'     => 'required',

        ], $this->customMessages);
        $company_id = auth()->user()->company_id;
        $data = new Member();
        $data->no = $request->no;
        $data->status = $request->status;
        $data->name = $request->name;
        $data->gender = 'Female';
        $data->nationality = 'Cambodian';
        $data->dob = $request->dob;
        $data->phone_number = $request->phone_number;
        $data->email = $request->email;
        $data->facebook = $request->facebook;
        $data->telegram = $request->telegram;
        //    $data->profile_url = $request->profile_url;
        $data->address = $request->address;
        $data->merital_status = $request->merital_status;
        $data->company = $request->company;
        $data->user_id = $request->parent_id;
        $data->job_id = $request->job_id;

        $data->company_id = $company_id;
        $data->role_id = '3';
        $data->school_id = $request->school_id;
        $data->start_year = $request->start_year;
        $data->end_year = $request->end_year;
        if ($request->profile_url) {

            $data['profile_url'] = $request->file('profile_url')->store('uploads/employee', 'photo');
        }
        $data->save();
        // $data = Member::create([
        //     'no'           => $request,
        //     'status'          => strip_tags(request()->post('status')),
        //     // 'password'         =>bcrypt($request->password),
        //     'role_id'       => strip_tags(request()->post('role_id')),
        //     'company_id'       => strip_tags(request()->post('company_id')),

        // ]);
        if ($data) {
            $response = [
                'code' => 0,
                'message' => 'Success'
            ];
        } else {
            $response = [
                'code' => -1,
                'message' => 'Sorry, Something went wrong'
            ];
        }









        return response()->json($response, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Member::find($id);
        $role = Role::all();
        $job = Job::all();
        $company = auth()->user()->company_id;
        $school = School::where('company_id', $company)
            ->orderBy('created_at', 'ASC')->get();
        $sm = User::where('role_id', 6)
            ->where('company_id', $company)
            ->get();
        return view('admin.settings.member.edit', compact('data', 'role', 'job', 'school', 'role', 'company', 'sm'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        request()->validate([
            'name' => 'required|string',
            // 'email'      => 'required|string|unique:users',
            'school_id' => 'required',
            'parent_id' => 'required',
            // 'role_id'     => 'required',

        ], $this->customMessages);
        $company_id = auth()->user()->company_id;
        $data = Member::find($id);
        $data->no = $request->no;
        $data->status = $request->status;
        $data->name = $request->name;
       
        $data->dob = $request->dob;
        $data->phone_number = $request->phone_number;
        $data->email = $request->email;
        $data->facebook = $request->facebook;
        $data->telegram = $request->telegram;
        //    $data->profile_url = $request->profile_url;
        $data->address = $request->address;
        $data->merital_status = $request->merital_status;
        $data->company = $request->company;
        $data->user_id = $request->parent_id;
        $data->job_id = $request->job_id;

        $data->company_id = $company_id;
        $data->role_id = '3';
        $data->school_id = $request->school_id;
        $data->start_year = $request->start_year;
        $data->end_year = $request->end_year;
        if ($request->file('profile_url')) {
            
            if($data->profile_url){
                if (File::exists($data->profile_url)) {
                    File::delete($data->profile_url);
                }
            }
           
            $data['profile_url'] = $request->file('profile_url')->store('uploads/employee', 'photo');
        }
        $data->update();
        $response =[
                'code'=>0,
                'message'=> 'Success',
                
        ];
        return response()->json([
            $response,200
        ]);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
