<?php

namespace App\Http\Controllers;

use App\Models\Counter;
use App\Models\Holiday;
use App\Models\Notice;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use App\Notifications\TelegramRegister;

class HolidayController extends Controller
{
    protected $customMessages = [
        'required' => 'Please input the :attribute.',
        'unique' => 'This :attribute has already been taken.',
        'max' => ':Attribute may not be more than :max characters.',
    ];

    public function index()
    {
        $data = Holiday::orderBy('from_date', 'ASC')->get();
        // $total=0;
        // for($i=0 ;$i < count($data);$i++){
        //     $result1 = Carbon::createFromFormat('Y-m-d', $data[$i]['from_date'])->isPast();
        //     $result2 = Carbon::createFromFormat('Y-m-d', $data[$i]['to_date'])->isPast();
        //     if($result1==false || $result2==false){
        //         $total += $data[$i]['duration'];
        //         // $total = $data[$i]['from_date'];
        //     }else{
        //         $total=0;
        //     }
        // }
        
        if (request()->ajax()) {
            return datatables()->of(Holiday::orderBy('from_date', 'ASC')->get())
            ->addColumn('action', 'admin.settings.action_holiday')
            ->addColumn('holiday_status', 'admin.settings.holiday_status')
            ->rawColumns(['action','holiday_status'])
            ->addIndexColumn()
            ->make(true);
        }

        // $pastDF=Carbon::parse('2024-02-10');
        // $pastDN=Carbon::parse( Carbon::now());
        // $duration_in_days =   $pastDN ->diffInDays( $pastDF)+1;
        // if($duration_in_days ==3){
        //     $notification = new Notice(
        //         [
        //             'notice' => "Wishing Letter",
        //             'noticedes' => "Holiday : DB HBD" . "\n" . "Date : 2024-02-10" . "\n",

        //             'telegramid' => Config::get('services.telegram_id')
        //         ]
        //     );

        //     //  // $notification->save();
        //     $notification->notify(new TelegramRegister());
        // }
        // return response()->json([
        //     'from_date'=>$pastDF,
        //     'now'=>$pastDN,
        //     'duration'=>$duration_in_days
        //     // 'tota'=>$total
        // ]);
        return view('admin.settings.holiday');
    }

    public function create()
    {

    }

    public function store(Request $request)
    {
        request()->validate([
            'name' => 'required|string',
            'from_date'      => 'required',
            'to_date'     => 'required',
            // 'notes'     => 'required|string',

        ], $this->customMessages);
        $pastDF=Carbon::parse( $request->from_date);
        $pastDT=Carbon::parse(  $request->to_date);
        $duration_in_days =   $pastDT ->diffInDays( $pastDF);
        $result1 = Carbon::createFromFormat('Y-m-d', $request->from_date)->isPast();
       
        $duration= ($duration_in_days +1);
        $status ="";
        if($result1==true){
            $status ="completed";
        }else{
            $status ="pending";
        }
       
        $data = Holiday::create([
            'name'           => strip_tags(request()->post('name')),
            'from_date'          => strip_tags(request()->post('from_date')),
            'to_date'         => strip_tags(request()->post('to_date')),
            'status'       => $status,
            'duration'       => $duration,
            'notes'       => strip_tags(request()->post('notes')),

        ]);
        
        // if($status =="pending"){
            
            
        //         $counter = Counter::all();
               
        //        for($i=0; $i< count($counter);$i++){
        //         // check user that change dayoff
                   
        //             $total = $counter[$i]['total_ph'] + $duration;
        //             $counter[$i]['total_ph'] = $total;
        //             $query = $counter[$i]->update();
        //        }
               
        // }
        return response()->json([
            'code'=>0,
            // 'counter'=>$counter_update,
           
            'message'=> "Data have been successfully added!"
        ]);
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $data = Holiday::findOrFail($id);

        return response()->json($data);
    }

    public function update(Request $request,$id)
    {

        request()->validate([
            'name' => 'required|string',
            'from_date'      => 'required|string',
            'to_date'     => 'required|string',

        ], $this->customMessages);
        $data = Holiday::findOrFail($id);
        $pastDF=Carbon::parse( $request->from_date);
        $pastDT=Carbon::parse(  $request->to_date);
        $duration_in_days =   $pastDT ->diffInDays( $pastDF);
        $duration= ($duration_in_days +1);
        $result1 = Carbon::createFromFormat('Y-m-d', $request->from_date)->isPast();
        // $result2 = Carbon::createFromFormat('Y-m-d', $pastDT)->isPast();
        // out put 1 ,but reality 2 
        // $duration_in_days = $request->to_date->diffInDays($request->from_date);
        // $duration= $duration_in_days;
        $duration= ($duration_in_days +1);
        $status ="";
        if($result1==true){
            $status ="completed";
        }else{
            $status ="pending";
        }
        $old_status = $data->status;
        $old_duration = $data->duration;
        // check status , if complete canot update
        $data->update([
            'name'           => strip_tags(request()->post('name')),
            'from_date'          => strip_tags(request()->post('from_date')),
            'to_date'         => strip_tags(request()->post('to_date')),
            'status'       => $status,
            'duration'=>$duration,
            'notes'       => strip_tags(request()->post('notes')),
        ]);
        $new_left_duration=0;
        // olde status ="pending" , new == complete 
        // if($old_status != $status){
        //     $counter = Counter::all();
               
        //        for($i=0; $i< count($counter);$i++){
        //         // check user that change dayoff
                   
        //             $total = $counter[$i]['total_ph'] - $old_duration;
        //             $counter[$i]['total_ph'] = $total;
        //             $query = $counter[$i]->update();
        //        }
            
        // }

        // re update user counter 

        return response()->json($data);
    }


    public function destroy($id)
    {
        $data = Holiday::find($id);
        if($data){
            if($data->status =="pending"){
            
                $data->delete();
                // $counter = Counter::all();
               
            //    for($i=0; $i< count($counter);$i++){
            //     // check user that change dayoff
                   
            //         $total = $counter[$i]['total_ph'] - $data->duration;
            //         $counter[$i]['total_ph'] = $total;
            //         $query = $counter[$i]->update();
            //    }
               
            }
            

        }
       
        

        return response()->json($data);
    }
}
