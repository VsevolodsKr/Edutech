<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\Encryptable;

class Users extends Authenticatable
{
    use HasFactory, HasApiTokens, Encryptable;
    protected $guard = 'user';
    protected $fillable = ['name', 'email', 'password', 'image'];
    protected $appends = ['encrypted_id'];
}
