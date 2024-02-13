<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use App\Models\Workday;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\Html\Button;
use Exception;
use Illuminate\Support\Str;

class WorkdayController extends Controller
{

    protected $customMessages = [
        'required' => 'Please input the :attribute.',
        // 'unique' => 'This :attribute has already been taken.',
        // 'max' => ':Attribute may not be more than :max characters.',
    ];

    public function index()
    {
        if (request()->ajax()) {
            return datatables()->of(Workday::orderBy('created_at', 'DESC')->get())
                ->addColumn('action', 'admin.users.action')
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('admin.settings.workday');
    }

    public function create()
    {
    }

    public function store(Request $request)
    {
        request()->validate([
            'name' => 'required|string',
            'working_day' => 'required|string',
            'off_day'     => 'required|string',

        ], $this->customMessages);
        if (str_contains($request->working_day, ',') ) {
            $workday = $request->working_day;
            $offDay = $request->off_day;
            $x = explode(",",$workday); 
            $y =  explode(",",$offDay);
            $code = 1;
            $mgs ="";
            $input1 = $x;
            $input2 = $y;
           $array = array_unique($input1); // Array is now (1, 2, 3)
                        $newWorday = implode(',', array_unique(explode(",",$request->working_day)));
                        $newOffDay = implode(',', array_unique(explode(",",$request->off_day)));
                        $result = array_intersect($array, $input2);
            if(count($result)==0){
                // 
                for($i=0;$i<$newWorday;$i++){
                    if($newWorday[$i] == 1 || $newWorday[$i]=="1"){
                        $code = 0;
                    }
                    elseif($newWorday[$i] == 2 || $newWorday[$i]=="2"){
                        $code = 0;
                    }
                    elseif($newWorday[$i] == 3 || $newWorday[$i]=="3"){
                        $code = 0;
                    }
                    elseif($newWorday[$i] == 4 || $newWorday[$i]=="4"){
                        $code = 0;
                    }
                    elseif($newWorday[$i] == 5 || $newWorday[$i]=="5"){
                        $code = 0;
                    }
                    elseif($newWorday[$i] == 6 || $newWorday[$i]=="6"){
                        $code = 0;
                    }else{
                        $code =-1;
                    }

                }
                
                for($i=0;$i<$newOffDay;$i++){
                    if($newWorday[$i] == 1 || $newWorday[$i]=="1"){
                        $code = 1;
                    }
                    elseif($newWorday[$i] == 2 || $newWorday[$i]=="2"){
                        $code = 1;
                    }
                    elseif($newWorday[$i] == 3 || $newWorday[$i]=="3"){
                        $code = 1;
                    }
                    elseif($newWorday[$i] == 4 || $newWorday[$i]=="4"){
                        $code = 1;
                    }
                    elseif($newWorday[$i] == 5 || $newWorday[$i]=="5"){
                        $code = 1;
                    }
                    elseif($newWorday[$i] == 6 || $newWorday[$i]=="6"){
                        $code = 1;
                    }else{
                        $code =-1;
                    }
                }
            }else{
                $code =-1;
            }
            if($code == 0 && $code ==1){
                $data = Workday::create([
                    'name'           => strip_tags(request()->post('name')),
                    'working_day'          =>  $newWorday,
                    'off_day'         => $newOffDay,
                    'notes'       => strip_tags(request()->post('notes')),
                    'type_date_time'         => 'server',
                    
        
                ]);
                $respone =[
                    'code'=>0,
                    'message'=>'Success',
                    
                ];
            }else{
                $respone =[
                    'code'=>-1,
                    'message'=>'Please checkin your input data',
                    
                ];
            }
        }else{
            $respone =[
                    'code'=>-1,
                    'message'=>'Wrong format input',
                    
                ];
        }
        
        
        
        return response()->json( $respone,200);
    }
    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $data = Workday::findOrFail($id);

        return response()->json($data);
    }

    public function update(Request $request,$id)
    {

        request()->validate([
            'name' => 'required|string',
            'working_day'      => 'required|string',
            'off_day'     => 'required|string',

        ], $this->customMessages);
        if (str_contains($request->working_day, ',') ) {
            $data = Workday::findOrFail($id);
            $workday = $request->working_day;
            $offDay = $request->off_day;
            $x = explode(",",$workday); 
            $y =  explode(",",$offDay);
            $code = 1;
            $mgs ="";
            $input1 = $x;
            $input2 = $y;
             $array = array_unique($input1); // Array is now (1, 2, 3)
                        $newWorday = implode(',', array_unique(explode(",",$request->working_day)));
                        $newOffDay = implode(',', array_unique(explode(",",$request->off_day)));
                        $result = array_intersect($array, $input2);
            
            if(count($result)==0){
                $code = 0;
            }else{
                $code =-1;
            }
            if($code == 0){
                $data->update([
                    'name'           => strip_tags(request()->post('name')),
                    'working_day'          => $newWorday,
                    'off_day'         => $newOffDay,
                    'notes'       => strip_tags(request()->post('notes')),
                ]);
                $respone =[
                    'code'=>0,
                    'message'=>'Success'
                ];
            }else{
                $respone =[
                    'code'=>-1,
                    'message'=>'Please checkin your input data',
                ];
            }
        }else{
            $respone =[
                    'code'=>-1,
                    'message'=>'Wrong format input',
                    
                ];
        }
        
        


        

        return response()->json($respone,200);
    }
    public function updateTime(Request $request, $date)
    {
        try {
            //code...
            $data = Workday::first();
            $day = Str::substr($date, 2, 2);
            $month = Str::substr($date, 0, 2);
            $year = Str::substr($date, 4, 4);
            $time = $month . '/' . $day . '/'. $year;
            // $time = (string)$month + "/" + (string)$day + "/" + (string)$year;
            // $comments =  Str::substr($request->$comment, 0, 9);
            $data->date_time =$time;
            $query=  $data->save();
            return response()->json([
                'code' => '0',
                'message' => 'Success',
                'date'=>$time
                

            ]);
        } catch (Exception $e) {
            // return response($e ,200);
            return response()->json(
                [
                    'message' => $e->getMessage(),
                    // 'data'=>[]
                ]
            );
        }



        // return response()->json( $query);
    }


    public function destroy($id)
    {
        $data = Workday::find($id);
        if ($data) {
            $emp = User::where('workday_id', $data->id)->first();
            if ($emp) {
                $respone = [
                    'message' => 'Cannot delete this workday',
                    'code' => -1,
                    //  'data'=> $emp,
                ];
            } else {
                $data->delete();
                $respone = [
                    'message' => 'Success',
                    'code' => 0,
                ];
            }
        }

        return response(
            $respone,
            200
        );

        // return response()->json($data);
    }
    public function showdate()
    {
        $data = Workday::first();

        // return response()->json([
        //     'data'=>$data
        // ]);
        // return response(
        //     [

        //         'data'=>$data->id
        //     ]
        // );
        return view('admin.settings.showdatetime', compact('data'));
    }
    public function updateDate(Request $r)
    {

        

        // $data = Workday::find($i);
        $values = [
            'type_date_time' => $r->type_date_time,
            
        ];
       $data= Workday::query()->update($values);
       return response()->json([
        'code'=>0,
        'message'=>'Success'
       ]);
        // if ($data) {
           
        // }
        // return redirect()->back()->with('success', "Datetime has been changed!");
    }
}
