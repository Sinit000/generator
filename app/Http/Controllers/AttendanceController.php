<?php

namespace App\Http\Controllers;

use App\Exports\ExportAttendance;
use App\Imports\AttendancesImport;
use App\Models\Attendance;
use App\Models\Department;
use App\Models\Leave;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

use Illuminate\Support\Facades\DB;
use PDF;
use Exception;



class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = User::whereNotIn('id', [1])->get();
        $department = Department::all();
        return view('admin.report.attendance_report', compact('data', 'department'));
        // return view('admin.report.leave_report', compact('data'));
        // return view('admin.report.import_attendance',compact('data'));
    }
    public function attendance()
    {
        $data = User::whereNotIn('id', [1])->get();
        $department = Department::all();
        return view('admin.report.attendance_report_total', compact('data', 'department'));
        // return view('admin.report.leave_report', compact('data'));
        // return view('admin.report.import_attendance',compact('data'));
    }
    public function importAttendance(Request $request)
    {

        //    $data= Excel::import(new AttendancesImport, $request->file_excel);
        //    $code =-1;
        //    $msg = "Success";
        // if($data){
        //     $code =0;
        //     $msg= "Success";
        // }else{
        //     $code = -1;
        //     $msg = "Error";
        // }
        return response([
            // 'Code'=>$code,
            'Msg' => $request->file,

        ]);
    }
    public function totalAttendance(Request $request, User $customer)
    {
        $date = date('Y-m-d');
        Carbon::setWeekStartsAt(Carbon::MONDAY);
        $start_date = $request->startDate ?? date('Y-m-d', strtotime(Carbon::now()->startOfMonth()));
        $end_date = $request->endDate ?? date('Y-m-d', strtotime(Carbon::now()->endOfMonth()));
        $status = $request->get('filter_status', '');
        // $limit = $request->get('limit', 10);
        $today = '1';
        $thisWeek = '2';
        $thisMonth = '3';
        $thisYear = '4';
        $lastMonth = '5';
        $code = '';


        if ($request->startDate  == $today && $request->endDate ==  $today) {
            $start_date = $date;
            $end_date = $date;
        } elseif ($request->startDate  == 'yesterday' && $request->endDate ==  'yesterday') {
            $start_date = date('Y-m-d', strtotime($date . '-1 day'));
            $end_date = date('Y-m-d', strtotime($date . '-1 day'));
        } elseif ($request->startDate  == $thisWeek && $request->endDate == $thisWeek) {
            $start_date = date('Y-m-d', strtotime(Carbon::now()->startOfWeek()));
            $end_date = date('Y-m-d', strtotime(Carbon::now()->endOfWeek()));
        }
        // elseif ($request->dateRange == 'last_week') {
        //     $start_date = Carbon::now()->startOfWeek();
        //     $end_date = Carbon::now()->endOfWeek();
        //     $start_date = date('Y-m-d', strtotime($start_date . ' -7 day'));
        //     $end_date = date('Y-m-d', strtotime($end_date . ' -7 day'));
        // } 
        elseif ($request->startDate == $thisMonth && $request->endDate == $thisMonth) {
            $start_date = date('Y-m-d', strtotime(Carbon::now()->startOfMonth()));
            $end_date = date('Y-m-d', strtotime(Carbon::now()->endOfMonth()));
        } elseif ($request->startDate  == $lastMonth && $request->endDate ==  $lastMonth) {
            $start = new Carbon('first day of last month');
            $end = new Carbon('last day of last month');
            $start_date = date('Y-m-d', strtotime($start->startOfMonth()));
            $end_date = date('Y-m-d', strtotime($end->endOfMonth()));
        } elseif ($request->startDate  == $thisYear && $request->endDate == $thisYear) {
            $start = new Carbon('first day of January ' . date('Y'));
            $end = new Carbon('last day of December ' . date('Y'));
            $start_date = date('Y-m-d', strtotime($start));
            $end_date = date('Y-m-d', strtotime($end));
        } else {
            $start_date = $request->startDate;
            $end_date = $request->endDate;
        }
        if ($request->export == 'false') {
            if ($request->userId == "0") {
                // $customers = $att->groupBy('user_id');
                $customers = Attendance::select(DB::raw('
                attendances.user_id AS user_id,
                attendances.id ,
                attendances.attendance_status ,
                count(attendances.id) AS total_attendance,
                CONCAT(users.name) AS user_name
                
                '))
                    ->leftJoin('users', 'users.id', '=', 'attendances.user_id')
                    // ->where('attendances.attendance_status', '=', 'Normal')
                    // ->Orwhere('attendances.checkout_time','!=' ,'-')
                    ->groupBy('user_id');
              
                $customers = $customers->whereDate('attendances.created_at', '>=', date('Y-m-d', strtotime($start_date)))
                    ->whereDate('attendances.created_at', '<=', date('Y-m-d', strtotime($end_date)))->get();
             
                    $new_start = date('d/m/Y', strtotime( $start_date));
                    $new_end = date('d/m/Y', strtotime( $end_date));
                   
                    // ->where('send_status', '=', "true")
                   
                    // $ex1 = Leave::where('user_id', 14)
                    // ->where('date',$start_date)
                    // ->whereBetween('from_date', [$new_start,  $new_start])
                    // ->whereBetween('to_date', [$new_end,  $new_end])
                    // ->get();
                   
                foreach ($customers as $key => $val) {
                    $ex1 = Leave::where('user_id', $val->user_id)
                    ->whereDate('from_date', '>=', date('d/m/Y', strtotime($start_date)))
                    ->whereDate('to_date', '<=', date('d/m/Y', strtotime($end_date)))->get();
                   
                    $p=0;
                    $m=0;
                    for($i =0; $i <count($ex1);$i++){
                        if($ex1[$i]['status'] == "P"){
                            $p +=1;
                        }
                        if($ex1[$i]['status']== "M"){
                            $m +=1;
                        }

                    }
                    
                    $val->mission = $m;
                    $val->p = $p;
                    // $v->total_attendance = 22- ($p+$m);

                }
                // $customers = $customers->groupBy('user_id');

                $items = $customers;
                return response()->json([
                    'data' => $customers,
                    'new_start'=>$new_start,
                    'end_date'=>$new_end,
                    'leave'=>$ex1 
                ]);
                // if (request()->ajax()) {
                //     return datatables()->of($items)
                //         // ->addColumn('action', 'admin.users.action')
                //         // ->rawColumns(['action'])
                //         ->addIndexColumn()
                //         ->make(true);
                // }


            } else {
                $customers = Attendance::select(DB::raw('
                attendances.date as checkin_date,
                attendances.checkin_time AS checkin_time,
                attendances.checkout_time AS checkout_time,
                attendances.attendance_status,
                attendances.attendance_worked,
                attendances.absent,
                attendances.ot_level_one as ot,
                CONCAT(users.name) AS user_name
                
                '))
                    ->leftJoin('users', 'users.id', '=', 'attendances.user_id')
                    ->where('attendances.user_id', '=', $request->userId);
                $customers = $customers->whereDate('attendances.created_at', '>=', date('Y-m-d', strtotime($start_date)))
                    ->whereDate('attendances.created_at', '<=', date('Y-m-d', strtotime($end_date)))->get();



                $items = $customers;
                if (request()->ajax()) {
                    return datatables()->of($items)
                        // ->addColumn('action', 'admin.users.action')
                        // ->rawColumns(['action'])
                        ->addIndexColumn()
                        ->make(true);
                }
                $code = "no";
            }


            // ->leftJoin('timetable_employees', 'users.id', '=', 'timetable_employees.user_id')
            // ->leftJoin('timetables', 'timetables.id', '=', 'users.timetable_id');




        } else {
            
            if ($request->userId == "0") {
                $export = new ExportAttendance($request);
                $file_name = 'attendances_' . $start_date . '_' . $end_date . '.xlsx';
            } else {
                $user = User::find($request->userId);
                $export = new ExportAttendance($request);
                $file_name = 'attendances_' . $user->name . '_' . $start_date . '_' . $end_date . '.xlsx';
            }


            return Excel::download($export, $file_name);// $export = new CheckinExport($request);
            // $file_name = 'attendances_' . $start_date . '_' . $end_date . '.xlsx';

            // return Excel::download($export, $file_name);
        }
        // return response()->json([
        //     'user_id'=>$request->userId,
        //     'start_date'=>$request->startDate,
        //     'end_date'=>$request->endDate,
        //     'export'=>$request->export,
        //     'code'=>$code

        // ]);
    }
    public function attendanceReport(Request $request, User $customer)
    {
        $date = date('Y-m-d');
        Carbon::setWeekStartsAt(Carbon::MONDAY);
        $start_date = $request->startDate ?? date('Y-m-d', strtotime(Carbon::now()->startOfMonth()));
        $end_date = $request->endDate ?? date('Y-m-d', strtotime(Carbon::now()->endOfMonth()));
        $status = $request->get('filter_status', '');
        // $limit = $request->get('limit', 10);
        $today = '1';
        $thisWeek = '2';
        $thisMonth = '3';
        $thisYear = '4';
        $lastMonth = '5';
        $code = '';


        if ($request->startDate  == $today && $request->endDate ==  $today) {
            $start_date = $date;
            $end_date = $date;
        } elseif ($request->startDate  == 'yesterday' && $request->endDate ==  'yesterday') {
            $start_date = date('Y-m-d', strtotime($date . '-1 day'));
            $end_date = date('Y-m-d', strtotime($date . '-1 day'));
        } elseif ($request->startDate  == $thisWeek && $request->endDate == $thisWeek) {
            $start_date = date('Y-m-d', strtotime(Carbon::now()->startOfWeek()));
            $end_date = date('Y-m-d', strtotime(Carbon::now()->endOfWeek()));
        }
        // elseif ($request->dateRange == 'last_week') {
        //     $start_date = Carbon::now()->startOfWeek();
        //     $end_date = Carbon::now()->endOfWeek();
        //     $start_date = date('Y-m-d', strtotime($start_date . ' -7 day'));
        //     $end_date = date('Y-m-d', strtotime($end_date . ' -7 day'));
        // } 
        elseif ($request->startDate == $thisMonth && $request->endDate == $thisMonth) {
            $start_date = date('Y-m-d', strtotime(Carbon::now()->startOfMonth()));
            $end_date = date('Y-m-d', strtotime(Carbon::now()->endOfMonth()));
        } elseif ($request->startDate  == $lastMonth && $request->endDate ==  $lastMonth) {
            $start = new Carbon('first day of last month');
            $end = new Carbon('last day of last month');
            $start_date = date('Y-m-d', strtotime($start->startOfMonth()));
            $end_date = date('Y-m-d', strtotime($end->endOfMonth()));
        } elseif ($request->startDate  == $thisYear && $request->endDate == $thisYear) {
            $start = new Carbon('first day of January ' . date('Y'));
            $end = new Carbon('last day of December ' . date('Y'));
            $start_date = date('Y-m-d', strtotime($start));
            $end_date = date('Y-m-d', strtotime($end));
        } else {
            $start_date = $request->startDate;
            $end_date = $request->endDate;
        }
        if ($request->export == 'false') {
            if ($request->userId == "0") {
                $code = "yes";
                $customers = Attendance::select(DB::raw('
                attendances.date as checkin_date,
                attendances.checkin_time AS checkin_time,
                attendances.checkout_time AS checkout_time,
                attendances.attendance_status,
                attendances.attendance_worked,
                attendances.absent,
                attendances.ot_level_one as ot,
                CONCAT(users.name) AS user_name
                
                '))
                    ->leftJoin('users', 'users.id', '=', 'attendances.user_id');
                    $customers = $customers->whereDate('attendances.created_at', '>=', date('Y-m-d', strtotime($start_date)))
                    ->whereDate('attendances.created_at', '<=', date('Y-m-d', strtotime($end_date)))->get();
                // $customers = $customers->whereDate('attendances.created_at', '>=', date('Y-m-d', strtotime($start_date)))
                //     ->whereDate('attendances.created_at', '<=', date('Y-m-d', strtotime($end_date)))->get();



                $items = $customers;
                if (request()->ajax()) {
                    return datatables()->of($items)
                        // ->addColumn('action', 'admin.users.action')
                        // ->rawColumns(['action'])
                        ->addIndexColumn()
                        ->make(true);
                }
            } else {
                $customers = Attendance::select(DB::raw('
                attendances.date as checkin_date,
                attendances.checkin_time AS checkin_time,
                attendances.checkout_time AS checkout_time,
                attendances.attendance_status,
                attendances.attendance_worked,
                attendances.absent,
                attendances.ot_level_one as ot,
                CONCAT(users.name) AS user_name
                
                '))
                    ->leftJoin('users', 'users.id', '=', 'attendances.user_id')
                    ->where('attendances.user_id', '=', $request->userId);
                $customers = $customers->whereDate('attendances.created_at', '>=', date('Y-m-d', strtotime($start_date)))
                    ->whereDate('attendances.created_at', '<=', date('Y-m-d', strtotime($end_date)))->get();



                $items = $customers;
                if (request()->ajax()) {
                    return datatables()->of($items)
                        // ->addColumn('action', 'admin.users.action')
                        // ->rawColumns(['action'])
                        ->addIndexColumn()
                        ->make(true);
                }
                $code = "no";
            }


            // ->leftJoin('timetable_employees', 'users.id', '=', 'timetable_employees.user_id')
            // ->leftJoin('timetables', 'timetables.id', '=', 'users.timetable_id');




        } else {
            // $export = new CheckinExport($request);
            if ($request->userId == "0") {
                $export = new ExportAttendance($request);
                $file_name = 'attendances_' . $start_date . '_' . $end_date . '.xlsx';
            } else {
                $user = User::find($request->userId);
                $export = new ExportAttendance($request);
                $file_name = 'attendances_' . $user->name . '_' . $start_date . '_' . $end_date . '.xlsx';
            }


            return Excel::download($export, $file_name);

            // return Excel::download($export, $file_name);
        }
        // return response()->json([
        //     'user_id'=>$request->userId,
        //     'start_date'=>$request->startDate,
        //     'end_date'=>$request->endDate,
        //     'export'=>$request->export,
        //     'code'=>$code

        // ]);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $data= Excel::import(new AttendancesImport, $request->file);
        // $code =-1;
        // $msg = "Success";
        //  if($data){
        //      $code =0;
        //      $msg= "Success";
        //  }else{
        //      $code = -1;
        //      $msg = "Error";
        //  }
        //  return response([
        //      'Code'=>$code,
        //      'Msg'=>$msg,

        //  ]);
        return response([
            // 'Code'=>$code,
            'Msg' => $request->file,

        ]);
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
        //
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
        //
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
