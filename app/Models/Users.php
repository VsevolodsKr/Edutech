<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Users extends Authenticatable
{
    use HasFactory, HasApiTokens;
    protected $guard = 'user';
    protected $fillable = ['name', 'email', 'password', 'image'];
    public $timestamps = false;
}
