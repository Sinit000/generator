<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('users')->insert([
            [
                
                'name'=>'admin',
                'username'=>"Admin",
                'terminal_id'=>"",
                'email'=>"admin@gmail.com",
                'password'=>Hash::make('123'),
                'department_id'=>1,
                'role_id' => 1,
                'timetable_id'=>1,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
           

        ]);
    }
}
