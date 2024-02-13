<?php

namespace Database\Seeders;

use App\Models\GroupDepartment;
use App\Models\Leavetype;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
       

        $this->call(RoleSeeder::class);
      
        $this->call(DepartmentSeeder::class);
        $this->call(TimetableSeeder::class);
        $this->call(UserSeeder::class);

        

    }
}
