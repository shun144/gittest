<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

// class User extends Model
// {
//     use HasFactory;
//     protected $fillable = ['name','email','password', 'store_id'];
// }


class User extends Authenticatable
{
    use HasFactory;
    protected $fillable = ['name','login_id','password', 'store_id'];
    // protected $guarded = ['created_at', 'updated_at'];
}


