<?php

namespace Database\Seeders;

use App\Models\Timetable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class TimetableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('timetables')->insert([
            [
                'timetable_name'=>"Normal Time",
                'shitf_name'=>"Normal Shift",
                'checkin_time'=>"08:00:00",
                'checkout_time'=>"16:00:00",
                'late_minute'=>"0",
                'early_leave'=>"0",
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'timetable_name'=>"GD",
                'shitf_name'=>"GD Shift",
                'checkin_time'=>"08:00:00",
                'checkout_time'=>"16:00:00",
                'late_minute'=>"0",
                'early_leave'=>"0",
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            
        ]);
    }
}
