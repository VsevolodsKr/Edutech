<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Teachers extends Authenticatable
{
    use HasFactory, HasApiTokens;
    protected $guard = 'teacher';
    protected $fillable = [
        'name',
        'profession',
        'email',
        'password',
        'image',
    ];
    public $timestamps = false;
}
