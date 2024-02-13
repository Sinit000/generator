<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Checkin;
use App\Models\Checkout;
use App\Models\Employee;
use App\Models\Location;
use App\Models\Notice;
use App\Models\Store;
use App\Models\Timetable;
use App\Notifications\TelegramRegister;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Support\Facades\Config;
use App\Exceptions\InvalidOrderException;
use App\Models\Department;
use App\Models\Leave;
use App\Models\Leavetype;
use App\Models\Notification;
use App\Models\Overtime;
use App\Models\Payslip;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;


use App\Models\Position;
use Carbon\Carbon;
// use App\Models\TimetableEmployee;
use App\Models\GroupDepartment;
use App\Models\Workday;
use App\Models\User;
use App\Models\Subleavetype;
use App\Models\Category;
use App\Models\Salary;
use App\Models\Contract;
use App\Models\Structure;
use App\Models\Counter;
use App\Models\Changedayoff;
use App\Models\Holiday;
use App\Models\Leaveout;
use App\Models\Overtimecompesation;
use App\Notifications\PushNotification;

// use Illuminate\Support\Str;

class EmployeeController extends Controller
{

    public function index()
    {
        //
    }

    public function register(Request $request)
    {


        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'gender' => 'required',
                'username' => 'required|string|unique:users,username',
                'department_id' => 'required',
                'position_id' => 'required',
                'password' => 'required'
            ]);
            if ($validator->fails()) {
                $error = $validator->errors()->all()[0];
                return response()->json(
                    [
                        'message' => $error,
                        'code' => -1,
                    ],
                    201
                );
            } else {
                $user = User::create([
                    'name' => $request['name'],
                    'gender' => $request['gender'],
                    'username' => $request['username'],
                    'position_id' => $request['position_id'],
                    'department_id' => $request['department_id'],
                    'password' => bcrypt($request['password'])
                ]);
                $token = $user->createToken('mytoken')->plainTextToken;
                $respone = [
                    'message' => 'Success',
                    'code' => 0,
                    'user' => $user,
                    'token' => $token,
                ];
                return response($respone, 200);
            }
        } catch (Exception $e) {
            return response()->json(
                [

                    'message' => $e->getMessage(),

                ],
                500
            );
        }
    }

    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'username' => 'required|string',
                'password' => 'required'
            ]);
            if ($validator->fails()) {
                $error = $validator->errors()->all()[0];
                return response()->json(
                    [
                        'message' => $error,
                        'code' => -1,

                    ],
                    201
                );
            } else {

                $user = User::with('role')->where('username', $request->username)
                    ->whereNotIn('id', [1])->first();
                // check password
                if ($user) {
                    if($user->device_imei){

                        
                        if (Hash::check($request->password, $user->password)) {
                            if($user->device_imei ==$request->device_imei ){
                                $token = $user->createToken('mytoken')->plainTextToken;
                                $respone = [
                                    'message' => 'Success',
                                    'code' => 0,
                                    'user' => $user,
                                    'token' => $token,
                                ];
                            }else{
                                $respone = [
                                    'message' => "Sorry you don't have permission to login this account",
                                    'code' => 0,
                                    
                                ];
                            }
                            // $token = $user->createToken('mytoken')->plainTextToken;
                            
                            return response($respone, 200);
                        } else {
                            return response()->json(
                                [
                                    'message' => "Wrong username and password",
                                    'code' => -1,
                                    
                                ]
                            );
                        }
                    }else{
                        // first time login
                        if (Hash::check($request->password, $user->password)) {
                            $token = $user->createToken('mytoken')->plainTextToken;
                            $respone = [
                                'message' => 'Success',
                                'code' => 0,
                                'user' => $user,
                                'token' => $token,
                            ];
                            return response($respone, 200);
                        } else {
                            return response()->json(
                                [
                                    'message' => "Wrong username and password",
                                    'code' => -1,
                                    // 'data'=>[]
                                ]
                            );
                        }
                    }
                    
                } else {
                    return response()->json(
                        [
                            'message' => "Username does not exist",
                            'code' => -1,
                            // 'data'=>[]
                        ]
                    );
                }
            }
        } catch (Exception $e) {
            return response()->json(
                [
                    'message' => $e->getMessage()
                    // 'data'=>[]
                ],
                500
            );
        }
    }
    public function changepassword(Request $request)
    {
        try {
            $use_id = $request->user()->id;
            $validator = Validator::make($request->all(), [
                'old_password' => 'required',
                'new_password' => 'required'
            ]);
            if ($validator->fails()) {
                $error = $validator->errors()->all()[0];
                return response()->json(
                    [
                        'message' => $error,
                        'code' => -1,
                        // 'data'=>[]
                    ],
                    201
                );
            } else {

                $user = User::find($use_id);
                // check password
                if ($user) {
                    // check the same or now
                    if (Hash::check($request->old_password, $user->password)) {
                        $user->update([
                            'password' => Hash::make($request->new_password)
                        ]);
                        $token = $user->createToken('mytoken')->plainTextToken;
                        $respone = [
                            'message' => 'Success',
                            'code' => 0,
                            'token' => $token

                        ];
                        // return response($respone ,200);
                    } else {
                        $respone = [
                            'message' => "Incorrect old password",
                            'code' => -1,
                        ];
                    }
                } else {
                    $respone = [
                        'message' => "Username does not exist",
                        'code' => -1,
                    ];
                }
                return response()->json(
                    $respone,
                    200
                );
            }
        } catch (Exception $e) {
            return response()->json(
                [
                    'message' => $e->getMessage()
                    // 'data'=>[]
                ],
                500
            );
        }
    }
    public function checkProfile(Request $request, $date)
    {
        try {
            //code...
            // get user by token with timetable
            $use_id = $request->user()->id;
            $typedate = Workday::first();
            $todayDate = "";
            // $checkinRecord = Checkin::where('user_id', $use_id)
            // ->whereNull('checkout_time')
            // ->latest()->first();

            // $todayDate = Carbon::now()->format('m/d/Y');

            $records = User::find($use_id);
            if ($records) {

                if ($typedate->type_date_time == "server") {
                    $todayDate = Carbon::now()->format('m/d/Y');
                    $checkinRecord = Checkin::where('user_id', $records->id)
                        ->where('date', '=', $todayDate)
                        ->first();
                } else {
                    // $todayDate= $request['date'];
                    // check leave condition
                    $d = date('m/d/Y', strtotime($date));
                    // $leave =Leave::where('user_id',$records->id)->latest()->first();
                    $checkinRecord = Checkin::where('user_id', $records->id)
                        // ->where('checkout_time','=','0')
                        ->where('date', '=', $d)

                        // ->whereNull('checkout_time')
                        ->latest()->first();
                    // ->where('date', '=', $todayDate)


                }
                $checkinStatus = "false";
                if ($checkinRecord) {
                    // $checkinStatus="true";
                    if ($checkinRecord->checkout_status) {
                        $checkinStatus = "present";
                    } else {
                        // if have only checkin_status
                        $checkinStatus = "true";
                    }
                    if ($checkinRecord->status == "absent") {
                        $checkinStatus = "absent";
                    }
                    if ($checkinRecord->status == "leave") {
                        $checkinStatus = "leave";
                    }
                }
                $records->checkin_status = $checkinStatus;
                if ($checkinRecord) {
                    $records->checkin_id = $checkinRecord['id'];
                } else {
                    $records->checkin_id = null;
                }
                $records->checkin = $checkinRecord;
            }

            $respone = [
                'message' => 'Success',
                'code' => 0,
                'user' =>  $records
                // 'checkin'=>$checkin,

            ];
            return response(
                $respone,
                200
            );
        } catch (Exception $e) {
            //throw $th;
            return response([
                'message' => $e->getMessage()
            ]);
        }
    }
    public function getprofile(Request $request)
    {
        try {
            //code...
            // get user by token with timetable
            $use_id = $request->user()->id;
            $typedate = Workday::first();
            $todayDate = "";



            $records = User::with('timetable', 'department', 'position', 'workday', 'role')->find($use_id);
            if ($records) {

                if ($typedate->type_date_time == "server") {
                    $todayDate = Carbon::now()->format('m/d/Y');
                    $checkinRecord = Checkin::where('user_id', $records->id)
                        ->where('date', '=', $todayDate)
                        ->first();
                } else {
                    // $todayDate= $request['date'];
                    $checkinRecord = Checkin::where('user_id', $records->id)
                        ->whereNull('checkout_time')
                        ->latest()->first();
                    // ->where('date', '=', $todayDate)


                }
                $checkinStatus = "false";
                if ($checkinRecord) {
                    // $checkinStatus="true";
                    if ($checkinRecord->checkout_status) {
                        $checkinStatus = "present";
                    } else {
                        // if have only checkin_status
                        $checkinStatus = "true";
                    }
                    if ($checkinRecord->status == "absent") {
                        $checkinStatus = "absent";
                    }
                    if ($checkinRecord->status == "leave") {
                        $checkinStatus = "leave";
                    }
                }
                $records->checkin_status = $checkinStatus;
                if ($checkinRecord) {
                    $records->checkin_id = $checkinRecord['id'];
                } else {
                    $records->checkin_id = null;
                }
                $records->checkin = $checkinRecord;
                // workday
                // $work="";
                // $dayoff = Workday::find($records->workday_id);
                // // $countd = Workday::find($record->workday_id)->count();
                // // check workday 
                // if( $dayoff){
                //     $workday = explode(',', $dayoff->off_day);
                //     // work day
                //     $check = "true";
                //     $notCheck = $this->getWeekday($todayDate);
                //     // 1 = count($dayoff)
                //     for ($i = 0; $i <  count( $workday); ++$i) {
                //         //   if offday = today check will false
                //         if ($workday[$i] == $notCheck) {
                //             $check = "false";
                //         }

                //     }
                //     if ($check == "false") {
                //         // day off cannot check
                //         $work="false";
                //     }else{
                //         $work="true";
                //     }
                // }
                // $records->workday= $work;
            }
            // if($records){
            //     $checkinStatus = "false";
            //     $checkinRecord = Leave::where('user_id',$records->id)
            //     ->where('date','=',$todayDate)
            //     ->first();
            //     if($checkinRecord)
            //     {
            //         $checkinStatus="true";
            //     }
            //     $records->leave_status=$checkinStatus;
            //     $records->leave = $checkinRecord;
            // }
            // if ($records) {
            //     // $checkinStatus = false;
            //     $checkinStatus = "0";

            //     $checkinRecord = TimetableEmployee::where('user_id', $records->id)->count();

            //     if ($checkinRecord) {
            //         if ($checkinRecord == 1) {
            //             $checkinStatus = "1";
            //         } else {
            //             $checkinStatus = "2";
            //         }
            //     }
            //     $records->em_type = $checkinStatus;
            //     // $record->leave = $checkinRecord;
            // }

            $respone = [
                'message' => 'Success',
                'code' => 0,
                'user' =>  $records
                // 'checkin'=>$checkin,

            ];
            return response(
                $respone,
                200
            );
        } catch (Exception $e) {
            //throw $th;
            return response([
                'message' => $e->getMessage()
            ]);
        }
    }
    public function userlocation(Request $request)
    {
        $storelat = "11.5539968";
        $storelon = "104.906752";

        $userlat = $request["lat"];
        $userlot = $request["lon"];
        $timeNow = Carbon::now()->format('H:i:s');
        $data = $this->distance($storelat, $storelon, $userlat, $userlot, "K");
        // $data= $this-> getDistanceBetweenPointsNew( $userlat,$userlot,$storelat,$storelon, 'kilometers' );
        return response()->json(
            [
                // 'status'=>'false',
                'message' => "Success",
                'data' => $data,
                'time' => $timeNow
            ],
            200
        );
    }
    function getDistanceBetweenPointsNew($latitude1, $longitude1, $latitude2, $longitude2, $unit)
    {
        $latitude1 = (float)$latitude1;
        $latitude2 = (float) $latitude2;
        $longitude1 = (float)$longitude1;
        $longitude2 = (float)$longitude2;
        $theta = $latitude1  - $latitude2;
        $distance = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))) + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta)));
        $distance = acos($distance);
        $distance = rad2deg($distance);
        $distance = $distance * 60 * 1.1515;
        switch ($unit) {
            case 'miles':
                break;
            case 'kilometers':
                $distance = $distance * 1.609344;
        }
        return (round($distance, 2));
    }
    public function testlocation(Request $request)
    {
        $data = Location::find(2);

        $location = $this->distance($data->lat, $data->lon, $request['lat'], $request['lon'], "K");
        $result = "";
        if ($data->lon > $request['lon']) {
            $result = $data->lon - $request['lon'];
        } else {
            $result = $request['lon'] - $data->lon;
        }
        $respone = [
            'message' => ".$request->lat. $request->lon",
            'code' => -1,
            'distance' => $location
        ];

        return response()->json(
            [
                // 'code'=>$code,
                'workday' => $result,
                'location' => $location,

            ]
        );
    }
    protected function distance($lat1, $lon1, $lat2, $lon2, $unit)
    {


        // $theta = $lon1 - $lon2;
        $lat1 = (float)$lat1;
        $lat2 = (float) $lat2;
        $lon1 = (float) $lon1;
        $lon2 = (float) $lon2;
        $theta = "";
        if ($lon1 > $lon2) {
            $theta = $lon1  - $lon2;
        } else {
            $theta = $lon2  - $lon1;
        }
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }
    public function checkin(Request $request)
    {
        try {
            $status = ["on time", "very good", "late", "too late"];
            $typedate = Workday::first();
            $timeNow = Carbon::now()->format('H:i:s');
            $checkinTimeServer = "";

            $todayDate = "";
            $overtime = "";
            if ($typedate->type_date_time == "server") {
                $todayDate = Carbon::now()->format('m/d/Y');
                $checkinTimeServer = $timeNow;
            } else {
                $todayDate = $request['date'];

                $checkinTimeServer = $request['checkin_time'];
            }



            $use_id = $request->user()->id;


            $employee = User::find($use_id);

            $targetStatus = "";
            $validator = Validator::make($request->all(), [
                'checkin_time' => 'required|string',
                'lat' => 'required',
                'lon' => "required",
            ]);
            if ($validator->fails()) {
                $error = $validator->errors()->all()[0];
                return response()->json(
                    [
                        'message' => $error,
                        'code' => -1,
                    ],
                    201
                );
            } else {
                // checkin employee id
                if ($employee) {
                    $overtime = Overtime::where('user_id', '=', $employee->id)
                    ->where('status','=','approved')
                            ->where('from_date', '=', $todayDate)
                            
                            ->orWhere('to_date', '=', $todayDate)->latest()->first();
                   
                    $position = Position::find($employee->position_id);
                    $scann = Checkin::where('user_id', '=', $employee->id)->latest()->first();
                    $i = "";
                    $store = Department::find($employee->department_id);
                    $data = Location::find($request['qr_id']);
                    if ($request['qr_id'] != $store->location_id) {
                        $respone = [
                            'message' => 'Wrong qr location',
                            'code' => -1,
                            'qr_send'=>$request['qr_id'],
                            'department'=>$store->location_id
                           
                        ];
                    } else {
                        // $latRequest = $request['lat'];
                        if($request['lat']==Null){
                           $respone = [
                                    'message' => "Please scan again",
                                    'code' => -2,
    
                                ];
                        }else{
                           if($request['lat'] && $request['lon'])
                            {
                                $location = $this->distance($data->lat, $data->lon, $request['lat'], $request['lon'], "K");
                       
                       
                            // out put 7km
                                if (
                                    $location >= 0.3
                                    // $location['distance']>=0.2
                                ) {
                                    // $respone = [
                                    //     'message' => '.$request['lat']."+". $request['lon']',
                                    //     'code' => -1,
                                    //     'distance' => $location
                                    // ];
                                    $respone = [
                                        'message' => "location are not allow",
                                        'code' => -1,
                                        'distance' => $location
                                    ];
                                } else {
                                    
                                   
                                    $otStatus = "false";
                                    $findtime = Timetable::find($employee->timetable_id);
                                    $userCheckin = Carbon::parse($checkinTimeServer);
                                    $userDuty = Carbon::parse($findtime->on_duty_time);
                                    $diff = $userCheckin->diff($userDuty);
                                    $hour = ($diff->h) * 60;
                                    $minute = $diff->i;
                                    $code = "";
                                    $min = "";
                                    $check = "";
                                    $lateMinuteAdmin = $findtime['late_minute'];
                                    $duration = 0;
                                    $half_day_leave=false;
                                    $dateCode =0;
        
                                    if ($scann  ) {
                                        // 
                                        if($scann->checkin_time == NULL){
                                            // request leave half day noon
                                            $half_day_leave =true;
                                        }
                                        if($half_day_leave==true){
                                            $code =0;
                                        }else{
                                            $code =0;
                                            // check today date
                                            if ($scann->date != $todayDate){
                                                $dateCode =0;
                                            }else{
                                                $dateCode =-1;
                                            }
                                            
                                        }
                                        if($code ==0 && $dateCode ==0){
                                            $workday = Workday::find($employee->workday_id);
                                            $countOffDay = explode(",", $workday->off_day);
        
                                            $contract = Contract::where('user_id', '=', $employee->id)->first();
                                            if ($contract) {
                                                $standardHour = ($contract->working_schedule) / (7 - count($countOffDay));
                                            } else {
                                                $standardHour = 9;
                                            }
                                            $duration = $standardHour / 2;
        
                                            // $schedule = TimetableEmployee::where('user_id', $employee->id)->first();
                                            // $findtime = Timetable::find($schedule->timetable_id);
                                            if ($findtime['late_minute'] == "0") {
        
                                                if ($findtime['on_duty_time'] == $checkinTimeServer) {
                                                    $targetStatus = $status[0];
                                                    $min = "0";
                                                    $duration = $duration;
                                                    $duration = round($duration, 2);
                                                    $code = 0;
                                                }   // 07:30 // 6
                                                elseif ($findtime['on_duty_time'] > $checkinTimeServer) {
                                                    $targetStatus = $status[1];
        
                                                    $min = $minute + $hour;
                                                    $duration = ($duration * 60 + $min) / 60;
                                                    $duration = round($duration, 2);
                                                    $code = 0;
                                                }
                                                // 08:00,8:30
                                                elseif ($findtime['on_duty_time'] < $checkinTimeServer) {
                                                    $targetStatus = $status[2];
                                                    $min = $minute + $hour;
        
                                                    $duration = ($duration * 60 - $min) / 60;
                                                    $duration = round($duration, 2);
                                                    $code = 0;
                                                }
        
                                                if ($typedate->type_date_time == "server") {
                                                    if($half_day_leave==true){
                                                        // $s
                                                        $scann->checkin_time = $timeNow;
                                                        $scann->checkin_status = $targetStatus;
                                                        $scann->checkin_late = $min;
                                                        $scann->duration = $duration;
                                                        $scann->update();
                                                        
                                                    }else{
                                                        $checkin = Checkin::create([
                                                        'checkin_time' => $timeNow,
                                                        'date' => $todayDate,
                                                        'status' => "checkin",
                                                        'checkin_status' => $targetStatus,
                                                        'checkin_late' => $min,
                                                        'send_status' => 'false',
                                                        'confirm' => 'false',
                                                        'ot_status' =>  $otStatus,
                                                        'user_id' => $employee->id,
                                                        'duration' => $duration,
        
        
                                                    ]);
                                                    }
                                                    
                                                } else {
                                                    if($half_day_leave==true){
                                                        // $s
                                                        $scann->checkin_time = $checkinTimeServer;
                                                        $scann->checkin_status = $targetStatus;
                                                        $scann->checkin_late = $min;
                                                        $scann->duration = $duration;
                                                        $scann->update();
                                                        
                                                    }else{
                                                        $checkin = Checkin::create([
                                                            'checkin_time' => $checkinTimeServer,
                                                            'date' => $todayDate,
                                                            'status' => "checkin",
                                                            'checkin_status' => $targetStatus,
                                                            'checkin_late' => $min,
                                                            'user_id' => $employee->id,
                                                            'send_status' => 'false',
                                                            'confirm' => 'false',
                                                            'duration' => $duration,
                                                            'ot_status' =>  $otStatus,
                                                            'created_at' => $request['created_at'],
                                                            'updated_at' => $request['created_at'],
        
                                                        ]);
                                                    }
                                                    
                                                }
        
                                                $respone = [
                                                    'message' => 'Success',
                                                    'code' => 0,
                                                ];
                                                $notification = new Notice(
                                                    [
                                                        'notice' => "Checkin",
                                                        'noticedes' => "Employee name : {$employee->name}" . "\n" . "Position : {$position->position_name}" . "\n" . "Checkin time : " . $checkinTimeServer . "\n" . "Date : " . $todayDate . "\n" . "Checkin status :" . $targetStatus . "\n" . "Time :" . $min . "\n",
        
                                                        'telegramid' => Config::get('services.telegram_id')
                                                    ]
                                                );
        
                                                //  // $notification->save();
                                                $notification->notify(new TelegramRegister());
                                                // check overtim
                                            } else {
        
                                                //  if admin set late minute
                                                if ($findtime['on_duty_time'] == $checkinTimeServer) {
                                                    $targetStatus = $status[0];
                                                    $min = 0;
                                                    // ($minute + $hour) ;
                                                    // - $lateMinuteAdmin;
                                                    $duration = $duration;
                                                    $duration = round($duration, 2);
                                                    $code = 0;
                                                }   // 07:30 // 6
                                                elseif ($findtime['on_duty_time'] > $checkinTimeServer) {
                                                    $targetStatus = $status[1];
                                                    $min = ($minute + $hour) - $lateMinuteAdmin;
        
                                                    $duration = ($duration * 60 + $min) / 60;
                                                    $duration = round($duration, 2);
                                                    $code = 0;
                                                }
                                                // 08:00,8:30
                                                elseif ($findtime['on_duty_time'] < $checkinTimeServer) {
                                                    $targetStatus = $status[2];
                                                    $min = ($minute + $hour) - $lateMinuteAdmin;
        
                                                    $duration = ($duration * 60 - $min) / 60;
                                                    $duration = round($duration, 2);
                                                    $code = 0;
                                                }
                                                if ($typedate->type_date_time == "server") {
                                                    $checkin = Checkin::create([
                                                        'checkin_time' => $timeNow,
                                                        'date' => $todayDate,
                                                        'status' => "checkin",
                                                        'checkin_status' => $targetStatus,
                                                        'checkin_late' => $min,
                                                        'send_status' => 'false',
                                                        'confirm' => 'false',
                                                        'ot_status' =>  $otStatus,
                                                        'user_id' => $employee->id,
                                                        'duration' => $duration,
                                                    ]);
                                                } else {
                                                    $checkin = Checkin::create([
                                                        'checkin_time' => $checkinTimeServer,
                                                        'date' => $todayDate,
                                                        'status' => "checkin",
                                                        'checkin_status' => $targetStatus,
                                                        'checkin_late' => $min,
                                                        'user_id' => $employee->id,
                                                        'send_status' => 'false',
                                                        'confirm' => 'false',
                                                        'ot_status' =>  $otStatus,
                                                        'duration' => $duration,
                                                        'created_at' => $request['created_at'],
                                                        'updated_at' => $request['created_at'],
        
        
                                                    ]);
                                                }
        
        
                                                $respone = [
                                                    'message' => 'Success',
                                                    'code' => 0,
        
                                                ];
                                                $notification = new Notice(
                                                    [
                                                        'notice' => "Checkin",
                                                        'noticedes' => "Employee name : {$employee->name}" . "\n" . "Position : {$position->position_name}" . "\n" . "Checkin time : " . $checkinTimeServer . "\n" . "Date : " . $todayDate . "\n" . "Checkin status :" . $targetStatus . "\n" . "Time :" . $min . "\n",
        
                                                        'telegramid' => Config::get('services.telegram_id')
                                                    ]
                                                );
        
                                                // $notification->save();
                                                $notification->notify(new TelegramRegister());
                                            }
                                            // if overtime
                                            $dayOff="";
                                            if ($overtime) {
                                                $work = Workday::find($employee->workday_id);
                                                if ($work->off_day != Null) {
                                                    $OffDay = explode(',', $work->off_day);
                                                    $check = "true";
                                                    // $notCheck= new GetWeekday();
                                                    // $notCheck->getday();
                                                    $notCheck = $this->getWeekday($todayDate);
                                                    // 1 = count($dayoff)
                                                    for ($y = 0; $y <  count($OffDay); $y++) {
                                                        //   if offday = today check will false
                                                        if ($OffDay[$y] == $notCheck) {
                                                            $check = "false";
                                                        }
                                                    }
                                                    if ($check == "false") {
                                                        // day off cannot check
                                                        $dayOff = "false";
                                                    } else {
                                                        $dayOff = "true";
                                                    }
                                                }
                                                if ($work->off_day == Null) {
                                                    $dayOff = "true";
                                                }
                                                $k="";
                                                // come to work
                                                if ($dayOff == "true") {
                                                    // work 8 hours , and cheif request 2 hour
                                                    $k="001";
                                                } else {
        
                                                    if ($overtime->pay_type == "holiday") {
                                                        $overtime->status = "completed";
                                                        $overtime->pay_status = "completed";
        
                                                        $counter = Counter::where('user_id', '=', $employee->id)->first();
                                                        if ($overtime->type == "hour") {
                                                            $counter->ot_temp  = $duration;
                                                        } else {
                                                            // complete when checkout
                                                            $counter->ot_temp  =  $duration;
                                                            // $counter->ot_duration  =$counter->ot_duration + $overtime->number * 9;
                                                            // $counter->update();
                                                        }
                                                        $counter->update();
                                                        $overtime->update();
                                                    } else {
        
                                                    //     $k="01";
                                                    //     // if overtime pay_type="cash", complete when checkou
                                                        $counter = Counter::where('user_id', '=', $employee->id)->first();
                                                        if ($overtime->type == "hour") {
                                                            $k="1";
                                                            // $overtime->status = "completed";
                                                            $counter->ot_temp  = $duration;
                                                        } else {
                                                            $k="2";
                                                            // complete when checkout
                                                            $counter->ot_temp  =  $duration;
                                                            // $counter->ot_duration  =$counter->ot_duration + $overtime->number * 9;
                                                            // $counter->update();
                                                        }
                                                        $counter->update();
                                                        $overtime->update();
                                                        
                                                    }
                                                }
                                
                                            }
                                            // 
                                        }else{
                                            $respone = [
                                                'message' => 'cannot checkin today',
                                                'code' => -1,
                                                'checkindate' => $todayDate,
                                                'lastcheckin' => $scann->date,
                                                
                                            ];
                                        }
                                    } else {
                                        
                                        // first scann for employee type 1 :have timetable 1
                                        $workday = Workday::find($employee->workday_id);
                                        $countOffDay = explode(",", $workday->off_day);
    
                                        $contract = Contract::where('user_id', '=', $employee->id)->first();
                                        if ($contract) {
                                            $standardHour = ($contract->working_schedule) / (7 - count($countOffDay));
                                        } else {
                                            $standardHour = 9;
                                        }
                                        $duration = $standardHour / 2;
    
                                        // $schedule = TimetableEmployee::where('user_id', $employee->id)->first();
                                        // $findtime = Timetable::find($schedule->timetable_id);
                                        if ($findtime['late_minute'] == "0") {
    
                                            if ($findtime['on_duty_time'] == $checkinTimeServer) {
                                                $targetStatus = $status[0];
                                                $min = "0";
                                                $duration = $duration;
                                                $duration = round($duration, 2);
                                                $code = 0;
                                            }   // 07:30 // 6
                                            elseif ($findtime['on_duty_time'] > $checkinTimeServer) {
                                                $targetStatus = $status[1];
    
                                                $min = $minute + $hour;
                                                $duration = ($duration * 60 + $min) / 60;
                                                $duration = round($duration, 2);
                                                $code = 0;
                                            }
                                            // 08:00,8:30
                                            elseif ($findtime['on_duty_time'] < $checkinTimeServer) {
                                                $targetStatus = $status[2];
                                                $min = $minute + $hour;
    
                                                $duration = ($duration * 60 - $min) / 60;
                                                $duration = round($duration, 2);
                                                $code = 0;
                                            }
    
                                            if ($typedate->type_date_time == "server") {
                                                if($half_day_leave==true){
                                                    // $s
                                                    $scann->checkin_time = $timeNow;
                                                    $scann->checkin_status = $targetStatus;
                                                    $scann->checkin_late = $min;
                                                    $scann->duration = $duration;
                                                    $scann->update();
                                                    
                                                }else{
                                                    $checkin = Checkin::create([
                                                    'checkin_time' => $timeNow,
                                                    'date' => $todayDate,
                                                    'status' => "checkin",
                                                    'checkin_status' => $targetStatus,
                                                    'checkin_late' => $min,
                                                    'send_status' => 'false',
                                                    'confirm' => 'false',
                                                    'ot_status' =>  $otStatus,
                                                    'user_id' => $employee->id,
                                                    'duration' => $duration,
    
    
                                                ]);
                                                }
                                                
                                            } else 
                                            {
                                                if($half_day_leave==true){
                                                    // $s
                                                    $scann->checkin_time = $checkinTimeServer;
                                                    $scann->checkin_status = $targetStatus;
                                                    $scann->checkin_late = $min;
                                                    $scann->duration = $duration;
                                                    $scann->update();
                                                    
                                                }else{
                                                    $checkin = Checkin::create([
                                                        'checkin_time' => $checkinTimeServer,
                                                        'date' => $todayDate,
                                                        'status' => "checkin",
                                                        'checkin_status' => $targetStatus,
                                                        'checkin_late' => $min,
                                                        'user_id' => $employee->id,
                                                        'send_status' => 'false',
                                                        'confirm' => 'false',
                                                        'duration' => $duration,
                                                        'ot_status' =>  $otStatus,
                                                        'created_at' => $request['created_at'],
                                                        'updated_at' => $request['created_at'],
    
                                                    ]);
                                                }
                                                
                                            }
    
                                            $respone = [
                                                'message' => 'Success',
                                                'code' => 0,
                                            ];
                                            $notification = new Notice(
                                                [
                                                    'notice' => "Checkin",
                                                    'noticedes' => "Employee name : {$employee->name}" . "\n" . "Position : {$position->position_name}" . "\n" . "Checkin time : " . $checkinTimeServer . "\n" . "Date : " . $todayDate . "\n" . "Checkin status :" . $targetStatus . "\n" . "Time :" . $min . "\n",
    
                                                    'telegramid' => Config::get('services.telegram_id')
                                                ]
                                            );
    
                                            //  // $notification->save();
                                            $notification->notify(new TelegramRegister());
                                            // check overtim
                                        } else {
    
                                            //  if admin set late minute
                                            if ($findtime['on_duty_time'] == $checkinTimeServer) {
                                                $targetStatus = $status[0];
                                                $min = 0;
                                                // ($minute + $hour) ;
                                                // - $lateMinuteAdmin;
                                                $duration = $duration;
                                                $duration = round($duration, 2);
                                                $code = 0;
                                            }   // 07:30 // 6
                                            elseif ($findtime['on_duty_time'] > $checkinTimeServer) {
                                                $targetStatus = $status[1];
                                                $min = ($minute + $hour) - $lateMinuteAdmin;
    
                                                $duration = ($duration * 60 + $min) / 60;
                                                $duration = round($duration, 2);
                                                $code = 0;
                                            }
                                            // 08:00,8:30
                                            elseif ($findtime['on_duty_time'] < $checkinTimeServer) {
                                                $targetStatus = $status[2];
                                                $min = ($minute + $hour) - $lateMinuteAdmin;
    
                                                $duration = ($duration * 60 - $min) / 60;
                                                $duration = round($duration, 2);
                                                $code = 0;
                                            }
                                            if ($typedate->type_date_time == "server") {
                                                $checkin = Checkin::create([
                                                    'checkin_time' => $timeNow,
                                                    'date' => $todayDate,
                                                    'status' => "checkin",
                                                    'checkin_status' => $targetStatus,
                                                    'checkin_late' => $min,
                                                    'send_status' => 'false',
                                                    'confirm' => 'false',
                                                    'ot_status' =>  $otStatus,
                                                    'user_id' => $employee->id,
                                                    'duration' => $duration,
                                                ]);
                                            } else {
                                                $checkin = Checkin::create([
                                                    'checkin_time' => $checkinTimeServer,
                                                    'date' => $todayDate,
                                                    'status' => "checkin",
                                                    'checkin_status' => $targetStatus,
                                                    'checkin_late' => $min,
                                                    'user_id' => $employee->id,
                                                    'send_status' => 'false',
                                                    'confirm' => 'false',
                                                    'ot_status' =>  $otStatus,
                                                    'duration' => $duration,
                                                    'created_at' => $request['created_at'],
                                                    'updated_at' => $request['created_at'],
    
    
                                                ]);
                                            }
    
    
                                            $respone = [
                                                'message' => 'Success',
                                                'code' => 0,
    
                                            ];
                                            $notification = new Notice(
                                                [
                                                    'notice' => "Checkin",
                                                    'noticedes' => "Employee name : {$employee->name}" . "\n" . "Position : {$position->position_name}" . "\n" . "Checkin time : " . $checkinTimeServer . "\n" . "Date : " . $todayDate . "\n" . "Checkin status :" . $targetStatus . "\n" . "Time :" . $min . "\n",
    
                                                    'telegramid' => Config::get('services.telegram_id')
                                                ]
                                            );
    
                                            // $notification->save();
                                            $notification->notify(new TelegramRegister());
                                        }
                                        // if overtime
                                        $dayOff="";
                                        if ($overtime) {
                                            $work = Workday::find($employee->workday_id);
                                            if ($work->off_day != Null) {
                                                $OffDay = explode(',', $work->off_day);
                                                $check = "true";
                                                // $notCheck= new GetWeekday();
                                                // $notCheck->getday();
                                                $notCheck = $this->getWeekday($todayDate);
                                                // 1 = count($dayoff)
                                                for ($y = 0; $y <  count($OffDay); $y++) {
                                                    //   if offday = today check will false
                                                    if ($OffDay[$y] == $notCheck) {
                                                        $check = "false";
                                                    }
                                                }
                                                if ($check == "false") {
                                                    // day off cannot check
                                                    $dayOff = "false";
                                                } else {
                                                    $dayOff = "true";
                                                }
                                            }
                                            if ($work->off_day == Null) {
                                                $dayOff = "true";
                                            }
                                            $k="";
                                            // come to work
                                            if ($dayOff == "true") {
                                                // work 8 hours , and cheif request 2 hour
                                                $k="001";
                                            } else {
    
                                                if ($overtime->pay_type == "holiday") {
                                                    $overtime->status = "completed";
                                                    $overtime->pay_status = "completed";
    
                                                    $counter = Counter::where('user_id', '=', $employee->id)->first();
                                                    if ($overtime->type == "hour") {
                                                        $counter->ot_temp  = $duration;
                                                    } else {
                                                        // complete when checkout
                                                        $counter->ot_temp  =  $duration;
                                                        // $counter->ot_duration  =$counter->ot_duration + $overtime->number * 9;
                                                        // $counter->update();
                                                    }
                                                    $counter->update();
                                                    $overtime->update();
                                                } else {
    
                                                //     $k="01";
                                                //     // if overtime pay_type="cash", complete when checkou
                                                    $counter = Counter::where('user_id', '=', $employee->id)->first();
                                                    if ($overtime->type == "hour") {
                                                        $k="1";
                                                        // $overtime->status = "completed";
                                                        $counter->ot_temp  = $duration;
                                                    } else {
                                                        $k="2";
                                                        // complete when checkout
                                                        $counter->ot_temp  =  $duration;
                                                        // $counter->ot_duration  =$counter->ot_duration + $overtime->number * 9;
                                                        // $counter->update();
                                                    }
                                                    $counter->update();
                                                    $overtime->update();
                                                    
                                                }
                                            }
                            
                                        }
                                            $dayOff="";
                                            if ($overtime) {
                                                $work = Workday::find($employee->workday_id);
                                                if ($work->off_day != Null) {
                                                    $OffDay = explode(',', $work->off_day);
                                                    $check = "true";
                                                    // $notCheck= new GetWeekday();
                                                    // $notCheck->getday();
                                                    $notCheck = $this->getWeekday($todayDate);
                                                    // 1 = count($dayoff)
                                                    for ($y = 0; $y <  count($OffDay); $y++) {
                                                        //   if offday = today check will false
                                                        if ($OffDay[$y] == $notCheck) {
                                                            $check = "false";
                                                        }
                                                    }
                                                    if ($check == "false") {
                                                        // day off cannot check
                                                        $dayOff = "false";
                                                    } else {
                                                        $dayOff = "true";
                                                    }
                                                }
                                                if ($work->off_day == Null) {
                                                    $dayOff = "true";
                                                }
                                                $k="";
                                                // come to work
                                                if ($dayOff == "true") {
                                                    // work 8 hours , and cheif request 2 hour
                                                    $k="001";
                                                } else {
        
                                                    if ($overtime->pay_type == "holiday") {
                                                        $overtime->status = "completed";
                                                        $overtime->pay_status = "completed";
        
                                                        $counter = Counter::where('user_id', '=', $employee->id)->first();
                                                        if ($overtime->type == "hour") {
                                                            $counter->ot_temp  = $duration;
                                                        } else {
                                                            // complete when checkout
                                                            $counter->ot_temp  =  $duration;
                                                            // $counter->ot_duration  =$counter->ot_duration + $overtime->number * 9;
                                                            // $counter->update();
                                                        }
                                                        $counter->update();
                                                        $overtime->update();
                                                    } else {
        
                                                    //     $k="01";
                                                    //     // if overtime pay_type="cash", complete when checkou
                                                        $counter = Counter::where('user_id', '=', $employee->id)->first();
                                                        if ($overtime->type == "hour") {
                                                            $k="1";
                                                            // $overtime->status = "completed";
                                                            $counter->ot_temp  = $duration;
                                                        } else {
                                                            $k="2";
                                                            // complete when checkout
                                                            $counter->ot_temp  =  $duration;
                                                            // $counter->ot_duration  =$counter->ot_duration + $overtime->number * 9;
                                                            // $counter->update();
                                                        }
                                                        $counter->update();
                                                        $overtime->update();
                                                        
                                                    }
                                                }
                                
                                            }
                                    }
                                }
                            }
                            else{
                                $respone = [
                                    'message' => "Please scan again",
                                    'code' => -2,
    
                                ];
                            }
                        }
                       
                        
                        
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
            }
        } catch (Exception $e) {
            return response([
                'message' => $e->getMessage()
            ]);
        }
    }
    function getWeekday($date)
    {
        return date('w', strtotime($date));
    }


    public function usercheckout(Request $request, $id)
    {
        try {
            $status = ["good", "very good", "early", "too early"];
            $typedate = Workday::first();
            $todayDate = "";
            $timeNow = Carbon::now()->format('H:i:s');
            // $serverTime = $request['checkout_time'];
            $checkoutTimeServer = "";
            if ($typedate->type_date_time == "server") {
                $todayDate = Carbon::now()->format('m/d/Y');
                $checkoutTimeServer = $timeNow;
            } else {
                $todayDate = $request['date'];
                $checkoutTimeServer = $request['checkout_time'];
            }

            $targetStatus = "";
            $use_id = $request->user()->id;
            $employee = User::find($use_id);
            // $findCheckid = Checkin::find($use_id);

            // $timeNow = Carbon::now()->format('H:i:s');
            $validator = Validator::make($request->all(), [
                // 'checkin_id' => 'required|string',
                'checkout_time' => 'required|string',
                'lat' => 'required',
                'lon' => "required",
            ]);
            if ($validator->fails()) {
                $error = $validator->errors()->all()[0];
                return response()->json(
                    [
                        'message' => $error,
                        'code' => -1,
                    ],
                    201
                );
            } else {
                // checkin employee id
                

                if ($employee) {
                     $overtime = Overtime::where('user_id', '=', $employee->id)
                    ->where('status','=','approved')
                            ->where('from_date', '=', $todayDate)
                            
                            ->orWhere('to_date', '=', $todayDate)->latest()->first();
                    $position = Position::find($employee->position_id);
                    // retain condition user can checkout two time in oneday
                    // $id =checkin id, if have check in id , it means already checkin
                    $scann = Checkin::where('id', '=', $id)->where('user_id', '=', $employee->id)
                        ->whereNull('checkout_time')
                        ->latest()->first();


                    $store = Department::find($employee->department_id);


                    $data = Location::find($request['qr_id']);
                    if ($data->id != $store->location_id) {
                        $respone = [
                            'message' => 'Wrong qr location',
                            'code' => -1,
                            'qr_send'=>$request['qr_id'],
                            'department'=>$store->location_id
                           
                        ];
                    } else {
                        if($request['lat']==Null){
                           $respone = [
                                    'message' => "Please scan again",
                                    'code' => -2,
    
                                ];
                        }else{
                            if($request['lat'] && $request['lat']){
                                 // $location = $this->distance($data->lat, $data->lon, $request['lat'], $request['lon'], "K");
                             $location = $this->distance($data->lat, $data->lon, $request['lat'], $request['lon'], "K");

                                // out put 7km
                                if ($location >= 0.3) {
                                   
                                   
                                    $respone = [
                                        'message' => "location is not allow",
                                        'code' => -1,
                                        'distance' => $location
                                    ];
                                } 
                                else {
                                    
                                    $findtime = Timetable::find($employee->timetable_id);
                                    $code = 2;
                                    $total_time_checkin =0;
                                    
                                    if ($typedate->type_date_time == "server") {
                                                    // if checkin time is past and check howlong for pass
                                        $result1 = Carbon::createFromFormat('H:i:s',$findtime->on_duty_time)->isPast();
                                        if( $result1==true){
                                            $userCheckout = Carbon::parse($checkoutTimeServer);
                                            $userOnDutyTime = Carbon::parse($findtime->on_duty_time);
                                            $diff = $userCheckout->diff($userOnDutyTime);
                                            $h_chekcin = ($diff->h)*60 ;
                                            $minute = $diff->i;
                                            
                                            $total_time_checkin =($h_chekcin + $minute)/60;
                                            if($total_time_checkin >=3){
                                                $code = 0;
                                            }else{
                                                $code = -1;
                                            }
                                        }
                                                    
                                    }else{
                                        $code = 0;
                                    }
                                    if($code == 0){
                                      $userCheckin = Carbon::parse($checkoutTimeServer);
                                        $userDuty = Carbon::parse($findtime->off_duty_time);
                                        $diff = $userCheckin->diff($userDuty);
                                        $hour = ($diff->h) * 60;
                                        $minute = $diff->i;
                                        $code = "";
                                        $min = "";
                                        $case = "";
                                        $lateMinuteAdmin = $findtime['early_leave'];
                                        $totalMn = "";
                                        $chekinLate = 0;
                                        $chekinBF = 0;
                                        $chekoutearly = 0;
                                        $chekoutBF = 0;
                                        $duration = 0;
                                        // second record
                                        // check checkin id and employee id
                                        if ($scann) {
                                            $standardHour = 0;
                                           
            
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
            
                                            // check employee type first
                                            if ($scann->date == $todayDate) {
                                                // check user workday
                                                $workday = Workday::find($employee->workday_id);
                                                $countOffDay = explode(",", $workday->off_day);
            
                                                $contract = Contract::where('user_id', '=', $employee->id)->first();
                                                if ($contract) {
                                                    $standardHour = ($contract->working_schedule) / (7 - count($countOffDay));
                                                } else {
                                                    $standardHour = 9;
                                                }
                                                // status = uncompleted
                                                $duration = $standardHour / 2;
                                                
                                                $duration = $standardHour / 2;
                                                
                                                
                                                $durationLeaveout= 0;
                                                
                                                if ($findtime) {
                                                    //    because user checkout must check if checkout time < 1 hour can checkout 
                                                    if ($findtime['early_leave'] == "0") {
                                                        if ($findtime['off_duty_time'] == $checkoutTimeServer) {
                                                            $targetStatus = $status[0];
                                                            $min = "0";
                                                            // $duration= $duration;
                                                            // $duration=round($duration,2);
                                                            $code = 0;
                                                            $case = "1";
                                                        }
                                                        // user leave before time out
                                                        // user leave 4, but time out 5
            
                                                        elseif ($findtime['off_duty_time'] > $checkoutTimeServer) {
                                                            $targetStatus = $status[3];
                                                            $min = $minute + $hour;
                                                            // $duration=($duration*60 - $min)/60 ;
                                                            // $duration=round($duration,2);
                                                            $code = 0;
                                                            $case = "2";
                                                        }
                                                        // user finish after time out 
                                                        // user leave 8 , timeout 6
                                                        elseif ($findtime['off_duty_time'] < $checkoutTimeServer) {
                                                            $targetStatus = $status[0];
                                                            $min = $minute + $hour;
                                                            // $duration=($duration*60 + $min)/60 ;
                                                            // $duration=round($duration,2);
                                                            $code = 0;
                                                            $case = "3";
                                                        }
                                                        // standard hour for 1 day =9 h * 60mn = mn 540 mn
                                                        if ($targetStatus == "early" || $targetStatus == "too early") {
                                                            $chekoutearly = $min;
                                                        } else {
                                                            $chekoutBF = $min;
                                                        }
                                                        $totalMn = ($scann->duration + $duration -$durationLeaveout) - ($chekoutearly +  $leaveDuration  - $chekoutBF ) / 60;
                                                        // $totalMn = $standardHour - ($chekinLate +  $chekoutearly +  $leaveDuration -  $chekinBF - $chekoutBF) / 60;
                                                        $totalMn = round($totalMn, 2);
                                                        $scann->user_id = $employee->id;
            
                                                        $scann->checkout_time = $checkoutTimeServer;
                                                        $scann->checkout_status = $targetStatus;
                                                        $scann->status = "present";
                                                        $scann->checkout_late = $min;
                                                        $scann->duration = $totalMn;
                                                        $scann->update();
                                                        $respone = [
                                                            'message' => 'Success',
                                                            'code' => 0,
                                                            'total_mn'=>$totalMn
            
                                                        ];
                                                        $notification = new Notice(
                                                            [
                                                                'notice' => "Checkout",
                                                                'noticedes' => "Employee name : {$employee->name}" . "\n" . "Position : {$position->position_name}" . "\n" . "Checkout time : " . $checkoutTimeServer . "\n" . "Date : " . $todayDate . "\n" . "Checkout status :" . $targetStatus . "\n" . "Time :" . $min . "\n",
            
                                                                'telegramid' => Config::get('services.telegram_id')
                                                            ]
                                                        );
            
                                                        $notification->notify(new TelegramRegister());
                                                        // if have overtime
                                                             $dayOff = "";
                                                            //  check leave out
                                                            // 
                                                            if ($overtime) {
                                                                $work = Workday::find($employee->workday_id);
                                                                if ($work->off_day != Null) {
                                                                    $OffDay = explode(',', $work->off_day);
                                                                    $check = "true";
                                                                    // $notCheck= new GetWeekday();
                                                                    // $notCheck->getday();
                                                                    $notCheck = $this->getWeekday($todayDate);
                                                                    // 1 = count($dayoff)
                                                                    for ($y = 0; $y <  count($OffDay); $y++) {
                                                                        //   if offday = today check will false
                                                                        if ($OffDay[$y] == $notCheck) {
                                                                            $check = "false";
                                                                        }
                                                                    }
                                                                    if ($check == "false") {
                                                                        // day off cannot check
                                                                        $dayOff = "false";
                                                                    } else {
                                                                        $dayOff = "true";
                                                                    }
                                                                }
                                                                if ($work->off_day == Null) {
                                                                    $dayOff = "true";
                                                                }
                                                                $minOvertime = 0;
                                                                $couterDdDuration = 0;
                                                                $k = "";
                                                                // overtime on workday
                                                                $ph = Holiday::whereDate('from_date', '>=', date('Y-m-d', strtotime($todayDate)))
                                                                ->whereDate('to_date', '<=', date('Y-m-d', strtotime($todayDate)))->first();
                                                                if($ph &&  $dayOff == "true"){
                                                                    $k = "5";
                                                                        // $otStatus = "true";
                                                                        $overtime->status = "completed";
                                                                        if ($overtime->pay_type == "holiday") {
                                                                            $overtime->pay_status = "completed";
                    
                                                                            $counter = Counter::where('user_id', '=', $employee->id)->first();
                                                                            if ($overtime->type == "hour") {
                                                                                if ($overtime->number > $totalMn) {
                                                                                    $counter->ot_duration  = $counter->ot_duration +  $totalMn;
                                                                                    $counter->ot_temp = "0";
                                                                                    $couterDdDuration = $counter->ot_duration;
                                                                                    $k = "1";
                                                                                }
                                                                                if ($overtime->number == $totalMn) {
                                                                                    $counter->ot_duration  = $counter->ot_duration +  $overtime->number;
                                                                                    $counter->ot_temp = "0";
                                                                                    $couterDdDuration = $counter->ot_duration;
                                                                                }
                                                                                if ($overtime->number < $totalMn) {
                                                                                    $counter->ot_duration  = $counter->ot_duration +  $overtime->number;
                                                                                    $counter->ot_temp = "0";
                                                                                    $couterDdDuration = $counter->ot_duration;
                                                                                }
                                                                            } else {
                                                                                // if overtime two day
                                                                                if ($overtime->number >= 2) {
                                                                                    $counter->ot_duration  = $counter->ot_duration + $totalMn;
                                                                                    $counter->ot_temp = "0";
                                                                                    $couterDdDuration = $counter->ot_duration;
                                                                                } else {
                                                                                    if($totalMn >= $overtime->ot_hour){
                                                                                            $counter->ot_duration  = $counter->ot_duration + $overtime->ot_hour;
                                                                                            $counter->ot_temp = "0"; 
                                                                                        }
                                                                                        if($totalMn < $overtime->ot_hour){
                                                                                            $counter->ot_duration  = $counter->ot_duration + $totalMn;
                                                                                            $counter->ot_temp = "0"; 
                                                                                        }
                                                                                        
                                                                                       
                                                                                        $couterDdDuration = $counter->ot_duration;
                                                                                }
                                                                                
                                                                            }
                                                                            $counter->update();
                                                                        }else{
                                                                            $realityTime = 0;
                    
                                                                                $counter = Counter::where('user_id', '=', $employee->id)->first();
                                                                                $realTotalTime=$totalMn/9;
                                                                                // $realTotalTime = round($totalMn,0);
                                                                                if ($overtime->type == "hour") {
                                                                                    // total 2, 1.8 
                                                                                    if ($overtime->number == $realTotalTime) {
                                                                                        $realityTime = $overtime->number ;
                                                                                        $counter->ot_temp  = "0";
                                                                                       
                                                                                    }
                                                                                    if ($overtime->number < $realTotalTime) {
                                                                                        $realityTime = $overtime->number;
                                                                                        $counter->ot_temp  = "0";
                                                                                       
                                                                                    }
                                                                                    if ($overtime->number > $realTotalTime) {
                                                                                        $realityTime  = $realTotalTime;
                                                                                        $counter->ot_temp  = "0";
                                                                                        
                                                                                    }
                                                                                    $total = $overtime-> ot_rate* $overtime->ot_method *  $realityTime;
                                                                                    $overtime->ot_hour= $realityTime;
                                                                                    $overtime->total_ot= $total;
                                                                                    // $overtime->ot_hour
                                                                                    // $overtime->
                                                                                } else {
                                                                                    // overtime number 1, so divide /9 , one day
                                                                                    
                                                                                    if ($overtime->number == $realTotalTime) {
                                                                                        $realityTime  =  $overtime->number;
                                                                                        $counter->ot_temp  = "0";
                                                                                        $k = "004";
                                                                                       
                                                                                    }
                                                                                    if ($overtime->number < $realTotalTime) {
                                                                                        $realityTime  =  $overtime->number;
                                                                                        $counter->ot_temp  = "0";
                                                                                        $k = "005";
                                                                                       
                                                                                    }
                                                                                    if ($overtime->number > $realTotalTime) {
                                                                                        $realityTime  =  $realTotalTime;
                                                                                        $counter->ot_temp  = "0";
                                                                                        $k = "006";
                                                                                        
                                                                                    }
                                                                                    $total = $overtime-> ot_rate* $overtime->ot_method * ( $realityTime*9);
                                                                                    $overtime->ot_hour= ( $realityTime*9);
                                                                                    $overtime->total_ot= round($total,2);
                                                                                    // complete when checkout
                                                                                   
                                                                                   
                                                                                }
                                                                                $counter->update();
                                                                        }
                                                                        $overtime->update();
                                                                }else{
                                                                    if ($dayOff == "true") {
                
                                                                        $minOvertime = 0;
                                                                        if ($findtime['off_duty_time'] == $checkoutTimeServer) {
                                                                        }
                                                                        // 5 , cheocut 7
                                                                        if ($findtime['off_duty_time'] < $checkoutTimeServer) {
                    
                                                                            $minOvertime = $minute + $hour;
                                                                        }
                                                                        if ($minOvertime >= 60) {
                                                                            $k = "001";
                                                                            $totalTime = $minOvertime / 60;
                    
                                                                            $overtime->status = "completed";
                                                                            // $overtime->pay_status = "completed";
                                                                            if ($overtime->pay_type == "holiday") {
                                                                                $overtime->pay_status = "completed";
                    
                                                                                $counter = Counter::where('user_id', '=', $employee->id)->first();
                                                                                if ($overtime->type == "hour") {
                                                                                    if ($overtime->number == $totalTime) {
                                                                                        $counter->ot_duration  = $counter->ot_duration + $overtime->number;
                                                                                        $couterDdDuration = $counter->ot_duration;
                                                                                        $k = "1";
                                                                                    }
                                                                                    if ($overtime->number < $totalTime) {
                                                                                        $counter->ot_duration  = $counter->ot_duration + $overtime->number;
                                                                                        $couterDdDuration = $counter->ot_duration;
                                                                                        $k = "2";
                                                                                    }
                                                                                    if ($overtime->number > $totalTime) {
                                                                                        $counter->ot_duration  = $counter->ot_duration + $totalTime;
                                                                                        $couterDdDuration = $counter->ot_duration;
                                                                                        $k = "3";
                                                                                    }
                                                                                } else {
                                                                                    // complete when checkout
                                                                                    $counter->ot_duration  = $counter->ot_duration + $duration;
                                                                                    $k = "4";
                                                                                    // $counter->ot_duration  =$counter->ot_duration + $overtime->number * 9;
                                                                                    // $counter->update();
                                                                                }
                                                                                $counter->update();
                                                                            }else{
                                                                                // calculate again overtime 
                                                                                $realityTime = 1;
                    
                                                                                $counter = Counter::where('user_id', '=', $employee->id)->first();
                                                                                $realTotalTime = round($totalTime,0);
                                                                                if ($overtime->type == "hour") {
                                                                                    // total 2, 1.8 
                                                                                    if ($overtime->number == $realTotalTime) {
                                                                                        $realityTime = $overtime->number ;
                                                                                        $counter->ot_temp  = "0";
                                                                                        $k = "5";
                                                                                       
                                                                                    }
                                                                                    if ($overtime->number < $realTotalTime) {
                                                                                        $realityTime = $overtime->number;
                                                                                        $counter->ot_temp  = "0";
                                                                                        $k = "6";
                                                                                    }
                                                                                    if ($overtime->number > $realTotalTime) {
                                                                                        $realityTime  = $realTotalTime;
                                                                                        $counter->ot_temp  = "0";
                                                                                        $k = "7";
                                                                                        
                                                                                    }
                                                                                    $total = $overtime-> ot_rate* $overtime->ot_method *  $realityTime;
                                                                                    $overtime->ot_hour= $realityTime;
                                                                                    $overtime->total_ot= $total;
                                                                                    $k = "8";
                                                                                    // $overtime->ot_hour
                                                                                    // $overtime->
                                                                                } else {
                                                                                    // overtime number 1, so divide /9 , one day
                                                                                    $realTotalTime= $realTotalTime/9;
                                                                                    if ($overtime->number == $realTotalTime) {
                                                                                        $realityTime  =  $overtime->number;
                                                                                        $counter->ot_temp  = "0";
                                                                                       
                                                                                    }
                                                                                    if ($overtime->number < $realTotalTime) {
                                                                                        $realityTime  =  $overtime->number;
                                                                                        $counter->ot_temp  = "0";
                                                                                       
                                                                                    }
                                                                                    if ($overtime->number > $realTotalTime) {
                                                                                        $realityTime  =  $realTotalTime;
                                                                                        $counter->ot_temp  = "0";
                                                                                        
                                                                                    }
                                                                                    $total = $overtime-> ot_rate* $overtime->ot_method *  $realityTime;
                                                                                    $overtime->ot_hour= $realityTime;
                                                                                    $overtime->total_ot= $total;
                                                                                    // complete when checkout
                                                                                   
                                                                                   
                                                                                }
                                                                                // $counter->update();
                                                                            }
                                                                            $overtime->update();
                                                                        } else {
                                                                            $k = "002";
                                                                        }
                    
                                                                    } else {
                                                                        $k = "5";
                                                                        // $otStatus = "true";
                                                                        $overtime->status = "completed";
                                                                        if ($overtime->pay_type == "holiday") {
                                                                            $overtime->pay_status = "completed";
                    
                                                                            $counter = Counter::where('user_id', '=', $employee->id)->first();
                                                                            if ($overtime->type == "hour") {
                                                                                if ($overtime->number > $totalMn) {
                                                                                    $counter->ot_duration  = $counter->ot_duration +  $totalMn;
                                                                                    $counter->ot_temp = "0";
                                                                                    $couterDdDuration = $counter->ot_duration;
                                                                                    $k = "1";
                                                                                }
                                                                                if ($overtime->number == $totalMn) {
                                                                                    $counter->ot_duration  = $counter->ot_duration +  $overtime->number;
                                                                                    $counter->ot_temp = "0";
                                                                                    $couterDdDuration = $counter->ot_duration;
                                                                                }
                                                                                if ($overtime->number < $totalMn) {
                                                                                    $counter->ot_duration  = $counter->ot_duration +  $overtime->number;
                                                                                    $counter->ot_temp = "0";
                                                                                    $couterDdDuration = $counter->ot_duration;
                                                                                }
                                                                            } else {
                                                                                // if overtime two day
                                                                                if ($overtime->number >= 2) {
                                                                                    $counter->ot_duration  = $counter->ot_duration + $totalMn;
                                                                                    $counter->ot_temp = "0";
                                                                                    $couterDdDuration = $counter->ot_duration;
                                                                                } else {
                                                                                    if($totalMn >= $overtime->ot_hour){
                                                                                            $counter->ot_duration  = $counter->ot_duration + $overtime->ot_hour;
                                                                                            $counter->ot_temp = "0"; 
                                                                                        }
                                                                                        if($totalMn < $overtime->ot_hour){
                                                                                            $counter->ot_duration  = $counter->ot_duration + $totalMn;
                                                                                            $counter->ot_temp = "0"; 
                                                                                        }
                                                                                        
                                                                                       
                                                                                        $couterDdDuration = $counter->ot_duration;
                                                                                }
                                                                                
                                                                            }
                                                                            $counter->update();
                                                                        }else{
                                                                            $realityTime = 0;
                    
                                                                                $counter = Counter::where('user_id', '=', $employee->id)->first();
                                                                                $realTotalTime=$totalMn/9;
                                                                                // $realTotalTime = round($totalMn,0);
                                                                                if ($overtime->type == "hour") {
                                                                                    // total 2, 1.8 
                                                                                    if ($overtime->number == $realTotalTime) {
                                                                                        $realityTime = $overtime->number ;
                                                                                        $counter->ot_temp  = "0";
                                                                                       
                                                                                    }
                                                                                    if ($overtime->number < $realTotalTime) {
                                                                                        $realityTime = $overtime->number;
                                                                                        $counter->ot_temp  = "0";
                                                                                       
                                                                                    }
                                                                                    if ($overtime->number > $realTotalTime) {
                                                                                        $realityTime  = $realTotalTime;
                                                                                        $counter->ot_temp  = "0";
                                                                                        
                                                                                    }
                                                                                    $total = $overtime-> ot_rate* $overtime->ot_method *  $realityTime;
                                                                                    $overtime->ot_hour= $realityTime;
                                                                                    $overtime->total_ot= $total;
                                                                                    // $overtime->ot_hour
                                                                                    // $overtime->
                                                                                } else {
                                                                                    // overtime number 1, so divide /9 , one day
                                                                                    
                                                                                    if ($overtime->number == $realTotalTime) {
                                                                                        $realityTime  =  $overtime->number;
                                                                                        $counter->ot_temp  = "0";
                                                                                        $k = "004";
                                                                                       
                                                                                    }
                                                                                    if ($overtime->number < $realTotalTime) {
                                                                                        $realityTime  =  $overtime->number;
                                                                                        $counter->ot_temp  = "0";
                                                                                        $k = "005";
                                                                                       
                                                                                    }
                                                                                    if ($overtime->number > $realTotalTime) {
                                                                                        $realityTime  =  $realTotalTime;
                                                                                        $counter->ot_temp  = "0";
                                                                                        $k = "006";
                                                                                        
                                                                                    }
                                                                                    $total = $overtime-> ot_rate* $overtime->ot_method * ( $realityTime*9);
                                                                                    $overtime->ot_hour= ( $realityTime*9);
                                                                                    $overtime->total_ot= round($total,2);
                                                                                    // complete when checkout
                                                                                   
                                                                                   
                                                                                }
                                                                                $counter->update();
                                                                        }
                                                                        $overtime->update();
                                                                    }
                                                                }
                                                                
                                                               }
                                                            // 
                                                    } else {
                                                        // if admin set for leave early
                                                        if ($findtime['off_duty_time'] == $checkoutTimeServer) {
                                                            $targetStatus = $status[0];
                                                            $min = "0";
                                                            $code = 0;
                                                        }
                                                        // user leave before time out
                                                        // user leave 4, but time out 5
                                                        elseif ($findtime['off_duty_time'] > $checkoutTimeServer) {
                                                            $targetStatus = $status[3];
                                                            $min = $minute + $hour;
                                                            $code = 0;
                                                        }
                                                        // user leave after time out
                                                        // 08:00,8:30
                                                        elseif ($findtime['off_duty_time'] < $checkoutTimeServer) {
                                                            $targetStatus = $status[0];
                                                            $min = $minute + $hour;
                                                            $code = 0;
                                                        }
                                                        if ($targetStatus == "early" || $targetStatus == "too early") {
                                                            $chekoutearly = $min;
                                                        } else {
                                                            $chekoutBF = $min;
                                                        }
                                                        $totalMn = ($scann->duration + $duration -$durationLeaveout) - ($chekoutearly +  $leaveDuration  - $chekoutBF) / 60;
                                                        // $totalMn =  $standardHour - ($chekinLate +  $chekoutearly +  $leaveDuration -  $chekinBF - $chekoutBF) / 60;
                                                        $totalMn = round($totalMn, 2);
                                                        $scann->user_id = $employee->id;
                                                        $scann->checkout_time = $checkoutTimeServer;
                                                        $scann->checkout_status = $targetStatus;
                                                        $scann->status = "present";
                                                        $scann->checkout_late = $min;
                                                        $scann->duration = $totalMn;
                                                        // $scann->update();
                                                        $respone = [
                                                            'message' => 'Success',
                                                            'code' => 0,
                                                        ];
                                                        $notification = new Notice(
                                                            [
                                                                'notice' => "Checkout",
                                                                'noticedes' => "Employee name : {$employee->name}" . "\n" . "Position : {$position->position_name}" . "\n" . "Checkout time : " . $checkoutTimeServer . "\n" . "Date : " . $todayDate . "\n" . "Checkout status :" . $targetStatus . "\n" . "Time :" . $min . "\n",
            
                                                                'telegramid' => Config::get('services.telegram_id')
                                                            ]
                                                        );
                                                        $notification->notify(new TelegramRegister());
                                                        // if have overtime
                                                        // $Leaveout = Leaveout::where('user_id',$employee->id)
                                                        // ->where('date','=',$todayDate)
                                                        // ->where('request_type','=','leave_out')
                                                        // ->where('status','=','completed')
                                                        // ->first();
                                                        // // $Leaveout=Leaveout::all();
                                                        // $songTime =  Leaveout::where('user_id',$employee->id)
                                                        // ->where('date','=',$todayDate)
                                                        // ->where('request_type','=','clear_leave_out')
                                                        // ->where('status','=','approved')
                                                        // ->first();
                                                        // // $totalMn
                                                        // $songTimeDuration =0;
                                                        // $durationLeaveout= 0;
                                                        // if($songTime){
                                                        //     if($songTime->type=="hour"){
                                                                
                                                        //         $durationLeaveout= $songTime['duration'];
                                                        //     }else{
                                                        //         // mn
                                                        //         $durationLeaveout= $songTime['duration'] /60;
                                                        //     }
                                                        //     if( $durationLeaveout > $totalMn){
                                                        //         $durationLeaveout = $totalMn;
                                                        //     }
                                                        //      if( $durationLeaveout == $totalMn){
                                                        //         $durationLeaveout = $durationLeaveout;
                                                        //     }
                                                        //     if($durationLeaveout < $totalMn){
                                                        //         $durationLeaveout =$durationLeaveout;
                                                        //     }
                                                        //     $findCounter = Counter::where('user_id','=',$employee->id)->first();
                                                        //     $minusDuration = 0;
                                                        //     if($findCounter){
                                                        //         $minusDuration = $findCounter->ot_duration +$durationLeaveout;
                                                                
                                                        //     }
                                                        //     $findCounter->ot_duration = $minusDuration;
                                                        //     $findCounter->update();
                                                        // }
                                                        
                                                        // if($Leaveout){
                                                        //     if($Leaveout->reason != "work"){
                                                        //         if($Leaveout->type=="hour"){
                                                        //             $durationLeaveout= $Leaveout['duration'];
                                                        //         }else{
                                                        //             $durationLeaveout= $Leaveout['duration'] /60;
                                                        //         }
                                                        //         if( $durationLeaveout > $totalMn){
                                                        //         $durationLeaveout = $totalMn;
                                                        //         }
                                                        //          if( $durationLeaveout == $totalMn){
                                                        //             $durationLeaveout = $durationLeaveout;
                                                        //         }
                                                        //         if($durationLeaveout < $totalMn){
                                                        //             $durationLeaveout =$durationLeaveout;
                                                        //         }
                                                        //         $findCounter = Counter::where('user_id','=',$employee->id)->first();
                                                        //         $minusDuration = 0;
                                                        //         if($findCounter){
                                                        //             if($findCounter->ot_duration >0){
                                                        //                 if($findCounter->ot_duration > $durationLeaveout){
                                                        //                     $minusDuration = $findCounter->ot_duration -$durationLeaveout;
                                                        //                 }
                                                        //                 if($findCounter->ot_duration == $durationLeaveout){
                                                        //                     $minusDuration=0;
                                                        //                 }
                                                        //                 if($findCounter->ot_duration < $durationLeaveout){
                                                        //                     $minusDuration =$durationLeaveout- $findCounter->ot_duration;
                                                        //                 }
                                                                        
                                                        //             }
                                                        //             if($findCounter->ot_duration <=0 ){
                                                        //                 $minusDuration = - $durationLeaveout;
                                                        //             }
                                                        //         }
                                                        //         $findCounter->ot_duration = $minusDuration;
                                                        //         $findCounter->update();
                                                        //     }
                                                            
                                                        // }
                                                         // if have overtime
                                                         $dayOff = "";
                                                        if ($overtime) {
                                                            $work = Workday::find($employee->workday_id);
                                                            if ($work->off_day != Null) {
                                                                $OffDay = explode(',', $work->off_day);
                                                                $check = "true";
                                                                // $notCheck= new GetWeekday();
                                                                // $notCheck->getday();
                                                                $notCheck = $this->getWeekday($todayDate);
                                                                // 1 = count($dayoff)
                                                                for ($y = 0; $y <  count($OffDay); $y++) {
                                                                    //   if offday = today check will false
                                                                    if ($OffDay[$y] == $notCheck) {
                                                                        $check = "false";
                                                                    }
                                                                }
                                                                if ($check == "false") {
                                                                    // day off cannot check
                                                                    $dayOff = "false";
                                                                } else {
                                                                    $dayOff = "true";
                                                                }
                                                            }
                                                            if ($work->off_day == Null) {
                                                                $dayOff = "true";
                                                            }
                                                            $minOvertime = 0;
                                                            $couterDdDuration = 0;
                                                            $k = "";
                                                            // overtime on workday
                                                            $ph = Holiday::whereDate('from_date', '>=', date('Y-m-d', strtotime($todayDate)))
                                                            ->whereDate('to_date', '<=', date('Y-m-d', strtotime($todayDate)))->first();
                                                            if($ph &&  $dayOff == "true"){
                                                                $k = "5";
                                                                    // $otStatus = "true";
                                                                    $overtime->status = "completed";
                                                                    if ($overtime->pay_type == "holiday") {
                                                                        $overtime->pay_status = "completed";
                
                                                                        $counter = Counter::where('user_id', '=', $employee->id)->first();
                                                                        if ($overtime->type == "hour") {
                                                                            if ($overtime->number > $totalMn) {
                                                                                $counter->ot_duration  = $counter->ot_duration +  $totalMn;
                                                                                $counter->ot_temp = "0";
                                                                                $couterDdDuration = $counter->ot_duration;
                                                                                $k = "1";
                                                                            }
                                                                            if ($overtime->number == $totalMn) {
                                                                                $counter->ot_duration  = $counter->ot_duration +  $overtime->number;
                                                                                $counter->ot_temp = "0";
                                                                                $couterDdDuration = $counter->ot_duration;
                                                                            }
                                                                            if ($overtime->number < $totalMn) {
                                                                                $counter->ot_duration  = $counter->ot_duration +  $overtime->number;
                                                                                $counter->ot_temp = "0";
                                                                                $couterDdDuration = $counter->ot_duration;
                                                                            }
                                                                        } else {
                                                                            // if overtime two day
                                                                            if ($overtime->number >= 2) {
                                                                                $counter->ot_duration  = $counter->ot_duration + $totalMn;
                                                                                $counter->ot_temp = "0";
                                                                                $couterDdDuration = $counter->ot_duration;
                                                                            } else {
                                                                                if($totalMn >= $overtime->ot_hour){
                                                                                        $counter->ot_duration  = $counter->ot_duration + $overtime->ot_hour;
                                                                                        $counter->ot_temp = "0"; 
                                                                                    }
                                                                                    if($totalMn < $overtime->ot_hour){
                                                                                        $counter->ot_duration  = $counter->ot_duration + $totalMn;
                                                                                        $counter->ot_temp = "0"; 
                                                                                    }
                                                                                    
                                                                                   
                                                                                    $couterDdDuration = $counter->ot_duration;
                                                                            }
                                                                            
                                                                        }
                                                                        $counter->update();
                                                                    }else{
                                                                        $realityTime = 0;
                
                                                                            $counter = Counter::where('user_id', '=', $employee->id)->first();
                                                                            $realTotalTime=$totalMn/9;
                                                                            // $realTotalTime = round($totalMn,0);
                                                                            if ($overtime->type == "hour") {
                                                                                // total 2, 1.8 
                                                                                if ($overtime->number == $realTotalTime) {
                                                                                    $realityTime = $overtime->number ;
                                                                                    $counter->ot_temp  = "0";
                                                                                   
                                                                                }
                                                                                if ($overtime->number < $realTotalTime) {
                                                                                    $realityTime = $overtime->number;
                                                                                    $counter->ot_temp  = "0";
                                                                                   
                                                                                }
                                                                                if ($overtime->number > $realTotalTime) {
                                                                                    $realityTime  = $realTotalTime;
                                                                                    $counter->ot_temp  = "0";
                                                                                    
                                                                                }
                                                                                $total = $overtime-> ot_rate* $overtime->ot_method *  $realityTime;
                                                                                $overtime->ot_hour= $realityTime;
                                                                                $overtime->total_ot= $total;
                                                                                // $overtime->ot_hour
                                                                                // $overtime->
                                                                            } else {
                                                                                // overtime number 1, so divide /9 , one day
                                                                                
                                                                                if ($overtime->number == $realTotalTime) {
                                                                                    $realityTime  =  $overtime->number;
                                                                                    $counter->ot_temp  = "0";
                                                                                    $k = "004";
                                                                                   
                                                                                }
                                                                                if ($overtime->number < $realTotalTime) {
                                                                                    $realityTime  =  $overtime->number;
                                                                                    $counter->ot_temp  = "0";
                                                                                    $k = "005";
                                                                                   
                                                                                }
                                                                                if ($overtime->number > $realTotalTime) {
                                                                                    $realityTime  =  $realTotalTime;
                                                                                    $counter->ot_temp  = "0";
                                                                                    $k = "006";
                                                                                    
                                                                                }
                                                                                $total = $overtime-> ot_rate* $overtime->ot_method * ( $realityTime*9);
                                                                                $overtime->ot_hour= ( $realityTime*9);
                                                                                $overtime->total_ot= round($total,2);
                                                                                // complete when checkout
                                                                               
                                                                               
                                                                            }
                                                                            $counter->update();
                                                                    }
                                                                    $overtime->update();
                                                            }else{
                                                                if ($dayOff == "true") {
            
                                                                    $minOvertime = 0;
                                                                    if ($findtime['off_duty_time'] == $checkoutTimeServer) {
                                                                    }
                                                                    // 5 , cheocut 7
                                                                    if ($findtime['off_duty_time'] < $checkoutTimeServer) {
                
                                                                        $minOvertime = $minute + $hour;
                                                                    }
                                                                    if ($minOvertime >= 60) {
                                                                        $k = "001";
                                                                        $totalTime = $minOvertime / 60;
                
                                                                        $overtime->status = "completed";
                                                                        // $overtime->pay_status = "completed";
                                                                        if ($overtime->pay_type == "holiday") {
                                                                            $overtime->pay_status = "completed";
                
                                                                            $counter = Counter::where('user_id', '=', $employee->id)->first();
                                                                            if ($overtime->type == "hour") {
                                                                                if ($overtime->number == $totalTime) {
                                                                                    $counter->ot_duration  = $counter->ot_duration + $overtime->number;
                                                                                    $couterDdDuration = $counter->ot_duration;
                                                                                    $k = "1";
                                                                                }
                                                                                if ($overtime->number < $totalTime) {
                                                                                    $counter->ot_duration  = $counter->ot_duration + $overtime->number;
                                                                                    $couterDdDuration = $counter->ot_duration;
                                                                                    $k = "2";
                                                                                }
                                                                                if ($overtime->number > $totalTime) {
                                                                                    $counter->ot_duration  = $counter->ot_duration + $totalTime;
                                                                                    $couterDdDuration = $counter->ot_duration;
                                                                                    $k = "3";
                                                                                }
                                                                            } else {
                                                                                // complete when checkout
                                                                                $counter->ot_duration  = $counter->ot_duration + $duration;
                                                                                $k = "4";
                                                                                // $counter->ot_duration  =$counter->ot_duration + $overtime->number * 9;
                                                                                // $counter->update();
                                                                            }
                                                                            $counter->update();
                                                                        }else{
                                                                            // calculate again overtime 
                                                                            $realityTime = 1;
                
                                                                            $counter = Counter::where('user_id', '=', $employee->id)->first();
                                                                            $realTotalTime = round($totalTime,0);
                                                                            if ($overtime->type == "hour") {
                                                                                // total 2, 1.8 
                                                                                if ($overtime->number == $realTotalTime) {
                                                                                    $realityTime = $overtime->number ;
                                                                                    $counter->ot_temp  = "0";
                                                                                    $k = "5";
                                                                                   
                                                                                }
                                                                                if ($overtime->number < $realTotalTime) {
                                                                                    $realityTime = $overtime->number;
                                                                                    $counter->ot_temp  = "0";
                                                                                    $k = "6";
                                                                                }
                                                                                if ($overtime->number > $realTotalTime) {
                                                                                    $realityTime  = $realTotalTime;
                                                                                    $counter->ot_temp  = "0";
                                                                                    $k = "7";
                                                                                    
                                                                                }
                                                                                $total = $overtime-> ot_rate* $overtime->ot_method *  $realityTime;
                                                                                $overtime->ot_hour= $realityTime;
                                                                                $overtime->total_ot= $total;
                                                                                $k = "8";
                                                                                // $overtime->ot_hour
                                                                                // $overtime->
                                                                            } else {
                                                                                // overtime number 1, so divide /9 , one day
                                                                                $realTotalTime= $realTotalTime/9;
                                                                                if ($overtime->number == $realTotalTime) {
                                                                                    $realityTime  =  $overtime->number;
                                                                                    $counter->ot_temp  = "0";
                                                                                   
                                                                                }
                                                                                if ($overtime->number < $realTotalTime) {
                                                                                    $realityTime  =  $overtime->number;
                                                                                    $counter->ot_temp  = "0";
                                                                                   
                                                                                }
                                                                                if ($overtime->number > $realTotalTime) {
                                                                                    $realityTime  =  $realTotalTime;
                                                                                    $counter->ot_temp  = "0";
                                                                                    
                                                                                }
                                                                                $total = $overtime-> ot_rate* $overtime->ot_method *  $realityTime;
                                                                                $overtime->ot_hour= $realityTime;
                                                                                $overtime->total_ot= $total;
                                                                                // complete when checkout
                                                                               
                                                                               
                                                                            }
                                                                            // $counter->update();
                                                                        }
                                                                        $overtime->update();
                                                                    } else {
                                                                        $k = "002";
                                                                    }
                
                                                                } else {
                                                                    $k = "5";
                                                                    // $otStatus = "true";
                                                                    $overtime->status = "completed";
                                                                    if ($overtime->pay_type == "holiday") {
                                                                        $overtime->pay_status = "completed";
                
                                                                        $counter = Counter::where('user_id', '=', $employee->id)->first();
                                                                        if ($overtime->type == "hour") {
                                                                            if ($overtime->number > $totalMn) {
                                                                                $counter->ot_duration  = $counter->ot_duration +  $totalMn;
                                                                                $counter->ot_temp = "0";
                                                                                $couterDdDuration = $counter->ot_duration;
                                                                                $k = "1";
                                                                            }
                                                                            if ($overtime->number == $totalMn) {
                                                                                $counter->ot_duration  = $counter->ot_duration +  $overtime->number;
                                                                                $counter->ot_temp = "0";
                                                                                $couterDdDuration = $counter->ot_duration;
                                                                            }
                                                                            if ($overtime->number < $totalMn) {
                                                                                $counter->ot_duration  = $counter->ot_duration +  $overtime->number;
                                                                                $counter->ot_temp = "0";
                                                                                $couterDdDuration = $counter->ot_duration;
                                                                            }
                                                                        } else {
                                                                            // if overtime two day
                                                                            if ($overtime->number >= 2) {
                                                                                $counter->ot_duration  = $counter->ot_duration + $totalMn;
                                                                                $counter->ot_temp = "0";
                                                                                $couterDdDuration = $counter->ot_duration;
                                                                            } else {
                                                                                if($totalMn >= $overtime->ot_hour){
                                                                                        $counter->ot_duration  = $counter->ot_duration + $overtime->ot_hour;
                                                                                        $counter->ot_temp = "0"; 
                                                                                    }
                                                                                    if($totalMn < $overtime->ot_hour){
                                                                                        $counter->ot_duration  = $counter->ot_duration + $totalMn;
                                                                                        $counter->ot_temp = "0"; 
                                                                                    }
                                                                                    
                                                                                   
                                                                                    $couterDdDuration = $counter->ot_duration;
                                                                            }
                                                                            
                                                                        }
                                                                        $counter->update();
                                                                    }else{
                                                                        $realityTime = 0;
                
                                                                            $counter = Counter::where('user_id', '=', $employee->id)->first();
                                                                            $realTotalTime=$totalMn/9;
                                                                            // $realTotalTime = round($totalMn,0);
                                                                            if ($overtime->type == "hour") {
                                                                                // total 2, 1.8 
                                                                                if ($overtime->number == $realTotalTime) {
                                                                                    $realityTime = $overtime->number ;
                                                                                    $counter->ot_temp  = "0";
                                                                                   
                                                                                }
                                                                                if ($overtime->number < $realTotalTime) {
                                                                                    $realityTime = $overtime->number;
                                                                                    $counter->ot_temp  = "0";
                                                                                   
                                                                                }
                                                                                if ($overtime->number > $realTotalTime) {
                                                                                    $realityTime  = $realTotalTime;
                                                                                    $counter->ot_temp  = "0";
                                                                                    
                                                                                }
                                                                                $total = $overtime-> ot_rate* $overtime->ot_method *  $realityTime;
                                                                                $overtime->ot_hour= $realityTime;
                                                                                $overtime->total_ot= $total;
                                                                                // $overtime->ot_hour
                                                                                // $overtime->
                                                                            } else {
                                                                                // overtime number 1, so divide /9 , one day
                                                                                
                                                                                if ($overtime->number == $realTotalTime) {
                                                                                    $realityTime  =  $overtime->number;
                                                                                    $counter->ot_temp  = "0";
                                                                                    $k = "004";
                                                                                   
                                                                                }
                                                                                if ($overtime->number < $realTotalTime) {
                                                                                    $realityTime  =  $overtime->number;
                                                                                    $counter->ot_temp  = "0";
                                                                                    $k = "005";
                                                                                   
                                                                                }
                                                                                if ($overtime->number > $realTotalTime) {
                                                                                    $realityTime  =  $realTotalTime;
                                                                                    $counter->ot_temp  = "0";
                                                                                    $k = "006";
                                                                                    
                                                                                }
                                                                                $total = $overtime-> ot_rate* $overtime->ot_method * ( $realityTime*9);
                                                                                $overtime->ot_hour= ( $realityTime*9);
                                                                                $overtime->total_ot= round($total,2);
                                                                                // complete when checkout
                                                                               
                                                                               
                                                                            }
                                                                            $counter->update();
                                                                    }
                                                                    $overtime->update();
                                                                }
                                                            }
                                                            
                                                        }
                                                        // 
                                                    }
                                                }
                                                // check song mong
                                                
                                                $Leaveout = Leaveout::where('user_id',$employee->id)
                                                ->where('date','=',$todayDate)
                                                ->where('request_type','=', 'leave_out')
                                                ->where('status','=','completed')
                                                ->first();
                                                $left_time =0;
                                                if($Leaveout){
                                                    if($Leaveout->reason != "work"){
                                                        if($Leaveout->type=="hour"){
                                                            $durationLeaveout= $Leaveout['duration'];
                                                        }else{
                                                            $durationLeaveout= $Leaveout['duration'] /60;
                                                        }
                                                        $left_time =$totalMn-9;
                                                        
                                                        $is_cut =false;
                                                        $minusDuration = 0;
                                                        if($left_time >= 1){
                                                            if($left_time > $durationLeaveout){
                                                                $is_cut =false;
                                                            }else{   
                                                                $is_cut =true;
                                                            }
                                                        }else{
                                                            $is_cut =true;
                                                        }
                                                        if($is_cut ==true){
                                                            $findCounter = Counter::where('user_id','=',$employee->id)->first();
                                                            $minusDuration = 0;
                                                                if($findCounter){
                                                                    if($findCounter->ot_duration >0){
                                                                        if($findCounter->ot_duration > $durationLeaveout){
                                                                            $minusDuration = $findCounter->ot_duration -$durationLeaveout;
                                                                        }
                                                                        if($findCounter->ot_duration == $durationLeaveout){
                                                                            $minusDuration=0;
                                                                        }
                                                                        if($findCounter->ot_duration < $durationLeaveout){
                                                                            $minusDuration =$durationLeaveout- $findCounter->ot_duration;
                                                                        }
                                                                        
                                                                    }
                                                                    if($findCounter->ot_duration <=0 ){
                                                                        $minusDuration = - $durationLeaveout;
                                                                    }
                                                                }
                                                                $findCounter->ot_duration = $minusDuration;
                                                                $findCounter->update();
                                                        }
                                                        
                                                        
                                                    }
                                                    
                                                }
                                                // $Leaveout=Leaveout::all();
                                                $songTime =  Leaveout::where('user_id',$employee->id)
                                                ->where('date','=',$todayDate)
                                                 ->where('request_type','=', 'clear_leave_out')
                                                ->where('status','=','approved')
                                                ->first();
                                                $songTimeDuration =0;
                                                if($songTime){
                                                    if($songTime->type=="hour"){
                                                        $durationLeaveout= $songTime['duration'];
                                                    }else{
                                                        $durationLeaveout= $songTime['duration'] /60;
                                                    }
                                                    $left_time =$totalMn-9;
                                                    if($left_time >= 1){
                                                        if($left_time > $durationLeaveout){
                                                            $left_time=$durationLeaveout;
                                                        }else{   
                                                            $left_time=$left_time;
                                                        }
                                                        if($findCounter){
                                                            $findCounter = Counter::where('user_id','=',$employee->id)->first();
                                                            $minusDuration = 0;
                                                            if($findCounter){
                                                                $minusDuration =$findCounter->ot_duration +  $left_time;
                                                                $findCounter->ot_duration = $minusDuration;
                                                                $findCounter->update();
                                                            }     
                                                                
                                                        }
                                                    
                                                    }
                                                    
                                                }
                                                
                                            } else {
                                                $respone = [
                                                    'message' => 'Already checkin',
                                                    'code' => -1,
                                                    'checkindate' => $todayDate,
                                                    'lastcheckin' => $scann->date,
                                                    // "req"=>$checkin,
                                                ];
                                            }
                                        } else {
                                            // don't have checkin id = employee id
                                            $respone = [
                                                'message' => 'No checkin id found ',
                                                'code' => -1,
                                            ];
                                        }
                                            // 
                                    }else{
                                             $respone = [
                                                'message' => 'Cannot checkout now ',
                                                'code' => -1,
                                            ];           
                                    }
                                   
                                }
                            }else{
                                $respone = [
                                    'message' => "Please scan again",
                                    'code' => -2,
    
                                ];
                            }
                        }
                        
                        
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
            }
        } catch (Exception $e) {
            return response([
                'message' => $e->getMessage()
            ]);
        }
    }
    public function leavetype(Request $request)
    {
        try {
            //code...
            $data = Leavetype::where('parent_id', '=', 0)->get();
            if ($data) {
                return response(
                    [
                        'code' => 0,
                        'message' => 'success',
                        'data' => $data
                    ]
                );
            }
        } catch (Exception $e) {
            // return response($e ,200);
            return response()->json(
                [
                    'message' => $e->getMessage(),
                    // 'data'=>[]
                ]
            );
        }
    }
    public function subleavetype(Request $request, $id)
    {
        try {

            $department = Leavetype::where('parent_id', '=', $id)->get();
            return response()->json(
                [
                    'message' => "Success",
                    'code' => 0,
                    'data' => $department
                ],
                200
            );
        } catch (Exception $e) {

            return response()->json(
                [
                    'message' => $e->getMessage(),

                ]
            );
        }
    }
    public function getSchedule(Request $request)
    {
        try {
            // $user_id = $request->user()->id;

            // $ex1 = TimetableEmployee::where('user_id', $user_id)->get();
            // foreach ($ex1 as $key => $val) {
            //     // $ex2 =  User::where('id',$val->user_id)->first();
            //     $time = Timetable::where('id', $val->timetable_id)->first();
            //     // $val->emplyee = $ex2;
            //     $val->timetable = $time;
            // }

            // //
            // return response()->json(
            //     ['data' => $ex1],
            //     200
            // );
        } catch (Exception $e) {
            // return response($e ,200);
            return response()->json(
                [
                    'message' => $e->getMessage(),
                    // 'data'=>[]
                ]
            );
        }
    }
    public function getleave(Request $request)
    {
        try {
            $user_id = $request->user()->id;
            // $employee = User::find($use_id);
            $pageSize = $request->page_size ?? 10;
            $fromDate = $request->get('from_date');
            $toDate = $request->get('to_date');
            $todayDate = Carbon::now()->format('m/d/Y');
            // $postion = Position::paginate($pageSize);

            //code...

            if ($request->has('from_date') && $request->has('to_date')) {
                // $total= Leave::where('user_id',$user_id)-> whereDate('leaves.created_at', '>=', date('Y-m-d',strtotime($fromDate)))
                // ->whereDate('leaves.created_at', '<=', date('Y-m-d',strtotime($toDate))) ->count();
                // $pending= Leave::where('user_id',$user_id)-> whereDate('leaves.created_at', '>=', date('Y-m-d',strtotime($fromDate)))
                // ->whereDate('leaves.created_at', '<=', date('Y-m-d',strtotime($toDate)))
                // ->where('status','=','pending')
                // ->count();
                // // include approve and reject
                // $complete= Leave::where('user_id',$user_id)-> whereDate('leaves.created_at', '>=', date('Y-m-d',strtotime($fromDate)))
                // ->whereDate('leaves.created_at', '<=', date('Y-m-d',strtotime($toDate)))
                // ->where('status','=','approved')
                // ->orWhere('status','=','rejected')
                // ->count();
                $data = Leave::with('leavetype')->where('user_id', $user_id)->whereDate('leaves.created_at', '>=', date('Y-m-d', strtotime($fromDate)))
                    ->whereDate('leaves.created_at', '<=', date('Y-m-d', strtotime($toDate)))
                    ->orderBy('created_at', 'DESC')->paginate($pageSize);
            } else {
                $data = Leave::with('leavetype')->where('user_id', $user_id)
                    ->orderBy('created_at', 'DESC')
                    ->paginate($pageSize);
            }
            // else{
            //     $complete= Leave::where('user_id',$user_id)->where('date','=',$todayDate)
            //     ->where('status','=','approved')
            //     ->orWhere('status','=','rejected')
            //      ->count();
            //      $pending= Leave::where('user_id',$user_id)->where('date','=',$todayDate)
            //      ->where('status','=','pending')
            //      ->count();
            //     $total= Leave::where('user_id',$user_id)->where('date','=',$todayDate) ->count();
            //     $data = Leave::with('leavetype')->where('user_id',$user_id)->with('user','leavetype')
            //     ->orderBy('created_at', 'DESC')->paginate($pageSize);
            // }
            // $response = [


            //     'total_leave'=>$total,
            //     'pending_leave'=>$pending,
            //     'complete_leave'=>$complete
            //     // 'checkin'=>$checkin,
            // ];
            return response()->json(
                $data,
                200
            );
        } catch (Exception $e) {
            // return response($e ,200);
            return response()->json(
                [
                    'message' => $e->getMessage(),
                    // 'data'=>[]
                ]
            );
        }
    }
    public function countEachleave(Request $request)
    {
        try {
            $user_id = $request->user()->id;
            // $employee = User::find($use_id);
            $pageSize = $request->page_size ?? 10;
            $fromDate = $request->get('from_date');
            $toDate = $request->get('to_date');
            $todayDate = Carbon::now()->format('m/d/Y');
            // $postion = Position::paginate($pageSize);

            //code...

            if ($request->has('from_date') && $request->has('to_date')) {
                // $total= Leave::where('user_id',$user_id)-> whereDate('leaves.created_at', '>=', date('Y-m-d',strtotime($fromDate)))
                // ->whereDate('leaves.created_at', '<=', date('Y-m-d',strtotime($toDate))) ->count();
                // $pending= Leave::where('user_id',$user_id)-> whereDate('leaves.created_at', '>=', date('Y-m-d',strtotime($fromDate)))
                // ->whereDate('leaves.created_at', '<=', date('Y-m-d',strtotime($toDate)))
                // ->where('status','=','pending')
                // ->count();
                // // include approve and reject
                // $complete= Leave::where('user_id',$user_id)-> whereDate('leaves.created_at', '>=', date('Y-m-d',strtotime($fromDate)))
                // ->whereDate('leaves.created_at', '<=', date('Y-m-d',strtotime($toDate)))
                // ->where('status','=','approved')
                // ->orWhere('status','=','rejected')
                // ->count();
                $data = Leave::with('leavetype')->where('user_id', $user_id)->whereDate('leaves.created_at', '>=', date('Y-m-d', strtotime($fromDate)))
                    ->whereDate('leaves.created_at', '<=', date('Y-m-d', strtotime($toDate)))
                    ->orderBy('created_at', 'DESC')->paginate($pageSize);
            } else {
                $data = Leave::with('leavetype')->where('user_id', $user_id)
                    ->orderBy('created_at', 'DESC')
                    ->paginate($pageSize);
            }
            // else{
            //     $complete= Leave::where('user_id',$user_id)->where('date','=',$todayDate)
            //     ->where('status','=','approved')
            //     ->orWhere('status','=','rejected')
            //      ->count();
            //      $pending= Leave::where('user_id',$user_id)->where('date','=',$todayDate)
            //      ->where('status','=','pending')
            //      ->count();
            //     $total= Leave::where('user_id',$user_id)->where('date','=',$todayDate) ->count();
            //     $data = Leave::with('leavetype')->where('user_id',$user_id)->with('user','leavetype')
            //     ->orderBy('created_at', 'DESC')->paginate($pageSize);
            // }
            // $response = [


            //     'total_leave'=>$total,
            //     'pending_leave'=>$pending,
            //     'complete_leave'=>$complete
            //     // 'checkin'=>$checkin,
            // ];
            return response()->json(
                $data,
                200
            );
        } catch (Exception $e) {
            // return response($e ,200);
            return response()->json(
                [
                    'message' => $e->getMessage(),
                    // 'data'=>[]
                ]
            );
        }
    }
    public function addleave(Request $request)
    {
        try {
            // $todayDate = Carbon::now()->format('m/d/Y H:i:s' );
            $use_id = $request->user()->id;
            $user = User::find($use_id);
            $position = Position::find($user->position_id);
            $countDuration = 0;

            $countLeaveeDuration  = 0;
            $leftDuration = 0;
            $message = "";
            $code = 2;
            $findLeave = "";
            $case = "";
            $typeDuraton = 0;
            $d = "";
            $validator = Validator::make($request->all(), [
                'reason' => 'required',
                'from_date' => 'required',
                'to_date' => 'required',
                'leave_type_id' => 'required',
                'number' => 'required',
                'type' => 'required'

            ]);
            if ($validator->fails()) {
                $error = $validator->errors()->all()[0];
                return response()->json(
                    [
                        // 'status'=>'false',
                        'message' => $error,
                        'code' => -1,
                        // 'data'=>[]
                    ],
                    201
                );
            } else {
                $typedate = Workday::first();
                // count leave employee and calculate
                // check leavetype first
                $type = $request['type'];
                if ($type == "hour") {
                    $countDuration = $request['number'];
                    $message = "hour";
                    $code = 0;
                }
                if ($type == "half_day_m") {
                    $countDuration = 4.5;
                    $message = "half day morning";
                    $code = 0;
                }
                if ($type == "half_day_n") {
                    $countDuration = 4.5;
                    $message = "half day afternoon";
                    $code = 0;
                }
                if ($type == "day") {
                    $startDate = date('m/d/Y', strtotime($request['from_date']));
                     $endDate = date('m/d/Y', strtotime($request['to_date']));
                     $work = Workday::find($user->workday_id);
                     $total=0;
                     while ($startDate <= $endDate) {
                        $start_date = date('Y-m-d', strtotime($startDate));
                        $d = Holiday::whereDate('from_date','=',$start_date)
                        ->orWhere('to_date','<=>',$start_date)
                        ->where('status','=','pending')
                        ->first();
                        $dayOff="";
                        if($d){
                            // holiday not create data 
                            $dayOff = "false";
                            // $total=$total-1;
                        }else{
                            if ($work->off_day !=Null ) {
                                $OffDay = explode(',', $work->off_day);
                                $check = "true";
                                // $notCheck= new GetWeekday();
                                // $notCheck->getday();
                                $notCheck = $this->getWeekday($startDate);
                                // 1 = count($dayoff)
                                for ($y = 0; $y <  count($OffDay); $y++) {
                                    //   if offday = today check will false
                                    if ($OffDay[$y] == $notCheck) {
                                        $check = "false";
                                    }
                                }
                                if ($check == "false") {
                                    // day off cannot check
                                    $dayOff = "false";
                                } else {
                                    $dayOff = "true";
                                }
                            }
                            if($work->off_day ==Null){
                                $dayOff = "true";
                            }
                            if( $dayOff=='true'){
                                $total =$total +1;
                            }
                        }
                        
                        
                        $startDate = date('m/d/Y', strtotime($startDate . '+1 day'));
                    }
                    $countDuration = $total;
                    // $pastDF = Carbon::parse($request['from_date']);
                    // $pastDT = Carbon::parse($request['to_date']);
                    // $countDuration =   $pastDT->diffInDays($pastDF);
                    // $countDuration = $countDuration + 1;
                    $leaveRequest = $request['leave_type_id'];
                    $findLeave = Leave::where('user_id', '=', $user->id)

                        ->where('leave_type_id', '=', $leaveRequest)
                        ->count();
                    // $leave = count($findLeave);
                    if ($findLeave >= 1) {
                        $leavecount = Leave::where('user_id', '=', $user->id)

                            ->where('leave_type_id', '=', $leaveRequest)
                            ->get();
                        for ($i = 0; $i < count($leavecount); $i++) {
                            $countLeaveeDuration += $leavecount[$i]['number'];
                        }
                        // // to get type and duration

                        $findnewLeave = Leave::where('user_id', '=', $user->id)
                            ->where('leave_type_id', '=', $request['leave_type_id'])
                            ->first();
                        if ($findnewLeave) {
                            $findnewLeavetype = Leavetype::find($request['leave_type_id']);

                            $d = $findnewLeavetype->duration;
                            if ($findnewLeavetype->parent_id == 0) {
                                // sick or unpaid
                                if ($findnewLeavetype->duration == "0") {
                                    $code = 0;
                                } elseif (str_contains($findnewLeavetype->duration, 'month')) {
                                    // check gender if man cannot request 
                                    if($user->gender=="M"){

                                       $message = "Sorry, you don't enough permissin to request";
                                            $code = -1;
                                    }else{
                                        $findPayslip = Payslip::where('user_id', '=', $user->id)
                                        ->count();
                                        if ($findPayslip <= 0) {
                                            $message = "Sorry, you don't enough permissin to request";
                                            $code = -1;
                                        }
                                        if ($findPayslip >= 12) {
                                            $code = 0;
                                        }
                                    }
                                } else {
                                    // hospitalilty leave
                                    $typeDuraton = $findnewLeavetype->duration;
                                    if ($countLeaveeDuration >= $typeDuraton) {


                                        $message = "Sorry, you reach the limit permission";
                                        $code = -1;
                                    }
                                    if ($countLeaveeDuration < $typeDuraton) {
                                        $leftDuration = $typeDuraton - $countLeaveeDuration;
                                        // check if duration that they input ,bigger than specific duration
                                        if ($countDuration >  $leftDuration) {

                                            $message = "Please minus your input duration ";
                                            $code = -1;
                                        }
                                        if ($countDuration <=  $leftDuration) {
                                            $code = 0;
                                        }
                                    }
                                }
                            } else {
                                // special leave
                                if(str_contains($findnewLeavetype->leave_type, 'Peternity')){
                                    if($user->gender=="F"){
    
                                        $message = "Sorry, you don't enough permissin to request";
                                        $code = -1;
                                     }else{
                                        $typeDuraton = $findnewLeavetype->duration;
                                        if ($countLeaveeDuration >= $typeDuraton) {

                                            $message = "Sorry, you reach the limit permission";
                                            $code = -1;
                                            $typeDuraton = $findnewLeavetype->duration;
                                        }
                                        if ($countLeaveeDuration < $typeDuraton) {
                                            $leftDuration = $typeDuraton - $countLeaveeDuration;
                                            // check if duration that they input ,bigger than specific duration
                                            if ($countDuration >  $leftDuration) {
    
                                                $message = "Please minus your input duration";
                                                $code = -1;
                                            }
                                            if ($countDuration <=  $leftDuration) {
                                                $message = "still left leave";
                                                $code = 0;
                                            }
                                        }
                                     }
                                    
                                }
                                else{
                                    $typeDuraton = $findnewLeavetype->duration;
                                    if ($countLeaveeDuration >= $typeDuraton) {

                                        $message = "Sorry, you reach the limit permission";
                                        $code = -1;
                                        $typeDuraton = $findnewLeavetype->duration;
                                    }
                                    if ($countLeaveeDuration < $typeDuraton) {
                                        $leftDuration = $typeDuraton - $countLeaveeDuration;
                                        // check if duration that they input ,bigger than specific duration
                                        if ($countDuration >  $leftDuration) {

                                            $message = "Please minus your input duration";
                                            $code = -1;
                                        }
                                        if ($countDuration <=  $leftDuration) {
                                            $message = "still left leave";
                                            $code = 0;
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        // first request leave 
                        // check leavetype ,
                        $type = Leavetype::find($request['leave_type_id']);
                        if ($type->parent_id == 0) {
                            // sick or unpaid
                            if ($type->duration == "0") {
                                $code = 0;
                            } elseif (str_contains($type->duration, 'month')) {
                                // check gender if man cannot request 
                                    if($user->gender=="M"){

                                       $message = "Sorry, you don't enough permissin to request";
                                            $code = -1;
                                    }else{
                                        $findPayslip = Payslip::where('user_id', '=', $user->id)
                                        ->count();
                                        if ($findPayslip <= 0) {
                                            $message = "Sorry, you don't enough permissin to request";
                                            $code = -1;
                                        }
                                        if ($findPayslip >= 12) {
                                            $code = 0;
                                        }
                                    }
                            } else {
                                // hosiptilty leave
                                $typeDuraton = $type->duration;
                                if ($countLeaveeDuration >= $typeDuraton) {


                                    $message = "Sorry, you reach the limit permission";
                                    $code = -1;
                                }
                                if ($countLeaveeDuration < $typeDuraton) {
                                    $leftDuration = $typeDuraton - $countLeaveeDuration;
                                    // check if duration that they input ,bigger than specific duration
                                    if ($countDuration >  $leftDuration) {

                                        $message = "Please minus your input duration ";
                                        $code = -1;
                                    }
                                    if ($countDuration <=  $leftDuration) {



                                        $code = 0;
                                    }
                                }
                            }
                        } else {
                            // special leave
                             if(str_contains($type->leave_type, 'Peternity')){
                                if($user->gender=="F"){

                                    $message = "Sorry, you don't enough permissin to request";
                                    $code = -1;
                                 }else{
                                    $typeDuraton = $type->duration;
                                    if ($countDuration >  $typeDuraton) {
                                        $message = "Please minus your input duration";
                                        $code = -1;
                                    }
                                    if ($countDuration <=  $typeDuraton) {
                                        // $message = "still left leave";
                                        $code = 0;
                                    }
                                 }
                                
                            }else{
                                $typeDuraton = $type->duration;
                                if ($countDuration >  $typeDuraton) {
                                    $message = "Please minus your input duration";
                                    $code = -1;
                                }
                                if ($countDuration <=  $typeDuraton) {
                                    // $message = "still left leave";
                                    $code = 0;
                                }
                            }
                        }
                    }
                }
                if ($code == 0) {
                    if ($typedate->type_date_time == "server") {
                        $todayDate = Carbon::now()->format('m/d/Y H:i:s');
                        $data = Leave::create([
                            'user_id' => $user->id,
                            'reason' => $request['reason'],
                            'note' => $request['note'],
                            'type' => $request['type'],
                            // 'subtype_id' => $request['subtype_id'],
                            'leave_type_id' => $request['leave_type_id'],
                            'from_date' => $request['from_date'],
                            'to_date' => $request['to_date'],
                            'number' => $countDuration,
                            'date' => $todayDate,
                            'status' => 'pending',
                             'send_status' => 'false',
                            'image_url' => $request['image_url']
                        ]);
                    } else {
                        $data = Leave::create([
                            'user_id' => $user->id,
                            'reason' => $request['reason'],
                            'note' => $request['note'],
                            'type' => $request['type'],
                            // 'subtype_id' => $request['subtype_id'],
                            'leave_type_id' => $request['leave_type_id'],
                            'from_date' => $request['from_date'],
                            'to_date' => $request['to_date'],
                            'number' => $countDuration,
                            'date' => $request['date'],
                            'status' => 'pending',
                             'send_status' => 'false',
                            'image_url' => $request['image_url'],
                            'created_at' => $request['created_at'],
                            'updated_at' => $request['created_at'],
                        ]);
                    }

                    $respone = [
                        'message' => "Success",
                        'code' => 0,

                    ];
                    $notification = new Notice(
                        [
                            'notice' => "Leave",
                            'noticedes' => "Employee name : {$user->name}" . "\n" . "Position : {$position->position_name}" . "\n" . "Reason : " . $request['reason'] . "\n" . "From Date : " . $request['from_date']  . "\n" . "To Date :" . $request['to_date'] . "\n" . "Type : " . $request['type'] . "\n" . "Duration :" . $countDuration . "\n",
                            'telegramid' => Config::get('services.telegram_id')
                        ]
                    );
                    $notification->notify(new TelegramRegister());
                } else {
                    $respone = [
                        'message' =>  $message,
                        'code' => $code,
                        // 'duration' => $d

                    ];
                }
                return response($respone, 200);
            }
        } catch (Exception $e) {
            // return response($e ,200);
            return response()->json(
                [
                    'message' => $e->getMessage(),
                    // 'data'=>[]
                ]
            );
        }
    }
    public function getleaveChief(Request $request)
    {
        try {
            //code...
            // get user by token with
            $use_id = $request->user()->id;
            $ex = User::find($use_id);
            // $find= Department::where('manager','=', $ex->);
            $pageSize = $request->page_size ?? 10;
            $fromDate = $request->get('from_date');
            $toDate = $request->get('to_date');


            $todayDate = Carbon::now()->format('m/d/Y');




            // foreach($user as $key =>$value){
            //     $leav = Leave::select('leaves.*') 
            //     ->where('user_id', $value->id)->first();
            //     //  ->join('users','users.id','leaves.user_id')
            //     //  ->get();
            //      $value->leave = $leav;
            //  }
            // $user = Department::where('departments.id','=',$ex ->department_id)
            // ->join('users','users.department_id','=','departments.id')
            // // ->where('users.department_id','=',$ex ->department_id)
            // ->get();
            // foreach($user as $key =>$value){
            //     $leav = Leave::select('leaves.*') 
            //     ->where('user_id', $value->id)->first();
            //     //  ->join('users','users.id','leaves.user_id')
            //     //  ->get();
            //      $value->leave = $leav;
            //  }


            if ($request->has('from_date') && $request->has('to_date')) {
                // $total= Leave:: whereDate('leaves.created_at', '>=', date('Y-m-d',strtotime($fromDate)))
                // ->whereDate('leaves.created_at', '<=', date('Y-m-d',strtotime($toDate))) ->count();
                // $pending= Leave:: whereDate('leaves.created_at', '>=', date('Y-m-d',strtotime($fromDate)))
                // ->whereDate('leaves.created_at', '<=', date('Y-m-d',strtotime($toDate)))
                // ->where('status','=','pending')
                // ->count();
                // // include approve and reject
                // $complete= Leave:: whereDate('leaves.created_at', '>=', date('Y-m-d',strtotime($fromDate)))
                // ->whereDate('leaves.created_at', '<=', date('Y-m-d',strtotime($toDate)))
                // ->where('status','=','approved')
                // ->orWhere('status','=','rejected')
                // ->count();
                $user = Leave::select('leaves.*', 'leavetypes.leave_type', 'users.name')
                    ->where('status', '=', 'approved')
                    ->join('leavetypes', 'leavetypes.id', '=', 'leaves.leave_type_id')
                    ->join('users', 'users.id', '=', 'leaves.user_id')
                    ->where('users.department_id', '=', $ex->department_id)
                    ->whereDate('leaves.created_at', '>=', date('Y-m-d', strtotime($fromDate)))
                    ->whereDate('leaves.created_at', '<=', date('Y-m-d', strtotime($toDate)))
                    ->orderBy('created_at', 'DESC')->paginate($pageSize);
                // $data = Leave::with('user','leavetype')
                // ->whereDate('leaves.created_at', '>=', date('Y-m-d',strtotime($fromDate)))
                // ->whereDate('leaves.created_at', '<=', date('Y-m-d',strtotime($toDate)))
                // ->orderBy('created_at', 'DESC')->paginate($pageSize);
            } else {

                $user = Leave::select('leaves.*', 'leavetypes.leave_type', 'users.name')
                    ->join('leavetypes', 'leavetypes.id', '=', 'leaves.leave_type_id')
                    ->join('users', 'users.id', '=', 'leaves.user_id')
                    ->where('users.department_id', '=', $ex->department_id)
                    ->orderBy('created_at', 'DESC')->paginate($pageSize);
            }

            //

            // $postion = Timetable::orderBy('created_at', 'DESC')->paginate($pageSize);

            // $respone = [
            //     'message'=>'Success',
            //     'code'=>0,
            //     'data'=>$data,
            //     'total_leave'=>$total,
            //     'pending_leave'=>$pending,
            //     'complete_leave'=>$complete
            //     // 'checkin'=>$checkin,
            // ];
            return response(
                $user,
                200
            );
        } catch (Exception $e) {
            //throw $th;
            return response([
                'message' => $e->getMessage()
            ]);
        }
    }
    public function editLeaveChief(Request $request, $id)
    {
        try {
            //code...
            // get user by token with timetable

            $data = Leave::find($id);
            $todayDate = Carbon::now()->format('m/d/Y');
            $timeNow = Carbon::now()->format('H:i:s');
            $leaveDuction = 0;
            $status = "";
            $message = "";
            if ($data) {
                $validator = Validator::make($request->all(), [

                    'status' => 'required',
                    'leave_deduction' => 'required',

                ]);
                if ($validator->fails()) {
                    $error = $validator->errors()->all()[0];
                    return response()->json(
                        [
                            'message' => $error,
                            'code' => -1,
                        ],
                        201
                    );
                } else {
                    if ($request["status"] == 'pending') {
                        $status = 'pending';
                    }
                    if ($request["status"] == 'approved') {
                        $status = 'approved';
                    } else {
                        $status = 'rejected';
                    }
                    $data->status = $status;


                    if ($request["leave_deduction"]) {
                        $leaveDuction = $request["leave_deduction"];
                    } else {
                        $leaveDuction = 0;
                    }
                    $data->leave_deduction = $leaveDuction;
                    $query = $data->update();
                    $profile = User::find($data->user_id);
                    //  $startDate = date('Y-m-d',strtotime($data->from_date));
                    $startDate = date('m/d/Y', strtotime($data->from_date));
                    $endDate = date('m/d/Y', strtotime($data->to_date));
                    if ($data->status == "rejected") {
                        $message = "Your leave request has been rejected!";
                        $respone = [
                            'message' => 'Success',
                            'code' => 0,
                        ];
                    }
                    if ($data->status == 'approved') {
                        // check type of leave late 1 hour, permission half day or 1 day 2 day
                        if ($data->type == "hour") {
                            // if one hour 

                        }
                        // half_day_m == "morning"
                        if ($data->type == "half_day_m") {
                            $att = new Checkin();
                            $att->user_id = $profile->id;
                            $att->status = 'scanned';
                            $att->checkin_time = "0";
                            // $att->checkout_time = "0";
                            $att->checkin_late = "0";
                            // $att->checkout_late = "0";
                            $att->checkin_status = "permission halfday morning";
                            // $att->checkout_status = "leave";
                            $att->date = $startDate;
                            $att->created_at = $startDate;
                            $att->save();
                            // $startDate=date('m/d/Y',strtotime($startDate.'+1 day'));
                            // $respone = [
                            //     'message' => 'Success',
                            //     'code' => 0,
                            // ];
                        }
                        // half_day_n :afernoon = checkout
                        if ($data->type == "half_day_n") {
                            $att = new Checkin();
                            $att->user_id = $profile->id;
                            // $att->status='permission half-day afternoon';
                            // $att->checkin_time = "0";
                            $att->checkout_time = "0";
                            // $att->checkin_late = "0";
                            $att->checkout_late = "0";
                            // $att->checkin_status = "permission half-day afternoon";
                            $att->checkout_status = "permission half-day afternoon";
                            $att->date = $startDate;
                            $att->created_at = $startDate;
                            $att->save();
                            // $startDate=date('m/d/Y',strtotime($startDate.'+1 day'));

                        }
                        if ($data->type == "day") {
                            while ($startDate <= $endDate) {
                                $att = new Checkin();
                                $att->user_id = $profile->id;
                                $att->status = 'leave';
                                $att->checkin_time = "0";
                                $att->checkout_time = "0";
                                $att->checkin_late = "0";
                                $att->checkout_late = "0";
                                $att->checkin_status = "leave";
                                $att->checkout_status = "leave";
                                $att->date = $startDate;
                                $att->created_at = $startDate;
                                $att->save();
                                $startDate = date('m/d/Y', strtotime($startDate . '+1 day'));
                            }

                            $respone = [
                                'message' => 'Success',
                                'code' => 0,
                            ];
                        }
                        $message = "your leave request has been approved!";
                        $respone = [
                            'message' => 'Success',
                            'code' => 0,
                        ];
                    }
                    if ($profile->device_token) {
                        $url = 'https://fcm.googleapis.com/fcm/send';
                        $dataArr = array(
                            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                            'id' => $request->id,
                            'status' => "done",

                        );
                        $notification = array(
                            'title' => $message,
                            'text' => $message,
                            // 'isScheduled' => "true",
                            // 'scheduledTime' => "2022-06-14 17:55:00",
                            'sound' => 'default',
                            'badge' => '1',
                        );
                        // "registration_ids" => $firebaseToken,
                        $arrayToSend = array(
                            "priority" => "high",
                            // "token"=>"7|Syty8L1QioCvQDQpl0axkahssTg542OE5HNCOpke",
                            // 'to'=>"/topics/6|bY5aVLz32sZrYIGjqpCqDUsRzFxopG8LgyRi0UOo",  
                            'to' => $profile->device_token,
                            // 'registration_ids'=>'6|bY5aVLz32sZrYIGjqpCqDUsRzFxopG8LgyRi0UOo',
                            'notification' => $notification,
                            'data' => $dataArr,
                            'priority' => 'high'
                        );
                        $fields = json_encode($arrayToSend);
                        $headers = array(
                            'Authorization: key=' . "AAAAqP0mBoo:APA91bEHUWxz5ZkOeZXpeoMSYtjQMdY8WCQyZSi7I5ycQJ3T6yUhqofYZ5w3AjCpjYSLm54Z3xTR3rsT7cLQ_L1xk7VNhODQDXi4GpxfRaDUH8eoefKuegD9_gx3IxKHIsFlLp8dcHe8",
                            'Content-Type: application/json'
                        );
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                        $result = curl_exec($ch);
                        // var_dump($result);
                        curl_close($ch);
                    }
                }
            } else {
                $respone = [
                    'message' => 'No leave id found',
                    'code' => -1,


                ];
            }

            return response(
                $respone,
                200
            );
        } catch (Exception $e) {

            return response([
                'message' => $e->getMessage()
            ]);
        }
    }
    public function editleave(Request $request, $id)
    {
        try {
            // $todayDate = Carbon::now()->format('m/d/Y H:i:s' );
            $use_id = $request->user()->id;
            $user = User::find($use_id);
            $countDuration = 0;
            $countDuration = 0;

            $countLeaveeDuration  = 0;
            $leftDuration = 0;
            $message = "";
            $code = 2;
            $findLeave = "";
            $case = "";
            $typeDuraton = 0;
            $d = "";
            $position = Position::find($user->position_id);
            $data = Leave::find($id);
            if ($data) {
                $validator = Validator::make($request->all(), [
                    'reason' => 'required',
                    'from_date' => 'required',
                    'to_date' => 'required',
                    'leave_type_id' => 'required',
                    'number' => 'required',
                    'type' => 'required',
                ]);
                if ($validator->fails()) {
                    $error = $validator->errors()->all()[0];
                    return response()->json(
                        [

                            'message' => $error,
                            'code' => -1,

                        ],
                        200
                    );
                } else {
                    $typedate = Workday::first();

                    // $data = Leave::where('user_id', $user->id)
                    //     ->where('id', $leave->id)
                    //     ->latest()
                    //     ->first();
                    if ($data->status == "pending") {
                        $type = $request['type'];
                        if ($type == "hour") {
                            $countDuration = $request['number'];
                            $message = "hour";
                            $code = 0;
                        }
                        if ($type == "half_day_m") {
                            $countDuration = 4;
                            $message = "half day morning";
                            $code = 0;
                        }
                        if ($type == "half_day_n") {
                            $countDuration = 4;
                            $message = "half day afternoon";
                            $code = 0;
                        }
                        if ($type == "day") {
                            $startDate = date('m/d/Y', strtotime($request['from_date']));
                            $endDate = date('m/d/Y', strtotime($request['to_date']));
                            $work = Workday::find($user->workday_id);
                            $total=0;
                            while ($startDate <= $endDate) {
                                $start_date = date('Y-m-d', strtotime($startDate));
                                $d = Holiday::whereDate('from_date','=',$start_date)
                                ->orWhere('to_date','<=>',$start_date)
                                ->where('status','=','pending')
                                ->first();
                                $dayOff="";
                                if($d){
                                    // holiday not create data 
                                    $dayOff = "false";
                                    // $total=$total-1;
                                }else{
                                    if ($work->off_day !=Null ) {
                                        $OffDay = explode(',', $work->off_day);
                                        $check = "true";
                                        // $notCheck= new GetWeekday();
                                        // $notCheck->getday();
                                        $notCheck = $this->getWeekday($startDate);
                                        // 1 = count($dayoff)
                                        for ($y = 0; $y <  count($OffDay); $y++) {
                                            //   if offday = today check will false
                                            if ($OffDay[$y] == $notCheck) {
                                                $check = "false";
                                            }
                                        }
                                        if ($check == "false") {
                                            // day off cannot check
                                            $dayOff = "false";
                                        } else {
                                            $dayOff = "true";
                                        }
                                    }
                                    if($work->off_day ==Null){
                                        $dayOff = "true";
                                    }
                                    if( $dayOff=='true'){
                                        $total =$total +1;
                                    }
                                }
                                
                                
                                $startDate = date('m/d/Y', strtotime($startDate . '+1 day'));
                            }
                            $countDuration = $total;
                            // $pastDF = Carbon::parse($request['from_date']);
                            // $pastDT = Carbon::parse($request['to_date']);
                            // $countDuration =   $pastDT->diffInDays($pastDF);
                            // $countDuration = $countDuration + 1;
                            $leaveRequest = $request['leave_type_id'];
                            $findLeave = Leave::where('user_id', '=', $user->id)


                                ->where('leave_type_id', '=', $leaveRequest)
                                ->where('status', '=', 'approved')
                                ->count();
                            // $leave = count($findLeave);
                            if ($findLeave >= 1) {
                                $leavecount = Leave::where('user_id', '=', $user->id)

                                    ->where('leave_type_id', '=', $leaveRequest)
                                    ->get();
                                for ($i = 0; $i < count($leavecount); $i++) {
                                    $countLeaveeDuration += $leavecount[$i]['number'];
                                }
                                // to get type and duration

                                $findnewLeave = Leave::where('user_id', '=', $user->id)
                                    ->where('leave_type_id', '=', $request['leave_type_id'])
                                    ->first();
                                if ($findnewLeave) {
                                    $findnewLeavetype = Leavetype::find($request['leave_type_id']);
                                    // sick leave and unpaid leave

                                    $d = $findnewLeavetype->duration;
                                    if ($findnewLeavetype->parent_id == 0) {
                                        // sick or unpaid
                                        if ($findnewLeavetype->duration == "0") {
                                            $code = 0;
                                        } elseif (str_contains($findnewLeavetype->duration, 'month')) {
                                            if($user->gender=="M"){
        
                                                $message = "Sorry, you reach the limit permission";
                                                 $code = -1;
                                             }else{
                                                 $findPayslip = Payslip::where('user_id', '=', $user->id)
                                                 ->count();
                                                 if ($findPayslip <= 0) {
                                                     $message = "Sorry, you don't enough permissin to request";
                                                     $code = -1;
                                                 }
                                                 if ($findPayslip >= 12) {
                                                     $code = 0;
                                                 }
                                             }
                                        } else {
                                            // hospitalilty leave
                                            $typeDuraton = $findnewLeavetype->duration;
                                            if ($countLeaveeDuration >= $typeDuraton) {


                                                $message = "Sorry, you reach the limit permission";
                                                $code = -1;
                                            }
                                            if ($countLeaveeDuration < $typeDuraton) {
                                                $leftDuration = $typeDuraton - $countLeaveeDuration;
                                                // check if duration that they input ,bigger than specific duration
                                                if ($countDuration >  $leftDuration) {

                                                    $message = "Please minus your input duration ";
                                                    $code = -1;
                                                }
                                                if ($countDuration <=  $leftDuration) {



                                                    $code = 0;
                                                }
                                            }
                                        }
                                    } else {
                                        // special leave
                                        if(str_contains($findnewLeavetype->leave_type, 'Peternity')){
                                            if($user->gender=="F"){
            
                                                $message = "Sorry, you don't enough permissin to request";
                                                $code = -1;
                                             }else{
                                                $typeDuraton = $findnewLeavetype->duration;
                                                if ($countLeaveeDuration >= $typeDuraton) {
        
                                                    $message = "Sorry, you reach the limit permission";
                                                    $code = -1;
                                                    $typeDuraton = $findnewLeavetype->duration;
                                                }
                                                if ($countLeaveeDuration < $typeDuraton) {
                                                    $leftDuration = $typeDuraton - $countLeaveeDuration;
                                                    // check if duration that they input ,bigger than specific duration
                                                    if ($countDuration >  $leftDuration) {
            
                                                        $message = "Please minus your input duration";
                                                        $code = -1;
                                                    }
                                                    if ($countDuration <=  $leftDuration) {
                                                        $message = "still left leave";
                                                        $code = 0;
                                                    }
                                                }
                                             }
                                            
                                        }
                                        else{
                                            $typeDuraton = $findnewLeavetype->duration;
                                            if ($countLeaveeDuration >= $typeDuraton) {
        
                                                $message = "Sorry, you reach the limit permission";
                                                $code = -1;
                                                $typeDuraton = $findnewLeavetype->duration;
                                            }
                                            if ($countLeaveeDuration < $typeDuraton) {
                                                $leftDuration = $typeDuraton - $countLeaveeDuration;
                                                // check if duration that they input ,bigger than specific duration
                                                if ($countDuration >  $leftDuration) {
        
                                                    $message = "Please minus your input duration";
                                                    $code = -1;
                                                }
                                                if ($countDuration <=  $leftDuration) {
                                                    $message = "still left leave";
                                                    $code = 0;
                                                }
                                            }
                                        }
                                    }
                                }
                            } else {

                                // leave status pending
                                $Leave = Leave::where('user_id', '=', $user->id)

                                    ->where('leave_type_id', '=', $leaveRequest)
                                    ->where('status', '=', 'pending')
                                    ->get();
                                // first request leave 
                                // check leavetype ,  



                                if (count($Leave) > 0) {
                                    for ($i = 0; $i < count($Leave); $i++) {
                                        if ($Leave[$i]['id'] == $data->leave_type_id) {
                                            $countLeaveeDuration = 0;
                                        } else {
                                            $countLeaveeDuration += $Leave[$i]['number'];
                                        }
                                    }
                                    $findnewLeave = Leave::where('user_id', '=', $user->id)
                                        ->where('leave_type_id', '=', $request['leave_type_id'])
                                        ->where('status', '=', 'pending')
                                        ->first();
                                    if ($findnewLeave) {
                                        $type = Leavetype::find($request['leave_type_id']);
                                        if ($type->parent_id == 0) {
                                            // sick or unpaid
                                            if ($type->duration == "0") {
                                                $code = 0;
                                            } elseif (str_contains($type->duration, 'month')) {
                                                if($user->gender=="M"){
        
                                                    $message = "Sorry, you reach the limit permission";
                                                     $code = -1;
                                                 }else{
                                                     $findPayslip = Payslip::where('user_id', '=', $user->id)
                                                     ->count();
                                                     if ($findPayslip <= 0) {
                                                         $message = "Sorry, you don't enough permissin to request";
                                                         $code = -1;
                                                     }
                                                     if ($findPayslip >= 12) {
                                                         $code = 0;
                                                     }
                                                 }
                                            } else {
                                                // hosiptilty leave
                                                $typeDuraton = $type->duration;
                                                if ($countLeaveeDuration >= $typeDuraton) {


                                                    $message = "Sorry, you reach the limit permission";
                                                    $code = -1;
                                                }
                                                if ($countLeaveeDuration < $typeDuraton) {
                                                    $leftDuration = $typeDuraton - $countLeaveeDuration;
                                                    // check if duration that they input ,bigger than specific duration
                                                    if ($countDuration >  $leftDuration) {

                                                        $message = "Please minus your input duration ";
                                                        $code = -1;
                                                    }
                                                    if ($countDuration <=  $leftDuration) {



                                                        $code = 0;
                                                    }
                                                }
                                            }
                                        } else {
                                            if(str_contains($type->leave_type, 'Peternity')){
                                                if($user->gender=="F"){
                                                    $message = "Sorry, you don't enough permissin to request";
                                                    $code = -1;
                                                }else{
                                                    $typeDuraton = $type->duration;
                                                    // $typeDuraton = $type->duration;
                                                    if ($countDuration >  $typeDuraton) {
                                                        $message = "Please minus your input duration";
                                                        $code = -1;
                                                    }
                                                    if ($countDuration <=  $typeDuraton) {
                                                        // $message = "still left leave";
                                                        $code = 0;
                                                    }
                                                }
                                            }else{
                                                $typeDuraton = $type->duration;
                                                // $typeDuraton = $type->duration;
                                                if ($countDuration >  $typeDuraton) {
                                                    $message = "Please minus your input duration";
                                                    $code = -1;
                                                }
                                                if ($countDuration <=  $typeDuraton) {
                                                    // $message = "still left leave";
                                                    $code = 0;
                                                }
                                            }
                                            // special leave
                                            
                                        }
                                    }
                                } else {
                                    $type = Leavetype::find($request['leave_type_id']);
                                    if ($type->parent_id == 0) {
                                        // sick or unpaid
                                        if ($type->duration == "0") {
                                            $code = 0;
                                        } elseif (str_contains($type->duration, 'month')) {
                                            if($user->gender=="M"){
        
                                                $message = "Sorry, you reach the limit permission";
                                                 $code = -1;
                                             }else{
                                                 $findPayslip = Payslip::where('user_id', '=', $user->id)
                                                 ->count();
                                                 if ($findPayslip <= 0) {
                                                     $message = "Sorry, you don't enough permissin to request";
                                                     $code = -1;
                                                 }
                                                 if ($findPayslip >= 12) {
                                                     $code = 0;
                                                 }
                                             }
                                        } else {
                                            // hosiptilty leave
                                            $typeDuraton = $type->duration;
                                            if ($countLeaveeDuration >= $typeDuraton) {


                                                $message = "Sorry, you reach the limit permission";
                                                $code = -1;
                                            }
                                            if ($countLeaveeDuration < $typeDuraton) {
                                                $leftDuration = $typeDuraton - $countLeaveeDuration;
                                                // check if duration that they input ,bigger than specific duration
                                                if ($countDuration >  $leftDuration) {

                                                    $message = "Please minus your input duration ";
                                                    $code = -1;
                                                }
                                                if ($countDuration <=  $leftDuration) {



                                                    $code = 0;
                                                }
                                            }
                                        }
                                    } else {
                                        if(str_contains($type->leave_type, 'Peternity')){
                                            if($user->gender=="F"){
                                                $message = "Sorry, you don't enough permissin to request";
                                                $code = -1;
                                            }else{
                                                $typeDuraton = $type->duration;
                                                // $typeDuraton = $type->duration;
                                                if ($countDuration >  $typeDuraton) {
                                                    $message = "Please minus your input duration";
                                                    $code = -1;
                                                }
                                                if ($countDuration <=  $typeDuraton) {
                                                    // $message = "still left leave";
                                                    $code = 0;
                                                }
                                            }
                                        }else{
                                            $typeDuraton = $type->duration;
                                            // $typeDuraton = $type->duration;
                                            if ($countDuration >  $typeDuraton) {
                                                $message = "Please minus your input duration";
                                                $code = -1;
                                            }
                                            if ($countDuration <=  $typeDuraton) {
                                                // $message = "still left leave";
                                                $code = 0;
                                            }
                                        }
                                    }
                                }
                                // $d= $type->duration;
                                // $contain = str_contains($d, "month");
                                // maternity leave (delivery baby)

                            }
                        }
                        if ($code == 0) {
                            if ($typedate->type_date_time == "server") {
                                $todayDate = Carbon::now()->format('m/d/Y H:i:s');
                                $data->user_id = $user->id;
                                $data->reason = $request['reason'];
                                $data->note = $request['note'];
                                $data->type = $request['type'];
                                $data->subtype = $request['subtype'];
                                $data->leave_type_id = $request['leave_type_id'];
                                $data->from_date = $request['from_date'];
                                $data->to_date = $request['to_date'];
                                $data->number =  $countDuration;
                                $data->image_url = $request['image_url'];
                                $data->status = 'pending';
                                $data->date =  $todayDate;
                                $data->update();
                            } else {
                                $data->user_id = $user->id;
                                $data->reason = $request['reason'];
                                $data->note = $request['note'];
                                $data->type = $request['type'];
                                $data->subtype = $request['subtype'];
                                $data->leave_type_id = $request['leave_type_id'];
                                $data->from_date = $request['from_date'];
                                $data->to_date = $request['to_date'];
                                $data->number =  $countDuration;
                                $data->image_url = $request['image_url'];
                                $data->status = 'pending';
                                $data->date =   $request['date'];
                                $data->update();
                            }


                            $respone = [
                                'message' => "Success",
                                'code' => 0,
                                // 'duraton'=>$d,

                            ];
                        } else {
                            $respone = [
                                'message' =>  $message,
                                'code' => $code,
                                // 'duraton'=>$d,


                            ];
                        }
                    }

                    return response($respone, 200);
                }
            } else {
                return response()->json(
                    [

                        'message' => "No leave id found",
                        'code' => -1,

                    ],
                    200
                );
            }
        } catch (Exception $e) {

            return response()->json(
                [
                    'message' => $e->getMessage(),

                ]
            );
        }
    }
    public function deleteLeave(Request $request, $id)
    {
        try {
            $data = Leave::find($id);
            $use_id = $request->user()->id;
            $user = User::find($use_id);

            if ($data) {
                // check if position id belong to employee table
                $findEm = Leave::where('user_id', $user->id)->where('id', $id)->latest()->first();

                if ($findEm) {

                    if ($findEm->status == "pending") {
                        $data->delete();
                        $respone = [
                            'message' => 'Success',
                            'code' => 0,
                        ];
                    } else {
                        $respone = [
                            'message' => 'no permission to delete',
                            'code' => -1,
                            // 'data'=>$findEm,
                            // 'id' =>$id
                        ];
                    }
                } else {
                    $respone = [
                        'message' => 'No employee id found',
                        'code' => -1,
                    ];
                }
            } else {
                $respone = [
                    'message' => 'No leave id found',
                    'code' => -1,
                ];
            }
            return response()->json(
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
    public function notification(Request $request)
    {

        try {
            $pageSize = $request->page_size ?? 10;
            //code...
            $data = Notification::paginate($pageSize);

            return response()->json(
                $data,
                200
            );
        } catch (Exception $e) {
            // return response($e ,200);
            return response()->json(
                [
                    'message' => $e->getMessage(),
                    // 'data'=>[]
                ]
            );
        }
    }
    // for push local notification
    public function editFcmUser(Request $request)
    {
        try {
            $use_id = $request->user()->id;
            $validator = Validator::make($request->all(), [
                'device_token' => 'required',
            ]);
            if ($validator->fails()) {
                $error = $validator->errors()->all()[0];
                return response()->json(
                    [
                        // 'status'=>'false',
                        'message' => $error,
                        'code' => -1,
                        // 'data'=>[]
                    ],
                    201
                );
            } else {
                $data = User::find($use_id);
                if ($data) {
                    $data->device_token = $request["device_token"];
                    $data->save();
                    $response = [
                        'message' => "Success",
                        'code' => 0,

                    ];
                } else {
                    $response = [
                        'message' => "No user id found",
                        'code' => -1,

                    ];
                }
                return response()->json(
                    $response,
                    200
                );
            }
        } catch (Exception $e) {
            // return response($e ,200);
            return response()->json(
                [
                    'message' => $e->getMessage(),
                    // 'data'=>[]
                ]
            );
        }
    }
    public function create()
    {
        //
    }
    public function getcheckinlist(Request $request)
    {
        try {

            $user_id = $request->user()->id;
            // $employee = User::find($use_id);
            $fromDate = $request->get('from_date');
            $toDate = $request->get('to_date');
            $todayDate = Carbon::now()->format('m/d/Y');
            $pageSize = $request->page_size ?? 10;
            //

            if ($request->has('from_date') && $request->has('to_date')) {


                $data  = Checkin::where('user_id', $user_id)
                    ->whereDate('checkins.created_at', '>=', date('Y-m-d', strtotime($fromDate)))
                    ->whereDate('checkins.created_at', '<=', date('Y-m-d', strtotime($toDate)))
                    ->orderBy('created_at', 'ASC')
                    ->paginate($pageSize);
            } else {
                $data = Checkin::where('user_id', $user_id)
                    ->orderBy('created_at', 'ASC')
                    ->paginate($pageSize);
            }

            return response()->json(
                $data,
                200
            );
        } catch (Exception $e) {
            //throw $th;
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }

        // $data = Checkin::with('timetable','department','position','store','store.location')->find($use_id);
    }
    public function getReport(Request $request)
    {
        try {

            $user_id = $request->user()->id;
            // $employee = User::find($use_id);
            $fromDate = $request->get('from_date');
            $toDate = $request->get('to_date');
            $todayDate = Carbon::now()->format('m/d/Y');
            $pageSize = $request->page_size ?? 10;
            $checkin = 0;
            $leave = "";
            $totalLeave = 0;
            $duction = 0;
            $leave_deduction = 0;
            $leaveout = 0;
            $cash = 0;
            $otHour = 0;
            $ot = "";
            $absent=0;
            //
            $totalOt = 0;

            if ($request->has('from_date') && $request->has('to_date')) {


                $checkin  = Checkin::where('user_id', $user_id)
                    ->whereDate('checkins.created_at', '>=', date('Y-m-d', strtotime($fromDate)))
                    ->whereDate('checkins.created_at', '<=', date('Y-m-d', strtotime($toDate)))
                    ->where('status', 'present')

                    ->count();
                $absent  = Checkin::where('user_id', $user_id)
                    ->whereDate('checkins.created_at', '>=', date('Y-m-d', strtotime($fromDate)))
                    ->whereDate('checkins.created_at', '<=', date('Y-m-d', strtotime($toDate)))
                    ->where('status', 'absent')

                    ->count();
                $leave  = Leave::where('user_id', $user_id)
                    ->whereDate('leaves.created_at', '>=', date('Y-m-d', strtotime($fromDate)))
                    ->whereDate('leaves.created_at', '<=', date('Y-m-d', strtotime($toDate)))
                    ->where('status', 'approved')
                    ->get();
                $totalLeave = count($leave);
                $duction = 0;
                for ($i = 0; $i < count($leave); $i++) {
                    if ($leave[$i]['leave_deduction']) {
                        $duction += $leave[$i]['leave_deduction'];
                    }
                }
                $leave_deduction = $duction;
                $leaveout  = Leaveout::where('user_id', $user_id)
                    ->whereDate('leaveouts.created_at', '>=', date('Y-m-d', strtotime($fromDate)))
                    ->whereDate('leaveouts.created_at', '<=', date('Y-m-d', strtotime($toDate)))
                    ->where('status', 'approved')
                    ->count();
                $ot  = Overtime::where('user_id', $user_id)
                    ->whereDate('overtimes.created_at', '>=', date('Y-m-d', strtotime($fromDate)))
                    ->whereDate('overtimes.created_at', '<=', date('Y-m-d', strtotime($toDate)))
                    ->where('status', 'completed')
                    ->get();
                $cash = 0;
                $holiday = 0;
                $otHour = 0;
                for ($i = 0; $i < count($ot); $i++) {
                    $otHour += $ot[$i]['ot_hour'];
                    if ($ot[$i]['pay_type'] == 'cash') {
                        $cash += $ot[$i]['total_ot'];
                    } else {
                        $holiday += $ot[$i]['ot_hour'];
                    }
                }
                $totalOt = count($ot);
            } else {
                $checkin  = Checkin::where('user_id', $user_id)
                    ->whereDate('checkins.created_at', '>=', date('Y-m-d', strtotime($fromDate)))
                    ->whereDate('checkins.created_at', '<=', date('Y-m-d', strtotime($toDate)))
                    ->where('status', 'present')
                    ->orWhere('status', 'checkin')
                    ->count();
                $absent  = Checkin::where('user_id', $user_id)
                    ->whereDate('checkins.created_at', '>=', date('Y-m-d', strtotime($fromDate)))
                    ->whereDate('checkins.created_at', '<=', date('Y-m-d', strtotime($toDate)))
                    ->where('status', 'absent')
                    
                    ->count();
                $leave  = Leave::where('user_id', $user_id)

                    ->where('status', 'approved')
                    ->get();
                $totalLeave = count($leave);
                $duction = 0;
                for ($i = 0; $i < count($leave); $i++) {
                    if ($leave[$i]['leave_deduction']) {
                        $duction += $leave[$i]['leave_deduction'];
                    }
                }
                $leave_deduction = $duction;
                $leaveout  = Leaveout::where('user_id', $user_id)

                    ->where('status', 'approved')
                    ->count();
                $ot  = Overtime::where('user_id', $user_id)

                    ->where('status', 'completed')
                    ->get();
                $cash = 0;
                $holiday = 0;
                $otHour = 0;
                for ($i = 0; $i < count($ot); $i++) {
                    for ($i = 0; $i < count($ot); $i++) {
                        $otHour += $ot[$i]['ot_hour'];
                        if ($ot[$i]['pay_type'] == 'cash') {
                            $cash += $ot[$i]['total_ot'];
                        } else {
                            $holiday += $ot[$i]['ot_hour'];
                        }
                    }
                }
                $totalOt = count($ot);
            }

            return response()->json(
                [
                    'attendance' => $checkin,
                    'absent'=>$absent,
                    'leave' => $totalLeave,
                    'leave_duction' => $leave_deduction,
                    'leaveout' => $leaveout,
                    'total_ot' => $totalOt,
                    'ot_cash' => $cash,
                    'ot_hour' => $otHour,
                    'ot_holiday'=>$holiday

                ],
                200
            );
        } catch (Exception $e) {
            //throw $th;
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }

        // $data = Checkin::with('timetable','department','position','store','store.location')->find($use_id);
    }
    public function updateprofile(Request $request)
    {
        try {
            //code...
            // get user by token with timetable
            $user_id = $request->user()->id;
            $data = User::find($user_id);
            if ($data) {
                $data->profile_url = $request->profile_url;
                $data->save();
                $respone = [
                    'message' => 'Success',
                    'code' => 0,

                ];
            } else {
                $respone = [
                    'message' => 'Wrong user id',
                    'code' => 0,

                ];
            }
            return response()->json(
                $respone,
                200
            );
            // $validator = Validator::make($request->all(), [
            //     'name' => 'required|string',
            //     'gender' => 'required',
            //     'store_id' => 'required',

            //     'department_id' => 'required',
            //     // 'timetable_id' => 'required',
            //     'position_id' => 'required',
            //     'password' => 'required'
            // ]);
            // if ($validator->fails()) {
            //     $error=$validator->errors()->all()[0];
            //     return response()->json(
            //         [
            //             // 'status'=>'false',
            //             'message'=>$error,
            //             'code'=>-1,
            //             // 'data'=>[]
            //         ],201
            //     );

            // }else{
            //     // $query = $request->all();
            //     // $data->update( $query);
            //     //  $respone = [
            //     //      'message'=>'Success',
            //     //      'code'=>0,

            //     //  ];


            //     // return response($respone ,200);

            // }
        } catch (Exception $e) {
            //throw $th;
            return response([
                'message' => $e->getMessage()
            ]);
        }
    }
    public function getLocation(Request $request)
    {
        // $data = Location::first();
        $latitude = 11.5791579;
        $longitude = 104.8682052;
        $radius = 20000;
        // 6371 is Earth radius in km.)


        $data = Location::selectRaw("*,
                    ( 6371 * acos( cos( radians(" . $latitude . ") ) *
                    cos( radians(lat) ) *
                    cos( radians(lon) - radians(" . $longitude . ") ) +
                    sin( radians(" . $latitude . ") ) *
                    sin( radians(lat) ) ) )
                    AS distance")

            ->first();

        $km = 1.0;
        // output 7km
        // calculate km
        try {
            if ($data['distance'] <= 0.5) {
                return  response()->json(
                    [
                        'code' => 0,
                        'data' => $data
                    ]
                );
            } else {
                return  response()->json(
                    [
                        'code' => -1,
                        'data' => "grater than 0.5 km"
                    ]
                );
            }
        } catch (Exception $e) {
            //throw $th;
            return  response()->json(
                [
                    'code' => 0,
                    'message' => $e->getMessage()
                ]
            );
        }
    }


    public function store(Request $request)
    {
        //
    }
    public function employeeUploads(Request $request)
    {
        try {

            // file(file):keywork in postman, if put photo , file(photo)
            $uploadedFileUrl = Cloudinary::upload($request->file('file')->getRealPath(), [
                'folder' => 'employee'
            ])->getSecurePath();
            //$result =  $request->file('file')->store('uploads/employee', 'photo');

            //    $request->file('photo')->store('uploads/employee','photo');
            $respone = [
                'message' => 'Sucess',
                'code' => 0,
                'profile_url' => $uploadedFileUrl

            ];


            return response()->json(
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
    // cheif department
    public function requestOvertime(Request $request)
    {
        try {
            // $todayDate = Carbon::now()->format('m/d/Y');
            $use_id = $request->user()->id;
            $ex = User::find($use_id);
            $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'number' => 'required',
                'type' => 'required',
                'reason' => 'required',
                'number' => 'required',
                'from_date' => 'required',
                'to_date' => 'required',
            ]);
            if ($validator->fails()) {
                $error = $validator->errors()->all()[0];
                return response()->json(
                    [
                        // 'status'=>'false',
                        'message' => $error,
                        'code' => -1,
                        // 'data'=>[]
                    ],
                    201
                );
            } else {
                $findEm = User::find($request['user_id']);
                $otRate = 0;
                $otHour = 0;
                // $otMethod = 0;
                // $total = 0;
                $totalDuration = 0;
                $contract = Contract::where('user_id', $request['user_id'])->first();
                $work = Workday::find($findEm->workday_id);
                // $typedate= Workday::first();
                $standarHour = 0;
                if ($contract) {
                    if ($work->off_day != Null) {
                        $workday = explode(',', $work->off_day);
                        if (count($workday) == 1) {
                            $standarHour = 26;
                        }
                        if (count($workday) == 2) {
                            $standarHour = 22;
                        }
                    } else {
                        // send extra to counter
                        // this day , take salary and devided to find one day salary 
                        $standarHour = 26;
                        // 4 day , add more with 26 day 
                        // $extra = 4;

                        // cannot use standard 30 even though user cancel day off
                    }
                    $structure = Structure::find($contract->structure_id);

                    $baseSalary = $structure->base_salary;
                    $SalaryOneday =  $baseSalary / $standarHour;
                    // $standarHour = 44;
                    // $standarHour =        $contract->working_schedule;
                    $SalaryOneHour =  $SalaryOneday / 9;
                    $duration = 0;
                    $duration_in_days = 0;
                    $total = 1;
                    if ($request['type'] == "hour") {
                        $duration = $request['number'];
                        $totalDuration = $duration;
                    } else {
                        // check date 
                        if ($request['from_date'] == $request['to_date']) {
                            $duration = 9;
                            $totalDuration = 1;
                        } else {
                            $dateFrom = "2022-07-21";
                            $dateTo = "2022-07-22";
                            $pastDF = Carbon::parse($request['from_date']);
                            $pastDT = Carbon::parse($request['to_date']);
                            $duration_in_days =   $pastDT->diffInDays($pastDF);
                            $totalDuration = $duration_in_days + 1;
                            // out put 1 ,but reality 2 
                            // $duration_in_days = $request->to_date->diffInDays($request->from_date);
                            // $duration= $duration_in_days;
                            $duration = ($duration_in_days + 1) * 9;
                        }
                    }
                    $otMethod = 1;
                    if ($request['ot_method']) {
                        $otMethod = $request['ot_method'];
                        if ($duration >= 9) {
                            $total = $SalaryOneday * $totalDuration *  $otMethod;
                        } else {
                            $total = $SalaryOneHour *  $totalDuration *  $otMethod;
                        }
                        $total = round($total, 2);
                        // $otRate =$request->ot_rate;
                        // $otHour = $request->ot_hour;


                    } else {
                        if ($duration >= 9) {
                            $total = $SalaryOneday * $totalDuration;
                        } else {
                            $total = $SalaryOneHour *  $totalDuration;
                        }
                        $total = round($total, 2);
                    }
                    // 
                    if ($work->type_date_time == "server") {
                        $todayDate = Carbon::now()->format('m/d/Y');
                        $user = Overtime::create([
                            'user_id' => $request['user_id'],
                            'reason' => $request['reason'],
                            'status' => 'pending',
                            'date' => $todayDate,
                            'number' => $totalDuration,
                            'type' => $request['type'],
                            'from_date' => $request['from_date'],
                            'to_date' => $request['to_date'],
                            'ot_rate' => round($SalaryOneHour, 2),
                            'ot_hour' => $duration,
                            'ot_method' => $otMethod,
                            'total_ot' => $total,
                            'pay_status' => 'pending',
                            'requested_by' => $ex->name,
                            'notes' => $request['note'],
                            'send_status' => 'false',

                        ]);
                    } else {
                        $user = Overtime::create([
                            'user_id' => $request['user_id'],
                            'reason' => $request['reason'],
                            'status' => 'pending',
                            'date' => $request['date'],
                            'number' => $totalDuration,
                            'type' => $request['type'],
                            'from_date' => $request['from_date'],
                            'to_date' => $request['to_date'],
                            'ot_rate' => round($SalaryOneHour, 2),
                            'ot_hour' => $duration,
                            'ot_method' => $otMethod,
                            'total_ot' => $total,
                            'pay_status' => 'pending',
                            'requested_by' => $ex->name,
                            'notes' => $request['note'],
                            'send_status' => 'false',
                            'created_at' => $request['created_at'],
                            'updated_at' => $request['created_at'],

                        ]);
                    }

                    $respone = [
                        'message' => 'Success',
                        'code' => 0,

                    ];
                    if ($findEm->device_token) {
                        $url = 'https://fcm.googleapis.com/fcm/send';
                        $dataArr = array(
                            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                            'id' => $request->id,
                            'target' => 'inform_overtime',
                            'target_value' => "",
                            'status' => "done",

                        );
                        $notification = array(
                            'title' => 'Overtime Requested!',
                            'text' => 'Overtime Requested!',

                            'sound' => 'default',
                            'badge' => '1',
                        );
                        // "registration_ids" => $firebaseToken,
                        $arrayToSend = array(
                            "priority" => "high",

                            'to' => $findEm->device_token,

                            'notification' => $notification,
                            'data' => $dataArr,
                            'priority' => 'high'
                        );
                        $fields = json_encode($arrayToSend);
                        $headers = array(
                            'Authorization: key=' . "AAAAqP0mBoo:APA91bEHUWxz5ZkOeZXpeoMSYtjQMdY8WCQyZSi7I5ycQJ3T6yUhqofYZ5w3AjCpjYSLm54Z3xTR3rsT7cLQ_L1xk7VNhODQDXi4GpxfRaDUH8eoefKuegD9_gx3IxKHIsFlLp8dcHe8",
                            'Content-Type: application/json'
                        );
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                        $result = curl_exec($ch);
                        // var_dump($result);
                        curl_close($ch);
                    }
                } else {
                    $respone = [
                        'message' => 'Sorry, emloyee has not sign contract yet!',
                        'code' => -1,
                    ];
                }

                return response()->json($respone, 200);
            }
        } catch (Exception $e) {
            return response()->json(
                [

                    'message' => $e->getMessage(),

                ],
                500
            );
        }
    }
    public function editOvertime(Request $request, $id)
    {
        try {
            // $todayDate = Carbon::now()->format('m/d/Y');
            $data = Overtime::find($id);
            if ($data) {
                $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'number' => 'required',
                    'reason' => 'required',
                    'number' => 'required',
                    'from_date' => 'required',
                    'to_date' => 'required',
                    'type' => 'required',
                    //   
                ]);
                if ($validator->fails()) {
                    $error = $validator->errors()->all()[0];
                    return response()->json(
                        [
                            // 'status'=>'false',
                            'message' => $error,
                            'code' => -1,
                            // 'data'=>[]
                        ],
                        201
                    );
                } else {
                    $previous_user = $data->user_id;
                    $findEm = User::find($request['user_id']);
                    $otRate = 1;
                    $otHour = 0;
                    // $otMethod = 0;
                    // $total = 0;
                    $contract = Contract::where('user_id', $request['user_id'])->first();
                    $totalDuration = 0;
                    $standarHour = 0;
                    $work = Workday::find($findEm->workday_id);
                    if ($contract) {
                        if ($work->off_day != Null) {
                            $workday = explode(',', $work->off_day);
                            if (count($workday) == 1) {
                                $standarHour = 26;
                            }
                            if (count($workday) == 2) {
                                $standarHour = 22;
                            }
                        } else {
                            // send extra to counter
                            // this day , take salary and devided to find one day salary 
                            $standarHour = 26;
                            // 4 day , add more with 26 day 
                            // $extra = 4;

                            // cannot use standard 30 even though user cancel day off
                        }
                        $structure = Structure::find($contract->structure_id);

                        $baseSalary = $structure->base_salary;
                        $SalaryOneday =  $baseSalary / $standarHour;
                        // $standarHour = 44;
                        // $standarHour =        $contract->working_schedule;
                        $SalaryOneHour =  $SalaryOneday / 9;
                        $duration = 0;
                        $duration_in_days = 0;
                        $total = 1;
                        if ($request['type'] == "hour") {
                            $duration = $request['number'];
                            $totalDuration = $duration;
                        } else {
                            // check date 
                            if ($request['from_date'] == $request['to_date']) {
                                $duration = 9;
                                $totalDuration = 1;
                            } else {
                                $dateFrom = "2022-07-21";
                                $dateTo = "2022-07-22";
                                $pastDF = Carbon::parse($request['from_date']);
                                $pastDT = Carbon::parse($request['to_date']);
                                $duration_in_days =   $pastDT->diffInDays($pastDF);
                                $totalDuration = $duration_in_days + 1;
                                // out put 1 ,but reality 2 
                                // $duration_in_days = $request->to_date->diffInDays($request->from_date);
                                // $duration= $duration_in_days;
                                $duration = ($duration_in_days + 1) * 9;
                            }
                        }
                        $otMethod = 1;
                        if ($request['ot_method']) {
                            $otMethod = $request['ot_method'];
                            if ($duration >= 9) {
                                $total = $SalaryOneday * $totalDuration *  $otMethod;
                            } else {
                                $total = $SalaryOneHour *  $totalDuration *  $otMethod;
                            }
                            $total = round($total, 2);
                            // $otRate =$request->ot_rate;
                            // $otHour = $request->ot_hour;


                        } else {
                            if ($duration >= 9) {
                                $total = $SalaryOneday * $totalDuration;
                            } else {
                                $total = $SalaryOneHour *  $totalDuration;
                            }
                            $total = round($total, 2);
                        }
                        // if user already approve urgent busy however status approve , but can edit other user
                        if ($data->pay_status == "pending"  && ($data->status == "pending" || $data->status == "approved")) {
                            if ($work->type_date_time == "server") {
                                $todayDate = Carbon::now()->format('m/d/Y');
                                $data->user_id = $request['user_id'];
                                $data->reason = $request['reason'];
                                $data->notes = $request['note'];
                                $data->from_date = $request['from_date'];
                                $data->to_date = $request['to_date'];
                                $data->number = $totalDuration;
                                $data->type = $request['type'];
                                // $data->status = $request['pay_status'];
                                $data->ot_rate =  round($SalaryOneHour, 2);
                                $data->ot_hour =  $duration;
                                $data->ot_method = $otMethod;
                                $data->total_ot = $total;
                                $data->date = $todayDate;
                                // $data->pay_status = "pending";
                                $data->update();
                            } else {
                                $data->user_id = $request['user_id'];
                                $data->reason = $request['reason'];
                                $data->notes = $request['note'];
                                $data->from_date = $request['from_date'];
                                $data->to_date = $request['to_date'];
                                $data->number = $totalDuration;
                                $data->type = $request['type'];
                                // $data->status = $request['pay_status'];
                                $data->ot_rate =  round($SalaryOneHour, 2);
                                $data->ot_hour =  $duration;
                                $data->ot_method = $otMethod;
                                $data->total_ot = $total;
                                $data->date = $request['date'];
                            }

                            $respone = [
                                'message' => 'Success',
                                'code' => 0,

                            ];
                            if ($previous_user != $request['user_id']) {
                                if ($findEm->device_token) {
                                    $url = 'https://fcm.googleapis.com/fcm/send';
                                    $dataArr = array(
                                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                                        'id' => $request->id,
                                        'target' => 'inform_overtime',
                                        'target_value' => "",
                                        'status' => "done",

                                    );
                                    $notification = array(
                                        'title' => 'Overtime Requested!',
                                        'text' => 'Overtime Requested!',

                                        'sound' => 'default',
                                        'badge' => '1',
                                    );
                                    // "registration_ids" => $firebaseToken,
                                    $arrayToSend = array(
                                        "priority" => "high",

                                        'to' => $findEm->device_token,

                                        'notification' => $notification,
                                        'data' => $dataArr,
                                        'priority' => 'high'
                                    );
                                    $fields = json_encode($arrayToSend);
                                    $headers = array(
                                        'Authorization: key=' . "AAAAqP0mBoo:APA91bEHUWxz5ZkOeZXpeoMSYtjQMdY8WCQyZSi7I5ycQJ3T6yUhqofYZ5w3AjCpjYSLm54Z3xTR3rsT7cLQ_L1xk7VNhODQDXi4GpxfRaDUH8eoefKuegD9_gx3IxKHIsFlLp8dcHe8",
                                        'Content-Type: application/json'
                                    );
                                    $ch = curl_init();
                                    curl_setopt($ch, CURLOPT_URL, $url);
                                    curl_setopt($ch, CURLOPT_POST, true);
                                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                                    $result = curl_exec($ch);
                                    // var_dump($result);
                                    curl_close($ch);
                                }
                            }
                        }
                    } else {
                        $respone = [
                            'message' => 'No employee id found',
                            'code' => -1,


                        ];
                    }
                }
            } else {
                $respone = [
                    'message' => 'No leave id found',
                    'code' => -1,

                ];
            }
            return response()->json($respone, 200);
        } catch (Exception $e) {
            return response()->json(
                [

                    'message' => $e->getMessage(),

                ],
                500
            );
        }
    }
    public function deleteOvertime($id)
    {
        try {
            // if stutus ==pending
            $data = Overtime::find($id);
            if ($data) {
                if ($data->status == "pending") {
                    $data->delete();
                    $respone = [
                        'message' => 'Success',
                        'code' => 0,
                    ];
                } else {
                    $respone = [
                        'message' => 'Cannot delete this overtime',
                        'code' => 0,
                    ];
                }
            } else {
                $respone = [
                    'message' => 'No overtime id found',
                    'code' => -1,
                ];
            }
            return response()->json(
                $respone,
                200
            );
        } catch (Exception $e) {

            return response()->json(
                [
                    'message' => $e->getMessage(),
                ]
            );
        }
    }
    public function getOvertime(Request $request)
    {
        try {
            $use_id = $request->user()->id;
            $ex = User::find($use_id);
            // $find= Department::where('manager','=', $ex->);
            $pageSize = $request->page_size ?? 10;
            $fromDate = $request->get('from_date');
            $toDate = $request->get('to_date');


            if ($request->has('from_date') && $request->has('to_date')) {

                $user = Overtime::select('overtimes.*', 'users.name')

                    ->join('users', 'users.id', '=', 'overtimes.user_id')
                    ->where('users.department_id', '=', $ex->department_id)
                    ->whereDate('overtimes.created_at', '>=', date('Y-m-d', strtotime($fromDate)))
                    ->whereDate('overtimes.created_at', '<=', date('Y-m-d', strtotime($toDate)))
                    ->orderBy('created_at', 'ASC')->paginate($pageSize);
            } else {

                $user = Overtime::select('overtimes.*', 'users.name')

                    ->join('users', 'users.id', '=', 'overtimes.user_id')
                    ->where('users.department_id', '=', $ex->department_id)
                    ->orderBy('created_at', 'ASC')->paginate($pageSize);
            }
            // $timetable = Overtime::orderBy('created_at', 'DESC')->paginate($pageSize);
            return response()->json(
                $user,
                200
            );
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }
    // public function getOvertByUser(Request $request, $id)
    // {
    //     try {
    //         $pageSize = $request->page_size ?? 10;
    //         $use_id = $request->user()->id;

    //         $department = Department::with('location')->where('group_department_id', $id)->paginate($pageSize);
    //         return response()->json(
    //             // 'code'=>0,
    //             // 'message'=>'sucess',
    //             $department,
    //             200
    //         );
    //     } catch (Exception $e) {
    //         //throw $th;
    //         return response()->json([
    //             'message' => $e->getMessage()
    //         ]);
    //     }
    // }
    // user by department
    public function getUserByDepartment(Request $request)
    {
        try {
            // 3
            $user_id = $request->user()->id;

            $user = User::find($user_id);

            // $find = Department::where('manager',  $user_id)->first();
            $pageSize = $request->page_size ?? 10;

            $department = User::where('department_id', $user->department_id)->whereNotIn('id', [1])->paginate($pageSize);
            return response()->json(
                $department,
                200
            );
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }
    public function getUserDetailByDepartment(Request $request, $id)
    {
        try {
            // 3
            $user_id = $request->user()->id;

            $department = User::with('position', 'department', 'timetable', 'workday')->where('id', '=', $id)->first();
            return response()->json(
                [
                    'code' => 0,
                    'message' => 'Success',
                    'data' => $department,
                ],
                200
            );
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }
    public function getAllUserByDepartment(Request $request)
    {
        try {
            // 3
            $user_id = $request->user()->id;

            $find = Department::where('manager', $user_id)->first();
            if ($find) {
                $department = User::where('department_id', $find->id)->whereNotIn('id', [1])->get();
                $response = [
                    'code' => 0,
                    'message' => 'Success',
                    'data' =>  $department
                ];
            } else {
                $response = [
                    'code' => -1,
                    'message' => "sorry, you don't have enough permission to request",

                ];
            }
            $pageSize = $request->page_size ?? 10;


            return response()->json(
                $response,
                200
            );
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }
    // get overtime for own 
    public function getMyOvertime(Request $request)
    {
        try {
            $user_id = $request->user()->id;
            // $employee = User::find($use_id);
            $pageSize = $request->page_size ?? 10;
            $fromDate = $request->get('from_date');
            $toDate = $request->get('to_date');
            $todayDate = Carbon::now()->format('m/d/Y');
            // $postion = Position::paginate($pageSize);

            //code...

            if ($request->has('from_date') && $request->has('to_date')) {

                $data = Overtime::where('user_id', $user_id)->whereDate('overtimes.created_at', '>=', date('Y-m-d', strtotime($fromDate)))
                    ->whereDate('overtimes.created_at', '<=', date('Y-m-d', strtotime($toDate)))
                    ->orderBy('created_at', 'ASC')->paginate($pageSize);
            } else {
                $data = Overtime::where('user_id', $user_id)
                    ->orderBy('created_at', 'ASC')
                    ->paginate($pageSize);
            }
            return response()->json(
                $data,
                200
            );
        } catch (Exception $e) {
            // return response($e ,200);
            return response()->json(
                [
                    'message' => $e->getMessage(),
                    // 'data'=>[]
                ]
            );
        }
    }
    // edit status overtime 
    public function editOvertimeStatus(Request $request, $id)
    {
        try {
            $data = Overtime::find($id);
            // for sending notification back to manager
            $ex1 = User::find($data->user_id);
            $department = Department::where('manager', $ex1->id);
            $todayDate = Carbon::now()->format('m/d/Y');
            $timeNow = Carbon::now()->format('H:i:s');
            $status = "";
            $title = "";
            $paytype = "";
            $code = "";

            if ($data) {
                $validator = Validator::make($request->all(), [
                    'status' => 'required',
                ]);
                if ($validator->fails()) {
                    $error = $validator->errors()->all()[0];
                    return response()->json(
                        [
                            'message' => $error,
                            'code' => -1,
                        ],
                        201
                    );
                } else {

                    if ($request["status"] == "rejected") {
                        $title = "Employee has been rejected overtime!";
                        $paytype = "";

                        // $paytype=null;  
                    }
                    if ($request["status"] == 'approved') {
                        // if approved user must choose pay_type :return as cash or holiday

                        $title = "Employee has been approved overtime!";
                        $paytype = $request["pay_type"];
                        // check with attendance user come or not

                    }
                    // when paytype status ="completed";
                    $data->status = $request["status"];
                    $data->pay_type = $paytype;
                    $query = $data->update();
                    $respone = [
                        'message' => 'Success',
                        'code' => 0,
                    ];
                    // if this user has manager send notifcation back to manager
                    if ($department) {

                        if ($ex1->device_token) {
                            $url = 'https://fcm.googleapis.com/fcm/send';
                            $dataArr = array(
                                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                                'id' => $request->id,
                                'target' => 'inform_leave',
                                'target_value' => "",
                                'status' => "done",

                            );
                            $notification = array(
                                'title' => $title,
                                'text' => $title,

                                'sound' => 'default',
                                'badge' => '1',
                            );
                            // "registration_ids" => $firebaseToken,
                            $arrayToSend = array(
                                "priority" => "high",

                                'to' => $ex1->device_token,

                                'notification' => $notification,
                                'data' => $dataArr,
                                'priority' => 'high'
                            );
                            $fields = json_encode($arrayToSend);
                            $headers = array(
                                'Authorization: key=' . "AAAAqP0mBoo:APA91bEHUWxz5ZkOeZXpeoMSYtjQMdY8WCQyZSi7I5ycQJ3T6yUhqofYZ5w3AjCpjYSLm54Z3xTR3rsT7cLQ_L1xk7VNhODQDXi4GpxfRaDUH8eoefKuegD9_gx3IxKHIsFlLp8dcHe8",
                                'Content-Type: application/json'
                            );
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $url);
                            curl_setopt($ch, CURLOPT_POST, true);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                            $result = curl_exec($ch);
                            // var_dump($result);
                            curl_close($ch);
                        }
                    }
                }
            } else {
                $respone = [
                    'message' => 'No leave id found',
                    'code' => -1,


                ];
            }

            return response(
                $respone,
                200
            );
        } catch (Exception $e) {

            return response([
                'message' => $e->getMessage()
            ]);
        }
    }


    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        //
    }


    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }




    // payroll
    public function getMypayslip(Request $request)
    {
        try {
            $user_id = $request->user()->id;
            $ex = User::find($user_id);
            // $find= Department::where('manager','=', $ex->);
            $pageSize = $request->page_size ?? 10;
            $fromDate = $request->get('from_date');
            $toDate = $request->get('to_date');

            if ($request->has('from_date') && $request->has('to_date')) {

                $data = Payslip::where('user_id', $user_id)->whereDate('payslips.created_at', '>=', date('Y-m-d', strtotime($fromDate)))
                    ->whereDate('payslips.created_at', '<=', date('Y-m-d', strtotime($toDate)))
                    ->orderBy('created_at', 'DESC')
                    ->paginate($pageSize);
                foreach ($data as $key => $val) {
                    $contract = Contract::where('user_id', '=', $val->user_id)->first();
                    $structure = Structure::where('id', '=', $contract->structure_id)->first();
                    $start_date = date('m/d/Y', strtotime($val->from_date));
                    $monthName = Carbon::createFromFormat('m/d/Y', $start_date)->format('F');
                    $val->month = $monthName;
                    $val->base_salary = $structure['base_salary'];
                    $val->user_allowance = $structure['allowance'];
                }
            } else {
                $data = Payslip::where('user_id', $user_id)
                    ->orderBy('created_at', 'DESC')
                    ->paginate($pageSize);
                foreach ($data as $key => $val) {
                    $contract = Contract::where('user_id', '=', $val->user_id)->first();
                    $structure = Structure::where('id', '=', $contract->structure_id)->first();
                    $start_date = date('m/d/Y', strtotime($val->from_date));
                    $monthName = Carbon::createFromFormat('m/d/Y', $start_date)->format('F');
                    $val->month = $monthName;
                    $val->base_salary = $structure['base_salary'];
                    $val->user_allowance = $structure['allowance'];
                }
            }
            // $timetable = Overtime::orderBy('created_at', 'DESC')->paginate($pageSize);
            return response()->json(
                $data,
                200
            );
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }
    // overtime compesation
    public function addOvertimeCompesation(Request $request)
    {
        try {
            $user_id = $request->user()->id;
            $ex = User::find($user_id);
            $position = Position::find($ex->position_id);
            // Carbon::now()->format('Y/m/d H:i:s')]);
            $todayDate = Carbon::now()->format('m/d/Y H:i:s');
            $typedate = Workday::first();
            // type = hour or day
            $validator = Validator::make($request->all(), [
                // 'request_type' => 'required',
                'type' => 'required',
                'reason' => 'required',
                'from_date' => 'required',
                'to_date' => 'required',
                // 'duration' => 'required'
            ]);
            if ($validator->fails()) {
                $error = $validator->errors()->all()[0];
                return response()->json(
                    [
                        'message' => $error,
                        'code' => -1,
                    ],
                    201
                );
            } else {
                // $findCounter = Counter::find()
                $duration = 0;
                if ($request['type'] == "hour") {
                    $duration = $request['duration'];
                } else {
                    $pastDF = Carbon::parse($request['from_date']);
                    $pastDT = Carbon::parse($request['to_date']);
                    $duration_in_days =   $pastDT->diffInDays($pastDF);

                    $duration = ($duration_in_days + 1);
                }
                if ($typedate->type_date_time == "server") {
                    $data = Overtimecompesation::create([
                        'user_id' => $ex->id,
                        'type' => $request['type'],
                        'reason' => $request['reason'],
                        'from_date' => $request['from_date'],
                        'to_date' => $request['to_date'],
                        'duration' =>  $duration,
                        'request_type' =>  'clear_ot',
                        'status' =>  "pending",
                        'date' => $todayDate
                    ]);
                } else {
                    $data = Overtimecompesation::create([
                        'user_id' => $ex->id,
                        'type' => $request['type'],
                        'reason' => $request['reason'],
                        'from_date' => $request['from_date'],
                        'to_date' => $request['to_date'],
                        'duration' =>  $duration,
                        'status' =>  "pending",
                        'date' => $request['date'],
                        'request_type' =>  'clear_ot',
                        'created_at' => $request['created_at'],
                        'updated_at' => $request['created_at'],
                    ]);
                }

                $respone = [
                    'message' => 'Success',
                    'code' => 0,
                    // 'data'=>$data->id
                ];
                $notification = new Notice(
                    [
                        'notice' => "Overtime Compesation",
                        'noticedes' => "Employee name : {$ex->name}" . "\n" . "Position : {$position->position_name}" . "\n" . "Reason : " . $request['reason'] . "\n" . "From Date : " . $request['from_date']  . "\n" . "To Date :" . $request['to_date'] . "\n" . "Type : " . $request['type'] . "\n" . "Duration :" . $duration . "\n",
                        'telegramid' => Config::get('services.telegram_id')
                    ]
                );
                $notification->notify(new TelegramRegister());
            }
            return response()->json(
                $respone,
                200
            );
        } catch (Exception $e) {
            return response()->json(
                [
                    'message' => $e->getMessage(),
                ]
            );
        }
    }
    // song mOng
    public function addSongTime(Request $request)
    {
        try {
            $user_id = $request->user()->id;
            $ex = User::find($user_id);
            $position = Position::find($ex->position_id);

            $todayDate = Carbon::now()->format('m/d/Y H:i:s');
            $typedate = Workday::first();

            $validator = Validator::make($request->all(), [

                'from_date' => 'required',
                'to_date' => 'required',

            ]);
            if ($validator->fails()) {
                $error = $validator->errors()->all()[0];
                return response()->json(
                    [
                        'message' => $error,
                        'code' => -1,
                    ],
                    201
                );
            } else {
                $duration = 0;
                $pastDF = Carbon::parse($request['from_date']);
                $pastDT = Carbon::parse($request['to_date']);
                $duration_in_days =   $pastDT->diffInDays($pastDF);

                $duration = ($duration_in_days + 1);
                if ($typedate->type_date_time == "server") {
                    $data = Overtimecompesation::create([
                        'user_id' => $ex->id,
                        'type' => 'day',
                        'reason' => 'Song time',
                        'from_date' => $request['from_date'],
                        'to_date' => $request['to_date'],
                        'duration' =>  $duration,
                        'request_type' =>  "song_mong",
                        'status' =>  "pending",

                        'date' => $todayDate
                    ]);
                } else {
                    $data = Overtimecompesation::create([
                        'user_id' => $ex->id,
                        'type' => 'day',
                        'reason' => 'Song time',
                        'from_date' => $request['from_date'],
                        'to_date' => $request['to_date'],
                        'duration' =>  $duration,
                        'status' =>  "pending",
                        'date' => $request['date'],
                        'request_type' =>  "song_mong",
                        'created_at' => $request['created_at'],
                        'updated_at' => $request['created_at'],
                    ]);
                }

                $respone = [
                    'message' => 'Success',
                    'code' => 0,
                    // 'data'=>$data->id
                ];
                $notification = new Notice(
                    [
                        'notice' => "Song Time Requested",
                        'noticedes' => "Employee name : {$ex->name}" . "\n" . "Position : {$position->position_name}" . "\n" . "Reason : " . 'Song time' . "\n" . "From Date : " . $request['from_date']  . "\n" . "To Date :" . $request['to_date'] . "\n" . "Type : " . 'day' . "\n" . "Duration :" . $duration . "\n",
                        'telegramid' => Config::get('services.telegram_id')
                    ]
                );
                $notification->notify(new TelegramRegister());
            }
            return response()->json(
                $respone,
                200
            );
        } catch (Exception $e) {
            return response()->json(
                [
                    'message' => $e->getMessage(),
                ]
            );
        }
    }
    public function editSongMong(Request $request, $id)
    {
        try {
            $data = Overtimecompesation::find($id);
            $user_id = $request->user()->id;
            $ex = User::find($user_id);
            $todayDate = Carbon::now()->format('m/d/Y H:i:s');
            $typedate = Workday::first();
            if ($data) {
                $validator = Validator::make($request->all(), [

                    'from_date' => 'required',
                    'to_date' => 'required',

                ]);
                if ($validator->fails()) {
                    $error = $validator->errors()->all()[0];
                    return response()->json(
                        [
                            'message' => $error,
                            'code' => -1,
                        ],
                        201
                    );
                } else {
                    if ($data->status == "pending") {
                        $duration = 0;
                        $pastDF = Carbon::parse($request['from_date']);
                        $pastDT = Carbon::parse($request['to_date']);
                        $duration_in_days =   $pastDT->diffInDays($pastDF);

                        $duration = ($duration_in_days + 1);

                        if ($typedate->type_date_time == "server") {
                            // $data->user_id = $ex->id;
                            // $data->type = $request['type'];
                            // $data->reason = $request['reason'];
                            $data->from_date = $request['from_date'];
                            $data->to_date = $request['to_date'];
                            $data->duration = $duration;
                            $data->date = $todayDate;
                            $data->update();
                        } else {
                            // $data->user_id = $ex->id;
                            // $data->type = $request['type'];
                            // $data->reason = $request['reason'];
                            $data->from_date = $request['from_date'];
                            $data->to_date = $request['to_date'];
                            $data->duration = $duration;
                            $data->date = $request['date'];
                            $data->update();
                        }



                        $respone = [
                            'message' => 'Success',
                            'code' => 0,

                        ];
                    }
                }
            } else {
                $respone = [
                    'message' => 'No compesation id found',
                    'code' => -1,
                ];
            }
            return response()->json(
                $respone,
                200
            );
        } catch (Exception $e) {
            return response()->json(
                [
                    'message' => $e->getMessage(),
                ]
            );
        }
    }
    public function deleteSongMong($id)
    {
        try {
            $data = Overtimecompesation::find($id);
            if ($data) {
                if ($data->status == "pending") {
                    $data->delete();
                    $respone = [
                        'message' => 'Success',
                        'code' => 0,
                    ];
                } else {
                    $respone = [
                        'message' => 'Cannot delete compesaton that completed',
                        'code' => -1,
                    ];
                }
            } else {
                $respone = [
                    'message' => 'No overtime compesation id found',
                    'code' => -1,
                ];
            }
            return response()->json(
                $respone,
                200
            );
        } catch (Exception $e) {

            return response()->json(
                [
                    'message' => $e->getMessage(),

                ]
            );
        }
    }
    public function getSongMong(Request $request)
    {
        try {
            $pageSize = $request->page_size ?? 10;
            $user_id = $request->user()->id;
            // $postion = Overtimecompesation::orderBy('created_at', 'DESC')->paginate($pageSize);
            $pageSize = $request->page_size ?? 10;
            $fromDate = $request->get('from_date');
            $toDate = $request->get('to_date');

            if ($request->has('from_date') && $request->has('to_date')) {

                $data = Overtimecompesation::where('user_id', $user_id)
                    ->where('request_type', '=', 'song_mong')
                    ->whereDate('overtimecompesations.created_at', '>=', date('Y-m-d', strtotime($fromDate)))

                    ->whereDate('overtimecompesations.created_at', '<=', date('Y-m-d', strtotime($toDate)))

                    ->orderBy('created_at', 'DESC')->paginate($pageSize);
            } else {
                $data = Overtimecompesation::where('user_id', $user_id)
                    ->where('request_type', '=', 'song_mong')
                    ->orderBy('created_at', 'DESC')
                    ->paginate($pageSize);
            }

            return response()->json(

                $data,
                200
            );
        } catch (Exception $e) {

            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }
    // ot compensation
    public function editOvertimeCompesation(Request $request, $id)
    {
        try {
            $data = Overtimecompesation::find($id);
            $user_id = $request->user()->id;
            $ex = User::find($user_id);
            $todayDate = Carbon::now()->format('m/d/Y H:i:s');
            $typedate = Workday::first();
            if ($data) {
                $validator = Validator::make($request->all(), [
                    'type' => 'required',
                    'reason' => 'required',
                    'from_date' => 'required',
                    'to_date' => 'required',
                    'duration' => 'required'
                ]);
                if ($validator->fails()) {
                    $error = $validator->errors()->all()[0];
                    return response()->json(
                        [
                            'message' => $error,
                            'code' => -1,
                        ],
                        201
                    );
                } else {
                    if ($data->status == "pending") {
                        $duration = 0;
                        if ($request['type'] == "hour") {
                            $duration = $request['duration'];
                        } else {
                            $pastDF = Carbon::parse($request['from_date']);
                            $pastDT = Carbon::parse($request['to_date']);
                            $duration_in_days =   $pastDT->diffInDays($pastDF);

                            $duration = ($duration_in_days + 1);
                        }

                        if ($typedate->type_date_time == "server") {
                            $data->user_id = $ex->id;
                            $data->type = $request['type'];
                            $data->reason = $request['reason'];
                            $data->from_date = $request['from_date'];
                            $data->to_date = $request['to_date'];
                            $data->duration = $duration;
                            $data->date = $todayDate;
                            $data->update();
                        } else {
                            $data->user_id = $ex->id;
                            $data->type = $request['type'];
                            $data->reason = $request['reason'];
                            $data->from_date = $request['from_date'];
                            $data->to_date = $request['to_date'];
                            $data->duration = $duration;
                            $data->date = $request['date'];
                            $data->update();
                        }



                        $respone = [
                            'message' => 'Success',
                            'code' => 0,

                        ];
                    }
                }
            } else {
                $respone = [
                    'message' => 'No compesation id found',
                    'code' => -1,
                ];
            }
            return response()->json(
                $respone,
                200
            );
        } catch (Exception $e) {
            return response()->json(
                [
                    'message' => $e->getMessage(),
                ]
            );
        }
    }
    public function deleteOvertimeCompastion($id)
    {
        try {
            $data = Overtimecompesation::find($id);
            if ($data) {
                if ($data->status == "pending") {
                    $data->delete();
                    $respone = [
                        'message' => 'Success',
                        'code' => 0,
                    ];
                } else {
                    $respone = [
                        'message' => 'Cannot delete compesaton that completed',
                        'code' => -1,
                    ];
                }
            } else {
                $respone = [
                    'message' => 'No overtime compesation id found',
                    'code' => -1,
                ];
            }
            return response()->json(
                $respone,
                200
            );
        } catch (Exception $e) {

            return response()->json(
                [
                    'message' => $e->getMessage(),

                ]
            );
        }
    }
    public function getOvertimeCompesation(Request $request)
    {
        try {
            $pageSize = $request->page_size ?? 10;
            $user_id = $request->user()->id;
            // $postion = Overtimecompesation::orderBy('created_at', 'DESC')->paginate($pageSize);
            $pageSize = $request->page_size ?? 10;
            $fromDate = $request->get('from_date');
            $toDate = $request->get('to_date');

            if ($request->has('from_date') && $request->has('to_date')) {

                $data = Overtimecompesation::where('user_id', $user_id)
                    ->where('request_type', '=', 'clear_ot')
                    ->whereDate('overtimecompesations.created_at', '>=', date('Y-m-d', strtotime($fromDate)))
                    ->whereDate('overtimecompesations.created_at', '<=', date('Y-m-d', strtotime($toDate)))
                    ->orderBy('created_at', 'DESC')->paginate($pageSize);
            } else {
                $data = Overtimecompesation::where('user_id', $user_id)
                    ->where('request_type', '=', 'clear_ot')
                    ->orderBy('created_at', 'DESC')
                    ->paginate($pageSize);
            }

            return response()->json(

                $data,
                200
            );
        } catch (Exception $e) {

            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }
    // get 
    public function getcounter(Request $request)
    {
        try {
            //code...
            // get user by token with timetable
            $use_id = $request->user()->id;
            $todayDate = Carbon::now()->format('m/d/Y');

            $user_id = $request->user()->id;
            // $employee = User::find($use_id);
            $fromDate = $request->get('from_date');
            $toDate = $request->get('to_date');
            $todayDate = Carbon::now()->format('m/d/Y');
            $pageSize = $request->page_size ?? 10;
            if ($request->has('from_date') && $request->has('to_date')) {
                // get sick leave 

                $sick  = Leave::where('user_id', $user_id)
                    ->whereDate('checkins.created_at', '>=', date('Y-m-d', strtotime($fromDate)))
                    ->whereDate('checkins.created_at', '<=', date('Y-m-d', strtotime($toDate)))
                    ->where('checkout_status', 'late')
                    ->count();
                // $early  = Checkin::where('user_id',$user_id)
                // ->whereDate('checkins.created_at', '>=', date('Y-m-d',strtotime($fromDate)))
                // ->whereDate('checkins.created_at', '<=', date('Y-m-d',strtotime($toDate)))
                // ->where('checkout_status','early')
                // ->count();
                $data = Counter::where('user_id', '=', $use_id)->whereDate('counters.created_at', '>=', date('Y-m-d', strtotime($fromDate)))
                    ->whereDate('counters.created_at', '<=', date('Y-m-d', strtotime($toDate)))->first();
            } else {
                $data = Counter::where('user_id', '=', $use_id)->first();
            }
            $respone = [
                'message' => 'Success',
                'code' => 0,
                'data' => $data
            ];



            return response(
                $respone,
                200
            );
        } catch (Exception $e) {
            //throw $th;
            return response([
                'message' => $e->getMessage()
            ]);
        }
    }
    // change holiday
    public function getChangeDayoff(Request $request)
    {
        try {

            $user_id = $request->user()->id;
            // $employee = User::find($use_id);
            $fromDate = $request->get('from_date');
            $toDate = $request->get('to_date');
            $todayDate = Carbon::now()->format('m/d/Y');
            $pageSize = $request->page_size ?? 10;
            if ($request->has('from_date') && $request->has('to_date')) {
                // $late  = Checkin::where('user_id',$user_id)
                // ->whereDate('checkins.created_at', '>=', date('Y-m-d',strtotime($fromDate)))
                // ->whereDate('checkins.created_at', '<=', date('Y-m-d',strtotime($toDate)))
                // ->where('checkout_status','late')

                // ->count();
                // $early  = Checkin::where('user_id',$user_id)
                // ->whereDate('checkins.created_at', '>=', date('Y-m-d',strtotime($fromDate)))
                // ->whereDate('checkins.created_at', '<=', date('Y-m-d',strtotime($toDate)))
                // ->where('checkout_status','early')
                // ->count();

                $data  = Changedayoff::with('workday', 'holiday')->where('user_id', $user_id)->whereDate('changedayoffs.created_at', '>=', date('Y-m-d', strtotime($fromDate)))
                    ->whereDate('changedayoffs.created_at', '<=', date('Y-m-d', strtotime($toDate)))
                    ->orderBy('created_at', 'DESC')
                    ->paginate($pageSize);
            } else {
                $data = Changedayoff::with('workday', 'holiday')->where('user_id', $user_id)

                    ->orderBy('created_at', 'DESC')
                    ->paginate($pageSize);
            }

            return response()->json(
                $data,
                200
            );
        } catch (Exception $e) {
            //throw $th;
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }
    public function addChangeholiday(Request $request)
    {
        try {
            $user_id = $request->user()->id;
            $ex = User::find($user_id);
            $position = Position::find($ex->position_id);
            $todayDate = Carbon::now()->format('m/d/Y H:i:s');

            $validator = Validator::make($request->all(), [
                // 'user_id' => 'required',
                'type' => 'required',
                'reason' => 'required',
                'from_date' => 'required',
                'to_date' => 'required',
                // 'duration' => 'required'
            ]);
            if ($validator->fails()) {
                $error = $validator->errors()->all()[0];
                return response()->json(
                    [
                        'message' => $error,
                        'code' => -1,
                    ],
                    201
                );
            } else {

                // if(user choose change holiday)
                // if($request['holiday_id']){
                //     $Holiday = Holiday::find($request['holiday_id']);

                // }
                $pastDF = Carbon::parse($request['from_date']);
                $pastDT = Carbon::parse($request['to_date']);
                $duration_in_days =   $pastDT->diffInDays($pastDF);

                $duration = ($duration_in_days + 1);

                $data = Changedayoff::create([
                    'user_id' => $ex->id,
                    'type' => $request['type'],
                    'reason' => $request['reason'],
                    'from_date' => $request['from_date'],
                    'to_date' => $request['to_date'],
                    'duration' => $duration,
                    'date' => $todayDate,
                    'status' =>  "pending",
                    'holiday_id' => $request['holiday_id'],
                    'workday_id' => $request['workday_id']
                ]);

                $respone = [
                    'message' => 'Success',
                    'code' => 0,

                ];

                $notification = new Notice(
                    [
                        'notice' => "Notification Change Dayoff",
                        'noticedes' => "Employee name : {$ex->name}" . "\n" . "Position : {$position->position_name}" . "\n" . "Reason : " . $request['reason'] . "\n" . "From Date : " . $request['from_date']  . "\n" . "To Date :" . $request['to_date'] . "\n" . "\n" . "Duration :" . $duration . "\n",
                        'telegramid' => Config::get('services.telegram_id')
                    ]
                );
                $notification->notify(new TelegramRegister());
            }
            return response()->json(
                $respone,
                200
            );
        } catch (Exception $e) {
            return response()->json(
                [
                    'message' => $e->getMessage(),
                ]
            );
        }
    }
    // get public holiday
    public function getPH(Request $request)
    {
        try {
            $user_id = $request->user()->id;
            $ex = User::find($user_id);
            // $position = Position::find($ex->position_id);
            // $todayDate = Carbon::now()->format('m/d/Y H:i:s' );
            $from_date = Carbon::now()->format('Y-m-d');
            $to_date = Carbon::now()->format('Y-m-d');

            // $result1 = Carbon::createFromFormat('Y-m-d',$from_date)->isPast();
            // $result2 = Carbon::createFromFormat('Y-m-d',  $to_date)->isPast();
            $ph = Holiday::whereDate('from_date', '>=', $from_date)
                ->whereDate('to_date', '>=', $to_date)->get();

            return response()->json(
                [
                    'code' => 0,
                    'message' => 'Success',
                    'data' => $ph
                ],
                200
            );
        } catch (Exception $e) {
            return response()->json(
                [
                    'message' => $e->getMessage(),
                ]
            );
        }
    }
    public function getHoliday(Request $request)
    {
        try {
            $user_id = $request->user()->id;
            $ex = User::find($user_id);
            // $position = Position::find($ex->position_id);
            // $todayDate = Carbon::now()->format('m/d/Y H:i:s' );
            $from_date = Carbon::now()->format('Y-m-d');
            $to_date = Carbon::now()->format('Y-m-d');

            // $result1 = Carbon::createFromFormat('Y-m-d',$from_date)->isPast();
            // $result2 = Carbon::createFromFormat('Y-m-d',  $to_date)->isPast();
            $ph = Holiday::all();
            // $status="";
            // foreach($ph as $key=>$val){
            //     $result1 = Carbon::createFromFormat('Y-m-d', $val->from_date)->isPast();
            //     // past
            //     if($result1==true){
            //         $status = "past";
            //     }else{
            //         $status = "pending";
            //     }
            //     $val->holiday_status = $status;
            // }
            // $ph = Holiday::whereDate('from_date', '>=', $from_date)
            //     ->whereDate('to_date', '>=', $to_date)->get();

            return response()->json(
                [
                    'code' => 0,
                    'message' => 'Success',
                    'data' => $ph
                ],
                200
            );
        } catch (Exception $e) {
            return response()->json(
                [
                    'message' => $e->getMessage(),
                ]
            );
        }
    }
    public function getWorkday(Request $request)
    {
        try {
            $user_id = $request->user()->id;
            $ex = User::find($user_id);

            $ph = Workday::all();

            return response()->json(
                [
                    'code' => 0,
                    'message' => 'Success',
                    'data' => $ph
                ],
                200
            );
        } catch (Exception $e) {
            return response()->json(
                [
                    'message' => $e->getMessage(),
                ]
            );
        }
    }
    public function editChangeholiday(Request $request, $id)
    {
        try {
            $user_id = $request->user()->id;
            $ex = User::find($user_id);
            $position = Position::find($ex->position_id);
            $todayDate = Carbon::now()->format('m/d/Y H:i:s');
            $data =  Changedayoff::find($id);
            if ($data) {
                $validator = Validator::make($request->all(), [
                    // 'user_id' => 'required',
                    'type' => 'required',
                    'reason' => 'required',
                    'from_date' => 'required',
                    'to_date' => 'required',
                    // 'duration' => 'required'
                ]);
                if ($validator->fails()) {
                    $error = $validator->errors()->all()[0];
                    return response()->json(
                        [
                            'message' => $error,
                            'code' => -1,
                        ],
                        201
                    );
                } else {
                    $pastDF = Carbon::parse($request['from_date']);
                    $pastDT = Carbon::parse($request['to_date']);
                    $duration_in_days =   $pastDT->diffInDays($pastDF);

                    $duration = ($duration_in_days + 1);
                    if ($data->status == "pending") {
                        $data->user_id = $ex->id;
                        $data->type = $request['type'];
                        $data->reason = $request['reason'];
                        $data->from_date = $request['from_date'];
                        $data->to_date = $request['to_date'];
                        $data->holiday_id = $request['holiday_id'];
                        $data->workday_id = $request['workday_id'];
                        $data->date =  $todayDate;
                        $data->duration = $duration;

                        $data->update();
                    }

                    $respone = [
                        'message' => 'Success',
                        'code' => 0,


                    ];

                    // $notification = new Notice(
                    //     [
                    //         'notice' => "Notification Change Dayoff",
                    //         'noticedes' => "Employee name : {$ex->name}" . "\n" . "Position : {$position->position_name}" . "\n" . "Reason : " . $request['reason'] . "\n" . "From Date : " . $request['from_date']  . "\n" . "To Date :" . $request['to_date'] . "\n" . "\n" . "Duration :" . $duration . "\n",
                    //         'telegramid' => Config::get('services.telegram_id')
                    //     ]
                    // );
                    // $notification->notify(new TelegramRegister());
                }
            } else {
                $respone = [
                    'message' => 'No changeday off id found',
                    'code' => -1,


                ];
            }

            return response()->json(
                $respone,
                200
            );
        } catch (Exception $e) {
            return response()->json(
                [
                    'message' => $e->getMessage(),
                ]
            );
        }
    }
    public function deleteChangedayoff($id)
    {
        try {
            $data = Changedayoff::find($id);
            if ($data) {
                if ($data->status == "pending") {
                    $data->delete();
                    $respone = [
                        'message' => 'Success',
                        'code' => 0,
                    ];
                } else {
                    $respone = [
                        'message' => 'Cannot delete change dayoff that completed',
                        'code' => -1,
                    ];
                }
            } else {
                $respone = [
                    'message' => 'No dayoff id found',
                    'code' => -1,
                ];
            }
            return response()->json(
                $respone,
                200
            );
        } catch (Exception $e) {

            return response()->json(
                [
                    'message' => $e->getMessage(),

                ]
            );
        }
    }
    // request leaveout
    // 
    public function getLeaveout(Request $request)
    {
        try {

            $user_id = $request->user()->id;
            // $employee = User::find($use_id);
            $fromDate = $request->get('from_date');
            $toDate = $request->get('to_date');
            $todayDate = Carbon::now()->format('m/d/Y');
            $pageSize = $request->page_size ?? 10;
            if ($request->has('from_date') && $request->has('to_date')) {


                $data  = Leaveout::where('user_id', $user_id)->whereDate('leaveouts.created_at', '>=', date('Y-m-d', strtotime($fromDate)))
                    ->whereDate('leaveouts.created_at', '<=', date('Y-m-d', strtotime($toDate)))
                    ->orderBy('created_at', 'ASC')
                    ->paginate($pageSize);
            } else {
                $data = Leaveout::where('user_id', $user_id)

                    ->orderBy('created_at', 'ASC')
                    ->paginate($pageSize);
            }

            return response()->json(
                $data,
                200
            );
        } catch (Exception $e) {
            //throw $th;
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }
    public function addLeaveout(Request $request)
    {
        try {
            // $user_id = $request->user()->id;
            // $ex = User::find($user_id);
            // $position = Position::find($ex->position_id);
            // $todayDate = Carbon::now()->format('m/d/Y');

            // $validator = Validator::make($request->all(), [
            //     'reason' => 'required',
            //     'request_type' => 'required',
            //     // 'reason' => 'required',
            //     'time_in' => 'required',
            //     'time_out' => 'required',
            //     // 'duration' => 'required'
            // ]);
            // if ($validator->fails()) {
            //     $error = $validator->errors()->all()[0];
            //     return response()->json(
            //         [
            //             'message' => $error,
            //             'code' => -1,
            //         ],
            //         201
            //     );
            // } else {
            //     $typedate = Workday::first();

            //     // $duration = ($duration_in_days + 1);
            //     $t1 = Carbon::parse($request['time_in']);
            //     $t2 = Carbon::parse($request['time_out']);
            //     $diff = $t1->diff($t2);
            //     $h = $diff->h;
            //     $mn = $diff->i;
            //     $duration = 0;
            //     $type = "";
            //     if ($mn <= 0) {
            //         $mn = 0;
            //         $type = "hour";
            //         // store duration as hour
            //         $duration = $h;
            //     } else {
            //         // store duration as mn
            //         $type = "minute";
            //         $duration = ($h * 60) + $mn;
            //     }
            //     $request_title = "";

            //     if ($typedate->type_date_time == "server") {
            //         $reques_status = "";
            //         if ($request['request_type'] == "leave_out") {
            //             $reques_status = "pending";
            //             $request_title = "Leaveout Requested";
            //         } else {
            //             $reques_status = "approved";
            //             $request_title = "Clear Leaveout Requested";
            //         }
            //         $todayDate = $todayDate;
            //         $data = Leaveout::create([
            //             'user_id' => $ex->id,
            //             'type' => $type,
            //             'reason' => $request['reason'],
            //             'time_in' => $request['time_in'],
            //             'time_out' => $request['time_out'],
            //             'duration' =>  $duration,
            //             'status' =>  $reques_status,
            //             'date' => $todayDate,
            //             'request_type' => $request['request_type'],

            //         ]);
            //     } else {
            //         $reques_status = "";
            //         if ($request['request_type'] == "leave_out") {
            //             $reques_status = "pending";
            //             $request_title = "Leaveout Requested";
            //         } else {
            //             $reques_status = "approved";
            //             $request_title = "Clear Leaveout Requested";
            //         }
            //         $todayDate = $request['date'];
            //         $data = Leaveout::create([
            //             'user_id' => $ex->id,
            //             'type' => $type,
            //             'reason' => $request['reason'],
            //             'time_in' => $request['time_in'],
            //             'time_out' => $request['time_out'],
            //             'duration' =>  $duration,
            //             'status' => $reques_status,
            //             'request_type' => $request['request_type'],
            //             'date' => $request['date'],
            //             'created_at' => $request['created_at'],
            //             'updated_at' => $request['created_at'],
            //         ]);
            //     }

            //     $respone = [
            //         'message' => 'Success',
            //         'code' => 0,
            //     ];

            //     $notification = new Notice(
            //         [
            //             'notice' => $request_title,
            //             'noticedes' => "Employee name : {$ex->name}" . "\n" . "Position : {$position->position_name}" . "\n" . "Reason : " . $request['reason'] . "\n" . "Date : " . $todayDate . "\n" . "Time out : " . $request['time_out'] . "\n" .  "Time in : " . $request['time_in'] . "\n" . "Type :" . $type . "\n" . "Duration :" . $duration . "\n",
            //             'telegramid' => Config::get('services.telegram_id')
            //         ]
            //     );
            //     $notification->notify(new TelegramRegister());
            //     $chief_department = Department::find($ex->department_id);
            //     if ($chief_department->manager) {
            //         $device_id_chief = User::find($chief_department->manager);
            //         if ($device_id_chief->device_token) {
            //             $url = 'https://fcm.googleapis.com/fcm/send';
            //             $dataArr = array(
            //                 'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            //                 'id' => $request->id,
            //                 'target' => 'inform_leaveout',
            //                 'target_value' => "",
            //                 'status' => "done",

            //             );
            //             $notification = array(
            //                 'title' => $request_title,
            //                 'text' => $request_title,

            //                 'sound' => 'default',
            //                 'badge' => '1',
            //             );
            //             // "registration_ids" => $firebaseToken,
            //             $arrayToSend = array(
            //                 "priority" => "high",

            //                 'to' => $device_id_chief->device_token,

            //                 'notification' => $notification,
            //                 'data' => $dataArr,
            //                 'priority' => 'high'
            //             );
            //             $fields = json_encode($arrayToSend);
            //             $headers = array(
            //                 'Authorization: key=' . "AAAAqP0mBoo:APA91bEHUWxz5ZkOeZXpeoMSYtjQMdY8WCQyZSi7I5ycQJ3T6yUhqofYZ5w3AjCpjYSLm54Z3xTR3rsT7cLQ_L1xk7VNhODQDXi4GpxfRaDUH8eoefKuegD9_gx3IxKHIsFlLp8dcHe8",
            //                 'Content-Type: application/json'
            //             );
            //             $ch = curl_init();
            //             curl_setopt($ch, CURLOPT_URL, $url);
            //             curl_setopt($ch, CURLOPT_POST, true);
            //             curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            //             curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            //             curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            //             $result = curl_exec($ch);
            //             // var_dump($result);
            //             curl_close($ch);
            //         }
            //     }
            // }
            $notification = new Notice(
                [
                    'notice' => 'Hello',
                    'noticedes' => "Employee name : sinit" . "\n" ,
                    'telegramid' => Config::get('services.telegram_id')
                ]
            );
            $notification->notify(new TelegramRegister());
            
            return response()->json(
                [
                    'code'=>0,
                    'success'=>'successs'
                ],
                200
            );
        } catch (Exception $e) {
            return response()->json(
                [
                    'message' => $e->getMessage(),
                ]
            );
        }
    }
    public function editLeaveout(Request $request, $id)
    {
        try {
            $user_id = $request->user()->id;
            $ex = User::find($user_id);
            $position = Position::find($ex->position_id);
            $data =  Leaveout::find($id);
            $todayDate = Carbon::now()->format('m/d/Y');
            if ($data) {
                $validator = Validator::make($request->all(), [

                    'reason' => 'required',
                    'time_in' => 'required',
                    'time_out' => 'required',

                ]);
                if ($validator->fails()) {
                    $error = $validator->errors()->all()[0];
                    return response()->json(
                        [
                            'message' => $error,
                            'code' => -1,
                        ],
                        201
                    );
                } else {
                    $typedate = Workday::first();
                    $t1 = Carbon::parse($request['time_in']);
                    $t2 = Carbon::parse($request['time_out']);
                    $diff = $t1->diff($t2);
                    $h = $diff->h;
                    $mn = $diff->i;
                    $duration = 0;
                    $type = "";
                    
                    
                    if ($mn <= 0) {
                        $mn = 0;
                        $type = "hour";
                        // store duration as hour
                        $duration = $h;
                    } else {
                        // store duration as mn
                        $type = "minute";
                        $duration = ($h * 60) + $mn;
                    }

                    if ($data->status == "pending") {

                        if ($typedate->type_date_time == "server") {
                            $data->user_id = $ex->id;
                            // $data->type = $request['type'];
                            $data->reason = $request['reason'];
                            $data->time_in = $request['time_in'];
                            $data->time_out = $request['time_out'];
                            $data->duration = $duration;
                            $data->type = $type;
                            $data->date = $todayDate;
                            $data->update();
                        } else {
                            $data->user_id = $ex->id;
                            // $data->type = $request['type'];
                            $data->reason = $request['reason'];
                            $data->time_in = $request['time_in'];
                            $data->time_out = $request['time_out'];
                            $data->duration = $duration;
                            $data->type = $type;
                            $data->date = $request['date'];
                            $data->update();
                        }
                    }

                    $respone = [
                        'message' => 'Success',
                        'code' => 0,


                    ];
                }
            } else {
                $respone = [
                    'message' => 'No leaveout id found',
                    'code' => -1,


                ];
            }

            return response()->json(
                $respone,
                200
            );
        } catch (Exception $e) {
            return response()->json(
                [
                    'message' => $e->getMessage(),
                ]
            );
        }
    }
    public function deleteLeaveout(Request $request, $id)
    {
        try {
            $data = Leaveout::find($id);
            if ($data) {
                if ($data->status == "pending") {
                    $data->delete();
                    $respone = [
                        'message' => 'Success',
                        'code' => 0,
                    ];
                } else {
                    $respone = [
                        'message' => 'Cannot delete leaveout that completed',
                        'code' => -1,
                    ];
                }
            } else {
                $respone = [
                    'message' => 'No dayoff id found',
                    'code' => -1,
                ];
            }
            return response()->json(
                $respone,
                200
            );
        } catch (Exception $e) {

            return response()->json(
                [
                    'message' => $e->getMessage(),

                ]
            );
        }
    }
    // chief department
    public function getleaveOutChief(Request $request)
    {
        try {
            $use_id = $request->user()->id;
            $ex = User::find($use_id);
            $pageSize = $request->page_size ?? 10;
            $fromDate = $request->get('from_date');
            $toDate = $request->get('to_date');
            $todayDate = Carbon::now()->format('m/d/Y');
            if ($request->has('from_date') && $request->has('to_date')) {
                $user = Leaveout::select('leaveouts.*', 'users.name')
                    ->join('users', 'users.id', '=', 'leaveouts.user_id')
                    ->where('users.department_id', '=', $ex->department_id)
                    ->whereDate('leaveouts.created_at', '>=', date('Y-m-d', strtotime($fromDate)))
                    ->whereDate('leaveouts.created_at', '<=', date('Y-m-d', strtotime($toDate)))
                    ->where('request_type', '=', 'leave_out')

                    ->orderBy('created_at', 'DESC')->paginate($pageSize);
            } else {
                $user = Leaveout::select('leaveouts.*', 'users.name')
                    ->join('users', 'users.id', '=', 'leaveouts.user_id')
                    ->where('users.department_id', '=', $ex->department_id)

                    ->orderBy('created_at', 'DESC')->paginate($pageSize);
            }
            return response(
                $user,
                200
            );
        } catch (Exception $e) {
            //throw $th;
            return response([
                'message' => $e->getMessage()
            ]);
        }
    }
    public function editLeaveOutChief(Request $request, $id)
    {
        try {
            //code...
            // get user by token with timetable
            $user_id = $request->user()->id;
            $ex = User::find($user_id);
            $data = Leaveout::find($id);

            $todayDate = Carbon::now()->format('m/d/Y');
            $timeNow = Carbon::now()->format('H:i:s');
            $leaveDuction = 0;
            $status = "";
            $message = "";
            if ($data) {
                $validator = Validator::make($request->all(), [
                    'status' => 'required',
                ]);
                if ($validator->fails()) {
                    $error = $validator->errors()->all()[0];
                    return response()->json(
                        [
                            'message' => $error,
                            'code' => -1,
                        ],
                        201
                    );
                } else {

                    if ($request["status"] == 'approved') {
                        $status = 'approved';
                    } else {
                        $status = 'rejected';
                    }
                    $data->status = $status;
                    $data->approve_by = $ex->name;
                    $query = $data->update();
                    $profile = User::find($data->user_id);
                    $code = 2;
                    //  $startDate = date('Y-m-d',strtotime($data->from_date));

                    if ($data->status == "rejected") {
                        $message = "Your leaveout request has been rejected!";
                        $code = -1;
                        $respone = [
                            'message' => 'Success',
                            'code' => 0,
                        ];
                    }
                    if ($data->status == 'approved') {
                        $code = 0;
                        $message = "your leaveout request has been approved!";
                        $respone = [
                            'message' => 'Success',
                            'code' => 0,
                        ];
                    }
                    if ($profile->device_token) {
                        $url = 'https://fcm.googleapis.com/fcm/send';
                        $dataArr = array(
                            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                            'id' => $request->id,
                            'target' => 'inform_leaveout',
                            'target_value' => "",
                            'status' => "done",

                        );
                        $notification = array(
                            'title' => $message,
                            'text' => $message,

                            'sound' => 'default',
                            'badge' => '1',
                        );
                        // "registration_ids" => $firebaseToken,
                        $arrayToSend = array(
                            "priority" => "high",

                            'to' => $profile->device_token,

                            'notification' => $notification,
                            'data' => $dataArr,
                            'priority' => 'high'
                        );
                        $fields = json_encode($arrayToSend);
                        $headers = array(
                            'Authorization: key=' . "AAAAqP0mBoo:APA91bEHUWxz5ZkOeZXpeoMSYtjQMdY8WCQyZSi7I5ycQJ3T6yUhqofYZ5w3AjCpjYSLm54Z3xTR3rsT7cLQ_L1xk7VNhODQDXi4GpxfRaDUH8eoefKuegD9_gx3IxKHIsFlLp8dcHe8",
                            'Content-Type: application/json'
                        );
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                        $result = curl_exec($ch);
                        // var_dump($result);
                        curl_close($ch);
                    }
                    if ($code == 0) {
                        $array = array();
                        $findSecuity = User::where('role_id', '=', 5)->get();
                        if (count($findSecuity) != 0) {
                            for ($i = 0; $i < count($findSecuity); $i++) {
                                $array[] = $findSecuity[$i]['device_token'];
                            }
                            $url = 'https://fcm.googleapis.com/fcm/send';
                            $dataArr = array(
                                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                                'id' => $request->id,
                                'target' => 'inform_leaveout',
                                'target_value' => "",
                                'status' => "done",

                            );
                            $notification = array(
                                'title' => "Employee Leaveout",
                                'text' => "Employee Leaveout",

                                'sound' => 'default',
                                'badge' => '1',
                            );
                            // "registration_ids" => $firebaseToken,
                            $arrayToSend = array(
                                "priority" => "high",

                                'registration_ids' => $array,

                                'notification' => $notification,
                                'data' => $dataArr,
                                'priority' => 'high'
                            );
                            $fields = json_encode($arrayToSend);
                            $headers = array(
                                'Authorization: key=' . "AAAAqP0mBoo:APA91bEHUWxz5ZkOeZXpeoMSYtjQMdY8WCQyZSi7I5ycQJ3T6yUhqofYZ5w3AjCpjYSLm54Z3xTR3rsT7cLQ_L1xk7VNhODQDXi4GpxfRaDUH8eoefKuegD9_gx3IxKHIsFlLp8dcHe8",
                                'Content-Type: application/json'
                            );
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $url);
                            curl_setopt($ch, CURLOPT_POST, true);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                            $result = curl_exec($ch);
                            // var_dump($result);
                            curl_close($ch);
                        }
                    }
                }
            } else {
                $respone = [
                    'message' => 'No leave id found',
                    'code' => -1,


                ];
            }

            return response(
                $respone,
                200
            );
        } catch (Exception $e) {

            return response([
                'message' => $e->getMessage()
            ]);
        }
    }
    // security
    public function getleaveOutSecurity(Request $request)
    {
        try {
            $use_id = $request->user()->id;
            $ex = User::find($use_id);
            $pageSize = $request->page_size ?? 10;
            $fromDate = $request->get('from_date');
            $toDate = $request->get('to_date');
            $todayDate = Carbon::now()->format('m/d/Y');
            if ($request->has('from_date') && $request->has('to_date')) {
                $user = Leaveout::with('user')
                    ->whereDate('leaveouts.created_at', '>=', date('Y-m-d', strtotime($fromDate)))
                    ->whereDate('leaveouts.created_at', '<=', date('Y-m-d', strtotime($toDate)))
                    ->where('request_type', '=', 'leave_out')
                    ->where('status', '!=', 'pending')

                    ->orderBy('created_at', 'ASC')->paginate($pageSize);
                // $user=User::all();
                // $user = Leaveout::with('user')->paginate($pageSize);
            } else {
                $user = Leaveout::with('user')
                    ->where('request_type', '=', 'leave_out')
                    ->where('status', 'approved')
                    ->orderBy('created_at', 'ASC')->paginate($pageSize);
            }
            return response(
                $user,
                200
            );
        } catch (Exception $e) {
            //throw $th;
            return response([
                'message' => $e->getMessage()
            ]);
        }
    }
    public function editLeaveOutSecurity(Request $request, $id)
    {
        try {
            //code...
            // get user by token with timetable
            $user_id = $request->user()->id;
            $ex = User::find($user_id);
            $data = Leaveout::find($id);
            $todayDate = Carbon::now()->format('m/d/Y');
            $timeNow = Carbon::now()->format('H:i:s');
            $leaveDuction = 0;
            $status = "";
            $message = "";
            $note = "";
           

            if ($data) {
                $validator = Validator::make($request->all(), [
                    'status' => 'required',
                    // 'arrived_time' => 'required',
                ]);
                if ($validator->fails()) {
                    $error = $validator->errors()->all()[0];
                    return response()->json(
                        [
                            'message' => $error,
                            'code' => -1,
                        ],
                        201
                    );
                } else {
                    $new_duration = 0;
                    if ($request["status"] == 'Uncomplete') {
                        $status = 'uncompleted';
                        // $arrive_time="00:00:00";
                    } else {
                        // $arrive_time=$request["arrived_time"];
                        $t1 = Carbon::parse($request["arrived_time"]);
                        $t2 = Carbon::parse($data->time_out);
                        $diff = $t1->diff($t2);
                        $h = $diff->h;
                        $mn = $diff->i;
                        $duration = 0;
                        $type = "";
                        if ($mn <= 0) {
                            $mn = 0;
                            $type = "hour";
                            // store duration as hour
                            $duration = $h;
                        } else {
                            // store duration as mn
                            $type = "minute";
                            $duration = ($h * 60) + $mn;
                        }

                        $status = 'completed';
                        $new_duration = $duration;
                    }


                    if ($status == "uncompleted") {

                        $data->status = $status;
                        $data->check_by = $ex->name;
                        $data->arrived_time = null;
                        $data->note = $request["note"];
                        $query = $data->update();
                        $respone = [
                            'message' => 'Success',
                            'code' => 0,
                            // 'arriving_time'=>$arrive_time
                        ];
                    } else {
                        $data->status = $status;
                        $data->type = $type;
                        $data->check_by = $ex->name;
                        $data->arrived_time = $request["arrived_time"];
                        $data->duration = $new_duration;

                        $data->note = $request["note"];
                        $k="";
                        // check reason of leaveout
                        // if($data->reason =="work" ){

                        // }else{
                        //      $counter = Counter::where('user_id','=',$ex->id )->first();
                        //     $left_duration=0;
                        //     if($type =="minute"){
                        //         $new_duration= $new_duration/60;
                                
                        //     }
                        //     // personal 5 -2
                        //     if($counter->ot_duration > $new_duration){
                        //         $left_duration = $counter->ot_duration - $new_duration;
                        //         $k="1";
                        //     }
                        //     if($counter->ot_duration ==$new_duration){
                        //         $left_duration =0;
                        //         $k="2";
                        //     }
                        //     if($counter->ot_duration < $new_duration){
                        //         // left 2, but new 3 so own 1
                        //         $left_duration = $counter->ot_duration - $new_duration;
                        //         $k="3";
                        //     }
                        //     $counter->ot_duration= $left_duration;
                        //     $counter->update();
                        // }
                        $query = $data->update();
                        $respone = [
                            'message' => 'Success',
                            'code' => 0,
                            // 'k'=>$k,
                            // 'new_duration'=>$new_duration,
                            // 'left_duration'=>$left_duration
                    
                            // 'arriving_time'=> $request["arrived_time"]
                        ];
                        
                    }
                }
            } else {
                $respone = [
                    'message' => 'No leaveout id found',
                    'code' => -1,


                ];
            }

            return response(
                $respone,
                200
            );
        } catch (Exception $e) {

            return response([
                'message' => $e->getMessage()
            ]);
        }
    }
    public function calculateTime(Request $reqesut)
    {
        try {
            // $t1 = Carbon::parse('2016-07-05 12:29:16');
            // $t2 = Carbon::parse('2016-07-04 13:30:10');
            $t1 = Carbon::parse('17:30:00');
            $t2 = Carbon::parse('20:03:00');
            $diff = $t1->diff($t2);
            return response(
                [
                    'h' => $diff->h,
                    'mn' => $diff->i
                ],
                200
            );
        } catch (Exception $e) {

            return response([
                'message' => $e->getMessage()
            ]);
        }
    }
    // dayofff chief
    public function getDayoffChief(Request $request)
    {
        try {
            $use_id = $request->user()->id;
            $ex = User::find($use_id);
            $pageSize = $request->page_size ?? 10;
            $fromDate = $request->get('from_date');
            $toDate = $request->get('to_date');
            $todayDate = Carbon::now()->format('m/d/Y');
            if ($request->has('from_date') && $request->has('to_date')) {
                // $data  = Changedayoff::where('user_id', $user_id)->whereDate('changedayoffs.created_at', '>=', date('Y-m-d', strtotime($fromDate)))
                //     ->whereDate('changedayoffs.created_at', '<=', date('Y-m-d', strtotime($toDate)))
                //     ->orderBy('created_at', 'DESC')
                //     ->paginate($pageSize);
                $user = Changedayoff::select('changedayoffs.*', 'users.name')
                    ->join('users', 'users.id', '=', 'changedayoffs.user_id')
                    ->where('users.department_id', '=', $ex->department_id)
                    ->whereDate('changedayoffs.created_at', '>=', date('Y-m-d', strtotime($fromDate)))
                    ->whereDate('changedayoffs.created_at', '<=', date('Y-m-d', strtotime($toDate)))
                    ->orderBy('created_at', 'DESC')->paginate($pageSize);
            } else {
                $user = Changedayoff::select('changedayoffs.*', 'users.name')
                    ->join('users', 'users.id', '=', 'changedayoffs.user_id')
                    ->where('users.department_id', '=', $ex->department_id)

                    ->orderBy('created_at', 'DESC')->paginate($pageSize);
            }
            return response(
                $user,
                200
            );
        } catch (Exception $e) {
            //throw $th;
            return response([
                'message' => $e->getMessage()
            ]);
        }
    }
    public function editDayoffChief(Request $request, $id)
    {
        try {
            //code...
            // get user by token with timetable
            $user_id = $request->user()->id;
            $ex = User::find($user_id);
            $data = Changedayoff::find($id);

            $todayDate = Carbon::now()->format('m/d/Y');
            $timeNow = Carbon::now()->format('H:i:s');
            $leaveDuction = 0;
            $status = "";
            $message = "";
            if ($data) {
                $validator = Validator::make($request->all(), [
                    'status' => 'required',
                ]);
                if ($validator->fails()) {
                    $error = $validator->errors()->all()[0];
                    return response()->json(
                        [
                            'message' => $error,
                            'code' => -1,
                        ],
                        201
                    );
                } else {

                    if ($request["status"] == 'approved') {
                        $status = 'approved';
                    } else {
                        $status = 'rejected';
                    }
                    $data->status = $status;
                    $data->approve_by = $ex->name;
                    $query = $data->update();
                    $profile = User::find($data->user_id);
                    //  $startDate = date('Y-m-d',strtotime($data->from_date));

                    if ($data->status == "rejected") {
                        $message = "Your leaveout request has been rejected!";
                        $respone = [
                            'message' => 'Success',
                            'code' => 0,
                        ];
                    }
                    if ($data->status == 'approved') {

                        $message = "your leaveout request has been approved!";
                        $respone = [
                            'message' => 'Success',
                            'code' => 0,
                        ];
                    }
                    if ($profile->device_token) {
                        $url = 'https://fcm.googleapis.com/fcm/send';
                        $dataArr = array(
                            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                            'id' => $request->id,
                            'status' => "done",

                        );
                        $notification = array(
                            'title' => $message,
                            'text' => $message,
                            // 'isScheduled' => "true",
                            // 'scheduledTime' => "2022-06-14 17:55:00",
                            'sound' => 'default',
                            'badge' => '1',
                        );
                        // "registration_ids" => $firebaseToken,
                        $arrayToSend = array(
                            "priority" => "high",
                            // "token"=>"7|Syty8L1QioCvQDQpl0axkahssTg542OE5HNCOpke",
                            // 'to'=>"/topics/6|bY5aVLz32sZrYIGjqpCqDUsRzFxopG8LgyRi0UOo",  
                            'to' => $profile->device_token,
                            // 'registration_ids'=>'6|bY5aVLz32sZrYIGjqpCqDUsRzFxopG8LgyRi0UOo',
                            'notification' => $notification,
                            'data' => $dataArr,
                            'priority' => 'high'
                        );
                        $fields = json_encode($arrayToSend);
                        $headers = array(
                            'Authorization: key=' . "AAAAqP0mBoo:APA91bEHUWxz5ZkOeZXpeoMSYtjQMdY8WCQyZSi7I5ycQJ3T6yUhqofYZ5w3AjCpjYSLm54Z3xTR3rsT7cLQ_L1xk7VNhODQDXi4GpxfRaDUH8eoefKuegD9_gx3IxKHIsFlLp8dcHe8",
                            'Content-Type: application/json'
                        );
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                        $result = curl_exec($ch);
                        // var_dump($result);
                        curl_close($ch);
                    }
                }
            } else {
                $respone = [
                    'message' => 'No leave id found',
                    'code' => -1,


                ];
            }

            return response(
                $respone,
                200
            );
        } catch (Exception $e) {
            return response([
                'message' => $e->getMessage()
            ]);
        }
    }
    public function addMyReminder(Request $request)
    {
        try {
            $user_id = $request->user()->id;
            $ex = User::find($user_id);
            $validator = Validator::make($request->all(), [
                'is_reminder' => 'required',
                'remind_from_date' => 'required',
                'before_timein' => 'required',
                'before_timeout' => 'required',
            ]);
            if ($validator->fails()) {
                $error = $validator->errors()->all()[0];
                return response()->json(
                    [
                        'message' => $error,
                        'code' => -1,
                    ],
                    201
                );
            } else {

                $ex->is_reminder = "true";
                $ex->remind_from_date = $request['remind_from_date'];
                $ex->before_timein = $request['before_timein'];
                $ex->before_timeout = $request['before_timeout'];
                $ex->update();




                $respone = [
                    'message' => 'Success',
                    'code' => 0,

                ];
            }
            return response()->json(
                $respone,
                200
            );
        } catch (Exception $e) {
            return response()->json(
                [
                    'message' => $e->getMessage(),
                ]
            );
        }
    }

    public function testingfunction()
    {
        try {
            // $year = Carbon::now()->format('Y');
            // $h= Holiday::latest()->first();
            // $d = explode('-',  $h->from_date);
            // $holiday="";
            // if( $year > $d[0]){

            //     $year = Carbon::now()->format('Y');
            //     $lastyear = $year-1;
            //     $holiday = Holiday::all();
            //     for($i=0;$i<count($holiday); $i++){
            //         $from_date = $holiday[$i]['from_date'];
            //         $to_date = $holiday[$i]['to_date'];   
            //         $new_from_date = str_replace($lastyear, $year, $from_date); 
            //         $new_to_date = str_replace($lastyear, $year, $to_date); 
            //         $holiday[$i]['from_date']=$new_from_date;
            //         $holiday[$i]['to_date']= $new_to_date;
            //          $holiday[$i]['status']= 'pending';
            //         $query= $holiday[$i]->update();
            //     }
            // }
            // $lastyear = $year1;
            // $holiday = Holiday::orderBy('from_date', 'ASC')->get();
            // $total = 0;
            // $result1  = "";
            // $result2 = "";
            // for ($i = 0; $i < count($holiday); $i++) {
            //     $result1 = Carbon::createFromFormat('Y-m-d', $holiday[$i]['from_date'])->isPast();
            //     $result2 = Carbon::createFromFormat('Y-m-d', $holiday[$i]['to_date'])->isPast();
            //     if ($result1 == false || $result2 == false) {
            //         $total += $holiday[$i]['duration'];
            //     } else {
            //         $total = 0;
            //     }
            // }

            $data = Holiday::whereDate('from_date','=',Carbon::now())
            ->orWhere('to_date','>=',Carbon::now())
            ->where('status','=','completed')
            ->first();
            $data = "2022/11/17";
            $newDate = Carbon::createFromFormat('Y/m/d', $data)->format('m/d/Y');
            // $pastDF = Carbon::parse($request['from_date']);
            //         $pastDT = Carbon::parse($request['to_date']);
            //         $countDuration =   $pastDT->diffInDays($pastDF);
            //         $countDuration = $countDuration + 1;
                                    
            // $from_date = "11/17/2022";
            // $to_date = "11/20/2022";
            // $startDate = date('m/d/Y', strtotime($from_date));
            // $endDate = date('m/d/Y', strtotime($to_date));
            // $user = User::find(2);
            // $work = Workday::find($user->workday_id);
            //     $total=0;
                $startDate = date('Y-m-d', strtotime("11/16/2022"));
                // $endDate = date('m/d/Y', strtotime($data->to_date));
                // $notCheck = $this->getWeekday($startDate);
                $d = Holiday::whereDate('from_date','=',$startDate)
                ->orWhere('to_date','>=',$startDate)
                ->where('status','=','pending')
                ->first();
                $start_date = date('Y-m-d', strtotime("11/10/2022"));
                $d = Holiday::whereDate('from_date','=',$start_date)
                ->orWhere('to_date','<=>',$start_date)
                ->where('status','=','pending')
                ->first();
                $start_date1 ="2022-09-01";
                $end_date1="2022-09-30";
                $startDate = date('Y/m/d', strtotime( $start_date1));
                $endDate = date('Y/m/d', strtotime( $end_date1));
                $ex1 = Leave::where('user_id', 28)
                ->whereBetween('from_date', [$startDate, $endDate])
                ->whereBetween('to_date', [$startDate, $endDate])
                ->where('send_status', '=', "true")
                ->get();
                $durationUnpaidLeave = 0;
                $deduction=0;
                $customers = User::all();
                foreach ($customers as $key => $val) {

                    for ($i = 0; $i < count($ex1); $i++) {
                        $leavetype = Leavetype::find($ex1[$i]->leave_type_id);
                        if (str_contains($leavetype->leave_type, 'month')) {
                            // will calculate 50%
                            $newAttendance = 'yes';
                        }
                        if (str_contains($leavetype->leave_type, 'Hospitality')) {
                            // will calculate 50%
                            // $newAttendance = 'yes';check duration
                            $durationUnpaidLeave = $ex1[$i]['number'];
                        }
                        if (str_contains($leavetype->leave_type, 'Marriage') || str_contains($leavetype->leave_type, 'Peternity') || str_contains($leavetype->leave_type, 'Funeral')) {
                            // will calculate 50%
                            // $newAttendance = 'yes';check duration
                            $durationUnpaidLeave = $ex1[$i]['number'];
                        }
                        if ($ex1[$i]['leave_deduction']) {
                            $deduction += $ex1[$i]['leave_deduction'];
                        } else {
                            $deduction = 0;
                        }
                    }
    
                    $val->leave_deduction = $deduction;
                    $val->duration_unpaid_leave = $durationUnpaidLeave;
                }
                $date =  Carbon::now();
                $todayDate = Carbon::now()->format('Y-m-d');
                $notification = Notification::whereDate('date', $todayDate)
                ->where('status','=','completed')
                ->first();

                $todayDate = Carbon::now()->format('Y-m-d');
                // $notification = Notification::whereDate('date', $todayDate)
                // ->where('status','=','pending')
                // ->first();
        $data = Notification::whereDate('date',$todayDate)
        ->where('status','=','pending')
        ->first();
        $timeNow = Carbon::now()->format('H:i');
      
        if ($data) {
           
            if($data->time){
                $time = Str::substr($data->time , 0, 5);
                if ($data->user_id == 0) {

                    if ($time == $timeNow) {
                        $a = new PushNotification();
                        $a->notify($data);
                    }
                   
                }
                //  else {
                    
                //     if ($time == $timeNow) {
                //         $case="now";
                //         $user = User::find($data->user_id);
                //         if ($user->device_token) {
                //             $case="have_device";
                //             // $a = new PushNotification();
                //             // $a->notify($data);
                //             $url = 'https://fcm.googleapis.com/fcm/send';
                //             $dataArr = array(
                //                 'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                //                 'id' => $data->id,
                //                 'target' => 'inform_notification',
                //                 'target_value' => "",
                //                 'status' => "done",
    
                //             );
    
                //             $notification = array(
                //                 'title' => $data->title,
                //                 'text' => $data->body,
    
                //                 'sound' => 'default',
                //                 'badge' => '1',
                //             );
                //             // "registration_ids" => $firebaseToken,
                //             $arrayToSend = array(
                //                 "priority" => "high",
    
                //                 'to' => $user->device_token,
                //                 // 'registration_ids'=>'6|bY5aVLz32sZrYIGjqpCqDUsRzFxopG8LgyRi0UOo',
                //                 'notification' => $notification,
                //                 'data' => $dataArr,
                //                 'priority' => 'high'
                //             );
                //             $fields = json_encode($arrayToSend);
                //             $headers = array(
                //                 'Authorization: key=' . "AAAAqP0mBoo:APA91bEHUWxz5ZkOeZXpeoMSYtjQMdY8WCQyZSi7I5ycQJ3T6yUhqofYZ5w3AjCpjYSLm54Z3xTR3rsT7cLQ_L1xk7VNhODQDXi4GpxfRaDUH8eoefKuegD9_gx3IxKHIsFlLp8dcHe8",
                //                 'Content-Type: application/json'
                //             );
                //             $ch = curl_init();
                //             curl_setopt($ch, CURLOPT_URL, $url);
                //             curl_setopt($ch, CURLOPT_POST, true);
                //             curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                //             curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                //             curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                //             $result = curl_exec($ch);
                //             // var_dump($result);
                //             curl_close($ch);
                //             // $t=[
                //             //     "title"=>$data->title,
                //             //     "body"=>$data->body,
                //             //     "device_token"=>$user->device_token
                //             // ];
                //             // $a = new PushNotification();
                //             // $a->notifySpecificuser($t);
                //         }
                //     }
                // }
                $data->status= "completed";
                 $data->update();
                // 
            }else{
                // if ($data->user_id != 0) {

                //     $case="now";
                //         $user = User::find($data->user_id);
                //         if ($user->device_token) {
                //             $case="have_device";
                //             // $a = new PushNotification();
                //             // $a->notify($data);
                //             $url = 'https://fcm.googleapis.com/fcm/send';
                //             $dataArr = array(
                //                 'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                //                 'id' => $data->id,
                //                 'target' => 'inform_notification',
                //                 'target_value' => "",
                //                 'status' => "done",
    
                //             );
    
                //             $notification = array(
                //                 'title' => $data->title,
                //                 'text' => $data->body,
    
                //                 'sound' => 'default',
                //                 'badge' => '1',
                //             );
                //             // "registration_ids" => $firebaseToken,
                //             $arrayToSend = array(
                //                 "priority" => "high",
    
                //                 'to' => $user->device_token,
                //                 // 'registration_ids'=>'6|bY5aVLz32sZrYIGjqpCqDUsRzFxopG8LgyRi0UOo',
                //                 'notification' => $notification,
                //                 'data' => $dataArr,
                //                 'priority' => 'high'
                //             );
                //             $fields = json_encode($arrayToSend);
                //             $headers = array(
                //                 'Authorization: key=' . "AAAAqP0mBoo:APA91bEHUWxz5ZkOeZXpeoMSYtjQMdY8WCQyZSi7I5ycQJ3T6yUhqofYZ5w3AjCpjYSLm54Z3xTR3rsT7cLQ_L1xk7VNhODQDXi4GpxfRaDUH8eoefKuegD9_gx3IxKHIsFlLp8dcHe8",
                //                 'Content-Type: application/json'
                //             );
                //             $ch = curl_init();
                //             curl_setopt($ch, CURLOPT_URL, $url);
                //             curl_setopt($ch, CURLOPT_POST, true);
                //             curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                //             curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                //             curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                //             $result = curl_exec($ch);
                //             // var_dump($result);
                //             curl_close($ch);
                //             // $t=[
                //             //     "title"=>$data->title,
                //             //     "body"=>$data->body,
                //             //     "device_token"=>$user->device_token
                //             // ];
                //             // $a = new PushNotification();
                //             // $a->notifySpecificuser($t);
                //         }
                   
                // } else {
                //     $a = new PushNotification();
                //         $a->notify($data);
                // }
                // $data->status= "completed";
                // $data->update();
            }

            
            
            //  $data=  ::update();

        }
               

               
                
            // while ($startDate <= $endDate) {
            //     $dayOff="";

            //     if ($work->off_day !=Null ) {
            //         $OffDay = explode(',', $work->off_day);
            //         $check = "true";
            //         // $notCheck= new GetWeekday();
            //         // $notCheck->getday();
            //         $notCheck = $this->getWeekday($startDate);
            //         // 1 = count($dayoff)
            //         for ($y = 0; $y <  count($OffDay); $y++) {
            //             //   if offday = today check will false
            //             if ($OffDay[$y] == $notCheck) {
            //                 $check = "false";
            //             }
            //         }
            //         if ($check == "false") {
            //             // day off cannot check
            //             $dayOff = "false";
            //         } else {
            //             $dayOff = "true";
            //         }
            //     }
            //     if($work->off_day ==Null){
            //         $dayOff = "true";
            //     }
            //     if( $dayOff=='true'){
            //         $total =$total +1;
            //     }
                
            //     $startDate = date('m/d/Y', strtotime($startDate . '+1 day'));
            // }
            $holiday = Holiday::orderBy('from_date', 'ASC')
            ->where('status','pending')
            ->get();
            $total = 0;
            $result1  = "";
            $result2 = "";
            for ($i = 0; $i < count($holiday); $i++) {
                $result1 = Carbon::createFromFormat('Y-m-d', $holiday[$i]['from_date'])->isPast();
                $result2 = Carbon::createFromFormat('Y-m-d', $holiday[$i]['to_date'])->isPast();
                if ($result1 == false || $result2 == false) {
                    $total += $holiday[$i]['duration'];
                } else {
                    $total = 0;
                }
            }
            $mon = Carbon::now()->format('m');
            return response()->json(

                [
                    'code' => 0,
                    // 'total_duration' =>$total,
                    // 'data'=>$d,
                    // 'data'=>$ex1,
                    // 'today'=>$todayDate,
                    // 'user'=>$notification,
                    // 'data'=>$date,
                    // 'total'=>$total,
                    'month'=>$mon 
                    // 'check'=>  $check
                    // 'year'=> $year,
                    // 'd'=> $d[0],
                    // 'holiday'=>$holiday
                ],
                200
            );
        } catch (Exception $e) {

            return response()->json(
                [
                    'message' => $e->getMessage(),

                ]
            );
        }
    }
}
