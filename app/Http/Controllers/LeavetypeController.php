<?php

namespace App\Http\Controllers;

use App\Models\Counter;
use App\Models\Leave;
use App\Models\Leavetype;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;

class LeavetypeController extends Controller
{
    protected $customMessages = [
        'required' => 'Please input the :attribute.',
        'unique' => 'This :attribute has already been taken.',
        'max' => ':Attribute may not be more than :max characters.',
    ];
    public function index()
    {
        if (request()->ajax()) {
            return datatables()->of(Leavetype::orderBy('created_at', 'ASC')->get())
            ->addColumn('action', 'admin.users.action')
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        return view('admin.settings.leavetype');
    }

    public function getComponent()
    {
        $data = Leavetype::all();
        return response()->json([
            'data'=>$data
        ]);
        
    }


    public function store(Request $request)
    {

        request()->validate([
            'leave_type' => 'required|string',
            'duration' => 'required|string',
           
        ], $this->customMessages);
        $data = new Leavetype();
        $parentId =0;
        $data->leave_type   = $request->leave_type;
        $data->notes=$request->notes;
        $scope = "0";
        if($request->duration){
            $scope = $request->duration;
        }
        if($request->parent_id){
            $parentId =$request->parent_id;
        }
        $data->duration   = $scope;
        $data->parent_id   = $parentId;
        $data->save();
        $user = User::first();
        if($user){
            $peternity =0;
            $meternity =0;
            $marriage =0;
            $hosipitality =0;
            $funeral =0;
            if(str_contains($request->leave_type, 'Peternity')){
                $peternity =$scope;
            }elseif(str_contains($request->leave_type, 'Meternity')){
                $meternity =$scope;
            }
            elseif(str_contains($request->leave_type, 'Marriage')){
                $marriage =$scope;
            }
            elseif(str_contains($request->leave_type, 'Funeral')){
                $funeral =$scope;
            }
             elseif(str_contains($request->leave_type, 'Hospitality')){
                $hosipitality  =$scope;
            }else{
        
            }
                    $counter = Counter::all();
                   
                   for($i=0; $i< count($counter);$i++){
                    // check user that change dayoff
                        $counter[$i]['hospitality_leave'] = $counter[$i]['hospitality_leave'] +  $hosipitality;
                        $counter[$i]['marriage_leave'] =$counter[$i]['marriage_leave']+  $marriage;
                        $counter[$i]['peternity_leave'] =$counter[$i]['peternity_leave']+  $peternity;
                        $counter[$i]['funeral_leave'] = $counter[$i]['funeral_leave']+ $funeral;
                        $counter[$i]['maternity_leave'] = $counter[$i]['maternity_leave']+  $meternity;
                        $query = $counter[$i]->update();
                   }
                   
        }
        return response()->json($query);

    }


    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        $type = Leavetype::all();
        $data = Leavetype::findOrFail($id);
            // return response()->json($data);
            return response()->json(['data'=>$data,'type'=>$type]);

    }

    public function update(Request $request, $id)
    {
        try {
            request()->validate([
                'leave_type' => 'required|string',
                'duration' => 'required|string',
            ], $this->customMessages);
            $data = Leavetype::findOrFail($id);
            $parentId =0;
            $data->leave_type   = $request->leave_type;
            $data->notes=$request->notes;
            $scope = "0";
            if($request->duration){
                $scope = $request->duration;
            }
            if($request->parent_id){
                $parentId =$request->parent_id;
            }
            $data->duration   = $scope;
            $data->parent_id   = $parentId;
            $query = $data->update();
            // check leave 
            $leave = Leave::all();
            if(count($leave)==0){
                $user = User::first();
                if($user){
                    $peternity =0;
                    $meternity =0;
                    $marriage =0;
                    $hosipitality =0;
                    $funeral =0;
                    if(str_contains($request->leave_type, 'Peternity')){
                        $peternity =$scope;
                    }elseif(str_contains($request->leave_type, 'Meternity')){
                        $meternity =$scope;
                    }
                    elseif(str_contains($request->leave_type, 'Marriage')){
                        $marriage =$scope;
                    }
                    elseif(str_contains($request->leave_type, 'Funeral')){
                        $funeral =$scope;
                    }
                     elseif(str_contains($request->leave_type, 'Hospitality')){
                        $hosipitality  =$scope;
                    }else{
                
                    }
                            $counter = Counter::all();
                           
                           for($i=0; $i< count($counter);$i++){
                            // check user that change dayoff
                                $counter[$i]['hospitality_leave'] = $counter[$i]['hospitality_leave'] +  $hosipitality;
                                $counter[$i]['marriage_leave'] =$counter[$i]['marriage_leave']+  $marriage;
                                $counter[$i]['peternity_leave'] =$counter[$i]['peternity_leave']+  $peternity;
                                $counter[$i]['funeral_leave'] = $counter[$i]['funeral_leave']+ $funeral;
                                $counter[$i]['maternity_leave'] = $counter[$i]['maternity_leave']+  $meternity;
                                $query = $counter[$i]->update();
                           }
                           
                }
                // 
            }
            
            return response()->json($query);
        } catch (Exception $e) {

                return response()->json(
                [
                    'message'=>$e->getMessage(),
                ]
            );
        }
    }


    public function destroy($id)
    {
        // $data = Leavetype::destroy($id);
        $data = Leavetype::find($id);
        if($data){
            // check if position id belong to employee table
           //  $postion = Leavetype::with('leaves')->where('id',$id)->get();
            $emp = Leave::where('leave_type_id', $data->id)->first();
           if(!$emp){
            // check parent id
            if($data->parent_id==0){
                $data->delete();
                $respone = [
                    'message'=>'Success',
                    'code'=>0,
                ];
            }else{
                $respone = [
                    'message'=>'Cannot delete this leave type',
                    'code'=>-1,
                   //  'data'=> $emp,
                ];
            }
               
           }else{
               $respone = [
                   'message'=>'Cannot delete this leave type',
                   'code'=>-1,
                  //  'data'=> $emp,
               ];
           }

        }else{
            $respone = [
                'message'=>'No leavetype id found',
                'code'=>-1,
            ];
        }
        return response()->json(
            $respone ,200
        );
    }
}
