<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;


class Department extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'department_name',
       
        'notes'
    ];
    public function user()
    {
        return $this->hasMany(User::class);
    }
}
