<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Notification;
use App\Models\User;
use App\Notifications\PushNotification;

use Carbon\Carbon;
use Illuminate\Support\Str;

class NotifyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:notify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
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
                if ($data->user_id != 0) {

                    if ($time == $timeNow) {
                        $case="now";
                        $user = User::find($data->user_id);
                        if ($user->device_token) {
                            $case="have_device";
                            // $a = new PushNotification();
                            // $a->notify($data);
                            $url = 'https://fcm.googleapis.com/fcm/send';
                            $dataArr = array(
                                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                                'id' => $data->id,
                                'target' => 'inform_notification',
                                'target_value' => "",
                                'status' => "done",
    
                            );
    
                            $notification = array(
                                'title' => $data->title,
                                'text' => $data->body,
    
                                'sound' => 'default',
                                'badge' => '1',
                            );
                            // "registration_ids" => $firebaseToken,
                            $arrayToSend = array(
                                "priority" => "high",
    
                                'to' => $user->device_token,
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
                            // $t=[
                            //     "title"=>$data->title,
                            //     "body"=>$data->body,
                            //     "device_token"=>$user->device_token
                            // ];
                            // $a = new PushNotification();
                            // $a->notifySpecificuser($t);
                        }
                    }
                   
                } else {
                    if ($time == $timeNow) {
                        $a = new PushNotification();
                        $a->notify($data);
                    }
                }
                // 
            }else{
                if ($data->user_id != 0) {

                    $case="now";
                        $user = User::find($data->user_id);
                        if ($user->device_token) {
                            $case="have_device";
                            // $a = new PushNotification();
                            // $a->notify($data);
                            $url = 'https://fcm.googleapis.com/fcm/send';
                            $dataArr = array(
                                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                                'id' => $data->id,
                                'target' => 'inform_notification',
                                'target_value' => "",
                                'status' => "done",
    
                            );
    
                            $notification = array(
                                'title' => $data->title,
                                'text' => $data->body,
    
                                'sound' => 'default',
                                'badge' => '1',
                            );
                            // "registration_ids" => $firebaseToken,
                            $arrayToSend = array(
                                "priority" => "high",
    
                                'to' => $user->device_token,
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
                            // $t=[
                            //     "title"=>$data->title,
                            //     "body"=>$data->body,
                            //     "device_token"=>$user->device_token
                            // ];
                            // $a = new PushNotification();
                            // $a->notifySpecificuser($t);
                        }
                   
                } else {
                    $a = new PushNotification();
                        $a->notify($data);
                }
            }

            
            $data->status= "completed";
            $data->update();
            //  $data=  ::update();

        }
    }
}
