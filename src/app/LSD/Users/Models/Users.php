<?php

namespace App\LSD\Users\Models;

use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    //
    protected $table = 'lsd_users';
    protected $hidden = ['password','token'];
}
