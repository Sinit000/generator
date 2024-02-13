<?php

namespace App\Console;

use App\Console\Commands\NotificationCommand;
use App\Models\Holiday;
use App\Models\Notification;
use App\Notifications\PushNotification;
use Carbon\Carbon;
use App\Models\Checkin;
use App\Models\Timetable;
use App\Models\User;
use App\Models\Workday;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        commands\HolidayCommand::class,
        // commands\NotifyCommand::class,
        // commands\CheckAbsentCommand::class,
        // commands\RenewHolidayCommand::class,
        
       
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        
        $schedule->command('command:holiday')
                 ->everyMinute();
        // $schedule->command('command:notify')
        //          ->everyMinute();
        // $schedule->command('command:absent')
        //          ->everyMinute();
        // $schedule->command('command:renewholiday')
        //          ->everyMinute();
                //  $schedule->command('command:renewcounter')
                //  ->everyMinute();
         
        // $schedule->call(function () {
        //     $user= User::whereNotIn('id',[1])->get();
        //     $todayDate = Carbon::now()->format('m/d/Y');
            
        //     for($i=0;$i<count($user);$i++){
        //         $user_id= $user[$i]['id'];
        //         $work = Workday::find($user[$i]['workday_id']);
        //         $code =2;
        //         $dayOff="";
    
        //         if ($work->off_day !=Null ) {
        //             $OffDay = explode(',', $work->off_day);
        //             $check = "true";
        //             $notCheck = $this->getWeekday($todayDate);
        //             // 1 = count($dayoff)
        //             for ($y = 0; $y <  count($OffDay); $y++) {
        //                 //   if offday = today check will false
        //                 if ($OffDay[$y] == $notCheck) {
        //                     $check = "false";
        //                 }
        //             }
        //             if ($check == "false") {
        //                 // day off cannot check
        //                 $dayOff = "false";
        //             } else {
        //                 $dayOff = "true";
        //             }
        //         }
        //         // elseif($work->off_day ==Null){
        //         //     $dayOff = "true";
        //         // }else{
        //         //     $dayOff = "true";
        //         // }
                
        //         // if workday but not come 
        //         if($dayOff == "true"){
        //             // $total +=1;
        //             $checkin = Checkin::where('user_id',$user[$i]['id'])
        //             // ->whereNull('checkin_time')
        //             ->where('date','=', $todayDate)
        //             ->latest()->first();
        //             if($checkin){
        //                 // $total =$total;
        //                 // check workday 
        //             }else{
                       
        //                 $time = Timetable::find($user[$i]['timetable_id']);
        //                 $result1 = Carbon::createFromFormat('H:i:s', $time->on_duty_time)->isPast();
        //                 $result2 = Carbon::createFromFormat('H:i:s', $time->off_duty_time)->isPast();
        //                 // // if past
        //                 if($result1==true && $result2==true){
        //                     $checkin = new Checkin();
        //                     $checkin->user_id = $user_id;
        //                         $checkin->checkin_time = "0";
        //                         $checkin->checkout_time = "0";
        //                         $checkin->status = "absent";
        //                         $checkin->date = $todayDate;
        //                         $checkin->checkout_time = "0";
        //                         $checkin->checkin_status = "absent";
        //                         $checkin->checkout_status = "absent";
        //                         $checkin->checkout_late = "0";
        //                         $checkin->checkin_late = "0";
        //                         $checkin->note = "mark by admin";
        //                         $checkin->send_status = "false";
        //                         $checkin->confirm = "false";
        //                         $checkin->duration = "0";
        //                         $checkin->ot_status = "0";
        //                         $checkin->status = "absent";
        //                         $checkin->save();
                            
        //                 }
        //             } 
        //         }
                
                
        //     }
   
        // })->everyFourHours();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
