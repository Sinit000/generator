<?php

namespace App\Imports;

use App\Models\Attendance;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;

class AttendancesImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // $user = User::where("terminal_id",$row['terminal_id'])->first();
        $user = User::whereNotIn('id', [1])->where("terminal_id",$row['terminal_id'])->first();
        
        if (isset($row['checkin'])) {
            return new Attendance([
                "checkin_time" => $row['checkin'],
                "checkout_time" => $row['checkout'],
                "user_id" => $user['id'],
                "date" => $row['date'],
                "total_duration" => $row['attended'],
                "attendance_worked" => $row['worked'],
                "absent" => $row['absent'],
                "attendance_status" => $row['attendance_status'],
                "ot_level_one" => $row['ot'],
            ]);
           
           
        } else {
        }
    }
}
