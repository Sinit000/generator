<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Attendance;
use App\Models\Leave;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;


class UsersImport implements ToModel, WithHeadingRow
// ,
// WithEvents

{

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    // use Importable;

    // public function registerEvents(): array
    // {
    // return [
    //     BeforeImport::class => function (BeforeImport $event) {
    //         $totalRows = $event->getReader()->getTotalRows();

    //         if (!empty($totalRows)) {
    //             echo $totalRows['Worksheet'];
    //         }
    //     }
    // ];
    // }
    public function model(array $row)
    {
        // import user

        // if (isset($row['terminal_id'])){
        //     if (isset($row['checkin'])) {
        //         return new User([
        //             'name'=>$row['name'],
        //             'terminal_id'=>$row['terminal_id'],
        //             'password'=>Hash::make('123'),
        //             'role_id'=>2,
        //         ]);
        //     }

        // }

        // export only one user

        if (isset($row['terminal_id'])) {
            $user = User::whereNotIn('id', [1])->where("terminal_id", $row['terminal_id'])->first();
            $status = "";
            $attendance_status = "";
            // 
            // 1 only checkin ,2 complete(checkin checkout)
            if ($user) {
                if (isset($row['checkin'])) {
                    $date =  date('d/m/Y', strtotime($row['date']));
                    $duration = "0";
                    if ($row['attendance_status'] == "PM") {
                        // Permission
                        $status = "4";
                        Leave::create([
                            'user_id' => $user->id,
                            'date' => $date,
                            'from_date' => $date,
                            'to_date' => $date,
                            'status' => 'PM',
                            'duration' => '1',
                            'created_at' => date('Y-m-d H:i:s', strtotime($row['date'])),
                            'updated_at' => date('Y-m-d H:i:s', strtotime($row['date'])),
                        ]);
                    } elseif ($row['attendance_status'] == "M") {
                        // Mission
                        $status = "5";
                        Leave::create([
                            'user_id' => $user->id,
                            'date' => $date,
                            'from_date' => $date,
                            'to_date' => $date,
                            'status' => 'M',
                            'duration' => '1',
                            'created_at' => date('Y-m-d H:i:s', strtotime($row['date'])),
                            'updated_at' => date('Y-m-d H:i:s', strtotime($row['date'])),
                        ]);
                    } elseif ($row['attendance_status'] == "PH") {
                        // holiday
                        $status = "3";
                    } else {
                        $stringLength = Str::length($row['attended']);
                        if ($row['attended'] == "0 min" || $row['attended'] == "1 min" || $row['attended'] == "2 min" || $row['attended'] == "3 min" || $row['attended'] == "4 min" || $row['attended'] == "5 min" || $row['attended'] == "6 min" || $row['attended'] == "7 min" || $row['attended'] == "8 min" || $row['attended'] == "9 min") {
                            // absent
                            $status = "1";
                        } else {
                            $stringLength = Str::length($row['attended']);

                            if ($stringLength == 6 || $stringLength == 5) {
                                if ($stringLength == 6) {
                                    $rest = substr($row['attended'], 0, 2);
                                    $duration = round(($rest) / 60, 2);
                                } else {
                                    $rest = substr($row['attended'], 0, 2);
                                    $duration = round(($rest) / 60, 1);
                                }
                                $status = "1";
                            } else {
                                $rest = substr($row['attended'], 0, 3);
                                $duration = round(($rest) / 60, 2);
                                if ($duration >= 7) {
                                    // normal work
                                    $status = "0";
                                } elseif ($duration < 6.30) {
                                    $status = "1";
                                } else {
                                    $status = "0";
                                }
                            }
                        }
                    }
                    
                    return new Attendance([
                        "checkin_time" => $row['checkin'],
                        "checkout_time" => $row['checkout'],
                        "user_id" => $user->id,
                        // "date" => "31/08/2023",
                        "checkin_late" => $row["late"],
                        "checkout_early" => $row["early_leave"],
                        "date" => $date,
                        "total_duration" => $row['attended'],
                        "attendance_worked" => $row['worked'],
                        "absent" => $row['absent'],
                        "attendance_status" => $row['attendance_status'],
                        "ot_level_one" => $row['ot'],
                        "status_hour" => $duration,
                        "status" => $status,
                        'created_at' => date('Y-m-d H:i:s', strtotime($row['date'])),
                        'updated_at' => date('Y-m-d H:i:s', strtotime($row['date'])),

                    ]);
                    // 
                } else {
                    // 
                }
            }
        } else {
            // 
        }
    }
}
