<?php

namespace App\Http\Controllers;

use App\Exports\UserExport;
use App\Imports\UsersImport;
use App\Models\Checkin;
use App\Models\Company;
use App\Models\Contract;
use App\Models\Counter;
use App\Models\Department;
use App\Models\Holiday;
use App\Models\Leavetype;
use App\Models\Position;
use App\Models\Role;
use App\Models\Structure;
use App\Models\Timetable;
use App\Models\TimetableEmployee;
use App\Models\User;
use App\Models\Workday;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Carbon;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Arr;

class UserController extends Controller
{

    protected $customMessages = [
        'required' => 'Please input the :attribute.',
        'unique' => 'This :attribute has alpready been taken.',
        'max' => ':Attribute may not be more than :max characters.',
    ];


    public function exportUsers(Request $request)
    {
        // $hh = new UserExport($request);
        // $hh->getState();
        // return response()-> json([
        //     "state"=>$hh
        // ]);
        return Excel::download(new UserExport($request), 'employees.xlsx');
    }
    public function index()
    {
        $company = auth()->user()->company_id;
        $data = User::all();
            
        if (request()->ajax()) {
            return datatables()->of($data)
                ->addColumn('action', 'admin.settings.action_location')
                // ->addColumn('image', 'admin1.users.image')
                // ->addColumn('image', function ($row) {
                //     $url= 'uploads/employee/rXbGc2oXcxMmzXss8zasrQF59FjSSEs7ENjg4Yjy.jpg';
                //      return '<div>  <img src="uploads/employee/rXbGc2oXcxMmzXss8zasrQF59FjSSEs7ENjg4Yjy.jpg" alt="logo" width="50" ></div>';
                //      })
                // ->rawColumns(['action', 'image'])
                ->rawColumns(['action'])

                ->addIndexColumn()
                ->make(true);
        }
       
        return view('admin.users.index');
    }

    public function importUser(Request $request)
    {
        
      $data =  Excel::import(new UsersImport, $request->file);

        return response([
            'message' => 'success',
            'code'=>'0',
            'data'=>$data
        ]);
       
       
        // for ($i = 0; $i < count($keyed[0][0][$i+1]); $i++) {
        //     $emails[[]]= 
        // }
        // return response()->json(
        //     $keyed[0][0][0]
        // );

        // return redirect()->route('users.index')->with('success', 'User Imported Successfully');
    }
}
