<?php

namespace App\Console\Commands;

use App\Models\Holiday;
use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Counter;
use App\Models\Leavetype;
use App\Models\User;

class RenewHolidayCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:renewholiday';

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
        $year = Carbon::now()->format('Y');
        $h = Holiday::latest()->first();
        $d = explode('-',  $h->from_date);
        $holiday = "";
        if ($year >     $d[0]) {

            $year = Carbon::now()->format('Y');
            $lastyear = $year - 1;
            $holiday = Holiday::all();
            for ($i = 0; $i < count($holiday); $i++) {
                $from_date = $holiday[$i]['from_date'];
                $to_date = $holiday[$i]['to_date'];
                $new_from_date = str_replace($lastyear, $year, $from_date);
                $new_to_date = str_replace($lastyear, $year, $to_date);
                $holiday[$i]['from_date'] = $new_from_date;
                $holiday[$i]['to_date'] = $new_to_date;
                $holiday[$i]['status'] = 'pending';
                $query = $holiday[$i]->update();
            }
            $holiday = Holiday::orderBy('from_date', 'ASC')
            
            ->get();
            $totalPh = 0;
            $result1  = "";
            $result2 = "";
            for ($i = 0; $i < count($holiday); $i++) {
                $result1 = Carbon::createFromFormat('Y-m-d', $holiday[$i]['from_date'])->isPast();
                $result2 = Carbon::createFromFormat('Y-m-d', $holiday[$i]['to_date'])->isPast();
                if ($result1 == false || $result2 == false) {
                    $totalPh += $holiday[$i]['duration'];
                } 
            }
            
           
            $counter = Counter::all();
            for ($i = 0; $i < count($counter); $i++) {
                // $leavetype = Leavetype::all();
                $user = User::find($counter[$i]['user_id']);
                $meternity_leave ="1_month";
                $peternity_leave ="7";
                $marriage_leave="3";
                $funeral_leave="3";
                $hospitality_leave="7";
            
                if($user->gender=="F"){
                    $meternity_leave = $meternity_leave;
                    $peternity_leave ="0";
                }else{
                    $meternity_leave = "0";
                    $peternity_leave =$peternity_leave;
                }   
                // for($i=0;$i<count($leavetype);$i++){
                //     if(str_contains($leavetype[$i]['leave_type'], 'Marriage')){
                //         $marriage_leave=$leavetype[$i]['duration'];
                         
                //     }elseif(str_contains($leavetype[$i]['leave_type'], 'Peternity')){
                //         $peternity_leave =$leavetype[$i]['duration'];
                //     }elseif(str_contains($leavetype[$i]['leave_type'], 'Hospitality')){
                //        $hospitality_leave =$leavetype[$i]['duration'];
                //    }
                //    elseif(str_contains($leavetype[$i]['leave_type'], 'Funeral')){
                //        $funeral_leave =$leavetype[$i]['duration'];
                //    }
                //    elseif(str_contains($leavetype[$i]['leave_type'], 'Maternity')){
                //     $peternity_leave =$leavetype[$i]['duration'];
                //     }
                //     else{
                        
                //     }
                   
                // }
                
                $counter[$i]['total_ph'] =  $totalPh;
                $counter[$i]['hospitality_leave'] = $hospitality_leave;
                $counter[$i]['marriage_leave'] = $marriage_leave;
                $counter[$i]['peternity_leave'] = $peternity_leave;
                $counter[$i]['maternity_leave'] = $meternity_leave;
                $counter[$i]['funeral_leave'] = $funeral_leave;
                $counter[$i]->update();
            }
        }
    }
}
