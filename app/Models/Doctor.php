<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;
    protected $fillable = ['name','email','phone_number','schedule','clinic_address','doctor_image'];
    protected $guarded = ['id'];
}
