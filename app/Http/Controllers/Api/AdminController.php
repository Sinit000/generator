<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Position;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use App\Models\Checkin;
use App\Models\Department;
use App\Models\Employee;
use App\Models\TimetableEmployee;
use App\Models\Leave;
use App\Models\Leavetype;
use App\Models\Location;
use App\Models\Notice;
use App\Models\Schedule;
use App\Models\Store;
use App\Models\Timetable;
use App\Models\Notification;
use App\Models\Holiday;
use App\Models\GroupDepartment;
use App\Models\Role;
use App\Models\Workday;
use App\Models\Overtime;
use App\Models\Product;
use App\Models\Contract;
use App\Models\Structure;
use App\Models\Salary;
use App\Models\Structuretype;
use App\Models\Payslip;
use App\Models\Counter;
use App\Models\Overtimecompesation;
use App\Models\Changedayoff;
use App\Models\CheckinHistory;
use App\Models\Leaveout;
use App\Notifications\PushNotification;
use App\Notifications\TelegramRegister;
use Carbon\Carbon;

use Illuminate\Support\Facades\DB;
use PDF;
use URL;

// use Javascript;


class AdminController extends Controller
{

    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [

                'email' => 'required|string',
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

                $user = User::where('email', $request->email)->first();
                // check password
                if ($user) {
                    // check user role
                    if ($user->role_id == 1) {
                        if (Hash::check($request->password, $user->password)) {
                            $data = User::with('role')->first();
                            $token = $user->createToken('mytoken')->plainTextToken;
                            $respone = [
                                'message' => 'Success',
                                'code' => 0,
                                'user' => $data,
                                'token' => $token,
                            ];
                            return response($respone, 200);
                        } else {
                            return response()->json(
                                [
                                    'message' => "Wrong email or password",
                                    'code' => -1,
                                    // 'data'=>[]
                                ]
                            );
                        }
                    } else {
                        return response()->json(
                            [
                                'message' => "Sorry, you don't have permission to login",
                                'code' => -1,
                                // 'data'=>[]
                            ]
                        );
                    }
                } else {
                    return response()->json(
                        [
                            'message' => "Email does not exist",
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
    public function resetPasswordByAdmin(Request $request, $id)
    {
        try {

            $find = User::find($id);
            if ($find) {
                $validator = Validator::make($request->all(), [
                    // 'old_password'=>'required',
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
                    $user = User::where('username', $find->username)->first();
                    // check password
                    if ($user) {
                        $user->update([
                            'password' => Hash::make($request->new_password)
                        ]);
                        $token = $user->createToken('mytoken')->plainTextToken;
                        $response = [
                            'message' => 'Success',
                            'code' => 0,
                            'token' => $token

                        ];
                        // if(Hash::check($request->old_password,$find->password)){
                        //     $user->update([
                        //         'password'=>Hash::make($request->new_password)
                        //     ]);
                        //     $token= $user->createToken('mytoken')->plainTextToken;
                        //     $response = [
                        //         'message'=>'Success',
                        //         'code'=>0,
                        //         'token'=>$token

                        //     ];
                        //     // return response($respone ,200);
                        // }else{
                        //     $response = [
                        //         'message'=>"Incorrect old password",
                        //         'code'=>-1,

                        //     ];

                        // }
                    } else {
                        return response()->json(
                            [
                                'message' => "Username does not exist",
                                'code' => -1,
                            ]
                        );
                    }
                }
            } else {

                $response = [
                    'message' => 'No employee Id found',
                    'code' => -1,
                ];
            }



            return response()->json(
                $response,
                200
            );
        } catch (Exception $e) {
            //throw $th;
            return response([
                'message' => $e->getMessage()
            ]);
        }
    }
    public function resetPassword(Request $request)
    {
        try {
            $user_id = $request->user()->id;
            $find = User::find($user_id);
            if ($find) {
                $validator = Validator::make($request->all(), [
                    // 'old_password' => 'required',
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
                    $user = User::where('email', $find->email)->first();
                    // check password
                    if ($user) {
                        if (Hash::check($request->old_password, $user->password)) {
                            $user->update([
                                'password' => Hash::make($request->new_password)
                            ]);
                            $token = $user->createToken('mytoken')->plainTextToken;
                            $response = [
                                'message' => 'Success',
                                'code' => 0,
                                'token' => $token,
                                'user' => $user

                            ];
                            // return response($respone ,200);
                        } else {
                            $response = [
                                'message' => "Incorrect old password",
                                'code' => -1,
                            ];
                        }

                        // check the same or now

                    } else {
                        $response = [
                            'message' => "Username does not exist",
                            'code' => -1,
                        ];
                    }
                }
            } else {
                $response = [
                    "message" => "No employe id found",
                    "code" => -1
                ];
            }


            return response()->json(
                $response,
                200
            );
        } catch (Exception $e) {
            //throw $th;
            return response([
                'message' => $e->getMessage()
            ]);
        }
    }
    public function getWorkday(Request $request)
    {
        try {
            $pageSize = $request->page_size ?? 10;
            $postion = Workday::orderBy('created_at', 'ASC')->paginate($pageSize);

            return response()->json(

                $postion,
                200
            );
        } catch (Exception $e) {
            //throw $th;
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getQr(Request $request)
    {
        try {


            return response()->json(
                ['data' => 'iVBORw0KGgoAAAANSUhEUgAAASwAAAEsCAYAAAB5fY51AAAABGdBTUEAALGPC/xhBQAAACBjSFJNAAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAAABmJLR0QAAAAAAAD5Q7t/AAAACXBIWXMAAABgAAAAYADwa0LPAAAQo0lEQVR42u3dwY7cthKFYfque7Kw4bf0Axh5gCBr+y1n4cVM9n0XhnKB3HSTVrFY51D/B2iVqEVS6kJac1L8cL/f7w0ADPynegAAMIqCBcAGBQuADQoWABsULAA2KFgAbFCwANigYAGwQcECYIOCBcAGBQuADQoWABsULAA2KFgAbFCwANigYAGwQcECYIOCBcAGBQuADQoWABsULAA2KFgAbFCwANigYAGwQcECYIOCBcAGBQuADQoWABsULAA2KFgAbFCwANigYAGwQcECYEOmYH369Kl9+PBh2yPq+/fv7eXl5f8+9+XlpX3//r369p0ef++YNb/s6/P8rvHhfr/fqwfR2s8b/uPHj+phpIku88vLS/vrr7/+9Z/dbrf2/v5ePcXT4++ZMb/s6/P8riHzX1h47tmX7ewXUWX8meeqXB9zULAA2KBgAbBBwQJgg4JlQP2F+ojb7VZybvXYMRcFS9z7+3v7+vVr9TDC/vjjj1Nf/Nvt1v7888+ycVdfH/9wF/Hx48d7a+3h8fr6Wj3E0Ph7vn37dr/dbk8/o+Lzb7fb/du3b9XL+3D8o+PrzfPLly/3t7e30+O7+vO7ik0O6/X1tX3+/Ll6mKfH31vmSE4o+/MVcl7RHFov/Pj29tZeXl5Oj+/qz+8q/CQUkZ31cc8hZefQIsUK61CwANigYAGwQcECYIOCJSKa9Xn20jn6wrw6hzQy/uj8q/+ogDEULBFnc0qH33///V+/dNEcV3UOaXT80fk/Oh9iqnMVh2iOpZ3ML40e0fGf9fb2dv/y5Uv6/KI5pGhOKppDyzpGx8/zu8Y2OazsJmO9ZcrMsby/v7fffvstdX7RHFI0JxXNoWWa0Q/rys/vTPwkNLAiIxS9RjQnpVqs1Md2NRQsADYoWABsULAA2KBgGXD4c/uzSEZ1jitzbliLgiXOpR/WoxxZdY4ryn3826nOVRyunmOJ5pAe5ahGc1zVsu9fNGfWc/XndxVyWIN6y1TdD+tZjmokx1X9GGTfv2jOrOfqz+8q/CQUEc36PPsy0uuJNdgFBQuADQoWABsULAA2KFgisvftU89JZY5BYX6Yg4IlInvfPvWcVLQfWHR9YKI6V3EgxxLj2o9q1b6H0fXp4fldgxzWoN4yVedYnPtRrdj3MLo+PTy/a/CTcBPO/ahWXDt7X0OsQcECYIOCBcAGBQuADQoWWmv1WaXsl+7qOTSMoWChtZaXgxqVvS+geg4Ng6pzFYdojqVaVj+s0ZxQC+ZwHlm1L+LZY1aOKrr+V39+V9kmh1Utsx/WSE6ol+OJ3OYV+yJGzMhRRdf/6s/vKvwkFKGcE1LvJTVjfZTXH/9DwQJgg4IFwAYFC4ANChaGKGeVlMeGuShYGFKd03qEHNXFVOcqDr0ciPvRU33+2RzSaE4rum9ib1/B6hzb1Z/fVWxyWO56yxzNUUXPj+SQRnJa0X0Te/sKVufYrv78rkLBWkS9YHE+BSsy/1V4hwXABgULgA0KFgAbFCwR0X5N2f2eei+tq8cfOT+7FxfmoWCJiPZryu731OtXVT3+s+e/v7+3r1+/htcHi1TnKqChJfejyt4X8Oz1Rw9okIk1oFZkX7zovofV+xKO4GuigYKF1lp8I8/eY5TZYFBhfliDd1gAbFCwANigYAGwQcFCmEPOKZpFU5gDKFgIcsk5Rft5Ze+biEHVuYpDNCeTdURzRtXHrH5QZ/tRjR5VovsursqhVZ+vQibWEM3JZIrmjKrN6AcV6Uc1ovIxjO67uCKHVn2+CpmCFc3JZOst0+7jz55/9WOonkOrPl8F77AA2KBgAbBBwQJgg4I1INqPqVp1P6oRLi99z46ffl1zULA6ov2YqlX3oxrlnnPK6hemkmOTUZ2rOLSTOaBZ+9r1ZO/bFz2i88uyKucUlXVfVuX4ovNzYRNryN7Xrid7376o6Pwyrcg5RWXGUlbk+Hpf411iDTYFq/qGVOeUekRuY9r8s+dXfX/Uc2AqeIcFwAYFC4ANChYAGzIFK3tfvWyRHM4VROa/Yu0yr6GQY3P/fh1kClb2vnrZzuZwruLs/Ffd/6z7o5Jjc/9+/a06VzFLS86Z9D6/nczhzMpp9ZzN+aj0S9qln9OvcsmxrSITa4iqjjU8syKn1ZtfJOej0C9pl35OZzjk2FahYE36/J7e9d0/P9suOaKs+ffssj4y77AAoIeCBcAGBQuAjW0KVnbORD1H1HupXzX3WXbJEZ2xw/2bZZuClZ0zUc8RPcrhRPspqeR0tskR/aJd7t801bmKQ3bORj2H9Kvjmn1U9QvLPv8qR/X9W0Um1pCds1HPIVVvE1bZLyz7/Cuovn+ryBQs9xxV9vyzZa9v7/PV+42pq75/q2zzDgvA/ihYAGxQsADYkClYyjmb6us7yNzXkPV/LjtnqLT+MgVLNWdTfX0XWfsasv7PZecM5da/OlcxKpoTaeI5lt74zo5/tJ+Su6z7u2rfS4yRiTX0RHMikX0PFcbXE9230eQxeCjz/q7Y9xJjbApWdc6nenw91fOvpn5/MYfMOywA6KFgAbBBwQJgw6JgjfyPl5n9oEZk5pBmnOuSs8lYgyvllHYnX7BG+wFl9YMalZVD6iHntGZ+u6+fjepcxaG6n9HZ8a3q91Q1vuxDrd9Slurnx6XfVY9MrKG6n1FvGar7PfU495NS6reUpfr5cel31SNTsKr7GfWWgRxYLpHHME3187NLjkz+HRYAHChYAGxQsADYkClYlVmW7BxTNEcWnUN2Dgx92TmuaA7R5aW7TME6m0OKys4xRXNko6pyYBiTneOK5hCjz98y1bmKHpd+RFk5puqczOj6R4+z63uVHFL2+qs+f/8kE2t4xqEfUWaOqTonM7L+Ub3H8Oo5pMrYidL6WBSs1vRzJNkP1NXnd/UcEjm5n2TeYQFADwULgA0KFgAbFgVL5YXfMztHAhTWvzLHpGDn5+tXyBesVf2sonbNMamsf1WOScWuz9cvq85VHKI5pqzrR3Mos3JM6uuvvi9idP2rjqs8f6NkYg3RHFN0Gpk5nRk5puzbFF1/9X0Rq2MBEVd4/kbJFKzsffmi18/+/Oz5VY+vOgflXLBa2//5GyX/DgsADhQsADYoWABsbFGwVvy5N/rSs/L62RT6gUXHuLvsfS9XsS9Yq/aFO5vTmZVjUs0JqfQD67l6jil738tlqnMVhxbsdxXNUfWur3rM6leUtf6r5lfV70qlX9hV2MQaev2uojkq5z97z+hXlLn+K+ZX2e9KoV/YVdgUrN4w3ffli4reRvX1y77/UdXzvwr7d1gAroOCBcAGBQuAjcsUrN5LV+c/ec8Y++77Gma/dMcalylYvZyPa05nVk5m930Ns3JeKv3CLqM6V3FowRxK7/wWzPmc5bKvYpVZ/bLO3v9Vh3q/MJd9Gy8Ta3hGIadTva9ipRn9stRjKer9wlz2baRgDX5+9fx2V33/s0XHz/P502XeYQHwR8ECYIOCBcAGBautyRBl76vnbuccWHT8ynNb7fIFa1W/n+x99dztmgOLjp/n4x+qcxWHlpzDye6nlZ1jyeo31TtUcjhn1zeag1PJ0Z29/7P6wam4TKwhu59Wdo4ls99Uj0IOJ7K+0RycQo4ucv9n9IMTKRPXyWGpnx9dn2zVj0n1/an+Qu++b+Soy7/DAuCDggXABgULgA0K1qBIP63on9urX3grxAXUc0ru+yqqr++BgjXobD+taI6mut+SSg5IPafkvq+i+vr+rTpXcWjJOazo+S2Yc+mpylkdRzRHpN5Pqer5iB6zcma7INYweP4z7jmr1uI5IvV+Ss7ta2bkzES+5mEUrMHze6LL6J6zUs/xVD8fUdnPvwveYQGwQcECYIOCBcDGNgWrMkeilFOpop7jiYyv+g8GM+wwh9Y2KlhVORK5nEoR9RzP2fFV5+Bmyc6JLVOdqzi04hxJ7/rZ/bSi139EJaeT3c8pq5+ZyhF9fqLrq2KbWEP29bP7aUWv/4xCTie7n1NmPzMFvftTnSNchYI16frV52fPL0q9n1N1zip7/tHPV7HNOywA+6NgAbBBwQJgQ6ZgVed4MvfFmzH+6EvRyvV1eaHrTCHrtoJMwarO8WTtizdr/NEcTdX67pJjUqe6L+N01bkK/NSK+ikdds8hRc/P2ncwOyenksObRSbWcHWZf1YnhxQ/P3PfweycnEIObxYKlojsHFD0C1utumBlf012z+HNIvMOCwB6KFgAbFCwANigYF1ANEdWbUa/qt4mDrurzjnOQsHaXDRHVm1Wv6pHObar5MSqc47TVOcqDh8/fizP+mQePdHPr84JZY+vOid2dnyz+qFlX9+FTKzh06dP7cePH9XDSNNb5misoDonlD2+6pxY7/5l90PLvr4LCtYi2QUr+zZWj686Jxa9f+rnu+AdFgAbFCwANihYAGxQsEREIgXq/ayi43N4YVzZDy2aQ3NCwRJxNgel3s8qOj6XnFRVP7RoDs1Oda7i0Mthvb6+Vg8xNH510ZyTes7q0fhU+kVlzTt6qOW4bGINr6+v7fPnz9XDPD1+kWV+KJpzUs9ZPRufQr+o6tjGM0o5Ln4SorXWwsUiGlrNDoX2NsHFY0qNHSlYAGxQsADYoGABsEHBQph6zkphX8jK+e+EgoUQ9ZyVyr6QVfPfTnWu4hDNYbXkPEp0/D1ZOaRZ/Zh6OavqflXRHFjVuF0OFdvksKq3yYrmsDJzSDP6MfVyVtX9qqI5MOUclAKRMsFPQhWZX/YZn90rBtVZHbJU10DBAmCDggXABgULgA0KFlpr8X5OlVuEqefA3Clt/0bBQmst3s+pal9D9RyYO7l9C6tzFYer57B611fv53TW6Pirc2DR9c/aN/JqyGEN6i1TNIcVyUEp9HOKGBl/dQ4suv6Z+0ZeCT8JTezcz2lk/NU5sOj6u98jFRQsADYoWABsULAA2KBgXYR61kg5Bxa9vlKOyR0F6yLU96VTzYFFry+XY3JXnas4kMOKXT9r3ir70j3KWfXG55KTOju/3vku93cUOaxBvWXKzmFFz49Q2JfuWc6qNz6HnFRkfr3zexTu7yh+EqKrutdVbwy98TnkpCLzG/13Ms5djYIFwAYFC4ANChYAGxQsdCnkiDJzWu7zi74wV5j/KAoWnlLJEWXltNznF+3npTL/YdW5ikM0h1XNPYcVzSFl5YhWna8u2u/rbD8xtfXbJodVzT2HFc0hZeaIVpyvLtrvK9JPTGn9KFiLxq9esKKPQfb4s89Xl31/XdaPd1gAbFCwANigYAGwQcHaRHWWJjtH1Pufm3e2Yn7qObUDBWsTVfsC9q4/K0f0qJ/X7vsKrpqfek7tb9W5ikMvx+R+9ETPf0Rl38LsfQOj86seX/X8XdjEGtz1ljnzz8oK+xZm7xvY05tf9fiq5++Cn4QXUN3rqTX9nkvq48NPFCwANihYAGxQsADYoGCJyM7BVOdsKiMX1fsaVttpbhQsEdk5mOqcTVVOrHpfw2pyOaogmVgDAPTwX1gAbFCwANigYAGwQcECYIOCBcAGBQuADQoWABsULAA2KFgAbFCwANigYAGwQcECYIOCBcAGBQuADQoWABsULAA2KFgAbFCwANigYAGwQcECYIOCBcAGBQuADQoWABsULAA2KFgAbFCwANigYAGwQcECYIOCBcAGBQuADQoWABsULAA2KFgAbPwXMgr8KvZ28CcAAAAldEVYdGRhdGU6Y3JlYXRlADIwMjItMDYtMDZUMDk6Mjc6MDgrMDA6MDDiSyuyAAAAJXRFWHRkYXRlOm1vZGlmeQAyMDIyLTA2LTA2VDA5OjI3OjA4KzAwOjAwkxaTDgAAAABJRU5ErkJggg=='],
                200
            );
        } catch (Exception $e) {
            //throw $th;
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }
    public function addWorkday(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'working_day' => 'required',
                'off_day' => 'required'

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
                 if (str_contains($request['working_day'], ',') ) {
                        $workday = $request['working_day'];
                        $offDay = $request['off_day'];
                        $x = explode(",",$workday); 
                        $y =  explode(",",$offDay);
                        $code = 1;
                        $mgs ="";
                        $input1 = $x;
                        $input2 = $y;
                        $array = array_unique($input1); // Array is now (1, 2, 3)
                        $newWorday = implode(',', array_unique(explode(",",$request['working_day'])));
                        $newOffDay = implode(',', array_unique(explode(",",$request['off_day'])));
                        $result = array_intersect($array, $input2);
                        $case ="";
                       
                        if(count($result)==0){
                            $code = 0;
                            $case ="1";
                        }else{
                            $code =-1;
                            $case ="2";
                        }
                        if($code == 0){
                            $data = Workday::create([
                                'name'           =>$request['name'],
                                'working_day'          =>$newWorday ,
                                'off_day'         => $newOffDay ,
                                'notes'       =>$request['notes'],
                                'type_date_time'         => 'server',
                                
                    
                            ]);
                            $respone =[
                                'code'=>0,
                                'message'=>'Success',
                                'result'=>$result,
                                'case'=>$case
                                
                            ];
                        }else{
                            $respone =[
                                'code'=>-1,
                                'message'=>'Please checkin your input data',
                                'case'=>$case
                                
                            ];
                        }
                 } else {
                           $respone =[
                            'code'=>-1,
                            'message'=>'Wrong format input',
                           
                        
                        ];  
                    }
                
               
                
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
    public function getAllWorkday(Request $request)
    {
        try {

            $postion = Workday::orderBy('created_at', 'ASC')->get();

            return response()->json(
                ['data' => $postion],
                200
            );
        } catch (Exception $e) {
            //throw $th;
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }
    public function editWorkday(Request $request, $id)
    {
       try {
            $data = Workday::find($id);
            if ($data) {
                $validator = Validator::make($request->all(), [
                    'working_day' => 'required',
                    'off_day' => 'required',
                    'name' => 'required',

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
                    if (str_contains($request['working_day'], ',') ) {
                        $workday = $request['working_day'];
                        $offDay = $request['off_day'];
                        $x = explode(",",$workday); 
                        $y =  explode(",",$offDay);
                        $code = 1;
                        $mgs ="";
                        $input1 = $x;
                        $input2 = $y;
                         $array = array_unique($input1); // Array is now (1, 2, 3)
                        $newWorday = implode(',', array_unique(explode(",",$request['working_day'])));
                        $newOffDay = implode(',', array_unique(explode(",",$request['off_day'])));
                        $result = array_intersect($array, $input2);
                        
                        if(count($result)==0){
                            $code = 0;
                        }else{
                            $code =-1;
                        }
                        if($code == 0){
                            $data->update([
                                'name'           =>$request['name'],
                                'working_day'          =>$newWorday ,
                                'off_day'         => $newOffDay ,
                                'notes'       =>$request['notes'],
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
                    
                }
            } else {
                $respone = [
                    'message' => 'No working id found',
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
    public function deleteWorkday($id)
    {
        try {
            $data = Workday::find($id);
            if ($data) {
                // check if position id belong to employee table
                $emp = User::where('workday_id', $data->id)->first();
                if ($emp) {
                    $respone = [
                        'message' => 'Cannot delete this working day',
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
            } else {
                $respone = [
                    'message' => 'No working id found',
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
    // for set working day to  all department all public

    // location
    public function addLocation(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'lat' => 'required',
                'lon' => 'required',
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
                $data = Location::create([
                    'name' => $request['name'],
                    'lat' => $request['lat'],
                    'lon' => $request['lon'],
                    'address_detail' => $request['address_detail'],
                    'notes' => $request['notes'],

                ]);
                $respone = [
                    'message' => 'Success',
                    'code' => 0,

                ];
                return response()->json(
                    $respone,
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
    public function editLocation(Request $request, $id)
    {
        try {
            $data = Location::find($id);
            if ($data) {
                $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'lat' => 'required',
                    'lon' => 'required',
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
                    $data->name = $request['name'];
                    $data->lat = $request['lat'];
                    $data->lon = $request['lon'];
                    $data->address_detail = $request['address_detail'];
                    $data->notes = $request['notes'];
                    $data->update();
                    $respone = [
                        'message' => 'Success',
                        'code' => 0,
                    ];
                }
            } else {
                $respone = [
                    'message' => 'No department id found',
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
    public function deleteLocation($id)
    {
        try {
            $data = Location::find($id);
            if ($data) {
                // check if position id belong to employee table
                $emp = Department::where('location_id', $data->id)->first();
                if ($emp) {
                    $respone = [
                        'message' => 'Cannot delete this department',
                        'code' => -1,
                    ];
                } else {
                    $data->delete();
                    $respone = [
                        'message' => 'Success',
                        'code' => 0,
                    ];
                }
            } else {
                $respone = [
                    'message' => 'No department id found',
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
    public function getLocation(Request $request)
    {
        try {
            $pageSize = $request->page_size ?? 10;
            $department = Location::orderBy('created_at', 'ASC')->paginate($pageSize);
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
    public function getAllLocation(Request $request)
    {
        try {
            // $pageSize = $request->page_size ??10;
            $department = Location::orderBy('created_at', 'ASC')->get();
            return response()->json(
                ['data' => $department],
                200
            );
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }
    public function getDepartment(Request $request)
    {
        try {
            $pageSize = $request->page_size ?? 10;
            // DESC
            $records = Department::with('location')->orderBy('created_at', 'ASC')->paginate($pageSize);
            foreach ($records as $record) {
                // $checkinStatus = false;
                $maneger_id = "no";
                $maneger_name = "no";

                $checkinRecord = User::find($record->manager);
                // ->first();
                if ($checkinRecord) {
                    $maneger_id = $checkinRecord->id;
                    $maneger_name = $checkinRecord->name;
                } else {
                    $maneger_id = null;
                    $maneger_name = null;
                }
                $record->manager_id = $maneger_id;
                $record->manager_name = $maneger_name;
            }
            return response()->json(
                $records,
                200
            );
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }
    public function getAllDepartment(Request $request)
    {
        try {

            $department = Department::orderBy('created_at', 'ASC')->get();
            return response()->json(
                ['data' => $department],
                200
            );
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }

    public function addDepartment(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'department_name' => 'required',
                // 'workday_id' => 'required',
                'location_id' => 'required'
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
                $department = Department::first();
                $userChief = "";
                if ($department) {

                    // check if the new  department have chief department or not
                    if ($request['manager']) {
                        $userChief = $request['manager'];
                        $x = Department::where('manager', '=', $request['manager'])->first();
                        if ($x) {
                            // remove the user from this department
                            $x->manager = Null;
                            $x->save();
                        }
                        // go to (1)
                    } else {
                        $userChief = Null;
                    }
                    $data = Department::create([
                        'department_name' => $request['department_name'],
                        // 'workday_id' => $request['workday_id'],
                        'location_id' => $request['location_id'],
                        'manager' => $userChief,
                        'notes' => $request['notes']
                    ]);

                    //  // check the user (1)

                    $user = User::find($request['manager']);
                    // check old deparment have this user to be their chief 

                    if ($user) {
                        $user->department_id = $data->id;
                        $user->save();
                        // check usr role to know this user have role chief department already or not
                    }
                    // $role = Role::find($user->role_id);



                } else {

                    $data = Department::create([
                        'department_name' => $request['department_name'],

                        'location_id' => $request['location_id'],

                        'notes' => $request['notes']
                    ]);
                }


                // $is_manager = "";
                // if ($request['manager']) {
                //     $user = User::find($request['manager']);

                //     // check user in department to know the same department or not
                //     if ($user) {
                //         $user->is_manager = "true";


                //         $is_manager =  $request['manager'];
                //     }
                //     // $is_manager =  $request['manager'];
                // } else {
                //     $is_manager = null;
                // }
                // $data = Department::create([
                //     'department_name' => $request['department_name'],
                //     // 'workday_id' => $request['workday_id'],
                //     'location_id' => $request['location_id'],
                //     'manager' => $is_manager,
                //     'notes' => $request['notes']
                // ]);
                // $user->department_id = $data->id;
                // $user->update();

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
    public function editDepartment(Request $request, $id)
    {
        try {
            $data = Department::find($id);
            if ($data) {
                $validator = Validator::make($request->all(), [
                    'department_name' => 'required',
                    // 'workday_id' => 'required',
                    'location_id' => 'required'
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
                    $data->department_name = $request['department_name'];
                    // $data->workday_id = $request['workday_id'];
                    $data->location_id = $request['location_id'];
                    $data->notes   = $request['note'];
                    $userChief = "";
                    $oldDepartment = "";
                    $oldChiefId = "";
                    $case = "";
                    $code = "";
                    $mgs = "";
                    if ($request['manager']) {
                        // if the department before have manager
                        $managerId = $data->manager;
                        if ($data->manager) {
                            if ($managerId == $$request['manager']) {
                                // if manager id and request the same
                                $userChief = $request['manager'];
                                $case = "1";
                                $code = 0;
                            } else {
                                $case = "01";
                                $code = -1;
                                $mgs = "Sorry, please change user role first";
                            }
                        } else {

                            $userChief = $request['manager'];
                            $user = User::find($request['manager']);
                            $oldDepartment = $user->department_id;
                            $oldChiefId = $user->id;
                            // //set new department automatically to user
                            $user->department_id =  $data->id;
                            // $userChief = $request->manager;
                            $user->save();
                            // // check user old department and remove manager=Null  if user 
                            $x = Department::find($oldDepartment);
                            if ($x->manager) {
                                if ($x->manager == $oldChiefId) {
                                    $x->manager = Null;
                                    $x->save();
                                    $case = "4";
                                    $code = 0;
                                } else {
                                    $case = "5";
                                    $code = 0;
                                }
                            } else {
                                $case = "006";
                                $code = 0;
                            }
                            // check if user used to be chief or not

                        }


                        // check manager id the same or not
                    } else {

                        // case department have chief and now don't have ,let remove user that belong to this position before
                        if ($data->manager) {
                            $userChief = Null;
                            // $findUser = User::find($data->manager);
                            $code = -1;
                            $mgs = "Sorry, please change user role first";
                            $case = "6";
                        } else {
                            // case department original don't have chief
                            $userChief = Null;
                            $code = 0;
                            $case = "7";
                        }
                    }
                    if ($code == 0) {
                        $data->manager =  $userChief;
                        $query = $data->update();
                        return response()->json([
                            'code' => 0,
                            'message' => 'Data has been updated successfully!',
                            // 'case'=>$case
                        ]);
                    } else {
                        return response()->json([
                            'code' => -1,
                            'message' => $mgs,
                            // 'case'=>$case
                        ]);
                    }
                    // $is_manager = "";
                    // if ($request['manager']) {
                    //     $user = User::find($request['manager']);
                    //     if ($user) {
                    //         $user->is_manager = "true";

                    //         $is_manager =  $request['manager'];
                    //     }
                    //     // $is_manager =  $request['manager'];
                    // } else {
                    //     $is_manager = null;
                    // }
                    // $data->manager = $is_manager;

                    // $data->update();
                    // $user->department_id = $data->id;
                    // $user->update();

                }
            } else {
                $respone = [
                    'message' => 'No department id found',
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
    public function deleteDepartment($id)
    {
        try {
            $data = Department::find($id);
            if ($data) {
                // check if position id belong to employee table


                $emp = User::where('department_id', $data->id)->first();
                if ($emp) {
                    $respone = [
                        'message' => 'Cannot delete this department',
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
            } else {
                $respone = [
                    'message' => 'No department id found',
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
    // Position
    public function addPosition(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'position_name' => 'required',
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
                $data = Position::create([
                    'position_name' => $request['position_name'],
                    'type' => $request['type'],
                ]);
                $respone = [
                    'message' => 'Success',
                    'code' => 0,
                ];
                return response()->json(
                    $respone,
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
    public function editPosition(Request $request, $id)
    {
        try {
            $data = Position::find($id);
            $validator = Validator::make($request->all(), [
                'position_name' => 'required',
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
                if ($data) {
                    $data->position_name = $request['position_name'];
                    $data->type = $request['type'];
                    $data->update();
                    $respone = [
                        'message' => 'Success',
                        'code' => 0,

                    ];
                } else {
                    $respone = [
                        'message' => 'No position found',
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
                    'message' => $e->getMessage(),
                ]
            );
        }
    }
    public function deletePosition($id)
    {
        try {
            $data = Position::find($id);
            if ($data) {
                $emp = User::where('position_id', $data->id)->first();
                if ($emp) {
                    $respone = [
                        'message' => 'Cannot delete this position',
                        'code' => -1,
                    ];
                } else {
                    $data->delete();
                    $respone = [
                        'message' => 'Success',
                        'code' => 0,
                    ];
                }
            } else {
                $respone = [
                    'message' => 'No position found',
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
    public function getPosition(Request $request)
    {
        try {
            $pageSize = $request->page_size ?? 10;
            $postion = Position::orderBy('created_at', 'ASC')->paginate($pageSize);

            return response()->json(

                $postion,
                200
            );
        } catch (Exception $e) {

            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }
    public function getAllPosition(Request $request)
    {
        try {
            // $pageSize = $request->page_size ??10;
            $postion = Position::orderBy('created_at', 'ASC')->get();

            return response()->json(

                ['data' => $postion],
                200
            );
        } catch (Exception $e) {

            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }
    // timetable
    public function addTimetable(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'timetable_name' => 'required',
                'on_duty_time' => 'required',
                'off_duty_time' => 'required',
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
                $late = '';
                $early = '';
                if ($request['late_minute']) {
                    $late = $request['late_minute'];
                } else {
                    $late = '0';
                }
                if ($request['early_leave']) {
                    $early = $request['early_leave'];
                } else {
                    $early = '0';
                }
                $data = Timetable::create([
                    'timetable_name' => $request['timetable_name'],
                    'on_duty_time' => $request['on_duty_time'],
                    'off_duty_time' => $request['off_duty_time'],
                    'late_minute' => $late,
                    'early_leave' => $early
                ]);
                $respone = [
                    'message' => 'Success',
                    'code' => 0,

                ];
                return response()->json(
                    $respone,
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
    public function editTimetable(Request $request, $id)
    {
        try {
            $data = Timetable::find($id);
            $validator = Validator::make($request->all(), [
                'timetable_name' => 'required',
                'on_duty_time' => 'required',
                'off_duty_time' => 'required',
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
                if ($data) {
                    $data->timetable_name = $request['timetable_name'];
                    $data->on_duty_time = $request['on_duty_time'];
                    $data->off_duty_time = $request['off_duty_time'];
                    $late = '';
                    $early = '';
                    if ($request['late_minute']) {
                        $late = $request['late_minute'];
                    } else {
                        $late = '0';
                    }
                    if ($request['early_leave']) {
                        $early = $request['early_leave'];
                    } else {
                        $early = '0';
                    }
                    $data->late_minute = $late;
                    $data->early_leave = $early;
                    $data->update();
                    $respone = [
                        'message' => 'Success',
                        'code' => 0,

                    ];
                } else {
                    $respone = [
                        'message' => 'No timetable id found',
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
                    'message' => $e->getMessage(),
                ]
            );
        }
    }
    public function deleteTimetable($id)
    {
        try {
            $data = Timetable::find($id);
            if ($data) {
                // check if position id belong to employee table
                $emp = TimetableEmployee::where('timetable_id', $data->id)->first();
                if ($emp) {
                    $respone = [
                        'message' => 'Cannot delete this timetable',
                        'code' => -1,

                    ];
                } else {
                    $data->delete();
                    $respone = [
                        'message' => 'Success',
                        'code' => 0,
                    ];
                }
            } else {
                $respone = [
                    'message' => 'No timetable id found',
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
    public function getTimetable(Request $request)
    {
        try {
            $pageSize = $request->page_size ?? 10;
            $timetable = Timetable::orderBy('created_at', 'ASC')->paginate($pageSize);
            return response()->json(
                $timetable,
                200
            );
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }
    public function getAllTimetable(Request $request)
    {
        try {

            $timetable = Timetable::orderBy('created_at', 'ASC')->get();
            return response()->json(
                ['data' => $timetable],
                200
            );
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }
    public function getRole(Request $request)
    {
        try {

            $timetable = Role::orderBy('created_at', 'ASC')->get();
            return response()->json(
                ['data' => $timetable],
                200
            );
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }
    public function addemployee(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'gender' => 'required',
                'username' => 'required|string|unique:users,username',
                'department_id' => 'required',
                'position_id' => 'required',
                'workday_id'     => 'required',
                'timetable_id'     => 'required',
                'role_id'     => 'required',
                'password' => 'required',

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
                 $merital_status ="";
                $couple_job ="";
                $minor_children ="";
                if($request['merital_status']){
                    if($request['merital_status'] =="single"){
                        $merital_status ="single";
                    }elseif($request['merital_status'] =="divorced"){
                        $merital_status ="divorced";
                        if($request['minor_children']){
                            $minor_children =$request['minor_children'];
                        }else{
                            $minor_children ="0";
                        }
                    }
                    else{
                        $merital_status ="married";
                        if($request['minor_children']){
                            $minor_children =$request['minor_children'];
                        }else{
                            $minor_children ="0";
                        }
                        if($request['spouse_job']){
                            $couple_job =$request['spouse_job'];
                        }else{
                            $couple_job ="";
                        }
                    }
                }

                $data = User::create([
                    'name' => $request['name'],
                     'no' => $request['staff_id'],
                    'gender' => $request['gender'],
                    'dob' => $request['dob'],
                    'nationality' => $request['nationality'],
                    'username' => $request['username'],
                    'email' => $request['email'],
                    'profile_url' => $request['profile_url'],
                    'office_tel' => $request['office_tel'],
                    'employee_phone' => $request['employee_phone'],
                    'address' => $request['address'],
                    'card_number' => $request['card_number'],
                    'spouse_job' => $couple_job,

                    'position_id' => $request['position_id'],
                    'department_id' => $request['department_id'],

                    'merital_status' => $merital_status,
                    'minor_children' => $minor_children,
                
                    'role_id' => $request['role_id'],
                    'timetable_id' => $request['timetable_id'],
                    'workday_id' => $request['workday_id'],
                    'status' => "false",
                    'payslip_status' => "0",

                    'password' => bcrypt($request['password'])
                ]);
                $findRole = Role::find($request['role_id']);
                if (str_contains($findRole, 'Cheif')) {
                    $depart = Department::find($request['department_id']);
                    $depart->manager = $data->id;
                    $depart->save();
                }
                $respone = [
                    'message' => 'Success',
                    'code' => 0,

                ];
                $holiday = Holiday::orderBy('from_date', 'ASC')->get();

                $total = 0;
                for ($i = 0; $i < count($holiday); $i++) {
                    $result1 = Carbon::createFromFormat('Y-m-d', $holiday[$i]['from_date'])->isPast();
                    $result2 = Carbon::createFromFormat('Y-m-d', $holiday[$i]['to_date'])->isPast();
                    if ($result1 == false || $result2 == false) {
                        $total += $holiday[$i]['duration'];
                    } else {
                        $total = 0;
                    }
                }
                // create counter for user
                $meternity_leave ="0";
                $peternity_leave ="0";
                $marriage_leave="0";
                $funeral_leave="0";
                $hospitality_leave="0";
                
                $leavetype = Leavetype::all();
                for($i=0;$i<count($leavetype);$i++){
                    if($leavetype[$i]['parent_id']==0){
                        if ($leavetype[$i]['duration'] == "0") {
                            
                        }
                        elseif (str_contains($leavetype[$i]['duration'], 'month')) {
                            $meternity_leave =$leavetype[$i]['duration'];
                        }else{
                            $hospitality_leave=$leavetype[$i]['duration'];
                        }
                    }else{
                        // special leave have 3 
                        if(str_contains($leavetype[$i]['leave_type'], 'Marriage')){
                            $marriage_leave=$leavetype[$i]['duration'];
                             
                        }elseif(str_contains($leavetype[$i]['leave_type'], 'Peternity')){
                            $peternity_leave =$leavetype[$i]['duration'];
                        }else{
                            $funeral_leave=$leavetype[$i]['duration'];
                        }
                    }
                }
                if($data->gender=="F"){
                    $meternity_leave = $meternity_leave;
                    $peternity_leave ="0";
                }else{
                    $meternity_leave = "0";
                    $peternity_leave =$peternity_leave;
                }

                $c = Counter::create([
                    'user_id'           =>  $data->id,
                    'ot_duration'          => '0',
                    'total_ph'         => $total,
                    'hospitality_leave'       => $hospitality_leave,
                    'marriage_leave'=>$marriage_leave,
                    'peternity_leave'=>$peternity_leave,
                    'maternity_leave'=>$meternity_leave,
                    'funeral_leave'=> $funeral_leave
                ]);
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
    public function editemployee(Request $request, $id)
    {
        try {
            $data = User::find($id);
            $roleId = "";
            $manager = "";
            $userId = "";
            $case = "";

            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'gender' => 'required',
                'department_id' => 'required',
                'workday_id'     => 'required',
                'timetable_id'     => 'required',
                'role_id'     => 'required',
                'position_id' => 'required',
            ]);
            if ($data) {
                $roleId = $data->role_id;
                $departmentId = $data->department_id;
                $userId = $data->id;
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
                    // $query = $request->all();
                    $merital_status ="";
                    $couple_job ="";
                    $minor_children ="";
                    if($request['merital_status']){
                        if($request['merital_status'] =="single"){
                            $merital_status ="single";
                        }elseif($request['merital_status'] =="divorced"){
                            $merital_status ="divorced";
                            if($request['minor_children']){
                                $minor_children =$request['minor_children'];
                            }else{
                                $minor_children ="0";
                            }
                        }
                        else{
                            $merital_status ="married";
                            if($request['minor_children']){
                                $minor_children =$request['minor_children'];
                            }else{
                                $minor_children ="0";
                            }
                            if($request['spouse_job']){
                                $couple_job =$request['spouse_job'];
                            }else{
                                $couple_job ="";
                            }
                        }
                    }
                    if ($roleId == $request['role_id'] && $departmentId == $request['department_id']) {
                        // check department , in case change department so remove cheif department before , insert update other department
                        $case = "0";
                    } elseif ($roleId == $request['role_id'] && $departmentId != $request['department_id']) {
                        // let change department chief
                        $de = Department::find($departmentId);
                        $de->manager = null;
                        $de->save();
                        $case = "1";
                        // find new department 
                        $new = Department::find($request['department_id']);
                        $new->manager = $userId;
                        $new->save();
                    } elseif ($roleId != $request['role_id'] && $departmentId == $request['department_id']) {
                        // change only role, check if new role is chief department
                        $findRole = Role::find($request['role_id']);
                        if (str_contains($findRole, 'Cheif')) {
                            $depart = Department::find($request['department_id']);
                            $depart->manager =  $userId;
                            $manager = $userId;
                            $depart->save();
                            $case = "2";
                        } else {
                            $case = "3";
                        }
                    } else {
                        // before role :accountant , and change to chief department
                        // deferent role and different department
                        // deferent role Id 
                        // $case = "4";
                        $findRole = Role::find($request['role_id']);
                        if (str_contains($findRole, 'Cheif')) {
                            $depart = Department::find($request['department_id']);
                            $depart->manager =  $userId;
                            $manager = $userId;
                            $depart->save();
                            $case = "4";
                        } else {
                            $case = "5";
                        }
                    }
                    $data->name = $request['name'];
                    $data->no = $request['staff_id'];
                    $data->gender = $request['gender'];
                    $data->nationality = $request['nationality'];
                    $data->dob = $request['dob'];
                    $data->office_tel = $request['office_tel'];
                    $data->card_number = $request['card_number'];
                     $data->profile_url = $request['profile_url'];
                   
                    $data->address = $request['address'];
                    $data->employee_phone = $request['employee_phone'];
                    $data->merital_status = $merital_status;
                    $data->minor_children = $minor_children;
                    $data->spouse_job = $couple_job;
                    $data->email = $request['email'];
                    $data->position_id = $request['position_id'];
                    $data->department_id = $request['department_id'];
                    $data->role_id = $request['role_id'];
                    $data->timetable_id = $request['timetable_id'];
                    $data->workday_id = $request['workday_id'];
                    $data->update();
                    $respone = [
                        'message' => 'Success',
                        'code' => 0,

                    ];
                }
            } else {
                $respone = [
                    'message' => 'No employee id found',
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
    public function deleteEmployee($id)
    {
        try {
            $data = User::find($id);
            if ($data) {
                // check if position id belong to employee table
                $emp = Checkin::where('user_id', $data->id)->first();
                //  $emp = TimetableEmployee::where('user_id', $data->id)->first();
                if ($emp) {
                    $respone = [
                        'message' => 'cannot delete employee who already checkin',
                        'code' => -1,
                    ];
                } else {
                    $data->delete();
                    $respone = [
                        'message' => 'Success',
                        'code' => 0,
                    ];
                }
            } else {
                $respone = [
                    'message' => 'No employee id found',
                    'code' => -1,

                ];
            }
            return response()->json(
                $respone,
                200
            );
        } catch (Exception $e) {

            return response([
                'message' => $e->getMessage()
            ]);
        }
    }
    // employee by department
    public function getEmployeeByDepartment(Request $request, $id)
    {
        try {
            $pageSize = $request->page_size ?? 10;

            $department = User::where('department_id', $id)->paginate($pageSize);
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

    public function getEmployee(Request $request)
    {
        try {
            //code...
            // get user by token with timetable
            $use_id = $request->user()->id;
            $pageSize = $request->page_size ?? 10;
            $todayDate = Carbon::now()->format('m/d/Y');
            // $postion = Timetable::paginate($pageSize);
            //count checkin ,late ,overtime, total employee
            // $records = Employee::all();
            $records = User::whereNotIn('id', [1])->orderBy('created_at', 'ASC')

                ->paginate($pageSize);
            // checkin
            foreach ($records as $record) {
                $checkinStatus = "false";
                $checkinRecord = Checkin::where('user_id', $record->id)
                    ->where('date', '=', $todayDate)
                    // ->where('status','!=','leave')
                    // ->latest()
                    ->first();
                // check work or not
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
            }




            return response(
                $records,
                200
            );
        } catch (Exception $e) {

            return response([
                'message' => $e->getMessage()
            ]);
        }
    }
    public function getEmployeeList(Request $request)
    {
        try {

            $pageSize = $request->page_size ?? 10;
            $todayDate = Carbon::now()->format('m/d/Y');

            $records = User::with('department', 'workday', 'position', 'role', 'timetable')->whereNotIn('id', [1])->orderBy('created_at', 'ASC')->paginate($pageSize);
            return response(
                $records,
                200
            );
        } catch (Exception $e) {

            return response([
                'message' => $e->getMessage()
            ]);
        }
    }
    public function getEmployeeDetail(Request $request, $id)
    {
        try {
            $todayDate = Carbon::now()->format('m/d/Y');
            $records = User::with('department', 'workday', 'position', 'role', 'timetable')->whereNotIn('id', [1])
                ->where('id', '=', $id)->first();
            // checkin
            if ($records) {
                $checkinStatus = "false";
                $checkinRecord = Checkin::where('user_id', $records->id)
                    ->where('date', '=', $todayDate)
                    // ->where('status','!=','leave')
                    // ->latest()
                    ->first();
                // check work or not
                $work = "";
                $dayoff = Workday::find($records->workday_id);
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
                $records->workday = $work;
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
                // new field create after compare with checkin
                $records->checkin_status = $checkinStatus;

                $records->checkin = $checkinRecord;
                if ($checkinRecord) {
                    $records->checkin_id = $checkinRecord['id'];
                } else {
                    $records->checkin_id = null;
                }
            }
            return response(
                $records,
                200
            );
        } catch (Exception $e) {

            return response([
                'message' => $e->getMessage()
            ]);
        }
    }
    public function getAttendanceEmployee(Request $request)
    {
        try {
            //code...
            // get user by token with timetable
            $use_id = $request->user()->id;
            $pageSize = $request->page_size ?? 10;
            // $todayDate = Carbon::now()->format('m/d/Y');

            $typedate = Workday::first();
            $todayDate = "";
            if ($typedate->type_date_time == "server") {
                $todayDate = Carbon::now()->format('m/d/Y');
            } else {
                $todayDate = date('m/d/Y', strtotime($request->get('date')));
            }
            $records = User::whereNotIn('id', [1])->orderBy('created_at', 'ASC')

                ->paginate($pageSize);
            // $records =User::select( DB::raw('users.name'))->get();
            // checkin
            foreach ($records as $record) {
                $checkinStatus = "false";
                $checkinRecord = Checkin::where('user_id', $record->id)
                    ->where('date', '=', $todayDate)
                    // ->where('status','!=','leave')
                    // ->latest()
                    ->first();
                // check work or not
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



                // new field create after compare with checkin
                $record->checkin_status = $checkinStatus;

                // $record->checkin = $checkinRecord;
                if ($checkinRecord) {
                    $record->checkin_id = $checkinRecord['id'];
                } else {
                    $record->checkin_id = null;
                }
            }






            return response(
                $records,
                200
            );
        } catch (Exception $e) {

            return response([
                'message' => $e->getMessage()
            ]);
        }
    }
    public function getAllEmployee(Request $request)
    {
        try {

            $use_id = $request->user()->id;
            $records = User::whereNotIn('id', [1])->orderBy('created_at', 'ASC')
                ->get();
            return response(
                ['data' => $records],
                200
            );
        } catch (Exception $e) {

            return response([
                'message' => $e->getMessage()
            ]);
        }
    }

    public function searchEmployee(Request $request, $query)
    {
        try {
            // $pageSize = $request->page_size ??10;
            $data = User::where('name', 'LIKE', '%' . $query . '%')
                ->orWhere('employee_phone', 'LIKE', '%' . $query . '%')
                ->orWhere('address', 'LIKE', '%' . $query . '%')
                ->orWhere('gender', 'LIKE', '%' . $query . '%')
                ->orWhere('nationality', 'LIKE', '%' . $query . '%')
                ->get();
            if ($data) {
                return response()->json(
                    [
                        'message' => "Success",
                        'code' => 0,
                        'data' => $data
                    ],
                    200
                );
            }
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }


    public function checkin(Request $request)
    {
        try {
            $status = ["on time", "very good", "late", "too late"];
            $todayDate = Carbon::now()->format('m/d/Y');
            $checkinTimeServer = Carbon::now()->format('H:i:s');
            $today = Carbon::now()->format('Y-m-d');
            // $checkinTime = $request['checkin_time'];
            $targetStatus = "";
            $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                // 'checkin_time' => 'required|string',
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
                $employee = User::find($request['user_id']);
                $otStatus = "false";

                if ($employee) {
                    $overtime = Overtime::where('user_id', '=', $employee->id)
                        ->where('status', '=', 'approved')
                        ->where('from_date', '=', $today)
                        ->orWhere('to_date', '=', $today)->latest()->first();
                    $position = Position::find($employee->position_id);
                    $scann = Checkin::where('user_id', '=', $employee->id)->latest()->first();
                    // $dateIn = $request['date'];
                    $i = "";
                    $findtime = Timetable::find($employee->timetable_id);
                    $userCheckin = Carbon::parse($checkinTimeServer);
                    $userDuty = Carbon::parse($findtime->on_duty_time);
                    $diff = $userCheckin->diff($userDuty);
                    $hour = ($diff->h) * 60;
                    $minute = $diff->i;
                    $code = "";
                    $min = "";
                    $lateMinuteAdmin = $findtime['late_minute'];
                    $duration = 0;


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
                                $checkin = Checkin::create([
                                    'checkin_time' => $checkinTimeServer,
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
                                $respone = [
                                    'message' => 'Success',
                                    'code' => 0,
                                ];;
                            } else {

                                //  if admin set late minute
                                if ($findtime['on_duty_time'] == $checkinTimeServer) {
                                    $targetStatus = $status[0];
                                    $min = ($minute + $hour);
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
                                $checkin = Checkin::create([
                                    'checkin_time' => $checkinTimeServer,
                                    'date' => $todayDate,
                                    'status' => "checkin",
                                    'checkin_status' => $targetStatus,
                                    'checkin_late' => $min,
                                    'send_status' => 'false',
                                    'user_id' => $employee->id,
                                    'confirm' => 'false',
                                    'ot_status' =>  $otStatus,
                                    'duration' => $duration,

                                ]);
                                $respone = [
                                    'message' => 'Success',
                                    'code' => 0,

                                ];
                            }
                            if ($overtime) {
                                // $otStatus = "true";
                                $overtime->status = "completed";
                                $overtime->update();
                                if ($overtime->pay_type == "holiday") {
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


                            ]);
                            $respone = [
                                'message' => 'Success',
                                'code' => 0,
                            ];
                        } else {

                            //  if admin set late minute
                            if ($findtime['on_duty_time'] == $checkinTimeServer) {
                                $targetStatus = $status[0];
                                $min = "0";
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
                            $checkin = Checkin::create([
                                'checkin_time' => $checkinTimeServer,
                                'date' => $todayDate,
                                'status' => "checkin",
                                'checkin_status' => $targetStatus,
                                'checkin_late' => $min,
                                'user_id' => $employee->id,
                                'confirm' => 'false',
                                'send_status' => 'false',
                                'duration' => $duration,
                                'ot_status' =>  $otStatus,

                            ]);
                            $respone = [
                                'message' => 'Success',
                                'code' => 0,

                            ];
                        }
                        if ($overtime) {
                            $otStatus = "true";
                            $overtime->status = "completed";
                            $overtime->update();
                            if ($overtime->pay_type == "holiday") {
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
            }
        } catch (Exception $e) {
            return response([
                'message' => $e->getMessage()
            ]);
        }
    }
    public function getAttendance(Request $request)
    {
        try {
            //code...
            // get user by token with timetable
            $use_id = $request->user()->id;
            $pageSize = $request->page_size ?? 10;
            $todayDate = Carbon::now()->format('m/d/Y');
            $checkin = Checkin::with('user')
                ->where('date', '=', $todayDate)

                ->paginate($pageSize);
            // $data = Leave::with('leavetype')->paginate($pageSize);
            // $respone = [
            //     'message' => 'Success',
            //     'code' => 0,
            //     'data' => $data,
            // ];
            return response(
                $checkin,
                200
            );
        } catch (Exception $e) {
            //throw $th;
            return response([
                'message' => $e->getMessage()
            ]);
        }
    }
    public function markAbsent(Request $request)
    {
        try {
            $todayDate = Carbon::now()->format('m/d/Y');
            $validator = Validator::make($request->all(), [
                'user_id' => 'required',
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
                $em = User::find($request['user_id']);
                if ($em) {
                    $data = Checkin::where('user_id', $request['user_id'])
                        ->where('date', $todayDate)->first();
                    if ($data) {
                        $respone = [
                            'message' => 'Employee already has action ',
                            'code' => -1,
                        ];
                    } else {
                        $user = Checkin::create([
                            'user_id' => $request['user_id'],
                            'checkin_time' => '0',
                            'checkout_time' => '0',
                            'status' => 'absent',
                            'date' => $todayDate,
                            'checkin_status' => 'absent',
                            'checkout_status' => 'absent',
                            'checkout_late' => '0',
                            'checkin_late' => '0',
                            'note' => 'mark by admin',
                            'send_status' => 'false',
                            'confirm' => 'false',
                            'duration' => '0',
                            'ot_status' =>  'false',
                        ]);
                        $respone = [
                            'message' => 'Success',
                            'code' => 0,
                        ];
                    }
                } else {
                    $respone = [
                        'message' => 'No employee id found',
                        'code' => -1,
                    ];
                }

                return response()->json(
                    $respone,
                    200
                );
            }
        } catch (Exception $e) {
            //throw $th;
            return response([
                'message' => $e->getMessage()
            ]);
        }
    }

    public function checkout(Request $request, $id)
    {
        try {
            $status = ["good", "very good", "early", "too fast"];
            $todayDate = Carbon::now()->format('m/d/Y');
            $targetStatus = "";
            // $findCheckid = Checkin::find($id);
            $findCheckid = Checkin::find($id);
            $timeNow = Carbon::now()->format('H:i:s');
            $checkoutTimeServer = $timeNow;
            if ($findCheckid) {
                $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    // 'checkout_time' => 'required|string',
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
                    $employee = User::find($request["user_id"]);


                    if ($employee) {


                        $position = Position::find($employee->position_id);
                        // retain condition user can checkout two time in oneday
                        $scann = Checkin::where('user_id', '=', $employee->id)
                            ->where('id', $id)
                            ->whereNull('checkout_time')
                            ->latest()->first();
                        if ($scann) {
                            $findtime = Timetable::find($employee->timetable_id);

                            $userCheckin = Carbon::parse($checkoutTimeServer);
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
                            $duration = 0;

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

                            // check employee type first
                            if ($scann->date = $todayDate) {
                                // $schedule = TimetableEmployee::where('user_id', $request['user_id'])->first();
                                $workday = Workday::find($employee->workday_id);
                                $countOffDay = explode(",", $workday->off_day);

                                $contract = Contract::where('user_id', '=', $employee->id)->first();
                                if ($contract) {
                                    $standardHour = ($contract->working_schedule) / (7 - count($countOffDay));
                                } else {
                                    $standardHour = 9;
                                }
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
                                        if ($findtime['off_duty_time'] ==  $checkoutTimeServer) {
                                            $targetStatus = $status[0];
                                            $min = "0";
                                            $code = 0;
                                        }
                                        // 07:30 // 6
                                        // 05:30 => 6:00
                                        // 05:30, 5:00
                                        elseif ($findtime['off_duty_time'] >  $checkoutTimeServer) {
                                            $targetStatus = $status[3];
                                            $min = $minute + $hour;
                                            $code = 0;
                                        }
                                        // 08:00,8:30
                                        elseif ($findtime['off_duty_time'] <  $checkoutTimeServer) {
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
                                        $scann->checkout_time =  $checkoutTimeServer;
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
                                        if ($findtime['off_duty_time'] ==  $checkoutTimeServer) {
                                            $targetStatus = $status[0];
                                            $min = "0";
                                            $code = 0;
                                        }
                                        // 07:30 // 6
                                        // 05:30 => 6:00
                                        // 05:30, 5:00
                                        elseif ($findtime['off_duty_time'] >  $checkoutTimeServer) {
                                            $targetStatus = $status[3];
                                            $min = $minute + $hour;
                                            $code = 0;
                                        }
                                        // 08:00,8:30
                                        elseif ($findtime['off_duty_time'] <  $checkoutTimeServer) {
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
                                        $scann->user_id = $request['user_id'];
                                        $scann->checkout_time =  $checkoutTimeServer;
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
                }
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
    function getWeekday($date)
    {
        return date('w', strtotime($date));
    }
    public function deleteCheckin($id)
    {
        // try {
        //     $data = Checkin::find($id);

        //     if ($data) {
        //         // $checkin = Checkin::find($id)->whereNull('checkout_time')->latest()->first();
        //         // check if position id belong to employee table
        //         if ($data->checkout_time) {
        //             $respone = [
        //                 'message' => 'cannot delete checkin',
        //                 'code' => -1,
        //             ];
        //         } else {
        //             $data->delete();
        //             $respone = [
        //                 'message' => 'Success',
        //                 'code' => 0,
        //             ];
        //         }
        //     } else {
        //         $respone = [
        //             'message' => 'No checkin id found',
        //             'code' => -1,
        //         ];
        //     }
        //     return response()->json(
        //         $respone,
        //         200
        //     );
        // } catch (Exception $e) {
        //     //throw $th;
        //     return response([
        //         'message' => $e->getMessage()
        //     ]);
        // }
    }
    public function getleave(Request $request)
    {
        try {
            //code...
            // get user by token with
            $use_id = $request->user()->id;
            $pageSize = $request->page_size ?? 10;
            $fromDate = $request->get('from_date');
            $toDate = $request->get('to_date');
            // $todayDate = Carbon::now()->format('m/d/Y');

            if ($request->has('from_date') && $request->has('to_date')) {

                $data = Leave::with('user', 'leavetype')->whereDate('leaves.created_at', '>=', date('Y-m-d', strtotime($fromDate)))
                    ->whereDate('leaves.created_at', '<=', date('Y-m-d', strtotime($toDate)))
                    ->orderBy('created_at', 'ASC')->paginate($pageSize);
            } else {
                $data = Leave::with('user', 'leavetype')
                    ->orderBy('created_at', 'ASC')->paginate($pageSize);
            }

            //


            return response(
                $data,
                200
            );
        } catch (Exception $e) {
            //throw $th;
            return response([
                'message' => $e->getMessage()
            ]);
        }
    }
    // public function addLeave(Request $request)
    // {
    //     try {
    //         $todayDate = Carbon::now()->format('m/d/Y');
    //         $validator = Validator::make($request->all(), [
    //             'user_id' => 'required',
    //             'leave_type_id' => 'required',
    //             'reason' => 'required',
    //             'number' => 'required',
    //             'from_date' => 'required',
    //             'to_date' => 'required',
    //         ]);
    //         if ($validator->fails()) {
    //             $error = $validator->errors()->all()[0];
    //             return response()->json(
    //                 [
    //                     // 'status'=>'false',
    //                     'message' => $error,
    //                     'code' => -1,
    //                     // 'data'=>[]
    //                 ],
    //                 201
    //             );
    //         } else {
    //             $findEm = Employee::find($request['user_id']);
    //             if ($findEm) {
    //                 $findLe = Leavetype::find($request['leave_type_id']);
    //                 if ($findLe) {
    //                     $user = Leave::create([
    //                         'user_id' => $request['user_id'],
    //                         'leave_type_id' => $request['leave_type_id'],
    //                         'reason' => $request['reason'],
    //                         'status' => 'pending',
    //                         'date' => $todayDate,
    //                         'number' => $request['number'],
    //                         'from_date' => $request['from_date'],
    //                         'to_date' => $request['to_date'],
    //                         'note' => $request['note']
    //                     ]);
    //                     $respone = [
    //                         'message' => 'Success',
    //                         'code' => 0,

    //                     ];
    //                 } else {
    //                     $respone = [
    //                         'message' => 'No leavetype id found',
    //                         'code' => -1,

    //                     ];
    //                 }
    //             } else {
    //                 $respone = [
    //                     'message' => 'No employee id found',
    //                     'code' => -1,

    //                 ];
    //             }






    //             return response()->json($respone, 200);
    //         }
    //     } catch (Exception $e) {
    //         return response()->json(
    //             [

    //                 'message' => $e->getMessage(),

    //             ],
    //             500
    //         );
    //     }
    // }
    public function editLeave(Request $request, $id)
    {
        try {
            //code...
            // get user by token with timetable

            $data = Leave::find($id);
            $todayDate = Carbon::now()->format('m/d/Y');
            $timeNow = Carbon::now()->format('H:i:s');
            $status = "";
            $message = "";
            if ($data) {
                $validator = Validator::make($request->all(), [

                    'status' => 'required',
                    'leave_deduction' => 'required'
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
                    $value = "";
                    if ($request['leave_deduction']) {
                        if (str_contains($request['leave_deduction'], '$')) {
                            $value = explode('$', $request['leave_deduction']);
                            $data->leave_deduction = $value[0];
                        } else {
                            $data->leave_deduction = $request['leave_deduction'];
                        }
                    } else {
                        $data->leave_deduction = "0";
                    }


                    // $data->leave_deduction = $request['leave_deduction'];

                    $query = $data->save();
                    $profile = User::find($data->user_id);
                    //  $startDate = date('Y-m-d',strtotime($data->from_date));
                    $startDate = date('m/d/Y', strtotime($data->from_date));
                    $endDate = date('m/d/Y', strtotime($data->to_date));
                    if ($data->status == "rejected") {
                        $message = "Your leave request has been rejected!";
                    }
                    if ($data->status == 'approved') {
                        $message = "Your leave request has been approved!";
                        // check type of leave late 1 hour, permission half day or 1 day 2 day
                        if ($data->type == "hour") {
                            // if one hour 
                            // $respone = [
                            //     'message' => 'Success',
                            //     'code' => 0,
                            // ];
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
                            // $respone = [
                            //     'message' => 'Success',
                            //     'code' => 0,
                            // ];
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

                            // $respone = [
                            //     'message' => 'Success',
                            //     'code' => 0,
                            // ];
                        }
                    }

                    $respone = [
                        'message' => 'Success',
                        'code' => 0,
                    ];
                    if ($profile->device_token) {
                        $url = 'https://fcm.googleapis.com/fcm/send';
                        $dataArr = array(
                            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                            'id' => $request->id,
                            'target' => 'inform_leave',
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
                    // cut on counter
                    $leavetype = Leavetype::find($data->leave_type_id);
                    $counter = Counter::where('user_id', '=', $data->user_id)->first();
                    $counterLeft = 0;
                    if (str_contains($leavetype->leave_type, 'Hospitality')) {
                        $counterLeft =  $counter->hospitality_leave - $data->number;
                        $counter->hospitality_leave = $counterLeft;
                    } elseif (str_contains($leavetype->leave_type, 'Marriage')) {
                        $counterLeft =  $counter->marriage_leave - $data->number;
                        $counter->marriage_leave = $counterLeft;
                    } elseif (str_contains($leavetype->leave_type, 'Peternity')) {
                        $counterLeft =  $counter->peternity_leave - $data->number;
                        $counter->peternity_leave = $counterLeft;
                    } elseif (str_contains($leavetype->leave_type, 'Funeral')) {
                        $counterLeft =  $counter->funeral_leave - $data->number;
                        $counter->funeral_leave = $counterLeft;
                    } elseif (str_contains($leavetype->maternity_leave, 'Maternity')) {
                        // $counterLeft =  0;
                        $counter->maternity_leave = 0;
                    } else {
                        $counterLeft =  0;
                    }
                    $counter->update();
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
    public function deleteLeave($id)
    {
        try {
            $data = Leave::find($id);
            if ($data) {
                if ($data->status == "pending") {
                    $data->delete();
                    $respone = [
                        'message' => 'Success',
                        'code' => 0,
                    ];
                } else {
                    $respone = [
                        'message' => 'Cannot delete',
                        'code' => -1,
                    ];
                }

                // $em = Position::with('employee');
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
    public function getLeaveType(Request $request)
    {
        try {
            $pageSize = $request->page_size ?? 10;
            $department = Leavetype::orderBy('created_at', 'ASC')->paginate($pageSize);
            return response()->json(

                $department,
                200
            );
        } catch (Exception $e) {
            //throw $th;
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }
    public function getAllLeaveType(Request $request)
    {
        try {
            // $pageSize = $request->page_size ??10;
            $department = Leavetype::orderBy('created_at', 'ASC')->get();
            return response()->json(

                ['data' => $department],
                200
            );
        } catch (Exception $e) {
            //throw $th;
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }
    public function addLeaveType(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'leave_type' => 'required',
                'duration' => 'required',


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

                $data = new Leavetype();
                $parentId = 0;
                $data->leave_type   = $request['leave_type'];
                $data->notes = $request['notes'];
                $scope = "0";
                if ($request['duration']) {
                    $scope = $request['duration'];
                }
                if ($request['parent_id']) {
                    $parentId = $request['parent_id'];
                }
                $data->duration   = $scope;
                $data->parent_id   = $parentId;

                $data->save();
               
                // check user
                $user = User::first();
                if($user){
                    $peternity =0;
                    $meternity =0;
                    $marriage =0;
                    $hosipitality =0;
                    $funeral =0;
                    if(str_contains($request['leave_type'], 'Peternity')){
                        $peternity =$scope;
                    }elseif(str_contains($request['leave_type'], 'Meternity')){
                        $meternity =$scope;
                    }
                    elseif(str_contains($request['leave_type'], 'Marriage')){
                        $marriage =$scope;
                    }
                    elseif(str_contains($request['leave_type'], 'Funeral')){
                        $funeral =$scope;
                    }
                    elseif(str_contains($request['leave_type'], 'Hospitality')){
                        $hosipitality  =$scope;
                    }else{
    
                    }
                    $counter = Counter::all();
                   
                   for($i=0; $i< count($counter);$i++){
                    // check user that change dayoff
                        $counter[$i]['hospitality_leave'] =  $hosipitality;
                        $counter[$i]['marriage_leave'] =  $marriage;
                        $counter[$i]['peternity_leave'] =  $peternity;
                        $counter[$i]['funeral_leave'] =  $funeral;
                        $counter[$i]['maternity_leave'] =   $meternity;
                        $query = $counter[$i]->update();
                   }
                   
                }else{

                }

                
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
    public function editLeaveType(Request $request, $id)
    {
        try {
            $data = Leavetype::find($id);
            if ($data) {
                $validator = Validator::make($request->all(), [
                    'leave_type' => 'required',


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

                    $parentId = 0;
                    $data->leave_type   = $request['leave_type'];
                    $data->notes = $request['notes'];
                    $scope = "0";
                    if ($request['duration']) {
                        $scope = $request['duration'];
                    }
                    if ($request['parent_id']) {
                        $parentId = $request['parent_id'];
                    }
                    $data->duration   = $scope;
                    $data->parent_id   = $parentId;
                   
                    
                    $data->update();

                    $respone = [
                        'message' => 'Success',
                        'code' => 0,

                    ];
                }
            } else {
                $respone = [
                    'message' => 'No leavetype id found',
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
    public function deleteLeaveType($id)
    {
        try {
            $data = Leavetype::find($id);
            if ($data) {
                // check if position id belong to employee table
                //  $postion = Leavetype::with('leaves')->where('id',$id)->get();

                $emp = Leave::where('leave_type_id', $data->id)->first();
                if (!$emp) {
                    if ($data->parent_id == 0) {
                        $data->delete();
                        $respone = [
                            'message' => 'Success',
                            'code' => 0,
                        ];
                    } else {
                        $respone = [
                            'message' => 'Cannot delete this leave type',
                            'code' => -1,

                        ];
                    }
                } else {
                    $respone = [
                        'message' => 'Cannot delete this leave type',
                        'code' => -1,

                    ];
                }
            } else {
                $respone = [
                    'message' => 'No leavetype id found',
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


    public function create()
    {
        //
    }
    public function report(Request $request)
    {
        try {
            $fromDate = $request->get('from_date');
            $toDate = $request->get('to_date');
            $todayDate = Carbon::now()->format('m/d/Y');
            $data = User::whereNotIn('id', [1])->count();

            if ($request->has('from_date') && $request->has('to_date')) {

                $checkin = Checkin::whereDate('checkins.created_at', '>=', date('Y-m-d', strtotime($fromDate)))
                    ->whereDate('checkins.created_at', '<=', date('Y-m-d', strtotime($toDate)))->count();
                $leave = Leave::whereDate('leaves.created_at', '>=', date('Y-m-d', strtotime($fromDate)))
                    ->whereDate('leaves.created_at', '<=', date('Y-m-d', strtotime($toDate)))->count();
                $late = Checkin::
                    // ->whereNotNull('checkout_time')
                    where('checkin_status', 'late')
                    ->whereDate('checkins.created_at', '>=', date('Y-m-d', strtotime($fromDate)))
                    ->whereDate('checkins.created_at', '<=', date('Y-m-d', strtotime($toDate)))
                    ->count();
                // absent
                $absent = Checkin::
                    // ->whereNotNull('checkout_time')
                    where('checkin_status', 'absent')
                    ->whereDate('checkins.created_at', '>=', date('Y-m-d', strtotime($fromDate)))
                    ->whereDate('checkins.created_at', '<=', date('Y-m-d', strtotime($toDate)))
                    ->where('status', '=', 'scanned')
                    ->count();

                $checkout = Checkin::whereNotNull('checkout_time')->whereDate('checkins.created_at', '>=', date('Y-m-d', strtotime($fromDate)))
                    ->whereDate('checkins.created_at', '<=', date('Y-m-d', strtotime($toDate)))
                    ->where('status', '=', 'present')
                    ->count();
                $overtime = Checkin::where('checkout_status', 'good')

                    ->whereDate('checkins.created_at', '>=', date('Y-m-d', strtotime($fromDate)))
                    ->whereDate('checkins.created_at', '<=', date('Y-m-d', strtotime($toDate)))->count();
            }
            $respone = [
                'message' => 'Success',
                'code' => 0,
                'late' => $late,
                'total_employee' => $data,
                'overtime' => $overtime,
                'checkin' => $checkin,
                'checkout' => $checkout,
                'leave' => $leave,
                'absent' =>  $absent
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
    public function upload(Request $request)
    {
        try {

            // file(file):keywork in postman, if put photo , file(photo)
            $uploadedFileUrl = Cloudinary::upload($request->file('file')->getRealPath(), [
                'folder' => 'employee'
            ])->getSecurePath();
            // $data->profile_url= $uploadedFileUrl;
            // $result = $request->file('file')->store('uploads/employee', 'photo');
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
    public function addNotification(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'body' => 'required'

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
                $todayDate = Carbon::now()->format('Y-m-d');
                // $timeNow = Carbon::now()->format('H:i:s');
                $timeNow = Carbon::now()->format('H:i');
                $code = "";
                $time = "";
                $status = 'pending';
                $data = new Notification();
                $case ="0";
                // $check_date = Carbon::createFromFormat('Y/m/d',$request['date'])->isPast();
                // $check_time = Carbon::createFromFormat('H:i:s', $request['time'])->isPast();
                $check_date="";
                if($request['date']){
                    $check_date = Carbon::createFromFormat('Y-m-d',$request['date'])->isPast();
                    // $requst_date = Carbon::createFromFormat('Y-m-d',$request['date']);
                    $requst_date = date('Y-m-d', strtotime($request['date']));
                    $check_time="";
                    if($requst_date == $todayDate){
                        $case ="1";
                        
                        if( $request['time']){
                            $check_time = Carbon::createFromFormat('H:i:s', $request['time'])->isPast();
                             if($check_time== true){
                                 $status ="completed";
                                }else{
                                    $status ="pending";
                                }
                        }else{
                            $status ="pending";
                        }
                    }else{
                        $case ="2";
                        if($check_date==true){
                            $status ="completed";
                        }else{
                            if( $request['time']){
                                $check_time = Carbon::createFromFormat('H:i:s', $request['time'])->isPast();
                                 if($check_time== true){
                                     $status ="completed";
                                    }else{
                                        $status ="pending";
                                    }
                            }else{
                                $status ="pending";
                            }
                        }
                    }
                    
                    
                }
               
                if ($request['user_id']) {
                    if ($request['date']) {
                        $time = Str::substr($request['time'] , 0, 5);
                        if ($request['date'] == $todayDate) {
                            // $time = $request->time;
                            if ($time == $timeNow) {
                                // push now
                                $code = 2;
                            } else {
                                $code = 1;
                            }
                        } else {
                            $code = 0;
                        }
                    } else {
                        // push notification now
                        $code = 2;
                    }
    
                    if ($code == 0 || $code == 1) {
                        
                        if($request['time']){
                            $data->time = $request['time'];
                        }
                        
                        if($request['date']){
                            $data->date = $request['date'];
                        }
                        $data->user_id = $request['user_id'];
                        $data->title = $request['title'];
                        $data->body = $request['body'];
                        
                        $data->status = $status;
                        $data->save();
                       
                        $response = [
                            'code' => 0,
                            'message' => 'Success'
                        ];
                    }
    
                    if ($code == 2) {
                        $status = 'completed';
                        if($request['time']){
                            $data->time = $request['time'];
                        }
                        
                        if($request['date']){
                            $data->date = $request['date'];
                        }
                        // $data->user_id = strip_tags(request()->post('user_id'));
                        $data->user_id = $request['user_id'];
                        $data->title = $request['title'];
                        $data->body = $request['body'];
                        
                        $data->status = $status;
                        $data->save();
                       
                        $findEm = User::find($request['user_id']);
                        if ($findEm->device_token) {
                            $url = 'https://fcm.googleapis.com/fcm/send';
                            $dataArr = array(
                                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                                'id' => $request->id,
                                'target'=>'inform_notification',
                                'target_value'=>$request['user_id'],
                                'status' => "done",
    
                            );
                            $notification = array(
                                'title' => $request['title'],
                                'text' => $request['body'],
                              
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
                        $response = [
                            'code' => 0,
                            'message' => 'Success'
                        ];
                    }
                } else {
                    if ($request['date']) {
                        $time = Str::substr($request['time'] , 0, 5);
                        if ($request['date'] == $todayDate) {
                            if ($time == $timeNow) {
                                // push now
                                $code = 2;
                                $case ="1";
                            } else {
                                $code = 1;
                                $case ="2";
                            }
                        } else {
                            $code = 0;
                            $case ="3";
                        }
                    } else {
                        // push notification now
                        $code = 2;
                        $case ="4";
                    }
                    if ($code == 0 || $code == 1) {
                        if($request['time']){
                            $data->time = $request['time'];
                        }
                        if($request['user_id']){
                            $data->user_id = $request['user_id'];
                        }else{
                            $data->user_id = 0;
                        }
                        if($request['date']){
                            $data->date = $request['date'];
                        }
                       
                        $data->title = $request['title'];
                        $data->body = $request['body'];
                        
                        $data->status = $status;
                        $data->save();
                        
                        $response = [
                            'code' => 0,
                            'message' => 'Success',
                            // 'case'=>$case,
                            // 'data_request'=>$request->date,
                            // 'date_today'=>$todayDate
                        ];
                    }
    
    
                    if ($code == 2) {
    
                        $status = 'completed';
                            if($request['time']){
                                $data->time = $request['time'];
                            }
                            if($request['user_id']){
                                $data->user_id = $request['user_id'];
                            }else{
                                $data->user_id = 0;
                            }
                            if($request['date']){
                                $data->date = $request['date'];
                            }
                           
                            $data->title = $request['title'];
                            $data->body = $request['body'];
                            
                            $data->status = $status;
                            $data->save();
                            
                            $url = 'https://fcm.googleapis.com/fcm/send';
                            $dataArr = array(
                                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                                'id' => $request->id,
                                'target'=>'inform_notification',
                                'target_value'=>"",
                                'status' => "done",
    
                            );
    
    
                            $notification = array(
                                'title' => $request['title'],
                                'text' => $request['body'],
    
                                'sound' => 'default',
                                'badge' => '1',
                            );
                            // "registration_ids" => $firebaseToken,
                            $arrayToSend = array(
                                "priority" => "high",
    
                                'to' => "/topics/all",
    
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
                       
                            $response = [
                                'code' => 0,
                                'message' => 'Success',
                                // 'case'=>$case,
                                // 'data_request'=>$request->date,
                                // 'date_today'=>$todayDate
                            ];
                    }
                }
                return response()->json(
                    $response,
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
    public function editNotification(Request $request, $id)
    {
        try {
            $data = Notification::find($id);
            $todayDate = Carbon::now()->format('Y-m-d');
            // $timeNow = Carbon::now()->format('H:i:s');
            $timeNow = Carbon::now()->format('H:i');
            $code = "";
            $status = 'pending';
            $check_date="";
                if($request['date']){
                    $check_date = Carbon::createFromFormat('Y-m-d',$request['date'])->isPast();
                    // $requst_date = Carbon::createFromFormat('Y-m-d',$request['date']);
                    $requst_date = date('Y-m-d', strtotime($request['date']));
                    $check_time="";
                    if($requst_date == $todayDate){
                        $case ="1";
                        
                        if( $request['time']){
                            $check_time = Carbon::createFromFormat('H:i:s', $request['time'])->isPast();
                             if($check_time== true){
                                 $status ="completed";
                                }else{
                                    $status ="pending";
                                }
                        }else{
                            $status ="pending";
                        }
                    }else{
                        $case ="2";
                        if($check_date==true){
                            $status ="completed";
                        }else{
                            if( $request['time']){
                                $check_time = Carbon::createFromFormat('H:i:s', $request['time'])->isPast();
                                 if($check_time== true){
                                     $status ="completed";
                                    }else{
                                        $status ="pending";
                                    }
                            }else{
                                $status ="pending";
                            }
                        }
                    }
                    
                    
                }
            if ($data) {
                $validator = Validator::make($request->all(), [
                    'title' => 'required',
                    'body' => 'required'

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
                    if ($data->status == "completed") {
                        // not push again , just save
                        $data->title =$request['title'] ;
                        $data->body =$request['body'] ;
                        $data->date =$request['date'];
                        
                        if($request['time']){
                            $data->time =$request['time'] ;
                        }
                        if($request['user_id']){
                            $data->user_id = $request['user_id'];
                        }else{
                            $data->user_id = 0;
                        }
                        
                        $data->update();
                        $response = [
                            'code' => 0,
                            'message' => 'Success'
                        ];
                    } else {
                        // check date, and user
                        if ($request['user_id']) {
                            if ($request['date']) {
                                $time = Str::substr($request['time'] , 0, 5);
                                if ($request['date'] == $todayDate) {
                                    // $time = $request->time;
                                    if ( $time == $timeNow) {
                                        $code = 2;
                                    } else {
                                        $code = 1;
                                    }
                                } else {
                                    $code = 0;
                                }
                            } else {
                                // push notification now
                                $code = 2;
                            }
            
                            if ($code == 0 || $code == 1) {
                                if($request['time']){
                                    $data->time = $request['time'];
                                }
                                
                                if($request['date']){
                                    $data->date = $request['date'];
                                }
                                $data->title =$request['title'] ;
                                $data->body =$request['body'] ;
                                $data->date =$request['date'];
                                $data->user_id = $request['user_id'];
                                
                                $data->status = $status;
                                $data->update();
                                
                                $response = [
                                    'code' => 0,
                                    'message' => 'Success'
                                ];
                            }
            
                            if ($code == 2) {
                                $status = 'completed';
                                if($request['time']){
                                    $data->time = $request['time'];
                                }
                                
                                if($request['date']){
                                    $data->date = $request['date'];
                                }
                                $data->title =$request['title'] ;
                                $data->body =$request['body'] ;
                                $data->date =$request['date'];
                                $data->date =$request['date'];
                                
                                $data->user_id = $request['user_id'];
                                $data->update();
                              
                                $findEm = User::find($request['user_id']);
                                if ($findEm->device_token) {
                                    $url = 'https://fcm.googleapis.com/fcm/send';
                                    $dataArr = array(
                                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                                        'id' => $request->id,
                                        'target'=>'inform_notification',
                                        'target_value'=>$findEm->id,
                                        'status' => "done",
            
                                    );
                                    $notification = array(
                                        'title' => $request['title'],
                                        'text' => $request['body'],
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
                                $response = [
                                    'code' => 0,
                                    'message' => 'Success'
                                ];
                            }
                        } else {
                            if ($request['date']) {
                                $time = Str::substr($request['time'] , 0, 5);
                                if ($request['date'] == $todayDate) {
                                    if ($time == $timeNow) {
                                        $code = 2;
                                    } else {
                                        $code = 1;
                                    }
                                } else {
                                    $code = 0;
                                }
                            } else {
                                // push notification now
                                $code = 2;
                            }
                            if ($code == 0 || $code == 1) {
                                if($request['time']){
                                    $data->time = $request['time'];
                                }
                                
                                if($request['date']){
                                    $data->date = $request['date'];
                                }
                                $data->title = $request['title'];
                                $data->body = $request['body'];
                                $data->user_id = 0;
                                $data->status = $status;
                                $data->update();
            
                                $response = [
                                    'code' => 0,
                                    'message' => 'Success'
                                ];
                            }
            
            
                            if ($code == 2) {
            
                               
                                if($request['time']){
                                    $data->time = $request['time'];
                                }
                                
                                if($request['date']){
                                    $data->date = $request['date'];
                                }
                                $data->title = $request['title'];
                                $data->body = $request['body'];
                                $data->user_id = 0;
                                $data->status = $status;
                                $data->update();
            
                                $url = 'https://fcm.googleapis.com/fcm/send';
                                $dataArr = array(
                                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                                    'id' => $request->id,
                                    'target'=>'inform_notification',
                                    'target_value'=>"",
                                    'status' => "done",
            
                                );
            
            
                                $notification = array(
                                    'title' => $request['title'],
                                    'text' => $request['body'],
            
                                    'sound' => 'default',
                                    'badge' => '1',
                                );
                                // "registration_ids" => $firebaseToken,
                                $arrayToSend = array(
                                    "priority" => "high",
            
                                    'to' => "/topics/all",
            
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
            
                              
                                curl_close($ch);
                               
                                $response = [
                                    'code' => 0,
                                    'message' => 'Success'
                                ];
                            }
                        }
                    }
                    
                }
            } else {
                $response = [
                    'message' => 'No notification id found',
                    'code' => -1,

                ];
            }


            return response()->json(
                $response,
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
    public function deleteNotification($id)
    {
        try {
            $data = Notification::find($id);
            if ($data) {

                $data->delete();
                $respone = [
                    'message' => 'Success',
                    'code' => 0,
                ];
            } else {
                $respone = [
                    'message' => 'No notifcation id found',
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
    public function getNotification(Request $request)
    {
        try {
            $pageSize = $request->page_size ?? 10;
            $department = Notification::orderBy('created_at', 'ASC')->paginate($pageSize);
            // $date ="";
            // foreach($department as $record){
            //     if($record->date){

            //     }else{
            //         $date =Carbon::parse($record->created_at)->format('Y-m-d'); 

            //     }
            //     $record->create_date = $date;
            // }

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
    // holiday
    public function addHoliday(Request $request)
    {
        try {
            $todayDate = Carbon::now()->format('m/d/Y');
            $validator = Validator::make($request->all(), [
                'name' => 'required',
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
                $pastDF = Carbon::parse($request['from_date']);
                $pastDT = Carbon::parse($request['to_date']);
                $duration_in_days =   $pastDT->diffInDays($pastDF);
                // out put 1 ,but reality 2 

                $duration = ($duration_in_days + 1);
                $result1 = Carbon::createFromFormat('Y-m-d', $request->from_date)->isPast();
                $status ="";
                if($result1==true){
                    $status ="completed";
                }else{
                    $status ="pending";
                }
                $data = Holiday::create([
                    'name' => $request['name'],
                    'from_date' => $request['from_date'],
                    'to_date' => $request['to_date'],
                    'status' =>  $status,
                    'duration'       => $duration,
                    'notes' => $request['notes']
                ]);


                if($status =="pending"){
            
            
                    $counter = Counter::all();
                   
                   for($i=0; $i< count($counter);$i++){
                    // check user that change dayoff
                       
                        $total = $counter[$i]['total_ph'] + $duration;
                        $counter[$i]['total_ph'] = $total;
                        $query = $counter[$i]->update();
                   }
                   
                }
                $respone = [
                    'message' => 'Success',
                    'code' => 0,


                ];


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
    public function getHoliday(Request $request)
    {
        try {

            $pageSize = $request->page_size ?? 10;
            $todayDate = Carbon::now()->format('Y-m-d');
            $records = Holiday::orderBy('created_at', 'ASC')->paginate($pageSize);
            foreach ($records as $key => $record) {
                $is_today = "false";
                if ($record->status == "pending") {
                    if ($record->from_date == $todayDate) {
                        $is_today = "true";
                    }
                }
                $record->today =  $is_today;
            }
            return response(
                $records,
                200
            );
        } catch (Exception $e) {

            return response([
                'message' => $e->getMessage()
            ]);
        }
    }


    public function editHoliday(Request $request, $id)
    {
        try {
            //code...

            $data = Holiday::find($id);
            $todayDate = Carbon::now()->format('m/d/Y');
            $timeNow = Carbon::now()->format('H:i:s');
            $status = "";
            if ($data) {
                $validator = Validator::make($request->all(), [
                    'name' => 'required',
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
                    $pastDF = Carbon::parse($request['from_date']);
                    $pastDT = Carbon::parse($request['to_date']);
                    $duration_in_days =   $pastDT->diffInDays($pastDF);
                    $result1 = Carbon::createFromFormat('Y-m-d', $request->from_date)->isPast();

                    $duration = ($duration_in_days + 1);
                    $status ="";
                    if($result1==true){
                        $status ="completed";
                    }else{
                        $status ="pending";
                    }
                    $old_status = $data->status;
                    $old_duration = $data->duration;
                    if ($data->status  == 'pending') {
                        $data->name = $request['name'];
                        $data->from_date = $request['from_date'];
                        $data->to_date = $request['to_date'];
                        $data->notes = $request['notes'];
                        $data->duration = $duration;
                        $data->save();
                        if($old_status != $status){
                            $counter = Counter::all();
                               
                               for($i=0; $i< count($counter);$i++){
                                // check user that change dayoff
                                   
                                    $total = $counter[$i]['total_ph'] - $old_duration;
                                    $counter[$i]['total_ph'] = $total;
                                    $query = $counter[$i]->update();
                               }
                            
                        }
                        $respone = [
                            'message' => 'Success',
                            'code' => 0,
                        ];
                    } else {
                        $respone = [
                            'message' => 'Cannot edit holiday',
                            'code' => -1,
                        ];
                    }
                }
            } else {
                $respone = [
                    'message' => 'No holiday id found',
                    'code' => -1,


                ];
            }

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
    public function deleteHoliday($id)
    {
        try {
            $data = Holiday::find($id);
            if ($data) {
                if ($data->status == "pending") {
                    $counter = Counter::all();
               
                    for($i=0; $i< count($counter);$i++){
                        // check user that change dayoff
                        
                            $total = $counter[$i]['total_ph'] - $data->duration;
                            $counter[$i]['total_ph'] = $total;
                            $query = $counter[$i]->update();
                    }
                    $data->delete();
                    $respone = [
                        'message' => 'Success',
                        'code' => 0,
                    ];
                } else {
                    $respone = [
                        'message' => 'Cannot delete',
                        'code' => -1,
                    ];
                }

                // $em = Position::with('employee');
            } else {
                $respone = [
                    'message' => 'No holiday id found',
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

    public function sendDailyReport(Request $request)
    {
        // $start  = Carbon::now()->format('2022-05-20');
        // $end = Carbon::now()->format('2022-05-23 ');
        $start  = Carbon::now()->format('Y-m-d');
        $end = Carbon::now()->format('Y-m-d H:i:s');
        $timeNow = Carbon::now()->format('H:i:s');
        $data['month'] = date('m-Y', strtotime($start));
        $data['employee'] = User::all();
        $attendance['attendance'] = DB::table('users')->join('checkins', 'checkins.user_id', '=', 'users.id')
            ->select('*')
            ->whereDate('checkins.created_at', '>=', date('Y-m-d', strtotime($start)))
            ->whereDate('checkins.created_at', '<=', date('Y-m-d', strtotime($end)))->get();
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
        // return response()->json(
        //     $data ,200
        // );
    }
    public function sendtoTelegram(Request $request)
    {
        $notification = new Notice(
            [
                'notice' => "Testing",
                'noticedes' => "",
                // 'noticedes'=>"employee_name"."\n"."checkin_time".$request['checkin_time']."\n"."date". $request['date']."\n"."checkin_status".$targetStatus."\n",
                'telegramid' => Config::get('services.telegram_id')
            ]
        );
        $notification->notify(new TelegramRegister());
    }
    public function store(Request $request)
    {
    }


    public function show($id)
    {
    }


    public function edit($id)
    {
    }


    public function update(Request $request, $id)
    {
    }


    public function destroy($id)
    {
    }
    // request overt
    public function requetOvertime(Request $request)
    {
        try {
            // $todayDate = Carbon::now()->format('m/d/Y');
            $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'number' => 'required',
                'reason' => 'required',
                'number' => 'required',
                'from_date' => 'required',
                'to_date' => 'required',
                'type' => 'required',
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
                $otMethod = 1;
                $total = 0;
                $typedate = Workday::first();
                $todayDate = "";
                $overtime = "";
                $contract = Contract::where('user_id', $request['user_id'])->first();
                if ($contract) {
                    // check date time server

                    $structure = Structure::find($contract->structure_id);

                    $baseSalary = $structure->base_salary;
                    $standarHour =        $contract->working_schedule;
                    // $standarHour = 44;
                    $SalaryOneHour =  ($baseSalary / 26) / 9;

                    $duration = 0;
                    $duration_in_days = 0;
                    if ($request['type'] == "hour") {
                        $duration = $request['number'];
                    } else {
                        // check date 
                        if ($request['from_date'] == $request['to_date']) {
                            $duration = 8;
                        } else {
                            $dateFrom = "2022-07-21";
                            $dateTo = "2022-07-22";
                            $pastDF = Carbon::parse($request['from_date']);
                            $pastDT = Carbon::parse($request['to_date']);
                            $duration_in_days =   $pastDT->diffInDays($pastDF);
                            // out put 1 ,but reality 2 
                            // $duration_in_days = $request->to_date->diffInDays($request->from_date);
                            // $duration= $duration_in_days;
                            $duration = ($duration_in_days + 1) * 8;
                        }
                    }
                    $value = "";
                    $otInput = 0;
                    if ($request['ot_method']) {
                        if (str_contains($request['ot_method'], '$')) {
                            $value = explode('$', $request['ot_method']);
                            $otInput = $value[0];
                        } else {
                            $otInput = $request['ot_method'];
                        }

                        $otMethod = $otInput;
                        $total = $SalaryOneHour *  $duration *  $otMethod;
                        $total = round($total, 2);
                    } else {
                        // $otRate =0;
                        // $otHour = 0;
                        $otMethod = 1;
                        $total = $SalaryOneHour *  $duration *  $otMethod;
                        $total = round($total, 2);
                    }
                    // 
                    $type = "";

                    if ($typedate->type_date_time == "server") {
                        $todayDate = Carbon::now()->format('m/d/Y');

                        $user = Overtime::create([
                            'user_id' => $request['user_id'],
                            'reason' => $request['reason'],
                            'status' => 'pending',
                            'date' => $todayDate,
                            'number' => $request['number'],
                            'type' => $request['type'],
                            'from_date' => $request['from_date'],
                            'to_date' => $request['to_date'],
                            'ot_rate' => round($SalaryOneHour, 2),
                            'ot_hour' => $duration,
                            'ot_method' => $otMethod,
                            'total_ot' => $total,
                            'send_status' => 'false',
                            'pay_status' => "pending",
                            'notes' => $request['note'],

                        ]);
                    } else {

                        $user = Overtime::create([
                            'user_id' => $request['user_id'],
                            'reason' => $request['reason'],
                            'status' => 'pending',
                            'date' => $request['date'],
                            'number' => $request['number'],
                            'type' => $request['type'],
                            'from_date' => $request['from_date'],
                            'to_date' => $request['to_date'],
                            'ot_rate' => round($SalaryOneHour, 2),
                            'ot_hour' => $duration,
                            'ot_method' => $otMethod,
                            'total_ot' => $total,
                            'pay_status' => "pending",
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
                            'status' => "done",

                        );
                        $notification = array(
                            'title' => "Overtime requested!",
                            'text' => "Overtime requested!",
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
                            'to' => $findEm->device_token,
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
            $todayDate = Carbon::now()->format('m/d/Y');
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
                    $otRate = 0;
                    $otHour = 0;
                    $otMethod = 1;
                    $total = 0;
                    $contract = Contract::where('user_id', $request['user_id'])->first();
                    $typedate = Workday::first();
                    $todayDate = "";
                    $overtime = "";

                    if ($contract) {
                        $structure = Structure::find($contract->structure_id);

                        $baseSalary = $structure->base_salary;
                        // $standarHour = 44;
                        $standarHour =        $contract->working_schedule;
                        $SalaryOneHour =  ($baseSalary / 26) / 9;
                        $duration = 0;
                        $duration_in_days = 0;
                        if ($request['type'] == "hour") {
                            $duration = $request['number'];
                        } else {
                            // check date 
                            if ($request['from_date'] == $request['to_date']) {
                                $duration = 8;
                            } else {
                                $dateFrom = "2022-07-21";
                                $dateTo = "2022-07-22";
                                $pastDF = Carbon::parse($request['from_date']);
                                $pastDT = Carbon::parse($request['to_date']);
                                $duration_in_days =   $pastDT->diffInDays($pastDF);
                                // out put 1 ,but reality 2 
                                // $duration_in_days = $request->to_date->diffInDays($request->from_date);
                                // $duration= $duration_in_days;
                                $duration = ($duration_in_days + 1) * 8;
                            }
                        }
                        // find total ot
                        if ($request['ot_method']) {
                            // $otRate =$request->ot_rate;
                            // $otHour = $request->ot_hour;
                            $otMethod = $request['ot_method'];
                            $total = $SalaryOneHour *  $duration * $otMethod;
                            $total = round($total, 2);
                        } else {
                            // $otRate =0;
                            // $otHour = 0;
                            $otMethod = 1;
                            $total = $SalaryOneHour *  $duration * 1;
                            $total = round($total, 2);
                        }
                        // if user already approve urgent busy however status approve , but can edit other user
                        if ($data->pay_status == "pending"  && ($data->status == "pending" || $data->status == "approved")) {
                            if ($typedate->type_date_time == "server") {
                                $todayDate = Carbon::now()->format('m/d/Y');
                                $data->user_id = $request['user_id'];
                                $data->reason = $request['reason'];
                                $data->notes = $request['note'];
                                $data->from_date = $request['from_date'];
                                $data->to_date = $request['to_date'];
                                $data->number = $request['number'];
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
                                $todayDate = $request['date'];
                                $data->user_id = $request['user_id'];
                                $data->reason = $request['reason'];
                                $data->notes = $request['note'];
                                $data->from_date = $request['from_date'];
                                $data->to_date = $request['to_date'];
                                $data->number = $request['number'];
                                $data->type = $request['type'];
                                // $data->status = $request['pay_status'];
                                $data->ot_rate =  round($SalaryOneHour, 2);
                                $data->ot_hour =  $duration;
                                $data->ot_method = $otMethod;
                                $data->total_ot = $total;
                                $data->date = $todayDate;
                                // $data->pay_status = "pending";
                                $data->update();
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
                                        'status' => "done",

                                    );
                                    $notification = array(
                                        'title' => "Overtime requested!",
                                        'text' => "Overtime requested!",
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
                                        'to' => $findEm->device_token,
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

                $user = Overtime::with('user')
                    ->whereDate('overtimes.created_at', '>=', date('Y-m-d', strtotime($fromDate)))
                    ->whereDate('overtimes.created_at', '<=', date('Y-m-d', strtotime($toDate)))
                    ->orderBy('created_at', 'ASC')->paginate($pageSize);
            } else {

                $user = Overtime::with('user')
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


    // report to make excel for application
    public function attendance(Request $request)
    {
        try {
            $fromDate = $request->get('from_date');
            $toDate = $request->get('to_date');
            $todayDate = Carbon::now()->format('m/d/Y');
            $data = User::whereNotIn('id', [1])->count();

            if ($request->has('from_date') && $request->has('to_date')) {

                $data = Checkin::with('user')->whereDate('checkins.created_at', '>=', date('Y-m-d', strtotime($fromDate)))
                    ->whereDate('checkins.created_at', '<=', date('Y-m-d', strtotime($toDate)))
                    ->orderBy('created_at', 'ASC')->get();
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
    public function leave(Request $request)
    {
        try {
            $fromDate = $request->get('from_date');
            $toDate = $request->get('to_date');
            $todayDate = Carbon::now()->format('m/d/Y');
            $data = User::whereNotIn('id', [1])->count();

            if ($request->has('from_date') && $request->has('to_date')) {

                $data = Leave::with('user', 'leavetype')->whereDate('leaves.created_at', '>=', date('Y-m-d', strtotime($fromDate)))
                    ->whereDate('leaves.created_at', '<=', date('Y-m-d', strtotime($toDate)))
                    ->orderBy('created_at', 'ASC')->get();
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
    public function overtime(Request $request)
    {
        try {
            $fromDate = $request->get('from_date');
            $toDate = $request->get('to_date');
            $todayDate = Carbon::now()->format('m/d/Y');
            $data = User::whereNotIn('id', [1])->count();

            if ($request->has('from_date') && $request->has('to_date')) {

                $data = Overtime::with('user')->whereDate('overtimes.created_at', '>=', date('Y-m-d', strtotime($fromDate)))
                    ->whereDate('overtimes.created_at', '<=', date('Y-m-d', strtotime($toDate)))
                    ->orderBy('created_at', 'ASC')->get();
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
    // structure
    // public function getStructuretype(Request $request)
    // {
    //     try {
    //         $pageSize = $request->page_size ?? 10;
    //         $postion = Structuretype::orderBy('created_at', 'DESC')->paginate($pageSize);

    //         return response()->json(

    //             $postion,
    //             200
    //         );
    //     } catch (Exception $e) {

    //         return response()->json([
    //             'message' => $e->getMessage()
    //         ]);
    //     }
    // }
    // public function addStructuretype(Request $request)
    // {
    //     try {

    //         $validator = Validator::make($request->all(), [
    //             'name' => 'required',
    //         ]);
    //         if ($validator->fails()) {
    //             $error = $validator->errors()->all()[0];
    //             return response()->json(
    //                 [
    //                     'message' => $error,
    //                     'code' => -1,
    //                 ],
    //                 201
    //             );
    //         } else {
    //             $data = Structuretype::create([
    //                 'name' => $request['name'],

    //             ]);
    //             $respone = [
    //                 'message' => 'Success',
    //                 'code' => 0,
    //             ];
    //             return response()->json(
    //                 $respone,
    //                 200
    //             );
    //         }
    //     } catch (Exception $e) {
    //         return response()->json(
    //             [
    //                 'message' => $e->getMessage(),
    //             ]
    //         );
    //     }
    // }
    // public function editStructuretype(Request $request, $id)
    // {
    //     try {
    //         $data = Structuretype::find($id);
    //         $validator = Validator::make($request->all(), [
    //             'name' => 'required',
    //         ]);
    //         if ($validator->fails()) {
    //             $error = $validator->errors()->all()[0];
    //             return response()->json(
    //                 [
    //                     'message' => $error,
    //                     'code' => -1,
    //                 ],
    //                 201
    //             );
    //         } else {
    //             if ($data) {
    //                 $data->name = $request['name'];

    //                 $data->update();
    //                 $respone = [
    //                     'message' => 'Success',
    //                     'code' => 0,

    //                 ];
    //             } else {
    //                 $respone = [
    //                     'message' => 'No structuretype id found',
    //                     'code' => -1,

    //                 ];
    //             }
    //             return response()->json(
    //                 $respone,
    //                 200
    //             );
    //         }
    //     } catch (Exception $e) {
    //         return response()->json(
    //             [
    //                 'message' => $e->getMessage(),
    //             ]
    //         );
    //     }
    // }
    // public function deleteStructuretype($id)
    // {
    //     try {
    //         $data = Structuretype::find($id);
    //         if ($data) {
    //             $emp = Structure::where('structure_type_id', $data->id)->first();
    //             if ($emp) {
    //                 $respone = [
    //                     'message' => 'Cannot delete this structuretype',
    //                     'code' => -1,
    //                 ];
    //             } else {
    //                 $data->delete();
    //                 $respone = [
    //                     'message' => 'Success',
    //                     'code' => 0,
    //                 ];
    //             }
    //         } else {
    //             $respone = [
    //                 'message' => 'No structuretype id found',
    //                 'code' => -1,
    //             ];
    //         }
    //         return response()->json(
    //             $respone,
    //             200
    //         );
    //     } catch (Exception $e) {
    //         return response()->json(
    //             [
    //                 'message' => $e->getMessage(),
    //             ]
    //         );
    //     }
    // }
    // structure
    public function getStructure(Request $request)
    {
        try {
            $pageSize = $request->page_size ?? 10;
            $postion = Structure::orderBy('created_at', 'ASC')->paginate($pageSize);

            return response()->json(

                $postion,
                200
            );
        } catch (Exception $e) {

            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }
    public function getAllStructure(Request $request)
    {
        try {
            $pageSize = $request->page_size ?? 10;
            $postion = Structure::all();

            return response()->json(
                [
                    'data' => $postion
                ],
                200
            );
        } catch (Exception $e) {

            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }
    public function addStructure(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'base_salary' => 'required',
                // 'currency' => 'required',
                // 'structure_type_id' => 'required',
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
                $bonus = 0;
                $senorityMoney = 0;
                $allowance = 0;

                if ($request['allowance']) {
                    $allowance = $request['allowance'];
                } else {
                    $allowance = 0;
                }
                $data = Structure::create([
                    'name' => $request['name'],
                    'base_salary' => $request['base_salary'],
                    // 'currency' => $request['currency'],
                    'allowance' => $allowance,
                    // 'structure_type_id' => $request['structure_type_id'],

                ]);
                $respone = [
                    'message' => 'Success',
                    'code' => 0,
                ];
                return response()->json(
                    $respone,
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
    public function editStructure(Request $request, $id)
    {
        try {
            $data = Structure::find($id);
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'base_salary' => 'required',
                // 'currency' => 'required',
                // 'structure_type_id' => 'required',
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
                $bonus = 0;
                $senorityMoney = 0;
                $allowance = 0;

                if ($request['allowance']) {
                    $allowance = $request['allowance'];
                } else {
                    $allowance = 0;
                }
                if ($data) {
                    $data->name = $request['name'];
                    $data->base_salary = $request['base_salary'];
                    // $data->currency = $request['currency'];
                    $data->allowance = $allowance;
                    // $data->structure_type_id = $request['structure_type_id'];

                    $data->update();
                    $respone = [
                        'message' => 'Success',
                        'code' => 0,

                    ];
                } else {
                    $respone = [
                        'message' => 'No structure id found',
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
                    'message' => $e->getMessage(),
                ]
            );
        }
    }
    public function deleteStructure($id)
    {
        try {
            $data = Structure::find($id);
            if ($data) {
                $emp = Contract::where('structure_id', $data->id)->first();
                if ($emp) {
                    $respone = [
                        'message' => 'Cannot delete this structure',
                        'code' => -1,
                    ];
                } else {
                    $data->delete();
                    $respone = [
                        'message' => 'Success',
                        'code' => 0,
                    ];
                }
            } else {
                $respone = [
                    'message' => 'No structure found',
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
    // contract
    public function getContract(Request $request)
    {
        try {
            $pageSize = $request->page_size ?? 10;
            $postion = Contract::with('user', 'structure')->orderBy('created_at', 'ASC')->paginate($pageSize);

            return response()->json(

                $postion,
                200
            );
        } catch (Exception $e) {

            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }
    public function getAllContract(Request $request)
    {
        try {
            $pageSize = $request->page_size ?? 10;
            $postion = Contract::with('user', 'structure')->get();

            return response()->json(
                [
                    'data' => $postion
                ],
                200
            );
        } catch (Exception $e) {

            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }
    public function addContract(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'ref_code' => 'required',
                'user_id' => 'required',
                'structure_id' => 'required',
                'start_date' => 'required',
                'end_date' => 'required',
                'working_schedule' => 'required',
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

                $data = Contract::create([
                    'ref_code' => $request['ref_code'],
                    'user_id' => $request['user_id'],
                    'structure_id' => $request['structure_id'],
                    'start_date' => $request['start_date'],
                    'end_date' => $request['end_date'],
                    'working_schedule' => $request['working_schedule'],
                    'status' => "running",

                ]);
                $respone = [
                    'message' => 'Success',
                    'code' => 0,
                ];
                return response()->json(
                    $respone,
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
    public function editContract(Request $request, $id)
    {
        try {
            $data = Contract::find($id);
            $validator = Validator::make($request->all(), [
                'ref_code' => 'required',
                'user_id' => 'required',
                'structure_id' => 'required',
                'start_date' => 'required',
                'end_date' => 'required',
                'working_schedule' => 'required',
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

                if ($data) {
                    $data->ref_code = $request['ref_code'];
                    $data->user_id = $request['user_id'];
                    $data->structure_id = $request['structure_id'];
                    $data->start_date = $request['start_date'];
                    $data->end_date = $request['end_date'];
                    $data->status = "running";
                    $data->working_schedule = $request['working_schedule'];

                    $data->update();
                    $respone = [
                        'message' => 'Success',
                        'code' => 0,

                    ];
                } else {
                    $respone = [
                        'message' => 'No contract id found',
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
                    'message' => $e->getMessage(),
                ]
            );
        }
    }
    public function deleteContract($id)
    {
        try {
            $data = Contract::find($id);
            if ($data) {

                $emp = Payslip::where('user_id', $data->user_id)->first();
                if ($emp) {
                    $respone = [
                        'message' => 'Cannot delete this contrat',
                        'code' => -1,
                    ];
                } else {
                    $data->delete();
                    $respone = [
                        'message' => 'Success',
                        'code' => 0,
                    ];
                }
            } else {
                $respone = [
                    'message' => 'No contract id found',
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
    // payroll 
    public function getPayslip(Request $request)
    {
        try {
            $pageSize = $request->page_size ?? 10;
            $fromDate = $request->get('from_date');
            $toDate = $request->get('to_date');
            $postion = Payslip::with('user')->orderBy('created_at', 'ASC')->paginate($pageSize);
            if ($request->has('from_date') && $request->has('to_date')) {

                $postion = Payslip::with('user')
                    ->whereDate('payslips.created_at', '>=', date('Y-m-d', strtotime($fromDate)))
                    ->whereDate('payslips.created_at', '<=', date('Y-m-d', strtotime($toDate)))
                    ->orderBy('created_at', 'ASC')->paginate($pageSize);
            } else {

                $postion = Payslip::with('user')
                    ->orderBy('created_at', 'ASC')->paginate($pageSize);
            }
            return response()->json(

                $postion,
                200
            );
        } catch (Exception $e) {

            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }
    public function addPayslip(Request $request)
    {
    }
    public function editPayslip(Request $request, $id)
    {
    }
    public function deletePayslip($id)
    {
        try {
            $data = Payslip::find($id);
            if ($data) {
                $emp = Contract::where('contract_id', $data->contract_id)->first();
                if ($emp->status == "running") {
                    $respone = [
                        'message' => 'Cannot delete this payslip',
                        'code' => -1,
                    ];
                } else {
                    $data->delete();
                    $respone = [
                        'message' => 'Success',
                        'code' => 0,
                    ];
                }
            } else {
                $respone = [
                    'message' => 'No payslip id found',
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
    public function editOTCompesation(Request $request, $id)
    {
        try {
            //code...
            // get user by token with timetable

            $data = Overtimecompesation::find($id);
            $todayDate = Carbon::now()->format('m/d/Y');
            $timeNow = Carbon::now()->format('H:i:s');
            $status = "";
            $message = "";
            $code = 2;
            $status = "";
            $totalDuration = 0;
            $case = "";

            $userDuration = 0;
            $leftDuration = 0;
            $Checkduration = 0;
            $title = "";
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
                    if ($request["status"] == 'pending') {
                        $status = 'pending';
                    }
                    if ($request["status"] == 'approved') {
                        $status = 'approved';
                    } else {
                        $status = 'rejected';
                    }
                    $create_att=false;
                    $startDate = date('m/d/Y', strtotime($data->from_date));
                    $endDate = date('m/d/Y', strtotime($data->to_date));
                    
                    if ($data->request_type == "clear_ot"){
                        $findCounter = Counter::where('user_id', '=', $data->user_id)->first();
                        if ($findCounter) {
                            // check input user duration to compare with counter
                            if ($status == "approved") {
                                if ($findCounter) {
                                    $totalDuration =  $findCounter->ot_duration;
                                }
                                // check user type for request , day or hour
                                if ($data->type == "hour") {
                                    $Checkduration = $data->duration;;
                                }
                                if ($data->type == "day") {
                                    $Checkduration = $data->duration * 9;
                                    $create_att=true;
                                }
                                if ($totalDuration == 0) {
                                    $message = "Sorry,cannot request this!";
                                    $code = -1;
                                    $case = "1";
                                } else {
                                    $userDuration = $Checkduration;
                                    if ($userDuration > $totalDuration) {
                                        $message = "Sorry,user ot compestion request is less the total ot that completed";
                                        $code = -1;
                                        $case = "2";
                                    }
                                    if ($userDuration == $totalDuration) {
                                        $leftDuration = 0;
                                        $message = "Your requst has been approved!";
                                        $code = 0;
                                        $case = "3";
                                    }
                                    if ($userDuration < $totalDuration) {
                                        $leftDuration = $totalDuration - $userDuration;
                                        $message = "Your requst has been approved!";
                                        $code = 0;
                                        $case = "4";
                                    }
                                }
                            }
                            if( $create_att==true){
                                $user = User::find($data->user_id);
                                if($user){
                                    $time = Timetable::find($user->timetable_id);
                                    while ($startDate <= $endDate) {
                                        $att = new Checkin();
                                        $att->user_id = $user->id;
                                        $att->status = 'present';
                                        $att->checkin_time = $time->on_duty_time;
                                        $att->checkout_time = $time->off_duty_time;
                                        $att->checkin_status = "on time";
                                        $att->checkout_status = "good";
                                        $att->checkin_late = "0";
                                        $att->checkout_late = "0";
                                        $att->duration = 9;
                                        $att->date = $startDate;
                                        $att->send_status = 'false';
                                        $att->confirm = 'false';
                                        $att->ot_status = 'false';
                                        $att->created_at = $startDate;
                                        $att->save();
                                        $startDate = date('m/d/Y', strtotime($startDate . '+1 day'));
                                    }
            
                                }
                                
                            }
                            if ($status == "rejected") {
    
                                $case = "10";
                                $code = 3;
                            }
                            if ($code == 0) {
                                $title = "Your ot compesation request has approved";
                                $data->status = "approved";
                                $findCounter->ot_duration = $leftDuration;
                                $query =   $findCounter->save();
                                // $data->duration = $leftDuration;
                                $query = $data->update();
                                // $data->approved_by=
                                $respone = [
                                    'message' => "Success",
                                    'code' => 0,
                                    'total_duration' => $findCounter,
                                    // 'left_duration'=>$findCounter->duration ,
                                    // 'user_duration'=> $data->duration,
                                    // 'case'=>$case
    
                                ];
                            }
                            if ($code == 3) {
                                $title = "Your ot compesation request has rejected";
                                $data->status = "rejected";
                                $query = $data->update();
                                $respone = [
                                    'message' =>  "Success",
                                    'code' => 0,
    
                                ];
                            }
                            if ($code == -1) {
                                $respone = [
                                    'message' =>  $message,
                                    'code' => -1,
                                    // 'total_duration'=>$totalDuration,
                                    // 'left_duration'=>$leftDuration,
                                    // 'user_duration'=> $data->duration,
                                    // 'case'=>$case
    
                                ];
                            }
                        } else {
                                $respone = [
                                    'message' =>  "Sorry, user don't have any overtime hour",
                                    'code' => -1,
        
        
                                ];
                        }
                    }else{
                        if ($status == "approved") {
                            // $findCounter->ot_duration = $leftDuration;
                            // $query =   $findCounter->save();
                            $data->status = "approved";
                            $query = $data->update();
                            // $data->approved_by=
                   
                        }
                        if ($status == "rejected") {
        
                            $data->status = "rejected";
                            $query = $data->update();
                        }
                        $respone = [
                            'message' => "Success",
                            'code' => 0,
                           
        
                        ];
                    }
                    // $data->status = $status;
                    
                    // $query = $data->save();
                    
                    // check user have overtime
                    
                    //  $startDate = date('Y-m-d',strtotime($data->from_date));
                    $profile = User::find($data->user_id);

                    if ($profile->device_token) {
                        $url = 'https://fcm.googleapis.com/fcm/send';
                        $dataArr = array(
                            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                            'id' => $request->id,
                            'target'=>'inform_linform_otcompesation',
                            'target_value'=>"",
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
                }
            } else {
                $respone = [
                    'message' => 'No ot compesation id found',
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
    public function getOTCompesation(Request $request)
    {
        try {
            //code...
            // get user by token with
            $use_id = $request->user()->id;
            $pageSize = $request->page_size ?? 10;
            $fromDate = $request->get('from_date');
            $toDate = $request->get('to_date');
            // $todayDate = Carbon::now()->format('m/d/Y');

            if ($request->has('from_date') && $request->has('to_date')) {

                $data = Overtimecompesation::with('user')->whereDate('overtimecompesations.created_at', '>=', date('Y-m-d', strtotime($fromDate)))
                    ->whereDate('overtimecompesations.created_at', '<=', date('Y-m-d', strtotime($toDate)))
                    ->orderBy('created_at', 'DESC')->paginate($pageSize);
            } else {
                $data = Overtimecompesation::with('user')
                    ->orderBy('created_at', 'DESC')->paginate($pageSize);
            }


            return response(
                $data,
                200
            );
        } catch (Exception $e) {
            //throw $th;
            return response([
                'message' => $e->getMessage()
            ]);
        }
    }
    public function getleaveOut(Request $request)
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
                    ->orderBy('created_at', 'ASC')->paginate($pageSize);
            } else {
                $user = Leaveout::with('user')

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
    public function getChangeDayoff(Request $request)
    {
        try {
            $use_id = $request->user()->id;
            $ex = User::find($use_id);
            $pageSize = $request->page_size ?? 10;
            $fromDate = $request->get('from_date');
            $toDate = $request->get('to_date');
            $todayDate = Carbon::now()->format('m/d/Y');
            if ($request->has('from_date') && $request->has('to_date')) {
                $user = Changedayoff::with('user')
                    ->whereDate('changedayoffs.created_at', '>=', date('Y-m-d', strtotime($fromDate)))
                    ->whereDate('changedayoffs.created_at', '<=', date('Y-m-d', strtotime($toDate)))
                    ->orderBy('created_at', 'ASC')->paginate($pageSize);
            } else {
                $user = Changedayoff::with('user')

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
    public function editChangeDayOff(Request $request, $id)
    {
        try {
            //code...
            // get user by token with timetable

            $data = Changedayoff::find($id);
            $message = "";
            $code = 2;
            $status = "";
            $respone = [
                'message' =>  $data,
                'code' => -1,
            ];
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
                    $profile = User::find($data->user_id);
                    if ($request["status"] == 'pending') {
                        $status = 'pending';
                    }
                    if ($request["status"] == 'approved') {
                        $status = 'approved';
                    } else {
                        $status = 'rejected';
                    }
                    // $findCounter = Counter::where('user_id', '=', $data->user_id)->first();
                    if ($status == "approved") {
                        $message = "Your request has been accepted!";
                        $code = 0;
                    }
                    if ($status == "rejected") {

                        $message = "Sorry, your request has been rejected!";
                        $code = 3;
                    }
                    $case = "";
                    // get duration from user request 
                    if ($code == 0) {
                        // if user can cell holiday

                        if (str_contains($data->type, 'cancel')) {

                            // change user work day , update user workday or create new work day 
                            // cannot change user workday to work 7 day in case this workday was share to other employee
                            $workday = Workday::whereNull('off_day')
                                ->orWhere('off_day', '=', "")
                                ->first();

                            if ($workday) {
                                $profile->workday_id = $workday->id;
                                $profile->update();

                                $case = "1";
                            } else {

                                $case = "2";
                                $w = Workday::create([
                                    'name'           => "Custom",
                                    'working_day'          => "0,1,2,3,4,5,6",
                                    'notes'       => "custom",

                                ]);
                                $profile->workday_id = $w->id;
                                $profile->update();
                                // $workId="no workday";


                            }
                        }
                        if (str_contains($data->type, 'change dayoff')) {
                            $profile->workday_id = $data->workday_id;
                            $profile->update();
                        }
                        if (str_contains($data->type, 'ph')) {
                        }
                        $data->status = "approved";
                        $query = $data->update();
                        $respone = [
                            'message' => "Success",
                            'code' => 0,

                        ];
                    }
                    if ($code == 3) {
                        $data->status = "rejected";
                        $query = $data->update();
                        $respone = [
                            'message' =>  "Success",
                            'code' => 0,
                        ];
                    }
                    // send message

                    if ($profile->device_token) {


                        $url = 'https://fcm.googleapis.com/fcm/send';
                        $dataArr = array(
                            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                            'id' => $request->id,
                            'target' => 'inform_dayoff',
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
                }
            } else {
                $respone = [
                    'message' =>  "No change dayoff id found",
                    'code' => -1,
                ];
            }

            return response(
                $respone,
                200
            );
        } catch (Exception $e) {

            return response([
                'message' => $e->getMessage(),

            ]);
        }
    }
    public function tineNow(Request $request)
    {

        try {
            // $data = Workday::first();
            // // $todayDate = "";
            // // $todayDate = Carbon::now()->format('m/d/Y');
            // $today = Carbon::now()->format('Y-m-d');
            // $now = Carbon::now()->format('Y-m-d');
            // $past = "20222-08-31";
            // $m = "";
            // $n = 11.5;
            // $result = $n % 2;
            // $myDate = '2022-09-01';
            // $result = Carbon::createFromFormat('Y-m-d', $myDate)->isPast();
            // if ($result == true) {
            //     $m = "true";
            // } else {
            //     $m = "false";
            // }
            // $holiday = Holiday::all();
            // $start_date = date('Y-m-d', strtotime(Carbon::now()->startOfMonth()));
            // $end_date = date('Y-m-d', strtotime(Carbon::now()->endOfMonth()));
            // // $start_date = date('Y-m-d', strtotime(Carbon::now()->startOfWeek()));
            // // $end_date = date('Y-m-d', strtotime(Carbon::now()->endOfWeek()));
            // $checkin = Checkin::where('created_at', [$start_date, $end_date])->get();


            // $total = 0;
            // for ($i = 0; $i < count($holiday); $i++) {
            //     $result1 = Carbon::createFromFormat('Y-m-d', $holiday[$i]['from_date'])->isPast();
            //     $result2 = Carbon::createFromFormat('Y-m-d', $holiday[$i]['to_date'])->isPast();
            //     if ($result1 == false || $result2 == false) {
            //         $total += $holiday[$i]['duration'];
            //     } else {
            //         $total = 0;
            //     }
            // }

            // date between
            // $overtime = Overtime::where('user_id','=',2)
            // ->whereBetween('from_date', '=', $todayDate)
            // ->whereBetween('from_date', '<=', $todayDate)->get();
            // ->where('from_date', '=', $todayDate)
            // ->orWhere('to_date', '>=', $todayDate)->get();
            // $data = Overtime::where('user_id','=',2)->get();
            // $overtime = Overtime::where('user_id', '=', 2)
            //     ->where('from_date', '=', $today)
            //     ->orWhere('to_date', '=', $today)->latest()->first();
            // ->where('from_date','=',$todayDate)->get();
            // ->orWhere('to_date','=',$todayDate)->get();
            // ->whereBetween('from_date', ["$todayDate", "$todayDate"])
            // ->between($todayDate,$todayDate)
            // ->where('from_date')
            // ->whereDate('from_date','=',  $todayDate)
            // ->whereDate('from_date','>=',  $todayDate)


            // if($data->type_date_time=="server"){
            //     $todayDate = Carbon::now()->format('m/d/Y H:i:s');
            //     $timeNow = Carbon::now()->format('H:i:s');
            // }else{
            //     // $todayDate = new Date().getDate();
            // }
            // $data = Payslip::all();

            // $data = Payslip::with('user', 'user.position','user.contract')->orderBy('created_at', 'DESC')->get();
            // foreach ($data as $key => $val) {
            //     //
            //     $start_date = date('m/d/Y', strtotime($val->from_date));
            //     $date = Carbon::createFromFormat('m/d/Y', $start_date);
            //     $monthName = $date->format('F');
            //     // format('l')
            //     $user = User::where('id', '=', $val->user_id)->first();
            //     $position = Position::where('id', '=', $user->position_id)->first();
            //     $ex1 = Contract::where('user_id', '=', $val->user_id)->first();
            //     $ex2 = Structure::where('id', '=', $ex1->structure_id)->first();
            //     $val->user = $user['name'];
            //     $val->position = $position['position_name'];
            //     $val->base_salary = $ex2['base_salary'];
            //     $val->month = $monthName;
            // }
            //     $data = Payslip::select(

            //         DB::raw('

            // payslips.user_id AS user_id,
            // payslips.from_date,
            // payslips.to_date,
            // payslips.advance_salary,
            // payslips.tax_salary,
            // payslips.deduction,
            // payslips.net_salary,
            // payslips.gross_salary,
            // payslips.wage_hour,
            // payslips.net_perday,
            // payslips.net_perhour,
            // payslips.total_attendance,

            // CONCAT(users.name) AS user_name,
            // CONCAT(positions.position_name) AS position_name,
            // CONCAT(structures.name) AS structure


            // ')
            //     )
            //         ->leftJoin('users', 'users.id', '=', 'payslips.user_id')
            //         ->leftJoin('positions', 'positions.id', '=', 'users.position_id')
            //         ->leftJoin('contracts', 'users.id', '=', 'contracts.user_id')
            //         ->leftJoin('structures', 'contracts.user_id', '=', 'structures.id')

            //         ->get();
            //     $myDate = '09/10/2022';
            //     $date = Carbon::createFromFormat('m/d/Y', $myDate);

            //     $monthName = $date->format('F');
            //     $start = new Carbon('first day of last month');
            //     $end = new Carbon('last day of last month');
            //     // $start_date = date('Y-m-d', strtotime($start->startOfMonth()));
            //     // $end_date = date('Y-m-d', strtotime($end->endOfMonth()));
            //     $start_date = date('Y-m-d', strtotime(Carbon::now()->startOfMonth()));
            //     $end_date = date('Y-m-d', strtotime(Carbon::now()->endOfMonth()));
            //     $d= "2022-09-01";
            //     $month = Str::substr($d, 5, 2);
            //     $records = Overtime::whereDate('overtimes.created_at', '>=', date('Y-m-d', strtotime($start_date)))
            // ->whereDate('overtimes.created_at', '<=', date('Y-m-d', strtotime($end_date)))->get();
            // $n = Notification::all();
            // $records = Leave::with('user','leavetype','user.position','user.department')->get();
            // $create_date ="";
            // $workday = "0,1,2,3,4,5";
            // $offDay = "2";
            // $values = explode(",",$workday); 
            // $off= explode(",",$offDay);

            // for($i=0;$i<count($n);$i++){
            //     $create_date = $n[$i]["created_at"]->toDateString();
            // }
            // validate backend 
            // for($i=0;$i<count($values);$i++){

            // }
            // $start_date= "2022-09-01";
            // $end_date= "2022-09-30";
            // $start_date = date('Y-m-d', strtotime(Carbon::now()->startOfMonth()));
            // $end_date = date('Y-m-d', strtotime(Carbon::now()->endOfMonth()));
            // $records = Checkin::select(

            //     DB::raw('
            //     checkins.user_id AS user_id,

            //     count(checkins.id) AS total_checkin,
            //     CONCAT(users.payslip_status) AS month,
            //     CONCAT(users.name) AS user_name,
            //     CONCAT(positions.position_name) AS position_name,
            //     CONCAT(departments.department_name) AS department_name,
            //     CONCAT(workdays.id) AS workday_id
            //     ')
            //     )
            //     ->leftJoin(
            //         'users',
            //         'users.id',
            //         '=',
            //         'checkins.user_id'

            //     )
            //     ->leftJoin('positions', 'positions.id', '=', 'users.position_id')
            //     ->leftJoin('departments', 'departments.id', '=', 'users.department_id')

            //     ->leftJoin('workdays', 'workdays.id', '=', 'users.workday_id')



            //     ->where('checkins.send_status', '=', 'true')->groupBy('user_id');

            // $records = $records->whereDate('checkins.created_at', '>=', date('Y-m-d', strtotime($start_date)))
            //     ->whereDate('checkins.created_at', '<=', date('Y-m-d', strtotime($end_date)))->get();
            // $start_date1 = date('Y/m/d', strtotime($start_date));
            // $end_date1 = date('Y/m/d',  strtotime($end_date));

            // foreach ($records as $key => $val) {

            //     $checkin = ['0'];
            //     $deduction = 0;
            //     $ex1 = Leave::where('user_id', $val->user_id)
            //         ->whereBetween('from_date', [$start_date1, $end_date1])
            //         ->whereBetween('to_date', [$start_date1, $end_date1])
            //         ->where('send_status', '=', "true")
            //         ->get();
            //     // check leavetype first be

            //     for ($i = 0; $i < count($ex1); $i++) {
            //         if ($ex1[$i]['leave_deduction']) {
            //             $deduction += $ex1[$i]['leave_deduction'];
            //         } else {
            //             $deduction = 0;
            //         }
            //     }

            //     $val->leave_deduction = $deduction;
            // }




            // $totalPh = 0;
            // $ph = Holiday::whereDate('from_date', '>=', date('Y-m-d', strtotime($start_date)))
            //     ->whereDate('to_date', '<=', date('Y-m-d', strtotime($end_date)))->get();

            // foreach ($records as $key => $v) {
            //     for ($i = 0; $i < count($ph); $i++) {
            //         // $totalPh += $ph[$i]['duration'];
            //         $from_date = date('m/d/Y', strtotime($ph[$i]['from_date']));
            //         $to_date = date('m/d/Y', strtotime($ph[$i]['to_date']));
            //         $user = Checkin::where('user_id', '=', $v->user_id)
            //             ->where('date', '>=', $from_date)
            //             ->where('date', '<=', $to_date)
            //             ->count();
            //         if ($user == 0) {
            //             $totalPh = $v['total_checkin'] + $ph[$i]['duration'];
            //         } else {
            //             $totalPh =  $v['total_checkin'];
            //         }
            //         $w = Workday::where('id', '=', $v->workday_id)->first();
            //         $check = "";
            //         $p =new PushNotification();

            //         $notCheck1 = $p->getWeekday($from_date);
            //         $notCheck2 = $p->getWeekday($to_date);


            //         if ($w->off_day == $notCheck1 || $w->off_day == $notCheck2) {
            //             $check = "true";
            //             // minus one off day , if user come to work on holiday
            //             $totalPh = $totalPh - 1;
            //         } else {

            //             $totalPh = $totalPh;
            //             $check = "false";
            //         }
            //     }
            //     $v->new_attendance = $totalPh;
            // }
            // $result = array();
            // for ($i = 0; $i < count($records); $i++) {
            //     $lastAttendance=0;
            //     if($records[$i]['new_attendance']==0){
            //         $lastAttendance= $records[$i]['total_checkin'];
            //     }else{
            //         $lastAttendance= $records[$i]['new_attendance'];
            //     }
            //     // $result[] = array(
            //     //     'id' => $i + 1,
            //     //     'month'=>$records[$i]['month'],
            //     //     'name' => $records[$i]['user_name'],
            //     //     'position' => $records[$i]['position_name'],
            //     //     'department' => $records[$i]['department_name'],
            //     //     'total_attendance' => $lastAttendance,
            //     //     'total_deduction' => $records[$i]['leave_deduction'],


            //     // );
            // }


            // foreach ($items as $key => $v) {
            //     for ($i = 0; $i < count($ph); $i++) {
            //         // $totalPh += $ph[$i]['duration'];
            //         $from_date = date('m/d/Y', strtotime($ph[$i]['from_date']));
            //         $to_date = date('m/d/Y', strtotime($ph[$i]['to_date']));
            //         $user = Checkin::where('user_id', '=', $v->user_id)
            //             ->where('date', '>=', $from_date)
            //             ->where('date', '<=', $to_date)
            //             ->count();
            //         if ($user == 0) {
            //             $totalPh = $v['total_checkin'] + $ph[$i]['duration'];
            //         } else {
            //             $totalPh =  $v['total_checkin'];
            //         }
            //         $w = Workday::where('id', '=', $v->workday_id)->first();
            //         $check = "";
            //         $p =new PushNotification();

            //         $notCheck1 = $p->getWeekday($from_date);
            //         $notCheck2 = $p->getWeekday($to_date);


            //         if ($w->off_day == $notCheck1 || $w->off_day == $notCheck2) {
            //             $check = "true";
            //             // minus one off day , if user come to work on holiday
            //             $totalPh = $totalPh - 1;
            //         } else {

            //             $totalPh = $totalPh;
            //             $check = "false";
            //         }
            //     }
            //     $v->new_attendance = $totalPh;
            // }
            // $result = array();
            // for ($i = 0; $i < count($records); $i++) {
            //     $lastAttendance=0;
            //     if($records[$i]['new_attendance']==0){
            //         $lastAttendance= $records[$i]['total_checkin'];
            //     }else{
            //         $lastAttendance= $records[$i]['new_attendance'];
            //     }
            //     $result[] = array(
            //         'id' => $i + 1,
            //         'month'=>$records[$i]['month'],
            //         'name' => $records[$i]['user_name'],
            //         'position' => $records[$i]['position_name'],
            //         'department' => $records[$i]['department_name'],
            //         'total_attendance' => $lastAttendance,
            //         'total_deduction' => $records[$i]['leave_deduction'],


            //     );
            // }
            // $user = User::find(61);
            // $code = 0;
            // $work = Workday::where('id', '=', $user->workday_id)->first();
            // if ($work->off_day !=Null  ) {
            //         $code =1;
            // }else{
            //     $code =-1;
            // }
            // $data = Location::find(10);
            // $location = $this->distance($data->lat, $data->lon, $request['lat'], $request['lon'], "K");
            // $array = array();
            // $findSecuity = User::where('role_id','=',5)->get();

            //             for($i=0;$i<count($findSecuity);$i++){
            //                 $array[] = $findSecuity[$i]['device_token'];
            // $typedate = Workday::first();
            // $timeNow = Carbon::now()->format('H:i:s');
            // $checkinTimeServer = "";

            // $todayDate = "";
            // $overtime = "";
            // if ($typedate->type_date_time == "server") {
            //     $todayDate = Carbon::now()->format('m/d/Y');
            //     $checkinTimeServer = $timeNow;
            // } else {
            //     $todayDate = $request['date'];

            //     $checkinTimeServer = $request['checkin_time'];
            // }


            // if ($typedate->type_date_time == "server") {
            //     $todayDate = Carbon::now()->format('m/d/Y');
            //     $today = Carbon::now()->format('Y-m-d');
            //     $overtime = Overtime::where('user_id', '=',4)
            //         ->where('from_date', '=', $today)
            //         ->where('status','=','approved')
            //         ->orWhere('to_date', '=', $today)->latest()->first();
            // } else {
            //     $todayDate = "10/30/2022";
            //     $overtime = Overtime::where('user_id', '=', 4)
            //         ->where('from_date', '=', $todayDate)
            //         ->where('status','=','approved')
            //         ->orWhere('to_date', '=', $todayDate)->latest()->first();
            // }
            // if ($overtime) {
            //     // $otStatus = "true";
            //     $overtime->status = "completed";


            //     if ($overtime->pay_type == "holiday") {
            //         $overtime->pay_status = "completed";
            //         $counter = Counter::where('user_id', '=', 4)->first();
            //         if ($overtime->type == "hour") {
            //             $counter->ot_duration  =$counter->ot_duration+ $overtime->number;
            //             $counter->update();
            //         } else {
            //             $counter->ot_duration  =$counter->ot_duration+ $overtime->number * 9;
            //             $counter->update();
            //         }


            //     }
            //     $overtime->update();
            // }
            //             }
            // $customers = Overtime::select(

            //     DB::raw('

            // overtimes.user_id AS user_id,

            // SUM(overtimes.ot_hour) AS hour,
            // SUM(overtimes.total_ot) AS total,
            // CONCAT(users.name) AS user_name

            // ')
            // )
            //     ->leftJoin('users', 'users.id', '=', 'overtimes.user_id')
            //     // ->leftJoin('positions', 'positions.id', '=', 'users.position_id')
            //     ->where('overtimes.send_status', '=', 'true')->groupBy('user_id')
            //     ->where('overtimes.pay_type', '=', 'cash')
            //     ->get();
            // $app = url()->current();
            // $url = URL::to("/");
            // $t = "3$";
            // $workday = "";
            // if (str_contains($t, '$')) {
            //     $workday = explode('$', $t);
            // }
            // $data = Holiday::whereDate('from_date',Carbon::now())->first();
            // if($data){

            //     $counter = Counter::all();
            //     $total =0;
            //     for($i=0; $i< count($counter);$i++){
            //      $duration = $counter[$i]['total_ph'];
            //         if( $duration >0){
            //              $total = $duration - $data->duration;
            //              $counter[$i]['total_ph'] = $total;
            //         }

            //         $query = $counter[$i]->update();

            //     }
            //     $a = new PushNotification();
            //     $a->notify($data);
            //     $data->status = "completed";
            //     $data->update();
            //  //    Counter::update();

            //  }
            // renew 
            // $data = Notification::whereDate('date', Carbon::now())->first();
            // $timeNow = Carbon::now()->format('H:i');
           
            // $case="";
            // if ($data) {
            //     //  $t =[
            //     //     "name"=>$data->name,
            //     //     'from_date'=>$data->date
            //     // ];
            //     // $a = new PushNotification();
            //     // $a->notify($data);
            //     $time="";
            //     if($data->time){
            //         $time = Str::substr($data->time , 0, 5);
            //     }
            //     if ($data->user_id != 0) {

            //         if ($time == $timeNow) {
            //             $case="now";
            //             $user = User::find($data->user_id);
            //             if ($user->device_token) {
            //                 $case="have_device";
            //                 // $a = new PushNotification();
            //                 // $a->notify($data);
            //                 $url = 'https://fcm.googleapis.com/fcm/send';
            //                 $dataArr = array(
            //                     'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            //                     'id' => $data->id,
            //                     'target' => 'inform_notification',
            //                     'target_value' => "",
            //                     'status' => "done",
    
            //                 );
    
            //                 $notification = array(
            //                     'title' => $data->title,
            //                     'text' => $data->body,
    
            //                     'sound' => 'default',
            //                     'badge' => '1',
            //                 );
            //                 // "registration_ids" => $firebaseToken,
            //                 $arrayToSend = array(
            //                     "priority" => "high",
    
            //                     'to' => $user->device_token,
            //                     // 'registration_ids'=>'6|bY5aVLz32sZrYIGjqpCqDUsRzFxopG8LgyRi0UOo',
            //                     'notification' => $notification,
            //                     'data' => $dataArr,
            //                     'priority' => 'high'
            //                 );
            //                 $fields = json_encode($arrayToSend);
            //                 $headers = array(
            //                     'Authorization: key=' . "AAAAqP0mBoo:APA91bEHUWxz5ZkOeZXpeoMSYtjQMdY8WCQyZSi7I5ycQJ3T6yUhqofYZ5w3AjCpjYSLm54Z3xTR3rsT7cLQ_L1xk7VNhODQDXi4GpxfRaDUH8eoefKuegD9_gx3IxKHIsFlLp8dcHe8",
            //                     'Content-Type: application/json'
            //                 );
            //                 $ch = curl_init();
            //                 curl_setopt($ch, CURLOPT_URL, $url);
            //                 curl_setopt($ch, CURLOPT_POST, true);
            //                 curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            //                 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            //                 curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            //                 $result = curl_exec($ch);
            //                 // var_dump($result);
            //                 curl_close($ch);
            //                 // $t=[
            //                 //     "title"=>$data->title,
            //                 //     "body"=>$data->body,
            //                 //     "device_token"=>$user->device_token
            //                 // ];
            //                 // $a = new PushNotification();
            //                 // $a->notifySpecificuser($t);
            //             }
            //         }
                   
            //     } else {
            //         if ($time == $timeNow) {
            //             $a = new PushNotification();
            //             $a->notify($data);
            //         }
            //     }
                

            // }
            // $user= User::whereNotIn('id',[1])->get();
            // $todayDate = Carbon::now()->format('m/d/Y');
            // $work = Workday::find($user[2]['workday_id']);
            // $total=0;
            // $workday = explode(',', $work->off_day);
            // $data = count($workday);
        // $time = Timetable::find($user[2]['timetable_id']);
        //         $result1 = Carbon::createFromFormat('H:i:s', $time->on_duty_time)->isPast();
        //         $result2 = Carbon::createFromFormat('H:i:s', $time->off_duty_time)->isPast();

        // for($i=0;$i<count($user);$i++){
        //     $user_id= $user[$i]['id'];
        //     $work = Workday::find($user[$i]['workday_id']);
        //     $code =2;
        //     $dayOff="";
        //     if ($work->off_day !=Null ) {
        //         $OffDay = explode(',', $work->off_day);
        //         $check = "true";
        //         $notCheck = $this->getWeekday($todayDate);
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
        //     // if workday but not come 
        //     if($dayOff == "true"){
        //         // $total +=1;
        //         $checkin = Checkin::where('user_id',$user[$i]['id'])
        //         // ->whereNull('checkin_time')
        //         ->where('date','=', $todayDate)
        //         ->latest()->first();
        //         if($checkin){
        //             // $total =$total;
        //             // check workday 
        //         }else{
                   
        //             $time = Timetable::find($user[$i]['timetable_id']);
        //             $result1 = Carbon::createFromFormat('H:i:s', $time->on_duty_time)->isPast();
        //             $result2 = Carbon::createFromFormat('H:i:s', $time->off_duty_time)->isPast();
        //             // // if past
        //             if($result1==true && $result2==true){
        //                 $checkin = new Checkin();
        //                 $checkin->user_id = $user_id;
        //                     $checkin->checkin_time = "0";
        //                     $checkin->checkout_time = "0";
        //                     $checkin->status = "absent";
        //                     $checkin->date = $todayDate;
        //                     $checkin->checkout_time = "0";
        //                     $checkin->checkin_status = "absent";
        //                     $checkin->checkout_status = "absent";
        //                     $checkin->checkout_late = "0";
        //                     $checkin->checkin_late = "0";
        //                     $checkin->note = "mark by admin";
        //                     $checkin->send_status = "false";
        //                     $checkin->confirm = "false";
        //                     $checkin->duration = "0";
        //                     $checkin->ot_status = "0";
        //                     $checkin->status = "absent";
        //                     $checkin->save();
        //             }
        //         } 
        //     }
            
        // }
        // $holiday = Holiday::all();
        // for($i=0;$i<count($holiday); $i++){
        //     $result1 = Carbon::createFromFormat('Y-m-d', $holiday[$i]['from_date'])->isPast();
        //     $result2 = Carbon::createFromFormat('Y-m-d', $holiday[$i]['to_date'])->isPast();
        //     $total=0;
        //     if($result1==true && $result2==true){
        //         $total +=1;
        //     }else{
        //         $total =-1;
        //     }
            
        // }
        // holiday pass all
        // if($total ==count($holiday)){
        //     $holiday = Holiday::all();
        //     for($i=0;$i<count($holiday); $i++){
        //         $result1 = Carbon::createFromFormat('Y-m-d', $holiday[$i]['from_date'])->isPast();
        //         $result2 = Carbon::createFromFormat('Y-m-d', $holiday[$i]['to_date'])->isPast();
        //         $total=0;
        //         if($result1==true && $result2==true){
        //             $total +=1;
        //         }else{
        //             $total =-1;
        //         }
                
        //     }
        // }
        $year = Carbon::now()->format('Y');
        $h= Holiday::latest()->first();
        $d = explode(',',  $h->from_date);
        if( $year > $d[0]){
           
            $year = Carbon::now()->format('Y');
            $lastyear = $year-1;
            $holiday = Holiday::all();
            for($i=0;$i<count($holiday); $i++){
                $from_date = $holiday[$i]['from_date'];
                $to_date = $holiday[$i]['to_date'];   
                $new_from_date = str_replace($year, $lastyear, $from_date); 
                $new_to_date = str_replace($year, $lastyear, $to_date); 
                $holiday[$i]['from_date']=$new_from_date;
                $holiday[$i]['to_date']= $new_to_date;
                 $holiday[$i]['status']= 'pending';
                $query= $holiday[$i]->update();
            }
        }
        // $lastyear = $year1;
            
            return response()->json(

                [
                    'code'=>0,
                    'last_year'=> $lastyear,
                    'holiday'=>$holiday
                ],
                200
            );
        // $find = 'Sinit_Chou';
        // $replace= 'Kouy_Roumdol';
        
        // $string = "Hi Sinit_Chou come to meet me ";
        // $result = str_replace($find, $replace, $string);
            // return response()->json(
            //     [
            //         'code'=>0,
            //         'message'=>"Success",
            //         // 'result'=>$result
            //         // 'last'=>$lastyear 
            //         // 'workday'=> $data
            //         // 'time_in' =>  $result1,
            //         // 'time_out'=>$result2,
            //         // // 'time_now'=>$timeNow,
            //         // 'data_time'=>$time
            //     ]
            // );
            // return response()->json([
            //     // 'end_date' => $end_date,
            //     // 'start_date' => $start_date,
            //     // 'month'=>$month ,
            //     // 'data'=>$records,

            //     // 'crate_date'=>$create_date
            // ]);

            // return response(

            //     [
            //         'today_date' => $today,
            //         'remaider' => $result,
            //         'date_between' => $overtime,
            //         'past' => $m,
            //         'total_ph' => $total,
            //         'start' => $start_date,
            //         'end' => $end_date,
            //         'create_at' => $checkin
            //         // 'data'=>$data
            //     ],
            //     200
            // );
        } catch (Exception $e) {
            //throw $th;
            return response([
                'message' => $e->getMessage()
            ]);
        }
    }
    public function testing(Request $request)
    {
        try {
            $year = Carbon::now()->format('Y');
            $lastyear = $year-1;

            return response()->json(

                [
                    'code'=>0,
                    'last_year'=> $lastyear
                ],
                200
            );
        } catch (Exception $e) {

            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }
    // user counter
    public function getCounter(Request $request)
    {
        try {
            $pageSize = $request->page_size ?? 10;
            $postion = Counter::with('user')->orderBy('created_at', 'ASC')->paginate($pageSize);

            return response()->json(

                $postion,
                200
            );
        } catch (Exception $e) {

            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }
    public function getdashboard(Request $request)
    {
        try {
            $todayDate = Carbon::now()->format('m/d/Y');
            $data = User::whereNotIn('id', [1])->count();
            $checkin = Checkin::where('checkin_status', '=', 'on time')
                ->where('date', '=', $todayDate)->count();
            $late = Checkin::where('checkin_status', '=', 'late')
                ->where('date', '=', $todayDate)->count();
            $overtime = Checkin::where('checkout_status', '=', 'very good')
                ->where('date', '=', $todayDate)->count();
            $absent = Checkin::where('status', '=', 'absent')
                ->where('date', '=', $todayDate)->count();
            $leave = Checkin::where('status', '=', 'leave')
                ->where('date', '=', $todayDate)->count();
            $dayOff = Checkin::where('status', '=', 'leave')
                ->where('date', '=', $todayDate)->count();

            

            return response()->json(
                [
                    'code' => '0',
                    'message' => 'Success',
                    'user' => $data,
                    'checkin' => $checkin,
                    'late' => $late,
                    'overtime' => $overtime,
                    'absent' => $absent,
                    'leave' => $leave,

                    // 'record'=>$records
                ],
                200
            );
        } catch (Exception $e) {
            //throw $th;
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }
    public function employeeReport(Request $request)
    {
        try {

            $user_id = $request->user()->id;
            // $employee = User::find($use_id);
            $fromDate = $request->get('from_date');
            $toDate = $request->get('to_date');
            $todayDate = Carbon::now()->format('m/d/Y');
            $pageSize = $request->page_size ?? 10;

            //
            $totalOt = 0;

            if ($request->has('from_date') && $request->has('to_date')) {

                $customers = User::select(DB::raw('
                users.name,
                users.id,
                users.status,
                users.gender,
                users.employee_phone,
                users.email,
                users.merital_status,
                users.minor_children,
                users.spouse_job,
                users.nationality,
                users.address,
                departments.department_name,
                positions.position_name,
                positions.type AS position_type,
                DATE_FORMAT(users.created_at, "%Y-%m-%d") as customer_date,
                contracts.start_date,
                contracts.end_date,
                contracts.working_schedule,
                structures.base_salary
            '))->leftJoin('departments', 'users.department_id', '=', 'departments.id')
                    ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
                    ->leftJoin('contracts', 'users.id', '=', 'contracts.user_id')

                    // ->rightJoin('contracts', 'users.id', '=', 'contracts.user_id')
                    ->leftJoin('structures', 'contracts.structure_id', '=', 'structures.id');


                $employee = $customers->whereDate('users.created_at', '>=', date('Y-m-d', strtotime($fromDate)))
                    ->whereDate('users.created_at', '<=', date('Y-m-d', strtotime($todayDate)))->paginate($pageSize);;
                // $employee  = User::whereNotIn('id', [1])
                //   -> whereDate('users.created_at', '>=', date('Y-m-d', strtotime($fromDate)))
                //     ->whereDate('users.created_at', '<=', date('Y-m-d', strtotime($toDate)))
                //     ->orderBy('created_at', 'ASC')
                //     ->paginate($pageSize);


            } else {
                $employee  = User::whereNotIn('id', [1])->orderBy('created_at', 'ASC')
                    ->paginate($pageSize);
            }

            return response()->json(
                $employee,
                200
            );
        } catch (Exception $e) {
            //throw $th;
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }
    public function attendanceReport(Request $request, $userId)
    {
        try {

            $user_id = $request->user()->id;
            // $employee = User::find($use_id);
            $fromDate = $request->get('from_date');
            $toDate = $request->get('to_date');
            $todayDate = Carbon::now()->format('m/d/Y');
            $pageSize = $request->page_size ?? 10;

            //
            $totalOt = 0;

            if ($request->has('from_date') && $request->has('to_date')) {
                // filter employee
                if ($request->id == "0") {
                    //     $employee = Checkin::with('user', 'user.timetable')->whereDate('checkins.created_at', '>=', date('Y-m-d', strtotime($fromDate)))
                    // ->whereDate('checkins.created_at', '<=', date('Y-m-d', strtotime($toDate)))
                    // ->get();
                    $employee = Checkin::select(DB::raw('
            
                    DATE_FORMAT(checkins.created_at, "%d-%m-%Y") as checkin_date,
                    checkins.id ,
                    checkins.checkin_time ,
                    checkins.date ,
                    checkins.checkin_status ,
                    checkins.checkin_late,
                    checkins.checkout_time,
                    checkins.checkout_status,
                    checkins.checkout_late,
                
                    checkins.status,
                    checkins.duration,
                    CONCAT(users.name) AS user_name,
                    CONCAT(timetables.on_duty_time ,"-",timetables.off_duty_time) AS timetable,
                    positions.position_name
                    '))
                        ->leftJoin('users', 'users.id', '=', 'checkins.user_id')
                        ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
                        // ->leftJoin('timetable_employees', 'users.id', '=', 'timetable_employees.user_id')
                        ->leftJoin('timetables', 'timetables.id', '=', 'users.timetable_id');
                    $employee = $employee->whereDate('checkins.created_at', '>=', date('Y-m-d', strtotime($fromDate)))
                        ->whereDate('checkins.created_at', '<=', date('Y-m-d', strtotime($toDate)))->paginate($pageSize);
                    //     $employee  = Checkin::with('user')->
                    //    whereDate('checkins.created_at', '>=', date('Y-m-d', strtotime($fromDate)))
                    //     ->whereDate('checkins.created_at', '<=', date('Y-m-d', strtotime($toDate)))
                    //     ->orderBy('created_at', 'ASC')
                    //     ->paginate($pageSize);
                } else {
                    $employee = Checkin::select(DB::raw('
                
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
                    CONCAT(timetables.on_duty_time ,"-",timetables.off_duty_time) AS timetable_id,
                    positions.position_name
                    '))
                        ->leftJoin('users', 'users.id', '=', 'checkins.user_id')
                        ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
                        // ->leftJoin('timetable_employees', 'users.id', '=', 'timetable_employees.user_id')
                        ->leftJoin('timetables', 'timetables.id', '=', 'users.timetable_id')
                        ->where('checkins.user_id', '=', $userId)
                        ->where('checkins.status', '=', 'present');
                    $employee = $employee->whereDate('checkins.created_at', '>=', date('Y-m-d', strtotime($fromDate)))
                        ->whereDate('checkins.created_at', '<=', date('Y-m-d', strtotime($toDate)))->paginate($pageSize);
                    // $employee  = Checkin::with('user')
                    // ->
                    // where('user_id','=',$userId)

                    // ->whereDate('checkins.created_at', '>=', date('Y-m-d', strtotime($fromDate)))
                    // ->whereDate('checkins.created_at', '<=', date('Y-m-d', strtotime($toDate)))
                    // ->orderBy('created_at', 'ASC')
                    // ->paginate($pageSize);
                }
            }

            return response()->json(
                $employee,
                200
            );
        } catch (Exception $e) {
            //throw $th;
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }
    public function getOvertimeReport(Request $request)
    {
        try {

            $user_id = $request->user()->id;
            // $employee = User::find($use_id);
            $fromDate = $request->get('from_date');
            $toDate = $request->get('to_date');
            $todayDate = Carbon::now()->format('m/d/Y');
            $pageSize = $request->page_size ?? 10;

            //
            $totalOt = 0;

            if ($request->has('from_date') && $request->has('to_date')) {
                // filter employee
                // $employee  = Overtime::with('user')->
                //    whereDate('overtimes.created_at', '>=', date('Y-m-d', strtotime($fromDate)))
                //     ->whereDate('overtimes.created_at', '<=', date('Y-m-d', strtotime($toDate)))
                //     ->orderBy('created_at', 'ASC')
                //     ->paginate($pageSize);
                if ($request->id == "0") {
                    $employee = Overtime::select(

                        DB::raw('
                        
                   
                        overtimes.id,
                        overtimes.reason,
                        overtimes.from_date,
                        overtimes.to_date,
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
                        CONCAT(users.name) AS user_name,
                        CONCAT(positions.position_name) AS position_name
                        ')
                    )
                        ->leftJoin('users', 'users.id', '=', 'overtimes.user_id')
                        ->leftJoin('positions', 'positions.id', '=', 'users.position_id')
                        ->where('overtimes.pay_type', '=', 'cash');
                } else {
                    $employee = Overtime::select(

                        DB::raw('
                        
                   
                        overtimes.id,
                        overtimes.reason,
                        overtimes.from_date,
                        overtimes.to_date,
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
                        CONCAT(users.name) AS user_name,
                        CONCAT(positions.position_name) AS position_name
                        ')
                    )
                        ->leftJoin('users', 'users.id', '=', 'overtimes.user_id')
                        ->leftJoin('positions', 'positions.id', '=', 'users.position_id')
                        ->where('overtimes.pay_type', '=', 'cash')
                        ->where('overtimes.user_id', '=', $request->id);
                }

                $employee = $employee->whereDate('overtimes.created_at', '>=', date('Y-m-d', strtotime($fromDate)))
                    ->whereDate('overtimes.created_at', '<=', date('Y-m-d', strtotime($toDate)))->paginate($pageSize);
            }

            return response()->json(
                $employee,
                200
            );
        } catch (Exception $e) {
            //throw $th;
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }
    public function getLeaveReport(Request $request)
    {
        try {

            $user_id = $request->user()->id;
            // $employee = User::find($use_id);
            $fromDate = $request->get('from_date');
            $toDate = $request->get('to_date');
            $todayDate = Carbon::now()->format('m/d/Y');
            $pageSize = $request->page_size ?? 10;

            //
            $totalOt = 0;

            if ($request->has('from_date') && $request->has('to_date')) {
                // filter employee
                // $employee  = Leave::with('user')->
                //    whereDate('leaves.created_at', '>=', date('Y-m-d', strtotime($fromDate)))
                //     ->whereDate('leaves.created_at', '<=', date('Y-m-d', strtotime($toDate)))
                //     ->orderBy('created_at', 'ASC')
                //     ->paginate($pageSize);

                if ($request->id == "0") {
                    $employee = Leave::select(DB::raw('
                        DATE_FORMAT(leaves.created_at, "%d-%m-%Y") as checkin_date,
                        leaves.id ,
                        leaves.reason ,
                        leaves.from_date,
                        leaves.to_date,
                        leaves.type,
                        leaves.send_status,
                        leaves.number,
                        leaves.date,
                        leaves.leave_deduction,
                        leaves.status,
                        CONCAT(users.name) AS user_name,
                        CONCAT(leavetypes.leave_type) AS leavetype,
                        CONCAT(positions.position_name) AS position_name
           
                    '))
                        ->leftJoin('users', 'users.id', '=', 'leaves.user_id')
                        ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
                        ->leftJoin('leavetypes', 'leavetypes.id', '=', 'leaves.leave_type_id');
                } else {
                    $employee = Leave::select(DB::raw('
                        DATE_FORMAT(leaves.created_at, "%d-%m-%Y") as checkin_date,
                        leaves.id ,
                        leaves.reason ,
                        leaves.from_date,
                        leaves.to_date,
                        leaves.type,
                        leaves.send_status,
                        leaves.number,
                        leaves.date,
                        leaves.leave_deduction,
                        leaves.status,
                        CONCAT(users.name) AS user_name,
                        CONCAT(leavetypes.leave_type) AS leavetype,
                        CONCAT(positions.position_name) AS position_name
           
                    '))
                        ->leftJoin('users', 'users.id', '=', 'leaves.user_id')
                        ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
                        ->leftJoin('leavetypes', 'leavetypes.id', '=', 'leaves.leave_type_id')
                        ->where('leaves.user_id', '=', $request->id);
                }

                // ->where('leaves.user_id', '=', $request->id)

                $employee = $employee->whereDate('leaves.created_at', '>=', date('Y-m-d', strtotime($fromDate)))
                    ->whereDate('leaves.created_at', '<=', date('Y-m-d', strtotime($toDate)))->paginate($pageSize);
            }

            return response()->json(
                $employee,
                200
            );
        } catch (Exception $e) {
            //throw $th;
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }
    public function getLeaveOutReport(Request $request)
    {
        try {

            $user_id = $request->user()->id;
            // $employee = User::find($use_id);
            $fromDate = $request->get('from_date');
            $toDate = $request->get('to_date');
            $todayDate = Carbon::now()->format('m/d/Y');
            $pageSize = $request->page_size ?? 10;

            //
            $totalOt = 0;

            if ($request->has('from_date') && $request->has('to_date')) {
                // filter employee
                if ($request->id == "0") {
                    $employee  = Leaveout::with('user')->whereDate('leaveouts.created_at', '>=', date('Y-m-d', strtotime($fromDate)))
                        ->whereDate('leaveouts.created_at', '<=', date('Y-m-d', strtotime($toDate)))

                        ->orderBy('created_at', 'ASC')
                        ->paginate($pageSize);
                } else {
                    $employee  = Leaveout::with('user')->whereDate('leaveouts.created_at', '>=', date('Y-m-d', strtotime($fromDate)))
                        ->whereDate('leaveouts.created_at', '<=', date('Y-m-d', strtotime($toDate)))
                        ->where('user_id', '=', $request->id)
                        ->orderBy('created_at', 'ASC')
                        ->paginate($pageSize);
                }
            }

            return response()->json(
                $employee,
                200
            );
        } catch (Exception $e) {
            //throw $th;
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }
    public function sendEmployee(Request $request)
    {
        try {



            $validator = Validator::make($request->all(), [
                'countries_ids' => 'required',
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
                $country_ids = $request['countries_ids'];
                $values = [
                    'status' => 'true'
                ];
                User::whereIn('id', $country_ids)->update($values);
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
            //throw $th;
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }
    public function confrimLeave(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'countries_ids' => 'required',
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
                $country_ids = $request['countries_ids'];
                $values = [
                    'send_status' => 'true'
                ];
                Leave::whereIn('id', $country_ids)->update($values);


                $respone = [
                    'message' => 'Success',
                    'code' => 0,

                ];
            }


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
    public function sendtoOvertimeAccount(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'countries_ids' => 'required',
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
                $country_ids = $request['countries_ids'];
                $values = [
                    'send_status' => 'true'
                ];
                Overtime::whereIn('id', $country_ids)->update($values);


                $respone = [
                    'message' => 'Success',
                    'code' => 0,
                    'data' => $country_ids
                ];
            }


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
    public function sendAttencanetoAccount(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'countries_ids' => 'required',
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
                $country_ids = $request['countries_ids'];
                $values = [
                    'send_status' => 'true'
                ];
                Checkin::whereIn('id', $country_ids)->update($values);


                $respone = [
                    'message' => 'Success',
                    'code' => 0,
                    'data' => $country_ids
                ];
            }


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
    // counter
    public function counter(Request $request)
    {
        try {
            $pageSize = $request->page_size ?? 10;
            $postion = Counter::with('user','user.position')->orderBy('created_at', 'ASC')->paginate($pageSize);

            return response()->json(

                $postion,
                200
            );
        } catch (Exception $e) {
            //throw $th;
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }
    public function editcounter(Request $request,$id)
    {
        try {
            
            $validator = Validator::make($request->all(), [
                'user_id' => 'required',
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
                
                $data = Counter::find($id);
                if($data){
                    $data->ot_duration = $request["ot_duration"];
                    $data->total_ph = $request["total_ph"];
                    $data->hospitality_leave = $request["hospitality_leave"];
                    $data->marriage_leave = $request["marriage_leave"];
                    $data->peternity_leave = $request["peternity_leave"];
                    $data->funeral_leave = $request["funeral_leave"];
                    $data->maternity_leave = $request["maternity_leave"];
                    $data->update();

                }
                $respone = [
                    'message' => 'Success',
                    'code' => 0, 
                ];
            }
            return response(
                $respone,
                200
            );
        } catch (Exception $e) {
            //throw $th;
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }
    public function getcheckinHistory(Request $request)
    {
        try {
            $pageSize = $request->page_size ?? 10;
            $postion = CheckinHistory::with('checkin.user')->orderBy('created_at', 'ASC')->paginate($pageSize);

            return response()->json(

                $postion,
                200
            );
        } catch (Exception $e) {
            //throw $th;
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }
}
