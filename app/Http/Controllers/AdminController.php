<?php

namespace App\Http\Controllers;

use App\Exports\CheckinExport;
use App\Exports\LeaveExport;
use App\Exports\OvertimeExport;
use App\Models\User;
use App\Models\Employee;
use App\Models\Latetime;
use App\Models\Attendance;
use App\Models\Checkin;
use App\Models\CheckinHistory;
use App\Models\Contract;
use App\Models\Counter;
use App\Models\Leave;
use App\Models\Overtime;
use App\Models\Timetable;
use App\Models\Workday;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;
use Exception;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UserExport;
use App\Models\Leaveout;
class AdminController extends Controller
{

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            return $next($request);
        })->except(['index']);
    }

    protected $customMessages = [
        'required' => 'Please input the :attribute.',
        'unique' => 'This :attribute has already been taken.',
        'integer' => ':Attribute must be a number.',
        'min' => ':Attribute must be at least :min.',
        'max' => ':Attribute may not be more than :max characters.',
        'profile_url.max' => ':Attribute size may not be more than :max kb.',
        'exists' => 'Not found.',
        'sn.required' => 'Please input Serial Number',
        'gender.required' => 'Please select "Male" or "Female".',
        'disabled.required' => 'Please select "Yes" or "No".',
        'role_id.required' => 'Please select Role.',
    ];

    public function dashboard()
    {
        // $todayDate = Carbon::now()->format('m/d/Y');
        // $data = User::whereNotIn('id', [1])->count();
        // $checkin = Checkin::where('checkin_status', '=', 'on time')
        //     ->where('date', '=', $todayDate)->count();
        // $late = Checkin::where('checkin_status', '=', 'late')
        //     ->where('date', '=', $todayDate)->count();
        // $overtime = Checkin::where('checkout_status', '=', 'very good')
        //     ->where('date', '=', $todayDate)->count();
        // $absent = Checkin::where('status', '=', 'absent')
        //     ->where('date', '=', $todayDate)->count();
        // $leave = Checkin::where('status', '=', 'leave')
        //     ->where('date', '=', $todayDate)->count();


        return view('admin.dashboard');
        // return view('admin.dashboard', compact('data', 'checkin', 'late', 'overtime', 'absent', 'leave'));
    }
    public function attendance(Request $request)
    {
        try {

            //code...
            // get user by token with timetable
            $use_id = $request->user()->id;
            $pageSize = $request->page_size ?? 10;
            $todayDate = Carbon::now()->format('m/d/Y');
            $start_date = date('Y-m-d', strtotime(Carbon::now()->startOfMonth()));
            $end_date = date('Y-m-d', strtotime(Carbon::now()->endOfMonth()));
            // $postion = Timetable::paginate($pageSize);
            //count checkin ,late ,overtime, total employee
            // $records = Employee::all();
            $records = User::with('timetable')->whereNotIn('id', [1])

                ->get();
            // $notCheck = $this->getWeekday($todayDate);
            // checkin
            foreach ($records as $record) {

                $checkinStatus = "false";
                $checkinRecord = Checkin::where('user_id', $record->id)
                    // ->whereDate('checkins.created_at', '>=', date('Y-m-d', strtotime($start_date)))
                    // ->whereDate('checkins.created_at', '<=', date('Y-m-d', strtotime($end_date)))
                    // ->orderBy('created_at', 'DESC')
                    ->where('date', '=', $todayDate)
                    // ->where('status','!=','leave')
                    // ->latest()
                    ->first();
                $work = "";
                $dayoff = Workday::find($record->workday_id);
                // $countd = Workday::find($record->workday_id)->count();
                // check workday 
                if ($dayoff) {
                    $workday = explode(',', $dayoff->off_day);
                    // work day
                    $check = "true";
                    $notCheck = $this->getWeekday($todayDate);
                    // 1 = count($dayoff)
                    for ($i = 0; $i <  count($workday); ++$i) {
                        //   if offday = today check will false
                        if ($workday[$i] == $notCheck) {
                            $check = "false";
                        }
                    }
                    if ($check == "false") {
                        // day off cannot check
                        $work = "false";
                    } else {
                        $work = "true";
                    }
                }
                $record->workday = $work;
                // if already checkin
                if ($checkinRecord) {
                    // if have checkout status : change to present (already checkin and checkout)
                    if ($checkinRecord->checkout_status) {
                        $checkinStatus = "present";
                    } else {
                        // only checkin
                        $checkinStatus = "true";
                    }
                    //    if checkin status =absent
                    if ($checkinRecord->status == "absent") {
                        $checkinStatus = "absent";
                    }
                    if ($checkinRecord->status == "leave") {
                        $checkinStatus = "leave";
                    }
                }


                // if($checkinRecord)
                // {
                //     $checkinStatus="true";
                // }
                // new field create after compare with checkin
                $record->checkin_status = $checkinStatus;

                $record->checkin = $checkinRecord;
                if ($checkinRecord) {
                    $record->checkin_id = $checkinRecord['id'];
                } else {
                    $record->checkin_id = null;
                }
                // check day off


            }
            if (request()->ajax()) {
                return datatables()->of($records)
                    ->addColumn('action', 'admin.attendance.action_attendance')
                    ->addColumn('btn', 'admin.attendance.action_btn')
                    ->addColumn('checkin_time', 'admin.attendance.component.checkin_time')
                    ->addColumn('checkin_status', 'admin.attendance.component.checkin_status')
                    ->addColumn('checkin_late', 'admin.attendance.component.checkin_late')

                    ->addColumn('checkout_time', 'admin.attendance.component.checkout_time')
                    ->addColumn('checkout_status', 'admin.attendance.component.checkout_status')
                    ->addColumn('checkout_late', 'admin.attendance.component.checkout_late')
                    ->addColumn('duration', 'admin.attendance.component.duration')
                    ->addColumn('date', 'admin.attendance.component.date')
                    // ->addColumn('checkbox', function($row){
                    //     if($row['send_status']=='false'){
                    //         return '<input type="checkbox" name="country_checkbox" data-id="'.$row['id'].'"><label></label>';
                    //     }

                    // })
                    ->rawColumns(['action', 'btn', 'checkin_time', 'checkin_status', 'checkin_late', 'checkout_time', 'checkout_status', 'checkout_late', 'duration', 'date'])
                    ->addIndexColumn()
                    ->make(true);
            }

            return view('admin.attendance.attendance', compact('records'));

            // return response(
            //     $records,
            //     200
            // );
        } catch (Exception $e) {

            return response([
                'message' => $e->getMessage()
            ]);
        }
    }
    public function checkin(Request $request, $userId)
    {
        try {
            $status = ["on time", "very good", "late", "too late"];
            $todayDate = Carbon::now()->format('m/d/Y');
            $timeNow = Carbon::now()->format('H:i:s');

            $targetStatus = "";


            $employee = User::find($userId);
            $duration = 0;

            if ($employee) {
                // $position = Position::find($employee->position_id);
                $scann = Checkin::where('user_id', '=', $employee->id)->latest()->first();
                // $dateIn = $request['date'];
                $i = "";
                $findtime = Timetable::find($employee->timetable_id);
                $userCheckin = Carbon::parse($timeNow);
                $userDuty = Carbon::parse($findtime->on_duty_time);
                $diff = $userCheckin->diff($userDuty);
                $hour = ($diff->h) * 60;
                $minute = $diff->i;
                $code = "";
                $case = "";
                $min = "";
                $today = Carbon::now()->format('Y-m-d');
                $lateMinuteAdmin = $findtime['late_minute'];
                $overtime = Overtime::where('user_id', '=', $employee->id)
                    ->where('from_date', '=', $today)
                    ->where('status','=','approved')
                    ->orWhere('to_date', '=', $today)->latest()->first();
                $otStatus = "false";

                if ($scann) {
                    if ($scann->date != $todayDate) {
                        $workday = Workday::find($employee->workday_id);
                        $countOffDay = explode(",", $workday->off_day);

                        $contract = Contract::where('user_id', '=', $employee->id)->first();
                        if ($contract) {
                            $standardHour = ($contract->working_schedule) / (7 - count($countOffDay));
                        } else {
                            $standardHour = 9;
                        }
                        $duration = $standardHour / 2;

                        // $schedule = TimetableEmployee::where('user_id', $request['user_id'])->first();
                        // $findtime = Timetable::find($schedule->timetable_id);
                        if ($findtime) {
                            if ($findtime['late_minute'] == "0") {

                                if ($findtime['on_duty_time'] == $timeNow) {
                                    $targetStatus = $status[0];
                                    $min = "0";
                                    $duration = $duration;
                                    $duration = round($duration, 2);
                                    $code = 0;
                                    $case = "1";
                                }   // 07:30 // 6
                                elseif ($findtime['on_duty_time'] > $timeNow) {
                                    $targetStatus = $status[1];
                                    $min = $minute + $hour;
                                    $duration = ($duration * 60 + $min) / 60;
                                    $duration = round($duration, 2);
                                    $code = 0;
                                    $case = "2";
                                }
                                // 08:00,8:30
                                elseif ($findtime['on_duty_time'] < $timeNow) {
                                    $targetStatus = $status[2];
                                    $min = $minute + $hour;
                                    $duration = ($duration * 60 - $min) / 60;
                                    $duration = round($duration, 2);
                                    $code = 0;
                                    $case = "3";
                                }
                                $checkin = Checkin::create([
                                    'checkin_time' => $timeNow,
                                    'date' => $todayDate,
                                    'status' => "checkin",
                                    'checkin_status' => $targetStatus,
                                    'checkin_late' => $min,
                                    'user_id' => $employee->id,
                                    'send_status' => 'false',
                                    'ot_status' =>  $otStatus,
                                    'confirm' => 'false',
                                    'duration' => $duration,


                                ]);
                                $respone = [
                                    'message' => 'Success',
                                    'code' => 0,
                                  
                                ];
                            } else {

                                //  if admin set late minute
                                if ($findtime['on_duty_time'] == $timeNow) {
                                    $targetStatus = $status[0];
                                    $min = ($minute + $hour);
                                    $duration = $duration;
                                    $duration = round($duration, 2);
                                    $code = 0;
                                }   // 07:30 // 6
                                elseif ($findtime['on_duty_time'] > $timeNow) {
                                    $targetStatus = $status[1];
                                    $min = ($minute + $hour) - $lateMinuteAdmin;
                                    $duration = ($duration * 60 + $min) / 60;
                                    $duration = round($duration, 2);
                                    $code = 0;
                                }
                                // 08:00,8:30
                                elseif ($findtime['on_duty_time'] < $timeNow) {
                                    $targetStatus = $status[2];
                                    $min = ($minute + $hour) - $lateMinuteAdmin;
                                    $duration = ($duration * 60 - $min) / 60;
                                    $duration = round($duration, 2);
                                    $code = 0;
                                }
                                $checkin = Checkin::create([
                                    'checkin_time' => $timeNow,
                                    'date' => $todayDate,
                                    'status' => "checkin",
                                    'checkin_status' => $targetStatus,
                                    'checkin_late' => $min,
                                    'user_id' => $employee->id,
                                    'send_status' => 'false',
                                    'ot_status' =>  $otStatus,
                                    'confirm' => 'false',
                                    'duration' => $duration,

                                ]);
                                $respone = [
                                    'message' => 'Success',
                                    'code' => 0,
                                    // 'message' => 'Success',
                                    // 'code' => 0,
                                    // 'checkin_time' => $timeNow,
                                    // 'date' => $todayDate,
                                    // 'status' => "checkin",
                                    // 'checkin_status' => $targetStatus,
                                    // 'checkin_late' => $min,
                                    // 'user_id' => $employee->id,
                                    // 'send_status' => 'false',
                                    // 'ot_status' =>  $otStatus,
                                    // 'confirm' => 'false',
                                    // 'duration' => $duration,

                                ];
                            }
                        }
                        if ($overtime) {
                            // $otStatus = "true";
                            $overtime->status = "completed";
                            // $overtime->update();
                            if ($overtime->pay_type == "holiday") {
                                $overtime->pay_status = "completed";
                                $counter = Counter::where('user_id', '=', $employee->id)->first();
                                if ($overtime->type == "hour") {
                                    $counter->ot_duration  = $counter->ot_duration + $overtime->number;
                                } else {
                                    $counter->ot_duration  = $counter->ot_duration + $overtime->number * 8;
                                }
                                $counter->update();
                            }
                            $overtime->update();
                        }
                        // update overtime if user overtime today

                    } else {
                        $respone = [
                            'message' => 'cannot checkin',
                            'code' => -1,
                            'checkindate' => $todayDate,
                            'lastcheckin' => $scann->date,
                            // "req"=>$checkin,
                        ];
                    }
                } else {
                    // first scann for employee type 1 :have timetable 1
                    // $schedule = TimetableEmployee::where('user_id', $request['user_id'])->first();
                    // $findtime = Timetable::find($schedule->timetable_id);

                    if ($findtime) {
                        if ($findtime['late_minute'] == "0") {

                            if ($findtime['on_duty_time'] == $timeNow) {
                                $targetStatus = $status[0];
                                $min = "0";
                                $duration = $duration;
                                $duration = round($duration, 2);
                                $code = 0;
                            }   // 07:30 // 6
                            elseif ($findtime['on_duty_time'] > $timeNow) {
                                $targetStatus = $status[1];
                                $min = $minute + $hour;
                                $duration = ($duration * 60 + $min) / 60;
                                $duration = round($duration, 2);
                                $code = 0;
                            }
                            // 08:00,8:30
                            elseif ($findtime['on_duty_time'] < $timeNow) {
                                $targetStatus = $status[2];
                                $min = $minute + $hour;
                                $duration = ($duration * 60 - $min) / 60;
                                $duration = round($duration, 2);
                                $code = 0;
                            }
                            $checkin = Checkin::create([
                                'checkin_time' => $timeNow,
                                'date' => $todayDate,
                                'status' => "checkin",
                                'checkin_status' => $targetStatus,
                                'checkin_late' => $min,
                                'user_id' => $employee->id,
                                'send_status' => 'false',
                                'confirm' => 'false',
                                'ot_status' =>  $otStatus,
                                'duration' => $duration,


                            ]);
                            $respone = [
                                'message' => 'Success',
                                'code' => 0,
                            ];
                        } else {

                            //  if admin set late minute
                            if ($findtime['on_duty_time'] == $timeNow) {
                                $targetStatus = $status[0];
                                $min = ($minute + $hour);
                                $duration = $duration;
                                $duration = round($duration, 2);
                                $code = 0;
                            }   // 07:30 // 6
                            elseif ($findtime['on_duty_time'] > $timeNow) {
                                $targetStatus = $status[1];
                                $min = ($minute + $hour) - $lateMinuteAdmin;
                                $duration = ($duration * 60 + $min) / 60;
                                $duration = round($duration, 2);
                                $code = 0;
                            }
                            // 08:00,8:30
                            elseif ($findtime['on_duty_time'] < $timeNow) {
                                $targetStatus = $status[2];
                                $min = ($minute + $hour) - $lateMinuteAdmin;
                                $duration = ($duration * 60 - $min) / 60;
                                $duration = round($duration, 2);
                                $code = 0;
                            }
                            $checkin = Checkin::create([
                                'checkin_time' => $timeNow,
                                'date' => $todayDate,
                                'status' => "checkin",
                                'checkin_status' => $targetStatus,
                                'checkin_late' => $min,
                                'user_id' => $employee->id,
                                'send_status' => 'false',
                                'confirm' => 'false',
                                'ot_status' =>  $otStatus,
                                'duration' => $duration,

                            ]);
                            $respone = [
                                'message' => 'Success',
                                'code' => 0,

                            ];
                        }
                    } else {
                        $respone = [
                            'message' => 'NO timetable',
                            'code' => -1,
                            // 'overtime'=>"hi",

                        ];
                    }
                    if ($overtime) {
                        // $otStatus = "true";
                        $overtime->status = "completed";
                        // $overtime->update();
                        if ($overtime->pay_type == "holiday") {
                            $overtime->pay_status = "completed";
                            $counter = Counter::where('user_id', '=', $employee->id)->first();
                            if ($overtime->type == "hour") {
                                $counter->ot_duration  = $counter->ot_duration + $overtime->number;
                            } else {
                                $counter->ot_duration  = $counter->ot_duration + $overtime->number * 8;
                            }
                            $counter->update();
                        }
                        $overtime->update();
                    }

                    // update overtime if user overtime today

                }
                return response()->json(
                    $respone,
                    200
                );
            } else {
                return response()->json(
                    [
                        'message' => "No employee id found",
                        'code' => -1,
                    ],
                    200
                );
            }
        } catch (Exception $e) {
            return response([
                'message' => $e->getMessage()
            ]);
        }
    }
    public function checkout(Request $request, $checkinId)
    {
        try {
            $status = ["good", "very good", "early", "too fast"];
            $todayDate = Carbon::now()->format('m/d/Y');
            $targetStatus = "";
            // $findCheckid = Checkin::find($id);
            $findCheckid = Checkin::find($checkinId);
            $timeNow = Carbon::now()->format('H:i:s');
            $employee = User::find($findCheckid->user_id);


            if ($employee) {


                // $position = Position::find($employee->position_id);
                // retain condition user can checkout two time in oneday
                $scann = Checkin::where('user_id', '=', $employee->id)
                    ->where('id', $checkinId)
                    ->whereNull('checkout_time')
                    ->latest()->first();
                if ($scann) {
                    $findtime = Timetable::find($employee->timetable_id);

                    $userCheckin = Carbon::parse($timeNow);
                    $userDuty = Carbon::parse($findtime->off_duty_time);
                    $diff = $userCheckin->diff($userDuty);
                    $hour = ($diff->h) * 60;
                    $minute = $diff->i;
                    $code = "";
                    $min = "";
                    $case = "";
                    $totalMn = "";
                    $chekinLate = 0;
                    $chekinBF = 0;
                    $chekoutearly = 0;
                    $chekoutBF = 0;
                    $lateMinuteAdmin = $findtime['early_leave'];
                    $standardHour = 0;
                    // if ($scann->checkin_status == "late" || $scann->checkin_status == "too late") {
                    //     $chekinLate =  $scann->checkin_late;
                    // } else {
                    //     // start 8, come 7: 30
                    //     $chekinBF =  $scann->checkin_late;
                    // }
                    $leave = Leave::where('user_id', '=', $employee->id)
                        ->where('from_date', '=', $todayDate)
                        ->first();
                    $leaveDuration = 0;
                    if ($leave) {
                        // check leave status
                        // calculate as mn

                        if ($leave->type == "hour") {

                            $leaveDuration = ($leave->number) * 60;
                        }
                        if ($leave->type == "half_day_m") {
                            $leaveDuration = 4.5 * 60;
                        }
                        if ($leave->type == "half_day_n") {
                            $leaveDuration = 4.5 * 60;
                        }
                    } else {
                        $leaveDuration = 0;
                    }
                    $durationLeaveout = 0;
                    // check employee type first
                    if ($scann->date = $todayDate) {
                        $workday = Workday::find($employee->workday_id);
                        $countOffDay = explode(",", $workday->off_day);
                        // $standardHour = 0;
                        // $schedule = TimetableEmployee::where('user_id', $request['user_id'])->first();
                        $contract = Contract::where('user_id', '=', $employee->id)->first();
                        if ($contract) {
                            $standardHour = ($contract->working_schedule) / (7 - count($countOffDay));
                        } else {
                            $standardHour = 9;
                        }
                        // if ($contract) {
                        //     $standardHour = ($contract->working_schedule) / 6;
                        // } else {
                        //     $standardHour = 9;
                        // }
                        $duration = $standardHour / 2;
                        $Leaveout = Leaveout::where('user_id', 2)
                            ->where('date', '=', $todayDate)
                            ->where('status', '=', 'completed')
                            ->first();
                        // $Leaveout=Leaveout::all();
                        $songTime =  Leaveout::where('user_id', $employee->id)
                            ->where('date', '=', $todayDate)
                            ->where('status', '=', 'approved')
                            ->first();
                        $songTimeDuration = 0;
                        if ($songTime) {
                            if ($songTime->type == "hour") {
                                $durationLeaveout = $songTime['duration'];
                            } else {
                                $durationLeaveout = $songTime['duration'] / 60;
                            }
                            $findCounter = Counter::where('user_id', '=', $employee->id)->first();
                            $minusDuration = 0;
                            if ($findCounter) {
                                $minusDuration = $findCounter->ot_duration + $durationLeaveout;
                            }
                            $findCounter->ot_duration = $minusDuration;
                            $findCounter->update();
                        }
                        

                        if ($Leaveout) {
                            if ($Leaveout->type == "hour") {
                                $durationLeaveout = $Leaveout['duration'];
                            } else {
                                $durationLeaveout = $Leaveout['duration'] / 60;
                            }
                            $findCounter = Counter::where('user_id', '=', $employee->id)->first();
                            $minusDuration = 0;
                            if ($findCounter) {
                                if ($findCounter->ot_duration > 0) {
                                    if ($findCounter->ot_duration > $durationLeaveout) {
                                        $minusDuration = $findCounter->ot_duration - $durationLeaveout;
                                    }
                                    if ($findCounter->ot_duration == $durationLeaveout) {
                                        $minusDuration = 0;
                                    }
                                    if ($findCounter->ot_duration < $durationLeaveout) {
                                        $minusDuration = $durationLeaveout - $findCounter->ot_duration;
                                    }
                                }
                                if ($findCounter->ot_duration <= 0) {
                                    $minusDuration = -$durationLeaveout;
                                }
                            }
                            $findCounter->ot_duration = $minusDuration;
                            $findCounter->update();
                        }


                        if ($findtime) {
                            if ($findtime['early_leave'] == "0") {
                                if ($findtime['off_duty_time'] == $timeNow) {
                                    $targetStatus = $status[0];
                                    $min = "0";
                                    $code = 0;
                                }
                                // 07:30 // 6
                                // 05:30 => 6:00
                                // 05:30, 5:00
                                elseif ($findtime['off_duty_time'] > $timeNow) {
                                    $targetStatus = $status[3];
                                    $min = $minute + $hour;
                                    $code = 0;
                                }
                                // 08:00,8:30
                                elseif ($findtime['off_duty_time'] < $timeNow) {
                                    $targetStatus = $status[0];
                                    $min = $minute + $hour;
                                    $code = 0;
                                }
                                if ($targetStatus == "early" || $targetStatus == "too early") {
                                    $chekoutearly = $min;
                                } else {
                                    $chekoutBF = $min;
                                }
                                $totalMn = ($scann->duration + $duration) - ($chekoutearly +  $leaveDuration  - $chekoutBF) / 60;
                                // $totalMn = $standardHour - ($chekinLate +  $chekoutearly +  $leaveDuration -  $chekinBF - $chekoutBF) / 60;
                                $totalMn = round($totalMn, 2);
                                // $scann->user_id = $request['user_id'];
                                $scann->checkout_time = $timeNow;
                                $scann->checkout_status = $targetStatus;
                                $scann->status = "present";
                                $scann->duration = $totalMn;
                                $scann->checkout_late = $min;
                                $scann->update();
                                $respone = [
                                    'message' => 'Success',
                                    'code' => 0,


                                ];
                            } else {
                                if ($findtime['off_duty_time'] == $timeNow) {
                                    $targetStatus = $status[0];
                                    $min = "0";
                                    $code = 0;
                                }
                                // 07:30 // 6
                                // 05:30 => 6:00
                                // 05:30, 5:00
                                elseif ($findtime['off_duty_time'] > $timeNow) {
                                    $targetStatus = $status[3];
                                    $min = $minute + $hour;
                                    $code = 0;
                                }
                                // 08:00,8:30
                                elseif ($findtime['off_duty_time'] < $timeNow) {
                                    $targetStatus = $status[0];
                                    $min = $minute + $hour;
                                    $code = 0;
                                }
                                if ($targetStatus == "early" || $targetStatus == "too early") {
                                    $chekoutearly = $min;
                                } else {
                                    $chekoutBF = $min;
                                }
                                $totalMn = ($scann->duration + $duration) - ($chekoutearly +  $leaveDuration  - $chekoutBF) / 60;
                                $totalMn = round($totalMn, 2);
                                // $scann->user_id = $request['user_id'];
                                $scann->checkout_time = $timeNow;
                                $scann->checkout_status = $targetStatus;
                                $scann->status = "present";
                                $scann->duration = $totalMn;
                                $scann->checkout_late = $min;
                                $scann->update();
                                $respone = [
                                    'message' => 'Success',
                                    'code' => 0,
                                ];
                            }
                        }
                    } else {
                        $respone = [
                            'message' => 'cannot checkout',
                            'code' => -1,
                            'checkindate' => $todayDate,
                            'lastcheckin' => $scann->date,
                            // "req"=>$checkin,
                        ];
                    }
                } else {
                    // don't have checkin id = employee id
                    $respone = [
                        'message' => 'employee already checkout ',
                        'code' => -1,

                    ];
                }


                return response()->json(
                    $respone,
                    200
                );
            } else {
                return response()->json(
                    [
                        'message' => "No employee found",
                        'code' => -1,
                    ],
                    200
                );
            }
        } catch (Exception $e) {
            return response([
                'message' => $e->getMessage()
            ]);
        }
    }
    // leave report

    function getWeekday($date)
    {
        return date('w', strtotime($date));
    }
    public function card()
    {
        $user = User::where('id', auth()->user()->id)->get();
        return view('admin.card', compact('user'));
    }
    public function index()
    {

        $todayDate = Carbon::now()->format('m/d/Y');
        $data = User::whereNotIn('id', [1])->count();
        $checkin = Checkin::where('checkin_status', '=', 'on time')
            ->where('date', '=', $todayDate)->count();
        $late = Checkin::where('checkin_status', '=', 'late')
            ->where('date', '=', $todayDate)->count();

        return view('admin.index', compact('data', 'checkin', 'late'));
    }
    public function qr()
    {
        return view('admin.settings.qr');
    }
    public function viewAttendanceAll()
    {
        // $data = User::whereNotIn('id', [1])->get();

        return view('admin.report.attendance_all');
    }
    public function viewAttendance()
    {
        $data = User::whereNotIn('id', [1])->get();

        return view('admin.report.attendace_report', compact('data'));
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
        // return response()->json([
        //     'dateRang'=>$request->id
        // ]);

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
            $customers = Checkin::select(DB::raw('
            
            DATE_FORMAT(checkins.created_at, "%d-%m-%Y") as checkin_date,
            checkins.checkin_time AS checkin_time,
            checkins.checkin_status AS checkin_status,
            checkins.checkin_late,
            checkins.checkout_time,
            checkins.checkout_status,
            checkins.checkout_late,
        
            checkins.status,
            checkins.duration,
            CONCAT(users.name) AS user_name,
            CONCAT(timetables.on_duty_time ,"-",timetables.off_duty_time) AS timetable_id
            '))
                ->leftJoin('users', 'users.id', '=', 'checkins.user_id')
                // ->leftJoin('timetable_employees', 'users.id', '=', 'timetable_employees.user_id')
                ->leftJoin('timetables', 'timetables.id', '=', 'users.timetable_id');

            if ($request->search && !empty($request->search)) {
                $search = $request->search;
                $customers = $customers
                    ->where(function ($query) use ($search) {
                        $query->where('users.id',      'like',     '%' . $search . '%');
                        $query->orWhere('users.name',      'like',     '%' . $search . '%');
                        $query->orWhere('users.email',      'like',     '%' . $search . '%');
                    });
                // ->where("customers.deleted", 0);
            }

            $customers = $customers->whereDate('checkins.created_at', '>=', date('Y-m-d', strtotime($start_date)))
                ->whereDate('checkins.created_at', '<=', date('Y-m-d', strtotime($end_date)))->get();
            $items = $customers;

            if (request()->ajax()) {
                return datatables()->of($items)
                    // ->addColumn('action', 'admin.users.action')
                    // ->rawColumns(['action'])
                    ->addIndexColumn()
                    ->make(true);
            }
        } else {
            $export = new CheckinExport($request);
            $file_name = 'attendances_' . $start_date . '_' . $end_date . '.xlsx';

            return Excel::download($export, $file_name);
        }
    }
    // attendance by employee
    public function attendanceEmployee(Request $request, User $customer)
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
        // return response()->json([
        //     'dateRang'=>$request->id
        // ]);

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
            $customers = Checkin::select(DB::raw('
                
            DATE_FORMAT(checkins.created_at, "%d-%m-%Y") as checkin_date,
            checkins.id AS checkin_id,
            checkins.checkin_time AS checkin_time,
            checkins.checkin_status AS checkin_status,
            checkins.checkin_late,
            checkins.checkout_time,
            checkins.checkout_status,
            checkins.checkout_late,
            checkins.send_status,
            checkins.status,
            checkins.duration,
            CONCAT(users.name) AS user_name,
            CONCAT(users.id) AS user_id,
            CONCAT(timetables.on_duty_time ,"-",timetables.off_duty_time) AS timetable_id
            '))
                ->leftJoin('users', 'users.id', '=', 'checkins.user_id')
                // ->leftJoin('timetable_employees', 'users.id', '=', 'timetable_employees.user_id')
                ->leftJoin('timetables', 'timetables.id', '=', 'users.timetable_id')
                ->where('checkins.user_id', '=', $request->id)
                // ->where('checkins.status','=','present')
            ;
            //     if ($request->search && !empty($request->search)) {
            //     $search = $request->search;
            //     $customers = $customers
            //         ->where(function ($query) use ($search) {
            //             $query->where('users.id',      'like',     '%' . $search . '%');
            //             $query->orWhere('users.name',      'like',     '%' . $search . '%');
            //             $query->orWhere('users.email',      'like',     '%' . $search . '%');

            //         });
            //     // ->where("customers.deleted", 0);
            // }


            $customers = $customers->whereDate('checkins.created_at', '>=', date('Y-m-d', strtotime($start_date)))
                ->whereDate('checkins.created_at', '<=', date('Y-m-d', strtotime($end_date)))->get();
            $items = $customers;
            // $d= Checkin::where('user_id','=',2)->whereDate('checkins.created_at', '>=', date('Y-m-d', strtotime($start_date)))
            // ->whereDate('checkins.created_at', '<=', date('Y-m-d', strtotime($end_date)))->get();


            if (request()->ajax()) {
                return datatables()->of($items)
                    // ->addColumn('action', 'admin.users.action')
                    // ->rawColumns(['action'])
                    ->addIndexColumn()
                    ->make(true);
            }
        } else {
            if ($request->id) {
                $user = User::find($request->id);
                $export = new CheckinExport($request);
                $file_name = 'attendances_' . $user->name . '_' . $start_date . '_' . $end_date . '.xlsx';
            } else {
                $export = new CheckinExport($request);
                $file_name = 'attendances_' . $start_date . '_' . $end_date . '.xlsx';
            }


            return Excel::download($export, $file_name);
        }
    }
    // leave report
    public function leaveView()
    {
        $data = User::whereNotIn('id', [1])->get();
        return view('admin.report.leave_report', compact('data'));
    }
    public function leaveReport(Request $request, User $customer)
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
        // return response()->json([
        //     'dateRang'=>$request->id
        // ]);

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

            // if (request()->ajax()) {
            //     return datatables()->of($items)
            //         ->addIndexColumn()
            //         ->make(true);
            // }
            if ($request->userId == "0") {
                $customers = Leave::select(DB::raw('
                DATE_FORMAT(leaves.created_at, "%d-%m-%Y") as checkin_date,
                leaves.id As leave_id,
                leaves.reason AS reason,
                leaves.from_date,
                leaves.type,
                leaves.send_status,
                leaves.number,
                leaves.date,
                leaves.leave_deduction,
                leaves.status,
                CONCAT(users.name) AS user_name,
                CONCAT(leavetypes.leave_type) AS typename
               
                '))
                    ->leftJoin('users', 'users.id', '=', 'leaves.user_id')
                    ->leftJoin('leavetypes', 'leavetypes.id', '=', 'leaves.leave_type_id');
                // ->where('leaves.user_id', '=', $request->id)

                $customers = $customers->whereDate('leaves.created_at', '>=', date('Y-m-d', strtotime($start_date)))
                    ->whereDate('leaves.created_at', '<=', date('Y-m-d', strtotime($end_date)))->get();
                $items = $customers;
            } else {
                $customers = Leave::select(DB::raw('
                DATE_FORMAT(leaves.created_at, "%d-%m-%Y") as checkin_date,
                leaves.id As leave_id,
                leaves.reason AS reason,
                leaves.from_date,
                leaves.type,
                leaves.send_status,
                leaves.number,
                leaves.date,
                leaves.leave_deduction,
                leaves.status,
                CONCAT(users.name) AS user_name,
                CONCAT(leavetypes.leave_type) AS typename
               
                '))
                    ->leftJoin('users', 'users.id', '=', 'leaves.user_id')
                    ->leftJoin('leavetypes', 'leavetypes.id', '=', 'leaves.leave_type_id')
                    ->where('leaves.user_id', '=', $request->userId);

                $customers = $customers->whereDate('leaves.created_at', '>=', date('Y-m-d', strtotime($start_date)))
                    ->whereDate('leaves.created_at', '<=', date('Y-m-d', strtotime($end_date)))->get();
                $items = $customers;
            }
            // return response()->json([
            //     'data'=>$items
            // ]);
            if (request()->ajax()) {
                return datatables()->of($items)
                    ->addIndexColumn()
                    ->make(true);
            }
        } else {
            if ($request->userId == "0") {
                $export = new LeaveExport($request);
                $file_name = 'leaves_' . $start_date . '_' . $end_date . '.xlsx';
            } else {
                $user = User::find($request->userId);
                $export = new LeaveExport($request);
                $file_name = 'leaves_' . $user->name . '_' . $start_date . '_' . $end_date . '.xlsx';
            }


            return Excel::download($export, $file_name);
        }
        //    return response()->json([
        //     'export'=>$request->export,
        //     'user_id'=>$request->userId,
        //     'start_date'=>$request->startDate,
        //     'end_date'=>$request->endDate
        //    ]);

        // return view('admin.report.employee_report',compact('request', 'start_date', 'end_date'));

    }
    // overtime report
    public function overtimeView(Request $request, Overtime $customer)
    {
        $data = User::whereNotIn('id', [1])->get();


        return view('admin.report.report_overtime', compact('data'));
    }
    public function overtimeReport(Request $request, Overtime $customer)
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
                $customers = Overtime::select(

                    DB::raw('
                    
               
                    overtimes.id As overtime_id,
                overtimes.reason,
                overtimes.from_date,
                overtimes.type,
                overtimes.number,
                overtimes.ot_rate,
                overtimes.ot_hour,
                overtimes.ot_method,
                overtimes.total_ot,
                overtimes.date,
                overtimes.requested_by,
                overtimes.pay_type,
                overtimes.send_status,
                overtimes.status,
                CONCAT(users.name) AS user_name,
                CONCAT(positions.position_name) AS position_name
                ')
                )
                    ->leftJoin('users', 'users.id', '=', 'overtimes.user_id')
                    ->leftJoin('positions', 'positions.id', '=', 'users.position_id')
                    ->where('overtimes.pay_type', '=', 'cash');
                $customers = $customers->whereDate('overtimes.created_at', '>=', date('Y-m-d', strtotime($start_date)))
                    ->whereDate('overtimes.created_at', '<=', date('Y-m-d', strtotime($end_date)))->get();
                $items = $customers;
            } else {
                $customers = Overtime::select(

                    DB::raw('
                    
               
                    overtimes.id As overtime_id,
                overtimes.reason,
                overtimes.from_date,
                overtimes.type,
                overtimes.number,
                overtimes.ot_rate,
                overtimes.ot_hour,
                overtimes.ot_method,
                overtimes.total_ot,
                overtimes.date,
                overtimes.requested_by,
                overtimes.pay_type,
                overtimes.status,
                overtimes.send_status,
                CONCAT(users.name) AS user_name,
                CONCAT(positions.position_name) AS position_name
                ')
                )
                    ->leftJoin('users', 'users.id', '=', 'overtimes.user_id')
                    ->leftJoin('positions', 'positions.id', '=', 'users.position_id')
                    ->where('overtimes.pay_type', '=', 'cash')
                    ->where('overtimes.user_id', '=', $request->userId);
                $customers = $customers->whereDate('overtimes.created_at', '>=', date('Y-m-d', strtotime($start_date)))
                    ->whereDate('overtimes.created_at', '<=', date('Y-m-d', strtotime($end_date)))->get();
                $items = $customers;
            }

            if (request()->ajax()) {
                return datatables()->of($items)

                    ->addIndexColumn()
                    ->make(true);
            }
        } else {
            // if ($request->startDate == "3" && $request->endDate == "3") {
            //     $start_date = date('Y-m-d', strtotime(Carbon::now()->startOfMonth()));
            //     $end_date = date('Y-m-d', strtotime(Carbon::now()->endOfMonth()));
            // }
            if ($request->userId == "0") {
                $export = new OvertimeExport($request);
                $file_name = 'overtimes_' . $start_date . '_' . $end_date . '.xlsx';
            } else {
                $user = User::find($request->userId);
                $export = new OvertimeExport($request);
                $file_name = 'overtimes_' . $user->name . '_' . $start_date . '_' . $end_date . '.xlsx';
            }

            // return response()->json([
            //     'data'=>$start_date
            // ]);
            // $file_name = 'employees_'.date('Y-m-d').'.xlsx'; 
            return Excel::download($export, $file_name);
        }
        // if ($request->id == 0) {
        //     $customers = Overtime::select(

        //         DB::raw('


        //         overtimes.id As overtime_id,
        //     overtimes.reason,

        //     overtimes.send_status,
        //     overtimes.from_date,
        //     overtimes.type,
        //     overtimes.number,
        //     overtimes.ot_rate,
        //     overtimes.ot_hour,
        //     overtimes.ot_method,
        //     overtimes.total_ot,
        //     overtimes.date,
        //     overtimes.requested_by,
        //     overtimes.pay_type,
        //     overtimes.status,
        //     CONCAT(users.name) AS user_name,
        //     CONCAT(positions.position_name) AS position_name
        //     ')
        //     )
        //         ->leftJoin('users', 'users.id', '=', 'overtimes.user_id')
        //         ->leftJoin('positions', 'positions.id', '=', 'users.position_id');
        // } else {

        // }




    }
    public function report(Request $request, User $user)
    {
        $date = date('Y-m-d');
        Carbon::setWeekStartsAt(Carbon::MONDAY);
        $start_date = $request->start_date ?? date('Y-m-d', strtotime(Carbon::now()->startOfMonth()));
        $end_date = $request->end_date ?? date('Y-m-d', strtotime(Carbon::now()->endOfMonth()));
        $status = $request->get('filter_status', '');
        $limit = $request->get('limit', 10);
        if ($request->between_date == 'today') {
            $start_date = $date;
            $end_date = $date;
        } elseif ($request->between_date == 'yesterday') {
            $start_date = date('Y-m-d', strtotime($date . '-1 day'));
            $end_date = date('Y-m-d', strtotime($date . '-1 day'));
        } elseif ($request->between_date == 'this_week') {
            $start_date = date('Y-m-d', strtotime(Carbon::now()->startOfWeek()));
            $end_date = date('Y-m-d', strtotime(Carbon::now()->endOfWeek()));
        } elseif ($request->between_date == 'last_week') {
            $start_date = Carbon::now()->startOfWeek();
            $end_date = Carbon::now()->endOfWeek();
            $start_date = date('Y-m-d', strtotime($start_date . ' -7 day'));
            $end_date = date('Y-m-d', strtotime($end_date . ' -7 day'));
        } elseif ($request->between_date == 'this_month') {
            $start_date = date('Y-m-d', strtotime(Carbon::now()->startOfMonth()));
            $end_date = date('Y-m-d', strtotime(Carbon::now()->endOfMonth()));
        } elseif ($request->between_date == 'last_month') {
            $start = new Carbon('first day of last month');
            $end = new Carbon('last day of last month');
            $start_date = date('Y-m-d', strtotime($start->startOfMonth()));
            $end_date = date('Y-m-d', strtotime($end->endOfMonth()));
        } elseif ($request->between_date == 'this_year') {
            $start = new Carbon('first day of January ' . date('Y'));
            $end = new Carbon('last day of December ' . date('Y'));
            $start_date = date('Y-m-d', strtotime($start));
            $end_date = date('Y-m-d', strtotime($end));
        } elseif ($request->between_date == 'last_year') {
            $last_year = date('Y') - 1;
            $start = new Carbon('first day of January ' . $last_year);
            $end = new Carbon('last day of December ' . $last_year);
            $start_date = date('Y-m-d', strtotime($start));
            $end_date = date('Y-m-d', strtotime($end));
        } elseif ($start_date != '' && $end_date != '') {
            $start_date = Date('Y-m-d', strtotime($start_date));
            $end_date = Date('Y-m-d', strtotime($end_date));
        }
        $customers = Checkin::select(DB::raw('
            
            DATE_FORMAT(checkins.created_at, "%d-%m-%Y") as checkin_date,
            checkins.checkin_time AS checkin_time,
            checkins.checkin_status AS checkin_status,
            checkins.checkin_late,
            checkins.checkout_time,
            checkins.checkout_status,
            checkins.checkout_late,
           
            checkins.status,
            CONCAT(users.name) AS user_name,
            CONCAT(timetables.on_duty_time ,"-",timetables.off_duty_time) AS timetable_id
        '))
            ->leftJoin('users', 'users.id', '=', 'checkins.user_id')
            ->leftJoin('timetable_employees', 'users.id', '=', 'timetable_employees.user_id')
            ->leftJoin('timetables', 'timetable_employees.timetable_id', '=', 'timetables.id');
        if ($request->search && !empty($request->search)) {
            $search = $request->search;
            $customers = $customers
                ->where(function ($query) use ($search) {
                    $query->where('users.id',      'like',     '%' . $search . '%');
                    $query->orWhere('users.name',      'like',     '%' . $search . '%');
                    $query->orWhere('users.email',      'like',     '%' . $search . '%');
                    // $query->orWhere('customers.age',      'like',     '%'.$search.'%');
                    // $query->orWhere('customers.phone1',      'like',     '%'.$search.'%');
                    // $query->orWhere('customers.phone2',      'like',     '%'.$search.'%');
                    // $query->orWhere('customers.email',      'like',     '%'.$search.'%');
                    // $query->orWhere('customers.fax',      'like',     '%'.$search.'%');
                    // $query->orWhere('customers.address',      'like',     '%'.$search.'%');
                    // $query->orWhere('customers.pob',      'like',     '%'.$search.'%');
                });
            // ->where("customers.deleted", 0);
        }

        $customers = $customers->whereBetween('checkins.created_at', [$start_date, $end_date]);
        $customers = $customers->get();
        $items = $customers;
        $start_date = date('d-m-Y', strtotime($start_date));
        $end_date = date('d-m-Y', strtotime($end_date));
        // return view('back-end.reports.loan_report',compact('start_date','end_date'))->with('item', $items);

    }

    public function systemReport(Request $request)
    {
        $from = $request->input('from');
        $data['month'] = date('m-Y', strtotime($from));
        $to = $request->input('to');
        $data['employee'] = User::all();
        // $data['leave']=Leave::where('employee_id',$employee_id)->get();
        $attendance['attendance'] = DB::table('users')->join('checkins', 'checkins.user_id', '=', 'users.id')
            ->select('*')
            ->whereDate('checkins.created_at', '>=', date('Y-m-d', strtotime($from)))
            ->whereDate('checkins.created_at', '<=', date('Y-m-d', strtotime($to)))->get();
        // $attendance['attendance']=Checkin::with('employee')
        // ->whereDate('checkins.created_at', '>=', date('Y-m-d',strtotime($from)))
        //  ->whereDate('checkins.created_at', '<=', date('Y-m-d',strtotime($to)))->get();
        //  return response([
        //                 'status'=>200,
        //                 'data'=>$attendance,
        //             ]);
        $pdf = PDF::loadView('admin.report.system_report_pdf', $attendance, $data);
        // $pdf->SetProtection(['copy', 'print'], '', 'pass');
        return $pdf->stream('report.pdf');
    }

    public function viewEmployee(Request $request)
    {
        return view('admin.report.employee_report');
    }

    public function employee(Request $request, User $customer)
    {

        // return Excel::download(new UserExport($request), 'employees.xlsx');
        $date = date('Y-m-d');
        Carbon::setWeekStartsAt(Carbon::MONDAY);
        $start_date = $request->start_date ?? date('Y-m-d', strtotime(Carbon::now()->startOfMonth()));
        $end_date = $request->end_date ?? date('Y-m-d', strtotime(Carbon::now()->endOfMonth()));
        $status = $request->get('filter_status', '');
        // $limit = $request->get('limit', 10);
        $today = '1';
        $thisWeek = '2';
        $thisMonth = '3';
        $thisYear = '4';
        $lastMonth = '5';
        $lastYear = '6';


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
        elseif ($request->startDate == $lastMonth && $request->endDate == $lastMonth) {
            $start = new Carbon('first day of last month');
            $end = new Carbon('last day of last month');
            $start_date = date('Y-m-d', strtotime($start->startOfMonth()));
            $end_date = date('Y-m-d', strtotime($end->endOfMonth()));
        } elseif ($request->startDate == $thisMonth && $request->endDate == $thisMonth) {
            $start_date = date('Y-m-d', strtotime(Carbon::now()->startOfMonth()));
            $end_date = date('Y-m-d', strtotime(Carbon::now()->endOfMonth()));
        } elseif ($request->startDate  == $thisYear && $request->endDate == $thisYear) {
            $start = new Carbon('first day of January ' . date('Y'));
            $end = new Carbon('last day of December ' . date('Y'));
            $start_date = date('Y-m-d', strtotime($start));
            $end_date = date('Y-m-d', strtotime($end));
        } elseif ($request->startDate  == $lastYear && $request->endDate == $lastYear) {
            $last_year = date('Y') - 1;
            $start = new Carbon('first day of January ' . $last_year);
            $end = new Carbon('last day of December ' . $last_year);
            $start_date = date('Y-m-d', strtotime($start));
            $end_date = date('Y-m-d', strtotime($end));
        } else {
            $start_date = $request->startDate;
            $end_date = $request->endDate;
        }
        if ($request->export == 'false') {



            // elseif ($request->startDate != '' && $request->endDate != '') {
            //     $start_date = Date('Y-m-d', strtotime($start_date));
            //     $end_date = Date('Y-m-d', strtotime($end_date));
            // }
            $customers = User::select(DB::raw('
               users.name,
               users.id,
               users.status,
               users.gender,
               users.employee_phone,
               users.email,
               departments.department_name,
               positions.position_name,
               positions.type AS position_type,
               DATE_FORMAT(users.created_at, "%Y-%m-%d") as customer_date,
               contracts.start_date,
               contracts.working_schedule,
               structures.base_salary
           '))->leftJoin('departments', 'users.department_id', '=', 'departments.id')
                ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
                ->leftJoin('contracts', 'users.id', '=', 'contracts.user_id')

                // ->rightJoin('contracts', 'users.id', '=', 'contracts.user_id')
                ->leftJoin('structures', 'contracts.structure_id', '=', 'structures.id');


            $customers = $customers->whereDate('users.created_at', '>=', date('Y-m-d', strtotime($start_date)))
                ->whereDate('users.created_at', '<=', date('Y-m-d', strtotime($end_date)))->get();
            $items = $customers;



            if (request()->ajax()) {
                return datatables()->of($items)
                    // ->addColumn('action', 'admin.users.action')
                    // ->rawColumns(['action'])
                    ->addIndexColumn()
                    ->make(true);
            }
        } else {
            $export = new UserExport($request);

            $file_name = 'employees_' . $start_date . '_' . $end_date . '.xlsx';
            return Excel::download($export, $file_name);
        }
    }
    public function sendEmployee(Request $request, User $customer)
    {

        try {
            $country_ids = $request->countries_ids;
            $values = [
                'status' => 'true'
            ];
            User::whereIn('id', $country_ids)->update($values);


            $respone = [
                'message' => 'Success',
                'code' => 0,
                'data' => $country_ids
            ];

            return response(
                $respone,
                200
            );
        } catch (Exception $e) {
            //throw $th;
            return response()->json(
                [
                    'message' => $e->getMessage(),
                    // 'data'=>[]
                ]
            );
        }
    }
    public function employeeReport(Request $request)
    {
        $employee_id = $request->user_id;
        
        $from = $request->input('from');
        $data['month'] = date('m-Y', strtotime($from));
        $to = $request->input('to');
        if ($employee_id) {
            $data['employee'] = User::find($employee_id);
            $data['leave'] = Leave::where('user_id', $employee_id)->get();
            $attendance['attendance'] = Checkin::with('user')->where('user_id', $employee_id)
                ->whereDate('checkins.created_at', '>=', date('Y-m-d', strtotime($from)))
                ->whereDate('checkins.created_at', '<=', date('Y-m-d', strtotime($to)))->get();

            $pdf = PDF::loadView('admin.report.employee_report_pdf', $attendance, $data);
            // $pdf->SetProtection(['copy', 'print'], '', 'pass');
            return $pdf->stream('document.pdf');
        }
    }
    public function resetPassowrd()
    {
        $data['employees'] = User::whereNotIn('id', [1])->get();
        return view('admin.settings.reset_user_password', $data);
    }
    // send to account
    public function sendAttencanetoAccount(Request $request)
    {
        try {
            
            $country_ids = $request->countries_ids;
            $user = User::find($request->user_id);
            $work = Workday::where('id', '=', $user->workday_id)->first();
            $date = date('Y-m-d');
            Carbon::setWeekStartsAt(Carbon::MONDAY);
            $start_date = $request->from_date ?? date('Y-m-d', strtotime(Carbon::now()->startOfMonth()));
            $end_date = $request->to_date ?? date('Y-m-d', strtotime(Carbon::now()->endOfMonth()));
            $status = $request->get('filter_status', '');
            // $limit = $request->get('limit', 10);
            $today = '1';
            $thisWeek = '2';
            $thisMonth = '3';
            $thisYear = '4';
            $lastMonth = '5';
            $lastYear = '6';


            if ($request->from_date  == $today && $request->to_date ==  $today) {
                $start_date = $date;
                $end_date = $date;
            } elseif ($request->from_date  == 'yesterday' && $request->to_date ==  'yesterday') {
                $start_date = date('Y-m-d', strtotime($date . '-1 day'));
                $end_date = date('Y-m-d', strtotime($date . '-1 day'));
            } elseif ($request->from_date  == $thisWeek && $request->to_date == $thisWeek) {
                $start_date = date('Y-m-d', strtotime(Carbon::now()->startOfWeek()));
                $end_date = date('Y-m-d', strtotime(Carbon::now()->endOfWeek()));
            }
        
            elseif ($request->from_date == $lastMonth && $request->to_date == $lastMonth) {
                $start = new Carbon('first day of last month');
                $end = new Carbon('last day of last month');
                $start_date = date('Y-m-d', strtotime($start->startOfMonth()));
                $end_date = date('Y-m-d', strtotime($end->endOfMonth()));
            } elseif ($request->from_date == $thisMonth && $request->to_date == $thisMonth) {
                $start_date = date('Y-m-d', strtotime(Carbon::now()->startOfMonth()));
                $end_date = date('Y-m-d', strtotime(Carbon::now()->endOfMonth()));
            } elseif ($request->from_date  == $thisYear && $request->to_date == $thisYear) {
                $start = new Carbon('first day of January ' . date('Y'));
                $end = new Carbon('last day of December ' . date('Y'));
                $start_date = date('Y-m-d', strtotime($start));
                $end_date = date('Y-m-d', strtotime($end));
            } elseif ($request->from_date  == $lastYear && $request->to_date == $lastYear) {
                $last_year = date('Y') - 1;
                $start = new Carbon('first day of January ' . $last_year);
                $end = new Carbon('last day of December ' . $last_year);
                $start_date = date('Y-m-d', strtotime($start));
                $end_date = date('Y-m-d', strtotime($end));
            } else {
                $start_date = $request->from_date;
                $end_date = $request->to_date;
            }
            $isWork = "";
            $countDuration=0;
            $standance_att=0;
            $monthName ="";
            $add_to_counter=1;
            if ($work->off_day != Null) {
                $isWork="true";
            } else {
                $isWork="false";
                $pastDF = Carbon::parse( $start_date);
                $pastDT = Carbon::parse( $end_date);
             
                $countDuration =   $pastDT->diffInDays($pastDF);
                $countDuration = $countDuration + 1; 
                 $monthName = Carbon::createFromFormat('Y-m-d',  $start_date)->format('F');
                $counter = Counter::where('user_id','=', $user->id)->first() ;

                if($monthName == "February"){
                    $standance_att= 24;
                }else{
                    $standance_att= 26;
                }
                $num_att= count($request->countries_ids);
                // $add_to_counter = 1;
                if($num_att > $standance_att ){
                    $add_to_counter = $num_att-$standance_att;
                    if($add_to_counter >=4){
                        $add_to_counter=4;
                    }else{
                        $add_to_counter = $add_to_counter;
                    }
                    $counter->ot_duration =$counter->ot_duration +  $add_to_counter * 9;
                    $counter->update();
                    // $isWork="bigg";
                }
                

                
            }
           

            $values = [
                'send_status' => 'true'
            ];
            Checkin::whereIn('id', $country_ids)->update($values);


            $respone = [
                'message' => 'Success',
                'code' => 0,
                // 'data' => $country_ids,
                // 'count'=>count($request->countries_ids),
                // 'star_date'=>$start_date,
                // 'end_date'=>$end_date,
                // 'work'=>$isWork,
                // 'duration'=>$countDuration,
                // 'month'=> $monthName,
                // 'duration_to_counter'=>$add_to_counter
            ];

            return response(
                $respone,
                200
            );
        } catch (Exception $e) {
            //throw $th;
            return response()->json(
                [
                    'message' => $e->getMessage(),
                    // 'data'=>[]
                ]
            );
        }
    }
    public function editcheckin(Request $request, $id)
    {
        try {
            $statusIn = ["on time", "very good", "late", "too late"];
            $statusOut = ["good", "very good", "early", "too early"];
            $todayDate = Carbon::now()->format('m/d/Y H:i:s');
            $timeNow = Carbon::now()->format('H:i:s');

            $targetStatusIn = "";
            $targetStatusOut = "";
            $userId = $request->user_id;
            $checkin = $request->checkin_time;
            $checkout = $request->checkout_time;
            $note = $request->note;


            $employee = User::find($userId);
            $checkin_status = "";
            $checkout_status = "";
            $checkin_late = 0;
            $checkout_late = 0;
            $checkintime = "";
            $checkouttime = "";
            $lastDuration = 0;


            if ($employee) {
                // $position = Position::find($employee->position_id);
                $scannIn = Checkin::find($id);
                // $dateIn = $request['date'];
                $i = "";
                $findtime = Timetable::find($employee->timetable_id);
                $userCheckin = Carbon::parse($checkin);
                $userDutyIn = Carbon::parse($findtime->on_duty_time);
                $diffIn = $userCheckin->diff($userDutyIn);
                $hourIn = ($diffIn->h) * 60;
                $minuteIn = $diffIn->i;
                $codeIn = "";
                $minIn = 0;
                $lateMinuteAdmin = $findtime['late_minute'];
                // checkout 
                $userCheckout = Carbon::parse($checkout);
                $userDutyOff = Carbon::parse($findtime->off_duty_time);
                $diffOut = $userCheckout->diff($userDutyOff);
                $hourOut = ($diffOut->h) * 60;
                $minuteOut = $diffOut->i;
                $codeOut = "";
                $minOut = "";
                $earlyMinuteAdmin = $findtime['early_leave'];
                // $today = Carbon::now()->format('Y-m-d');
                $totalMn = "";
                $chekinLate = 0;
                $chekinBF = 0;
                $chekoutearly = 0;
                $chekoutBF = 0;
                $status_attendance="";
                if($checkin){
                    $status_attendance="checkin";
                }
                if( $checkout){
                    $status_attendance="present";
                }
                if ($scannIn) {
                    $checkin = $request->checkin_time;
                // $checkout = $request->checkout_time;
                    
                    if ($findtime['late_minute'] == "0") {

                        if ($findtime['on_duty_time'] == $checkin) {
                            $targetStatusIn = $statusIn[0];
                            $minIn = "0";
                            $codeIn = 0;
                        }   // 07:30 // 6
                        elseif ($findtime['on_duty_time'] > $checkin) {

                            $targetStatusIn = $statusIn[1];
                            $minIn = $minuteIn + $hourIn;
                            $codeIn = 0;
                        }
                        // 08:00,8:30
                        elseif ($findtime['on_duty_time'] < $checkin) {

                            $targetStatusIn = $statusIn[2];
                            $minIn = $minuteIn + $hourIn;
                            $codeIn = 0;
                        }
                        $checkintime = $checkin;
                        $checkin_status = $targetStatusIn;
                        $checkin_late = $minIn;
                    } else {

                        //  if admin set late minute
                        if ($findtime['on_duty_time'] == $checkin) {
                            $targetStatusIn = $statusIn[0];
                            $minIn = ($minuteIn + $hourIn) - $lateMinuteAdmin;
                            $codeIn = 0;
                        }   // 07:30 // 6
                        elseif ($findtime['on_duty_time'] > $checkin) {
                            $targetStatusIn = $statusIn[1];
                            $minIn = ($minuteIn + $hourIn) - $lateMinuteAdmin;
                            $codeIn = 0;
                        }
                        // 08:00,8:30
                        elseif ($findtime['on_duty_time'] < $checkin) {
                            $targetStatusIn = $statusIn[2];
                            $minIn = ($minuteIn + $hourIn) - $lateMinuteAdmin;
                            $codeIn = 0;
                        }
                        $checkintime = $checkin;
                        $checkin_status = $targetStatusIn;
                        $checkin_late = $minIn;
                    }
                    // checkout function
                    $standardHour = 0;
                    if ($targetStatusIn == "late" || $targetStatusIn == "too late") {
                        $chekinLate =  $minIn;
                    } else {
                        // start 8, come 7: 30
                        $chekinBF =  $minIn;
                    }
                    $contract = Contract::where('user_id', '=', $employee->id)->first();
                    if ($contract) {
                        $standardHour = ($contract->working_schedule) / 6;
                    } else {
                        $standardHour = 9;
                    }
                    $leave = Leave::where('user_id', '=', $employee->id)
                        ->where('from_date', '=', $scannIn->date)
                        ->first();
                    $leaveDuration = 0;
                    if ($leave) {
                        // check leave status
                        // calculate as mn

                        if ($leave->type == "hour") {

                            $leaveDuration = ($leave->number) * 60;
                        }
                        if ($leave->type == "half_day_m") {
                            $leaveDuration = 4.5 * 60;
                        }
                        if ($leave->type == "half_day_n") {
                            $leaveDuration = 4.5 * 60;
                        }
                    } else {
                        $leaveDuration = 0;
                    }
                    if ($findtime['early_leave'] == "0") {
                        if ($findtime['off_duty_time'] == $checkout) {
                            $targetStatusOut = $statusOut[0];
                            $minOut = "0";
                            $codeOut = 0;
                            $case = "1";
                        }
                        // user leave before time out
                        // user leave 4, but time out 5
                        elseif ($findtime['off_duty_time'] > $checkout) {
                            $targetStatusOut = $statusOut[3];
                            $minOut = $minuteOut + $hourOut;
                            $codeOut = 0;
                            $case = "2";
                        }
                        // user finish after time out 
                        // user leave 8 , timeout 6
                        elseif ($findtime['off_duty_time'] < $checkout) {
                            $targetStatusOut = $statusOut[0];
                            $minOut = $minuteOut + $hourOut;
                            $codeOut = 0;
                            $case = "3";
                        }
                        // standard hour for 1 day =9 h * 60mn = mn 540 mn
                        if ($targetStatusOut == "early" || $targetStatusOut == "too early") {
                            $chekoutearly = $minOut;
                        } else {
                            $chekoutBF = $minOut;
                        }
                        $totalMn = $standardHour - ($chekinLate +  $chekoutearly +  $leaveDuration -  $chekinBF - $chekoutBF) / 60;
                        $totalMn = round($totalMn, 2);
                        $checkouttime = $checkout;
                        $checkout_status = $targetStatusOut;
                        $checkout_late = $minOut;
                        $lastDuration = $totalMn;
                    } else {
                        // if admin set for leave early
                        if ($findtime['off_duty_time'] == $checkout) {
                            $targetStatusOut = $statusOut[0];
                            $minOut = "0";
                            $codeOut = 0;
                        }
                        // user leave before time out
                        // user leave 4, but time out 5
                        elseif ($findtime['off_duty_time'] > $checkout) {
                            $targetStatusOut = $statusOut[3];
                            $minOut = $minuteOut + $hourOut;
                            $codeOut = 0;
                        }
                        // user leave after time out
                        // 08:00,8:30
                        elseif ($findtime['off_duty_time'] < $checkout) {
                            $targetStatusOut = $statusOut[0];
                            $minOut = $minuteOut + $hourOut;
                            $codeOut = 0;
                        }
                        if ($targetStatusOut == "early" || $targetStatusOut == "too early") {
                            $chekoutearly = $minOut;
                        } else {
                            $chekoutBF = $minOut;
                        }
                        $totalMn =  $standardHour - ($chekinLate +  $chekoutearly +  $leaveDuration -  $chekinBF - $chekoutBF) / 60;
                        $totalMn = round($totalMn, 2);
                        $checkouttime = $checkout;
                        $checkout_status = $targetStatusOut;
                        $checkout_late = $minOut;
                        $lastDuration = $totalMn;
                    }
                    $scannIn->checkin_time = $checkintime;
                    $scannIn->checkout_time = $checkouttime;
                    $scannIn->checkin_status = $checkin_status;
                    $scannIn->checkout_status = $checkout_status;
                    $scannIn->checkin_late = $checkin_late;
                    $scannIn->checkout_late = $checkout_late;
                    $scannIn->status = $status_attendance;
                    $scannIn->duration = $lastDuration;
                    $scannIn->update();
                    $query = CheckinHistory::create([
                        'checkin_id' => $id,
                        'checkin_time' => $checkintime,
                        'date' => $scannIn->date,
                        'status' => "Present",
                        'checkin_status' => $checkin_status,
                        'checkin_late' => $checkin_late,
                        'edit_date' => $todayDate,
                        'checkout_time' =>  $checkouttime,
                        'checkout_status' => $checkout_status,
                        'checkout_late' => $checkout_late,
                        'duration' => $lastDuration,
                        'note' => $note
                    ]);
                    $respone = [
                        'code' => 0,
                        'message' => "Success"
                        // 'checkin_id'=>$id,
                        // 'checkin_time' => $checkintime,
                        // 'date' => $scannIn->date,
                        // 'status' => "Present",
                        // 'checkin_status' => $checkin_status,
                        // 'checkin_late' => $checkin_late,
                        // 'edit_date' => $todayDate,
                        // 'checkout_time' =>  $checkouttime,
                        // 'checkout_status' => $checkout_status,
                        // 'checkout_late' => $checkout_late,
                        // 'duration' => $lastDuration,
                        // 'note'=>$note,
                        // 'checkin_late'=>$chekinLate,
                        // 'standard_hour'=>$standardHour,
                        // 'chekcout_early'=>$chekoutearly,
                        // 'leave_duration'=>$leaveDuration,
                        // 'checkin_bf'=>$chekinBF,
                        // 'checkout_bf'=>$chekoutBF
                    ];
                }
                // $respone =[
                //     'checkin_id'=>$id,
                //     'checkin_time'=>$request->checkin_time,
                //     'checkout_time'=>$request->checkout_time,
                //     'user_id'=>$request->user_id
                // ];
                return response()->json(
                    $respone,
                    200
                );
            } else {
                return response()->json(
                    [
                        'message' => "No checkin id found",
                        'code' => -1,
                    ],
                    200
                );
            }
        } catch (Exception $e) {
            return response([
                'message' => $e->getMessage()
            ]);
        }
    }
}
