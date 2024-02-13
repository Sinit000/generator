<?php

namespace App\Exports;

use App\Models\Checkin;
use App\Models\Holiday;
use App\Models\Leave;
use App\Models\Workday;
use App\Notifications\PushNotification;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\DB;

class CheckinExport implements FromCollection, WithHeadings, WithEvents
{
    private $request;
    public function __construct($param)
    {
        $this->request = $param;
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
        $start_date = $this->request->startDate ?? date('Y-m-d', strtotime(Carbon::now()->startOfMonth()));
        $end_date = $this->request->endDate ?? date('Y-m-d', strtotime(Carbon::now()->endOfMonth()));
        if ($this->request->startDate == $today && $this->request->endDate == $today) {
            $start_date = $date;
            $end_date = $date;
        } elseif ($this->request->startDate  == $yesterday && $this->request->endDate ==  $yesterday) {
            $start_date = date('Y-m-d', strtotime($date . '-1 day'));
            $end_date = date('Y-m-d', strtotime($date . '-1 day'));
        } elseif ($this->request->startDate  == $thisWeek && $this->request->endDate == $thisWeek) {
            $start_date = date('Y-m-d', strtotime(Carbon::now()->startOfWeek()));
            $end_date = date('Y-m-d', strtotime(Carbon::now()->endOfWeek()));
        } elseif ($this->request->startDate == $thisMonth && $this->request->endDate == $thisMonth) {
            $start_date = date('Y-m-d', strtotime(Carbon::now()->startOfMonth()));
            $end_date = date('Y-m-d', strtotime(Carbon::now()->endOfMonth()));
        } elseif ($this->request->startDate == $lastMonth && $this->request->endDate == $lastMonth) {
            $start = new Carbon('first day of last month');
            $end = new Carbon('last day of last month');
            $start_date = date('Y-m-d', strtotime($start->startOfMonth()));
            $end_date = date('Y-m-d', strtotime($end->endOfMonth()));
        } elseif ($this->request->startDate  == $thisYear && $this->request->endDate == $thisYear) {
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
        } else {
            $start_date = $this->request->startDate;
            $end_date = $this->request->endDate;
        }
        if ($this->request->id) {
            $records = Checkin::with('user', 'user.timetable')->whereDate('checkins.created_at', '>=', date('Y-m-d', strtotime($start_date)))
                ->whereDate('checkins.created_at', '<=', date('Y-m-d', strtotime($end_date)))
                ->where('user_id', $this->request->id)
                ->get();
        } else {
            $records = Checkin::with('user', 'user.timetable')->whereDate('checkins.created_at', '>=', date('Y-m-d', strtotime($start_date)))
                ->whereDate('checkins.created_at', '<=', date('Y-m-d', strtotime($end_date)))
                ->get();
        }
        if($this->request->account){
            $records = Checkin::select(

                DB::raw('
                checkins.user_id AS user_id,
                
                count(checkins.id) AS total_checkin,
                CONCAT(users.payslip_status) AS month,
                CONCAT(users.name) AS user_name,
                CONCAT(positions.position_name) AS position_name,
                CONCAT(departments.department_name) AS department_name,
                CONCAT(workdays.id) AS workday_id
                ')
                )
                ->leftJoin(
                    'users',
                    'users.id',
                    '=',
                    'checkins.user_id'
    
                )
                ->leftJoin('positions', 'positions.id', '=', 'users.position_id')
                ->leftJoin('departments', 'departments.id', '=', 'users.department_id')
            
                ->leftJoin('workdays', 'workdays.id', '=', 'users.workday_id')

    
                
                ->where('checkins.send_status', '=', 'true')->groupBy('user_id');
            
            $records = $records->whereDate('checkins.created_at', '>=', date('Y-m-d', strtotime($start_date)))
                ->whereDate('checkins.created_at', '<=', date('Y-m-d', strtotime($end_date)))->get();
            $start_date1 = date('Y/m/d', strtotime($start_date));
            $end_date1 = date('Y/m/d',  strtotime($end_date));
    
            foreach ($records as $key => $val) {
    
                $checkin = ['0'];
                $deduction = 0;
                $ex1 = Leave::where('user_id', $val->user_id)
                    ->whereBetween('from_date', [$start_date1, $end_date1])
                    ->whereBetween('to_date', [$start_date1, $end_date1])
                    ->where('send_status', '=', "true")
                    ->get();
                // check leavetype first be
    
                for ($i = 0; $i < count($ex1); $i++) {
                    if ($ex1[$i]['leave_deduction']) {
                        $deduction += $ex1[$i]['leave_deduction'];
                    } else {
                        $deduction = 0;
                    }
                }
    
                $val->leave_deduction = $deduction;
            }
            
    
    
          
            $totalPh = 0;
            $ph = Holiday::whereDate('from_date', '>=', date('Y-m-d', strtotime($start_date)))
                ->whereDate('to_date', '<=', date('Y-m-d', strtotime($end_date)))->get();
    
            foreach ($records as $key => $v) {
                for ($i = 0; $i < count($ph); $i++) {
                    // $totalPh += $ph[$i]['duration'];
                    $from_date = date('m/d/Y', strtotime($ph[$i]['from_date']));
                    $to_date = date('m/d/Y', strtotime($ph[$i]['to_date']));
                    $user = Checkin::where('user_id', '=', $v->user_id)
                        ->where('date', '>=', $from_date)
                        ->where('date', '<=', $to_date)
                        ->count();
                    if ($user == 0) {
                        $totalPh = $v['total_checkin'] + $ph[$i]['duration'];
                    } else {
                        $totalPh =  $v['total_checkin'];
                    }
                    $w = Workday::where('id', '=', $v->workday_id)->first();
                    $check = "";
                    $p =new PushNotification();

                    $notCheck1 = $p->getWeekday($from_date);
                    $notCheck2 = $p->getWeekday($to_date);
    
                   
                    if ($w->off_day == $notCheck1 || $w->off_day == $notCheck2) {
                        $check = "true";
                        // minus one off day , if user come to work on holiday
                        $totalPh = $totalPh - 1;
                    } else {
    
                        $totalPh = $totalPh;
                        $check = "false";
                    }
                }
                $v->new_attendance = $totalPh;
            }
            $result = array();
            for ($i = 0; $i < count($records); $i++) {
                $lastAttendance=0;
                if($records[$i]['new_attendance']==0 || $records[$i]['new_attendance'] =="0"){
                    $lastAttendance= $records[$i]['total_checkin'];
                }else{
                    $lastAttendance= $records[$i]['new_attendance'];
                }
                $result[] = array(
                    'id' => $i + 1,
                    'month'=>$records[$i]['month'],
                    'name' => $records[$i]['user_name'],
                    'position' => $records[$i]['position_name'],
                    'department' => $records[$i]['department_name'],
                    // 'total_attendance' => $lastAttendance,
                     'total_attendance' =>  $lastAttendance,
                    'total_deduction' => $records[$i]['leave_deduction'],
                    // 'new_attenance' =>  $lastAttendance,
                    

                );
            }
        }
        
        // return Overtime::all();


        $result = array();
        for ($i = 0; $i < count($records); $i++) {
            $result[] = array(
                'id' => $i + 1,
                'date' => $records[$i]['date'],
                'name' => $records[$i]['user']['name'],
                'position' => $records[$i]['user']['position']['position_name'],
                'department' => $records[$i]['user']['department']['department_name'],
                'on_duty_time' => $records[$i]['user']['timetable']['on_duty_time'],
                'off_duty_time' => $records[$i]['user']['timetable']['off_duty_time'],
                'checkin_time' => $records[$i]['checkin_time'],
                'checkin_status' => $records[$i]['checkin_status'],
                'checkin_late' => $records[$i]['checkin_late'],
                'checkout_time' => $records[$i]['checkout_time'],
                'checkout_status' => $records[$i]['checkout_status'],
                'checkout_late' => $records[$i]['checkout_late'],
                'duration' => $records[$i]['duration'],
                'status' => $records[$i]['status'],
                'note' => $records[$i]['note'],

            );
        }
        return collect($result);
    }
    public function headings(): array
    {
        if($this->request->account){
            return [
                [
    
                    'Attedanace  Report',
                ],
                [
                    '#',
                    'Monthly',
                    'Name',
                    'Position',
                    'Department',
                    'Total Attedance',
                    'Leave Deduction',
                    
                    
                ]
    
    
            ];
        }else{
            return [
                [
    
                    'Attedanace  Report',
                ],
                [
                    '#',
                    'Date',
                    'Name',
                    'Position',
                    'Department',
                    'Time in',
                    'Time out',
                    'Checkin Time',
                    'Checkin Status',
                    'Late',
                    'Checkout Time',
                    'Checkout Status',
                    'Early',
                    'Duration',
                    'Status',
                    'Note'
                ]
    
    
            ];
        }
        
    }
    // style to column
    public function registerEvents(): array
    {
        if($this->request->account){
            return [
                AfterSheet::class    => function (AfterSheet $event) {
    
                    // from Column A1 to Column P1
    
                    $cellRange1 = 'A1';
                    $cellRange2 = 'A2:G2'; // All 
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
        }else{
            return [
                AfterSheet::class    => function (AfterSheet $event) {
    
                    // from Column A1 to Column P1
    
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
}
