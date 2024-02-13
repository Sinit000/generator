<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Attendance extends Model
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $fillable = [
        'checkin_time',
        'checkout_time',
        'user_id',
        'date',
        'total_duration',
        'attendance_worked',
        'attendance_status',
        
        'checkin_late',
        'checkout_early',
        'absent',
        'break',
        'leave_type',
        'ot_level_one',
    
        'created_at',
        'updated_at',
        'status_hour',
        'status'
        

    ];
    public function user()
    {
        return $this-> belongsTo(User::class);
    }
}
