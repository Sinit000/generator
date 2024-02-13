<?php

namespace App\Exports;

use App\Models\Contract;
use App\Models\Structure;
use App\Models\User;
use GuzzleHttp\Psr7\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class UserExport implements FromCollection, WithHeadings, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */

    private $request;

    function __construct($param) {
      $this-> request = $param;
    }

    public function getState(){
      return $this-> request;
    }
    
    
    public function collection()
    {
      $today = '1';
        $thisWeek = '2';
        $thisMonth = '3';
        $thisYear = '4';
        $lastMonth = '5';
        $lastYear = '6';
        $yesterday = '7';
        $date = date('Y-m-d');
        $start_date =$this-> request->startDate ?? date('Y-m-d', strtotime(Carbon::now()->startOfMonth()));
        $end_date =$this-> request->endDate ?? date('Y-m-d', strtotime(Carbon::now()->endOfMonth()));
        if($this->request->startDate== $today && $this->request->endDate== $today){
            $start_date = $date;
            $end_date = $date;
        }
        elseif ($this->request->startDate  == $yesterday && $this->request->endDate ==  $yesterday) {
            $start_date = date('Y-m-d', strtotime($date . '-1 day'));
            $end_date = date('Y-m-d', strtotime($date . '-1 day'));
        } elseif ($this->request->startDate  == $thisWeek && $this->request->endDate == $thisWeek) {
            $start_date = date('Y-m-d', strtotime(Carbon::now()->startOfWeek()));
            $end_date = date('Y-m-d', strtotime(Carbon::now()->endOfWeek()));
        }
        elseif ($this->request->startDate == $thisMonth && $this->request->endDate == $thisMonth) {
            $start_date = date('Y-m-d', strtotime(Carbon::now()->startOfMonth()));
            $end_date = date('Y-m-d', strtotime(Carbon::now()->endOfMonth()));
        }
        elseif ($this->request->startDate == $lastMonth && $this->request->endDate == $lastMonth) {
            $start = new Carbon('first day of last month');
            $end = new Carbon('last day of last month');
            $start_date = date('Y-m-d', strtotime($start->startOfMonth()));
            $end_date = date('Y-m-d', strtotime($end->endOfMonth()));
        }
        elseif ($this->request->startDate  == $thisYear && $this->request->endDate == $thisYear) {
            $start = new Carbon('first day of January ' . date('Y'));
            $end = new Carbon('last day of December ' . date('Y'));
            $start_date = date('Y-m-d', strtotime($start));
            $end_date = date('Y-m-d', strtotime($end));
        } elseif ($this->request->startDate == $lastYear && $this->request->endDate == $lastYear) {
            $last_year = date('Y') - 1;
            $start = new Carbon('first day of January ' . $last_year);
            $end = new Carbon('last day of December ' . $last_year);
            $start_date = date('Y-m-d', strtotime($start));
            $end_date = date('Y-m-d', strtotime($end));
        }
        else {
            $start_date = $this->request->startDate;
            $end_date = $this->request->endDate;
        }
        if($this->request->account){
            $records = User::with('department','position')
            ->whereDate('users.created_at', '>=', date('Y-m-d', strtotime($start_date )))
          ->whereDate('users.created_at', '<=', date('Y-m-d', strtotime($end_date)))
          ->where('status','=','true')
          ->get();
        }else{
            $records = User::with('department','position')
            ->whereDate('users.created_at', '>=', date('Y-m-d', strtotime($start_date)))
          ->whereDate('users.created_at', '<=', date('Y-m-d', strtotime($end_date)))->get();
        }
       
        foreach($records as $key=>$val){
            $contract = Contract::where('user_id',$val->id)->first();
            $con_startdate = "";
            $con_enddate = "";
            $schedule = "";
            $baseSalary =0;
            if($contract){
              $con_startdate=$contract->end_date ."/".$contract->start_date;
              // $con_enddate= $contract->start_date;
              $schedule= $contract->working_schedule;
              $structure = Structure::find($contract->structure_id);
              $baseSalary =$structure->base_salary;

            }
            $val->start_contract = $con_startdate;
            $val->end_contract = $con_enddate;
            $val->working_schedule = $schedule;
            $val->base_salary = $baseSalary;
        }
     

        $result = array();
        for($i=0; $i<count($records);$i++){
          $result[] = array(
            'id'=>$i+1,
            'join_date'=>$records[$i]['created_at']->toDateString(),
            'name' => $records[$i]['name'],
            'gender' => $records[$i]['gender'],
            'nationality' => $records[$i]['nationality'],
            'phone' => $records[$i]['employee_phone'],
            'address' => $records[$i]['address'],
            'marital_status' => $records[$i]['merital_status'],
            'minor_children' => $records[$i]['minor_children'],
            'spouse_job' => $records[$i]['spouse_job'],
            'department' => $records[$i]['department']["department_name"],
            'position' => $records[$i]['position']["position_name"],
            'type' => $records[$i]['position']["type"],
            'start_contract'=>$con_startdate ,
            'working_schedule'=>$records[$i]['working_schedule'] ,
            'base_salary'=>$records[$i]['base_salary'] ,

         );
        }

        return collect($result);
    }
    public function headings(): array
    {
       return [
        [
          'Employee Report'
        ],
        [
          '#',
          'Join Date',
          'Name',
          'Nationality',
          'Gender',
          'Phone',
          'Address',
          'Marital Status',
          'Minor Children',
          'Spouse Job',
          'Department',
          'Position',
          'Type',
          'Contract',
          'Schedule',
          'Base Salary'
        ]
       ];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
              $cellRange1 = 'A1';
              $cellRange2 = 'A2:P2'; // All 
              $event->sheet->getDelegate()
                  // ->setCellValue('A1:P1', 'STUDENT PERFORMANCE')
                  ->getStyle($cellRange1)

                  ->getFont()

                  ->setSize(14)

                  ->setBold(true);
              $event->sheet->getDelegate()
                  // ->setCellValue('A1:P1', 'STUDENT PERFORMANCE')
                  ->getStyle($cellRange2)

                  ->getFont()

                  ->setSize(12)

                  ->setBold(true);

   
            },
        ];
    }
}
