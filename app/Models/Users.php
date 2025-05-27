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
    protected $fillable = ['name', 'email', 'password', 'image', 'status'];
    protected $appends = ['encrypted_id'];

    public function likes() {
        return $this->hasMany('App\Models\Likes', 'user_id', 'id');
    }

    public function comments() {
        return $this->hasMany('App\Models\Comments', 'user_id', 'id');
    }

    public function bookmarks() {
        return $this->hasMany('App\Models\Bookmarks', 'user_id', 'id');
    }
}
