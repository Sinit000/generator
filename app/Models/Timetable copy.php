<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Timetable extends Model
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $table =  "timetables";
    protected $fillable = [
        'timetable_name',
        'shitf_name',
        'checkin_time',
        'late_minute',
        'early_leave',
        'checkout_time',
        

    ];
    public function user()
    {
        return $this->hasMany(User::class);
    }
}
