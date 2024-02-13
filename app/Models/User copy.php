<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    use HasFactory, Notifiable,HasApiTokens;



    // public function getRouteKeyName()
    // {
    //     return 'name';
    // }
    protected $table =  "users";
    protected $fillable = [
        'name',
        'terminal_id',
        'password',
        'role_id',
        'department_id',
        'timetable_id',
        'username',
        'email',
        'session_id'
        
        
    ];
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    public function leave()
    {
        return $this->hasMany(Leave::class,'user_id','id');
    }
    protected $hidden = [
        'password',
        'session_id'
    ];


    
}
