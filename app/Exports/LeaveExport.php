<?php

namespace App\Exports;

use App\Models\Leave;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;

class LeaveExport implements FromCollection, WithHeadings, WithEvents
{
    /**
     * @return \Illuminate\Support\Collection
     */
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
        // return Overtime::all();
        
        if($this->request->userId=="0"){
            $records = Leave::with('user', 'leavetype', 'user.position', 'user.department')->whereDate('leaves.created_at', '>=', date('Y-m-d', strtotime($start_date)))
            ->whereDate('leaves.created_at', '<=', date('Y-m-d', strtotime($end_date)))->get();
        }else{
            $records = Leave::with('user', 'leavetype', 'user.position', 'user.department')
            ->whereDate('leaves.created_at', '>=', date('Y-m-d', strtotime($start_date)))
            ->whereDate('leaves.created_at', '<=', date('Y-m-d', strtotime($end_date)))
            ->where('user_id','=',$this->request->userId)
            ->get();
        }
        $result = array();
        for ($i = 0; $i < count($records); $i++) {
            $result[] = array(
                'id' => $i + 1,
                'name' => $records[$i]['user']['name'],
                'position' => $records[$i]['user']['position']['position_name'],
                'department' => $records[$i]['user']['department']['department_name'],
                'leave_type' => $records[$i]['leavetype']['leave_type'],
                'reason' => $records[$i]['reason'],
                'type' => $records[$i]['type'],

                'number' => $records[$i]['number'],
                'from_date' => $records[$i]['from_date'],
                'to_date' => $records[$i]['to_date'],
                'request_date' => $records[$i]['date'],
                'leave_deduction' => $records[$i]['leave_deduction'],
                'status' => $records[$i]['status'],
                'note' => $records[$i]['note'],

            );
        }
        return collect($result);
    }
    public function headings(): array
    {
        return [
            [

                'Employee Leave Report',
            ],
            [
                '#',
                'Name',
                'Position',
                'Department',
                'Leave type',
                'Reason',
                'Type',
                'Duration',
                'From Date',
                'To Date',
                'Request Date',
                'Leave Deduction',
                'Status',
                'Note',
            ]


        ];
    }
    // style to column
    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function (AfterSheet $event) {
                // $event->sheet->setFont(array(
                //     'family'     => 'Calibri',
                //     'size'       => '15',
                //     'bold'       => true
                // ));
                // from Column A1 to Column P1
                // $cellRange = 'A1:I1'; // All headers
                // $event->sheet->getDelegate()->getStyle('A1:P1')
                $cellRange1 = 'A1';
                $cellRange2 = 'A2:N2'; // All 
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
                // $event->sheet->getDelegate()->getStyle($cellRange2)
                // ->getAlignment()
                // ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER) ;   
                // $event->sheet->getDelegate()->getStyle('A2:N2')
                //                                 ->getFill()
                //                                 ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                //                                 ->getStartColor()
                //                                 ->setARGB('FFFF0000');
                // $event->sheet->getDelegate()->getRowDimension('1')->setRowHeight(40);
                // $event->sheet->getDelegate()->getColumnDimension('A1')->setWidth(50);


            },
        ];
    }
}
