<?php

namespace App\Console\Commands;

use App\Models\Counter;
use Illuminate\Console\Command;
use App\Models\Holiday;
use App\Models\Leavetype;
use App\Models\User;
use Carbon\Carbon;

class RenewCounterCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:renewcounter';

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
        if ($year > $d[0]) {
            
            $holiday = Holiday::orderBy('from_date', 'ASC')->get();
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
            // $user = User::whereNotIn('id', [1])->get();

            
            $counter = Counter::all();
            for($i=0;$i<count($counter);$i++){
                $user = User::find($counter[$i]['user_id']);
                if($user){
                    if($user->gender=="F"){
                        $meternity_leave = $meternity_leave;
                        $peternity_leave ="0";
                    }else{
                        $meternity_leave = "0";
                        $peternity_leave =$peternity_leave;
                    }
                }
                $counter[$i]['total_ph']= $total;
                $counter[$i]['hospitality_leave']= $hospitality_leave;
                $counter[$i]['marriage_leave']= $marriage_leave;
                $counter[$i]['peternity_leave']= $peternity_leave;
                $counter[$i]['maternity_leave']= $meternity_leave;
                $counter[$i]['funeral_leave']= $funeral_leave;
                $counter[$i]->update();
            }
           
            
            
        }
    }
}
