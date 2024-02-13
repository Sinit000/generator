<?php

namespace App\Console\Commands;

use App\Models\Changedayoff;
use App\Models\Counter;
use App\Models\Holiday;
use App\Models\Notification;
use App\Models\User;
use App\Notifications\PushNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use App\Notifications\TelegramRegister;
use Illuminate\Console\Command;
use App\Models\Notice;




class HolidayCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:holiday';

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
            // $data = Holiday::whereDate('from_date',Carbon::now())
            // ->where('status','=','pending')
            // ->first();
            $data = Holiday::where('status','=','pending')
            ->get();

            for($i=0;$i<count($data);$i++){
                $pastDF=Carbon::parse($data[$i]['from_date']);
                $pastDN=Carbon::parse( Carbon::now());
                $duration_in_days =   $pastDN ->diffInDays( $pastDF)+1;
                if($duration_in_days ==3){
                    $notification = new Notice(
                        [
                            'notice' => "Wishing Letter",
                            'noticedes' => "Event : {$data[$i]['name']}" . "\n" . "Date : {$data[$i]['from_date']}" . "\n",        
                            'telegramid' => Config::get('services.telegram_id')
                        ]
                    );
                    //  // $notification->save();
                    $notification->notify(new TelegramRegister());
                }
            }
            // $secod = Holiday::whereDate('from_date','=',Carbon::now())
            // ->orWhere('to_date','>=',Carbon::now())
            // ->where('status','=','completed')
            // ->first();
           
            
            
            
    }
}
